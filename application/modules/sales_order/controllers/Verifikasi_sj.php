<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Verifikasi_sj extends MY_Controller {
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

		/*Edited by Muslam (Edit Jabatan)*/
		$this->akses = array(
			'SCF' => 'verifikasi'
		);
		$this->tombol = array(
			'verifikasi' => '<button class="btn btn-primary" disabled onclick="VerifikasiSJ.verifikasi(this)"><i class="glyphicon glyphicon-ok"></i> Verifikasi</button>',
		);
		$this->_user = $this->session->userdata ( 'kode_user' );
        $this->_user_level = $this->session->userdata('level_user');

        //cetak_r( $this->_user_level);

		$this->result = array (
			'status' => 0,
			'content' => ''
		);

	//	$this->load->model('pengembalian_sak/m_pengembalian_sak','mps');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));

	}
    public function index($kode_farm = null){
		$level_user = $this->session->userdata('level_user');
        // cetak_r($this->session->userdata);
        $kode_farm = (empty($kode_farm)) ? $this->session->userdata('kode_farm') : $kode_farm;
        // echo $kode_farm;
		switch($level_user){
			case 'SCF':
				$this->SCF($kode_farm);
				break;
			default :
				echo 'Halaman ini bukan untuk anda ';
		}
	}

    public function SCF($kode_farm = NULL){
		$tglSekarang = date('Y-m-d');
		$day = date('N');

		$datalist = array(
			//'list_pengajuan' => $this->ph->listPengajuan(array()),
			'tombol' => $this->tombol[$this->akses[$this->_user_level]],
			//'checkbox' => $this->checkbox[$this->_user_level],
			'level_user' => $this->_user_level,
			'kode_farm' => $kode_farm
		);
		$sales_order_header = $this->load->view('sales_order/'.$this->grup_farm.'/list_verifikasi_penerimaan_uang_header',$datalist,TRUE);

		$data = array(
			'sales_order_header' => $sales_order_header
		);

		echo $this->load->view('sales_order/'.$this->grup_farm.'/verifikasi_sj',$data, true);
	}
    public function check_nomor_sj(){
        $no_sj = $this->input->post('no_sj');
		$this->load->model('sales_order/m_surat_jalan','msj');
		$content = $this->msj->check_nomor_sj($no_sj)->result_array();

        // cetak_r($content);
		$error_msg = '';
		$error = 0;
		if (empty($content)) {
			$error_msg = 'Nomor Surat Jalan tidak dikenali';
			$error = 1;
		}else {
			$content = $content[0];
		}
		display_json(array('content'=>$content, 'error'=>$error, 'error_msg'=>$error_msg));
    }
    public function load_page(){
		$this->load->model('sales_order/m_surat_jalan_d','msjd');
		$data['data_sj'] = $this->input->post('data_sj');
		$data['data_detail_sj'] = $this->msjd->get_many_by(array('no_sj' => $this->input->post('no_sj')));
		// cetak_r($data);
		// $data['list_detail_sj'] = $this->load->view('telurafkir/penerimaan/list_view.php',$data,true);
		$this->load->view('sales_order/'.$this->grup_farm.'/list_verifikasi_sj',$data);
    }
	public function simpan_verifikasi(){
		$this->load->model('sales_order/m_surat_jalan','msj');
		$no_sj = $this->input->post('no_sj');

		$update_sj = $this->msj->update($no_sj, array( 'tgl_verifikasi_security' => date('Y-m-d H:i:s'), 'user_verifikasi_security' => $this->_user));
		if($update_sj){
			$result['success'] = 1;
		}else {
			$result['success'] = 0;
		}
		echo display_json($result);
	}
}
