<?php
class M_bapd extends CI_Model{
	public function __construct(){
		parent::__construct();
	}

	public function list_bapd($awaldocin,$akhirdocin,$farm,$access,$filter_status){

		$filter = array();
		$where = ' where ';
		if(!empty($awaldocin)){
			array_push($filter," bd.TGL_DOC_IN between '".$awaldocin."' and '".$akhirdocin."'");
			if(!empty($filter_status)){
				array_push($filter,$filter_status);
			}

		}
		if(!empty($access)){
			switch($access){
				case 'ack':
					array_push($filter,' bd.status = \'N\'');
					break;
				case 'approve':
					array_push($filter,' bd.status = \'RV\'');
					break;
				case 'create':
					array_push($filter,' bd.status in (\'D\',\'RJ\')');
					break;
			}
		}

		$where .= implode($filter,' and ');
		$sql = <<<SQL
		select bd.no_reg
			,ks.kode_kandang
			,mh.nama_hatchery
			,bd.tgl_doc_in
			,bd.status
			,case bd.STATUS
			--	when 'RJ' then (select top 1 tgl_buat from log_bap_doc where no_reg = bd.no_reg and status = 'N' order by no_urut desc)
				when 'N' then null
				else (select top 1 convert(varchar(10), tgl_buat, 126) 
				+ ' ' + substring(convert(varchar(19), tgl_buat, 126), 12, 8) 
				tgl_buat from log_bap_doc where no_reg = bd.no_reg and status = 'RV' order by no_urut desc)
				end tindaklanjutpengawas
			,case bd.STATUS
				when 'RJ' then (select top 1 convert(varchar(10), tgl_buat, 126) 
				+ ' ' + substring(convert(varchar(19), tgl_buat, 126), 12, 8) 
				tgl_buat from log_bap_doc where no_reg = bd.no_reg and status = 'RJ' order by no_urut desc)
				when 'RV' then null
				else (select top 1 convert(varchar(10), tgl_buat, 126) 
				+ ' ' + substring(convert(varchar(19), tgl_buat, 126), 12, 8) 
				tgl_buat from log_bap_doc where no_reg = bd.no_reg and status = 'A' order by no_urut desc)
				end tindaklanjutkafarm
		from bap_doc bd
		join m_hatchery mh on mh.kode_hatchery = bd.kode_hatchery
		join kandang_siklus ks on ks.no_reg = bd.no_reg and ks.kode_farm = '{$farm}'
		join m_kandang mk on mk.kode_kandang = ks.kode_kandang and mk.kode_farm = ks.kode_farm
		{$where}
SQL;

		return $this->db->query($sql);
	}

	public function list_sj($noreg){
		$sql = <<<SQL
		select no_reg
			,no_sj
			,tgl_terima
			,sum(jml_box) jmlbox
		from bap_doc_box where no_reg = '{$noreg}'
		group by no_reg
			,no_sj
			,tgl_terima
SQL;
		return $this->db->query($sql);
	}

	public function riwayatbap($noreg = array()){
		if(!is_array($noreg)){
			$noreg = array($noreg);
		}
		$noreg_str = implode("','",$noreg);
		$sql = <<<SQL
		select lbd.status
			,lbd.keterangan
			,stuff(convert(varchar(19), lbd.tgl_buat, 126),11,1,' ') tgl_buat	
			,mp.nama_pegawai
			,lbd.no_reg
		from log_bap_doc lbd
		join m_pegawai mp on mp.kode_pegawai = lbd.user_buat
		where lbd.no_reg in ('{$noreg_str}')
		order by lbd.no_reg asc,lbd.no_urut desc
SQL;
		return $this->db->query($sql);
	}
	public function resumebapd($where){
		$sql = <<<SQL
		select bd.no_reg
			,ks.kode_kandang
			,mh.nama_hatchery
			,ks.tgl_doc_in
			,sum(bdb.jml_box) jmlbox
			,bd.stok_awal
			,bd.jml_afkir
			,bd.bb_rata2
			,bd.uniformity
			,(select top 1 tgl_buat from log_bap_doc where no_reg = bd.no_reg and status = 'RV' order by no_urut) tindaklanjutpengawas
			,(select top 1 tgl_buat from log_bap_doc where no_reg = bd.no_reg and status = 'A' order by no_urut) tindaklanjutkafarm
		from bap_doc bd
		join bap_doc_box bdb
			on bd.no_reg = bdb.no_reg
		join m_hatchery mh
			on mh.kode_hatchery = bd.kode_hatchery
		join kandang_siklus ks
			on ks.no_reg = bd.no_reg and {$where}
		group by bd.no_reg
			,ks.kode_kandang
			,mh.nama_hatchery
			,ks.tgl_doc_in
			,bd.stok_awal
			,bd.jml_afkir
			,bd.bb_rata2
			,bd.uniformity
SQL;
		return $this->db->query($sql);
	}

