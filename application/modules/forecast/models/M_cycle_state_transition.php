<?php
class M_cycle_state_transition extends MY_Model{
	protected $_table;
	private $_user;

	public function __construct(){
		parent::__construct();
		$this->_table = 'cycle_state_transition';
	}

}
