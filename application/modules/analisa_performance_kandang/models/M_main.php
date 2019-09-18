<?php

class M_main extends MY_Model {

    private $dbSqlServer;

    public function __construct() {
        parent::__construct();
        $this->dbSqlServer = $this->load->database('default', TRUE);
    }

    public function get_data_order_kandang($tanggal_kirim_awal = NULL, $tanggal_kirim_akhir = NULL, $kode_farm) {
        $query = <<<QUERY

       		EXEC ANALISA_PERFORMANCE_KANDANG 
			   '$tanggal_kirim_awal','$tanggal_kirim_akhir', '$kode_farm'

QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_max_tanggal_kebutuhan($kode_farm) {
        $query = <<<QUERY
			SELECT 
				REPLACE(CONVERT(VARCHAR(10),CAST(MAX(TGL_KEB_AKHIR) AS DATETIME),105),'-',' ') tgl_kirim 
				, REPLACE(CONVERT(VARCHAR(10),CAST(MAX(TGL_KEB_AKHIR) AS DATETIME) + 1,105),'-',' ') max_tgl_kebutuhan 
			FROM ORDER_KANDANG
			WHERE KODE_FARM = '$kode_farm'

QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_min_tanggal_doc_in($kode_farm) {
        $query = <<<QUERY

			SELECT 
				REPLACE(CONVERT(VARCHAR(10),CAST(MIN(TGL_DOC_IN) AS DATETIME),105),'-',' ') tgl_kirim 
				, REPLACE(CONVERT(VARCHAR(10),CAST(MIN(TGL_DOC_IN) AS DATETIME) + 1,105),'-',' ') max_tgl_kebutuhan 
			FROM KANDANG_SIKLUS
			WHERE KODE_FARM = '$kode_farm'
			AND STATUS_SIKLUS = 'O'

QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function group_daftar_barang($kode_farm = NULL, $tanggal_kebutuhan_awal = NULL, $tanggal_kebutuhan_akhir = NULL, $kode_barang = NULL) {
        $barang = (empty($kode_barang)) ? $this->get_daftar_barang($kode_farm, $tanggal_kebutuhan_awal, $tanggal_kebutuhan_akhir) : $this->get_tambah_daftar_barang($kode_barang, $kode_farm, $tanggal_kebutuhan_awal, $tanggal_kebutuhan_akhir);
        // echo $kode_farm.'='.$tanggal_kebutuhan_awal.'='.$tanggal_kebutuhan_akhir = NULL;
        $result = [];
        foreach ($barang as $key => $item) {
            $jumlah_kebutuhan = 0;
            $result [$item ['kode_barang']] = array(
                'nama_barang' => $item ['nama_barang'],
                'bentuk_barang' => $item ['bentuk_barang'],
                'sum_jml_kebutuhan_barang' => $item ['sum_jml_kebutuhan_barang'],
                'sum_jml_pp_barang' => $item ['sum_jml_pp_barang']
            );
        }
        foreach ($barang as $key => $item) {
            $jumlah_kebutuhan = 0;
            $result [$item ['kode_barang']] ['data_kandang'] [$item ['kode_kandang']] = array(
                'sum_hari_kandang' => $item ['sum_hari_kandang'],
                'no_reg' => $item ['no_reg'],
                'nama_kandang' => $item ['nama_kandang'],
                'populasi' => $item ['populasi'],
                'tgl_rhk_terakhir' => $item ['tgl_rhk_terakhir'],
                'range_umur' => $item ['range_umur'],
                'sum_jml_kebutuhan_kandang' => $item ['sum_jml_kebutuhan_kandang'],
                'ada' => $item ['ada'],
                'stok_kandang' => $item ['stok_kandang'],
                'sum_jml_pp_kandang' => $item ['sum_jml_pp_kandang']
            );
        }
        foreach ($barang as $key => $item) {
            $jumlah_kebutuhan = 0;
            $result [$item ['kode_barang']] ['data_kandang'] [$item ['kode_kandang']] ['data_tgl_kebutuhan'] [$item ['tgl_kebutuhan']] = array(
                'b_jml_kebutuhan' => $item ['b_jml_kebutuhan'],
                'j_jml_kebutuhan' => $item ['j_jml_kebutuhan'],
                'b_jml_pp' => $item ['b_jml_pp'],
                'j_jml_pp' => $item ['j_jml_pp']
            );
        }

        /*
         * $result = simpleGrouping($barang,'kode_barang');
         * foreach($result as $key=>$item){
         * $result[$key] = simpleGrouping($item,'no_reg');
         * }
         *
         * foreach($result as $key1=>$item1){
         * foreach($item1 as $key2=>$item2){
         * $result[$key1][$key2] = simpleGrouping($item2,'tgl_kebutuhan');
         * }
         * }
         */

        return $result;
    }

    public function group_daftar_barang_api($kode_farm = NULL, $tanggal_kebutuhan_awal = NULL, $tanggal_kebutuhan_akhir = NULL, $kode_barang = NULL) {
        $barang = (empty($kode_barang)) ? $this->get_daftar_barang($kode_farm, $tanggal_kebutuhan_awal, $tanggal_kebutuhan_akhir) : $this->get_tambah_daftar_barang($kode_barang, $kode_farm, $tanggal_kebutuhan_awal, $tanggal_kebutuhan_akhir);
        // echo $kode_farm.'='.$tanggal_kebutuhan_awal.'='.$tanggal_kebutuhan_akhir = NULL;
        $result = [];
        foreach ($barang as $key => $item) {
            $jumlah_kebutuhan = 0;
            $result ['_' . $item ['kode_barang']] = array(
                'kode_barang' => $item ['kode_barang'],
                'nama_barang' => $item ['nama_barang'],
                'bentuk_barang' => $item ['bentuk_barang'],
                'sum_jml_kebutuhan_barang' => $item ['sum_jml_kebutuhan_barang'],
                'sum_jml_pp_barang' => $item ['sum_jml_pp_barang']
            );
        }
        foreach ($barang as $key => $item) {
            $jumlah_kebutuhan = 0;
            $result ['_' . $item ['kode_barang']] ['data_kandang'] [$item ['kode_kandang']] = array(
                'kode_kandang' => $item ['kode_kandang'],
                'sum_hari_kandang' => $item ['sum_hari_kandang'],
                'no_reg' => $item ['no_reg'],
                'nama_kandang' => $item ['nama_kandang'],
                'populasi' => $item ['populasi'],
                'tgl_rhk_terakhir' => $item ['tgl_rhk_terakhir'],
                'range_umur' => $item ['range_umur'],
                'sum_jml_kebutuhan_kandang' => $item ['sum_jml_kebutuhan_kandang'],
                'ada' => $item ['ada'],
                'stok_kandang' => $item ['stok_kandang'],
                'sum_jml_pp_kandang' => $item ['sum_jml_pp_kandang']
            );
        }
        foreach ($barang as $key => $item) {
            $jumlah_kebutuhan = 0;
            $result ['_' . $item ['kode_barang']] ['data_kandang'] [$item ['kode_kandang']] ['data_tgl_kebutuhan'] ['_' . $item ['tgl_kebutuhan']] = array(
                'tgl_kebutuhan' => $item ['tgl_kebutuhan'],
                'b_jml_kebutuhan' => $item ['b_jml_kebutuhan'],
                'j_jml_kebutuhan' => $item ['j_jml_kebutuhan'],
                'b_jml_pp' => $item ['b_jml_pp'],
                'j_jml_pp' => $item ['j_jml_pp']
            );
        }

        /*
         * $result = simpleGrouping($barang,'kode_barang');
         * foreach($result as $key=>$item){
         * $result[$key] = simpleGrouping($item,'no_reg');
         * }
         *
         * foreach($result as $key1=>$item1){
         * foreach($item1 as $key2=>$item2){
         * $result[$key1][$key2] = simpleGrouping($item2,'tgl_kebutuhan');
         * }
         * }
         */

        return $result;
    }

    public function get_daftar_barang($kode_farm = NULL, $tanggal_kebutuhan_awal = NULL, $tanggal_kebutuhan_akhir = NULL) {
        $query = <<<QUERY


       		EXEC DAFTAR_BARANG_ANALISA_PERFORMANCE_KANDANG 
			   '$kode_farm',
			   '$tanggal_kebutuhan_awal',
			   '$tanggal_kebutuhan_akhir'

QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tambah_daftar_barang($kode_barang = NULL, $kode_farm = NULL, $tanggal_kebutuhan_awal = NULL, $tanggal_kebutuhan_akhir = NULL) {
        $query = <<<QUERY


       		EXEC TAMBAH_BARANG_ANALISA_PERFORMANCE_KANDANG 
			   '$kode_barang',
			   '$kode_farm',
			   '$tanggal_kebutuhan_awal',
			   '$tanggal_kebutuhan_akhir'

QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tambah_barang($kode_farm) {
        $query = <<<QUERY
			SELECT DISTINCT
				M.KODE_BARANG kode_barang
				, MB.NAMA_BARANG nama_barang
				, MGB.DESKRIPSI grup_barang
				, dbo.BENTUK_CONVERTION(MB.BENTUK_BARANG) tipe_barang
			FROM MOVEMENT M
			JOIN MOVEMENT_D MD ON MD.KODE_FARM = M.KODE_FARM
				AND MD.KODE_BARANG = M.KODE_BARANG
				AND MD.NO_PALLET = M.NO_PALLET
				AND	MD.NO_KAVLING = M.NO_KAVLING
			JOIN M_BARANG MB ON MB.KODE_BARANG = M.KODE_BARANG
			JOIN M_GRUP_BARANG MGB ON MGB.GRUP_BARANG = MB.GRUP_BARANG
			WHERE M.JML_ON_HAND > 0
			OR MD.JML_ON_PUTAWAY > 0
			AND M.KODE_FARM = '$kode_farm'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cek_order_kandang($data) {
        $kode_farm = $data ['kode_farm'];
        $tgl_kirim = $data ['tanggal_kirim'];
        $tgl_kirim = date('Y-m-d', strtotime(convert_month($tgl_kirim, 2)));
        $tgl_kebutuhan_awal = $data ['tanggal_kebutuhan_awal'];
        $tgl_kebutuhan_awal = date('Y-m-d', strtotime(convert_month($tgl_kebutuhan_awal, 2)));
        $tgl_kebutuhan_akhir = $data ['tanggal_kebutuhan_akhir'];
        $tgl_kebutuhan_akhir = date('Y-m-d', strtotime(convert_month($tgl_kebutuhan_akhir, 2)));
        $query = <<<QUERY

			SELECT
				COUNT(*) ada
				, NO_ORDER no_order
			from ORDER_KANDANG
			where KODE_FARM = '$kode_farm'
			and TGL_KIRIM = '$tgl_kirim'
			and TGL_KEB_AWAL = '$tgl_kebutuhan_awal'
			and TGL_KEB_AKHIR = '$tgl_kebutuhan_akhir'
			GROUP BY NO_ORDER
QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_analisa_performance_kandang($data) {
        $this->dbSqlServer->conn_id->beginTransaction();

        $data_order_kandang_e = $data ['data_order_kandang_e'];

        $result_oke = 0;
        foreach ($data_order_kandang_e as $key => $value) {
            $result = $this->update_order_kandang_e($value);
            $result_oke = $result_oke + $result ['result'];
        }

        if ($result_oke == count($data_order_kandang_e)) {
            $this->dbSqlServer->conn_id->commit();
            $return = 1;
        } else {
            $this->dbSqlServer->conn_id->rollback();
            $return = 0;
        }

        return $return;
    }

    public function simpan_analisa_performance_kandang($data, $user) {
        $this->dbSqlServer->conn_id->beginTransaction();

        $data_order_kandang = $data ['data_order_kandang'];
        $data_order_kandang_d = $data ['data_order_kandang_d'];
        $data_order_kandang_e = $data ['data_order_kandang_e'];

        $result_ok = $this->insert_order_kandang($data_order_kandang [0], $user);

        $result_okd = 0;
        $result_oke = 0;

        if ($result_ok ['result'] == 1) {

            $return ['no_order'] = $result_ok ['no_order'];

            foreach ($data_order_kandang_d as $key => $value) {
                $result = $this->insert_order_kandang_d($value, $result_ok ['no_order'], $result_ok ['kode_farm']);
                $result_okd = $result_okd + $result ['result'];
            }

            // echo $result_okd .'=='. count($data_order_kandang_d);

            if ($result_okd == count($data_order_kandang_d)) {
                foreach ($data_order_kandang_e as $key => $value) {
                    $result = $this->insert_order_kandang_e($value, $result_ok ['no_order'], $result_ok ['kode_farm']);
                    $result_oke = $result_oke + $result ['result'];
                }
                // echo $result_oke .'=='. count($data_order_kandang_e);
            }
        }

        if ($result_oke == count($data_order_kandang_e)) {
            $this->dbSqlServer->conn_id->commit();
            $return ['success'] = 1;
        } else {
            $this->dbSqlServer->conn_id->rollback();
            $return ['success'] = 0;
        }

        return $return;
    }

    public function insert_order_kandang($data, $user) {
        $kode_farm = $data ['kode_farm'];
        $tgl_kirim = $data ['tanggal_kirim'];
        $tgl_kirim = date('Y-m-d', strtotime(convert_month($tgl_kirim, 2)));
        $tgl_kebutuhan_awal = $data ['tanggal_kebutuhan_awal'];
        $tgl_kebutuhan_awal = date('Y-m-d', strtotime(convert_month($tgl_kebutuhan_awal, 2)));
        $tgl_kebutuhan_akhir = $data ['tanggal_kebutuhan_akhir'];
        $tgl_kebutuhan_akhir = date('Y-m-d', strtotime(convert_month($tgl_kebutuhan_akhir, 2)));
        $status_order = 'D';
        // $user = 'SADAM';
        $query = <<<QUERY

       		EXEC SIMPAN_ORDER_KANDANG 
       			'$kode_farm',
       			'$tgl_kirim',
       			'$tgl_kebutuhan_awal',
       			'$tgl_kebutuhan_akhir',
       			'$status_order',
       			'$user'

QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_order_kandang_d($data, $no_order, $kode_farm) {
        $no_reg = $data ['no_reg'];
        $tgl_lhk = $data ['tgl_lhk'];
        $tgl_lhk = (empty($tgl_lhk)) ? '' : date('Y-m-d', strtotime(convert_month($tgl_lhk, 2)));
        $umur = $data ['umur'];
        $query = <<<QUERY

       		EXEC SIMPAN_ORDER_KANDANG_D
				'$kode_farm',
				'$no_order',
				'$no_reg',
				'$tgl_lhk',
				$umur


QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_order_kandang_e($data, $no_order, $kode_farm) {
        $no_reg = $data ['no_reg'];
        $kode_barang = $data ['kode_barang'];
        $tgl_kebutuhan = $data ['tgl_kebutuhan'];
        $jenis_kelamin = $data ['jenis_kelamin'];
        $jml_performance = $data ['jml_performance'];
        $jml_pp = $data ['jml_pp'];
        $query = <<<QUERY

			EXEC SIMPAN_ORDER_KANDANG_E
				'$kode_farm',
				'$no_order',
				'$no_reg',
				'$kode_barang',
				'$tgl_kebutuhan',
				'$jenis_kelamin',
				NULL,
				$jml_performance,
				$jml_pp


QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update_order_kandang_e($data) {
        $kode_farm = $data ['kode_farm'];
        $no_order = $data ['no_order'];
        $no_reg = $data ['no_reg'];
        $kode_barang = $data ['kode_barang'];
        $tgl_kebutuhan = $data ['tgl_kebutuhan'];
        $jenis_kelamin = $data ['jenis_kelamin'];
        $jml_performance = $data ['jml_performance'];
        $jml_pp = $data ['jml_pp'];
        $keterangan = $data ['keterangan'];
        $query = <<<QUERY

			EXEC UPDATE_ORDER_KANDANG_E
				'$kode_farm',
				'$no_order',
				'$no_reg',
				'$kode_barang',
				'$tgl_kebutuhan',
				'$jenis_kelamin',
				NULL,
				$jml_performance,
				$jml_pp,
				'$keterangan'


QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function release_analisa_performance_kandang($alldata, $user) {
        $this->dbSqlServer->conn_id->beginTransaction();

        $data = $alldata ['data'];

        $result = 0;
        $lot_not_available = 0;
        foreach ($data as $key => $value) {
            $r = $this->simpan_release($value, $user);
            $result = $result + $r ['result'];
            if ($r ['result'] == 2) {
                $lot_not_available = $lot_not_available + 1;
            }
        }

        if ($result == count($data)) {
            $this->dbSqlServer->conn_id->commit();
            $return = ($lot_not_available > 0) ? 2 : 1;
        } else {
            $this->dbSqlServer->conn_id->rollback();
            $return = ($lot_not_available > 0) ? 2 : 0;
        }

        return $return;
    }

    public function simpan_release($data, $user) {
        $kode_farm = $data ['kode_farm'];
        $kode_barang = $data ['kode_barang'];
        $no_order = $data ['no_order'];
        $jml_pp = $data ['sum_jumlah_pp'];
        // $user = 'ADMIN';
        $keterangan1 = 'PICK';
        $keterangan2 = $data ['no_reg'];
        $no_reg = $data ['no_reg'];
        $jenis_kelamin = $data ['jenis_kelamin'];
        $query = <<<QUERY

			EXEC RELEASE_ANALISA_PERFORMANCE_KANDANG_BARU
				'$kode_farm',
				'$kode_barang',
				'$no_order',
				$jml_pp,
				'$user',
				'$keterangan1',
				'$keterangan2',
				'$no_reg',
				'$jenis_kelamin'


QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
