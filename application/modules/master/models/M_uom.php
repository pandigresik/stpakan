<?php

class M_uom extends CI_Model {

    private $dbSqlServer;

    public function __construct() {
        parent::__construct();
        $this->dbSqlServer = $this->load->database("default", true);
    }

    function get_uom($start = null, $offset = null, $satuan = null, $deskripsi = null, $satuan_dasar = null, $konversi = null) {
        $filter_str = "";
        $filter_arr = array();

        $filter_bottom_str = "";
        $filter_bottom_arr = array();

        if (isset($satuan))
            $filter_arr [] = "uom1.UOM like '%" . $satuan . "%'";
        if (isset($deskripsi))
            $filter_arr [] = "uom1.DESKRIPSI like '%" . $deskripsi . "%'";
        if (isset($satuan_dasar))
            $filter_arr [] = "uom2.DESKRIPSI like '%" . $satuan_dasar . "%'";
        if (isset($konversi))
            $filter_arr [] = "uom1.KONVERSI like '%" . $konversi . "%'";

        if (count($filter_arr) > 0) {
            $filter_str .= " where ";
            $filter_str .= implode(" and ", $filter_arr);
        }

        if (isset($start) and isset($offset))
            $filter_bottom_arr [] = "row > {$start} and row <= {$offset}";

        if (count($filter_bottom_arr) > 0) {
            $filter_bottom_str .= " where ";
            $filter_bottom_str .= implode(" and ", $filter_bottom_arr);
        }

        $sql = <<<QUERY
			select * from (
				SELECT
					ROW_NUMBER() OVER (ORDER BY uom1.UOM) AS ROW
					, uom1.*
					, uom2.DESKRIPSI DESKRIPSI_BASE_UOM
				FROM M_UOM uom1
				LEFT JOIN M_UOM uom2 ON uom1.BASE_UOM = uom2.UOM
				$filter_str
			) mainqry
			$filter_bottom_str
QUERY;
        // echo $sql;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_uom_by_id($satuan) {
        $sql = <<<QUERY
				SELECT 
					uom1.* 
					, uom2.UOM UOM_BASE_UOM
					, uom2.DESKRIPSI DESKRIPSI_BASE_UOM
				FROM M_UOM uom1
				LEFT JOIN M_UOM uom2 ON uom1.BASE_UOM = uom2.UOM WHERE uom1.UOM = '{$satuan}'
	
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function get_all_uom() {
        $sql = <<<QUERY
				SELECT 
					uom1.* 
					, uom2.DESKRIPSI DESKRIPSI_BASE_UOM
				FROM M_UOM uom1
				LEFT JOIN M_UOM uom2 ON uom1.BASE_UOM = uom2.UOM
	
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function list_konversi() {
        $sql = <<<QUERY
                SELECT DISTINCT KONVERSI 
                FROM M_UOM
                ORDER BY KONVERSI ASC
    
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function check_uom($satuan) {
        $sql = <<<QUERY
			select count(*) n_result
			FROM M_UOM WHERE UOM = '{$satuan}'
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function insert($data) {
        $this->db->insert("M_UOM", $data);
        return ($this->db->affected_rows() != 1) ? false : true;
    }

    function update($data, $satuan) {
        $this->db->where("UOM", $satuan);
        $this->db->update("M_UOM", $data);

        return ($this->db->affected_rows() != 1) ? false : true;
    }

}