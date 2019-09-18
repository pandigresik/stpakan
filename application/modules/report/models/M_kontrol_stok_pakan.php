<?php
class M_kontrol_stok_pakan extends CI_Model{
	public function __construct(){
		parent::__construct();
	}

	public function data_pp($noreg){
		$kode_farm = substr($noreg,0,2);
		$sql = <<<SQL
		select l.no_lpb
			,l.tgl_rilis
			,l.tgl_ubah
			,l.status_lpb
			,stuff((SELECT nama_pegawai FROM M_PEGAWAI WHERE KODE_PEGAWAI = l.user_buat),1,0,'') user_buat
			,CASE 
				when l.status_lpb NOT in ('D','N') THEN (SELECT TOP 1 nama_pegawai FROM M_PEGAWAI WHERE KODE_PEGAWAI = l.USER_UBAH)
				ELSE NULL
			 END user_review
			,case
				when l.status_lpb in ('A','V','RJ') then  (SELECT nama_pegawai FROM M_PEGAWAI WHERE KODE_PEGAWAI = (select top 1 user_reject from review_lpb_budidaya where no_lpb = l.no_lpb))
				else null
			end user_approve
			,case when l.status_lpb in ('RJ','V') then (select top 1 ket_reject from review_lpb_budidaya where no_lpb = l.no_lpb) else NULL end ket_reject
			,stuff(convert(varchar(19), l.tgl_buat, 126),11,1,' ') tgl_buat		
			,stuff(convert(varchar(19), l.tgl_rilis, 126),11,1,' ') tgl_rilis
			,stuff(convert(varchar(19), l.tgl_ubah, 126),11,1,' ') tgl_review				
			,stuff(convert(varchar(19), coalesce(l.tgl_approve1,(select top 1 tgl_reject from review_lpb_budidaya where no_lpb = l.no_lpb)), 126),11,1,' ') tgl_approve1
			,fd.tgl_kirim tgl_kirim_forecast
			,le.tgl_kirim
			,le.tgl_kebutuhan
			,le.tgl_lhk
			,le.jml_order
			,le.kode_barang
			,mb.nama_barang
			,datediff(day,ks.tgl_doc_in,le.tgl_kebutuhan) umur
			,case when DATEDIFF(day,l.tgl_rilis,le.tgl_kirim) < 2 then 0 else 1 end status_rilis
			,case when DATEDIFF(day,l.tgl_ubah,le.tgl_kirim) < 2 then 0 else 1 end status_review
			,case when DATEDIFF(day,l.tgl_approve1,le.tgl_kirim) < 2 then 0 else 1 end status_approve
			,rlb.jml_rekomendasi
		from lpb l
		join lpb_e le on le.NO_LPB = l.NO_LPB and le.NO_REG = '{$noreg}'
		join m_barang mb on mb.kode_barang = le.kode_barang
		join kandang_siklus ks on ks.no_reg = le.no_reg
		join review_lpb_budidaya rlb on rlb.no_lpb = le.no_lpb  and rlb.kode_barang = le.kode_barang and rlb.no_reg = le.no_reg and rlb.tgl_kebutuhan = le.tgl_kebutuhan
		left join (
			SELECT f.tgl_kirim, fd.tgl_kebutuhan FROM 
			FORECAST f 
			JOIN FORECAST_D fd ON fd.FORECAST = f.id
			WHERE f.KODE_SIKLUS = (select top 1 kode_siklus from kandang_siklus where no_reg = '{$noreg}') AND f.KODE_FLOK_BDY = (select top 1 flok_bdy from kandang_siklus where no_reg = '{$noreg}')
			GROUP BY fd.tgl_kebutuhan,f.tgl_kirim 
		)fd on fd.tgl_kebutuhan = le.tgl_kebutuhan
		where l.status_lpb != 'V' and l.KODE_FARM = '{$kode_farm}' and le.jml_order > 0
		order by le.tgl_kebutuhan asc
SQL;
		return $this->db->query($sql)->result_array();		
	}

	public function data_penerimaan_do($noreg){
		$kode_farm = substr($noreg,0,2);
		$sql = <<<SQL
		select distinct l.no_lpb
			,do.no_do
		--	,do.tgl_buat
			,do_e.kode_barang
			,do_e.jml_muat
			,do.status_do
			,vdp.nopol 
			,vdp.tgl_verifikasi
			,mp1.nama_pegawai user_verifikasi
			,md.put_date tgl_terima
			,pe.jumlah 
			,pe.berat
			,md.kode_pallet
			,mp.nama_pegawai user_buat
			,me.nama_ekspedisi
		--	,datediff(day,do.tgl_kirim,do.tgl_buat) selisih
			,ov.no_polisi rit
			,vdp.photo
		from lpb l
		join op on op.no_lpb = l.no_lpb
		join do on do.no_op = op.no_op 
		join op_vehicle ov on ov.no_op = do.no_op and ov.no_urut = do.no_urut
		join m_ekspedisi me on me.kode_ekspedisi = ov.kode_ekspedisi
		join do_e on do_e.no_do = do.no_do  and do_e.kode_barang = ov.kode_barang and do_e.no_reg = '{$noreg}'
		left join verifikasi_do_pakan vdp on vdp.no_do  = do_e.no_do
		left join penerimaan p on p.keterangan1 = do.no_do 
		left join penerimaan_e pe on pe.no_penerimaan = p.no_penerimaan and p.kode_farm = pe.kode_farm and do_e.kode_barang = pe.kode_barang 
		left join MOVEMENT_D md on md.NO_PALLET = pe.NO_PALLET and pe.KODE_FARM = md.KODE_FARM and md.KETERANGAN1 = 'PUT' and md.PUT_DATE is not null
		left join m_pegawai mp on mp.kode_pegawai = p.user_buat
		left join m_pegawai mp1 on mp1.kode_pegawai = vdp.user_verifikasi
		where l.status_lpb != 'V' and l.KODE_FARM = '{$kode_farm}'
		order by md.put_date

SQL;
		return $this->db->query($sql)->result_array();		
	}

