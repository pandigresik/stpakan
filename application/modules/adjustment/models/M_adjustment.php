<?php
class M_adjustment extends CI_Model{
	private $dbSqlServer ;
	
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}
		
	function get_adjustment($start = null, $offset = null, $noadjustment, $tanggal, $namafarm, $tipe, $alasan){
		$filter_str = "";
		$filter_arr = array();
		
		$filter_bottom_str = "";
		$filter_bottom_arr = array();
		
		if(isset($noadjustment))
			$filter_arr[] = "b.nama_farm like '%".$namafarm."%'";
		if(isset($tanggal))
			$filter_arr[] = "a.kode_gudang like '%".$kodegudang."%'";
		if(isset($namafarm))
			$filter_arr[] = "a.nama_gudang like '%".$namagudang."%'";
		if(isset($tipe))
			$filter_arr[] = "a.nama_gudang like '%".$namagudang."%'";
		if(isset($alasan))
			$filter_arr[] = "a.nama_gudang like '%".$namagudang."%'";
		
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
				select ROW_NUMBER() OVER (ORDER BY a.no_adjustment desc) as row,
					a.no_adjustment, 
					a.tgl_adjustment,
					replace(convert(varchar(11),a.tgl_adjustment,106),' ','-') as tgl_adjustment_formated,
					b.kode_farm, b.nama_farm, a.tipe_adjustment, 
					case a.tipe_adjustment when 'I' then 'In' else 'Out' end tipe_adjustment_desc,
					a.alasan_adjustment,
					a.keterangan1, a.keterangan2
				from adjustment a 
				inner join m_farm b on a.kode_farm = b.kode_farm
				$filter_str
	) mainqry
	$filter_bottom_str
QUERY;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
		
	}
}