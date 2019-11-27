<?php
class M_bapdocsj extends MY_Model{
	protected $_table;
	private $_user;
	protected $primary_key;
	public function __construct(){
		parent::__construct();
		$this->_table = 'bap_doc_sj';
		$this->_user = $this->session->userdata('kode_user');
	}
}
