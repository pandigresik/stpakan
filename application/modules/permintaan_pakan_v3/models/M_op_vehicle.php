<?php
class M_op_vehicle extends CI_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'op_vehicle';
	}
	
	public function insert($param = array()){
		$this->db->insert($this->_table,$param);
	//	log_message('error',$this->db->last_query());
	}
	
	public function update($no_pp,$data){
		$this->db->where('no_lpb',$no_pp);
		$this->db->update($this->_table,$data);
	}
	
	public function detail_ekspedisi($no_op,$tgl_kirim){
		$sql = <<<SQL
		select do.no_do  
			,opv.kode_barang
			,opv.kode_ekspedisi
			,opv.jml_kirim
			,opv.no_urut
			,do.status_do		
		from OP_VEHICLE opv
		inner join do 
			on do.NO_OP = opv.NO_OP and do.NO_URUT = opv.NO_URUT and do.status_do != 'D'
		where opv.no_op = '{$no_op}'
		and opv.tgl_kirim = '{$tgl_kirim}'
SQL;
		
		return $this->db->query($sql);
	}
	
	public function detail_ekspedisi_tglkirim($kode_farm,$tgl_kirim){
		$sql = <<<SQL
		select do.no_do
			,opv.no_op  
			,opv.kode_barang
			,opv.kode_ekspedisi
			,opv.jml_kirim
			,opv.no_urut
			,do.status_do	
			,opv.no_polisi rit	
		from OP_VEHICLE opv
		inner join do 
		--	on do.NO_OP = opv.NO_OP and do.NO_URUT = opv.NO_URUT and do.status_do != 'D' and do.kode_farm = '{$kode_farm}'
			on do.NO_OP = opv.NO_OP and do.NO_URUT = opv.NO_URUT and do.kode_farm = '{$kode_farm}'
		where opv.tgl_kirim = '{$tgl_kirim}'
SQL;
		
		return $this->db->query($sql);
	}
}