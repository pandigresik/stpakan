<?php
class M_penerimaan_retur_farm extends MY_Model{	
	
	public function ganti_pallet($kode_farm, $kode_pakan){
		$sql = <<<SQL
			select m.KODE_PALLET, m.NO_PALLET, m.BERAT_AVAILABLE, m.JML_ON_HAND from movement m
			where NO_PALLET >= (
				select min(no_pallet) from movement_d where kode_farm = '{$kode_farm}' 
				and KETERANGAN1 = 'PUT' and KETERANGAN2 in (
					select no_reg from KANDANG_SIKLUS where STATUS_SIKLUS = 'O' and kode_siklus in (
						select kode_siklus from m_periode where kode_farm = '{$kode_farm}' and status_periode = 'A'
					)
				)
			) and kode_farm = '{$kode_farm}' and JML_ON_HAND > 0 and JML_ON_HAND <= 40 and m.kode_barang = '{$kode_pakan}' order by m.kode_barang ASC, kode_pallet ASC 
SQL;
		return $this->db->query($sql)->result_array();

	}
	
	public function kandang_farm($kode_farm){
		$sql = <<<SQL
			select ks.kode_kandang, ks.kode_farm, mk.nama_kandang, mk.no_flok from kandang_siklus ks
			join m_farm mf on ks.kode_farm = mf.kode_farm 
			join m_kandang mk on ks.kode_farm = mk.kode_farm and ks.kode_kandang = mk.kode_kandang
			where ks.kode_farm = '{$kode_farm}' and ks.status_siklus = 'O'
SQL;
		return $this->db->query($sql)->result_array();
	}
	
}