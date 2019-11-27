<?php

class M_transaksi extends MY_Model {

    private $dbSqlServer;

    public function __construct() {
        parent::__construct();
        $this->dbSqlServer = $this->load->database('default', TRUE);
    }

    function get_data_do($start = null, $offset = null, $kode_farm = null, $do_belum_diterima = null, $tanggal_kirim_awal = null, $tanggal_kirim_akhir = null, $no_op = null, $no_do = null, $no_sj = null, $nama_ekspedisi = null, $tanggal_kirim = null) {
        $filter_str = "";
        $filter_arr = array();

        $filter_bottom_str = "";
        $filter_bottom_arr = array();

        if ($do_belum_diterima == 1)
        /** tambahkan filter batasan tgl kirim do >= tgl buat pp pertama kali untuk siklus yang aktif */
            $filter_arr [] = " do.TGL_KIRIM >= (SELECT min(tgl_buat) FROM LPB WHERE KODE_SIKLUS = (SELECT KODE_SIKLUS FROM M_PERIODE WHERE status_periode = 'A' AND KODE_FARM = '".$kode_farm."')) AND p.TGL_TERIMA IS NULL";

        if (isset($tanggal_kirim_awal) && isset($tanggal_kirim_akhir))
            $filter_arr [] = "CAST(do.TGL_KIRIM AS DATE) BETWEEN '" . $tanggal_kirim_awal . "' AND '" . $tanggal_kirim_akhir ."'";

        if (isset($no_op))
            $filter_arr [] = "do.NO_OP LIKE '%" . $no_op . "%'";

        if (isset($no_do))
            $filter_arr [] = "do.NO_DO LIKE '%" . $no_do . "%'";

        if (isset($no_sj))
            $filter_arr [] = "p.KODE_SURAT_JALAN LIKE '%" . $no_sj . "%'";

        if (isset($nama_ekspedisi))
            $filter_arr [] = "me.NAMA_EKSPEDISI LIKE '%" . $nama_ekspedisi . "%'";

        if (isset($tanggal_kirim))
            $filter_arr [] = "CAST(do.TGL_KIRIM AS DATE) = '" . $tanggal_kirim . "'";

        if (count($filter_arr) > 0) {
            $filter_str .= " WHERE ";
            $filter_str .= implode(" AND ", $filter_arr);
        }

        if (isset($start) and isset($offset))
            $filter_bottom_arr [] = "row > {$start} AND row <= {$offset}";

        if (count($filter_bottom_arr) > 0) {
            $filter_bottom_str .= " WHERE ";
            $filter_bottom_str .= implode(" AND ", $filter_bottom_arr);
        }

        $query = <<<QUERY
            SELECT
                *
            FROM (
            SELECT DISTINCT
                ROW_NUMBER() OVER (ORDER BY mainqry.tanggal_kirim ASC) row
                , mainqry.*
            FROM (
                SELECT DISTINCT
                    ISNULL(do.NO_OP,'') no_op
                    , ISNULL(do.NO_DO,'') no_do
                    , p.KETERANGAN1 tmp_no_do
                    , ISNULL(p.KODE_SURAT_JALAN,'') tmp_no_sj
                    , ISNULL((select dbo.get_kode_sj(ISNULL(do.NO_DO,''),isnull(p.KETERANGAN1,''),ISNULL(p.KODE_SURAT_JALAN,''))),'') no_sj
                    , ISNULL(me.NAMA_EKSPEDISI,'') nama_ekspedisi
                    , do.TGL_KIRIM tanggal_kirim
                    , CASE
                        WHEN p.TGL_TERIMA IS NOT NULL THEN CAST(p.TGL_TERIMA AS DATE)
                        ELSE NULL
                    END tanggal_terima
                    , CASE
                        WHEN p.TGL_TERIMA IS NOT NULL THEN CAST(p.TGL_TERIMA AS TIME)
                        ELSE NULL
                    END jam_terima
                    , ISNULL(mp.NAMA_PEGAWAI,'') penerima
                    , ISNULL(ba.NO_BA,'') no_ba
                    , CASE
                        WHEN p.NO_PENERIMAAN IS NOT NULL THEN 0
                        ELSE 1
                    END pink
                FROM DO DO
                JOIN OP_VEHICLE opv ON opv.NO_OP = do.NO_OP AND do.KODE_FARM = '$kode_farm'
                    and DO.NO_URUT = opv.NO_URUT
                JOIN M_EKSPEDISI me ON me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
                LEFT JOIN PENERIMAAN p ON p.KETERANGAN1 like '%'+do.NO_DO+'%' and p.KODE_FARM = do.KODE_FARM
                LEFT JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = p.USER_BUAT
                LEFT JOIN BERITA_ACARA ba ON ba.KETERANGAN2 = p.KODE_SURAT_JALAN AND do.KODE_FARM = ba.KODE_FARM AND ba.NO_PENERIMAAN = p.NO_PENERIMAAN
                $filter_str
            ) mainqry
            ) mainqry_end
            $filter_bottom_str
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verifikasi_do($kode_farm, $nomor_do) {
        $query = <<<QUERY
            SELECT DISTINCT
                do.NO_DO no_do
                , mk.NAMA_KANDANG nama_kandang
                , mk.KODE_KANDANG kode_kandang
                , ks.NO_REG no_reg
                , ISNULL(p.KODE_SURAT_JALAN,'') no_sj
                , ISNULL(do.NO_OP,'') no_op
                , CAST(do.TGL_KIRIM AS DATE) tanggal_kirim
                , CAST(isnull(p.TGL_TERIMA,GETDATE()) as date) tanggal_terima
                , ISNULL(me.NAMA_EKSPEDISI,'') nama_ekspedisi
                , (
                    select top 1 ks.FLOK_BDY
                    from LPB_E le
                    join KANDANG_SIKLUS ks
                        on ks.NO_REG = le.NO_REG
                        and ks.KODE_FARM = le.KODE_FARM
                    where le.NO_LPB = op.NO_LPB
                    and le.KODE_FARM = op.KODE_FARM
                ) kode_flok                
                , ISNULL(p.NO_KENDARAAN_KIRIM,'') nopol_kirim
                , ISNULL(p.NO_KENDARAAN_TERIMA,'') nopol_terima
                , p.TGL_SURAT_JALAN tanggal_sj
                , p.KUANTITAS_KG kuantitas_kg
                , p.KUANTITAS_SAK kuantitas_zak
                , p.TGL_VERIFIKASI_DO tanggal_verifikasi_do
                , ISNULL(p.NAMA_SOPIR,'') sopir
                , CASE
                     WHEN CAST(do.TGL_KIRIM AS DATE) < CAST(GETDATE() AS DATE) THEN 0
                    ELSE 1
                END validasi_tanggal_kirim
                , ISNULL(p.NO_PENERIMAAN,'') no_penerimaan
                , ISNULL(p.KETERANGAN1,'') list_do
            FROM DO DO
            JOIN DO_E doe
                on do.NO_DO = doe.NO_DO
            JOIN KANDANG_SIKLUS ks
                on ks.NO_REG = doe.NO_REG
                and ks.KODE_FARM = do.KODE_FARM
            JOIN M_KANDANG mk
                on mk.KODE_FARM = ks.KODE_FARM
                and mk.KODE_KANDANG = ks.KODE_KANDANG
            JOIN OP_VEHICLE opv ON opv.NO_OP = do.NO_OP AND do.KODE_FARM = :kode_farm
                    and DO.NO_URUT = opv.NO_URUT
            AND do.NO_DO in ('$nomor_do') --and do.STATUS_DO = 'C'
            JOIN OP op on op.NO_OP = opv.NO_OP and op.KODE_FARM = do.KODE_FARM
            JOIN M_EKSPEDISI me ON me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
            LEFT JOIN PENERIMAAN p ON p.KETERANGAN1 like '%'+do.NO_DO+'%' AND p.KODE_FARM = do.KODE_FARM
            LEFT JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = p.USER_BUAT
            LEFT JOIN BERITA_ACARA ba ON ba.KETERANGAN2 = p.KODE_SURAT_JALAN AND do.KODE_FARM = ba.KODE_FARM AND ba.NO_PENERIMAAN = p.NO_PENERIMAAN

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->bindParam(':kode_farm', $kode_farm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function penimbangan_pakan_brd($kode_farm, $nomor_do) {
        $data = $this->data_penimbangan_pakan_brd($kode_farm, $nomor_do);
        $result=[];
        foreach ($data as $key => $value) {
            $result[$value['kode_pakan']] = array(
                'kode_pakan' => $value['kode_pakan'],
                'nama_pakan' => $value['nama_pakan'],
                'bentuk_pakan' => $value['bentuk_pakan'],
                'jml_sj' => $value['jml_sj'],
                'jml_terima' => $value['jml_terima'],
                'jml_tolak' => $value['jml_tolak'],
                'jml_hilang' => $value['jml_hilang'],
            );
        }
        foreach ($data as $key => $value) {
            $result[$value['kode_pakan']]['detail'][] = array(
                'no_reg' => $value['no_reg'],
                'nama_kandang' => $value['nama_kandang'],
                'jenis_kelamin' => $value['jenis_kelamin'],
                'jml_seharusnya' => $value['jml_seharusnya'],
                'timbangan_kg' => $value['timbangan_kg'],
                'timbangan_sak' => $value['timbangan_sak'],
                'kavling' => $value['kavling'],
                'selesai' => $value['selesai'],
            );
        }
        return $result;
    }

    public function penimbangan_pakan_bdy($kode_farm, $nomor_do) {
        $query = <<<QUERY
            select
                r.KODE_BARANG kode_pakan
                , mb.NAMA_BARANG nama_pakan
                , dbo.BENTUK_CONVERTION(MB.BENTUK_BARANG) bentuk_pakan
                , r.JML_SJ jml_sj
            from (
                select
                    doe.KODE_BARANG
                    , sum(doe.JML_MUAT) JML_SJ
                from DO do
                join DO_D dod
                    on do.NO_DO = dod.NO_DO
                    and do.KODE_FARM = dod.KODE_FARM
                    and do.NO_OP = dod.NO_OP
                        and do.KODE_FARM = '$kode_farm'
                    and do.NO_DO in ('$nomor_do')
                join DO_E doe
                    on doe.NO_DO = dod.NO_DO
                    and dod.KODE_BARANG = doe.KODE_BARANG
                group by
                    doe.KODE_BARANG
            ) r
            join M_BARANG mb
                on mb.KODE_BARANG = r.KODE_BARANG

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function data_penimbangan($kode_farm, $nomor_do) {
        $data = $this->all_data_penimbangan_pakan_bdy($kode_farm, $nomor_do);
        $result=[];
        foreach ($data as $key => $value) {
            $result[$value['kode_pakan']] = array(
                'kode_pakan' => $value['kode_pakan'],
                'nama_pakan' => $value['nama_pakan'],
                'bentuk_pakan' => $value['bentuk_pakan'],
                'jml_sj' => $value['jml_sj'],
                'jml_terima' => $value['jml_terima'],
                'jml_terima_per_kavling' => $value['jml_terima_per_kavling'],
                'jml_tolak' => $value['jml_tolak'],
                'jml_hilang' => $value['jml_hilang'],
            );
        }
        foreach ($data as $key => $value) {
            if($value['status_stok']=='NM'){
                $result[$value['kode_pakan']]['detail'][$value['no_pallet']] = array(
                    'no_pallet' => $value['no_pallet'],
                    'no_kavling' => $value['no_kavling'],
                    'kode_pallet' => $value['kode_pallet'],
                    'berat_pallet' => $value['berat_pallet'],
                    'berat_timbang' => $value['berat_terima_per_kavling'] + $value['berat_pallet'],
                    'berat_terima' => $value['berat_terima_per_kavling'],
                    'timbangan_sak' => $value['jml_terima_per_kavling'],
                    'selesai' => $value['selesai'],
                );
            }
        }
        foreach ($data as $key => $value) {
            if($value['status_stok']=='NM'){
                $result[$value['kode_pakan']]['detail'][$value['no_pallet']]['detail'][] = array(
                    'no_reg' => $value['no_reg'],
                    'nama_kandang' => $value['nama_kandang'],
                    'jml_kebutuhan' => (($value['status_stok']=='DM') ? 0 : $value['jml_kebutuhan']),
                    'jml_aktual' => $value['jml_aktual'],
                    'berat_aktual' => $value['berat_aktual'],
                    'status_stok' => $value['status_stok'],
                    'sisa' => (($value['status_stok']=='DM') ? 0 : ($value['jml_kebutuhan'] - $value['jml_aktual']))
                );
            }
        }
        return $result;
    }

    public function all_data_penimbangan_pakan_bdy($kode_farm, $nomor_do) {
        $array_nomor_do = str_replace("'", "", $nomor_do);
        $list_do = explode(',', $array_nomor_do);
        $no_do = $list_do[0];
        $query = <<<QUERY
                select
                    *
                from (
                    select
                        r.KODE_BARANG kode_pakan
                        , mb.NAMA_BARANG nama_pakan
                        , dbo.BENTUK_CONVERTION(MB.BENTUK_BARANG) bentuk_pakan
                        , r.JML_SJ jml_sj
                    from (
                        select
                            doe.KODE_BARANG
                            , sum(doe.JML_MUAT) JML_SJ
                        from DO do
                        join DO_D dod
                            on do.NO_DO = dod.NO_DO
                            and do.KODE_FARM = dod.KODE_FARM
                            and do.NO_OP = dod.NO_OP
                                and do.KODE_FARM = '$kode_farm'
                            and do.NO_DO in ('$nomor_do')
                        join DO_E doe
                            on doe.NO_DO = dod.NO_DO
                            and dod.KODE_BARANG = doe.KODE_BARANG
                        group by
                            doe.KODE_BARANG
                    ) r
                    join M_BARANG mb
                        on mb.KODE_BARANG = r.KODE_BARANG
                ) r
                join (
                    select distinct
                        pd.KODE_BARANG kode_barang
                        , m.NO_PALLET no_pallet
                        , m.NO_KAVLING no_kavling
                        , m.KODE_PALLET kode_pallet
                        , m.BERAT_PALLET berat_pallet
                        --, (
                        --    select top 1
                        --        fm.BERAT_PALLET
                        --    from MOVEMENT fm
                        --    where fm.NO_KAVLING = m.NO_KAVLING
                        --    and fm.KODE_FARM = m.KODE_FARM
                        --    and fm.KODE_BARANG = m.KODE_BARANG
                        --    and fm.KETERANGAN1 = m.KETERANGAN1
                        --    and cast(fm.PUT_DATE as date) = cast(m.PUT_DATE as date)
                        --    order by NO_PALLET asc
                        --) berat_pallet
                        , m.BERAT_PUTAWAY berat_terima_per_kavling
                        , m.JML_PUTAWAY jml_terima_per_kavling
                        , pd.BERAT_TERIMA berat_terima
                        , pd.JML_TERIMA jml_terima
                        , pd.JML_RUSAK jml_tolak
                        , pd.JML_KURANG jml_hilang
                        , 1 selesai
                        , md.KETERANGAN2 no_reg
                        , mk.NAMA_KANDANG nama_kandang
                        , sdk.JML_SJ jml_kebutuhan
                        , md.JML_PUTAWAY jml_aktual
                        , md.BERAT_PUTAWAY berat_aktual
                        , md.STATUS_STOK status_stok
                    from PENERIMAAN p
                    join PENERIMAAN_D pd
                        on p.NO_PENERIMAAN = pd.NO_PENERIMAAN
                        and p.KODE_FARM = pd.KODE_FARM
                        and p.KETERANGAN1 like '%$no_do%'
                        and p.KODE_FARM = '$kode_farm'
                    join PENERIMAAN_E pe
                        on pe.NO_PENERIMAAN = pd.NO_PENERIMAAN
                        and pd.KODE_BARANG = pe.KODE_BARANG
                        and pe.KODE_FARM = pd.KODE_FARM
                    join MOVEMENT m
                        on m.KODE_FARM = pe.KODE_FARM
                        and m.KODE_BARANG = pe.KODE_BARANG
                        and m.KODE_BARANG = pd.KODE_BARANG
                        and m.NO_PALLET = pe.NO_PALLET
                        and pe.STATUS_STOK = m.STATUS_STOK
                    join MOVEMENT_D md
                        on md.KODE_FARM = m.KODE_FARM
                        and md.KODE_BARANG = m.KODE_BARANG
                        and md.KODE_BARANG = pd.KODE_BARANG
                        and md.NO_PALLET = m.NO_PALLET
                        and md.NO_PALLET = pe.NO_PALLET
                        and md.NO_REFERENSI = pe.NO_PENERIMAAN
                        and md.STATUS_STOK = m.STATUS_STOK
                    left join (
                        select
                            doe.KODE_BARANG
                            , doe.NO_REG
                            , sum(doe.JML_MUAT) JML_SJ
                        from DO do
                        join DO_D dod
                            on do.NO_DO = dod.NO_DO
                            and do.KODE_FARM = dod.KODE_FARM
                            and do.NO_OP = dod.NO_OP
                                and do.KODE_FARM = '$kode_farm'
                            and do.NO_DO in ('$nomor_do')
                        join DO_E doe
                            on doe.NO_DO = dod.NO_DO
                            and dod.KODE_BARANG = doe.KODE_BARANG
                        group by
                            doe.KODE_BARANG
                            , doe.NO_REG
                    ) sdk
                        on sdk.KODE_BARANG = md.KODE_BARANG
                        and sdk.NO_REG = md.KETERANGAN2
                    left join KANDANG_SIKLUS ks
                        on ks.NO_REG = md.KETERANGAN2
                        and ks.KODE_FARM = md.KODE_FARM
                    left join M_KANDANG mk
                        on mk.KODE_KANDANG = ks.KODE_KANDANG
                        and mk.KODE_FARM = ks.KODE_FARM
                ) s
                    on r.kode_pakan = s.kode_barang


QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detail_penimbangan_pakan($kode_farm,$kode_flok,$no_pallet,$no_kavling, $kode_pakan, $list_kode_pakan) {
        $query = <<<QUERY
            EXEC GENERATE_NO_KAVLING '$kode_farm','$kode_flok','$no_pallet','$no_kavling', '$kode_pakan', '$list_kode_pakan'

QUERY;
        #echo $query;   
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function data_sub_detail_penimbangan_pakan($kode_farm, $nomor_do) {
        $query = <<<QUERY
            select
                doe.KODE_BARANG kode_pakan
                , doe.NO_REG no_reg
                , mk.NAMA_KANDANG nama_kandang
                , sum(doe.JML_MUAT) jml_kebutuhan
            from DO do
            join DO_D dod
                on do.NO_DO = dod.NO_DO
                and do.KODE_FARM = dod.KODE_FARM
                and do.NO_OP = dod.NO_OP
                and do.KODE_FARM = '$kode_farm'
                and do.NO_DO in ('$nomor_do')
            join DO_E doe
                on doe.NO_DO = dod.NO_DO
                and doe.KODE_BARANG = dod.KODE_BARANG
            join KANDANG_SIKLUS ks
                on ks.NO_REG = doe.NO_REG
                and ks.KODE_FARM = do.KODE_FARM
            join M_KANDANG mk
                on mk.KODE_KANDANG = ks.KODE_KANDANG
                and ks.KODE_FARM = mk.KODE_FARM
            group by
                doe.KODE_BARANG
                , doe.NO_REG
                , mk.NAMA_KANDANG
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sub_detail_penimbangan_pakan($kode_farm, $nomor_do) {
        $data = $this->data_sub_detail_penimbangan_pakan($kode_farm, $nomor_do);
        $result=[];
        foreach ($data as $key => $value) {
            $result[$value['kode_pakan']][] = $value;
        }
        return $result;
    }

    public function penimbangan_pakan_bdy_old($kode_farm, $nomor_do, $kode_flok) {
        $data = $this->data_penimbangan_pakan_bdy($kode_farm, $nomor_do, $kode_flok);
        $result=[];
        foreach ($data as $key => $value) {
            $result[$value['kode_pakan']] = array(
                'kode_pakan' => $value['kode_pakan'],
                'nama_pakan' => $value['nama_pakan'],
                'bentuk_pakan' => $value['bentuk_pakan'],
                'jml_sj' => $value['jml_sj'],
            );
        }
        foreach ($data as $key => $value) {
            $result[$value['kode_pakan']]['detail'][$value['no_pallet']] = array(
                'no_pallet' => $value['no_pallet'],
                'no_kavling' => $value['no_kavling'],
                'jml_on_hand' => $value['jml_on_hand'],
            );
        }
        foreach ($data as $key => $value) {
            $result[$value['kode_pakan']]['detail'][$value['no_pallet']]['sub_detail'][] = array(
                'no_reg' => $value['no_reg'],
                'nama_kandang' => $value['nama_kandang'],
                'jml_kebutuhan' => $value['jml_kebutuhan'],
            );
        }
        return $result;

    }

    public function data_penimbangan_pakan_bdy($kode_farm, $nomor_do, $kode_flok) {

        $query = <<<QUERY

            EXEC PENIMBANGAN_PENERIMAAN_PAKAN_BDY '$kode_farm', '$nomor_do', '$kode_flok'

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function data_penimbangan_pakan_brd($kode_farm, $nomor_do) {
        $query = <<<QUERY
            SELECT
              DOD.KODE_BARANG kode_pakan
              , MB.NAMA_BARANG nama_pakan
              , dbo.BENTUK_CONVERTION(MB.BENTUK_BARANG) bentuk_pakan
              , DOD.JML_MUAT jml_sj
              --, ISNULL(SMP.SUM_JUMLAH, 0) jml_terima
              , CASE
                    WHEN ISNULL(SMD.JML_RUSAK, 0) = 0 THEN ISNULL(SMP.SUM_JUMLAH, 0)
                    ELSE ISNULL(DOD.JML_MUAT, 0)-ISNULL(SMD.JML_RUSAK, 0)-ISNULL(SMD.JML_KURANG, 0)
                END jml_terima
              , ISNULL(SMD.JML_RUSAK, 0) jml_tolak
              , ISNULL(SMD.JML_KURANG, 0) jml_hilang
              , KS.NO_REG no_reg
              , MK.NAMA_KANDANG nama_kandang
              , DOE.JENIS_KELAMIN jenis_kelamin
              , DOE.JML_MUAT jml_seharusnya
              --, ISNULL(SMD.BERAT,0) timbangan_kg
              , CAST(ISNULL(SMD.BERAT,0) AS VARCHAR(MAX)) timbangan_kg
              --, ISNULL(MTB.JML_SAK,0) timbangan_sak
              , CASE
                    WHEN MTB.JML_SAK IS NULL AND SMD.BERAT IS NOT NULL THEN
                        isnull((SELECT top 1 ISNULL(result.JML_SAK,0) FROM (
                            SELECT
                                mtb.*
                                , (mtb.BRT_STD*mtb.JML_SAK) - mtb.BATAS_BAWAH data_min
                                , (mtb.BRT_STD*mtb.JML_SAK) + mtb.BATAS_ATAS data_max
                                , 0 result
                            FROM M_TOLERANSI_BERAT mtb
                            WHERE mtb.BRT_STD = 50
                        ) result
                        WHERE result.data_max<=isnull(SMD.BERAT,0)
                        ORDER BY result.data_max DESC),0)
                    ELSE ISNULL(MTB.JML_SAK,0)
                END timbangan_sak
              , ISNULL(SMD.NO_KAVLING,'-') kavling
              , CASE
                  WHEN SMD.NO_KAVLING IS NOT NULL THEN 1
                  ELSE 0
              END selesai
            FROM DO DO
            JOIN DO_D DOD ON DOD.NO_DO = DO.NO_DO
              AND DOD.KODE_FARM = DO.KODE_FARM
              AND DOD.NO_OP = DO.NO_OP
              AND DO.KODE_FARM = '$kode_farm'
              AND DO.NO_DO = '$nomor_do'
            JOIN DO_E DOE ON DOE.NO_DO = DOD.NO_DO
              AND DOE.KODE_BARANG = DOD.KODE_BARANG
            JOIN KANDANG_SIKLUS KS
              ON KS.NO_REG = DOE.NO_REG
              AND KS.KODE_FARM = DO.KODE_FARM
            JOIN M_KANDANG mk
              ON MK.KODE_FARM = KS.KODE_FARM
              AND KS.KODE_KANDANG = MK.KODE_KANDANG
            JOIN M_BARANG mb
              ON MB.KODE_BARANG = DOD.KODE_BARANG
            LEFT JOIN (
              SELECT
                MD.KODE_FARM
                , M.KETERANGAN1 NO_REG
                , MD.KODE_BARANG
                , MD.JENIS_KELAMIN
                , P.KETERANGAN1 NO_DO
                , PE.NO_PALLET
                , MD.NO_KAVLING
                , PE.JUMLAH
                , PE.BERAT
                , PE.STATUS_STOK
                , PD.JML_TERIMA
                , PD.JML_RUSAK
                , PD.JML_KURANG
              FROM PENERIMAAN P
              LEFT JOIN PENERIMAAN_D PD
                ON PD.NO_PENERIMAAN = P.NO_PENERIMAAN
                AND PD.KODE_FARM = P.KODE_FARM
                AND P.KODE_FARM = '$kode_farm'
                AND P.KETERANGAN1 = '$nomor_do'
              LEFT JOIN PENERIMAAN_E PE
                ON PE.KODE_FARM = PD.KODE_FARM
                AND PE.NO_PENERIMAAN = PD.NO_PENERIMAAN
                AND PE.KODE_BARANG = PD.KODE_BARANG
              LEFT JOIN MOVEMENT_D MD
                ON MD.KODE_FARM = PE.KODE_FARM
                AND MD.KODE_BARANG = PE.KODE_BARANG
                AND MD.NO_PALLET = PE.NO_PALLET
                AND PE.NO_PENERIMAAN = MD.NO_REFERENSI
                AND MD.KETERANGAN1 = 'PUT'
              LEFT JOIN MOVEMENT M
                ON M.KODE_FARM = MD.KODE_FARM
                AND M.NO_PALLET = MD.NO_PALLET
                AND M.KODE_BARANG = MD.KODE_BARANG
                AND M.JENIS_KELAMIN = MD.JENIS_KELAMIN
                AND M.NO_KAVLING = MD.NO_KAVLING
              WHERE PE.NO_PENERIMAAN IS NOT NULL
            ) SMD
              ON SMD.KODE_FARM = DO.KODE_FARM
              AND SMD.NO_REG = DOE.NO_REG
              AND SMD.KODE_BARANG = DOE.KODE_BARANG
              AND SMD.JENIS_KELAMIN = DOE.JENIS_KELAMIN
              AND SMD.NO_DO = DOE.NO_DO
            LEFT JOIN (
              SELECT
                PE.KODE_FARM
                , PE.KODE_BARANG
                , P.KETERANGAN1 NO_DO
                , SUM(PE.JUMLAH) SUM_JUMLAH
              FROM PENERIMAAN P
              LEFT JOIN PENERIMAAN_D PD
                ON PD.NO_PENERIMAAN = P.NO_PENERIMAAN
                AND PD.KODE_FARM = P.KODE_FARM
                AND P.KODE_FARM = '$kode_farm'
                AND P.KETERANGAN1 = '$nomor_do'
              LEFT JOIN PENERIMAAN_E PE
                ON PE.KODE_FARM = PD.KODE_FARM
                AND PE.NO_PENERIMAAN = PD.NO_PENERIMAAN
                AND PE.KODE_BARANG = PD.KODE_BARANG
                GROUP BY
                  PE.KODE_FARM
                  , PE.KODE_BARANG
                  , P.KETERANGAN1
            ) SMP
              ON SMP.KODE_FARM = DO.KODE_FARM
              AND SMP.KODE_BARANG = DOD.KODE_BARANG
              AND SMP.NO_DO = DO.NO_DO
            LEFT JOIN M_TOLERANSI_BERAT MTB
              ON ((MTB.BRT_STD*MTB.JML_SAK) - MTB.BATAS_BAWAH) <= SMD.BERAT
              AND ((MTB.BRT_STD*MTB.JML_SAK) + MTB.BATAS_ATAS) >= SMD.BERAT
              AND MTB.BRT_STD = 50
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pakan_rusak_hilang($kode_farm, $nomor_do, $kode_barang) {
        $query = <<<QUERY
            SELECT
                hdr.*
                , md.JML_PUTAWAY jml_putaway
                , md.BERAT_PUTAWAY berat_putaway
                , md.KETERANGAN1 keterangan_rusak
                , case
                    when hdr.konfirmasi = 1 and hdr.jml_rusak_old = 0 then -1
                    else hdr.jml_rusak_old
                end jml_rusak
                , BAD.ATTACHMENT_NAME attachment_name
                , BAD.NO_BA no_ba
            FROM (
                SELECT DISTINCT
                    pd.KODE_BARANG kode_barang
                    , mb.NAMA_BARANG nama_barang
                    , CASE
                        WHEN MB.BENTUK_BARANG = 'T' THEN 'TEPUNG'
                        WHEN MB.BENTUK_BARANG = 'C' THEN 'CRUMBLE'
                        WHEN MB.BENTUK_BARANG = 'P' THEN 'PALLET'
                        WHEN MB.BENTUK_BARANG = 'A' THEN 'CAIR'
                        ELSE ''
                    END bentuk_barang
                    , pd.JML_SJ jml_sj
                    , pd.JML_TERIMA jml_aktual
                    , (pd.JML_SJ - pd.JML_TERIMA) jml_sisa
                    , p.NO_PENERIMAAN no_penerimaan
                    , p.KODE_FARM kode_farm
                    , pd.JML_RUSAK jml_rusak_old
                    , case
                        when pd.JML_KURANG <= 0 then
                            case
                                when pd.JML_KURANG+pd.JML_RUSAK+pd.JML_TERIMA = pd.JML_SJ then -1
                                else (pd.JML_SJ - pd.JML_TERIMA)
                            end
                        else pd.JML_KURANG
                    end jml_kurang
                    , case
                        when pd.JML_KURANG+pd.JML_RUSAK+pd.JML_TERIMA = pd.JML_SJ then 1
                        else 0
                    end konfirmasi
                    , pd.KETERANGAN1 keterangan_kurang
                FROM PENERIMAAN p
                JOIN PENERIMAAN_D pd ON p.NO_PENERIMAAN = pd.NO_PENERIMAAN AND p.KODE_FARM = pd.KODE_FARM
                JOIN PENERIMAAN_E pe ON pe.KODE_FARM = pd.KODE_FARM AND pe.NO_PENERIMAAN = pd.NO_PENERIMAAN
                JOIN M_BARANG mb ON mb.KODE_BARANG = pd.KODE_BARANG
                WHERE p.KETERANGAN1 = '$nomor_do'
                AND p.KODE_FARM = '$kode_farm'
                AND pd.KODE_BARANG = '$kode_barang'
                AND pd.JML_SJ > pd.JML_TERIMA
            ) hdr
            LEFT JOIN MOVEMENT_D md ON md.NO_REFERENSI = hdr.NO_PENERIMAAN
                AND hdr.KODE_FARM = md.KODE_FARM
                AND md.KODE_BARANG = hdr.kode_barang
                AND md.NO_KAVLING = 'DMG'
            LEFT JOIN BERITA_ACARA BA
                ON BA.KODE_FARM = hdr.kode_farm
                AND BA.NO_PENERIMAAN = hdr.no_penerimaan
            LEFT JOIN BERITA_ACARA_D BAD
                ON BAD.KODE_FARM = BA.kode_farm
                AND BA.NO_BA = BAD.NO_BA
                AND hdr.kode_barang = BAD.KODE_BARANG
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function cek_kode_verifikasi_kavling($data) {
        $no_kavling = $data ['no_kavling'];
        $kode_farm = $data ['kode_farm'];
        $kode_verifikasi = $data ['kode_verifikasi'];
        $query = <<<QUERY
            SELECT * FROM M_KAVLING
            WHERE NO_KAVLING = :no_kavling
            AND KODE_FARM = :kode_farm
            AND KODE_VERIFIKASI = :kode_verifikasi
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->bindParam(':kode_farm', $kode_farm);
        $stmt->bindParam(':no_kavling', $no_kavling);
        $stmt->bindParam(':kode_verifikasi', $kode_verifikasi);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function susun_data_berita_acara($kode_farm, $no_berita_acara) {
        $data = $this->get_data_berita_acara($kode_farm, $no_berita_acara);
        $result = [];
        foreach ($data as $key => $value) {
            $result = array(
                'no_ba' => $value['no_ba'],
                'tgl_kedatangan' => $value['tgl_kedatangan'],
                'nama_farm' => $value['nama_farm'],
                'no_sj' => $value['no_sj'],
                'no_spm' => $value['no_spm'],
                'no_op' => $value['no_op'],
                'no_penerimaan' => $value['no_penerimaan'],
                'ekspedisi' => $value['ekspedisi'],
                'no_kendaraan_terima' => $value['no_kendaraan_terima'],
                'nama_sopir' => $value['nama_sopir']
            );
        }
        foreach ($data as $key => $value) {
            $result['detail_barang'][$value['kode_barang']] = array(
                'kode_barang' => $value['kode_barang'],
                'nama_barang' => $value['nama_barang'],
                'bentuk_barang' => $value['bentuk_barang'],
                'jml_sj' => $value['jml_sj'],
                'jml_rusak' => $value['jml_rusak'],
                'jml_kurang' => $value['jml_kurang'],
                'keterangan_kurang' => $value['keterangan_kurang']
            );
        }
        foreach ($data as $key => $value) {
            $result['detail_barang'][$value['kode_barang']]['detail_timbang'][] = array(
                'jml_putaway' => $value['jml_putaway'],
                'berat_putaway' => $value['berat_putaway'],
                'keterangan_rusak' => $value['keterangan_rusak']
            );
        }
        return $result;
    }

    public function get_data_berita_acara($kode_farm,$no_berita_acara) {
        $query = <<<QUERY
            SELECT DISTINCT
                p.NO_PENERIMAAN no_penerimaan
                , REPLACE(CONVERT(VARCHAR(10),ba.TGL_BUAT,105),'-',' ') tgl_buat
                , REPLACE(CONVERT(VARCHAR(10),p.TGL_TERIMA,105),'-',' ') tgl_kedatangan
                , p.NAMA_EKSPEDISI ekspedisi
                , ba.NO_BA no_ba
                , p.KODE_SURAT_JALAN no_sj
                , p.NO_OP no_op
                , p.KODE_FARM kode_farm
                , MF.NAMA_FARM nama_farm
                , p.NAMA_SOPIR nama_sopir
                , p.NO_KENDARAAN_TERIMA no_kendaraan_terima
                , p.NO_SPM no_spm
                , pd.KODE_BARANG kode_barang
                , MB.NAMA_BARANG nama_barang
                , CASE
                    WHEN MB.BENTUK_BARANG = 'T' THEN 'TEPUNG'
                    WHEN MB.BENTUK_BARANG = 'C' THEN 'CRUMBLE'
                    WHEN MB.BENTUK_BARANG = 'P' THEN 'PALLET'
                    WHEN MB.BENTUK_BARANG = 'A' THEN 'CAIR'
                    ELSE ''
                END bentuk_barang
                , pd.JML_SJ jml_sj
                , pd.JML_RUSAK jml_rusak
                , pd.JML_KURANG jml_kurang
                , CASE
                    WHEN pd.JML_KURANG > 0 THEN pd.KETERANGAN1
                    ELSE ''
                END keterangan_kurang
                , ba.TIPE_BA tipe_ba
                , md.JML_PUTAWAY jml_putaway
                , md.BERAT_PUTAWAY berat_putaway
                , md.KETERANGAN1 keterangan_rusak
            FROM PENERIMAAN p
            JOIN PENERIMAAN_D pd ON p.NO_PENERIMAAN = pd.NO_PENERIMAAN AND p.KODE_FARM = pd.KODE_FARM
            JOIN M_BARANG MB ON MB.KODE_BARANG = pd.KODE_BARANG
            JOIN M_FARM MF ON MF.KODE_FARM = p.KODE_FARM
            LEFT JOIN BERITA_ACARA ba ON ba.NO_PENERIMAAN = p.NO_PENERIMAAN
            LEFT JOIN BERITA_ACARA_D bad ON bad.NO_BA = ba.NO_BA AND ba.KODE_FARM = bad.KODE_FARM AND bad.KODE_BARANG = pd.KODE_BARANG AND bad.KODE_FARM = pd.KODE_FARM
            LEFT JOIN MOVEMENT_D md ON md.NO_REFERENSI = pd.NO_PENERIMAAN
                AND pd.KODE_FARM = md.KODE_FARM
                AND md.KODE_BARANG = pd.kode_barang
                AND md.NO_KAVLING = 'DMG'
            WHERE ba.NO_BA = '$no_berita_acara'
            AND p.KODE_FARM = '$kode_farm'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function check_ekspedisi($nama_ekspedisi, $nopol_ekspedisi) {
        $query = <<<QUERY
            SELECT
                COUNT(*) ada
            FROM M_EKSPEDISI me
            JOIN M_EKPEDISI_VEHICLE mev ON mev.KODE_EKSPEDISI = me.KODE_EKSPEDISI
            WHERE me.NAMA_EKSPEDISI = '$nama_ekspedisi'
            AND mev.NO_KENDARAAN = '$nopol_ekspedisi'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validasi_tutup($no_penerimaan) {
        $query = <<<QUERY
            select (
                SELECT count(*) FROM PENERIMAAN_E
                WHERE NO_PENERIMAAN = '$no_penerimaan'
                AND STATUS_STOK IS NULL
            ) validasi_timbang
            , (
                SELECT count(*) FROM PENERIMAAN_D
                WHERE NO_PENERIMAAN = '$no_penerimaan'
                and JML_SJ <> JML_TERIMA+JML_KURANG+JML_RUSAK
            ) validasi_rk
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_daftar_penerimaan($tanggal_kirim_awal = NULL, $tanggal_kirim_akhir = NULL, $kode_farm) {
        $tgl_where = (!empty($tanggal_kirim_awal) && (!empty($tanggal_kirim_akhir))) ? "and opv.TGL_KIRIM BETWEEN '$tanggal_kirim_awal' AND '$tanggal_kirim_akhir'" : "";
        $query = <<<QUERY
            SELECT
                p.NO_OP no_op
                , ISNULL(ba.NO_BA,'-') no_berita_acara
                , p.NO_PENERIMAAN no_penerimaan
                , p.KODE_SURAT_JALAN no_sj
                , p.NAMA_EKSPEDISI ekspedisi
                , REPLACE(CONVERT(VARCHAR(10),opv.TGL_KIRIM,105),'-',' ') tanggal_kirim
                , p.KETERANGAN1 no_do
                , p.NO_KENDARAAN_KIRIM no_kendaraan_kirim
                , p.NO_SPM no_spm
                , pd.KODE_BARANG kode_barang
                , pd.JML_SJ jml_sj
            FROM PENERIMAAN p
            JOIN (
                SELECT
                    pd.KODE_FARM
                    , pd.NO_PENERIMAAN
                    , pd.KODE_BARANG
                    , pd.JML_SJ
                FROM PENERIMAAN p
                JOIN PENERIMAAN_D pd on pd.NO_PENERIMAAN = p.NO_PENERIMAAN and pd.KODE_FARM = p.KODE_FARM
                where p.KODE_FARM = '$kode_farm'
            ) pd on p.KODE_FARM = pd.KODE_FARM and p.NO_PENERIMAAN = pd.NO_PENERIMAAN
            JOIN OP_VEHICLE opv ON opv.NO_OP = p.NO_OP
            LEFT JOIN BERITA_ACARA ba ON ba.NO_PENERIMAAN = p.NO_PENERIMAAN AND ba.KODE_FARM = p.KODE_FARM
            WHERE p.KODE_FARM = '$kode_farm'
            $tgl_where
            GROUP BY opv.TGL_KIRIM
                    , p.NO_PENERIMAAN
                    , p.NO_OP
                    , ba.NO_BA
                    , p.KODE_SURAT_JALAN
                    , p.NAMA_EKSPEDISI
                    , p.KETERANGAN1
                    , p.NO_KENDARAAN_KIRIM
                    , p.NO_SPM
                    , pd.KODE_BARANG
                    , pd.JML_SJ
            ORDER BY opv.TGL_KIRIM DESC
                    , p.NO_PENERIMAAN DESC
                    , p.NO_OP
                    , ba.NO_BA
                    , p.KODE_SURAT_JALAN
                    , p.NAMA_EKSPEDISI
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tutup_otomatis($nomor_do, $kode_farm) {
        $query = <<<QUERY
            EXEC TUTUP_SURAT_JALAN '$kode_farm','$nomor_do'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        // return ($result['STATUS_TERIMA'] == 'C') ? 1 : 0 ;

        return $result ['result'];
    }

    public function rekomendasi_kavling($kode_farm, $nama_gudang, $no_reg, $jumlah_zak, $kavling) {
        $query = <<<QUERY
            EXEC REKOMENDASI_KAVLING '$kode_farm','$nama_gudang','$no_reg','$jumlah_zak','$kavling'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ['result'];
    }

    public function group_layout_kavling($kode_farm, $grup_farm) {
        $alldata = ($grup_farm == 'bdy') ? $this->layout_kavling_bdy($kode_farm) : $this->layout_kavling($kode_farm);

        $result = [];
        foreach ($alldata as $key => $item) {
            $result ['max_no_baris'] = $item ['MAX_NO_BARIS'];
            $result ['data_kavling'] [$item ['NAMA_GUDANG']] [$item ['NO_POSISI']] [$item ['LAYOUT_POSISI']] [$item ['NO_KOLOM']] [$item ['NO_BARIS']] [$item ['NO_KAVLING']] [] = $item;
            $result ['data_kolom'] [$item ['NAMA_GUDANG']] [$item ['NO_POSISI']] [$item ['LAYOUT_POSISI']] = array(
                'min_kolom' => $item['min_kolom'],
                'max_kolom' => $item['max_kolom']
               );
        }
        /*
         * foreach($result as $key1=>$item1){
         * foreach($result as $key2=>$item2){
         * foreach($result as $key3=>$item3){
         * $result ;
         * }
         * }
         * }
         */
        return $result;
    }

    public function layout_kavling($kode_farm) {
        $query = <<<QUERY
            --EXEC LAYOUT_KAVLING_STOCK '$kode_farm'
            EXEC BROWSE_KAVLING '$kode_farm'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function layout_kavling_bdy($kode_farm) {
        $query = <<<QUERY
            --EXEC LAYOUT_KAVLING_STOCK '$kode_farm'
            EXEC BROWSE_KAVLING_BDY '$kode_farm'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cek_konversi($berat_standart,$berat) {
        $query = <<<QUERY
        SELECT top 1 * FROM (
            SELECT
                mtb.*
                , (mtb.BRT_STD*mtb.JML_SAK) + mtb.BATAS_BAWAH data_min
                , (mtb.BRT_STD*mtb.JML_SAK) + mtb.BATAS_ATAS data_max
                , 0 result
            FROM M_TOLERANSI_BERAT mtb
            WHERE mtb.BRT_STD = $berat_standart
        ) result
        WHERE result.data_max>=$berat
        ORDER BY result.data_max 
QUERY;
       
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cek_diluar_toleransi($berat_standart,$berat) {
        $query = <<<QUERY
            SELECT top 1 * FROM (
                SELECT
                    mtb.*
                    , (mtb.BRT_STD*mtb.JML_SAK) - mtb.BATAS_BAWAH data_min
                    , (mtb.BRT_STD*mtb.JML_SAK) + mtb.BATAS_ATAS data_max
                    , 0 result
                FROM M_TOLERANSI_BERAT mtb
                WHERE mtb.BRT_STD = $berat_standart
            ) result
            WHERE result.data_max<=$berat
            ORDER BY result.data_max DESC
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cek_maks_pallet($kode_farm,$no_kavling) {
        $query = <<<QUERY
            select
                isnull(MAX_KUANTITAS,1000) maks
            from M_KAVLING mk
            join M_GUDANG mg
                on mk.KODE_FARM = mg.KODE_FARM
                and mk.KODE_GUDANG = mg.KODE_GUDANG
            where mk.NO_KAVLING = left('$no_kavling',5)
            and mk.KODE_FARM = '$kode_farm'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function detail_penerimaan($kode_farm, $no_penerimaan, $no_op) {
        $query = <<<QUERY
            select distinct
                pd.KODE_BARANG kode_barang
                , MB.NAMA_BARANG nama_barang
                , CASE
                    WHEN MB.BENTUK_BARANG = 'T' THEN 'TEPUNG'
                    WHEN MB.BENTUK_BARANG = 'C' THEN 'CRUMBLE'
                    WHEN MB.BENTUK_BARANG = 'P' THEN 'PALLET'
                    WHEN MB.BENTUK_BARANG = 'A' THEN 'CAIR'
                    ELSE ''
                END bentuk_barang
                , opv.BERAT_KIRIM berat_sj
                , opv.JML_KIRIM jumlah_sj
                , pd.JML_TERIMA terima_baik_zak
                , case
                        when pd.BERAT_TERIMA = 0 then 0
                        else pd.BERAT_TERIMA
                end terima_baik_kg
                , pd.JML_RUSAK terima_rusak_zak
                , case
                        when pd.BERAT_RUSAK = 0 then 0
                        else pd.BERAT_RUSAK
                end terima_rusak_kg
                , pd.JML_KURANG jumlah_kurang_zak
                , p.NAMA_EKSPEDISI +' '+p.NO_KENDARAAN_TERIMA keterangan
                , uom.DESKRIPSI satuan
                , 'Feedmill' asal_terima_dari
                , 'Pasuruan' kota
                , p.NO_OP no_op
                , p.KODE_SURAT_JALAN no_sj
                , p.NO_PENERIMAAN no_bpb
                , ba.NO_BA no_ba
            from PENERIMAAN p
            join PENERIMAAN_D pd on p.NO_PENERIMAAN = pd.NO_PENERIMAAN and p.KODE_FARM = pd.KODE_FARM
            join M_BARANG mb on mb.KODE_BARANG = pd.KODE_BARANG
            join OP_VEHICLE opv on opv.KODE_BARANG = pd.KODE_BARANG and p.NO_OP = opv.NO_OP and pd.JML_SJ = opv.JML_KIRIM
            join M_UOM uom on uom.UOM = mb.UOM
            left join BERITA_ACARA ba on ba.KODE_FARM = p.KODE_FARM and ba.NO_PENERIMAAN = p.NO_PENERIMAAN
            where p.NO_PENERIMAAN = '$no_penerimaan'
            and p.KODE_FARM = '$kode_farm'
            and opv.NO_OP = '$no_op'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validasi_barcode_penerimaan($kode_farm, $no_sj, $no_do, $no_op, $no_spm, $all_kode_barang, $all_jumlah) {
        $query = <<<QUERY
            EXEC VALIDASI_BARCODE_PENERIMAAN_PAKAN_BARU
                '$kode_farm',
                '$no_sj',
                '$no_do',
                '$no_op',
                '$no_spm',
                '$all_kode_barang',
                '$all_jumlah'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_header_barang($data, $kode_farm) {
        $result = [];
        foreach ($data as $key => $value) {

            $no_op = $value ['no_op'];
            $no_do = $value ['no_do'];
            $kode_barang = $value ['kode_barang'];
            $jumlah = $value ['jumlah'];

            $query = <<<QUERY
                    SELECT
                        result.*
                        ----, result.jumlah_aktual_old+ISNULL(pd.JML_RUSAK,0) jumlah_aktual
                        ----, result.berat_aktual_old+ISNULL(pd.BERAT_RUSAK,0) berat_aktual
                        --, ISNULL(pd.JML_TERIMA,0)+ISNULL(pd.JML_RUSAK,0) jumlah_aktual
                        --, ISNULL(pd.BERAT_TERIMA,0)+ISNULL(pd.BERAT_RUSAK,0) berat_aktual
                        , ISNULL(pd.JML_TERIMA,0) jumlah_aktual
                        --, ISNULL(pd.BERAT_TERIMA,0) berat_aktual
                        , CASE
                            WHEN result.berat_aktual_old = 0 THEN result.berat_aktual_old
                            ELSE ISNULL(pd.BERAT_TERIMA,0)
                        END berat_aktual
                        , (select VALUE from SYS_CONFIG where DESCRIPTION = 'TOLERANSI') toleransi
                    FROM (
                        SELECT
                            kode_farm,no_penerimaan,status_terima,kode_barang
                            , no_kendaraan_terima,nama_sopir,nama_ekspedisi
                            --,no_pallet
                            , nama_barang,bentuk_barang,jumlah_sj,berat_sj
                            , ISNULL(SUM(TMP.jumlah_aktual),0) jumlah_aktual_old
                            , ISNULL(SUM(TMP.berat_aktual),0) berat_aktual_old
                        FROM (
                            SELECT DISTINCT
                                OP.KODE_FARM kode_farm
                                , P.NO_PENERIMAAN no_penerimaan
                                , MD.NO_PALLET no_pallet
                                , P.STATUS_TERIMA status_terima
                                , P.NO_KENDARAAN_TERIMA no_kendaraan_terima
                                , P.NAMA_SOPIR nama_sopir
                                , ME.NAMA_EKSPEDISI nama_ekspedisi
                                --, PE.NO_PALLET no_pallet
                                , OPV.KODE_BARANG kode_barang
                                , MB.NAMA_BARANG nama_barang
                                , CASE
                                    WHEN MB.BENTUK_BARANG = 'T' THEN 'TEPUNG'
                                    WHEN MB.BENTUK_BARANG = 'C' THEN 'CRUMBLE'
                                    WHEN MB.BENTUK_BARANG = 'P' THEN 'PALLET'
                                    WHEN MB.BENTUK_BARANG = 'A' THEN 'CAIR'
                                    ELSE ''
                                    END bentuk_barang
                                , OPV.JML_KIRIM jumlah_sj
                                , OPV.BERAT_KIRIM berat_sj
                                , CASE
                                    WHEN MD.JML_ON_PUTAWAY > 0 AND MD.JML_PUTAWAY <= 0 THEN 0
                                    ELSE ISNULL(PE.JUMLAH,0)
                                END jumlah_aktual
                                , CASE
                                    WHEN MD.JML_ON_PUTAWAY > 0 AND MD.JML_PUTAWAY <= 0 THEN 0
                                    ELSE ISNULL(PE.BERAT,0)
                                END berat_aktual
                                --, ISNULL(PE.JUMLAH,0) jumlah_aktual
                                --, ISNULL(PE.BERAT,0) berat_aktual
                            FROM OP_VEHICLE OPV
                            JOIN M_BARANG MB ON MB.KODE_BARANG = OPV.KODE_BARANG
                            JOIN OP OP ON OP.NO_OP = OPV.NO_OP
                            JOIN M_EKSPEDISI ME ON ME.KODE_EKSPEDISI = OPV.KODE_EKSPEDISI
                            LEFT JOIN PENERIMAAN P ON P.NO_OP = OP.NO_OP AND P.KODE_FARM = OP.KODE_FARM AND P.KETERANGAN1 = '$no_do'
                            LEFT JOIN PENERIMAAN_E PE ON PE.KODE_FARM = P.KODE_FARM AND PE.NO_PENERIMAAN = P.NO_PENERIMAAN AND OPV.KODE_BARANG = PE.KODE_BARANG
                            LEFT JOIN MOVEMENT_D MD ON MD.NO_PALLET = PE.NO_PALLET AND MD.KODE_FARM = PE.KODE_FARM AND MD.KODE_BARANG = PE.KODE_BARANG AND MD.KETERANGAN1 = 'PUT' AND MD.NO_REFERENSI = PE.NO_PENERIMAAN
                            WHERE OPV.NO_OP = :no_op
                            AND OPV.KODE_BARANG = :kode_barang
                            AND OPV.JML_KIRIM = :jumlah
                            AND OP.KODE_FARM = :kode_farm
                        ) TMP
                        GROUP BY kode_farm,no_penerimaan,status_terima--,no_pallet
                                ,no_kendaraan_terima,nama_sopir,nama_ekspedisi
                                ,kode_barang,nama_barang,bentuk_barang,jumlah_sj,berat_sj
                    ) result
                    LEFT JOIN PENERIMAAN_D pd ON pd.NO_PENERIMAAN = result.no_penerimaan AND pd.KODE_FARM = result.kode_farm AND pd.KODE_BARANG = result.kode_barang
QUERY;
            // echo $query;
            $stmt = $this->dbSqlServer->conn_id->prepare($query);
            $stmt->bindParam(':no_op', $no_op);
            $stmt->bindParam(':kode_barang', $kode_barang);
            $stmt->bindParam(':jumlah', $jumlah);
            $stmt->bindParam(':kode_farm', $kode_farm);
            $stmt->execute();

            $tmp_result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (isset($tmp_result ['berat_sj'])) {
                $result [] = $tmp_result;
            }
        }
        $message = (count($data) == count($result)) ? 1 : 0;
        return array(
            'message' => $message,
            'result' => $result
        );
    }

    public function konfirmasi_selesai($data, $user, $kode_farm) {
        $result = 0;

        $this->dbSqlServer->conn_id->beginTransaction();

        $timbang = $this->simpan_hasil_timbang($data, $user);

        if($timbang['result'] == 1){
            $data ['status_barang'] = 'NM';
            $data ['kode_farm'] = $kode_farm;
            $data ['sys_no_kavling'] = $timbang['kavling'];
            $data ['no_kavling'] = $timbang['kavling'];
            $data ['no_pallet'] = $timbang['no_pallet'];
            $data ['no_penerimaan'] = $timbang['no_penerimaan'];
            $data ['keterangan2'] = 1;
            $data ['nama_gudang'] = NULL;

            $konfirmasi = $this->simpan_konfirmasi($data, $user);

            $result = $konfirmasi['result'];
        }

        $data['result'] = $result;

        if($result == 1){
            $this->dbSqlServer->conn_id->commit();
        }
        else{
            $this->dbSqlServer->conn_id->rollback();
        }

        return $data;


    }

    public function simpan_hasil_timbang($data, $user) {
        $no_sj = $data ['no_sj'];
        $tanggal_sj = $data ['tanggal_sj'];
        $kuantitas_kg = $data ['kuantitas_kg'];
        $kuantitas_zak = $data ['kuantitas_zak'];
        $tanggal_verifikasi_do = $data ['tanggal_verifikasi_do'];
        $no_op = $data ['no_op'];
        $no_do = $data ['no_do'];
        $no_spm = NULL; #$data ['no_spm'];
        $nama_ekspedisi = $data ['nama_ekspedisi'];
        $no_kendaraan_kirim = $data ['no_kendaraan_kirim'];
        $no_kendaraan_terima = $data ['no_kendaraan_terima'];
        $nama_sopir = $data ['nama_sopir'];
        $kode_barang = $data ['kode_barang'];
        $all_kode_barang = $data ['all_kode_barang'];
        $berat_aktual = $data ['berat_aktual'];
        $jumlah_aktual = $data ['jumlah_aktual'];
        $all_jumlah = $data ['all_jumlah'];
        $no_reg = $data ['no_reg'];
        $jenis_kelamin = $data ['jenis_kelamin'];
        $query = <<<QUERY
            EXEC SIMPAN_TIMBANG_PENERIMAAN_PAKAN_BARU
                '$no_sj',
                '$tanggal_sj',
                '$kuantitas_kg',
                '$kuantitas_zak',
                '$tanggal_verifikasi_do',
                '$no_op',
                '$no_do',
                '$no_spm',
                '$nama_ekspedisi',
                '$no_kendaraan_kirim',
                '$no_kendaraan_terima',
                :nama_sopir,
                '$kode_barang',
                '$all_kode_barang',
                $berat_aktual,
                $jumlah_aktual,
                '$all_jumlah',
                '$user',
                'PUT',
                'BY SYSTEM',
                '$no_reg',
                '$jenis_kelamin'

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->bindParam ( ':nama_sopir', $nama_sopir );
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function simpan_konfirmasi($data, $user) {
        $status_barang = $data ['status_barang'];
        $kode_farm = $data ['kode_farm'];
        $sys_no_kavling = $data ['sys_no_kavling'];
        $no_kavling = $data ['no_kavling'];
        $no_pallet = $data ['no_pallet'];
        $kode_barang = $data ['kode_barang'];
        $no_penerimaan = $data ['no_penerimaan'];
        $jumlah_aktual = $data ['jumlah_aktual'];
        $berat_aktual = $data ['berat_aktual'];
        $nama = $user;
        $tmp_keterangan2 = $data ['keterangan2'];
        $keterangan2 = ($tmp_keterangan2 == 1) ? 'SYSTEM' : $nama;
        $no_reg = $data ['no_reg'];
        $jenis_kelamin = $data ['jenis_kelamin'];
        $nama_gudang = $data ['nama_gudang'];

        $query = <<<QUERY

            EXEC KONFIRMASI_PENERIMAAN_PAKAN_BARU
            '$kode_farm',
            '$sys_no_kavling',
            '$no_kavling',
            '$no_pallet',
            '$kode_barang',
            '$status_barang',
            '$no_penerimaan',
            $jumlah_aktual,
            $berat_aktual,
            '$nama',
            'PUT',
            $tmp_keterangan2,
            'BY $keterangan2',
            '$no_reg',
            '$jenis_kelamin'

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_detail_barang($kode_farm, $no_penerimaan, $no_pallet, $kode_barang, $jumlah, $no_op, $no_do) {
        $query = <<<QUERY

            EXEC DETAIL_BARANG_PENERIMAAN_PAKAN_TERBARU
               '$kode_farm'
              ,'$no_penerimaan'
              ,'$no_pallet'
              ,'$kode_barang'
              ,$jumlah
              ,'PUT'
              ,'$no_op'
              ,'$no_do'

QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        // $stmt->bindParam ( ':kode_farm', $kode_farm );
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function susun_detail_barang_rk($kode_farm, $no_penerimaan) {
        $data = $this->get_detail_barang_rk($kode_farm, $no_penerimaan);
        $result = [];
        foreach ($data as $key => $value) {
            $result['header_barang'][$value['kode_barang']] = array(
                'kode_barang' => $value['kode_barang'],
                'nama_barang' => $value['nama_barang'],
                'bentuk_barang' => $value['bentuk_barang'],
                'jml_sj' => $value['jml_sj'],
                'jml_rusak' => $value['jml_rusak'],
                'jml_kurang' => $value['jml_kurang'],
                'keterangan_kurang' => $value['keterangan_kurang']
            );
            $result['konfirmasi'] = $value['konfirmasi'];
        }
        foreach ($data as $key => $value) {
            $result['header_barang'][$value['kode_barang']]['detail_barang'][] = array(
                'jml_putaway' => $value['jml_putaway'],
                'berat_putaway' => $value['berat_putaway'],
                'keterangan_rusak' => $value['keterangan_rusak']
            );
        }
        return $result;
    }

    public function get_detail_barang_rk($kode_farm, $no_penerimaan) {
        $query = <<<QUERY
            SELECT
                hdr.*
                , md.JML_PUTAWAY jml_putaway
                , md.BERAT_PUTAWAY berat_putaway
                , md.KETERANGAN1 keterangan_rusak
                , case
                    when hdr.konfirmasi = 1 and hdr.jml_rusak_old = 0 then -1
                    else hdr.jml_rusak_old
                end jml_rusak
            FROM (
                SELECT DISTINCT
                    pd.KODE_BARANG kode_barang
                    , mb.NAMA_BARANG nama_barang
                    , CASE
                        WHEN MB.BENTUK_BARANG = 'T' THEN 'TEPUNG'
                        WHEN MB.BENTUK_BARANG = 'C' THEN 'CRUMBLE'
                        WHEN MB.BENTUK_BARANG = 'P' THEN 'PALLET'
                        WHEN MB.BENTUK_BARANG = 'A' THEN 'CAIR'
                        ELSE ''
                    END bentuk_barang
                    , pd.JML_SJ jml_sj
                    , pd.JML_TERIMA jml_aktual
                    , (pd.JML_SJ - pd.JML_TERIMA) jml_sisa
                    , p.NO_PENERIMAAN no_penerimaan
                    , p.KODE_FARM kode_farm
                    , pd.JML_RUSAK jml_rusak_old
                    , case
                        when pd.JML_KURANG <= 0 then
                            case
                                when pd.JML_KURANG+pd.JML_RUSAK+pd.JML_TERIMA = pd.JML_SJ then -1
                                else (pd.JML_SJ - pd.JML_TERIMA)
                            end
                        else pd.JML_KURANG
                    end jml_kurang
                    , case
                        when pd.JML_KURANG+pd.JML_RUSAK+pd.JML_TERIMA = pd.JML_SJ then 1
                        else 0
                    end konfirmasi
                    , pd.KETERANGAN1 keterangan_kurang
                FROM PENERIMAAN p
                JOIN PENERIMAAN_D pd ON p.NO_PENERIMAAN = pd.NO_PENERIMAAN AND p.KODE_FARM = pd.KODE_FARM
                JOIN PENERIMAAN_E pe ON pe.KODE_FARM = pd.KODE_FARM AND pe.NO_PENERIMAAN = pd.NO_PENERIMAAN
                JOIN M_BARANG mb ON mb.KODE_BARANG = pd.KODE_BARANG
                WHERE p.NO_PENERIMAAN = '$no_penerimaan'
                AND p.KODE_FARM = '$kode_farm'
                AND pd.JML_SJ > pd.JML_TERIMA
            ) hdr
            LEFT JOIN MOVEMENT_D md ON md.NO_REFERENSI = hdr.NO_PENERIMAAN
                AND hdr.KODE_FARM = md.KODE_FARM
                AND md.KODE_BARANG = hdr.kode_barang
                AND md.NO_KAVLING = 'DMG'



            --SELECT DISTINCT
            --    pd.KODE_BARANG kode_barang
            --    , mb.NAMA_BARANG nama_barang
            --    , CASE
            --        WHEN MB.BENTUK_BARANG = 'T' THEN 'TEPUNG'
            --        WHEN MB.BENTUK_BARANG = 'C' THEN 'CRUMBLE'
            --        WHEN MB.BENTUK_BARANG = 'P' THEN 'PALLET'
            --        WHEN MB.BENTUK_BARANG = 'A' THEN 'CAIR'
            --        ELSE ''
            --    END bentuk_barang
            --    , pd.JML_SJ jml_sj
            --    , pd.JML_TERIMA jml_aktual
            --    , (pd.JML_SJ - pd.JML_TERIMA) jml_sisa
            --FROM PENERIMAAN p
            --JOIN PENERIMAAN_D pd ON p.NO_PENERIMAAN = pd.NO_PENERIMAAN AND p.KODE_FARM = pd.KODE_FARM
            --JOIN PENERIMAAN_E pe ON pe.KODE_FARM = pd.KODE_FARM AND pe.NO_PENERIMAAN = pd.NO_PENERIMAAN
            --JOIN M_BARANG mb ON mb.KODE_BARANG = pd.KODE_BARANG
            --WHERE p.NO_PENERIMAAN = '$no_penerimaan'
            --AND p.KODE_FARM = '$kode_farm'
            --AND pd.JML_SJ > pd.JML_TERIMA

QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validasi_file($fileFormat,$fileSize){
        $count = 0;
        $arraFileFormat = ['doc','docx'];
        if(!in_array($fileFormat, $arraFileFormat)){
            $count = $count + 1;
        }
        if(($fileSize > 10000000)||($fileSize <= 0)){
            $count = $count + 1;
        }
        return $count;
    }

    function mssql_escape($data) {
        if(is_numeric($data))
          return $data;
        $unpacked = unpack('H*hex', $data);
        return '0x' . $unpacked['hex'];
    }

    public function simpan_konfirmasi_rk($data, $kode_farm, $user, $fileContent, $format, $fileSize, $fileName) {

        $validasi_file = (empty($fileContent)) ? 0 : $this->validasi_file($format,$fileSize);


        $attachment = (empty($fileContent)) ? null : $this->mssql_escape(file_get_contents($fileContent));
            #echo $fileName;
            #echo $attachment;
        #}

        $this->dbSqlServer->conn_id->beginTransaction();

        $count = 0;
        $no_ba = '';

        if ($validasi_file == 0) {

            $count_header_rusak = 0;
            if(isset($data['data_rusak'])){
                foreach ($data['data_rusak'] as $key => $value) {

                    $data_header_rusak = $this->insert_header_konfirmasi_rk($value, $kode_farm, $user, $attachment, $fileName);

                    //echo '['.$data_header_rusak['result'].']';
                    if($data_header_rusak['result']==1 && $data_header_rusak['attachment']==1){
                        $count_header_rusak = $count_header_rusak;
                        $no_ba=$data_header_rusak['no_ba'];
                    }
                    else{
                        $count_header_rusak = $count_header_rusak + 1;
                    }


                    $count_detail_rusak = 0;
                    foreach ($value['detail_rusak'] as $k => $v) {
                        $data_detail_rusak = $this->insert_detail_konfirmasi_rk($data_header_rusak['no_pallet'], $v, $kode_farm, $user);

                        //echo '{'.$data_detail_rusak['no_pallet'].'}';

                        $count_detail_rusak = (empty($data_detail_rusak['no_pallet'])) ? $count_detail_rusak + 1 : $count_detail_rusak;

                    }

                    $count = ($count_detail_rusak>0) ? $count + 1 : $count;


                }
            }
            $count = ($count_header_rusak>0) ? $count + 1 : $count;
            $count_header_kurang = 0;
            if(isset($data['data_kurang'])){
                foreach ($data['data_kurang'] as $key => $value) {
                    $data_header_kurang = $this->insert_header_konfirmasi_rk($value, $kode_farm, $user);

                    if($data_header_kurang['result']==1 && $data_header_kurang['attachment']==1){
                        $count_header_kurang = $count_header_kurang;
                        $no_ba=$data_header_kurang['no_ba'];
                    }
                    else{
                        $count_header_kurang = $count_header_kurang + 1;
                    }

                }
            }
            $count = ($count_header_kurang>0) ? $count + 1 : $count;

        }

        $result = [];
        if($count==0){
            $this->dbSqlServer->conn_id->commit();
            $result = array(
                    'result' => 1,
                    'no_ba' => $no_ba,
                    'format_not_valid' => $validasi_file
                );
        }
        else{
            $this->dbSqlServer->conn_id->rollback();
            $result = array(
                    'result' => 0
                );
        }
        return $result;
    }

    public function insert_header_konfirmasi_rk($data, $kode_farm, $user, $attachment=NULL, $attachment_name=NULL) {
        $kode_barang = $data ['kode_barang'];
        $jumlah = $data ['jumlah'];
        $berat = $data ['berat'];
        $keterangan = $data ['keterangan'];
        $no_sj = $data ['no_sj'];
        $no_penerimaan = $data ['no_penerimaan'];
        $tipe_ba = $data ['tipe_ba'];

        $query = <<<QUERY

            EXEC SIMPAN_HEADER_KONFIRMASI_RK
                '$kode_farm',
                '$kode_barang',
                '$jumlah',
                '$berat',
                '$keterangan',
                '$user',
                '$no_sj',
                '$no_penerimaan',
                '$tipe_ba',
                NULL,
                '$attachment_name'

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        #$stmt->bindParam(':attachment',$attachment, PDO::PARAM_LOB, 0, PDO::SQLSRV_ENCODING_BINARY);
        #$stmt->bindParam(':attachment', $attachment);
        $stmt->execute();
        $r = $stmt->fetch(PDO::FETCH_ASSOC);

        $r['attachment'] = 1;

        if(!empty($attachment)){
            $ru = $this->update_berita_acara_d($data, $kode_farm, $attachment, $r['no_ba']);
            $r['attachment'] = $ru['attachment'];
        }

        return $r;
    }

    public function update_berita_acara_d($data, $kode_farm, $attachment, $no_ba){
        $kode_barang = $data ['kode_barang'];
        $no_sj = $data ['no_sj'];

        $query = <<<QUERY

            UPDATE BERITA_ACARA_D
            SET ATTACHMENT = $attachment
            OUTPUT CASE WHEN INSERTED.ATTACHMENT IS NOT NULL THEN 1 ELSE 0 END attachment
            WHERE NO_BA = '$no_ba'
            AND KODE_FARM = '$kode_farm'
            AND KODE_BARANG = '$kode_barang'
            AND KETERANGAN2 = '$no_sj'

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        #$stmt->bindParam(':attachment',$attachment, PDO::PARAM_LOB, 0, PDO::SQLSRV_ENCODING_BINARY);
        #$stmt->bindParam(':attachment', $attachment);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_detail_konfirmasi_rk($no_pallet, $data, $kode_farm, $user) {
        $kode_barang = $data ['kode_barang'];
        $jumlah = $data ['jumlah'];
        $berat = $data ['berat'];
        $keterangan = $data ['keterangan'];
        $no_sj = $data ['no_sj'];
        $no_penerimaan = $data ['no_penerimaan'];
        $no_kavling = 'DMG';
        $status_stok = 'DM';

        $query = <<<QUERY

            insert into MOVEMENT_D
            OUTPUT INSERTED.NO_PALLET no_pallet
            values (
                '$kode_farm'
                , '$no_kavling'
                , '$no_pallet'
                , '$kode_barang'
                , ''
                , '$no_penerimaan'
                , $jumlah
                , $jumlah
                , 0
                , 0
                , $jumlah
                , $berat
                , 0
                , 0
                , 0
                , 0
                , GETDATE()
                , '$user'
                , NULL
                , NULL
                , '$status_stok'
                , '$keterangan'
                , '$no_sj'
            )

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_detail_konfirmasi_rk_old($no_pallet, $data, $kode_farm, $user) {
        $count = 0;
        $data_movement_d = $this->insert_movement_d($no_pallet, $data, $kode_farm, $user);
        echo '=@'.$data_movement_d['no_pallet'].'@=';
        $count = (empty($data_movement_d['no_pallet'])) ? $count + 1 : $count;
        $data_penerimaan_e = $this->insert_penerimaan_e($no_pallet, $data, $kode_farm, $user);
        echo '=$'.$data_penerimaan_e['no_pallet'].'$=';
        $count = (empty($data_penerimaan_e['no_pallet'])) ? $count + 1 : $count;
        return $count;
    }

    public function insert_penerimaan_e_old($no_pallet, $data, $kode_farm, $user) {
        $no_penerimaan = $data ['no_penerimaan'];
        $kode_barang = $data ['kode_barang'];
        $jumlah = $data ['jumlah'];
        $berat = $data ['berat'];
        $status_stok = 'DM';

        $query = <<<QUERY

            insert into PENERIMAAN_E
            OUTPUT INSERTED.NO_PALLET no_pallet
            values (
                '$no_pallet'
                , '$kode_farm'
                , '$no_penerimaan'
                , '$kode_barang'
                , $jumlah
                , $berat
                , '$status_stok'
            )

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_movement_d_old($no_pallet, $data, $kode_farm, $user) {
        $kode_barang = $data ['kode_barang'];
        $jumlah = $data ['jumlah'];
        $berat = $data ['berat'];
        $keterangan = $data ['keterangan'];
        $no_sj = $data ['no_sj'];
        $no_penerimaan = $data ['no_penerimaan'];
        $no_kavling = 'DMG';
        $status_stok = 'DM';

        $query = <<<QUERY

            insert into MOVEMENT_D
            OUTPUT INSERTED.NO_PALLET no_pallet
            values (
                '$kode_farm'
                , '$no_kavling'
                , '$no_pallet'
                , '$kode_barang'
                , ''
                , '$no_penerimaan'
                , $jumlah
                , $jumlah
                , 0
                , 0
                , $jumlah
                , $berat
                , 0
                , 0
                , 0
                , 0
                , GETDATE()
                , '$user'
                , NULL
                , NULL
                , '$status_stok'
                , '$keterangan'
                , '$no_sj'
            )

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function daftar_do_dan_sj($kode_farm, $do_params) {
        $query = <<<QUERY
            select
                r.*
            from do do
            join (
                SELECT DISTINCT
                    ISNULL(do.NO_DO,'') no_do
                    , ISNULL(do.NO_OP,'') no_op
                    , ISNULL(p.KODE_SURAT_JALAN,'') no_sj
                    , ISNULL(me.NAMA_EKSPEDISI,'') nama_ekspedisi
                    , do.TGL_KIRIM tanggal_kirim
                    , (
                        select top 1 ks.FLOK_BDY
                        from LPB_E le
                        join KANDANG_SIKLUS ks
                            on ks.NO_REG = le.NO_REG
                            and ks.KODE_FARM = le.KODE_FARM
                        where le.NO_LPB = op.NO_LPB
                        and le.KODE_FARM = op.KODE_FARM
                    ) kode_flok
                FROM DO DO
                JOIN OP_VEHICLE opv ON opv.NO_OP = do.NO_OP AND do.KODE_FARM = '$kode_farm'
                JOIN OP op on op.NO_OP = opv.NO_OP and op.KODE_FARM = do.KODE_FARM
                JOIN M_EKSPEDISI me ON me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
                LEFT JOIN PENERIMAAN p ON p.KETERANGAN1 = do.NO_DO AND p.KODE_FARM = do.KODE_FARM
            ) r
                on do.no_op = r.no_op
            where do.no_op in (
                select no_op from do where no_do in ('$do_params')
            )

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function simpan_penerimaan($kode_farm, $data, $user,$keterangan_nopol = '') {
        $result = 0;
        $error = 0;

        $this->dbSqlServer->conn_id->beginTransaction();
        $no_penerimaan = '';
        $data_penerimaan = $data['penerimaan'];
        $penerimaan = $this->insert_penerimaan($kode_farm, $data_penerimaan, $user,$keterangan_nopol);
        $no_penerimaan = $penerimaan['no_penerimaan'];
        if(empty($no_penerimaan)){
            $error++;
        }
        else{
            $data_penerimaan_d = $data['penerimaan_d'];
            foreach ($data_penerimaan_d as $key0 => $value0) {
                $penerimaan_d = $this->insert_penerimaan_d($kode_farm, $no_penerimaan, $value0, $user);
                if(empty($penerimaan_d['no_penerimaan'])){
                    $error++;
                }
            }
            if($error == 0){
                $data_penerimaan_e = $data['penerimaan_e'];
                foreach ($data_penerimaan_e as $key1 => $value1) {
                    $penerimaan_e = $this->insert_penerimaan_e($kode_farm, $no_penerimaan, $value1, $user);
                    $no_pallet = $penerimaan_e['no_pallet'];                    
                    if(empty($no_pallet)){
                        $error++;
                    }
                    else{
                        $movement = $this->insert_movement($kode_farm, $no_pallet, $no_penerimaan, $value1, $user);
                        $status_stok = $movement['status_stok'];
                        #echo '['.$status_stok.']';
                        if(empty($status_stok)){
                            $error++;
                        }
                        else{
                            if($status_stok == 'NM') {
                                $data_movement_d = $data['movement_d'][$no_pallet];
                                foreach ($data_movement_d as $key2 => $value2) {
                                    $movement_d = $this->insert_movement_d($kode_farm, $no_pallet, $no_penerimaan, $value2, $user);
                                    if(empty($movement_d['no_pallet'])){
                                        $error++;
                                    }
                                }
                            }
                            else{
                                $movement_d = $this->insert_movement_d($kode_farm, $no_pallet, $no_penerimaan, $value1, $user);
                                if(empty($movement_d['no_pallet'])){
                                    $error++;
                                }
                            }

                        }
                    }
                }
            }
        }
        #echo '['.$error.']';
        $tmp_no_do = '';
        if($error == 0){
            $update_status_penerimaan = $this->update_status_penerimaan($kode_farm,$no_penerimaan);
            if($update_status_penerimaan['status_terima']<>'C'){
                $error++;
            }

            $data_berita_acara = $data['berita_acara'];
            $no_sj = implode(',',$data_berita_acara['no_sj']);
            $no_do = implode(',',$data_berita_acara['no_do']);

            $tmp_no_do = $no_do;
            if(isset($data['berita_acara_d'])){
                $data_berita_acara_d = $data['berita_acara_d'];
                if(count($data_berita_acara_d) > 0){
                    $berita_acara = $this->insert_berita_acara($kode_farm, $no_penerimaan, $no_sj, $user);
                    $no_ba = $berita_acara['no_ba'];
                    if(empty($no_ba)){
                        $error++;
                    }
                    else{
                        foreach ($data_berita_acara_d as $key3 => $value3) {
                            $berita_acara_d = $this->insert_berita_acara_d($kode_farm, $no_do, $no_ba, $no_sj, $value3, $user);
                            if(empty($berita_acara_d['no_ba'])){
                                $error++;
                            }
                        }
                    }

                }

            }

        }
        /*
        if($error == 0){
            $generate_outstanding = $this->generate_outstanding($kode_farm, $user, $tmp_no_do);
            if(empty($generate_outstanding['result'])){
                $error++;
            }
        }
        */
        #echo $error;

        if($error == 0){
            $result = 1;
            $this->delete_attachment($kode_farm, $no_penerimaan);
            $this->dbSqlServer->conn_id->commit();
        }
        else{
            $this->dbSqlServer->conn_id->rollback();
            $this->delete_attachment_per_do($kode_farm, $tmp_no_do);
        }

        $result = array(
            'result' => $result,
            'no_penerimaan' => $no_penerimaan,
            'no_do' => $tmp_no_do
        );

        return $result;
    }
    public function insert_penerimaan($kode_farm, $data, $user,$keterangan_nopol) {
        $sopir = $data['sopir'];
        $ekspedisi = $data['nama_ekspedisi'];
        $no_sj = implode(',',$data['no_sj']);
        $nopol_kirim = $data['no_kendaraan_kirim'];
        $nopol_terima = $data['no_kendaraan_terima'];
        $no_spm = isset($data['no_spm']) ? implode(',',$data['no_spm']) : '';
        $no_do = implode(',',$data['no_do']);

		$tanggal_sj = implode(',',$data['tanggal_sj']);
        $kuantitas_kg = implode(',',$data['kuantitas_kg']);
        $kuantitas_zak = implode(',',$data['kuantitas_zak']);
        $tgl_verifikasi_do = implode(',',$data['tgl_verifikasi_do']);

        $tanggal_sj = empty($tanggal_sj) ? null : $tanggal_sj;
        $kuantitas_kg = empty($kuantitas_kg) ? null : $kuantitas_kg;
        $kuantitas_zak = empty($kuantitas_zak) ? null : $kuantitas_zak;
        $tgl_verifikasi_do = empty($tgl_verifikasi_do) ? null : $tgl_verifikasi_do;
        $alasan = $keterangan_nopol;
        $tanggal_terima = $data['tanggal_terima'];
        $query = <<<QUERY
            INSERT INTO [dbo].[PENERIMAAN](
                    [NO_PENERIMAAN]
                   ,[KODE_FARM]
                   ,[NAMA_SOPIR]
                   ,[NAMA_EKSPEDISI]
                   ,[KODE_SURAT_JALAN]
                   ,[NO_KENDARAAN_KIRIM]
                   ,[NO_KENDARAAN_TERIMA]
                   ,[NO_SPM]
                   ,[TGL_TERIMA]
                   ,[KETERANGAN1]
                   ,[STATUS_TERIMA]
                   ,[TGL_BUAT]
                   ,[TGL_UBAH]
                   ,[USER_BUAT]
                   ,[USER_UBAH]
                   ,[TGL_SURAT_JALAN]
                   ,[KUANTITAS_KG]
                   ,[KUANTITAS_SAK]
                   ,[TGL_VERIFIKASI_DO]
                   ,[ALASAN]
            )
            OUTPUT inserted.NO_PENERIMAAN no_penerimaan
            VALUES (
                    (
                        SELECT
                            ISNULL(RIGHT('00000000'+ISNULL(CAST(MAX(NO_PENERIMAAN)+1 AS VARCHAR(8)),'1'),8),'00000001')
                        FROM PENERIMAAN
                        WHERE KODE_FARM = '$kode_farm'
                    ),
                    '$kode_farm',
                    :sopir,                    
                    :ekspedisi,
                    '$no_sj',
                    '$nopol_kirim',
                    '$nopol_terima',
                    '$no_spm',
                    GETDATE(),
                    '$no_do',
                    'N',
                    GETDATE(),
                    GETDATE(),
                    '$user',
                    '$user',
                    :tanggal_sj,
                    :kuantitas_kg,
                    :kuantitas_zak,
                    :tgl_verifikasi_do,
                    :alasan
            )
QUERY;
        #echo $query;
        
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->bindParam(':sopir', $sopir);
        $stmt->bindParam(':ekspedisi', $ekspedisi);
        $stmt->bindParam(':tgl_verifikasi_do', $tgl_verifikasi_do);
        $stmt->bindParam(':kuantitas_zak', $kuantitas_zak);
        $stmt->bindParam(':kuantitas_kg', $kuantitas_kg);
        $stmt->bindParam(':tanggal_sj', $tanggal_sj);
        $stmt->bindParam(':alasan', $alasan);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insert_penerimaan_d($kode_farm, $no_penerimaan, $data, $user) {
        $kode_barang = $data['kode_barang'];
        $jml_sj = $data['jml_sj'];
        $jml_terima = $data['jml_terima'];
        $jml_rusak = $data['jml_rusak'];
        $jml_kurang = $data['jml_kurang'];
        $berat_terima = $data['berat_terima'];
        $berat_rusak = $data['berat_rusak'];
        $keterangan_kurang = $data['keterangan_kurang'];
        $query = <<<QUERY
            INSERT INTO [dbo].[PENERIMAAN_D](
                    [KODE_FARM]
                   ,[NO_PENERIMAAN]
                   ,[KODE_BARANG]
                   ,[JML_SJ]
                   ,[JML_TERIMA]
                   ,[JML_RUSAK]
                   ,[JML_KURANG]
                   ,[BERAT_TERIMA]
                   ,[BERAT_RUSAK]
                   ,[KETERANGAN1]
                   ,[JML_PUTAWAY]
                   ,[TGL_BUAT]
                   ,[TGL_UBAH]
                   ,[USER_BUAT]
                   ,[USER_UBAH]
            )
            OUTPUT inserted.NO_PENERIMAAN no_penerimaan
            VALUES (
                    '$kode_farm',
                    '$no_penerimaan',
                    '$kode_barang',
                    $jml_sj,
                    $jml_terima,
                    $jml_rusak,
                    $jml_kurang,
                    $berat_terima,
                    $berat_rusak,
                    '$keterangan_kurang',
                    $jml_terima,
                    GETDATE(),
                    GETDATE(),
                    '$user',
                    '$user'
            )
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insert_penerimaan_e($kode_farm, $no_penerimaan, $data, $user) {
        $no_pallet = $data['no_pallet'];
        $kode_barang = $data['kode_barang'];
        $jumlah = $data['jumlah'];
        $berat = $data['berat'];
        $status_stok = $data['status_stok'];
        $query = <<<QUERY
            INSERT INTO [dbo].[PENERIMAAN_E](
                    [NO_PALLET]
                   ,[KODE_FARM]
                   ,[NO_PENERIMAAN]
                   ,[KODE_BARANG]
                   ,[JUMLAH]
                   ,[BERAT]
                   ,[STATUS_STOK]
            )
            OUTPUT inserted.NO_PENERIMAAN no_penerimaan
                , inserted.NO_PALLET no_pallet
            VALUES (
                    (
                        SELECT
                        CASE
                        WHEN '$no_pallet' = '' then (
                            SELECT
                                'DMG'+
                                ISNULL(
                                RIGHT('00000000'+ISNULL(CAST(SUBSTRING(MAX(NO_PALLET),4,8)+1 AS VARCHAR(8)),'1'),8)
                                , 'DMG00000001') no_pallet
                            from MOVEMENT
                            WHERE KODE_FARM = '$kode_farm'
                                AND left(NO_PALLET,3) = 'DMG'
                        )
                        else '$no_pallet'
                        end
                    ),
                    '$kode_farm',
                    '$no_penerimaan',
                    '$kode_barang',
                    $jumlah,
                    $berat,
                    '$status_stok'
            )
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insert_movement($kode_farm, $no_pallet, $no_referensi, $data, $user) {
        $no_kavling = $data['no_kavling'];
        $kode_barang = $data['kode_barang'];
        $jumlah = $data['jumlah'];
        $berat = $data['berat'];
        $status_stok = $data['status_stok'];
        $kode_flok = $data['kode_flok'];
        $keterangan1 = ($status_stok == 'DM') ? $no_referensi : $kode_flok;
        $keterangan2 = ($status_stok == 'DM') ? 'N/A' : 'BY SYSTEM';
        $berat_pallet = $data['berat_pallet'];
        $query = <<<QUERY
            INSERT INTO [dbo].[MOVEMENT](
                   [KODE_FARM]
                   ,[NO_KAVLING]
                   ,[NO_PALLET]
                   ,[KODE_BARANG]
                   ,[JENIS_KELAMIN]
                   ,[JML_ON_HAND]
                   ,[JML_AVAILABLE]
                   ,[BERAT_AVAILABLE]
                   ,[JML_PUTAWAY]
                   ,[BERAT_PUTAWAY]
                   ,[PUT_DATE]
                   ,[PUT_NAME]
                   ,[STATUS_STOK]
                   ,[KETERANGAN1]
                   ,[KETERANGAN2]
                   ,[BERAT_PALLET]
                   ,[KODE_PALLET]
            )
            OUTPUT inserted.NO_PALLET no_pallet
                , inserted.STATUS_STOK status_stok
             VALUES(
                    '$kode_farm',
                    left('$no_kavling',5),
                    '$no_pallet',
                    '$kode_barang',
                    'C',
                    $jumlah,
                    $jumlah,
                    $berat,
                    $jumlah,
                    $berat,
                    GETDATE(),
                    '$user',
                    '$status_stok',
                    '$keterangan1',
                    '$keterangan2',
                    '$berat_pallet',
                    '$no_kavling'
            )
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insert_movement_d($kode_farm, $no_pallet, $no_referensi, $data, $user) {
        $no_kavling = $data['no_kavling'];
        $kode_barang = $data['kode_barang'];
        $jumlah = $data['jumlah'];
        $berat = $data['berat'];
        $status_stok = $data['status_stok'];
        $keterangan_rusak = $data['keterangan_rusak'];
        $keterangan1 = ($status_stok == 'DM') ? $keterangan_rusak : 'PUT';
        $no_reg = $data['no_reg'];
        $keterangan2 = ($status_stok == 'DM') ? 'N/A' : $no_reg;
        $query = <<<QUERY
            INSERT INTO [dbo].[MOVEMENT_D](
                   [KODE_FARM]
                   ,[NO_KAVLING]
                   ,[NO_PALLET]
                   ,[KODE_BARANG]
                   ,[JENIS_KELAMIN]
                   ,[NO_REFERENSI]
                   ,[JML_ON_HAND]
                   ,[JML_AVAILABLE]
                   ,[BERAT_AVAILABLE]
                   ,[JML_PUTAWAY]
                   ,[BERAT_PUTAWAY]
                   ,[PUT_DATE]
                   ,[PUT_NAME]
                   ,[STATUS_STOK]
                   ,[KETERANGAN1]
                   ,[KETERANGAN2]
                   ,[KODE_PALLET]
            )
            OUTPUT inserted.NO_PALLET no_pallet
             VALUES(
                    '$kode_farm',
                    left('$no_kavling',5),
                    '$no_pallet',
                    '$kode_barang',
                    'C',
                    '$no_referensi',
                    $jumlah,
                    $jumlah,
                    $berat,
                    $jumlah,
                    $berat,
                    GETDATE(),
                    '$user',
                    '$status_stok',
                    '$keterangan1',
                    '$keterangan2',
                    '$no_kavling'
            )
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insert_berita_acara($kode_farm, $no_penerimaan, $no_sj, $user) {
        $keterangan2 = $no_sj;
        $query = <<<QUERY
            INSERT INTO [dbo].[BERITA_ACARA](
                    [KODE_FARM]
                   ,[NO_BA]
                   ,[NO_PENERIMAAN]
                   ,[KETERANGAN2]
                   ,[TGL_BUAT]
                   ,[USER_BUAT]
                   ,[TGL_UBAH]
                   ,[USER_UBAH]
            )
            OUTPUT inserted.NO_BA no_ba
             VALUES(
                    '$kode_farm',
                    (
                        SELECT
                            ISNULL(RIGHT('00000000'+ISNULL(CAST(MAX(NO_BA)+1 AS VARCHAR(8)),'1'),8),'00000001') no_ba
                        FROM BERITA_ACARA
                        WHERE KODE_FARM = '$kode_farm'
                    ),
                    '$no_penerimaan',
                    '$keterangan2',
                    GETDATE(),
                    '$user',
                    GETDATE(),
                    '$user'
             )
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function insert_berita_acara_d($kode_farm, $no_do, $no_ba, $no_sj, $data, $user) {
        $keterangan2 = $no_sj;
        $nama_file = $data['nama_file'];
        $kode_barang = $data['kode_barang'];
        $jml_rusak = $data['jml_rusak'];
        $jml_kurang = $data['jml_kurang'];
        $keterangan_kurang = $data['keterangan_kurang'];
        $keterangan1 = ($jml_kurang > 0) ? $keterangan_kurang : NULL;
        $query = <<<QUERY
            INSERT INTO [dbo].[BERITA_ACARA_D](
                    [KODE_FARM]
                   ,[NO_BA]
                   ,[KODE_BARANG]
                   ,[JML_RUSAK]
                   ,[JML_KURANG]
                   ,[KETERANGAN1]
                   ,[KETERANGAN2]
                   ,[ATTACHMENT]
                   ,[ATTACHMENT_NAME]
            )
            OUTPUT inserted.NO_BA no_ba
             VALUES(
                    '$kode_farm',
                    '$no_ba',
                    '$kode_barang',
                    $jml_rusak,
                    $jml_kurang,
                    :keterangan1,
                    '$keterangan2',
                    (
                        select
                            ISNULL(
                            (select
                                    ATTACHMENT
                                from TMP_ATTACHMENT
                                where KODE_FARM = '$kode_farm'
                                and NO_DO = '$no_do'
                                and KODE_BARANG = '$kode_barang')
                            , NULL)

                    ),
                    '$nama_file'
            )
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->bindParam(':keterangan1', $keterangan1);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function simpan_attachment($kode_farm, $nomor_do, $kode_barang, $fileContent) {
        $attachment = $this->mssql_escape(file_get_contents($fileContent));
        $query = <<<QUERY
            INSERT INTO [dbo].[TMP_ATTACHMENT]
                   ([KODE_FARM]
                   ,[NO_DO]
                   ,[KODE_BARANG]
                   ,[ATTACHMENT])
            OUTPUT inserted.KODE_BARANG kode_pakan
            VALUES (
                    '$kode_farm',
                    '$nomor_do',
                    '$kode_barang',
                    $attachment
            )
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        #$stmt->bindParam(':attachment', $attachment);
        #$stmt->bindParam(':attachment',$attachment, PDO::PARAM_LOB, 0, PDO::SQLSRV_ENCODING_BINARY);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update_status_penerimaan($kode_farm,$no_penerimaan) {
        $query = <<<QUERY
            UPDATE penerimaan
            SET STATUS_TERIMA = 'C'
            OUTPUT inserted.STATUS_TERIMA status_terima
            where NO_PENERIMAAN = '$no_penerimaan'
            and KODE_FARM = '$kode_farm'
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function get_no_penerimaan($kode_farm, $nomor_do) {
        $query = <<<QUERY
            select
                NO_PENERIMAAN no_penerimaan
            from penerimaan
            where keterangan1 like '%$nomor_do%'
            and KODE_FARM = '$kode_farm'
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function ganti_kavling($kode_farm, $kode_flok, $kode_barang) {
        $query = <<<QUERY
            select
                KODE_PALLET no_kavling
                , sum(JML_ON_HAND) timbangan_sak
                --, sum(BERAT_PALLET + BERAT_PUTAWAY) berat
                , (
                    select top 1
                        sum(tm.BERAT_PALLET + tm.BERAT_PUTAWAY)
                    from MOVEMENT tm
                    where tm.KETERANGAN1 = '$kode_flok'
                    and tm.KODE_FARM = '$kode_farm'
                    and tm.kode_barang = '$kode_barang'
                    and tm.PUT_DATE = max(m.PUT_DATE)
                    AND tm.KODE_PALLET = M.KODE_PALLET
                    and tm.no_pallet like 'SYS%'
                    group by tm.KODE_PALLET
                ) berat

            from MOVEMENT m
            where KETERANGAN1 = '$kode_flok'
            and KODE_FARM = '$kode_farm'
            and kode_barang = '$kode_barang'
            and cast(PUT_DATE as date) = cast(GETDATE() as date)
            and no_pallet like 'SYS%'
            and JML_AVAILABLE > 0
            group by KODE_PALLET
            order by KODE_PALLET asc
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function delete_attachment($kode_farm, $no_penerimaan) {
        $query = <<<QUERY

            delete from TMP_ATTACHMENT
            where KODE_FARM = '$kode_farm'
            and NO_DO in (
                select
                    p.KETERANGAN1 no_do
                from PENERIMAAN p
                left join BERITA_ACARA ba
                    on p.NO_PENERIMAAN = ba.NO_PENERIMAAN
                    and p.KODE_FARM = ba.KODE_FARM
                    and p.KODE_SURAT_JALAN = ba.KETERANGAN2
                left join BERITA_ACARA_D bd
                    on bd.KODE_FARM = ba.KODE_FARM
                    and bd.NO_BA = ba.NO_BA
                    and bd.JML_RUSAK > 0
                where p.NO_PENERIMAAN = '$no_penerimaan'
                and p.KODE_FARM = '$kode_farm'
            )
            and KODE_BARANG in (
                select
                    bd.KODE_BARANG kode_barang
                from PENERIMAAN p
                left join BERITA_ACARA ba
                    on p.NO_PENERIMAAN = ba.NO_PENERIMAAN
                    and p.KODE_FARM = ba.KODE_FARM
                    and p.KODE_SURAT_JALAN = ba.KETERANGAN2
                left join BERITA_ACARA_D bd
                    on bd.KODE_FARM = ba.KODE_FARM
                    and bd.NO_BA = ba.NO_BA
                    and bd.JML_RUSAK > 0
                where p.NO_PENERIMAAN = '$no_penerimaan'
                and p.KODE_FARM = '$kode_farm'
            )

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function delete_attachment_per_do($kode_farm, $no_do) {
        $query = <<<QUERY

            delete from TMP_ATTACHMENT
            where KODE_FARM = '$kode_farm'
            and NO_DO = '$no_do'
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function data_pakan_rusak_hilang($kode_farm, $no_penerimaan) {
        $data = $this->all_data_pakan_rusak_hilang($kode_farm, $no_penerimaan);

        $result=[];

        foreach ($data as $key => $value) {
            $result[$value['kode_barang']][] = $value;

        }

        #$result = $data;
        return $result;
    }
    public function all_data_pakan_rusak_hilang($kode_farm, $no_penerimaan) {
        $query = <<<QUERY
                    select
                        pe.KODE_BARANG kode_barang
                        , bd.JML_RUSAK jml_rusak
                        , bd.ATTACHMENT_NAME attachment_name
                        , pe.BERAT berat_rusak
                        , isnull(md.KETERANGAN1,'') keterangan_rusak
                        , bd.JML_KURANG jml_kurang
                        , isnull(bd.KETERANGAN1,'') keterangan_kurang
                    from BERITA_ACARA ba
                    join BERITA_ACARA_D bd
                        on ba.NO_BA = bd.NO_BA
                        and ba.NO_PENERIMAAN = '$no_penerimaan'
                        and ba.KODE_FARM = '$kode_farm'
                        and ba.KODE_FARM = bd.KODE_FARM
                    join PENERIMAAN_D pd
                        on pd.NO_PENERIMAAN = ba.NO_PENERIMAAN
                        and pd.KODE_BARANG = bd.KODE_BARANG
                        and bd.KODE_FARM = ba.KODE_FARM
                    join PENERIMAAN_E pe
                        on pe.NO_PENERIMAAN = pd.NO_PENERIMAAN
                        and pe.KODE_BARANG = pd.KODE_BARANG
                        and pe.KODE_FARM = pd.KODE_FARM
                        and pe.STATUS_STOK = 'DM'
                    join MOVEMENT_D md
                        on md.NO_PALLET = pe.NO_PALLET
                        and md.KODE_FARM = pe.KODE_FARM
                        and md.KODE_BARANG = pe.KODE_BARANG
                        and md.NO_REFERENSI = pe.NO_PENERIMAAN
                        and md.STATUS_STOK = pe.STATUS_STOK
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function generate_outstanding($kode_farm, $user, $tmp_no_do) {
        $query = <<<QUERY
            EXEC KONFIRMASI_PENERIMAAN_PAKAN_BDY '$kode_farm','$user','$tmp_no_do'

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function ganti_hand_pallet($kode_farm) {
        $query = <<<QUERY
            select
                mhp.KODE_HAND_PALLET kode_hand_pallet
                , BRT_BERSIH berat
            from M_HAND_PALLET mhp
            where mhp.KODE_FARM = '$kode_farm'
            and mhp.STATUS_PALLET = 'N'
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function get_gudang_in_farm($kode_farm){
        $sql = <<<QUERY
            select a.kode_gudang, a.nama_gudang
            from m_gudang a
            where a.kode_farm = '{$kode_farm}'
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function stok_gudang($kode_farm, $kode_gudang){
        $sql = <<<QUERY
            select
                sum(m.JML_AVAILABLE) jml_stok
                , mg.DESKRIPSI nama_barang
                , mk.nama_kandang
            from (
                select
                    md_put.NO_REG
                    , md_put.KODE_BARANG
                    , md_put.JML_PUTAWAY-isnull(md_pick.JML_PICK,0) JML_AVAILABLE
                from (
                    select
                        KETERANGAN2 NO_REG
                        , KODE_BARANG
                        , md.KODE_FARM
                        , sum(JML_PUTAWAY) JML_PUTAWAY
                    from MOVEMENT_D md
                    join m_kavling mk
                        on mk.kode_farm = md.kode_farm
                        and md.NO_KAVLING = mk.NO_KAVLING
                    where STATUS_STOK = 'NM'
                    and KETERANGAN1 = 'PUT'
                    and md.KODE_FARM = '$kode_farm'
                    and mk.KODE_GUDANG = '$kode_gudang'
                    and md.no_pallet >= (
                       select min(no_pallet) from MOVEMENT_D md
                       join KANDANG_SIKLUS ks
                        on ks.NO_REG = md.keterangan2 and ks.status_siklus = 'O' and ks.KODE_FARM = md.KODE_FARM
                       where md.keterangan1 = 'PUT' and md.KODE_FARM = '$kode_farm'
                    )
                    group by
                        KETERANGAN2
                        , KODE_BARANG
                        , md.KODE_FARM
                ) md_put
                left join (
                    select
                        KETERANGAN2 NO_REG
                        , KODE_BARANG
                        , md.KODE_FARM
                        , sum(JML_PICK) JML_PICK
                    from MOVEMENT_D md
                    join m_kavling mk
                        on mk.kode_farm = md.kode_farm
                        and md.NO_KAVLING = mk.NO_KAVLING
                    where STATUS_STOK = 'NM'
                    and KETERANGAN1 = 'PICK'
                    and md.KODE_FARM = '$kode_farm'
                    and mk.KODE_GUDANG = '$kode_gudang'
                    and md.no_pallet >= (
                       select min(no_pallet) from MOVEMENT_D md
                       join KANDANG_SIKLUS ks
                        on ks.NO_REG = md.keterangan2 and ks.status_siklus = 'O' and ks.KODE_FARM = md.KODE_FARM
                       where md.keterangan1 = 'PUT' and md.KODE_FARM = '$kode_farm'
                    )
                    group by
                        KETERANGAN2
                        , KODE_BARANG
                        , md.KODE_FARM
                ) md_pick
                    on md_put.NO_REG = md_pick.NO_REG
                    and md_put.KODE_BARANG = md_pick.KODE_BARANG
                    and md_put.KODE_FARM = md_pick.KODE_FARM
            ) m
            join KANDANG_SIKLUS ks
                on ks.NO_REG = m.NO_REG
                and ks.KODE_FARM = '$kode_farm'
                and m.jml_available > 0
            join M_BARANG mb
                on mb.KODE_BARANG = m.KODE_BARANG
            join M_GRUP_BARANG mg
                on mg.GRUP_BARANG = mb.GRUP_BARANG
            join M_KANDANG mk
                on mk.KODE_KANDANG = ks.KODE_KANDANG
                and mk.KODE_FARM = ks.KODE_FARM
            group by
                mg.DESKRIPSI
                , mk.nama_kandang
            order by
                mg.DESKRIPSI
                , mk.nama_kandang
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_tanggal_aktivasi($kode_farm = ''){
        $sql = <<<QUERY
            select top 1 convert(date,stamp) as tgl from KANDANG_SIKLUS ks
            inner join M_PERIODE mp on ks.KODE_SIKLUS = mp.KODE_SIKLUS and mp.STATUS_PERIODE = 'A' and ks.STATUS_SIKLUS = 'O' and mp.KODE_FARM = '$kode_farm'
            left join cycle_state_transition cy on ks.NO_REG = cy.noreg and state = 'RL'
            order by stamp asc
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_all_pallet($kode_farm = ''){
        $sql = <<<QUERY
        select * from m_pallet pl
        inner join M_KAVLING kv on pl.NO_KAVLING = kv.NO_KAVLING and kv.STATUS_KAVLING = 'A'
        where pl.STATUS_PALLET = 'N' and pl.KODE_FARM = '$kode_farm'

QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_pallet_sudah_timbang($kode_farm = '',$tgl = ''){
        $sql = <<<QUERY
        select * from m_pallet pl
        inner join M_KAVLING kv on pl.NO_KAVLING = kv.NO_KAVLING and kv.STATUS_KAVLING = 'A'
        where pl.STATUS_PALLET = 'N' and pl.KODE_FARM = '$kode_farm' and pl.TGL_TIMBANG >= '$tgl'
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function get_hand_pallet($kode_farm = '',$tgl = ''){
        $sql = <<<QUERY
            select * from M_HAND_PALLET
            where STATUS_PALLET = 'N' and KODE_FARM = '$kode_farm' and TGL_TIMBANG < '$tgl' OR TGL_TIMBANG is null
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
