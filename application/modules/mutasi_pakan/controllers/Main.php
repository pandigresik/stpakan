<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Main extends MY_Controller {

    protected $_level_user;
    protected $_user;
    protected $_farm;
    protected $_grup_farm;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array(
            'm_main'
        ));
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_level_user = strtolower($this->session->userdata('level_user'));
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
    }

    public function index() {
        $data['no_mutasi'] = $this->input->post('no_mutasi');
        $kode_farm = ($this->_level_user == 'kf') ? $this->_farm : '';
        $data['base_url'] = base_url();
        $data['grup_farm'] = $this->_grup_farm;
        $data['data_kandang'] = $this->m_main->data_kandang($kode_farm);
        $data['data_farm'] = $this->m_main->data_farm();
        $data['level_user'] = $this->_level_user;
        $this->load->view($this->_grup_farm . '/main_' . $this->_level_user, $data);
    }

    public function get_data_kandang() {
        $kode_farm = $this->input->post('farm');
        $data = $this->m_main->data_kandang($kode_farm);
        echo json_encode($data);
    }

    public function get_data_mutasi_pakan() {
        $p_kode_farm = $this->input->post('farm');
        $kode_farm = ($this->_level_user == 'kf') ? $this->_farm : $p_kode_farm;
        $data['level_user'] = $this->_level_user;
        $belum_tindak_lanjut = $this->input->post('belum_tindak_lanjut');
        $no_mutasi = $this->input->post('no_mutasi');
        $tanggal = $this->input->post('tanggal');
        $tanggal_awal = $this->input->post('tanggal_awal');
        $tanggal_akhir = $this->input->post('tanggal_akhir');
        $kandang = $this->input->post('kandang');
        $data['data_mutasi_pakan'] = $this->m_main->get_data_mutasi_pakan($this->_level_user, $kode_farm, $belum_tindak_lanjut, $no_mutasi, $tanggal, $tanggal_awal, $tanggal_akhir, $kandang);
        $this->load->view($this->_grup_farm . '/daftar_mutasi_pakan', $data);
    }

    public function get_detail_mutasi_pakan() {
        $kode_farm = $this->input->post('kode_farm');
        $no_mutasi = $this->input->post('no_mutasi');
        $detail_mutasi_pakan = $this->m_main->get_detail_mutasi_pakan($kode_farm, $no_mutasi);
        $data = [];
        foreach ($detail_mutasi_pakan as $key => $value) {
            $data[$value['kandang_asal']]['detail'][] = $value;
            $data[$value['kandang_asal']]['kandang_asal'] = $value['kandang_asal'];
            $data[$value['kandang_asal']]['jenis_pakan'] = $value['jenis_pakan'];
            $data[$value['kandang_asal']]['umur_asal'] = $value['umur_asal'];
            $data[$value['kandang_asal']]['dh_asal'] = $value['dh_asal'];
            $data[$value['kandang_asal']]['fcr_asal'] = $value['fcr_asal'];
            $data[$value['kandang_asal']]['ip_asal'] = $value['ip_asal'];
            $data[$value['kandang_asal']]['dh_asal_red'] = $value['dh_asal_red'];
            $data[$value['kandang_asal']]['fcr_asal_red'] = $value['fcr_asal_red'];
            $data[$value['kandang_asal']]['ip_asal_red'] = $value['ip_asal_red'];
        }
        $data['detail_mutasi_pakan'] = $data;
        $data['rowspan'] = count($detail_mutasi_pakan);
        $this->load->view($this->_grup_farm . '/detail_mutasi_pakan', $data);
    }

    public function tindak_lanjut() {
        $no_mutasi = $this->input->post('no_mutasi');
        $keputusan = $this->input->post('keputusan');
        $alasan = $this->input->post('alasan');
        $data = $this->m_main->tindak_lanjut($this->_user, $no_mutasi, $keputusan, $alasan);
        #echo json_encode($data);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function ack() {
        $no_mutasi = $this->input->post('no_mutasi');
        $keputusan = 'ACK';
        $data = $this->m_main->ack($this->_user, $no_mutasi, $keputusan);
        echo json_encode($data);
    }

}
