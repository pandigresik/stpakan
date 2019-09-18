<?php
class M_harga_barang extends CI_Model {
	private $dbSqlServer;
	public function __construct() {
		parent::__construct ();
		$this->dbSqlServer = $this->load->database ( "default", true );
	}
		
	function get_harga_barang($start = null, $offset = null, $pelanggan = null, $kode_barang = null, $nama_barang = null, $satuan = null, $bentuk_pakan = null, $tanggal_berlaku = null) {
		$filter_str = "";
		$filter_arr = array ();
		
		$filter_bottom_str = "";
		$filter_bottom_arr = array ();
		
		if (isset ( $pelanggan ))
			$filter_arr [] = "mp.NAMA_PELANGGAN like '%" . $pelanggan . "%'";
		if (isset ( $kode_barang ))
			$filter_arr [] = "mb.KODE_BARANG like '%" . $kode_barang . "%'";
		if (isset ( $nama_barang ))
			$filter_arr [] = "mb.NAMA_BARANG like '%" . $nama_barang . "%'";
		if (isset ( $satuan ))
			$filter_arr [] = "mu.DESKRIPSI like '%" . $satuan . "%'";
		if (isset ( $bentuk_pakan ))
			$filter_arr [] = "mb.BENTUK_BARANG like '%" . $bentuk_pakan . "%'";
		if (isset ( $tanggal_berlaku ))
			$filter_arr [] = "CAST(hb.TGL_BERLAKU AS DATE) = '" . $tanggal_berlaku . "'";
		
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
				select
					ROW_NUMBER() OVER (ORDER BY hb.TGL_BERLAKU ASC) AS ROW
					, mp.NAMA_PELANGGAN nama_pelanggan
					, mp.KODE_PELANGGAN kode_pelanggan
					, mb.KODE_BARANG kode_barang
					, mb.NAMA_BARANG nama_barang
					, mu.UOM uom
					, mu.DESKRIPSI satuan
					, mb.BENTUK_BARANG bentuk_pakan
					, CASE 
						WHEN MB.BENTUK_BARANG = 'T' then 'TEPUNG'
						WHEN MB.BENTUK_BARANG = 'C' then 'CRUMBLE'
						WHEN MB.BENTUK_BARANG = 'P' then 'PALLET'
						WHEN MB.BENTUK_BARANG = 'A' then 'CAIR'
						ELSE ''
						END bentuk_pakan_label
					, REPLACE(CONVERT(VARCHAR(10),hb.TGL_BERLAKU,105),'-',' ') tanggal_berlaku
					, hb.HARGA harga
				from HARGA_BARANG hb
				join M_PELANGGAN mp on mp.KODE_PELANGGAN = hb.KODE_PELANGGAN
				join M_BARANG mb on mb.KODE_BARANG = hb.KODE_BARANG
				join M_UOM mu on mu.UOM = hb.UOM
				$filter_str
			) mainqry
			$filter_bottom_str
QUERY;
		#echo $sql;
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	function get_harga_barang_by_id( $kode_pelanggan, $kode_barang, $uom, $tanggal_berlaku) {
		$sql = <<<QUERY
				select
					mp.NAMA_PELANGGAN nama_pelanggan
					, mp.KODE_PELANGGAN kode_pelanggan
					, mb.KODE_BARANG kode_barang
					, mb.NAMA_BARANG nama_barang
					, mu.UOM uom
					, mu.DESKRIPSI satuan
					, mb.BENTUK_BARANG bentuk_pakan
					, CASE 
						WHEN MB.BENTUK_BARANG = 'T' then 'TEPUNG'
						WHEN MB.BENTUK_BARANG = 'C' then 'CRUMBLE'
						WHEN MB.BENTUK_BARANG = 'P' then 'PALLET'
						WHEN MB.BENTUK_BARANG = 'A' then 'CAIR'
						ELSE ''
						END bentuk_pakan_label
					, REPLACE(CONVERT(VARCHAR(10),hb.TGL_BERLAKU,105),'-',' ') tanggal_berlaku
					, hb.HARGA harga
				from HARGA_BARANG hb
				join M_PELANGGAN mp on mp.KODE_PELANGGAN = hb.KODE_PELANGGAN
				join M_BARANG mb on mb.KODE_BARANG = hb.KODE_BARANG
				join M_UOM mu on mu.UOM = hb.UOM
				WHERE hb.KODE_PELANGGAN = '$kode_pelanggan'
				AND hb.KODE_BARANG = '$kode_barang'
				AND hb.UOM = '$uom'
				AND CAST(hb.TGL_BERLAKU AS DATE) = '$tanggal_berlaku'
	
QUERY;
		#echo $sql;
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function check_harga_barang($kode_pelanggan, $kode_barang, $uom, $tanggal_berlaku) {
		$sql = <<<QUERY
				select
					COUNT(*) n_result
				from HARGA_BARANG hb
				join M_PELANGGAN mp on mp.KODE_PELANGGAN = hb.KODE_PELANGGAN
				join M_BARANG mb on mb.KODE_BARANG = hb.KODE_BARANG
				join M_UOM mu on mu.UOM = hb.UOM
				WHERE hb.KODE_PELANGGAN = '$kode_pelanggan'
				AND hb.KODE_BARANG = '$kode_barang'
				AND hb.UOM = '$uom'
				AND CAST(hb.TGL_BERLAKU AS DATE) = '$tanggal_berlaku'
QUERY;
		#echo $sql;
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function master_satuan() {
		$sql = <<<QUERY
				SELECT DESKRIPSI FROM M_UOM
				GROUP BY DESKRIPSI
				ORDER BY DESKRIPSI ASC
QUERY;
		#echo $sql;
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	function list_satuan() {
		$sql = <<<QUERY
				select distinct
					UOM uom
					, DESKRIPSI deskripsi
				from M_UOM
				order by uom asc
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	function search_data_harga($pelanggan,$kode_barang,$tanggal_berlaku) {
	    $tambahan = "";
	    if(!empty($tanggal_berlaku)){
	        $tanggal_berlaku = date ( 'Y-m-d', strtotime ( convert_month ( $tanggal_berlaku, 2 ) ) );
	        $tambahan = "AND TGL_BERLAKU = '$tanggal_berlaku'";
	    }
		$sql = <<<QUERY
				SELECT TOP 1 
					hb.*
					, REPLACE(CONVERT(VARCHAR(10),hb.TGL_BERLAKU,105),'-',' ') TGL_BERLAKU_NEW
					, mp.NAMA_PELANGGAN nama_pelanggan
					, mp.KODE_PELANGGAN kode_pelanggan
					, mb.KODE_BARANG kode_barang
					, mb.NAMA_BARANG nama_barang
					, mb.BENTUK_BARANG bentuk_pakan
					, CASE 
						WHEN MB.BENTUK_BARANG = 'T' then 'TEPUNG'
						WHEN MB.BENTUK_BARANG = 'C' then 'CRUMBLE'
						WHEN MB.BENTUK_BARANG = 'P' then 'PALLET'
						WHEN MB.BENTUK_BARANG = 'A' then 'CAIR'
						ELSE ''
					END bentuk_pakan_label
				FROM HARGA_BARANG hb
				join M_PELANGGAN mp on mp.KODE_PELANGGAN = hb.KODE_PELANGGAN
				join M_BARANG mb on mb.KODE_BARANG = hb.KODE_BARANG
				join M_UOM mu on mu.UOM = hb.UOM
				WHERE hb.KODE_BARANG = '$kode_barang'
				AND hb.KODE_PELANGGAN = '$pelanggan'
				AND hb.TGL_BERLAKU = (
					SELECT MAX(TGL_BERLAKU) FROM HARGA_BARANG
					WHERE KODE_BARANG = '$kode_barang'
					AND KODE_PELANGGAN = '$pelanggan'
					$tambahan
				)
				ORDER BY hb.TGL_BERLAKU DESC
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function list_barang($kode_barang=NULL) {
		$params = (empty($kode_barang)) ? "" : "WHERE MB.KODE_BARANG = '$kode_barang'";
		$sql = <<<QUERY
				select *
				, CASE 
					WHEN MB.BENTUK_BARANG = 'T' then 'TEPUNG'
					WHEN MB.BENTUK_BARANG = 'C' then 'CRUMBLE'
					WHEN MB.BENTUK_BARANG = 'P' then 'PALLET'
					WHEN MB.BENTUK_BARANG = 'A' then 'CAIR'
					ELSE ''
					END BENTUK_BARANG_LABEL
				from M_BARANG MB
				$params
				order by NAMA_BARANG asc
QUERY;
		#echo $sql;
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	function list_pelanggan() {
		$sql = <<<QUERY
				select *
				from M_PELANGGAN
				order by NAMA_PELANGGAN asc
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	function kontrol_efektif($tanggal_berlaku) {
		$sql = <<<QUERY
				select
					case 
						when cast('$tanggal_berlaku' as date) <= cast(getdate() as date) then 0
						else 1
					end result
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function max_efektif($kode_pelanggan,$kode_barang){
		$sql = <<<QUERY
				select max(TGL_BERLAKU) max_efektif from HARGA_BARANG
				where KODE_PELANGGAN = '$kode_pelanggan'
				and KODE_BARANG = '$kode_barang'
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		$result = $stmt->fetch ( PDO::FETCH_ASSOC );
		return (empty($result['max_efektif'])) ? '' : date('Y-m-d',strtotime($result['max_efektif']));
	}
	function insert($data) {
		$this->db->insert ( "HARGA_BARANG", $data );
		
		return ($this->db->affected_rows () != 1) ? false : true;
	}
	function update($harga, $kode_pelanggan, $kode_barang, $uom, $tanggal_berlaku , $tanggal_berlaku_lama) {
		$sql = <<<QUERY
				UPDATE HARGA_BARANG
				SET TGL_BERLAKU = '$tanggal_berlaku'
					, UOM = '$uom'
					, HARGA = $harga
				OUTPUT INSERTED.HARGA
				WHERE KODE_PELANGGAN = '$kode_pelanggan'
				AND KODE_BARANG = '$kode_barang'
				AND TGL_BERLAKU = '$tanggal_berlaku_lama'
QUERY;
		#echo $sql;
		$stmt = $this->dbSqlServer->conn_id->prepare ( $sql );
		$stmt->execute ();
		$return = $stmt->fetch ( PDO::FETCH_ASSOC );
		return (!empty($return['HARGA'])) ? true : false;
	}
}