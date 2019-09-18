<?php

class M_transaksi extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function data_jenis_pakan($no_reg = NULL) {
        if(empty($no_reg)){
        $query = <<<QUERY
            select
                *
            from M_BARANG
            order by NAMA_BARANG ASC
QUERY;

        }
        else{
        $query = <<<QUERY
    --        select
    --            mb.KODE_BARANG kode_barang
    --            , mb.NAMA_BARANG nama_barang
    --        from KANDANG_MOVEMENT km
    --        join M_BARANG mb
    --            on mb.KODE_BARANG = km.KODE_BARANG
    --        where km.NO_REG = '$no_reg'
    select * from (
      select md.KODE_BARANG kode_barang
      	,mb.nama_barang
      	,sum(md.jml_putaway) - sum(jml_pick) stok
      from MOVEMENT_D md
      inner join m_barang mb
      	on mb.kode_barang = md.kode_barang
      where md.KETERANGAN2 = '$no_reg' and status_stok = 'NM'
      group by md.KETERANGAN2
      		,md.kode_barang
      		,mb.nama_barang
      )y where y.stok > 0
QUERY;
        }

        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function data_server($kode_farm, $h_tanggal_kebutuhan) {
        $query = <<<QUERY
            select
                cast(getdate() as date) tanggal_server
                , cast(getdate() + $h_tanggal_kebutuhan as date) tanggal_server_besok_lusa
                , (select NAMA_FARM from M_FARM where KODE_FARM = '$kode_farm') farm
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_daftar_kandang($no_mutasi, $str_kandang, $kode_farm, $tanggal_pemberian, $nama_farm, $jenis_pakan, $kuantitas_pemberian_pakan, $tanggal_kebutuhan, $kandang_asal) {
        $query = <<<QUERY
            select
                r.*
            from (
                select
                    r.*
                    , case
                        when r.dh < r.dh_std then 1
                        else 0
                    end dh_red
                    , case
                        when r.fcr < r.fcr_std then 1
                        else 0
                    end fcr_red
                    , case
                        when r.ip < r.ip_std then 1
                        else 0
                    end ip_red
                    , 0 exist
                from (
                    select
                        mk.KODE_KANDANG kode_kandang
                        , mk.NAMA_KANDANG nama_kandang
                        , ks.NO_REG no_reg
                        , 0 jml_terima
                        --, ks.TGL_DOC_IN tanggal_doc_in
                        , datediff(day,ks.TGL_DOC_IN,'$tanggal_pemberian') umur
                        , rhk.C_DAYA_HIDUP dh
                        , msbd.DH_KUM_PRC dh_std
                        --, msb.TARGET_DH_PRC dh_std
                        --, rhk_pakan.BRT_PAKAI berat_pakai
                        --, rhk.C_JUMLAH jumlah
                        --, rhk.C_BERAT_BADAN berat_badan
                        , cast(((((rhk_pakan.BRT_PAKAI/rhk.C_JUMLAH)*1000)/(rhk.C_BERAT_BADAN*1000))*1000) /1000 as decimal(20,3)) fcr
                        , msb.TARGET_FCR_PRC fcr_std
                        , round(cast( ((rhk.C_DAYA_HIDUP/100) * rhk.C_BERAT_BADAN * 10000) /
                        ((((((rhk_pakan.BRT_PAKAI/rhk.C_JUMLAH)*1000)/(rhk.C_BERAT_BADAN*1000))*1000) /1000)*datediff(day,ks.TGL_DOC_IN,'$tanggal_pemberian')) as decimal(20,3)),0) ip
                        , msb.TARGET_IP ip_std
                        , isnull(md.JML_STOK_GUDANG,0) stok_gudang
                        , isnull(md.BERAT_STOK_GUDANG,0) berat_stok_gudang
                        , isnull(km.JML_STOK,0) stok_kandang
                        , isnull(km.BERAT_STOK,0) berat_stok_kandang
                    from M_KANDANG mk
                    join KANDANG_SIKLUS ks
                        on ks.KODE_KANDANG = mk.KODE_KANDANG
                        and mk.KODE_FARM = ks.KODE_FARM
                        and mk.STATUS_KANDANG = 'A'
                        and ks.STATUS_SIKLUS = 'O'
                        and ks.KODE_KANDANG <> '$kandang_asal'
                        and ks.KODE_KANDANG NOT IN ('$str_kandang')
                    join (
                        select
                            rhk.*
                        from (
                            select
                                NO_REG
                                , max(TGL_TRANSAKSI) TGL_TRANSAKSI
                            from RHK
                            where C_BERAT_BADAN > 0
                            group by NO_REG
                        ) sum_rhk
                        JOIN RHK rhk
                            on sum_rhk.NO_REG = rhk.NO_REG
                            and sum_rhk.TGL_TRANSAKSI = rhk.TGL_TRANSAKSI
                    ) rhk
                        on rhk.NO_REG = ks.NO_REG
                    join RHK_PAKAN rhk_pakan
                        on rhk_pakan.NO_REG = rhk.NO_REG and rhk_pakan.KODE_BARANG = '$jenis_pakan'
                    --    on rhk_pakan.NO_REG = rhk.NO_REG
                        and rhk_pakan.TGL_TRANSAKSI = rhk.TGL_TRANSAKSI
                    join M_STD_BUDIDAYA msb
                        on msb.KODE_FARM = ks.KODE_FARM
                        and msb.KODE_STD_BUDIDAYA = ks.KODE_STD_BUDIDAYA
                    join M_STD_BUDIDAYA_D msbd
                        on msbd.KODE_STD_BUDIDAYA = msb.KODE_STD_BUDIDAYA
                        and msbd.STD_UMUR = datediff(day,ks.TGL_DOC_IN,'$tanggal_pemberian')
                    left join (
                        select
                            md.KODE_FARM
                            , md.KETERANGAN2 NO_REG
                            , SUM(md.JML_PUTAWAY) JML_PUTAWAY
                            , SUM(md.BERAT_PUTAWAY) BERAT_PUTAWAY
                            , SUM(md.JML_PICK) JML_PICK
                            , SUM(md.BERAT_PICK) BERAT_PICK
                            , SUM(md.JML_PUTAWAY) - SUM(md.JML_PICK) JML_STOK_GUDANG
                            , SUM(md.BERAT_PUTAWAY) - SUM(md.BERAT_PICK) BERAT_STOK_GUDANG
                        from MOVEMENT_D md
                        JOIN M_BARANG mb
                            ON mb.KODE_BARANG = md.KODE_BARANG
                            and mb.KODE_BARANG = '$jenis_pakan'
              --          where md.KODE_FARM = '$kode_farm'
                        where md.KODE_FARM = '$kode_farm' and status_stok = 'NM'
                        group by
                            md.KODE_FARM
                            , md.KETERANGAN2
                    ) md
                        on md.KODE_FARM = ks.KODE_FARM
                        and md.NO_REG = ks.NO_REG
                    left join (
                        SELECT
                            km.NO_REG
                            , sum(km.JML_STOK) JML_STOK
                            , sum(km.BERAT_STOK) BERAT_STOK
                        from KANDANG_MOVEMENT km
                        JOIN M_BARANG mb
                            ON mb.KODE_BARANG = km.KODE_BARANG
                            and mb.KODE_BARANG = '$jenis_pakan'
                        GROUP BY km.NO_REG
                    ) km
                        on km.NO_REG = ks.NO_REG
                    where mk.KODE_FARM = '$kode_farm'
                    --and isnull(km.JML_STOK,0) > 0
                ) r
                UNION ALL
                select
                    r.*
                    , case
                        when r.dh < r.dh_std then 1
                        else 0
                    end dh_red
                    , case
                        when r.fcr < r.fcr_std then 1
                        else 0
                    end fcr_red
                    , case
                        when r.ip < r.ip_std then 1
                        else 0
                    end ip_red
                    , 1 exist
                from (
                    SELECT
                        ks.KODE_KANDANG kode_kandang
                        , mk.NAMA_KANDANG nama_kandang
                        , mpd.NO_REG_TUJUAN no_reg
                        , mpd.JML_TERIMA jml_terima
                        , mpd.UMUR umur
                        , mpd.DAYA_HIDUP dh
                        , msbd.DH_KUM_PRC dh_std
                        --, msb.TARGET_DH_PRC dh_std
                        , mpd.FCR fcr
                        , msb.TARGET_FCR_PRC fcr_std
                        , mpd.INDEX_PERFORMANCE ip
                        , msb.TARGET_IP ip_std
                        , mpd.JML_AKHIR_GUDANG stok_gudang
                        , mpd.BERAT_AKHIR_GUDANG berat_stok_gudang
                        , mpd.JML_AKHIR_KANDANG stok_kandang
                        , mpd.BERAT_AKHIR_KANDANG berat_stok_kandang
                    FROM MUTASI_PAKAN_D mpd
                    JOIN mutasi_pakan mp
                        ON mp.NO_MUTASI = mpd.NO_MUTASI
                    JOIN KANDANG_SIKLUS ks
                        ON ks.KODE_FARM = mp.KODE_FARM
                        and ks.NO_REG = mpd.NO_REG_TUJUAN
                    join M_STD_BUDIDAYA msb
                        on msb.KODE_FARM = ks.KODE_FARM
                        and msb.KODE_STD_BUDIDAYA = ks.KODE_STD_BUDIDAYA
                    join M_STD_BUDIDAYA_D msbd
                        on msbd.KODE_STD_BUDIDAYA = msb.KODE_STD_BUDIDAYA
                        and msbd.STD_UMUR = mpd.UMUR
                    JOIN M_KANDANG mk
                        ON mk.KODE_KANDANG = ks.KODE_KANDANG
                        and mk.KODE_FARM = mp.KODE_FARM
                    WHERE mpd.NO_MUTASI = '$no_mutasi'
                ) r
            ) r
            JOIN RHK rhk
                on rhk.NO_REG = r.no_reg
                and rhk.TGL_TRANSAKSI = (select cast(cast('$tanggal_pemberian' as datetime) - 1 as date))
            order by r.exist DESC, r.nama_kandang ASC
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_konsumsi_per_ekor($kode_farm, $tanggal_pemberian, $nama_farm, $jenis_pakan, $kuantitas_pemberian_pakan, $tanggal_kebutuhan, $kandang_asal) {
        $query = <<<QUERY
            select
                (((isnull(sum_md.JML_STOK_GUDANG,0)*(sum(md.BERAT_PUTAWAY) / sum(md.JML_PUTAWAY
                ))) - ($kuantitas_pemberian_pakan * (sum(md.BERAT_PUTAWAY) / sum(md.JML_PUTAWAY
                )))) / rhk.C_JUMLAH) * 1000 konsumsi_per_ekor
                , msbd.PKN_HR_STD standar_konsumsi_budidaya
                --, ks.KODE_STD_BUDIDAYA kode_standar_budidaya
            from MOVEMENT_D md
            left join (
                select
                    md.KODE_FARM
                    , md.KETERANGAN2 NO_REG
                    , SUM(md.JML_PUTAWAY) JML_PUTAWAY
                    , SUM(md.JML_PICK) JML_PICK
                    , SUM(md.JML_PUTAWAY) - SUM(md.JML_PICK) JML_STOK_GUDANG
                from MOVEMENT_D md
                JOIN M_BARANG mb
                    ON mb.KODE_BARANG = md.KODE_BARANG
                    and mb.KODE_BARANG = '$jenis_pakan'
                where md.KODE_FARM = '$kode_farm'
                group by
                    md.KODE_FARM
                    , md.KETERANGAN2
            ) sum_md
                on sum_md.KODE_FARM = md.KODE_FARM
                and sum_md.NO_REG = md.KETERANGAN2
            join (
                select
                    rhk.*
                from (
                    select
                        NO_REG
                        , max(TGL_TRANSAKSI) TGL_TRANSAKSI
                    from RHK
                    where TGL_TRANSAKSI < '$tanggal_pemberian'
                    group by NO_REG
                ) sum_rhk
                JOIN RHK rhk
                    on sum_rhk.NO_REG = rhk.NO_REG
                    and sum_rhk.TGL_TRANSAKSI = rhk.TGL_TRANSAKSI
            ) rhk
                on rhk.NO_REG = md.KETERANGAN2
            join KANDANG_SIKLUS ks
                on ks.KODE_FARM = md.KODE_FARM
                and ks.NO_REG = md.KETERANGAN2
            join M_STD_BUDIDAYA_D msbd
                on msbd.KODE_STD_BUDIDAYA = ks.KODE_STD_BUDIDAYA
                --and msbd.STD_UMUR = DATEDIFF(day, ks.TGL_DOC_IN, '$tanggal_kebutuhan') + 1
                and msbd.STD_UMUR = DATEDIFF(day, ks.TGL_DOC_IN, '$tanggal_kebutuhan')
            JOIN M_BARANG mb
                ON mb.KODE_BARANG = msbd.KODE_BARANG
                and mb.KODE_BARANG = '$jenis_pakan'
            where ks.KODE_KANDANG = '$kandang_asal'
            and md.KODE_BARANG = '$jenis_pakan'
            and md.KETERANGAN1 = 'PUT'
            and md.KODE_FARM = '$kode_farm'
            group by
                sum_md.JML_STOK_GUDANG
                , rhk.C_JUMLAH
                , msbd.PKN_HR_STD
                , ks.KODE_STD_BUDIDAYA
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function simpan_mutasi($kode_farm, $user, $data) {
        $result = 0;
        $count = 0;

        $this->db->conn_id->beginTransaction();

        $mutasi_pakan = $this->insert_mutasi_pakan($kode_farm, $user, $data);

        $no_mutasi = $mutasi_pakan ['no_mutasi'];
        $alasan = $data ['alasan'];
        if (!empty($no_mutasi)) {
            foreach ($data['data_detail'] as $key => $value) {
                #print_r($value);
                $mutasi_pakan_d = $this->insert_mutasi_pakan_d($kode_farm, $user, $no_mutasi, $value);
                if (!empty($mutasi_pakan_d ['jml_terima'])) {
                    $count++;
                }
            }
        }

        $review_mutasi_pakan = $this->insert_review_mutasi_pakan($user, $no_mutasi, $alasan);
        if (empty($review_mutasi_pakan['keputusan'])) {
            $count = 0;
        }

        #echo count($data).'=='.$count;

        if (count($data['data_detail']) == $count) {
            $result = 1;
            $this->db->conn_id->commit();
        } else {
            $this->db->conn_id->rollback();
        }

        $output = array(
            'result' => $result,
            'no_mutasi' => $no_mutasi
        );

        return $output;
    }

    public function insert_mutasi_pakan($kode_farm, $user, $data) {
        $aksi = $data['aksi'];
        $no_mutasi = ($aksi == 'new') ? '' : $data['no_mutasi'];
        $tanggal_pemberian = $data['tanggal_pemberian'];
        $tanggal_kebutuhan = $data['tanggal_kebutuhan'];
        $no_reg_asal = $data['no_reg_asal'];
        $jumlah_mutasi = $data['kuantitas_pemberian_pakan'];
        $tanggal_pemberian = $data['tanggal_pemberian'];
        $jenis_pakan = $data['jenis_pakan'];
        $query = <<<QUERY
            INSERT INTO [dbo].[MUTASI_PAKAN]
                   ([NO_MUTASI]
                   ,[KODE_FARM]
                   ,[TGL_PEMBERIAN]
                   ,[TGL_KEBUTUHAN]
                   ,[NO_REG_ASAL]
                   ,[KODE_BARANG]
                   ,[JML_MUTASI]
                   ,[BERAT_MUTASI]
                   ,[TGL_BUAT]
                   ,[USER_BUAT]
                   ,[REF_ID])
            OUTPUT inserted.NO_MUTASI no_mutasi
            VALUES (
                (
                    SELECT
                        ISNULL(RIGHT('000000'+ISNULL(CAST(MAX(left(NO_MUTASI,6))+1 AS VARCHAR(6)),'1'),6),'000001')
                        +'/MT/'
                        +'{$kode_farm}/'
                        +(select dbo.MONTH_CONVERTION(month(getdate())))+'/'
                        +str(YEAR(getdate()),4)
                    FROM MUTASI_PAKAN WHERE KODE_FARM = '{$kode_farm}'
                ),
                '$kode_farm',
                '$tanggal_pemberian',
                '$tanggal_kebutuhan',
                '$no_reg_asal',
                '$jenis_pakan',
                '$jumlah_mutasi',
                NULL,
                GETDATE(),
                '$user',
                (
                    select
                        case
                            when '$no_mutasi' = '' then NULL
                            else '$no_mutasi'
                        end
                )
            )
QUERY;
      #  echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_mutasi_pakan_d($kode_farm, $user, $no_mutasi, $data) {

        $no_reg_tujuan = $data['no_reg_tujuan'];
        $umur = $data['umur'];
        $dh = $data['dh'];
        $fcr = $data['fcr'];
        $ip = $data['ip'];
        $stok_gudang = $data['stok_gudang'];
        $berat_stok_gudang = $data['berat_stok_gudang'];
        $stok_kandang = $data['stok_kandang'];
        $berat_stok_kandang = $data['berat_stok_kandang'];
        $jml_terima = $data['jml_terima'];
        $no_reg_tujuan = $data['no_reg_tujuan'];
        $query = <<<QUERY
            INSERT INTO [dbo].[MUTASI_PAKAN_D]
                   ([NO_MUTASI]
                   ,[NO_REG_TUJUAN]
                   ,[UMUR]
                   ,[DAYA_HIDUP]
                   ,[FCR]
                   ,[INDEX_PERFORMANCE]
                   ,[JML_AKHIR_GUDANG]
                   ,[BERAT_AKHIR_GUDANG]
                   ,[JML_AKHIR_KANDANG]
                   ,[BERAT_AKHIR_KANDANG]
                   ,[JML_TERIMA]
                   ,[BERAT_TERIMA])
            OUTPUT inserted.JML_TERIMA jml_terima
            VALUES (
                '$no_mutasi',
                '$no_reg_tujuan',
                '$umur',
                $dh,
                $fcr,
                $ip,
                $stok_gudang,
                $berat_stok_gudang,
                $stok_kandang,
                $berat_stok_kandang,
                $jml_terima,
                NULL --(($berat_stok_gudang/$stok_gudang)*$jml_terima)
            )
QUERY;
    #    echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_review_mutasi_pakan($user, $no_mutasi, $alasan) {
        $query = <<<QUERY
            INSERT INTO [dbo].[REVIEW_MUTASI_PAKAN]
                   ([NO_MUTASI]
                   ,[TGL_BUAT]
                   ,[USER_BUAT]
                   ,[KEPUTUSAN]
                   ,[ALASAN])
            OUTPUT inserted.KEPUTUSAN keputusan
            VALUES (
                '$no_mutasi',
                GETDATE(),
                '$user',
                'N',
                '$alasan'
            )
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function mutasi_pakan($no_mutasi) {
        $query = <<<QUERY
            SELECT
                mp.*
                , mf.NAMA_FARM
                , ks.KODE_KANDANG kode_kandang
                , (
                    select
                        isnull(ALASAN,'') alasan
                    from REVIEW_MUTASI_PAKAN
                    where USER_BUAT = (
                        select
                            KODE_PEGAWAI
                        from M_PEGAWAI
                        where GRUP_PEGAWAI = 'KFM'
                    )
                    and NO_MUTASI = '$no_mutasi'
                ) alasan
            FROM MUTASI_PAKAN mp
            JOIN M_FARM mf
                ON mf.KODE_FARM = mp.KODE_FARM
            JOIN KANDANG_SIKLUS ks
                ON ks.KODE_FARM = mp.KODE_FARM
                and ks.NO_REG = mp.NO_REG_ASAL
            WHERE mp.NO_MUTASI = '$no_mutasi'
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function mutasi_pakan_d($no_mutasi) {
        $query = <<<QUERY
            SELECT
                mpd.*
                , ks.KODE_KANDANG kode_kandang
            FROM MUTASI_PAKAN_D mpd
            JOIN mutasi_pakan mp
                ON mp.NO_MUTASI = mpd.NO_MUTASI
            JOIN KANDANG_SIKLUS ks
                ON ks.KODE_FARM = mp.KODE_FARM
                and ks.NO_REG = mpd.NO_REG_TUJUAN
            WHERE mpd.NO_MUTASI = '$no_mutasi'
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cek_lhk($no_reg, $tanggal_transaksi) {
        $query = <<<QUERY
            select
                --count(*) lhk_found
                *
                , isnull(C_BERAT_BADAN,0) berat_badan
                , (select cast(cast('$tanggal_transaksi' as datetime) - 1 as date)) tgl_transaksi
                --1 lhk_found
            from RHK where NO_REG = '$no_reg' AND TGL_TRANSAKSI = (select cast(cast('$tanggal_transaksi' as datetime) - 1 as date))
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
