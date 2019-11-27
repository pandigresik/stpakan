<?php
class M_hatchery extends MY_Model{
	protected $_table;
	private $_user;
	protected $primary_key;
	public function __construct(){
		parent::__construct();
		$this->_table = 'm_hatchery';
		$this->primary_key = 'kode_hatchery';
		$this->_user = $this->session->userdata('kode_user');
	}
}
