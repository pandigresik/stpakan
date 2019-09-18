<?php
class M_pelanggan extends CI_Model{
	private $dbSqlServer ;
	private $_table;
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
		$this->_table = 'M_PELANGGAN';
	}
	
	function get_pelanggan_browse(){
		$sql = <<<QUERY
			select kode_pelanggan, nama_pelanggan, alamat, kota, no_telp, term_pembayaran from m_pelanggan
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_pelanggan($start = null, $offset = null, $kodepelanggan = null, $namapelanggan = null,
						   $alamat = null, $kota = null){
		
		$filter_str = "";
		$filter_arr = array();
		
		$filter_bottom_str = "";
		$filter_bottom_arr = array();
		
		if(isset($kodepelanggan))
			$filter_arr[] = "a.kode_pelanggan like '%".$kodepelanggan."%'";
		if(isset($namapelanggan))
			$filter_arr[] = "a.nama_pelanggan like '%".$namapelanggan."%'";
		if(isset($alamat))
			$filter_arr[] = "a.alamat like '%".$alamat."%'";
		if(isset($kota))
			$filter_arr[] = "a.kota like '%".$kota."%'";
				
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
				ROW_NUMBER() OVER (ORDER BY a.nama_pelanggan) as row,
				a.kode_pelanggan, a.nama_pelanggan, a.alamat, a.kota
				from m_pelanggan a
				$filter_str
	) mainqry
	$filter_bottom_str
QUERY;
		
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function insert($data){
		$this->dbSqlServer->insert("m_pelanggan", $data);
		
		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}
	
	function update($data, $id){
		$this->dbSqlServer->where("kode_pelanggan", $id);
		$this->dbSqlServer->update("m_pelanggan", $data); 
		
		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}

	function check_kodepelanggan($kodepelanggan){
		$sql = <<<QUERY
			select count(*) n_result
			from m_pelanggan
			where kode_pelanggan = '{$kodepelanggan}'
	
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function checkNamaPelanggan($nama_pelanggan){
		$this->dbSqlServer->where("nama_pelanggan", $nama_pelanggan);		
		return $this->dbSqlServer->get('m_pelanggan')->result_array();
	}

	public function kode_pelanggan()
	{
		$no_urut = 0;
		$tmp = $this->db->order_by('kode_pelanggan','desc')->where('kode_pelanggan like CONVERT(VARCHAR(6), getdate(), 112)+\'%\'')->get($this->_table);
        $tmp = $tmp->row(0);

        $prefix = $this->dbSqlServer->query("SELECT CONVERT(VARCHAR(6), getdate(), 112) prefix")->result_array()[0]['prefix'];

		
        
        if(count($tmp) > 0){
           $no_urut = (int)substr($tmp->KODE_PELANGGAN,-3);
        }        
        $no_urut++;
        $no_urut = str_pad($no_urut,3,'0',STR_PAD_LEFT);
        

        return $prefix.$no_urut;
	}
}