<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Transaksi extends MY_Controller {

    protected $_user;
    protected $_namauser;
    protected $_farm;
    protected $_berat;
    protected $_grup_farm;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array(
            'm_transaksi'
        ));
        $this->load->helper('stpakan');
        $this->load->config('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_namauser = $this->session->userdata('nama_user');
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_berat = $this->config->item('berat_standart');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
    }

    public function index() {
        $this->view();
    }

    public function get_data_detail_order_kandang($no_order, $kode_farm) {
        return $this->m_transaksi->get_data_detail_order_kandang($no_order, $kode_farm, $this->_berat);
    }

    public function simpan_konfirmasi() {
        $data = $this->input->post('data');
        $result = $this->m_transaksi->simpan_konfirmasi($data [0], $this->_farm, $this->_user);
        echo json_encode($result);
    }

    public function cek_kode_verifikasi_kavling() {
        $data = $this->input->post('data');
        $result = $this->m_transaksi->cek_kode_verifikasi_kavling($data [0]);
        echo (count($result) > 0) ? 1 : 0;
    }

    public function cek_konversi() {
        $berat = $this->input->post('berat');
        $result = $this->m_transaksi->cek_konversi($this->_berat, $berat);

        echo (count($result) > 0) ? json_encode($result [0]) : json_encode($this->m_transaksi->cek_diluar_toleransi($this->_berat, $berat));
    }

    public function cetak_picking_list() {
        $data['grup_farm'] = $this->_grup_farm;
        $no_order = $this->input->post('no_order');
        $kode_farm = $this->_farm;
        $data ['items'] = $this->get_data_detail_order_kandang($no_order, $kode_farm);
        $data ['no_order'] = $no_order;
        $this->load->view($this->_grup_farm.'/cetak_picking_list', $data);
    }

    public function cetak_daftar_pengambilan() {
        $no_order = $this->input->get('no_order');
        $pick = $this->input->get('pick');
        $kode_farm = $this->_farm;

        $this->load->library('Pdf');
        $pdf = new Pdf('P', PDF_UNIT, 'A5', true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $data = $this->get_data_detail_order_kandang($no_order, $kode_farm);

        if ($pick == 1) {
            $html = $this->load->view('pengambilan_barang/'.$this->_grup_farm.'/cetak_picking_list_pdf', array(
                'items' => $data,
                'grup_farm' => $this->_grup_farm
                    ), true);
        } else {
            $html = $this->load->view('pengambilan_barang/'.$this->_grup_farm.'/cetak_sending_list_pdf', array(
                'items' => $data,
                'grup_farm' => $this->_grup_farm
                    ), true);
        }

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('pengambilan_barang.pdf', 'I');
    }

    public function view($offset = 0) {
        $data['grup_farm'] = $this->_grup_farm;
        $no_order = $this->input->post('no_order');
        $summaryData = $this->input->post('summary');
        $data ['no_order'] = $no_order;
        $data ['kode_penerima'] = $this->_user;
        $data ['penerima'] = $this->_namauser;
        $kode_farm = $this->_farm;       
        $data ['user_gudang'] = $this->m_transaksi->get_user_gudang($kode_farm);
        $data ['json_user_gudang'] = json_encode($data ['user_gudang']);
        if ($this->_grup_farm == 'bdy'){
            $data_pengambilan = $this->m_transaksi->susun_data_detail_order_kandang_bdy($no_order, $kode_farm, $this->_berat);
            $data ['items_result_transaksi'] = $data_pengambilan['data'];
       
        }        
        $this->load->model('master/m_hand_pallet','mhp');
        $data['jml_hand_pallet'] = count($this->mhp->as_array()->get_many_by(array('status_pallet'=>'N')));

        $lockTimbangan = $this->db->select(array('kode_config','value'))->where(array('kode_config' => '_lock_timbangan','kode_farm' => $kode_farm,'context' => 'pengambilan_barang','status' => '1'))->get('SYS_CONFIG_GENERAL')->row_array();        
        $data['lockTimbangan'] = empty($lockTimbangan) ? 1 : $lockTimbangan['value'];
        $data['summary'] = $summaryData;
        $this->load->view($this->_grup_farm.'/transaksi', $data);
    }

    function simpan_data(){
        $grup_farm = $this->_grup_farm;
        $kode_farm = $this->_farm;
        $data = $this->input->post('data');
        $result = $this->m_transaksi->simpan_data($kode_farm, $data, $this->_user);
        #echo json_encode($result);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    function get_data_riwayat_pengambilan(){
        $kode_farm = $this->_farm;
        $no_reg = $this->input->post('no_reg');
        $data['riwayat'] = $this->m_transaksi->get_data_riwayat_pengambilan($kode_farm, $no_reg);
        $this->load->view($this->_grup_farm.'/riwayat_pengambilan', $data);
    }

    function cek_pallet(){
        $grup_farm = $this->_grup_farm;
        $kode_farm = $this->_farm;
        $no_pallet = $this->input->post('no_pallet');
        $zak = $this->input->post('zak');
        $result = $this->m_transaksi->cek_pallet($kode_farm, $no_pallet, $zak);
        echo json_encode($result);
    }

    function simpan_transaksi_verifikasi(){
        $kode_farm = $this->_farm;
        $user = $this->_user;
        $transaction = $this->input->post('transaction');
        $kode_flok = $this->input->post('kode_flok');
        $result = $this->m_transaksi->simpan_transaksi_verifikasi($kode_farm, $user, $transaction, $kode_flok);
        echo json_encode($result);
    }

    function cek_verifikasi(){
        $kode_farm = $this->_farm;
        $user = $this->_user;
        $date_transaction = $this->input->post('date_transaction');
        $kode_flok = $this->input->post('kode_flok');
        /** jika ada parameter noreg, maka pastikan yang melakukan finger adalah operator kandang tersebut */
        $no_reg = $this->input->post('noreg');
        $level = $this->input->post('level');
        $lockFinger = $this->config->item('lockFinger');
        if(!$lockFinger){
            $result = array('kode_pegawai' => 'PG0001', 'nama_pegawai' => 'BPM', 'verificator' => 'PG0001');
            if(!empty($level)){
                $result['status'] = 1;
                $result['match'] = 1;
            }
        }else{
            if(!empty($level)){
                $result = $this->m_transaksi->cek_verifikasi($kode_farm, $user, $date_transaction, $kode_flok);
                if(!empty($result['kode_pegawai'])){
                    $result['status'] = 1;
                    $result['match'] = 0;
                    /** cek diplotting pelaksana */
                    $cari_petugas = array(
                        'no_reg' => $no_reg                         
                    );
                    /** jika $level == KAFARM, maka bandingkan dengan user yang sedang login */
                    if($level == 'KAFARM'){
                        if($result['kode_pegawai'] == $this->_user){
                            $result['match'] = 1;
                        }
                    }else{
                        $plotting = $this->db->where($cari_petugas)->get('m_ploting_pelaksana')->result_array();
                        if(!empty($plotting)){
                            foreach($plotting as $_p){
                                if($result['kode_pegawai'] == $_p[$level]){
                                    $result['match'] = 1;
                                }
                            }
                        }
                    }
                    
                }else{
                    $result = array('status' => 0);
                }                
            }else{
                $result = $this->m_transaksi->cek_verifikasi($kode_farm, $user, $date_transaction, $kode_flok);
            }            
        }
                
        echo json_encode($result);
    }
}
