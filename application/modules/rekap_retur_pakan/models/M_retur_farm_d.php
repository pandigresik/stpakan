<?php
class M_retur_farm_d extends MY_Model{	
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'RETUR_FARM_D';
	}

	public function listPakan($noRetur){
		$sql = <<<SQL
		select rfd.*,mb.nama_barang NAMA_PAKAN 
		from {$this->_table} rfd
		join m_barang mb on mb.kode_barang = rfd.kode_pakan		
		where no_retur = '{$noRetur}'
SQL;
		return $this->db->query($sql)->result_array();		
	}
	public function listPakanTimbang($noRetur){
		$sql = <<<SQL
		select md.*,mb.nama_barang NAMA_PAKAN 
		from movement_d md
		join m_barang mb on mb.kode_barang = md.kode_barang
		where md.no_referensi = '{$noRetur}'
SQL;
		return $this->db->query($sql)->result_array();		
	}
}
