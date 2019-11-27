<?php
class M_std_budidaya extends MY_Model{
	protected $_table; 
	protected $primary_key = 'kode_std_budidaya';
	public function __construct(){
		parent::__construct();
		$this->_table = 'm_std_budidaya';
	}
}