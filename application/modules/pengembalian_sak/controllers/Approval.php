<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Approval extends MY_Controller {
	protected $result;
	protected $_user;
	private $grup_farm;
	private $_canApprove = array('KDV','PD');
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'stpakan' );
		$this->_user = $this->session->userdata ( 'kode_user' );
		$this->result = array (
				'status' => 0,
				'content' => ''
		);
		$this->load->model('pengembalian_sak/m_pengembalian_sak','mps');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
	}
	public function index() {
	/*	$list_farm = $this->list_farm();
		$data_list_farm = array();
		$data_list_farm['list_farm'] = $list_farm;
		$data['list_farm_retur'] = $this->load->view('pengembalian_sak/list_farm_retur',$data_list_farm,true);
	*/	$this->load->view('pengembalian_sak/'.$this->grup_farm.'/approval');
	}

	private function list_farm(){
		$this->load->model('forecast/m_forecast','mf');
		$x = $this->mf->list_farm()->result_array();
		/* pastikan hanya farm yang aktif saja */
		$tmp = array();
		foreach($x as $l){
			$kf = $l['kode_farm'];
			if(!isset($tmp[$kf])){
				$tmp[$kf] = $l;
			}
		}
		return $tmp;
	}

	public function list_farm_retur(){
		$status = $this->input->post('status');
		$tanggal_cari = $this->input->post('tanggal');
		$custom_param = null;
		$tanggal_params = array('startDate' => null, 'endDate' => null);
		if(!empty($tanggal_cari['operand'])){
			switch($tanggal_cari['operand']){
				case 'between':
					$custom_param = $tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\' and \''.$tanggal_cari['endDate'].'\'';
					$tanggal_params = array('startDate' => $tanggal_cari['startDate'], 'endDate'=>$tanggal_cari['endDate']);
					break;
				case '<=':
					$custom_param= $tanggal_cari['operand'].' \''.$tanggal_cari['endDate'].'\'';
					$tanggal_params = array('startDate' => null, 'endDate'=>$tanggal_cari['endDate']);
					break;
				case '>=':
					$custom_param= $tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\'';
					$tanggal_params = array('startDate' => $tanggal_cari['startDate'], 'endDate'=>null);
					break;
			}
		}
		$list_farm = $this->list_farm();
		$data_list_farm = array();
		$data_list_farm['list_farm'] = $list_farm;
		$jml_retur = $this->mps->get_list_retur_approval($status,$custom_param)->result_array();
		$data_list_farm['jml_retur'] = $this->grouping_arr($jml_retur,'jml_retur');
		$data_list_farm['status'] = $status;
		$data_list_farm['tanggal'] = $tanggal_params;
		$this->load->view('pengembalian_sak/'.$this->grup_farm.'/list_farm_retur',$data_list_farm);
	}

	public function view_retur_sak(){
		$kode_farm = $this->input->post('kode_farm');
		$kode_siklus = $this->input->post('kode_siklus');
		$status = $this->input->post('status');
		$tanggal_cari = $this->input->post('tanggal');
		$custom_param = null;
		if(!empty($tanggal_cari['operand'])){
			switch($tanggal_cari['operand']){
				case 'between':
					$custom_param = $tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\' and \''.$tanggal_cari['endDate'].'\'';
					break;
				case '<=':
					$custom_param= $tanggal_cari['operand'].' \''.$tanggal_cari['endDate'].'\'';
					break;
				case '>=':
					$custom_param= $tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\'';
					break;
			}
		}

		$data['list_retur'] = $this->mps->view_retur_sak($kode_farm,$kode_siklus,$status,$custom_param)->result_array();
		$data['sisa_hutang'] = $this->grouping_sisa($this->get_sisa_hutang($kode_farm));
	//	$data['sisa_hutang'] = $this->get_sisa_hutang($kode_farm);
	//	echo '<pre>';print_r($data['sisa_hutang']);die();
		$this->load->view('pengembalian_sak/'.$this->grup_farm.'/header_retur',$data);
	}

	public function detail_retur_sak(){
		$no_pengembalian = $this->input->post('no_pengembalian');
		$keputusan = $this->input->post('keputusan');
		$reviewkadept = $this->input->post('reviewkadept');
		$noreg = substr($no_pengembalian,0,strlen($no_pengembalian) - 4);
		$nourut = substr($no_pengembalian,-3,3);
		$list_retur = $this->mps->view_pengembalian($noreg,$nourut)->result_array();
		$tmp = array();
		foreach($list_retur as $r){
			$kode_pakan = $r['KODE_PAKAN'];
			$jk = $r['JENIS_KELAMIN'];
			$nama_pakan = $r['NAMA_BARANG'];
			$jml_sak = $r['JML_SAK'];
			$brt_sak = $r['BRT_SAK'];
			if(!isset($tmp[$kode_pakan])){
				$tmp[$kode_pakan] = array();
			}
			if(!isset($tmp[$kode_pakan][$jk])){
				$tmp[$kode_pakan][$jk] = array();
				$tmp[$kode_pakan][$jk]['jml_retur']= 0;
				$tmp[$kode_pakan][$jk]['brt_sak']= 0;
				$tmp[$kode_pakan][$jk]['nama_pakan']= $nama_pakan;
				$tmp[$kode_pakan][$jk]['retur_sak_kosong_item_pakan']= $r['ID'];
				$tmp[$kode_pakan][$jk]['keterangan']= $r['KETERANGAN'];
			}
			$tmp[$kode_pakan][$jk]['jml_retur'] += $jml_sak;
			$tmp[$kode_pakan][$jk]['brt_sak'] += $brt_sak;
		}
		$user_level = $this->session->userdata('level_user');
		$data['approve'] = in_array($user_level, $this->_canApprove);
		$data['list_retur'] = $tmp;
		$data['keputusan'] = $keputusan;
		$data['reviewkadept'] = $reviewkadept;
		/* grouping menjadi perpakan, perjeniskelamin */
		$this->load->view('pengembalian_sak/'.$this->grup_farm.'/detail_retur',$data);
	}

	public function approve_retur_sak(){
		$id_retur = $this->input->post('id_retur');
		$keputusan = $this->input->post('keputusan');
		$this->load->model('pengembalian_sak/m_review_hutang_retur_sak','mrhrs');
		$tgl_server = Modules::run('home/home/getDateServer');
		$data['waktu'] = $tgl_server->saatini;
		$data['keputusan'] = $keputusan;
		$data['reviewer'] = $this->_user;
		if($this->mrhrs->update_by(array('retur_sak_kosong' => $id_retur),$data)){
			$this->result['status'] = 1 ;
			$this->result['content'] = convertElemenTglWaktuIndonesia($tgl_server->saatini);
		}
		// echo json_encode($this->result);
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function review_retur_sak(){
		$data = $this->input->post('data');
		$this->load->model('pengembalian_sak/m_retur_sak_kosong_item_pakan','rskip');

		$tgl_server = Modules::run('home/home/getDateServer');
		$tgl_review_kadep = $tgl_server->saatini;
		$this->db->trans_start();
		foreach($data as $d){
			$this->rskip->update_by(array('id' => $d['retur_sak_kosong_item_pakan']),array('tgl_review_kadep' => $tgl_review_kadep,'keterangan' => $d['keterangan']));
		}
		$this->db->trans_complete();
		if($this->db->trans_status() === FALSE){
			$this->result['content'] = 'Gagal menyimpan..';
		}
		else{
			$this->result['status'] = 1;
			$this->result['content'] = convertElemenTglWaktuIndonesia($tgl_server->saatini);
		}

		// echo json_encode($this->result);
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	/* $par adalah parameter yang akan digrouping hasilnya */
	private function grouping_arr($arr,$par){
		$t = array();
		foreach($arr as $r){
			$kf = $r['kode_farm'];
			if(!isset($t[$kf])){
				$t[$kf] = $r[$par];
			}
		}
		return $t;
	}

	private function grouping_sisa($arr){
		$t = array();
		foreach($arr as $r){
			$kf = $r['no_reg'];
			if(!isset($t[$kf])){
				$t[$kf] = $r['hutang_retur'];
			}
		}
		return $t;
	}

	private function grouping_sisa_hutang($arr){
		$t = array();
		foreach($arr as $r){
			$n = $r['no_reg'];
			$kb = $r['kode_barang'];
			if(!isset($t[$n])){
				$t[$n] = array();
			}
			if(!isset($t[$n][$kb])){
				$t[$n][$kb] = $r['hutang_retur'];
			}
		}
		return $t;
	}


	private function get_sisa_hutang($kodefarm){
		return $this->mps->get_sisa_hutang($kodefarm)->result_array();
	}
}
