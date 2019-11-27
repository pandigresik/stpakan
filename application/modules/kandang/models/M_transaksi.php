<?php

class M_transaksi extends MY_Model
{
    private $dbSqlServer;
    protected $_user;

    public function __construct()
    {
        parent::__construct();
        $this->dbSqlServer = $this->load->database('default', true);
        $this->_user = $this->session->userdata('kode_user');
    }

    public function get_data_kandang($start = null, $offset = null, $kode_farm = null, $siklus = null, $flock = null, $kandang = null, $koordinator = null, $pengawas = null, $operator = null, $tgl_doc_in = null, $periode1 = null, $periode2 = null)
    {
        $filter_str = '';
        $filter_arr = array();

        $filter_bottom_str = '';
        $filter_bottom_arr = array();

        if (isset($siklus)) {
            $filter_arr[] = "replace(substring(KS.NO_REG,charindex('/', KS.NO_REG)+1,7),'/','') LIKE '%".$siklus."%'";
        }

        if (isset($flock)) {
            $filter_arr[] = "KS.flok_bdy LIKE '%".$flock."%'";
        }

        if (isset($kandang)) {
            $filter_arr[] = "KS.kode_kandang LIKE '%".$kandang."%'";
        }

        if (isset($koordinator)) {
            $filter_arr[] = "COALESCE(MP.NAMA_PEGAWAI,
                    (SELECT top 1 NAMA_PEGAWAI FROM M_PEGAWAI mp
                    inner join PEGAWAI_D pd on mp.KODE_PEGAWAI = pd.KODE_PEGAWAI and pd.KODE_FARM = 'GD'
                    WHERE GRUP_PEGAWAI = 'KPPB')) LIKE '%".$koordinator."%'";
        }

        if (isset($pengawas)) {
            $filter_arr[] = "MP2.NAMA_PEGAWAI LIKE '%".$pengawas."%'";
        }

        if (isset($operator)) {
            $filter_arr[] = "MP3.NAMA_PEGAWAI LIKE '%".$operator."%'";
        }

        if (isset($tgl_doc_in)) {
            $filter_arr[] = "CAST(KS.tgl_doc_in AS DATE) = '".$tgl_doc_in."'";
        }

        if (count($filter_arr) > 0) {
            $filter_str .= ' WHERE ';
            $filter_str .= " KS.STATUS_SIKLUS != 'P'";
            $filter_str .= " AND KS.KODE_FARM = '$kode_farm' AND ";

            $filter_str .= implode(' AND ', $filter_arr);
        } else {
            $filter_str .= ' WHERE ';
            $filter_str .= " KS.STATUS_SIKLUS != 'P'";
            $filter_str .= " AND KS.KODE_FARM = '$kode_farm' ";
            $filter_str .= " AND KS.KODE_SIKLUS between $periode1 and $periode2";
        }
        if (isset($start) and isset($offset)) {
            $filter_bottom_arr[] = "row > {$start} AND row <= {$offset}";
        }

        if (count($filter_bottom_arr) > 0) {
            $filter_bottom_str .= ' WHERE ';
            $filter_bottom_str .= implode(' AND ', $filter_bottom_arr);
        }

