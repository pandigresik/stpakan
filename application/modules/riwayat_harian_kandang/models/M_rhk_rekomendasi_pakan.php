<?php
class M_rhk_rekomendasi_pakan extends MY_Model{
	protected $_table = 'rhk_rekomendasi_pakan';
	public function __construct(){
		parent::__construct();
	
	}
	/** jumlah ekor ayam diambil dari lhk sebelum tglini, jika tidak ada ambil dari bapdoc */
	function rekomendasiPakanStandart($noreg, $tgl_kebutuhan){
		$sql = <<<QUERY
		SELECT mstd.PKN_HR pkn_hr
		FROM kandang_siklus ks
		JOIN M_STD_BUDIDAYA_D mstd ON mstd.KODE_STD_BUDIDAYA = ks.KODE_STD_BUDIDAYA AND mstd.STD_UMUR = datediff(day,ks.TGL_DOC_IN,'{$tgl_kebutuhan}')
		WHERE NO_REG = '{$noreg}'
QUERY;
		return $this->db->query($sql)->row_array();
	}

	function listPakanGudangRekomendasi($noreg){
		$sql = <<<SQL
		select x.*,mb.nama_barang from (
			select
				md_put.NO_REG
				, md_put.kode_barang		
				, md_put.JML_PUTAWAY-isnull(md_pick.JML_PICK,0) JML_AVAILABLE
			from (
				select
					KETERANGAN2 NO_REG
					, kode_barang
					, KODE_FARM
					, sum(JML_PUTAWAY) JML_PUTAWAY
				from MOVEMENT_D
				where KETERANGAN2 = '{$noreg}'
				and STATUS_STOK = 'NM'
				and KETERANGAN1 = 'PUT'
				
				group by
					KETERANGAN2
					, KODE_BARANG
					, KODE_FARM
			) md_put
			left join (
				select
					KETERANGAN2 NO_REG
					, KODE_BARANG
					, KODE_FARM
					, sum(JML_PICK) JML_PICK
				from MOVEMENT_D
				where KETERANGAN2 = '{$noreg}'
				and STATUS_STOK = 'NM'
				and KETERANGAN1 = 'PICK'
				
				group by
					KETERANGAN2
					, KODE_BARANG
					, KODE_FARM
			) md_pick
				on md_put.NO_REG = md_pick.NO_REG
				and md_put.KODE_BARANG = md_pick.KODE_BARANG
				and md_put.KODE_FARM = md_pick.KODE_FARM
		)x 
		join m_barang mb on mb.kode_barang = x.kode_barang
		where x.jml_available > 0 
SQL;
		return $this->db->query($sql)->result_array();		
	}

}