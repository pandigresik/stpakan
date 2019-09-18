<?php
class M_import_docin extends CI_Model{
	private $_user;
	public function __construct(){
		parent::__construct();
		$this->_user = $this->session->userdata('kode_user');
	}
	
	/*public function siklus_tahunan($tahun = NULL){
		$where = !empty($tahun) ? ' and year(tgl_doc_in) = \''.$tahun.'\'' : ' and year(TGL_DOC_IN) >= year(CURRENT_TIMESTAMP) - 2  ';
		$sql = <<<SQL
		select
			distinct year(TGL_DOC_IN) tahun
			,min(ks.tgl_doc_in) awal_docin
			,case (select top 1 status_approve from log_kandang_siklus_bdy where kode_siklus = max(ks.kode_siklus) order by no_urut desc)
				when 'D' then 'DRAFT'
				when 'A' then 'APPROVE'
				when 'N' then 'PENGAJUAN'
				when 'RV' then 'REVIEW'
				when 'RJ' then 'REJECT'
			end	status
			from kandang_siklus ks
			where kode_std_budidaya is not null
			and flok_bdy is not null
			{$where}
			group by year(TGL_DOC_IN)
SQL;

		return $this->db->query($sql);
	}*/

	public function siklus_tahunan($tahun = NULL){		
		$where = !empty($tahun) ? ' and substring(periode_siklus,1,4) = \''.$tahun.'\'' : ' and substring(periode_siklus,1,4) >= year(CURRENT_TIMESTAMP) - 2  ';		
		$sql = <<<SQL
		select
			distinct substring(periode_siklus,1,4) tahun
			,min(ks.tgl_doc_in) awal_docin
			,case (select top 1 status_approve from log_kandang_siklus_bdy where kode_siklus = max(ks.kode_siklus) order by no_urut desc)
				when 'D' then 'DRAFT'
				when 'A' then 'APPROVE'
				when 'N' then 'PENGAJUAN'
				when 'RV' then 'REVIEW'
				when 'RJ' then 'REJECT'
			end	status
			from kandang_siklus ks
			join m_periode mp on ks.kode_siklus = mp.kode_siklus
			where kode_std_budidaya is not null
			and flok_bdy is not null
			{$where}
			group by substring(periode_siklus,1,4)
SQL;

		return $this->db->query($sql);
	}

	public function get_std_budidaya($where){
		$wher_str = '';
		if(!empty($where)){
			$wher_str = ' where '.$where;
		}

		$sql = <<<SQL
		SELECT TOP 1 kode_std_budidaya,target_umur_panen FROM m_std_budidaya {$wher_str} ORDER BY tgl_efektif DESC
SQL;
		return $this->db->query($sql);
	}


	public function farm_preview($where){
		$wher_str = '';
		if(!empty($where)){
			$wher_str = ' where '.$where;
		}
		$sql = <<<SQL
		SELECT distinct mf.kode_farm,mf.nama_farm FROM kandang_siklus ks
		inner join m_farm mf
			on mf.kode_farm = ks.kode_farm
		{$wher_str}
SQL;
		return $this->db->query($sql);
	}


}
