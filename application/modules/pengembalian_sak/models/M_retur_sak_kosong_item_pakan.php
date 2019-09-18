<?php
class M_retur_sak_kosong_item_pakan extends MY_Model{
	
	public function __construct(){
		parent::__construct();
		$this->_table = 'retur_sak_kosong_item_pakan';
		$this->_primary_key= 'id';
	}
	public function simpan($data){
		$retur_sak_kosong = $data['retur_sak_kosong'];
		$kode_pakan = $data['kode_pakan'];
		$jenis_kelamin = $data['jenis_kelamin'];
		$keterangan = $data['keterangan'];
		$jml_pakai = $data['jml_pakai'];
		$jml_kirim = $data['jml_kirim'];
		$hutang = $data['hutang'];
		$sql = <<<SQL
	
		insert into  retur_sak_kosong_item_pakan(retur_sak_kosong,kode_pakan,jenis_kelamin,keterangan,jml_pakai,jml_kirim,hutang)
		output inserted.id
		values ('{$retur_sak_kosong}','{$kode_pakan}','{$jenis_kelamin}','{$keterangan}','{$jml_pakai}','{$jml_kirim}','{$hutang}')
SQL;
		
	$stmt = $this->db->conn_id->prepare($sql);
	$stmt->execute();
	return $stmt->fetch(2);
	}
}