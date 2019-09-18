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
            'm_main'
        ));
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->session->userdata('kode_farm');
    }

    public function index() {
        $data ['list'] = $this->get_data_order_kandang();
        $this->load->view('main', $data);
    }

    public function get_data_order_kandang($tanggal_kirim_awal = NULL, $tanggal_kirim_akhir = NULL) {
        return $this->m_main->get_data_order_kandang($tanggal_kirim_awal, $tanggal_kirim_akhir, $this->_farm);
    }

    public function baru() {
        $farm = $this->input->post('kode_farm');
        $farm = empty($farm) ? $this->_farm : $farm;
        $data ['farm'] = $farm;
        $data ['no_order'] = $this->input->post('no_order');
        $data ['status'] = $this->input->post('status');
        $min_tgl = $this->m_main->get_min_tanggal_doc_in($farm);
        $max_tgl = $this->m_main->get_max_tanggal_kebutuhan($farm);
        $tgl = empty($max_tgl ['tgl_kirim']) ? $min_tgl : $max_tgl;
        $tanggal_kebutuhan_awal = $this->input->post('tanggal_kebutuhan_awal');
        $data ['tanggal_kirim'] = empty($tanggal_kebutuhan_awal) ? convert_month($tgl ['tgl_kirim'], 1) : $this->input->post('tanggal_kirim');
        $data ['tanggal_kebutuhan_awal'] = empty($tanggal_kebutuhan_awal) ? convert_month($tgl ['max_tgl_kebutuhan'], 1) : $this->input->post('tanggal_kebutuhan_awal');
        $data ['tanggal_kebutuhan_akhir'] = $this->input->post('tanggal_kebutuhan_akhir');
        // $data ['items'] = $this->get_data_barang ();
        $this->load->view('baru', $data);
    }

    public function tambah_barang() {
        $data ['tambah_barang'] = $this->m_main->tambah_barang($this->_farm);
        $this->load->view('tambah_barang', $data);
    }

    public function update_analisa_performance_kandang() {
        $data = $this->input->post('data');
        $result = $this->m_main->update_analisa_performance_kandang($data [0]);
        echo $result;
    }

    public function simpan_analisa_performance_kandang() {
        $data = $this->input->post('data');
        $result = $this->m_main->simpan_analisa_performance_kandang($data [0], $this->_user);
        echo json_encode($result);
    }

    public function release_analisa_performance_kandang() {
        $data = $this->input->post('data');
        $result = $this->m_main->release_analisa_performance_kandang($data [0], $this->_user);
        echo $result;
    }

    public function cek_order_kandang() {
        $data = $this->input->post('data');
        $result = $this->m_main->cek_order_kandang($data [0] ['data_order_kandang'] [0]);
        echo json_encode(isset($result ['ada']) ? $result : array(
                    'ada' => 0
                        ) );
    }

    public function tambah_daftar_barang() {
        $kode_barang = $this->input->post('kode_barang');
        $data ['data_terakhir'] = $this->input->post('data_terakhir');
        $kode_farm = $this->_farm; // $this->input->post ( 'kode_farm' );
        $tanggal_kebutuhan_awal = $this->input->post('tanggal_kebutuhan_awal');
        $tanggal_kebutuhan_awal = date('Y-m-d', strtotime(convert_month($tanggal_kebutuhan_awal, 2)));
        $tanggal_kebutuhan_akhir = $this->input->post('tanggal_kebutuhan_akhir');
        $tanggal_kebutuhan_akhir = date('Y-m-d', strtotime(convert_month($tanggal_kebutuhan_akhir, 2)));
        $data ['barang'] = $this->m_main->group_daftar_barang($kode_farm, $tanggal_kebutuhan_awal, $tanggal_kebutuhan_akhir, $kode_barang);

        $this->load->view('tambah_daftar_barang', $data);
    }

    public function generate() {
        $kode_farm = $this->_farm; // $this->input->post ( 'kode_farm' );
        $tanggal_kebutuhan_awal = $this->input->post('tanggal_kebutuhan_awal');
        $tanggal_kebutuhan_awal = date('Y-m-d', strtotime(convert_month($tanggal_kebutuhan_awal, 2)));
        $tanggal_kebutuhan_akhir = $this->input->post('tanggal_kebutuhan_akhir');
        $tanggal_kebutuhan_akhir = date('Y-m-d', strtotime(convert_month($tanggal_kebutuhan_akhir, 2)));
        $data ['barang'] = $this->m_main->group_daftar_barang($kode_farm, $tanggal_kebutuhan_awal, $tanggal_kebutuhan_akhir);

        $this->load->view('daftar_barang', $data);
    }

    public function get_data_barang() {
        return array(
            array(
                'kode_barang' => '1174-13-33',
                'nama_barang' => 'P 2 COBB',
                'bentuk' => 'TEPUNG',
                'jumlah_keb' => '32',
                'jumlah_pp' => '32'
            ),
            array(
                'kode_barang' => '1174-14-23',
                'nama_barang' => 'PJB COBB',
                'bentuk' => 'TEPUNG',
                'jumlah_keb' => '62',
                'jumlah_pp' => '57'
            ),
            array(
                'kode_barang' => '1174-13-34',
                'nama_barang' => 'P 3 COBB',
                'bentuk' => 'TEPUNG',
                'jumlah_keb' => '186',
                'jumlah_pp' => '181'
            )
        );
    }

}
