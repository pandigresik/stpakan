<?php
class M_pengembalian_pakan_rusak extends CI_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
	//	$this->_table = 'lpb';
	}
	public function get_stok_pakan($noreg){
		return $this->db
			->select('km.kode_barang,mb.nama_barang,km.jml_stok,km.jenis_kelamin,mb.bentuk_barang')
			->join('m_barang mb',' mb.kode_barang = km.kode_barang')
			->where(array('no_reg' => $noreg))
			->get('kandang_movement km');
	}

	public function get_retur_pakan($noreg){
		$sql = <<<SQL

		select rpr.NO_REG no_reg
				,rpri.KODE_PAKAN kode_barang
				,rpri.JENIS_KELAMIN jenis_kelamin
				,count(*) jml_kirim
		from RETUR_PAKAN_RUSAK rpr
		join RETUR_PAKAN_RUSAK_ITEM rpri
			on rpri.RETUR_PAKAN_RUSAK = rpr.id
		join RETUR_PAKAN_RUSAK_ITEM_TIMBANG rprit
			on rprit.RETUR_PAKAN_RUSAK_ITEM = rpri.id
		where rpr.no_reg = '{$noreg}'
		group by rpr.NO_REG, rpri.KODE_PAKAN, rpri.JENIS_KELAMIN
SQL;
		return $this->db->query($sql);
	}

	public function list_pengembalian_pakan_rusak($kode_farm,$tanggal_cari = NULL,$no_retur = NULL){
		$no_retur = '';
		$where = '';
		if(!empty($tanggal_cari)){
			$where = 'where cast(rsk.tgl_buat as date) '.$tanggal_cari;
		}
		$where_no_retur = '';
		if(!empty($no_retur)){
			$where_no_retur = ' and rsk.NO_REG+\'-\'+rsk.NO_URUT = \''.$no_retur.'\'';
		}
		$sql = <<<SQL
		select rsk.NO_REG+'-'+rsk.NO_URUT no_retur
			,mk.NAMA_KANDANG nama_kandang
			,rsk.TGL_BUAT tgl_buat
			,rskip.JML_RETUR jml_retur
			,mp.nama_pegawai user_buat
			,mpv.nama_pegawai user_verifikasi
		from RETUR_PAKAN_RUSAK rsk
		inner join RETUR_PAKAN_RUSAK_ITEM rskip
			on rsk.id = rskip.RETUR_PAKAN_RUSAK {$where_no_retur}
		inner join KANDANG_SIKLUS ks
			on ks.NO_REG = rsk.NO_REG and ks.kode_farm = '{$kode_farm}'
		inner join M_KANDANG mk
			on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM
		inner join m_pegawai mp
			on mp.kode_pegawai = rsk.user_buat
		inner join m_pegawai mpv
			on mpv.kode_pegawai = rsk.user_verifikasi
		{$where}
		group by rsk.NO_REG
			,rsk.NO_URUT
			,mk.NAMA_KANDANG
			,rsk.TGL_BUAT
			,rskip.JML_RETUR
			,mp.nama_pegawai
			,mpv.nama_pegawai

SQL;

	return $this->db->query($sql);
	}
	public function view_pengembalian($noreg,$nourut){
		$sql = <<<SQL

		select rsk.NO_REG
				,mk.NAMA_KANDANG
				,rsk.TGL_BUAT
				,rskip.KODE_PAKAN
				,mb.NAMA_BARANG
				,mb.BENTUK_BARANG
				,rskip.JENIS_KELAMIN
				,rskip.JML_RETUR
				,rskip.JML_STOK
				,cast(rskitp.BRT_SAK as int) BRT_SAK
				,rskitp.KETERANGAN
				,rskitp.NO_URUT
				,mb.NAMA_BARANG
				,mp.nama_pegawai admin_gudang
				,mpv.nama_pegawai user_verifikasi
		from RETUR_PAKAN_RUSAK rsk
		inner join RETUR_PAKAN_RUSAK_ITEM rskip
			on rskip.RETUR_PAKAN_RUSAK = rsk.id
		inner join RETUR_PAKAN_RUSAK_ITEM_TIMBANG rskitp
			on rskitp.RETUR_PAKAN_RUSAK_ITEM = rskip.id
		inner join m_barang mb
			on mb.KODE_BARANG = rskip.KODE_PAKAN
		inner join KANDANG_SIKLUS ks
			on ks.NO_REG = rsk.NO_REG
		inner join M_KANDANG mk
			on mk.KODE_FARM = ks.KODE_FARM and mk.KODE_KANDANG = ks.KODE_KANDANG
		inner join m_pegawai mp
			on mp.kode_pegawai = rsk.user_buat
		inner join m_pegawai mpv
			on mpv.kode_pegawai = rsk.user_verifikasi
		where rsk.NO_REG = '{$noreg}'
		and rsk.NO_URUT = '{$nourut}'
SQL;

		return $this->db->query($sql);
	}

	public function get_user($kode_farm,$user_level) {
		$query = <<<QUERY
            SELECT
                MP.KODE_PEGAWAI kode_pegawai
                , MP.NAMA_PEGAWAI nama_pegawai
            FROM M_PEGAWAI MP
            JOIN PEGAWAI_D PD ON PD.KODE_PEGAWAI = MP.KODE_PEGAWAI AND PD.KODE_FARM = '{$kode_farm}' AND MP.STATUS_PEGAWAI = 'A' AND GRUP_PEGAWAI = '{$user_level}'
            ORDER BY NAMA_PEGAWAI ASC
QUERY;
		$stmt = $this->db->conn_id->prepare($query);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

}
