<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Kontrol_pemakaian_penjualan_glangsing extends MY_Controller {
	protected $result = array('status' => 0, 'content'=>'', 'message'=> '');
	function __construct(){
		parent::__construct();
		$this->load->helper('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->load->model('report/m_kontrol_pemakaian_penjualan_glangsing','mkppg');	
	}	

	public function index(){
		$this->load->model('report/m_report','report');
		$data = array(
			'list_farm' => $this->report->list_farm(NULL,'bdy')->result()
		);			
		$this->load->view('report/'.$this->grup_farm.'/kontrol_pemakaian_penjualan_glangsing',$data);
	}
	
	public function listSiklus($farm){			
		$this->result['content'] = $this->mkppg->listSiklus($farm)->result();
		$this->result['status'] = 1;		
		echo json_encode($this->result);
	}

	public function listBudgetGlangsing(){		
		$kodesiklus = $this->input->post('kodeSiklus');
		$nextsiklus = $this->input->post('nextSiklus');
		$farm = $this->input->post('kodeFarm');
		$budgetSiklus = $this->mkppg->listBudget($kodesiklus)->result();
		$content = array();
		$viewBudget = '';
		if(!empty($budgetSiklus)){
			foreach($budgetSiklus as $bs){
				$permintaanGlangsing = $this->mkppg->resumePpsk($kodesiklus,$bs->kode_budget)->row();
				$awalSiklus =  $this->mkppg->getAwalSiklus($kodesiklus)->row_array();
				$nextSiklus = empty($nextsiklus) ? array('awal_siklus' => tglSetelah(date('Y-m-d'),1)) : $this->mkppg->getAwalSiklus($nextsiklus)->row_array();
				$so = $this->mkppg->getSO($awalSiklus['awal_siklus'],$nextSiklus['awal_siklus'],$bs->kode_budget,$farm)->result();				
				$resumeSO = array('so' => 0, 'verifikasi_pembayaran' => 0, 'sj' => 0);
				if(!empty($so)){
					foreach($so as $s){
						$resumeSO['so'] += $s->jumlah;
						if($s->status_order == 'A'){
							$resumeSO['verifikasi_pembayaran'] += $s->jumlah;
							$resumeSO['sj'] += $s->jumlah_sj;
						}
					}
				}
				$detailPpsk = $this->mkppg->detailPpsk($kodesiklus,$bs->kode_budget)->result();
				$detailSO = $this->mkppg->detailSO($awalSiklus['awal_siklus'],$nextSiklus['awal_siklus'],$bs->kode_budget,$farm)->result();
				$rowPpsk = count($detailPpsk);
				$rowSO = count($detailSO); 
				$totalRow = $rowPpsk > $rowSO ? $rowPpsk : $rowSO;
				$tmp = array(					
					'total_budget' => $bs->jml_order,
					'ppsk' => $permintaanGlangsing,
					'so' => $resumeSO,
					'detailPpsk' => $detailPpsk,
					'detailSO' => $detailSO,
					'totalRow' => $totalRow					
				);
				switch($bs->kode_budget){
					case 'GB':
						$viewBudget = 'detail_budget_gb';
						break;
					default:
						$viewBudget = 'detail_budget';	
				}
				
								
				$content[$bs->nama_barang] = $this->load->view('report/bdy/'.$viewBudget,$tmp,true);
			}
		}
		
		$this->result['content'] = $content;
		$this->result['status'] = 1;		
		echo json_encode($this->result);
	}

	public function detailPpsk(){
		$ppsk = $this->input->get('ppsk');
		$data = array(
			'detailPengembalian' => $this->mkppg->detailPengembalian($ppsk)->result()
		);
		$this->load->view('report/bdy/detail_ppsk',$data);		
	}

}
