<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Main extends MY_Controller {
    protected $_user;
    protected $_farm;
    protected $_grup_farm;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array(
            'm_transaksi'
        ));
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
    }

    public function index() {
        $data['grup_farm']=$this->_grup_farm;
        $this->load->view("daftar_do", $data);
    }

    public function get_pagination() {
        $offset = 10;
        $page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
        $is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

        $do_belum_diterima = (($this->input->post("do_belum_diterima")) and $is_search == true) ? $this->input->post("do_belum_diterima") : null;
        $tanggal_kirim_awal = (($this->input->post("tanggal_kirim_awal")) and $is_search == true) ? $this->input->post("tanggal_kirim_awal") : null;
        $tanggal_kirim_akhir = (($this->input->post("tanggal_kirim_akhir")) and $is_search == true) ? $this->input->post("tanggal_kirim_akhir") : null;
        $no_op = (($this->input->post("no_op")) and $is_search == true) ? $this->input->post("no_op") : null;
        $no_do = (($this->input->post("no_do")) and $is_search == true) ? $this->input->post("no_do") : null;
        $no_sj = (($this->input->post("no_sj")) and $is_search == true) ? $this->input->post("no_sj") : null;
        $nama_ekspedisi = (($this->input->post("nama_ekspedisi")) and $is_search == true) ? $this->input->post("nama_ekspedisi") : null;
        $tanggal_kirim = (($this->input->post("tanggal_kirim")) and $is_search == true) ? $this->input->post("tanggal_kirim") : null;

        $data_semua_do = $this->m_transaksi->get_data_do(null, null, $this->_farm, $do_belum_diterima, $tanggal_kirim_awal, $tanggal_kirim_akhir, $no_op, $no_do, $no_sj, $nama_ekspedisi, $tanggal_kirim);

        $data_do = $this->m_transaksi->get_data_do(($page_number * $offset), ($page_number + 1) * $offset, $this->_farm, $do_belum_diterima, $tanggal_kirim_awal, $tanggal_kirim_akhir, $no_op, $no_do, $no_sj, $nama_ekspedisi, $tanggal_kirim);

        $total = count($data_semua_do);
        $pages = ceil($total / $offset);

        if (count($data_do) > 0) {
            $data = array(
                'TotalRows' => $pages,
                'Rows' => $data_do
            );

            $this->output->set_content_type('application/json');
            echo json_encode(array(
                $data
            ));
        } else {
            $data = array(
                'TotalRows' => $pages,
                'Rows' => $data_do
            );
            echo json_encode(array($data));
        }

        exit();
    }

    public function get_daftar_penerimaan($tanggal_kirim_awal = NULL, $tanggal_kirim_akhir = NULL) {
        $result = $this->m_transaksi->get_daftar_penerimaan($tanggal_kirim_awal, $tanggal_kirim_akhir, $this->_farm);
        return $result;
    }

    public function susun_daftar_penerimaan($data){
        $result = [];
        foreach ($data as $key => $item) {
            $result [$item ['no_sj']] = array(
                'no_sj' => $item ['no_sj'],
                'no_op' => $item ['no_op'],
                'no_berita_acara' => $item ['no_berita_acara'],
                'no_penerimaan' => $item ['no_penerimaan'],
                'ekspedisi' => $item ['ekspedisi'],
                'tanggal_kirim' => $item ['tanggal_kirim'],
                'no_do' => $item ['no_do'],
                'no_kendaraan_kirim' => $item ['no_kendaraan_kirim'],
                'no_spm' => $item ['no_spm']
            );
        }
        foreach ($data as $key => $item) {
            $result [$item ['no_sj']]['detail_barang'][] = array(
                'no_berita_acara' => $item ['no_berita_acara'],
                'kode_barang' => $item ['kode_barang'],
                'jml_sj' => $item ['jml_sj']
            );
        }
        return $result;
    }

    public function get_data_daftar_penerimaan() {
        $tanggal_kirim_awal = $this->input->post('tanggal_kirim_awal');
        $tanggal_kirim_akhir = $this->input->post('tanggal_kirim_akhir');
        $tanggal_kirim_awal = date('Y-m-d', strtotime(convert_month($tanggal_kirim_awal, 2)));
        $tanggal_kirim_akhir = date('Y-m-d', strtotime(convert_month($tanggal_kirim_akhir, 2)));
        $data ['list'] = $this->susun_daftar_penerimaan($this->get_daftar_penerimaan($tanggal_kirim_awal, $tanggal_kirim_akhir));
        if($tanggal_kirim_awal>$tanggal_kirim_akhir){
            echo 2;
        }
        else{
            $this->load->view('daftar_penerimaan_pakan',$data);
        }
    }

    public function get_data_penerimaan() {
        $tanggal_kirim = $this->input->post('tanggal_kirim');
        (empty($tanggal_kirim)) ? date('d M Y') : $tanggal_kirim;
        $data ['all_list'] = array(
            array(
                'tgl_kirim' => '05 Apr 2015',
                'tgl_kebutuhan' => '06 Apr 2015 s/d 07 Apr 2015',
                'jumlah_kebutuhan' => '270',
                'jumlah_belum_proses' => '270',
                'cetak' => 'Cetak Picking List'
            ),
            array(
                'tgl_kirim' => '08 Apr 2015',
                'tgl_kebutuhan' => '09 Apr 2015 s/d 10 Apr 2015',
                'jumlah_kebutuhan' => '100',
                'jumlah_belum_proses' => '100',
                'cetak' => 'Cetak Picking List'
            )
        );
        $data ['list'] = [];
        foreach ($data ['all_list'] as $key => $value) {
            if (date('d M Y', strtotime($tanggal_kirim)) == date('d M Y', strtotime($value ['tgl_kirim']))) {
                $data ['list'] [] = $value;
            }
        }
        echo json_encode($data ['list']);
    }

}
