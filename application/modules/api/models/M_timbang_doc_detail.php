<?php
class M_timbang_doc_detail extends MY_Model{
	protected $_table = 'timbang_doc_detail';
	protected $before_create = array('setNourut');
	public function __construct(){
		parent::__construct();
	}

	public function setNourut($row){
		$nomerAkhir = 0;
		$max = $this->db->select_max('no_urut')->where(['no_reg' => $row['no_reg']])->get($this->_table)->row_array();
		if(!empty($max)){
			$nomerAkhir = $max['no_urut'];
		}
		$nomerAkhir++;
		$row['no_urut'] = $nomerAkhir;
		return $row;
	}
}