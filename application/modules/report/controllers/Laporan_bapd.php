<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Laporan_bapd extends MY_Controller{
	protected $result;
	protected $_user;
	protected $_idFarm;
	private $grup_farm;
	public function __construct(){
		parent::__construct();
		$this->result = array('status' => 0, 'content'=> '', 'message' => '');
		$this->_user = $this->session->userdata('kode_user');
		$this->load->helper('stpakan');
		$this->load->model('penerimaan_docin/m_bapd','bapd');
		$this->load->model('penerimaan_docin/m_bapdocbox', 'bdb');
		$this->load->model('report/M_laporan_bapd', 'lap_bapd');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_idFarm = $this->session->userdata('kode_farm');
	}
	public function index(){
		$kodefarm 					= $this->session->userdata('kode_farm');
		$user_level 				= $this->session->userdata('level_user');
		$data['user_level']			= $user_level;
		$data['list_farm']			= $this->db->select(array('kode_farm', 'nama_farm'))->get('M_FARM')->result_array();
		$data['periode']    		= $this->input->get('periode');
		$data['kodefarm']    		= $this->input->get('kodefarm');
		$this->load->view('report/laporan_bapd/daftarbapd', $data);
	}

	public function list_bapd(){
		$get_periode 		= $this->input->get('periode');
		$get_kodefarm		= $this->input->get('kodefarm');
		$kodefarm 			= array();
		$user_level 		= $this->session->userdata('level_user');
		$noreg 				= array();
		$data 				= array();
		
		if($get_periode != '' && $get_kodefarm != ''){
			//$kodefarm[0] = $get_kodefarm;
			//$data_noreg = $get_kodefarm.'/'.$get_periode.'/01';
			//array_push($noreg, $data_noreg);
			$noreg[$get_kodefarm] = $get_periode;
		}else{
			$paramPeriode = array(
				'status_periode' => 'A'
			);
			if($user_level == 'KF'){
				$paramPeriode['kode_farm'] = $this->session->userdata('kode_farm');
			}	
				if($get_kodefarm != ''){
					$paramPeriode['kode_farm'] = $get_kodefarm;
				}
				$dataPeriode = $this->db->where($paramPeriode)->get('m_periode')->result_array();
				if(!empty($dataPeriode)){
					foreach($dataPeriode as $_dp){
						$noreg[$_dp['KODE_FARM']] = $_dp['PERIODE_SIKLUS'];
					}
				}
		}
		
		
		//$data['idf'] = $kodefarm;
		$dataLen = count($noreg);
		foreach($noreg as $_kf => $periode_siklus){	
			//$noreg_arr = explode('/', $noreg[$i]);
			$nama_farm = $this->db->select('NAMA_FARM')->where('KODE_FARM', $_kf)->get('M_FARM')->result();
			$list_bapd = $this->lap_bapd->list_bapd($_kf, $periode_siklus)->result_array();
			$data['kodefarm'][$_kf] = $_kf;
			$data['nama_farm'][$_kf] = $nama_farm[0]->NAMA_FARM;
			$data['periode_siklus'][$_kf] = $periode_siklus;
			$data['user_level'] = $user_level;
			$data['list_bapd'][$_kf] = $list_bapd;
			$data['box'][$_kf] = array();
			$data['afkir'][$_kf] = array();
			$lastNoreg = '';
			$sumJmlBox = 0;
			$bapdlen = 0;
			foreach($list_bapd as $lb){
				$no_reg = $lb['no_reg'];
				//$check_umur = $this->lap_bapd->check_umur($no_reg)->count_all_results();
				//if($check_umur>7){
					$getBDB = $this->bdb->as_array()->get_many($no_reg);
					$bdblen = 0;
					foreach($getBDB as $g_bdb){
						$bdblen++;
						if($lastNoreg == ''){ 
							$lastNoreg = $g_bdb['NO_REG']; 
							$bapdlen++;
						}
						if($lastNoreg != $g_bdb['NO_REG']){
							$lastNoreg = $g_bdb['NO_REG'];
							$sumJmlBox = $g_bdb['JML_BOX'];
							$bapdlen++;
						}else{
							$sumJmlBox += $g_bdb['JML_BOX'];
						}
						
						if($bdblen == count($getBDB)){
							//echo $lastNoreg.' => '.$sumJmlBox."<br>";
							$data['box'][$_kf][$lastNoreg] 	= $sumJmlBox;
							$data['afkir'][$_kf][$lastNoreg]	= $this->lap_bapd->get_umur_7hari($lastNoreg)->result();
						}
					}
				/*}else{
					$data['box'][$_kf][$no_reg] 	= 'Nan';
					$data['afkir'][$_kf][$no_reg]	= 'Nan';
				}*/
			}
		}
		$this->load->view('report/laporan_bapd/list_bapd', $data);
	}
	
	public function get_kode_box(){
		$tipe = $this->input->get('tipe');
		$get_kodefarm = $this->input->get('kodefarm');
		switch($tipe){
			case 0:
				$kode_farm = $this->_idFarm;
				$kodebox = null;
				$kodebox = $this->lap_bapd->get_kode_box($kode_farm)->result_array();
				$this->output->set_content_type('application/json')->set_output(json_encode($kodebox));
			break;
			case 1:
				$kode_farm = $get_kodefarm;
				$kodebox = null;
				$kodebox = $this->lap_bapd->get_kode_box($kode_farm)->result_array();
				$this->output->set_content_type('application/json')->set_output(json_encode($kodebox));
			break;
		}
	}
	
	public function bapd_pdf(){
		$this->load->model('penerimaan_docin/m_bapdoc', 'bd');
		$this->load->model('penerimaan_docin/m_logbapdoc', 'lbd');
		$kodefarm = array();
		$noreg = array();
		$user_level = $this->session->userdata('level_user');
		error_reporting(0);
		$this->load->library('Pdf');
		$pdf = new Pdf ( 'P', PDF_UNIT, 'A4', true, 'UTF-8', false );
		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );	
		$pdf->SetMargins(PDF_MARGIN_LEFT, 8, PDF_MARGIN_RIGHT);
		
		$get_periode 	= $_GET['periode'];
		$get_kodefarm 	= $_GET['kodefarm'];
		
		if($get_periode != '' && $get_kodefarm != ''){
			//$kodefarm[0] = $get_kodefarm;
			//$data_noreg = $get_kodefarm.'/'.$get_periode.'/01';
			//array_push($noreg, $data_noreg);
			$noreg[$get_kodefarm] = $get_periode;
		}else{
			$paramPeriode = array(
				'status_periode' => 'A'
			);
			if($user_level == 'KF'){
				$paramPeriode['kode_farm'] = $this->session->userdata('kode_farm');
			}
				if(!empty($get_kodefarm)){
					$paramPeriode['kode_farm'] = $get_kodefarm;
				}	
				$dataPeriode = $this->db->where($paramPeriode)->get('m_periode')->result_array();
				if(!empty($dataPeriode)){
					foreach($dataPeriode as $_dp){
						$noreg[$_dp['KODE_FARM']] = $_dp['PERIODE_SIKLUS'];
					}
				}
		}
		
		//for($i=0;$i<count($noreg);$i++){ 
		foreach($noreg as $_kf => $periode_siklus){		
			$excNum 		= 1;
			$nama_farm 		= $this->db->select('NAMA_FARM')->where('KODE_FARM', $_kf)->get('M_FARM')->result();
			$param 			= array(
					'STATUS_SIKLUS'	=> 'O',
					'KODE_FARM'		=> $_kf
			);
			
			$list_bapd 		= $this->lap_bapd->list_bapd($_kf, $periode_siklus)->result_array();
			$bapdlen 		= count($list_bapd)%2;
			$dataLen 		= count($list_bapd);
			
			//if($bapdlen==1){ $dataLen -= 1; }
			foreach($list_bapd as $lb){
				//$check_umur = $this->lap_bapd->check_umur($lb['no_reg'])->count_all_results();
				//if($excNum <= $dataLen && $check_umur > 7){
					$no_reg 		= $lb['no_reg'];
					//$allsj 		= $this->bdb->as_array()->get_many($no_reg);
					$allsj			= $this->lap_bapd->list_sj($no_reg)->result_array();
					$exp_noreg 		= explode('/', $no_reg);
					$no_kandang 	= $exp_noreg[2];
					$strain 		= $this->lap_bapd->get_strain($no_reg)->result();
					$penghitung 	= $this->lap_bapd->get_user_info($no_reg, 'N')->result();
					$saksi 			= $this->lap_bapd->get_user_info($no_reg, 'RV')->result();
					$mengetahui 	= $this->lap_bapd->get_user_info($no_reg, 'A')->result();
					$afkir 			= $this->lap_bapd->get_umur_7hari($no_reg)->result();
					$dataPDF = array(
						'nama_farm'		=> $nama_farm[0]->NAMA_FARM,
						'no_kandang'	=> $no_kandang,
						'noreg'			=> $no_reg,
						'dataSJ'		=> $allsj,
						'strain'		=> $strain[0]->kode_strain,
						'hatchery'		=> $lb['nama_hatchery'],
						'afkir'			=> $afkir[0]->JML_AFKIR + $lb['jml_afkir'],
						'bb'			=> $lb['bb_rata2'],
						'uniformity'	=> $lb['uniformity'],
						'tgl_docin'		=> $lb['tgl_doc_in'],
						'saksi'			=> $saksi,
						'penghitung'	=> $penghitung,
						'mengetahui'	=> $mengetahui
					);
					$html = $this->load->view('report/laporan_bapd/bapd_pdf', $dataPDF, true );
					if($excNum == $dataLen && $excNum%2 == 1){
						$pdf->AddPage($html);
						$pdf->writeHTML ( $html, true, false, true, false, '' );
					}elseif($excNum%2 == 1){
						$pdf->AddPage();
						$pdf->writeHTML ( $html, true, false, true, false, '' );
					}else{
						$pdf->Line($html);
						$pdf->writeHTML ( $html, true, false, true, false, '' );
					}
				//}
				$excNum++;
			}
		}
		$pdf->Output( 'BAPD.pdf', 'I' );
	}
	
	public function kodebox_pdf(){
		$user_level = $this->session->userdata('level_user');
		$kodebox = null;
		$html = '';
		
		error_reporting(0);
		$this->load->library('Pdf');
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A4', true, 'UTF-8', false );
		$pdf->SetPrintHeader (false);
		$pdf->SetPrintFooter (true);
		$pdf->SetMargins(25, 10, 25);
		
		$get_periode = $_GET['periode'];
		$get_kodefarm = $_GET['kodefarm'];
		
		if($get_periode != '' && $get_kodefarm != ''){
			$kodefarm	= $get_kodefarm;
			$periode 	= $get_periode;
			$noreg_like	= $kodefarm.'/'.$periode;
			$nama_farm 	= $this->db->select('NAMA_FARM')->where('KODE_FARM', $kodefarm)->get('M_FARM')->result();
			$kodebox 	= $this->lap_bapd->get_kode_box_periode($noreg_like)->result_array();
			$dataPDF 	= array(
				'kodebox' 	=> $kodebox,
				'namafarm'	=> $nama_farm[0]->NAMA_FARM
			);
			$html = $this->load->view('report/laporan_bapd/kodebox_bapd_pdf', $dataPDF, true);
			$pdf->AddPage();
			$pdf->writeHTML ( $html, true, false, true, false, '' );
		}else{
			if($user_level == 'KF'){
				$kodefarm 	= $this->_idFarm;
				$nama_farm 	= $this->db->select('NAMA_FARM')->where('KODE_FARM', $kodefarm)->get('M_FARM')->result();
				$kodebox 	= $this->lap_bapd->get_kode_box($kodefarm)->result_array();
				$dataPDF 	= array(
					'kodebox' 	=> $kodebox,
					'namafarm'	=> $nama_farm[0]->NAMA_FARM
				);
				$html 		= $this->load->view('report/laporan_bapd/kodebox_bapd_pdf', $dataPDF, true);
				$pdf->AddPage();
				$pdf->writeHTML ( $html, true, false, true, false, '' );
			}else{	
				/*$allFarm = $this->lap_bapd->get_all_kodefarm()->result_array();
				foreach($allFarm as $kd_farm){
					$kodefarm 	= $kd_farm['KODE_FARM'];
					$nama_farm 	= $this->db->select('NAMA_FARM')->where('KODE_FARM', $kodefarm)->get('M_FARM')->result();
					$kodebox 	= $this->lap_bapd->get_kode_box($kodefarm)->result_array();
					$dataPDF 	= array(
						'kodebox'	=> $kodebox,
						'namafarm'	=> $nama_farm[0]->NAMA_FARM
					);
					$html 		= $this->load->view('report/laporan_bapd/kodebox_bapd_pdf', $dataPDF, true);
					$pdf->AddPage();
					$pdf->writeHTML ( $html, true, false, true, false, '' );
				}*/
				$kodefarm 	= $get_kodefarm;
				$nama_farm 	= $this->db->select('NAMA_FARM')->where('KODE_FARM', $kodefarm)->get('M_FARM')->result();
				$kodebox 	= $this->lap_bapd->get_kode_box($kodefarm)->result_array();
				$dataPDF 	= array(
					'kodebox'	=> $kodebox,
					'namafarm'	=> $nama_farm[0]->NAMA_FARM
				);
				$html 		= $this->load->view('report/laporan_bapd/kodebox_bapd_pdf', $dataPDF, true);
				$pdf->AddPage();
				$pdf->writeHTML ( $html, true, false, true, false, '' );
				/*$kode_farm = $get_kodefarm;
				$kodebox = $this->lap_bapd->get_kode_box($kode_farm)->result_array();
				$this->output->set_content_type('application/json')->set_output(json_encode($kodebox));*/
			}
		}
		
		$pdf->Output('kodebox_bapd.pdf', 'I');
	}

	
	public function detailInformasi(){
		$this->load->model('report/m_report', 'report');
		$tahun = $this->input->get('tahun');
		$data['tipe'] = $this->input->get('tipe');
		$data['pilih_farm'] = $this->input->get('farm');
		$farm = $this->report->listFarm($this->input->get('farm'));
		
		$data['kandang']['kodefarm'] = $farm; 
		$data['kandang']['content'] = $this->report->detailInformasi($farm,$tahun)->result_array();
		$this->load->view('report/laporan_bapd/detailInformasi',$data);
	}
}