	public function  dropping_pakan($noreg){
		$sql = <<<SQL
		select  oke.no_reg
			,oke.tgl_kebutuhan
			,min(md.picked_date) picked_date
			,md.kode_pallet
			,sum(md.jml_pick) jml_pick
			,md.kode_barang
			,sum(md.berat_pick) berat_pick
			,mp.nama_pegawai picked_name
			,coalesce(rrp.jml_permintaan, (select top 1 jml_order from lpb_e le join lpb l on l.no_lpb = le.no_lpb and l.status_lpb =  'A' where le.no_reg = oke.no_reg and le.kode_barang = md.kode_barang)) jml_permintaan
			,mb.nama_barang
			,case when datediff(day,min(md.picked_date),oke.tgl_kebutuhan) < 1 then 1 else 0 end telat_dropping 
<<<<<<< HEAD
			,tp.user_buat user_verifikasi_kandang
			,tp.tgl_buat timbang_kandang
			,tp.berat as berat_kandang
=======
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
		from order_kandang_e oke
		join order_kandang_d okd on okd.no_order = oke.no_order and okd.no_reg = oke.no_reg and okd.status_order = 'C'
		left join rhk_rekomendasi_pakan rrp on rrp.tgl_kebutuhan = oke.tgl_kebutuhan and rrp.kode_barang = oke.kode_barang and rrp.no_reg = oke.no_reg
		join movement_d md on md.no_referensi = oke.no_order and md.kode_farm = okd.kode_farm and md.keterangan2 = oke.no_reg and oke.kode_barang = md.kode_barang
		join m_pegawai mp on mp.kode_pegawai = md.picked_name
		join m_barang mb on mb.kode_barang = md.kode_barang
<<<<<<< HEAD
		left join timbang_pakan tp on tp.no_order = oke.no_order and tp.no_reg = oke.no_reg
=======
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
		where oke.no_reg = '{$noreg}'
		group by oke.no_reg
			,oke.tgl_kebutuhan
			,md.kode_pallet
			,md.kode_barang
			,mp.nama_pegawai
			,rrp.jml_permintaan
			,mb.nama_barang
<<<<<<< HEAD
			,tp.user_buat
			,tp.tgl_buat
			,tp.berat
=======
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
		order by min(md.picked_date)	
SQL;
		return $this->db->query($sql)->result_array();		
	}

	public function  rhk_pakan($noreg, $panen = false){
		$whereTglSetelahPanen = '';
		if($panen){
			$whereTglSetelahPanen = ' and r.tgl_transaksi >= (select min(tgl_panen) tgl_panen from realisasi_panen where no_reg =\''.$noreg.'\')';
		}
		$sql = <<<SQL
		select rc.no_reg
			,rc.tgl_cetak
			,mp1.nama_pegawai user_cetak 
			,r.tgl_buat
			,r.tgl_transaksi tgl_kebutuhan
			,mp.nama_pegawai user_buat
			,mp2.nama_pegawai user_ack
			,r.ack1 tgl_ack
			,r.c_mati mati
			,r.c_afkir afkir 
			,r.c_berat_badan berat_badan
			,r.c_jumlah jumlah
			,rp.kode_barang
			,mb.nama_barang
			,rp.jml_pakai
			,rp.jml_akhir
			,datediff(day,ks.tgl_doc_in,r.tgl_transaksi) umur
			,case when datediff(day,rc.tgl_cetak,r.tgl_transaksi) <= 0 then 0 else 1 end telat_cetak
			/* 1980 itu jumlah menit sampai dengan jam 09 pagi*/
			,case when datediff(minute,r.tgl_transaksi,r.tgl_buat) <= 1980 then 0 else 1 end telat_entry
		from rhk_cetak rc
		join rhk r on r.no_reg = rc.no_reg and r.tgl_transaksi = rc.tgl_transaksi 
		join kandang_siklus ks on ks.no_reg = r.no_reg
		join rhk_pakan rp on rp.no_reg = r.no_reg and rp.tgl_transaksi = r.tgl_transaksi 
		join m_barang mb on mb.kode_barang = rp.kode_barang
		left join m_pegawai mp on mp.kode_pegawai = r.user_buat
		left join m_pegawai mp1 on mp1.kode_pegawai = rc.user_cetak
		left join m_pegawai mp2 on mp2.kode_pegawai = r.user_ack1
		where rc.no_reg = '{$noreg}' {$whereTglSetelahPanen}
SQL;
		return $this->db->query($sql)->result_array();		
	}

