<?php
class M_pegawai extends MY_Model{

	private $_user;
	protected $before_create = array('no_urut');
	public function __construct(){
		parent::__construct();
		$this->_table = 'M_PEGAWAI';
		$this->_primary_key= 'kode_pegawai';
		$this->_user = $this->session->userdata('kode_user');
	}
}
