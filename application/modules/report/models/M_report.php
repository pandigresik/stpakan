<?php
class M_report extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function stok_pakan($kode_farm,$tgl_transaksi,$tgl_akses){

		$sql = <<<SQL
		exec dbo.STOK_PAKAN '{$kode_farm}','{$tgl_transaksi}'
SQL;
		$stmt = $this->db->conn_id->prepare($sql);

		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function stok_kavling_bdy($kode_farm,$tgl_transaksi){
		$sql = <<<SQL
		exec dbo.stok_pakan_kavling_bdy '{$kode_farm}','{$tgl_transaksi}'
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function stok_kandang_bdy($kode_farm,$tgl_transaksi){
		$sql = <<<SQL
		exec dbo.stok_pakan_kandang_bdy '{$kode_farm}','{$tgl_transaksi}'
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function stok_pakan_bdy($kode_farm,$tgl_transaksi){

		$sql = <<<SQL
		exec dbo.stok_pakan_bdy '{$kode_farm}','{$tgl_transaksi}'
SQL;
		$stmt = $this->db->conn_id->prepare($sql);

		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function detail_retur_sak($noreg,$tgl_transaksi){
		$sql = <<<SQL
		exec dbo.DETAIL_RETUR_SAK '{$noreg}','{$tgl_transaksi}'
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function detail_terima($kode_barang,$tgl_terima,$no_kavling,$noreg = NULL, $kode_farm){
		$where_noreg = !empty($noreg) ? ' and md.keterangan2 = \''.$noreg.'\'' : '';
		if($no_kavling == 'DMG'){
			$sql = <<<SQL
			select stuff(convert(varchar(19), md.put_date, 126),11,1,' ') TGL_TERIMA
				,md.keterangan1 KODE_SURAT_JALAN
				,md.no_referensi DO
				,md.JML_PUTAWAY
				,md.BERAT_PUTAWAY
				,md.PUT_NAME USER_BUAT
			from MOVEMENT_D md
			where (md.KETERANGAN1 = 'RETUR' or left(md.KETERANGAN1,3) = 'SYS')
				and md.KODE_BARANG = '{$kode_barang}'
				and md.NO_KAVLING = '{$no_kavling}'
				{$where_noreg}
				and cast(md.PUT_DATE as date) = '{$tgl_terima}'
				and md.kode_farm = '{$kode_farm}'
SQL;
		}
		else{
			$sql = <<<SQL
			select stuff(convert(varchar(19), p.TGL_TERIMA, 126),11,1,' ') TGL_TERIMA
				,p.KODE_SURAT_JALAN
				,p.KETERANGAN1 DO
				,'-' NO_REF
				,md.JML_PUTAWAY
				,md.BERAT_PUTAWAY
				,p.USER_BUAT
			from PENERIMAAN p
			inner join MOVEMENT_D md
				on md.NO_REFERENSI = p.NO_PENERIMAAN
				and md.KODE_BARANG = '{$kode_barang}'
				and md.NO_KAVLING = '{$no_kavling}'
				{$where_noreg}
			--	and cast(md.PUT_DATE as date) = '{$tgl_terima}'
				and md.PUT_DATE is not null
			where cast(p.TGL_TERIMA as date) =  '{$tgl_terima}'
				and md.kode_farm = '{$kode_farm}'
			union
			select stuff(convert(varchar(19), md.PUT_DATE, 126),11,1,' ') TGL_TERIMA
				,'-' KODE_SURAT_JALAN
				, '-' DO
				,(select top 1 rkd.no_retur from movement_d mmd
					join retur_kandang_d rkd on rkd.no_reg = mmd.no_referensi 
						and rkd.kode_barang = mmd.kode_barang
						AND mmd.kode_farm = md.kode_farm
					where mmd.no_pallet = md.no_referensi) NO_REF
				,md.JML_PUTAWAY
				,md.BERAT_PUTAWAY
				,md.PUT_NAME USER_BUAT
			from MOVEMENT_D md
				where md.KODE_BARANG = '{$kode_barang}'
				and md.NO_KAVLING = '{$no_kavling}' and md.no_referensi like 'RTN%'
				{$where_noreg}
				and cast(md.PUT_DATE as date) = '{$tgl_terima}'
				and md.kode_farm = '{$kode_farm}'
				and md.PUT_DATE is not null

SQL;
		}

		return $this->db->query($sql);
	}

	public function rinci_retur_sak($noreg,$tgl_transaksi){
		$sql = <<<SQL
		select 'RS/'+rsk.NO_REG + '-'+rsk.NO_URUT no_retur
			,rskitp.JML_SAK retur
			,rski.kode_pakan kode_barang
			,convert(varchar(5),rsk.TGL_BUAT,108) tgl_buat
		from RETUR_SAK_KOSONG rsk
		inner join RETUR_SAK_KOSONG_ITEM_PAKAN rski
			on rsk.id = rski.RETUR_SAK_KOSONG
		inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskitp
			on rskitp.RETUR_SAK_KOSONG_ITEM_PAKAN = rski.id
		where rsk.NO_REG = '{$noreg}' and cast(rsk.TGL_BUAT as date) = '{$tgl_transaksi}'
		order by rsk.tgl_buat
SQL;

	return $this->db->query($sql);
	}

	public function pelunasan_retur_sak($noreg,$tgl_akses,$tgl_transaksi,$data_tambahan){
		$r = array();
		foreach($data_tambahan as $kb =>$dt){
			$sql = <<<SQL
		exec dbo.get_pelunasan_sak_kosong '{$noreg}','{$kb}','{$tgl_transaksi}',{$dt}

SQL;
			$stmt = $this->db->conn_id->prepare($sql);

			$stmt->execute();
			//	print_r($stmt->errorInfo());
			//$stmt->fetchAll(2);
			$r = array_merge($r,$stmt->fetchAll(2));
		}
		return $r;

	}

	public function detail_kandang($kode_kandang,$kode_farm){
		$tgl_rhk = $this->db->select('max(tgl_transaksi) tgl_transaksi, no_reg')->group_by('no_reg')->get_compiled_select('rhk');

		return $this->db->select('ks.no_reg,ks.tgl_doc_in,ks.jml_betina,ks.jml_jantan,mf.nama_flok,mf.tgl_tetas,rhk.tgl_transaksi rhk_terakhir')
			->join('m_flok mf','mf.kode_flok = ks.kode_flok')
			->join('('.$tgl_rhk.') rhk','rhk.no_reg = ks.no_reg','left')
			->where(array('ks.kode_kandang' => $kode_kandang, 'ks.kode_farm' => $kode_farm))
			->get('kandang_siklus ks');
	}

	public function detail_kandang_bdy($kode_kandang,$kode_farm){
		return $this->db->select('ks.no_reg,ks.kode_siklus,mp.periode_siklus,ks.tgl_doc_in,ks.jml_populasi,ks.flok_bdy')
		->join('m_periode mp','mp.kode_siklus = ks.kode_siklus')
		->where(array('ks.kode_kandang' => $kode_kandang, 'ks.kode_farm' => $kode_farm))
		->where('ks.tgl_doc_in <= getdate()')
		->order_by('ks.tgl_doc_in','desc')
		->get('kandang_siklus ks');
	}

	public function report_rhk($tgl_docin,$noreg,$custom_param){
		$sql =<<<SQL

		select r.no_reg
			,r.tgl_transaksi
			,r.b_daya_hidup b_dh
			,r.j_daya_hidup j_dh
			,datediff(day,'{$tgl_docin}',r.tgl_transaksi) hari
	--		,datediff(day,'{$tgl_docin}',r.tgl_transaksi)/7 minggu
			,avg(r.j_jumlah) j_jumlah
			,avg(r.b_jumlah) b_jumlah
			,str(cast(avg(r.b_jumlah) as float)/avg(r.j_jumlah),5,4) rasio
			,avg(r.J_MATI + r.J_AFKIR) j_mati
			,avg(r.b_MATI + r.b_AFKIR) b_mati
			,rp.jenis_kelamin
			,sum(abs(rp.brt_pakai)) brt_pakai
			,avg(r.j_berat_badan) j_berat_badan
			,avg(r.b_berat_badan) b_berat_badan
			,sum(rpr.PROD_BAIK) produksi
			,msb_b.DH_PRC b_dh_prc
			,msb_j.DH_PRC j_dh_prc
			,msb_b.TARGET_PKN b_target_pkn
			,msb_j.TARGET_PKN j_target_pkn
			,msb_b.TARGET_BB b_target_bb
			,msb_j.TARGET_BB j_target_bb
		from rhk r
			inner join kandang_siklus ks
			on ks.NO_REG = r.NO_REG
		inner join M_STD_BREEDING msb_b
			on ks.KODE_STD_BREEDING_B = msb_b.KODE_STD_BREEDING and msb_b.STD_UMUR = datediff(week,'{$tgl_docin}',r.tgl_transaksi)
		inner join M_STD_BREEDING msb_j
			on ks.KODE_STD_BREEDING_J = msb_j.KODE_STD_BREEDING and msb_j.STD_UMUR = datediff(week,'{$tgl_docin}',r.tgl_transaksi)
		inner join RHK_PAKAN rp
			on rp.NO_REG = r.NO_REG and rp.TGL_TRANSAKSI = r.TGL_TRANSAKSI
		left join RHK_PRODUKSI rpr
			on rpr.NO_REG = r.NO_REG and rpr.TGL_TRANSAKSI = r.TGL_TRANSAKSI
		where r.no_reg = '{$noreg}' and	{$custom_param}
		group by r.NO_REG
			,r.TGL_TRANSAKSI
			,r.b_daya_hidup
			,r.j_daya_hidup
			,rp.JENIS_KELAMIN
			,r.J_BERAT_BADAN
			,r.b_BERAT_BADAN
			,msb_b.DH_PRC
			,msb_j.DH_PRC
			,msb_b.TARGET_PKN
			,msb_j.TARGET_PKN
			,msb_b.TARGET_BB
			,msb_j.TARGET_BB
SQL;

		return $this->db->query($sql);
	}

	public function report_rhk_bdy($tgl_docin,$noreg, $flock = null){
		if($flock != null){
			$sql = <<<SQL
			select tgl_transaksi, hari, jenis_kelamin, max(c_dh)c_dh
				, sum(c_jumlah) c_jumlah, sum(jml_panen) jml_panen, sum(c_jumlah) - sum(jml_panen) total
				, sum(c_deplesi) c_deplesi
				, sum(c_mati) c_mati, sum(c_afkir) c_afkir, sum(c_awal) c_awal
				, sum(brt_pakai) brt_pakai, sum(jml_pakai) jml_pakai, sum(c_berat_badan) c_berat_badan
				, sum(c_dh_prc) c_dh_prc, sum(c_target_fcr) c_target_fcr
				, sum(c_target_ip) c_target_ip, sum(c_target_deplesi) c_target_deplesi
				, sum(c_target_bb) c_target_bb, sum(c_pkn_kum_std) c_pkn_kum_std
				, sum(c_target_pkn) c_target_pkn, sum(c_target_adg) c_target_adg
			from (
			select r.no_reg
						,ks.FLOK_BDY
						,r.tgl_transaksi
						,r.c_daya_hidup c_dh
						,datediff(day, ks.TGL_DOC_IN,r.tgl_transaksi) hari
						,avg(r.c_jumlah) c_jumlah
						,avg(isnull(rpanen.jml_panen,0)) jml_panen
						,avg(r.c_MATI + r.c_AFKIR) c_deplesi
						,avg(r.c_MATI) c_mati
						,avg(r.c_AFKIR) c_afkir
						,avg(r.c_awal) c_awal
						,'C' jenis_kelamin
						,sum(rp.brt_pakai) brt_pakai
						,sum(rp.jml_pakai) jml_pakai
						,r.c_berat_badan c_berat_badan
						,msb.DH_KUM_PRC c_dh_prc
						,msb.FCR c_target_fcr
						,mb.TARGET_IP c_target_ip
						,msb.DH_HR_PRC c_target_deplesi
						,msb.TARGET_BB c_target_bb
						,msb.pkn_kum_std c_pkn_kum_std
						,case
							when msb.pkn_hr > 0 then msb.pkn_hr
							else msb.pkn_hr_std
						end c_target_pkn
						,case
							when (datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) % 7) = 0 then
								case when datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) = 7 then (msb.TARGET_BB - (select bb_rata2 from bap_doc where no_reg = r.no_reg)) / 7
								else (msb.TARGET_BB - (select target_bb from M_STD_budidaya_d where KODE_STD_BUDIDAYA = msb.KODE_STD_Budidaya and std_umur = (datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) - 7 ))) / 7
								end
							else 0
						 end
						 'c_target_adg'
					from rhk r
					left join (
						select no_reg, tgl_panen, sum(jumlah_aktual) jml_panen from realisasi_panen group by no_reg, tgl_panen) rpanen
						on r.no_reg = rpanen.no_reg and r.tgl_transaksi = rpanen.tgl_panen
					inner join kandang_siklus ks
						on ks.NO_REG = r.NO_REG
					inner join M_STD_BUDIDAYA mb
						on mb.KODE_STD_BUDIDAYA = ks.KODE_STD_BUDIDAYA
					left join M_STD_budidaya_d msb
						on ks.KODE_STD_Budidaya = msb.KODE_STD_Budidaya
						and msb.std_umur = datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi)
					inner join RHK_PAKAN rp
						on rp.NO_REG = r.NO_REG and rp.TGL_TRANSAKSI = r.TGL_TRANSAKSI
					where r.no_reg like '{$noreg}%'
					group by r.no_reg
						,ks.FLOK_BDY
						,r.tgl_transaksi
						,r.c_daya_hidup
						,r.c_berat_badan
						, ks.TGL_DOC_IN
						,msb.DH_KUM_PRC
						,msb.FCR
						,msb.DH_HR_PRC
						,msb.TARGET_BB
						,msb.pkn_hr
						,msb.pkn_hr_std
						,mb.TARGET_IP
						,msb.KODE_STD_Budidaya
						,msb.pkn_kum_std

			) a
			where FLOK_BDY = '{$flock}'
			group by tgl_transaksi, hari, jenis_kelamin
SQL;
		}
		else{
			$sql =<<<SQL

			select r.no_reg
				,r.tgl_transaksi
				,r.c_daya_hidup c_dh
				,datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) hari
				,avg(r.c_jumlah) c_jumlah
				,avg(isnull(rpanen.jml_panen,0)) jml_panen
				,avg(r.c_MATI + r.c_AFKIR) c_deplesi
				,avg(r.c_MATI) c_mati
				,avg(r.c_AFKIR) c_afkir
				,avg(r.c_awal) c_awal
				,'C' jenis_kelamin
				,sum(rp.brt_pakai) brt_pakai
				,sum(rp.jml_pakai) jml_pakai
				,r.c_berat_badan c_berat_badan
				,msb.DH_KUM_PRC c_dh_prc
				,msb.FCR c_target_fcr
				,mb.TARGET_IP c_target_ip
				,msb.DH_HR_PRC c_target_deplesi
				,msb.TARGET_BB c_target_bb
				,msb.pkn_kum_std c_pkn_kum_std
				,case
					when msb.pkn_hr > 0 then msb.pkn_hr
					else msb.pkn_hr_std
				end c_target_pkn
				,case
					when (datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) % 7) = 0 then
						case when datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) = 7 then (msb.TARGET_BB - (select bb_rata2 from bap_doc where no_reg = r.no_reg)) / 7
						else (msb.TARGET_BB - (select target_bb from M_STD_budidaya_d where KODE_STD_BUDIDAYA = msb.KODE_STD_Budidaya and std_umur = (datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) - 7 ))) / 7
						end
					else 0
				 end
				 'c_target_adg'
			from rhk r
			left join (
				select no_reg, tgl_panen, sum(jumlah_aktual) jml_panen from realisasi_panen group by no_reg, tgl_panen) rpanen
				on r.no_reg = rpanen.no_reg and r.tgl_transaksi = rpanen.tgl_panen
			inner join kandang_siklus ks
				on ks.NO_REG = r.NO_REG
			inner join M_STD_BUDIDAYA mb
				on mb.KODE_STD_BUDIDAYA = ks.KODE_STD_BUDIDAYA
			left join M_STD_budidaya_d msb
				on ks.KODE_STD_Budidaya = msb.KODE_STD_Budidaya
				and msb.std_umur = datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi)
			inner join RHK_PAKAN rp
				on rp.NO_REG = r.NO_REG and rp.TGL_TRANSAKSI = r.TGL_TRANSAKSI
			where r.no_reg = '{$noreg}'
			group by r.no_reg
				,r.tgl_transaksi
				,r.c_daya_hidup
				,r.c_berat_badan
				,msb.DH_KUM_PRC
				,msb.FCR
				,msb.DH_HR_PRC
				,msb.TARGET_BB
				,msb.pkn_hr
				,msb.pkn_hr_std
				,mb.TARGET_IP
				,msb.KODE_STD_Budidaya
				,msb.pkn_kum_std
				, ks.TGL_DOC_IN

