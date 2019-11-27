<?php
class M_retur_sak_kosong extends MY_Model{
	
	public function __construct(){
		parent::__construct();
		$this->_table = 'retur_sak_kosong';
		$this->_primary_key= 'id';
	}
	
	public function simpan($data){
		$no_reg = $data['no_reg'];
		$user_buat = $data['user_buat'];
		$user_verifikasi = $data['user_verifikasi'];
		$sql = <<<SQL
		
		insert into  retur_sak_kosong(no_reg,no_urut,tgl_buat,user_buat,user_verifikasi) 
		output inserted.id,inserted.tgl_buat,inserted.no_urut 
		values ('{$no_reg}',(select replace(str(coalesce(max(no_urut),0)+1,3),' ',0) from retur_sak_kosong rsk where rsk.NO_reg = '{$no_reg}'),getdate(),'{$user_buat}','{$user_verifikasi}')
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->execute();
		return $stmt->fetch(2);
	}
}