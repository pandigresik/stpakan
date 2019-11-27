<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Transaksi extends MY_Controller {

    protected $_user;
    protected $_farm;
    protected $_berat;
    protected $_grup_farm;
    protected $_filetimbang;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array(
            'm_transaksi'
        ));
        $this->load->helper('stpakan');
        $this->load->config('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_berat = $this->config->item('berat_standart');
        $this->_filetimbang = $this -> config -> item('filetimbang');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
    }

    public function index() {
        //$this->view();
        $data['kode_farm']=$this->_farm;
        $data['grup_farm']=$this->_grup_farm;
        $nomor_do = $this->input->post('nomor_do');
        $data['nomor_do'] = $nomor_do;
        $penerimaan = $this->m_transaksi->get_no_penerimaan($this->_farm, $nomor_do);
        $data['no_penerimaan'] = (empty($penerimaan['no_penerimaan']) ? 0 : 1);
        $this->load->view($this->_grup_farm.'/transaksi', $data);
    }

    public function verifikasi_do() {
        $nomor_do = $this->input->post('nomor_do');
        $file_data_timbang = $this->get_data_timbang($nomor_do);
        $result = $this->m_transaksi->verifikasi_do($this->_farm, $nomor_do);                
        $status = empty($result) ? 0 : 1;
        /** pastikan sudah dilakukan verifikasi DO pakan */        
        if($status){
            $cekSyarat = $this->db->select(array('kode_config','value'))->where(array('kode_config' => '_verify_do_pakan','kode_farm' => $this->_farm,'context' => 'penerimaan_pakan','status' => '1'))->get('SYS_CONFIG_GENERAL')->row_array();
            $harusVerifikasi = empty($cekSyarat) ? 1 : $cekSyarat['value']; 
            if($harusVerifikasi){
                $this->load->model('penerimaan_pakan/m_verifikasi_do_pakan','mvdo');
                $verifikasi_do = $this->mvdo->as_array()->get_by(array('no_do'=> $nomor_do));
                if(empty($verifikasi_do)){
                    $status = 0;
                    $this->result['message'] = 'Belum dilakukan verifikasi DO';
                }else{
                    $result[0]['nopol_terima'] = $verifikasi_do['NOPOL'];
                    $result[0]['sopir'] = $verifikasi_do['NAMA_SOPIR'];
                }
            }            
        }else{
            $this->result['message'] = '*No. DO tidak valid';
        }

            if($status){
                $this->result['content'] = $result[0];        
                $op = $result[0]['no_op'];            
                $data_sj = array(
                    'status' => 0,
                );
                if(empty($result[0]['no_penerimaan'])){
                    $data_sj = $this->sinkron_surat_jalan($op, $nomor_do);
                }             
                
                $no_sj = '';
                /** untuk simulasi dibuat sama dengan nopol_terima */
                //$nopol_kirim = $result[0]['nopol_kirim'];
                $nopol_kirim = $result[0]['nopol_terima'];
                $no_spm = '';
                $tanggal_sj = '';
                $kuantitas_kg = '';
                $kuantitas_zak = '';
                $tgl_verifikasi_do = '';
                if($data_sj['status'] == 1){
                    $data = $data_sj['content']->pjs[0];
                    $no_sj = $data->nomor_sj;
                    $nopol_kirim = $data->nopol_kirim;
                    $no_spm = $data->no_spm;
                    $tanggal_sj = $data->tanggal_sj;
                    $kuantitas_kg = $data->kuantitas_kg;
                    $kuantitas_zak = $data->kuantitas_zak;
                    $tgl_verifikasi_do = $data->tgl_verifikasi_do;
                }
                $result[0]['nopol_kirim'] = $nopol_kirim;
                $result[0]['no_sj'] = $no_sj;
                $result[0]['no_spm'] = $no_spm;
                $result[0]['tanggal_sj'] = $tanggal_sj;
                $result[0]['kuantitas_kg'] = $kuantitas_kg;
                $result[0]['kuantitas_zak'] = $kuantitas_zak;
                $result[0]['tanggal_verifikasi_do'] = $tgl_verifikasi_do;
            }
            $result[0]['all_data'] = '';
            if(!empty($file_data_timbang)){
                $result[0]['all_data'] = $file_data_timbang;
            }
        $this->result['status'] = $status;
        echo json_encode($this->result);
    }

    public function cek_tara_pallet(){
        $tgl = $this->m_transaksi->get_tanggal_aktivasi($this->_farm);
        $hand_pallet = $this->m_transaksi->get_hand_pallet($this->_farm,$tgl[0]['tgl']);
        $all_pallet = $this->m_transaksi->get_all_pallet($this->_farm);
        $sudah_timbang = $this->m_transaksi->get_pallet_sudah_timbang($this->_farm,$tgl[0]['tgl']);

        if (($tgl[0]['tgl'] != '') && (count($all_pallet) == count($sudah_timbang)) && (count($hand_pallet) == 0)) {
            $this->result['success'] = true;
        }else {
            $this->result['success'] = false;
        }
                
        outputJson($this->result);
    }

    public function penimbangan_pakan() {
        $nomor_do = $this->input->post('nomor_do');
        $no_penerimaan = $this->input->post('no_penerimaan');
        switch ($this->_grup_farm) {
            case 'bdy':
                $do_params = implode("','", $nomor_do);
                $detail_kandang = $this->m_transaksi->data_sub_detail_penimbangan_pakan($this->_farm, $do_params);
                if(!empty($no_penerimaan)){
                    $new_detail_kandang=[];
                    foreach ($detail_kandang as $key => $value) {
                        $new_detail_kandang[$value['kode_pakan']][$value['nama_kandang']] = array(
                            'nama_kandang' => $value['nama_kandang'],
                            'jml_kebutuhan' => $value['jml_kebutuhan']
                        );
                    }
                    $data['detail_kandang'] = $new_detail_kandang;
                    $data['data_penimbangan'] = $this->m_transaksi->data_penimbangan($this->_farm, $do_params);
                    $data['data_pakan_rusak_hilang'] = json_encode($this->m_transaksi->data_pakan_rusak_hilang($this->_farm, $no_penerimaan));
                    $this->load->view($this->_grup_farm.'/penimbangan_pakan', $data);
                }
                else{
                    $data['penimbangan_pakan'] = $this->m_transaksi->penimbangan_pakan_bdy($this->_farm, $do_params);
                    $data['sub_detail_penimbangan_pakan'] = json_encode($this->m_transaksi->sub_detail_penimbangan_pakan($this->_farm, $do_params));
                    $data['berat_standart'] = $this->_berat;
                    $this->load->view($this->_grup_farm.'/header_penimbangan_pakan', $data);

                }
                break;

            default:
                $data['penimbangan_pakan'] = $this->m_transaksi->penimbangan_pakan_brd($this->_farm, $nomor_do);
                $this->load->view($this->_grup_farm.'/penimbangan_pakan', $data);
                break;
        }

    }

    public function detail_penimbangan_pakan() {
        $data['data_ke'] = $this->input->post('data_ke');
        $kode_pakan = $this->input->post('kode_pakan');
        $no_pallet = $this->input->post('no_pallet');
        $kode_flok = $this->input->post('kode_flok');
        $data['no_pallet'] = $no_pallet;
        $len = $this->input->post('len');
        $data['len'] = $len;
        $baris = $this->input->post('baris');
        $data['baris'] = empty($baris) ? 1 : $baris;
        $array_no_kavling = $this->input->post('no_kavling');
        $array_kode_pakan = $this->input->post('list_kode_pakan');
        $no_kavling = '';
        $list_kode_pakan = '';
        if(count($array_no_kavling) > 0){
            $no_kavling = implode($array_no_kavling, ',');
        }
        if(count($array_kode_pakan) > 0){
            $list_kode_pakan = implode($array_kode_pakan, ',');
        }
        $kode_farm = $this->_farm;  
        $data['detail_penimbangan_pakan'] = $this->m_transaksi->detail_penimbangan_pakan($this->_farm, $kode_flok, $no_pallet, $no_kavling, $kode_pakan, $list_kode_pakan);        
        $lockTimbangan = $this->db->select(array('kode_config','value'))->where(array('kode_config' => '_lock_timbang_fm','kode_farm' => $kode_farm,'context' => 'penerimaan_pakan','status' => '1'))->get('SYS_CONFIG_GENERAL')->row_array();
        $data['lockTimbangan'] = empty($lockTimbangan) ? 1 : $lockTimbangan['value'];
        $this->load->view($this->_grup_farm.'/detail_penimbangan_pakan', $data);
    }

    public function pakan_rusak_hilang() {
        $data['sisa'] = $this->input->post('sisa');
        $data['nama_pakan'] = $this->input->post('nama_pakan');
        $data['kode_pakan'] = $this->input->post('kode_pakan');
        $data['tmp_data_ke'] = $this->input->post('data_ke');

        switch ($this->_grup_farm) {
            case 'bdy':
                $data['pakan_rusak_hilang'] = $this->input->post('pakan_rusak_hilang');
                break;

            default:
                $data['nomor_do'] = $this->input->post('nomor_do');
                $data['pakan_rusak_hilang'] = $this->m_transaksi->pakan_rusak_hilang($this->_farm, $data['nomor_do'], $data['kode_pakan']);
                break;
        }
        $this->load->view($this->_grup_farm.'/pakan_rusak_hilang', $data);
    }

    public function cek_kode_verifikasi_kavling() {
        $data = $this->input->post('data');
        $result = $this->m_transaksi->cek_kode_verifikasi_kavling($data [0]);
        echo (count($result) > 0) ? 1 : 0;
    }

    public function validasi_tutup() {
        $no_penerimaan = $this->input->post('no_penerimaan');
        $result = $this->m_transaksi->validasi_tutup($no_penerimaan);
        echo json_encode($result);
    }

    public function get_detail_barang_rk() {
        $no_penerimaan = $this->input->post('no_penerimaan');
        $data['list'] = $this->m_transaksi->susun_detail_barang_rk($this->_farm,$no_penerimaan);

        $this->load->view($this->_grup_farm.'/detail_barang_rk',$data);
    }

    public function cek_konversi() {
        $berat = $this->input->post('berat');
        $result = $this->m_transaksi->cek_konversi($this->_berat, $berat);        
        if(!empty($result)){
            $konversi_sak = round($berat/$this->_berat);        
            $result[0]['KONFIRMASI_SAK'] = $result[0]['JML_SAK'] == $konversi_sak ? 0 : 1;        
        }else{
            $result[0] = array();
        }
        
        echo json_encode($result[0]);
    }

    public function cek_maks_pallet() {
        $no_kavling = $this->input->post('no_kavling');
        $result = $this->m_transaksi->cek_maks_pallet($this->_farm, $no_kavling);

        echo json_encode($result);
    }

    public function cetak_bukti_penerimaan_barang() {
        $no_penerimaan = $this->input->get('no_penerimaan');
        $no_op = $this->input->get('no_op');
        $kode_farm = $this->_farm;

        $this->load->library('Pdf');
        $pdf = new Pdf('P', PDF_UNIT, 'A5', true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $data = $this->m_transaksi->detail_penerimaan($kode_farm, $no_penerimaan, $no_op);

        $html = $this->load->view($this->_grup_farm.'/penerimaan_pakan/bukti_penerimaan_barang_pdf', array(
            'list' => $data
        ), true);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('bukti_penerimaan_barang.pdf', 'I');
    }

    public function view_berita_acara() {
        $no_berita_acara = $this->input->post('no_berita_acara');
        $data['list']= $this->m_transaksi->susun_data_berita_acara($this->_farm, $no_berita_acara);

        $this->load->view($this->_grup_farm.'/berita_acara',$data);
    }

    public function cetak_berita_acara() {
        $no_berita_acara = $this->input->get('no_berita_acara');
        $data= $this->m_transaksi->susun_data_berita_acara($this->_farm, $no_berita_acara);

        $this->load->library('Pdf');
        $pdf = new Pdf('P', PDF_UNIT, 'A5', true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $html = $this->load->view('penerimaan_pakan/berita_acara_pdf', array(
                'list' => $data
                    ), true);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('berita_acara.pdf', 'I');
    }

    public function detail_penerimaan() {
        $bukti = $this->input->post('bukti');
        $no_op = $this->input->post('no_op');
        $no_penerimaan = $this->input->post('no_penerimaan');
        $data['list'] = $this->m_transaksi->detail_penerimaan($this->_farm, $no_penerimaan, $no_op);

        ($bukti == 1) ? $this->load->view($this->_grup_farm.'/bukti_penerimaan_barang', $data) : $this->load->view($this->_grup_farm.'/detail_penerimaan', $data);
    }

    public function simpan_konfirmasi() {
        $data = $this->input->post('data');
        $result = $this->m_transaksi->simpan_konfirmasi($data [0], $this->_user);
        echo json_encode($result);
    }

    public function simpan_konfirmasi_rk() {
        $tmp_data = $this->input->post('data');
        $data = json_decode($tmp_data,true);
        #print_r($data);
        $fileName   = $this->input->post('attachment_name');
        $data_file   = $this->input->post('attachment');
        #echo $fileName;
        if(empty($fileName)){
        $format      = null;
        $fileSize    = null;
        $fileFormat  = null;
        $statusError = null;
        $fileContent = null;
        }
        else{
        $format      = substr(strrchr($fileName,'.'),1); #pathinfo($this->input->post('attachment'),PATHINFO_EXTENSION);
        $fileSize    = $_FILES["attachment"]["size"];
        $fileFormat  = $_FILES["attachment"]["type"];
        $statusError = $_FILES["attachment"]["error"];
        $fileContent = $_FILES["attachment"]["tmp_name"];
        }
        #echo 'fileName = '.$fileName.', format = '.$format.', fileSize = '.$fileSize.', fileFormat = '.$fileFormat.', statusError = '.$statusError.', fileContent = '.$fileContent;

        //$result = $this->m_transaksi->simpan_konfirmasi_rk($data, $this->_farm, $this->_user, $fileContent, $format, $fileSize, $fileName);
        echo json_encode($result);
    }

    public function tutup_otomatis() {
        $nomor_do = $this->input->post('nomor_do');
        $result = $this->m_transaksi->tutup_otomatis($nomor_do, $this->_farm);
        echo $result;
    }

    public function rekomendasi_kavling() {
        $nama_gudang = $this->input->post('nama_gudang');
        $no_reg = $this->input->post('no_reg');
        $jumlah_zak = $this->input->post('jumlah_zak');
        $kavling = $this->input->post('kavling');
        $result = $this->m_transaksi->rekomendasi_kavling($this->_farm, $nama_gudang, $no_reg, $jumlah_zak, $kavling);
        echo $result;
    }

    public function get_data_penerimaan() {
        $kode_farm = $this->_farm;
        $all_data = $this->input->post('data');
        $result = $this->m_transaksi->get_header_barang($all_data, $kode_farm);
        echo json_encode($result);
    }

    public function check_ekspedisi() {
        $kode_farm = $this->_farm;
        $nama_ekspedisi = $this->input->post('nama_ekspedisi');
        $nopol_ekspedisi = $this->input->post('nopol_ekspedisi');
        $result = $this->m_transaksi->check_ekspedisi($nama_ekspedisi, $nopol_ekspedisi);
        echo json_encode($result);
    }

    public function get_data_detail_penerimaan() {
        $no_penerimaan = $this->input->post('no_penerimaan');
        $no_pallet = $this->input->post('no_pallet');
        $kode_barang = $this->input->post('kode_barang');
        $kode_farm = $this->_farm;
        $jumlah = $this->input->post('jumlah');
        $no_op = $this->input->post('no_op');
        $no_do = $this->input->post('no_do');
        $result = $this->m_transaksi->get_detail_barang($kode_farm, $no_penerimaan, $no_pallet, $kode_barang, $jumlah, $no_op, $no_do);

        echo json_encode($result);
    }

    public function simpan_hasil_timbang() {
        $all_data = $this->input->post('data');
        $result = $this->m_transaksi->simpan_hasil_timbang($all_data [0], $this->_user); // [];
        echo json_encode($result);
    }

    public function konfirmasi_selesai() {
        $all_data = $this->input->post('data');
        $result = $this->m_transaksi->konfirmasi_selesai($all_data [0], $this->_user, $this->_farm); // [];
        echo json_encode($result);
    }

    public function validasi_barcode_penerimaan() {
        $kode_farm = $this->_farm;
        $no_sj = $this->input->post('no_sj');
        $no_do = $this->input->post('no_do');
        $no_op = $this->input->post('no_op');
        $no_spm = $this->input->post('no_spm');
        $all_kode_barang = $this->input->post('all_kode_barang');
        $all_jumlah = $this->input->post('all_jumlah');
        $result = $this->m_transaksi->validasi_barcode_penerimaan($kode_farm, $no_sj, $no_do, $no_op, $no_spm, $all_kode_barang, $all_jumlah);
        echo json_encode($result);
    }

    public function layout_kavling() {
        $kode_farm = $this->_farm;
        $alldata = $this->m_transaksi->group_layout_kavling($kode_farm, $this->_grup_farm);
        $data ['layout'] = isset($alldata['data_kavling']) ? $alldata['data_kavling'] : array();
        $data ['data_kolom'] = isset($alldata['data_kavling']) ? $alldata['data_kolom'] : array();
        $data ['max_no_baris'] = isset($alldata['data_kavling']) ? $alldata['max_no_baris'] : array();
        $this->load->view($this->_grup_farm.'/layout_kavling', $data);
    }

    public function layout_gudang() {
        $kode_farm = $this->_farm;
        $alldata = $this->m_transaksi->group_layout_kavling($kode_farm, $this->_grup_farm);
        $data ['layout'] = isset($alldata['data_kavling']) ? $alldata['data_kavling'] : array();
        $data ['data_kolom'] = isset($alldata['data_kavling']) ? $alldata['data_kolom'] : array();
        $data ['max_no_baris'] = isset($alldata['data_kavling']) ? $alldata['max_no_baris'] : array();
        $data ['gudang'] = $this->m_transaksi->get_gudang_in_farm($kode_farm);
        $this->load->view($this->_grup_farm.'/layout_gudang', $data);
    }

    public function set_gudang() {
        $kode_farm = $this->_farm;
        $kode_gudang = $this->input->post('kode_gudang');
        $data['stok_gudang'] = $this->m_transaksi->stok_gudang($kode_farm, $kode_gudang);
        $this->load->view($this->_grup_farm.'/stok_gudang', $data);
    }

    public function view($offset = 0) {
        // $data ['all_items'] = array (
        $tab_active = $this->input->post('tab_active');
        $data ['tab_active'] = (empty($tab_active)) ? 1 : $tab_active;
        $tanggal_kirim = $this->input->post('tanggal_kirim');
        $data ['items'] = array();
        $data ['items_result'] = [];
        if (!empty($tanggal_kirim)) {
            foreach ($data ['items'] as $key => $value) {
                if (date('d M Y', strtotime($key)) == date('d M Y', strtotime($tanggal_kirim))) {
                    $data ['items_result'] = $value;
                }
            }
        } else {
            foreach ($data ['items'] as $key1 => $value2) {
                foreach ($value2 as $key2 => $value2) {
                    $data ['items_result'] [] = $value2;
                }
            }
        }
       
        $this->load->view($this->_grup_farm.'/transaksi', $data);
    }

    public function get_data_surat_jalan($op=null, $do=null){
        $no_op = (empty($op)) ? $this->input->post('no_op') : $op;
        $nomor_do = (empty($do)) ? $this->input->post('nomor_do') : $do;
        $r = $this->sinkron_surat_jalan($no_op, $nomor_do);
        $this->result = $r;
        echo json_encode($this->result);
    }

    public function sinkron_surat_jalan($no_op = NULL, $nomor_do = NULL){
        $data = array(
                'no_op' => $no_op #$this->input->post('no_op')
                , 'nomor_do' => $nomor_do
                #'no_op' => '26711/14' #$no_op #$this->input->post('no_op')
                #'no_op' => '00959/13'
        );
        $r = Modules::run('cproduksi/suratjalan/data_surat_jalan',$data);
        
        return $r;

    }

    public function daftar_do_dan_sj(){
        $no_do = $this->input->post('nomor_do');
        $do_params = implode("','", $no_do);
        $data ['list'] = $this->m_transaksi->daftar_do_dan_sj($this->_farm, $do_params);
        $this->load->view($this->_grup_farm.'/daftar_do_dan_sj',$data);
    }

    public function simpan_penerimaan(){
        $data = $this->input->post('data');
        #echo '<pre>'.print_r(json_decode($data, 1)).'</pre>';
        $nomor_do = $this->input->post('nomor_do');
        $all_data = $this->input->post('all_data');
        $keterangan_nopol = $this->input->post('keterangan_nopol');
        #echo '<pre>'.print_r(json_decode($data, 1)).'</pre>';
        $this->save_data_timbang($nomor_do, $all_data);
        $result = $this->m_transaksi->simpan_penerimaan($this->_farm, $data, $this->_user,$keterangan_nopol);
        #echo json_encode($result);

        if($result['result'] == 1){
            $path = $this ->_filetimbang;
            $nomor_do = str_replace('/', '-', $nomor_do);
            $filename = $path.'/'.$nomor_do.".log";
            unlink($filename);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function simpan_attachment(){
        $kode_pakan = $this->input->post('kode_pakan');
        $no_do = $this->input->post('nomor_do');
        $fileContent = $_FILES["attachment"]["tmp_name"];
        $result = $this->m_transaksi->simpan_attachment($this->_farm, $no_do, $kode_pakan, $fileContent);
        echo (isset($result['kode_pakan'])) ? json_encode($result) : 0;
    }
    public function get_all_data(){
        $nomor_do = $this->input->post('nomor_do');
        $nomor_do = str_replace(",", "','", $nomor_do);
        $result = $this->m_transaksi->verifikasi_do($this->_farm, $nomor_do);
        echo json_encode($result);
    }
    public function ganti_kavling(){
        $kode_flok = $this->input->post('kode_flok');
        $kode_barang = $this->input->post('kode_barang');
        $data_kavling = $this->input->post('data_kavling');
        $data['data_ke_detail_pakan'] = $this->input->post('data_ke_detail_pakan');
        #echo $this->input->post('data_ke_detail_pakan');
        $data['data_ke_detail'] = $this->input->post('data_ke_detail');
        #echo $this->input->post('data_ke_detail');
        $all_data = $this->m_transaksi->ganti_kavling($this->_farm, $kode_flok, $kode_barang);
        $j = count($all_data);
        $list = $all_data;
        if(isset($data_kavling)){
            foreach ($data_kavling as $key => $value) {
                $list[$j]= $value;
                $j++;
            }
        }
        $r = [];
        foreach ($list as $key => $value) {
            $r[$value['no_kavling']]['detail'][]=$value;
            $r[$value['no_kavling']]['berat']=0;
            $r[$value['no_kavling']]['sak']=0;
        }
        foreach ($r as $key1 => $value1) {
            $jumlah_berat = 0;
            $jumlah_sak = 0;
            foreach ($value1['detail'] as $key2 => $value2) {
                $jumlah_berat = $jumlah_berat+$value2['berat'];
                $jumlah_sak = $jumlah_sak+$value2['timbangan_sak'];
            }
            $r[$key1]['berat'] = $jumlah_berat;
            $r[$key1]['sak'] = $jumlah_sak;
        }
        $data['list'] = $r;
        $this->load->view($this->_grup_farm.'/ganti_kavling',$data);
    }
    public function ganti_hand_pallet(){
        $data['data_ke_detail_pakan'] = $this->input->post('data_ke_detail_pakan');
        $data['data_ke_detail'] = $this->input->post('data_ke_detail');
        $data['data_hand_pallet'] = $this->m_transaksi->ganti_hand_pallet($this->_farm);
        $this->load->view($this->_grup_farm.'/ganti_hand_pallet',$data);
    }



    function save_data_timbang($nomor_do = NULL, $all_data = NULL){
        $path = $this -> _filetimbang;
        $nomor_do = (empty($nomor_do)) ? $this->input->post('nomor_do') : $nomor_do;
        $nomor_do = str_replace('/', '-', $nomor_do);
        $all_data = (empty($all_data)) ? $this->input->post('all_data') : $all_data;
        $filename = $path.'/'.$nomor_do.".log";
        $file = fopen($filename,"w");
        #$all_data = 'ok';
        fwrite($file,$all_data);
        fclose($file);
        //unlink($filename);
    }

    public function get_data_timbang($nomor_do){
        $line = '';
        $nomor_do = str_replace('/', '-', $nomor_do);
        $filename = $this -> _filetimbang.'/'.$nomor_do.'.log';
        if (file_exists($filename)) {
            #echo $filename;
            $fh = fopen($filename,'r');
            #while ($line = fgets($fh)) {
            $line = fgets($fh);
            $line = str_replace(' ', '', $line);
            #}
            fclose($fh);
        }
        return $line;
    }

    public function showImage(){
        $this->load->model('penerimaan_pakan/m_verifikasi_do_pakan','mvdo');
        $nomerDo = $this->input->get('do');
        $do = $this->mvdo->as_array()->get_by(array('no_do'=> $nomerDo));
        $this->output
            ->set_content_type('jpeg')
            ->set_output(file_get_contents($do['PHOTO']));
    }
}
