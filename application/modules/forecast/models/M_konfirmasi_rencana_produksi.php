<?php
class M_konfirmasi_rencana_produksi extends MY_Model{
	protected $_table;
	protected $_user; 
	public function __construct(){
		parent::__construct();
		$this->_table = 'konfirmasi_rencana_produksi';
		$this->_user = $this->session->userdata('kode_user');
	}
}