<?php
class M_lpbe extends CI_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'lpb_e';
	}
	public function get($param = array()){
		if(!empty($param)){
			$this->db->like($param);
		}
		return $this->db->get($this->_table);
	}
	
	public function get_last_pp($param = array()){
		if(!empty($param)){
			$this->db->where($param);
		}
		$this->db->select_max('tgl_keb_akhir');
		return $this->db->get($this->_table);
	}
	
	public function insert($param = array()){
		$this->db->insert($this->_table,$param);
	}
	
	public function update($where,$update){
		$this->db->where($where);
		$this->db->update($this->_table,$update);
	}
	
	public function delete($where){
		$this->db->where($where);
		$this->db->delete($this->_table);
	}
	
	public function insert_batch($param = array()){
		$this->db->insert_batch($this->_table,$param);
		
	}
	public function riwayat_pp($noreg,$jmlHariTerakhir,$tgl_rilis_pp = NULL){
		$listPakan = $this->db->distinct()->select(array('kode_barang'))->where(array('no_reg' => $noreg))->get_compiled_select('lpb_e');
		$nama_barang = $this->db->select(array('nama_barang'))->where('kode_barang in ('.$listPakan.')')->get('m_barang')->result_array();
		$_strBarang = array();
		$whereTglRilis = !empty($tgl_rilis_pp) ? ' and l.tgl_rilis < \''.$tgl_rilis_pp.'\'' : '';
		if(empty($nama_barang)){
			return array();
		}

		foreach($nama_barang as $nb){
			array_push($_strBarang,$nb['nama_barang']);
		}
		$paramBarang = implode('","',$_strBarang);
		$sql = <<<SQL
		SELECT * FROM (
			select top {$jmlHariTerakhir} le.tgl_kebutuhan,mb.nama_barang, le.jml_order
				from {$this->_table} le
				inner join lpb l on l.no_lpb = le.no_lpb and l.status_lpb = 'A' {$whereTglRilis}
				inner join m_barang mb on mb.kode_barang = le.kode_barang
				where no_reg = '{$noreg}'
				order by tgl_kebutuhan DESC
		)h		
		pivot
		(
			sum(jml_order)
			for nama_barang IN ("{$paramBarang}")
		) piv		
SQL;
			
		return $this->db->query($sql)->result_array();
	}

	public function rhk_pakan($noreg){
		$listPakan = $this->db->distinct()->select(array('kode_barang'))->where(array('no_reg' => $noreg))->get_compiled_select('rhk_pakan');
		$nama_barang = $this->db->select(array('nama_barang'))->where('kode_barang in ('.$listPakan.')')->get('m_barang')->result_array();
		$_strBarang = array();
		foreach($nama_barang as $nb){
			array_push($_strBarang,$nb['nama_barang']);
		}
		$paramBarang = implode('","',$_strBarang);
		$sql = <<<SQL
		SELECT * FROM (
		select rp.tgl_transaksi,jml_pakai,mb.nama_barang
				from rhk_pakan rp		
				JOIN M_BARANG mb ON mb.KODE_BARANG = rp.KODE_BARANG 
				where no_reg = '{$noreg}'				
		)h		
		pivot
		(
		  sum(jml_pakai)
		  for nama_barang IN ("{$paramBarang}")
		) piv;		
SQL;
			
		return $this->db->query($sql)->result_array();
	}
	
}