<?php
class M_strain extends CI_Model{
	private $dbSqlServer ;
	
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}
	
	function get_strain_browse(){
		$sql = <<<QUERY
			select * from (
				select ROW_NUMBER() OVER (ORDER BY a.NAMA_STRAIN) as row,
					   a.*
				from M_STRAIN a
	) mainqry
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}