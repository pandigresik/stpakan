<?php
class M_pallet extends MY_Model{
	protected $_table;
	private $_user;
	public $primary_key = '';
	public function __construct(){
		parent::__construct();
		$this->_table = 'm_pallet';
		$this->_user = $this->session->userdata('kode_user');
	}
}
