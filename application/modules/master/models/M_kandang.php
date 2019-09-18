<?php
class M_kandang extends CI_Model{
	private $dbSqlServer ;

	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}

	function get_kandang($start = null, $offset = null, $namafarm = null, $namakandang = null,
						   $kapasitaskandangjantan = null, $kapasitaskandangbetina = null,$kapasitaskandang = null, $tipekandang = null, $tipelantai = null, $status = null){

		$filter_str = "";
		$filter_arr = array();

		$filter_bottom_str = "";
		$filter_bottom_arr = array();

		if(isset($namafarm))
			$filter_arr[] = "b.nama_farm like '%".$namafarm."%'";
		if(isset($namakandang))
			$filter_arr[] = "a.nama_kandang like '%".$namakandang."%'";
		if(isset($kapasitaskandangjantan))
			$filter_arr[] = "a.jml_jantan = '".$kapasitaskandangjantan."'";
		if(isset($kapasitaskandangbetina))
			$filter_arr[] = "a.jml_betina = '".$kapasitaskandangbetina."'";
		if(isset($kapasitaskandang))
				$filter_arr[] = "a.max_populasi = '".$kapasitaskandang."'";				
		if(isset($tipekandang))
			$filter_arr[] = "a.tipe_kandang = '".$tipekandang."'";
		if(isset($tipelantai))
			$filter_arr[] = "a.tipe_lantai = '".$tipelantai."'";
		if(isset($status))
			$filter_arr[] = "a.status_kandang = '".$status."'";

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
				select ROW_NUMBER() OVER (ORDER BY a.nama_kandang) as row, a.kode_kandang, b.kode_farm,
				b.nama_farm, a.nama_kandang, a.jml_jantan, a.jml_betina, a.max_populasi,
				a.luas_kandang_jantan, a.luas_kandang_betina, a.tipe_kandang,
				a.tipe_lantai, replace(a.status_kandang, ' ', '') status_kandang, replace(a.kode_verifikasi, ' ', '') kode_verifikasi
				from m_kandang a
				inner join pegawai_d c on c.kode_farm = a.kode_farm and c.kode_pegawai = '{$kode_user}'
				inner join m_farm b on a.kode_farm = b.kode_farm
				$filter_str
	) mainqry
	$filter_bottom_str
QUERY;

//		log_message("error", $sql);
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_kandang_by_id($kode_farm, $kode_kandang){
		$sql = <<<QUERY
			select a.kode_kandang, b.kode_farm,
				b.nama_farm, a.nama_kandang, a.jml_jantan, a.jml_betina,
				a.luas_kandang_jantan, a.luas_kandang_betina, a.tipe_kandang,
				a.tipe_lantai, a.status_kandang, a.kode_verifikasi,
				a.max_populasi,a.luas_kandang,a.no_flok,a.jml_sekat
			from m_kandang a
				inner join m_farm b on a.kode_farm = b.kode_farm
			where b.kode_farm = '{$kode_farm}' and a.kode_kandang = '{$kode_kandang}'

QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function cek_kode_kandang($kodefarm, $kodekandang){
		$sql = <<<QUERY
			select count(*) n_result
			from m_kandang
			where kode_farm = '{$kodefarm}' and kode_kandang = '{$kodekandang}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function check_digitcheck_kandang($kodefarm, $digit_check){
		$sql = <<<QUERY
			select count(*) n_result
			from m_kandang
			where kode_farm = '{$kodefarm}' kode_verifikasi = '{$digit_check}'

QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function insert($data){
		$this->dbSqlServer->insert("m_kandang", $data);

		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}

	function update($data, $kodefarm, $kodekandang){
		$this->dbSqlServer->where("kode_farm", $kodefarm);
		$this->dbSqlServer->where("kode_kandang", $kodekandang);
		$this->dbSqlServer->update("m_kandang", $data);

		return ($this->dbSqlServer->affected_rows() != 1) ? false : true;
	}

}
