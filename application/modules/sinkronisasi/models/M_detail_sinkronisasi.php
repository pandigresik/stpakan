<?php
class M_detail_sinkronisasi extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'detail_sinkronisasi_e';
	}
}