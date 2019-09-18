<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );

class Realisasi_penjualan extends MY_Controller {
	protected $result;
	protected $_user;
	private $grup_farm;	
	private $tombol;	

	public function __construct() {
		parent::__construct ();
		$this->load->helper('stpakan');
		
		$level_user = $this->session->userdata('level_user');					
		$this->tombol = array(
			'create' => '<button class="btn btn-default" data-url="'.site_url('sales_order/pemusnahan_bangkai/index').'" onclick="realisasiPenjualan.goto(this)"><i class="glyphicon glyphicon-warning-sign"></i> BA Pemusnahan</button>
						<button class="btn btn-default simpansj" disabled onclick="realisasiPenjualan.simpan(this)">Simpan</button>
						<button class="btn btn-default cetaksj" disabled onclick="realisasiPenjualan.cetakSJ(this)">Cetak SJ</button>
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
			'tombol' => $this->tombol['create'],	
			'tgl_sekarang' => tglIndonesia(date('Y-m-d'),'-',' ')		
		);
		$this->load->view('sales_order/'.$this->grup_farm.'/realisasi_penjualan',$data);
	}

	public function listDO(){		
		$startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		$status =  $this->input->get('status');
		$params = array(
			'start_date' => $startDate,
			'end_date' => $endDate,
			'status' => $status
		);	
		$this->load->model('sales_order/m_surat_jalan','msj');
		
		$data = array(
			'surat_jalan' => $this->msj->list_do($params)->result()	
		);		
		$this->load->view('sales_order/'.$this->grup_farm.'/realisasi_listdo',$data);		
	}	
	public function detailSJ(){		
		$no_sj = $this->input->get('no_sj');				
		$this->load->model('sales_order/m_surat_jalan','msj');
		$this->load->model('sales_order/m_surat_jalan_d','msjd');
		$barang = $this->db->select(array('nama_barang','kode_barang'))->where(array('grup_barang' => '000117'))->get('m_barang')->result_array();
		$data = array(
			'surat_jalan' => $this->msj->get($no_sj),
			'detail' => $this->msjd->get_many_by(array('no_sj' => $no_sj)),
			'barang' => arr2DToarrKey($barang,'kode_barang')
		);
		$this->load->view('sales_order/'.$this->grup_farm.'/realisasi_detail_tabelSJ',$data);		
	}	
	
	public function simpan(){
		$this->load->model('sales_order/m_surat_jalan','msj');
		$this->load->model('sales_order/m_surat_jalan_d','msjd');
		$no_sj = $this->input->post('no_sj');
		$update = $this->msj->update_by(array('no_sj' => $no_sj),array('tgl_realisasi' => date('Y-m-d H:i:s'), 'user_realisasi' => $this->_user));
		if($update){
			$this->result['status'] = 1;
			$this->result['message'] = 'Data pengiriman barang berhasil disimpan ';			
			/* update glangsing_movement dan glangsing_movement_d */
			$this->load->model('pengembalian_sak/m_glangsing_movement','gm');
			$this->load->model('pengembalian_sak/m_glangsing_movement_d','gmd');
			$kodefarm = $this->session->userdata('kode_farm');
			$periode_aktif = $this->db->select('kode_siklus')->where(array('kode_farm' => $kodefarm))->order_by('kode_siklus','desc')->get('glangsing_movement')->row_array();
			$kode_siklus = $periode_aktif['kode_siklus'];
			$detailSJ = $this->msjd->get_many_by(array('no_sj' => $no_sj));
			if(!empty($detailSJ)){
				foreach($detailSJ as $dsj){
					$whereGlangsingBudgetMovement = array('kode_barang' => $dsj->kode_barang,'kode_farm' => $kodefarm,'kode_siklus' => $kode_siklus);
					$stokAkhirGlangsingBudget = $this->gm->get_by($whereGlangsingBudgetMovement);
					$jmlAwal = $stokAkhirGlangsingBudget->jml_stok;
					$jmlAkhir = $jmlAwal - $dsj->jumlah;
					$glangsingMovementBudget = array(
						'kode_farm' => $kodefarm,
						'kode_siklus' => $kode_siklus,
						'kode_barang' => $dsj->kode_barang,
						'no_referensi' => $no_sj,
						'jml_awal' => $jmlAwal,
						'jml_order' => -1 * $dsj->jumlah,
						'jml_akhir' => $jmlAkhir,
						'tgl_transaksi' => date('Y-m-d'),
						'keterangan1' => 'SOLD_OUT',
						'keterangan2' => '',
						'user_buat' => $this->_user,
					);
					$this->gmd->insert($glangsingMovementBudget);					
					$this->gm->update_by($whereGlangsingBudgetMovement,array('jml_stok' => $jmlAkhir));
				}
			}
			
		}else{
			$this->result['message'] = 'Data pengiriman barang gagal disimpan ';
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}

	public function cetakSJ(){
		$no_sj = $this->input->post('no_sj');		
		if(empty($no_sj)){
			$no_sj = $this->input->get('no_sj');		
		}
		$this->sj_pdf($no_sj);
	}

	private function sj_pdf($no_sj) {	
		error_reporting(0);
		$kodefarm = $this->session->userdata('kode_farm');
		$this->load->model('sales_order/m_surat_jalan','msj');
		$this->load->model('sales_order/m_surat_jalan_d','msjd');	
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A4', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );				
		$pdf->SetFontSize(16);		
		$pdf->SetMargins(8, 8, 8, 8); 			
		
		$barang = $this->db->select(array('nama_barang','kode_barang'))->where(array('grup_barang' => '000117'))->get('m_barang')->result_array();
		$sj = $this->msj->get($no_sj);
		$sjd = $this->msjd->get_many_by(array('no_sj' => $no_sj));		
		$dataFarm = $this->db->select('NAMA_FARM,ALAMAT_FARM,KOTA')->where(array('kode_farm' => $kodefarm))->get('m_farm')->row();		
		$params = $pdf->serializeTCPDFtagParameters ( array (
			$no_sj,
			'QRCODE,H',
			'',
			'',
			32,
			32
		) );
		$b = '<tcpdf method="write2DBarcode" params="' . $params . '" />';				
		$html = $this->load->view ( 'sales_order/bdy/cetak_sj_pdf', array (
				'suratJalan' => $sj,
				'detail_sj' => $sjd,
				'barang' => arr2DToarrKey($barang,'kode_barang'),		
				'barcode' => $b,				
				'dataFarm' => $dataFarm,
				
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
}