	public function listBapdSJ($pencarian){
		$kode_siklus = isset($pencarian['kode_siklus']) ? $pencarian['kode_siklus'] : '';
		$kode_farm = isset($pencarian['kode_farm']) ? $pencarian['kode_farm'] : '';
		$kode_kandang = isset($pencarian['kode_kandang']) ? $pencarian['kode_kandang'] : '';
		$kode_hatchery = isset($pencarian['kode_hatchery']) ? $pencarian['kode_hatchery'] : '';
		$level_user = isset($pencarian['level_user']) ? $pencarian['level_user'] : '';
		$tindak_lanjut = isset($pencarian['tindak_lanjut']) ? $pencarian['tindak_lanjut'] : 0;
		$kode_pegawai = isset($pencarian['kode_pegawai']) ? $pencarian['kode_pegawai'] : '';

		$whereKandang = empty($kode_kandang) ? '' : '  ks.kode_kandang = \''.$kode_kandang.'\'';
		$whereHatchery = empty($kode_hatchery) ? '' : '  bd.kode_hatchery = \''.$kode_hatchery.'\'';
		$whereStatusTindakLanjut = '';
		
		$whereSiklus = '';
		if(!empty($kode_siklus)){
			$whereSiklus = ' ks.KODE_SIKLUS = '.$kode_siklus;	
		}

		if($tindak_lanjut){
			$tmp_kode_siklus = $this->db->select(array('kode_siklus'))->where(array('kode_farm' => $kode_farm, 'status_periode' => 'A'))->get('m_periode')->row_array();
			$kode_siklus = $tmp_kode_siklus['kode_siklus'];
			$whereSiklus = ' ks.KODE_SIKLUS = \''.$kode_siklus.'\'';
			if($level_user == 'P'){
				/** sementara dimatikan dulu */
				$whereStatusTindakLanjut = '  (bd.status is null or bd.status in (\'RJ\'))';
				
			}else{
				$whereStatusTindakLanjut = '  bd.status in (\'N\')';
			}
		}

		$whereNoreg = '';
		if($level_user == 'P'){
			$siklusPengawas = !empty($kode_siklus) ? ' AND KODE_SIKLUS = '.$kode_siklus : '';
			$whereNoreg = '  ks.no_reg IN (SELECT DISTINCT no_reg FROM M_PLOTING_PELAKSANA WHERE PENGAWAS = \''.$kode_pegawai.'\''.$siklusPengawas.' )';
		}
		$whereAll = array();
		if(!empty($whereSiklus)){
			array_push($whereAll,$whereSiklus);
		}
		if(!empty($whereNoreg)){
			array_push($whereAll,$whereNoreg);
		}
		if(!empty($whereHatchery)){
			array_push($whereAll,$whereHatchery);
		}
		if(!empty($whereKandang)){
			array_push($whereAll,$whereKandang);
		}
		if(!empty($whereStatusTindakLanjut)){
			array_push($whereAll,$whereStatusTindakLanjut);
		}
		$whereCondition = !empty($whereAll) ? ' where '.implode(' and ',$whereAll) : '';
		$sql = <<<SQL
		select * from(
			SELECT mp.periode_siklus, ks.kode_kandang, ks.tgl_doc_in
				,(SELECT sum(JML_BOX) FROM BAP_DOC_SJ WHERE NO_REG = ks.NO_REG ) jmlboxterima
				,(SELECT distinct nama_hatchery FROM M_HATCHERY WHERE kode_hatchery IN (SELECT TOP 1 kode_hatchery FROM BAP_DOC_SJ WHERE NO_REG = ks.NO_REG) ) nama_hatchery
				,ks.JML_POPULASI/100 jmlbox
				,bd.jml_afkir
				,bd.bb_rata2/1000 bb_rata2
				,bd.uniformity
				,bd.stok_awal
				,bd.status
				,ks.no_reg
			FROM KANDANG_SIKLUS ks
			JOIN M_PERIODE mp ON mp.KODE_SIKLUS = ks.KODE_SIKLUS
			LEFT JOIN BAP_DOC bd ON bd.NO_REG = ks.NO_REG 
		--	LEFT JOIN M_HATCHERY mh ON mh.KODE_HATCHERY = bd.KODE_HATCHERY
			{$whereCondition}
		)x where x.jmlboxterima >= x.jmlbox order by x.kode_kandang asc	
SQL;
		
		return $this->db->query($sql);		
	}

	public function listBapdImport(){
		$sql = <<<SQL
	SELECT * FROM (	
		SELECT ks.kode_farm,ks.kode_siklus,mp.periode_siklus,count(bd.NO_SJ) jmlbapd, max(ks.TGL_DOC_IN) akhir_doc_in
		FROM KANDANG_SIKLUS ks
		JOIN M_PERIODE mp ON mp.KODE_SIKLUS = ks.KODE_SIKLUS
		LEFT JOIN BAP_DOC_BOX bd ON bd.NO_REG = ks.NO_REG
		WHERE ks.STATUS_SIKLUS != 'P'
		GROUP BY ks.kode_farm,ks.kode_siklus,mp.periode_siklus 
	)x WHERE datediff(day,x.akhir_doc_in,getdate()) >= 7
ORDER BY x.periode_siklus desc,x.kode_farm
SQL;
		return $this->db->query($sql);
	}
}
