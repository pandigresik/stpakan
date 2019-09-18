<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Permintaan extends MY_Controller {
	protected $result;
	protected $_user;
	private $grup_farm;
	private $akses;
	private $tombol;
	private $dbSqlServer;
	private $_statusPPSK;
	private $_orderTabel;

	public function __construct() {
		parent::__construct ();
		$this->load->helper('stpakan');
		$this->dbSqlServer = $this->load->database('default', TRUE);
		$level_user = $this->session->userdata('level_user');
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
			'KDV' => array('approve'),
			'KDB' => array('approve')
		);
		$this->tombol = array(
			/*'approve' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'A\')">Approve</button>
					&nbsp;<button class="btn btn-danger tooltipster" onclick="permintaanSak.update(this,\'RJ\')">Reject</button>',
			*/
			'approve' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'A\')">Approve</button>
					&nbsp;<span id="btn_reject" class="btn btn-danger tooltipster"  onclick="permintaanSak.update(this,\'RJ\')">Reject</span>',

			/*'ack' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'R\')">ACK</button>
					&nbsp;<span id="tes" class="btn btn-danger tooltipster" onclick="permintaanSak.update(this,\'RJ\')">Reject</span>',*/

			'ack' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'R\')">Approve</button>
					&nbsp;<span id="btn_reject" class="btn btn-danger tooltipster"  onclick="permintaanSak.update(this,\'RJ\')">Reject</span>',

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

		switch($level_user){
			case 'KF':
				$this->_statusPPSK = "'D','N','V','R','A'";
				$this->_orderTabel = array(
					'D' => 1,
					'N' => 2,
					'RJ' => 3,
					'R' => 4,
					'A' => 5,
					'V' => 6,
				);
				break;
			case 'KD':
				$this->_statusPPSK = "'N','R','A'";
				$this->_orderTabel = array(
					'D' => 2,
					'N' => 1,
					'RJ' => 3,
					'R' => 4,
					'A' => 5,
					'V' => 6,
				);
				break;
			case 'KDV':
				$this->_statusPPSK = "'R','A'";
				$this->_orderTabel = array(
					'D' => 5,
					'N' => 4,
					'RJ' => 2,
					'R' => 1,
					'A' => 3,
					'V' => 6,
				);
				break;
			case 'KDB':
				$this->_statusPPSK = "'R','A'";
				$this->_orderTabel = array(
					'D' => 5,
					'N' => 4,
					'RJ' => 2,
					'R' => 1,
					'A' => 3,
					'V' => 6,
				);
				break;
		}
	}
	function listBudgetGlangsing2(){
		$kategori = $this->input->post('kategori')?$this->input->post('kategori'):'';
		$query = $this->db->query("
		select * from BUDGET_GLANGSING_D BGD
		left join M_BUDGET_PEMAKAIAN_GLANGSING MBPG on MBPG.KODE_BUDGET = BGD.KODE_BUDGET
		INNER JOIN M_PERIODE MP ON MP.KODE_SIKLUS = BGD.KODE_SIKLUS
		INNER JOIN BUDGET_GLANGSING BG ON BG.KODE_SIKLUS = BGD.KODE_SIKLUS
		where BGD.NO_URUT = (
			select MAX(NO_URUT) from BUDGET_GLANGSING_D where KODE_SIKLUS = BGD.KODE_SIKLUS
		)
		AND MP.KODE_SIKLUS in (
		 	select top 1 ks.kode_siklus from kandang_siklus ks where ks.status_siklus = 'O' and mp.kode_farm = ks.kode_farm
		) and MP.KODE_FARM = '$this->grup_farm' AND BG.STATUS = 'A' and MBPG.KATEGORI_BUDGET = '$kategori'
		");
		$data['keterangan'] = $query->result();
		return $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/list_keterangan',$data);
	}
	public function index(){
		$level_user = $this->session->userdata('level_user');
		/*switch($level_user){
			case 'AG':
				$this->adminGudang();
				break;
			case 'KF':
				$this->kafarm();
				break;
			default :
				echo 'Halaman ini bukan untuk anda ';
		}*/

		/*Edited by Muslam (Edit Jabatan)*/
		switch($level_user){
			case 'KF':
				$this->kafarm();
				break;
			case 'KD':
				$this->kadept();
				break;
			case 'KDV':
				$this->kadeptB();
				break;
			case 'KDB':
				$this->kadeptB();
				break;
			default :
				echo 'Halaman ini bukan untuk anda ';
		}
	}

	public function kadeptB() {
		$kodefarm = $this->session->userdata('kode_farm');
		$tombol_buat = '<div class="btn btn-default" data-aksi="transaksi_pengembalian" data-no_pengembalian="" onclick="Pengembalian.transaksi(this,\'#transaksi\')">Buat Baru</div>';
		$data['list_pp'] = null;
		$data['buat_baru'] = $tombol_buat;

		$dataPermintaan = array(
			'list_permintaan' => $this->listPermintaan($kodefarm,$this->_statusPPSK,$this->_orderTabel)
		);
		$data['list_permintaan'] = $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/list_permintaan',$dataPermintaan,TRUE);
		$data['form_permintaan'] = '';

		$log_ppsk_prop = 'none';
		$data['historiPermintaanSak'] = $this->getHistoriPermintaanSak($kodefarm,$log_ppsk_prop);
		$data['list_farm'] = $this->listfarm($this->_user);
		$data['kodefarm'] = $kodefarm;
		$data['list_farm_prop'] = 'inline';
		$data['list_permintaan_prop'] = 'none';

		$this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/permintaan',$data);
	}

	public function kadept() {
		$kodefarm = $this->session->userdata('kode_farm');
		$tombol_buat = '<div class="btn btn-default" data-aksi="transaksi_pengembalian" data-no_pengembalian="" onclick="Pengembalian.transaksi(this,\'#transaksi\')">Buat Baru</div>';
		$data['list_pp'] = null;
		$data['buat_baru'] = $tombol_buat;
		//$status = "'N','R','A','RJ'";

		$dataPermintaan = array(
			'list_permintaan' => $this->listPermintaan($kodefarm,$this->_statusPPSK,$this->_orderTabel)
		);
		$data['list_permintaan'] = $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/list_permintaan',$dataPermintaan,TRUE);
		$data['form_permintaan'] = '';

		$log_ppsk_prop = 'none';
		$data['historiPermintaanSak'] = $this->getHistoriPermintaanSak($kodefarm,$log_ppsk_prop);
		$data['list_farm'] = $this->listfarm($this->_user);
		$data['kodefarm'] = $kodefarm;
		$data['list_farm_prop'] = 'inline';
		$data['list_permintaan_prop'] = 'none';

		$this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/permintaan',$data);
	}

	public function kafarm() {
		$kodefarm = $this->session->userdata('kode_farm');
		//$data['buat_baru'] = $tombol_buat;
		//$status = "'D','N','V','R','RJ','A'";

		$dataPermintaan = array(
			'list_permintaan' => $this->listPermintaan($kodefarm,$this->_statusPPSK,$this->_orderTabel)
		);
		$data['list_permintaan'] = $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/list_permintaan',$dataPermintaan,TRUE);

		$status_siklus = $this->getStatusSiklus($kodefarm)['STATUS_BUDGET'];
		$selisih_hari_doc = $this->getDOCInDiffDay($kodefarm)['SELISIH_HARI'];
		if($status_siklus == 'C'){
			$data['form_permintaan'] =  $this->newFormPenjualan(TRUE);// $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/form_permintaan',$dataForm,TRUE);
		}else{
			// if($selisih_hari_doc < 7){
			// 	$data['form_permintaan'] =  $this->newFormPenjualan(TRUE);// $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/form_permintaan',$dataForm,TRUE);
			// }
			// else{
				$data['form_permintaan'] =  $this->newForm(TRUE);// $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/form_permintaan',$dataForm,TRUE);
			// }
		}
		$log_ppsk_prop = 'none';
		$data['historiPermintaanSak'] = $this->getHistoriPermintaanSak($kodefarm, $log_ppsk_prop);
		$data['list_farm'] = $this->listfarm($this->_user);
		$data['kodefarm'] = $kodefarm;
		$data['list_farm_prop'] = 'none';
		$data['list_permintaan_prop'] = 'inline';

		$this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/permintaan',$data);
	}
	/* cari semua sak yang sudah dikembalikan - sak yang sudah diminta pada siklus yang aktif */
	private function sakTersedia($kodefarm){
		return $this->mps->sakTersedia($kodefarm)->row_array();
	}
	private function listBudgetGlangsing($kodefarm,$kategori_budget){
		// $query = $this->db->query("
		// 	select * from M_BUDGET_PEMAKAIAN_GLANGSING where status = 'A' and KATEGORI_BUDGET = '$kategori_budget'
		// ");
		$query = $this->db->query("
			SELECT * FROM BUDGET_GLANGSING_D BGD
			INNER JOIN M_PERIODE MP ON MP.KODE_SIKLUS = BGD.KODE_SIKLUS
			INNER JOIN BUDGET_GLANGSING BG ON BG.KODE_SIKLUS = BGD.KODE_SIKLUS
			INNER JOIN M_BUDGET_PEMAKAIAN_GLANGSING MBPG ON MBPG.KODE_BUDGET = BGD.KODE_BUDGET
			WHERE NO_URUT = (
				SELECT MAX(NO_URUT) FROM BUDGET_GLANGSING_D WHERE KODE_SIKLUS = MP.KODE_SIKLUS
			)
			AND MP.KODE_SIKLUS = (
				select top 1 ks.kode_siklus from kandang_siklus ks where ks.status_siklus = 'O' and mp.kode_farm = ks.kode_farm
			) and MP.KODE_FARM = '$kodefarm' AND BG.STATUS = 'A'
			AND MBPG.KATEGORI_BUDGET = '$kategori_budget'
		");
		return $query->result();
	}
	private function getStatusSiklus($kode_farm){
		/*$query = $this->db->query("
			 select *,bg.STATUS STATUS_BUDGET from m_periode mp
			 left join BUDGET_GLANGSING bg on bg.KODE_SIKLUS = mp.KODE_SIKLUS
			 where mp.STATUS_PERIODE = 'A' and mp.KODE_FARM = '$kode_farm';
		");*/
		$sql = <<<QUERY
			select *,bg.STATUS STATUS_BUDGET from m_periode mp
			 left join BUDGET_GLANGSING bg on bg.KODE_SIKLUS = mp.KODE_SIKLUS
			 where mp.STATUS_PERIODE = 'A' and mp.KODE_FARM = '$kode_farm';
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetch(0);
		//return $query->row();
		return $hasil;
	}

	private function getDOCInDiffDay($kode_farm){
		/*$query = $this->db->query("
			 select top 1 datediff(DAY,GETDATE(),max(ks.TGL_DOC_IN)) SELISIH_HARI from M_PERIODE mp
			 join KANDANG_SIKLUS ks on mp.KODE_SIKLUS = ks.KODE_SIKLUS
			 where mp.KODE_SIKLUS > (select kode_siklus from m_periode where status_periode = 'A') and mp.KODE_FARM = '$kode_farm'
			 group by mp.kode_siklus
			 order by mp.KODE_SIKLUS ASC;
		");
		return $query->row();*/

		$sql = <<<QUERY
			select top 1 datediff(DAY,GETDATE(),max(ks.TGL_DOC_IN)) SELISIH_HARI from M_PERIODE mp
			 join KANDANG_SIKLUS ks on mp.KODE_SIKLUS = ks.KODE_SIKLUS
			 where mp.KODE_SIKLUS > (select kode_siklus from m_periode where status_periode = 'A' and KODE_FARM = mp.KODE_FARM) and mp.KODE_FARM = '$kode_farm'
			 group by mp.kode_siklus
			 order by mp.KODE_SIKLUS ASC;
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetch(0);
		//return $query->row();
		return $hasil;
	}

	private function getMasterBudget($kode_budget){
		$query = $this->db->query("
			select * from M_BUDGET_PEMAKAIAN_GLANGSING where status = 'A' and KODE_BUDGET = '$kode_budget'
		");
		return $query->row();
	}

	public function listPermintaan($kodefarm,$status,$order_table){
		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','mps');
		$periode_aktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode'=>'A'))->get('M_PERIODE')->row_array();
		/* cari pada periode yang aktif saja */
		return $this->mps->listPermintaan($kodefarm,$periode_aktif['periode_siklus'],$status,$order_table)->result_array();
	}

	private function listPegawai($kodefarm){
		$this->load->model('permintaan_sak_kosong/m_pegawai','mpg');
		$pegawai_farm = $this->db->select('kode_pegawai')->where(array('kode_farm' => $kodefarm))->get_compiled_select('PEGAWAI_D');
		//return $this->mpg->as_array()->get_many_by(' kode_pegawai in ('.$pegawai_farm.') and status_pegawai = \'A\' and grup_pegawai in (\'KFM\',\'AGF\',\'KPPB\',\'PPB\') ');

		/*Edited by Muslam (edit Jabatan)*/
		return $this->mpg->as_array()->get_many_by(' kode_pegawai in ('.$pegawai_farm.') and status_pegawai = \'A\' and grup_pegawai in (\'KFM\',\'AGF\',\'PPB\',\'KPPB\') ');
	}

	public function viewFormPenjualan(){
		// $kodefarm 	 = $this->session->userdata('kode_farm');
		$level_user  = $this->session->userdata('level_user');
		$no_ppsk 	 = $this->input->get('no_ppsk');
		$status 		 = $this->input->get('status');
		$kode_budget = $this->input->get('kode_budget');
		$kodefarm 	 = $this->input->get('kodefarm');
		//$status = $this->getMasterBudget();
		$sakGudang = $this->sakTersedia($kodefarm);
		$tombol = '';
		$readonly = 'readonly';
		$this->load->model('permintaan_sak_kosong/m_ppsk','mpsk');
		$dataPpsk = $this->mpsk->as_array()->get($no_ppsk);
		$sakTersedia = $sakGudang['sak_tersedia'];

		//$budgettotal = $this->getBudgetTotal($kodefarm,$kategori_budget,$kode_budget);
		//$budgetterpakai = $this->getBudgetTerpakai($sakGudang['prefix_ppsk'],$kategori_budget,$kode_budget);
		//$budgetsisa = $budgettotal - $budgetterpakai;

		//$kodefarm = $this->session->userdata('kode_farm');
		$sakGudang = $this->sakTersedia($kodefarm);
		$budgettotal = $this->getBudgetTotal($kodefarm,'','');
		$budgetterpakai = $this->getBudgetTerpakai($sakGudang['prefix_ppsk'],'','');
		$budgetsisa = $budgettotal - $budgetterpakai;
		$dataForm = array(
			'no_ppsk' => $dataPpsk['NO_PPSK'],
			//'keterangan' => $this->listBudgetGlangsing($kodefarm,$kategori_budget),
			//'kategori'	=> $kategori_budget,
			//'kode_budget'=>$kode_budget,
			'sak_tersimpan' => $sakTersedia,
			'jml_sak' => $dataPpsk['JML_SAK'],
			'user_penerima' => $dataPpsk['USER_PEMINTA'],
			'prefix_ppsk' => str_replace('PPSK', 'PSLG', $sakGudang['prefix_ppsk']),
			'no_do' => $dataPpsk['NO_DO'],
			//'list_user' => $this->listPegawai($kodefarm),
			'tombol' => $this->tombol['updatepenjualan'],
			'readonly' => $readonly,
			//'budget_sisa' => $budgetsisa,//$this->
			//'budget_sisa_t' => $budgetsisa + $dataPpsk['JML_SAK'],//$this->
			//'budget_total' => $budgettotal//$this->budgetTersedia($kodefarm),
		);
		$this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/form_penjualan',$dataForm);
	}

	public function viewForm(){
		// $kodefarm 		  = $this->session->userdata('kode_farm');
		$level_user 	  = $this->session->userdata('level_user');
		$no_ppsk 		  = $this->input->get('no_ppsk');
		$status 		  = $this->input->get('status');
		$kode_budget 	  = $this->input->get('kode_budget');
		$kodefarm		  = $this->input->get('kodefarm');
		$kategori_budget = $this->getMasterBudget($kode_budget)->KATEGORI_BUDGET;
		//$status = $this->getMasterBudget();
		$sakGudang = $this->sakTersedia($kodefarm);
		$periode_aktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode' => 'A'))->get('M_PERIODE')->row_array();
		$tombol = '';
		$readonly = 'readonly';
		$readonly_over = 'readonly';
		$this->load->model('permintaan_sak_kosong/m_ppsk','mpsk');
		$dataPpsk = $this->mpsk->as_array()->get($no_ppsk);
		$sakTersedia = $sakGudang['sak_tersedia'];


		$budgettotal = $this->getBudgetTotal($kodefarm,$kategori_budget,$kode_budget);
		$budgetterpakai = $this->getBudgetTerpakai($sakGudang['prefix_ppsk'],$kategori_budget,$kode_budget);
		$budgetsisa = $budgettotal - $budgetterpakai;

		switch($status){
			case 'D':
				switch($level_user){
					/*case 'AG':
						$tombol = $this->tombol['update'];
						/* jika AG maka bisa edit maka set sak tersedia = sak tersedia + yang diminta
						$sakTersedia += $dataPpsk['JML_SAK'];
						$readonly = '';
						break;
					case 'KF':
						$tombol = $this->tombol['ack'];
						break;*/
					/*Edited by Muslam (Edit Jabatan)*/
					case 'KF':
						$tombol = $this->tombol['update'];
						/* jika AG maka bisa edit maka set sak tersedia = sak tersedia + yang diminta*/
						$sakTersedia += $dataPpsk['JML_SAK'];
						$readonly = '';
						if(intval($dataPpsk['JML_OVER']) > 0) {
							$readonly_over = '';
						}else{
							$readonly_over = 'readonly';
						}
						break;
					case 'KD':
						$tombol = $this->tombol['ack'];
						break;
				}
				break;
			case 'N':
				switch($level_user){
					case 'KD':
						$tombol = $this->tombol['ack'];
					break;
				}
				break;
			case 'RJ':
				switch($level_user){
					case 'KF':
						$tombol = $this->tombol['update'];
						$readonly = '';
						if(intval($dataPpsk['JML_OVER']) > 0) {
							$readonly_over = '';
						}else{
							$readonly_over = 'readonly';
						}
					break;
				}
				break;
			case 'R':
				switch($level_user){
					case 'KDV':
						$tombol = $this->tombol['approve'];
					break;
					case 'KDB':
						$tombol = $this->tombol['approve'];
					break;
				}
				break;
			default:
		}
		$dataForm = array(
			'no_ppsk' => $dataPpsk['NO_PPSK'],
			'keterangan' => $this->listBudgetGlangsing($kodefarm,$kategori_budget),
			'kategori'	=> $kategori_budget,
			'kode_budget'=>$kode_budget,
			'sak_tersimpan' => $sakTersedia - $this->getSakTerpakai($kodefarm,$periode_aktif['periode_siklus']),
			'jml_sak' => $dataPpsk['JML_SAK'],
			'hrg_sak' => $dataPpsk['HARGA_SAK'],
			'user_peminta' => $dataPpsk['USER_PEMINTA'],
			'jml_over' => $dataPpsk['JML_OVER'],
			'alasan_over'=> $this->getLogPPSK($no_ppsk)['KETERANGAN'],
			'prefix_ppsk' => $sakGudang['prefix_ppsk'],
			'list_user' => $this->listPegawai($kodefarm),
			'tombol' => $tombol,
			'readonly' => $readonly,
			'readonly_over' => $readonly_over,
			'budget_sisa' => $budgetsisa,//$this->
			'budget_sisa_t' => $budgetsisa + $dataPpsk['JML_SAK'],//$this->
			'budget_total' => $budgettotal//$this->budgetTersedia($kodefarm),
		);
		$this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/form_permintaan',$dataForm);
	}

	public function newForm($html = FALSE){
		$kodefarm = $this->session->userdata('kode_farm');
		$sakGudang = $this->sakTersedia($kodefarm);
		$budgettotal = $this->getBudgetTotal($kodefarm,'I','');
		$budgetterpakai = $this->getBudgetTerpakai($sakGudang['prefix_ppsk'],'I','');
		$budgetsisa = $budgettotal - $budgetterpakai;
		$periode_aktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode' => 'A'))->get('M_PERIODE')->row_array();

		$dataForm = array(
			'no_ppsk' => '',
			'keterangan' => $this->listBudgetGlangsing($kodefarm,'I'),
			'sak_tersimpan' => $sakGudang['sak_tersedia'] - $this->getSakTerpakai($kodefarm,$periode_aktif['periode_siklus']),
			'jml_sak' => null,
			'hrg_sak' => null,
			'user_peminta' => '',
			'prefix_ppsk' => $sakGudang['prefix_ppsk'],
			'list_user' => $this->listPegawai($kodefarm),
			'tombol' => $this->tombol['create'],
			'kategori' => 'I',
			'kode_budget'=>'',
			'jml_over'=>'',
			'alasan_over'=>'',
			'budget_sisa' => $budgetsisa,//$this->budgetSisa("PPSK/{"."$kodefarm}/{".$periode_aktif['periode_siklus']."}/%"),
			'budget_sisa_t' => $budgetsisa,//$this->budgetSisa("PPSK/{"."$kodefarm}/{".$periode_aktif['periode_siklus']."}/%"),
			'budget_total' => $budgettotal//$this->budgetTersedia($kodefarm),
		);
		if($html){
			return $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/form_permintaan',$dataForm,TRUE);
		}else{
			$this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/form_permintaan',$dataForm);
		}
	}

	public function newFormPenjualan($html = FALSE){
		$kodefarm = $this->session->userdata('kode_farm');
		$sakGudang = $this->sakTersedia($kodefarm);
		$budgettotal = $this->getBudgetTotal($kodefarm,'','');
		$budgetterpakai = $this->getBudgetTerpakai($sakGudang['prefix_ppsk'],'','');
		$budgetsisa = $budgettotal - $budgetterpakai;
		$periode_aktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode' => 'A'))->get('M_PERIODE')->row_array();

		$dataForm = array(
			'no_ppsk' => '',
			'keterangan' => $this->listBudgetGlangsing($kodefarm,'I'),
			'sak_tersimpan' => $sakGudang['sak_tersedia'] - $this->getSakTerpakai($kodefarm,$periode_aktif['periode_siklus']),
			'jml_sak' => null,
			'user_penerima' => '',
			'prefix_ppsk' => str_replace('PPSK', 'PSLG', $sakGudang['prefix_ppsk']),
			'list_user' => $this->listPegawai($kodefarm),
			'tombol' => $this->tombol['createpenjualan'],
			'kategori' => 'I',
			'kode_budget'=>'',
			'no_do'=>'',
			'budget_sisa' => $budgetsisa,//$this->budgetSisa("PPSK/{"."$kodefarm}/{".$periode_aktif['periode_siklus']."}/%"),
			'budget_sisa_t' => $budgetsisa,//$this->budgetSisa("PPSK/{"."$kodefarm}/{".$periode_aktif['periode_siklus']."}/%"),
			'budget_total' => $budgettotal//$this->budgetTersedia($kodefarm),
		);
		if($html){
			return $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/form_penjualan',$dataForm,TRUE);
		}else{
			$this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/form_penjualan',$dataForm);
		}
	}

	public function simpan(){
		$error		 = false;
		$kodefarm 	 = $this->session->userdata('kode_farm');
		$data 		 = $this->input->post('data');
		$status 		 = $this->input->post('nextStatus');
		$alasan_over = $this->input->post('alasan_over');
		$nextStatus  = $this->input->post('nextStatus');
		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','ppsk');
		$this->load->model('permintaan_sak_kosong/m_ppsk','mpsk');
		$this->load->model('permintaan_sak_kosong/m_log_ppsk','mlpsk');
		$tgl_buat    = $this->get_today();
		$no_ppsk		 = $this->ppsk->no_ppsk($data['no_ppsk']);
		$no_urut_log_ppsk = $this->ppsk->no_urut_log_ppsk($no_ppsk);
		$message = array(
			'success' => array(
				'D' => 'Permintaan sak kosong berhasil disimpan sebagai draft',
				'N' => 'Permintaan sak kosong berhasil dirilis',
			),
			'error' => array(
				'D' => 'Permintaan sak kosong gagal disimpan',
				'N' => 'Permintaan sak kosong gagal dirilis',
			)
		);

		$this->dbSqlServer->trans_begin();

		$data_ppsk = array();
		$data_ppsk['NO_PPSK'] 		= $no_ppsk;
		$data_ppsk['JML_SAK'] 		= (isset($data['jml_sak'])) ? $data['jml_sak'] : NULL;
		$data_ppsk['USER_PEMINTA'] = (isset($data['user_peminta'])) ? $data['user_peminta'] : NULL;
		$data_ppsk['STATUS'] 		= (isset($data['status'])) ? $data['status'] : NULL;
		$data_ppsk['KODE_BUDGET'] 	= (isset($data['kode_budget'])) ? $data['kode_budget'] : NULL;
		$data_ppsk['NO_DO']		 	= (isset($data['no_do'])) ? $data['no_do'] : NULL;
		$data_ppsk['JML_OVER'] 		= (isset($data['jml_over'])) ? $data['jml_over'] : NULL;
		$data_ppsk['HARGA_SAK'] 	= (isset($data['harga_sak'])) ? $data['harga_sak'] : NULL;

		$this->dbSqlServer->insert("PPSK", $data_ppsk);

		if($this->dbSqlServer->affected_rows() > 0){
			$sinkronisasi = array();
			$sinkronisasi['transaksi'] = "simpan_permintaan_sak_kosong";
			$sinkronisasi['asal'] 		= $kodefarm;
			$sinkronisasi['tujuan'] 	= "FM";
			$sinkronisasi['aksi'] 		= "PUSH";
			$sinkronisasi['tgl_buat'] 	= $tgl_buat['today'];

			$this->dbSqlServer->insert("sinkronisasi", $sinkronisasi);

			if($this->dbSqlServer->affected_rows() > 0){
				$id = $this->dbSqlServer->insert_id();

				$detail_sinkronisasi = array();
				$detail_sinkronisasi["sinkronisasi"] = $id;
				$detail_sinkronisasi["aksi"] 	= "I";
				$detail_sinkronisasi["tabel"] = "PPSK";
				$detail_sinkronisasi["kunci"] = '{"NO_PPSK":"'.$no_ppsk.'"}';
				$detail_sinkronisasi["status_identity"] = 0;
				$this->dbSqlServer->insert("detail_sinkronisasi", $detail_sinkronisasi);

				if($this->dbSqlServer->affected_rows() <= 0){
					// $this->dbSqlServer->trans_rollback();
					// return false;
					$error = true;
				}

				$data_log_ppsk = array();
				$data_log_ppsk['NO_PPSK'] 	 = $no_ppsk;
				$data_log_ppsk['NO_URUT'] 	 = $no_urut_log_ppsk;
				$data_log_ppsk['STATUS'] 	 = $status;
				$data_log_ppsk['USER_BUAT'] = $this->_user;
				$data_log_ppsk['TGL_BUAT']  = $tgl_buat['today'];
				$data_log_ppsk['KETERANGAN']= $alasan_over;

				$this->dbSqlServer->insert("LOG_PPSK", $data_log_ppsk);

				if($this->dbSqlServer->affected_rows() > 0){
					$detail_sinkronisasi = array();
					$detail_sinkronisasi["sinkronisasi"] = $id;
					$detail_sinkronisasi["aksi"] 	= "I";
					$detail_sinkronisasi["tabel"] = "LOG_PPSK";
					$detail_sinkronisasi["kunci"] = '{"NO_PPSK":"'.$no_ppsk.'","NO_URUT":"'.$no_urut_log_ppsk.'"}';
					$detail_sinkronisasi["status_identity"] = 0;
					$this->dbSqlServer->insert("detail_sinkronisasi", $detail_sinkronisasi);

					if($this->dbSqlServer->affected_rows() <= 0){
						// $this->dbSqlServer->trans_rollback();
						// return false;
						$error = true;
					}
				}
				else {
					$error = true;
				}
			}
			else {
				$error = true;
			}
		}
		
		if(!$error){
         $this->dbSqlServer->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = $message['success'][$nextStatus];
		}
		else{
         $this->dbSqlServer->trans_rollback();
			$this->result['message'] = $message['error'][$nextStatus];
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function update(){
		$error		 = false;
		// $kodefarm 	 = $this->session->userdata('kode_farm');
		$type 		= $this->input->post('type')?$this->input->post('type'):'';
		$data 		= $this->input->post('data');
		$nextStatus = $this->input->post('nextStatus');
		//$kodefarm	= $this->input->post('kodefarm');
		$tgl_buat    = $this->get_today();
		//$keterangan_reject = $this->input->post('keterangan_reject');
		//$keterangan_over = $this->input->post('keterangan_over');
		$data['status'] = $nextStatus;
		$keterangan = ($nextStatus != 'RJ')? $this->input->post('alasan_over') : $this->input->post('keterangan_reject');
		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','ppsk');
		$this->load->model('permintaan_sak_kosong/m_ppsk','mpsk');
		$this->load->model('permintaan_sak_kosong/m_log_ppsk','mlpsk');

		$no_ppsk = $data['no_ppsk'];
		$kodefarm = $this->getKodeFarm($no_ppsk);
		$no_urut_log_ppsk = $this->ppsk->no_urut_log_ppsk($no_ppsk);
		$message = array(
			'success' => array(
				'D' => 'Permintaan sak kosong berhasil disimpan sebagai draft',
				'N' => 'Permintaan sak kosong berhasil dirilis',
				'R' => 'Permintaan sak kosong dengan No. Permintaan Sak '.$no_ppsk.' berhasil di-Review',
				'RJ' => 'Permintaan sak kosong dengan No. Permintaan Sak '.$no_ppsk.' berhasil di-Reject',
				'A' => 'Permintaan sak kosong dengan No. Permintaan Sak '.$no_ppsk.' berhasil di-Approve',
				'V' => 'Permintaan sak kosong dengan No. Permintaan Sak '.$no_ppsk.' berhasil dihapus/dibatalkan',
			),
			'error' => array(
				'D' => 'Permintaan sak kosong gagal diubah',
				'N' => 'Permintaan sak kosong gagal dirilis',
				'R' => 'Permintaan sak kosong gagal di-Review',
				'RJ' => 'Permintaan sak kosong gagal di-Reject',
				'A' => 'Permintaan sak kosong gagal di-Approve',
				'V' => 'Permintaan sak kosong gagal dihapus/dibatalkan'
			)
		);
		if($type == 'penjualan'){
			$message = array(
				'success' => array(
					'A' => 'Data berhasil diubah',
				),
				'error' => array(
					'A' => 'data gagal diubah'
				)
			);
		}
		$this->dbSqlServer->trans_begin();

		$data_ppsk = array();
		$data_ppsk['NO_PPSK'] 		= $no_ppsk;
		$data_ppsk['JML_SAK'] 		= (isset($data['jml_sak'])) ? $data['jml_sak'] : NULL;
		$data_ppsk['USER_PEMINTA'] = (isset($data['user_peminta'])) ? $data['user_peminta'] : NULL;
		$data_ppsk['STATUS'] 		= (isset($data['status'])) ? $data['status'] : NULL;
		$data_ppsk['KODE_BUDGET'] 	= (isset($data['kode_budget'])) ? $data['kode_budget'] : NULL;
		$data_ppsk['NO_DO']		 	= (isset($data['no_do'])) ? $data['no_do'] : NULL;
		$data_ppsk['JML_OVER'] 		= (isset($data['jml_over'])) ? $data['jml_over'] : NULL;
		$data_ppsk['HARGA_SAK'] 	= (isset($data['harga_sak'])) ? $data['harga_sak'] : NULL;

		$this->dbSqlServer->where("NO_PPSK", $no_ppsk);
		$this->dbSqlServer->update("PPSK", $data_ppsk);

		if($this->dbSqlServer->affected_rows() > 0){
			$sinkronisasi = array();
			$sinkronisasi['transaksi'] = "ubah_permintaan_sak_kosong";
			if($nextStatus != 'D' && $nextStatus != 'N'){
				$sinkronisasi['asal'] 		= "FM";
				$sinkronisasi['tujuan'] 	= $kodefarm;
			}else {
				$sinkronisasi['asal'] 		= $kodefarm;
				$sinkronisasi['tujuan'] 	= "FM";
			}
			$sinkronisasi['aksi'] 		= "PUSH";
			$sinkronisasi['tgl_buat'] 	= $tgl_buat['today'];

			$this->dbSqlServer->insert("sinkronisasi", $sinkronisasi);

			if($this->dbSqlServer->affected_rows() > 0){
				$id = $this->dbSqlServer->insert_id();

				$detail_sinkronisasi = array();
				$detail_sinkronisasi["sinkronisasi"] = $id;
				$detail_sinkronisasi["aksi"] 	= "U";
				$detail_sinkronisasi["tabel"] = "PPSK";
				$detail_sinkronisasi["kunci"] = '{"NO_PPSK":"'.$no_ppsk.'"}';
				$detail_sinkronisasi["status_identity"] = 0;
				$this->dbSqlServer->insert("detail_sinkronisasi", $detail_sinkronisasi);

				if($this->dbSqlServer->affected_rows() <= 0){
					// $this->dbSqlServer->trans_rollback();
					// return false;
					$error = true;
				}

				$data_log_ppsk = array();
				$data_log_ppsk['NO_PPSK'] 	 = $no_ppsk;
				$data_log_ppsk['NO_URUT'] 	 = $no_urut_log_ppsk;
				$data_log_ppsk['STATUS'] 	 = $nextStatus;
				$data_log_ppsk['USER_BUAT'] = $this->_user;
				$data_log_ppsk['TGL_BUAT']  = $tgl_buat['today'];
				$data_log_ppsk['KETERANGAN']= $keterangan;

				$this->dbSqlServer->insert("LOG_PPSK", $data_log_ppsk);

				if($this->dbSqlServer->affected_rows() > 0){
					$detail_sinkronisasi = array();
					$detail_sinkronisasi["sinkronisasi"] = $id;
					$detail_sinkronisasi["aksi"] 	= "I";
					$detail_sinkronisasi["tabel"] = "LOG_PPSK";
					$detail_sinkronisasi["kunci"] = '{"NO_PPSK":"'.$no_ppsk.'","NO_URUT":"'.$no_urut_log_ppsk.'"}';
					$detail_sinkronisasi["status_identity"] = 0;
					$this->dbSqlServer->insert("detail_sinkronisasi", $detail_sinkronisasi);

					if($this->dbSqlServer->affected_rows() <= 0){
						// $this->dbSqlServer->trans_rollback();
						// return false;
						$error = true;
					}
				}
			}
		}
		// $this->db->trans_start();
		//
		// $this->mpsk->update($no_ppsk,$data);
		//
		// $dataLog = array(
		// 	'no_ppsk' => $no_ppsk,
		// 	'status' => $nextStatus,
		// 	'user_buat' => $this->_user,
		// 	'keterangan' => $keterangan
		// );
		// $this->mlpsk->insert($dataLog);
		//
		// $this->db->trans_complete();
		if($error){
			$this->dbSqlServer->trans_rollback();
			$this->result['message'] = $message['error'][$nextStatus];
		}
		else{
			$this->dbSqlServer->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = $message['success'][$nextStatus];
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function getBudgetTotal($kode_farm,$kategori,$kode_budget){
		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','mps');
		return $this->mps->getBudgetTotal($kode_farm,$kategori,$kode_budget);
	}

	public function getBudgetTerpakai($prefix_ppsk,$kategori,$kode_budget){
		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','mps');
		return $this->mps->getBudgetTerpakai($prefix_ppsk,$kategori,$kode_budget);
	}

	public function getSakTerpakai($kode_farm,$periode_siklus){
		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','mps');
		return $this->mps->getSakTerpakai($kode_farm,$periode_siklus);
	}

	public function getJumlahSak(){
		// $kodefarm 	 = $this->session->userdata('kode_farm');
		$prefix_ppsk = $this->input->post('prefix_ppsk');
		$kategori 	 = $this->input->post('kategori');
		$kode_budget = $this->input->post('kode_budget');
		$kodefarm 	 = $this->input->post('kodefarm');

		$budgettotal 	 = $this->getBudgetTotal($kodefarm,$kategori,$kode_budget);
		$budgetterpakai = $this->getBudgetTerpakai($prefix_ppsk,$kategori,$kode_budget);
		$budgetsisa 	 = $budgettotal - $budgetterpakai;

		$result['budgettotal'] = $budgettotal;
		$result['budgetsisa']  = $budgetsisa;
		echo json_encode($result);
	}

	public function getHistoriPermintaanSak($kodefarm = '', $log_ppsk_prop = '')
	{
		$data['histori_permintaan'] = $this->GetListHistori($kodefarm);
		$data['log_ppsk_prop'] = $log_ppsk_prop;
		return $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/histori_permintaan',$data,TRUE);
	}

	public function loadViewHistory()
	{
		$no_ppsk  = $this->input->post('no_ppsk');
		$kodefarm = $this->input->post('kodefarm');
		$data['histori_permintaan'] = $this->GetListHistori($kodefarm,$no_ppsk);
		$data['log_ppsk_prop'] = 'inline';
		echo $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/histori_permintaan',$data,TRUE);
	}
	public function GetListHistori($kodefarm = '', $no_ppsk = '')
	{
		// $kodefarm = $this->session->userdata('kode_farm');

		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','mps');
		$periode_aktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode' => 'A'))->get('M_PERIODE')->row_array();
		/* cari pada periode yang aktif saja */
		return $this->mps->listHistori($kodefarm,$periode_aktif['periode_siklus'],$no_ppsk)->result_array();
	}

	function cek_status_reject_home(){
		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','mps');
		echo $this->mps->doCek_status_reject_home($this->_user_level,$this->_farm);
	}

	function getLogPPSK($no_ppsk){
		$sql = <<<QUERY
			select top 1 * from LOG_PPSK lpp
			join PPSK pp on pp.NO_PPSK = lpp.NO_PPSK and lpp.NO_PPSK = '$no_ppsk'
			where lpp.STATUS != 'RJ'
			order by lpp.NO_URUT desc
			;
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetch(0);
		//return $query->row();
		return $hasil;
	}

	public function ApprovalPsk($value=''){
		$kodefarm = $this->session->userdata('kode_farm');
		$user_id	= $this->session->userdata('kode_user');
		$periode_aktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode'=>'A'))->get('M_PERIODE')->row_array();

		$data['listoverbudget'] = $this->listOverBudget($kodefarm, $periode_aktif['periode_siklus'], $user_id);
		$this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/approvalpsk',$data);
	}

	public function listOverBudget($kodefarm, $periode_siklus, $user_id){
		$sql = <<<QUERY
		select mp.GRUP_PEGAWAI,pp.* ,
		COALESCE((select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'D' ORDER BY NO_URUT DESC), (select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC)) TGL_BUAT ,

		(select top 1 tgl_buat from LOG_PPSK
			JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI in('WKDV','WKBA')
			where no_ppsk = pp.no_ppsk and status = 'A' ORDER BY NO_URUT DESC
		) TGL_APPROVE,

		COALESCE((select top 1 tgl_buat from LOG_PPSK
			JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDV'
			where no_ppsk = pp.no_ppsk and status = 'A' ORDER BY NO_URUT DESC
		),(select top 1 tgl_buat from LOG_PPSK
			JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDV'
			where no_ppsk = pp.no_ppsk and status = 'RJ' ORDER BY NO_URUT DESC)) TGL_APPROVE_KADIV,

		(select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'R' ORDER BY NO_URUT DESC) TGL_ACK ,

		(select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC) TGL_RILIS ,


		case
			when pp.STATUS = 'D' then 'Draft'
			when pp.STATUS = 'N' then 'New (Rilis)'
			when pp.STATUS = 'R' then 'Review'
			when pp.STATUS = 'RJ' then 'Rejected'
			when pp.STATUS = 'A' then 'Approved'
			when pp.STATUS = 'V' then 'Void'
		end STATUS_DESC,

		case
			when mp.GRUP_PEGAWAI = 'WKDV' AND pp.JML_OVER > 0 then 1
			when mp.GRUP_PEGAWAI = 'WKDV' AND pp.JML_OVER < 0 then 2
			when pp.STATUS = 'V' then 3
			when mp.GRUP_PEGAWAI = 'KDV' then 4

		end urutan,
		COALESCE(mbpg.NAMA_BUDGET,'Penjualan Sak '+'(DO: '+pp.NO_DO+')') KETERANGAN ,

		COALESCE(mp2.NAMA_PEGAWAI,UPPER(pp.USER_PEMINTA)) NAMA_PEGAWAI,

		(select top 1 keterangan from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC) ALASAN_OVER,
		'' STATUS_MESSAGE,
		(select COUNT(*) from LOG_PPSK lpp where lpp.USER_BUAT = mp.KODE_PEGAWAI AND mp.GRUP_PEGAWAI = 'KDV' and lpp.NO_PPSK = pp.NO_PPSK) JML_APPROVE

		from PPSK pp
		JOIN log_ppsk lpp on lpp.NO_PPSK = pp.NO_PPSK
		--JOIN #tmp_logppsk tlp ON tlp.NO_PPSK = lpp.NO_PPSK AND tlp.max_tgl_buat = lpp.TGL_BUAT
		JOIN (
		  SELECT NO_PPSK,max(NO_URUT) NO_URUT FROM LOG_PPSK GROUP BY NO_PPSK
		  ) tlp ON tlp.NO_PPSK = lpp.NO_PPSK AND tlp.NO_URUT = lpp.NO_URUT
		INNER JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
		LEFT JOIN M_PEGAWAI mp2 ON mp2.KODE_PEGAWAI = pp.USER_PEMINTA
		left join M_BUDGET_PEMAKAIAN_GLANGSING mbpg on mbpg.kode_budget = pp.kode_budget
		where
		-- pp.no_ppsk like '%{$kodefarm}/{$periode_siklus}/%'
		--and
		 pp.status in('A')
		AND pp.NO_PPSK NOT IN (SELECT NO_PPSK FROM LOG_PPSK WHERE USER_BUAT = '$user_id')

	UNION ALL

		SELECT distinct mp.GRUP_PEGAWAI,pp.*,
		COALESCE((select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'D' ORDER BY NO_URUT DESC), (select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC)) TGL_BUAT ,

				(select top 1 tgl_buat from LOG_PPSK
					JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI in('WKDV','WKBA')
					where no_ppsk = pp.no_ppsk and status = 'A' ORDER BY NO_URUT DESC
				) TGL_APPROVE,

				COALESCE((select top 1 tgl_buat from LOG_PPSK
					JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDV'
					where no_ppsk = pp.no_ppsk and status = 'A' ORDER BY NO_URUT DESC
				),(select top 1 tgl_buat from LOG_PPSK
					JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDV'
					where no_ppsk = pp.no_ppsk and status = 'RJ' ORDER BY NO_URUT DESC)) TGL_APPROVE_KADIV,

				(select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'R' ORDER BY NO_URUT DESC) TGL_ACK ,

				(select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC) TGL_RILIS ,


				case
					when pp.STATUS = 'D' then 'Draft'
					when pp.STATUS = 'N' then 'New (Rilis)'
					when pp.STATUS = 'R' then 'Review'
					when pp.STATUS = 'RJ' then 'Rejected'
					when pp.STATUS = 'A' then 'Approved'
					when pp.STATUS = 'V' then 'Void'
				end STATUS_DESC,

				case
					when mp.GRUP_PEGAWAI = 'WKDV' AND pp.JML_OVER > 0 then 1
					when mp.GRUP_PEGAWAI = 'WKDV' AND pp.JML_OVER < 0 then 2
					when pp.STATUS = 'V' then 3
					when mp.GRUP_PEGAWAI = 'KDV' then 4
				end urutan,
				COALESCE(mbpg.NAMA_BUDGET,'Penjualan Sak '+'(DO: '+pp.NO_DO+')') KETERANGAN ,

				COALESCE(mp2.NAMA_PEGAWAI,UPPER(pp.USER_PEMINTA)) NAMA_PEGAWAI,

				(select top 1 keterangan from LOG_PPSK where no_ppsk = pp.no_ppsk and status ='N' ORDER BY NO_URUT DESC) ALASAN_OVER,
		case
					when pp.STATUS = 'V' then '*Canceled by '+(SELECT TOP 1 mp.NAMA_PEGAWAI FROM LOG_PPSK lpp JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT WHERE NO_PPSK = pp.NO_PPSK AND lpp.STATUS = 'V' ORDER BY lpp.NO_URUT DESC)
					when pp.STATUS = 'A' then 'Tidak ada over budget <br>*Approved by '+(SELECT TOP 1 mp.NAMA_PEGAWAI FROM LOG_PPSK lpp JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT WHERE NO_PPSK = pp.NO_PPSK AND lpp.STATUS = 'A' ORDER BY lpp.NO_URUT DESC)
				end STATUS_MESSAGE,
				(select COUNT(*) from LOG_PPSK lpp where lpp.USER_BUAT = mp.KODE_PEGAWAI AND mp.GRUP_PEGAWAI = 'KDV' and lpp.NO_PPSK = pp.NO_PPSK) JML_APPROVE
		FROM PPSK pp
		JOIN (SELECT NO_PPSK FROM LOG_PPSK WHERE USER_BUAT = '$user_id') mk ON mk.NO_PPSK = pp.NO_PPSK

		JOIN log_ppsk lpp on lpp.NO_PPSK = pp.NO_PPSK
		--JOIN #tmp_logppsk tlp ON tlp.NO_PPSK = lpp.NO_PPSK AND tlp.max_tgl_buat = lpp.TGL_BUAT
		JOIN (
		  SELECT NO_PPSK,max(NO_URUT) NO_URUT FROM LOG_PPSK GROUP BY NO_PPSK
		  ) tlp ON tlp.NO_PPSK = lpp.NO_PPSK AND tlp.NO_URUT = lpp.NO_URUT
		INNER JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
		LEFT JOIN M_PEGAWAI mp2 ON mp2.KODE_PEGAWAI = pp.USER_PEMINTA
		left join M_BUDGET_PEMAKAIAN_GLANGSING mbpg on mbpg.kode_budget = pp.kode_budget

		ORDER BY urutan ASC,TGL_APPROVE_KADIV ASC

QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
		//return $query->row();
		return $hasil;
	}
	public function getFormPPSKData(){
		$no_ppsk = $this->input->post('no_ppsk');
		$sql = <<<QUERY
			SELECT TOP 1 * FROM PPSK pp
			LEFT JOIN LOG_PPSK lpp ON pp.NO_PPSK = lpp.NO_PPSK
			WHERE pp.NO_PPSK = '$no_ppsk' AND pp.STATUS != 'RJ'
			ORDER BY lpp.NO_URUT DESC

QUERY;

				$stmt = $this->db->conn_id->prepare($sql);
		        $stmt->execute();
		        $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
				//return $query->row();
				//return $hasil;
		echo json_encode($hasil);
	}
	function get_today(){
		$sql = <<<QUERY
		select getdate() as [today]
QUERY;
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
	function listfarm($user_id = ''){
		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','mps');
		return $this->mps->getListFarm($user_id);
	}
	function loadListPermintaan(){
		$kode_farm = $this->input->post('kode_farm');
		$dataPermintaan = array(
			'list_permintaan' => $this->listPermintaan($kode_farm,$this->_statusPPSK,$this->_orderTabel)
		);
		echo $this->load->view('permintaan_sak_kosong/'.$this->grup_farm.'/list_permintaan',$dataPermintaan,TRUE);
	}
	function getKodeFarm($no_ppsk = ''){
		$this->load->model('permintaan_sak_kosong/m_permintaan_sak','mps');
		return $this->mps->getKodeFarm($no_ppsk);
	}
}
