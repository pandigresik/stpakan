<?php
class M_user extends MY_Model{
	private $dbSqlServer ;
	
	public function __construct(){
		parent::__construct();
<<<<<<< HEAD
		$this->_table = 'm_pegawai';
		$this->_primary_key= 'kode_pegawai';
=======
		$this->_table = 'user';
		$this->_primary_key= 'id';
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
	}
	public function login($username,$password){		
		$sql = <<<SQL
		exec dbo.LOGIN_CHECK :username,:password
SQL;
		$stmt = $this->db->conn_id->prepare ( $sql );
		$stmt->bindParam(':username',$username);
		$stmt->bindParam(':password',$password);
		$stmt->execute();
	//	print_r($stmt->errorInfo());
		return $stmt->fetch(2);
	}
	
	public function getPermission($username){
		return $this->db
		//	->select('w.id,w.token,w.label')
			->select('w.token')
			->from('privilege_map pm')
			->join('workbook w','w.id=pm.workbook')
			->where(array('pm.published'=>1,'w.published'=>1,'pm.[user]'=>$username))
			->get();
	}
	
	public function changePassword($username,$oldPassword,$newPassword){
		$sql = <<<QUERY
		UPDATE m_pegawai SET password = '{$newPassword}'
		WHERE kode_pegawai = '{$username}' AND password = '{$oldPassword}'
QUERY;
		$this->db->query($sql);		
	}
	
	public function get_menu($level_user){
		$sql = <<<QUERY
		exec dbo.CREATE_MENU 0,'{$level_user}'
QUERY;
		return $this->db->query($sql);
	}
	
	public function affectedRow(){
		return $this->db->affected_rows();
	}
	
	public function listAccess($userid){
		return $this->db
		->select('w.id,w.token,w.label')
		->from('privilege_map pm')
		->join('workbook w','w.id=pm.workbook')
		->where(array('pm.published'=>1,'w.published'=>1,'pm.[user]'=>$username))
		->get();
	}
<<<<<<< HEAD
}
=======
}
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
