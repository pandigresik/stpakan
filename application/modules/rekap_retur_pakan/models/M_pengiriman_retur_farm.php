<?php
class M_pengiriman_retur_farm extends MY_Model{	
	
	public function get_pallet_timbang($kode_farm, $kode_pakan){
		$exkodepakan = explode(',', $kode_pakan);
		$pakan = array();
		for($i=0;$i<count($exkodepakan);$i++){
			array_push($pakan, "'".$exkodepakan[$i]."'");
		}
		$kodepakan = implode(',', $pakan);
		$sql = <<<SQL
			select m.*, mb.NAMA_BARANG from movement m
	join M_barang mb on mb.kode_barang = m.kode_barang 
	where NO_PALLET >= (
		select min(no_pallet) from movement_d where kode_farm = '{$kode_farm}' 
		and KETERANGAN1 = 'PUT' and KETERANGAN2 in (
			select no_reg from KANDANG_SIKLUS where STATUS_SIKLUS = 'O' and kode_siklus in (
				select kode_siklus from m_periode where kode_farm = '{$kode_farm}' and status_periode = 'A'
			)
		)
	) and kode_farm = '{$kode_farm}' and JML_ON_HAND > 0 and m.kode_barang in ({$kodepakan}) order by kode_barang ASC, kode_pallet ASC 

SQL;
		return $this->db->query($sql)->result_array();
	}
	
	public function get_data_pallet($kode_farm, $no_retur){
			$sql = <<<SQL
				select md.*, mb.NAMA_BARANG, mp.NAMA_PEGAWAI from movement_d md 
				join M_BARANG mb on md.KODE_BARANG = mb.KODE_BARANG
				join M_PEGAWAI mp on md.PICKED_NAME = mp.KODE_PEGAWAI
				where NO_REFERENSI = '{$no_retur}' and KODE_FARM = '{$kode_farm}'
				order by KODE_BARANG ASC, KODE_PALLET ASC
SQL;

		return $this->db->query($sql)->result_array();
	}
	
	public function get_no_pallet($kode_farm, $kode_pallet){
		$sql = <<<SQL
			select m.no_pallet, m.jml_available, m.berat_available from movement m
	where NO_PALLET >= (
		select min(no_pallet) from movement_d where kode_farm = '{$kode_farm}' 
		and KETERANGAN1 = 'PUT' and KETERANGAN2 in (
			select no_reg from KANDANG_SIKLUS where STATUS_SIKLUS = 'O' and kode_siklus in (
				select kode_siklus from m_periode where kode_farm = '{$kode_farm}' and status_periode = 'A'
			)
		)
	) and kode_farm = '{$kode_farm}' and JML_ON_HAND > 0 and m.kode_pallet = '{$kode_pallet}' order by kode_pallet ASC
SQL;
		return $this->db->query($sql)->result_array();
	}
	
	public function ganti_hand_pallet($kode_farm) {
        $query = <<<QUERY
            select
                mhp.KODE_HAND_PALLET kode_hand_pallet
                , BRT_BERSIH berat
            from M_HAND_PALLET mhp
            where mhp.KODE_FARM = '$kode_farm'
            and mhp.STATUS_PALLET = 'N'
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
}