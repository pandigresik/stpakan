<?php
class M_pengajuan_harga extends MY_Model{
	protected $primary_key;
	public function __construct(){
		parent::__construct();
		$this->_table = 'pengajuan_harga';
		$this->primary_key= 'no_pengajuan_harga';
	}
	public function no_pengajuan_harga($no_pengajuan_harga, $check_db = true)
	{
		$no_urut = 0;
		if($check_db){
			$tmp = $this->db->order_by('no_pengajuan_harga','desc')->where('no_pengajuan_harga like \''.$no_pengajuan_harga.'%\'')->get($this->_table);
	        $tmp = $tmp->row(0);
			//cetak_r($tmp, false);
	        
	        if(count($tmp) > 0){
	           $no_urut = (int)substr($tmp->no_pengajuan_harga,-3);
	        }
	    }else{
	    	$no_pengajuan_harga = substr(0, strlen($no_pengajuan_harga)-3);
	    	if($no_pengajuan_harga != ''){
	    		$no_urut = (int)substr($no_pengajuan_harga,-3);
	    	}
	    }
        $no_urut++;
        $no_urut = str_pad($no_urut,3,'0',STR_PAD_LEFT);
        //cetak_r($no_urut);

        return $no_pengajuan_harga.$no_urut;
	}

	public function listPengajuan($kode_farm = null, $tglPengajuan = NULL,$statusPengajuan = array()){
		$hariKebelakang = -45;
		$where = '';		
		if(!empty($kode_farm)){
			$where .= " and mf.kode_farm = '".$kode_farm."'";
		}
		if(!empty($tglPengajuan)){
			$where .= " and CAST(ph.tgl_pengajuan AS DATE) = '".$tglPengajuan."'";
		}
		if(!empty($statusPengajuan)){
			$listStatus = "'".implode("','",$statusPengajuan)."'";
			$cariStatus = <<<SQL
			join (select no_pengajuan_harga,max(no_urut) no_urut from log_pengajuan_harga group by no_pengajuan_harga) lph_terakhir 
				on lph_terakhir.no_pengajuan_harga = ph.no_pengajuan_harga 
			join log_pengajuan_harga lph on lph.no_pengajuan_harga = ph.no_pengajuan_harga and lph.no_urut = lph_terakhir.no_urut
				 and lph.status in ({$listStatus})
SQL;

		}
		$sql = <<<SQL
				select ph.no_pengajuan_harga, ph.ref_id, ph.tgl_pengajuan, ph.kode_farm, mf.nama_farm
				, pd.kode_barang, mb.nama_barang,pd.harga_reg, cast(pd.harga_jual as int) harga_jual, pd.estimasi_jumlah, pd.satuan
				, lph.status
				from pengajuan_harga ph
				join pengajuan_harga_d pd on ph.no_pengajuan_harga = pd.no_pengajuan_harga
				join M_FARM mf on ph.kode_farm = mf.kode_farm {$where}
				join m_barang mb on pd.kode_barang = mb.kode_barang
				{$cariStatus}
				where ph.tgl_pengajuan >= cast(dateadd(day,{$hariKebelakang},getdate()) as date)
				order by ph.tgl_pengajuan desc,ph.kode_farm

SQL;
		
	    return $this->db->query($sql)->result_array();
	}

	public function listStatusApproval($where = ''){
		$sql = <<<SQL
				select ph.no_pengajuan_harga, ph.tgl_pengajuan, ph.kode_farm, mf.nama_farm
				, mp.nama_pegawai, lg.status, isnull(lg.keterangan, '') keterangan, CONVERT(VARCHAR(19),lg.tgl_buat,121) tgl_buat
				, case when lg.status = 'D' then 'Dibuat'
					when lg.status = 'N' then 'Dirilis'
				    when lg.status = 'R1' then 'Dikoreksi'
				    when lg.status = 'A' then 'Disetujui'
				    when lg.status = 'RJ' then 'Ditolak'
					when lg.status = 'RJV' then 'Ditolak'
				    when lg.status = 'V' then 'Revisi'
				    else '' end status_detail
				, lg.status
				, lg.no_urut
				, mp.grup_pegawai
				from pengajuan_harga ph
				join log_pengajuan_harga lg on ph.no_pengajuan_harga = lg.no_pengajuan_harga and lg.status != 'V'
				join M_FARM mf on ph.kode_farm = mf.kode_farm
				join m_pegawai mp on lg.user_buat = mp.kode_pegawai
				{$where}
				order by ph.no_pengajuan_harga, lg.no_urut desc

SQL;
		
	    return $this->db->query($sql)->result_array();
	}

