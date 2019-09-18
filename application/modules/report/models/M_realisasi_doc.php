<?php
class M_realisasi_doc extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	
	public function get_bap_doc($noreg){
		$sql = <<<SQL
			select bd.*, mh.NAMA_HATCHERY
			from BAP_DOC bd
			left join M_HATCHERY mh on bd.KODE_HATCHERY = mh.KODE_HATCHERY
			where bd.NO_REG = '{$noreg}'	
SQL;
		return $this->db->query($sql);
	}

	public function get_kode_box_noreg($noreg){
		$sql = <<<SQL
			select bds.NO_SJ NO_SJ, bds.FOTO,
			bdb.KODE_BOX, bdb.JML_BOX, bds.TgL_TERIMA, mp.NAMA_PEGAWAI 
				from BAP_DOC_BOX bdb 
				join BAP_DOC_SJ bds on bds.NO_REG = bdb.NO_REG 
					and bds.NO_SJ = substring(bdb.NO_SJ, 0, 11) 
				join M_PEGAWAI mp on mp.KODE_PEGAWAI = bds.USER_BUAT
				where bdb.no_reg = '{$noreg}'
				order by NO_SJ ASC
SQL;
		return $this->db->query($sql);
	}
	
	public function get_status_approval($noreg){
		$sql = <<<SQL
			select TOP(1) * 
				from LOG_BAP_DOC 
				where NO_REG = '{$noreg}' 
				order by NO_URUT DESC
SQL;
		return $this->db->query($sql);
	}
	
	public function get_log_approval($noreg){
		$sql = <<<SQL
			select lbd.*, mp.NAMA_PEGAWAI
			from LOG_BAP_DOC lbd
			left join M_PEGAWAI mp on lbd.USER_BUAT = mp.KODE_PEGAWAI
			where lbd.NO_REG = '{$noreg}'
			order by NO_URUT DESC	
SQL;
		return $this->db->query($sql);
	}
}
?>