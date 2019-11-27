<?php

class M_main extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_data_mutasi_pakan($level_user, $kode_farm, $belum_tindak_lanjut, $no_mutasi, $tanggal, $tanggal_awal, $tanggal_akhir, $kandang) {
        $filter_str = "";
        $filter_arr = array();

        $bottom_filter_str = "";
        $bottom_filter_arr = array();

        if ($belum_tindak_lanjut == 1){
            switch($level_user){
                case 'kf' :
                $filter_arr [] = "rmp.KEPUTUSAN in ('N','V','RU','RJ')";
                break;
                case 'kd' :
                $filter_arr [] = "rmp.KEPUTUSAN in ('N')";
                break;
                case 'kdv' :
                $filter_arr [] = "rmp.KEPUTUSAN in ('RV')";
                break;
            }
        }
        else{
            switch($level_user){
                case 'kdv' :
                $bottom_filter_arr [] = "r.tindak_lanjut_kepala_departemen IS NOT NULL";
                break;
            }
        }



        if (!empty($kode_farm))
            $filter_arr [] = "mp.KODE_FARM = '$kode_farm'";
        if (!empty($no_mutasi))
            $filter_arr [] = "mp.NO_MUTASI like '%$no_mutasi%'";
        if (!empty($tanggal))
            $filter_arr [] = "mp.$tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
        if (!empty($kandang))
            $filter_arr [] = "ks.KODE_KANDANG = '$kandang'";

        if (count($filter_arr) > 0) {
            $filter_str .= " where ";
            $filter_str .= implode(" and ", $filter_arr);
        }

        if (count($bottom_filter_arr) > 0) {
            $bottom_filter_str .= " where ";
            $bottom_filter_str .= implode(" and ", $bottom_filter_arr);
        }