	public function getListFarm($kode_pegawai = ''){
		$sql = <<<SQL
				select distinct pd.*, mf.*
				from PEGAWAI_D pd
				join M_FARM mf on pd.KODE_FARM = mf.KODE_FARM
				join KANDANG_SIKLUS ks on pd.KODE_FARM = ks.KODE_FARM and mf.KODE_FARM = ks.KODE_FARM 
					and STATUS_SIKLUS in('O','C')
				where 
				-- ((getdate() between ks.TGL_DOC_IN and ks.TGL_PANEN) or ks.TGL_PANEN > dateadd(month, -4, getdate()))
				--	and 
				pd.KODE_PEGAWAI = '{$kode_pegawai}'

SQL;
		log_message("error",$sql);
		return $this->db->query($sql)->result_array();		

	}

	public function getBudgetYangAktif(){
		$sql = <<<SQL
				select mp.KODE_FARM, bg.*, bgd.NO_URUT, bgd.KODE_BUDGET, bgd.JML_ORDER, bgd.KATEGORI_BUDGET
				from BUDGET_GLANGSING bg
				join M_PERIODE mp on bg.KODE_SIKLUS = mp.KODE_SIKLUS
				join BUDGET_GLANGSING_D bgd on bg.KODE_SIKLUS = bgd.KODE_SIKLUS and bgd.KATEGORI_BUDGET = 'E'
				join (
					select KODE_SIKLUS, max(NO_URUT) NO_URUT 
					from BUDGET_GLANGSING_D
					group by KODE_SIKLUS
					) bgd2 on bgd.KODE_SIKLUS = bgd2.KODE_SIKLUS and bgd.NO_URUT = bgd2.NO_URUT
				join (
					select mp.KODE_FARM, max(bg.KODE_SIKLUS) KODE_SIKLUS
					from BUDGET_GLANGSING bg
					join M_PERIODE mp on bg.KODE_SIKLUS = mp.KODE_SIKLUS
					group by mp.KODE_FARM
				) bg2 on bg.KODE_SIKLUS = bg2.KODE_SIKLUS and bg.KODE_SIKLUS = bg2.KODE_SIKLUS

SQL;
		return $this->db->query($sql)->result_array();
	}

	public function getBarang(){
		$sql = <<<SQL
				select KODE_BARANG, ALIAS, NAMA_BARANG, UOM, TIPE_BARANG
				from M_BARANG
				where GRUP_BARANG = '000117' and STATUS_BARANG = 'A'
				and TIPE_BARANG = 'E'
SQL;
		return $this->db->query($sql)->result_array();
		
	}

