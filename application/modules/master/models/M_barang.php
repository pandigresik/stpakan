<?php
class M_barang extends CI_Model{
	private $dbSqlServer ;
	
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}
	
	function get_satuanbarang(){
		$sql = <<<QUERY
			select uom, deskripsi, base_uom, konversi
			from m_uom
QUERY;
			
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_mastergrupbarang(){
		$sql = <<<QUERY
			select grup_barang as id, deskripsi as name
			from m_grup_barang
QUERY;
			
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function add_masterbarang($deskripsi){
		$sql = <<<QUERY
			insert into m_grup_barang (deskripsi) output inserted.grup_barang values('{$deskripsi}')
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result["grup_barang"];
	}
	
	function get_barang($start = null, $offset = null, $jenisbarang = null, $tipebarang = null, 
						$kodebarang = null, $namabarang = null, $bentukbarang = null, $satuan = null, $status = null){
		
		$filter_str = "";
		$filter_arr = array();
		
		$filter_bottom_str = "";
		$filter_bottom_arr = array();
		
		if(isset($jenisbarang))
			$filter_arr[] = "a.jenis_barang = '".$jenisbarang."'";
		if(isset($tipebarang))
			$filter_arr[] = "a.tipe_barang ='".$tipebarang."'";
		if(isset($kodebarang))
			$filter_arr[] = "a.kode_barang like '%".$kodebarang."%'";
		if(isset($namabarang))
			$filter_arr[] = "a.nama_barang like '%".$namabarang."%'";
		if(isset($bentukbarang))
			$filter_arr[] = "a.bentuk_barang = '".$bentukbarang."'";
		if(isset($satuan))
			$filter_arr[] = "a.uom = '".$satuan."'";
		if(isset($status))
			$filter_arr[] = "a.status_barang = '".$status."'";
		
		if(count($filter_arr) > 0){
			$filter_str .= " where ";
			$filter_str .= implode(" and ", $filter_arr);
		}
		
		if(isset($start) and isset($offset))
			$filter_bottom_arr[] = "row > {$start} and row <= {$offset}";
		
		if(count($filter_bottom_arr) > 0){
			$filter_bottom_str .= " where ";
			$filter_bottom_str .= implode(" and ", $filter_bottom_arr);
		}
		
		$sql = <<<QUERY
			select * from (
				select 
				ROW_NUMBER() OVER (ORDER BY a.nama_barang) as row,
				a.kode_barang, a.alias, a.nama_barang, a.jenis_barang, a.grup_barang, c.deskripsi as nama_grup_barang,
				a.uom, a.bentuk_barang, a.tipe_barang, a.pakan_betina, a.pakan_jantan, a.usia_awal_ternak, a.usia_akhir_ternak, 
				a.status_barang, 
				substring(convert (varchar, a.tgl_buat, 113),1,len(convert (varchar, a.tgl_buat, 113))-7) [tgl_buat], dbo.BENTUK_CONVERTION(a.bentuk_barang) bentuk_barang_konversi
				from m_barang a 
				inner join m_uom b on a.uom = b.uom
				inner join m_grup_barang c on a.grup_barang = c.grup_barang
				$filter_str
	) mainqry
	$filter_bottom_str
QUERY;
		
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_barang_by_id($kode_barang){
		$sql = <<<QUERY
			select 
				ROW_NUMBER() OVER (ORDER BY a.nama_barang) as row,
				a.kode_barang, a.alias, a.nama_barang, a.jenis_barang, a.grup_barang, c.deskripsi as nama_grup_barang,
				a.uom, a.bentuk_barang, a.tipe_barang, a.pakan_betina, a.pakan_jantan, a.usia_awal_ternak, a.usia_akhir_ternak, 
				a.status_barang, 
				substring(convert (varchar, a.tgl_buat, 113),1,len(convert (varchar, a.tgl_buat, 113))-7) [tgl_buat]
			from m_barang a 
				inner join m_uom b on a.uom = b.uom
				inner join m_grup_barang c on a.grup_barang = c.grup_barang
			where a.kode_barang = '{$kode_barang}'
	
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function cek_kode_barang($kodebarang){
		$sql = <<<QUERY
			select count(*) n_result
			from m_barang
			where kode_barang = '{$kodebarang}'
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function insert($data){
		$this->dbSqlServer->insert("m_barang", $data);
		
		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}
	
	function update($data, $id){
		$this->dbSqlServer->where("kode_barang", $id);
		$this->dbSqlServer->update("m_barang", $data); 
		
		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}
	
	function update_in($param_where,$where,$data){
		$this->dbSqlServer->where_in($param_where, $where);
		$this->dbSqlServer->update("m_barang", $data);
		return ($this->dbSqlServer->affected_rows() == 0) ? false : true;
	}
	
	function get_sinkron_barang($paramCari = array()){
		$cari = array('mb.STATUS_BARANG is null');
		if(!empty($paramCari)){
			foreach($paramCari as $k => $val){
				array_push($cari,' mb.'.$k.' like \'%'.$val.'%\' ');
			}
		}
		$cari = implode(' and ',$cari);
		$sql = <<<SQL
		select mb.KODE_BARANG kode_barang
				,mb.NAMA_BARANG nama_barang
				,dbo.bentuk_convertion(mb.BENTUK_BARANG) bentuk_barang
				,mgb.DESKRIPSI grup_barang
				,mb.status_barang status_barang
		from m_barang mb
		inner join M_GRUP_BARANG mgb
		on mb.GRUP_BARANG = mgb.GRUP_BARANG
		where {$cari}
SQL;
	return $this->dbSqlServer->query($sql);
	
	}
}