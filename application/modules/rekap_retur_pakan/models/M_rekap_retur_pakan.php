<?php
class M_rekap_retur_pakan extends CI_Model{
	private $dbSqlServer ;
	
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}
	
	function get_today(){
		$sql = <<<QUERY
		select getdate() as [today]
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function get_retur_pakan($kode_farm, $tgl_awal = null, $tgl_akhir = null){
		$filter = "";
		if(isset($tgl_awal) and isset($tgl_akhir)){
			$filter = "and rhk.tgl_transaksi >= '" . $tgl_awal . "' and rhk.tgl_transaksi <= '" . $tgl_awal . "'";
		}elseif(isset($tgl_awal) and !isset($tgl_akhir)){
			$filter = "and rhk.tgl_transaksi >= '" . $tgl_awal . "'";
		}elseif(!isset($tgl_awal) and isset($tgl_akhir)){
			$filter = "and rhk.tgl_transaksi <= '" . $tgl_awal . "'";
		}else{
			$filter = "";
		}		
		
		$sql = <<<QUERY
		select substring(convert (varchar, rhk.tgl_transaksi, 113), 1, len(convert (varchar, rhk.tgl_transaksi, 113))) [tgl_tutupsiklus], 
			ks.kode_farm, ks.kode_kandang, mk.nama_kandang, rk.no_retur, rk.no_reg, 
			substring(convert (varchar, rkd.tgl_retur, 113),1,len(convert (varchar, rkd.tgl_retur, 113))-7) tgl_retur, 
			substring(convert (varchar, rkd.tgl_on_putaway, 113),1,len(convert (varchar, rkd.tgl_on_putaway, 113))-7) tgl_on_putaway,
			substring(convert (varchar, rkd.tgl_putaway, 113),1,len(convert (varchar, rkd.tgl_putaway, 113))-7) tgl_putaway,
			rkd.kode_barang, mb.nama_barang, rkd.jml_on_retur, rkd.brt_on_retur, rkd.jml_retur, rkd.brt_retur, rkd.jml_putaway, rkd.brt_putaway,
			substring(convert (varchar, rk.tgl_approve, 113),1,len(convert (varchar, rk.tgl_approve, 113))-7) tgl_approve,
			substring(convert (varchar, rk.tgl_terima, 113),1,len(convert (varchar, rk.tgl_terima, 113))-7) tgl_terima,	
			rk.USER_BUAT, mp1.NAMA_PEGAWAI nama_buat, rk.user_approve, mp2.nama_pegawai nama_approve, rk.user_terima, mp3.NAMA_PEGAWAI nama_terima   
		from kandang_siklus ks
		inner join (
		  select a.* 
		  from rhk a 
		  where tgl_transaksi in (
			select top 1 b.tgl_transaksi
			from rhk b
			where b.no_reg = a.no_reg
			order by tgl_transaksi desc
		  )
		) rhk on rhk.no_reg = ks.no_reg
		inner join retur_kandang rk on rk.no_reg = ks.no_reg
		inner join m_kandang mk on mk.kode_kandang = ks.kode_kandang and mk.kode_farm = ks.kode_farm
		left join retur_kandang_d rkd on rkd.no_retur = rk.no_retur and rkd.no_reg = rk.no_reg
		inner join m_barang mb on mb.kode_barang = rkd.kode_barang 
		left join M_PEGAWAI mp1 on mp1.KODE_PEGAWAI = rk.USER_BUAT
		left join M_PEGAWAI mp2 on mp2.KODE_PEGAWAI = rk.USER_approve
		left join M_PEGAWAI mp3 on mp3.KODE_PEGAWAI = rk.USER_terima
		where ks.status_siklus = 'C' 
		and ks.kode_farm = '{$kode_farm}' 
		$filter
QUERY;

		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_retur_pakan_detail($no_retur, $no_reg){
		$sql = <<<QUERY
		select a.NO_RETUR, a.NO_REG, b.KODE_BARANG, c.NAMA_BARANG, dbo.BENTUK_CONVERTION(c.BENTUK_BARANG) BENTUK_BARANG, 
		coalesce(b.JML_ON_RETUR, b.JML_RETUR) JML, coalesce(b.BRT_ON_RETUR, b.BRT_RETUR) BRT, 
		substring(convert (varchar, b.tgl_on_putaway, 113),1,len(convert (varchar, b.tgl_on_putaway, 113))-7) TGL_ON_PUTAWAY, 
		substring(convert (varchar, b.TGL_RETUR, 113),1,len(convert (varchar, b.TGL_RETUR, 113))-7) tgl_retur,
		substring(convert (varchar, a.TGL_APPROVE, 113),1,len(convert (varchar, a.TGL_APPROVE, 113))-7) tgl_approve,
		substring(convert (varchar, a.TGL_TERIMA, 113),1,len(convert (varchar, a.TGL_TERIMA, 113))-7) tgl_terima,
		mp1.NAMA_PEGAWAI user_buat, mp2.NAMA_PEGAWAI user_approve, mp3.NAMA_PEGAWAI user_terima 
		from RETUR_KANDANG a 
		inner join RETUR_KANDANG_D b on b.NO_RETUR = a.NO_RETUR AND b.NO_REG = a.NO_REG 
		inner join M_BARANG c on c.KODE_BARANG = b.KODE_BARANG 
		left join M_PEGAWAI mp1 on mp1.KODE_PEGAWAI = a.USER_BUAT 
		left join M_PEGAWAI mp2 on mp2.KODE_PEGAWAI = a.USER_approve 
		left join M_PEGAWAI mp3 on mp3.KODE_PEGAWAI = a.USER_terima 
		where a.NO_RETUR = '{$no_retur}' and a.NO_REG = '{$no_reg}'
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function proses_pengajuan_retur($no_retur, $no_reg, $user){
		$sql = <<<QUERY
		update retur_kandang_d set 
			jml_retur = jml_on_retur, 
			brt_retur = brt_on_retur, 
			jml_on_retur = null, 
			brt_on_retur = null,
			tgl_retur = getdate(),
			tgl_ubah = getdate(),
			user_ubah = '{$user}'
		where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        if($stmt->execute())
			return true;
		else
			return false;
	}
	
	function proses_persetujuan_retur($no_retur, $no_reg, $user, $level_user){
		if($level_user == "KF"){
			$sql = <<<QUERY
			update retur_kandang_d set  
				jml_on_putaway = jml_retur, 
				brt_on_putaway = brt_retur,
				tgl_on_putaway = getdate(),
				tgl_ubah = getdate(),
				user_ubah = '{$user}'
			where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;
		}elseif($level_user == "AG"){
			$sql = <<<QUERY
			update retur_kandang_d set  
				jml_putaway = jml_retur, 
				brt_putaway = brt_retur,
				tgl_putaway = getdate(),
				tgl_ubah = getdate(),
				user_ubah = '{$user}'
			where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;
		}else{
			$sql = <<<QUERY
				
QUERY;
		}
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        if($stmt->execute()){
			$status = false;
			if($level_user == "KF"){
				$sql = <<<QUERY
				update retur_kandang set  
				tgl_approve = getdate(),
				keterangan1 = '{$no_retur}',
				user_approve = '{$user}'
				where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;
				$stmt = $this->dbSqlServer->conn_id->prepare($sql);
				$stmt->execute();
				
				$status = true;
			}elseif($level_user == "AG"){
				$sql = <<<QUERY
				update retur_kandang set  
				tgl_terima = getdate(),
				keterangan1 = '{$no_retur}',
				user_terima = '{$user}'
				where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;
				$stmt = $this->dbSqlServer->conn_id->prepare($sql);
				$stmt->execute();
				
				$status = true;
			}else{
				$sql = <<<QUERY
				
QUERY;
			}
			
			if($status){
				$sql = <<<QUERY
				select nama_pegawai from m_pegawai where kode_pegawai = '{$user}'
QUERY;
				$stmt = $this->dbSqlServer->conn_id->prepare($sql);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				
				return $result["nama_pegawai"];
			}
			
			return "failed";
		}else
			return "failed";
	}
}