SQL;
		}

		#echo $sql;
		return $this->db->query($sql);
	}

	public function stok_awal_kandang($tgl_docin,$noreg,$flock=null){
		if($flock != null){
		$sql = <<<SQL
			select sum(bd.stok_awal) - (
					select sum(c_afkir)
					from rhk
					join KANDANG_SIKLUS ks on rhk.NO_REG = ks.NO_REG and ks.FLOK_BDY = '1' and ks.NO_REG like '{$noreg}%'
				) stok_awal
			from bap_doc bd
			join KANDANG_SIKLUS ks on bd.NO_REG = ks.NO_REG and ks.FLOK_BDY = '{$flock}' and ks.NO_REG like '{$noreg}%'
SQL;
		}else{
		$sql = <<<SQL
			select stok_awal - (select sum(c_afkir) from rhk where no_reg = '{$noreg}' and datediff(day,'{$tgl_docin}',tgl_transaksi) <= 7)  stok_awal
			from bap_doc where no_reg = '{$noreg}'
SQL;
		}
		return $this->db->query($sql);
	}

	public function bb_sebelumnya($tgl_docin,$noreg,$custom_param){
		$sql = <<<SQL
		select datediff(day,'{$tgl_docin}',r.tgl_transaksi)  hari
			,r.b_berat_badan
			,r.j_berat_badan
		from rhk r
		where r.no_reg = '{$noreg}'
			and {$custom_param}
			and (r.B_BERAT_BADAN > 0 or r.J_BERAT_BADAN > 0)
SQL;
		return $this->db->query($sql);
	}

	public function bb_sebelumnya_bdy($tgl_docin,$noreg, $flock=null){
		if($flock != null){
			$sql = <<<SQL
			select hari, avg(c_berat_badan) c_berat_badan
			from (
				select ks.FLOK_BDY, bd.no_reg, 0 hari, bb_rata2/1000 c_berat_badan
				from bap_doc bd
				join KANDANG_SIKLUS ks on bd.NO_REG = ks.NO_REG
				where bd.no_reg like '{$noreg}%'
				union
				select ks.FLOK_BDY, r.no_reg, datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) hari
				,r.c_berat_badan
				from rhk r
				join KANDANG_SIKLUS ks on r.NO_REG = ks.NO_REG
				where r.no_reg like '{$noreg}%'
				and r.c_BERAT_BADAN > 0
				and datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) % 7 = 0
			) a
			where FLOK_BDY = '{$flock}'
			group by hari
