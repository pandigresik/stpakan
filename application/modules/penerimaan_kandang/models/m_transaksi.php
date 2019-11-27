<?php

class M_transaksi extends MY_Model {

    private $dbSqlServer;

    public function __construct() {
        parent::__construct();
        $this->dbSqlServer = $this->load->database('default', TRUE);
    }

    public function get_data_order_kandang($tanggal_kirim_awal = NULL, $tanggal_kirim_akhir = NULL, $kode_farm) {
        $tgl_where = (!empty($tanggal_kirim_awal) && (!empty($tanggal_kirim_akhir))) ? "and ld.TGL_KIRIM BETWEEN '$tanggal_kirim_awal' AND '$tanggal_kirim_akhir'" : "";
        $query = <<<QUERY
			--EXEC PICK_HEADER '$tanggal_kirim_awal', '$tanggal_kirim_akhir', '$kode_farm'

			SELECT 
				ok.kode_farm
				, ok.no_order
				, ok.tgl_kirim_old
				, ok.tgl_kirim
				, ok.tgl_keb_awal
				, ok.tgl_keb_akhir
				, ok.jumlah_kebutuhan
				, (ok.jumlah_kebutuhan-isnull(SUM(pkd.JML_TERIMA),0)) jumlah_belum_proses 
				, ok.status_order
			FROM (
				SELECT
					ok.KODE_FARM kode_farm
					, ok.NO_ORDER no_order
	                , REPLACE(CONVERT(VARCHAR(10),ok.TGL_KIRIM,105),'-',' ') tgl_kirim_old
					, REPLACE(CONVERT(VARCHAR(10),(CAST(ok.TGL_KEB_AWAL AS DATETIME) - 1),105),'-',' ') tgl_kirim
	                , REPLACE(CONVERT(VARCHAR(10),ok.TGL_KEB_AWAL,105),'-',' ') tgl_keb_awal
					, REPLACE(CONVERT(VARCHAR(10),ok.TGL_KEB_AKHIR,105),'-',' ') tgl_keb_akhir
					, ISNULL(SUM(oke.JML_ORDER),0) jumlah_kebutuhan
					, ok.STATUS_ORDER status_order
				FROM LPB l
				JOIN LPB_D ld ON l.KODE_FARM = ld.KODE_FARM AND l.NO_LPB = ld.NO_LPB
				JOIN LPB_E le ON le.KODE_FARM = ld.KODE_FARM AND le.NO_LPB = ld.NO_LPB AND le.TGL_KIRIM = ld.TGL_KIRIM
				LEFT JOIN ORDER_KANDANG ok ON ok.KODE_FARM = ld.KODE_FARM and ok.TGL_KEB_AWAL = ld.TGL_KEB_AWAL and ok.TGL_KEB_AKHIR = ld.TGL_KEB_AKHIR
				LEFT JOIN ORDER_KANDANG_D okd ON ok.KODE_FARM = okd.KODE_FARM AND ok.NO_ORDER = okd.NO_ORDER AND okd.NO_REG = le.NO_REG
				LEFT JOIN ORDER_KANDANG_E oke ON oke.KODE_BARANG = le.KODE_BARANG AND oke.NO_REG = le.NO_REG AND oke.JENIS_KELAMIN = le.JENIS_KELAMIN AND oke.TGL_KEBUTUHAN = le.TGL_KEBUTUHAN
				WHERE ld.KODE_FARM = '$kode_farm'
				$tgl_where
				AND l.TGL_APPROVE1 IS NOT NULL
				GROUP BY ok.KODE_FARM
						, ok.NO_ORDER
						, ok.TGL_KIRIM
						, ok.TGL_KEB_AWAL
						, ok.TGL_KEB_AKHIR
						, ok.STATUS_ORDER
				) ok
				left join PENERIMAAN_KANDANG pk on pk.NO_ORDER = ok.no_order and ok.kode_farm = substring(pk.NO_REG,1,CHARINDEX('-',pk.NO_REG) - 1)
				left join PENERIMAAN_KANDANG_D pkd on pkd.NO_PENERIMAAN_KANDANG = pk.NO_PENERIMAAN_KANDANG and pk.NO_REG = pkd.NO_REG and pkd.JML_TERIMA > 0
				WHERE ok.status_order != 'N'
				group by 
					ok.kode_farm
					, ok.no_order
					, ok.tgl_kirim_old
					, ok.tgl_kirim
					, ok.tgl_keb_awal
					, ok.tgl_keb_akhir
					, ok.jumlah_kebutuhan
					, ok.status_order

QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_data_detail_order_kandang($no_order, $kode_farm) {
        $query = <<<QUERY
       		SELECT 
				PK.NO_REG no_reg
				, PK.NO_ORDER no_order
				, KS.KODE_FARM kode_farm
				, MF.NAMA_FARM farm
				, REPLACE(CONVERT(VARCHAR(10),OK.TGL_KIRIM,105),'-',' ') tgl_kirim
				, REPLACE(CONVERT(VARCHAR(10),OK.TGL_KEB_AWAL,105),'-',' ') tgl_keb_awal
				, REPLACE(CONVERT(VARCHAR(10),OK.TGL_KEB_AKHIR,105),'-',' ') tgl_keb_akhir
				, PK.NO_PENERIMAAN_KANDANG no_penerimaan_kandang
				, PK.NO_ORDER no_order
				, KS.KODE_KANDANG kode_kandang
				, PKD.JENIS_KELAMIN kode_jenis_kelamin
				, CASE 
					WHEN PKD.JENIS_KELAMIN = 'J' then 'JANTAN'
					WHEN PKD.JENIS_KELAMIN = 'B' then 'BETINA'
					ELSE '-'
					END jenis_kelamin
				, PKD.KODE_BARANG kode_barang
				, MB.NAMA_BARANG nama_barang
				--, abs(PKD.JML_TERIMA) tmp_jumlah
				--, PKD.JML_TERIMA jumlah
				, abs(PKD.JML_TERIMA) jumlah
				, PKD.JML_TERIMA tmp_jumlah
				, CASE 
					WHEN MB.BENTUK_BARANG = 'T' THEN 'TEPUNG'
					WHEN MB.BENTUK_BARANG = 'C' THEN 'CRUMBLE'
					WHEN MB.BENTUK_BARANG = 'P' THEN 'PALLET'
					WHEN MB.BENTUK_BARANG = 'A' THEN 'CAIR'
					ELSE ''
				END bentuk_pakan
				--, CASE 
				--	WHEN PKD.JML_TERIMA > 0 THEN 1
				--	ELSE 0
				--END remark
				, CASE 
					WHEN MP1.NAMA_PEGAWAI IS NOT NULL THEN 1
					ELSE 0
				END remark
				, MP1.NAMA_PEGAWAI user_gudang
				, MP2.NAMA_PEGAWAI user_buat
				, REPLACE(CONVERT(VARCHAR(10),KMD.TGL_BUAT,105),'-',' ') tgl_buat
				, KMD.TGL_BUAT waktu_buat
			FROM PENERIMAAN_KANDANG PK
			JOIN PENERIMAAN_KANDANG_D PKD ON PKD.NO_PENERIMAAN_KANDANG = PK.NO_PENERIMAAN_KANDANG AND PK.NO_REG = PKD.NO_REG
			LEFT JOIN M_PEGAWAI MP1 ON MP1.KODE_PEGAWAI = PKD.USER_GUDANG
			LEFT JOIN KANDANG_MOVEMENT_D KMD 
				ON KMD.NO_REG = PKD.NO_REG
				AND KMD.KODE_BARANG = PKD.KODE_BARANG
				AND KMD.JENIS_KELAMIN = PKD.JENIS_KELAMIN
				--AND KMD.JML_ORDER = PKD.JML_TERIMA
				AND KMD.KETERANGAN2 = PKD.NO_PENERIMAAN_KANDANG
				AND KMD.KETERANGAN1 = 'PENERIMAAN KANDANG'
			LEFT JOIN M_PEGAWAI MP2 ON MP2.KODE_PEGAWAI = KMD.USER_BUAT
			JOIN M_BARANG MB ON MB.KODE_BARANG = PKD.KODE_BARANG
			JOIN KANDANG_SIKLUS KS ON KS.NO_REG = PK.NO_REG
			JOIN M_FARM MF ON MF.KODE_FARM = KS.KODE_FARM
			JOIN ORDER_KANDANG OK ON OK.NO_ORDER = PK.NO_ORDER AND OK.KODE_FARM = KS.KODE_FARM
			WHERE PK.NO_ORDER = :no_order
			AND KS.KODE_FARM = :kode_farm
            order by PK.NO_REG ASC, PKD.JENIS_KELAMIN ASC
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->bindParam(':no_order', $no_order);
        $stmt->bindParam(':kode_farm', $kode_farm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cek_verifikasi_rfid_card($data) {
        $kode_kandang = $data ['kode_kandang'];
        $kode_farm = $data ['kode_farm'];
        $rfid_card = $data ['rfid_card'];
        $query = <<<QUERY
       		SELECT
				* 
			FROM M_KANDANG 
			WHERE KODE_FARM = :kode_farm
			AND KODE_KANDANG = :kode_kandang 
			AND KODE_VERIFIKASI = :rfid_card
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->bindParam(':kode_farm', $kode_farm);
        $stmt->bindParam(':kode_kandang', $kode_kandang);
        $stmt->bindParam(':rfid_card', $rfid_card);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

	public function get_user_gudang($kode_farm) {
        $query = <<<QUERY
       		SELECT
				MP.KODE_PEGAWAI kode_pegawai
				, MP.NAMA_PEGAWAI nama_pegawai
			FROM M_PEGAWAI MP 
			JOIN PEGAWAI_D PD ON PD.KODE_PEGAWAI = MP.KODE_PEGAWAI AND PD.KODE_FARM = '$kode_farm' AND MP.STATUS_PEGAWAI = 'A' AND GRUP_PEGAWAI = 'AG'
			ORDER BY NAMA_PEGAWAI ASC
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function simpan_konfirmasi($data, $user) {
        $final_result = 0;
        $cnt = 0;

        $this->dbSqlServer->conn_id->beginTransaction();

        foreach ($data as $key => $value) {

            $kode_farm = $value ['kode_farm'];
            $no_reg = $value ['no_reg'];
            $no_penerimaan_kandang = $value ['no_penerimaan_kandang'];
            $kode_barang = $value ['kode_barang'];
            $no_order = $value ['no_order'];
            $keterangan1 = 'PENERIMAAN KANDANG';
            $user_buat = $user;
            $jenis_kelamin = $value ['jenis_kelamin'];
            $user_gudang = $value ['user_gudang'];
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
            // echo $query;
            $stmt = $this->dbSqlServer->conn_id->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $final_result = $final_result + $result ['RESULT'];
            $cnt++;
        }

        if ($final_result == $cnt) {
            $this->dbSqlServer->conn_id->commit();
            $final_result = 1;
        } else {
            $this->dbSqlServer->conn_id->rollback();
            $final_result = 0;
        }

        return $final_result;
    }

}