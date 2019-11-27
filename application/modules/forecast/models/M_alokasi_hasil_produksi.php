<?php
class M_alokasi_hasil_produksi extends MY_Model{
	protected $_table;
	protected $primary_key = 'id';
	public function __construct(){
		parent::__construct();
		$this->_table = 'alokasi_hasil_produksi';
	}
}
