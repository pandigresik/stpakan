<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Verifikasi_do extends MY_Controller {

	public function __construct() {
		parent::__construct ();
		$this->load->helper('stpakan');
		$this->dbSqlServer = $this->load->database('default', TRUE);
		
		
	}

	public function index(){
		$this->load->view('sales_order/Verifikasi_do/index', array('a', 'b'));
	}

	public function getDO()
	{
		$text = $this->input->post('no_do');

		$do = $this->dbSqlServer->select('no_do')
			->from('surat_jalan')
			->where('no_do', $text)
			->where('tgl_verifikasi')
			->get()
			->result_object();

		if (!empty($do)) {
			$result = array(
				'status' => 1,
				'content' => $text
			);

			echo json_encode($result);
			exit;
		} else {
			$result = array(
				'status' => 0,
				'content' => $text
			);

			echo json_encode($result);
			exit;
		}
	}

	public function getKendaraan()
	{
		$text = $this->input->post('no_kendaraan');
		$no_do = $this->input->post('no_do');
		$kendaraan = $this->dbSqlServer->select('no_kendaraan')
			->from('surat_jalan')
			->where('no_kendaraan', $text)
			->where('no_do', $no_do)
			->where('tgl_verifikasi')
			->get()
			->result_object();

		if (!empty($kendaraan)) {
			$result = array(
				'status' => 1,
				'content' => $text
			);

			echo json_encode($result);
			exit;
		} else {
			$result = array(
				'status' => 0,
				'content' => $text
			);

			echo json_encode($result);
			exit;
		}
	}

	public function getPin()
	{
		$text = $this->input->post('kode_verifikasi');
		$no_kendaraan = $this->input->post('no_kendaraan');
		$no_do = $this->input->post('no_do');

		$kodever = $this->dbSqlServer->select('kode_verifikasi')
			->from('surat_jalan')
			->where('kode_verifikasi', $text)
			->where('no_do', $no_do)
			->where('no_kendaraan', $no_kendaraan)
			->where('tgl_verifikasi')
			->get()
			->result_object();

		if (!empty($kodever)) {
			$result = array(
				'status' => 1,
				'content' => $text
			);

			echo json_encode($result);
			exit;
		} else {
			$result = array(
				'status' => 0,
				'content' => $text
			);

			echo json_encode($result);
			exit;
		}
	}	


	public function verifikasiDO()
	{
		$user_verifikasi = $this->session->userdata('kode_user');
		$tgl_verifikasi  = (new \DateTime())->format('Y-m-d H:i:s');
		
		$kode_verifikasi = $this->input->post('kode_verifikasi');
		$no_kendaraan = $this->input->post('no_kendaraan');
		$no_do = $this->input->post('no_do');

		$this->dbSqlServer->update('surat_jalan', array('tgl_verifikasi' => $tgl_verifikasi, 'user_verifikasi' => $user_verifikasi), array('kode_verifikasi' => $kode_verifikasi, 'no_kendaraan' => $no_kendaraan, 'no_do' => $no_do));

		$result = array(
			'status' => 1,
			'content' => array(
				'kode_verifikasi' => $kode_verifikasi,
				'no_kendaraan' => $no_kendaraan,
				'no_do' => $no_do
			)
		);

		echo json_encode($result);
		exit;
	}

}
