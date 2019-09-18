<?php
class M_detail_forecast_pakan extends MY_Model{
	protected $_table;
	private $_user;
	public function __construct(){
		parent::__construct();
		$this->_table = 'detail_forecast_pakan';
		$this->_user = $this->session->userdata('kode_user');
	}
}

	