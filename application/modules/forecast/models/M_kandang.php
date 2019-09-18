<?php
class M_kandang extends MY_Model{
	protected $_table; 
	public function __construct(){
		parent::__construct();
		$this->_table = 'm_kandang';
	}
}