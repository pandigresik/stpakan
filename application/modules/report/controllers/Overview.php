<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Overview extends MY_Controller {
	
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
		$header = '<div class="alert alert-warning text-center" style="font-size:120%;color:#000">Overview Approval</div>';
        $this->result['content'] =$header. summaryFarm($this->mrks->list_kandang_all(array('ks.status_siklus' => 'O')),'onclick="KSP.showOverviewApproval(this)"','div_farm_overview');
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	
	public function detail(){
		$this->load->model('report/m_overview','mo');
		$kode_farm	   = $this->input->post('kode_farm');
		$kode_siklus   = $this->input->post('kode_siklus');
		$data_plotting = $this->mo->plotting_pelaksana($kode_siklus,'RV')->result_array();
		$data_pp = simpleGrouping($this->mo->permintaan_pakan($kode_siklus,'RV')->result_array(),'no_lpb');
		$data_plotting_do = $this->groupTglKirimDO($this->mo->plotting_do_pakan($kode_siklus,'R')->result_array());
		$data_ppsk = $this->mo->permintaan_glangsing($kode_farm,'A0')->result_array();
		$data_pengajuan_harga = simpleGrouping($this->mo->pengajuan_harga($kode_farm,'R1')->result_array(),'no_pengajuan_harga');
		$harga_lama = array();
		if(!empty($data_pengajuan_harga)){	
			foreach($data_pengajuan_harga as $_dp){
				$tgl_pengajuan = $_dp[0]['tgl_pengajuan'];
				if(!empty($tgl_pengajuan)) break;
			}
			
			$harga_lama = arr2DToarrKey($this->mo->harga_lama($kode_farm,$tgl_pengajuan)->result_array(),'kode_barang');
		}
		
		$data = array(
			'plotting_pelaksana' => $this->load->view('bdy/kontrol_stok_pakan/overview/plotting_pelaksana',array('header' => 'Plotting Pelaksana','jumlah' => (!empty($data_plotting) ? count(array_unique(array_column($data_plotting,'flok_bdy'))) : 0) , 'data' => $data_plotting, 'kode_siklus' => $kode_siklus),TRUE),
			'pp' => $this->load->view('bdy/kontrol_stok_pakan/overview/pp',array('header' => 'Permintaan Pakan','jumlah' => count($data_pp),'data' => $data_pp, 'kode_siklus' => $kode_siklus),TRUE),
			'plotting_do_pakan' => $this->load->view('bdy/kontrol_stok_pakan/overview/plotting_do_pakan',array('header' => 'Plotting DO Pakan','jumlah' => count($data_plotting_do), 'data' => $data_plotting_do,'kode_farm' => $kode_farm),TRUE),
			'ppsk' => $this->load->view('bdy/kontrol_stok_pakan/overview/ppsk',array('header' => 'Permintaan Glangsing Bekas Pakai','jumlah' => count($data_ppsk), 'data' => $data_ppsk),TRUE),
			'pengajuan_harga' => $this->load->view('bdy/kontrol_stok_pakan/overview/pengajuan_harga',array('header' => 'Pengajuan Harga Glangsing','jumlah' => count($data_pengajuan_harga), 'data' => $data_pengajuan_harga, 'harga_lama' => $harga_lama),TRUE),
		);
		$this->load->view('bdy/kontrol_stok_pakan/overview_farm', $data);
	}

	public function detailPlottingPelaksana(){
		$this->load->model('report/m_overview','mo');
		$kode_farm	   = $this->input->post('kode_farm');
		$kode_siklus   = $this->input->post('kode_siklus');
		$data_plotting = $this->mo->plotting_pelaksana($kode_siklus,'RV')->result_array();
		$this->load->view('bdy/kontrol_stok_pakan/overview/plotting_pelaksana',array('headerMessage' => 'Apakah Anda yakin akan melanjutkan tindak lanjut plotting pelaksana dengan rincian berikut ?', 'data' => $data_plotting, 'kode_siklus' => $kode_siklus));
	}

	public function detailPermintaanPakan(){
		$this->load->model('report/m_overview','mo');
		$kode_farm	   = $this->input->post('kode_farm');
		$kode_siklus   = $this->input->post('kode_siklus');
		$data_pp = simpleGrouping($this->mo->permintaan_pakan($kode_siklus,'RV')->result_array(),'no_lpb');
		$this->load->view('bdy/kontrol_stok_pakan/overview/pp',array('headerMessage' => 'Apakah Anda yakin akan melanjutkan tindak lanjut permintaan pakan dengan rincian berikut ?	','data' => $data_pp, 'kode_siklus' => $kode_siklus));
	}

	public function detailDOPakan(){
		$this->load->model('report/m_overview','mo');
		$kode_farm	   = $this->input->post('kode_farm');
		$kode_siklus   = $this->input->post('kode_siklus');
		$data_plotting_do = $this->groupTglKirimDO($this->mo->plotting_do_pakan($kode_siklus,'R')->result_array());
		$this->load->view('bdy/kontrol_stok_pakan/overview/plotting_do_pakan',array('headerMessage' => 'Apakah Anda yakin akan melanjutkan tindak lanjut plotting DO pakan dengan rincian berikut ?','data' => $data_plotting_do,'kode_farm' => $kode_farm));
	}

	public function detailPengajuanHarga(){
		$this->load->model('report/m_overview','mo');
		$kode_farm	   = $this->input->post('kode_farm');
		$data_pengajuan_harga = simpleGrouping($this->mo->pengajuan_harga($kode_farm,'R1')->result_array(),'no_pengajuan_harga');
		$harga_lama = array();
		if(!empty($data_pengajuan_harga)){	
			foreach($data_pengajuan_harga as $_dp){
				$tgl_pengajuan = $_dp[0]['tgl_pengajuan'];
				if(!empty($tgl_pengajuan)) break;
			}
			
			$harga_lama = arr2DToarrKey($this->mo->harga_lama($kode_farm,$tgl_pengajuan)->result_array(),'kode_barang');
		}
		$this->load->view('bdy/kontrol_stok_pakan/overview/pengajuan_harga',array('headerMessage' => 'Apakah Anda yakin akan melanjutkan tindak lanjut pengajuan harga glangsing dengan rincian berikut ?', 'data' => $data_pengajuan_harga, 'harga_lama' => $harga_lama,'kode_farm' => $kode_farm));
	}

	public function detailPPSK(){
		$this->load->model('report/m_overview','mo');
		$kode_farm	   = $this->input->post('kode_farm');
		$data_ppsk = $this->mo->permintaan_glangsing($kode_farm,'A0')->result_array();
		$this->load->view('bdy/kontrol_stok_pakan/overview/ppsk',array('headerMessage' => 'Apakah Anda yakin akan melanjutkan tindak lanjut permintaan glangsing bekas pakai dengan rincian berikut ?', 'data' => $data_ppsk,'kode_farm' => $kode_farm));
		
	}

	private function groupTglKirimDO($arr){
		$result = array();
		if(!empty($arr)){
			foreach($arr as $r){
				$tglkirim = $r['tgl_kirim'];
				$do = $r['no_do'];
				if(!isset($result[$tglkirim])){
					$result[$tglkirim] = array('rowspan' => 0, 'detail' => array());
				}
				if(!isset($result[$tglkirim]['detail'][$do])){
					$result[$tglkirim]['detail'][$do] = array();
				}
				$result[$tglkirim]['rowspan']++;
				array_push($result[$tglkirim]['detail'][$do],$r);
			}
		}
		return $result;
	}
}    
