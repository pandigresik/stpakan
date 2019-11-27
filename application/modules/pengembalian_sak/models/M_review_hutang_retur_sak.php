<?php
class M_review_hutang_retur_sak extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'review_hutang_retur_sak';
		$this->_primary_key = 'id';
	}
}