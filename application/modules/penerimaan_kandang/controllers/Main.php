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

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array(
            'm_transaksi'
        ));
        $this->load->helper('stpakan');
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->session->userdata('kode_farm');
    }

    public function index() {
        $data ['list'] = $this->get_data_order_kandang();
        $this->load->view('main', $data);
    }

    public function get_data_order_kandang($tanggal_kirim_awal = NULL, $tanggal_kirim_akhir = NULL) {
        return $this->m_transaksi->get_data_order_kandang($tanggal_kirim_awal, $tanggal_kirim_akhir, $this->_farm);
    }

    public function get_data_pengambilan() {
        $tanggal_kirim_awal = $this->input->post('tanggal_kirim_awal');
        $tanggal_kirim_akhir = $this->input->post('tanggal_kirim_akhir');
        $tanggal_kirim_awal = date('Y-m-d', strtotime(convert_month($tanggal_kirim_awal, 2)));
        $tanggal_kirim_akhir = date('Y-m-d', strtotime(convert_month($tanggal_kirim_akhir, 2)));
        $data ['list'] = $this->get_data_order_kandang($tanggal_kirim_awal, $tanggal_kirim_akhir);
        echo ($tanggal_kirim_awal>$tanggal_kirim_akhir) ? 2 : json_encode($data ['list']);
    }

    /*
     * public function index() {
     * $data ['list'] = $this->all_data ();
     * $this->load->view ( 'main', $data );
     * }
     * public function all_data() {
     * return array (
     * array (
     * 'tgl_kirim' => '05 Apr 2015',
     * 'tgl_kebutuhan' => '06 Apr 2015 s/d 07 Apr 2015',
     * 'jumlah_kebutuhan' => '270',
     * 'jumlah_belum_proses' => '270',
     * 'cetak' => 'Cetak Picking List'
     * ),
     * array (
     * 'tgl_kirim' => '08 Apr 2015',
     * 'tgl_kebutuhan' => '09 Apr 2015 s/d 10 Apr 2015',
     * 'jumlah_kebutuhan' => '100',
     * 'jumlah_belum_proses' => '100',
     * 'cetak' => 'Cetak Picking List'
     * )
     * );
     * }
     * public function get_data_penerimaan() {
     * $tanggal_kirim = $this->input->post ( 'tanggal_kirim' );
     * (empty ( $tanggal_kirim )) ? date ( 'd M Y' ) : $tanggal_kirim;
     * $data ['all_list'] = $this->all_data ();
     * $data ['list'] = [ ];
     * foreach ( $data ['all_list'] as $key => $value ) {
     * if (date ( 'd M Y', strtotime ( $tanggal_kirim ) ) == date ( 'd M Y', strtotime ( $value ['tgl_kirim'] ) )) {
     * $data ['list'] [] = $value;
     * }
     * }
     * echo json_encode ( $data ['list'] );
     * }
     */
}
