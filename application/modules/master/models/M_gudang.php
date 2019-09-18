<?php
class M_gudang extends CI_Model{
	private $dbSqlServer ;

	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}

	function get_gudang($start = null, $offset = null, $namafarm = null, $kodegudang = null, $namagudang = null,$beratmaksimal = null,$qtymaksimal = null){

		$filter_str = "";
		$filter_arr = array();

		$filter_bottom_str = "";
		$filter_bottom_arr = array();

		if(isset($namafarm))
			$filter_arr[] = "b.nama_farm like '%".$namafarm."%'";
		if(isset($kodegudang))
			$filter_arr[] = "a.kode_gudang like '%".$kodegudang."%'";
		if(isset($namagudang))
			$filter_arr[] = "a.nama_gudang like '%".$namagudang."%'";
		if(isset($beratmaksimal))
			$filter_arr[] = "a.max_berat like '%".$beratmaksimal."%'";
		if(isset($qtymaksimal))
			$filter_arr[] = "a.max_kuantitas like '%".$qtymaksimal."%'";

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
				select ROW_NUMBER() OVER (ORDER BY a.nama_gudang) as row,
					   a.kode_farm, a.kode_gudang, a.nama_gudang, b.nama_farm, coalesce(a.max_berat,0) max_berat, coalesce(a.max_kuantitas,0) max_kuantitas
				from m_gudang a
				inner join pegawai_d c on c.kode_farm = a.kode_farm and c.kode_pegawai = '{$kode_user}'
				left join m_farm b on a.kode_farm = b.kode_farm
				$filter_str
	) mainqry
	$filter_bottom_str
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_gudang_by_id($kode_farm, $kode_gudang){
		$sql = <<<QUERY
			select ROW_NUMBER() OVER (ORDER BY a.nama_gudang) as row,
				   a.kode_farm, a.kode_gudang, a.nama_gudang, b.nama_farm, coalesce(a.max_berat,0) max_berat, coalesce(a.max_kuantitas,0) max_kuantitas
			from m_gudang a
			left join m_farm b on a.kode_farm = b.kode_farm
			where a.kode_farm = '{$kode_farm}' and a.kode_gudang = '{$kode_gudang}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function check_kode_gudang($kodefarm, $kodegudang){
		$sql = <<<QUERY
			select count(*) n_result
			from m_gudang
			where kode_farm = '{$kodefarm}' and kode_gudang = '{$kodegudang}'

QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function insert($data){
		$this->dbSqlServer->insert("m_gudang", $data);

		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}

	function update($data, $kodefarm, $kodegudang){
		$this->dbSqlServer->where("kode_farm", $kodefarm);
		$this->dbSqlServer->where("kode_gudang", $kodegudang);
		$this->dbSqlServer->update("m_gudang", $data);

		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}
}
