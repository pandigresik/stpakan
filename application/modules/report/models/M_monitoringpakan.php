<?php
class M_monitoringpakan extends CI_Model{

	public function __construct(){
		parent::__construct();

	}
	public function listpp($docin,$kodefarm){
		$sql = $this->querylistpp($docin,$kodefarm);
		$sql .= ' order by le.TGL_KEBUTUHAN';
		return $this->db->query($sql);
	}

	private function querylistpp($docin,$kodefarm,$distinct = FALSE){
		if($distinct){
			return <<<SQL
			select distinct le.no_lpb
			from (
				select no_reg from kandang_siklus ks where ks.TGL_DOC_IN = '{$docin}' and ks.kode_farm = '{$kodefarm}'
			) ks
			join lpb_e le
				on ks.no_reg = le.no_reg
			join lpb l
				on le.no_lpb = l.no_lpb and l.STATUS_LPB = 'A'
SQL;
		}
		else{
			return <<<SQL
			select le.TGL_KIRIM
				,le.NO_LPB
				,le.TGL_KEBUTUHAN
				,sum(le.JML_ORDER) JML_ORDER
				,le.KODE_BARANG
				,l.TGL_APPROVE1
				,l.TGL_RILIS
				,(select distinct tgl_review from review_lpb_budidaya where no_lpb = le.no_lpb) TGL_REVIEW
			from (
				select no_reg from kandang_siklus ks where ks.TGL_DOC_IN = '{$docin}' and ks.kode_farm = '{$kodefarm}'
			) ks
			join lpb_e le
				on ks.no_reg = le.no_reg
			join lpb l
				on le.no_lpb = l.no_lpb and l.STATUS_LPB = 'A'
			group by 	le.TGL_KIRIM
				,le.no_lpb
				,le.TGL_KEBUTUHAN
				,le.KODE_BARANG
				,l.TGL_APPROVE1
				,l.TGL_RILIS
SQL;
		}

	}

	private function querylistdo($docin,$kodefarm){
		$sqlpp = $this->querylistpp($docin,$kodefarm,TRUE);
		return <<<SQL
		select do_d.NO_DO
			  ,do_d.JML_MUAT JML
			  ,do_d.KODE_BARANG
			  ,do.TGL_BUAT
			  ,l.NO_LPB
		from ({$sqlpp}) l
		join op
			on op.no_lpb = l.no_lpb
		join do
			on do.no_op = op.no_op
		join do_d
			on do_d.no_do = do.no_do
SQL;
	}

	public function listdo($docin,$kodefarm){
		$sql = $this->querylistdo($docin,$kodefarm);
		return $this->db->query($sql);
	}

	public function listterimakandang($docin,$kodefarm){
		$sql = <<<SQL
		select max(kmd.TGL_BUAT) TGL_BUAT
			  ,sum(kmd.JML_ORDER) JML
			  ,kmd.KODE_BARANG
			  ,oke.TGL_KEBUTUHAN
		from PENERIMAAN_KANDANG pk
		join KANDANG_MOVEMENT_D kmd
			on kmd.KETERANGAN1 = 'PENERIMAAN KANDANG' and kmd.KETERANGAN2 = pk.NO_PENERIMAAN_KANDANG and kmd.NO_REG = pk.NO_REG
		join ORDER_KANDANG_E oke
			on oke.no_order = pk.NO_ORDER and oke.no_reg = pk.NO_REG and oke.KODE_BARANG = kmd.KODE_BARANG
		join (select no_reg from kandang_siklus ks where ks.TGL_DOC_IN = '{$docin}' and ks.kode_farm = '{$kodefarm}') ks
			on ks.no_reg = oke.no_reg
		group by kmd.KODE_BARANG
			  ,oke.TGL_KEBUTUHAN
SQL;

		return $this->db->query($sql);
	}

	public function listterimagudang($docin,$kodefarm){
		$sqldo = $this->querylistdo($docin,$kodefarm);
		$sql = <<<SQL
		select pd.TGL_BUAT
			,pd.JML_TERIMA JML
			,pd.KODE_BARANG
			,p.KETERANGAN1
			,do.NO_LPB
			,do.NO_DO
		from PENERIMAAN p
		join PENERIMAAN_d pd
			on p.no_penerimaan = pd.NO_PENERIMAAN
		join ({$sqldo}) do
			on do.no_do = p.KETERANGAN1 and do.kode_barang = pd.KODE_BARANG
SQL;
		return $this->db->query($sql);
	}

	public function listrhk($docin,$kodefarm){
		$sql = <<<SQL
		select rp.TGL_TRANSAKSI
			,sum(rp.jml_pakai) JML
			,rp.KODE_BARANG
			,max(r.TGL_BUAT) TGL_BUAT
		from rhk_pakan rp
		join rhk r
			on r.no_reg = rp.no_reg and r.TGL_TRANSAKSI = rp.TGL_TRANSAKSI
		join (select no_reg from kandang_siklus ks where ks.TGL_DOC_IN = '{$docin}' and ks.kode_farm = '{$kodefarm}') ks
			on ks.no_reg = rp.no_reg
		group by rp.TGL_TRANSAKSI
			,rp.KODE_BARANG
SQL;
		return $this->db->query($sql);
	}
	public function listforecast($docin,$kodefarm,$flock){
		$sql = <<<SQL
		select f.TGL_KIRIM
			,fd.TGL_KEBUTUHAN
			,fd.JML_FORECAST
			,fd.KODE_BARANG
		from FORECAST f
		join forecast_d fd
			on f.id = fd.FORECAST
		where f.KODE_FLOK_BDY = '{$flock}'
		and f.KODE_SIKLUS = (select distinct kode_siklus from kandang_siklus ks where ks.TGL_DOC_IN = '{$docin}' and ks.kode_farm = '{$kodefarm}')
		order by fd.tgl_kebutuhan
SQL;
		return $this->db->query($sql);
	}
	public function listsakkembali($docin,$kodefarm){
		$sql = <<<SQL
		select max(rsk.TGL_BUAT) TGL_BUAT
--			,cast (DATEADD(day,-1,rsk.TGL_RHK) as date) TGL_KEBUTUHAN
			,rsk.TGL_RHK TGL_KEBUTUHAN
			,rskip.KODE_PAKAN KODE_BARANG
			,sum(rskitp.JML_SAK) JML
		from (select no_reg from kandang_siklus ks where ks.TGL_DOC_IN = '{$docin}' and ks.kode_farm = '{$kodefarm}') ks
		join RETUR_SAK_KOSONG rsk
			on ks.no_reg = rsk.no_reg
		join RETUR_SAK_KOSONG_ITEM_PAKAN rskip
			on rskip.RETUR_SAK_KOSONG = rsk.id
		join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskitp
			on rskitp.RETUR_SAK_KOSONG_ITEM_PAKAN = rskip.id
		group by rsk.TGL_RHK
			,rskip.KODE_PAKAN
SQL;
		return $this->db->query($sql);
	}
}
