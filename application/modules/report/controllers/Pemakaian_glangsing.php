<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Pemakaian_glangsing extends MY_Controller {
	
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_report','report');
		$this->load->model('report/m_report_kontrol_stok','mrks');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		
	}

	public function index() {
		$this->result['status'] = 1;
		$eventKandangKlik = 'KSP.showDetailPemakaianGlangsing(this)';
		$header = '<div class="alert alert-warning text-center" style="font-size:120%;color:#000">Realisasi Pemakaian dan Penjualan Glangsing</div>';
        $this->result['content'] = $header. summaryFarm($this->mrks->list_kandang_all(array('ks.status_siklus' => 'O')),'onclick="KSP.showKandang(this,\''.$eventKandangKlik.'\')"');
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	
	public function overview_glangsing(){
		$this->load->model('report/m_kontrol_pemakaian_penjualan_glangsing','mkppg');	
		$kodesiklus = $this->input->post('kode_siklus');
		$kode_farm = $this->input->post('kode_farm');
		$budgetSiklus = $this->mkppg->listBudget($kodesiklus)->result();
		$content = array();
		
		if(!empty($budgetSiklus)){
			foreach($budgetSiklus as $bs){
				$pemusnahan = 0;
				$permintaanGlangsing = $this->mkppg->resumePpsk($kodesiklus,$bs->kode_budget)->row();
				$awalSiklus =  $this->mkppg->getAwalSiklus($kodesiklus)->row_array();
				$nextSiklus = empty($nextsiklus) ? array('awal_siklus' => tglSetelah(date('Y-m-d'),1)) : $this->mkppg->getAwalSiklus($nextsiklus)->row_array();

				if($bs->kode_budget == 'GB'){
					$sql_subquery = $this->db->select('no_ppsk')->where(array('kode_siklus' => $kodesiklus,'kode_budget' => 'GB'))->get_compiled_select('ppsk_new');
					$pemusnahan = $this->db->select_sum('jml')->where('no_ppsk in ('.$sql_subquery.')')->get('ba_pemusnahan')->row_array();
					$pemusnahan = $pemusnahan['jml'];
				}
				$so = $this->mkppg->getSO($awalSiklus['awal_siklus'],$nextSiklus['awal_siklus'],$bs->kode_budget,$kode_farm)->result();				
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
				
				$tmp = array(					
					'total_budget' => $bs->jml_order,
					'ppsk' => $permintaanGlangsing,
					'so' => $resumeSO,
					'pemusnahan' => $pemusnahan
				);
				
				$content[$bs->nama_barang] = $tmp;
			}
		}
		$this->load->view('report/bdy/kontrol_stok_pakan/overview_glangsing',array('content' => $content));
	}

	public function detail(){
		$noreg = $this->input->post('noreg');
		$kode_farm = substr($noreg,0,2);
		$tgl_docin = $this->input->post('tgl_docin');
		$sql_subquery = $this->db->select('kode_siklus')->where(array('no_reg' => $noreg))->get_compiled_select('kandang_siklus');
		$tgl_docin_awal = $this->db->select_min('tgl_doc_in')->where('kode_siklus = ('.$sql_subquery.')')->get('kandang_siklus')->row_array();
		$tgl_docin = $tgl_docin_awal['tgl_doc_in'];
		//$tgl_docin = '2018-01-01';
		$list_ppsk = $this->mrks->list_ppsk($noreg,$tgl_docin);
		$log_ppsk = simpleGrouping($this->mrks->log_ppsk($noreg),'no_ppsk');
		$so = $this->mrks->sales_order($tgl_docin,$kode_farm);
		$log_so = simpleGrouping($this->mrks->log_sales_order($tgl_docin,$kode_farm),'no_so');
		
		$pengajuan_harga = arr2DToarrKey($this->mrks->pengajuan_harga($tgl_docin,$kode_farm),'tgl_pengajuan');
		
		$log_pengajuan_harga = simpleGrouping($this->mrks->log_pengajuan_harga($tgl_docin,$kode_farm),'no_pengajuan_harga');
		$pengajuan_harga_d = simpleGrouping($this->mrks->pengajuan_harga_d($tgl_docin,$kode_farm),'no_pengajuan_harga');

		$data = array(
			'page1' => $this->load->view('report/bdy/kontrol_stok_pakan/dpg_page1.php',array('ppsks' => $list_ppsk,'logs' => $log_ppsk, 'kode_farm' => $kode_farm),TRUE),
			'page2' => $this->load->view('report/bdy/kontrol_stok_pakan/dpg_page2.php',array('ph' => $pengajuan_harga,'phd' => $pengajuan_harga_d,'log_ph' => $log_pengajuan_harga,'so' => $so,'logs' => $log_so, 'kode_farm' => $kode_farm),TRUE),
			'tgldocin' => $tgl_docin,
			'maxPage' => 2
		);
		$this->load->view('report/bdy/kontrol_stok_pakan/detail_pemakaian_glangsing',$data);
	}
}    
