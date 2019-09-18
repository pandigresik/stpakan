<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Verifikasi_penerimaan_uang extends MY_Controller {
	protected $result;
	protected $_user;
	private $grup_farm;
	private $akses;
	private $tombol;
	private $checkbox;
	private $dbSqlServer;
	private $_statusPPSK;
	private $_orderTabel;

	public function __construct() {
		parent::__construct ();
		$this->load->helper('stpakan');
		$this->dbSqlServer = $this->load->database('default', TRUE);
		$level_user = $this->session->userdata('level_user');
		$this->load->model('sales_order/m_pengajuan_harga','ph');
		$this->load->model('permintaan_glangsing/m_ppsk','ppsk');
		$this->load->model('master/m_farm','mf');
		$this->load->model('master/m_pelanggan','mp');
		$this->load->model('sales_order/m_sales_order','so');
		$this->load->model('sales_order/m_sales_order_d','sod');
		$this->load->model('sales_order/m_log_sales_order','logso');
		
		$this->akses = array(            
            'KSR' => 'verifikasi',
			'KDKEU' => 'verifikasi',
			'WKKEU' => 'verifikasi'
		);
		$this->tombol = array(
			'create' => '<div id="btn1"><button class="btn btn-default" onclick="salesOrder.openLaporanStokGlangsingPage()">Kembali</button>
						<button class="btn btn-primary" id="addNewSO" onclick="salesOrder.addNewSO()">Tambah SO</button>
						<button class="btn btn-default" disabled onclick="pengajuanHarga.submit(this,\'N\')">Cetak SO</button>
						<button class="btn btn-default" disabled onclick="pengajuanHarga.submit(this,\'N\')">Cetak DO</button></div>
						<div id="btn2"><button class="btn btn-default" onclick="salesOrder.openLaporanStokGlangsingPage()">Kembali</button>
						<button class="btn btn-primary" id="addNewSO" onclick="salesOrder.clickSubmit()">Simpan</button></div>',
			'review' => '<button class="btn btn-primary" disabled onclick="pengajuanHarga.submit(this,\'R1\')"><i class="glyphicon glyphicon-ok"></i> Approve</button>
							<button class="btn btn-default" disabled onclick="pengajuanHarga.submit(this,\'RJ\')"><i class="glyphicon glyphicon-remove"></i> Reject</button>',
			'verifikasi' => '<button class="btn btn-default" onclick="VerifikasiPU.refreshPage(this)"> Kembali</button>
							<button class="btn btn-default" onclick="VerifikasiPU.refreshPage()">Refresh</button>
							<button class="btn btn-primary" disabled onclick="VerifikasiPU.verifikasi(this)"><i class="glyphicon glyphicon-ok"></i> Verifikasi Pembayaran</button>',
		);
		$this->checkbox = array(
				'KVLOG' => array(
					'N'	 => "<input onclick='pengajuanHarga.check_button(this)' class='check_hrg' type='checkbox'/>",
					'R1' => 'Dikoreksi',
					'A'  => 'Disetujui',
		            'RJ' => "Ditolak",
				),
				'KDV' => array(
					'N'	 => 'Dibuat',
					'R1' => "<input onclick='pengajuanHarga.check_button(this)' class='check_hrg' type='checkbox'/>",
					'A'  => 'Disetujui',
		            'RJ' => "Ditolak",
				),
				'KDLOG' => array(
					'N'	 => 'Dibuat',
					'R1' => 'Dikoreksi',
					'A'  => 'Disetujui',
		            'RJ' => "Ditolak",
				),
			);
		$this->_user = $this->session->userdata ( 'kode_user' );
        $this->_user_level = $this->session->userdata('level_user');

     

		$this->result = array (
			'status' => 0,
			'content' => ''
		);


		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));

	}

	public function index($kode_farm = null){
		$level_user = $this->session->userdata('level_user');
		switch($level_user){
			case 'KDKEU':
				$this->KDKEU($kode_farm);
				break;
			case 'WKKEU':
				$this->KDKEU($kode_farm);
				break;	
			case 'KSR':
				$this->KSR($kode_farm);
				break;
			default :
				echo 'Halaman ini bukan untuk anda ';
		}
	}
	public function KSR($kode_farm = NULL){
		$tglSekarang = date('Y-m-d');
		$day = date('N');

		$sales_order_header = $this->loadPage();

		$data = array(
			'tombol' => $this->tombol[$this->akses[$this->_user_level]],
			'kode_farm' => $kode_farm,
			'sales_order_header'	=> $sales_order_header,
		);

		echo $this->load->view('sales_order/'.$this->grup_farm.'/verifikasi_penerimaan_uang',$data, true);
	}
	public function KDKEU($kode_farm = NULL){
		$tglSekarang = date('Y-m-d');
		$day = date('N');	
		$sales_order_header = $this->loadPage();

		$data = array(
			'tombol' => $this->tombol[$this->akses[$this->_user_level]],
			'kode_farm' => $kode_farm,
			'sales_order_header'	=> $sales_order_header,
		);

		echo $this->load->view('sales_order/'.$this->grup_farm.'/verifikasi_penerimaan_uang',$data, true);
	}

	public function verifikasiSO(){
		$tmp_data = $this->input->post('data');
        $so_data = json_decode($tmp_data,true);
	
		$result = $this->so->simpan_verifikasi_pembayaran($so_data);
		echo json_encode($result);

	}

	public function loadPage()
	{
		$start_date = $this->input->post('startDate');
		$end_date 	= $this->input->post('endDate');
		$is_ajax 	= $this->input->post('isAjax');
		$datalist = array(
	
			'list_so' 	  => $this->so->getSOList($start_date, $end_date, $this->_user_level),
			'status_list' => $this->logso->getLogSO($this->so->getSOList($start_date, $end_date, $this->_user_level)),	
			'level_user'  => $this->_user_level,
		);
		if ($is_ajax == true) {
			echo $sales_order_header = $this->load->view('sales_order/'.$this->grup_farm.'/list_verifikasi_penerimaan_uang_header',$datalist,TRUE);
		}else {
			return $sales_order_header = $this->load->view('sales_order/'.$this->grup_farm.'/list_verifikasi_penerimaan_uang_header',$datalist,TRUE);
		}
	}

}
