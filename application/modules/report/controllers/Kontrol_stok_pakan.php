<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Kontrol_stok_pakan extends MY_Controller {
	protected $result;
	protected $_user;
	protected $grup_farm;
	
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_report','report');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_user = $this->session->userdata('kode_user');
	}

	public function index($farm = null) {
		$data = array();
		$this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan/index',$data);
	}

    public function kontrol($farm = null) {
		$data = array();
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan',$data);
	}

}
