<?php
class M_riwayat_harian_kandang_bdy extends CI_Model{
	private $dbSqlServer ;

	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}

	function get_today(){
		$sql = <<<QUERY
		select getdate() as [today]
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	//---------added 31/10/2015-----------
	function get_lhk($noreg, $tgl_lhk){
		$sql = <<<QUERY
		select r.*, k.FLOK_BDY, k.KODE_FARM, f.NAMA_FARM, sum(coalesce(p.JUMLAH_AKHIR,0)) jumlah_panen
		from RHK r
		left join REALISASI_PANEN p
		on r.NO_REG = p.NO_REG and r.TGL_TRANSAKSI = p.TGL_PANEN
		inner join KANDANG_SIKLUS k on k.NO_REG = r.NO_REG
		inner join M_FARM f on f.KODE_FARM = k.KODE_FARM
		where r.NO_REG = '{$noreg}' and r.TGL_TRANSAKSI = '{$tgl_lhk}'
		group by r.NO_REG, r.B_LAIN2, r.J_SELEKSI, r.B_SELEKSI, r.J_SEXSLIP, r.B_SEXSLIP,
		r.J_PINDAH, r.B_PINDAH, r.J_AFKIR, r.B_AFKIR, r.J_MATI, r.TGL_TRANSAKSI, r.B_MATI,
		r.J_LAIN2, r.B_TERIMA, r.J_TERIMA, r.B_TERIMA_LAIN, r.J_TERIMA_LAIN, r.B_KANIBAL,
		r.J_KANIBAL, r.B_CAMPUR, r.J_CAMPUR, r.B_BERAT_BADAN, r.J_BERAT_BADAN, r.B_JUMLAH,
		r.J_JUMLAH, r.BERAT_TELUR, r.KETERANGAN1, r.TGL_BUAT, r.KETERANGAN2, r.USER_BUAT,
		r.ACK_KF, r.ACK_DIR, r.ACK_DESC, r.B_UNIFORMITY, r.J_UNIFORMITY, r.B_DAYA_HIDUP,
		r.J_DAYA_HIDUP, r.B_JUMLAH_PEMBAGI, r.J_JUMLAH_PEMBAGI, r.B_CV, r.J_CV, r.C_MATI,
		r.C_AFKIR, r.C_KURANG_LAIN, r.C_TERIMA_LAIN, r.C_JUMLAH, r.C_BERAT_BADAN, r.ACK1,
		r.USER_ACK1, r.ACK2, r.USER_ACK2, r.C_DAYA_HIDUP, r.C_AWAL,k.FLOK_BDY, k.KODE_FARM, f.NAMA_FARM

	
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_lhk_pakan($noreg, $tgl_lhk){
		$sql = <<<QUERY
		select M_BARANG.NAMA_BARANG, dbo.BENTUK_CONVERTION(M_BARANG.BENTUK_BARANG) BENTUK_BARANG, coalesce(qry.BRT_AKHIR, 0) BRT_AWAL, coalesce(qry.JML_AKHIR, 0) JML_AWAL, rhk_pakan.*,
		coalesce(qry2.jml_retur, 0) jml_retur, coalesce(qry2.brt_sak, 0) brt_sak
		from rhk_pakan
		inner join M_BARANG on RHK_PAKAN.KODE_BARANG = M_BARANG.KODE_BARANG
		left join (
			select M_BARANG.NAMA_BARANG, rhk_pakan.* from rhk_pakan
			inner join M_BARANG on RHK_PAKAN.KODE_BARANG = M_BARANG.KODE_BARANG
			where rhk_pakan.no_reg = '{$noreg}' and rhk_pakan.tgl_transaksi = (select left(convert(varchar,DATEADD(DAY, -1, '{$tgl_lhk}'),120), 10))
		)qry on qry.KODE_BARANG = rhk_pakan.KODE_BARANG and qry.JENIS_KELAMIN = rhk_pakan.JENIS_KELAMIN

		left join (
			select a.no_reg, b.kode_pakan, b.jenis_kelamin, sum(b.jml_retur) jml_retur, sum(brt_sak) brt_sak
			from RETUR_PAKAN_RUSAK a
			left join RETUR_PAKAN_RUSAK_ITEM b on a.ID = b.RETUR_PAKAN_RUSAK
			left join RETUR_PAKAN_RUSAK_ITEM_TIMBANG c on c.RETUR_PAKAN_RUSAK_ITEM = b.ID
			where a.no_reg = '{$noreg}' and left(convert(varchar,a.tgl_buat,120), 10) = (
				select
				left(convert(varchar,DATEADD(DAY, -1, min(coalesce(qry1.tgl_buat, qry2.tgl_buat))),120), 10)tgl_buat
				from
				(
					select no_reg, kode_barang, tgl_buat
					from kandang_movement_d
					where no_reg = '{$noreg}' and (keterangan1 = 'LHK') and tgl_transaksi = '{$tgl_lhk}'
				) qry1
				full outer join
				(
					select no_reg, kode_barang, max(tgl_buat) tgl_buat
					from kandang_movement_d
					where no_reg = '{$noreg}' and (keterangan1 = 'PENERIMAAN KANDANG')
				 and tgl_transaksi = '{$tgl_lhk}'
					group by no_reg, kode_barang, jenis_kelamin
				) qry2 on qry2.no_reg = qry1.no_reg and qry1.kode_barang = qry2.kode_barang
			)
			group by a.NO_REG, a.TGL_BUAT, b.kode_pakan, b.jenis_kelamin
		) qry2 on qry2.no_reg = rhk_pakan.no_reg and qry2.kode_pakan = rhk_pakan.kode_barang and qry2.jenis_kelamin = rhk_pakan.jenis_kelamin


		where rhk_pakan.no_reg = '{$noreg}' and rhk_pakan.tgl_transaksi = '{$tgl_lhk}'
QUERY;


		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_lhk_penimbangan($noreg, $tgl_lhk){
		$sql = <<<QUERY
			select rpb.*
			from RHK_PENIMBANGAN_BB rpb
			where rpb.NO_REG = '{$noreg}' and rpb.TGL_TRANSAKSI = '{$tgl_lhk}'
			order by rpb.sekat
QUERY;


		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_kandang_siklus($kode_farm){
		$sql = <<<QUERY
		select a.kode_kandang as id, b.nama_kandang as name, a.kode_siklus, a.no_reg, a.kode_farm, a.tgl_doc_in, a.tipe_kandang,
		a.jml_populasi, a.jml_populasi_terima, a.kode_std_budidaya, a.flok_bdy, a.tgl_panen, b.jml_sekat, dateadd(day,1,a.tgl_doc_in) tgl_kebutuhan_awal, e.nama_flok, BAP_DOC.status
		from kandang_siklus a
		inner join m_kandang b on b.kode_kandang = a.kode_kandang and b.kode_farm = a.kode_farm
		inner join (
		  select no_reg, keterangan1
		  from kandang_movement_d
		  where keterangan1 = 'PENERIMAAN KANDANG'
		  group by no_reg, keterangan1
		) c on c.no_reg = a.no_reg
		inner join m_farm d on d.kode_farm = a.kode_farm and d.grup_farm = 'BDY'
		inner join BAP_DOC on a.NO_REG = BAP_DOC.NO_REG and BAP_DOC.STATUS = 'A'
		left join m_flok e on a.flok_bdy = e.kode_flok and a.kode_farm = e.kode_farm
		where a.kode_farm = '{$kode_farm}'
		and a.status_siklus = 'O'
		--where a.kode_farm = 'BT1' and a.status_siklus = 'O'
		group by a.kode_kandang, b.nama_kandang, a.kode_siklus, a.no_reg, a.kode_farm, a.tgl_doc_in, a.tipe_kandang, a.jml_populasi, a.jml_populasi_terima, a.kode_std_budidaya, a.flok_bdy, a.tgl_panen, b.jml_sekat, e.nama_flok, BAP_DOC.status

QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_populasi_awal($no_reg, $tgl_transaksi){
		$sql = <<<QUERY
		select ks.kode_kandang, ks.no_reg, ks.tgl_doc_in, rhk.tgl_transaksi, datediff(day, ks.tgl_doc_in, rhk.tgl_transaksi) umur,
			coalesce(rhk.jml_populasi_awal, bap_doc.stok_awal) jml_populasi_awal, --coalesce(rhk.jml_populasi_awal, ks.jml_populasi) jml_populasi_awal,
			rhk7.jml_populasi_awal populasi_awal_7
		from kandang_siklus ks
		left join (
			select no_reg, tgl_transaksi,
				(coalesce(c_jumlah,0)+
					coalesce(c_kurang_lain,0)+
					coalesce(c_afkir,0)+
					coalesce(c_mati,0)+
					coalesce(c_terima_lain,0)
				) jml_awal,
				coalesce(c_terima_lain,0)c_terima_lain,
				coalesce(c_mati,0)c_mati,
				coalesce(c_afkir,0)c_afkir,
				coalesce(c_kurang_lain,0)c_kurang_lain,
				coalesce(c_jumlah,0)c_jumlah,
				c_awal jml_populasi_awal
			from rhk
			where no_reg = '{$no_reg}' and tgl_transaksi = dateadd(day, -1, '{$tgl_transaksi}')
		)rhk on rhk.no_reg = ks.no_reg
		left join (
			select rhk.no_reg
					,rhk.tgl_transaksi
					,coalesce(c_awal, 0) jml_populasi_awal
			from rhk
			left join kandang_siklus ks on ks.no_reg = rhk.no_reg
			where rhk.no_reg = '{$no_reg}'
			and datediff(day, ks.tgl_doc_in, rhk.tgl_transaksi) = 7
		)rhk7 on rhk7.no_reg = ks.no_reg
		left join bap_doc on bap_doc.no_reg = ks.no_reg
		where ks.no_reg = '{$no_reg}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_last_lhk($no_reg){
		$sql = <<<QUERY
		select q.*, r.ack_kf from (
		  select no_reg, max(tgl_transaksi) tgl_transaksi
		  from rhk
		  where no_reg = '{$no_reg}'
		  group by no_reg
		)q left join rhk r on r.NO_REG = q.NO_REG and r.TGL_TRANSAKSI = q.TGL_TRANSAKSI

QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_yesterday_lhk($no_reg, $tgl_transaksi){
		$sql = <<<QUERY
		select *
		from rhk
		where no_reg = '{$no_reg}' and tgl_transaksi = dateadd(day, -1, '{$tgl_transaksi}')
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_pastday_timbang_bb($no_reg, $tgl_transaksi){
		$sql = <<<QUERY
		select *
		from rhk
		where no_reg = '{$no_reg}' and tgl_transaksi = (
			select max(tgl_transaksi)
			from rhk where c_berat_badan is not null
			and no_reg = '{$no_reg}' and tgl_transaksi < '{$tgl_transaksi}'
		)
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_batas_pakai_pakan($no_reg, $tgl_lhk){
		$sql = <<<QUERY
		select jenis_kelamin, (jml_performance * 50) jml_performance, (detail_order * 50) detail_order
		from lpb_e
		inner join lpb
			on lpb.no_lpb = lpb_e.no_lpb and lpb.status_lpb = 'A'
		where no_reg = '{$no_reg}' and tgl_kebutuhan = '{$tgl_lhk}'
		-- group by jenis_kelamin, no_reg
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_jumlah_bj_last_lhk($no_reg, $tgl_transaksi, $tgl_doc_in){
		$sql = <<<QUERY
		select coalesce(a.no_reg, b.no_reg) no_reg
			   ,coalesce(a.tgl_transaksi, b.tgl_kebutuhan_awal) tgl --coalesce(a.tgl_transaksi, b.tgl_doc_in) tgl
			   ,coalesce(a.c_jumlah, b.stok_awal) jml --,coalesce(a.c_jumlah, b.jml_populasi) jml
			   ,b.stok_awal jml_doc_in --,b.jml_populasi jml_doc_in
		from rhk a
		right join (
		  select kandang_siklus.no_reg, kandang_siklus.tgl_doc_in, dateadd(day, 1, kandang_siklus.tgl_doc_in) tgl_kebutuhan_awal, coalesce(jml_populasi,0) jml_populasi, stok_awal
		  from kandang_siklus
		  inner join bap_doc on bap_doc.no_reg = kandang_siklus.no_reg
		  where kandang_siklus.no_reg = '{$no_reg}' and kandang_siklus.tgl_doc_in = '{$tgl_doc_in}' and status_siklus = 'O'
		) b on b.no_reg = a.no_reg and a.tgl_transaksi = dateadd(day,-1,'{$tgl_transaksi}')
QUERY;


		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}



	function get_jumlah_panen($no_reg, $tgl_transaksi){
		$sql = <<<QUERY
		select coalesce(sum(r.JUMLAH_AKHIR),0) jumlah_akhir
		from REALISASI_PANEN r
		where r.NO_REG = '{$no_reg}' and r.TGL_PANEN = '{$tgl_transaksi}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_pakan_last_lhk_dummy(){
		$data = array();
		$data[] = array(
			"jk"=> "J",
			"jenis_kelamin"=> "Jantan",
			"kode_barang"=> "111-23-4",
			"nama_barang"=> "DELE",
			"jml_awal"=> 10,
			"jml_kirim"=> 10,
			"jml_pakai"=> 0,
			"jml_akhir"=> 10,
			"bentuk_barang"=> "PELET"
		);

		return $data;
	}

	function get_target_bb($umur, $noreg){
		$sql = <<<QUERY
		select KODE_STD_BREEDING, JENIS_KELAMIN, STD_UMUR, TARGET_BB
		from M_STD_BREEDING
		where STD_UMUR = {$umur}
		and KODE_STD_BREEDING in (
			select KODE_STD_BREEDING_B KODE_STD_BREEDING
			from KANDANG_SIKLUS
			where NO_REG = '{$noreg}'
			union
			select KODE_STD_BREEDING_J
			from KANDANG_SIKLUS
			where NO_REG = '{$noreg}'
		)
QUERY;


		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_bb_std($umur, $kode_std_budidaya){
		$sql = <<<QUERY
		select msbd.kode_std_budidaya, msbd.target_bb
		from m_std_budidaya_d msbd
		inner join m_std_budidaya msb on msb.kode_std_budidaya = msbd.kode_std_budidaya
		left join kandang_siklus ks on ks.kode_std_budidaya = msb.kode_std_budidaya and ks.kode_farm = msb.kode_farm
		where msbd.std_umur = {$umur} and ks.kode_std_budidaya = '{$kode_std_budidaya}'
		group by msbd.kode_std_budidaya, msbd.target_bb
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
/* gak jadi, dikontrol di fitur yang lain, asumsinya kondisi ideal 	
	function get_pakan_last_lhk($no_reg){
		$sql = <<<SQL
		select getdate() tgl_buat,a.*,'Campur' jenis_kelamin,mb.NAMA_BARANG nama_barang,dbo.BENTUK_CONVERTION(mb.BENTUK_BARANG) bentuk_barang 
		from (
			select kode_barang,jml_akhir jml_order,berat_akhir berat_order, 'LHK' keterangan  from KANDANG_MOVEMENT_D where NO_REG = '{$no_reg}' and tgl_buat = (select max(tgl_buat) from KANDANG_MOVEMENT_D where NO_REG = '{$no_reg}' and (KETERANGAN1 = 'LHK' or  and KETERANGAN1 = 'RETUR SISA PAKAN')) 
			union all
			select kode_barang,sum(jml_order) jml_order,sum(berat_order) berat_order, KETERANGAN1 keterangan from KANDANG_MOVEMENT_D where NO_REG = '{$no_reg}' and KETERANGAN1 = 'PENERIMAAN KANDANG'
			and TGL_BUAT > (select coalesce(max(tgl_buat),(SELECT dateadd(minute,2,min(tgl_buat))  FROM KANDANG_MOVEMENT_D WHERE NO_REG = '{$no_reg}' and KETERANGAN1 = 'PENERIMAAN KANDANG')) from KANDANG_MOVEMENT_D where NO_REG = '{$no_reg}' and KETERANGAN1 = 'LHK')
			group by KODE_BARANG,KETERANGAN1
			union all 
			select kode_barang,sum(jml_order) jml_order,sum(berat_order) berat_order ,KETERANGAN1 keterangan from KANDANG_MOVEMENT_D where NO_REG = '{$no_reg}' and KETERANGAN1 = 'RETUR SISA PAKAN'
			and TGL_BUAT > (select max(tgl_buat) from KANDANG_MOVEMENT_D where NO_REG = '{$no_reg}' and KETERANGAN1 = 'LHK')
			group by KODE_BARANG,KETERANGAN1
		)a join m_barang mb on mb.KODE_BARANG = a.KODE_BARANG
SQL;
		
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        $tmp = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$result = array();
		
		if(!empty($tmp)){			
			$result = $this->hitungStokPakan($tmp);
		}
		return $result;				
	}*/
	/* grouping per pakan dan per keterangan 
	private function hitungStokPakan($arr){
		$result = array();
		$tmp = array();
		if(!empty($arr)){
			$infoGlobal = array();
			foreach($arr as $r){
				$kode_barang = $r['kode_barang'];
				$keterangan = $r['keterangan'];
				if(!isset($tmp[$kode_barang])){
					$tmp[$kode_barang] = array();
				}
				$tmp[$kode_barang][$keterangan] = $r;
				if(!isset($infoGlobal[$kode_barang])){
					$infoGlobal[$kode_barang] = array(
						'tgl_buat' => $r['tgl_buat'],
						'jenis_kelamin' => $r['jenis_kelamin'],
						'jk' => 'C',
						'kode_barang' => $r['kode_barang'],
						'nama_barang' => $r['nama_barang'],
						'jml_retur' => 0,
						'brt_sak' => 0,
						'berat_awal' => 0,
						'berat_kirim' => 0,
						'berat_pakai' => 0,
						'jml_awal' => 0,
						'jml_kirim' => 0,
						'jml_pakai' => 0,
					//	'berat_akhir' => 0,
						'bentuk_barang' => $r['bentuk_barang']	
					);
				}
			}
			if(!empty($infoGlobal)){
				foreach($infoGlobal as $kb => $_r){
					$infoGlobal[$kode_barang]['jml_retur'] = isset($tmp[$kb]['RETUR SISA PAKAN']) ? $tmp[$kb]['RETUR SISA PAKAN']['jml_order'] : 0;
					$infoGlobal[$kode_barang]['brt_sak'] = isset($tmp[$kb]['RETUR SISA PAKAN']) ? $tmp[$kb]['RETUR SISA PAKAN']['berat_order'] : 0;
					$infoGlobal[$kode_barang]['jml_awal'] = isset($tmp[$kb]['LHK']) ? $tmp[$kb]['LHK']['jml_order'] : 0;
					$infoGlobal[$kode_barang]['berat_awal'] = isset($tmp[$kb]['LHK']) ? $tmp[$kb]['LHK']['berat_order'] : 0;
					$infoGlobal[$kode_barang]['jml_kirim'] = isset($tmp[$kb]['PENERIMAAN KANDANG']) ? $tmp[$kb]['PENERIMAAN KANDANG']['jml_order'] : 0;
					$infoGlobal[$kode_barang]['berat_kirim'] = isset($tmp[$kb]['PENERIMAAN KANDANG']) ? $tmp[$kb]['PENERIMAAN KANDANG']['berat_order'] : 0;
					array_push($result,$infoGlobal[$kode_barang]);	
				}
			}
			
		}
		
		return $result;
	} */
	
	function get_pakan_last_lhk($no_reg){
		/*$sql = <<<QUERY
		select
		  a.tgl_buat,
			a.jenis_kelamin jk,
			case when a.jenis_kelamin = 'C' then 'Campur' end jenis_kelamin,
			a.kode_barang, b.nama_barang,
		  case when a.berat_order>= 0 then d.berat_awal else berat_akhir end berat_awal,
			case when coalesce(c.berat_order, a.berat_order)>= 0 then coalesce(c.berat_order,a.berat_order) else 0 end berat_kirim,
			case when a.berat_order< 0 then a.berat_order else 0 end berat_pakai,
			case when a.jml_order>= 0 then d.jml_awal else jml_akhir end jml_awal,
			case when coalesce(c.jml_order, a.jml_order)>= 0 then coalesce(c.jml_order,a.jml_order) else 0 end jml_kirim,
			case when a.jml_order< 0 then a.jml_order else 0 end jml_pakai,
			a.jml_akhir, a.berat_akhir,
			dbo.BENTUK_CONVERTION(b.bentuk_barang) bentuk_barang
		from (
		  select * from KANDANG_MOVEMENT_D
		  where no_reg = '{$no_reg}' and tgl_buat in (
			select distinct coalesce(qry1.tgl_buat, qry2.tgl_buat) tgl_buat
			from
		  (select no_reg, kode_barang, max(tgl_buat) tgl_buat
		  from kandang_movement_d
			where no_reg = '{$no_reg}' and (keterangan1 = 'LHK')
			group by no_reg, kode_barang, jenis_kelamin) qry1
		  full outer join
		  (select no_reg, kode_barang, max(tgl_buat) tgl_buat
		  from kandang_movement_d
			where no_reg = '{$no_reg}' and (keterangan1 = 'PENERIMAAN KANDANG')
			group by no_reg, kode_barang, jenis_kelamin) qry2 on qry2.no_reg = qry1.no_reg and qry1.kode_barang = qry2.kode_barang
		  )) a
		  left join (
			  select no_reg, kode_barang, jenis_kelamin, jml_awal, berat_awal from KANDANG_MOVEMENT_D
				  where no_reg = '{$no_reg}' and tgl_buat in (
					select distinct coalesce(qry1.tgl_buat, qry2.tgl_buat) tgl_buat
					from
				  ( select no_reg, kode_barang, min(tgl_buat) tgl_buat
					from kandang_movement_d
					  where no_reg = '{$no_reg}' and (keterangan1 = 'LHK')
				  --and (select left(convert(varchar,tgl_buat,120), 10)) = '2016-01-01'
					  group by no_reg, kode_barang, jenis_kelamin
				) qry1
				  full outer join
				  ( select no_reg, kode_barang, min(tgl_buat) tgl_buat
					from kandang_movement_d
					  where no_reg = '{$no_reg}' and (keterangan1 = 'PENERIMAAN KANDANG')
				  --and (select left(convert(varchar,tgl_buat,120), 10)) = '2016-01-01'
					  group by no_reg, kode_barang, jenis_kelamin
				) qry2 on qry2.no_reg = qry1.no_reg and qry1.kode_barang = qry2.kode_barang
				  )
			) d on d.no_reg = a.no_reg and d.kode_barang = a.kode_barang and d.jenis_kelamin = a.jenis_kelamin
		  left join (
			select no_reg, kode_barang, jenis_kelamin, berat_order, jml_order, max(tgl_buat) tgl_buat
			from kandang_movement_d
			where no_reg = '{$no_reg}' and (keterangan1 = 'PENERIMAAN KANDANG')
			group by no_reg, kode_barang, jenis_kelamin,berat_order,jml_order
		  ) c on c.no_reg = a.no_reg and c.kode_barang = a.kode_barang and c.jenis_kelamin = a.jenis_kelamin and c.tgl_buat > a.tgl_buat
		  inner join m_barang b on a.kode_barang = b.kode_barang
		  group by a.tgl_buat, a.jenis_kelamin, a.kode_barang, b.nama_barang, a.berat_awal, d.berat_awal, a.berat_order, a.berat_akhir, a.jml_awal, d.jml_awal, a.jml_order, a.jml_akhir, bentuk_barang, c.berat_order, c.jml_order
		--group by a.tgl_buat, a.jenis_kelamin, a.kode_barang, b.nama_barang, a.berat_awal, a.berat_order, a.berat_akhir, a.jml_awal, a.jml_order, a.jml_akhir, bentuk_barang, c.berat_order, c.jml_order
		order by a.jenis_kelamin desc, b.nama_barang asc
QUERY;*/

	$sql = <<<QUERY
		select
			a.tgl_buat,
			a.jenis_kelamin jk,
			case when a.jenis_kelamin = 'C' then 'Campur' end jenis_kelamin,
			a.kode_barang, b.nama_barang,
			coalesce(d.jml_retur, 0) jml_retur,
			coalesce(d.brt_sak, 0) brt_sak,
			case when a.berat_order>= 0 then a.berat_awal else a.berat_akhir end berat_awal,
			case when coalesce(c.berat_order, a.berat_order)>= 0 then coalesce(c.berat_order,a.berat_order) else 0 end berat_kirim,
			--case when coalesce(a.berat_order, c.berat_order)>= 0 then coalesce(a.berat_order,c.berat_order) else 0 end berat_kirim,
			case when a.berat_order< 0 then a.berat_order else 0 end berat_pakai,
			case when a.jml_order>= 0 then a.jml_awal else a.jml_akhir end jml_awal,
			case when coalesce(c.jml_order, a.jml_order)>= 0 then coalesce(c.jml_order,a.jml_order) else 0 end jml_kirim,
			--case when coalesce(a.jml_order, c.jml_order)>= 0 then coalesce(a.jml_order,c.jml_order) else 0 end jml_kirim,
			case when a.jml_order< 0 then a.jml_order else 0 end jml_pakai,
			coalesce(c.jml_akhir,0) jml_akhir, coalesce(c.berat_akhir,0) berat_akhir,
			dbo.BENTUK_CONVERTION(b.bentuk_barang) bentuk_barang
		from
			(
			
				select kmd.*
				from kandang_movement_d kmd,
				(
				  select distinct coalesce(qry1.no_reg, qry2.no_reg) no_reg, coalesce(qry1.kode_barang, qry2.kode_barang) kode_barang, coalesce(qry1.tgl_buat, qry2.tgl_buat) tgl_buat
				  from
				  (
					select no_reg, kode_barang, max(tgl_buat) tgl_buat
					from kandang_movement_d
					where no_reg = '{$no_reg}' and (keterangan1 = 'LHK' or keterangan1 = 'RETUR SISA PAKAN')
					group by no_reg, kode_barang, jenis_kelamin
				  ) qry1
				  full outer join
				  (
					select no_reg, kode_barang, max(tgl_buat) tgl_buat
					from kandang_movement_d
					where no_reg = '{$no_reg}' and (keterangan1 = 'PENERIMAAN KANDANG')
					group by no_reg, kode_barang, jenis_kelamin
				  ) qry2 on qry2.no_reg = qry1.no_reg and qry1.kode_barang = qry2.kode_barang
				)kmd2
				where
				kmd.no_reg = '{$no_reg}'
				and kmd.no_reg = kmd2.no_reg
				and kmd.kode_barang = kmd2.kode_barang
				and kmd.tgl_buat = kmd2.tgl_buat
			) a
			left join (
				select no_reg, kode_barang, jenis_kelamin, sum(berat_order) berat_order, sum(jml_order) jml_order, max(tgl_buat) tgl_buat, max(berat_akhir) berat_akhir, max(jml_akhir) jml_akhir
				from kandang_movement_d
				
				where no_reg = '{$no_reg}' and (keterangan1 = 'PENERIMAAN KANDANG')
				and (
					(
						(select max(tgl_buat) from KANDANG_MOVEMENT_D where dbo.KANDANG_MOVEMENT_D.KETERANGAN1 = 'LHK' and no_reg = '{$no_reg}') is not null
						and
						(tgl_buat > (select max(tgl_buat) from KANDANG_MOVEMENT_D where dbo.KANDANG_MOVEMENT_D.KETERANGAN1 = 'LHK' and no_reg = '{$no_reg}'))
					)
					or
					(
						(select max(tgl_buat) from KANDANG_MOVEMENT_D where dbo.KANDANG_MOVEMENT_D.KETERANGAN1 = 'LHK' and no_reg = '{$no_reg}') is null
						and
						(tgl_buat > (select max(tgl_buat) from KANDANG_MOVEMENT_D where dbo.KANDANG_MOVEMENT_D.KETERANGAN1 = 'LHK' and no_reg = '{$no_reg}'))
					)
					or
					(
						(select max(tgl_buat) from KANDANG_MOVEMENT_D where dbo.KANDANG_MOVEMENT_D.KETERANGAN1 = 'LHK' and no_reg = '{$no_reg}') is null
						and
						(tgl_buat >= (select max(tgl_buat) from KANDANG_MOVEMENT_D where dbo.KANDANG_MOVEMENT_D.KETERANGAN1 = 'PENERIMAAN KANDANG' and no_reg = '{$no_reg}'))
					)
				)
				group by no_reg, kode_barang, jenis_kelamin

			) c on c.no_reg = a.no_reg and c.kode_barang = a.kode_barang and c.jenis_kelamin = a.jenis_kelamin and c.tgl_buat >= a.tgl_buat
			left join (
				select a.no_reg, b.kode_pakan, b.jenis_kelamin, sum(b.jml_retur) jml_retur, sum(brt_sak) brt_sak
				from RETUR_PAKAN_RUSAK a
				left join RETUR_PAKAN_RUSAK_ITEM b on a.ID = b.RETUR_PAKAN_RUSAK
				left join RETUR_PAKAN_RUSAK_ITEM_TIMBANG c on c.RETUR_PAKAN_RUSAK_ITEM = b.ID
				where a.no_reg = '{$no_reg}' and a.tgl_buat > (
					select max(coalesce(qry1.tgl_buat, qry2.tgl_buat)) tgl_buat
					from
					(
						select no_reg, kode_barang, max(tgl_buat) tgl_buat
						from kandang_movement_d
						where no_reg = '{$no_reg}' and (keterangan1 = 'LHK')
						group by no_reg, kode_barang, jenis_kelamin
					) qry1
					full outer join
					(
						select no_reg, kode_barang, max(tgl_buat) tgl_buat
						from kandang_movement_d
						where no_reg = '{$no_reg}' and (keterangan1 = 'PENERIMAAN KANDANG')
						group by no_reg, kode_barang, jenis_kelamin
					) qry2 on qry2.no_reg = qry1.no_reg and qry1.kode_barang = qry2.kode_barang
				)
				group by a.NO_REG, a.TGL_BUAT, b.kode_pakan, b.jenis_kelamin
			  )d on d.no_reg = a.no_reg and d.kode_pakan = a.kode_barang and d.jenis_kelamin = a.jenis_kelamin
			inner join m_barang b on a.kode_barang = b.kode_barang
			group by a.tgl_buat, a.jenis_kelamin, a.kode_barang, b.nama_barang, a.berat_awal, a.berat_order, a.berat_akhir, a.jml_awal, a.jml_order, a.jml_akhir, bentuk_barang, c.berat_order, c.jml_order, c.jml_akhir, c.berat_akhir, d.jml_retur, d.brt_sak
			order by a.jenis_kelamin desc, b.nama_barang asc
QUERY;

	//	log_message("error", $sql);
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_farm($kode_farm){
		$sql = <<<QUERY
		select kode_farm, nama_farm
		from m_farm
		where kode_farm = '{$kode_farm}'

QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function insert_lhk_awal($lhk_header, $kandang_movement, $kandang_movement_d, $lhk_pakan, $rhk_penimbangan, $tutup_siklus, $kompensasi_stok_h, $kompensasi_stok_d){
		$this->dbSqlServer->trans_begin();
		$pass = true;

		if(isset($tutup_siklus)){
			$update["status_siklus"] = 'C';
			$sql = <<<QUERY
			update kandang_siklus set status_siklus = 'C', tgl_ubah = getdate() where no_reg = '{$tutup_siklus}'
QUERY;

			$stmt = $this->dbSqlServer->conn_id->prepare($sql);
			if($stmt->execute()){
				$kandang_siklus = $this->check_kandang_open();
				$kode_siklus = $kandang_siklus["kode_siklus"];
				$jml_kandang_open = $kandang_siklus["jml_kandang_open"];

				if($jml_kandang_open > 0){
					$pass = true;
				}else{
					$sql = <<<QUERY
					update m_periode set status_periode = 'N' where kode_siklus = '{$kode_siklus}'
QUERY;
					$stmt = $this->dbSqlServer->conn_id->prepare($sql);
					if($stmt->execute()){
						$pass = true;
					}else{
						$pass = false;
					}
				}

			}else{
				$pass = false;
			}
		}

		if($pass){

			$this->dbSqlServer->insert("rhk", $lhk_header);
			$lhk_pakan_result;
			if(is_array($lhk_pakan) and count($lhk_pakan) > 0){
				$success = 0;
				for($i=0;$i<count($lhk_pakan);$i++){
					$this->dbSqlServer->insert("rhk_pakan", $lhk_pakan[$i]);
					if($this->dbSqlServer->affected_rows() > 0){
						$success++;
					}
				}

				if($success == count($lhk_pakan))
					$lhk_pakan_result = true;
				else
					$lhk_pakan_result = false;
			}else{
				$lhk_pakan_result = true;
			}

			if($lhk_pakan_result){
				$rhk_penimbangan_result;
				if(is_array($rhk_penimbangan) and count($rhk_penimbangan) > 0){
					$success = 0;
					for($i=0;$i<count($rhk_penimbangan);$i++){
						$this->dbSqlServer->insert("rhk_penimbangan_bb", $rhk_penimbangan[$i]);
						if($this->dbSqlServer->affected_rows() > 0){
							$success++;
						}
					}

					if($success == count($rhk_penimbangan))
						$rhk_penimbangan_result = true;
					else
						$rhk_penimbangan_result = false;
				}else
					$rhk_penimbangan_result = true;

				if($rhk_penimbangan_result){
					$kandang_movement_result;
					if(is_array($kandang_movement) and count($kandang_movement) > 0){
						$success = 0;
						for($i=0;$i<count($kandang_movement);$i++){

							// $dt_upt["jml_stok"] = $kandang_movement[$i]["jml_stok"];
							// $dt_upt["berat_stok"] = $kandang_movement[$i]["berat_stok"];

							// $this->dbSqlServer->where("no_reg", $kandang_movement[$i]["no_reg"]);
							// $this->dbSqlServer->where("kode_barang", $kandang_movement[$i]["kode_barang"]);
							// $this->dbSqlServer->where("jenis_kelamin", $kandang_movement[$i]["jenis_kelamin"]);
							// $this->dbSqlServer->update("kandang_movement", $dt_upt);
							// if($this->dbSqlServer->affected_rows() > 0){
								$success++;
							// }
						}

						if($success == count($kandang_movement))
							$kandang_movement_result = true;
						else
							$kandang_movement_result = false;
					}else
						$kandang_movement_result = true;

					if($kandang_movement_result){
						$kandang_movement_d_result;
						if(is_array($kandang_movement_d) and count($kandang_movement_d) > 0){
							$success = 0;
							for($i=0;$i<count($kandang_movement_d);$i++){
								// $this->dbSqlServer->insert("kandang_movement_d", $kandang_movement_d[$i]);
								// if($this->dbSqlServer->affected_rows() > 0){
									$success++;
								// }
							}

							if($success == count($kandang_movement_d))
								$kandang_movement_d_result = true;
							else
								$kandang_movement_d_result = false;
						}else
							$kandang_movement_d_result = true;

						if($kandang_movement_d_result){
							$kompensasi_stok_h_result;
							if(is_array($kompensasi_stok_h) and count($kompensasi_stok_h) > 0){
								$success = 0;
								for($i=0;$i<count($kompensasi_stok_h);$i++){
									// $this->dbSqlServer->where("no_penerimaan_kandang", $kompensasi_stok_h[$i]["no_pengiriman"]);
									// $this->dbSqlServer->where("no_reg", $kompensasi_stok_h[$i]["no_reg"]);
									// $this->dbSqlServer->where("tgl_transaksi", $kompensasi_stok_h[$i]["tgl_transaksi"]);
									// $this->dbSqlServer->where("kode_barang", $kompensasi_stok_h[$i]["kode_barang"]);
									// $this->dbSqlServer->update("log_stok_kandang", array(
										// "sak_sisa"=>$kompensasi_stok_h[$i]["sak_sisa"],
										// "kg_sisa"=>$kompensasi_stok_h[$i]["kg_sisa"],
										// "kg_rata2"=>$kompensasi_stok_h[$i]["kg_rata2"]
										// )
									// );

									// if($this->dbSqlServer->affected_rows() > 0){
										$success++;
									// }
								}

								if($success == count($kompensasi_stok_h))
									$kompensasi_stok_h_result = true;
								else
									$kompensasi_stok_h_result = false;
							}else
								$kompensasi_stok_h_result = true;

							if($kompensasi_stok_h){
								$kompensasi_stok_d_result;
								if(is_array($kompensasi_stok_d) and count($kompensasi_stok_d) > 0){
									$success = 0;
									for($i=0;$i<count($kompensasi_stok_d);$i++){
										// $this->dbSqlServer->insert("log_stok_kandang_d", $kompensasi_stok_d[$i]);
										// if($this->dbSqlServer->affected_rows() > 0){
											$success++;
										// }
									}

									if($success == count($kompensasi_stok_d))
										$kompensasi_stok_d_result = true;
									else
										$kompensasi_stok_d_result = false;
								}else
									$kompensasi_stok_d_result = true;

								if($kompensasi_stok_d_result){
									$this->dbSqlServer->trans_commit();
									return true;
								}else{
									$this->dbSqlServer->trans_rollback();
									return false;
								}
							}else{
								$this->dbSqlServer->trans_commit();
								return true;
							}
						}else{
							$this->dbSqlServer->trans_rollback();
							return false;
						}
					}else{
						$this->dbSqlServer->trans_rollback();
						return false;
					}
				}else{
					$this->dbSqlServer->trans_rollback();
					return false;
				}

			}else{
				$this->dbSqlServer->trans_rollback();
				return false;
			}
		}else{
			$this->dbSqlServer->trans_rollback();
			return false;
		}
	}

	function insert_lhk($lhk_header, $kandang_movement, $kandang_movement_d, $lhk_pakan, $rhk_penimbangan, $tutup_siklus, $kompensasi_stok_h, $kompensasi_stok_d){
		$this->dbSqlServer->trans_begin();
		$pass = true;

		if(isset($tutup_siklus)){
			$update["status_siklus"] = 'C';
			$sql = <<<QUERY
			update kandang_siklus set status_siklus = 'C', tgl_ubah = getdate() where no_reg = '{$tutup_siklus}'
QUERY;

			$stmt = $this->dbSqlServer->conn_id->prepare($sql);
			if($stmt->execute()){
				$kandang_siklus = $this->check_kandang_open();
				$kode_siklus = $kandang_siklus["kode_siklus"];
				$jml_kandang_open = $kandang_siklus["jml_kandang_open"];

				if($jml_kandang_open > 0){
					$pass = true;
				}else{
					$sql = <<<QUERY
					update m_periode set status_periode = 'N' where kode_siklus = '{$kode_siklus}'
QUERY;
					$stmt = $this->dbSqlServer->conn_id->prepare($sql);
					if($stmt->execute()){
						$pass = true;
					}else{
						$pass = false;
					}
				}

			}else{
				$pass = false;
			}
		}

		if($pass){

			$sql_rhk = "delete from RHK where NO_REG = '".$lhk_header["no_reg"]."' and TGL_TRANSAKSI = '".$lhk_header["tgl_transaksi"]."'";
			$sql_rhk_pakan = "delete from RHK_PAKAN where NO_REG = '".$lhk_header["no_reg"]."' and TGL_TRANSAKSI = '".$lhk_header["tgl_transaksi"]."'";
			$sql_rhk_penimbangan = "delete from RHK_PENIMBANGAN_BB where NO_REG = '".$lhk_header["no_reg"]."' and TGL_TRANSAKSI = '".$lhk_header["tgl_transaksi"]."'";


		//	log_message("error", $sql_rhk_penimbangan);
			$this->dbSqlServer->query($sql_rhk_penimbangan);
			if($this->dbSqlServer->affected_rows()>0){
			//	log_message("error", "hapus penimbangan");
				$this->dbSqlServer->query($sql_rhk_pakan);
				if($this->dbSqlServer->affected_rows()>0){
			//		log_message("error", "hapus pakan");
					$this->dbSqlServer->query($sql_rhk);
					//if($this->dbSqlServer->affected_rows()>0){
			//			log_message("error", "hapus rhk");

						$this->dbSqlServer->insert("rhk", $lhk_header);
						$lhk_pakan_result;
						if(is_array($lhk_pakan) and count($lhk_pakan) > 0){
							$success = 0;
							for($i=0;$i<count($lhk_pakan);$i++){
								$this->dbSqlServer->insert("rhk_pakan", $lhk_pakan[$i]);
								if($this->dbSqlServer->affected_rows() > 0){
									$success++;
								}
							}

							if($success == count($lhk_pakan))
								$lhk_pakan_result = true;
							else
								$lhk_pakan_result = false;
						}else{
							$lhk_pakan_result = true;
						}

						if($lhk_pakan_result){
							//log_message("error", "insert rhk_pakan");
							$rhk_penimbangan_result;
							if(is_array($rhk_penimbangan) and count($rhk_penimbangan) > 0){
								$success = 0;
								for($i=0;$i<count($rhk_penimbangan);$i++){
									$this->dbSqlServer->insert("rhk_penimbangan_bb", $rhk_penimbangan[$i]);
									if($this->dbSqlServer->affected_rows() > 0){
										$success++;
									}
								}

								if($success == count($rhk_penimbangan))
									$rhk_penimbangan_result = true;
								else
									$rhk_penimbangan_result = false;
							}else
								$rhk_penimbangan_result = true;

							if($rhk_penimbangan_result){
								$kandang_movement_result;
								if(is_array($kandang_movement) and count($kandang_movement) > 0){
									$success = 0;
									for($i=0;$i<count($kandang_movement);$i++){

										$dt_upt["jml_stok"] = $kandang_movement[$i]["jml_stok"];
										$dt_upt["berat_stok"] = $kandang_movement[$i]["berat_stok"];

										$this->dbSqlServer->where("no_reg", $kandang_movement[$i]["no_reg"]);
										$this->dbSqlServer->where("kode_barang", $kandang_movement[$i]["kode_barang"]);
										$this->dbSqlServer->where("jenis_kelamin", $kandang_movement[$i]["jenis_kelamin"]);
										$this->dbSqlServer->update("kandang_movement", $dt_upt);
										if($this->dbSqlServer->affected_rows() > 0){
											$success++;
										}
									}

									if($success == count($kandang_movement))
										$kandang_movement_result = true;
									else
										$kandang_movement_result = false;
								}else
									$kandang_movement_result = true;

								if($kandang_movement_result){
									$kandang_movement_d_result;
									if(is_array($kandang_movement_d) and count($kandang_movement_d) > 0){
										$success = 0;
										for($i=0;$i<count($kandang_movement_d);$i++){
											$this->dbSqlServer->insert("kandang_movement_d", $kandang_movement_d[$i]);
											if($this->dbSqlServer->affected_rows() > 0){
												$success++;
											}
										}

										if($success == count($kandang_movement_d))
											$kandang_movement_d_result = true;
										else
											$kandang_movement_d_result = false;
									}else
										$kandang_movement_d_result = true;

									if($kandang_movement_d_result){
										$kompensasi_stok_h_result;
										if(is_array($kompensasi_stok_h) and count($kompensasi_stok_h) > 0){
											$success = 0;
											for($i=0;$i<count($kompensasi_stok_h);$i++){
												$this->dbSqlServer->where("no_penerimaan_kandang", $kompensasi_stok_h[$i]["no_pengiriman"]);
												$this->dbSqlServer->where("no_reg", $kompensasi_stok_h[$i]["no_reg"]);
												$this->dbSqlServer->where("tgl_transaksi", $kompensasi_stok_h[$i]["tgl_transaksi"]);
												$this->dbSqlServer->where("kode_barang", $kompensasi_stok_h[$i]["kode_barang"]);
												$this->dbSqlServer->update("log_stok_kandang", array(
													"sak_sisa"=>$kompensasi_stok_h[$i]["sak_sisa"],
													"kg_sisa"=>$kompensasi_stok_h[$i]["kg_sisa"],
													"kg_rata2"=>$kompensasi_stok_h[$i]["kg_rata2"]
													)
												);

												if($this->dbSqlServer->affected_rows() > 0){
													$success++;
												}
											}

											if($success == count($kompensasi_stok_h))
												$kompensasi_stok_h_result = true;
											else
												$kompensasi_stok_h_result = false;
										}else
											$kompensasi_stok_h_result = true;

										if($kompensasi_stok_h){
											$kompensasi_stok_d_result;
											if(is_array($kompensasi_stok_d) and count($kompensasi_stok_d) > 0){
												$success = 0;
												for($i=0;$i<count($kompensasi_stok_d);$i++){
													$this->dbSqlServer->insert("log_stok_kandang_d", $kompensasi_stok_d[$i]);
													if($this->dbSqlServer->affected_rows() > 0){
														$success++;
													}
												}

												if($success == count($kompensasi_stok_d))
													$kompensasi_stok_d_result = true;
												else
													$kompensasi_stok_d_result = false;
											}else
												$kompensasi_stok_d_result = true;

											if($kompensasi_stok_d_result){
												$this->dbSqlServer->trans_commit();
												return true;
											}else{
												$this->dbSqlServer->trans_rollback();
												return false;
											}
										}else{
											$this->dbSqlServer->trans_commit();
											return true;

											// $this->dbSqlServer->trans_rollback();
											// return false;
										}
									}else{
										$this->dbSqlServer->trans_rollback();
										return false;
									}
								}else{
									$this->dbSqlServer->trans_rollback();
									return false;
								}
							}else{
								$this->dbSqlServer->trans_rollback();
								return false;
							}

						}else{
							$this->dbSqlServer->trans_rollback();
							return false;
						}
					// }
					// else{
						// $this->dbSqlServer->trans_rollback();
						// return false;
					// }
				}
				else{
					$this->dbSqlServer->trans_rollback();
					return false;
				}
			}
			else{
				//log_message("error", "rulbeg");
				$this->dbSqlServer->trans_rollback();
				return false;
			}
		}else{
			$this->dbSqlServer->trans_rollback();
			return false;
		}
	}

	function check_panen_exist($noreg){
		$sql = <<<QUERY
			select count(*) n_panen from realisasi_panen
			where no_reg = '{$noreg}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	/*untuk tutup siklus*/

	function tutup_siklus($noreg, $kode_farm, $user){
		$sql = <<<QUERY
		exec dbo.LHK_TUTUP_SIKLUS_BDY :noreg, :kodefarm, :user
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
		$stmt->bindParam ( ':noreg', $noreg );
		$stmt->bindParam ( ':kodefarm', $kode_farm );
		$stmt->bindParam ( ':user', $user );
        $stmt->execute();
	}

	function buat_persetujuan_retur($noreg, $user, $setuju){
		$sql = <<<QUERY
		exec dbo.LHK_PERSETUJUAN_RETUR :noreg, :user, :setuju
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
		$stmt->bindParam ( ':noreg', $noreg );
		$stmt->bindParam ( ':user', $user );
		$stmt->bindParam ( ':setuju', $setuju );
		$stmt->execute();
	}

	/*untuk pemantauan lhk*/
	function get_min_doc_in($kode_farm){
		$sql = <<<QUERY
		select min(tgl_doc_in) tgl_doc_in
		from kandang_siklus
		where kode_farm = '{$kode_farm}'
			and status_siklus= 'O'
		group by kode_farm

QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_data_lhk($start_date, $end_date, $kode_farm, $param){

		$param = (trim($param) !== "" and strlen(trim($param)) > 0) ? "where " . $param : "";

		$sql = <<<QUERY
		select *, ks.tgl_doc_in, SUBSTRING(convert(varchar, qry.colDate, 113), 0, 13) tgl_lhk, SUBSTRING(convert(varchar, ks.tgl_doc_in, 113), 0, 13) tgl_doc_in_lhk from dbo.get_data_pemantauan_lhk('{$start_date}', '{$end_date}', '{$kode_farm}') qry
		inner join m_farm mf on mf.kode_farm = qry.kode_farm and mf.grup_farm = 'BDY'
		left join (
			select qa.kode_farm kode_farm_kd, qa.no_reg no_reg2, qa.tgl_transaksi tgl_transaksi2, qa.tgl_doc_in
			from (
				select b.kode_farm, a.no_reg, a.TGL_TRANSAKSI, a.KODE_BARANG, a.JENIS_KELAMIN, sum(a.brt_pakai) brt_pakai, b.tgl_doc_in
				from rhk_pakan a
				inner join KANDANG_SIKLUS b on b.NO_REG = a.NO_REG
				group by b.kode_farm, a.no_reg, a.TGL_TRANSAKSI, a.KODE_BARANG, a.JENIS_KELAMIN, b.tgl_doc_in
			) qa left join
			(
				select * from (
					select KODE_FARM, NO_REG, TGL_KEBUTUHAN, JENIS_KELAMIN,
					case when max(JML_PERFORMANCE) > max(detail_order) then (max(JML_PERFORMANCE)*50) else (max(detail_order)*50) end BERAT_MAKS
					from lpb_e
					group by kode_farm, no_reg, tgl_kebutuhan, jenis_kelamin
				)a
				group by KODE_FARM, NO_REG, TGL_KEBUTUHAN, JENIS_KELAMIN, BERAT_MAKS
			)qb on qa.KODE_FARM = qb.KODE_FARM
			   and qa.NO_REG = qb.NO_REG
			   and qa.TGL_TRANSAKSI = qb.TGL_KEBUTUHAN
			   and qa.JENIS_KELAMIN = qb.JENIS_KELAMIN
			where qa.BRT_PAKAI > qb.BERAT_MAKS
		)qc on  qc.kode_farm_kd = qry.kode_farm and qc.NO_REG2 = qry.noReg and qc.TGL_TRANSAKSI2 = qry.colDate
		left join KANDANG_SIKLUS ks on ks.NO_REG = qry.noReg and ks.KODE_FARM = qry.kode_farm
		$param
		order by qry.nama_kandang asc, qry.colDate asc

QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function simpan_ack_kf($desc, $no_reg, $tgl_transaksi){
		$sql = <<<QUERY
		update rhk set ack_kf = getdate(), ack_desc = '{$desc}' where no_reg = '{$no_reg}' and tgl_transaksi = '{$tgl_transaksi}'
QUERY;
		$this->dbSqlServer->where("no_reg", $no_reg);
		$this->dbSqlServer->where("tgl_transaksi", $tgl_transaksi);

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        return $stmt->execute();
	}

	function simpan_ack_kadep($no_reg, $tgl_transaksi, $user){
		$user = $this->session->userdata("kode_user");
		$sql = <<<QUERY
		update rhk set ack1 = getdate(), user_ack1 = '{$user}' where no_reg = '{$no_reg}' and tgl_transaksi = '{$tgl_transaksi}'
QUERY;
		$this->dbSqlServer->where("no_reg", $no_reg);
		$this->dbSqlServer->where("tgl_transaksi", $tgl_transaksi);

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        return $stmt->execute();
	}

	function simpan_ack_kadiv($no_reg, $tgl_transaksi, $user){
		$user = $this->session->userdata("kode_user");
		$sql = <<<QUERY
		update rhk set ack2 = getdate(), user_ack2 = '{$user}' where no_reg = '{$no_reg}' and tgl_transaksi = '{$tgl_transaksi}'
QUERY;
		$this->dbSqlServer->where("no_reg", $no_reg);
		$this->dbSqlServer->where("tgl_transaksi", $tgl_transaksi);

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        return $stmt->execute();
	}

	function get_farm_for_pemantauan($kode_user){
		$sql = <<<QUERY
		select pd.kode_farm, mf.nama_farm, count(rhk.no_reg) jml
		from pegawai_d pd
		inner join m_farm mf on pd.kode_farm = mf.kode_farm and mf.grup_farm = 'BDY'
		left join kandang_siklus ks on mf.kode_farm = ks.kode_farm and ks.status_siklus = 'O'
		left join rhk on rhk.no_reg = ks.no_reg and rhk.ack_kf is not null and rhk.ack_dir is null
		where pd.kode_pegawai = '{$kode_user}'
		group by pd.kode_farm, mf.nama_farm
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	/*Untuk penambahan /pengurangan lain-lain*/
	function simpanLain2($no_reg, $tgl_transaksi, $tipe, $b_jml, $j_jml, $keterangan, $no_berita_acara, $attachment, $attachment_format){

		$sql = <<<QUERY
			insert into rhk_lain2 (no_reg, tgl_transaksi, tipe, b_jml, j_jml, keterangan, no_berita_acara, attachment, attachment_format)
			values ('{$no_reg}', '{$tgl_transaksi}', '{$tipe}', {$b_jml}, {$j_jml}, '{$keterangan}', '{$no_berita_acara}', {$attachment}, '{$attachment_format}')
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        return $stmt->execute();
	}

	function get_accumulated_pakan($noreg, $tgl_transaksi, $is_input = false){
		if($is_input){
		$sql = <<<QUERY
			select sum(brt_pakai) total_pakai
			from rhk_pakan
			where no_reg = '{$noreg}' and tgl_transaksi < '{$tgl_transaksi}'
			group by no_reg
QUERY;
		}else{
			$sql = <<<QUERY
			select sum(brt_pakai) total_pakai
			from rhk_pakan
			where no_reg = '{$noreg}' and tgl_transaksi <= '{$tgl_transaksi}'
			group by no_reg
QUERY;
		}

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_kompensasi_stok($stok, $no_reg, $kode_barang){
		$sql = <<<QUERY
		exec dbo.get_kompensasi_stok :stok, :no_reg, :kode_barang
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
		$stmt->bindParam ( ':stok', $stok );
		$stmt->bindParam ( ':no_reg', $no_reg );
		$stmt->bindParam ( ':kode_barang', $kode_barang );
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function check_kandang_open($noreg){
		$sql = <<<QUERY
			select kode_siklus, count(*) jml_kandang_open
			from kandang_siklus
			where status_siklus = 'O'
			and kode_siklus = (
				select mp.kode_siklus from m_periode mp
				inner join kandang_siklus ks on ks.kode_siklus = mp.kode_siklus
				where ks.no_reg = '{$noreg}'
			)
			group by kode_siklus
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_last_bb_rata($no_reg){
		$sql = <<<QUERY
		select coalesce(convert(numeric(8,3),sum(berat)/convert(numeric(8,3),sum(jumlah))),0)/1000 bb_rata_last from RHK_PENIMBANGAN_BB
		where NO_REG = '{$no_reg}'
		and TGL_TRANSAKSI in (
			select max(TGL_TRANSAKSI)
			from RHK_PENIMBANGAN_BB
			where NO_REG = '{$no_reg}' and  JUMLAH > 0
		)
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_last_bb_rata_pemantauan($no_reg, $tgl_transaksi){
		$sql = <<<QUERY
		select convert(numeric(8,3), coalesce(convert(numeric(8,3),sum(berat)/convert(numeric(8,3),sum(jumlah))),0)/1000) bb_rata_last from RHK_PENIMBANGAN_BB
		where NO_REG = '{$no_reg}'
		and TGL_TRANSAKSI in (
			select max(TGL_TRANSAKSI)
			from RHK_PENIMBANGAN_BB
			where TGL_TRANSAKSI < '{$tgl_transaksi}' and NO_REG = '{$no_reg}' and  JUMLAH > 0
		)
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function get_tanggal_kebutuhan_LHK($tglLHK){
		$sql = <<<QUERY
		SELECT dateadd(day, 2, '{$tglLHK}') AS tgl_kebutuhan
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function belumDropping($noreg,$tgldocin){
		$sql = <<<SQL
		select count(*) ada
		from
			(
				select distinct le.no_reg,le.tgl_kebutuhan
				from lpb l 
				join lpb_e le on l.no_lpb = le.no_lpb and le.no_reg = '{$noreg}' and le.tgl_kebutuhan <= DATEADD(day,2,'{$tgldocin}') and le.tgl_kebutuhan = cast(getdate() as date)
				where l.STATUS_LPB = 'A'
				union all
				select distinct rpp.no_reg,rpp.tgl_kebutuhan 
				from rhk_rekomendasi_pakan rpp
				where rpp.no_reg = '{$noreg}' 
				and rpp.tgl_kebutuhan = cast(getdate() as date)
				and rpp.jml_permintaan > 0
				and datediff(day,'{$tgldocin}',rpp.tgl_kebutuhan) >= 3
			)rekomendasi 
			left join (
				select oke.tgl_kebutuhan,sum(oke.jml_order) jml_order
				,(select sum(jml_on_pick) from movement_d where KETERANGAN2 = '{$noreg}' and NO_REFERENSI = oke.no_order) belum_dropping
				from order_kandang_e oke 
				join ORDER_KANDANG_D okd on oke.NO_ORDER = okd.no_order and oke.no_reg = okd.no_reg 
				where oke.no_reg = '{$noreg}'  and oke.tgl_kebutuhan = cast(getdate() as date)
				group by oke.tgl_kebutuhan,oke.no_order
			)dropping on rekomendasi.tgl_kebutuhan = dropping.tgl_kebutuhan
		where (dropping.belum_dropping > 0 or dropping.tgl_kebutuhan is null)
SQL;
		return $this->db->query($sql)->row_array();		
	}
}
