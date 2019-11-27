<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
  hanya digunakan di budidaya saja
 */
class Forecast_pp extends MY_Controller {
	protected $result;
	protected $_user;
	protected $grup_farm;
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_report_forecast_pp','rfp');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		//$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->grup_farm = 'bdy';
		$this->load->helper('stpakan');
	}
	public function index() {
		$data['list_farm'] = Modules::run('forecast/forecast/list_farm',$this->grup_farm,NULL);
		$this->load->view('report/bdy/forecast_pp',$data);
	}

	public function forecast_vs_pp($kodefarm){
		$this->load->model('report/m_report_forecast_pp','rfp');
		$data_kirim = $this->rfp->forecast_vs_pp($kodefarm)->result_array();
		
		$this->load->view('report/bdy/tabel_forecast_pp',array('data'=>$data_kirim));
	}
}
