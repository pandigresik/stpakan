<?php

class M_transaksi extends MY_Model
{
    private $dbSqlServer;

    public function __construct()
    {
        parent::__construct();
        $this->dbSqlServer = $this->load->database('default', true);
    }

    public function get_data_order_kandang_bdy($tgl_kirim_awal, $tgl_kirim_akhir, $kode_farm)
    {
        //$umur_sebelum_ada_lhk = 2;
        $sql = <<<SQL
        select * from (
            SELECT ks.flok_bdy
                ,ks.kode_kandang
                ,CASE WHEN datediff(day,ks.TGL_DOC_IN,le.TGL_KEBUTUHAN) = 1 THEN dateadd(day,-2,le.tgl_kebutuhan) ELSE dateadd(day,-1,le.tgl_kebutuhan) END tgl_kirim
                ,le.tgl_kebutuhan
                ,sum(le.jml_order) jml_permintaan 
                ,(
					SELECT sum(kmd.jml_order) FROM PENERIMAAN_KANDANG pk
					JOIN KANDANG_MOVEMENT_D kmd ON kmd.NO_REG = pk.NO_REG AND pk.NO_PENERIMAAN_KANDANG = kmd.KETERANGAN2 AND kmd.KETERANGAN1 = 'PENERIMAAN KANDANG'
					WHERE pk.NO_REG = ks.NO_REG AND pk.NO_ORDER = ok.no_order
				) jml_dropping 
                ,ok.no_order               
                ,ok.status_order
                ,case when ok.no_order != null then 0 else 1 end generate        
              --  ,iif(ok.no_order is null,1,0) generate
            FROM LPB l 
            JOIN lpb_e le ON le.NO_LPB = l.NO_LPB AND le.KODE_FARM = l.KODE_FARM and (le.tgl_kebutuhan between dateadd(day,1,'{$tgl_kirim_awal}') and dateadd(day,2,'{$tgl_kirim_akhir}'))
            JOIN KANDANG_SIKLUS ks ON ks.NO_REG = le.NO_REG AND datediff(day,ks.TGL_DOC_IN,le.TGL_KEBUTUHAN) <= (SELECT VALUE FROM SYS_CONFIG_GENERAL WHERE KODE_CONFIG = 'umur_sebelum_ada_lhk' AND KODE_FARM = '{$kode_farm}')
            LEFT JOIN ORDER_KANDANG_E oke ON oke.NO_REG = le.NO_REG AND oke.TGL_KEBUTUHAN = le.TGL_KEBUTUHAN
            LEFT JOIN ORDER_KANDANG ok ON ok.KODE_FARM = le.KODE_FARM AND ok.NO_ORDER = oke.NO_ORDER AND ok.NO_REFERENSI IS NULL
            WHERE l.STATUS_LPB = 'A'
            AND l.KODE_FARM = '{$kode_farm}'
            GROUP BY ks.flok_bdy,ks.no_reg,ks.kode_kandang,ks.TGL_DOC_IN,le.tgl_kebutuhan,ok.NO_ORDER,ok.status_order
            union all 
            SELECT ks.flok_bdy
                ,ks.kode_kandang
                ,dateadd(day,-1,rp.tgl_kebutuhan) tgl_kirim
                ,rp.tgl_kebutuhan tgl_kebutuhan
                ,sum(coalesce(rp.jml_permintaan,0)) jml_permintaan 
                ,(
					SELECT sum(kmd.jml_order) FROM PENERIMAAN_KANDANG pk
					JOIN KANDANG_MOVEMENT_D kmd ON kmd.NO_REG = pk.NO_REG AND pk.NO_PENERIMAAN_KANDANG = kmd.KETERANGAN2 AND kmd.KETERANGAN1 = 'PENERIMAAN KANDANG'
					WHERE pk.NO_REG = ks.NO_REG AND pk.NO_ORDER = ok.no_order and kmd.kode_barang = oke.kode_barang
				) jml_dropping 
                ,ok.no_order
                ,ok.status_order
                ,0 generate
            FROM rhk_rekomendasi_pakan rp                     
            JOIN KANDANG_SIKLUS ks ON ks.NO_REG = rp.NO_REG AND ks.KODE_FARM = '{$kode_farm}' AND ks.STATUS_SIKLUS = 'O'
            LEFT JOIN ORDER_KANDANG_E oke ON oke.NO_REG = rp.NO_REG AND oke.TGL_KEBUTUHAN = rp.tgl_kebutuhan and oke.kode_barang = rp.kode_barang
            LEFT JOIN ORDER_KANDANG ok ON ok.KODE_FARM = ks.KODE_FARM AND ok.NO_ORDER = oke.NO_ORDER AND ok.NO_REFERENSI IS NULL
            WHERE dateadd(day,1,rp.tgl_transaksi) BETWEEN '{$tgl_kirim_awal}' AND '{$tgl_kirim_akhir}'
            GROUP BY ks.flok_bdy,ks.no_reg,ks.kode_kandang,ks.TGL_DOC_IN,rp.tgl_kebutuhan,ok.NO_ORDER,ok.status_order,oke.kode_barang
        )z where z.tgl_kirim between  '{$tgl_kirim_awal}' and '{$tgl_kirim_akhir}'   
        order by z.tgl_kirim,z.flok_bdy                  
SQL;
        //log_message('error', $sql);

        return $this->db->query($sql)->result_array();
    }

