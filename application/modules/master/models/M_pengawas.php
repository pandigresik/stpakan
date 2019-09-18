<?php
class M_pengawas extends CI_Model{
	private $dbSqlServer ;
	
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}
	
	function get_pengawas_by_id($kode_pengawas){
		$sql = <<<QUERY
			select a.kode_pegawai, a.nama_pegawai, 
				a.jenis_kelamin, a.no_telp, a.grup_pegawai,
				a.username, a.password, a.status_pegawai
			from m_pegawai a
			where a.kode_pegawai = '{$kode_pengawas}'
	
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function get_pengawas($start = null, $offset = null, $kodepengawas = null, $namapengawas = null,
						   $jeniskelamin = null, $status = null){
		
		$filter_str = "";
		$filter_arr = array();
		
		$filter_bottom_str = "";
		$filter_bottom_arr = array();
		
		if(isset($kodepengawas))
			$filter_arr[] = "a.kode_pegawai like '%".$kodepengawas."%'";
		if(isset($namapengawas))
			$filter_arr[] = "a.nama_pegawai like '%".$namapengawas."%'";
		if(isset($jeniskelamin))
			$filter_arr[] = "a.jenis_kelamin = '".$jeniskelamin."'";
		if(isset($status))
			$filter_arr[] = "a.status_pegawai = '".$status."'";
				
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
				ROW_NUMBER() OVER (ORDER BY a.kode_pegawai) as row,
				a.kode_pegawai, a.nama_pegawai, a.jenis_kelamin, a.no_telp, a.grup_pegawai, a.username, a.password, a.status_pegawai 
				from m_pegawai a
				$filter_str
	) mainqry
	$filter_bottom_str
QUERY;
			
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_next_kode_pengawas(){
		$sql = <<<QUERY
			select 'PG' + right(replicate('0',4)+cast((substring(kode, 4, 8) + 1) as varchar(15)),4) aS kode
			from (
			select max(kode_pegawai)as kode
			from m_pegawai
			) qry
QUERY;
			
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function insert($data){
		$this->dbSqlServer->insert("m_pegawai", $data);
		
		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}
	
	function insert_d($data){
		$this->dbSqlServer->insert("pegawai_d", $data);
		
		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}
	
	function update($data, $id){
		$this->dbSqlServer->where("kode_pegawai", $id);
		$this->dbSqlServer->update("m_pegawai", $data); 
		
		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}
	
	function get_username($username){
		$sql = <<<QUERY
			select count(*) eksis from m_pegawai where username = '{$username}'
QUERY;
			
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function get_gruppegawai(){
		$sql = <<<QUERY
			select grup_pegawai, deskripsi from m_grup_pegawai order by deskripsi asc
QUERY;
			
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}