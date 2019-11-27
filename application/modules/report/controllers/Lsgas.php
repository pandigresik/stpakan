<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Lsgas extends MY_Controller {
	protected $result;
	protected $_user;
	protected $grup_farm;
	protected $adg_tampil = array(7,14,21,28);
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_lsgas','report');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_user = $this->session->userdata('kode_user');
		$this->adg_tampil = array(7,14,21,28);
	}
	public function index() {
		$data['list_farm'] = $this->getListFarm($this->session->userdata('kode_user'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->listSiklus($this->input->get('siklus'),$data['list_farm']);
		$data['kodefarm'] = $this->session->userdata('kode_farm');
		$user_level = $this->session->userdata('level_user');
		switch ($user_level) {
			case 'KF':
				$data['button'] = '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>';
				$data['list_farm_prop'] = 'none';
				break;
			case 'KD':
				$data['button'] = '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>';
				$data['list_farm_prop'] = 'inline';
				break;
			case 'KDB':
				$data['button'] = '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>';
				$data['list_farm_prop'] = 'inline';
				break;
			case 'KA':
				$data['button'] = '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>';
				$data['list_farm_prop'] = 'inline';
				break;
			case 'KDV':
				$data['button'] = '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>';
				$data['list_farm_prop'] = 'inline';
				break;
			default:
				# code...
				break;
		}
		$this->load->view('report/'.$this->grup_farm.'/lsgas',$data);
	}

	public function stok_pakan(){
		$user_level = $this->session->userdata('level_user');
		switch($user_level){
			case 'KF':
				$kode_farm = $this->session->userdata('kode_farm');
				$pilih_farm = 0;
				break;
			case 'KD':
				$kode_farm = NULL;
				$pilih_farm = 1;
				break;
			case 'KDV':
				$kode_farm = NULL;
				$pilih_farm = 1;
				break;
			case 'KDB':
				$kode_farm = NULL;
				$pilih_farm = 1;
				break;
			case 'KA':
				$kode_farm = NULL;
				$pilih_farm = 1;
				break;
			default:
				$kode_farm = $this->session->userdata('kode_farm');
				$pilih_farm = 0;
		}
		$data['list_farm'] = $this->list_farm($kode_farm);
		$data['nama_farm'] = ($user_level == 'KF') ? strip_tags($data['list_farm']): null;
		$data['pilih_farm'] = $pilih_farm;
		$this->load->view('report/stok_pakan',$data);
	}
	/* list farm berdasarkan user */
	public function userFarm(){
		$r = $this->db->select('mf.kode_farm,mf.nama_farm')
						->join('pegawai_d pd','pd.kode_farm = mf.kode_farm and pd.kode_pegawai = \''.$this->_user.'\'')
						->group_by(array('mf.kode_farm','mf.nama_farm'))
						->get('m_farm mf')
						->result_array();

		$this->result['status'] = 1;
		$this->result['content'] = $r;
		echo json_encode($this->result);
	}


	private function list_farm($id = null){

		$arr = $this->report->list_farm($id,$this->grup_farm)->result_array();
		$tmp = array();
		$t = '';
		$checked = '';
		/* check langsung jika yang login kepala farm */
		if(!empty($id)){
			$checked = 'checked';
		}
		if(!empty($arr)){
			foreach($arr as $cb){
				$t = '<div class="col-md-6"><div class="checkbox"><label><input type="checkbox" value="'.$cb['kode_farm'].'" '.$checked.' />'.$cb['nama_farm'].' ('.$cb['kode_strain'].')</label></div></div>';
				array_push($tmp,$t);
			}
		}
		return implode(' ',$tmp);

	}

	public function getListFarm($kode_pegawai = ''){
       $this->db->join('M_FARM mf','mf.KODE_FARM = pd.KODE_FARM','left');
       $this->db->where('pd.KODE_PEGAWAI',$kode_pegawai);
       return $this->db->get('PEGAWAI_D pd')->result();
    }

	private function listFarm($farm){
		if($farm == 'ALL'){
			$r = $this->db->select('mf.kode_farm,mf.nama_farm')
							->join('pegawai_d pd','pd.kode_farm = mf.kode_farm and pd.kode_pegawai = \''.$this->_user.'\'')
							->group_by(array('mf.kode_farm','mf.nama_farm'))
							->get('m_farm mf')
							->result_array();
			$result = array();
			foreach($r as $y){
				array_push($result,$y['kode_farm']);
			}
			$farm = implode('\',\'',$result);
		}
		return $farm;
	}
	private function listSiklus($siklus,$listfarm){
		if(empty($siklus)){
			return null;
		}
		$r = $this->db->select('kode_siklus')
						->where(array('periode_siklus' => $siklus))
						->where('kode_farm in (\''.$listfarm.'\')')
						->get('m_periode')
						->result_array();
	  $result = array();
		foreach($r as $y){
			array_push($result,$y['kode_siklus']);
			}
		return implode('\',\'',$result);
	}

	public function getStokGlangsingData(){
		echo $this->report->doReadStokGlangsingData();
	}

	public function cetakHistori(){
		//$kode_farm = $this->session->userdata('kode_farm');


		$kode_farm = $this->input->get('kode_farm');
		$kode_siklus = $this->input->get('kode_siklus');
		$siklus_periode = $this->input->get('siklus_periode');
		$no_urut = $this->input->get('no_urut');
		$query = $this->db->query("EXEC CETAK_STK_GLANGSING_AKHIR_SIKLUS '$kode_farm',$kode_siklus")->result_array();
		foreach ($query as $key => $result) {
			$data['periode_siklus'] = $result['PERIODE_SIKLUS'];
			$data['siklus_lalu'] = $result['SIKLUS_LALU'];
			$data['tahun'] = substr($result['PERIODE_SIKLUS'],0,4);
			$data['bulan'] = substr($result['PERIODE_SIKLUS'],5,2);
			$data['nama_farm'] = $this->getDataFarm($kode_farm)->NAMA_FARM;
			$data['total_pakan_terima'] = $result['JML_TERIMA_PAKAN'];
			$data['total_pakan_pakai'] = $result['JML_PAKAI_PAKAN'];
			$data['sisa_pakan'] = $result['JML_TERIMA_PAKAN'] - $result['JML_PAKAI_PAKAN'];
			$data['stok_lalu'] = $result['SAK_AWAL'];
			$data['pemasukan_siklus_ini'] = $result['SAK_TERIMA'];
			$data['glangsing_saat_ini'] = $result['SAK_TERIMA'] + $result['SAK_AWAL'];
			$data['pemakaian_internal'] = $result['SAK_PAKAI_INTERN'];
			$data['pemakaian_eksternal'] = $result['SAK_PAKAI_EKSTERN'];
			$data['dijual'] = $result['SAK_DIJUAL'];
			$data['sisa'] = $result['SAK_SISA'];
			$data['budget_internal'] = $this->getBudgetData($kode_siklus,'I');
			$data['budget_eksternal'] = $this->getBudgetData($kode_siklus,'E');
			$data['sisa_budget_internal'] = $this->getBudgetTotal($kode_siklus,'I')->JML_ORDER ;
			$data['sisa_budget_eksternal'] = $this->getBudgetTotal($kode_siklus,'E')->JML_ORDER;
			$data['rilis_by'] = $this->getUserLSGAS($result['KODE_SIKLUS'],$result['NO_URUT'],'N')['NAMA_PEGAWAI'];
			$data['review_by'] = $this->getUserLSGAS($result['KODE_SIKLUS'],$result['NO_URUT'],'R')['NAMA_PEGAWAI'];
			$data['approve_by'] = $this->getUserLSGAS($result['KODE_SIKLUS'],$result['NO_URUT'],'A1')['NAMA_PEGAWAI'];
			$data['ack_by'] = $this->getUserLSGAS($result['KODE_SIKLUS'],$result['NO_URUT'],'A2')['NAMA_PEGAWAI'];
			$data['base_url'] = base_url();
			$data['status_lsgas'] = $result['STATUS_LSGAS'];
		}


		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A4', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );

		$params = $pdf->serializeTCPDFtagParameters ( array (

		) );

			$b = '<tcpdf method="write2DBarcode" params="' . $params . '" />';
			$html = $this->load->view ( 'report/cetak_histori',$data, true );
			$pdf->AddPage ();
			$pdf->writeHTML ( $html, true, false, true, false, '' );

		$pdf->Output ( 'Delivery Order.pdf', 'I' );
	}

	public function getUserLSGAS($kode_siklus,$no_urut,$status)
	{
		$this->load->model('report/m_lsgas','m_lsgas');

		return $this->m_lsgas->getUserLSGAS($kode_siklus,$no_urut,$status);
	}
	public function getDataFarm($kode_farm = ''){
		$query = $this->db->query("
			select * from m_farm where kode_farm = '$kode_farm'
		");
		return $query->row();
	}

	public function getHargaJualGlangsing(){
		$kode_siklus = $this->input->post('kode_siklus');
		$query = $this->db->query("
		select hj.*, mg.NAMA_BUDGET
		from harga_jual_glangsing hj
		join M_BUDGET_PEMAKAIAN_GLANGSING mg on hj.KODE_BUDGET = mg.KODE_BUDGET
		where kode_siklus = '$kode_siklus'
			and no_urut =(select max(no_urut) from harga_jual_glangsing where kode_siklus = '$kode_siklus')
		");
		echo json_encode($query->result_array());
	}

	public function getPeriode($kode_siklus = ''){
		$query = $this->db->query("
			select * from m_periode where kode_siklus = '$kode_siklus'
		");
		return $query->row();
	}
	public function getTotalPakanTerima($kode_siklus){
		$query = $this->db->query("SELECT sum(jml_putaway) JML_TERIMA FROM MOVEMENT_D
		WHERE KETERANGAN1 = 'PUT'
		AND NO_PALLET LIKE 'SYS%'
		AND KETERANGAN2 LIKE ''+
		(SELECT TOP 1 substring(NO_REG,0,8) FROM KANDANG_SIKLUS JOIN M_PERIODE ON KANDANG_SIKLUS.KODE_SIKLUS = M_PERIODE.KODE_SIKLUS AND M_PERIODE.KODE_SIKLUS = '$kode_siklus')+'%' AND NO_REFERENSI IN(SELECT no_penerimaan FROM PENERIMAAN)");

		return $query->row();
	}

	public function getTotalPakanPakai($kode_siklus){
		$query = $this->db->query("SELECT sum(JML_PAKAI) JML_PAKAI FROM RHK_PAKAN WHERE NO_REG LIKE ''+
		(SELECT TOP 1 substring(NO_REG,0,8) FROM KANDANG_SIKLUS JOIN M_PERIODE ON KANDANG_SIKLUS.KODE_SIKLUS = M_PERIODE.KODE_SIKLUS AND M_PERIODE.KODE_SIKLUS = '$kode_siklus')+'%'");

		return $query->row();
	}

	public function getSGAS($kode_siklus,$no_urut){
		$query = $this->db->query("SELECT mp.PERIODE_SIKLUS,sgas.*
			FROM STOK_GLANGSING_AKHIR_SIKLUS sgas
			LEFT JOIN M_PERIODE mp ON mp.KODE_SIKLUS = sgas.KODE_SIKLUS
			JOIN (
				SELECT
					lsgas.KODE_SIKLUS kode,
					lsgas.NO_URUT urut,
					max(lsgas.NO_URUT_APPROVE) max_urut
				FROM LOG_STOK_GLANGSING_AKHIR_SIKLUS lsgas
				GROUP BY
				lsgas.KODE_SIKLUS, lsgas.NO_URUT
			) ts ON ts.kode = sgas.KODE_SIKLUS AND ts.urut = sgas.NO_URUT
			JOIN LOG_STOK_GLANGSING_AKHIR_SIKLUS lsgas ON lsgas.KODE_SIKLUS = ts.kode AND lsgas.NO_URUT = ts.urut AND lsgas.NO_URUT_APPROVE = ts.max_urut
			where sgas.KODE_SIKLUS = '$kode_siklus' and sgas.NO_URUT = '$no_urut'
			");
		return $query->row();
	}

	public function getBudgetData($kode_siklus,$kategori){
		$query = $this->db->query("
			SELECT COALESCE(SUM(JML_SAK+JML_OVER),0) JML_ORDER, MAX(mbpg.NAMA_BUDGET) NAMA_BUDGET,
			PPSK.KODE_BUDGET
			FROM PPSK
			INNER JOIN M_BUDGET_PEMAKAIAN_GLANGSING mbpg ON mbpg.KODE_BUDGET = PPSK.KODE_BUDGET AND mbpg.KATEGORI_BUDGET = '$kategori'
			INNER JOIN M_PERIODE mp on mp.KODE_SIKLUS = $kode_siklus
			WHERE NO_PPSK LIKE '%PPSK/'+mp.KODE_FARM+'/'+mp.PERIODE_SIKLUS+'%'
			group by PPSK.KODE_BUDGET
		");

		return $query->result();
	}

	public function getBudgetTotal($kode_siklus,$kategori){
		$query = $this->db->query("
			SELECT COALESCE(sum(JML_ORDER),0) JML_ORDER FROM BUDGET_GLANGSING_D bgd
			JOIN M_BUDGET_PEMAKAIAN_GLANGSING mbpg ON mbpg.KODE_BUDGET = bgd.KODE_BUDGET AND mbpg.KATEGORI_BUDGET = '$kategori'
			AND bgd.KODE_SIKLUS = $kode_siklus and bgd.NO_URUT = (select max(NO_URUT) from BUDGET_GLANGSING_D where KODE_SIKLUS = bgd.KODE_SIKLUS)
		");

		return $query->row();
	}

	function updateLsgas(){
		$error		 = false;
		// $kodefarm 	 = $this->session->userdata('kode_farm');
		$no_urut = 1;
		$kode_siklus	= $this->input->post('kode_siklus')?$this->input->post('kode_siklus'):'';
		$kode_farm		= $this->input->post('kode_farm')?$this->input->post('kode_farm'):'';
		$harga_dijual		= $this->input->post('harga_dijual')?$this->input->post('harga_dijual'):0;
		$nextStatus 	= $this->input->post('nextStatus');
		$periode_siklus 	= $this->input->post('periode_siklus');
		//$kodefarm	= $this->input->post('kodefarm');
		$tgl_buat    = $this->get_today();
		//$keterangan_reject = $this->input->post('keterangan_reject');
		//$keterangan_over = $this->input->post('keterangan_over');
		$keterangan = $this->input->post('keterangan_reject');
		$this->load->model('report/m_lsgas','m_lsgas');

		$message = array(
			'success' => array(
				'N' => 'Penyimpanan Laporan Stok Glangsing Akhir Siklus '.$periode_siklus.' berhasil dilakukan',
				'R' => 'Proses Approval Laporan Stok Glangsing Akhir Siklus '.$periode_siklus.' berhasil dilakukan',
				'RJ' => 'Proses Reject Laporan Stok Glangsing Akhir Siklus '.$periode_siklus.' berhasil dilakukan',
				'A' => 'Proses Approval Laporan Stok Glangsing Akhir Siklus '.$periode_siklus.' berhasil dilakukan',
			),
			'error' => array(
				'N' => 'Penyimpanan Laporan Stok Glangsing Akhir Siklus gagal dilakukan',
				'R' => 'Proses Approval Laporan Stok Glangsing Akhir Siklus gagal dilakukan',
				'RJ' => 'Proses Reject Laporan Stok Glangsing Akhir Siklus gagal dilakukan',
				'A' => 'Proses Approval Laporan Stok Glangsing Akhir Siklus gagal dilakukan',
			)
		);
		$this->db->trans_begin();
		if($nextStatus == 'N'){
			$lsgas_data = $this->db->query("EXEC SIMPAN_STK_GLANGSING_AKHIR_SIKLUS '$kode_farm',$kode_siklus")->result_array();
			$this->db->insert("STOK_GLANGSING_AKHIR_SIKLUS",$lsgas_data[0]);

			if($this->db->affected_rows() > 0){
				$no_urut = $lsgas_data[0]['NO_URUT'];

				$this->db->query("insert into harga_jual_glangsing(KODE_SIKLUS,NO_URUT,KODE_BUDGET,HARGA) values ('$kode_siklus',$no_urut,'GP',$harga_dijual)");

				$sinkronisasi = array();
				$sinkronisasi['transaksi'] = "simpan_laporan_stok_glangsing_akhir_siklus";
				if($nextStatus == 'N'){
					$sinkronisasi['asal'] 		= $kode_farm;
					$sinkronisasi['tujuan'] 	= "FM";
				}else {
					$sinkronisasi['asal'] 		= "FM";
					$sinkronisasi['tujuan'] 	= $kode_farm;
				}
				$sinkronisasi['aksi'] 		= "PUSH";
				$sinkronisasi['tgl_buat'] 	= $tgl_buat['today'];

				$this->db->insert("sinkronisasi", $sinkronisasi);

				if($this->db->affected_rows() > 0){
					$id = $this->db->insert_id();

					$detail_sinkronisasi = array();
					$detail_sinkronisasi["sinkronisasi"] = $id;
					$detail_sinkronisasi["aksi"] 	= "I";
					$detail_sinkronisasi["tabel"] = "STOK_GLANGSING_AKHIR_SIKLUS";
					$detail_sinkronisasi["kunci"] = '{"KODE_SIKLUS":"'.$kode_siklus.'","NO_URUT":"'.$no_urut.'"}';
					$detail_sinkronisasi["status_identity"] = 0;
					$this->db->insert("detail_sinkronisasi", $detail_sinkronisasi);

					$detail_sinkronisasi = array();
					$detail_sinkronisasi["sinkronisasi"] = $id;
					$detail_sinkronisasi["aksi"] 	= "I";
					$detail_sinkronisasi["tabel"] = "harga_jual_glangsing";
					$detail_sinkronisasi["kunci"] = '{"KODE_SIKLUS":"'.$kode_siklus.'","NO_URUT":"'.$no_urut.'","KODE_BUDGET":"GP"}';
					$detail_sinkronisasi["status_identity"] = 0;
					$this->db->insert("detail_sinkronisasi", $detail_sinkronisasi);

					if($this->db->affected_rows() <= 0){
						// $this->db->trans_rollback();
						// return false;
						$error = true;
					}
					$data_log_sgas = array(
						'KODE_SIKLUS' => $kode_siklus,
						'NO_URUT'	  => $no_urut,
						'NO_URUT_APPROVE'=>1,
						'STATUS'	=> $nextStatus,
						'USER_BUAT'	=> $this->_user,
						'TGL_BUAT'	=> $tgl_buat['today']
					);
					$this->db->insert("LOG_STOK_GLANGSING_AKHIR_SIKLUS",$data_log_sgas);
					if($this->db->affected_rows() > 0){
						$detail_sinkronisasi = array();
						$detail_sinkronisasi["sinkronisasi"] = $id;
						$detail_sinkronisasi["aksi"] 	= "I";
						$detail_sinkronisasi["tabel"] = "LOG_STOK_GLANGSING_AKHIR_SIKLUS";
						$detail_sinkronisasi["kunci"] = '{"KODE_SIKLUS":"'.$kode_siklus.'","NO_URUT":"'.$no_urut.'","NO_URUT_APPROVE":"1"}';
						$detail_sinkronisasi["status_identity"] = 0;
						$this->db->insert("detail_sinkronisasi", $detail_sinkronisasi);

						if($this->db->affected_rows() <= 0){
							// $this->db->trans_rollback();
							// return false;
							$error = true;
						}
					}
				}
			}
		}else {
			// $this->db->where("KODE_SIKLUS", $kode_siklus);
			// $this->db->update("STOK_GLANGSING_AKHIR_SIKLUS", $data_ppsk);
			//
			// if($this->db->affected_rows() > 0){
			// 	$sinkronisasi = array();
			// 	$sinkronisasi['transaksi'] = "ubah_permintaan_sak_kosong";
			// 	if($nextStatus != 'D' && $nextStatus != 'N'){
			// 		$sinkronisasi['asal'] 		= "FM";
			// 		$sinkronisasi['tujuan'] 	= $kodefarm;
			// 	}else {
			// 		$sinkronisasi['asal'] 		= $kodefarm;
			// 		$sinkronisasi['tujuan'] 	= "FM";
			// 	}
			// 	$sinkronisasi['aksi'] 		= "PUSH";
			// 	$sinkronisasi['tgl_buat'] 	= $tgl_buat['today'];
			//
			// 	$this->db->insert("sinkronisasi", $sinkronisasi);
			//
			// 	if($this->db->affected_rows() > 0){
			// 		$id = $this->db->insert_id();
			//
			// 		$detail_sinkronisasi = array();
			// 		$detail_sinkronisasi["sinkronisasi"] = $id;
			// 		$detail_sinkronisasi["aksi"] 	= "U";
			// 		$detail_sinkronisasi["tabel"] = "PPSK";
			// 		$detail_sinkronisasi["kunci"] = '{"NO_PPSK":"'.$no_ppsk.'"}';
			// 		$detail_sinkronisasi["status_identity"] = 0;
			// 		$this->db->insert("detail_sinkronisasi", $detail_sinkronisasi);
			//
			// 		if($this->db->affected_rows() <= 0){
			// 			// $this->db->trans_rollback();
			// 			// return false;
			// 			$error = true;
			// 		}
			//
			// 		$data_log_ppsk = array();
			// 		$data_log_ppsk['NO_PPSK'] 	 = $no_ppsk;
			// 		$data_log_ppsk['NO_URUT'] 	 = $no_urut_log_ppsk;
			// 		$data_log_ppsk['STATUS'] 	 = $nextStatus;
			// 		$data_log_ppsk['USER_BUAT'] = $this->_user;
			// 		$data_log_ppsk['TGL_BUAT']  = $tgl_buat['today'];
			// 		$data_log_ppsk['KETERANGAN']= $keterangan;
			//
			// 		$this->db->insert("LOG_PPSK", $data_log_ppsk);
			//
			// 		if($this->db->affected_rows() > 0){
			// 			echo "hsdjfhds";
			// 			$detail_sinkronisasi = array();
			// 			$detail_sinkronisasi["sinkronisasi"] = $id;
			// 			$detail_sinkronisasi["aksi"] 	= "I";
			// 			$detail_sinkronisasi["tabel"] = "LOG_STOK_GLANGSING_AKHIR_SIKLUS";
			// 			$detail_sinkronisasi["kunci"] = '{"KODE_SIKLUS":"'.$no_ppsk.'","NO_URUT":"'.$no_urut.'","NO_URUT_APPROVE":"1"}';
			// 			$detail_sinkronisasi["status_identity"] = 0;
			// 			$this->db->insert("detail_sinkronisasi", $detail_sinkronisasi);
			//
			// 			if($this->db->affected_rows() <= 0){
			// 				// $this->db->trans_rollback();
			// 				// return false;
			// 				$error = true;
			// 			}
			// 		}
			// 	}
			// }
			//echo "exec UBAH_STK_GLANGSING_AKHIR_SIKLUS '$kode_siklus','$nextStatus','$keterangan','$this->_user'";
			$this->db->query("exec UBAH_STK_GLANGSING_AKHIR_SIKLUS '$kode_siklus','$nextStatus','$keterangan','$this->_user'");
			/*if($this->db->affected_rows() <= 0){
				// $this->db->trans_rollback();
				// return false;
				$error = true;
			}*/
		}
		if($error){
			$this->db->trans_rollback();
			$this->result['message'] = $message['error'][$nextStatus];
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = $message['success'][$nextStatus];
			$this->result['kode_siklus'] = $kode_siklus;
			$this->result['periode_siklus'] = $periode_siklus;
			$this->result['no_urut'] = $no_urut;
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));

	}
	function get_today(){
		$sql = <<<QUERY
		select getdate() as [today]
QUERY;
		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
}
