<?php
class M_std_budidaya extends CI_Model{
	private $dbSqlServer ;
	
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}
	
	function get_strain(){
		$sql = <<<QUERY
			select kode_strain, nama_strain, umur_awal_layer
			from m_strain
QUERY;
			
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_grup_pakan(){
		$sql = <<<QUERY
			select c.kode_barang, c.nama_barang, c.bentuk_barang grup_barang, c.bentuk deskripsi from(
				select 
			  a.nama_barang,
			  a.kode_barang,
			  (CONVERT(varchar(10), a.kode_barang)+'*'+a.bentuk_barang+'*'+CONVERT(varchar(10), a.grup_barang)) bentuk_barang, 
			  (a.nama_barang + ' ' + (select dbo.bentuk_convertion(a.bentuk_barang))) bentuk
				from m_barang a 
				inner join m_grup_barang b on b.grup_barang = a.grup_barang
				where a.bentuk_barang is not null
			) c
			group by c.kode_barang, c.nama_barang, c.bentuk_barang, c.bentuk
			order by c.nama_barang

QUERY;
		//query yg lama
		// $sql = <<<QUERY
			// select c.bentuk_barang grup_barang, c.bentuk deskripsi from(
				// select (CONVERT(varchar(10), a.grup_barang)+'-'+a.bentuk_barang) bentuk_barang, (b.deskripsi + ' ' + (select dbo.bentuk_convertion(a.bentuk_barang))) bentuk
				// from m_barang a 
				// inner join m_grup_barang b on b.grup_barang = a.grup_barang
				// where a.bentuk_barang is not null
			// ) c
			// group by c.bentuk_barang, c.bentuk
			// order by c.bentuk

// QUERY;
			
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function get_farm_bdy(){
		$sql = <<<QUERY
		select * from m_farm where GRUP_FARM = 'BDY' order by NAMA_FARM
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_last_std($kode_strain, $jenis_kelamin, $tipe_kandang, $m_in, $m_out){
		
		
		$sql = <<<QUERY
			select a.*,
				replace(convert(varchar(11),a.tgl_efektif_max,106),' ',' ') as tgl_efektif_max_formated
			from (
				select max(kode_std_breeding) as kode_std_breeding, kode_strain, jenis_kelamin, tipe_kandang, musim , max(tgl_efektif) as tgl_efektif_max  
				from m_std_breeding
				where 
				kode_strain = '{$kode_strain}' and
				jenis_kelamin = '{$jenis_kelamin}' and 
				tipe_kandang = '{$tipe_kandang}' and 
				(musim = '{$m_in}' or musim = '{$m_out}')
				group by kode_strain, jenis_kelamin, tipe_kandang, musim 
			) a
QUERY;
	
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_detail_std($kode_std, $musim){
		$sql = <<<QUERY
			select * from (
				select c.kode_std_breeding, min(c.std_umur) umur_awal, max(c.std_umur) umur_akhir, c.grup_barang, c.deskripsi, c.jenis_kelamin, c.tipe_kandang, c.musim, deskripsi_full 
				from (
				select a.kode_std_breeding, a.std_umur, (CONVERT(varchar(10), a.kode_barang)+'*'+a.bentuk+'*'+CONVERT(varchar(10), a.grup_barang)) grup_barang, b.deskripsi, a.jenis_kelamin, a.tipe_kandang, a.musim, 
				(d.nama_barang) deskripsi_full
				from m_std_breeding a 
				inner join m_grup_barang b on a.grup_barang = b.grup_barang 
				left join m_barang d on a.kode_barang = d.kode_barang 
				where a.kode_std_breeding = '{$kode_std}' and a.musim = '{$musim}'
				) c 
				group by c.kode_std_breeding, c.grup_barang, c.deskripsi, c.jenis_kelamin, c.tipe_kandang, c.musim, deskripsi_full
			) d order by d.umur_awal
QUERY;

		//old
		// $sql = <<<QUERY
			// select c.kode_std_breeding, min(c.std_umur) umur_awal, max(c.std_umur) umur_akhir, c.grup_barang, c.deskripsi, c.jenis_kelamin, c.tipe_kandang, c.musim, deskripsi_full 
			// from (
			// select a.kode_std_breeding, a.std_umur, (CONVERT(varchar(10), b.grup_barang)+'-'+a.bentuk) grup_barang, b.deskripsi, a.jenis_kelamin, a.tipe_kandang, a.musim, 
			// (b.deskripsi + ' ' + (select dbo.bentuk_convertion(a.bentuk))) deskripsi_full
			// from m_std_breeding a 
			// inner join m_grup_barang b on a.grup_barang = b.grup_barang 
			// where a.kode_std_breeding = '{$kode_std}' and a.musim = '{$musim}'
			// ) c 
			// group by c.kode_std_breeding, c.grup_barang, c.deskripsi, c.jenis_kelamin, c.tipe_kandang, c.musim, deskripsi_full;
// QUERY;
	
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_range_detail_std($kode_std, $musim){
		$sql = <<<QUERY
			select * from (
				select min(c.std_umur) umur_awal, max(c.std_umur) umur_akhir, c.deskripsi
				from (
				select a.kode_std_breeding, a.std_umur, a.masa_pertumbuhan, e.deskripsi
				from m_std_breeding a 
				inner join m_grup_barang b on a.grup_barang = b.grup_barang 
				left join m_barang d on a.kode_barang = d.kode_barang
				left join m_pertumbuhan e on e.kode_pertumbuhan = a.masa_pertumbuhan  
				where a.kode_std_breeding = '{$kode_std}' and a.musim = '{$musim}'
				) c 
				group by c.kode_std_breeding, c.masa_pertumbuhan, c.deskripsi
			) d order by d.umur_awal
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_detail_std_budidaya($kode_std, $musim){
		$sql = <<<QUERY
			select 
			  a.masa_pertumbuhan, 
			  c.deskripsi deskripsi_masa_pertumbuhan, 
			  a.kode_std_breeding,
			  a.std_umur,
			  convert(varchar(10),isnull(a.mati_prc,0)) mati_prc, convert(varchar(10),isnull(a.afkir_prc,0)) afkir_prc, convert(varchar(10),isnull(a.seleksi_prc,0)) seleksi_prc,
			  convert(varchar(10),isnull(a.dh_prc,0)) dh_prc,
			  convert(varchar(10),isnull(a.target_pkn,0)) target_pkn, convert(varchar(10),isnull(a.energi,0)) energi, convert(varchar(10),isnull(a.total_energi,0)) total_energi, convert(varchar(10),isnull(a.protein,0)) protein, convert(varchar(10),isnull(a.total_protein,0)) total_protein,
			  convert(varchar(10),isnull(a.target_bb,0)) target_bb, convert(varchar(10),isnull(a.bb_prc,0)) bb_prc,
			  a.grup_barang, b.deskripsi,
			  isnull(a.keterangan,'') keterangan, 
			  a.pengurangan_populasi as pengurangan,
			 (d.nama_barang) deskripsi_full
			  
			from m_std_breeding a 
			inner join m_grup_barang b on a.grup_barang = b.grup_barang 
			left join m_barang d on d.kode_barang = a.kode_barang 
			inner join m_pertumbuhan c on a.masa_pertumbuhan = c.kode_pertumbuhan 
			where a.kode_std_breeding = '{$kode_std}' and a.musim = '{$musim}' 
			order by a.std_umur
QUERY;
		
		
		// $sql = <<<QUERY
			// select 
			  // a.masa_pertumbuhan, 
			  // c.deskripsi deskripsi_masa_pertumbuhan, 
			  // a.kode_std_breeding,
			  // a.std_umur,
			  // convert(varchar(10),isnull(a.mati_prc,0)) mati_prc, convert(varchar(10),isnull(a.afkir_prc,0)) afkir_prc, convert(varchar(10),isnull(a.seleksi_prc,0)) seleksi_prc,
			  // convert(varchar(10),isnull(a.dh_prc,0)) dh_prc,
			  // convert(varchar(10),isnull(a.target_pkn,0)) target_pkn, convert(varchar(10),isnull(a.energi,0)) energi, convert(varchar(10),isnull(a.total_energi,0)) total_energi, convert(varchar(10),isnull(a.protein,0)) protein, convert(varchar(10),isnull(a.total_protein,0)) total_protein,
			  // convert(varchar(10),isnull(a.target_bb,0)) target_bb, convert(varchar(10),isnull(a.bb_prc,0)) bb_prc,
			  // a.grup_barang, b.deskripsi,
			  // isnull(a.keterangan,'') keterangan, 
			  // a.pengurangan_populasi as pengurangan,
			 // (b.deskripsi + ' ' + (select dbo.bentuk_convertion(a.bentuk))) deskripsi_full
			  
			// from m_std_breeding a 
			// inner join m_grup_barang b on a.grup_barang = b.grup_barang 
			// inner join m_pertumbuhan c on a.masa_pertumbuhan = c.kode_pertumbuhan
			// where a.kode_std_breeding = '{$kode_std}' and a.musim = '{$musim}' 
			// order by a.std_umur
// QUERY;
	
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_rows_std($kode_std, $umur_awal, $umur_akhir){
		$sql = <<<QUERY
			select 
				kode_std_breeding, kode_strain, musim, tipe_kandang, jenis_kelamin, tgl_efektif, std_umur, mati_prc, afkir_prc, 
				seleksi_prc, dh_prc, target_pkn, energi, total_energi, protein, total_protein, target_bb, bb_prc, grup_barang, 
				bentuk, masa_pertumbuhan, keterangan, pengurangan_populasi
			from m_std_breeding 
			where kode_std_breeding = '{$kode_std}' and std_umur >= {$umur_awal} and std_umur <= {$umur_akhir} 
			order by std_umur
QUERY;
	
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function insert_multiple_std_breeding($data){
		$this->dbSqlServer->trans_begin();
		
		$success = 0;
		foreach($data as $d){
			$this->dbSqlServer->insert("m_std_breeding", $d);
			
			if($this->dbSqlServer->affected_rows() > 0){
				$success++;
			}
		}
		
		if($success == count($data)){
			$this->dbSqlServer->trans_commit();
			return 1;
		}else{
			$this->dbSqlServer->trans_rollback();
			return 0;
		}	
		// $this->dbSqlServer->insert_batch("m_std_breeding", $data);
		// if($this->dbSqlServer->affected_rows() > 0)
			// return $this->dbSqlServer->affected_rows();
		// else
			// return 0;
	}
	
	function update_std_breeding($data, $kode, $umur){
		$this->dbSqlServer->trans_begin();
		
		$success = 0;
		for($i=0;$i<count($umur);$i++){
			$detail = $data[$i];
			
			$this->dbSqlServer->where("kode_std_breeding", $kode);
			$this->dbSqlServer->where("std_umur", $umur[$i]);
			$this->dbSqlServer->update("m_std_breeding", $detail);
			
			if($this->dbSqlServer->affected_rows() > 0){
				$success++;
			}
		}
		
		if($success == count($umur)){
			$this->dbSqlServer->trans_commit();
			return true;
		}else{
			$this->dbSqlServer->trans_rollback();
			return false;
		}		
		
	}
	
	function get_last_std_formated($format){
		$sql = <<<QUERY
			select max(kode_std_breeding) kode_std_breeding
			from m_std_breeding
			where kode_std_breeding like '{$format}%'
QUERY;
	
		log_message("error", $sql);
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function get_range_kebutuhan_pakan($kode_strain){
		$sql = <<<QUERY
			select kode_strain, min(umur_awal) umur_awal, max(umur_akhir) umur_akhir 
			from masa_pertumbuhan 
			where kode_strain = '{$kode_strain}'
			group by kode_strain
QUERY;
	
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function get_masa_pertumbuhan($kode_strain){
		$sql = <<<QUERY
			select b.kode_pertumbuhan, b.deskripsi, a.umur_awal, a.umur_akhir 
			from masa_pertumbuhan a 
			inner join m_pertumbuhan b on a.kode_pertumbuhan = b.kode_pertumbuhan
			where a.kode_strain = '{$kode_strain}'

QUERY;
	
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}