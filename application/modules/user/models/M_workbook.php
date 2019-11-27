<?php
class M_workbook extends MY_Model{
	private $dbSqlServer ;
	
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database('sqlserver77',TRUE);
		$this->setConnection($this->dbSqlServer);
		$this->_table = 'workbook';
		$this->_primary_key= 'id';
	}
}