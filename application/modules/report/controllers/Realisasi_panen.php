<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Realisasi_panen extends MY_Controller {
	
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_report','report');
		$this->load->model('report/m_report_kontrol_stok','mrks');
		$this->load->model('report/m_realisasi_doc', 'm_model');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		
	}

	public function index() {
		$this->result['status'] = 1;
		$eventKandangKlik = 'KSP.showDetailRealisasiPanen(this)';
		$header = '<div class="alert alert-warning text-center" style="font-size:120%;color:#000">Realisasi Panen</div>';
        $this->result['content'] =$header. summaryFarm($this->mrks->list_kandang_all(array('ks.status_siklus' => 'O')),'onclick="KSP.showKandang(this,\''.$eventKandangKlik.'\')"');
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	
	public function detail(){
		$this->load->model('riwayat_harian_kandang/m_ploting_pelaksana','mpp');
		$this->load->model('report/m_kontrol_stok_pakan','mksp');
		$noreg		= $this->input->post('noreg');
		$tgl_docin	= $this->input->post('tgl_docin');
		$flag_panen = TRUE;
		$data_rhk 	= $this->mksp->rhk_pakan($noreg,$flag_panen);
		$data_panen	= simpleGrouping($this->mksp->panen($noreg),'tgl_panen');
		$rhks = Modules::run('report/rencana_realisasi_pemakaian/groupingTglKodebarang',$data_rhk);
		$pengawas = $this->mpp->as_array()->get_by(array('no_reg' => $noreg));
		$namaPengawas = $this->db->where(array('kode_pegawai' => $pengawas['PENGAWAS']))->get('m_pegawai')->row_array();
		//$stokHarianKandang = Modules::run('report/rencana_realisasi_pemakaian/stokPerTglLhk',$this->mksp->stokHarianKandang($noreg));
		$data = array(
			'page1' => $this->load->view('bdy/kontrol_stok_pakan/panen/page1',array('rhks' => $rhks,'panen' => $data_panen , 'noreg' => $noreg),TRUE),
			'page2' => $this->load->view('bdy/kontrol_stok_pakan/panen/page2',array('rhks' => $rhks,'panen' => $data_panen , 'noreg' => $noreg),TRUE),
			'page3' => $this->load->view('bdy/kontrol_stok_pakan/panen/page3',array('rhks' => $rhks,'panen' => $data_panen , 'noreg' => $noreg,'pengawas' => $namaPengawas['NAMA_PEGAWAI']),TRUE),
			'tgldocin' => $tgl_docin,
			'maxPage' => 3
		);
		$this->load->view('bdy/kontrol_stok_pakan/detail_realisasi_panen', $data);
	}

	private function groupingTglKodebarang($data){
		$result = array();
		if(!empty($data)){
			foreach($data as $d){
				$tgl = $d['tgl_kebutuhan'];
				$kb = $d['kode_barang'];
				if(!isset($result[$tgl])){
					$result[$tgl] = array();
				}
				if(!isset($result[$tgl][$kb])){
					$result[$tgl][$kb] = array();
				}
				$result[$tgl][$kb][] = $d;
			}
		}
		return $result;
	}
}    
