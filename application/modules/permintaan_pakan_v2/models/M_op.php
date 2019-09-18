<?php
class M_op extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'op';
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
		$this->db->select('op.*,m_farm.KODE_PELANGGAN');
		$this->db->from($this->_table);
		$this->db->join('m_farm','m_farm.kode_farm = op.kode_farm');
		return $this->db->get();
	}

	public function header_op($no_op){
		$sql = <<<SQL
		select  stuff (
					(select distinct ', '+ convert(varchar(max),op_d.tgl_kirim)
					from op_d
					where no_op = op.no_op
					for xml path (''))
					,1,1,'') tgl_kirim
			,mf.NAMA_FARM nama_farm
			,op.NO_OP no_op
		from op
		inner join op_d opd
			on opd.NO_OP = op.NO_OP
		inner join m_farm mf
			on mf.KODE_FARM = op.KODE_FARM
		where op.no_op_logistik = '{$no_op}'
		group by mf.NAMA_FARM
			,op.NO_OP
SQL;
		return $this->db->query($sql);
	}

	public function header_op_budidaya($no_pp){
		$sql = <<<SQL
		select  stuff (
					(select distinct ', '+ convert(varchar(max),op_d.tgl_kirim)
					from op_d
					where no_op = op.no_op
					for xml path (''))
					,1,1,'') tgl_kirim
			,mf.NAMA_FARM nama_farm
			,op.NO_OP no_op
		from op
		inner join op_d opd
			on opd.NO_OP = op.NO_OP
		inner join m_farm mf
			on mf.KODE_FARM = op.KODE_FARM
		where op.no_lpb = '{$no_pp}'
		group by mf.NAMA_FARM
			,op.NO_OP
SQL;
		return $this->db->query($sql);
	}

	public function header_op_kk($where = array()){
		foreach($where as $w){
			$this->db->or_where($w);
		}
		$this->db->select('op.*,m_farm.*');
		$this->db->from($this->_table);
		$this->db->join('m_farm','m_farm.kode_farm = op.kode_farm');
		return $this->db->get();
	}
}
