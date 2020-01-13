<?php
class M_realisasi_panen extends CI_Model{
	private $dbSqlServer ;

	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}
	
	public function get_data_do($noreg = NULL){
		$whereNoreg = '';
		if(!empty($noreg)){
			$whereNoreg = ' and do.NO_REG = \''.$noreg.'\'';
		}
		$sql = <<<QUERY
		select plg.NAMA_PELANGGAN
		,NO_DO,KODE_PELANGGAN,JUMLAH,TGL_BUAT,BERAT,TGL_PANEN,KODE_FARM,NO_REG,NO_SJ,RIT,NOPOL,ID_SOPIR,SOPIR,NIK_TIMPANEN,MULAI_PANEN,SELESAI_PANEN,jam_brngkt,jam_tiba_farm,jam_tiba_rpa,jam_potong 
		,CASE WHEN substring(DO.NO_DO,0,4) = 'BDY' THEN BERAT + (BERAT * .1) ELSE 2500 END BERAT_MAX
		,CASE WHEN substring(DO.NO_DO,0,4) = 'BDY' THEN 3300 ELSE 2500 END MAX_RIT
		from REALISASI_PANEN_DO do
		inner join M_PELANGGAN plg on plg.kode_pelanggan = do.kode_pelanggan 
		left join REALISASI_PANEN panen on panen.NO_DO = do.NO_DO
		where panen.NO_DO is null {$whereNoreg}
QUERY;
		#log_message('error',$sql);
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function get_do_nyeser($noreg){
		$periodeSiklus = substr($noreg,0,9);
		$sqlDO = <<<SQL
		SELECT do_panen.no_do
			,do_panen.rit
			,do_panen.tgl_panen				
			,0 sudah_panen
		FROM REALISASI_PANEN_DO do_panen
		INNER JOIN (
			SELECT TGL_PANEN,kode_farm,rit FROM REALISASI_PANEN_DO
			WHERE rit IS NOT NULL
			GROUP BY TGL_PANEN,kode_farm,rit 
			HAVING count(*) > 1
		)do_nyeser ON do_nyeser.tgl_panen = do_panen.tgl_panen AND do_nyeser.kode_farm =  do_panen.kode_farm AND do_nyeser.rit = do_panen.rit		
		WHERE do_panen.no_reg = '{$noreg}'

SQL;
		$do_nyeser = $this->db->query($sqlDO)->result_array();
		if(!empty($do_nyeser)){
			/* cari yang sudah dipanen pertanggal panen dan perrit*/			
			$sql = <<<SQL
			SELECT do_panen.tgl_panen,do_panen.no_do,do_panen.rit,do_panen.nopol
				,(SELECT sum(berat_aktual) FROM REALISASI_PANEN WHERE NO_DO IN (SELECT NO_DO FROM REALISASI_PANEN_DO rpd WHERE rpd.RIT = do_panen.rit AND rpd.TGL_PANEN = do_panen.tgl_panen) )  berat
				FROM REALISASI_PANEN_DO do_panen
					INNER JOIN (
						SELECT TGL_PANEN,kode_farm,rit,nopol FROM REALISASI_PANEN_DO
						WHERE rit IS NOT NULL AND no_reg like '{$periodeSiklus}%' 
						GROUP BY TGL_PANEN,kode_farm,rit,nopol 
						HAVING count(*) > 1
					)do_nyeser ON do_nyeser.tgl_panen = do_panen.tgl_panen AND do_nyeser.kode_farm =  do_panen.kode_farm AND do_nyeser.rit = do_panen.rit AND do_nyeser.nopol = do_panen.nopol
				WHERE do_panen.no_reg like '{$periodeSiklus}%' 						
SQL;
			$panen = $this->db->query($sql)->result_array();		
			if(!empty($panen)){
				// $panen = arr2DToarrKey($panen,'tgl_panen');
				$_panen = array();
				foreach($panen as $_tmp){
					$rit = $_tmp['rit'];
					$tgl_panen = $_tmp['tgl_panen'];
					if(!isset($_panen[$tgl_panen])){
						$_panen[$tgl_panen] = array();
					}
					if(!isset($_panen[$tgl_panen][$rit])){
						$_panen[$tgl_panen][$rit] = array('berat' => $_tmp['berat']);
					}
				}
				foreach($do_nyeser as &$_do){
					if(isset($_panen[$_do['tgl_panen']])){
						$rit = $_do['rit'];
						if(isset($_panen[$_do['tgl_panen']][$rit])){
							$_do['sudah_panen'] = $_panen[$_do['tgl_panen']][$rit]['berat'];
						}						
					}
				}
			}
		}
		
		return $do_nyeser;
	}
	public function get_realisasi_panen($no_reg){
		$sql = <<<QUERY
		select rp.no_surat_jalan, rp.no_do, rp.no_reg, rp.tgl_panen, rp.umur_panen, rp.berat_tara, rp.berat_aktual,
			rp.jumlah_aktual, rp.berat_badan_rata2, rp.tgl_datang, rp.tgl_mulai, rp.tgl_selesai, rp.berat_akhir, rp.jumlah_akhir, coalesce(rp.berat_timbang,0) berat_timbang, coalesce(rp.jumlah_timbang,0) jumlah_timbang, 
			substring(convert (varchar, rp.tgl_panen, 113),1,len(convert (varchar, rp.tgl_panen, 113))) tgl_panen_format,
			substring(convert (varchar, rp.tgl_datang, 113),1,len(convert (varchar, rp.tgl_datang, 113))-7) tgl_datang_format,
			substring(convert (varchar, rp.tgl_mulai, 113),1,len(convert (varchar, rp.tgl_mulai, 113))-7) tgl_mulai_format,
			substring(convert (varchar, rp.tgl_buat, 113),1,len(convert (varchar, rp.tgl_buat, 113))-13) tgl_buat_format,
			substring(convert (varchar, rp.tgl_selesai, 113),1,len(convert (varchar, rp.tgl_selesai, 113))-7) tgl_selesai_format,
			rpd.kode_pelanggan, rpd.berat berat_do, rpd.jumlah jumlah_do, 
			mp.nama_pelanggan
		from realisasi_panen rp 
		left join realisasi_panen_do rpd on rpd.no_do = rp.no_do 
		left join m_pelanggan mp on mp.kode_pelanggan = rpd.kode_pelanggan 
		where rp.no_reg = '{$no_reg}' 
		order by rp.tgl_panen asc;
QUERY;
	
	//	log_message("error", $sql);
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	}

	function simpan_panen($panen, $panen_filter, $panen_tara, $panen_detail){
		$pass = true;
		
		if($pass){
			$this->dbSqlServer->trans_begin();
			$this->dbSqlServer->where($panen_filter);
			$this->dbSqlServer->update("realisasi_panen", $panen);
			
		//	log_message("error", $this->dbSqlServer->last_query());
			
			$realisasi_panen_tara_result;			
			if($this->dbSqlServer->affected_rows() > 0){
				if(is_array($panen_tara) and count($panen_tara) > 0){
					$success = 0;
					for($i=0;$i<count($panen_tara);$i++){
						$this->dbSqlServer->insert("realisasi_panen_tara_keranjang", $panen_tara[$i]);
						log_message("error", $this->dbSqlServer->last_query());
						if($this->dbSqlServer->affected_rows() > 0){
							$success++;
						}
					}
					
					if($success == count($panen_tara))
						$realisasi_panen_tara_result = true;
					else
						$realisasi_panen_tara_result = false;
				}else{
					$realisasi_panen_tara_result = true;
				}
				
				if($realisasi_panen_tara_result){
					$realisasi_panen_detail_result;
					if(is_array($panen_detail) and count($panen_detail) > 0){
						$success = 0;
						for($i=0;$i<count($panen_detail);$i++){
							$this->dbSqlServer->insert("realisasi_panen_detail", $panen_detail[$i]);
							log_message("error", $this->dbSqlServer->last_query());
							if($this->dbSqlServer->affected_rows() > 0){
								$success++;
							}
						}

						if($success == count($panen_detail))
							$realisasi_panen_detail_result = true;
						else
							$realisasi_panen_detail_result = false;
					}else
						$realisasi_panen_detail_result = true;
					
					if($realisasi_panen_detail_result){
						$this->dbSqlServer->trans_commit();
						return true;
					}else{
						$this->dbSqlServer->trans_rollback();
						return false;
					}
				}else{
					$this->dbSqlServer->trans_rollback();
					return false;
				}
			}else{
				$this->dbSqlServer->trans_rollback();
				return false;
			}
		}
	}
	
	function get_tara_panen($noreg, $nodo, $nosj){
		$sql = <<<QUERY
			select rpt.* 
			from REALISASI_PANEN rp
			inner join REALISASI_PANEN_TARA_KERANJANG rpt ON rpt.NO_SURAT_JALAN = rp.NO_SURAT_JALAN 
			where rp.NO_REG = '{$noreg}' and rp.NO_DO = '{$nodo}' and rp.NO_SURAT_JALAN = '{$nosj}' 
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_ayam_panen($noreg, $nodo, $nosj){
		$sql = <<<QUERY
			select rpd.* 
			from REALISASI_PANEN rp
			inner join REALISASI_PANEN_DETAIL rpd ON rpd.NO_SURAT_JALAN = rp.NO_SURAT_JALAN
			where rp.NO_REG = '{$noreg}' and rp.NO_DO = '{$nodo}' and rp.NO_SURAT_JALAN = '{$nosj}' 
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function simpan_admin_farm($panen){
		$this->dbSqlServer->trans_begin();
		$this->dbSqlServer->insert("realisasi_panen", $panen);
		
		if($this->dbSqlServer->affected_rows() > 0){
			$this->dbSqlServer->trans_commit();
			return true;
		}else{
			$this->dbSqlServer->trans_rollback();
			return false;
		}
	}
	
	function simpan_do_susulan($panen, $no_reg, $no_sj){
		$this->dbSqlServer->where("no_reg", $no_reg);
		$this->dbSqlServer->where("no_surat_jalan", $no_sj);
		$this->dbSqlServer->update("realisasi_panen", $panen);
		
		if($this->dbSqlServer->affected_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
	
	function get_realisasi_panen_spec($no_reg, $no_do, $no_sj){
		$sql = <<<QUERY
			select rp.* 
			from REALISASI_PANEN rp
			where rp.NO_REG = '{$no_reg}' and rp.NO_DO = '{$no_do}' and rp.NO_SURAT_JALAN = '{$no_sj}' 
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function cek_sj($no_sj){
		$sql = <<<QUERY
			select count(*) result
			from REALISASI_PANEN where NO_SURAT_JALAN = '{$no_sj}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
}