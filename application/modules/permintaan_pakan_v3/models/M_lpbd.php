<?php
class M_lpbd extends CI_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'lpb_d';
	}
	public function get($param = array()){
		if(!empty($param)){
			$this->db->like($param);
		}
		return $this->db->get($this->_table);
	}

	public function get_last_pp($kode_farm,$kodeflok = NULL){
		$paramflok = '';
		if(!empty($kodeflok)){
			$paramflok = <<<SQL
		inner join lpb_e le
				on le.NO_LPB = lpb.NO_LPB
		inner join KANDANG_SIKLUS ks
		on ks.no_reg = le.NO_REG and ks.STATUS_SIKLUS = 'O' and ks.KODE_FARM = '{$kode_farm}' and ks.KODE_KANDANG in (
			select KODE_KANDANG from m_kandang where KODE_FARM = '{$kode_farm}' and NO_FLOK = '{$kodeflok}'
		)
SQL;
		}


		$sql = <<<SQL
		SELECT MAX("tgl_keb_akhir") AS "tgl_keb_akhir"
		FROM "lpb_d"
		inner join lpb
		on lpb.NO_LPB = lpb_d.NO_LPB and lpb.KODE_FARM = '{$kode_farm}'
		and lpb.STATUS_LPB = 'A'
		{$paramflok}
SQL;

		return $this->db->query($sql);
	}

	public function get_last_pp_noreg($noreg){	
		$sql = <<<SQL
		SELECT MAX("tgl_keb_akhir") AS "tgl_keb_akhir"
		FROM "lpb_d"
		inner join lpb
			on lpb.NO_LPB = lpb_d.NO_LPB and lpb.STATUS_LPB = 'A'
		inner join lpb_e le
			on le.NO_LPB = lpb.NO_LPB and le.NO_REG = '{$noreg}'		
SQL;

		return $this->db->query($sql);
	}

	public function insert($param = array()){
		$this->db->insert($this->_table,$param);
	}
	public function delete($where){
		$this->db->where($where);
		$this->db->delete($this->_table);
	}
}
