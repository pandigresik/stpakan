<?php
class M_op_d extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'op_d';
	}
	public function get($param = array()){
		if(!empty($param)){
			$this->db->where($param);
		}
		return $this->db->get($this->_table);
	}
		
	public function get_op_sinkron($where = array()){
		foreach($where as $w){
			$this->db->or_where($w);
		}
		$this->db->select(array('op_d.NO_OP','op_d.KODE_BARANG','cast(sum(jml_order) as int) as jml_order','cast(sum(jml_order) * 50 as int) as kg_order','sum(harga_total) as harga'));
		$this->db->from($this->_table);
		$this->db->join('op', 'op.no_op = op_d.no_op');
		$this->db->group_by(array('op_d.KODE_BARANG','op_d.NO_OP'));
		return $this->db->get();
	}
}