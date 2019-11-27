<?php
class M_estimasi_tanggal_produksi extends MY_Model{
	protected $_table; 
	protected $primary_key = 'id';
	public function __construct(){
		parent::__construct();
		$this->_table = 'estimasi_tanggal_produksi';
	}
}