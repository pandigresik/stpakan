<?php
class M_ksg extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->helper ( 'stpakan' );
		$this->dbSqlServer = $this->load->database('default', TRUE);
		$level_user = $this->session->userdata('level_user');
	}

    public function getStokGlangsingData($kode_farm = '', $req_status = '', $level_user_db = '')
    {
		$sql = <<<QUERY
			EXEC dbo.GET_GLANGSING_AKHIR_SIKLUS '{$kode_farm}', '{$req_status}', '', '{$level_user_db}'
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getDetailPermintaanSak($kode_siklus, $saldo)
    {
		$sql = <<<QUERY
			EXEC dbo.get_permintaan_sak '{$kode_siklus}', '{$saldo}'
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function getNoUrutSgas($kodeSiklus = '')
	{
		$sql = <<<QUERY
			select top 1 * from STOK_GLANGSING_AKHIR_SIKLUS where KODE_SIKLUS = $kodeSiklus order by no_urut desc
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	public function getNoUrutLsgas($kodeSiklus = '',$no_urut = 0)
	{
		$sql = <<<QUERY
			select * from LOG_STOK_GLANGSING_AKHIR_SIKLUS where KODE_SIKLUS = $kodeSiklus and NO_URUT = $no_urut order by no_urut_approve desc
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	public function doUpdateKsg($data = null, $nextStatus = '', $keterangan_reject = ''){
		$error = false;

		$message = array(
			'success' => array(
				'D' => 'Laporan Stok Glangsing Akhir Siklus berhasil disimpan sebagai draft',
				'N' => 'Laporan Stok Glangsing Akhir Siklus berhasil dirilis',
				'R1' => 'Laporan Stok Glangsing Akhir Siklus berhasil di-Review',
				'R2' => 'Laporan Stok Glangsing Akhir Siklus berhasil di-Review',
				'RJ'=> 'Laporan Stok Glangsing Akhir Siklus berhasil di-Reject',
				'A' => 'Laporan Stok Glangsing Akhir Siklus berhasil di-Approve',
				'V' => 'Laporan Stok Glangsing Akhir Siklus berhasil dihapus/dibatalkan',
			),
			'error' => array(
				'D' => 'Laporan Stok Glangsing Akhir Siklus gagal diubah',
				'N' => 'Laporan Stok Glangsing Akhir Siklus gagal dirilis',
				'R1' => 'Laporan Stok Glangsing Akhir Siklus gagal di-Review',
				'R2' => 'Laporan Stok Glangsing Akhir Siklus gagal di-Review',
				'RJ' => 'Laporan Stok Glangsing Akhir Siklus gagal di-Reject',
				'A' => 'Laporan Stok Glangsing Akhir Siklus gagal di-Approve',
				'V' => 'Laporan Stok Glangsing Akhir Siklus gagal dihapus/dibatalkan'
			)
		);
		if (count($data) > 0) {
			// cetak_r($data);
			foreach ($data as $key => $value) {
				$no_urut = $this->getNoUrutSgas($value['kodeSiklus']);
				if($no_urut == null){
					$no_urut = 1;
				}else {
					$no_urut = $no_urut['NO_URUT'] + 1;
				}
				//$no_urut_approve = $this->getNoUrutLsgas($value['kodeSiklus'], $no_urut);

				//$no_urut_sgas 	 = isset($this->getNoUrutSgas($kodeSiklus)) ? 'g' : '1';
				if ($nextStatus == 'N') {
					$arr_ksg = array(
						'KODE_SIKLUS' 			=> $value['kodeSiklus'],
						'NO_URUT' 				=> $no_urut,
						'JML_TERIMA_PAKAN' 		=> 1,
						'JML_PAKAI_PAKAN' 		=> 1,
						'SAK_AWAL' 				=> 1,
						'SAK_TERIMA' 			=> 1,
						'SAK_PAKAI_INTERN' 		=> 1,
						'SAK_PAKAI_EKSTERN' 	=> 1,
						'SAK_DIJUAL' 			=> 1,
						'SAK_SISA' 				=> 1,
						'PENGAJUAN_SAK_DIJUAL' 	=> $value['jml_pengajuan'],
						'PENGAJUAN_HARGA_SAK' 	=> $value['harga_pengajuan']
					);

					$this->dbSqlServer->insert("STOK_GLANGSING_AKHIR_SIKLUS",$arr_ksg);
					if($this->dbSqlServer->affected_rows() < 0){
						$error++;
					}
					$arr_log_ksg = array(
						'KODE_SIKLUS'	  => $value['kodeSiklus'],
						'NO_URUT'		  => $no_urut,
						'NO_URUT_APPROVE' => 1,
						'STATUS'		  => $nextStatus,
						'KETERANGAN'	  => $keterangan_reject,
						'USER_BUAT'		  => $this->session->userdata('kode_user'),
						// 'TGL_BUAT'		  => $tgl_buat['today']
					);
					$this->dbSqlServer->insert("LOG_STOK_GLANGSING_AKHIR_SIKLUS",$arr_log_ksg);

					// harga_d
					// foreach ($value['harga_d'] as $key => $data_d) {
					// 	$arr_log_ksg = array(
					// 		'KODE_SIKLUS' => $value['kodeSiklus'],
					// 		'NO_URUT'	  => $no_urut,
					// 		'KODE_BUDGET' => $data_d['kodeBudget'],
					// 		'HARGA'		  => $data_d['value']
					// 	);
					// 	$this->dbSqlServer->insert("HARGA_JUAL_GLANGSING",$arr_log_ksg);
					//
					// 	if($this->dbSqlServer->affected_rows() < 0){
					// 		$error++;
					// 	}
					// }
				}
				else {
					$no_urut = $this->getNoUrutSgas($value['kodeSiklus']);
					$no_urut_approve = $this->getNoUrutLsgas($value['kodeSiklus'], $no_urut['NO_URUT']);
					$arr_log_ksg = array(
						'KODE_SIKLUS'	  => $value['kodeSiklus'],
						'NO_URUT'		  => $no_urut['NO_URUT'],
						'NO_URUT_APPROVE' => $no_urut_approve['NO_URUT_APPROVE']+1,
						'STATUS'		  => $nextStatus,
						'KETERANGAN'	  => $keterangan_reject,
						'USER_BUAT'		  => $this->session->userdata('kode_user'),
						// 'TGL_BUAT'		  => $tgl_buat['today']
					);
					$this->dbSqlServer->insert("LOG_STOK_GLANGSING_AKHIR_SIKLUS",$arr_log_ksg);
					if($this->dbSqlServer->affected_rows() < 0){
						$error++;
					}
				}
			}
		}

		if($error){
			// $this->dbSqlServer->trans_rollback();
			$result['message'] = $message['error'][$nextStatus];
		}
		else{
			//$this->dbSqlServer->trans_commit();
			$result['status'] = 1;
			$result['message'] = $message['success'][$nextStatus];
		}
		return json_encode($result);
	}
	public function no_urut_log_ppsk($no_ppsk){
       /* dapatkan no_urut berdasarkan no_reg */
          $tmp = $this->db->order_by('no_urut','desc')->where(array('no_ppsk'=>$no_ppsk))->get('log_ppsk');
          $tmp = $tmp->row(0);

          if(count($tmp) > 0){
             $no_urut = $tmp->NO_URUT;
          }
          else{
             $no_urut = 0;
          }
          $no_urut++;
          return $no_urut;
    }
	public function getListFarm($kode_pegawai = ''){
       $this->db->join('M_FARM mf','mf.KODE_FARM = pd.KODE_FARM','left');
       $this->db->where('pd.KODE_PEGAWAI',$kode_pegawai);
       return $this->db->get('PEGAWAI_D pd')->result();
    }
	public function getPpskData($tgl_buat = '',$kode_farm = ''){
		$this->db->where('CAST(TGL_BUAT as DATE) = ',$tgl_buat);
		$this->db->like('NO_PPSK',$kode_farm);
		$this->db->where('STATUS','N');
        return $this->db->get('LOG_PPSK')->result();
	}
	function get_today(){
 		$sql = <<<QUERY
 		select getdate() as [today]
QUERY;
 		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
         $stmt->execute();
         return $stmt->fetch(PDO::FETCH_ASSOC);
 	}
	public function doUpdatePpsk($data = null, $nextStatus = '', $keterangan_reject = ''){
		$error = 0;
		$message = array(
			'success' => array(
				'D' => 'Permintaan sak kosong berhasil disimpan sebagai draft',
				'N' => 'Permintaan sak kosong berhasil dirilis',
				'R' => 'Permintaan sak kosong berhasil di-Review',
				'RJ' => 'Permintaan sak kosong berhasil di-Reject',
				'A' => 'Permintaan sak kosong berhasil di-Approve',
				'V' => 'Permintaan sak kosong berhasil dihapus/dibatalkan',
			),
			'error' => array(
				'D' => 'Permintaan sak kosong gagal diubah',
				'N' => 'Permintaan sak kosong gagal dirilis',
				'R' => 'Permintaan sak kosong gagal di-Review',
				'RJ' => 'Permintaan sak kosong gagal di-Reject',
				'A' => 'Permintaan sak kosong gagal di-Approve',
				'V' => 'Permintaan sak kosong gagal dihapus/dibatalkan'
			)
		);
		// $this->dbSqlServer->trans_begin();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				$ppsk_data = $this->getPpskData($value['tgl_buat'],$value['kode_farm']);
				if(count($ppsk_data) > 0){
					foreach ($ppsk_data as $key_ppsk => $value_ppsk) {
						// cetak_r($value_ppsk,false);
						// $this->dbSqlServer->where("NO_PPSK", $no_ppsk);
						$tgl_buat	= $this->get_today();
						if($nextStatus == 'R1'){
							$nextStatus = 'R';
						}elseif ($nextStatus == 'R2') {
							$nextStatus = 'A';
						}
						$arr_ppsk = array(
							'NO_PPSK' 	=> $value_ppsk->NO_PPSK,
							'NO_URUT' 	=> $this->no_urut_log_ppsk($value_ppsk->NO_PPSK),
						    'STATUS' 	=> $nextStatus,
						    'USER_BUAT' => $this->session->userdata('kode_user'),
						    'TGL_BUAT' 	=> $tgl_buat['today'],
						    'KETERANGAN' => $keterangan_reject
						);
						$this->dbSqlServer->insert("LOG_PPSK",$arr_ppsk);
						if($this->dbSqlServer->affected_rows() < 0){
							$error++;
						}else {
							$this->dbSqlServer->where("NO_PPSK",$value_ppsk->NO_PPSK);
							$this->dbSqlServer->update("PPSK",array('STATUS' => $nextStatus));
						}
					}
				}
			}
		}
		// $this->dbSqlServer->trans_commit();
		if($error){
			// $this->dbSqlServer->trans_rollback();
			$result['message'] = $message['error'][$nextStatus];
		}
		else{
			//$this->dbSqlServer->trans_commit();
			$result['status'] = 1;
			$result['message'] = $message['success'][$nextStatus];
		}
		return json_encode($result);

	}
}
