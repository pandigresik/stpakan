<?php
class M_review_penggantian_pakan_rusak extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'review_penggantian_pakan_rusak';
		$this->_primary_key = 'id';
	}
}