<?php
class M_retur_pakan_rusak extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'retur_pakan_rusak';
		$this->_primary_key = 'id';
	}
	public function simpan($data){
		$no_reg = $data['no_reg'];
		$user_buat = $data['user_buat'];
		$user_verifikasi = $data['user_verifikasi'];
		$attachment = $data['attachment'];
		

		
	$os = strtoupper ( substr ( PHP_OS, 0, 3 ) );
	if($os === 'WIN'){
		$sql = <<<SQL
		
		insert into retur_pakan_rusak(no_reg,no_urut,tgl_buat,user_buat,user_verifikasi,attachment)
		output inserted.id,inserted.tgl_buat,inserted.no_urut
		values ('{$no_reg}',(select replace(str(coalesce(max(no_urut),0)+1,3),' ',0) from retur_pakan_rusak rsk where rsk.NO_reg = '{$no_reg}'),getdate(),'{$user_buat}','{$user_verifikasi}',:attachment)
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam(':attachment',$attachment, PDO::PARAM_LOB, 0, PDO::SQLSRV_ENCODING_BINARY);
		
	}
	else{
		$sql = <<<SQL
		
		insert into retur_pakan_rusak(NO_REG,NO_URUT,TGL_BUAT,USER_BUAT,USER_VERIFIKASI,ATTACHMENT)
		output inserted.id,inserted.tgl_buat,inserted.no_urut
		values ('{$no_reg}',(select replace(str(coalesce(max(no_urut),0)+1,3),' ',0) from retur_pakan_rusak rsk where rsk.NO_reg = '{$no_reg}'),getdate(),'{$user_buat}','{$user_verifikasi}',{$attachment})
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
	}	
	
		$stmt->execute();
		return $stmt->fetch(2);
	
	}
}