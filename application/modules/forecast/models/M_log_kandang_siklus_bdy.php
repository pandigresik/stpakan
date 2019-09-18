<?php
class M_log_kandang_siklus_bdy extends MY_Model{
	protected $_table; 
	private $_user;
	public function __construct(){
		parent::__construct();
		$this->_table = 'log_kandang_siklus_bdy';
		$this->_user = $this->session->userdata('kode_user');
	}
	
	public function get_no_urut($where){
		$no_urut = $this->db->select_max('no_urut','max')->where($where)->get($this->_table)->row();
		$urut = !empty($no_urut->max) ? $no_urut->max + 1 : 1;  
		return $urut;  
	}
}