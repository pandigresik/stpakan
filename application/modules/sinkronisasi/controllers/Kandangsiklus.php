<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Kandangsiklus extends MX_Controller {
	protected $result;
	protected $_user;
	private $_total_op;
	public function __construct() {
		parent::__construct ();
		$this->load->library('format');
		$this->load->helper('file');
	}
	public function index() {
		$kandang = $this->db
				->where(array('status_siklus' => 'O'))
				->get('kandang_siklus')
				->result_array();
		$format_d = new Format;
		$format_d->set_data($kandang);
		write_file('sinkron_file/kandangsiklus.csv', $format_d->to_csv(),'w');
			
	}
	
}
