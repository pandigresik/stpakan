<?php
class M_pembayaran extends MY_Model{
	protected $primary_key;
	private $dbSqlServer;
	public function __construct(){
		parent::__construct();
		$this->_table = 'pembayaran';
		$this->primary_key= 'no_so';
		$this->dbSqlServer = $this->load->database('default', TRUE);
	}
}
