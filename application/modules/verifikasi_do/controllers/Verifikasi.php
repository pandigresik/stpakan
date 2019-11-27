<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Verifikasi extends MY_Controller{
	protected $result;
	protected $_user;
	
	public function __construct(){
		parent::__construct();
		$this->result = array('status' => 0, 'content'=> '');
		
		$this->load->helper('stpakan');
	}
	public function index(){
		$this->load->view('verifikasi_do/verifikasi');
	}
	
	public function cek_do(){
		$this->load->model('permintaan_pakan/m_pembelian_pakan','mpp');
		$no_do = $this->input->post('no_do');
		$do = $this->mpp->detail_do($no_do)->result_array();
		$data = array();
		$data['message'] = null;
		if(empty($do)){
			$data['message'] = 'Nomor DO '.$no_do.' tidak ditemukan';
		}
		else{
			if($do[0]['status_do'] == 'C'){
				$data['message'] = 'Nomor DO '.$no_do.' sudah diverifikasi';
			}
		}
		$data['do'] = $do;
		
		$this->load->view('verifikasi_do/detail_do',$data);
	}

	public function update_status_do(){
		$tglServer = Modules::run('home/home/getDateServer');
		$this->_tglSekarang = $tglServer->saatini;
		$this->load->model('permintaan_pakan/m_do','md');
		$no_do = $this->input->post('no_do');
		$where_do = array('no_do'=> $no_do ,'status_do' => 'N');
		$this->md->update($where_do,array('status_do' => 'C','tgl_verifikasi'=>$this->_tglSekarang));
		if($this->db->affected_rows() > 0){
			$this->result['status'] = 1;
		}
		echo json_encode($this->result);
	}
}
