<?php
class M_alokasi_pakan_lolos_untuk_farm extends MY_Model{
	protected $_table;
//	protected $primary_key = 'id';
	public function __construct(){
		parent::__construct();
		$this->_table = 'alokasi_pakan_lolos_untuk_farm';
	}
}
