<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Review extends MY_Controller {
	protected $_user;
	protected $_username;
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'stpakan' );
		$this->_user = $this->session->userdata ( 'kode_user' );
		$this->_username = $this->session->userdata ( 'nama_user' );
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->load->model('review_pakan_rusak/m_review_pakan_rusak','mppr');
	}
	public function index() {
		$this->load->view('review_pakan_rusak/review');
	}
	public function header_pakan_rusak(){
		$kodefarm = $this->session->userdata ('kode_farm');
		$tindak_lanjut = $this->input->post('tindak_lanjut');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$no_retur = $this->input->post('no_retur');
		$kandang = $this->input->post('kandang');
	
		$data['pakan_rusak'] = $this->mppr->header_pakan_rusak($kodefarm, $tindak_lanjut, $startDate, $endDate, $no_retur, $kandang);
		
		$this->load->view('review_pakan_rusak/header_review', $data);
	}
	public function detail_pakan_rusak(){
		$kodefarm = $this->session->userdata ('kode_farm');
		$no_reg = $this->input->post('no_reg');
		$no_urut = $this->input->post('no_urut');
	
		$data['pakan_rusak'] = $this->mppr->detail_pakan_rusak($kodefarm, $no_reg, $no_urut);
		
		$this->load->view('review_pakan_rusak/detail_review', $data);
	}
	public function simpan(){
		$kodefarm = $this->session->userdata ('kode_farm');
		$no_reg = $this->input->post('no_reg');
		$no_urut = $this->input->post('no_urut');
		$transaksi = $this->input->post('transaksi');
		$alasan = $this->input->post('alasan');
	
		$result = ($this->_grup_farm == 'bdy') ? $this->mppr->simpan_review_pakan_rusak_bdy($kodefarm, $this->_user, $no_reg, $no_urut, $transaksi, $alasan) : $this->mppr->simpan_review_pakan_rusak($kodefarm, $this->_user, $no_reg, $no_urut, $transaksi, $alasan);
		#echo json_encode($result);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
	}
	public function download(){
		$no_retur = $this->input->get('no_retur');
		#echo $no_retur;
		$result = $this->mppr->data_retur($no_retur);
        header('Content-type: application/msword');
        echo $result['ATTACHMENT'];
	}
}
