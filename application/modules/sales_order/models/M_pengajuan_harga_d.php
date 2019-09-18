<?php
class M_pengajuan_harga_d extends MY_Model{
	protected $primary_key;
	public function __construct(){
		parent::__construct();
		$this->_table = 'pengajuan_harga_d';		
	}
}
