<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Perencanaan_awal extends MY_Controller {
	
	public function __construct() {
        parent::__construct ();
        $this->load->model('report/m_report','report');
		$this->load->model('report/m_report_kontrol_stok','mrks');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		
	}

	public function index() {
		$this->result['status'] = 1;
		$eventKandangKlik = 'KSP.showDetailPerencanaanAwal(this)';
		$header = '<div class="alert alert-warning text-center" style="font-size:120%;color:#000">Perencanaan Awal Siklus</div>';
        $this->result['content'] = $header. summaryFarm($this->mrks->list_kandang_all(array('ks.status_siklus' => 'O')),'onclick="KSP.showKandang(this,\''.$eventKandangKlik.'\')"');
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
    }

    public function detail(){
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$noreg = $this->input->post('noreg');
		$periode_siklus_arr = explode('/', $noreg);
		$kode_farm = substr($noreg,0,2);
		$periode_siklus = $periode_siklus_arr[1];
		$tgl_docin = $this->input->post('tgl_docin');
		$jenisBarangRencanaPengiriman = $this->mrks->getJenisBarangRencanaPengiriman();
		$rencanaPengirimanRaw = $this->mrks->getRencanaPengiriman($noreg);
		$kandang_siklus = $this->db->select(array('kode_siklus','flok_bdy'))->where(array('no_reg' => $noreg))->get('kandang_siklus')->row_array();
		$kode_siklus = $kandang_siklus['kode_siklus'];
		$flok_bdy = $kandang_siklus['flok_bdy'];
		$jumlahKandangPerFlok = $this->db->where(array('kode_siklus' => $kode_siklus,'flok_bdy' => $flok_bdy))->get('kandang_siklus')->num_rows();
		$pengajuan_budget = $this->mrks->getPengajuanBudget($kode_siklus);
		$plotting_pelaksana = $this->mrks->getPlottingPelaksana($noreg);
		$aktivasi_siklus = $this->mrks->getAktivasiSiklus($noreg, $kode_siklus);
		
		foreach ($rencanaPengirimanRaw as $keyRencanaPengirimanRaw=>$valRencanaPengirimanRaw) {
			$tgl_kirim = $valRencanaPengirimanRaw['TGL_KIRIM'];
			$tgl_kebutuhan = $valRencanaPengirimanRaw['TGL_KEBUTUHAN'];
			$kode_barang = $valRencanaPengirimanRaw['KODE_BARANG'];
			if(!isset($rencanaPengiriman[$tgl_kirim][$tgl_kebutuhan])){
				$rencanaPengiriman[$tgl_kirim][$tgl_kebutuhan] = array(
					'umur'=>$valRencanaPengirimanRaw['umur'],
				);
			}
			$rencanaPengiriman[$tgl_kirim][$tgl_kebutuhan][$kode_barang] = array(
				'jumlah'=> ($valRencanaPengirimanRaw['JML_FORECAST'] / $jumlahKandangPerFlok ),
			);
		}
		
		
		$data['tgl_docin'] = $tgl_docin;
		$data['periode_siklus'] = $periode_siklus;
		$data['jenisBarangRencanaPengiriman'] = $jenisBarangRencanaPengiriman;
		$data['rencanaPengiriman'] = $rencanaPengiriman;
		$data['pengajuan_budget'] = $pengajuan_budget;
		$data['plotting_pelaksana'] = $plotting_pelaksana;
		$data['aktivasi_siklus'] = $aktivasi_siklus;
		$data['logPengajuanBudget'] = $this->mrks->log_budget_glangsing($kode_siklus);
		$data['status_plotting_pelaksana'] = $this->mrks->log_ploting_pelaksana($noreg);
		$data['kode_siklus'] = $kode_siklus;
		$data['flok_bdy'] = $flok_bdy;
		$data['kode_farm'] = $kode_farm;
		$data['status_timbang'] = arr2DToarrKey($this->mrks->status_timbang_pallet($kode_siklus,$kode_farm),'jenis');
		$data["tbl_detail_rencana_pengiriman"] = $this->load->view('report/bdy/kontrol_stok_pakan/tbl_detail_rencana_pengiriman',$data,true);
		$data["tbl_detail_pengajuan_budget"] = $this->load->view('report/bdy/kontrol_stok_pakan/tbl_detail_pengajuan_budget',$data,true);
		$data["tbl_detail_plotting_pelaksana"] = $this->load->view('report/bdy/kontrol_stok_pakan/tbl_detail_plotting_pelaksana',$data,true);
		$data["tbl_detail_perencanaan_awal"] = $this->load->view('report/bdy/kontrol_stok_pakan/tbl_detail_perencanaan_awal',$data, true);
		
		$this->load->view('report/bdy/kontrol_stok_pakan/detail_perencanaan_awal',$data);
		
	}
	
	public function detail_pallet(){
		$kode_siklus = $this->input->post('kode_siklus');
		$kode_farm = $this->input->post('kode_farm');
		$jenis = $this->input->post('jenis');
		$details = $this->mrks->detail_timbang_pallet($kode_siklus,$kode_farm,$jenis);
		$viewDetail = $jenis == 'pallet' ? 'detail_pallet' : 'detail_hand_pallet';
		$this->load->view('report/bdy/kontrol_stok_pakan/'.$viewDetail,array('data' => $details));

	}
}    
