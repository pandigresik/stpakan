<?php
class M_laporan_stok_glangsing extends MY_Model{
	public function getEstimasiStok($kode_farm = NULL,$tglTransaksi){
		$whereFarm = '';
		if(!empty($kode_farm)){
			$whereFarm = " and gmd.kode_farm = '".$kode_farm."'";
		}
		$sql = <<<SQL
		select gmd.kode_farm,gmd.kode_siklus, mf.nama_farm,mp.periode_siklus, gmd.kode_barang, mb.nama_barang,coalesce(gmd.jml_akhir,0) jml_estimasi 		
		from glangsing_movement_kp_d gmd
		join M_BARANG mb on gmd.kode_barang = mb.KODE_BARANG								
		join m_periode mp on gmd.kode_siklus = mp.KODE_SIKLUS
		join m_farm mf on gmd.kode_farm = mf.kode_farm
		JOIN (
			SELECT max(tgl_buat) tgl_terakhir,kode_farm,kode_siklus,kode_barang FROM glangsing_movement_kp_d
			WHERE tgl_buat <= '{$tglTransaksi}'
			GROUP BY kode_farm,kode_siklus,kode_barang
		)stok_awal_hari ON stok_awal_hari.tgl_terakhir = gmd.tgl_buat AND stok_awal_hari.kode_farm = gmd.kode_farm AND stok_awal_hari.kode_siklus = gmd.kode_siklus AND stok_awal_hari.kode_barang = gmd.kode_barang
		WHERE gmd.kode_barang != 'GB' {$whereFarm}				
		order by gmd.kode_farm, gmd.kode_barang, gmd.kode_siklus	
SQL;
		
		return $this->db->query($sql)->result_array();
	}

	public function getEstimasiStokTerakhir($kode_farm = NULL,$kode_siklus = NULL){
		$whereFarm = '';
		$whereSiklus = '';
		if(!empty($kode_farm)){
			$whereFarm = " and gm.kode_farm = '".$kode_farm."'";
		}
		if(!empty($kode_siklus)){
			$whereSiklus = " and gm.kode_siklus = '".$kode_siklus."'";
		}
		$sql = <<<SQL
		select gm.kode_farm
				, mf.nama_farm
				,mp.periode_siklus
				,gm.kode_siklus
				,gm.kode_barang
				,mb.nama_barang
				,gm.jml_stok jml_estimasi 
		from glangsing_movement_kp gm
		join M_BARANG mb on gm.kode_barang = mb.KODE_BARANG
		JOIN M_PERIODE mp ON mp.KODE_SIKLUS = gm.kode_siklus 
		JOIN M_FARM mf ON mf.KODE_FARM = gm.kode_farm 		
		WHERE gm.jml_stok > 0 and gm.kode_barang != 'GB'
		{$whereFarm}
		{$whereSiklus}
		order by gm.kode_farm, gm.kode_barang, gm.kode_siklus
SQL;
			
		return $this->db->query($sql)->result_array();
	}
}
