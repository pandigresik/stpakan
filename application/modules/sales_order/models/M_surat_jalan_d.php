<?php
class M_surat_jalan_d extends MY_Model{
	protected $primary_key;
	
	public function __construct(){
		parent::__construct();
		$this->_table = 'surat_jalan_d';			
	}
}
