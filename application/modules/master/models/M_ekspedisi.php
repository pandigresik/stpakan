<?php

class M_ekspedisi extends CI_Model {

    private $dbSqlServer;

    protected $nopolavail = "";

    public function __construct() {
        parent::__construct();
        $this->dbSqlServer = $this->load->database("default", true);
    }

    private function connect()
    {
        try {
            $dbc = new \PDO($this->dbSqlServer->dsn, $this->dbSqlServer->username, $this->dbSqlServer->password);

            $dbc->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return (Object)array(
                'return' => $dbc,
                'message' => 'connected.'
            );
        } catch (\PDOException $e) {
            return (Object)array(
                'return' => false,
                'message' => $e->getMessage()
            );
        }
    }

    function get_ekspedisi($start = null, $offset = null, $nama_ekspedisi = null, $alamat = null, $kota = null, $jumlah_kendaraan = null, $kode_farm = null) {
        $filter_str = "";
        $filter_arr = array();

        $filter_bottom_str = "";
        $filter_bottom_arr = array();
        
        $filter_arr [] = "mf.KODE_FARM IS NULL";

        if (isset($nama_ekspedisi))
            $filter_arr [] = "me.NAMA_EKSPEDISI like '%" . $nama_ekspedisi . "%'";
        if (isset($alamat))
            $filter_arr [] = "me.ALAMAT like '%" . $alamat . "%'";
        if (isset($kota))
            $filter_arr [] = "me.KOTA like '%" . $kota . "%'";

        if (count($filter_arr) > 0) {
            $filter_str .= " where ";
            $filter_str .= implode(" and ", $filter_arr);
        }

        if (isset($start) and isset($offset))
            $filter_bottom_arr [] = "row > {$start} and row <= {$offset}";

        if (count($filter_bottom_arr) > 0) {
            $filter_bottom_str .= " where ";
            $filter_bottom_str .= implode(" and ", $filter_bottom_arr);
        }

        $sql = <<<QUERY
			select * from (
				SELECT 
                    ROW_NUMBER() OVER (ORDER BY me.NAMA_EKSPEDISI ASC) AS ROW
                    , me.* 
                    , mev.*
                    , ISNULL(mev.VEHICLE_COUNT_OLD,0) VEHICLE_COUNT
                    , mf.KODE_FARM
                FROM M_EKSPEDISI me
                LEFT JOIN (
                    SELECT count(coutk.kode) AS VEHICLE_COUNT_OLD, coutk.kode AS KODE FROM
                    (
                    SELECT
                        COUNT(mev.KODE_EKSPEDISI) VEHICLE_COUNT_OLD
                        , mev.KODE_EKSPEDISI KODE, mev.NO_KENDARAAN
                    FROM M_EKPEDISI_VEHICLE_NEW mev
                    GROUP BY mev.KODE_EKSPEDISI, mev.NO_KENDARAAN, mev.TIPE_KENDARAAN
                    ) AS coutk
                    GROUP BY coutk.kode
                ) mev ON mev.KODE = me.KODE_EKSPEDISI
                LEFT JOIN (
                    SELECT KODE_FARM 
                    FROM M_FARM 
                    WHERE KODE_FARM <> '$kode_farm'
                ) mf ON mf.KODE_FARM = me.KODE_EKSPEDISI
				$filter_str
			) mainqry
			$filter_bottom_str
QUERY;
         
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function get_ekspedisi_by_id($kode_ekspedisi) {
        $sql = <<<QUERY
				select 
				    top 1 me.*
				from M_EKSPEDISI me
				where me.KODE_EKSPEDISI = '{$kode_ekspedisi}'
	
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function get_ekspedisi_vehicle_by_kode_ekspedisi($kode_ekspedisi) {
        $sql = <<<QUERY
                select
                    mev.KODE_EKSPEDISI,
                    ISNULL(mev.NO_KENDARAAN,'') NO_KENDARAAN,
                    ISNULL(mev.TIPE_KENDARAAN,'') TIPE_KENDARAAN,
                    ISNULL(cast(mev.MAX_KUANTITAS as varchar(50)),'') MAX_KUANTITAS,
                    ISNULL(cast(mev.MAX_BERAT as varchar(50)),'') MAX_BERAT
                from M_EKPEDISI_VEHICLE_NEW mev
                where mev.KODE_EKSPEDISI = '{$kode_ekspedisi}'
                group by
                    mev.KODE_EKSPEDISI,
                    mev.NO_KENDARAAN,
                    mev.TIPE_KENDARAAN,
                    mev.MAX_KUANTITAS,
                    mev.MAX_BERAT
    
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function get_ekspedisi_vehicle_detil_by_kode_ekspedisi($kode_ekspedisi, $no_pol, $tipe_kendaraan, $kuantitas_maksimal, $berat_maksimal) {
        $sql = <<<QUERY
                select
                    mev.KODE_FARM,
                    ISNULL(cast(mev.MAX_RIT as varchar(50)),'') MAX_RIT,
					mev.MIN_RIT
                from M_EKPEDISI_VEHICLE_NEW mev
                where 
                    mev.KODE_EKSPEDISI = '{$kode_ekspedisi}'
                    and mev.NO_KENDARAAN = '{$no_pol}'
                    and mev.TIPE_KENDARAAN = '{$tipe_kendaraan}'
                    and mev.MAX_KUANTITAS = '{$kuantitas_maksimal}'
                    and mev.MAX_BERAT = '{$berat_maksimal}'
    
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function hapus_kendaraan($no_pol) {
        $sql = <<<QUERY
				DELETE FROM M_EKPEDISI_VEHICLE_NEW 
				OUTPUT DELETED.NO_KENDARAAN no_pol
				WHERE NO_KENDARAAN = '{$no_pol}'
	
QUERY;
        // echo $sql;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function get_all_kota() {
        $sql = <<<QUERY
                SELECT * FROM M_KOTA ORDER BY KOTA ASC
    
QUERY;
        // echo $sql;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function generate_kode_ekspedisi() {
        $sql = <<<QUERY
				SELECT 'EKS'+ISNULL(RIGHT('00000'+ISNULL(CAST(SUBSTRING(MAX(KODE_EKSPEDISI),4,5)+1 AS VARCHAR(5)),'1'),5),'EKS00001') KODE_EKSPEDISI
				from M_EKSPEDISI
				where SUBSTRING(KODE_EKSPEDISI,1,3) = 'EKS'
	
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ["KODE_EKSPEDISI"];
    }

    public function checkavaildata($ekspedisi, $ekspedisi_vehicle)
    {
        $availev = 0;
        $nopolavail = [];
        foreach ($ekspedisi_vehicle as $key => $value) {
            $pdo = $this->connect()->return;

            $stmt = $pdo->prepare("select * from M_EKPEDISI_VEHICLE_NEW mev where mev.NO_KENDARAAN = ?");
            $stmt->execute(array($value['no_pol']));

            $fetchmev = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($fetchmev)) {
                $availev += 1;
                array_push($nopolavail, $value['no_pol']);
            }
        }

        if (!empty($nopolavail)) {
            $this->nopolavail = implode(", ", $nopolavail);
        }

        $pdo = null;

        $availe = 0;

        $pdo = $this->connect()->return;

        $stmt = $pdo->prepare("select * from M_EKSPEDISI me where me.KODE_EKSPEDISI = ?");
        $stmt->execute(array($ekspedisi['kode_ekspedisi']));

        $fetchme = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($fetchme)) {
            $availe += 1;
        }

        if ($availe == 0 && $availev == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkavaildata1($ekspedisi, $ekspedisi_vehicle)
    {
        $availev = 0;
        $nopolavail = [];
        foreach ($ekspedisi_vehicle as $key => $value) {
            $pdo = $this->connect()->return;

            $stmt = $pdo->prepare("select * from M_EKPEDISI_VEHICLE_NEW mev where mev.NO_KENDARAAN = ?");
            $stmt->execute(array($value['no_pol']));

            $fetchmev = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($fetchmev)) {
                $availev += 1;
                array_push($nopolavail, $value['no_pol']);
            }
        }

        if (!empty($nopolavail)) {
            $this->nopolavail = implode(", ", $nopolavail);
        }

        if ($availev == 0) {
            return true;
        } else {
            return false;
        }
    }

    function simpan_ekspedisi($data) {
        $this->dbSqlServer->conn_id->beginTransaction();

        $data_ekspedisi = $data['data_ekspedisi'][0];
        $data_ekspedisi_vehicle = $data['data_kendaraan'];

        if ($this->checkavaildata($data_ekspedisi, $data_ekspedisi_vehicle)) {
            $return = [
                'gen_kode_ekspedisi' => $this->generate_kode_ekspedisi(),
                'success' => 1,
                'kode_ekspedisi' => $data_ekspedisi['kode_ekspedisi']
            ];

            $this->insert_ekspedisi($data_ekspedisi);
            foreach ($data_ekspedisi_vehicle as $key => $value) {
                $this->insert_ekspedisi_vehicle($value, $data_ekspedisi['kode_ekspedisi']);
            }           
            
        } else {
            $nopoltmp = [];
            foreach ($data_ekspedisi_vehicle as $key => $value) {
                $nopoltmp[] = $value['no_pol'];
            }
            $return = [
                'success' => 2,
                'kode_ekspedisi' => $data_ekspedisi['kode_ekspedisi'],
                'no_pol' => $this->nopolavail
            ];
        }   

        return $return;
    }


    public function queries($qry)
    {
        $pdo = $this->connect()->return;

        $stmt = $pdo->prepare($qry);
        $stmt->execute();

        return $stmt;
    }

    function simpan_update_ekspedisi($data) {
        $this->dbSqlServer->conn_id->beginTransaction();

        $data_ekspedisi = $data['data_ekspedisi'][0];
        $data_ekspedisi_vehicle = $data['data_kendaraan'];


        $pdo = $this->connect()->return;
        $stmt = $pdo->prepare("select * from M_EKPEDISI_VEHICLE_NEW mev where mev.KODE_EKSPEDISI = ?");
        $stmt->execute(array($data_ekspedisi['kode_ekspedisi']));
        $fetchmev = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->drop_ekspedisi_vehicle($data_ekspedisi['kode_ekspedisi']);

        if ($this->checkavaildata1($data_ekspedisi, $data_ekspedisi_vehicle)) {
            // die('1');
            $return = [
                'success' => 1,
                'kode_ekspedisi' => $data_ekspedisi['kode_ekspedisi']
            ];

            $this->update_ekspedisi($data_ekspedisi);
            foreach ($data_ekspedisi_vehicle as $key => $value) {
                $this->insert_ekspedisi_vehicle($value, $data_ekspedisi['kode_ekspedisi']);
            }
        } else {
            // die('2');
            $pdo = null;

            $stckmev = [];

            foreach ($fetchmev as $key => $value) {
                $stckmev[] = $value['NO_KENDARAAN'];

                $pdo = $this->connect()->return;
                $stmt = $pdo->prepare("insert into M_EKPEDISI_VEHICLE_NEW(
                    KODE_EKSPEDISI, 
                    NO_KENDARAAN, 
                    TIPE_KENDARAAN, 
                    MAX_KUANTITAS, 
                    MAX_BERAT, 
                    KODE_FARM, 
                    MAX_RIT)values(
                        ?, ?, ?, ?, ?, ?, ?
                    )");
                $stmt->execute(array(
                    $value['KODE_EKSPEDISI'],
                    $value['NO_KENDARAAN'],
                    $value['TIPE_KENDARAAN'],
                    $value['MAX_KUANTITAS'],
                    $value['MAX_BERAT'],
                    $value['KODE_FARM'],
                    $value['MAX_RIT']
                ));
            }
            

            /*$nopoltmp = [];
            foreach ($data_ekspedisi_vehicle as $key => $value) {
                if (!in_array($value['no_pol'], $stckmev)) {
                    $nopoltmp[] = $value['no_pol'];
                }                
            }*/
            
            $return = [
                'success' => 2,
                'no_pol' => $this->nopolavail
            ];
        }   

        return $return;
    }

    function cek_no_pol($no_pol) {
        $sql = <<<QUERY
				
				select count(*) n_count, KODE_EKSPEDISI kode_ekspedisi from M_EKPEDISI_VEHICLE_NEW where NO_KENDARAAN = '$no_pol'
				group by KODE_EKSPEDISI
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function insert_ekspedisi($data) {
        $kode_ekspedisi = $data ['kode_ekspedisi'];
        $nama_ekspedisi = $data ['nama_ekspedisi'];
        $alamat = $data ['alamat'];
        $kota = $data ['kota'];
        $grup = 'E';

        $pdo = $this->connect()->return;

        $stmt = $pdo->prepare("insert into M_EKSPEDISI(KODE_EKSPEDISI, NAMA_EKSPEDISI, ALAMAT, KOTA, GRUP_EKSPEDISI)values(?, ?, ?, ?, ?)");
        $stmt->execute(array(
            $kode_ekspedisi,
            $nama_ekspedisi,
            $alamat,
            $kota,
            $grup
        ));

        $stmt = $pdo->prepare("select top 1 * from M_EKSPEDISI as me where me.kode_ekspedisi = ? order by me.KODE_EKSPEDISI desc");
        $stmt->execute(array($kode_ekspedisi));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    function insert_ekspedisi_vehicle($data, $kode_ekspedisi) {
        // $kode_ekspedisi = $data['kode_ekspedisi'];
        $no_pol = $data['no_pol'];
        $tipe_kendaraan = $data ['tipe_kendaraan'];
        $kuantitas_maksimal = $data ['kuantitas_maksimal'];
        $berat_maksimal = $data ['berat_maksimal'];
        $kode_farm = $data ['kode_farm'];
        $max_rit = $data ['max_rit'];
		$min_rit = $data['min_rit'];

        foreach ($kode_farm as $key => $value) {
            $pdo = $this->connect()->return;

            $stmt = $pdo->prepare("insert into M_EKPEDISI_VEHICLE_NEW(KODE_EKSPEDISI, NO_KENDARAAN, TIPE_KENDARAAN, MAX_KUANTITAS, MAX_BERAT, KODE_FARM, MAX_RIT, MIN_RIT)values(?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute(array(
                $kode_ekspedisi,
                $no_pol,
                $tipe_kendaraan,
                $kuantitas_maksimal,
                $berat_maksimal,
                $value,
                $max_rit[$key],
				$min_rit[$key]
            ));
        }

        $stmt = $pdo->prepare("select top 1 * from M_EKPEDISI_VEHICLE_NEW as mev where mev.NO_KENDARAAN = ?");
        $stmt->execute(array($no_pol));

        return $stmt->fetch(PDO::FETCH_ASSOC);/*


        $sql = <<<QUERY
				insert into M_EKPEDISI_VEHICLE_NEW 
				output inserted.NO_KENDARAAN no_kendaraan
				values (
					'$kode_ekspedisi',
					'$no_pol',
					'$tipe_kendaraan',
					$kuantitas_maksimal,
					$berat_maksimal,
                    '$kode_farm',
                    $max_rit
				)
QUERY;
        // echo $sql;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);*/
    }

    function update_ekspedisi($data) {
        $kode_ekspedisi = $data ['kode_ekspedisi'];
        $nama_ekspedisi = $data ['nama_ekspedisi'];
        $alamat = $data ['alamat'];
        $kota = $data ['kota'];

        $pdo = $this->connect()->return;

        $stmt = $pdo->prepare("update M_EKSPEDISI set 
            NAMA_EKSPEDISI = ?
            , KOTA = ?
            , ALAMAT = ?
        where KODE_EKSPEDISI = ?");

        $stmt->execute(array(
            $nama_ekspedisi,
            $kota,
            $alamat,
            $kode_ekspedisi
        ));

        $stmt = $pdo->prepare("select top 1 * from M_EKSPEDISI as me where me.kode_ekspedisi = ? order by me.KODE_EKSPEDISI desc");
        $stmt->execute(array($kode_ekspedisi));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function drop_ekspedisi_vehicle($kode_ekspedisi) {
        $pdo = $this->connect()->return;

        $stmt = $pdo->prepare("delete mev from M_EKPEDISI_VEHICLE_NEW mev where mev.KODE_EKSPEDISI = ?");

        $stmt->execute(array($kode_ekspedisi));
    }

    function update_ekspedisi_vehicle($data, $kode_ekspedisi) {
        // $kode_ekspedisi = $data['kode_ekspedisi'];
        $no_pol = $data ['no_pol'];
        $tipe_kendaraan = $data ['tipe_kendaraan'];
        $kuantitas_maksimal = $data ['kuantitas_maksimal'];
        $berat_maksimal = $data ['berat_maksimal'];
        $kode_farm = $data ['kode_farm'];
        $max_rit = $data ['max_rit'];

        foreach ($kode_farm as $key => $value) {
            $pdo = $this->connect()->return;

            $stmt = $pdo->prepare("update M_EKPEDISI_VEHICLE_NEW
                set TIPE_KENDARAAN = ?
                , MAX_KUANTITAS = ?
                , MAX_BERAT = ?
                , KODE_FARM = ?
                , MAX_RIT = ?
            where NO_KENDARAAN = ?");

            $stmt->execute(array(
                $tipe_kendaraan,
                $kuantitas_maksimal,
                $berat_maksimal,
                $value,
                $max_rit[$key],
                $no_pol
            ));
        }        

        $stmt = $pdo->prepare("select top 1 * from M_EKPEDISI_VEHICLE_NEW as mev where mev.NO_KENDARAAN = ?");
        $stmt->execute(array($no_pol));
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}