<?php

class M_retur_sisa_pakan extends CI_Model
{
    private $dbSqlServer;

    public function __construct()
    {
        parent::__construct();
        $this->dbSqlServer = $this->load->database('default', true);
    }

    public function get_today()
    {
        $sql = <<<QUERY
		select getdate() as [today]
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_kandang_siklus($kode_farm)
    {
        $sql = <<<QUERY
		select kandang.kode_kandang as id, kandang.nama_kandang as name, kandang.*, coalesce(realisasi_panen.jml_panen,0) jml_panen from (
			select mp.kode_siklus, ks.kode_farm, ks.kode_kandang, mk.nama_kandang, ks.no_reg,
			  CONVERT(VARCHAR(11),ks.tgl_doc_in,106) tgl_doc_in, max(rhk.tgl_transaksi) tgl_transaksi, coalesce(DATEDIFF(day,  max(lpb.tgl_kebutuhan), max(rhk.TGL_TRANSAKSI)),0) umur,
			ks.flok_bdy, mf.nama_flok, ks.tgl_panen
			from M_PERIODE mp
			inner join KANDANG_SIKLUS ks on mp.KODE_FARM = ks.KODE_FARM and mp.KODE_SIKLUS = ks.KODE_SIKLUS
			inner join M_KANDANG mk on mk.KODE_FARM = ks.KODE_FARM AND mk.KODE_KANDANG = ks.KODE_KANDANG
			left join M_FLOK mf on mf.KODE_FLOK = ks.FLOK_BDY and mf.KODE_FARM = ks.KODE_FARM
		--	inner join RHK rhk on rhk.NO_REG = ks.NO_REG and DATEADD(day, 1, rhk.TGL_TRANSAKSI) = cast(getdate() as date) and rhk.ACK_KF is not null
			inner join RHK rhk on rhk.NO_REG = ks.NO_REG and DATEADD(day, 2, rhk.TGL_TRANSAKSI) = cast(getdate() as date) and rhk.ACK_KF is not null
			left join(
			  select LPB_E.KODE_FARM, LPB_E.NO_REG, MIN(LPB_E.TGL_KEBUTUHAN) TGL_KEBUTUHAN
			  from LPB
			  inner join LPB_E on dbo.LPB.KODE_FARM = dbo.LPB_E.KODE_FARM
			   and LPB_E.NO_LPB = dbo.LPB.NO_LPB and LPB.STATUS_LPB = 'A'
			   group by LPB_E.KODE_FARM, LPB_E.NO_REG
			)lpb on lpb.KODE_FARM = mp.KODE_FARM and lpb.NO_REG = ks.NO_REG
			where mp.STATUS_PERIODE = 'A' and ks.STATUS_SIKLUS = 'O' and mp.KODE_FARM = '{$kode_farm}'
			group by mp.KODE_SIKLUS, ks.KODE_FARM, ks.KODE_KANDANG, mk.NAMA_KANDANG, ks.NO_REG,
			  ks.TGL_DOC_IN, ks.FLOK_BDY, mf.NAMA_FLOK, ks.TGL_PANEN
		)kandang
		left join (
		  select NO_REG, COUNT(*) JML_PANEN
		  from REALISASI_PANEN
		  group by NO_REG
		) realisasi_panen on realisasi_panen.NO_REG = kandang.NO_REG

QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_gudang_tujuan($kode_farm)
    {
        $sql = <<<QUERY
		select kode_farm, kode_gudang, nama_gudang, max_berat, max_kuantitas from m_gudang where kode_farm = '{$kode_farm}'
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_kandang_tujuan($kode_farm, $no_reg)
    {
        $sql = <<<QUERY
		select kandang.kode_kandang as id, kandang.nama_kandang as name, kandang.*, coalesce(realisasi_panen.jml_panen,0) jml_panen from (
			select mp.kode_siklus, ks.kode_farm, ks.kode_kandang, mk.nama_kandang, ks.no_reg,
			  CONVERT(VARCHAR(11),ks.tgl_doc_in,106) tgl_doc_in, max(rhk.tgl_transaksi) tgl_transaksi, coalesce(DATEDIFF(day,  max(lpb.tgl_kebutuhan), max(rhk.TGL_TRANSAKSI)),0) umur,
			ks.flok_bdy, mf.nama_flok, ks.tgl_panen
			from M_PERIODE mp
			inner join KANDANG_SIKLUS ks on mp.KODE_FARM = ks.KODE_FARM and mp.KODE_SIKLUS = ks.KODE_SIKLUS
			inner join M_KANDANG mk on mk.KODE_FARM = ks.KODE_FARM AND mk.KODE_KANDANG = ks.KODE_KANDANG
			left join M_FLOK mf on mf.KODE_FLOK = ks.FLOK_BDY and mf.KODE_FARM = ks.KODE_FARM
			left join RHK rhk on rhk.NO_REG = ks.NO_REG
			left join(
			  select LPB_E.KODE_FARM, LPB_E.NO_REG, MIN(LPB_E.TGL_KEBUTUHAN) TGL_KEBUTUHAN
			  from LPB
			  inner join LPB_E on dbo.LPB.KODE_FARM = dbo.LPB_E.KODE_FARM
			   and LPB_E.NO_LPB = dbo.LPB.NO_LPB and LPB.STATUS_LPB = 'A'
			   group by LPB_E.KODE_FARM, LPB_E.NO_REG
			)lpb on lpb.KODE_FARM = mp.KODE_FARM and lpb.NO_REG = ks.NO_REG
			where mp.STATUS_PERIODE = 'A' and ks.STATUS_SIKLUS = 'O' and mp.KODE_FARM = '{$kode_farm}'
			group by mp.KODE_SIKLUS, ks.KODE_FARM, ks.KODE_KANDANG, mk.NAMA_KANDANG, ks.NO_REG,
			  ks.TGL_DOC_IN, ks.FLOK_BDY, mf.NAMA_FLOK, ks.TGL_PANEN
		)kandang
		left join (
		  select NO_REG, COUNT(*) JML_PANEN
		  from REALISASI_PANEN
		  group by NO_REG
		) realisasi_panen on realisasi_panen.NO_REG = kandang.NO_REG
		where kandang.no_reg != '{$no_reg}' and coalesce(realisasi_panen.jml_panen,0) < 1
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_sisa_pakan($no_reg)
    {
        $sql = <<<QUERY
		select km.no_reg, km.kode_barang, mb.nama_barang, km.tgl_transaksi, km.jml_akhir - coalesce(rt.jml_retur,0) jml_akhir, km.berat_akhir - coalesce(rt.brt_retur,0) berat_akhir, dbo.BENTUK_CONVERTION(mb.bentuk_barang) bentuk_barang,
			coalesce(rt.jml_retur,0)jml_retur, coalesce(brt_retur,0)brt_retur
		from kandang_movement_d km
		inner join m_barang mb on km.KODE_BARANG = mb.kode_barang
		left join (
			select rkd.no_reg, rkd.kode_barang, sum(rkd.jml_retur) jml_retur, sum(brt_retur) brt_retur 
			from RETUR_KANDANG_D rkd
			join RETUR_KANDANG rd on rd.no_reg = rkd.no_reg and rd.no_retur = rkd.no_retur and rd.user_approve is null and rd.keterangan2 is null
			where rkd.no_reg = '{$no_reg}'
			group by rkd.no_reg, rkd.kode_barang
			-- select no_reg, kode_barang, sum(jml_retur) jml_retur, sum(brt_retur) brt_retur from RETUR_KANDANG_D
			-- where no_reg = '{$no_reg}'
			-- group by no_reg, kode_barang
		)rt on rt.no_reg = km.no_reg and rt.kode_barang = km.kode_barang
		where km.no_reg = '{$no_reg}' and km.tgl_buat in (
			select max(tgl_buat) from KANDANG_MOVEMENT_D
			where no_reg = '{$no_reg}'
			group by kode_barang
		)
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_generate_no_retur($no_retur_arr, $no_reg, $tujuan)
    {
        // $sql = <<<QUERY
        // select top 1
        // 'RL/'+'{$no_reg}-'+CONVERT(char, coalesce((reverse(left(reverse(no_retur), charindex('-', reverse(no_retur)) -1))+1),1)) no_retur
        // from retur_kandang
        // where no_reg = '{$no_reg}'
        // order by no_retur desc
        // QUERY;

        // $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        // $stmt->execute();
        // $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // $no_retur = (isset($result["no_retur"]) and !empty($result["no_retur"])) ? $result["no_retur"] : 'RL/'.$no_reg.'-1';

        // return $no_retur;

        foreach ($no_retur_arr as $no) {
            if ($no['asal'] == $no_reg and $no['tujuan'] == $tujuan) {
                return $no['no_retur'];
            }
        }
    }

    public function ubah_retur($no_reg, $no_retur, $kode_barang, $retur_kandang, $retur_kandang_d)
    {
        $kodefarm = $this->session->userdata('kode_farm');
        $tgl_buat = $this->get_today();

        $this->dbSqlServer->trans_begin();
        $this->dbSqlServer->where('no_reg', $no_reg);
        $this->dbSqlServer->where('no_retur', $no_retur);
        $this->dbSqlServer->update('retur_kandang', $retur_kandang);
        log_message('error', $this->db->last_query());
        if ($this->dbSqlServer->affected_rows() > 0) {
            $success = 0;
            for ($i = 0; $i < count($kode_barang); ++$i) {
                $this->dbSqlServer->where('no_reg', $no_reg);
                $this->dbSqlServer->where('no_retur', $no_retur);
                $this->dbSqlServer->where('kode_barang', $kode_barang[$i]);

                $jml = $retur_kandang_d[$i]['jml_retur'];
                $brt = $retur_kandang_d[$i]['brt_retur'];

                $update['jml_retur'] = $jml;
                $update['brt_retur'] = $brt;

                $this->dbSqlServer->update('retur_kandang_d', $update);
                ++$success;
            }

            if ($success == count($kode_barang)) {
                $this->dbSqlServer->trans_commit();

                return true;
            } else {
                $this->dbSqlServer->trans_rollback();

                return false;
            }
        } else {
            return false;
        }
    }

    public function get_generate_no_retur_arr(&$no_retur_arr, $no_reg, $tujuan)
    {
        $temp = '';
        if (count($no_retur_arr) > 0) {
            for ($i = 0; $i < count($no_retur_arr); ++$i) {
                if ($no_retur_arr[$i]['asal'] == $no_reg and $no_retur_arr[$i]['tujuan'] != $tujuan) {
                    $temp = array('asal' => $no_reg, 'tujuan' => $tujuan, 'no' => (($no_retur_arr[$i]['no'] * 1) + 1), 'no_retur' => 'RL/'.$no_reg.'-'.(($no_retur_arr[$i]['no'] * 1) + 1));
                }
            }
        } else {
            $sql = <<<QUERY
			select top 1
			'RL/'+'{$no_reg}-'+CONVERT(char, coalesce((reverse(left(reverse(no_retur), charindex('-', reverse(no_retur)) -1))+1),1)) no_retur,
			CONVERT(char, coalesce((reverse(left(reverse(no_retur), charindex('-', reverse(no_retur)) -1))+1),1)) no
			from retur_kandang
			where no_reg = '{$no_reg}'
			order by no_retur desc
QUERY;

            $stmt = $this->dbSqlServer->conn_id->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $no = (isset($result['no']) and !empty($result['no'])) ? $result['no'] : '1';
            $no_retur = (isset($result['no_retur']) and !empty($result['no_retur'])) ? $result['no_retur'] : 'RL/'.$no_reg.'-1';

            $temp = array('asal' => $no_reg, 'tujuan' => $tujuan, 'no' => $no, 'no_retur' => $no_retur);
        }

        if (!empty($temp)) {
            $no_retur_arr[] = $temp;
        }
    }

    public function simpan_retur($no_reg, $user, $tgl_retur, $tgl_buat, $data)
    {
        $kodefarm = $this->session->userdata('kode_farm');
        $no_retur_arr = array();
        foreach ($data as $d) {
            $this->get_generate_no_retur_arr($no_retur_arr, $no_reg, $d['tujuan']);
        }

        $n_success = 0;
        $this->dbSqlServer->trans_begin();

        $no_retur_temp = array();
        $inserted_no_retur = array();
        $id = 0;
        foreach ($data as $d) {
            $tgl_buat = $this->get_today();
            $no_retur = $this->get_generate_no_retur($no_retur_arr, $no_reg, $d['tujuan']);
            $no_retur = trim($no_retur);
            $tuj_retur = trim($d['tujuan']);

            $retur_kandang = array(
                'no_retur' => $no_retur,
                'no_reg' => $no_reg,
                'tgl_retur' => $tgl_retur,
                'keterangan1' => $d['tujuan'],
                'tgl_buat' => $tgl_buat['today'],
                'tgl_approve' => null,
                'user_buat' => $user,
            );

            $retur_kandang_d = array(
                'no_retur' => $no_retur,
                'tgl_retur' => $tgl_retur,
                'no_reg' => $no_reg,
                'kode_barang' => $d['pakan'],
                'jml_retur' => $d['sak'],
                'brt_retur' => $d['berat'],
                'tgl_buat' => $tgl_buat['today'],
                'user_buat' => $user,
            );

            $success = false;
            if (count($inserted_no_retur) > 0) {
                if (in_array($no_retur, $inserted_no_retur)) {
                    $success = true;
                } else {
                    $inserted_no_retur[] = $no_retur;
                    $this->dbSqlServer->insert('retur_kandang', $retur_kandang);
                    $this->dbSqlServer->insert('retur_kandang_d', $retur_kandang_d);
                }
            } else {
                $inserted_no_retur[] = $no_retur;
                $this->dbSqlServer->insert('retur_kandang', $retur_kandang);
                $this->dbSqlServer->insert('retur_kandang_d', $retur_kandang_d);
            }
            ++$n_success;
        }

        if ($n_success == count($data)) {
            $this->dbSqlServer->trans_commit();

            return $inserted_no_retur;
        } else {
            $this->dbSqlServer->trans_rollback();

            return false;
        }
    }

    public function get_retur_sisa_pakan($kode_farm, $tgl_awal = null, $tgl_akhir = null, $no_retur = null)
    {
        $filter = '';
        if (isset($tgl_awal) and isset($tgl_akhir)) {
            $filter = "and rhk.tgl_retur >= '".$tgl_awal."' and rhk.tgl_retur <= '".$tgl_awal."'";
        } elseif (isset($tgl_awal) and !isset($tgl_akhir)) {
            $filter = "and rhk.tgl_retur >= '".$tgl_awal."'";
        } elseif (!isset($tgl_awal) and isset($tgl_akhir)) {
            $filter = "and rhk.tgl_retur <= '".$tgl_awal."'";
        } elseif (isset($no_retur)) {
            $filter = "and rk.no_retur = '".$no_retur."'";
        } else {
            $filter = '';
        }
        $level_user = $this->session->userdata('level_user');
        $filterGudang = '';
        if ($level_user == 'AG') {
            $filterGudang = ' and rk.keterangan1 in (select kode_gudang from m_gudang where kode_farm = \''.$kode_farm.'\') and rk.keterangan2 is null';
        }
        $sql = <<<QUERY
		select *
		,
		(
		  select top 1 kmd.jml_akhir
		  from kandang_movement_d kmd
		  where kmd.no_reg = sub_a.no_reg and kmd.kode_barang = sub_a.kode_barang
		  order by kmd.tgl_buat desc
		)jml_akhir
		from (
			select ks.kode_farm, mkg.nama_farm, ks.kode_kandang, mk.nama_kandang, rk.no_retur, rk.no_reg, coalesce(mkg.nama_kandang,'') tujuan_retur,
				rk.keterangan1 as kode_tujuan_retur,
				rkd.tgl_retur tgl_retur_ori,
				substring(convert (varchar, rkd.tgl_retur, 113),1,len(convert (varchar, rkd.tgl_retur, 113))-13) tgl_retur,
				substring(convert (varchar, rkd.tgl_on_putaway, 113),1,len(convert (varchar, rkd.tgl_on_putaway, 113))-7) tgl_on_putaway,
				substring(convert (varchar, rkd.tgl_putaway, 113),1,len(convert (varchar, rkd.tgl_putaway, 113))-7) tgl_putaway,
				rkd.kode_barang, mb.nama_barang, dbo.BENTUK_CONVERTION(mb.bentuk_barang) bentuk_barang, rkd.jml_on_retur, rkd.brt_on_retur, rkd.jml_retur, rkd.brt_retur, rkd.jml_putaway, rkd.brt_putaway,
				substring(convert (varchar, rk.tgl_approve, 113),1,len(convert (varchar, rk.tgl_approve, 113))-7) tgl_approve,
				substring(convert (varchar, rk.tgl_terima, 113),1,len(convert (varchar, rk.tgl_terima, 113))-7) tgl_terima,
				rk.USER_BUAT, mp1.NAMA_PEGAWAI nama_buat, rk.user_approve, mp2.nama_pegawai nama_approve, rk.user_terima, mp3.NAMA_PEGAWAI nama_terima
			,rkd2.jml_retur_pakai,rkd2.brt_retur_pakai
			,case when ks2.no_reg is not null then 'kandang' else 'gudang' end tipe_retur
			,case when tgl_approve is null then '1' else '0' end can_edit
            ,rk.keterangan2
            ,substring(convert (varchar, rk.tgl_buat, 113),1,len(convert (varchar, rk.tgl_buat, 113))-7) tgl_buat
			,msb.kode_strain strain
			from kandang_siklus ks
			inner join m_std_budidaya msb on msb.kode_std_budidaya = ks.kode_std_budidaya
			inner join retur_kandang rk on rk.no_reg = ks.no_reg
			inner join m_kandang mk on mk.kode_kandang = ks.kode_kandang and mk.kode_farm = ks.kode_farm
			left join retur_kandang_d rkd on rkd.no_retur = rk.no_retur and rkd.no_reg = rk.no_reg
			inner join m_barang mb on mb.kode_barang = rkd.kode_barang
			left join M_PEGAWAI mp1 on mp1.KODE_PEGAWAI = rk.USER_BUAT
			left join M_PEGAWAI mp2 on mp2.KODE_PEGAWAI = rk.USER_approve
			left join M_PEGAWAI mp3 on mp3.KODE_PEGAWAI = rk.USER_terima
			left join M_PERIODE mp4 on mp4.KODE_SIKLUS = ks.kode_siklus and mp4.KODE_FARM = ks.kode_farm
			left join (
				select distinct ks.kode_farm, mf.nama_farm , ks.no_reg, ks.kode_kandang, mk.nama_kandang
				from kandang_siklus ks
				inner join m_farm mf on mf.kode_farm = ks.kode_farm
				inner join m_kandang mk on mk.kode_kandang = ks.kode_kandang
					and mk.kode_farm = ks.kode_farm
				union
				select mg.kode_farm, mf.nama_farm, mg.kode_gudang, '' no_reg, mg.nama_gudang
				from m_gudang mg
				inner join m_farm mf on mf.kode_farm = mg.kode_farm
			)mkg on mkg.kode_farm = ks.kode_farm and mkg.no_reg = rk.keterangan1
			left join(
				select no_reg, kode_barang, sum(jml_retur) jml_retur_pakai, sum(brt_retur) brt_retur_pakai
				from retur_kandang_d
				group by no_reg, kode_barang
			)rkd2 on rkd2.no_reg = ks.no_reg and rkd2.kode_barang = rkd.kode_barang
			left join kandang_siklus ks2 on ks2.no_reg = rk.keterangan1
			where --ks.status_siklus = 'C' and
			mp4.STATUS_PERIODE = 'A'
			and ks.kode_farm = '{$kode_farm}'
            $filter
            {$filterGudang}
		)sub_a
		order by nama_approve
QUERY;
        //log_message('error', $sql);
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function proses_persetujuan_retur_sisa($no_retur, $user, $level_user, $kodefarm)
    {
        // $this->dbSqlServer->trans_begin();
        if ($level_user == 'KF') {
            $sql = <<<QUERY
			update retur_kandang_d set
				jml_on_putaway = jml_retur
				,brt_on_putaway = brt_retur
				,tgl_on_putaway = getdate()
				
				,tgl_ubah = getdate()
				,user_ubah = '{$user}'
			where no_retur = '{$no_retur}'
QUERY;
        } elseif ($level_user == 'AG') {
            $sql = <<<QUERY
			update retur_kandang_d set
				jml_putaway = jml_retur
				,brt_putaway = brt_retur
				,tgl_putaway = getdate()
				,tgl_ubah = getdate()
				,user_ubah = '{$user}'
			where no_retur = '{$no_retur}'
QUERY;
        } else {
            $sql = <<<QUERY

QUERY;
        }

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        if ($stmt->execute()) {
            $status = false;
            if ($level_user == 'KF') {
                $sql = <<<QUERY
				update retur_kandang set
				tgl_approve = getdate()
				,user_approve = '{$user}'
				where no_retur = '{$no_retur}'
QUERY;
                $stmt = $this->dbSqlServer->conn_id->prepare($sql);
                $stmt->execute();

                $status = true;
            } elseif ($level_user == 'AG') {
                $sql = <<<QUERY
				update retur_kandang set
				tgl_terima = getdate()
				,user_terima = '{$user}'
				where no_retur = '{$no_retur}'
QUERY;
                $stmt = $this->dbSqlServer->conn_id->prepare($sql);
                $stmt->execute();

                $status = true;
            } else {
                $sql = <<<QUERY

QUERY;
            }

            if ($status) {
                //Run Procedure
                log_message('error', 'sampai sini procedure');
                $sql = <<<QUERY
					exec dbo.LHK_PERSETUJUAN_RETUR '{$no_retur}', '{$user}', '{$level_user}'
QUERY;
                $stmt = $this->dbSqlServer->conn_id->prepare($sql);
                log_message('error', $sql);
                if ($stmt->execute()) {
                    $sql = <<<QUERY
					select nama_pegawai from m_pegawai where kode_pegawai = '{$user}'
QUERY;
                    $stmt = $this->dbSqlServer->conn_id->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    return $result['nama_pegawai'];
                } else {
                    return 'failed';
                }
            }

            return 'failed';
        } else {
            return 'failed';
        }
    }

    /*end  of baru*/

    public function get_retur_pakan($kode_farm, $tgl_awal = null, $tgl_akhir = null)
    {
        $filter = '';
        if (isset($tgl_awal) and isset($tgl_akhir)) {
            $filter = "and rhk.tgl_transaksi >= '".$tgl_awal."' and rhk.tgl_transaksi <= '".$tgl_awal."'";
        } elseif (isset($tgl_awal) and !isset($tgl_akhir)) {
            $filter = "and rhk.tgl_transaksi >= '".$tgl_awal."'";
        } elseif (!isset($tgl_awal) and isset($tgl_akhir)) {
            $filter = "and rhk.tgl_transaksi <= '".$tgl_awal."'";
        } else {
            $filter = '';
        }

        $sql = <<<QUERY
		select substring(convert (varchar, rhk.tgl_transaksi, 113), 1, len(convert (varchar, rhk.tgl_transaksi, 113))) [tgl_tutupsiklus],
			ks.kode_farm, ks.kode_kandang, mk.nama_kandang, rk.no_retur, rk.no_reg,
			substring(convert (varchar, rkd.tgl_retur, 113),1,len(convert (varchar, rkd.tgl_retur, 113))-7) tgl_retur,
			substring(convert (varchar, rkd.tgl_on_putaway, 113),1,len(convert (varchar, rkd.tgl_on_putaway, 113))-7) tgl_on_putaway,
			substring(convert (varchar, rkd.tgl_putaway, 113),1,len(convert (varchar, rkd.tgl_putaway, 113))-7) tgl_putaway,
			rkd.kode_barang, mb.nama_barang, rkd.jml_on_retur, rkd.brt_on_retur, rkd.jml_retur, rkd.brt_retur, rkd.jml_putaway, rkd.brt_putaway,
			substring(convert (varchar, rk.tgl_approve, 113),1,len(convert (varchar, rk.tgl_approve, 113))-7) tgl_approve,
			substring(convert (varchar, rk.tgl_terima, 113),1,len(convert (varchar, rk.tgl_terima, 113))-7) tgl_terima,
			rk.USER_BUAT, mp1.NAMA_PEGAWAI nama_buat, rk.user_approve, mp2.nama_pegawai nama_approve, rk.user_terima, mp3.NAMA_PEGAWAI nama_terima
		from kandang_siklus ks
		inner join (
		  select a.*
		  from rhk a
		  where tgl_transaksi in (
			select top 1 b.tgl_transaksi
			from rhk b
			where b.no_reg = a.no_reg
			order by tgl_transaksi desc
		  )
		) rhk on rhk.no_reg = ks.no_reg
		inner join retur_kandang rk on rk.no_reg = ks.no_reg
		inner join m_kandang mk on mk.kode_kandang = ks.kode_kandang and mk.kode_farm = ks.kode_farm
		left join retur_kandang_d rkd on rkd.no_retur = rk.no_retur and rkd.no_reg = rk.no_reg
		inner join m_barang mb on mb.kode_barang = rkd.kode_barang
		left join M_PEGAWAI mp1 on mp1.KODE_PEGAWAI = rk.USER_BUAT
		left join M_PEGAWAI mp2 on mp2.KODE_PEGAWAI = rk.USER_approve
		left join M_PEGAWAI mp3 on mp3.KODE_PEGAWAI = rk.USER_terima
		left join M_PERIODE mp4 on mp4.KODE_SIKLUS = ks.kode_siklus and mp4.KODE_FARM = ks.kode_farm
		--where ks.status_siklus = 'C' and mp4.STATUS_PERIODE = 'A'
		and ks.kode_farm = '{$kode_farm}'
		$filter
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_retur_pakan_detail($no_retur, $no_reg)
    {
        $sql = <<<QUERY
		select a.NO_RETUR, a.NO_REG, b.KODE_BARANG, c.NAMA_BARANG, dbo.BENTUK_CONVERTION(c.BENTUK_BARANG) BENTUK_BARANG,
		coalesce(b.JML_ON_RETUR, b.JML_RETUR) JML, coalesce(b.BRT_ON_RETUR, b.BRT_RETUR) BRT,
		substring(convert (varchar, b.tgl_on_putaway, 113),1,len(convert (varchar, b.tgl_on_putaway, 113))-7) TGL_ON_PUTAWAY,
		substring(convert (varchar, b.TGL_RETUR, 113),1,len(convert (varchar, b.TGL_RETUR, 113))-7) tgl_retur,
		substring(convert (varchar, a.TGL_APPROVE, 113),1,len(convert (varchar, a.TGL_APPROVE, 113))-7) tgl_approve,
		substring(convert (varchar, a.TGL_TERIMA, 113),1,len(convert (varchar, a.TGL_TERIMA, 113))-7) tgl_terima,
		mp1.NAMA_PEGAWAI user_buat, mp2.NAMA_PEGAWAI user_approve, mp3.NAMA_PEGAWAI user_terima
		from RETUR_KANDANG a
		inner join RETUR_KANDANG_D b on b.NO_RETUR = a.NO_RETUR AND b.NO_REG = a.NO_REG
		inner join M_BARANG c on c.KODE_BARANG = b.KODE_BARANG
		left join M_PEGAWAI mp1 on mp1.KODE_PEGAWAI = a.USER_BUAT
		left join M_PEGAWAI mp2 on mp2.KODE_PEGAWAI = a.USER_approve
		left join M_PEGAWAI mp3 on mp3.KODE_PEGAWAI = a.USER_terima
		where a.NO_RETUR = '{$no_retur}' and a.NO_REG = '{$no_reg}'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function proses_pengajuan_retur($no_retur, $no_reg, $user)
    {
        $sql = <<<QUERY
		update retur_kandang_d set
			jml_retur = jml_on_retur,
			brt_retur = brt_on_retur,
			jml_on_retur = null,
			brt_on_retur = null,
			tgl_retur = getdate(),
			tgl_ubah = getdate(),
			user_ubah = '{$user}'
		where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function proses_persetujuan_retur($no_retur, $no_reg, $user, $level_user)
    {
        if ($level_user == 'KF') {
            $sql = <<<QUERY
			update retur_kandang_d set
				jml_on_putaway = jml_retur,
				brt_on_putaway = brt_retur,
				tgl_on_putaway = getdate(),
				tgl_ubah = getdate(),
				user_ubah = '{$user}'
			where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;
        } elseif ($level_user == 'AG') {
            $sql = <<<QUERY
			update retur_kandang_d set
				jml_putaway = jml_retur,
				brt_putaway = brt_retur,
				tgl_putaway = getdate(),
				tgl_ubah = getdate(),
				user_ubah = '{$user}'
			where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;
        } else {
            $sql = <<<QUERY

QUERY;
        }

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        if ($stmt->execute()) {
            $status = false;
            if ($level_user == 'KF') {
                $sql = <<<QUERY
				update retur_kandang set
				tgl_approve = getdate(),
				keterangan1 = '{$no_retur}',
				user_approve = '{$user}'
				where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;
                $stmt = $this->dbSqlServer->conn_id->prepare($sql);
                $stmt->execute();

                $status = true;
            } elseif ($level_user == 'AG') {
                $sql = <<<QUERY
				update retur_kandang set
				tgl_terima = getdate(),
				keterangan1 = '{$no_retur}',
				user_terima = '{$user}'
				where no_retur = '{$no_retur}' and no_reg = '{$no_reg}'
QUERY;
                $stmt = $this->dbSqlServer->conn_id->prepare($sql);
                $stmt->execute();

                $status = true;
            } else {
                $sql = <<<QUERY

QUERY;
            }

            if ($status) {
                $sql = <<<QUERY
				select nama_pegawai from m_pegawai where kode_pegawai = '{$user}'
QUERY;
                $stmt = $this->dbSqlServer->conn_id->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                return $result['nama_pegawai'];
            }

            return 'failed';
        } else {
            return 'failed';
        }
    }
}