	public function  pengembalian_sak($noreg){
		$sql = <<<SQL
		select rsk.tgl_buat
			,rsk.tgl_rhk tgl_kebutuhan
			,rskip.id
			,sum(rskitp.jml_sak) jml_sak
			,mp.nama_pegawai user_buat 
			,rskip.kode_pakan kode_barang
			,case when datediff(day,rsk.tgl_rhk,rsk.tgl_buat) > 1 then 1 else 0 end telat_retur
		from retur_sak_kosong rsk
		join retur_sak_kosong_item_pakan rskip on rsk.id = rskip.retur_sak_kosong 
		join retur_sak_kosong_item_timbang_pakan rskitp on rskitp.retur_sak_kosong_item_pakan = rskip.id 
		join m_pegawai mp on mp.kode_pegawai = rsk.user_buat
		where rsk.no_reg = '{$noreg}'
		group by rskip.id,mp.nama_pegawai,rsk.tgl_buat ,rskip.kode_pakan,rsk.tgl_rhk
SQL;
		return $this->db->query($sql)->result_array();		
	}

	public function stokHarianGudang($noreg){
		$sql = <<<SQL
		select * from (
			select sum(x.STOK) stok,x.KODE_BARANG kode_barang,max(x.TGL) tgl_buat,x.KETERANGAN1 keterangan1 from (
				select JML_PUTAWAY STOK,NO_REFERENSI,KODE_BARANG,PUT_DATE TGL,KETERANGAN1 from movement_d where keterangan2 = '{$noreg}'  and KETERANGAN1 = 'PUT'
				union all
				select -1 * JML_PICK STOK,NO_REFERENSI,KODE_BARANG,PICKED_DATE TGL,KETERANGAN1 from movement_d where keterangan2 = '{$noreg}'  and KETERANGAN1 = 'PICK'
				)x group by x.NO_REFERENSI,x.KODE_BARANG,x.KETERANGAN1
		)y 
		order by y.tgl_buat
SQL;
		return $this->db->query($sql)->result_array();		
	}

	public function stokHarianKandang($noreg){
		$sql = <<<SQL
		select KODE_BARANG kode_barang,JML_ORDER jml_order,TGL_BUAT tgl_buat,tgl_transaksi tgl_kebutuhan, keterangan1 from KANDANG_MOVEMENT_D where no_reg = '{$noreg}' order by tgl_buat 
SQL;
		return $this->db->query($sql)->result_array();		
	}

	public function realisasi_panen($noreg){
		$sql = <<<SQL
		SELECT tgl_panen,sum(jumlah_aktual) jumlah,sum(berat_aktual) berat, (sum(berat_aktual)/sum(jumlah_aktual)) berat_rata FROM 
		REALISASI_PANEN WHERE NO_REG = '{$noreg}'
		GROUP BY tgl_panen
SQL;
		return $this->db->query($sql)->result_array();	
	}

	public function log_do($noreg){
		return $this->db->distinct()
					->select('op.no_op,op.no_lpb,lpd.no_urut,lpd.status,lpd.keterangan,lpd.tgl_buat, mp.nama_pegawai')
					->where(array('keterangan1' => $noreg))
					->join('do d','op.no_op = d.no_op')
					->join('log_ploting_do lpd','lpd.no_do = d.no_do')
					->join('m_pegawai mp','mp.kode_pegawai = lpd.user_buat')
					->order_by('op.no_op')
					->order_by('no_urut','desc')
					->get('op')
					->result_array();
	}

	public function panen($noreg){
		return $this->db->select('rpd.no_do,rpd.kode_pelanggan,rpd.no_sj,rpd.berat,rpd.jumlah')
						->select('rp.berat_aktual r_berat,rp.jumlah_aktual r_jumlah,rp.berat_badan_rata2 r_berat_badan,rp.tgl_panen,rp.tgl_datang,rp.tgl_mulai,rp.tgl_selesai')	
						->select('vdp.nama_sopir sopir,vdp.nopol,vdp.photo,vdp.tgl_verifikasi_sj keluar_farm')
						->select('dateadd(hour,-5,rp.tgl_datang) berangkat_rpa',FALSE)
						->select('dateadd(hour,-5,rp.tgl_datang) verifikasi_berangkat_rpa',FALSE)
						->select('dateadd(hour,5,vdp.tgl_verifikasi_sj) tiba_rpa',FALSE)
						->select('\'operator\' operator,\'tim_panen\' tim_panen,\'sopir\' sopir_ack')
						->where(array('rp.no_reg' => $noreg))
						->join('realisasi_panen_do rpd','rp.no_do = rpd.no_do and rp.no_reg = rpd.no_reg')
						->join('verifikasi_do_panen vdp','vdp.no_do = rp.no_do','left')
						->get('realisasi_panen rp')
						->result_array();
	}
}
