<?php
class M_lpb extends CI_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'lpb';
	}
	public function get($param = array()){
		if(!empty($param)){
			$this->db->like($param);
		}
		return $this->db->get($this->_table);
	}

	public function get_pp($param = array(),$custom_param = array()){
		$where = '';
		if(!empty($param)){
			$tmp = array();
			foreach($param as $key => $val){
				array_push($tmp,$key.' = \''.$val.'\'');
			}
			$where .= 'where '.implode(' and ',$tmp);
		}
		if(!empty($custom_param)){
			if(empty($where)){
				$where .= 'where '.implode(' and ',$custom_param);
			}
			else{
				$where .= 'and '.implode(' and ',$custom_param);
			}
		}
		if(!empty($kode_farm)){

		}
		$sql = <<<SQL
		select lpb.no_lpb
			,lpb.tgl_buat
			,lpb.tgl_rilis
			,lpb.tgl_approve1
			,stuff (
				(select ','+ convert(varchar,ld.TGL_KIRIM)
				from lpb_d ld
				where ld.NO_LPB = lpb.NO_LPB
				for xml path (''))
				,1,1,'') tgl_kirim
			,stuff (
				(select ','+ convert(varchar,datediff(day,ld.TGL_KIRIM,ld.TGL_KEB_AKHIR)) + '*kt*' + coalesce(ld.keterangan,'')
				from lpb_d ld
				where ld.NO_LPB = lpb.NO_LPB
				for xml path (''))
				,1,1,'') umur_pakan
			,lpb.ref_id
			,lpb.status_lpb
			,sum(le.jml_order) kuantitas_pp
		from lpb
		INNER join m_periode mp
		on lpb.kode_siklus = mp.KODE_SIKLUS and lpb.KODE_FARM = mp.KODE_FARM and mp.STATUS_PERIODE = 'A'
		inner join lpb_e le
			on le.NO_LPB = lpb.NO_LPB
		{$where}
		group by lpb.no_lpb
			,lpb.tgl_buat
			,lpb.tgl_rilis
			,lpb.tgl_approve1
			,lpb.REF_ID
			,lpb.status_lpb
		order by lpb.tgl_buat desc

SQL;

		return $this->db->query($sql);

	}

	public function get_pp_bdy($param = array(),$custom_param = array(),$flok = null){
		$where = '';
		if(!empty($param)){
			$tmp = array();
			foreach($param as $key => $val){
				array_push($tmp,$key.' = \''.$val.'\'');
			}
			$where .= 'where '.implode(' and ',$tmp);
		}
		if(!empty($custom_param)){
			if(empty($where)){
				$where .= 'where '.implode(' and ',$custom_param);
			}
			else{
				$where .= 'and '.implode(' and ',$custom_param);
			}
		}
		$whereFlok = '';
		if(!empty($flok)){			
			$kode_farm = $param['lpb.kode_farm'];
			$whereFlok = ' and ks.kode_farm =\''.$kode_farm.'\' and ks.flok_bdy = \''.$flok.'\'';			
		}
		

		$sql = <<<SQL
		select lpb.no_lpb
			,mp.kode_strain
			,mf.nama_farm			
			,stuff((SELECT nama_pegawai FROM M_PEGAWAI WHERE KODE_PEGAWAI = LPB.USER_BUAT),1,0,'') user_buat
			,CASE 
				when lpb.status_lpb NOT in ('D','N') THEN (SELECT TOP 1 nama_pegawai FROM M_PEGAWAI WHERE KODE_PEGAWAI = LPB.USER_UBAH)
				ELSE NULL
			 END user_review
			,case
				when lpb.status_lpb = 'A' then  (SELECT TOP 1 nama_pegawai FROM M_PEGAWAI WHERE GRUP_PEGAWAI = 'KDV')
				when lpb.status_lpb = 'V' then  (SELECT nama_pegawai FROM M_PEGAWAI WHERE KODE_PEGAWAI = (select top 1 user_reject from review_lpb_budidaya where no_lpb = lpb.no_lpb))
				when lpb.status_lpb = 'RJ' then  (SELECT nama_pegawai FROM M_PEGAWAI WHERE KODE_PEGAWAI = (select top 1 user_reject from review_lpb_budidaya where no_lpb = lpb.no_lpb))
				else null
			end user_approve
			,case when lpb.status_lpb in ('RJ','V') then (select top 1 ket_reject from review_lpb_budidaya where no_lpb = lpb.no_lpb) else NULL end ket_reject
			,stuff(convert(varchar(19), lpb.tgl_buat, 126),11,1,' ') tgl_buat		
			,stuff(convert(varchar(19), lpb.tgl_rilis, 126),11,1,' ') tgl_rilis
			,stuff(convert(varchar(19), lpb.tgl_ubah, 126),11,1,' ') tgl_review				
			,stuff(convert(varchar(19), coalesce(lpb.tgl_approve1,(select top 1 tgl_reject from review_lpb_budidaya where no_lpb = lpb.no_lpb)), 126),11,1,' ') tgl_approve1
		--	,coalesce(lpb.tgl_approve1,(select top 1 tgl_reject from review_lpb_budidaya where no_lpb = lpb.no_lpb)) tgl_approve1
			,stuff (
				(select ','+ convert(varchar,ld.TGL_KIRIM)
				from lpb_d ld
				where ld.NO_LPB = lpb.NO_LPB
				for xml path (''))
				,1,1,'') tgl_kirim
			,stuff (
				(select ','+ convert(varchar(19), ld.TGL_KEB_AWAL, 126)+' s/d '+convert(varchar(19), ld.TGL_KEB_AKHIR, 126)
				from lpb_d ld
				where ld.NO_LPB = lpb.NO_LPB
				for xml path (''))
				,1,1,'') tgl_kebutuhan
			,stuff (
				(select ','+ convert(varchar,datediff(day,ld.TGL_KIRIM,ld.TGL_KEB_AKHIR)) + '*kt*' + coalesce(ld.keterangan,'')
				from lpb_d ld
				where ld.NO_LPB = lpb.NO_LPB
				for xml path (''))
				,1,1,'') umur_pakan
			,lpb.ref_id
			,case
				when lpb.ref_id is not null then  (select xx.status_lpb from lpb xx where xx.no_lpb = lpb.ref_id)
				else null
			end status_ref_id
			,lpb.status_lpb
			,sum(le.jml_order) kuantitas_pp
			,rlb.jml_optimasi optimasi_pp
			,rlb.jml_rekomendasi rekomendasi_pp
			-- ,case lpb.status_lpb when 'V' then '' else rlb.jml_review end persetujuan_pp
			,rlb.jml_review persetujuan_pp
			,(select top 1 convert(varchar,ks.FLOK_BDY)+' - '+ks.KODE_KANDANG
				from lpb_e le
				inner join kandang_siklus ks
					on ks.NO_REG = le.no_reg 				
				where no_lpb = lpb.no_lpb)
				flok_kandang			
		from lpb
		inner join m_farm mf
			on lpb.kode_farm = mf.kode_farm
		INNER join m_periode mp
		on lpb.kode_siklus = mp.KODE_SIKLUS and lpb.KODE_FARM = mp.KODE_FARM and mp.STATUS_PERIODE = 'A'
		inner join lpb_e le
			on le.NO_LPB = lpb.NO_LPB
			and le.no_reg in (select ks.no_reg
				from kandang_siklus ks where ks.status_siklus = 'O'	{$whereFlok}			 
			)
		inner join (
			select no_lpb
						,sum(jml_optimasi) jml_optimasi
						,sum(coalesce(jml_rekomendasi,jml_optimasi)) jml_rekomendasi
						,sum(coalesce(jml_review,jml_optimasi)) jml_review
			from review_lpb_budidaya
			group by no_lpb
		)rlb
				on rlb.no_lpb = lpb.no_lpb
		{$where}
		group by lpb.no_lpb
			,lpb.user_buat
			,lpb.user_ubah
			,lpb.tgl_buat
			,lpb.tgl_ubah
			,lpb.tgl_rilis
			,lpb.tgl_approve1
			,lpb.REF_ID
			,lpb.status_lpb
			,mp.kode_strain
			,mf.nama_farm
			,rlb.jml_optimasi
			,rlb.jml_rekomendasi
			,rlb.jml_review
		order by lpb.tgl_buat desc
SQL;
		
		return $this->db->query($sql);

	}

	public function get_keterangan_reject_pp_bdy($param = array(),$custom_param = array()){
		$where = '';
		if(!empty($param)){
			$tmp = array();
			foreach($param as $key => $val){
				array_push($tmp,$key.' = \''.$val.'\'');
			}
			$where .= 'where '.implode(' and ',$tmp);
		}
		if(!empty($custom_param)){
			if(empty($where)){
				$where .= 'where '.implode(' and ',$custom_param);
			}
			else{
				$where .= 'and '.implode(' and ',$custom_param);
			}
		}
		$sql = <<<SQL
		select distinct lpb.no_lpb
				,rlb.ket_reject
		from lpb
		join review_lpb_budidaya rlb
			on lpb.no_lpb = rlb.no_lpb and rlb.user_reject is not null
		{$where}
SQL;
	return $this->db->query($sql);
}

	public function get_pending_pp($kodefarm,$kodeflok = NULL){
		$result = 0;
		$pp_terakhir = $this->get_last_pp($kodefarm, $kodeflok)->row_array();
		$periode_aktif = $this->db->select('periode_siklus, kode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode' => 'A'))->get('M_PERIODE')->row_array();
		if(!empty($pp_terakhir['buat_lpb_terakhir'])){
			$pp = $this->db->where(array('kode_siklus' => $periode_aktif['kode_siklus'],'tgl_buat' => $pp_terakhir['buat_lpb_terakhir']))->get($this->_table)->row_array();
			if(!empty($pp)){
				$result = $pp['STATUS_LPB'] != 'A' ? 1 : 0;	
			}			
		}
		return $result;
	}

	public function get_pending_pp_noreg($noreg){
		$result = 0;
		$pp_terakhir = $this->get_last_pp_noreg($noreg)->row_array();
		$periode_aktif = $this->db->select('kode_siklus')->where(array('no_reg' => $noreg))->get('kandang_siklus')->row_array();
		if(!empty($pp_terakhir['buat_lpb_terakhir'])){
			$pp = $this->db->where(array('kode_siklus' => $periode_aktif['kode_siklus'],'tgl_buat' => $pp_terakhir['buat_lpb_terakhir']))->get($this->_table)->row_array();
			
			if(!empty($pp)){
				$result = $pp['STATUS_LPB'] != 'A' ? 1 : 0;	
			}			
		}
		return $result;
	}

	
	public function get_last_pp_noreg($noreg){		
		$sql = <<<SQL
		select max(l.tgl_buat) buat_lpb_terakhir
		from lpb l
		inner join lpb_e le
		on l.no_lpb = le.no_lpb
		where le.no_reg = '{$noreg}'
SQL;

		return $this->db->query($sql);
	}

	public function get_last_pp($kodefarm,$kodeflok){
		$jo = '';
		if(!empty($kodeflok)){
			$jo = <<<SQL
			and ks.FLOK_BDY = '{$kodeflok}'
SQL;

		}
		$sql = <<<SQL
		select max(l.tgl_buat) buat_lpb_terakhir
		from lpb l
		inner join lpb_e le
		on l.no_lpb = le.no_lpb
		inner join kandang_siklus ks
		on le.NO_REG = ks.NO_REG and ks.KODE_FARM = '{$kodefarm}' {$jo}
		/*
		inner join m_kandang mk
			on mk.KODE_FARM = ks.KODE_FARM
			and mk.KODE_KANDANG = ks.KODE_KANDANG
			and mk.KODE_FARM = '{$kodefarm}'
			{$jo}
		*/	
SQL;

		return $this->db->query($sql);
	}

	public function get_last_no_pp($kodefarm,$kodeflok,$tgl_buat){
		$jo = '';
		if(!empty($kodeflok)){
			$jo = <<<SQL
			and ks.FLOK_BDY = '{$kodeflok}'
SQL;

		}
		$sql = <<<SQL
		select l.no_lpb no_lpb_terakhir
		from lpb l
		inner join lpb_e le
		on l.no_lpb = le.no_lpb
		inner join kandang_siklus ks
		on le.NO_REG = ks.NO_REG and ks.KODE_FARM = '{$kodefarm}' {$jo}
		/*
		inner join m_kandang mk
			on mk.KODE_FARM = ks.KODE_FARM
			and mk.KODE_KANDANG = ks.KODE_KANDANG
			and mk.KODE_FARM = '{$kodefarm}'
			{$jo}
		*/	
		where l.tgl_buat = '{$tgl_buat}'
SQL;

		return $this->db->query($sql);
	}


	public function simpan($kode_farm,$kode_siklus,$user,$tgl_sekarang,$status_lpb,$ref_id = NULL){
		$tgl_rilis = NULL;
		$tgl_approve1 = NULL;
		$additional_kode = '';
		if(empty($kode_siklus)){
			$kodesiklus_sql = $this->db->distinct()->select('kode_siklus')->where(array('kode_farm' => $kode_farm, 'status_siklus' => 'O'))->get('kandang_siklus')->row_array();
			$kode_siklus = $kodesiklus_sql['kode_siklus'];
		}
		switch($status_lpb){
			case 'N' :
				$tgl_rilis = $tgl_sekarang;
				break;
			case 'A':
				$tgl_rilis = $tgl_sekarang;
				$tgl_approve1 = $tgl_sekarang;
				break;
			case 'RV':
				$tgl_rilis = $tgl_sekarang;
				$additional_kode = 'RV';
				break;
			default:

		}

		$sql = <<<SQL
		insert into lpb (kode_farm,kode_siklus,no_lpb,status_lpb,tgl_rilis,tgl_buat,tgl_ubah,user_buat,user_ubah,ref_id,tgl_approve1) output inserted.no_lpb
		values (:kode_farm,:kode_siklus,dbo.generate_nopp(:kode_farm1,:tgl_sekarang)+'{$additional_kode}','{$status_lpb}',:tgl_rilis,:tgl_buat,:tgl_ubah,:user_buat,:user_ubah,:ref_id,:tgl_approve1)
SQL;

		$stmt = $this->db->conn_id->prepare ($sql);
		$stmt->bindParam ( ':kode_farm', $kode_farm );
		$stmt->bindParam ( ':kode_siklus', $kode_siklus );
		$stmt->bindParam ( ':kode_farm1', $kode_farm );
		$stmt->bindParam ( ':tgl_sekarang', $tgl_sekarang );
		$stmt->bindParam ( ':tgl_rilis', $tgl_rilis );
		$stmt->bindParam ( ':tgl_buat', $tgl_sekarang );
		$stmt->bindParam ( ':tgl_ubah', $tgl_sekarang );
		$stmt->bindParam ( ':user_buat', $user );
		$stmt->bindParam ( ':user_ubah', $user );
		$stmt->bindParam ( ':ref_id', $ref_id );
		$stmt->bindParam ( ':tgl_approve1', $tgl_approve1 );

		$stmt->execute();
	//	print_r($stmt->errorInfo());
		return $stmt->fetch( PDO::FETCH_ASSOC );
	}

	public function insert($param = array()){
		$this->db->insert($this->_table,$param);
	}

	public function update($no_pp,$data){
		$this->db->where('no_lpb',$no_pp);
		$this->db->update($this->_table,$data);
	}

	public function notif_pp_reject(){
		$sql = <<<SQL
		select * from (
			select max(l.no_lpb) no_lpb
					,mk.KODE_FARM kode_farm
					,NO_FLOK lpb_terakhir
					,(select STATUS_LPB from lpb where no_lpb = max(l.no_lpb)) status_lpb
					,(select distinct(ket_reject) from review_lpb_budidaya where no_lpb = max(l.no_lpb)) ket_reject
					from lpb l
					inner join lpb_e le
					on l.no_lpb = le.no_lpb
					inner join
					kandang_siklus ks
					on le.NO_REG = ks.NO_REG
					inner join m_kandang mk
						on mk.KODE_FARM = ks.KODE_FARM
						and mk.KODE_KANDANG = ks.KODE_KANDANG
					inner join m_farm mf
						on mf.KODE_FARM = mk.KODE_FARM and mf.GRUP_FARM = 'bdy'
			group by mk.NO_FLOK
				,mk.KODE_FARM
			)x where x.status_lpb = 'RJ'
SQL;
		return $this->db->query($sql);
	}
}
