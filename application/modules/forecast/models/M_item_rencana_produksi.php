<?php
class M_item_rencana_produksi extends MY_Model{
	protected $_table;
	protected $primary_key = 'id';
	public function __construct(){
		parent::__construct();
		$this->_table = 'item_rencana_produksi';
	}
}
