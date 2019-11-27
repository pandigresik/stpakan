<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );

class Pemusnahan_bangkai extends MY_Controller {
	protected $result;
	protected $_user;
	private $grup_farm;
	private $tombol;

	public function __construct() {
		parent::__construct ();
		$this->load->helper('stpakan');

		$level_user = $this->session->userdata('level_user');
		$this->tombol = array(
			'create' => '<button class="btn btn-default" data-url="'.site_url('sales_order/realisasi_penjualan/index').'" onclick="pemusnahanBangkai.goto(this)"><i class="glyphicon glyphicon-shopping-cart"></i> Realisasi Penjualan</button>
						',
		);

		$this->_user = $this->session->userdata ( 'kode_user' );
		$this->result = array (
			'status' => 0,
			'content' => ''
		);
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));

	}

	public function index(){
		$data = array(
			'tombol' => $this->tombol['create']
		);
		$this->load->view('sales_order/'.$this->grup_farm.'/pemusnahan_bangkai',$data);
	}

	public function listBA(){
		$startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		$params = array(
			'start_date' => $startDate,
			'end_date' => $endDate,
			'kode_farm' => $this->session->userdata('kode_farm')
		);
		$this->load->model('sales_order/m_ba_pemusnahan','mba');

		$data = array(
			'berita_acara' => $this->mba->list_ba($params)->result()
		);
		$this->load->view('sales_order/'.$this->grup_farm.'/realisasi_listBA',$data);
	}

	public function simpan(){
		$this->load->model('sales_order/m_ba_pemusnahan','mba');
		$jml = $this->input->post('jml');
		$no_ppsk = $this->input->post('no_ppsk');
		$tgl_kebutuhan = $this->input->post('tgl_kebutuhan');
		$keterangan = $this->input->post('keterangan');
		$kode_farm = $this->session->userdata('kode_farm');
		$berita_acara_baru = $this->mba->no_berita_acara($kode_farm);
		$dataInsert = array(
			'no_berita_acara' => $berita_acara_baru,
			'no_ppsk' => $no_ppsk,
			'tgl_kebutuhan' => $tgl_kebutuhan,
			'kode_farm' => $kode_farm,
			'kode_barang' => 'GB',
			'jml' => $jml,
			'keterangan' => $keterangan,
			'user_buat' => $this->_user
		);
		$simpan = $this->mba->insert($dataInsert);
		$this->result['status'] = 1;
		$this->result['content'] = $berita_acara_baru;
		$this->result['message'] = 'Berita Acara Pemusnahan Glangsing Bangkai berhasil di -<i>generate</i>';

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}

	public function detailKandang(){
		$this->load->model('sales_order/m_ba_pemusnahan','mba');
		$no_ppsk = $this->input->post('no_ppsk');
		$data = array(
			'detail_kandang' => $this->mba->detail_kandang($no_ppsk)->result()
		);
		$this->load->view('sales_order/'.$this->grup_farm.'/detail_kandangBA',$data);
	}

	public function cetakBA(){
		$ba = $this->input->post('ba');
		if(empty($ba)){
			$ba = $this->input->get('ba');
		}
		$this->ba_pdf($ba);
	}

	private function ba_pdf($no_ba) {
		error_reporting(0);
		$this->load->model('sales_order/m_ba_pemusnahan','mba');
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'P', PDF_UNIT, 'A4', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );
		$pdf->SetFontSize(8);
		$pdf->SetMargins(8, 8, 8,8);
		$berita_acara = $this->mba->get($no_ba);
		$html = $this->load->view ( 'sales_order/bdy/cetak_ba_pemusnahan_pdf', array (
				'berita_acara' => $berita_acara,
				'berita_acara_d' => $this->mba->detail_kandang($berita_acara->no_ppsk)->result(),
				'farm' => $this->db->select('nama_farm')->where(array('kode_farm' => $berita_acara->kode_farm))->get('m_farm')->row()
		), true );
		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );
		/*
		$marginPage = 3;
		$pdf->Line($marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,$marginPage);
		$pdf->Line($pdf->getPageWidth()-$marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,  $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage, $pdf->getPageHeight()-$marginPage, $pdf->getPageWidth()-$marginPage, $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage,$marginPage,$marginPage, $pdf->getPageHeight()-$marginPage);
		*/
		$pdf->Output ( 'Berita Acara.pdf', 'I' );
	}
}
