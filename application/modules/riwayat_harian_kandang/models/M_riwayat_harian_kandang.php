<?php

class M_riwayat_harian_kandang extends CI_Model
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

    //---------added 31/10/2015-----------
    public function get_lhk($noreg, $tgl_lhk)
    {
        $sql = <<<QUERY
        select * from rhk where no_reg = '{$noreg}' and tgl_transaksi = '{$tgl_lhk}'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_lhk_pakan($noreg, $tgl_lhk)
    {
        $sql = <<<QUERY
        select M_BARANG.NAMA_BARANG, coalesce(qry.BRT_AKHIR, 0) BRT_AWAL, coalesce(qry.JML_AKHIR, 0) JML_AWAL, rhk_pakan.*
        from rhk_pakan
        inner join M_BARANG on RHK_PAKAN.KODE_BARANG = M_BARANG.KODE_BARANG
        left join (
            select M_BARANG.NAMA_BARANG, rhk_pakan.* from rhk_pakan
            inner join M_BARANG on RHK_PAKAN.KODE_BARANG = M_BARANG.KODE_BARANG
            where rhk_pakan.no_reg = '{$noreg}' and rhk_pakan.tgl_transaksi = (select left(convert(varchar,DATEADD(DAY, -1, '{$tgl_lhk}'),120), 10))
        )qry on qry.KODE_BARANG = rhk_pakan.KODE_BARANG and qry.JENIS_KELAMIN = rhk_pakan.JENIS_KELAMIN
        where rhk_pakan.no_reg = '{$noreg}' and rhk_pakan.tgl_transaksi = '{$tgl_lhk}'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_lhk_vaksin_obat($noreg, $tgl_lhk)
    {
        $sql = <<<QUERY
        select * from (
            select M_BARANG.NAMA_BARANG, M_BARANG.JENIS_BARANG,  RHK_VAKSIN.* from RHK_VAKSIN
            inner join M_BARANG on RHK_VAKSIN.KODE_BARANG = M_BARANG.KODE_BARANG
            where RHK_VAKSIN.no_reg = '{$noreg}' and RHK_VAKSIN.tgl_transaksi = '{$tgl_lhk}'
            ) as s
            pivot(
                sum (berat_pakai)
                for jenis_kelamin in (B,J)
            )as pv
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_lhk_produksi($noreg, $tgl_lhk)
    {
        $sql = <<<QUERY
            select * from rhk_produksi
            where no_reg = '{$noreg}' and tgl_transaksi = '{$tgl_lhk}'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_lhk_penimbangan($noreg, $tgl_lhk, $umur_minggu)
    {
        $sql = <<<QUERY
            select rpb.*, subqry.TARGET_BB
            from RHK_PENIMBANGAN_BB rpb
            left join (
                select KODE_STD_BREEDING, JENIS_KELAMIN, STD_UMUR, TARGET_BB
                from M_STD_BREEDING
                where STD_UMUR = {$umur_minggu}
                and KODE_STD_BREEDING in (
                    select KODE_STD_BREEDING_B KODE_STD_BREEDING
                    from KANDANG_SIKLUS
                    where NO_REG = '{$noreg}'
                    union
                    select KODE_STD_BREEDING_J
                    from KANDANG_SIKLUS
                    where NO_REG = '{$noreg}'
                )
            )subqry on subqry.JENIS_KELAMIN = rpb.JENIS_KELAMIN
            where rpb.NO_REG = '{$noreg}' and rpb.TGL_TRANSAKSI = '{$tgl_lhk}'
            order by rpb.NO_REG, rpb.TGL_TRANSAKSI, rpb.JENIS_KELAMIN, rpb.BERAT
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //-----------------------------------------

    public function get_kandang_siklus($kode_farm)
    {
        $sql = <<<QUERY
        select a.kode_kandang as id, b.nama_kandang as name, a.kode_siklus, a.no_reg, a.kode_flok, d.nama_flok, a.kode_farm, a.kode_std_breeding_j, a.kode_std_breeding_b, a.jml_betina, a.jml_jantan, a.jml_betina_terima, a.jml_jantan_terima, a.umur_afkir, a.tgl_afkir, a.tgl_doc_in, a.tipe_kandang, a.tipe_lantai, a.luas_kandang_betina, a.luas_kandang_jantan, a.status_siklus, b.kode_verifikasi
        from kandang_siklus a
        inner join m_kandang b on a.kode_kandang = b.kode_kandang
        inner join (
            select no_reg, keterangan1
            from kandang_movement_d
            where keterangan1 = 'PENERIMAAN KANDANG'
            group by no_reg, keterangan1
        ) e on e.no_reg = a.no_reg
        inner join m_farm c on a.kode_farm = c.kode_farm and d.grup_farm = 'BRD'
        left join m_flok d on d.kode_flok = a.kode_flok
        where a.kode_farm = '{$kode_farm}' and b.kode_farm = '{$kode_farm}' and a.status_siklus = 'O'
        group by a.kode_kandang, b.nama_kandang, a.kode_siklus, a.no_reg, a.kode_flok, d.nama_flok, a.kode_farm, a.kode_std_breeding_j, a.kode_std_breeding_b, a.jml_betina, a.jml_jantan, a.jml_betina_terima, a.jml_jantan_terima, a.umur_afkir, a.tgl_afkir, a.tgl_doc_in, a.tipe_kandang, a.tipe_lantai, a.luas_kandang_betina, a.luas_kandang_jantan, a.status_siklus, b.kode_verifikasi
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detail_penimbangan_per_sekat($noreg, $tgllhk)
    {
        $sql = <<<QUERY
        SELECT NO_REG, TGL_TRANSAKSI, SEKAT, JUMLAH, BERAT, KETERANGAN
        FROM RHK_PENIMBANGAN_BB
        WHERE NO_REG='{$noreg}' AND TGL_TRANSAKSI LIKE '%{$tgllhk}%';
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detail_populasi($noreg, $tgllhk)
    {
        $sql = <<<QUERY
        SELECT NO_REG, TGL_TRANSAKSI, C_MATI, C_AFKIR, TGL_BUAT, USER_BUAT, ACK_KF
        FROM RHK
        WHERE NO_REG='{$noreg}' AND TGL_TRANSAKSI LIKE '%{$tgllhk}%';
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detail_pakan($noreg, $tgllhk)
    {
        /*
        $sql = <<<QUERY
        SELECT pakan.NO_REG, pakan.TGL_TRANSAKSI, pakan.JENIS_KELAMIN, barang.KODE_BARANG, barang.NAMA_BARANG, pakan.JML_PAKAI, isnull(rekomendasi.JML_PERMINTAAN,0) as JML_PERMINTAAN
        FROM RHK_PAKAN pakan
        LEFT OUTER JOIN rhk_rekomendasi_pakan rekomendasi ON rekomendasi.NO_REG = pakan.NO_REG AND rekomendasi.tgl_transaksi=pakan.TGL_TRANSAKSI AND rekomendasi.KODE_BARANG=pakan.KODE_BARANG
        INNER JOIN M_BARANG barang ON barang.KODE_BARANG = pakan.KODE_BARANG
        WHERE pakan.NO_REG='{$noreg}' AND pakan.TGL_TRANSAKSI LIKE '%{$tgllhk}%';
QUERY;
*/
        /** agar bisa tampil semua, jika ada rekomendasi pakan yang belum masuk rhk_pakan */
        $sql = <<<QUERY
        select x.NO_REG, x.TGL_TRANSAKSI, 'C' as JENIS_KELAMIN, barang.KODE_BARANG, barang.NAMA_BARANG, isnull(pakan.JML_PAKAI,0) as JML_PAKAI, isnull(rekomendasi.JML_PERMINTAAN,0) as JML_PERMINTAAN
        from 
        (select distinct KODE_BARANG, KETERANGAN2 as NO_REG, '{$tgllhk}' as TGL_TRANSAKSI from movement_d where KETERANGAN2 = '{$noreg}')x
        inner JOIN M_BARANG barang ON barang.KODE_BARANG = x.KODE_BARANG
        left join rhk_pakan pakan on pakan.KODE_BARANG = x.KODE_BARANG and pakan.NO_REG = '{$noreg}'
        and pakan.TGL_TRANSAKSI = '{$tgllhk}'
        LEFT OUTER JOIN rhk_rekomendasi_pakan rekomendasi ON rekomendasi.NO_REG = x.NO_REG AND rekomendasi.tgl_transaksi=x.TGL_TRANSAKSI AND rekomendasi.KODE_BARANG=x.KODE_BARANG		                 
QUERY;        
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_max_rowspan($param1, $param2, $param3)
    {
        $sql = <<<QUERY
        select max(rowspan) as max_rowspan
        from (
            SELECT {$param1} as rowspan
            UNION
            select {$param2} as rowspan
            UNION
            select {$param3} as rowspan
        ) tbl_rowspan
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_header_finger_lhk($noreg, $tgllhk)
    {
        $sql = <<<QUERY
        SELECT siklus.NO_REG, siklus.KODE_KANDANG, siklus.FLOK_BDY, siklus.TGL_DOC_IN, CAST('{$tgllhk}' AS DATE) AS tgl_lhk,
        DATEDIFF(DAY, siklus.TGL_DOC_IN, CAST('{$tgllhk}' AS DATE)) AS umur_hari, plotting.PENGAWAS, pegawai.NAMA_PEGAWAI
        FROM KANDANG_SIKLUS siklus
        INNER JOIN (
            SELECT NO_REG, PENGAWAS 
            FROM M_PLOTING_PELAKSANA 
            GROUP BY NO_REG, PENGAWAS
        ) plotting ON plotting.NO_REG = siklus.NO_REG
        INNER JOIN M_PEGAWAI pegawai ON pegawai.KODE_PEGAWAI = plotting.PENGAWAS
        WHERE siklus.NO_REG='{$noreg}'
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //get populasi pakan RHK
    public function get_populasi_pakan_rhk($noreg)
    {
        $sql = <<<QUERY
        SELECT * FROM (
            SELECT c_awal, c_jumlah, c_mati, c_afkir, ROW_NUMBER () OVER (ORDER BY TGL_TRANSAKSI DESC) AS RowNum
            FROM RHK
            where NO_REG = '{$noreg}'
        ) formatRHK 
        WHERE RowNum = 2
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //BEGIN FUNCTION CETAK FORM LHK
    //cetak form LHK , tambahan DC tgl 03 Juli 2018 get cetakan form LHK
    public function get_cetak_form_lhk($kode_farm, $kode_pegawai = null)
    {
        $joinPengawas = '';
        if (!empty($kode_pegawai)) {
            $joinPengawas = ' JOIN M_PLOTING_PELAKSANA mpp ON mpp.NO_REG = siklus.NO_REG AND mpp.PENGAWAS = \''.$kode_pegawai.'\'';
        }
/*        $sql = <<<QUERY
        SELECT distinct siklus.NO_REG
                , CAST(getdate() AS DATE) AS tgl_lhk
                , siklus.FLOK_BDY AS flock
                , siklus.KODE_KANDANG AS kandang
                , siklus.TGL_DOC_IN
                , DATEDIFF(DAY, siklus.TGL_DOC_IN, getdate()) AS umur_hari
                , RHK_CETAK.TGL_CETAK AS status_cetak
                , case when ORDER_KANDANG_E.NO_REG is not null then ORDER_KANDANG_E.NO_REG 
                -- periksa apakah ada lpb_e ada atau tidak, jika tidak ada maka bisa dropping                   
                  ELSE CASE WHEN (SELECT count(no_reg) FROM lpb_e JOIN lpb ON lpb.NO_LPB = lpb_e.NO_LPB  AND lpb.STATUS_LPB = 'A' WHERE NO_REG = siklus.NO_REG AND lpb_e.TGL_KEBUTUHAN = CAST(getdate() AS DATE) ) = 0 THEN '1'
                        ELSE NULL end
                end status_dropping_pakan,
                case when RHK.NO_REG is not null then 1
                else 0
                end status_entri_lhk,
                isnull(rhk.JML_REKOMENDASI,0) as JML_REKOMENDASI,
                CAST(getdate()-1 AS DATE) AS tgl_lhk_sebelum
        FROM kandang_siklus siklus
        LEFT OUTER JOIN RHK_CETAK ON RHK_CETAK.NO_REG = siklus.NO_REG  AND RHK_CETAK.TGL_TRANSAKSI = CAST(getdate() AS DATE)
        LEFT OUTER JOIN (
            SELECT oke.NO_REG, TGL_KEBUTUHAN
            FROM ORDER_KANDANG_E oke
            JOIN ORDER_KANDANG_D okd ON okd.NO_ORDER = oke.NO_ORDER AND oke.NO_REG = okd.NO_REG AND okd.KODE_FARM = '{$kode_farm}'
            AND okd.STATUS_ORDER = 'C'
            JOIN KANDANG_SIKLUS ks ON ks.NO_REG = okd.NO_REG AND ks.STATUS_SIKLUS = 'O'
            WHERE oke.TGL_KEBUTUHAN = CAST(getdate() AS date)
            GROUP BY oke.NO_REG, oke.TGL_KEBUTUHAN
        ) ORDER_KANDANG_E ON ORDER_KANDANG_E.NO_REG=siklus.NO_REG 
        LEFT OUTER JOIN (
            SELECT RHK.NO_REG, RHK.TGL_TRANSAKSI, isnull(sum(rekomendasi.JML_REKOMENDASI),0) AS JML_REKOMENDASI
            FROM RHK
            LEFT OUTER JOIN rhk_rekomendasi_pakan rekomendasi ON rekomendasi.NO_REG = RHK.NO_REG AND rekomendasi.tgl_transaksi=RHK.TGL_TRANSAKSI
            GROUP BY RHK.NO_REG, RHK.TGL_TRANSAKSI
        ) rhk on rhk.NO_REG = siklus.NO_REG and rhk.TGL_TRANSAKSI = CAST(getdate()-1 AS DATE)
        {$joinPengawas}
        where siklus.KODE_FARM = '{$kode_farm}' and siklus.STATUS_SIKLUS = 'O'
        AND DATEDIFF(DAY, siklus.TGL_DOC_IN, getdate()) >= 1
QUERY;
*/
        $sql = <<<QUERY
        declare @maxCetak integer =  (select value from SYS_CONFIG_GENERAL where KODE_FARM = '{$kode_farm}' and KODE_CONFIG = '_max_cetak_rhk')
        SELECT distinct siklus.NO_REG
                , CAST(getdate() + @maxCetak AS DATE) AS tgl_lhk
                , siklus.FLOK_BDY AS flock
                , siklus.KODE_KANDANG AS kandang
                , siklus.TGL_DOC_IN
                , DATEDIFF(DAY, siklus.TGL_DOC_IN, getdate() + @maxCetak) AS umur_hari
                , RHK_CETAK.TGL_CETAK AS status_cetak
                , case when ORDER_KANDANG_E.NO_REG is not null then ORDER_KANDANG_E.NO_REG 
                -- periksa apakah ada lpb_e ada atau tidak, jika tidak ada maka bisa dropping                   
                  ELSE CASE WHEN (SELECT count(no_reg) FROM lpb_e JOIN lpb ON lpb.NO_LPB = lpb_e.NO_LPB  AND lpb.STATUS_LPB = 'A' WHERE NO_REG = siklus.NO_REG AND lpb_e.TGL_KEBUTUHAN = CAST(getdate() + @maxCetak AS DATE) ) = 0 THEN '1'
                        ELSE NULL end
                end status_dropping_pakan,
                case when RHK.NO_REG is not null then 1
                else 0
                end status_entri_lhk,
                isnull(rhk.JML_REKOMENDASI,0) as JML_REKOMENDASI,
                CAST(getdate() + @maxCetak -1 AS DATE) AS tgl_lhk_sebelum
        FROM kandang_siklus siklus
        LEFT OUTER JOIN RHK_CETAK ON RHK_CETAK.NO_REG = siklus.NO_REG  AND RHK_CETAK.TGL_TRANSAKSI = CAST(getdate() + @maxCetak AS DATE)
        LEFT OUTER JOIN (
            SELECT oke.NO_REG, TGL_KEBUTUHAN
            FROM ORDER_KANDANG_E oke
            JOIN ORDER_KANDANG_D okd ON okd.NO_ORDER = oke.NO_ORDER AND oke.NO_REG = okd.NO_REG AND okd.KODE_FARM = '{$kode_farm}'
            AND okd.STATUS_ORDER = 'C'
            JOIN KANDANG_SIKLUS ks ON ks.NO_REG = okd.NO_REG AND ks.STATUS_SIKLUS = 'O'
            WHERE oke.TGL_KEBUTUHAN = CAST(getdate() + @maxCetak AS date)
            GROUP BY oke.NO_REG, oke.TGL_KEBUTUHAN
        ) ORDER_KANDANG_E ON ORDER_KANDANG_E.NO_REG=siklus.NO_REG 
        LEFT OUTER JOIN (
            SELECT RHK.NO_REG, RHK.TGL_TRANSAKSI, isnull(sum(rekomendasi.JML_REKOMENDASI),0) AS JML_REKOMENDASI
            FROM RHK
            LEFT OUTER JOIN rhk_rekomendasi_pakan rekomendasi ON rekomendasi.NO_REG = RHK.NO_REG AND rekomendasi.tgl_transaksi=RHK.TGL_TRANSAKSI
            GROUP BY RHK.NO_REG, RHK.TGL_TRANSAKSI
        ) rhk on rhk.NO_REG = siklus.NO_REG and rhk.TGL_TRANSAKSI = CAST(getdate() + @maxCetak - 1 AS DATE)
        {$joinPengawas}
        where siklus.KODE_FARM =  '{$kode_farm}' and siklus.STATUS_SIKLUS = 'O'
        AND DATEDIFF(DAY, siklus.TGL_DOC_IN, getdate()) >= 0
QUERY;

        
        //log_message('error',$sql);
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //get detail cetak form LHK pada saat print dilakukan berdasar nomor
    public function get_detail_print_lhk($barcode)
    {
        $sql = <<<QUERY
        SELECT *, DATEDIFF(DAY, tblDtl.TGL_DOC_IN, isnull(tblDtl.tgl_lhk,dateadd(day, 1, tblDtl.TGL_DOC_IN))) AS umur_hari
        FROM (
            SELECT rc.NO_REG, siklus.TGL_DOC_IN, siklus.KODE_KANDANG, rc.tgl_transaksi AS tgl_lhk, rc.USER_CETAK
            FROM RHK_CETAK rc
            LEFT OUTER JOIN KANDANG_SIKLUS siklus ON siklus.NO_REG = rc.NO_REG
            WHERE rc.barcode = '{$barcode}'
        --  GROUP BY rc.NO_REG, siklus.TGL_DOC_IN, siklus.KODE_KANDANG, siklus.KODE_FARM
        ) tblDtl
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //get nama pegawai berdasarkan kode pegawai
    public function get_nama_pegawai($kode_pegawai)
    {
        $sql = <<<QUERY
        SELECT *
        FROM M_PEGAWAI 
        where status_pegawai = 'A' and kode_pegawai = '{$kode_pegawai}'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tanggal_kebutuhan_LHK($tglLHK)
    {
        $sql = <<<QUERY
        SELECT dateadd(day, 2, '{$tglLHK}') AS tgl_kebutuhan
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //fungsi untuk generate transaksi verifikasi fingerprint pada saat cetak form LHK
    public function simpan_transaksi_verifikasi($kode_farm, $user, $transaction, $kode_flok = '', $kode_kandang = '')
    {
        $query = <<<QUERY
            insert into fingerprint_verification (
                kode_farm,
                [transaction],
                date_transaction,
                [user],
                kode_flok,
                kode_kandang
            )
            --output inserted.date_transaction
            output left(cast(inserted.date_transaction as date),10)+' '+left(cast(inserted.date_transaction as time),12) date_transaction
            values (
                '$kode_farm',
                '$transaction',
                getdate(),
                '$user',
                $kode_flok,
                '$kode_kandang'
            )

QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_rhk_cetak($rhk_cetak)
    {
        $success = true;
        if (true) {
            $this->dbSqlServer->trans_begin();

            $success = $success && $this->dbSqlServer->insert('RHK_CETAK', $rhk_cetak);

            if ($success) {
                $this->dbSqlServer->trans_commit();

                return true;
            } else {
                $this->dbSqlServer->trans_rollback();

                return false;
            }
        }
    }

    public function update_detail_penimbangan_rhk($no_reg, $tgllhk, $perubahan_data_lhk)
    {
        $success = true;
        if (true) {
            $this->dbSqlServer->trans_begin();
            $totalJumlah = 0;
            $totalBerat = 0;
            foreach ($perubahan_data_lhk['penimbangan']["$no_reg"]["$tgllhk"] as $keyDetailLHK => $valDetailLHK) {
                $data = array(
                    'SEKAT' => $valDetailLHK['sekat'],
                    'JUMLAH' => $valDetailLHK['jumlah'],
                    'BERAT' => $valDetailLHK['berat'],
                    'KETERANGAN' => $valDetailLHK['keterangan'],
                );
                $where = array(
                    'NO_REG' => $no_reg,
                    'TGL_TRANSAKSI' => $tgllhk,
                    'SEKAT' => $valDetailLHK['sekat'],
                );
                $success = $success && $this->dbSqlServer->update('RHK_PENIMBANGAN_BB', $data, $where);

                $totalJumlah += $valDetailLHK['jumlah'];
                $totalBerat += $valDetailLHK['berat'];
            }

            /** update c_berat_badan di rhk */
            $data_rhk = array(
                'c_berat_badan' => (($totalBerat / $totalJumlah) / 1000),
            );
            unset($where['SEKAT']);
            $this->dbSqlServer->update('RHK', $data_rhk, $where);

            if ($success) {
                $this->dbSqlServer->trans_commit();

                return true;
            } else {
                $this->dbSqlServer->trans_rollback();

                return false;
            }
        }
    }

    public function update_detail_populasi_rhk($no_reg, $tgllhk, $perubahan_data_lhk, $umur, $jmlAwalArr)
    {
        $success = true;
        if (true) {
            $this->dbSqlServer->trans_begin();

            foreach ($perubahan_data_lhk['populasi']["$no_reg"]["$tgllhk"] as $keyDetailLHK => $valDetailLHK) {
                $jumlah_ayam_awal = $jmlAwalArr['c_jumlah_sebelumnya'];
                if ($umur <= 7) {
                    $c_awal = $jmlAwalArr['c_jumlah_sebelumnya'] - $valDetailLHK['c_afkir'];
                    $c_jumlah = $jmlAwalArr['c_jumlah_sebelumnya'] - $jmlAwalArr['c_mati'] - $valDetailLHK['c_mati'];
                } else {
                    $c_awal = $jmlAwalArr['c_awal_sebelumnya'];
                    $c_jumlah = $jumlah_ayam_awal - $valDetailLHK['c_mati'] - $valDetailLHK['c_afkir'];
                }

                $data = array(
                    'C_MATI' => $valDetailLHK['c_mati'],
                    'C_AFKIR' => $valDetailLHK['c_afkir'],
                    'C_JUMLAH' => $c_jumlah,
                    'C_AWAL' => $c_awal,
                );
                $where = array(
                    'NO_REG' => $no_reg,
                    'TGL_TRANSAKSI' => $tgllhk,
                );
                $success = $success && $this->dbSqlServer->update('RHK', $data, $where);
            }

            if ($success) {
                $this->dbSqlServer->trans_commit();

                return true;
            } else {
                $this->dbSqlServer->trans_rollback();

                return false;
            }
        }
    }

    public function update_detail_pakan_rhk($no_reg, $tgllhk, $berat_pakan, $perubahan_data_lhk)
    {
        $success = true;
        if (true) {
            $this->dbSqlServer->trans_begin();

            foreach ($perubahan_data_lhk['pakan']["$no_reg"]["$tgllhk"] as $keyDetailLHK => $valDetailLHK) {
                //update rekomendasi pakan
                $data = array(
                    'JML_PERMINTAAN' => $valDetailLHK['jml_permintaan'],
                );
                $where = array(
                    'NO_REG' => $no_reg,
                    'TGL_TRANSAKSI' => $tgllhk,
                    'KODE_BARANG' => $valDetailLHK['kode_barang'],
                );
                $sudahAdaPermintaan = $this->dbSqlServer->where($where)->get('rhk_rekomendasi_pakan')->row_array();
                if (!empty($sudahAdaPermintaan)) {
                    $success = $success && $this->dbSqlServer->update('rhk_rekomendasi_pakan', $data, $where);
                } else {
                    $data['TGL_KEBUTUHAN'] = tglSetelah($tgllhk, 2);
                    $data['JML_REKOMENDASI'] = 0;
                    $success = $success && $this->dbSqlServer->insert('rhk_rekomendasi_pakan', array_merge($data, $where));
                }

                /** cari stok terakhir noreg tersebut */
                $stok_pakan = $this->db->where(array('no_reg' => $no_reg, 'kode_barang' => $valDetailLHK['kode_barang']))->get('kandang_movement')->row_array();
                $stok_akhir = $stok_pakan['JML_STOK'] - $valDetailLHK['jml_pakai'];
                $berat_akhir = $stok_pakan['BERAT_STOK'] - $berat_pakan["$valDetailLHK[kode_barang]"];
                //update rhk pakan
                $data = array(
                    'JML_PAKAI' => $valDetailLHK['jml_pakai'],
                    'BRT_PAKAI' => $berat_pakan["$valDetailLHK[kode_barang]"],
                    'JML_AKHIR' => $stok_akhir,
                    'BRT_AKHIR' => $berat_akhir,
                );
                $where = array(
                    'NO_REG' => $no_reg,
                    'TGL_TRANSAKSI' => $tgllhk,
                    'KODE_BARANG' => $valDetailLHK['kode_barang'],
                );
                $success = $success && $this->dbSqlServer->update('RHK_PAKAN', $data, $where);
            }

            if ($success) {
                $this->dbSqlServer->trans_commit();

                return true;
            } else {
                $this->dbSqlServer->trans_rollback();

                return false;
            }
        }
    }

    public function get_default_pakan_cetakLHK($no_reg)
    {
        $sql = <<<QUERY
        SELECT distinct std_budidaya.KODE_BARANG, brg.NAMA_BARANG
        FROM KANDANG_SIKLUS siklus
        INNER JOIN M_STD_BUDIDAYA_D std_budidaya ON std_budidaya.KODE_STD_BUDIDAYA = siklus.KODE_STD_BUDIDAYA
        INNER JOIN M_BARANG brg ON brg.KODE_BARANG = std_budidaya.KODE_BARANG
        WHERE siklus.no_reg='{$no_reg}' AND std_budidaya.KODE_BARANG IS NOT NULL AND std_budidaya.KODE_BARANG<>''       
        ORDER BY brg.NAMA_BARANG
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cek_verifikasi($kode_farm, $user, $date_transaction, $kode_flok = '', $no_reg = '', $kode_kandang = '')
    {
        $str = '';
        /* nunggu aplikasi fingernya di sesuaikan */
        if ($kode_flok != '') {
            //$str .= "INNER JOIN KANDANG_SIKLUS ks ON fv.kode_flok = ks.FLOK_BDY AND ks.KODE_FARM = '$kode_farm' AND ks.STATUS_SIKLUS = 'O' AND fv.verificator = ks.PENGAWAS1 OR fv.verificator = ks.PENGAWAS2";
        }
        $query = <<<QUERY
            select top 1
                fv.*
                , mp.KODE_PEGAWAI kode_pegawai
                , mp.NAMA_PEGAWAI nama_pegawai
                , mplotting.OPERATOR
            from fingerprint_verification fv
            $str
            left join M_PEGAWAI mp
                on mp.kode_pegawai = fv.verificator
            left join (
                SELECT KODE_KANDANG, OPERATOR
                FROM M_PLOTING_PELAKSANA
                where NO_REG = '$no_reg' and KODE_KANDANG = '$kode_kandang'
            ) mplotting ON mplotting.KODE_KANDANG = fv.kode_kandang
            where fv.date_transaction = '$date_transaction'
            and fv.kode_farm = '$kode_farm' and fv.kode_kandang = '$kode_kandang' and fv.[transaction] = 'cetak_form_lhk'
QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function granted_user_fingerprint($no_reg, $kode_kandang)
    {
        $sql = <<<QUERY
        SELECT OPERATOR
        FROM M_PLOTING_PELAKSANA
        where NO_REG = '$no_reg' and KODE_KANDANG = '$kode_kandang'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //END FUNCTION CETAK FORM LHK

    public function get_batas_pakai_pakan($no_reg, $tgl_lhk)
    {
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

    public function get_last_lhk($no_reg)
    {
        $sql = <<<QUERY
        select no_reg, max(tgl_transaksi) tgl_transaksi
        from rhk
        where no_reg = '{$no_reg}'
        group by no_reg
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_jumlah_bj_last_lhk($no_reg, $tgl_transaksi, $tgl_doc_in)
    {
        $sql = <<<QUERY
        select
            coalesce(c.no_reg, d.no_reg) no_reg,
            coalesce(c.tgl_transaksi, d.tgl_doc_in) tgl,
            coalesce(c.b_jumlah, d.jml_betina) b_jml,
            coalesce(c.j_jumlah, d.jml_jantan) j_jml,
            coalesce(c.b_pindah_ext, d.b_pindah_ext) b_pindah,
            coalesce(c.j_pindah_ext, d.j_pindah_ext) j_pindah
            ,coalesce(c.b_pindah_semu, e.b_pindah_semu) b_pindah_semu
            ,coalesce(c.j_pindah_semu, e.j_pindah_semu) j_pindah_semu
            ,c.b_daya_hidup
            ,c.j_daya_hidup
            ,c.b_jumlah_pembagi
            ,c.j_jumlah_pembagi
        from (
            select a.no_reg, a.tgl_transaksi, a.b_jumlah, a.j_jumlah, a.b_pindah, a.j_pindah
                ,coalesce(e_pindah_b.b_pindah,0) b_pindah_ext
                ,coalesce(e_pindah_b.b_pindah_semu,0) b_pindah_semu
                ,coalesce(e_pindah_j.j_pindah,0) j_pindah_ext
                ,coalesce(e_pindah_j.j_pindah_semu,0) j_pindah_semu
                ,ISNULL(b_daya_hidup, 0) b_daya_hidup, ISNULL(j_daya_hidup, 0) j_daya_hidup
                ,ISNULL(b_jumlah_pembagi, 0) b_jumlah_pembagi, ISNULL(j_jumlah_pembagi, 0) j_jumlah_pembagi
            from rhk a
            left join (
                select no_reg_tujuan, tgl_transaksi, sum(jumlah) b_pindah, sum(jumlah_semu) b_pindah_semu
                from rhk_pindah
                where jenis_kelamin = 'B'
                group by no_reg_tujuan, tgl_transaksi
            ) e_pindah_b on e_pindah_b.no_reg_tujuan = a.no_reg and e_pindah_b.tgl_transaksi = '{$tgl_transaksi}'
            left join (
                select no_reg_tujuan, tgl_transaksi, sum(jumlah) j_pindah, sum(jumlah_semu) j_pindah_semu
                from rhk_pindah
                where jenis_kelamin = 'J'
                group by no_reg_tujuan, tgl_transaksi
            ) e_pindah_j on e_pindah_j.no_reg_tujuan = a.no_reg and e_pindah_j.tgl_transaksi = '{$tgl_transaksi}'
            where a.no_reg = '{$no_reg}'
            and a.tgl_transaksi = DATEADD(day,-1,'{$tgl_transaksi}')
        ) c right join (
            select b.no_reg, b.tgl_doc_in, b.jml_betina, b.jml_jantan, coalesce(e_pindah_b.b_pindah,0) b_pindah_ext, coalesce(e_pindah_j.j_pindah,0) j_pindah_ext
            from kandang_siklus b
            left join (
                select no_reg_tujuan, sum(jumlah) b_pindah
                from rhk_pindah
                where jenis_kelamin = 'B'
                group by no_reg_tujuan
            ) e_pindah_b on e_pindah_b.no_reg_tujuan = b.no_reg
            left join (
                select no_reg_tujuan, sum(jumlah) j_pindah
                from rhk_pindah
                where jenis_kelamin = 'J'
                group by no_reg_tujuan
            ) e_pindah_j on e_pindah_j.no_reg_tujuan = b.no_reg
            where b.no_reg = '{$no_reg}' and b.tgl_doc_in = '{$tgl_doc_in}' and b.tipe_kandang = 'O'
        ) d on c.no_reg = d.no_reg
        left join(
            select e_pindah_b2.no_reg_tujuan, e_pindah_b2.tgl_transaksi, e_pindah_b2.b_pindah, e_pindah_b2.b_pindah_semu, e_pindah_j2.j_pindah, e_pindah_j2.j_pindah_semu from(
                select no_reg_tujuan, tgl_transaksi, sum(jumlah) b_pindah, sum(jumlah_semu) b_pindah_semu
                from rhk_pindah
                where jenis_kelamin = 'B'
                group by no_reg_tujuan, tgl_transaksi
            )e_pindah_b2 left join (
                select no_reg_tujuan, tgl_transaksi, sum(jumlah) j_pindah, sum(jumlah_semu) j_pindah_semu
                from rhk_pindah
                where jenis_kelamin = 'J'
                group by no_reg_tujuan, tgl_transaksi
            )e_pindah_j2 on e_pindah_b2.no_reg_tujuan = e_pindah_j2.no_reg_tujuan and e_pindah_b2.tgl_transaksi = e_pindah_j2.tgl_transaksi
            where e_pindah_b2.tgl_transaksi = '{$tgl_transaksi}'
        )e on e.no_reg_tujuan = d.no_reg

         /*select
              coalesce(c.no_reg, d.no_reg) no_reg,
              coalesce(c.tgl_transaksi, d.tgl_doc_in) tgl,
              coalesce(c.b_jumlah, d.jml_betina) b_jml,
              coalesce(c.j_jumlah, d.jml_jantan) j_jml,
              coalesce(c.b_pindah, 0) b_pindah,
              coalesce(c.j_pindah, 0) j_pindah
         from (
             select a.no_reg, a.tgl_transaksi, a.b_jumlah, a.j_jumlah, a.b_pindah, a.j_pindah
             from rhk a
             where a.no_reg = '{$no_reg}'
             and a.tgl_transaksi = DATEADD(day,-1,'{$tgl_transaksi}')
         ) c right join (
             select b.no_reg, b.tgl_doc_in, b.jml_betina, b.jml_jantan
             from kandang_siklus b
             where b.no_reg = '{$no_reg}' and b.tgl_doc_in = '{$tgl_doc_in}' and b.tipe_kandang = 'O'
         ) d on c.no_reg = d.no_reg*/
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_pakan_last_lhk_dummy()
    {
        $data = array();
        $data[] = array(
            'jk' => 'J',
            'jenis_kelamin' => 'Jantan',
            'kode_barang' => '111-23-4',
            'nama_barang' => 'DELE',
            'jml_awal' => 10,
            'jml_kirim' => 10,
            'jml_pakai' => 0,
            'jml_akhir' => 10,
            'bentuk_barang' => 'PELET',
        );

        return $data;
    }

    public function get_target_bb($umur, $noreg)
    {
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

    public function get_pakan_last_lhk($no_reg)
    {
        $sql = <<<QUERY
        select
          a.tgl_buat,
            a.jenis_kelamin jk,
            case when a.jenis_kelamin = 'J' then 'Jantan' else 'Betina' end jenis_kelamin,
            a.kode_barang, b.nama_barang,
          case when a.berat_order>= 0 then a.berat_awal else berat_akhir end berat_awal,
            case when coalesce(c.berat_order, a.berat_order)>= 0 then coalesce(c.berat_order,a.berat_order) else 0 end berat_kirim,
            case when a.berat_order< 0 then a.berat_order else 0 end berat_pakai,
            case when a.jml_order>= 0 then a.jml_awal else jml_akhir end jml_awal,
            case when coalesce(c.jml_order, a.jml_order)>= 0 then coalesce(c.jml_order,a.jml_order) else 0 end jml_kirim,
            case when a.jml_order< 0 then a.jml_order else 0 end jml_pakai,
            a.jml_akhir,
            dbo.BENTUK_CONVERTION(b.bentuk_barang) bentuk_barang
        from (
          select * from KANDANG_MOVEMENT_D
          where no_reg = '{$no_reg}' and tgl_buat in (
            select distinct coalesce(qry1.tgl_buat, qry2.tgl_buat) tgl_buat
            from
          (select no_reg, max(tgl_buat) tgl_buat
          from kandang_movement_d
            where no_reg = '{$no_reg}' and (keterangan1 = 'LHK')
            group by no_reg, kode_barang, jenis_kelamin) qry1
          full outer join
          (select no_reg, max(tgl_buat) tgl_buat
          from kandang_movement_d
            where no_reg = '{$no_reg}' and (keterangan1 = 'PENERIMAAN KANDANG')
            group by no_reg, kode_barang, jenis_kelamin) qry2 on qry2.no_reg = qry1.no_reg
          )) a
          left join (
            select no_reg, kode_barang, jenis_kelamin, berat_order, jml_order, max(tgl_buat) tgl_buat
            from kandang_movement_d
            where no_reg = '{$no_reg}' and (keterangan1 = 'PENERIMAAN KANDANG')
            group by no_reg, kode_barang, jenis_kelamin,berat_order,jml_order
          ) c on c.no_reg = a.no_reg and c.kode_barang = a.kode_barang and c.jenis_kelamin = a.jenis_kelamin and c.tgl_buat > a.tgl_buat
          inner join m_barang b on a.kode_barang = b.kode_barang
        group by a.tgl_buat, a.jenis_kelamin, a.kode_barang, b.nama_barang, a.berat_awal, a.berat_order, a.berat_akhir, a.jml_awal, a.jml_order, a.jml_akhir, bentuk_barang, c.berat_order, c.jml_order
        order by a.jenis_kelamin desc, b.nama_barang asc
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_obat()
    {
        $sql = <<<QUERY
        select kode_barang, nama_barang
        from m_barang
        where jenis_barang = 'O' and nama_barang is not null
        order by nama_barang asc

QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_vaksin()
    {
        $sql = <<<QUERY
        select kode_barang, nama_barang
        from m_barang
        where jenis_barang = 'V' and nama_barang is not null
        order by nama_barang asc

QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_farm($kode_farm)
    {
        $sql = <<<QUERY
        select kode_farm, nama_farm
        from m_farm
        where kode_farm = '{$kode_farm}'

QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_lhk($lhk_header, $pindah_ayam, $kandang_movement, $kandang_movement_d, $lhk_obat, $lhk_pakan, $lhk_produksi, $rhk_penimbangan, $tutup_siklus)
    {
        $pass = true;

        if (isset($tutup_siklus)) {
            $update['status_siklus'] = 'C';
            $sql = <<<QUERY
            update kandang_siklus set status_siklus = 'C', tgl_ubah = getdate() where no_reg = '{$tutup_siklus}'
QUERY;

            $stmt = $this->dbSqlServer->conn_id->prepare($sql);
            if ($stmt->execute()) {
                $pass = true;
            } else {
                $pass = false;
            }
        }

        if ($pass) {
            $this->dbSqlServer->trans_begin();

            $this->dbSqlServer->insert('rhk', $lhk_header);
            $pindah_ayam_result;
            if (is_array($pindah_ayam) and count($pindah_ayam) > 0) {
                $success = 0;
                for ($i = 0; $i < count($pindah_ayam); ++$i) {
                    $this->dbSqlServer->insert('rhk_pindah', $pindah_ayam[$i]);
                    if ($this->dbSqlServer->affected_rows() > 0) {
                        ++$success;
                    }
                }

                if ($success == count($pindah_ayam)) {
                    $pindah_ayam_result = true;
                } else {
                    $pindah_ayam_result = false;
                }

                // $this->dbSqlServer->insert_batch("rhk_pindah", $pindah_ayam);
                // if($this->dbSqlServer->affected_rows() > 0)
                    // $pindah_ayam_result = true;
                // else
                    // $pindah_ayam_result = false;
            } else {
                $pindah_ayam_result = true;
                //$this->dbSqlServer->insert("rhk_pindah", $pindah_ayam);
            }

            if ($pindah_ayam_result) {
                $lhk_pakan_result;
                if (is_array($lhk_pakan) and count($lhk_pakan) > 0) {
                    $success = 0;
                    for ($i = 0; $i < count($lhk_pakan); ++$i) {
                        $this->dbSqlServer->insert('rhk_pakan', $lhk_pakan[$i]);
                        if ($this->dbSqlServer->affected_rows() > 0) {
                            ++$success;
                        }
                    }

                    if ($success == count($lhk_pakan)) {
                        $lhk_pakan_result = true;
                    } else {
                        $lhk_pakan_result = false;
                    }

                    //$this->dbSqlServer->insert_batch("rhk_pakan", $lhk_pakan);
                    //if($this->dbSqlServer->affected_rows() > 0)
                        //$lhk_pakan_result = true;
                    //else
                        //$lhk_pakan_result = false;
                } else {
                    $lhk_pakan_result = true;
                    //$this->dbSqlServer->insert("rhk_pakan", $lhk_pakan);
                }

                if ($lhk_pakan_result) {
                    $rhk_penimbangan_result;

                    if (is_array($rhk_penimbangan) and count($rhk_penimbangan) > 0) {
                        $success = 0;
                        for ($i = 0; $i < count($rhk_penimbangan); ++$i) {
                            $this->dbSqlServer->insert('rhk_penimbangan_bb', $rhk_penimbangan[$i]);
                            if ($this->dbSqlServer->affected_rows() > 0) {
                                ++$success;
                            }
                        }

                        if ($success == count($rhk_penimbangan)) {
                            $rhk_penimbangan_result = true;
                        } else {
                            $rhk_penimbangan_result = false;
                        }
                    } else {
                        $rhk_penimbangan_result = true;
                    }
                    if ($rhk_penimbangan_result) {
                        $kandang_movement_result;
                        if (is_array($kandang_movement) and count($kandang_movement) > 0) {
                            $success = 0;
                            for ($i = 0; $i < count($kandang_movement); ++$i) {
                                $dt_upt['jml_stok'] = $kandang_movement[$i]['jml_stok'];
                                $dt_upt['berat_stok'] = $kandang_movement[$i]['berat_stok'];

                                $this->dbSqlServer->where('no_reg', $kandang_movement[$i]['no_reg']);
                                $this->dbSqlServer->where('kode_barang', $kandang_movement[$i]['kode_barang']);
                                $this->dbSqlServer->where('jenis_kelamin', $kandang_movement[$i]['jenis_kelamin']);
                                $this->dbSqlServer->update('kandang_movement', $dt_upt);
                                if ($this->dbSqlServer->affected_rows() > 0) {
                                    ++$success;
                                }
                            }

                            if ($success == count($kandang_movement)) {
                                $kandang_movement_result = true;
                            } else {
                                $kandang_movement_result = false;
                            }
                        } else {
                            $kandang_movement_result = true;
                        }
                        if ($kandang_movement_result) {
                            $kandang_movement_d_result;
                            if (is_array($kandang_movement_d) and count($kandang_movement_d) > 0) {
                                $success = 0;
                                for ($i = 0; $i < count($kandang_movement_d); ++$i) {
                                    $this->dbSqlServer->insert('kandang_movement_d', $kandang_movement_d[$i]);
                                    if ($this->dbSqlServer->affected_rows() > 0) {
                                        ++$success;
                                    }
                                }

                                if ($success == count($kandang_movement_d)) {
                                    $kandang_movement_d_result = true;
                                } else {
                                    $kandang_movement_d_result = false;
                                }
                            } else {
                                $kandang_movement_d_result = true;
                            }

                            if ($kandang_movement_d_result) {
                                //$this->dbSqlServer->insert_batch("rhk_vaksin", $lhk_obat);
                                for ($i = 0; $i < count($lhk_obat); ++$i) {
                                    $this->dbSqlServer->insert('rhk_vaksin', $lhk_obat[$i]);
                                }

                                //$this->dbSqlServer->insert_batch("rhk_vaksin", $lhk_produksi);
                                for ($i = 0; $i < count($lhk_produksi); ++$i) {
                                    $this->dbSqlServer->insert('rhk_produksi', $lhk_produksi[$i]);
                                }

                                $no_reg = $lhk_header['no_reg'];
                                $tgl_transaksi = $lhk_header['tgl_transaksi'];

                                $this->dbSqlServer->query("EXEC REKLAS '{$no_reg}', '{$tgl_transaksi}'");

                                $this->dbSqlServer->trans_commit();

                                return true;
                            } else {
                                $this->dbSqlServer->trans_rollback();

                                return false;
                            }
                        } else {
                            $this->dbSqlServer->trans_rollback();

                            return false;
                        }
                    } else {
                        $this->dbSqlServer->trans_rollback();

                        return false;
                    }
                } else {
                    $this->dbSqlServer->trans_rollback();

                    return false;
                }
            } else {
                $this->dbSqlServer->trans_rollback();

                return false;
            }
        } else {
            return false;
        }
    }

    /*untuk tutup siklus*/

    public function tutup_siklus($noreg, $kode_farm, $user)
    {
        $sql = <<<QUERY
        exec dbo.LHK_TUTUP_SIKLUS :noreg, :kodefarm, :user
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->bindParam(':noreg', $noreg);
        $stmt->bindParam(':kodefarm', $kode_farm);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
    }

    public function buat_persetujuan_retur($noreg, $user, $setuju)
    {
        $sql = <<<QUERY
        exec dbo.LHK_PERSETUJUAN_RETUR :noreg, :user, :setuju
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->bindParam(':noreg', $noreg);
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':setuju', $setuju);
        $stmt->execute();
    }

    /*untuk pemantauan lhk*/
    public function get_min_doc_in($kode_farm)
    {
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

    public function get_data_lhk($start_date, $end_date, $kode_farm, $param)
    {
        $param = (trim($param) !== '' and strlen(trim($param)) > 0) ? 'where '.$param : '';

        $sql = <<<QUERY
        select * from dbo.get_data_pemantauan_lhk('{$start_date}', '{$end_date}', '{$kode_farm}') qry
        inner join m_farm mf on mf.kode_farm = qry.kode_farm and mf.grup_farm = 'BRD'
        left join (
            select qa.kode_farm kode_farm_kd, qa.no_reg no_reg2, qa.tgl_transaksi tgl_transaksi2
            from (
                select b.kode_farm, a.no_reg, a.TGL_TRANSAKSI, a.KODE_BARANG, a.JENIS_KELAMIN, sum(a.brt_pakai) brt_pakai
                from rhk_pakan a
                inner join KANDANG_SIKLUS b on b.NO_REG = a.NO_REG
                group by b.kode_farm, a.no_reg, a.TGL_TRANSAKSI, a.KODE_BARANG, a.JENIS_KELAMIN
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
        $param
        order by qry.nama_kandang asc, qry.colDate asc
QUERY;
        //log_message("error", $sql);
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function simpan_ack_kf($desc, $no_reg, $tgl_transaksi)
    {
        $sql = <<<QUERY
        update rhk set ack_kf = getdate(), ack_desc = '{$desc}' where no_reg = '{$no_reg}' and tgl_transaksi = '{$tgl_transaksi}'
QUERY;
        $this->dbSqlServer->where('no_reg', $no_reg);
        $this->dbSqlServer->where('tgl_transaksi', $tgl_transaksi);

        //log_message("error", $sql);
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);

        return $stmt->execute();
    }

    public function simpan_ack_dir($no_reg, $tgl_transaksi)
    {
        $sql = <<<QUERY
        update rhk set ack_dir = getdate() where no_reg = '{$no_reg}' and tgl_transaksi = '{$tgl_transaksi}'
QUERY;
        $this->dbSqlServer->where('no_reg', $no_reg);
        $this->dbSqlServer->where('tgl_transaksi', $tgl_transaksi);

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);

        return $stmt->execute();
    }

    public function get_farm_for_pemantauan($kode_user)
    {
        $sql = <<<QUERY
        select pd.kode_farm, mf.nama_farm, count(rhk.no_reg) jml
        from pegawai_d pd
        inner join m_farm mf on pd.kode_farm = mf.kode_farm and mf.grup_farm = 'BRD'
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
    public function simpanLain2($no_reg, $tgl_transaksi, $tipe, $b_jml, $j_jml, $keterangan, $no_berita_acara, $attachment, $attachment_format)
    {
        $sql = <<<QUERY
            insert into rhk_lain2 (no_reg, tgl_transaksi, tipe, b_jml, j_jml, keterangan, no_berita_acara, attachment, attachment_format)
            values ('{$no_reg}', '{$tgl_transaksi}', '{$tipe}', {$b_jml}, {$j_jml}, '{$keterangan}', '{$no_berita_acara}', {$attachment}, '{$attachment_format}')
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);

        return $stmt->execute();
    }
}

