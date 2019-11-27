<?php
class M_kontrol_stok_glangsing extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->helper ( 'stpakan' );
		$this->dbSqlServer = $this->load->database('default', TRUE);
		$level_user = $this->session->userdata('level_user');
	}

    public function getStokGlangsingData($kode_farm = '', $req_status = '', $level_user_db = '')
    {
		$sql = <<<QUERY
			EXEC dbo.GET_GLANGSING_AKHIR_SIKLUS_NEW '{$kode_farm}', '{$req_status}', '', '{$level_user_db}'
QUERY;
		#cetak_r($sql);
		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getDetailPermintaanSak($kode_siklus, $saldo)
    {
		$sql = <<<QUERY
			EXEC dbo.get_permintaan_sak_new '{$kode_siklus}', '{$saldo}'
QUERY;

		#cetak_r($sql);
		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getDetailPerDate($kodeSiklus){
		$sql = <<<SQL
			select ph.no_ppsk, isnull(ph.ref_id, '') ref_id, ph.tgl_permintaan, ph.tgl_kebutuhan, ph.kode_budget, mb.nama_budget
				, ph.jml_diminta, ph.jml_over_budget, ph.keterangan alasan, ph.kode_siklus
				, lg.no_urut, lg.user_buat, lg.tgl_buat, isnull(lg.keterangan,'') keterangan, mp.nama_pegawai
				, case when lg.status = 'N' then 'Dibuat'
						when lg.status = 'R' then 'Dikoreksi'
						when lg.status = 'A' then 'Disetujui'
						when lg.status = 'RJ' then 'Ditolak'
						else '' end status_text
				, case when lg.status = 'R' then 'R1'
						when lg.status = 'A' and mp.grup_pegawai in('KBA','WKBA') then 'R2'
						else lg.status end status
			from ppsk_new ph
			join log_ppsk_new lg on ph.no_ppsk = lg.no_ppsk
			join m_budget_pemakaian_glangsing mb on ph.kode_budget = mb.kode_budget
			join m_pegawai mp on lg.user_buat = mp.kode_pegawai
			where ph.kode_siklus = '{$kodeSiklus}'
			order by ph.no_ppsk desc, lg.no_urut
SQL;

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
          $tmp = $this->db->order_by('no_urut','desc')->where(array('no_ppsk'=>$no_ppsk))->get('log_ppsk_new');
          $tmp = $tmp->row(0);

          if(count($tmp) > 0){
             $no_urut = $tmp->no_urut;
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
	public function getPpskData($no_ppsk = ''){
		$this->db->like('no_ppsk',$no_ppsk);
		$this->db->where('status','N');
        return $this->db->get('log_ppsk_new')->result();
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
		$result['ppsk'] = array();
		$message = array(
			'success' => array(
				'D' => 'Permintaan sak kosong berhasil disimpan sebagai draft',
				'N' => 'Permintaan sak kosong berhasil dirilis',
				'R' => 'Permintaan sak kosong berhasil dikoreksi',
				'RJ' => 'Permintaan sak kosong berhasil ditolak',
				'A' => 'Permintaan sak kosong berhasil disetujui',
				'A0' => 'Permintaan sak kosong berhasil disetujui',
				'V' => 'Permintaan sak kosong berhasil dihapus/dibatalkan',
			),
			'error' => array(
				'D' => 'Permintaan sak kosong gagal diubah',
				'N' => 'Permintaan sak kosong gagal dirilis',
				'R' => 'Permintaan sak kosong gagal dikoreksi',
				'RJ' => 'Permintaan sak kosong gagal ditolak',
				'A' => 'Permintaan sak kosong gagal disetujui',
				'A0' => 'Permintaan sak kosong gagal disetujui',
				'V' => 'Permintaan sak kosong gagal dihapus/dibatalkan'
			)
		);
		//$this->dbSqlServer->trans_begin();
		if(count($data) > 0){
			foreach ($data as $key => $value) {
				//cetak_r($value);
				$ppsk_data = $this->getPpskData($value['no_ppsk']);
				if(count($ppsk_data) > 0){
					foreach ($ppsk_data as $key_ppsk => $value_ppsk) {

						$tgl_buat	= $this->get_today();
						if($nextStatus == 'R1'){
							$nextStatus = 'R';
						}elseif ($nextStatus == 'R2') {
							$nextStatus = 'A';
							$level_user = $this->session->userdata('level_user');
							if($level_user != 'KDV'){
								/** jik over budget maka jika usernya bukan kadiv maka set nextStatus = A0 */
								$tmp_ppsk = $this->db->where(array('no_ppsk' => $value['no_ppsk']))->get('ppsk_new')->row();
								if($tmp_ppsk->jml_over_budget > 0){
									$nextStatus = 'A0';
								}
							}
						}
						
						$arr_ppsk = array(
							'no_ppsk' 	=> $value_ppsk->no_ppsk,
							'no_urut' 	=> $this->no_urut_log_ppsk($value_ppsk->no_ppsk),
						    'status' 	=> $nextStatus,
						    'user_buat' => $this->session->userdata('kode_user'),
						    'tgl_buat' 	=> $tgl_buat['today'],
						    'keterangan' => $keterangan_reject
						);
						
						$result['ppsk'][] = array(
							'no_ppsk' => $arr_ppsk['no_ppsk'],
							'no_urut' => $arr_ppsk['no_urut']
						); 
						/** nilainya dikembalikan dulu, karena ini sifatnya loopong */
						if ($nextStatus == 'A0') {
							$nextStatus = 'A';
						}
						$this->dbSqlServer->insert("LOG_PPSK_NEW",$arr_ppsk);
						if($this->dbSqlServer->affected_rows() < 0){
							$error++;
						}
						/*else {
							$this->dbSqlServer->where("NO_PPSK",$value_ppsk->NO_PPSK);
							$this->dbSqlServer->update("PPSK",array('STATUS' => $nextStatus));
						}*/
					}
				}
			}
		}
		// $this->dbSqlServer->trans_commit();
		if($error){
			//$this->dbSqlServer->trans_rollback();
			$result['status'] = 0;
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
