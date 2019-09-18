<?php
class M_sinkronisasi extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'sinkronisasi';
	}
}