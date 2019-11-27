<?php
class M_retur_sak_kosong_item_timbang_pakan extends MY_Model{
	
	public function __construct(){
		parent::__construct();
		$this->_table = 'retur_sak_kosong_item_timbang_pakan';
		$this->_primary_key= 'id';
	}
	
}