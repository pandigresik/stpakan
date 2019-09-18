<?php
class M_retur_pakan_rusak_item extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'retur_pakan_rusak_item';
	}
	public function simpan($data){
		$retur_sak_kosong = $data['retur_pakan_rusak'];
		$kode_pakan = $data['kode_pakan'];
		$jenis_kelamin = $data['jenis_kelamin'];
		$jml_retur = $data['jml_retur'];
		$jml_stok = $data['jml_stok'];
		$sql = <<<SQL

		insert into  retur_pakan_rusak_item(retur_pakan_rusak,kode_pakan,jenis_kelamin,jml_retur,jml_stok)
		output inserted.id
		values ('{$retur_sak_kosong}','{$kode_pakan}','{$jenis_kelamin}','{$jml_retur}','{$jml_stok}')
SQL;

		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->execute();
		return $stmt->fetch(2);
	}
}
