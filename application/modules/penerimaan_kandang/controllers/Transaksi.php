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
        $this->view();
    }

    public function get_data_detail_order_kandang($no_order, $kode_farm) {
        return $this->m_transaksi->get_data_detail_order_kandang($no_order, $kode_farm);
    }

    public function simpan_konfirmasi() {
        $data = $this->input->post('data');
        $result = $this->m_transaksi->simpan_konfirmasi($data, $this->_user);
        echo $result;
    }

    public function cek_verifikasi_rfid_card() {
        $data = $this->input->post('data');
        $result = $this->m_transaksi->cek_verifikasi_rfid_card($data [0]);
        echo (count($result) > 0) ? 1 : 0;
    }

    public function cetak_picking_list() {
        $no_order = $this->input->post('no_order');
        $kode_farm = $this->_farm;
        $data ['items'] = $this->get_data_detail_order_kandang($no_order, $kode_farm);
        $this->load->view('cetak_picking_list', $data);
    }

    public function cetak_daftar_penerimaan() {
        $no_order = $this->input->get('no_order');
        $kode_farm = $this->_farm;

        $this->load->library('Pdf');
        $pdf = new Pdf('P', PDF_UNIT, 'A5', true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $data = $this->get_data_detail_order_kandang($no_order, $kode_farm);

        $html = $this->load->view('penerimaan_kandang/cetak_put_list_pdf', array(
            'items' => $data
                ), true);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('penerimaan_kandang.pdf', 'I');
    }

    public function view($offset = 0) {
        $no_order = $this->input->post('no_order');
        $kode_farm = $this->_farm;
        $tab_active = $this->input->post('tab_active');
        $data ['tab_active'] = (empty($tab_active)) ? 1 : $tab_active;
        $data['user_gudang'] = $this->m_transaksi->get_user_gudang($kode_farm);
        $data ['items_result'] = $this->get_data_detail_order_kandang($no_order, $kode_farm);
        $jml = count($data ['items_result']);
        $config ['base_url'] = base_url() . '#penerimaan_kandang/transaksi/view';
        $config ['total_rows'] = $jml;
        $config ['per_page'] = 15;
        $config ['uri_segment'] = 3;
        $config ['full_tag_open'] = "<ul class='pagination'>";
        $config ['full_tag_close'] = "</ul>";
        $config ['num_tag_open'] = '<li>';
        $config ['num_tag_close'] = '</li>';
        $config ['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config ['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config ['next_tag_open'] = "<li>";
        $config ['next_tagl_close'] = "</li>";
        $config ['prev_tag_open'] = "<li>";
        $config ['prev_tagl_close'] = "</li>";
        $config ['first_tag_open'] = "<li>";
        $config ['first_tagl_close'] = "</li>";
        $config ['last_tag_open'] = "<li>";
        $config ['last_tagl_close'] = "</li>";
        $this->pagination->initialize($config);
        $data ['halaman'] = $this->pagination->create_links();
        $data ['offset'] = $offset;

        $data ['items'] = $data ['items_result'];
        /*
         * $data ['items'] = [ ];
         * $tmp_on_pick = '';
         * $tmp_pick = '';
         * foreach ( $data ['items_result'] as $key => $value ) {
         * if (($tmp_on_pick != $value ['tmp_jumlah']) || ($tmp_pick != $value ['jumlah'])) {
         * $data ['items'] [] = $value;
         * }
         * $tmp_on_pick = $value ['jumlah'];
         * $tmp_pick = $value ['tmp_jumlah'];
         * }
         */
        /*
         * foreach ( $data ['items_result'] as $key => $value ) {
         * echo $offset;
         * if (($key >= $offset) and ($key < $offset + $config ['per_page'])) {
         * $data ['items'] [] = $value;
         * }
         * }
         */
        $this->load->view('transaksi', $data);
    }

    /*
     * public function index() {
     * $this->view ();
     * }
     * public function view($offset = 0) {
     * // $data ['all_items'] = array (
     * $tab_active = $this->input->post ( 'tab_active' );
     * $data ['tab_active'] = (empty ( $tab_active )) ? 1 : $tab_active;
     * $tanggal_kirim = $this->input->post ( 'tanggal_kirim' );
     * $data ['items'] = array (
     * '05 Apr 2015' => array (
     * array (
     * 'kode_kandang' => 'K003',
     * 'kode_barang' => '1174-13-33',
     * 'nama_barang' => 'P 3 COBB',
     * 'jumlah' => '10',
     * 'bentuk_pakan' => 'TEPUNG',
     * 'remark' => '1'
     * ),
     * array (
     * 'kode_kandang' => 'K004',
     * 'kode_barang' => '1174-13-33',
     * 'nama_barang' => 'P 3 COBB',
     * 'jumlah' => '',
     * 'bentuk_pakan' => 'TEPUNG',
     * 'remark' => ''
     * ),
     * array (
     * 'kode_kandang' => 'K005',
     * 'kode_barang' => '1174-13-33',
     * 'nama_barang' => 'P 3 COBB',
     * 'jumlah' => '',
     * 'bentuk_pakan' => 'TEPUNG',
     * 'remark' => ''
     * ),
     * array (
     * 'kode_kandang' => 'K006',
     * 'kode_barang' => '1174-13-33',
     * 'nama_barang' => 'P 3 COBB',
     * 'jumlah' => '',
     * 'bentuk_pakan' => 'TEPUNG',
     * 'remark' => ''
     * )
     * ),
     * '08 Apr 2015' => array (
     * array (
     * 'kode_kandang' => 'K007',
     * 'kode_barang' => '1174-13-33',
     * 'nama_barang' => 'P 3 COBB',
     * 'jumlah' => '',
     * 'bentuk_pakan' => 'TEPUNG',
     * 'remark' => ''
     * ),
     * array (
     * 'kode_kandang' => 'K008',
     * 'kode_barang' => '1174-13-33',
     * 'nama_barang' => 'P 3 COBB',
     * 'jumlah' => '',
     * 'bentuk_pakan' => 'TEPUNG',
     * 'remark' => ''
     * ),
     * array (
     * 'kode_kandang' => 'K009',
     * 'kode_barang' => '1174-13-33',
     * 'nama_barang' => 'P 3 COBB',
     * 'jumlah' => '',
     * 'bentuk_pakan' => 'TEPUNG',
     * 'remark' => ''
     * )
     * )
     * );
     * $data ['items_result'] = [ ];
     * if (! empty ( $tanggal_kirim )) {
     * foreach ( $data ['items'] as $key => $value ) {
     * if (date ( 'd M Y', strtotime ( $key ) ) == date ( 'd M Y', strtotime ( $tanggal_kirim ) )) {
     * $data ['items_result'] = $value;
     * }
     * }
     * } else {
     * foreach ( $data ['items'] as $key1 => $value2 ) {
     * foreach ( $value2 as $key2 => $value2 ) {
     * $data ['items_result'] [] = $value2;
     * }
     * }
     * }
     *
     * $jml = count ( $data ['items_result'] );
     * $config ['base_url'] = base_url () . '#penerimaan_kandang/transaksi/view';
     * $config ['total_rows'] = $jml;
     * $config ['per_page'] = 2;
     * $config ['uri_segment'] = 2;
     * $config ['full_tag_open'] = "<ul class='pagination'>";
     * $config ['full_tag_close'] = "</ul>";
     * $config ['num_tag_open'] = '<li>';
     * $config ['num_tag_close'] = '</li>';
     * $config ['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
     * $config ['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
     * $config ['next_tag_open'] = "<li>";
     * $config ['next_tagl_close'] = "</li>";
     * $config ['prev_tag_open'] = "<li>";
     * $config ['prev_tagl_close'] = "</li>";
     * $config ['first_tag_open'] = "<li>";
     * $config ['first_tagl_close'] = "</li>";
     * $config ['last_tag_open'] = "<li>";
     * $config ['last_tagl_close'] = "</li>";
     * /*
     * $config ['full_tag_open'] = "<ul class='pagination pagination-sm' style='position:relative; top:-25px;'>";
     * $config ['full_tag_close'] = "</ul>";
     * $config ['num_tag_open'] = '<li>';
     * $config ['num_tag_close'] = '</li>';
     * $config ['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
     * $config ['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
     * $config ['next_tag_open'] = "<li>";
     * $config ['next_tagl_close'] = "</li>";
     * $config ['prev_tag_open'] = "<li>";
     * $config ['prev_tagl_close'] = "</li>";
     * $config ['first_tag_open'] = "<li>";
     * $config ['first_tagl_close'] = "</li>";
     * $config ['last_tag_open'] = "<li>";
     * $config ['last_tagl_close'] = "</li>";
     *
     * $this->pagination->initialize ( $config );
     * $data ['halaman'] = $this->pagination->create_links ();
     * $data ['offset'] = $offset;
     * $data ['items'] = [ ];
     * foreach ( $data ['items_result'] as $key => $value ) {
     * if ($key >= $offset and $key < $offset + $config ['per_page']) {
     * $data ['items'] [] = $value;
     * }
     * }
     *
     * $this->load->view ( 'transaksi', $data );
     * }
     */
}
