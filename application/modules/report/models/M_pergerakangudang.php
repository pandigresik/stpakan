<?php
class M_pergerakangudang extends CI_Model{

	public function __construct(){
		parent::__construct();

	}
	public function gudangterima($kodesiklus){
		$sql = <<<SQL
		select md.no_pallet no_penyimpanan
			,p.KETERANGAN1 no_do
			,md.KODE_PALLET kode_pallet
			,md.KODE_BARANG kode_barang
			,ks.FLOK_BDY flok
			,cast(md.PUT_DATE as date) tgl_datang
			,'Penerimaan' keterangan
			,md.JML_PUTAWAY terima_sak
			,md.BERAT_PUTAWAY terima_kg
			,md.PUT_DATE tgl_simpan
--		,mg.NAMA_GUDANG nama_gudang
		from MOVEMENT_D md
		join kandang_siklus ks on ks.no_reg = md.keterangan2 and ks.KODE_SIKLUS = '{$kodesiklus}'
--	join m_pallet mp on mp.KODE_FARM = ks.KODE_FARM and md.KODE_PALLET = mp.KODE_PALLET
--	join m_gudang mg on mg.KODE_GUDANG = mp.KODE_GUDANG and mg.KODE_FARM = mp.KODE_FARM
		join PENERIMAAN p
			on p.NO_PENERIMAAN = md.NO_REFERENSI
		where md.KETERANGAN1 = 'PUT'
SQL;
		return $this->db->query($sql);
	}

	public function gudangkeluar($kodesiklus){
		$sql = <<<SQL
		select md.no_pallet no_penyimpanan
			,md.NO_REFERENSI no_referensi
			,md.KODE_PALLET kode_pallet
			,md.KODE_BARANG kode_barang
			,ks.FLOK_BDY flok
			,'___Pengambilan '+md.KETERANGAN2 keterangan
			,md.JML_PICK ambil_sak
			,md.BERAT_PICK ambil_kg
			,md.PICKED_DATE tgl_simpan
--		,mg.NAMA_GUDANG nama_gudang
		from MOVEMENT_D md
		join kandang_siklus ks on ks.no_reg = md.keterangan2 and ks.KODE_SIKLUS = '{$kodesiklus}'
--	join m_pallet mp on mp.KODE_FARM = ks.KODE_FARM and md.KODE_PALLET = mp.KODE_PALLET
--	join m_gudang mg on mg.KODE_GUDANG = mp.KODE_GUDANG and mg.KODE_FARM = mp.KODE_FARM
		where md.KETERANGAN1 = 'PICK'
SQL;

		return $this->db->query($sql);
	}
}
