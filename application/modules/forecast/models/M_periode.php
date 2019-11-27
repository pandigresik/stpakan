<?php
class M_periode extends MY_Model{
	protected $_table; 
	private $_user;
	public $primary_key = 'kode_siklus';
	public function __construct(){
		parent::__construct();
		$this->_table = 'm_periode';
		$this->_user = $this->session->userdata('kode_user');
	}
}