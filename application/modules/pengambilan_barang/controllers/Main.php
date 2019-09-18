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
            'm_transaksi'
        ));
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
    }

    public function index() {        
        $data['grup_farm'] = $this->_grup_farm;
        $data ['list'] = array();
        $tglServer = Modules::run('home/home/getDateServer');
		$data['hari_ini'] = $tglServer->saatini;
        $this->load->view($this->_grup_farm.'/main', $data);
    }

    public function get_data_order_kandang($tanggal_kirim_awal = NULL, $tanggal_kirim_akhir = NULL) {
        switch ($this->_grup_farm) {
            case 'bdy':
                $data = $this->m_transaksi->get_data_order_kandang_bdy($tanggal_kirim_awal, $tanggal_kirim_akhir, $this->_farm);
                break;

            default:
                $data = $this->m_transaksi->get_data_order_kandang($tanggal_kirim_awal, $tanggal_kirim_akhir, $checkbox_normal, $checkbox_retur, $checkbox_belum_proses, $this->_farm);
                break;
        }

        return $data;
    }

    public function simpan_generate_permintaan() {
        $kode_farm = $this->_farm; // $this->input->post ( 'kode_farm' );
        switch ($this->_grup_farm) {
            case 'bdy':                
                $kode_flok = $this->input->post('kode_flok');                
                $tanggal_kebutuhan = $this->input->post('tanggal_kebutuhan');
                $result = $this->m_transaksi->generate_picking_list($kode_farm, $kode_flok, $tanggal_kebutuhan, $this->_user);
              
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($result));
                break;

            default:
                $tanggal_kirim = $this->input->post('tanggal_kirim');
                $tanggal_kirim = date('Y-m-d', strtotime(convert_month($tanggal_kirim, 2)));
                $tanggal_kebutuhan_awal = $this->input->post('tanggal_kebutuhan_awal');
                $tanggal_kebutuhan_awal = date('Y-m-d', strtotime(convert_month($tanggal_kebutuhan_awal, 2)));
                $tanggal_kebutuhan_akhir = $this->input->post('tanggal_kebutuhan_akhir');
                $tanggal_kebutuhan_akhir = date('Y-m-d', strtotime(convert_month($tanggal_kebutuhan_akhir, 2)));
                $result = $this->m_transaksi->simpan_generate_permintaan($kode_farm, $tanggal_kirim, $tanggal_kebutuhan_awal, $tanggal_kebutuhan_akhir, $this->_user);
                echo json_encode($result);
                break;
        }

    }

    public function get_data_pengambilan() {
        $tanggal_kirim_awal = $this->input->post('tanggal_kirim_awal');
        $tanggal_kirim_akhir = $this->input->post('tanggal_kirim_akhir');     
        $tanggal_kirim_awal = (empty($tanggal_kirim_awal)) ? date('Y-m-d') : $tanggal_kirim_awal;
        $tanggal_kirim_akhir = (empty($tanggal_kirim_akhir)) ? date('Y-m-d') : $tanggal_kirim_akhir;
        $data ['list'] = $this->get_data_order_kandang($tanggal_kirim_awal, $tanggal_kirim_akhir);        
        switch ($this->_grup_farm) {
            case 'bdy':
                $data['kode_farm'] = $this->_farm;
                $this->load->view($this->_grup_farm.'/picking_list', $data);
                break;

            default:
                echo ($tanggal_kirim_awal > $tanggal_kirim_akhir) ? 2 : json_encode($data ['list']);
                break;
        }
    }

    public function check_sisa_hutang_sak(){
        $result = array('status' => 0, 'message' => '', 'content' => array());
        $flok = $this->input->get('flok');
        $kodefarm = $this->session->userdata('kode_farm');
        /* cek hutang sak */
        $this->load->model('pengembalian_sak/m_pengembalian_sak','mps');
		$hutang_sak = $this->mps->get_sisa_hutang($kodefarm,$flok)->result();
		$punya_hutang = array();
		foreach($hutang_sak as $hs){
			if($hs->hutang_retur > 0){
				$punya_hutang[$hs->no_reg] = $hs->hutang_retur;
			}
		}
		/* jika gak punya hutang boleh langsung */
		if(empty($punya_hutang)){
			$result['status'] = 1;
        }
        
        echo json_encode($result);
    }

}
