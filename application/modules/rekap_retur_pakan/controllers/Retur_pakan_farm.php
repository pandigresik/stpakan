<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Retur_pakan_farm extends MY_Controller{
	protected $grup_farm;
	private $tombol;
	private $akses;
	function __construct(){
		parent::__construct();	
		$this->load->helper('stpakan');
		$this->load->config('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));			
		$this->tombol = array(			
			'create' => '<button class="btn btn-primary baru" onclick="Returpakanfarm.baru(this)">Baru</button>',
			'update' => '<button class="btn btn-default ubah hide" onclick="Returpakanfarm.ubah(this)">Ubah</button>',
			'review' => '<button class="btn btn-primary review" onclick="Returpakanfarm.review(this)">Review</button>',
			'approve' => '<button class="btn btn-primary approve" onclick="Returpakanfarm.approve(this)">Approve</button>',
			'reject' => '<button class="btn btn-danger reject" onclick="Returpakanfarm.reject(this)">Reject</button>',
			'kembali' => '<button class="btn btn-primary kembali" onclick="Returpakanfarm.kembali(this)">Kembali</button>',			
		);
		$this->load->model('rekap_retur_pakan/m_retur_farm','rf');
		$this->load->model('rekap_retur_pakan/m_retur_farm_d','rfd');
		$this->load->model('rekap_retur_pakan/m_log_retur_farm','lrf');
		$this->load->model('master/m_periode_siklus', 'ps');
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
		$viewPage = 'rekap_retur_pakan/retur_pakan_farm/index';
		if($user_level == 'AG'){
			$viewPage = 'rekap_retur_pakan/retur_pakan_farm/gudang';
		}
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

	public function detail_retur(){
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

	public function list_retur(){
		$user_level = $this->session->userdata('level_user');
		$belumTindakLanjut = $this->input->get('belumTindakLanjut'); 
		$farmAsal = $this->input->get('farmAsal');
		$farmTujuan = $this->input->get('farmTujuan');
		$startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		$parameter = array(
			'belumTindakLanjut' => $belumTindakLanjut, 			
		);
		if(!empty($farmAsal)){
			$parameter['farm_asal'] = $farmAsal;
		}
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
		//$data['listPakanSisa'] = '';
		
		foreach($data['returs'] as $dataReturn){
			$data['listPakanSisa'][$dataReturn['NO_RETUR']] = $this->rfd->listPakan($dataReturn['NO_RETUR']);
		}
		
		foreach($data['returs'] as $dataReturn){
			$data['listHistory'][$dataReturn['NO_RETUR']] = $this->lrf->history($dataReturn['NO_RETUR']);
		}
		
		$data['rejectedStatus'] = $user_level == 'KF' ? $this->rejectedStatus() : array();
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm'); 
		$data['user_level'] = $user_level;
	
		$this->load->view('rekap_retur_pakan/retur_pakan_farm/list_retur',$data);
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
		/*$parameter['farm_asal'] = $parameter['farm_asal'];
		$parameter['farm_tujuan'] = $parameter['farm_tujuan'];*/
		$tglKirim = array(
			'startDate' => $startDate,
			'endDate' => $endDate
		);
		$data['returs'] = $this->rf->listReturTimbang($parameter,$tglKirim);				
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm');
		$this->load->view('rekap_retur_pakan/retur_pakan_farm/list_retur_timbang',$data);
	}
	private function getListRetur($parameter,$tglKirim){ 
		$user_level = $this->session->userdata('level_user');
		$statusTindakLanjut = array(
			'KF' => array('RJ1','RJ2'),							
			'KD' => array('N'),			
			'KDV' => array('RV')			
		);
		
		if($parameter['belumTindakLanjut']){
			$this->db->where_in('status',$statusTindakLanjut[$user_level]);
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
		$data = array(
			'detail' => simpleGrouping($this->rf->detailTimbang($noRetur,$kodeFarm),'KODE_BARANG'),
			'lockTimbangan' => $this->config->item('lockTimbangan')
		);		
		$this->load->view('rekap_retur_pakan/retur_pakan_farm/detail_timbang',$data);
	}
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

	public function cetakSJ(){
		$no_sj = $this->input->post('no_sj');		
		$this->sj_pdf($no_sj);
	}

	private function sj_pdf($no_sj) {	
		error_reporting(0);		
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A4', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );				
		$pdf->SetFontSize(15);		
		$pdf->SetMargins(8, 8, 8, 8); 			
		$sj = $this->rf->get($no_sj);				
		$sjd = $this->rfd->listPakanTimbang($no_sj);		
		$dataFarmAsal = $this->db->select('NAMA_FARM,ALAMAT_FARM,KOTA')->where(array('kode_farm' => $sj->FARM_ASAL))->get('m_farm')->row();		
		$dataFarmTujuan = $this->db->select('NAMA_FARM,ALAMAT_FARM,KOTA')->where(array('kode_farm' => $sj->FARM_TUJUAN))->get('m_farm')->row();		
		$params = $pdf->serializeTCPDFtagParameters ( array (
			$no_sj,
			'QRCODE,H',
			'',
			'',
			32,
			32
		) );
		$b = '<tcpdf method="write2DBarcode" params="' . $params . '" />';				
		$html = $this->load->view ( 'rekap_retur_pakan/retur_pakan_farm/cetak_sj_pdf', array (
				'suratJalan' => $sjd[0],
				'detail_sj' => $sjd,				
				'barcode' => $b,				
				'dataFarmAsal' => $dataFarmAsal,
				'dataFarmTujuan' => $dataFarmTujuan,				
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
	private function rejectedStatus(){
		return array(			
			'RJ1' => 'Reject',
			'RJ2' => 'Reject',			
		);
	}
	
	public function check_ayam_dan_pakan(){
		$kode_farm = $this->input->post('kode_farm');
		$hasilCheck = '';
		$ayam_kandang = 0;
		$echo = 0;
		
		$pakan_kandang = $this->check_stok_pakan($kode_farm);
		$arr_ayam = $this->ps->stok_ayam('BW')->result_array();
		$arr_ayam = array();
		if(count($arr_ayam) > 0){
			foreach($arr_ayam as $ayam_semua_kandang){
				$ayam_kandang += $ayam_semua_kandang['sisa'];
			}
		}
		
		if($ayam_kandang > 0){
			$hasilCheck = 'proses_panen';
		}/*else{
			if($pakan_kandang > 0){
				$hasilCheck = 'belum_retur';
			}else{
				$hasilCheck = 'pengajuan_baru';
			}
		}*/
		$hasilCheck = 'pengajuan_baru';
		echo json_encode(array('notif_check'=>$hasilCheck));
	}
	
	private function check_stok_pakan($kode_farm){
		/*$sql = <<<SQL
			select sum(km.JML_STOK) as stok_pakan_kandang from kandang_movement km
			JOIN kandang_siklus ks ON ks.NO_REG = km.NO_REG AND ks.STATUS_SIKLUS = 'O' 
			AND ks.KODE_FARM = '{$kode_farm}'
SQL;*/
		$periode_siklus = '2018-4';
		$sql = <<<SQL
			select kode_barang,sum(jml_available) - coalesce((
				select sum(rfd.JUMLAH) from RETUR_FARM rf
				inner join RETUR_FARM_D rfd on rf.NO_RETUR = rfd.NO_RETUR
				where rf.status not in ('RJ1','RJ2','V') and rf.NO_RETUR like 'RL/{$kode_farm}/{$periode_siklus}%' 
				and rfd.kode_pakan = kode_barang),0) stok_pakan_kandang 
				from movement where NO_PALLET >= (
					select min(no_pallet) from movement_d where kode_farm = '{$kode_farm}' and KETERANGAN1 = 'PUT' and KETERANGAN2 in (
					select no_reg from KANDANG_SIKLUS where STATUS_SIKLUS = 'O' and KODE_FARM = '{$kode_farm}'
				) 
			) and kode_farm = '{$kode_farm}' and jml_available > 0
			group by kode_barang
SQL;
		
		$data_array = $this->db->query($sql)->result_array();
		$echo = 0;
		if($data_array[0]['stok_pakan_kandang'] != null){
			$echo = $data_array[0]['stok_pakan_kandang'];
		}
		return $echo;
	}
	
	//json sorting
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
	//end json sorting
	
}
