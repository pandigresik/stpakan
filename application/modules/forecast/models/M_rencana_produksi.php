<?php
class M_rencana_produksi extends MY_Model{
	protected $_table; 
	protected $primary_key = 'id';
	public function __construct(){
		parent::__construct();
		$this->_table = 'rencana_produksi';
	}
}