	public function get_count_by_status($status){
		$sql = <<<SQL
				select ph.kode_farm, count(*) total
				from log_pengajuan_harga lh
				join (
					select no_pengajuan_harga, max(no_urut) no_urut
					from log_pengajuan_harga
					group by no_pengajuan_harga
				) ld on lh.no_pengajuan_harga = ld.no_pengajuan_harga and lh.no_urut = ld.no_urut
				join pengajuan_harga ph on lh.no_pengajuan_harga = ph.no_pengajuan_harga
				where lh.status = '{$status}'
				group by ph.kode_farm
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function getPengajuanHargaAktif($kodeFarm){
		$sql = <<<SQL
				select tmp.kode_farm, ori.kode_barang, ori.estimasi_jumlah, ori.satuan, ori.harga_jual, ori.no_pengajuan_harga
				from pengajuan_harga_d ori
				join (
					select max(lp.no_pengajuan_harga) no_pengajuan_harga, pd.kode_barang, ph.kode_farm
					from pengajuan_harga ph
					join pengajuan_harga_d pd on ph.no_pengajuan_harga = pd.no_pengajuan_harga
					join log_pengajuan_harga lp on ph.no_pengajuan_harga = lp.no_pengajuan_harga and lp.status = 'A'
					where ph.kode_farm = '{$kodeFarm}'
					group by pd.kode_barang, ph.kode_farm
					) tmp on ori.no_pengajuan_harga = tmp.no_pengajuan_harga and ori.kode_barang = tmp.kode_barang
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function getPengajuanTerakhir($user_level,$kode_farm = NULL){		
		$whereFarm = '';
		if(!empty($kode_farm)){
			$whereFarm = ' where ph.kode_farm = \''.$kode_farm.'\'';
		}
		if(empty($whereFarm)){
			if($user_level == 'KF'){
				$whereFarm = ' where ph.no_pengajuan_harga not like \'%RV%\'';
			}else{
				$whereFarm = ' where ph.no_pengajuan_harga like \'%RV%\'';
			}
		}else{
			if($user_level == 'KF'){
				$whereFarm .= ' and ph.no_pengajuan_harga not like \'%RV%\'';
			}else{
				$whereFarm .= ' and ph.no_pengajuan_harga like \'%RV%\'';
			}
		}
		$sql = <<<SQL
		select b.no_pengajuan_harga,b.kode_farm,b.tgl_pengajuan
				,case 
					when b.status = 'D' and b.keterangan is not null then 'RL'
					else b.status
					end 
				status	
	from (
			SELECT a.no_pengajuan_harga,a.kode_farm,a.tgl_pengajuan,a.keterangan
					,(SELECT TOP 1 status FROM log_pengajuan_harga WHERE no_pengajuan_harga = a.no_pengajuan_harga ORDER BY no_urut desc) status
		FROM(
			select max(ph.no_pengajuan_harga) no_pengajuan_harga, ph.kode_farm,max(convert(date,ph.tgl_pengajuan)) tgl_pengajuan, ph.keterangan
			from pengajuan_harga ph
			{$whereFarm}
			group by ph.kode_farm,ph.keterangan				
		)a				
	)b				
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function getEstimasiStok(){
		$sql = <<<SQL
		select gm.kode_farm, mf.nama_farm, gm.kode_barang, mb.nama_barang, (gm.jml_stok - coalesce(pengurang.jml_diminta,0)) jml_estimasi
		from glangsing_movement gm
		join M_BARANG mb on gm.kode_barang = mb.KODE_BARANG
		JOIN (
			SELECT max(kode_siklus) AS kode_siklus,kode_farm FROM glangsing_movement GROUP BY kode_farm
		)siklus_terakhir ON siklus_terakhir.kode_siklus = gm.kode_siklus AND gm.kode_farm = siklus_terakhir.kode_farm
		join m_farm mf on gm.kode_farm = mf.kode_farm
		LEFT JOIN (
			SELECT sum(pd.jml_diminta) jml_diminta,pn.kode_siklus, pn.kode_budget FROM ppsk_new  pn
			INNER JOIN ppsk_d pd ON pn.no_ppsk = pd.no_ppsk AND pd.tgl_terima IS NULL AND pd.jml_diminta > 0
			WHERE tgl_terima IS null
			GROUP BY pn.kode_siklus, pn.kode_budget
		)pengurang ON pengurang.kode_siklus = gm.kode_siklus AND pengurang.kode_budget = gm.kode_barang
		order by gm.kode_farm, gm.kode_barang
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function hargaRegional($kodeFarm){
		$tahun = date('Y');
		$sql = <<<SQL
		SELECT ph.no_pengajuan_harga,pd.kode_barang,pd.harga_reg FROM pengajuan_harga ph
		JOIN pengajuan_harga_d pd ON pd.no_pengajuan_harga = ph.no_pengajuan_harga 
		WHERE ph.tgl_pengajuan = ( 
		SELECT max(tgl_pengajuan) FROM pengajuan_harga WHERE no_pengajuan_harga like 'PH/{$kodeFarm}/{$tahun}%'
		) AND ph.keterangan IS NULL AND ph.kode_farm = '{$kodeFarm}'
SQL;
		return $this->db->query($sql)->result_array();
	}	
}
