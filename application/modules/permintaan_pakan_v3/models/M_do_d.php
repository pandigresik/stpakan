<?php
class M_do_d extends CI_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'do_d';
	}
	
	public function insert($param = array()){
		
	//	$this->db->insert($this->_table,$param);
	//	log_message('error',$this->db->last_query());
		$sql = <<<SQL
		exec dbo.SIMPAN_DO_D '{$param['kode_farm']}','{$param['no_do']}','{$param['no_op']}', '{$param['kode_barang']}', '{$param['jml_muat']}', '{$param['tgl_kirim']}'		
SQL;
		$this->db->query($sql);
		
	}
	
	public function update($no_pp,$data){
		$this->db->where('no_lpb',$no_pp);
		$this->db->update($this->_table,$data);
	}
}