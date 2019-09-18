<?php
class M_forecast_d extends MY_Model{
	protected $_table;
//	protected $before_create = array('forecast');
	public function __construct(){
		parent::__construct();
		$this->_table = 'forecast_d';
	}

}
