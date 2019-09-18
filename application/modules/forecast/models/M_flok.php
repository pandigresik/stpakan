<?php
class M_flok extends CI_Model{
	protected $_table;
	protected $_user; 
	public function __construct(){
		parent::__construct();
		$this->_table = 'm_flok';
		$this->_user = $this->session->userdata('kode_user');
	}
	public function get($param = array()){
		if(!empty($param)){
			$this->db->like($param);
		}
		return $this->db->get($this->_table);
	}
	
	public function insert($param = array()){
		$r = $this->db->insert($this->_table,$param);
		if(!$r){
			throw new Exception('my exception message',1234);
		}
		
		
		
	}

    public function validasi_flok($namaflok) {
        $query = <<<QUERY
            select count(*) n_count from M_FLOK
            where NAMA_FLOK = '$namaflok'
QUERY;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ['n_count'];
    }
}