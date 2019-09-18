<?php
class M_permintaan_pakan extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function list_kebutuhan_pakan($params = array(),$hitung_ulang){
		$startDate = $params['kebutuhan_awal'];
		$endDate = $params['kebutuhan_akhir'];
		$kode_farm = $params['kode_farm'];
	
		$sql = <<<SQL
			exec dbo.PERMINTAAN_PAKAN_PP :kode_farm,:start_date,:end_date
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam(':kode_farm',$kode_farm);
		$stmt->bindParam(':start_date',$startDate);
		$stmt->bindParam(':end_date',$endDate);
	
		$stmt->execute();
	//	print_r($stmt->errorInfo());
		return $stmt->fetchAll(2);
	}
	/* kebutuhan pakan yang sudah diapprove */
	public function list_kebutuhan_pakan_approve($params = array()){
		$startDate = $params['kebutuhan_awal'];
		$endDate = $params['kebutuhan_akhir'];
		$no_lpb = empty($params['no_lpb']) ? NULL : $params['no_lpb'];
		$kode_farm = $params['kode_farm'];
		$sql = <<<SQL
		exec dbo.PERMINTAAN_PAKAN_DIREKTUR :kode_farm,:start_date,:end_date,:no_lpb
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam(':kode_farm',$kode_farm);
		$stmt->bindParam(':start_date',$startDate);
		$stmt->bindParam(':end_date',$endDate);
		$stmt->bindParam(':no_lpb',$no_lpb);

		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function list_kebutuhan_pakan_bdy_approve($no_lpb){

		$sql = <<<SQL
		exec dbo.pp_bdy_approve :no_lpb
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam(':no_lpb',$no_lpb);

		$stmt->execute();
		return $stmt->fetchAll(2);
	}
	public function list_kebutuhan_pakan_bdy($farm,$no_reg,$keb_awal,$keb_akhir,$tgl_awal_ganti = NULL){						
		$sql = <<<SQL
		exec dbo.pp_bdy_v2 :farm,:no_reg,:keb_awal,:keb_akhir,:tgl_awal_ganti
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam(':farm',$farm);
		$stmt->bindParam(':no_reg',$no_reg);
		$stmt->bindParam(':keb_awal',$keb_awal);
		$stmt->bindParam(':keb_akhir',$keb_akhir);
		$stmt->bindParam(':tgl_awal_ganti',$tgl_awal_ganti);
		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function list_kebutuhan_pakan_tambahan_bdy($noreg,$keb_awal,$keb_akhir,$kodepj){
		$sql = <<<SQL
		exec dbo.pp_bdy_pakan_tambahan_v2 :no_reg,:keb_awal,:keb_akhir,:kodepj
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam(':no_reg',$noreg);		
		$stmt->bindParam(':keb_awal',$keb_awal);
		$stmt->bindParam(':keb_akhir',$keb_akhir);
		$stmt->bindParam(':kodepj',$kodepj);
		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function list_pp($no_pp){
		$sql = <<<SQL
		select ld.NO_LPB
					,ld.TGL_KIRIM
					,ld.TGL_KEB_AWAL
					,ld.TGL_KEB_AKHIR
					,ld.KETERANGAN
					,sum(le.JML_ORDER) TOTAL_PP
					, rv.rekomendasi_pp REKOMENDASI_PP
					, rv.persetujuan_pp PERSETUJUAN_PP
			from lpb_d ld
			inner join lpb_e le
				on le.NO_LPB = ld.NO_LPB and le.TGL_KIRIM = ld.TGL_KIRIM
			inner join (
				select no_lpb
						,sum(coalesce(jml_rekomendasi,jml_optimasi)) rekomendasi_pp
						,sum(coalesce(jml_review,jml_optimasi)) persetujuan_pp
				 from review_lpb_budidaya where no_lpb = '{$no_pp}'
				group by no_lpb
			)rv on rv.no_lpb = ld.no_lpb
			where ld.NO_LPB = '{$no_pp}'
			group by ld.no_lpb
					,ld.TGL_KIRIM
					,ld.TGL_KEB_AWAL
					,ld.TGL_KEB_AKHIR
					,ld.KETERANGAN
					, rv.rekomendasi_pp
					, rv.persetujuan_pp
SQL;
		return $this->db->query($sql);
	}

	public function approve_presdir($no_lpb,$kode_farm,$user){
		$sql = <<<QUERY
			EXEC APPROVE_PP_PRESDIR '$no_lpb','$kode_farm','$user'
QUERY;
		return $this->db->query($sql);
	}

	public function approve_pp_budidaya($no_lpb,$grup_farm,$user){
		$sql = <<<QUERY
			EXEC APPROVE_PP_BUDIDAYA '$no_lpb','$grup_farm','$user'
QUERY;
		return $this->db->query($sql);
	}

	public function op_belum_ada_do(){
		$sql = <<<SQL
		select distinct op.NO_OP no_op
			,op.NO_LPB no_pp
			,op.TGL_OP tgl_op
			,op_d.TGL_KIRIM tgl_kirim
			,mf.NAMA_FARM nama_farm
			,mf.kode_farm
		from op
		inner join op_d
			on op.NO_OP = op_d.NO_OP
		inner join m_farm mf
			on mf.KODE_FARM = op.KODE_FARM
		left join do
			on op.NO_OP = do.NO_OP and do.TGL_KIRIM = op_d.TGL_KIRIM
		where do.NO_OP is null
SQL;
		return $this->db->query($sql);
	}

	public function detail_lpb($kodefarm){
		$sql =<<<SQL
		select l.NO_LPB no_lpb
			,ld.TGL_KIRIM tgl_kirim
			,ld.TGL_KEB_AWAL tgl_keb_awal
			,ld.TGL_KEB_AKHIR tgl_keb_akhir
		from lpb l
		inner join lpb_d ld
			on l.NO_LPB = ld.NO_LPB
		where l.KODE_SIKLUS =  (
	 		select top 1 KODE_SIKLUS from M_PERIODE where KODE_FARM = '{$kodefarm}' and STATUS_PERIODE = 'A' order by kode_siklus
		)
	 	and l.status_lpb != 'V'
SQL;

		return $this->db->query($sql);

	}

	public function docin_rhk_perkandang($kodefarm){
		$sql = <<<SQL
		select mk.NAMA_KANDANG nama
			, ks.TGL_DOC_IN doc_in
			, ks.NO_REG no_reg
			, r.tgl_rhk rhk
			, r.tgl_buat entry
		from KANDANG_SIKLUS ks
		inner join M_KANDANG mk
			on mk.KODE_KANDANG = ks.KODE_KANDANG  and mk.KODE_FARM = '{$kodefarm}'
		left join(
			select no_reg, max(TGL_TRANSAKSI) tgl_rhk,max(tgl_buat) tgl_buat from rhk
			group by no_reg
		)r on r.NO_REG = ks.NO_REG
		where ks.KODE_SIKLUS = (
	 		select top 1 KODE_SIKLUS from M_PERIODE where KODE_FARM = '{$kodefarm}' and STATUS_PERIODE = 'A' order by kode_siklus
		)
	 	and ks.status_siklus = 'O'

SQL;


		return $this->db->query($sql);
	}

	public function cek_input_lhk($kodefarm,$kodeflok,$all = FALSE){
	 $paramflok = empty($kodeflok) ? '' : ' and no_flok = '.$kodeflok;
	 $whereAll = !$all ? ' where l.sudahentrylhk = 0' : '';
	 $sql = <<<SQL
		select * from (
				select mk.NAMA_KANDANG nama
				, ks.TGL_DOC_IN doc_in
				, ks.NO_REG no_reg
				, r.tgl_rhk rhk
				, r.tgl_buat entry
				, rhk.ack_kf
				, case
				when r.tgl_rhk = cast(getdate() - 1 as date) then 1
				else 0
				end sudahentrylhk
				,cast(getdate() - 1 as date) lhkharusinput
				from KANDANG_SIKLUS ks
				inner join M_KANDANG mk
				on mk.KODE_KANDANG = ks.KODE_KANDANG  and mk.KODE_FARM = '{$kodefarm}' {$paramflok}
				left join(
						select no_reg, max(TGL_TRANSAKSI) tgl_rhk,max(tgl_buat) tgl_buat from rhk
						where no_reg in ( select no_reg from KANDANG_SIKLUS where KODE_FARM = '{$kodefarm}'
							and status_siklus = 'O'
								and TGL_TRANSAKSI <= cast(getdate() - 1 as date)
						)
						group by no_reg
				)r on r.NO_REG = ks.NO_REG
				left join rhk on rhk.NO_REG = r.NO_REG and rhk.TGL_TRANSAKSI = r.tgl_rhk
				where	ks.status_siklus = 'O'
				and ks.kode_farm = '{$kodefarm}'
				and ks.TGL_DOC_IN <= cast(getdate() - 2  as date)
		)l
		{$whereAll}
SQL;

	 return $this->db->query($sql);
	}

	public function cek_input_lhk_noreg($noreg,$all = FALSE){		
		$whereAll = !$all ? ' where l.sudahentrylhk = 0' : '';
		$sql = <<<SQL
		   select * from (
				   select mk.NAMA_KANDANG nama
				   , ks.TGL_DOC_IN doc_in
				   , ks.NO_REG no_reg
				   , r.tgl_rhk rhk
				   , r.tgl_buat entry
				   , rhk.ack_kf
				   , case
				   when r.tgl_rhk = cast(getdate() - 1 as date) then 1
				   else 0
				   end sudahentrylhk
				   ,cast(getdate() - 1 as date) lhkharusinput
				   from KANDANG_SIKLUS ks
				   inner join M_KANDANG mk
				   on mk.KODE_KANDANG = ks.KODE_KANDANG  and mk.KODE_FARM = ks.kode_farm
				   left join(
						   select no_reg, max(TGL_TRANSAKSI) tgl_rhk,max(tgl_buat) tgl_buat from rhk
						   where no_reg = '{$noreg}' and TGL_TRANSAKSI <= cast(getdate() - 1 as date)
						   group by no_reg
				   )r on r.NO_REG = ks.NO_REG
				   left join rhk on rhk.NO_REG = r.NO_REG and rhk.TGL_TRANSAKSI = r.tgl_rhk
				   where ks.no_reg = '{$noreg}'				   
				   and ks.TGL_DOC_IN <= cast(getdate() - 2  as date)
		   )l
		   {$whereAll}
SQL;

		return $this->db->query($sql);
	   }

	public function no_op_terakhir($kodefarm){
		$sql = <<<SQL
		select top 1 NO_OP_Pakai no_op_pakai from m_op
				where GRUP_FARM = (
				select grup_farm from m_farm where kode_farm = '{$kodefarm}'
				)
				and kode_farm = '{$kodefarm}'
				and TAHUN = year(current_timestamp)
				and cast(NO_OP_PAKAI as int) <= cast(NO_OP_AKHIR as int)
			order by TGL_KIRIM asc
SQL;

		return $this->db->query($sql);
	}
	/* op dibuat perkandang jadi pengecekannya adalah op_pakai + jml_kandang <= op_akhir */
	public function no_op_terakhir_bdy($kodefarm,$no_pp){
		$sql = <<<SQL
		select top 1 NO_OP_Pakai no_op_pakai from m_op
				where GRUP_FARM = (
				select grup_farm from m_farm where kode_farm = '{$kodefarm}'
				)
				and kode_farm = '{$kodefarm}'
				and TAHUN = year(current_timestamp)
				and (cast(NO_OP_PAKAI as int) + (select count(distinct no_reg) from lpb_e where no_lpb = '{$no_pp}')) <= cast(NO_OP_AKHIR as int)
			order by TGL_KIRIM asc

SQL;

		return $this->db->query($sql);
	}

	public function sisa_pakan($kodefarm){
		$sql = <<<SQL
		select sum(JML_AVAILABLE) sisa_pakan
			, JENIS_KELAMIN jenis_kelamin
			, KODE_BARANG kode_barang
			, KETERANGAN1 no_reg
		from MOVEMENT
		where keterangan1 in (
			select distinct no_reg from KANDANG_SIKLUS
			where KODE_FARM = '{$kodefarm}' and STATUS_SIKLUS = 'O'
		)
		group by JENIS_KELAMIN
			, KODE_BARANG
			, KETERANGAN1
SQL;
		return $this->db->query($sql);
	}

	public function get_pakan_ganti($no_reg,$jenis_kelamin,$umur){
		$sql = <<<SQL
		select msb.KODE_BARANG kode_barang
				,mb.NAMA_BARANG nama_barang
				,STD_UMUR umur
				,msb.BENTUK bentuk
		from M_STD_BREEDING msb
		inner join M_BARANG mb
			on mb.KODE_BARANG = msb.KODE_BARANG
		where KODE_STD_BREEDING = (
			select KODE_STD_BREEDING_{$jenis_kelamin} from KANDANG_SIKLUS
			where NO_REG = '{$no_reg}'
			)
			and std_umur in ({$umur})
SQL;

		return $this->db->query($sql);
	}

	public function kertas_kerja($no_reg,$tgl_keb_awal = NULL,$tgl_keb_akhir = NULL){
		$sql = <<<SQL
				exec dbo.kertas_kerja :no_reg,:keb_awal,:keb_akhir
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam(':no_reg',$no_reg);
		$stmt->bindParam(':keb_awal',$tgl_keb_awal);
		$stmt->bindParam(':keb_akhir',$tgl_keb_akhir);
		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function kertas_kerja_bdy($no_reg,$tgldocin){
		$sql = <<<SQL
				exec dbo.kertas_kerja_bdy :no_reg,:tgldocin
SQL;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam(':no_reg',$no_reg);
		$stmt->bindParam(':tgldocin',$tgldocin);

		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function riwayat_pp($no_pp,$no_reg,$jk){
		$sql = <<<SQL
		select l.NO_LPB no_lpb
			,stuff(convert(varchar(19),l.TGL_RILIS, 126),11,1,' ') tgl_rilis
			,mp.NAMA_PEGAWAI nama_pegawai
			,ld.TGL_KEB_AWAL tgl_keb_awal
			,ld.TGL_KEB_AKHIR tgl_keb_akhir
			,sum(le.jml_order) kuantitas_pp
			,case
				when l.STATUS_LPB = 'A' then 'FINAL'
				else 'BATAL'
			  end status
			,l.status_lpb
			,ks.flok_bdy flok
			from lpb l
			inner join lpb_d ld
				on l.NO_LPB = ld.NO_LPB
			inner join lpb_e le
				on le.NO_LPB = l.NO_LPB and le.no_reg = '{$no_reg}' and le.jenis_kelamin = '{$jk}'
			inner join kandang_siklus ks
				on ks.no_reg = le.no_reg
			inner join M_PEGAWAI mp
				on mp.KODE_PEGAWAI = l.USER_BUAT
			where ld.tgl_keb_awal = (select tgl_keb_awal from lpb_d where no_lpb = '{$no_pp}')	
			group by l.NO_LPB
					,l.TGL_RILIS
					,mp.NAMA_PEGAWAI
					,ld.TGL_KEB_AWAL
					,ld.TGL_KEB_AKHIR
					,l.STATUS_LPB
					,ks.flok_bdy
SQL;
		return $this->db->query($sql);
	}

	public function get_hari_libur($minDate){
		if(empty($minDate)){
			$sql = <<<SQL
		select tanggal from M_KALENDER where tanggal >= current_timestamp - 20
SQL;
		}
		else{
			$sql = <<<SQL
		select tanggal from M_KALENDER where tanggal >= '{$minDate}'
SQL;
		}

		return $this->db->query($sql);
	}
	function sisa_konsumsi_pakan($kode_farm,$no_lpb,$no_reg = NULL){
		if(empty($no_lpb)){
			$no_lpb = NULL;
		}
		$sql = <<<SQL
			exec get_sisa_pakan_bdy_v2 :kode_farm,:no_lpb,:no_reg
SQL;

		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam(':kode_farm',$kode_farm);
		$stmt->bindParam(':no_lpb',$no_lpb);
		$stmt->bindParam(':no_reg',$no_reg);
		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	function butuh_approval(){
		$sql =<<<SQL
		-- cari forecast yang butuh approval
		select 'Forecast' header
			,ks.NO_REG no
			,ks.tgl_doc_in tgl_buat
			,mf.NAMA_FARM nama_farm
			,ks.kode_farm kode_farm
		from KANDANG_SIKLUS ks
		inner join M_FARM mf
			on mf.KODE_FARM = ks.KODE_FARM
		where TGL_APPROVE1 is null and STATUS_SIKLUS = 'O' and tgl_rilis is not null
		union
		-- cari PP yang butuh approval
		select  'Permintaan' header
			, l.NO_LPB
			,l.TGL_BUAT
			,mf.NAMA_FARM
			,l.kode_farm kode_farm
		from lpb l
		inner join M_FARM mf
			on mf.KODE_FARM = l.KODE_FARM
		where l.STATUS_LPB = 'N'
SQL;

		return $this->db->query($sql);

	}

	function ack_admin_breeding(){
		$sql =<<<SQL
		-- cari forecast yang butuh approval
		select 'Forecast' header
			,ks.NO_REG no
			,ks.tgl_doc_in tgl_buat
			,mf.NAMA_FARM nama_farm
			,ks.kode_farm kode_farm
		from KANDANG_SIKLUS ks
		inner join M_FARM mf
			on mf.KODE_FARM = ks.KODE_FARM
		where STATUS_SIKLUS = 'O' -- and USER_APPROVE1 is null
		union
		-- cari PP yang butuh approval
		select  'Order Pembelian' header
			, op.NO_op
			,op.TGL_BUAT
			,mf.NAMA_FARM
			,op.kode_farm kode_farm
		from op
		inner join M_FARM mf
			on mf.KODE_FARM = op.KODE_FARM
		inner join lpb l
			on l.NO_LPB = op.NO_LPB
		inner join lpb_e le
			on l.NO_LPB = le.NO_LPB
		inner join KANDANG_SIKLUS ks
			on ks.NO_REG = le.NO_REG and ks.STATUS_SIKLUS = 'O'
		group by 	op.NO_op
			,op.TGL_BUAT
			,mf.NAMA_FARM
			,op.kode_farm
		order by tgl_buat

SQL;
		return $this->db->query($sql);
	}

	function comparepp($pp){
		$sql =<<<SQL
		select l.no_lpb
			, l.STATUS_LPB
			, ld.TGL_KIRIM
			, ld.TGL_KEB_AWAL
			, ld.TGL_KEB_AKHIR
			, le.KODE_BARANG
			, mb.NAMA_BARANG
			, sum(le.jml_order) jml_pp
			, rv.optimasi_pp
			, rv.rekomendasi_pp
			, rv.persetujuan_pp
		from lpb l
		inner join lpb_d ld
			on l.NO_LPB = ld.NO_LPB
		inner join lpb_e le
			on le.NO_LPB = ld.NO_LPB  and ld.TGL_KIRIM = le.TGL_KIRIM
		inner join m_barang mb
			on mb.KODE_BARANG = le.KODE_BARANG
		inner join (
				select no_lpb
						,kode_barang
						,sum(jml_optimasi) optimasi_pp
						,sum(coalesce(jml_rekomendasi,jml_optimasi)) rekomendasi_pp
						,sum(coalesce(jml_review,jml_optimasi)) persetujuan_pp
				 from review_lpb_budidaya where no_lpb in ('{$pp}')
				group by no_lpb,kode_barang
		)rv on rv.no_lpb = l.no_lpb and rv.kode_barang = le.kode_barang
		where l.no_lpb in ('{$pp}')
		group by l.no_lpb
		, l.STATUS_LPB
		, ld.TGL_KIRIM
		, ld.TGL_KEB_AWAL
		, ld.TGL_KEB_AKHIR
		, le.KODE_BARANG
		, mb.NAMA_BARANG
		, rv.optimasi_pp
		, rv.rekomendasi_pp
		, rv.persetujuan_pp
SQL;

	return $this->db->query($sql);
	}

	function monitoring_pp($periode_tahun,$kode_farm,$tanggal = NULL){

		$where = '';
		if(!empty($tanggal['operand'])){
			switch($tanggal['operand']){
				case 'between':
					$tanggal_param = $tanggal['operand'].' \''.$tanggal['startDate'].'\' and \''.$tanggal['endDate'].'\'';
					break;
				case '<=':
					$tanggal_param = $tanggal['operand'].' \''.$tanggal['endDate'].'\'';
					break;
				case '>=':
					$tanggal_param = $tanggal['operand'].' \''.$tanggal['startDate'].'\'';
					break;
			}
			$field = $tanggal['fieldname'];

			switch($field){
				case 'tgl_buat' :
					$where = ' where cast(xx.tgl_buat as date) '.$tanggal_param;
					break;
				case 'tgl_verifikasi' :
					$where = ' where  cast(xx.tgl_verifikasi as date) '.$tanggal_param;
					break;
				case 'tgl_sj' :
					$where = ' where cast(xx.tgl_sj as date) '.$tanggal_param;
					break;
				case 'tgl_terima' :
					$where = ' where cast(xx.tgl_terima as date) '.$tanggal_param;
					break;
				default:
			}
		}
		$sql = <<<SQL
	select * from(
		select pp.NO_LPB no_lpb
				,pp.REF_ID ref_id
				,pp.TGL_BUAT tgl_buat
				,pp.TGL_RILIS  tgl_rilis
				,pp.TGL_APPROVE1 tgl_op
				,ops.NO_OP no_op
				,ops.NO_OP_LOGISTIK no_op_logistik
				,pp.jml_order kuantitas_pp
				,cast(ops.kuantitas_op as int) kuantitas_op
				,dos.NO_DO no_do
				,dos.TGL_KIRIM tgl_kirim
		--		,dos.tgl_verifikasi
				,dos.nama_ekspedisi
				,dos.total_do kuantitas_do
				,kt.KODE_SURAT_JALAN surat_jalan
				,kt.NO_SPM spm
				,kt.TGL_TERIMA tgl_terima
				,kt.total_terima
				,kt.berat_terima
				,kt.NO_BA berita_acara
				,kt.tgl_sj
				,kt.tgl_vdo tgl_verifikasi
				,kt.sj_kg
				,kt.sj_sak
		from (
			select l.NO_LPB
				,l.REF_ID
				,l.TGL_BUAT
				,l.TGL_RILIS
				,l.TGL_APPROVE1
				,sum(le.jml_order) jml_order
			from lpb l
			inner join LPB_E le
				on le.NO_LPB = l.NO_LPB
			inner join KANDANG_SIKLUS ks
				on ks.NO_REG = le.NO_REG and year(ks.TGL_DOC_IN) = '{$periode_tahun}'
			where l.STATUS_LPB != 'V'  and l.KODE_FARM = '{$kode_farm}'

			group by l.NO_LPB
				,l.REF_ID
				,l.TGL_BUAT
				,l.TGL_RILIS
				,l.TGL_APPROVE1
		)pp
		left join
			(
				select op.NO_LPB
					,op.NO_OP
					,op.NO_OP_LOGISTIK
					,sum(op_d.JML_ORDER) kuantitas_op
				from op
				inner join op_d
					on op.NO_OP = op_d.NO_OP
				where op.KODE_FARM  = '{$kode_farm}'
				group by op.NO_LPB
					,op.NO_OP
					,op.NO_OP_LOGISTIK
			)
		ops
			on ops.NO_LPB = pp.NO_LPB
		left join (
			select do.NO_DO
				,do.NO_OP
				,do.TGL_KIRIM
				,do.tgl_verifikasi
				,me.nama_ekspedisi
				,sum(opv.JML_KIRIM) total_do
			from do
			inner join OP_VEHICLE opv
				on opv.NO_OP = do.NO_OP and opv.NO_URUT = do.NO_URUT
			inner join M_EKSPEDISI me
				on me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
			where do.STATUS_DO != 'D'  and do.KODE_FARM = '{$kode_farm}'

			group by do.NO_DO
				,do.NO_OP
				,do.TGL_KIRIM
				,me.nama_ekspedisi
				,do.tgl_verifikasi
		)dos on dos.NO_OP = ops.NO_OP
		left join (
			select p.KETERANGAN1 do
				,p.KODE_SURAT_JALAN
				,p.NO_SPM
				,p.TGL_TERIMA
				,sum(pd.JML_TERIMA) total_terima
				,sum(pd.BERAT_TERIMA) berat_terima
				,ba.NO_BA
				,p.TGL_SURAT_JALAN tgl_sj
				,p.tgl_verifikasi_do tgl_vdo
				,p.kuantitas_kg sj_kg
				,p.kuantitas_sak sj_sak
			from PENERIMAAN p
			inner join PENERIMAAN_D pd
				on p.NO_PENERIMAAN = pd.NO_PENERIMAAN
			left join BERITA_ACARA ba
				on ba.NO_PENERIMAAN = p.NO_PENERIMAAN
			where p.STATUS_TERIMA = 'C' and p.KODE_FARM = '{$kode_farm}'

			group by p.KETERANGAN1
				,p.KODE_SURAT_JALAN
				,p.NO_SPM
				,p.TGL_TERIMA
				,ba.NO_BA
				,p.TGL_SURAT_JALAN
				,p.tgl_verifikasi_do
				,p.kuantitas_kg
				,p.kuantitas_sak
		)kt on kt.do = dos.NO_DO
)xx {$where}
SQL;

		return $this->db->query($sql);
	}

	function monitoring_pp_bdy($periode_tahun,$kode_farm,$tanggal = NULL){

		$where = '';
		if(!empty($tanggal['operand'])){
			switch($tanggal['operand']){
				case 'between':
					$tanggal_param = $tanggal['operand'].' \''.$tanggal['startDate'].'\' and \''.$tanggal['endDate'].'\'';
					break;
				case '<=':
					$tanggal_param = $tanggal['operand'].' \''.$tanggal['endDate'].'\'';
					break;
				case '>=':
					$tanggal_param = $tanggal['operand'].' \''.$tanggal['startDate'].'\'';
					break;
			}
			$field = $tanggal['fieldname'];

			switch($field){
				case 'tgl_buat' :
					$where = ' where cast(xx.tgl_buat as date) '.$tanggal_param;
					break;
				case 'tgl_verifikasi' :
					$where = ' where  cast(xx.tgl_verifikasi as date) '.$tanggal_param;
					break;
				case 'tgl_sj' :
					$where = ' where cast(xx.tgl_sj as date) '.$tanggal_param;
					break;
				case 'tgl_terima' :
					$where = ' where cast(xx.tgl_terima as date) '.$tanggal_param;
					break;
				default:
			}
		}
		$sql = <<<SQL
	select * from(
		select pp.NO_LPB no_lpb
				,pp.REF_ID ref_id
				,pp.flok_bdy
				,stuff(convert(varchar(19), pp.TGL_BUAT, 126),11,1,' ') tgl_buat
				,stuff(convert(varchar(19), pp.TGL_RILIS, 126),11,1,' ') tgl_rilis
				,stuff(convert(varchar(19), pp.TGL_APPROVE1, 126),11,1,' ') tgl_op
				,ops.NO_OP no_op
				,ops.NO_OP_LOGISTIK no_op_logistik
				,pp.jml_order kuantitas_pp
				,cast(ops.kuantitas_op as int) kuantitas_op
				,dos.NO_DO no_do
				,coalesce(dos.TGL_KIRIM,pp.TGL_KIRIM) tgl_kirim
		--		,dos.tgl_verifikasi
				,dos.nama_ekspedisi
				,dos.total_do kuantitas_do
				,kt.KODE_SURAT_JALAN surat_jalan
				,kt.NO_SPM spm
				,stuff(convert(varchar(19), kt.TGL_TERIMA, 126),11,1,' ') tgl_terima
				,kt.total_terima
				,kt.berat_terima
				,kt.NO_BA berita_acara
				,kt.tgl_sj
				,kt.tgl_vdo tgl_verifikasi
				,kt.sj_kg
				,kt.sj_sak
				,kt.no_penerimaan
		from (
			select l.NO_LPB
				,l.REF_ID
				,l.TGL_BUAT
				,l.TGL_RILIS
				,le.TGL_KIRIM
				,l.TGL_APPROVE1
				,sum(le.jml_order) jml_order
				,ks.FLOK_BDY flok_bdy
			from lpb l
			inner join LPB_E le
				on le.NO_LPB = l.NO_LPB
			inner join KANDANG_SIKLUS ks
				on ks.NO_REG = le.NO_REG and year(ks.TGL_DOC_IN) = '{$periode_tahun}'
			where l.STATUS_LPB not in ('V','RJ')  and l.KODE_FARM = '{$kode_farm}'
			group by l.NO_LPB
				,l.REF_ID
				,l.TGL_BUAT
				,l.TGL_RILIS
				,le.TGL_KIRIM
				,l.TGL_APPROVE1
				,ks.FLOK_BDY
		)pp
		left join
			(
				select op.NO_LPB
					,op.NO_OP
					,op.NO_OP_LOGISTIK
					,sum(op_d.JML_ORDER) kuantitas_op
				from op
				inner join op_d
					on op.NO_OP = op_d.NO_OP
				where op.KODE_FARM  = '{$kode_farm}'
				group by op.NO_LPB
					,op.NO_OP
					,op.NO_OP_LOGISTIK
			)
		ops
			on ops.NO_LPB = pp.NO_LPB
		left join (
			select do.NO_DO
				,do.NO_OP
				,do.TGL_KIRIM
				,do.tgl_verifikasi
				,me.nama_ekspedisi
				,sum(opv.JML_KIRIM) total_do
			from do
			inner join OP_VEHICLE opv
				on opv.NO_OP = do.NO_OP and opv.NO_URUT = do.NO_URUT
			inner join M_EKSPEDISI me
				on me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
			where do.STATUS_DO != 'D'  and do.KODE_FARM = '{$kode_farm}'

			group by do.NO_DO
				,do.NO_OP
				,do.TGL_KIRIM
				,me.nama_ekspedisi
				,do.tgl_verifikasi
		)dos on dos.NO_OP = ops.NO_OP
		left join (
			select p.KETERANGAN1 do
				,p.NO_PENERIMAAN
				,p.KODE_SURAT_JALAN
				,p.NO_SPM
				,p.TGL_TERIMA
				,sum(pd.JML_TERIMA) total_terima
				,sum(pd.BERAT_TERIMA) berat_terima
				,ba.NO_BA
				,p.TGL_SURAT_JALAN tgl_sj
				,p.tgl_verifikasi_do tgl_vdo
				,p.kuantitas_kg sj_kg
				,p.kuantitas_sak sj_sak
			from PENERIMAAN p
			inner join PENERIMAAN_D pd
				on p.NO_PENERIMAAN = pd.NO_PENERIMAAN
			left join BERITA_ACARA ba
				on ba.NO_PENERIMAAN = p.NO_PENERIMAAN
			where p.STATUS_TERIMA = 'C' and p.KODE_FARM = '{$kode_farm}'
			group by p.KETERANGAN1
				,p.NO_PENERIMAAN
				,p.KODE_SURAT_JALAN
				,p.NO_SPM
				,p.TGL_TERIMA
				,ba.NO_BA
				,p.TGL_SURAT_JALAN
				,p.tgl_verifikasi_do
				,p.kuantitas_kg
				,p.kuantitas_sak
		)kt on kt.do = dos.NO_DO
)xx {$where}
	order by xx.tgl_buat desc
SQL;

		return $this->db->query($sql);
	}

	public function get_flock_farm($kodefarm){
		return $this->db->select('jml_flok')->where(array('kode_farm'=> $kodefarm))->get('m_farm')->row();
	}

	public function realisasi_pp($kodefarm,$flok){
		$sql = <<<SQL
		select 	x.no_reg
		, x.kuantitas
		, y.realisasi
	from
		(
		select ks.no_reg
			,sum(le.jml_order) kuantitas
		from lpb_e le
		inner join lpb l
			on l.no_lpb = le.no_lpb and l.status_lpb = 'A' and l.kode_farm = '{$kodefarm}'
		inner join KANDANG_SIKLUS ks
			on ks.NO_REG = le.no_reg and ks.STATUS_SIKLUS = 'O' and ks.kode_farm = '{$kodefarm}' and ks.FLOK_BDY = '{$flok}'
		group by ks.no_reg
		)x
		left join (
			select ks.no_reg
				,sum(md.JML_PUTAWAY)  realisasi
			from kandang_siklus ks
			inner join MOVEMENT_D md
			on  ks.KODE_FARM = md.KODE_FARM and md.keterangan1 = 'PUT' and md.KETERANGAN2 = ks.NO_REG
			where ks.kode_farm = '{$kodefarm}' and ks.flok_bdy = '{$flok}'
			group by ks.no_reg

		)y on y.no_reg = x.no_reg

SQL;

		return $this->db->query($sql);
	}

	public function get_pakan_standart($noreg){
		$sql = <<<SQL
			select x.*,mb.nama_barang from(
			 select max(md.std_umur) umur_akhir,md.kode_barang kode_pakan from M_STD_BUDIDAYA_D md
			 where md.KODE_STD_BUDIDAYA = (select ks.KODE_STD_BUDIDAYA from kandang_siklus ks where ks.NO_REG = '{$noreg}')
			 group by md.kode_barang
			 )x
			 inner join m_barang mb
			 	on mb.kode_barang = x.kode_pakan
		 order by x.umur_akhir
SQL;
		return $this->db->query($sql);
	}
	/** dibagi sejumlah kandang, karena pada forecast itu perflok bukan perkandang */
	public function budgetPakanNoReg($noreg){
		$sql = <<<SQL
		SELECT mb.kode_barang,mb.nama_barang,ceiling(sum(fd.JML_FORECAST) / (SELECT count(no_reg) FROM KANDANG_SIKLUS WHERE flok_bdy = f.KODE_FLOK_BDY AND kode_siklus = f.KODE_SIKLUS)) budget
		FROM FORECAST f
		JOIN FORECAST_D fd ON fd.FORECAST = f.id
		JOIN kandang_siklus ks ON ks.KODE_SIKLUS = f.KODE_SIKLUS 
			AND ks.flok_bdy = f.KODE_FLOK_BDY AND ks.NO_REG = '{$noreg}'
		JOIN M_BARANG mb ON mb.KODE_BARANG = fd.KODE_BARANG	
		GROUP BY mb.NAMA_BARANG,mb.KODE_BARANG,f.KODE_FLOK_BDY,f.KODE_SIKLUS
SQL;
		return $this->db->query($sql);
	}

	public function realisasi_pp_noreg($noreg,$tgl_rilis_pp = NULL){
		$whereTglRilis = !empty($tgl_rilis_pp) ? ' and l.tgl_rilis < \''.$tgl_rilis_pp.'\'' : '';
		$sql = <<<SQL
		select sum(le.jml_order) kuantitas
			,le.kode_barang
		from lpb_e le
		inner join lpb l
			on l.no_lpb = le.no_lpb and l.status_lpb = 'A' {$whereTglRilis}
		where le.no_reg = '{$noreg}'
		group by le.kode_barang		

SQL;
		
		return $this->db->query($sql);
	}

	
	public function pakan_farm_lain($noreg,$tgl_keb_awal,$tgl_keb_akhir){				
		$kodefarm = substr($noreg,0,2);
		$sql = <<<SQL
		SELECT oke.tgl_kebutuhan,sum(oke.jml_order) jml_order,oke.kode_barang 
		FROM ORDER_KANDANG ok 
		INNER JOIN ORDER_KANDANG_E oke ON ok.NO_ORDER = oke.NO_ORDER AND oke.NO_REG = '{$noreg}' and oke.tgl_kebutuhan between '{$tgl_keb_awal}' and '{$tgl_keb_akhir}'
		WHERE ok.NO_REFERENSI IS NOT NULL AND ok.NO_REFERENSI LIKE 'RL/%' AND ok.NO_REFERENSI NOT LIKE 'RL/{$kodefarm}%'
		AND ok.STATUS_ORDER = 'C'
		group by oke.tgl_kebutuhan,oke.kode_barang 
SQL;

		return $this->db->query($sql);
	}
}
