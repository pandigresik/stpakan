<?php
class M_pengembalian_sak extends CI_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
	//	$this->_table = 'lpb';
	}
	public function get_kandang($kode_farm,$nama_kandang){
			return $this->db
				->query("select rhk.no_reg
								,mk.nama_kandang
								,mk.kode_kandang
							from(
								select max(tgl_transaksi) lhk,left(no_reg,len(no_reg) - 10) kode_farm,right(no_reg,2) kode_kandang
								from rhk
								where tgl_transaksi <= getdate() and left(no_reg,len(no_reg) - 10) = '".$kode_farm."'
								group by left(no_reg,len(no_reg) - 10)
								,right(no_reg,2)
								)rhk_terakhir
								inner join rhk
									on rhk.tgl_transaksi = rhk_terakhir.lhk
									and left(rhk.no_reg,len(rhk.no_reg) - 10) = rhk_terakhir.kode_farm
									and right(rhk.no_reg,2) = rhk_terakhir.kode_kandang
								inner join m_kandang mk
								on mk.kode_farm = rhk_terakhir.kode_farm
									and mk.kode_kandang = rhk_terakhir.kode_kandang
									and mk.nama_kandang like '%".$nama_kandang."%'
								"
					);
	}
	/* Dapatkan jumlah sak pakan yang telah dipakai berdasarkan rhk_pakan
	 * grouping berdasarkan kode_barang dan jenis_kelamin
	 * @param no_reg
	 * */
	public function get_list_pakan_terpakai($no_reg){
		return $this->db
			->select('mb.nama_barang,rp.kode_barang,rp.jenis_kelamin,sum(abs(rp.jml_pakai)) jml_pakai')
			->where(array('no_reg'=>$no_reg))
			->join('m_barang mb','mb.kode_barang = rp.kode_barang')
			->group_by('rp.kode_barang,rp.jenis_kelamin,mb.nama_barang')
			->get('rhk_pakan rp');
	}

	/* Dapatkan jumlah sak pakan yang telah dikirim oleh gudang
	 * grouping berdasarkan kode_barang dan jenis_kelamin
	* @param no_reg
	* */
	public function get_list_pakan_dikirim($no_reg){
		return $this->db
		->select('kode_barang,jenis_kelamin, sum(JML_ORDER) jml_kirim')
		->where(array('keterangan1'=>'PENERIMAAN KANDANG','no_reg'=>$no_reg))
		->group_by('kode_barang,jenis_kelamin')
		->get('KANDANG_MOVEMENT_D');
	}

	/* Dapatkan jumlah sak pakan yang telah dikirim oleh gudang
	 * grouping berdasarkan kode_barang dan jenis_kelamin
	* @param no_reg
	* */
	public function get_list_pakan_akhir($no_reg){
		return $this->db
				->query("select msk.kode_barang, 'C' jenis_kelamin, msk.jml_in, isnull(klr.jml_out, 0) jml_out, isnull(rtn.jml_sak, 0) jml_sak, isnull(rhk.jml_pakai,0) jml_pakai 
						from (
							select kode_barang, sum(jml_order) jml_in from kandang_movement_d
							where no_reg = '".$no_reg."' and keterangan1 = 'penerimaan kandang' --and tgl_buat in(select max(tgl_buat) from kandang_movement_d where no_reg = 'bw/2017-2/12')
							group by kode_barang
						) msk
						left join (
							select kode_barang, sum(jml_order) jml_out from kandang_movement_d
							where no_reg = '".$no_reg."' and keterangan1 not in('penerimaan kandang','lhk')
							group by kode_barang
						) klr on msk.kode_barang = klr.kode_barang
						left join (
							select kode_pakan, sum(isnull(re.jml_sak, 0) ) jml_sak
							from retur_sak_kosong rh
							join retur_sak_kosong_item_pakan rd on rh.id = rd.retur_sak_kosong
							join retur_sak_kosong_item_timbang_pakan re on rd.id = re.retur_sak_kosong_item_pakan
							where no_reg = '".$no_reg."' 
							group by kode_pakan
						) rtn on msk.kode_barang = rtn.kode_pakan
left join (
							select KODE_BARANG, sum(isnull(JML_PAKAI,0)) jml_pakai
							from rhk_pakan
							where no_reg = '".$no_reg."' 
							group by KODE_BARANG
						) rhk on msk.KODE_BARANG = rhk.KODE_BARANG"
					);
	}

	/* Dapatkan jumlah sak yang telah dikembalikan
	 * @param no_reg
	 * */
	public function get_pengembalian_sak($no_reg){
		$sql = <<<SQL

		select rskip.KODE_PAKAN kode_barang
			,rskip.JENIS_KELAMIN jenis_kelamin
			,sum(rskitp.jml_sak) jml_kirim
			,sum(rskitp.brt_sak) brt_sak
		from retur_sak_kosong rsk
		inner join retur_sak_kosong_item_pakan rskip
			on rsk.id = rskip.RETUR_SAK_KOSONG
		inner join retur_sak_kosong_item_timbang_pakan rskitp
			on rskip.id = rskitp.retur_sak_kosong_item_pakan
		where rsk.NO_REG = '{$no_reg}'
		group by KODE_PAKAN,JENIS_KELAMIN
SQL;
	return $this->db->query($sql);
	}
	
	public function get_nama_kandang($noreg){
		$sql = <<<SQL
			select mk.NAMA_KANDANG NAMA from kandang_siklus ks 
inner join M_KANDANG mk on (ks.KODE_KANDANG = mk.KODE_KANDANG and ks.KODE_FARM = mk.KODE_FARM)
where ks.NO_REG = '{$noreg}'
SQL;

		$data = $this->db->query($sql);
		return $data->result();
	}

	public function view_pengembalian($noreg,$nourut){
		$sql = <<<SQL

		select rsk.NO_REG
				,mk.NAMA_KANDANG
				,rsk.TGL_BUAT
				,rskip.ID
				,rskip.KODE_PAKAN
				,rskip.JENIS_KELAMIN
				,rskip.JML_KIRIM
				,rskip.JML_PAKAI
				,rskip.HUTANG
				,rskitp.JML_SAK
				,cast(rskitp.BRT_SAK as int) BRT_SAK
				,rskitp.NO_URUT
				,mb.NAMA_BARANG
				,rskip.KETERANGAN
		from RETUR_SAK_KOSONG rsk
		inner join RETUR_SAK_KOSONG_ITEM_PAKAN rskip
			on rskip.RETUR_SAK_KOSONG = rsk.id
		inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskitp
			on rskitp.RETUR_SAK_KOSONG_ITEM_PAKAN = rskip.id
		inner join m_barang mb
			on mb.KODE_BARANG = rskip.KODE_PAKAN
		inner join KANDANG_SIKLUS ks
			on ks.NO_REG = rsk.NO_REG
		inner join M_KANDANG mk
			on mk.KODE_FARM = ks.KODE_FARM and mk.KODE_KANDANG = ks.KODE_KANDANG
		where rsk.NO_REG = '{$noreg}'
		and rsk.NO_URUT = '{$nourut}'
SQL;

		return $this->db->query($sql);
	}

	//public function list_pengembalian_sak($kodefarm,$tanggal_cari = NULL){
	public function list_pengembalian_sak($kodefarm){
		/*$where = '';
		if(!empty($tanggal_cari)){
			$where = 'where cast(rsk.tgl_buat as date) '.$tanggal_cari;
		}
		$sql = <<<SQL
		select rsk.NO_REG+'-'+rsk.NO_URUT NO_PENGEMBALIAN
			,mk.NAMA_KANDANG
			,mk.NO_FLOK
			,rsk.TGL_BUAT
			,mb.NAMA_BARANG NAMA_PAKAN
			,sum(rskip.JML_KIRIM) JML_KIRIM
			,sum(rskip.JML_PAKAI) JML_PAKAI
			,sum(rskip.JML_PAKAI - rskip.HUTANG) AKTUAL
			,sum(rskitp.JML_SAK) SAK_KEMBALI
			,sum(rskip.HUTANG) HUTANG
			, stuff (
				(select distinct ','+ case
					when  hutang > 0 then 'OUTSTANDING'
						else 'OK'
					end
				from RETUR_SAK_KOSONG_ITEM_PAKAN
				where RETUR_SAK_KOSONG = rsk.id
				for xml path (''))
				,1,1,'') STATUS

		from RETUR_SAK_KOSONG rsk
		inner join RETUR_SAK_KOSONG_ITEM_PAKAN rskip
			on rsk.id = rskip.RETUR_SAK_KOSONG
		inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskitp
			on rskitp.RETUR_SAK_KOSONG_ITEM_PAKAN = rskip.ID
		inner join KANDANG_SIKLUS ks
			on ks.NO_REG = rsk.NO_REG and ks.kode_farm = '{$kodefarm}'
		inner join M_KANDANG mk
			on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM
		inner join M_BARANG mb
			on rskip.KODE_PAKAN = mb.KODE_BARANG
		{$where}
		group by rsk.NO_REG
			,rsk.id
			,rsk.NO_URUT
			,mk.NAMA_KANDANG
			,mk.NO_FLOK
			,mb.NAMA_BARANG
			,rsk.TGL_BUAT
		order by rsk.tgl_buat desc
SQL;*/
		
		$sql = <<<SQL
			select rsk.NO_REG+'-'+rsk.NO_URUT NO_PENGEMBALIAN
				,mk.NAMA_KANDANG
				,mk.NO_FLOK
				,rsk.TGL_BUAT
				,mb.NAMA_BARANG NAMA_PAKAN
				,sum(rskip.JML_KIRIM) JML_KIRIM
				,sum(rskip.JML_PAKAI) JML_PAKAI
				,sum(rskip.JML_PAKAI - rskip.HUTANG) AKTUAL
				,sum(rskitp.JML_SAK) SAK_KEMBALI
				,sum(rskip.HUTANG) HUTANG
				, stuff (
					(select distinct ','+ case
						when  hutang > 0 then 'OUTSTANDING'
							else 'OK'
						end
					from RETUR_SAK_KOSONG_ITEM_PAKAN
					where RETUR_SAK_KOSONG = rsk.id
					for xml path (''))
					,1,1,'') STATUS

			from RETUR_SAK_KOSONG rsk
			inner join RETUR_SAK_KOSONG_ITEM_PAKAN rskip
				on rsk.id = rskip.RETUR_SAK_KOSONG
			inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskitp
				on rskitp.RETUR_SAK_KOSONG_ITEM_PAKAN = rskip.ID
			inner join KANDANG_SIKLUS ks
				on ks.NO_REG = rsk.NO_REG and ks.kode_farm = '{$kodefarm}'
			inner join M_KANDANG mk
				on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM
			inner join M_BARANG mb
				on rskip.KODE_PAKAN = mb.KODE_BARANG
			where ks.status_siklus = 'O'
			group by rsk.NO_REG
				,rsk.id
				,rsk.NO_URUT
				,mk.NAMA_KANDANG
				,mk.NO_FLOK
				,mb.NAMA_BARANG
				,rsk.TGL_BUAT
			order by rsk.tgl_buat DESC, rsk.no_urut ASC
SQL;
		return $this->db->query($sql);
	}

	public function cek_input_lhk($noreg){
		return $this->db
				->where('tgl_transaksi = cast(getdate() - 1 as date)')
				->where(array('no_reg'=> $noreg))
				->where('ack_kf is not null')
				->get('rhk');
	}

	public function cek_max_pengembalian($noreg){
		$sql = <<<SQL
		select case
			when cast(convert(varchar(10),max(tgl_transaksi)) + ' 09:00' as datetime) + 1 > getdate() then 1
			else 0
			end status
		from rhk
		where no_reg = '{$noreg}'
SQL;
		return $this->db->query($sql);

	}

	public function view_retur_sak($kode_farm,$kode_siklus,$status,$custom_param = null){
		$where = '';
		if($status){
			$where = <<<W
			inner join (
			select ks.kode_farm,rsk.no_reg,max(no_urut) no_urut from RETUR_SAK_KOSONG rsk
			inner join KANDANG_SIKLUS ks
				on ks.NO_REG = rsk.NO_REG and ks.STATUS_SIKLUS = 'O'
			group by rsk.no_reg,ks.kode_farm
		)rr on rr.no_reg = rsk.NO_REG and rr.no_urut = rsk.NO_URUT
		where rhrs.keputusan is null
W;

		}
		else{
			$where = <<<W
			where cast(rsk.tgl_buat as date) {$custom_param}
W;

		}


		$sql = <<<SQL
		select rsk.NO_REG + '-'+ rsk.NO_URUT no_retur
				,rsk.no_reg
				,rsk.id
				,rsk.tgl_rhk
				,mk.NAMA_KANDANG nama_kandang
				,rsk.TGL_BUAT tgl_buat
				,sum(rskip.JML_KIRIM) jml_kirim
				,sum(rskip.JML_PAKAI) jml_pakai
				,sum(rskip.HUTANG) hutang
				,sum(rskitp.JML_SAK) jml_retur
				,max(rskip.tgl_review_kadep) tgl_review_kadept
				,rhrs.keputusan
				,rhrs.waktu
				,case
					when retur_terakhir.tgl_buat = rsk.TGL_BUAT then 1
				else 0 end aktif
		from review_hutang_retur_sak rhrs
		inner join RETUR_SAK_KOSONG rsk
			on rhrs.RETUR_SAK_KOSONG = rsk.ID
		inner join RETUR_SAK_KOSONG_ITEM_PAKAN rskip
			on rskip.RETUR_SAK_KOSONG = rsk.id
		inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskitp
			on rskitp.RETUR_SAK_KOSONG_ITEM_PAKAN = rskip.id
		inner join KANDANG_SIKLUS ks
			on ks.NO_REG = rsk.NO_REG and ks.kode_farm = '{$kode_farm}' and ks.kode_siklus = '{$kode_siklus}'
		inner join M_KANDANG mk
			on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM
		left join (
			select  max(rsk.tgl_buat) tgl_buat,no_reg from RETUR_SAK_KOSONG rsk group by rsk.NO_REG
		)retur_terakhir on retur_terakhir.NO_REG = rsk.NO_REG
		{$where}
		group by rsk.NO_REG
				,rsk.id
				,rsk.tgl_rhk
				,rsk.NO_URUT
				,mk.NAMA_KANDANG
				,rsk.TGL_BUAT
				,rhrs.KEPUTUSAN
				,rhrs.WAKTU
				,retur_terakhir.tgl_buat
		order by rsk.TGL_BUAT desc
SQL;

		return $this->db->query($sql);
	}
	/* cari total jumlah retur per farm berdasarkan status
	 * jika status = 1, maka cari retur yang terakhir saja dan keputusan = NULL
	 * */
	public function get_list_retur_approval($status,$tanggal_cari){
		if(!$status){
			$where = null;
			if(!empty($tanggal_cari)){
				$where = 'and cast(rsk.tgl_buat as date) '.$tanggal_cari;
			}
			$sql = <<<SQL
		select ks.kode_farm,count(*) jml_retur
		from REVIEW_HUTANG_RETUR_SAK rhrs
		inner join RETUR_SAK_KOSONG rsk
			on rsk.id = rhrs.RETUR_SAK_KOSONG {$where}
		inner join KANDANG_SIKLUS ks
			on ks.NO_REG = rhrs.NO_REG and ks.STATUS_SIKLUS = 'O'
		group by ks.KODE_FARM
SQL;
		}
		else{
			$sql = <<<SQL
		select rr.kode_farm
			,count(rhrs.NO_REG) jml_retur
		from RETUR_SAK_KOSONG rsk
		inner join (
			select ks.kode_farm,rsk.no_reg,max(no_urut) no_urut from RETUR_SAK_KOSONG rsk
			inner join KANDANG_SIKLUS ks
				on ks.NO_REG = rsk.NO_REG and ks.STATUS_SIKLUS = 'O'
			group by rsk.no_reg,ks.kode_farm
		)rr on rr.no_reg = rsk.NO_REG and rr.no_urut = rsk.NO_URUT
		inner join REVIEW_HUTANG_RETUR_SAK rhrs
			on rhrs.RETUR_SAK_KOSONG = rsk.id  and rhrs.KEPUTUSAN is null
		group by rr.KODE_FARM

SQL;
		}

		return $this->db->query($sql);

	}

	public function get_sisa_hutang($kodefarm, $flok = NULL){
		$whereFlok = !empty($flok) ? ' and ks.FLOK_BDY = \''.$flok.'\'' : '';
		$sql = <<<SQL
		select rhk.NO_REG no_reg
		--	,rhk.KODE_BARANG kode_barang
			,rhk.pakai - coalesce(retur.jml_retur,0) hutang_retur
		from (
			select rp.no_reg
			--,rp.kode_barang
			,sum(abs(rp.jml_pakai)) pakai
			from rhk_pakan rp
			inner join KANDANG_SIKLUS ks
				on ks.NO_REG = rp.NO_REG and ks.STATUS_SIKLUS = 'O' and ks.KODE_FARM = '{$kodefarm}' {$whereFlok}
			group by rp.no_reg -- ,rp.kode_barang
		)rhk
		left join
		(
			select rsk.NO_REG
				--	,rski.KODE_PAKAN
					,sum(rskitp.JML_SAK) jml_retur
			from RETUR_SAK_KOSONG rsk
			inner join KANDANG_SIKLUS ks
				on ks.NO_REG = rsk.NO_REG and ks.STATUS_SIKLUS = 'O' and ks.KODE_FARM = '{$kodefarm}' {$whereFlok}
			inner join RETUR_SAK_KOSONG_ITEM_PAKAN rski
				on rsk.id = rski.RETUR_SAK_KOSONG
			inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskitp
				on rskitp.RETUR_SAK_KOSONG_ITEM_PAKAN = rski.id

			group by rsk.NO_REG --,rski.KODE_PAKAN
		)retur
			on rhk.NO_REG = retur.NO_REG -- and rhk.KODE_BARANG = retur.KODE_PAKAN

SQL;

		return $this->db->query($sql);
	}

	public function get_sisa_hutang_noreg($noreg){		
		$sql = <<<SQL
		select rhk.NO_REG no_reg
		--	,rhk.KODE_BARANG kode_barang
			,rhk.pakai - coalesce(retur.jml_retur,0) hutang_retur
		from (
			select rp.no_reg
			--,rp.kode_barang
			,sum(abs(rp.jml_pakai)) pakai
			from rhk_pakan rp
			where rp.no_reg = '{$noreg}'
			group by rp.no_reg -- ,rp.kode_barang
		)rhk
		left join
		(
			select rsk.NO_REG
				--	,rski.KODE_PAKAN
					,sum(rskitp.JML_SAK) jml_retur
			from RETUR_SAK_KOSONG rsk			
			inner join RETUR_SAK_KOSONG_ITEM_PAKAN rski
				on rsk.id = rski.RETUR_SAK_KOSONG
			inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskitp
				on rskitp.RETUR_SAK_KOSONG_ITEM_PAKAN = rski.id
			where rsk.no_reg = '{$noreg}'
			group by rsk.NO_REG --,rski.KODE_PAKAN
		)retur
			on rhk.NO_REG = retur.NO_REG -- and rhk.KODE_BARANG = retur.KODE_PAKAN

SQL;

		return $this->db->query($sql);
	}

	public function check_pengembalian_hari_ini($kode_farm,$flok){
		$sql = <<<SQL
		select distinct ks.no_reg ksnoreg,rsk.no_reg,rsk.tgl_buat
		from KANDANG_SIKLUS ks
		left join RETUR_SAK_KOSONG rsk
			on ks.NO_REG = rsk.NO_REG and cast(rsk.tgl_buat as date) = cast(getdate() as date)
		where ks.STATUS_SIKLUS = 'O' and ks.KODE_FARM = '{$kode_farm}' and ks.FLOK_BDY = '{$flok}'
		and dateadd(day,2,TGL_DOC_IN) <= cast(getdate() as date) -- rhk pertama kali adalah h+2 dari docin
SQL;
		return $this->db->query($sql);
	}

	public function check_pengembalian_noreg_hari_ini($noreg){
		$sql = <<<SQL
		select distinct ks.no_reg ksnoreg,rsk.no_reg,rsk.tgl_buat
		from KANDANG_SIKLUS ks
		left join RETUR_SAK_KOSONG rsk
			on ks.NO_REG = rsk.NO_REG and cast(rsk.tgl_buat as date) = cast(getdate() as date)
		where ks.no_reg = '{$noreg}'
		and dateadd(day,2,TGL_DOC_IN) <= cast(getdate() as date) -- rhk pertama kali adalah h+2 dari docin
SQL;
		return $this->db->query($sql);
	}

	public function getKandangByRFID($rfid){
		$sql = <<<SQL
		SELECT ks.no_reg,ks.kode_kandang,ks.flok_bdy,mk.nama_kandang,mpp.pengawas FROM M_KANDANG mk
		JOIN KANDANG_SIKLUS ks ON ks.KODE_FARM = mk.KODE_FARM AND ks.KODE_KANDANG = mk.KODE_KANDANG AND ks.STATUS_SIKLUS = 'O'
		JOIN M_PLOTING_PELAKSANA mpp ON ks.NO_REG = mpp.NO_REG
		WHERE mk.KODE_VERIFIKASI = '{$rfid}'
SQL;
		return $this->db->query($sql);
	}
}
