<?php
class M_review_pakan_rusak extends CI_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
	//	$this->_table = 'lpb';
	}
	public function header_pakan_rusak($kodefarm, $tindak_lanjut, $startDate, $endDate, $no_retur, $kandang) {
		$rpr = '';
		$mk = '';
		if($tindak_lanjut == 1){
			$rpr .= " AND rppr.WAKTU IS NULL";
		}
		if($tindak_lanjut == 0){
			$startDate = date('Y-m-d',strtotime($startDate));
			$endDate = date('Y-m-d',strtotime($endDate));
			$rpr .= " AND CAST(rpr.TGL_BUAT AS DATE) BETWEEN '$startDate' AND '$endDate'";
		}
		if(!empty($no_retur)){
			$rpr .= " AND 'RP/'+rpr.NO_REG+'-'+rpr.NO_URUT LIKE '%$no_retur%'";
		}
		if(!empty($kandang)){
			$mk .= " AND mk.NAMA_KANDANG LIKE '%$kandang%'";
		}
        $query = <<<QUERY
       		SELECT DISTINCT
				'RP/'+rpr.NO_REG+'-'+rpr.NO_URUT no_retur
				, rpr.NO_REG no_reg
				, rpr.NO_URUT no_urut
				, mk.NAMA_KANDANG kandang
				, rpr.TGL_BUAT tgl_retur
				, mp2.NAMA_PEGAWAI diserahkan_oleh
				, mp1.NAMA_PEGAWAI penerima
				, rppr.WAKTU wkt_review
				, CASE
					WHEN rppr.KEPUTUSAN = 'A' THEN 'Approved'
					WHEN rppr.KEPUTUSAN = 'R' THEN 'Reject'+' ('+rppr.ALASAN+')'
					ELSE ''
				END keputusan
				, rpr.ATTACHMENT attachment
				, 'RP/'+rpr.NO_REG+'-'+rpr.NO_URUT+'.doc' attachment_name
			FROM REVIEW_PENGGANTIAN_PAKAN_RUSAK rppr
			JOIN RETUR_PAKAN_RUSAK rpr
				ON rppr.RETUR_PAKAN_RUSAK = rpr.ID
				$rpr
			JOIN KANDANG_SIKLUS ks
				ON ks.NO_REG = rpr.NO_REG
				AND ks.KODE_FARM = '$kodefarm'
			JOIN M_KANDANG mk
				ON mk.KODE_KANDANG = ks.KODE_KANDANG
				AND mk.KODE_FARM = ks.KODE_FARM
				$mk
			JOIN M_PEGAWAI mp1
				ON mp1.KODE_PEGAWAI = rpr.USER_BUAT
			JOIN M_PEGAWAI mp2
				ON mp2.KODE_PEGAWAI = rpr.USER_VERIFIKASI
			
QUERY;
		#echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	public function detail_pakan_rusak($kodefarm, $no_reg, $no_urut){
		$no_retur = $no_reg.'-'.$no_urut;
        $query = <<<QUERY
       		SELECT 
				mb.NAMA_BARANG nama_pakan
				, rpri.JENIS_KELAMIN jenis_kelamin
				, 1 jml_retur --rpri.JML_RETUR jml_retur
				, rprit.BRT_SAK berat
			FROM RETUR_PAKAN_RUSAK rpr
			JOIN RETUR_PAKAN_RUSAK_ITEM rpri
				ON rpr.ID = rpri.RETUR_PAKAN_RUSAK
				AND rpr.NO_REG+'-'+rpr.NO_URUT = '$no_retur'
			JOIN RETUR_PAKAN_RUSAK_ITEM_TIMBANG rprit
				ON rpri.ID = rprit.RETUR_PAKAN_RUSAK_ITEM
			JOIN M_BARANG mb
				ON mb.KODE_BARANG = rpri.KODE_PAKAN
QUERY;
		#echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function simpan_review_pakan_rusak($kode_farm, $user, $no_reg, $no_urut, $transaksi, $alasan){
        $query = <<<QUERY
			GENERATE_PENGAMBILAN_PAKAN_RUSAK
				'$kode_farm',
				'$no_reg',
				'$no_urut',
				'$user',
				'$transaksi',
				'$alasan'
QUERY;
		#echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	public function simpan_review_pakan_rusak_bdy($kode_farm, $user, $no_reg, $no_urut, $transaksi, $alasan){
        $this->update_review_pakan_rusak_bdy($kode_farm, $user, $no_reg, $no_urut, $transaksi, $alasan);
        return $this->get_data_review_pakan_rusak_bdy($no_reg, $no_urut);
	}
	public function update_review_pakan_rusak_bdy($kode_farm, $user, $no_reg, $no_urut, $transaksi, $alasan){
        $query = <<<QUERY
			
			UPDATE REVIEW_PENGGANTIAN_PAKAN_RUSAK
			SET WAKTU = GETDATE()
				, REVIEWER = '$user'
				, KEPUTUSAN = '$transaksi'
				, ALASAN = '$alasan'
			WHERE RETUR_PAKAN_RUSAK = (
				SELECT 
					ID 
				FROM RETUR_PAKAN_RUSAK rpr
				WHERE rpr.NO_REG = '$no_reg'
				AND rpr.NO_URUT = '$no_urut'
			)
QUERY;
		#echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	public function get_data_review_pakan_rusak_bdy($no_reg, $no_urut){
        $query = <<<QUERY
			
			SELECT 
				case
					when KEPUTUSAN is null or KEPUTUSAN = '' then 0
					else 1
				end result
				, CAST(WAKTU AS DATE) tgl_review
				, LEFT(CAST(WAKTU AS TIME),5) wkt_review
			FROM REVIEW_PENGGANTIAN_PAKAN_RUSAK
			WHERE RETUR_PAKAN_RUSAK = (
				SELECT 
					ID 
				FROM RETUR_PAKAN_RUSAK rpr
				WHERE rpr.NO_REG = '$no_reg'
				AND rpr.NO_URUT = '$no_urut'
			)
QUERY;
		#echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	public function simpan_review_pakan_rusak_bdy_backup($kode_farm, $user, $no_reg, $no_urut, $transaksi, $alasan){
        $query = <<<QUERY
			GENERATE_PICKING_LIST_PAKAN_RUSAK
				'$kode_farm',
				'$no_reg',
				'$no_urut',
				'$user',
				'$transaksi',
				'$alasan'
QUERY;
		#echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
    public function data_retur($no_retur){
        $query = <<<QUERY
        	SELECT 
				* 
			FROM RETUR_PAKAN_RUSAK rpr
			WHERE rpr.NO_REG+'-'+rpr.NO_URUT = '$no_retur'
QUERY;
		#echo $query;
        $stmt  = $this->db->conn_id->prepare($query);
        $stmt->execute();
        #$stmt->bindColumn(1, $type, PDO::PARAM_STR, 256);
        #$stmt->bindColumn(2, $lob, PDO::PARAM_LOB, 0, PDO::SQLSRV_ENCODING_BINARY);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}