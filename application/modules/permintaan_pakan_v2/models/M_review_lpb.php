<?php
class M_review_lpb extends MY_Model{
	protected $_table;
	protected $return_type = 'array';
	public function __construct(){
		parent::__construct();
		$this->_table = 'review_lpb_budidaya';
	}
}