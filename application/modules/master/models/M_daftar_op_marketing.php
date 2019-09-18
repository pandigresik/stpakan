<?php
class M_daftar_op_marketing extends CI_Model {
	private $dbSqlServer;
	public function __construct() {
		parent::__construct ();
		$this->dbSqlServer = $this->load->database ( "default", true );
	}
	function get_op_marketing($start = null, $offset = null, $grup = null, $tahun = null, $tanggal_kirim = null, $no_op_awal = null, $no_op_akhir = null, $no_op_pakai = null, $farm = null) {
		$filter_str = "";
		$filter_arr = array ();
		
		$filter_bottom_str = "";
		$filter_bottom_arr = array ();
		
		if (isset ( $grup ))
			$filter_arr [] = "mop.GRUP_FARM like '%" . $grup . "%'";
		if (isset ( $tahun ))
			$filter_arr [] = "mop.TAHUN like '%" . $tahun . "%'";
		if (isset ( $tanggal_kirim ))
			$filter_arr [] = "CAST(mop.TGL_KIRIM AS DATE) = '" . $tanggal_kirim . "'";
		if (isset ( $no_op_awal ))
			$filter_arr [] = "mop.NO_OP_AWAL like '%" . $no_op_awal . "%'";
		if (isset ( $no_op_akhir ))
			$filter_arr [] = "mop.NO_OP_AKHIR like '%" . $no_op_akhir . "%'";
		if (isset ( $no_op_pakai ))
			$filter_arr [] = "mop.NO_OP_PAKAI like '%" . $no_op_pakai . "%'";
		if (isset ( $farm ))
			$filter_arr [] = "mop.KODE_FARM like '%" . $farm . "%'";
		
		if (count ( $filter_arr ) > 0) {
			$filter_str .= " where ";
			$filter_str .= implode ( " and ", $filter_arr );
		}
		
		if (isset ( $start ) and isset ( $offset ))
			$filter_bottom_arr [] = "row > {$start} and row <= {$offset}";
		
		if (count ( $filter_bottom_arr ) > 0) {
			$filter_bottom_str .= " where ";
			$filter_bottom_str .= implode ( " and ", $filter_bottom_arr );
		}
		
		$sql = <<<QUERY
			select * from (
				SELECT 
					ROW_NUMBER() OVER (ORDER BY mop.TGL_KIRIM ASC) AS ROW
					, mop.* 
					, REPLACE(CONVERT(VARCHAR(10),CAST(TGL_KIRIM AS DATETIME),105),'-',' ') TGL_KIRIM_TEXT 
					, CASE
						WHEN mop.GRUP_FARM = 'BDY' THEN 'Budidaya'
						WHEN mop.GRUP_FARM = 'BRD' THEN 'Breeding'
						ELSE 'None'
					END GRUP_FARM_LABEL,
					mf.NAMA_FARM
				FROM M_OP mop 
				JOIN m_farm mf ON mop.KODE_FARM = mf.KODE_FARM
				$filter_str
			) mainqry
			$filter_bottom_str
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	function get_op_marketing_by_id($tanggal_kirim, $kode_farm) {
		$sql = <<<QUERY
				SELECT 
					mop.* 
					, REPLACE(CONVERT(VARCHAR(10),CAST(TGL_KIRIM AS DATETIME),105),'-',' ') TGL_KIRIM_TEXT
				FROM M_OP mop
				where CAST(mop.TGL_KIRIM AS DATE) = '{$tanggal_kirim}' and mop.KODE_FARM = '{$kode_farm}'
	
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function kontrol_op_pakai ( $tahun,$no_op_awal,$no_op_akhir,$no_op_pakai ) {
		$params = "";
		if(!empty($tahun)){
			$params = "and cast(SUBSTRING(NO_OP,CHARINDEX('/',NO_OP)+1,100) as int) = cast('$tahun' as int)";
		}
		$sql = <<<QUERY
				select count(*) n_count from op
				where cast(SUBSTRING(NO_OP,1,CHARINDEX('/',NO_OP)-1) as int) = cast('$no_op_pakai' as int) --between 135 and 137
				$params
	
QUERY;
		#echo $sql;
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function kontrol_simpan ( $tahun,$no_op_awal,$no_op_akhir,$no_op_pakai ) {
		$sql = <<<QUERY
				select count(*) n_count from op
				where cast(SUBSTRING(NO_OP,1,CHARINDEX('/',NO_OP)-1) as int) between cast('$no_op_awal' as int) and cast('$no_op_akhir' as int)
				and cast(SUBSTRING(NO_OP,CHARINDEX('/',NO_OP)+1,100) as int) = cast('$tahun' as int)
	
QUERY;
		#echo $sql;
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function kontrol_kirim($tanggal_kirim) {
		$sql = <<<QUERY
				select
					case 
						when cast('$tanggal_kirim' as date) < cast(getdate() as date) then 0
						else 1
					end result
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function get_grup_farm($kode_farm = NULL) {
		$condition = empty ( $kode_farm ) ? "" : "WHERE KODE_FARM = '{$kode_farm}'";
		$sql = <<<QUERY
				SELECT DISTINCT
					GRUP_FARM
					, CASE
						WHEN GRUP_FARM = 'BDY' THEN 'Budidaya'
						WHEN GRUP_FARM = 'BRD' THEN 'Breeding'
						ELSE 'None'
					END GRUP_FARM_LABEL
				FROM M_FARM
				$condition
	
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	
	function get_nama_farm($kode_grup_farm = NULL, $kode_farm = NULL) {
		$condition = array();
		$condition_str = "";
		
		if(isset ($kode_grup_farm) and !empty ( $kode_grup_farm )){
			$condition[] = " GRUP_FARM = '{$kode_grup_farm}' ";
		}
		
		if(isset($kode_farm) and !empty ( $kode_farm)){
			$condition[] = " KODE_FARM = '{$kode_farm}' ";
		}
		
		if(count($condition) > 0){
			$condition_str = " WHERE ".implode(" AND ", $condition);
		}
		
		$sql = <<<QUERY
				SELECT KODE_FARM, NAMA_FARM
				FROM M_FARM
				$condition_str
	
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	
	function get_tahun() {
		$sql = <<<QUERY
				SELECT DISTINCT
					TAHUN
				FROM M_OP
				ORDER BY TAHUN ASC	
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	function check_op_marketing($tanggal_kirim, $kode_farm) {
		$sql = <<<QUERY
				SELECT 
					count(*) n_result 
				FROM M_OP mop
				where CAST(mop.TGL_KIRIM AS DATE) = '{$tanggal_kirim}' and mop.KODE_FARM = '{$kode_farm}'
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function insert($data) {
		$this->db->insert ( "M_OP", $data );
		
		return ($this->db->affected_rows () != 1) ? false : true;
	}
	function update($data, $tanggal_kirim) {
		$this->db->where ( "TGL_KIRIM", $tanggal_kirim );
		$this->db->update ( "M_OP", $data );
		
		return ($this->db->affected_rows () != 1) ? false : true;
	}
}