<?php
class M_config extends MY_Model{
	
	public function __construct(){
		parent::__construct();
		$this->_table = 'sys_config';
		$this->_primary_key= 'config_id';
	}
	
	public function getDate(){
		$sql = <<<SQL
		select convert(date,current_timestamp) tglserver,current_timestamp saatini
SQL;
		return $this->db->query($sql);
	}
}