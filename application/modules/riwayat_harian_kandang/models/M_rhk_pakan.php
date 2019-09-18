<?php
class M_rhk_pakan extends MY_Model{
	protected $_table = 'rhk_pakan';
	public function __construct(){
		parent::__construct();
	}
	
	public function getPemakaian($keyWhere){
		return $this->db->select('m_barang.nama_barang,rhk_pakan.jml_pakai,rhk_rekomendasi_pakan.jml_permintaan')
				->join('rhk_rekomendasi_pakan','rhk_rekomendasi_pakan.no_reg = rhk_pakan.no_reg and rhk_rekomendasi_pakan.tgl_transaksi = rhk_pakan.tgl_transaksi and rhk_rekomendasi_pakan.kode_barang = rhk_pakan.kode_barang','left')
				->join('m_barang','m_barang.kode_barang = rhk_pakan.kode_barang')
				->where(array('rhk_pakan.no_reg' => $keyWhere['no_reg'], 'rhk_pakan.tgl_transaksi' => $keyWhere['tgl_transaksi']))
				->get($this->_table);

	}
}