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
    protected $_h_tanggal_kebutuhan;
    protected $_grup_farm;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array(
            'm_main'
            , 'm_transaksi'
        ));
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_namauser = $this->session->userdata('nama_user');
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
        $this->_h_tanggal_kebutuhan = 2;
    }

    public function index() {
        $data['grup_farm'] = $this->_grup_farm;
        $data['data_server'] = $this->m_transaksi->data_server($this->_farm, $this->_h_tanggal_kebutuhan);
        $data['data_jenis_pakan'] = array(); #$this->m_transaksi->data_jenis_pakan();
        $data['data_kandang'] = $this->m_main->data_kandang($this->_farm);
        $data['mutasi_pakan'] = array();
        $data['aksi'] = 'new';
        $this->load->view($this->_grup_farm . '/transaksi', $data);
    }

    public function revisi() {
        $no_mutasi = $this->input->post('no_mutasi');
        $no_reg = $this->input->post('no_reg');
        $data['grup_farm'] = $this->_grup_farm;
        $data['aksi'] = 'revisi';
        $mutasi_pakan = $this->m_transaksi->mutasi_pakan($no_mutasi);
        $data['mutasi_pakan'] = $mutasi_pakan;
        $data['data_server'] = array(
            'tanggal_server' => $mutasi_pakan['TGL_PEMBERIAN'],
            'tanggal_server_besok_lusa' => $mutasi_pakan['TGL_KEBUTUHAN'],
            'farm' => $mutasi_pakan['NAMA_FARM']
        );
        $data['data_jenis_pakan'] = $this->m_transaksi->data_jenis_pakan($no_reg);
        $data['data_kandang'] = $this->m_main->data_kandang($this->_farm);
        $this->load->view($this->_grup_farm . '/transaksi', $data);
    }

    public function get_daftar_kandang() {
        $no_mutasi = $this->input->post('no_mutasi');
        $mutasi_pakan_d = $this->m_transaksi->mutasi_pakan_d($no_mutasi);
        $array_kandang = array();
        $str_kandang= "";
        if(count($mutasi_pakan_d) > 0){
            foreach ($mutasi_pakan_d as $key => $value) {
                array_push($array_kandang, $value['kode_kandang']);
            }
            $str_kandang = implode(",", $array_kandang);
            $str_kandang = str_replace(",", "','", $str_kandang);
        }

        $tanggal_pemberian = $this->input->post('tanggal_pemberian');
        $nama_farm = $this->input->post('nama_farm');
        $jenis_pakan = $this->input->post('jenis_pakan');
        $kuantitas_pemberian_pakan = $this->input->post('kuantitas_pemberian_pakan');
        $data['kuantitas_pemberian_pakan'] = $kuantitas_pemberian_pakan;
        $tanggal_kebutuhan = $this->input->post('tanggal_kebutuhan');
        $kandang_asal = $this->input->post('kandang_asal');
        $daftar_kandang = $this->m_transaksi->get_daftar_kandang($no_mutasi, $str_kandang, $this->_farm, $tanggal_pemberian, $nama_farm, $jenis_pakan, $kuantitas_pemberian_pakan, $tanggal_kebutuhan, $kandang_asal);


        $data['daftar_kandang'] = $daftar_kandang;
        $this->load->view($this->_grup_farm . '/daftar_kandang', $data);
    }

    public function get_konsumsi_per_ekor() {
        $tanggal_pemberian = $this->input->post('tanggal_pemberian');
        $nama_farm = $this->input->post('nama_farm');
        $jenis_pakan = $this->input->post('jenis_pakan');
        $kuantitas_pemberian_pakan = $this->input->post('kuantitas_pemberian_pakan');
        $tanggal_kebutuhan = $this->input->post('tanggal_kebutuhan');
        $kandang_asal = $this->input->post('kandang_asal');
        $data = $this->m_transaksi->get_konsumsi_per_ekor($this->_farm, $tanggal_pemberian, $nama_farm, $jenis_pakan, $kuantitas_pemberian_pakan, $tanggal_kebutuhan, $kandang_asal);
        echo json_encode($data);
    }

    public function simpan_mutasi() {
        $data = $this->input->post('data');
        $result = $this->m_transaksi->simpan_mutasi($this->_farm, $this->_user, $data);
        #echo json_encode($result);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function cek_lhk() {
        $no_reg = $this->input->post('no_reg_asal');
        $tgl_transaksi = $this->input->post('tgl_transaksi');
        $result = $this->m_transaksi->cek_lhk($no_reg, $tgl_transaksi);
        $result['tgl_transaksi'] = date('Y-m-d',strtotime($tgl_transaksi));
        echo json_encode($result);
    }

    public function data_jenis_pakan() {
        $no_reg = $this->input->post('no_reg_asal');
        $result = $this->m_transaksi->data_jenis_pakan($no_reg);
        echo json_encode($result);
    }

}