        $query = <<<QUERY
            select
                r.*
            from(
                select
                    mp.KODE_FARM kode_farm
                    , mp.NO_MUTASI no_mutasi
                    , mp.TGL_PEMBERIAN tanggal_pemberian
                    , mp.TGL_KEBUTUHAN tanggal_kebutuhan
                    , mp.KODE_BARANG id_jenis_pakan
                    , mb.NAMA_BARANG jenis_pakan
                    , mp.JML_MUTASI jumlah_mutasi
                    , ks.KODE_KANDANG kandang
                    , ks.NO_REG no_reg_asal
                    --, DATEDIFF(day,ks.TGL_DOC_IN,mp.TGL_KEBUTUHAN) + 1 umur
                    , DATEDIFF(day,ks.TGL_DOC_IN,mp.TGL_KEBUTUHAN) umur
                    , case
                        WHEN rmp.KEPUTUSAN = 'N' THEN 'New / Rilis'
                        WHEN rmp.KEPUTUSAN = 'V' THEN 'Void'
                        WHEN rmp.KEPUTUSAN = 'RV' THEN 'Reviewed'
                        WHEN rmp.KEPUTUSAN = 'RJ' THEN 'Rejected'
                        WHEN rmp.KEPUTUSAN = 'A' THEN 'Approved'
                        WHEN rmp.KEPUTUSAN = 'RU' THEN 'Review Ulang'
                        WHEN rmp.KEPUTUSAN = 'ACK' THEN 'Ack.'
                    end status_permintaan_mutasi
                    , CASE
                        WHEN rmp.KEPUTUSAN in ('V','RU') then (
                            isnull(
                            (select
                                tmp_mp.NO_MUTASI
                            from MUTASI_PAKAN tmp_mp
                            where tmp_mp.REF_ID = mp.NO_MUTASI)
                            , '1'
                            )
                        )
                        WHEN rmp.KEPUTUSAN in ('RJ') then '2'
                        ELSE '0'
                    END tindak_lanjut_kepala_farm
                    , (
                        select
                            ALASAN
                        from REVIEW_MUTASI_PAKAN
                        where USER_BUAT = (
                            select
                                KODE_PEGAWAI
                            from M_PEGAWAI
                            where GRUP_PEGAWAI = 'KFM'
                        )
                        and NO_MUTASI = mp.NO_MUTASI
                    ) alasan_tindak_lanjut_kepala_farm
                    ,(
                        select
                            ALASAN
                        from REVIEW_MUTASI_PAKAN
                        where USER_BUAT = (
                            select
                                KODE_PEGAWAI
                            from M_PEGAWAI
                            where GRUP_PEGAWAI = 'KDP'
                        )
                        and NO_MUTASI = mp.NO_MUTASI
                    ) tindak_lanjut_kepala_departemen
                    , (
                        select
                            ALASAN
                        from REVIEW_MUTASI_PAKAN
                        where USER_BUAT = (
                            select
                                KODE_PEGAWAI
                            from M_PEGAWAI
                            where GRUP_PEGAWAI = 'KDV'
                        )
                        and NO_MUTASI = mp.NO_MUTASI
                    ) tindak_lanjut_kepala_divisi
                    , (
                        select
                            TGL_BUAT
                        from REVIEW_MUTASI_PAKAN
                        where USER_BUAT = (
                            select
                                KODE_PEGAWAI
                            from M_PEGAWAI
                            where GRUP_PEGAWAI = 'KDP'
                        )
                        and NO_MUTASI = mp.NO_MUTASI
                    ) waktu_tindak_lanjut_kepala_departemen
                    , (
                        select
                            TGL_BUAT
                        from REVIEW_MUTASI_PAKAN
                        where USER_BUAT = (
                            select
                                KODE_PEGAWAI
                            from M_PEGAWAI
                            where GRUP_PEGAWAI = 'KDV'
                        )
                        and NO_MUTASI = mp.NO_MUTASI
                    ) waktu_tindak_lanjut_kepala_divisi
                from (
                    select
                        mp.NO_MUTASI
                        , max(rmp.TGL_BUAT) TGL_BUAT
                    from MUTASI_PAKAN mp
                    join REVIEW_MUTASI_PAKAN rmp
                        on mp.NO_MUTASI = rmp.NO_MUTASI
                    group by mp.NO_MUTASI
                ) sum_mp
                join MUTASI_PAKAN mp
                    on mp.NO_MUTASI = sum_mp.NO_MUTASI
                join M_BARANG mb
                    on mb.KODE_BARANG = mp.KODE_BARANG
                join REVIEW_MUTASI_PAKAN rmp
                    on rmp.NO_MUTASI = mp.NO_MUTASI
                    and rmp.TGL_BUAT = sum_mp.TGL_BUAT
                join KANDANG_SIKLUS ks
                    on ks.NO_REG = mp.NO_REG_ASAL
                    and ks.KODE_FARM = mp.KODE_FARM
                $filter_str
            ) r
            $bottom_filter_str

QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detail_mutasi_pakan($kode_farm, $no_mutasi) {
        $query = <<<QUERY
            select distinct
                r.*
                , case
                    when r.dh_asal < r.dh_asal_std then 1
                    else 0
                end dh_asal_red
                , case
                    when r.fcr_asal < r.fcr_asal_std then 1
                    else 0
                end fcr_asal_red
                , case
                    when r.ip_asal < r.ip_asal_std then 1
                    else 0
                end ip_asal_red
                , case
                    when r.dh_tujuan < r.dh_tujuan_std then 1
                    else 0
                end dh_tujuan_red
                , case
                    when r.fcr_tujuan < r.fcr_tujuan_std then 1
                    else 0
                end fcr_tujuan_red
                , case
                    when r.ip_tujuan < r.ip_tujuan_std then 1
                    else 0
                end ip_tujuan_red
            from (
                select
                    mp.KODE_BARANG jenis_pakan
                    , ks_asal.KODE_KANDANG kandang_asal
                    , DATEDIFF(day,ks_asal.TGL_DOC_IN,mp.TGL_PEMBERIAN) umur_asal
                    , rhk.C_DAYA_HIDUP dh_asal
                    , msbd_asal.DH_KUM_PRC dh_asal_std
                    --, msb_asal.TARGET_DH_PRC dh_asal_std
                    , cast(((((rhk_pakan.BRT_PAKAI/rhk.C_JUMLAH)*1000)/(rhk.C_BERAT_BADAN*1000))*1000) /1000 as decimal(20,3)) fcr_asal
                    , msb_asal.TARGET_FCR_PRC fcr_asal_std
                    , round(((rhk.C_DAYA_HIDUP/100) * rhk.C_BERAT_BADAN * 10000) / 
                    ((((((rhk_pakan.BRT_PAKAI/rhk.C_JUMLAH)*1000)/(rhk.C_BERAT_BADAN*1000))*1000) /1000)*
                    datediff(day,ks_asal.TGL_DOC_IN,mp.TGL_PEMBERIAN)),0) ip_asal
                    , round(msb_asal.TARGET_IP,0) ip_asal_std
                    , mpd.JML_TERIMA kuantitas_mutasi
                    , ks_tujuan.KODE_KANDANG kandang_tujuan
                    , mpd.UMUR umur_tujuan
                    , mpd.DAYA_HIDUP dh_tujuan
                    , msbd_tujuan.DH_KUM_PRC dh_tujuan_std
                    --, msb_tujuan.TARGET_DH_PRC dh_tujuan_std
                    , mpd.FCR fcr_tujuan
                    , msb_tujuan.TARGET_FCR_PRC fcr_tujuan_std
                    , round(mpd.INDEX_PERFORMANCE,0) ip_tujuan
                    , round(msb_tujuan.TARGET_IP,0) ip_tujuan_std
                    , mpd.JML_AKHIR_GUDANG stok_gudang
                    , mpd.JML_AKHIR_KANDANG stok_kandang
                from MUTASI_PAKAN mp
                join MUTASI_PAKAN_D mpd
                    on mpd.NO_MUTASI = mp.NO_MUTASI
                --join REVIEW_MUTASI_PAKAN rmp
                --    on rmp.NO_MUTASI = mp.NO_MUTASI
                join KANDANG_SIKLUS ks_asal
                    on ks_asal.NO_REG = mp.NO_REG_ASAL
                    and ks_asal.KODE_FARM = mp.KODE_FARM
                join KANDANG_SIKLUS ks_tujuan
                    on ks_tujuan.NO_REG = mpd.NO_REG_TUJUAN
                    and ks_tujuan.KODE_FARM = mp.KODE_FARM
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
                    on rhk.NO_REG = mp.NO_REG_ASAL
                join RHK_PAKAN rhk_pakan
                    on rhk_pakan.NO_REG = rhk.NO_REG
                    and rhk_pakan.TGL_TRANSAKSI = rhk.TGL_TRANSAKSI
                join M_STD_BUDIDAYA msb_asal
                    on msb_asal.KODE_FARM = ks_asal.KODE_FARM
                    and msb_asal.KODE_STD_BUDIDAYA = ks_asal.KODE_STD_BUDIDAYA
                join M_STD_BUDIDAYA_D msbd_asal
                    on msbd_asal.KODE_STD_BUDIDAYA = msb_asal.KODE_STD_BUDIDAYA
                    and msbd_asal.STD_UMUR = DATEDIFF(day,ks_asal.TGL_DOC_IN,mp.TGL_PEMBERIAN)
                join M_STD_BUDIDAYA msb_tujuan
                    on msb_tujuan.KODE_FARM = ks_tujuan.KODE_FARM
                    and msb_tujuan.KODE_STD_BUDIDAYA = ks_tujuan.KODE_STD_BUDIDAYA
                join M_STD_BUDIDAYA_D msbd_tujuan
                    on msbd_tujuan.KODE_STD_BUDIDAYA = msb_tujuan.KODE_STD_BUDIDAYA
                    and msbd_tujuan.STD_UMUR = mpd.UMUR
                where mp.KODE_FARM = '$kode_farm'
                and mp.NO_MUTASI = '$no_mutasi'
            ) r

QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function data_kandang($kode_farm=NULL) {
        $query = <<<QUERY
            select
                mk.KODE_KANDANG kode_kandang
                , mk.NAMA_KANDANG nama_kandang
                , ks.NO_REG no_reg
            from M_KANDANG mk
            join KANDANG_SIKLUS ks
                on ks.KODE_KANDANG = mk.KODE_KANDANG
                and mk.KODE_FARM = ks.KODE_FARM
                and mk.STATUS_KANDANG = 'A'
                and ks.STATUS_SIKLUS = 'O'
            where mk.KODE_FARM = '$kode_farm'
            order by mk.NAMA_KANDANG ASC
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function data_farm() {
        $query = <<<QUERY
            select 
                * 
            from M_FARM
            where GRUP_FARM = 'BDY'
            order by NAMA_FARM asc
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tindak_lanjut($user, $no_mutasi, $keputusan, $alasan) {
        $cek = $this->cek_review_mutasi_pakan($user, $no_mutasi);
        $result = ($cek['found'] == 0) ? $this->insert_review_mutasi_pakan($user, $no_mutasi, $keputusan, $alasan) : $this->update_review_mutasi_pakan($user, $no_mutasi, $keputusan, $alasan);
        return $result;

    }

    public function insert_review_mutasi_pakan($user, $no_mutasi, $keputusan, $alasan) {
        $query = <<<QUERY

            INSERT INTO [dbo].[REVIEW_MUTASI_PAKAN]
                   ([NO_MUTASI]
                   ,[TGL_BUAT]
                   ,[USER_BUAT]
                   ,[KEPUTUSAN]
                   ,[ALASAN])
            OUTPUT inserted.KEPUTUSAN keputusan
                , inserted.NO_MUTASI no_mutasi
            VALUES (
                '$no_mutasi',
                GETDATE(),
                '$user',
                '$keputusan',
                '$alasan'
            )
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_review_mutasi_pakan($user, $no_mutasi, $keputusan, $alasan) {
        $query = <<<QUERY

            UPDATE REVIEW_MUTASI_PAKAN
            SET KEPUTUSAN = '$keputusan'
                , ALASAN = '$alasan'
            OUTPUT inserted.KEPUTUSAN keputusan
                , inserted.NO_MUTASI no_mutasi
            where NO_MUTASI = '$no_mutasi'
            and USER_BUAT = '$user'
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function ack($user, $no_mutasi, $keputusan) {
        $query = <<<QUERY

            UPDATE REVIEW_MUTASI_PAKAN
            SET KEPUTUSAN = '$keputusan'
            OUTPUT inserted.KEPUTUSAN keputusan
                , inserted.NO_MUTASI no_mutasi
            where NO_MUTASI = '$no_mutasi'
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cek_review_mutasi_pakan($user, $no_mutasi) {
        $query = <<<QUERY
            select 
                count(*) found 
            from REVIEW_MUTASI_PAKAN
            where NO_MUTASI = '$no_mutasi'
            and USER_BUAT = '$user'
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function data_notif_mutasi_pakan($level_user) {
        $level_user = strtolower($level_user);
        switch ($level_user) {
            case 'kf':
                $query = <<<QUERY
                    select
                        *
                    from (
                        select
                            rmp.NO_MUTASI no_mutasi
                            , rmp.KEPUTUSAN keputusan
                            , mp.REF_ID ref_id
                        from (
                            select
                                mp.NO_MUTASI
                                , max(rmp.TGL_BUAT) TGL_BUAT
                            from MUTASI_PAKAN mp
                            join REVIEW_MUTASI_PAKAN rmp
                                on mp.NO_MUTASI = rmp.NO_MUTASI
                            group by mp.NO_MUTASI
                        ) sum_mp
                        join REVIEW_MUTASI_PAKAN rmp
                            on rmp.NO_MUTASI = sum_mp.NO_MUTASI
                            and rmp.TGL_BUAT = sum_mp.TGL_BUAT
                        left join MUTASI_PAKAN mp
                            on rmp.NO_MUTASI = mp.REF_ID
                        where rmp.KEPUTUSAN in ('RJ','RU')
                    ) r
                    where r.ref_id IS NULL
QUERY;
                break;
            case 'kd':
                $query = <<<QUERY
                    select
                        rmp.NO_MUTASI no_mutasi
                        , rmp.KEPUTUSAN keputusan
                        , rmp.ALASAN alasan
                        , mpg.GRUP_PEGAWAI grup_pegawai
                        , lower(mf.NAMA_FARM) nama_farm
                        , lower(mk.NAMA_KANDANG) nama_kandang
                    from (
                        select
                            mp.NO_MUTASI
                            , max(rmp.TGL_BUAT) TGL_BUAT
                        from MUTASI_PAKAN mp
                        join REVIEW_MUTASI_PAKAN rmp
                            on mp.NO_MUTASI = rmp.NO_MUTASI
                        group by mp.NO_MUTASI
                    ) sum_mp
                    join REVIEW_MUTASI_PAKAN rmp
                        on rmp.NO_MUTASI = sum_mp.NO_MUTASI
                        and rmp.TGL_BUAT = sum_mp.TGL_BUAT
                    join MUTASI_PAKAN mp
                        on mp.NO_MUTASI = rmp.NO_MUTASI
                    join M_PEGAWAI mpg
                        on mpg.KODE_PEGAWAI = rmp.USER_BUAT
                    join M_FARM mf
                        on mf.KODE_FARM = mp.KODE_FARM
                    join KANDANG_SIKLUS ks
                        on ks.NO_REG = mp.NO_REG_ASAL
                        and ks.KODE_FARM = mp.KODE_FARM
                    join M_KANDANG mk
                        on mk.KODE_KANDANG = ks.KODE_KANDANG
                        and ks.KODE_FARM = mk.KODE_FARM
                    where rmp.KEPUTUSAN in ('RJ','N')
                    and mpg.GRUP_PEGAWAI <> '$level_user'
QUERY;
                break;
            case 'kdv':
                $query = <<<QUERY
                    select
                        rmp.NO_MUTASI no_mutasi
                        , rmp.KEPUTUSAN keputusan
                        , mpg.GRUP_PEGAWAI grup_pegawai
                        , lower(mf.NAMA_FARM) nama_farm
                        , lower(mk.NAMA_KANDANG) nama_kandang
                    from (
                        select
                            mp.NO_MUTASI
                            , max(rmp.TGL_BUAT) TGL_BUAT
                        from MUTASI_PAKAN mp
                        join REVIEW_MUTASI_PAKAN rmp
                            on mp.NO_MUTASI = rmp.NO_MUTASI
                        group by mp.NO_MUTASI
                    ) sum_mp
                    join REVIEW_MUTASI_PAKAN rmp
                        on rmp.NO_MUTASI = sum_mp.NO_MUTASI
                        and rmp.TGL_BUAT = sum_mp.TGL_BUAT
                    join MUTASI_PAKAN mp
                        on mp.NO_MUTASI = rmp.NO_MUTASI
                    join M_PEGAWAI mpg
                        on mpg.KODE_PEGAWAI = rmp.USER_BUAT
                    join M_FARM mf
                        on mf.KODE_FARM = mp.KODE_FARM
                    join KANDANG_SIKLUS ks
                        on ks.NO_REG = mp.NO_REG_ASAL
                        and ks.KODE_FARM = mf.KODE_FARM
                    join M_KANDANG mk
                        on mk.KODE_KANDANG = ks.KODE_KANDANG
                        and ks.KODE_FARM = mk.KODE_FARM
                    where rmp.KEPUTUSAN in ('RV')
QUERY;
                break;
        }
        
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function notif_mutasi_pakan($level_user){
        $data_notif_mutasi_pakan = $this->data_notif_mutasi_pakan($level_user);
        $result = array(
            'title' => 'Mutasi Pakan',
            'message' => array()
        );

        foreach ($data_notif_mutasi_pakan as $key => $value) {
            switch ($value['keputusan']) {
                case 'N':
                    $message = 'Terdapat permintaan mutasi pakan pada '.ucfirst($value['nama_kandang']).' - '.ucfirst($value['nama_farm']).'.';
                    break;
                case 'RV':
                    $message = 'Terdapat permintaan mutasi pakan pada '.ucfirst($value['nama_kandang']).' - '.ucfirst($value['nama_farm']).'.';
                    break;
                case 'RJ':
                    $message = ($level_user=='kd') ? 'Permintaan mutasi pakan dengan no. mutasi : '.$value['no_mutasi'].' direject. "'.$value['alasan'].'"' : 'Permintaan mutasi pakan dengan no. mutasi : '.$value['no_mutasi'].' direject.';
                    break;
                case 'RU':
                    $message = 'Permohonan review ulang permintaan mutasi pakan dengan no. mutasi : '.$value['no_mutasi'].'.';
                    break;
                default:
                    $message = 'Keputusan tidak valid.';
                    break;
            }
            array_push($result['message'], $message);
        }

        return $result;
    }


}