        $query = <<<QUERY
            SELECT
                *
            FROM (
            SELECT DISTINCT
                ROW_NUMBER() OVER (ORDER BY mainqry.KODE_SIKLUS DESC, mainqry.KODE_KANDANG asc) row
                , mainqry.*
            FROM (
                SELECT
				KS.KODE_SIKLUS,
                KS.NO_REG,
                PL.STATUS,
                PL.USER_BUAT,
                convert(varchar(10),PL.TGL_BUAT,126)+' '+convert(varchar(5),PL.TGL_BUAT,108) TGL_BUAT,
                PL.USER_REVIEW,
                convert(varchar(10),PL.TGL_REVIEW,126)+' '+convert(varchar(5),PL.TGL_REVIEW,108) TGL_REVIEW,
                PL.USER_ACK,
                convert(varchar(10),PL.TGL_ACK,126)+' '+convert(varchar(5),PL.TGL_ACK,108) TGL_ACK,
                replace(substring(KS.NO_REG,charindex('/', KS.NO_REG)+1,7),'/','') periode,
                KS.tgl_doc_in, KS.flok_bdy, KS.kode_kandang, PL.koordinator, PL.pengawas, PL.operator,
                COALESCE(MP.NAMA_PEGAWAI, '-',
                    (SELECT top 1 NAMA_PEGAWAI FROM M_PEGAWAI mp
                    inner join PEGAWAI_D pd on mp.KODE_PEGAWAI = pd.KODE_PEGAWAI and pd.KODE_FARM = '$kode_farm'
                    WHERE GRUP_PEGAWAI = 'KPPB')) nama_koordinator,
                COALESCE(MP2.NAMA_PEGAWAI,'-') nama_pengawas, COALESCE(MP3.NAMA_PEGAWAI,'-') nama_operator
                FROM KANDANG_SIKLUS KS
                LEFT JOIN M_PLOTING_PELAKSANA PL ON KS.NO_REG = PL.NO_REG AND KS.KODE_KANDANG = PL.KODE_KANDANG AND KS.KODE_SIKLUS = PL.KODE_SIKLUS
                LEFT JOIN M_PEGAWAI MP ON PL.KOORDINATOR = MP.KODE_PEGAWAI
                LEFT JOIN M_PEGAWAI MP2 ON PL.PENGAWAS = MP2.KODE_PEGAWAI
                LEFT JOIN M_PEGAWAI MP3 ON PL.OPERATOR = MP3.KODE_PEGAWAI
                $filter_str
            ) mainqry
            ) mainqry_end
            $filter_bottom_str
QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFarm($kode_farm = '')
    {
        $query = <<<QUERY
        SELECT * FROM M_FARM
        where kode_farm = :kode_farm
QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->bindParam(':kode_farm', $kode_farm);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSiklus($kode_farm = '')
    {
        $query = <<<QUERY
        SELECT M_PERIODE.*, (
            select top 1 kode_siklus from M_PERIODE where kode_farm = '$kode_farm' and
            kode_siklus < (select kode_siklus from M_PERIODE where kode_farm = '$kode_farm' and STATUS_PERIODE = 'A')
            order by kode_siklus desc
        ) as siklus_sebelum FROM M_PERIODE
        where kode_farm = '$kode_farm' and kode_siklus <= (select kode_siklus from M_PERIODE where kode_farm = '$kode_farm' and STATUS_PERIODE = 'A')
        order by kode_siklus desc,STATUS_PERIODE desc
QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlotting($kode_farm = '', $kode_siklus = '')
    {
        $query = <<<QUERY
            select ks.*, pl.KOORDINATOR, pl.PENGAWAS, pl.OPERATOR,
            	COALESCE(MP.NAMA_PEGAWAI,'') NAMA_KOORDINATOR, COALESCE(MP2.NAMA_PEGAWAI,'') NAMA_PENGAWAS
            from KANDANG_SIKLUS ks
            left join (
            	SELECT NO_REG
            		,koordinator
                    ,pengawas
            		,KODE_SIKLUS
            		,stuff (
                        (select DISTINCT ','+ mpo.NAMA_PEGAWAI
                        from M_PLOTING_PELAKSANA pp
            			left join m_pegawai mpo on pp.OPERATOR = mpo.KODE_PEGAWAI
                        where mpp.NO_REG= no_reg AND mpp.KOORDINATOR = pp.koordinator AND mpp.PENGAWAS = pp.pengawas
                        for xml path (''))
                        ,1,1,''
            		) operator
            	FROM M_PLOTING_PELAKSANA mpp
            	GROUP BY NO_REG,koordinator,pengawas,KODE_SIKLUS
            ) pl on ks.KODE_SIKLUS = pl.KODE_SIKLUS and ks.NO_REG = pl.NO_REG
            LEFT JOIN M_PEGAWAI mp ON pl.KOORDINATOR = mp.KODE_PEGAWAI
            LEFT JOIN M_PEGAWAI mp2 ON pl.PENGAWAS = mp2.KODE_PEGAWAI
            where ks.kode_farm = '$kode_farm' and ks.kode_siklus = '$kode_siklus'

            order by ks.KODE_KANDANG asc
QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        // $stmt->bindParam(':kode_farm', $kode_farm);
        // $stmt->bindParam(':kode_siklus', $kode_siklus);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listPegawai($kode_farm = '', $grup_pegawai = '', $nama_pegawai = '')
    {
        $str = '';
        if (isset($nama_pegawai)) {
            $str .= "and nama_pegawai like '%$nama_pegawai%'";
        }
        $query = <<<QUERY
        select * from M_PEGAWAI mp
        inner join PEGAWAI_D pd on mp.KODE_PEGAWAI = pd.KODE_PEGAWAI and pd.KODE_FARM = '$kode_farm'
        where mp.GRUP_PEGAWAI = '$grup_pegawai' and mp.STATUS_PEGAWAI = 'A' $str
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listPlottingPegawai($kode_siklus)
    {
        $query = <<<SQL
        select ks.no_reg,ks.flok_bdy flok,ks.kode_kandang
            ,mpp.koordinator,mpp.pengawas,mpp.operator 
            ,mpk.nama_pegawai nama_koordinator
            ,mppg.nama_pegawai nama_pengawas
            ,mpo.nama_pegawai nama_operator
        from kandang_siklus ks
        left join  m_ploting_pelaksana mpp on mpp.no_reg = ks.no_reg
        left join m_pegawai mpk on mpk.kode_pegawai = mpp.koordinator
        left join m_pegawai mppg on mppg.kode_pegawai = mpp.pengawas
        left join m_pegawai mpo on mpo.kode_pegawai = mpp.operator
        where ks.kode_siklus = '{$kode_siklus}' and ks.status_siklus = 'O'
        order by ks.flok_bdy,ks.kode_kandang
SQL;

        return $this->db->query($query)->result_array();
    }

    /*
    public function listPlottingPegawai($kode_farm = '',$grup_pegawai = '',$kode_siklus){
        $str = '';
        $str2 = '';
        $arr_str = array();
        $arr_str2 = array();
        if (isset($grup_pegawai)) {
            foreach ($grup_pegawai as $key=>$val) {
                $arr_str[] = "select '{$val}' as deskripsi, {$key} as urutan ";
                $arr_str2[] = " grup_pegawai.DESKRIPSI like '%{$val}%' ";
            }
            $str = implode(" UNION ", $arr_str);
            $str2 = implode(" OR ", $arr_str2);
        }
        $query = <<<QUERY
        select *
        from M_PEGAWAI mp
        INNER JOIN PEGAWAI_D pd on mp.KODE_PEGAWAI = pd.KODE_PEGAWAI and pd.KODE_FARM = '{$kode_farm}'
        INNER JOIN (
            SELECT grup_pegawai.GRUP_PEGAWAI, grup_pegawai.DESKRIPSI, tblurutan.urutan
            FROM M_GRUP_PEGAWAI grup_pegawai
            INNER JOIN (
                {$str}
            ) tblurutan ON tblurutan.deskripsi = grup_pegawai.deskripsi
            WHERE {$str2}
        ) mgp ON mgp.GRUP_PEGAWAI = mp.GRUP_PEGAWAI
        JOIN (
            SELECT kode_pegawai, coalesce(KOORDINATOR_FLOK, PENGAWAS_FLOK, OPERATOR_FLOK) AS KODE_FLOK,
            coalesce(KOORDINATOR_KANDANG, PENGAWAS_KANDANG, OPERATOR_KANDANG) AS KODE_KANDANG
            FROM (
                SELECT PEGAWAI.KODE_PEGAWAI,
                stuff((
                    SELECT ',' + CONVERT(varchar(10), siklus.FLOK_BDY)
                    FROM M_PLOTING_PELAKSANA plotting
                    INNER JOIN KANDANG_SIKLUS siklus ON siklus.NO_REG = plotting.NO_REG AND siklus.KODE_KANDANG=plotting.KODE_KANDANG
                    WHERE koordinator = pegawai.KODE_PEGAWAI and plotting.kode_siklus = '{$kode_siklus}'
                    ORDER BY plotting.NO_REG, plotting.KODE_SIKLUS, plotting.KODE_KANDANG
                    FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') AS KOORDINATOR_FLOK,
                stuff((
                    SELECT ',' + plotting.KODE_KANDANG
                    FROM M_PLOTING_PELAKSANA plotting
                    WHERE koordinator = pegawai.KODE_PEGAWAI and plotting.kode_siklus = '{$kode_siklus}'
                    ORDER BY plotting.NO_REG, plotting.KODE_SIKLUS, plotting.KODE_KANDANG
                    FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') AS KOORDINATOR_KANDANG,
                stuff((
                    SELECT ',' + CONVERT(varchar(10), siklus.FLOK_BDY)
                    FROM M_PLOTING_PELAKSANA plotting
                    INNER JOIN KANDANG_SIKLUS siklus ON siklus.NO_REG = plotting.NO_REG AND siklus.KODE_KANDANG=plotting.KODE_KANDANG
                    WHERE pengawas = pegawai.KODE_PEGAWAI and plotting.kode_siklus = '{$kode_siklus}'
                    ORDER BY plotting.NO_REG, plotting.KODE_SIKLUS, plotting.KODE_KANDANG
                    FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') AS PENGAWAS_FLOK,
                stuff((
                    SELECT ',' + plotting.KODE_KANDANG
                    FROM M_PLOTING_PELAKSANA plotting
                    WHERE pengawas = pegawai.KODE_PEGAWAI and plotting.kode_siklus = '{$kode_siklus}'
                    ORDER BY plotting.NO_REG, plotting.KODE_SIKLUS, plotting.KODE_KANDANG
                    FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') AS PENGAWAS_KANDANG,
                stuff((
                    SELECT ',' + CONVERT(varchar(10), siklus.FLOK_BDY)
                    FROM M_PLOTING_PELAKSANA plotting
                    INNER JOIN KANDANG_SIKLUS siklus ON siklus.NO_REG = plotting.NO_REG AND siklus.KODE_KANDANG=plotting.KODE_KANDANG
                    WHERE operator = pegawai.KODE_PEGAWAI and plotting.kode_siklus = '{$kode_siklus}'
                    ORDER BY plotting.NO_REG, plotting.KODE_SIKLUS, plotting.KODE_KANDANG
                    FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') AS OPERATOR_FLOK,
                stuff((
                    SELECT ',' + plotting.KODE_KANDANG
                    FROM M_PLOTING_PELAKSANA plotting
                    WHERE operator = pegawai.KODE_PEGAWAI and plotting.kode_siklus = '{$kode_siklus}'
                    ORDER BY plotting.NO_REG, plotting.KODE_SIKLUS, plotting.KODE_KANDANG
                    FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '') AS OPERATOR_KANDANG
                FROM M_PEGAWAI pegawai
                INNER JOIN PEGAWAI_D detpegawai ON detpegawai.KODE_PEGAWAI = PEGAWAI.KODE_PEGAWAI AND detpegawai.KODE_FARM='{$kode_farm}'
                WHERE STATUS_PEGAWAI='A'
            ) tblPlotting
        ) plotting ON plotting.KODE_PEGAWAI=mp.KODE_PEGAWAI AND plotting.KODE_FLOK IS NOT NULL AND plotting.KODE_KANDANG IS NOT null
        where mp.STATUS_PEGAWAI = 'A'
        ORDER BY urutan, mp.NAMA_PEGAWAI
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }*/
    //getStatusPlotting
    public function isCompletePloting($kode_siklus = '')
    {
        $query = <<<QUERY
        select count(*) belum_ploting
        from kandang_siklus ks 
        left join M_PLOTING_PELAKSANA mpp on mpp.no_reg = ks.no_reg
        where ks.kode_siklus = '{$kode_siklus}' and ks.status_siklus = 'O' and mpp.operator is null
QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['belum_ploting'] ? 0 : 1;
    }

    public function AutomaticallyInsertFlock($kode_farm = '', $kode_siklus = '', $deskripsi)
    {
        $query = <<<QUERY
        SELECT stuff((
			SELECT ',' + CONVERT(varchar(10), KANDANG_SIKLUS.FLOK_BDY)
			FROM KANDANG_SIKLUS
			INNER JOIN (
				SELECT count(*) AS jumlah_koordinator
				FROM M_PEGAWAI
				INNER JOIN PEGAWAI_D ON PEGAWAI_D.KODE_PEGAWAI = M_PEGAWAI.KODE_PEGAWAI
				INNER JOIN M_GRUP_PEGAWAI ON M_GRUP_PEGAWAI.GRUP_PEGAWAI=M_PEGAWAI.GRUP_PEGAWAI
				WHERE STATUS_PEGAWAI='A' AND KODE_FARM='{$kode_farm}' AND DESKRIPSI='{$deskripsi}'
			) pegawai ON jumlah_koordinator = 1
			WHERE KODE_SIKLUS='{$kode_siklus}' AND KODE_FARM='{$kode_farm}'
			GROUP BY FLOK_BDY
			FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, ''
		) AS FLOK_BDY
QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function plotted_all_kandang($kode_farm = '', $str_kode_kandang = '')
    {
        $query = <<<QUERY
		SELECT count(*) AS jumlah
		from (
			SELECT KANDANG_SIKLUS.KODE_KANDANG, tblkandang.kandang
			FROM KANDANG_SIKLUS 
			LEFT OUTER JOIN (
				{$str_kode_kandang}
			) tblkandang ON tblkandang.kandang = KANDANG_SIKLUS.KODE_KANDANG
			WHERE KODE_FARM='{$kode_farm}' AND tblkandang.kandang IS NULL and KANDANG_SIKLUS.STATUS_SIKLUS = 'O' 
		) plotted_kandang
QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function plotted_all_kandang_flok($kode_siklus = '', $flok = '', $str_kode_kandang = '')
    {
        $flok_str = implode(',', $flok);
        $query = <<<QUERY
		SELECT count(*) AS jumlah
		from (
			SELECT KANDANG_SIKLUS.KODE_KANDANG, tblkandang.kandang
			FROM KANDANG_SIKLUS 
			LEFT OUTER JOIN (
				{$str_kode_kandang}
			) tblkandang ON tblkandang.kandang = KANDANG_SIKLUS.KODE_KANDANG
			WHERE kode_siklus='{$kode_siklus}' AND tblkandang.kandang IS NULL and KANDANG_SIKLUS.STATUS_SIKLUS = 'O' and  KANDANG_SIKLUS.flok_bdy in ({$flok_str})
		) plotted_kandang
QUERY;
        //echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPegawai($kode_farm = '', $grup_pegawai = '', $nama_pegawai = '')
    {
        $json = [];

        $result = $this->listPegawai($kode_farm, $grup_pegawai, $nama_pegawai);

        foreach ($result as $key => $value) {
            $json[] = ['id' => $value['KODE_PEGAWAI'], 'text' => $value['NAMA_PEGAWAI']];
        }

        return json_encode($json);
    }

    public function cekPengawas($kode_farm = '', $kode_siklus = '', $flock = '', $pengawas = '')
    {
        $query = <<<QUERY
        select * from M_PLOTING_PELAKSANA pl
        inner join KANDANG_SIKLUS ks on ks.NO_REG = pl.NO_REG and ks.KODE_FARM = '$kode_farm'
        left join M_PEGAWAI mp on pl.pengawas = mp.KODE_PEGAWAI
        where pl.pengawas = '$pengawas' and pl.KODE_SIKLUS = $kode_siklus and ks.FLOK_BDY != $flock
QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        // $stmt->bindParam(':kode_farm', $kode_farm);
        // $stmt->bindParam(':kode_siklus', $kode_siklus);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cekOperator($kode_farm = '', $kode_siklus = '', $operator = '')
    {
        $query = <<<QUERY
        select * from M_PLOTING_PELAKSANA pl
        inner join KANDANG_SIKLUS ks on ks.NO_REG = pl.NO_REG and ks.KODE_FARM = '$kode_farm'
        left join M_PEGAWAI mp on pl.operator = mp.KODE_PEGAWAI
        where pl.operator = '$operator' and pl.KODE_SIKLUS = $kode_siklus
QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        // $stmt->bindParam(':kode_farm', $kode_farm);
        // $stmt->bindParam(':kode_siklus', $kode_siklus);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_plotting_pelaksana($plotting_pelaksana)
    {
        $success = true;
        if (true) {
            $this->dbSqlServer->trans_begin();

            foreach ($plotting_pelaksana as $key => $val) {
                $success = $success && $this->dbSqlServer->insert('M_PLOTING_PELAKSANA', $val);
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

    public function notifikasi($kode_farm)
    {
        $sql = <<<SQL
        SELECT DISTINCT ks.flok_bdy
        FROM KANDANG_SIKLUS ks
        LEFT JOIN M_PLOTING_PELAKSANA mpp ON mpp.NO_REG = ks.NO_REG 
        WHERE ks.STATUS_SIKLUS = 'O' AND ks.KODE_FARM = '{$kode_farm}'  AND mpp.NO_REG IS NULL
SQL;

        return $this->db->query($sql)->result_array();
    }
}