SQL;

		}else{
			$sql = <<<SQL
					select 0 hari,(select bb_rata2/1000 from bap_doc where no_reg =  '{$noreg}') c_berat_badan
					union
					select datediff(day,'{$tgl_docin}',r.tgl_transaksi) hari
						,r.c_berat_badan
					from rhk r
					where r.no_reg = '{$noreg}'
						and r.c_BERAT_BADAN > 0
						and datediff(day,'{$tgl_docin}',r.tgl_transaksi) % 7 = 0
				--	union
				--	select umur_panen hari,avg(BERAT_BADAN_RATA2) c_berat_badan
				--	from REALISASI_PANEN where no_reg =  '{$noreg}'  group by UMUR_PANEN
SQL;
		}


				return $this->db->query($sql);
	}

	public function get_pemakaian_pakan_flock($noreg, $flock){
		$sql = <<<SQL
					select umur, min(tgl_transaksi) tgl_transaksi, nama_pakan, kode_barang, jenis_kelamin
						, sum(isnull(jml_terima,0)) jml_terima, sum(isnull(brt_terima,0))brt_terima
						, sum(isnull(jml_pakai,0))jml_pakai, sum(isnull(brt_pakai,0)) brt_pakai
						, sum(isnull(jml_akhir,0)) jml_akhir, sum(isnull(brt_akhir,0)) brt_akhir
						, avg(komposisi_pakan) komposisi_pakan
					from (
						select r.NO_REG, ks.FLOK_BDY, datediff(day,ks.TGL_DOC_IN,r.tgl_transaksi) umur, r.TGL_TRANSAKSI tgl_transaksi
							,mb.NAMA_BARANG +', '+dbo.BENTUK_CONVERTION(mb.bentuk_barang) nama_pakan
							,r.KODE_BARANG kode_barang
							,r.jenis_kelamin
							,coalesce(r.JML_TERIMA,0) jml_terima
							,coalesce(r.BRT_TERIMA,0) brt_terima
							,abs(coalesce(r.JML_PAKAI,0)) jml_pakai
							,abs(coalesce(r.BRT_PAKAI,0)) brt_pakai
							,r.JML_AKHIR jml_akhir
							,r.BRT_AKHIR brt_akhir
							,isnull(oke.komposisi_pakan,0) komposisi_pakan
						from rhk_pakan r
						inner join m_barang mb
							on mb.KODE_BARANG = r.KODE_BARANG
						left join (
									select le.no_reg
										,le.tgl_kebutuhan
										,le.kode_barang
										,le.komposisi_pakan
									from lpb l
									join lpb_e le
										on le.no_lpb = l.no_lpb and le.no_reg like 'BW/2017-2%'
									where l.status_lpb = 'A'
						)oke
							on oke.NO_REG = r.no_reg
								and oke.kode_barang = r.kode_barang
								and oke.tgl_kebutuhan = r.tgl_transaksi
						join KANDANG_SIKLUS ks on r.NO_REG = ks.NO_REG
						where r.NO_REG like 'BW/2017-2%'
					) a
					where FLOK_BDY = 1
					group by umur, nama_pakan, kode_barang, jenis_kelamin
SQL;
		return $this->db->query($sql);
	}

	public function get_pemakaian_pakan($noreg,$custom_param = NULL){
		$where_custom = !empty($custom_param) ? ' and '.$custom_param : '';
//		$noreg = 'SG5-2015/01';
		$sql = <<<SQL
		select r.TGL_TRANSAKSI tgl_transaksi
			,mb.NAMA_BARANG +', '+dbo.BENTUK_CONVERTION(mb.bentuk_barang) nama_pakan
			,r.KODE_BARANG kode_barang
			,r.jenis_kelamin
			,coalesce(r.JML_TERIMA,0) jml_terima
			,coalesce(r.BRT_TERIMA,0) brt_terima
			,abs(coalesce(r.JML_PAKAI,0)) jml_pakai
			,abs(coalesce(r.BRT_PAKAI,0)) brt_pakai
			,r.JML_AKHIR jml_akhir
			,r.BRT_AKHIR brt_akhir
			,oke.komposisi_pakan
		from rhk_pakan r
		inner join m_barang mb
			on mb.KODE_BARANG = r.KODE_BARANG
		left join (
					select le.no_reg
						,le.tgl_kebutuhan
						,le.kode_barang
						,le.komposisi_pakan
					from lpb l
					join lpb_e le
						on le.no_lpb = l.no_lpb and le.no_reg = '{$noreg}'
					where l.status_lpb = 'A'
		)oke
			on oke.NO_REG = r.no_reg
				and oke.kode_barang = r.kode_barang
				and oke.tgl_kebutuhan = r.tgl_transaksi
		where r.NO_REG = '{$noreg}'
		{$where_custom}
SQL;

		return $this->db->query($sql);
	}

	public function get_penggantian_pakan_flock($noreg, $flock){
		$sql = <<<SQL
					select min(tgl_transaksi) tgl_transaksi,
						kode_barang, nama_pakan, jenis_kelamin
						, sum(isnull(jml,0)) jml, keterangan
					from (
					select od.flok_bdy, kd.TGL_TRANSAKSI tgl_transaksi
						,kd.KODE_BARANG kode_barang
						,mb.NAMA_BARANG +', '+dbo.BENTUK_CONVERTION(mb.bentuk_barang) nama_pakan
						,kd.JENIS_KELAMIN jenis_kelamin
						,sum(kd.JML_ORDER) jml
						,'diganti' keterangan
					from KANDANG_MOVEMENT_d kd
					inner join (
						select ks.FLOK_BDY
							,k.TGL_BUAT
							,k.NO_ORDER
							,ke.KODE_BARANG
							,ke.JENIS_KELAMIN
							,kd.NO_REG
							,pk.no_penerimaan_kandang
						from order_kandang k
						inner join order_kandang_d kd
							on k.NO_ORDER = kd.NO_ORDER and k.KODE_FARM = kd.KODE_FARM
						inner join order_kandang_e ke
							on ke.NO_ORDER = kd.NO_ORDER and ke.NO_REG = kd.NO_REG
							and k.NO_REFERENSI like 'RP%'
						inner join PENERIMAAN_KANDANG pk
							on pk.NO_ORDER = k.no_order
						inner join KANDANG_SIKLUS ks on kd.no_reg = ks.no_reg
						where kd.no_reg like '{$noreg}%'
						group by
							ks.FLOK_BDY
							,k.TGL_BUAT
							,k.NO_ORDER
							,ke.KODE_BARANG
							,ke.JENIS_KELAMIN
							,kd.NO_REG
							,pk.no_penerimaan_kandang
					)od
					on od.no_penerimaan_kandang = kd.KETERANGAN2 and od.NO_REG = kd.NO_REG and od.kode_barang = kd.KODE_BARANG
					inner join m_barang mb
						on mb.kode_barang = kd.kode_barang
					where KETERANGAN1 = 'PENERIMAAN KANDANG'
					group by od.FLOK_BDY
						, kd.TGL_TRANSAKSI
						,kd.KODE_BARANG
						,kd.JENIS_KELAMIN
						,mb.nama_barang
						,mb.bentuk_barang
					union
					select ks.flok_bdy
						,cast(rpr.TGL_BUAT as date) tgl_transaksi
						,rpri.KODE_PAKAN kode_barang
						,mb.NAMA_BARANG +', '+dbo.BENTUK_CONVERTION(mb.bentuk_barang) nama_pakan
						,rpri.JENIS_KELAMIN jenis_kelamin
						,count(*) jml
						,'retur' keterangan
					from RETUR_PAKAN_RUSAK rpr
					inner join RETUR_PAKAN_RUSAK_ITEM rpri
						on rpri.RETUR_PAKAN_RUSAK = rpr.ID
					inner join RETUR_PAKAN_RUSAK_ITEM_TIMBANG rprt
						on rprt.RETUR_PAKAN_RUSAK_ITEM = rpri.id
					inner join m_barang mb
						on mb.kode_barang = rpri.kode_pakan
					inner join KANDANG_SIKLUS ks on rpr.no_reg = ks.no_reg
					where rpr.no_reg like '{$noreg}%'
					group by ks.FLOK_BDY
						, cast(rpr.TGL_BUAT as date)
						,rpri.KODE_PAKAN
						,rpri.JENIS_KELAMIN
						,mb.nama_barang
						,mb.bentuk_barang
					) a
					where flok_bdy = '{$flock}'
					group by kode_barang, nama_pakan, jenis_kelamin, keterangan
SQL;
		return $this->db->query($sql);
	}

	public function get_penggantian_pakan($noreg,$tgl_param = NULL){
		$where_tgl_1 = !empty($tgl_param) ? ' and cast(k.TGL_BUAT as date) '. $tgl_param : '';
		$where_tgl_2 = !empty($tgl_param) ? ' and cast(rpr.TGL_BUAT as date) '.$tgl_param : '';
//		$noreg = 'BT1-2015/01';
		$sql = <<<SQL
		select kd.TGL_TRANSAKSI tgl_transaksi
			,kd.KODE_BARANG kode_barang
			,mb.NAMA_BARANG +', '+dbo.BENTUK_CONVERTION(mb.bentuk_barang) nama_pakan
			,kd.JENIS_KELAMIN jenis_kelamin
			,sum(kd.JML_ORDER) jml
			,'diganti' keterangan
		from KANDANG_MOVEMENT_d kd
		inner join (
			select k.TGL_BUAT
				,k.NO_ORDER
				,ke.KODE_BARANG
				,ke.JENIS_KELAMIN
				,kd.NO_REG
				,pk.no_penerimaan_kandang
			from order_kandang k
			inner join order_kandang_d kd
				on k.NO_ORDER = kd.NO_ORDER and k.KODE_FARM = kd.KODE_FARM and kd.no_reg = '{$noreg}'
			inner join order_kandang_e ke
				on ke.NO_ORDER = kd.NO_ORDER and ke.NO_REG = kd.NO_REG
				and k.NO_REFERENSI like 'RP%'
			inner join PENERIMAAN_KANDANG pk
				on pk.NO_ORDER = k.no_order
			{$where_tgl_1}
			group by  k.TGL_BUAT
				,k.NO_ORDER
				,ke.KODE_BARANG
				,ke.JENIS_KELAMIN
				,kd.NO_REG
				,pk.no_penerimaan_kandang
		)od
		on od.no_penerimaan_kandang = kd.KETERANGAN2 and od.NO_REG = kd.NO_REG and od.kode_barang = kd.KODE_BARANG
		inner join m_barang mb
			on mb.kode_barang = kd.kode_barang
		where KETERANGAN1 = 'PENERIMAAN KANDANG'
		group by kd.TGL_TRANSAKSI
			,kd.KODE_BARANG
			,kd.JENIS_KELAMIN
			,mb.nama_barang
			,mb.bentuk_barang
		union
		select cast(rpr.TGL_BUAT as date) tgl_transaksi
			,rpri.KODE_PAKAN kode_barang
			,mb.NAMA_BARANG +', '+dbo.BENTUK_CONVERTION(mb.bentuk_barang) nama_pakan
			,rpri.JENIS_KELAMIN jenis_kelamin
			,count(*) jml
			,'retur' keterangan
		from RETUR_PAKAN_RUSAK rpr
		inner join RETUR_PAKAN_RUSAK_ITEM rpri
			on rpri.RETUR_PAKAN_RUSAK = rpr.ID
		inner join RETUR_PAKAN_RUSAK_ITEM_TIMBANG rprt
			on rprt.RETUR_PAKAN_RUSAK_ITEM = rpri.id
		inner join m_barang mb
			on mb.kode_barang = rpri.kode_pakan
		where rpr.no_reg = '{$noreg}' {$where_tgl_2}
		group by cast(rpr.TGL_BUAT as date)
			,rpri.KODE_PAKAN
			,rpri.JENIS_KELAMIN
			,mb.nama_barang
			,mb.bentuk_barang
SQL;

		return $this->db->query($sql);
	}


	public function get_retur_sak_bdy($noreg, $tgl_param = NULL, $flock = null){
		$where = '';
		$where_tgl = !empty($tgl_param) ? ' tb.tgl_transaksi '.$tgl_param : '';
		$where_flok = !empty($flock) ? ' ks.flok_bdy '.$flock : '';
		$where = ($where_tgl != '') || ($where_flok != '') ? ' where ' : '';
		$where .= ($where_tgl != '') ? ' '.$where_tgl.' ' : '';
		$where .= ($where_tgl != '') && ($where_flok != '') ? ' and ' : '';
		$where .= ($where_flok != '') ? ' '.$where_flok.' ' : '';
//		$noreg = 'BT1-2015/01';
		$sql = <<<MMM
		exec dbo.get_retur_sak_kosong '{$noreg}','{$where}'
MMM;
	return $this->db->query($sql);

	}

	public function get_retur_sak($noreg,$tgl_param = NULL){
		$where_tgl = !empty($tgl_param) ? ' and cast(rsk.TGL_BUAT as date) '.$tgl_param : '';
//		$noreg = 'BT1-2015/01';
		$sql = <<<MMM
		select cast(rsk.TGL_BUAT as date) tgl_transaksi
			,rski.KODE_PAKAN kode_barang
			,mb.NAMA_BARANG +', '+dbo.BENTUK_CONVERTION(mb.bentuk_barang) nama_pakan
			,rski.JENIS_KELAMIN jenis_kelamin
			,rski.HUTANG hutang
			,sum(rskip.JML_SAK) jml_retur
		from RETUR_SAK_KOSONG rsk
		inner join RETUR_SAK_KOSONG_ITEM_PAKAN rski
			on rski.RETUR_SAK_KOSONG = rsk.id
		inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskip
			on rskip.RETUR_SAK_KOSONG_ITEM_PAKAN = rski.ID
		inner join m_barang mb
			on mb.KODE_BARANG = rski.KODE_PAKAN
		where rsk.no_reg = '{$noreg}' {$where_tgl}
		group by cast(rsk.TGL_BUAT as date)
			,rski.JENIS_KELAMIN
			,rski.KODE_PAKAN
			,rski.HUTANG
			,mb.nama_barang
			,mb.bentuk_barang
MMM;
	return $this->db->query($sql);
	}

	public function get_retur_sak_terakhir($noreg, $flock = null){
//		$noreg = 'BT1-2015/01';
		if($flock != null){
			$sql = <<<SQL
						select cast(rsk.TGL_BUAT as date) tgl_transaksi
							,rski.JENIS_KELAMIN jenis_kelamin
							,rski.KODE_PAKAN kode_barang
							,rski.HUTANG hutang
							,rski.JML_PAKAI jml_pakai
							,count(rskip.JML_SAK) jml_retur
						from RETUR_SAK_KOSONG_ITEM_PAKAN rski
						inner join RETUR_SAK_KOSONG rsk
							on rsk.id = rski.RETUR_SAK_KOSONG
						inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskip
							on rskip.RETUR_SAK_KOSONG_ITEM_PAKAN = rski.ID
						inner join(
							select rsk.NO_REG
									,rski.JENIS_KELAMIN
									,rski.KODE_PAKAN
									,max(rsk.TGL_BUAT) tgl_retur_terakhir
								from RETUR_SAK_KOSONG rsk
								inner join RETUR_SAK_KOSONG_ITEM_PAKAN rski
									on rski.RETUR_SAK_KOSONG = rsk.id
								inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskip
									on rskip.RETUR_SAK_KOSONG_ITEM_PAKAN = rski.ID
								join kandang_siklus ks on rsk.no_reg = ks.no_reg
								where rsk.NO_REG like '{$noreg}%' and ks.flok_bdy = '{$flock}'
								group by rsk.NO_REG
									,rski.JENIS_KELAMIN
									,rski.KODE_PAKAN
						)zz on zz.KODE_PAKAN = rski.KODE_PAKAN
							and zz.JENIS_KELAMIN = rski.JENIS_KELAMIN
							and zz.tgl_retur_terakhir = rsk.TGL_BUAT
						join kandang_siklus ks on rsk.no_reg = ks.no_reg
						where rsk.NO_REG like '{$noreg}%' and ks.flok_bdy = '{$flock}'
						group by cast(rsk.TGL_BUAT as date)
							,rski.JENIS_KELAMIN
							,rski.KODE_PAKAN
							,rski.HUTANG
							,rski.JML_PAKAI
SQL;
		}else{
			$sql = <<<SQL
						select cast(rsk.TGL_BUAT as date) tgl_transaksi
							,rski.JENIS_KELAMIN jenis_kelamin
							,rski.KODE_PAKAN kode_barang
							,rski.HUTANG hutang
							,rski.JML_PAKAI jml_pakai
							,count(rskip.JML_SAK) jml_retur
						from RETUR_SAK_KOSONG_ITEM_PAKAN rski
						inner join RETUR_SAK_KOSONG rsk
							on rsk.id = rski.RETUR_SAK_KOSONG
						inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskip
							on rskip.RETUR_SAK_KOSONG_ITEM_PAKAN = rski.ID
						inner join(
							select rsk.NO_REG
									,rski.JENIS_KELAMIN
									,rski.KODE_PAKAN
									,max(rsk.TGL_BUAT) tgl_retur_terakhir
								from RETUR_SAK_KOSONG rsk
								inner join RETUR_SAK_KOSONG_ITEM_PAKAN rski
									on rski.RETUR_SAK_KOSONG = rsk.id
								inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskip
									on rskip.RETUR_SAK_KOSONG_ITEM_PAKAN = rski.ID
								where rsk.NO_REG = '{$noreg}'
								group by rsk.NO_REG
									,rski.JENIS_KELAMIN
									,rski.KODE_PAKAN
						)zz on zz.KODE_PAKAN = rski.KODE_PAKAN
							and zz.JENIS_KELAMIN = rski.JENIS_KELAMIN
							and zz.tgl_retur_terakhir = rsk.TGL_BUAT
						where rsk.NO_REG = '{$noreg}'
						group by cast(rsk.TGL_BUAT as date)
							,rski.JENIS_KELAMIN
							,rski.KODE_PAKAN
							,rski.HUTANG
							,rski.JML_PAKAI
SQL;
		}
		return $this->db->query($sql);

	}

	public function list_farm($id = null,$grup_farm){
		$where = 'where mf.grup_farm = \''.$grup_farm.'\'';
		$where .= (!empty($id)) ? ' and mf.kode_farm = \''.$id.'\'' : '' ;
		$sql = <<<SQL
		select mf.kode_farm
			,mp.kode_siklus
			,mf.nama_farm
			,mp.kode_strain
			,mp.periode_siklus
		from m_farm mf
		inner join m_periode mp
			on mp.kode_farm = mf.kode_farm and mp.status_periode = 'A' and kode_siklus in (select distinct kode_siklus from kandang_siklus where status_siklus = 'O' )
		{$where}
		order by mp.KODE_SIKLUS
SQL;
		
		return $this->db->query($sql);
	}

	public function populasiAwal($noreg){
		$sql = <<<SQL
		select bd.bb_rata2
				,mh.nama_hatchery
				--,(select c_awal from rhk where no_reg = ks.no_reg and datediff(day,ks.tgl_doc_in,tgl_transaksi) = 7) stok_awal
				, bd.stok_awal -
				(select sum(c_afkir) from rhk where no_reg = ks.no_reg and tgl_transaksi <= dateadd(day,7, ks.tgl_doc_in)) stok_awal
		from bap_doc bd
		join m_hatchery mh
			on bd.kode_hatchery = mh.kode_hatchery
		join kandang_siklus ks
		on ks.no_reg = bd.no_reg
		where ks.no_reg = '{$noreg}'
SQL;
		return $this->db->query($sql);
	}

    public function populasiSetelahUmur7($noreg){
        $sql = <<<SQL
        select c_awal stok_awal from rhk
        join kandang_siklus ks
		on ks.no_reg = rhk.no_reg
        where rhk.no_reg = '{$noreg}' and datediff(day,ks.tgl_doc_in,tgl_transaksi) = 7
SQL;
        return $this->db->query($sql);
    }

	public function kandangAsalMutasi($noref){
		$sql = <<<SQL
		select ks.kode_kandang
		from MUTASI_PAKAN mp
		join kandang_siklus ks  on ks.NO_REG = mp.NO_REG_ASAL
		where no_mutasi = '{$noref}'
SQL;
		return $this->db->query($sql);
	}

	public function detailInformasi($farm,$tahun){
		$sql = <<<SQL
		select mp.periode_siklus
			,ks.flok_bdy
			,ks.kode_kandang
			,mf.nama_farm
			,ks.tgl_doc_in
			,ks.jml_populasi
	--		,ks.status_siklus
			,(select min(tgl_panen) from realisasi_panen where no_reg = ks.no_reg) tgl_panen
			,(select sum(jumlah_aktual) from realisasi_panen where no_reg = ks.no_reg) jml_panen

		from kandang_siklus ks
		join m_periode mp
			on ks.kode_siklus = mp.kode_siklus and left(mp.periode_siklus,4) = '{$tahun}'
		join m_farm mf
			on mf.kode_farm = ks.kode_farm
		where ks.kode_farm in ('{$farm}') and ks.status_siklus = 'C'
		order by mp.periode_siklus,ks.flok_bdy,ks.tgl_doc_in
SQL;

			return $this->db->query($sql);
	}

	public function jumlah_panen($noreg, $flock = null){
		if($flock != null){
			$sql = <<<SQL
						select UMUR_PANEN hari,sum(JUMLAH_AKTUAL) total,sum(berat_aktual) bb
						from REALISASI_PANEN rp
						join KANDANG_SIKLUS ks on rp.no_reg = ks.no_reg
						where rp.no_reg like '{$noreg}%' and ks.flok_bdy = '{$flock}'
						group by UMUR_PANEN
SQL;
		}else{
			$sql = <<<SQL
						select UMUR_PANEN hari,sum(JUMLAH_AKTUAL) total,sum(berat_aktual) bb
						from REALISASI_PANEN
						where no_reg = '{$noreg}'
						group by UMUR_PANEN
SQL;
		}

		return $this->db->query($sql);
	}

	public function retur_pakan_akhir_siklus($kodefarm,$tgl_retur){
		$sql = <<<SQL
		select rkd.no_reg
				,rkd.kode_barang
				,sum(rkd.jml_retur) jml_retur
				,sum(rkd.brt_retur) brt_retur
		from retur_kandang rk
		join retur_kandang_d rkd on rk.no_retur = rkd.no_retur
		join kandang_siklus ks on ks.no_reg = rkd.no_reg and ks.kode_farm = '{$kodefarm}'
		where rk.tgl_retur = '{$tgl_retur}' and rk.tgl_approve is not null
		group by rkd.no_reg,rkd.kode_barang
SQL;
		return $this->db->query($sql);
	}
	public function PJSKListBarang(){
		$sql = <<<QUERY
			select tmp.KODE_BARANG,M_BARANG.NAMA_BARANG
from (

       select tr.TGL_KEBUTUHAN, tr.NO_REG, tr.NAMA_KANDANG, tr.KODE_BARANG, tr.TGL_BUAT TGL_TERIMA, tr.JML_TERIMA, rhk.TGL_BUAT TGL_PAKAI, rhk.JML_PAKAI, rt.TGL_BUAT TGL_KEMBALI, rt.SAK,

       rhk.JML_PAKAI - rt.SAK HUTANG_HR

       from (

             select ok.TGL_KEBUTUHAN, md.KETERANGAN2 NO_REG, mk.NAMA_KANDANG, md.KODE_BARANG, max(md.PICKED_DATE) TGL_BUAT, sum(md.JML_PICK) JML_TERIMA

             from MOVEMENT_D md

             join (select NO_ORDER, NO_REG, TGL_KEBUTUHAN

                    from order_kandang_e

                    group by NO_ORDER, NO_REG, TGL_KEBUTUHAN

                    ) ok on md.KETERANGAN2 = ok.NO_REG and md.NO_REFERENSI = ok.NO_ORDER

             join KANDANG_SIKLUS ks on md.KETERANGAN2 = ks.NO_REG

             join M_KANDANG mk on ks.KODE_FARM = mk.KODE_FARM and ks.KODE_KANDANG = mk.KODE_KANDANG

             group by md.KETERANGAN2, mk.NAMA_KANDANG, md.KODE_BARANG, ok.TGL_KEBUTUHAN

       ) tr

       join (

             select rp.NO_REG, rp.TGL_TRANSAKSI, rp.KODE_BARANG, sum(rp.JML_PAKAI) JML_PAKAI, max(rh.TGL_BUAT) TGL_BUAT

             from rhk_pakan rp

             join rhk rh on rp.no_reg = rh.no_reg and rp.TGL_TRANSAKSI = rh.TGL_TRANSAKSI

             group by rp.NO_REG, rp.KODE_BARANG, rp.TGL_TRANSAKSI

       ) rhk on tr.NO_REG = rhk.NO_REG and tr.TGL_KEBUTUHAN = rhk.TGL_TRANSAKSI and tr.KODE_BARANG = rhk.KODE_BARANG

       join (

             select rsh.NO_REG

                    , mk.NAMA_KANDANG

                    , rsh.TGL_RHK

                    , rsd.KODE_PAKAN

                    , sum(rse.JML_SAK) SAK

                    , max(rsh.TGL_BUAT) TGL_BUAT

                    , sum(rse.BRT_SAK) KG

             from RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rse

             join RETUR_SAK_KOSONG_ITEM_PAKAN rsd on rse.RETUR_SAK_KOSONG_ITEM_PAKAN = rsd.ID

             join RETUR_SAK_KOSONG rsh on rsd.RETUR_SAK_KOSONG = rsh.ID

             join KANDANG_SIKLUS ks on rsh.NO_REG = ks.NO_REG

             join M_KANDANG mk on ks.KODE_FARM = mk.KODE_FARM and ks.KODE_KANDANG = mk.KODE_KANDANG

             group by rsh.NO_REG, rsd.KODE_PAKAN, mk.NAMA_KANDANG, rsh.TGL_RHK

             --order by rsh.NO_REG, rsh.TGL_RHK

       ) rt on tr.NO_REG = rt.NO_REG and tr.TGL_KEBUTUHAN = rt.TGL_RHK and tr.KODE_BARANG = rt.KODE_PAKAN

) tmp

join kandang_siklus ks on tmp.NO_REG = ks.NO_REG and ks.STATUS_SIKLUS = 'O'
LEFT JOIN M_BARANG ON M_BARANG.KODE_BARANG = tmp.KODE_BARANG
GROUP BY tmp.kode_barang,M_BARANG.NAMA_BARANG
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function PJSKListTanggal(){
		$sql = <<<QUERY
			select cast(tmp.TGL_KEMBALI as date) TGL_TRANSAKSI
from (

       select tr.TGL_KEBUTUHAN, tr.NO_REG, tr.NAMA_KANDANG, tr.KODE_BARANG, tr.TGL_BUAT TGL_TERIMA, tr.JML_TERIMA, rhk.TGL_BUAT TGL_PAKAI, rhk.JML_PAKAI, rt.TGL_BUAT TGL_KEMBALI, rt.SAK,

       rhk.JML_PAKAI - rt.SAK HUTANG_HR

       from (

             select ok.TGL_KEBUTUHAN, md.KETERANGAN2 NO_REG, mk.NAMA_KANDANG, md.KODE_BARANG, max(md.PICKED_DATE) TGL_BUAT, sum(md.JML_PICK) JML_TERIMA

             from MOVEMENT_D md

             join (select NO_ORDER, NO_REG, TGL_KEBUTUHAN

                    from order_kandang_e

                    group by NO_ORDER, NO_REG, TGL_KEBUTUHAN

                    ) ok on md.KETERANGAN2 = ok.NO_REG and md.NO_REFERENSI = ok.NO_ORDER

             join KANDANG_SIKLUS ks on md.KETERANGAN2 = ks.NO_REG

             join M_KANDANG mk on ks.KODE_FARM = mk.KODE_FARM and ks.KODE_KANDANG = mk.KODE_KANDANG

             group by md.KETERANGAN2, mk.NAMA_KANDANG, md.KODE_BARANG, ok.TGL_KEBUTUHAN

       ) tr

       join (

             select rp.NO_REG, rp.TGL_TRANSAKSI, rp.KODE_BARANG, sum(rp.JML_PAKAI) JML_PAKAI, max(rh.TGL_BUAT) TGL_BUAT

             from rhk_pakan rp

             join rhk rh on rp.no_reg = rh.no_reg and rp.TGL_TRANSAKSI = rh.TGL_TRANSAKSI

             group by rp.NO_REG, rp.KODE_BARANG, rp.TGL_TRANSAKSI

       ) rhk on tr.NO_REG = rhk.NO_REG and tr.TGL_KEBUTUHAN = rhk.TGL_TRANSAKSI and tr.KODE_BARANG = rhk.KODE_BARANG

       join (

             select rsh.NO_REG

                    , mk.NAMA_KANDANG

                    , rsh.TGL_RHK

                    , rsd.KODE_PAKAN

                    , sum(rse.JML_SAK) SAK

                    , max(rsh.TGL_BUAT) TGL_BUAT

                    , sum(rse.BRT_SAK) KG

             from RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rse

             join RETUR_SAK_KOSONG_ITEM_PAKAN rsd on rse.RETUR_SAK_KOSONG_ITEM_PAKAN = rsd.ID

             join RETUR_SAK_KOSONG rsh on rsd.RETUR_SAK_KOSONG = rsh.ID

             join KANDANG_SIKLUS ks on rsh.NO_REG = ks.NO_REG

             join M_KANDANG mk on ks.KODE_FARM = mk.KODE_FARM and ks.KODE_KANDANG = mk.KODE_KANDANG

             group by rsh.NO_REG, rsd.KODE_PAKAN, mk.NAMA_KANDANG, rsh.TGL_RHK

             --order by rsh.NO_REG, rsh.TGL_RHK

       ) rt on tr.NO_REG = rt.NO_REG and tr.TGL_KEBUTUHAN = rt.TGL_RHK and tr.KODE_BARANG = rt.KODE_PAKAN

) tmp

join kandang_siklus ks on tmp.NO_REG = ks.NO_REG and ks.STATUS_SIKLUS = 'O'
LEFT JOIN M_BARANG ON M_BARANG.KODE_BARANG = tmp.KODE_BARANG
GROUP BY cast(tmp.TGL_KEMBALI as date) TGL_TRANSAKSI
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function listFarm($farm){
		if($farm == 'ALL'){
			$r = $this->db->select('mf.kode_farm,mf.nama_farm')
							->join('pegawai_d pd','pd.kode_farm = mf.kode_farm and pd.kode_pegawai = \''.$this->_user.'\'')
							->group_by(array('mf.kode_farm','mf.nama_farm'))
							->get('m_farm mf')
							->result_array();
			$result = array();
			foreach($r as $y){
				array_push($result,$y['kode_farm']);
			}
			print_r($r);
			$farm = implode('\',\'',$result);
		}
		return $farm;
	}

	public function listSiklus($siklus,$listfarm){
		if(empty($siklus)){
			return null;
		}
		$r = $this->db->select('kode_siklus')
						->where(array('periode_siklus' => $siklus))
						->where('kode_farm in (\''.$listfarm.'\')')
						->get('m_periode')
						->result_array();
	  $result = array();
		foreach($r as $y){
			array_push($result,$y['kode_siklus']);
			}
		return implode('\',\'',$result);
	}

	public function lsamFlock($kode_farm, $flock, $periode_siklus){
		$sql = <<<SQL
		exec dbo.get_lsam_flock '{$kode_farm}', '{$flock}', '{$periode_siklus}'
SQL;
		//print_r($sql);
		$stmt = $this->db->conn_id->prepare($sql);

		$stmt->execute();
		$result = $stmt->fetchAll(2);
		//print_r($result);
		return $result;
	}

	public function lsamKandang($no_reg){
		$sql = <<<SQL
		exec dbo.get_lsam_kandang '{$no_reg}'
SQL;
		//print_r($sql);
		$stmt = $this->db->conn_id->prepare($sql);

		$stmt->execute();
		$result = $stmt->fetchAll(2);
		//print_r($result);
		return $result;
	}

	public function lsamFarm($kode_farm, $periode_siklus){
		$sql = <<<SQL
		exec dbo.get_lsam_farm '{$kode_farm}', '{$periode_siklus}'
SQL;
		
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function reportApprovalPp($no_reg){
		$sql = <<<SQL
		SELECT le.jml_order,le.TGL_KEBUTUHAN tgl_transaksi,le.kode_barang, 'C' jenis_kelamin
		, CASE WHEN rlb.USER_REVIEW = rlb.USER_REJECT THEN 0 ELSE 1 END kadiv_approve
		FROM lpb_e le
		JOIN lpb l ON l.NO_LPB = le.NO_LPB AND l.STATUS_LPB = 'A' 
		JOIN review_lpb_budidaya rlb ON rlb.NO_LPB = l.NO_LPB AND le.kode_barang  = rlb.KODE_BARANG AND le.TGL_KEBUTUHAN = rlb.TGL_KEBUTUHAN AND rlb.NO_REG = le.NO_REG
		WHERE le.NO_REG  = '{$no_reg}'
		ORDER BY le.TGL_KEBUTUHAN desc 
SQL;
		return $this->db->query($sql);
	}
}
