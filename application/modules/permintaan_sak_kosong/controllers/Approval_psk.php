<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Approval_psk extends MY_Controller {
	protected $result;
	protected $_user;
	private $grup_farm;
	private $akses;
	private $tombol;
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'stpakan' );
		/* by pass dulu
		$this->session->set_userdata(
		array(
			'isLogin' => 1,
			'kode_user' => 'PG00001',
			'level_user' => 'KF',
			'level_user_db' => 'AG',
			'kode_farm' => 'CJ',
			'nama_user' => 'ANTON',
			'grup_farm'	=> 'BDY'
			)
		);*/
		/*$this->akses = array(
			'AG' => array('create','update'),
			'KF' => array('ack')
		);*/
		/*Edited by Muslam (Edit Jabatan)*/
		$this->akses = array(
			'KF' => array('create','update'),
			'KD' => array('ack'),
			'KDV' => array('approve')
		);
		$this->tombol = array(
			/*'approve' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'A\')">Approve</button>
					&nbsp;<button class="btn btn-danger tooltipster" onclick="permintaanSak.update(this,\'RJ\')">Reject</button>',
			*/
			'approve' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'A\')">Approve</button>
					&nbsp;<span id="btn_reject" class="btn btn-danger tooltipster">Reject</span>',
			
			/*'ack' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'R\')">ACK</button>
					&nbsp;<span id="tes" class="btn btn-danger tooltipster" onclick="permintaanSak.update(this,\'RJ\')">Reject</span>',*/

			'ack' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'R\')">ACK</button>
					&nbsp;<span id="btn_reject" class="btn btn-danger tooltipster">Reject</span>',

			'update' => 
				'<button class="btn btn-primary" onclick="permintaanSak.update(this,\'D\')">Simpan Draft</button>&nbsp;
				<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>&nbsp;
				<button class="btn btn-default" onclick="permintaanSak.baru(this)">Baru</button>&nbsp;
			<button class="btn btn-danger" onclick="permintaanSak.update(this,\'V\')">Batal</button>',

			'create' => '<button class="btn btn-primary" onclick="permintaanSak.submit(this,\'D\')">Simpan Draft</button>
				<button class="btn btn-primary" onclick="permintaanSak.submit(this,\'N\')">Rilis</button>',

			'createpenjualan' => '<button class="btn btn-primary" onclick="permintaanSak.submitPenjualan(this,\'A\')">Simpan</button>',


			'updatepenjualan' => '<button class="btn btn-primary" onclick="permintaanSak.updatePenjualan(this,\'A\')">Simpan</button>',
		);
		$this->_user = $this->session->userdata ( 'kode_user' );
        $this->_user_level = $this->session->userdata('level_user');
        $this->_farm = $this->session->userdata('kode_farm');

		$this->result = array (
				'status' => 0,
				'content' => ''
		);

	//	$this->load->model('pengembalian_sak/m_pengembalian_sak','mps');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));

		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','mps');
	}

	public function index(){
		$data['listoverbudget'] = $this->listOverBudget();
		$this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/list_keterangan',$data);
	}
}
