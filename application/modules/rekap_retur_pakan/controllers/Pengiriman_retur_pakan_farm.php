<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Pengiriman_retur_pakan_farm extends MY_Controller{
	protected $grup_farm;
	private $tombol;
	private $akses;
	private $user;
	protected $thisFarm;
	function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');			
		$this->load->helper('stpakan');
		$this->load->config('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));			
		
		$this->thisFarm = $this->session->userdata('kode_farm');
		$this->user = $this->session->userdata('kode_user');
		$this->load->model('rekap_retur_pakan/m_retur_farm','rf');
		$this->load->model('rekap_retur_pakan/m_retur_farm_d','rfd');
		$this->load->model('rekap_retur_pakan/m_log_retur_farm','lrf');
		$this->load->model('rekap_retur_pakan/m_pengiriman_retur_farm', 'prf');
	}
	
	function index(){
		$farm = NULL;
		$kodefarm = $this->session->userdata('kode_farm');
		$user_level = $this->session->userdata('level_user');
		$list_farm = array(
			'KF' => $kodefarm,
			'AG' => $kodefarm,			
			'KDV' => $farm,
			'KD' => $farm,			
		);
		$akses = array(	
			'KF' => array($this->tombol['create'],$this->tombol['update']),	
			'AG' => '',								
			'KD' => array($this->tombol['review'],$this->tombol['reject']),			
			'KDV' => array($this->tombol['approve'],$this->tombol['reject'])			
		);
		$data['tombol'] = isset($akses[$user_level]) ? $akses[$user_level] : array(); 
		$data['list_farm'] = empty($list_farm[$user_level]) ? '' : Modules::run('forecast/forecast/list_farm',$this->grup_farm,$list_farm[$user_level]);
		$data['all_farm'] = $this->dropdownFarm();
		$viewPage = 'rekap_retur_pakan/pengiriman_retur_pakan_farm/index';
		$this->load->view($viewPage,$data);				
	}

	public function alokasi_retur(){
		$kodefarm = $this->session->userdata('kode_farm');
		$noRetur = $this->input->get('no_retur');
		$data['listPakanSisa'] = $this->sisaPakan($kodefarm);		
		$data['tombol'] = array($this->tombol['kembali']);	
		$idFarmTujuan = NULL;
		$data['tglKirim'] = date('Y-m-d');
		if(!empty($noRetur)){
			$data['retur'] = $this->rf->as_array()->get($noRetur);
			$data['logRetur'] = $this->lrf->as_array()->order_by('no_urut','desc')->limit(1)->get_by(array('no_retur' => $noRetur));
			$idFarmTujuan = $data['retur']['FARM_TUJUAN'];
			if($data['retur']['TGL_KIRIM'] >= $data['tglKirim']){
				$data['tglKirim'] = $data['retur']['TGL_KIRIM'];
			}	
			$data['listPakan'] = arr2DToarrKey($this->rfd->as_array()->get_many_by(array('no_retur'=>$noRetur)),'KODE_PAKAN');					
		}
		$data['farmTujuan'] = $this->dropdownFarm($idFarmTujuan, array($kodefarm));
		$this->load->view('rekap_retur_pakan/retur_pakan_farm/alokasi_retur',$data);
	}

	public function detail_pengiriman_retur(){
		$noRetur = $this->input->get('no_retur');
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm');
		$data['listPakanSisa'] = $this->rfd->listPakan($noRetur);
		$data['retur'] = $this->rf->as_array()->get($noRetur);
		$data['listStatus'] = $this->listStatus();
		$data['logRetur'] = $this->lrf->history($noRetur);
		$this->load->view('rekap_retur_pakan/retur_pakan_farm/detail_retur',$data);
	}

	public function simpan() {
		$kodefarm = $this->session->userdata('kode_farm');
		$user_buat = $this->session->userdata('kode_user');
        $tmp_data = $this->input->post('data');
        $data = json_decode($tmp_data,true);        
        $fileName   = $this->input->post('attachment_name'); 		
		$t = $this->do_upload('attachment');
		if($t['status']){
			$periode_aktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode'=>'A'))->get('M_PERIODE')->row_array();
			if(!empty($periode_aktif)){
				$statusRetur = 'N';
				$noRetur = $this->rf->getNomerRetur($kodefarm,$periode_aktif['periode_siklus']);				
				$keterangan = $data['keterangan'];
				$detailPakan = $data['detailPakan'];
				unset($data['keterangan']);
				unset($data['detailPakan']);
				$data['no_retur'] = $noRetur->nomer;
				$data['farm_asal'] = $kodefarm;
				$data['status'] = $statusRetur;
				$data['lampiran'] = $t['data']['upload_data']['file_name'];				
				$this->db->trans_begin();
				$this->rf->insert($data);
				if(isset($data['no_referensi'])){
					$this->rf->update($data['no_referensi'],array('status' => 'V'));
				}
				foreach($detailPakan as $dp){
					$dp['no_retur'] =  $noRetur->nomer;
					$this->rfd->insert($dp);
				}
				/** insert log */
				$logRetur = array(
					'no_retur' => $noRetur->nomer,					
					'status' => $statusRetur,
					'user_buat' => $user_buat,
					'keterangan' => $keterangan
				);
				$this->lrf->insert($logRetur);
				if ($this->db->trans_status() === FALSE )
					{
						$this->db->trans_rollback();						
						$this->result['message'] = 'Penyimpanan gagal';
					}
					else{
						$this->db->trans_commit();
						$this->result['status'] = 1;
						$this->result['message'] = 'Pengajuan retur no. '.$noRetur->nomer.' berhasil disimpan';

					}
			}else{
				$this->result['message'] = 'Tidak ada siklus yang aktif';
			}
		}else{
			$this->result['message'] = json_encode($t['data']['error']);
		}
		
        
        $this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function updateStatus(){		
		$user_buat = $this->session->userdata('kode_user');
		$noRetur = $this->input->post('no_retur'); 
		$kdStatus = $this->input->post('kd_status');
		$keterangan = $this->input->post('keterangan');

		$nextApproveStatus = array(
			'N' => 'RV',
			'RV' => 'A'
		);
		$nextRejectStatus = array(
			'N' => 'RJ1',
			'RV' => 'RJ2'
		);
		$nexStatus = !empty($keterangan) ? $nextRejectStatus : $nextApproveStatus;
		$this->db->trans_begin();
		$this->rf->update($noRetur,array('status' => $nexStatus[$kdStatus]));								
		/** insert log */
		$logRetur = array(
			'no_retur' => $noRetur,					
			'status' => $nexStatus[$kdStatus],
			'user_buat' => $user_buat,			
		);
		if(!empty($keterangan)){
			$logRetur['keterangan'] = $keterangan;
		}
		$this->lrf->insert($logRetur);
		if ($this->db->trans_status() === FALSE )
		{
			$this->db->trans_rollback();						
			$this->result['message'] = 'Penyimpanan gagal';
		}
		else{
			$this->db->trans_commit();
			$listStatus = $this->listStatus();
			$labelStatus = strtolower($listStatus[$nexStatus[$kdStatus]]);
			$this->result['status'] = 1;
			$this->result['message'] = 'Pengajuan retur no. '.$noRetur.' berhasil di'.$labelStatus;
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));

	}
	private function do_upload($file){
		$config = array(
		    'upload_path'          => 'file_upload/',
		    'allowed_types'        => 'doc|pdf|docx|png|jpg|jpeg|gif',
		    'max_size'             => 102400
		);
		$result = array();		
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload($file)){
				$result['status'] = 0;
				$result['data'] = array('error' => $this->upload->display_errors());
		}else{
				$result['status'] = 1;
				$result['data'] = array('upload_data' => $this->upload->data());
		}
		return $result;
	}
	
	
	/*cek finger AGF*/
	public function check_admin_verifikator(){
		$date_trans = $this->input->post('date_transaction');
		$result = array('status'=>'0', 'match'=>'0');
		$getVerify = $this->db->where(array('date_transaction'=>$date_trans));
		$getVerify = $this->db->get('fingerprint_verification')->result_array();
		if(count($getVerify)>0){
			$verificator = $getVerify[0]['verificator'];
			if(!empty($verificator)){
				$result['status'] = '1';
				$sql = <<<SQL
					select pd.kode_farm, pd.kode_pegawai, mp.nama_pegawai from pegawai_d pd 
					join m_pegawai mp on mp.kode_pegawai = pd.kode_pegawai 
					where mp.grup_pegawai = 'AGF' and pd.kode_pegawai = '{$verificator}'
SQL;
				$adminCheck = $this->db->query($sql)->result_array();
				if(count($adminCheck)>0){
					if($this->thisFarm == $adminCheck[0]['kode_farm']){
						$result['match'] = '1';
						$result['nama_pegawai'] = $adminCheck[0]['nama_pegawai'];
					}
				}
			}
		}else{
			$result['status'] = '0';
		}
		
		echo json_encode($result);
	}
	/*end cek finger AGF*/

	public function list_pengiriman_retur(){
		$user_level = $this->session->userdata('level_user');
		$belumTindakLanjut = $this->input->get('belumTindakLanjut'); 
		$farmTujuan = $this->input->get('farmTujuan');
		$startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		$parameter = array(
			'belumTindakLanjut' => $belumTindakLanjut, 			
		);
		$parameter['farm_asal'] = $this->thisFarm;
		if(!empty($farmTujuan)){
			$parameter['farm_tujuan'] = $farmTujuan;
		}
		$tglKirim = array(
			'startDate' => $startDate,
			'endDate' => $endDate
		);
		
		$dataRetur = $this->getListRetur($parameter,$tglKirim);
		$data['returs'] = $this->setOrderBy($dataRetur, 'TGL_KIRIM asc');
		$data['listStatus'] = $this->listStatus();
		$data['list_kendaraan'] = $this->list_kendaraan();
		foreach($data['returs'] as $dataReturn){
			$data['listPakanSisa'][$dataReturn['NO_RETUR']] = $this->rfd->listPakan($dataReturn['NO_RETUR']);
		}
		
		foreach($data['returs'] as $dataReturn){
			$data['listHistory'][$dataReturn['NO_RETUR']] = $this->lrf->history($dataReturn['NO_RETUR']);
		}
		
		$data['rejectedStatus'] = $user_level == 'KF' ? $this->rejectedStatus() : array();
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm'); 
		$data['user_level'] = $user_level;
		$this->load->view('rekap_retur_pakan/pengiriman_retur_pakan_farm/list_pengiriman',$data);
	}
	public function list_retur_timbang(){
		$belumTindakLanjut = $this->input->get('belumTindakLanjut');
		$kodefarm = $this->session->userdata('kode_farm');		
		$farmTujuan = $this->input->get('farmTujuan');
		$startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		$parameter = array(
			'belumTindakLanjut' => $belumTindakLanjut, 			
		);
		$parameter['rf.farm_asal'] = $kodefarm;				
		if(!empty($farmTujuan)){
			$parameter['rf.farm_tujuan'] = $farmTujuan;
		}
		$parameter['farm_asal'] = $parameter['farm_asal'];
		$parameter['farm_tujuan'] = $parameter['farm_tujuan'];
		$tglKirim = array(
			'startDate' => $startDate,
			'endDate' => $endDate
		);
		$data['returs'] = $this->rf->listReturTimbang($parameter,$tglKirim);				
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm');
		$this->load->view('rekap_retur_pakan/retur_pakan_farm/list_retur_timbang',$data);
	}
	private function getListRetur($parameter,$tglKirim){ 
		if($parameter['belumTindakLanjut']){
			$this->db->where_in('status', 'A');
		}else{
			unset($parameter['belumTindakLanjut']);
			$this->db->where($parameter);
		}
		if(!empty($tglKirim['startDate'])){
			if(!empty($tglKirim['endDate'])){
				$this->db->where('tgl_kirim between \''.$tglKirim['startDate'].'\' and \''.$tglKirim['endDate'].'\'');
			}else{
				$this->db->where('tgl_kirim >=\''.$tglKirim['startDate'].'\'');
			}
		}else{
			if(!empty($tglKirim['endDate'])){
				$this->db->where('tgl_kirim <=\''.$tglKirim['endDate'].'\'');
			}
		}

		return $this->rf->as_array()->get_all();		
	}
	/* sisa pakan adalah pakanReturGudang - jml yang akan diretur namun belum dilakukan proses generate dan statusnya bukan void
	*/		
	private function sisaPakan($kodefarm){
		$pakanReturGudang = $this->rf->getSisaPakan($kodefarm);
		$periodeAktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode'=>'A'))->get('M_PERIODE')->row_array();		
		if(!empty($periodeAktif) && !empty($pakanReturGudang)){			
			$pengurangPakan = $this->rf->pengurangPakan($kodefarm,$periodeAktif['periode_siklus']);			
			if(!empty($pengurangPakan)){
				$pengurangPakan = arr2DToarrKey($pengurangPakan,'kode_pakan');			
			}			
		}
		
		if(!empty($pakanReturGudang)){
			$pakanReturGudang = arr2DToarrKey($pakanReturGudang,'kode_pakan');
			if(!empty($pengurangPakan)){
				foreach($pakanReturGudang as $kp => &$pr){					
					if(isset($pengurangPakan[$kp])){
						$pr['jumlah'] -= $pengurangPakan[$kp]['jumlah'];
						$pr['berat'] -= $pengurangPakan[$kp]['jumlah'] * 50;
					}					
				}
			}
		}
		return $pakanReturGudang;
	}	

	public function generate(){
		$kodeFarm = $this->session->userdata('kode_farm');		
		$noRetur = $this->input->get('no_retur');
		$result = $this->rf->generate($kodeFarm,$noRetur);
		$this->result['status'] = $result->result == 1 ? 1 : 0;		
		$this->result['message'] = $result->result ? 'Generate pengiriman berhasil' : $result->pesan;
		if($result->result){
			$this->result['content'] = array('no_pengiriman' => $noRetur, 'jml_kebutuhan' => $result->total);
		}
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	
	public function timbang(){
		$kodeFarm = $this->session->userdata('kode_farm');
		$noRetur = $this->input->get('no_retur');
		$noReg = str_replace('RL/', '', $noRetur); echo $noReg;
		$data = array( 
			'detail' => simpleGrouping($this->rf->detailTimbang($noRetur,$kodeFarm),'KODE_BARANG'),
			'lockTimbangan' => $this->config->item('lockTimbangan')
		);
		$this->load->view('rekap_retur_pakan/pengiriman_retur_pakan_farm/detail_timbang',$data);
	}
	
	public function tabel_timbang(){
		$no_retur = $this->input->get('no_retur'); 
		$kode_pakan = $this->input->get('kode_pakan');
		$kirim = $this->input->get('kirim');
		$noExp = explode('/', $no_retur);
		$kode_farm = $noExp[1];
		$no_reg = substr($no_retur, 3, strlen($no_retur));
		$dm = array();
		switch($kirim){
			case 0:	
				//$dm = $this->prf->get_pallet_timbang($kode_farm, $no_retur, $kode_pakan);
				$dm = $this->prf->get_pallet_timbang($kode_farm, $kode_pakan);
			break;
			case 1:
				$dm = $this->prf->get_data_pallet($kode_farm, $no_retur);
			break;
		}
		
		$data = array( 
			'detail' 		=> $dm,
			'lockTimbangan' => $this->config->item('lockTimbangan'),
			'kirim'			=> $kirim 
		);
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm');
		$data['mpallet'] = arr2DToarrKey($this->get_m_pallet(), 'kode_pallet');
		$data['hpallet'] = $this->db->get_where('M_HAND_PALLET', array('STATUS_PALLET'=>'N', '_DEFAULT'=>'1', 'KODE_FARM'=>$this->thisFarm))->result_array();
		$this->load->view('rekap_retur_pakan/pengiriman_retur_pakan_farm/tabel_timbang_pallet', $data);
	}
	/*end set tabel timbang pallet*/
	
	/*simpan pengiriman retur*/
	public function simpan_pengiriman(){
		$sopir = $this->input->post('sopir');
		$nopol = $this->input->post('nopol');
		$noretur = $this->input->post('no_retur');
		$pallet = $this->input->post('pallet');
		$farm = $this->thisFarm;
		
		foreach($pallet as $pl){
			$exe=1;
			$jmlretur = $pl['jml_retur'];
			$date = date('Y-m-d H:i:s');
			$getPallet = $this->prf->get_no_pallet($farm, $pl['kode_pallet']);
			$jmlselanjutnya = $jmlretur;
			$beratbersih = $pl['berat_bersih'];
			
			foreach($getPallet as $dp){
				$onhand = $dp['jml_available'];
				$hitungCheck = $onhand - $jmlretur;
				$berat_available = $dp['berat_available'];
				
				$beratpick = round($beratbersih/$onhand, 3);
				$beratpick = round($beratpick*$jmlselanjutnya, 3);
					
				if($hitungCheck < 0){
					$jmlselanjutnya = $jmlretur - $onhand;
					$jmlretur = $onhand;
					$onhand = 0;
					$berat_available = 0.000;
				}else{
					$berat_available = round($berat_available - $beratpick, 3); 
					$jmlretur = $jmlselanjutnya;
					$onhand = $onhand - $jmlretur;
				}
				
				if($exe){$this->update_mv($dp['no_pallet'], $jmlretur, $berat_available, $beratpick, $onhand, $date, $noretur);}
				if($hitungCheck==0){$exe = 0;}
			}
		}
		
		$this->update_rf($sopir, $nopol, $noretur);
	}
	
	private function update_rf($sopir, $nopol, $noretur){
		$this->db->trans_begin();
		$rfBaru = array(
				'NOPOL' => $nopol,
				'SOPIR' => $sopir
			);	
		$this->db->where('NO_RETUR', $noretur);	
		$this->db->update('RETUR_FARM', $rfBaru);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
		}else{
			$this->db->trans_commit();
		}
	}
	
	private function update_mv($no_pallet, $jml, $berat_sisa, $beratbersih, $sisa, $date, $noretur){
		$parameter = array('no_pallet' => $no_pallet, 'kode_farm' => $this->thisFarm);
		$updateMV = array(
			'JML_ON_HAND'		=> $sisa,
			'JML_AVAILABLE'		=> $sisa,
			'PICKED_DATE' 		=> $date,
			'JML_PICK' 			=> $jml,
			'BERAT_AVAILABLE'	=> $berat_sisa,
			'PICKED_NAME'		=> $this->user,
			'BERAT_PICK'		=> $beratbersih
		);
		
		$this->db->trans_begin();
		$this->db->where($parameter);
		$this->db->update('MOVEMENT', $updateMV);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
		}else{
			$this->db->trans_commit();
			$this->insert_mvd($parameter, $noretur);
		}
	}
	
	private function insert_mvd($parameter, $noretur){
		$movement = $this->db->where($parameter);
		$movement = $this->db->get('MOVEMENT')->result_array();
		foreach($movement as $mv){
			$dataMD = array(
				'KODE_FARM'			=> $mv['KODE_FARM'],
				'NO_KAVLING'		=> $mv['NO_KAVLING'],
				'NO_PALLET'			=> $mv['NO_PALLET'],
				'KODE_BARANG'		=> $mv['KODE_BARANG'],
				'JENIS_KELAMIN'		=> $mv['JENIS_KELAMIN'],
				'NO_REFERENSI'		=> $noretur,
				'JML_ON_HAND'		=> $mv['JML_ON_HAND'],
				'JML_AVAILABLE'		=> $mv['JML_AVAILABLE'],
				'BERAT_AVAILABLE'	=> $mv['BERAT_AVAILABLE'],
				'JML_ON_PUTAWAY'	=> $mv['JML_ON_PUTAWAY'],
				'BERAT_ON_PUTAWAY'	=> $mv['BERAT_ON_PUTAWAY'],
				'JML_PUTAWAY'		=> $mv['JML_PUTAWAY'],
				'BERAT_PUTAWAY'		=> $mv['BERAT_ON_PUTAWAY'],
				'JML_ON_PICK'		=> $mv['JML_ON_PICK'],
				'BERAT_ON_PICK'		=> $mv['BERAT_ON_PICK'],
				'JML_PICK'			=> $mv['JML_PICK'],
				'BERAT_PICK'		=> $mv['BERAT_PICK'],
				'PUT_DATE'			=> $mv['PUT_DATE'],
				'PUT_NAME'			=> $mv['PUT_NAME'],
				'PICKED_DATE'		=> $mv['PICKED_DATE'],
				'PICKED_NAME'		=> $mv['PICKED_NAME'],
				'STATUS_STOK'		=> $mv['STATUS_STOK'],
				'KETERANGAN1'		=> 'PICK',
				'KETERANGAN2'		=> 'BY SYSTEM',
				'KODE_PALLET'		=> $mv['KODE_PALLET'],
				'BERAT_PALLET'		=> $mv['BERAT_PALLET'],
			);
			$this->db->trans_begin();
			$this->db->insert('MOVEMENT_D', $dataMD);
			if($this->db->trans_status() == false){
				$this->db->trans_rollback();
			}else{
				$this->db->trans_commit();
			}
		}
	}
	/*end simpan pengiriman retur*/
	
	
	public function simpanTimbang(){
		$user_buat = $this->session->userdata('kode_user');
		$data = $this->input->post('data');
		$kendaraan = json_encode($this->input->post('kendaraan'));
		$kodeFarm = $this->session->userdata('kode_farm');
		$this->db->trans_begin();
		foreach($data as $d){
			$berat_pick = ($d['berat_bersih'] / $d['jumlah_aktual']) * $d['jml_pick'];
			$whereMovement = array('no_pallet' => $d['no_pallet'], 'kode_farm' => $kodeFarm);
			$whereMovementD = array('no_pallet' => $d['no_pallet'], 'kode_farm' => $kodeFarm, 'no_referensi' => $d['no_referensi']);
			/** update movement dan movement_d */
			$dataMovement = array('picked_name' => $user_buat);
			$dataMovementD = array('jml_on_hand' => $d['jumlah_aktual'] - $d['jml_pick'], 'jml_pick' => $d['jml_pick'], 'jml_on_pick' => 0, 'berat_pick' => $berat_pick,'picked_name' => $user_buat);
			$this->db->where($whereMovement)
					->set('jml_on_pick','jml_on_pick - '.$d['jml_pick'],false)
					->set('jml_pick','jml_pick + '.$d['jml_pick'],false)
					->set('jml_on_hand','jml_on_hand - '.$d['jml_pick'],false)
					->set('berat_pick','berat_pick + '.$berat_pick,false)
					->set('picked_date','getdate()',false)
					->update('movement',$dataMovement);
			$this->db->where($whereMovementD)
					->set('keterangan2','keterangan2 + \'#'.$kendaraan.'\'',false)
					->set('picked_date','getdate()',false)->update('movement_d',$dataMovementD);
		}				
		if ($this->db->trans_status() === FALSE ){
			$this->db->trans_rollback();						
			$this->result['message'] = 'Penyimpanan gagal';
		}else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = 'Pengiriman retur pakan antar farm berhasil disimpan';
		}

        $this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	/*list rekomendasi NOPOL*/
	public function get_rekomendasi_nopol(){
		$parameter = array(
				'FARM_ASAL' => $this->thisFarm,
				'NOPOL !=' => ''
			);
		$this->db->trans_begin();
		$this->db->select('NOPOL');
		$this->db->where($parameter);
		$this->db->group_by('NOPOL');
		$this->output->set_content_type('application/json')
			->set_output(json_encode($this->db->get('RETUR_FARM')->result_array()));
		$this->db->trans_commit();		
	}
	/*list rekomendasi NOPOL*/

	/*cetak surat jalan*/
	public function cetakSJ(){
		$no_retur = $this->input->post('no_retur');	
		$this->sj_pdf($no_retur);
	}
	private function sj_pdf($no_retur) {	
		error_reporting(0);		
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A4', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );				
		$pdf->SetFontSize(15);		
		$pdf->SetMargins(8, 8, 8, 8); 			
		$sj = $this->rf->get($no_retur);		
		$sjd = $this->rfd->listPakanTimbang($no_retur);		
		$dataFarmAsal = $this->db->select('NAMA_FARM,ALAMAT_FARM,KOTA')->where(array('kode_farm' => $sj->FARM_ASAL))->get('m_farm')->row();		
		$dataFarmTujuan = $this->db->select('NAMA_FARM,ALAMAT_FARM,KOTA')->where(array('kode_farm' => $sj->FARM_TUJUAN))->get('m_farm')->row();		
		
		$params = $pdf->serializeTCPDFtagParameters ( array (
			$no_retur,
			'QRCODE,H',
			'',
			'',
			32,
			32
		) );
		$b = '<tcpdf method="write2DBarcode" params="' . $params . '" />';
		
		$html = $this->load->view ( 'rekap_retur_pakan/pengiriman_retur_pakan_farm/cetak_sj_pdf', array (
				'suratJalan' 		=> $sjd[0],
				'detail_sj'			=> $sjd,				
				'barcode' 			=> $b,	
				'dataFarmAsal' 		=> $dataFarmAsal,
				'dataFarmTujuan' 	=> $dataFarmTujuan,
				'sopir'				=> $sj->SOPIR,
				'nopol'				=> $sj->NOPOL,
		), true );
		
		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );
		$marginPage = 3;			
		$pdf->Line($marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,$marginPage);
		$pdf->Line($pdf->getPageWidth()-$marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,  $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage, $pdf->getPageHeight()-$marginPage, $pdf->getPageWidth()-$marginPage, $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage,$marginPage,$marginPage, $pdf->getPageHeight()-$marginPage);			
					
		$pdf->Output ( 'Surat Jalan.pdf', 'I' );
	}
	/*end cetak surat jalan*/
	
	
	private function get_m_pallet($kodepallet = NULL){
		$farm = $this->thisFarm;
		$q = $this->db->select(array('kode_pallet', 'brt_bersih'))->where(array('kode_farm'=>$farm, 'status_pallet'=>'N'));
		if(!empty($kodepallet)){
			$q->where('kode_pallet',$kodepallet);
		}
		return $q->get('M_PALLET')->result_array();
	}
	
	/*set dropdown farm*/
	private function listFarm($id = NULL){
		$q = $this->db->select(array('kode_farm','nama_farm'))->where('grup_farm','BDY');
		if(!empty($id)){
			$q->where(array('kode_farm' => $id));
		}
		return $q->get('m_farm')->result_array();
	}
	public function dropdownFarm($id = NULL, $exclude = array()){
		$arr = $this->listFarm($id);
		$s = '<select class="form-control">';
		$s .= '<option value="">Pilih Farm</option>';
		$selected = '';		
		foreach($arr as $val){
			if(!empty($id)){
				if($id == $val['kode_farm']){
					$selected = 'selected = "selected"';
				}
				else{
					$selected = '';
				}
			}
			if(!empty($exclude)){
				if(!in_array($val['kode_farm'],$exclude)){
					$s .= '<option value="'.$val['kode_farm'].'" '.$selected.'>'.$val['nama_farm'].'</option>';
				}
			}else{
				$s .= '<option value="'.$val['kode_farm'].'" '.$selected.'>'.$val['nama_farm'].'</option>';
			}
			
			

		}
		$s .= '</select>';
		return $s;
	}
	/*set dropdown farm*/

	/*status retur*/
	private function listStatus(){
		return array(
			'N' => 'Rilis',
			'V' => 'Void',
			'RV' => 'Review',
			'RJ1' => 'Reject',
			'RJ2' => 'Reject',
			'A' => 'Approve'
		);
	}
	/*end status retur*/
	
	
	private function rejectedStatus(){
		return array(			
			'RJ1' => 'Reject',
			'RJ2' => 'Reject',			
		);
	}
	
	public function list_kendaraan(){
		$q = $this->db->select(array('NO_KENDARAAN'));
		return $q->get('M_EKPEDISI_VEHICLE_NEW')->result_array();
	}
	
	/*get angka berat timbang*/
	public function get_berat_timbang (){
		echo file_get_contents(base_url('api/Timbangan/timbang'));
	}
	/*end get angka berat timbang*/
	
	/*json sorting*/
	function setOrderBy(array &$arr, $order = null) {
		if (is_null($order)) {
			return $arr;
		}
		usort($arr, function($a, $b) use($order) {
			$result = array();
			list($field, $sort) = array_map('trim', explode(' ', trim($order)));
			if (!(isset($a[$field]) && isset($b[$field]))) {
				continue;
			}
			if (strcasecmp($sort, 'desc') === 0) {
				$tmp = $a;
				$a = $b;
				$b = $tmp;
			}
			if (is_numeric($a[$field]) && is_numeric($b[$field]) ) {
				$result[] = $a[$field] - $b[$field];
			} else {
				$result[] = strcmp($a[$field], $b[$field]);
			}
			return implode('', $result);
		});
		return $arr;
	}
	/*end json sorting*/
	
}