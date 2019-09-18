<?php
class M_sales_order_d extends MY_Model{
	protected $primary_key;	
	public function __construct(){
		parent::__construct();
		$this->_table = 'sales_order_d';
		$this->primary_key= 'no_so';
	}	
}