    public function get_data_detail_order_kandang_bdy($no_order, $kode_farm)
    {
        $query = <<<QUERY
            select distinct
                r.*                        
                , r.berat_per_pallet        
                , CAST(mv.berat_putaway / mv.jml_putaway  AS DECIMAL(5,3)) berat_rata2    
                , (
                    select
                        *
                    from (
                    select
                        sum(tmp_movement.JML_ON_HAND) JML_ON_HAND
                    from MOVEMENT tmp_movement
                    where tmp_movement.NO_PALLET like 'SYS%'
                    and tmp_movement.no_kavling = r.no_kavling
                    and tmp_movement.KODE_PALLET = r.kode_pallet
                    and tmp_movement.KODE_FARM = '$kode_farm'
                    and tmp_movement.KODE_BARANG = r.kode_barang
                 
                    and tmp_movement.KETERANGAN1 = r.kode_flok
                    and tmp_movement.no_pallet >= (
                       select min(no_pallet) from MOVEMENT_D md
                       join KANDANG_SIKLUS ks
                        on ks.NO_REG = md.keterangan2 and ks.status_siklus = 'O' and ks.KODE_FARM = md.KODE_FARM
                       where md.keterangan1 = 'PUT' and md.KODE_FARM = '$kode_farm'
                    )
                    group by
                        tmp_movement.NO_KAVLING
                        , tmp_movement.KODE_PALLET
                        , tmp_movement.KODE_BARANG
                        , tmp_movement.KETERANGAN1
                    ) x
                    where x.JML_ON_HAND > 0
                ) stok_kavling
                ,mp.BRT_BERSIH	berat_pallet_murni
				,(select mhp.BRT_BERSIH from M_HAND_PALLET mhp where mhp._DEFAULT = 1 and mhp.KODE_FARM = '$kode_farm' and mhp.STATUS_PALLET = 'N') berat_hand_pallet
            from (
                SELECT
                  MD.NO_KAVLING no_kavling
                  , MD.KODE_PALLET kode_pallet
                  , OKE.KODE_BARANG kode_barang
                  , MB.NAMA_BARANG nama_barang
                  , M.NO_PALLET no_pallet
              
                  , pd.BERAT_TIMBANG - pd.BERAT_ORDER  berat_pallet
                  , OKE.NO_REG no_reg
                  , MK.KODE_KANDANG kode_kandang
                  , MK.NAMA_KANDANG nama_kandang
               
                  , CASE
                        WHEN (md.JML_ON_PICK+md.JML_PICK) >= OKE.JML_PP THEN OKE.JML_PP
                        ELSE (md.JML_ON_PICK+md.JML_PICK)
                  END jml_kebutuhan
                  , (md.JML_ON_PICK+md.JML_PICK) jml_order_per_kandang
                  , M.KETERANGAN1 kode_flok
                  , MP.KODE_PEGAWAI id_diterima_oleh
                  , MP.NAMA_PEGAWAI diterima_oleh
                  , PD.BERAT_ORDER berat_bersih_per_kandang
              
                  , OKE.JML_ORDER jml_kebutuhan_per_kandang
                  , PD.JML_ORDER jml_aktual_per_kandang
                  , PD.JML_KONVERSI_TIMBANG jml_konversi_timbang
                  , PD.JML_ORDER_AKTUAL jml_aktual
                  , case
                    when pd.NO_ORDER is not null then 1
                    else 0
                  end selesai
                  , MP2.KODE_PEGAWAI id_diserahkan_oleh
                  , MP2.NAMA_PEGAWAI diserahkan_oleh
               
                  , (
                    select top 1
                        JML_AVAILABLE
                    from MOVEMENT_D md
                    where md.NO_PALLET = m.NO_PALLET
                    and md.KODE_FARM = m.KODE_FARM
                    and md.KETERANGAN1 = 'PICK'
                    and md.KETERANGAN2 = OKE.NO_REG
                    and md.NO_REFERENSI = oke.NO_ORDER
                  ) + (
                    select
                        sum(md.JML_ON_PICK)
                    from MOVEMENT_D md
                    where md.NO_KAVLING = m.NO_KAVLING
                    and md.KODE_FARM = m.KODE_FARM
                    and md.KETERANGAN1 = 'PICK'
                    and md.NO_REFERENSI = oke.NO_ORDER
                  ) sisa
                  , pd.BERAT_TIMBANG berat_per_pallet
                  , OK.TGL_KEB_AWAL
                  , cast(m.PUT_DATE as date) PUT_DATE
                  , OKE.JML_RETUR_SAK_KOSONG jml_retur_sak_kosong
                FROM ORDER_KANDANG OK
                JOIN ORDER_KANDANG_D OKD
                  ON OKD.NO_ORDER = OK.NO_ORDER
                  AND OKD.KODE_FARM = OK.KODE_FARM
                  AND OK.NO_ORDER = '$no_order'
                  AND OK.KODE_FARM = '$kode_farm'
                JOIN ORDER_KANDANG_E OKE
                  ON OKE.NO_ORDER = OKD.NO_ORDER
                  AND OKD.NO_REG = OKE.NO_REG
                JOIN KANDANG_SIKLUS KS
                  ON KS.NO_REG = OKE.NO_REG
                  AND KS.KODE_FARM = OK.KODE_FARM
                JOIN M_KANDANG MK
                  ON MK.KODE_KANDANG = KS.KODE_KANDANG
                  AND MK.KODE_FARM = KS.KODE_FARM
                JOIN M_BARANG MB
                  ON MB.KODE_BARANG = OKE.KODE_BARANG
                JOIN MOVEMENT_D MD
                  ON MD.KODE_FARM = OKD.KODE_FARM
                  and MD.JENIS_KELAMIN = OKE.JENIS_KELAMIN
                  and MD.KODE_BARANG = OKE.KODE_BARANG
                  AND MD.NO_REFERENSI = OKE.NO_ORDER
                  AND MD.KETERANGAN2 = OKE.NO_REG
                  AND MD.KETERANGAN1 = 'PICK'
                JOIN MOVEMENT M
                  ON M.NO_PALLET = MD.NO_PALLET
                  AND M.NO_KAVLING = MD.NO_KAVLING
                  AND M.KODE_BARANG = MD.KODE_BARANG
                  AND M.JENIS_KELAMIN = MD.JENIS_KELAMIN
                  AND M.KODE_FARM = MD.KODE_FARM
                LEFT JOIN PENERIMAAN_KANDANG PK
                  ON PK.NO_ORDER = OK.NO_ORDER
                  AND PK.NO_REG = MD.KETERANGAN2
                LEFT JOIN PENERIMAAN_KANDANG_D PKD
                  ON PKD.NO_PENERIMAAN_KANDANG = PK.NO_PENERIMAAN_KANDANG
                  AND PKD.NO_REG = PK.NO_REG
                  AND PKD.KODE_BARANG = MD.KODE_BARANG
                LEFT JOIN KANDANG_MOVEMENT_D KMD
                    ON KMD.NO_REG = PKD.NO_REG
                    AND KMD.KODE_BARANG = PKD.KODE_BARANG
                    AND KMD.JENIS_KELAMIN = PKD.JENIS_KELAMIN                   
                    AND KMD.KETERANGAN2 = PKD.NO_PENERIMAAN_KANDANG
                    AND KMD.KETERANGAN1 = 'PENERIMAAN KANDANG'
                LEFT JOIN M_PEGAWAI MP
                
                    ON MP.KODE_PEGAWAI = PKD.USER_GUDANG
                left join PICK_D PD
                    ON PD.NO_PALLET = MD.NO_PALLET
                    AND PD.NO_REG = MD.KETERANGAN2
                    AND PD.NO_ORDER = MD.NO_REFERENSI
                    AND PD.KODE_BARANG = MD.KODE_BARANG
                    AND PD.KODE_FARM = MD.KODE_FARM
                    AND MD.NO_KAVLING = PD.NO_KAVLING
                LEFT JOIN M_PEGAWAI MP2
                    ON MP2.KODE_PEGAWAI = PD.USER_BUAT
                WHERE
                (
                    CASE
                        WHEN (md.JML_ON_PICK+md.JML_PICK) >= OKE.JML_ORDER THEN OKE.JML_ORDER
                        ELSE (md.JML_ON_PICK+md.JML_PICK)
                    END
                ) > 0
            ) r
            INNER JOIN movement mv ON mv.no_pallet = r.no_pallet AND mv.KODE_FARM = '$kode_farm'
            left join m_pallet mp
            on mp.KODE_PALLET = r.kode_pallet
               and mp.STATUS_PALLET = 'N'
               and mp.KODE_FARM = '$kode_farm'
            order by
                r.PUT_DATE asc
                , r.nama_kandang ASC
                , r.NO_PALLET ASC


QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generate_picking_list($kode_farm, $kode_flok, $tanggal_kebutuhan, $user)
    {
        $no_penerimaan = '';
        $no_referensi = '';
        /* cek dulu apakah sudah pernah digenerate atau belum */
        $tmpHasil = array('result' => 0);
        if (empty($no_referensi)) {
            $whereReferensi = ' where ok.no_referensi is null ';
            $sqlCekGenerate = <<<SQL

        SELECT count(*) result
        FROM ORDER_KANDANG ok 
        JOIN ORDER_KANDANG_D okd 
        ON ok.KODE_FARM = okd.KODE_FARM AND ok.NO_ORDER = okd.NO_ORDER
        JOIN ORDER_KANDANG_E oke 
        ON okd.no_reg = oke.no_reg AND okd.NO_ORDER = oke.NO_ORDER 
        AND oke.TGL_KEBUTUHAN = '{$tanggal_kebutuhan}' AND oke.NO_REG IN (
            SELECT no_reg FROM KANDANG_SIKLUS WHERE KODE_FARM = '{$kode_farm}' AND STATUS_SIKLUS = 'O' AND FLOK_BDY = '{$kode_flok}'
        )
        {$whereReferensi}
SQL;
        } else {
            $whereReferensi = ' and md.no_referensi = (SELECT no_order FROM ORDER_KANDANG WHERE kode_farm = \''.$kode_farm.'\' and no_referensi = \''.$no_referensi.'\')';
            $sqlCekGenerate = <<<SQL

    SELECT count(*) result
    FROM MOVEMENT_D md    
    where md.KODE_FARM = '{$kode_farm}' and md.no_pallet like 'SYS%'
    {$whereReferensi}
SQL;
        }

        $stmtCek = $this->dbSqlServer->conn_id->prepare($sqlCekGenerate);
        $stmtCek->execute();
        $tmpHasil = $stmtCek->fetch(PDO::FETCH_ASSOC);

        $query = <<<QUERY

            EXEC GENERATE_PICKING_LIST
               '$kode_farm',
               '$kode_flok',
               '$no_penerimaan',
               '$no_referensi',
               '$tanggal_kebutuhan',
               '$user'


QUERY;

        if (!$tmpHasil['result']) {
            $stmt = $this->dbSqlServer->conn_id->prepare($query);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return array('result' => 8, 'no_pengambilan' => null);
        }
    }

    public function simpan_generate_permintaan($kode_farm, $tanggal_kirim, $tanggal_kebutuhan_awal, $tanggal_kebutuhan_akhir, $user)
    {
        $query = <<<QUERY

            EXEC SIMPAN_GENERATE_PERMINTAAN_TERBARU
               '$kode_farm',
               '$tanggal_kirim',
               '$tanggal_kebutuhan_awal',
               '$tanggal_kebutuhan_akhir',
               '$user'


QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_data_detail_order_kandang($no_order, $kode_farm, $berat_standart)
    {
        $query = <<<QUERY
            SELECT DISTINCT
                OK.NO_ORDER no_order
                , OKE.TGL_KEBUTUHAN tgl_kebutuhan
                , KS.NO_REG no_reg
                , KS.FLOK_BDY flock
                , OK.KODE_FARM kode_farm
                , MF.NAMA_FARM farm
                , REPLACE(CONVERT(VARCHAR(10),OK.TGL_KIRIM,105),'-',' ') tgl_kirim
                , REPLACE(CONVERT(VARCHAR(10),OK.TGL_KEB_AWAL,105),'-',' ') tgl_keb_awal
                , REPLACE(CONVERT(VARCHAR(10),OK.TGL_KEB_AKHIR,105),'-',' ') tgl_keb_akhir
                , KS.KODE_KANDANG kode_kandang
                , MD.JENIS_KELAMIN kode_jenis_kelamin
                , CASE
                    WHEN MD.JENIS_KELAMIN = 'J' then 'JANTAN'
                    WHEN MD.JENIS_KELAMIN = 'B' then 'BETINA'
                    ELSE '-'
                    END jenis_kelamin
                , MD.NO_KAVLING id_kavling
                , MD.KODE_PALLET kode_pallet
                , MD.NO_PALLET no_pallet
                , MD.KODE_BARANG kode_barang
                , MB.NAMA_BARANG nama_barang
                , ISNULL(OKE.JML_STOK_GUDANG,0) jml_stok_gudang
                --, TMP.SUM_JML_ORDER kebutuhan_pakan
                , OKE.JML_PP kebutuhan_pakan
                , OKE.JML_STOK_AKHIR sisa_pakan
                , ISNULL(OKE.JML_ORDER_OUTSTANDING,0) jml_order_outstanding
                , MD.JML_ON_PICK jumlah
                , MD.JML_PICK tmp_jumlah
                , m.BERAT_PALLET berat_pallet
                , PD.JML_KONVERSI_TIMBANG jumlah_konversi_timbang
                , PD.JML_ORDER_AKTUAL jumlah_aktual_sak
                , CASE
                    WHEN MD.BERAT_PICK = 0 then NULL
                    ELSE MD.BERAT_PICK
                    END berat
                , CASE
                    WHEN MB.BENTUK_BARANG = 'T' then 'TEPUNG'
                    WHEN MB.BENTUK_BARANG = 'C' then 'CRUMBLE'
                    WHEN MB.BENTUK_BARANG = 'P' then 'PALLET'
                    WHEN MB.BENTUK_BARANG = 'A' then 'CAIR'
                    ELSE ''
                    END bentuk_pakan
                , CASE
                    WHEN MD.BERAT_PICK > 0 AND PKD.USER_GUDANG IS NULL then 1
                    WHEN MD.BERAT_PICK > 0 AND PKD.USER_GUDANG IS NOT NULL then 2
                    ELSE 0
                    END keterangan
                , (select VALUE from SYS_CONFIG where DESCRIPTION = 'TOLERANSI') toleransi
                , (mtb.BRT_STD*mtb.JML_SAK) - mtb.BATAS_BAWAH data_min
                , (mtb.BRT_STD*mtb.JML_SAK) + mtb.BATAS_ATAS data_max
                , CASE
                    WHEN PKD.NO_PENERIMAAN_KANDANG IS NOT NULL THEN 1
                    ELSE 0
                END penerimaan_kandang
                , MP1.KODE_PEGAWAI kode_user_gudang
                , MP1.NAMA_PEGAWAI user_gudang
                , MP2.NAMA_PEGAWAI user_buat
                , PKD.NO_PENERIMAAN_KANDANG no_penerimaan_kandang
                , REPLACE(CONVERT(VARCHAR(10),KMD.TGL_BUAT,105),'-',' ') tgl_buat
                , LEFT(CAST(KMD.TGL_BUAT AS TIME),5) wkt_buat
            FROM ORDER_KANDANG OK
            JOIN ORDER_KANDANG_E OKE ON OK.NO_ORDER = OKE.NO_ORDER
            LEFT JOIN LPB_E LE
                ON LE.NO_REG = OKE.NO_REG
                AND LE.KODE_BARANG = OKE.KODE_BARANG
                AND LE.TGL_KEBUTUHAN = OKE.TGL_KEBUTUHAN
                AND LE.JENIS_KELAMIN = OKE.JENIS_KELAMIN
            JOIN MOVEMENT_D MD
              ON MD.NO_REFERENSI = OK.NO_ORDER
                AND MD.KODE_FARM = OK.KODE_FARM
                AND OKE.JENIS_KELAMIN = MD.JENIS_KELAMIN
                AND OKE.NO_REG = MD.KETERANGAN2
                AND OKE.KODE_BARANG = MD.KODE_BARANG
            JOIN MOVEMENT m
                on m.NO_PALLET = md.NO_PALLET
                and m.NO_KAVLING = md.NO_KAVLING
                and m.KODE_BARANG = md.KODE_BARANG
                and m.KODE_FARM = md.KODE_FARM
                and m.JENIS_KELAMIN = md.JENIS_KELAMIN

            LEFT JOIN PICK_D PD
                ON PD.KODE_FARM = MD.KODE_FARM
                AND PD.NO_PALLET = MD.NO_PALLET
                AND PD.KODE_BARANG = MD.KODE_BARANG
                AND PD.NO_KAVLING = MD.NO_KAVLING
                AND OK.NO_ORDER = pd.NO_ORDER
            JOIN KANDANG_SIKLUS KS ON KS.NO_REG = MD.KETERANGAN2 AND KS.KODE_FARM = OK.KODE_FARM
            JOIN M_BARANG MB ON MB.KODE_BARANG = MD.KODE_BARANG
            JOIN M_FARM MF ON MF.KODE_FARM = OK.KODE_FARM
            --join PENERIMAAN_E pe on pe.KODE_FARM = MD.KODE_FARM and pe.NO_PALLET = MD.NO_PALLET
            LEFT JOIN M_TOLERANSI_BERAT mtb on mtb.BRT_STD = :berat_standart AND mtb.JML_SAK = MD.JML_ON_PICK
            LEFT JOIN PENERIMAAN_KANDANG PK ON PK.NO_ORDER = OK.NO_ORDER AND PK.NO_REG = KS.NO_REG
            LEFT JOIN PENERIMAAN_KANDANG_D PKD ON PKD.NO_REG = PKD.NO_REG
                AND PKD.NO_PENERIMAAN_KANDANG = PK.NO_PENERIMAAN_KANDANG
                AND PKD.KODE_BARANG = MD.KODE_BARANG
                AND PKD.JML_TERIMA = MD.JML_PICK
                AND PKD.JENIS_KELAMIN = MD.JENIS_KELAMIN
                AND PKD.NO_REG = PD.NO_REG
            LEFT JOIN M_PEGAWAI MP1 ON MP1.KODE_PEGAWAI = PKD.USER_GUDANG
            LEFT JOIN KANDANG_MOVEMENT_D KMD
                ON KMD.NO_REG = PKD.NO_REG
                AND KMD.KODE_BARANG = PKD.KODE_BARANG
                AND KMD.JENIS_KELAMIN = PKD.JENIS_KELAMIN
                --AND KMD.JML_ORDER = PKD.JML_TERIMA
                AND KMD.KETERANGAN2 = PKD.NO_PENERIMAAN_KANDANG
                AND KMD.KETERANGAN1 = 'PENERIMAAN KANDANG'
            LEFT JOIN M_PEGAWAI MP2 ON MP2.KODE_PEGAWAI = KMD.USER_BUAT
            WHERE OK.NO_ORDER = :no_order
            --and pe.STATUS_STOK = 'NM'
            and MD.STATUS_STOK = 'NM'
            AND OK.KODE_FARM = :kode_farm
            AND MD.KETERANGAN1 = 'PICK'
            AND (MD.JML_ON_PICK+MD.JML_PICK) > 0
            order by OKE.TGL_KEBUTUHAN ASC, KS.NO_REG ASC, MD.JENIS_KELAMIN ASC
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->bindParam(':no_order', $no_order);
        $stmt->bindParam(':kode_farm', $kode_farm);
        $stmt->bindParam(':berat_standart', $berat_standart);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function susun_data_detail_order_kandang($no_order, $kode_farm, $berat_standart)
    {
        $result = $this->get_data_detail_order_kandang($no_order, $kode_farm, $berat_standart);
        $data = [];
        foreach ($result as $key => $value) {
            $data[$value['kode_kandang'].'#'.$value['id_kavling']]['kode_user_gudang'] = $value['kode_user_gudang'];
            $data[$value['kode_kandang'].'#'.$value['id_kavling']]['user_buat'] = $value['user_buat'];
            $data[$value['kode_kandang'].'#'.$value['id_kavling']]['user_gudang'] = $value['user_gudang'];
            $data[$value['kode_kandang'].'#'.$value['id_kavling']]['no_order'] = $value['no_order'];
            $data[$value['kode_kandang'].'#'.$value['id_kavling']]['farm'] = $value['farm'];
            $data[$value['kode_kandang'].'#'.$value['id_kavling']]['tgl_kirim'] = $value['tgl_kirim'];
            $data[$value['kode_kandang'].'#'.$value['id_kavling']]['tgl_keb_awal'] = $value['tgl_keb_awal'];
            $data[$value['kode_kandang'].'#'.$value['id_kavling']]['tgl_keb_akhir'] = $value['tgl_keb_akhir'];
            $data[$value['kode_kandang'].'#'.$value['id_kavling']]['detail'][] = $value;
        }

        return $data;
    }

    public function susun_data_detail_order_kandang_bdy($no_order, $kode_farm)
    {
        $result = $this->get_data_detail_order_kandang_bdy($no_order, $kode_farm);
        $data = [];
        $berat_bersih_per_kandang = 0;
        $jml_aktual_per_kandang = 0;
        $jml_aktual = 0;
        $jml_konversi_timbang = 0;
        $berat_per_pallet = 0;
        $sisa = 0;
        $berat_pallet = 0;
        $jml_kebutuhan_per_kandang = 0;

        foreach ($result as $key => $value) {
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['nama_barang'] = $value['nama_barang'];
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['no_kavling'] = $value['no_kavling'];
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['stok_kavling'] = $value['stok_kavling'];
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['kode_flok'] = $value['kode_flok'];
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['diserahkan_oleh'] = $value['diserahkan_oleh'];
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['id_diserahkan_oleh'] = $value['id_diserahkan_oleh'];
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['selesai'] = $value['selesai'];

            if (!empty($value['sisa'])) {
                $sisa = $value['sisa'];
            }
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['sisa'] = $sisa;
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['stok_kavling'] = $value['stok_kavling'];
            if (!empty($value['berat_bersih_per_kandang'])) {
                if (!isset($data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_bersih'])) {
                    $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_bersih'] = 0;
                }
                $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_bersih'] += $value['berat_bersih_per_kandang'];
            }
            $berat_hand_pallet = !empty($value['berat_hand_pallet']) ? $value['berat_hand_pallet'] : 0;

            $berat_pallet = ($berat_hand_pallet + $value['berat_pallet_murni']);
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_pallet'] = $berat_pallet;
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_pallet_murni'] = $value['berat_pallet_murni'];
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_hand_pallet'] = $berat_hand_pallet;
            if (!empty($value['berat_per_pallet'])) {
                $berat_per_pallet = $value['berat_per_pallet'];
            }
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_per_pallet'] = $berat_per_pallet;
            if (!empty($value['jml_kebutuhan_per_kandang'])) {
                $jml_kebutuhan_per_kandang = $value['jml_kebutuhan_per_kandang'];
            }
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['jml_kebutuhan_per_kandang'] = $jml_kebutuhan_per_kandang;
            if (!empty($value['jml_konversi_timbang'])) {
                $jml_konversi_timbang = $value['jml_konversi_timbang'];
            }
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['jml_konversi_timbang'] = $jml_konversi_timbang;
            if (!empty($value['jml_aktual'])) {
                $jml_aktual = $value['jml_aktual'];
            }
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['jumlah_aktual'] = $jml_aktual;
            if (!empty($value['jml_aktual_per_kandang'])) {
                $jml_aktual_per_kandang = $value['jml_aktual_per_kandang'];
            }
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['jml_aktual_per_kandang'] = $jml_aktual_per_kandang;
            if (!empty($value['berat_bersih_per_kandang'])) {
                $berat_bersih_per_kandang = $value['berat_bersih_per_kandang'];
            }
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_bersih_per_kandang'] = $berat_bersih_per_kandang;
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['detail'][$value['no_reg']]['detail'][] = $value;
            if (!isset($data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_rata2'])) {
                $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_rata2'] = array();
            }
            $data[$value['kode_barang'].'#'.$value['kode_pallet']]['detail']['data_pallet']['berat_rata2'][] = $value['berat_rata2'];
        }

        foreach ($data as $key1 => $value1) {
            foreach ($value1['detail'] as $key2 => $value2) {
                $berat_bersih = 0;
                $jumlah_aktual = 0;
                $selesai = 0;
                $jml_konversi_timbang = 0;
                $jml_sisa = $value2['jumlah_aktual'];
                foreach ($value2['detail'] as $key3 => $value3) {
                    foreach ($value3['detail'] as $key4 => $value4) {
                        $berat_bersih = $berat_bersih + $value4['berat_bersih_per_kandang'];
                        $jumlah_aktual = $jumlah_aktual + $value4['jml_aktual_per_kandang'];
                        $jml_sisa = $jml_sisa - $value4['jml_aktual_per_kandang'];
                        if ($value4['selesai'] == 1) {
                            $selesai = 1;
                            $jml_konversi_timbang = $value4['jml_konversi_timbang'];
                        }
                    }
                }
                $data[$key1]['detail'][$key2]['berat_bersih'] = $berat_bersih;
                $data[$key1]['detail'][$key2]['selesai'] = $selesai;
                $data[$key1]['detail'][$key2]['jml_konversi_timbang'] = $jml_konversi_timbang;
                $data[$key1]['detail'][$key2]['sisa'] = $jml_sisa;
            }
        }

        return array(
            'data' => $data,
          //  'data_hutang' => $data_hutang
        );
    }

    public function cek_kode_verifikasi_kavling($data)
    {
        $no_kavling = $data['no_kavling'];
        $kode_farm = $data['kode_farm'];
        $kode_verifikasi = $data['kode_verifikasi'];
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

    public function simpan_konfirmasi($data, $kode_farm, $user)
    {
        $result = 0;

        $this->dbSqlServer->conn_id->beginTransaction();

        $pengambilan = $this->simpan_konfirmasi_pengambilan($data, $kode_farm, $user);
        $penerimaan = [];
        //print_r($pengambilan);
        if (!empty($pengambilan['no_penerimaan_kandang'])) {
            $result = 1;
            $penerimaan = $this->simpan_konfirmasi_penerimaan($data, $pengambilan['no_penerimaan_kandang'], $kode_farm, $user);
            //print_r($penerimaan);
            if ($penerimaan['result'] == 0) {
                $result = 0;
            }
        }

        $output = array(
            'result' => $result,
            'data' => $penerimaan,
        );

        if ($result == 0) {
            $this->dbSqlServer->conn_id->rollback();
        } else {
            $this->dbSqlServer->conn_id->commit();
        }

        return $output;
    }

    public function simpan_konfirmasi_pengambilan($data, $kode_farm, $user)
    {
        $no_reg = $data['no_reg'];
        $id_kavling = $data['id_kavling'];
        $no_pallet = $data['no_pallet'];
        $kode_barang = $data['kode_barang'];
        $no_order = $data['no_order'];
        $jumlah = $data['jumlah'];
        $jumlah_aktual_zak = isset($data['jumlah_aktual_zak']) ? $data['jumlah_aktual_zak'] : 0;
        $jumlah_konversi_timbang = isset($data['jumlah_konversi_timbang']) ? $data['jumlah_konversi_timbang'] : 0;
        $berat = $data['berat'];
        $jenis_kelamin = $data['jenis_kelamin'];
        $query = <<<QUERY
            EXEC PICKING_CONFIRM_BARU
               '$kode_farm'
              ,'$no_reg'
              ,'$id_kavling'
              ,'$no_pallet'
              ,'$kode_barang'
              ,'$no_order'
              ,$jumlah
              ,$jumlah_konversi_timbang
              ,$jumlah_aktual_zak
              ,$berat
              ,'$user'
              ,'$no_reg'
              ,'$jenis_kelamin'
QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function simpan_konfirmasi_penerimaan($data, $no_penerimaan_kandang, $kode_farm, $user)
    {
        $no_reg = $data['no_reg'];
        $kode_barang = $data['kode_barang'];
        $no_order = $data['no_order'];
        $keterangan1 = 'PENERIMAAN KANDANG';
        $user_buat = $user;
        $jenis_kelamin = $data['jenis_kelamin'];
        $user_gudang = $data['user_gudang'];
        $query = <<<QUERY

        EXEC KONFIRMASI_PENERIMAAN_KANDANG_TERBARU
            '$kode_farm',
            '$no_reg',
            '$no_penerimaan_kandang',
            '$no_order',
            '$kode_barang',
            '$keterangan1',
            '$user_buat',
            '$jenis_kelamin',
            '$user_gudang'

QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cek_konversi($berat_standart, $berat)
    {
        $query = <<<QUERY
            SELECT * FROM (
                SELECT
                    mtb.*
                    , (mtb.BRT_STD*mtb.JML_SAK) - mtb.BATAS_BAWAH data_min
                    , (mtb.BRT_STD*mtb.JML_SAK) + mtb.BATAS_ATAS data_max
                    , 1 result
                FROM M_TOLERANSI_BERAT mtb
                WHERE mtb.BRT_STD = $berat_standart
            ) result
            WHERE result.data_min<=$berat
            AND result.data_max>=$berat
QUERY;
        // echo $query.'<br><br><br>';
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cek_diluar_toleransi($berat_standart, $berat)
    {
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
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_user_gudang($kode_farm)
    {
        $query = <<<QUERY
            SELECT
                MP.KODE_PEGAWAI kode_pegawai
                , MP.NAMA_PEGAWAI nama_pegawai
            FROM M_PEGAWAI MP
            JOIN PEGAWAI_D PD ON PD.KODE_PEGAWAI = MP.KODE_PEGAWAI AND PD.KODE_FARM = '$kode_farm' AND MP.STATUS_PEGAWAI = 'A' AND GRUP_PEGAWAI = 'PPB'
            ORDER BY NAMA_PEGAWAI ASC
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function simpan_data($kode_farm, $data, $user)
    {
        $result = 0;
        $totalPengambilan = 0;
        $this->dbSqlServer->conn_id->beginTransaction();

        foreach ($data as $key => $value) {
            $totalPengambilan += $value['jumlah_aktual_zak'];
            $pengambilan = $this->simpan_pengambilan($value, $kode_farm, $user);
            //echo 'Pengambilan '.print_r($pengambilan);
            $penerimaan = [];
            if (!empty($pengambilan['no_penerimaan_kandang'])) {
                $penerimaan = $this->simpan_penerimaan($value, $pengambilan['no_penerimaan_kandang'], $kode_farm, $user);
                //echo 'Penerimaan '.print_r($penerimaan);
                if ($penerimaan['result'] == 1) {
                    ++$result;
                }
            }
        }

        //echo count($data) .'=='. $result;
        if (count($data) == $result) {
            $status = 1;
            $this->dbSqlServer->conn_id->commit();
        } else {
            $status = 0;
            $this->dbSqlServer->conn_id->rollback();
        }

        $output = array(
            'result' => $status,
            'totalPengambilan' => $totalPengambilan,
        );

        return $output;
    }

    public function simpan_pengambilan($data, $kode_farm, $user)
    {
        $no_reg = $data['no_reg'];
        $id_kavling = $data['id_kavling'];
        $no_pallet = $data['no_pallet'];
        $kode_barang = $data['kode_barang'];
        $no_order = $data['no_order'];
        //$jumlah = $data ['jumlah'];
        $jumlah = isset($data['jumlah_aktual_zak']) ? $data['jumlah_aktual_zak'] : 0;
        $jumlah_aktual_zak = isset($data['jumlah_aktual']) ? $data['jumlah_aktual'] : 0;
        $jumlah_konversi_timbang = isset($data['jumlah_konversi_timbang']) ? $data['jumlah_konversi_timbang'] : 0;
        $berat = $data['berat'];
        $berat_timbang = isset($data['berat_timbang']) ? $data['berat_timbang'] : 0;
        $jenis_kelamin = $data['jenis_kelamin'];
        $kode_flok = $data['kode_flok'];
        $query = <<<QUERY
            EXEC PICKING_CONFIRM_BDY
               '$kode_farm'
              ,'$no_reg'
              ,'$id_kavling'
              ,'$no_pallet'
              ,'$kode_barang'
              ,'$no_order'
              ,$jumlah
              ,$jumlah_konversi_timbang
              ,$jumlah_aktual_zak
              ,$berat
              ,$berat_timbang
              ,'$user'
              ,'$no_reg'
              ,'$jenis_kelamin'
              ,'$kode_flok'
QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function simpan_penerimaan($data, $no_penerimaan_kandang, $kode_farm, $user)
    {
        $no_reg = $data['no_reg'];
        $no_pallet = $data['no_pallet'];
        $kode_barang = $data['kode_barang'];
        $no_order = $data['no_order'];
        $keterangan1 = 'PENERIMAAN KANDANG';
        $user_buat = $user;
        $jenis_kelamin = $data['jenis_kelamin'];
        $user_gudang = $data['user_gudang'];
        $kode_flok = $data['kode_flok'];
        $query = <<<QUERY

        EXEC KONFIRMASI_PENERIMAAN_KANDANG_BDY
            '$kode_farm',
            '$no_reg',
            '$no_penerimaan_kandang',
            '$no_order',
            '$kode_barang',
            '$keterangan1',
            '$user_buat',
            '$jenis_kelamin',
            '$user_gudang',
            '$kode_flok',
            '$no_pallet'

QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function list_kandang($kode_farm)
    {
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
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_data_riwayat_pengambilan($kode_farm, $no_reg)
    {
        $query = <<<QUERY
            EXEC RIWAYAT_PENGAMBILAN_PAKAN '$kode_farm', '$no_reg'
QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cek_pallet($kode_farm, $no_pallet, $zak)
    {
        $query = <<<QUERY
            select
                *
            from MOVEMENT_D
            where KODE_FARM = '$kode_farm'
            AND NO_PALLET = '$no_pallet'
            AND KETERANGAN1 = 'PUT'

QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function simpan_transaksi_verifikasi($kode_farm, $user, $transaction, $kode_flok = '')
    {
        $query = <<<QUERY
            insert into fingerprint_verification (
                kode_farm,
                [transaction],
                date_transaction,
                [user],
                kode_flok
            )
            --output inserted.date_transaction
            output left(cast(inserted.date_transaction as date),10)+' '+left(cast(inserted.date_transaction as time),12) date_transaction
            values (
                '$kode_farm',
                '$transaction',
                getdate(),
                '$user',
                '$kode_flok'
            )

QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cek_verifikasi($kode_farm, $user, $date_transaction, $kode_flok = '')
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
            from fingerprint_verification fv
            $str
            left join M_PEGAWAI mp
                on mp.kode_pegawai = fv.verificator
            where fv.date_transaction = '$date_transaction'
            and fv.kode_farm = '$kode_farm'
QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
