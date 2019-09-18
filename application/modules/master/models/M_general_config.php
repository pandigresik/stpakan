<?php
class M_general_config extends MY_Model{
	protected $_table = 'sys_config_general';
	public function __construct(){
		parent::__construct();
	}	

	public function listContext(){
		$this->db->distinct()->select(array('context'));
		return $this->as_array()->get_all();
	}
}
