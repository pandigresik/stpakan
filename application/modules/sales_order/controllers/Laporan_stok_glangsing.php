<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Laporan_stok_glangsing extends MY_Controller {
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
		
		$level_user = $this->session->userdata('level_user');
		$this->load->model('sales_order/m_pengajuan_harga','ph');
		$this->load->model('permintaan_glangsing/m_ppsk','ppsk');		
		$this->load->model('sales_order/m_laporan_stok_glangsing','lsg');		
			
		$this->akses = array(
			'LOG' => 'create',
			'KDLOG' => 'review',
			'KVLOG' => 'review',
			'KDV' => 'review',
		);
		$this->tombol = array(
			'create' => '<button class="btn btn-default" onclick="laporanStokGlangsing.refresh(this)">Refresh</button>
						<button class="btn btn-default" disabled onclick="laporanStokGlangsing.detail(this)">Detail</button>
						<button class="btn btn-default" disabled onclick="laporanStokGlangsing.openSODOPage(this,\'N\')">Buat SO</button>',
			'review' => '<button class="btn btn-default" onclick="laporanStokGlangsing.refresh(this)">Refresh</button>
						<button class="btn btn-default" disabled onclick="laporanStokGlangsing.detail(this)">Detail</button>
						',			
		
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
			case 'KDLOG':
				$this->KDLOG();
				break;
			case 'KVLOG':
				$this->KVLOG($kode_farm);
				break;
			case 'KDV':
				$this->KVLOG($kode_farm);
				break;
			case 'LOG':
				$this->LOG($kode_farm);
				break;
			default :
				echo 'Halaman ini bukan untuk anda ';
		}
	}

	public function LOG($kode_farm = null){
		$list_laporan = $this->listLaporan($kode_farm,NULL,TRUE);
		$tglSekarang = date('Y-m-d');
		$data = array(
			'list_laporan'	=> $list_laporan,
			'tgl_sekarang'  => tglIndonesia($tglSekarang,'-',' ')
		);

		$this->load->view('sales_order/'.$this->grup_farm.'/laporan_stok_glangsing',$data);
	}

	public function KDLOG($kode_farm = null){
		$this->LOG($kode_farm);
	}

	public function KVLOG($kode_farm = null){
		$this->LOG($kode_farm);
	}

	public function listLaporan($kode_farm = NULL,$tglTransaksi = NULL, $return = FALSE){	
		$this->load->model('sales_order/m_sales_order','mso');	
		if(empty($tglTransaksi)){
			$tglTransaksi = $this->input->get('tgl_awal');
		}				
		if(empty($tglTransaksi)){
			$tglTransaksi = date('Y-m-d');
		}				
		if(empty($kode_farm)){
			$kode_farm =  $this->input->get('kode_farm');
		}
				
		$show_outstanding =  $this->input->get('show_outstanding');		
		if($show_outstanding == ''){
			$show_outstanding = 1;
		}
		$show_all = $this->input->get('show_all');
		$hariIni = date('Y-m-d');
		$datalist = array(			
		//	'list_estimasi_jumlah' => $tglTransaksi == $hariIni ? $this->ubahArrayGlangsing($this->lsg->getEstimasiStokTerakhir($kode_farm)) : $this->ubahArrayGlangsing($this->lsg->getEstimasiStok($kode_farm,$tglTransaksi)),			
			'list_estimasi_jumlah' => $this->ubahArrayGlangsing($this->lsg->getEstimasiStok($kode_farm,$tglTransaksi)),			
			'list_farm' => $this->ph->getListFarm($this->_user),			
			'so_do_harian' => $this->ubahArrayKeySO($this->mso->getSalesOrderHarian($kode_farm,NULL,$tglTransaksi)),
			'so_do_verifikasi' => $this->ubahArrayKeySO($this->mso->getSalesOrderHarian($kode_farm,NULL,$tglTransaksi,'A')),
			'so_do_sj' => $this->ubahArrayKeySO($this->mso->getSalesOrderSJ($kode_farm,NULL,$tglTransaksi)),			
		//	'so_do_pengurang' => $this->ubahArrayKeySO($this->mso->getSalesOrderPengurang($kode_farm,NULL,$tglTransaksi)),
			'tombol' => $this->tombol[$this->akses[$this->_user_level]],			
			'level_user' => $this->_user_level,
			'show_outstanding' => $show_outstanding
		);
		
		if($return){
			return $this->load->view('sales_order/'.$this->grup_farm.'/list_laporan_stok_glangsing',$datalist,TRUE);
		}else{
			$this->load->view('sales_order/'.$this->grup_farm.'/list_laporan_stok_glangsing',$datalist);
		}
	}

	public function detailSO(){
		$this->load->model('sales_order/m_sales_order','mso');
		$tgl_awal = $this->input->get('tgl_awal');
		$kode_farm = $this->input->get('kode_farm');
		$listSO = $this->mso->get_many_by(array('tgl_so'=>$tgl_awal, 'kode_farm' => $kode_farm));
		$data = array(
			'listSO' => $listSO
		);
		$this->load->view('sales_order/'.$this->grup_farm.'/detail_SO',$data);
	}

	public function detailTabelSO(){
		$this->load->model('sales_order/m_sales_order','mso');
		$this->load->model('sales_order/m_sales_order_d','msod');
		$this->load->model('sales_order/m_log_sales_order','mlso');
		$this->load->model('sales_order/m_surat_jalan','msj');
		$no_so = $this->input->get('no_so');
		
		$header = $this->mso->get($no_so);
		$detailSO = $this->msod->get_many_by(array('no_so'=>$no_so));
		$logSO = $this->mlso->order_by('no_urut','desc')->get_many_by(array('no_so'=>$no_so));
		$farm = $this->db->where(array('kode_farm' => $header->kode_farm))->get('m_farm')->row();
		$pelanggan = $this->db->where(array('kode_pelanggan' => $header->kode_pelanggan))->get('m_pelanggan')->row();
		$barang = $this->db->select(array('nama_barang','kode_barang'))->where(array('grup_barang' => '000117'))->get('m_barang')->result_array();
		$pegawai = $this->db->select(array('nama_pegawai','kode_pegawai'))->get('m_pegawai')->result_array();		
		$surat_jalans = $this->msj->get_many_by(array('no_so' => $no_so));
		$data = array(
			'nama_pelanggan' => $pelanggan->NAMA_PELANGGAN,
			'nama_farm' => $farm->NAMA_FARM,
			'header' => $header,
			'detail' => $detailSO,
			'barang' => arr2DToarrKey($barang,'kode_barang'),
			'pegawai' => arr2DToarrKey($pegawai,'kode_pegawai'),
			'surat_jalans' => $surat_jalans,
			'log' => $logSO
		);
		$this->load->view('sales_order/'.$this->grup_farm.'/detail_tabel_SO',$data);
	}
	

	private function ubahArrayKeySO($arr){		
		$result = array();
		if(empty($arr)){
			return $result;
		}
				
		foreach($arr as $y){
			$kf = $y['kode_farm'];
			$kode_siklus = $y['kode_siklus'];
			$kode_barang = $y['kode_barang'];
			if(!isset($result[$kf])){
				$result[$kf] = array();				
			}
			if(!isset($result[$kf][$kode_siklus])){
				$result[$kf][$kode_siklus] = array();							
			}
			if(!isset($result[$kf][$kode_siklus][$kode_barang])){
				$result[$kf][$kode_siklus][$kode_barang] = $y;							
			}
		}		
		return $result;
	}	

	private function ubahArrayGlangsing($arr){	
		
		$result = array();
		if(empty($arr)){
			return $result;
		}
				
		foreach($arr as $y){
			$kf = $y['kode_farm'];
			$kode_siklus = $y['kode_siklus'];
			if(!isset($result[$kf])){
				$result[$kf] = array();				
			}
			if(!isset($result[$kf][$kode_siklus])){
				$result[$kf][$kode_siklus] = array();							
			}
			array_push($result[$kf][$kode_siklus],$y);
		}		
		return $result;
	}	

}
