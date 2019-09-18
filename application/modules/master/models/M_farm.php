<?php
class M_farm extends CI_Model{
	private $dbSqlServer ;
	
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}

	private function connect()
    {
        try {
            $dbc = new \PDO($this->dbSqlServer->dsn, $this->dbSqlServer->username, $this->dbSqlServer->password);

            $dbc->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return (Object)array(
                'return' => $dbc,
                'message' => 'connected.'
            );
        } catch (\PDOException $e) {
            return (Object)array(
                'return' => false,
                'message' => $e->getMessage()
            );
        }
    }

    public function queries($qry)
    {
    	$pdo = $this->connect()->return;

        $stmt = $pdo->prepare($qry);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
	function get_farm_browse(){
		$sql = <<<QUERY
			select * from (
				select ROW_NUMBER() OVER (ORDER BY a.nama_farm) as row,
					   a.kode_farm, a.kode_pelanggan, b.nama_pelanggan as grup_pelanggan, a.nama_farm, 
					   a.alamat_farm as alamat, a.kota, a.tipe_farm, a.grup_farm,
					 a.nama_farm + ' - ' + a.kode_farm as nama_farm_full,
					a.jml_flok
				from m_farm a
				left join m_pelanggan b on a.kode_pelanggan = b.kode_pelanggan
	) mainqry
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function get_kota_browse(){
		$sql = <<<QUERY
			select kota, propinsi from m_kota
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function cek_kode_farm($kodefarm){
		$sql = <<<QUERY
			select count(*) jml from m_farm where kode_farm = '{$kodefarm}'
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	
	function get_farm($start = null, $offset = null, $kodefarm = null, $namafarm = null, $alamatfarm = null, 
							$kotafarm = null, $tipefarm = null, $grupfarm = null, $gruppelanggan = null){
		
		$filter_str = "";
		$filter_arr = array();
		
		$filter_bottom_str = "";
		$filter_bottom_arr = array();
		
		if(isset($kodefarm))
			$filter_arr[] = "a.kode_farm like '%".$kodefarm."%'";
		if(isset($namafarm))
			$filter_arr[] = "a.nama_farm like '%".$namafarm."%'";
		if(isset($alamatfarm))
			$filter_arr[] = "a.alamat_farm like '%".$alamatfarm."%'";
		if(isset($kotafarm))
			$filter_arr[] = "a.kota like '%".$kotafarm."%'";
		if(isset($tipefarm))
			$filter_arr[] = "a.tipe_farm = '".$tipefarm."'";
		if(isset($grupfarm))
			$filter_arr[] = "a.grup_farm = '".$grupfarm."'";
		if(isset($gruppelanggan))
			$filter_arr[] = "b.grup_pelanggan like '%".$gruppelanggan."%'";
				
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
		
		$kode_user = $this->session->userdata("kode_user");
				
		$sql = <<<QUERY
			select * from (
				select ROW_NUMBER() OVER (ORDER BY a.nama_farm) as row,
					   a.kode_farm, a.kode_pelanggan, b.nama_pelanggan as grup_pelanggan, a.nama_farm, 
					   a.alamat_farm as alamat, a.kota, a.tipe_farm, a.grup_farm, a.jml_flok
				from m_farm a
				inner join pegawai_d c on c.kode_farm = a.kode_farm and c.kode_pegawai = '{$kode_user}'
				left join m_pelanggan b on a.kode_pelanggan = b.kode_pelanggan
				$filter_str
	) mainqry
	$filter_bottom_str
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function insert($data){
		$this->dbSqlServer->insert("m_farm", $data);
		
		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}
	
	function update($data, $id){
		$this->dbSqlServer->where("kode_farm", $id);
		$this->dbSqlServer->update("m_farm", $data); 
		
		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}
}