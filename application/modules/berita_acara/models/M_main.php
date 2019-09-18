<?php

class M_main extends MY_Model {

    private $dbSqlServer;

    public function __construct() {
        parent::__construct();
        $this->dbSqlServer = $this->load->database('default', TRUE);
    }

    public function generate_no_ba($kode_farm) {
        $query = <<<QUERY
       		SELECT 
				ISNULL(RIGHT('00000000'+ISNULL(CAST(MAX(NO_BA)+1 AS VARCHAR(8)),'1'),8),'00000001') no_ba
			FROM BERITA_ACARA
			WHERE KODE_FARM = '$kode_farm'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ['no_ba'];
    }

    public function list_berita_acara($kode_farm) {
        $query = <<<QUERY
       		SELECT 
				ba.* 
				, CASE 
					WHEN ba.TIPE_BA = 'R' THEN 'Rusak'
					WHEN ba.TIPE_BA = 'K' THEN 'Kurang'
					ELSE ''
				END TIPE_BA_LABEL
				, p.KODE_SURAT_JALAN kode_surat_jalan
			FROM BERITA_ACARA ba
			JOIN PENERIMAAN p ON p.NO_PENERIMAAN = ba.NO_PENERIMAAN
			WHERE ba.KODE_FARM = '$kode_farm'
			AND p.STATUS_TERIMA = 'C'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function list_surat_jalan($kode_farm) {
        $query = <<<QUERY
       		SELECT
				p.NO_PENERIMAAN no_penerimaan
				, p.KODE_SURAT_JALAN kode_surat_jalan
				, p.NO_OP no_op
				, SUM(pd.JML_SJ) jml_sj
				, SUM(pd.JML_TERIMA) + SUM(pd.JML_RUSAK) jml_aktual
			FROM PENERIMAAN p
			JOIN PENERIMAAN_D pd ON pd.KODE_FARM = p.KODE_FARM AND p.NO_PENERIMAAN = pd.NO_PENERIMAAN
			WHERE p.KODE_FARM = '$kode_farm'
			AND p.STATUS_TERIMA = 'C'
			GROUP BY p.NO_PENERIMAAN, p.NO_OP, p.KODE_SURAT_JALAN
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function simpan($kode_farm, $no_sj, /* $no_ba, */ $tipe_ba, $keterangan1, $user) {
        $query = <<<QUERY
       		EXEC SIMPAN_BERITA_ACARA 
				'$kode_farm',
				'$no_sj',
				'$tipe_ba',
				'$keterangan1',
				'$user'
QUERY;
        // echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function get_data($kode_farm, $no_sj, $tipe_ba) {
        $tipe_where = ($tipe_ba == 'K') ? "AND pd.JML_KURANG > 0" : "AND pd.JML_RUSAK > 0";
        $query = <<<QUERY
       		SELECT DISTINCT
				p.NO_PENERIMAAN no_penerimaan
				, REPLACE(CONVERT(VARCHAR(10),ba.TGL_BUAT,105),'-',' ') tgl_buat
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
				, pd.JML_RUSAK jml_rusak
				, pd.JML_KURANG jml_kurang
				, ba.KETERANGAN1 keterangan
				, ba.TIPE_BA tipe_ba
			FROM PENERIMAAN p 
			JOIN PENERIMAAN_D pd ON p.NO_PENERIMAAN = pd.NO_PENERIMAAN AND p.KODE_FARM = pd.KODE_FARM
			JOIN M_BARANG MB ON MB.KODE_BARANG = pd.KODE_BARANG
			JOIN M_FARM MF ON MF.KODE_FARM = p.KODE_FARM
			LEFT JOIN BERITA_ACARA ba on ba.NO_PENERIMAAN = p.NO_PENERIMAAN
			WHERE p.KODE_SURAT_JALAN = '$no_sj'
			AND p.KODE_FARM = '$kode_farm'
			$tipe_where
QUERY;
        #echo $query;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}