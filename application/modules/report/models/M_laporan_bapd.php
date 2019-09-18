<?php
class M_laporan_bapd extends CI_Model{
	public function __construct(){
		parent::__construct();
	}

	//public function list_bapd($awaldocin,$akhirdocin,$farm,$access,$filter_status){
	public function list_bapd($farm, $siklus){
		$sql = <<<SQL
			select bd.no_reg
			,ks.kode_kandang
			,mh.nama_hatchery
			,bd.tgl_doc_in
			,bd.status
			,bd.jml_afkir
			,bd.stok_awal
			,bd.bb_rata2
			,bd.uniformity
			,case bd.STATUS
			--	when 'RJ' then (select top 1 tgl_buat from log_bap_doc where no_reg = bd.no_reg and status = 'N' order by no_urut desc)
				when 'N' then null
				else (select top 1 convert(varchar(10), tgl_buat, 126) 
				+ ' ' + substring(convert(varchar(19), tgl_buat, 126), 12, 8) tgl_buat
				from log_bap_doc where no_reg = bd.no_reg and status = 'RV' order by no_urut desc)
				end tindaklanjutpengawas
			,case bd.STATUS
				when 'RJ' then (select top 1 convert(varchar(10), tgl_buat, 126) 
				+ ' ' + substring(convert(varchar(19), tgl_buat, 126), 12, 8) tgl_buat
				from log_bap_doc where no_reg = bd.no_reg and status = 'RJ' order by no_urut desc)
				when 'RV' then null
				else (select top 1 convert(varchar(10), tgl_buat, 126) 
				+ ' ' + substring(convert(varchar(19), tgl_buat, 126), 12, 8) tgl_buat 
				from log_bap_doc where no_reg = bd.no_reg and status = 'A' order by no_urut desc)
				end tindaklanjutkafarm
		from bap_doc bd
		join m_hatchery mh on mh.kode_hatchery = bd.kode_hatchery
		join kandang_siklus ks on ks.no_reg = bd.no_reg and ks.kode_farm = '{$farm}'
		join m_kandang mk on mk.kode_kandang = ks.kode_kandang and mk.kode_farm = ks.kode_farm
		where bd.NO_REG like '{$farm}/{$siklus}/%' and DATEDIFF(day, bd.TGL_DOC_IN, GETDATE()) > 7
SQL;
		log_message('error',$sql);
		return $this->db->query($sql);
	}

	public function list_sj($noreg){
		/*$sql = <<<SQL
		select no_reg
			,no_sj
			,tgl_terima
			,sum(jml_box) jmlbox
		from bap_doc_box where no_reg = '{$noreg}'
		group by no_reg
			,no_sj
			,tgl_terima
SQL;*/
		$sql = <<<SQL
			select NO_REG
			,NO_SJ
			,convert(varchar(10), TGL_TERIMA, 126) + ' ' +
			SUBSTRING(convert(varchar(19), TGL_TERIMA, 126), 12, 8) TGL_TERIMA
			,JML_BOX
		from BAP_DOC_BOX where NO_REG = '{$noreg}'
SQL;
		return $this->db->query($sql);
	}
	
	public function get_strain($noreg){
		$sql = <<<SQL
		select msb.kode_strain
		from kandang_siklus ks
		join m_std_budidaya msb on msb.kode_std_budidaya = ks.kode_std_budidaya
		where ks.no_reg = '{$noreg}'
SQL;
		return $this->db->query($sql);
	}
	
	public function get_kode_box($kodefarm){
		$sql = <<<SQL
			select bd.*, mh.NAMA_HATCHERY, bdb.NO_SJ, bdb.KODE_BOX, bdb.JML_BOX 
			from BAP_DOC bd 
			join BAP_DOC_BOX bdb on bdb.NO_REG = bd.NO_REG
			join M_HATCHERY mh on mh.KODE_HATCHERY = bd.KODE_HATCHERY
			where bd.no_reg in (
				select NO_REG from KANDANG_SIKLUS where STATUS_SIKLUS = 'O' and KODE_SIKLUS in (
					select KODE_SIKLUS from m_periode where KODE_FARM = '{$kodefarm}' and STATUS_PERIODE = 'A'
				)
			)
SQL;
		return $this->db->query($sql);
	}
	
	public function get_kode_box_periode($noreg_like){
	$sql = <<<SQL
			select bd.*, mh.NAMA_HATCHERY, bdb.NO_SJ, bdb.KODE_BOX, bdb.JML_BOX 
			from BAP_DOC bd 
			join BAP_DOC_BOX bdb on bdb.NO_REG = bd.NO_REG
			join M_HATCHERY mh on mh.KODE_HATCHERY = bd.KODE_HATCHERY
			where bd.no_reg like '{$noreg_like}%'
SQL;
		return $this->db->query($sql);	
	}
	
	public function get_user_info($noreg, $tipe){
		$sql = <<<SQL
			select TOP(1) lbd.*, mp.NAMA_PEGAWAI 
			from LOG_BAP_DOC lbd 
			join m_pegawai mp on mp.kode_pegawai = lbd.user_buat
			where no_reg like '{$noreg}' and status = '{$tipe}' 
			order by no_urut DESC
SQL;
		return $this->db->query($sql);
	}
	
	public function get_all_kodefarm(){
		return $this->db->select('KODE_FARM')->get('M_FARM');
	}
	
	/*public function check_umur($noreg){
		return $this->db->where('NO_REG', $noreg)->from('RHK');
	}*/
	
	public function get_umur_7hari($noreg){
		$sql = <<<SQL
				select SUM(r.C_AFKIR) JML_AFKIR
				from RHK r
				join KANDANG_SIKLUS ks on ks.NO_REG = r.NO_REG
				where r.NO_REG = '{$noreg}'
				and r.TGL_TRANSAKSI < DATEADD(day,8,ks.tgl_doc_in)
SQL;
		//DATEADD(day,7,ks.tgl_doc_in)
		return $this->db->query($sql);
	}
}