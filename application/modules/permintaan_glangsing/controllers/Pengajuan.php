<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Pengajuan extends MY_Controller {
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
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		
		$this->akses = array(
			'KF' => array('create','update'),
			'AG' => array('pick'),
			'KD' => array('ack'),
			'KDV' => array('approve'),
			'KDB' => array('approve')
		);
		$this->tombol = array(
		
			'approve' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'A\')">Approve</button>
					&nbsp;<span id="btn_reject" class="btn btn-danger tooltipster"  onclick="permintaanSak.update(this,\'RJ\')">Reject</span>',

			'ack' => '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'R\')">Approve</button>
					&nbsp;<span id="btn_reject" class="btn btn-danger tooltipster"  onclick="permintaanSak.update(this,\'RJ\')">Reject</span>',

			'update' =>
				'<button class="btn btn-primary" onclick="permintaanSak.update(this,\'D\')">Simpan Draft</button>&nbsp;
				<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>&nbsp;
				<button class="btn btn-default" onclick="permintaanSak.baru(this)">Baru</button>&nbsp;
			<button class="btn btn-danger" onclick="permintaanSak.update(this,\'V\')">Batal</button>',

			'create' => '<button class="btn btn-primary" onclick="permintaanSak.submit(this,\'N\')">Simpan</button>',

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

		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));

		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');

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
			case 'AG':
				$this->_statusPPSK = "'A'";
				$this->_orderTabel = array(
					'A' => 1,
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
		) and MP.KODE_FARM = 'BW' AND BG.STATUS = 'A' and MBPG.KATEGORI_BUDGET = '$kategori'
		");
		$data['keterangan'] = $query->result();
		return $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/list_keterangan',$data);
	}
	public function index(){
		$level_user = $this->session->userdata('level_user');
		
		switch($level_user){
			case 'KF':
				$this->kafarm_gudang();
				break;
			case 'AG':
				$this->kafarm_gudang();
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
			'list_permintaan' => $this->listPermintaan(array('kode_farm' => $kodefarm),$this->_statusPPSK,$this->_orderTabel)
		);
		$data['list_permintaan'] = $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/list_permintaan',$dataPermintaan,TRUE);
		$data['form_permintaan'] = '';

		$log_ppsk_prop = 'none';
		$data['historiPermintaanSak'] = $this->getHistoriPermintaanSak($kodefarm,$log_ppsk_prop);
		$data['list_farm'] = $this->listfarm($this->_user);
		$data['kodefarm'] = $kodefarm;
		$data['list_farm_prop'] = 'inline';
		$data['list_permintaan_prop'] = 'none';

		$this->load->view('permintaan_glangsing/'.$this->grup_farm.'/permintaan',$data);
	}

	public function kadept() {
		$kodefarm = $this->session->userdata('kode_farm');
		$tombol_buat = '<div class="btn btn-default" data-aksi="transaksi_pengembalian" data-no_pengembalian="" onclick="Pengembalian.transaksi(this,\'#transaksi\')">Buat Baru</div>';
		$data['list_pp'] = null;
		$data['buat_baru'] = $tombol_buat;

		$dataPermintaan = array(
			'list_permintaan' => $this->listPermintaan(array('kode_farm' => $kodefarm),$this->_statusPPSK,$this->_orderTabel)
		);
		$data['list_permintaan'] = $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/list_permintaan',$dataPermintaan,TRUE);
		$data['form_permintaan'] = '';

		$log_ppsk_prop = 'none';
		$data['historiPermintaanSak'] = $this->getHistoriPermintaanSak($kodefarm,$log_ppsk_prop);
		$data['list_farm'] = $this->listfarm($this->_user);
		$data['kodefarm'] = $kodefarm;
		$data['list_farm_prop'] = 'inline';
		$data['list_permintaan_prop'] = 'none';

		$this->load->view('permintaan_glangsing/'.$this->grup_farm.'/permintaan',$data);
	}

	public function kafarm_gudang() {
		$kodefarm = $this->session->userdata('kode_farm');
		$level_user = $this->session->userdata('level_user');
		//$data['buat_baru'] = $tombol_buat;
		//$status = "'D','N','V','R','RJ','A'";

		$dataPermintaan = array(
			'list_permintaan' => $this->listPermintaan(array('kode_farm' => $kodefarm),$this->_statusPPSK,$this->_orderTabel)
		);
		$data['list_permintaan'] = $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/list_permintaan',$dataPermintaan,TRUE);

		$status_siklus = $this->getStatusSiklus($kodefarm)['STATUS_BUDGET'];
		$selisih_hari_doc = $this->getDOCInDiffDay($kodefarm)['SELISIH_HARI'];
		if($status_siklus == 'C'){
			// $data['form_permintaan'] =  $this->newFormPenjualan(TRUE);// $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/form_permintaan',$dataForm,TRUE);
		}else{
			// if($selisih_hari_doc < 7){
			// 	$data['form_permintaan'] =  $this->newFormPenjualan(TRUE);// $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/form_permintaan',$dataForm,TRUE);
			// }
			// else{
				//$data['form_permintaan'] =  $this->newForm(TRUE);// $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/form_permintaan',$dataForm,TRUE);
			// }
		}
		$log_ppsk_prop = 'none';
		$data['historiPermintaanSak'] = $this->getHistoriPermintaanSak($kodefarm, $log_ppsk_prop);
		$data['kategori'] = $this->mps->getKategori();
		$data['list_farm'] = $this->listfarm($this->_user);
		$data['kodefarm'] = $kodefarm;
		$data['level_user'] = $level_user;
		$data['list_farm_prop'] = 'none';
		$data['list_permintaan_prop'] = 'inline';

		$this->load->view('permintaan_glangsing/'.$this->grup_farm.'/permintaan',$data);
	}
	/* cari semua sak yang sudah dikembalikan - sak yang sudah diminta pada siklus yang aktif */
	private function sakTersedia($kodefarm){
		return $this->mps->sakTersedia($kodefarm)->row_array();
	}
	private function listBudgetGlangsing($kodefarm){
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
		");
		return $query->result();
	}
	private function getStatusSiklus($kode_farm){
		
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

	public function listPermintaan($filter,$status,$order_table){
		$kodefarm = $filter['kode_farm'];
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		$periode_aktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode'=>'A'))->get('M_PERIODE')->row_array();
		/* cari pada periode yang aktif saja */
		return $this->mps->listPermintaan($filter,$periode_aktif['periode_siklus'],$status,$order_table)->result_array();
	}

	private function listPegawai($kodefarm){
		$this->load->model('permintaan_glangsing/m_pegawai','mpg');
		$pegawai_farm = $this->db->select('kode_pegawai')->where(array('kode_farm' => $kodefarm))->get_compiled_select('PEGAWAI_D');
		//return $this->mpg->as_array()->get_many_by(' kode_pegawai in ('.$pegawai_farm.') and status_pegawai = \'A\' and grup_pegawai in (\'KFM\',\'AGF\',\'KPPB\',\'PPB\') ');

		/*Edited by Muslam (edit Jabatan)*/
		return $this->mpg->as_array()->get_many_by(' kode_pegawai in ('.$pegawai_farm.') and status_pegawai = \'A\' and grup_pegawai in (\'KFM\',\'AGF\',\'PPB\',\'KPPB\') ');
	}


	public function viewForm(){
		// $kodefarm 		  = $this->session->userdata('kode_farm');
		//cetak_r($this->session->userdata());
		$level_user 	  = $this->session->userdata('level_user');
		$no_ppsk 		  = $this->input->get('no_ppsk');
		$status 		  = $this->input->get('status');
		$kode_siklus 	  = $this->input->get('kode_siklus');
		$kode_budget 	  = $this->input->get('kode_budget');
		$kodefarm		  = $this->input->get('kodefarm');
		$keterangan		  = $this->input->get('keterangan');
		$kategori_budget = $this->getMasterBudget($kode_budget)->KATEGORI_BUDGET;
		//$status = $this->getMasterBudget();
		$sakGudang = $this->sakTersedia($kodefarm);
		$periode_aktif = $this->db->select('periode_siklus, kode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode' => 'A'))->get('M_PERIODE')->row_array();
		$tombol = '';
		$readonly = 'readonly';
		$readonly_over = 'readonly';
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mpsk');
		$this->load->model('permintaan_glangsing/m_ppsk','ppsk');
		$dataPpsk = $this->ppsk->as_array()->get($no_ppsk);
		$sakTersedia = $sakGudang['sak_tersedia'];


		$budgettotal = $this->getBudgetTotal($kodefarm,$kategori_budget,$kode_budget);
		//$budgetterpakai = $this->getBudgetTerpakai($sakGudang['prefix_ppsk'], $periode_aktif['kode_siklus'],$kode_budget);
		$budgetterpakai = $this->getBudgetTerpakai($kodefarm,$periode_aktif['kode_siklus'],$kode_budget);
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
						$sakTersedia += $dataPpsk['jml_diminta'];
						$readonly = '';
						if(intval($dataPpsk['jml_over_budget']) > 0) {
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
					case 'KF':
						//$tombol = $this->tombol['update'];
						/* jika AG maka bisa edit maka set sak tersedia = sak tersedia + yang diminta*/
						$sakTersedia += $dataPpsk['jml_diminta'];
						$readonly = '';
						if(intval($dataPpsk['jml_over_budget']) > 0) {
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
			case 'RJ':
				switch($level_user){
					case 'KF':
						$tombol = $this->tombol['create'];
						$readonly = '';
						if(intval($dataPpsk['jml_over_budget']) > 0) {
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


		$result = $this->getJumlahGlangsing($dataPpsk['no_ppsk'], $dataPpsk['tgl_kebutuhan'], $kategori_budget, $kode_budget, $kodefarm);

		$dataForm = array(
			'no_ppsk' => $dataPpsk['no_ppsk'],
			'keterangan' => $this->listBudgetGlangsing($kodefarm,$kategori_budget),
			'kategori'	=> $kategori_budget,
			'kode_budget'=>$kode_budget,
		//	'sak_tersimpan' => $sakTersedia - $this->getSakTerpakai($kodefarm,$periode_aktif['periode_siklus']),
			'sak_tersimpan' => $sakTersedia,
			'jml_sak' => $dataPpsk['jml_diminta'],
			//'hrg_sak' => $dataPpsk['HARGA_SAK'],
			//'user_peminta' => $dataPpsk['USER_PEMINTA'],
			'jml_over' => $dataPpsk['jml_over_budget'],
			'alasan_over'=> $dataPpsk['keterangan'],
			//'prefix_ppsk' => $sakGudang['prefix_ppsk'],
			'list_user' => $this->listPegawai($kodefarm),
			'tombol' => $tombol,
			'readonly' => $readonly,
			'readonly_over' => $readonly_over,
			'budget_sisa' => $budgetsisa,//$this->
			'budget_sisa_t' => $budgetsisa + $dataPpsk['jml_diminta'],//$this->
			'tgl_sekarang' => date('Y-m-d'),
			'tgl_permintaan' => $dataPpsk['tgl_permintaan'],
			'tgl_permintaan_text' => convertElemenTglIndonesia($dataPpsk['tgl_permintaan']),
			'tgl_kebutuhan' => $dataPpsk['tgl_kebutuhan'],
			'tgl_kebutuhan_text' => convertElemenTglIndonesia($dataPpsk['tgl_kebutuhan']),
			'kode_siklus' => $kode_siklus,
			'budget_total' => $budgettotal, //$this->budgetTersedia($kodefarm),
			'status' => $status,
			'budget_sisa' => $result['budgetsisa'],
			'remarks' => $keterangan,
			'listPermintaan' => $this->getListKandang($dataPpsk['no_ppsk'], $kode_budget, $dataPpsk['tgl_kebutuhan'], $status),
			'readonly' => true
		);
		$this->load->view('permintaan_glangsing/'.$this->grup_farm.'/form_permintaan',$dataForm);
	}

	public function getListPengambilan(){
		$noPpsk = $this->input->post('no_ppsk');
		$kodeBudget = $this->input->post('kode_budget');
		$tglKebutuhan = $this->input->post('tgl_kebutuhan');
		echo $this->getListKandang($noPpsk, $kodeBudget, $tglKebutuhan, 'A', true);
	}

	public function getListKandang($noPpsk, $kodeBudget, $tglKebutuhan, $status,$json = false){
		$level_user = $this->session->userdata('level_user');
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mpsk');
		$listKandang = $this->mpsk->getDaftarPermintaan($noPpsk, $kodeBudget, $tglKebutuhan);
		//cetak_r($listKandang);
		$str = '';
		foreach ($listKandang as $key => $value) {
			$str .= '<tr data-kode-kandang="'.$value['KODE_KANDANG'].'" data-kode-flok="'.$value['FLOK_BDY'].'" data-noreg="'.$value['no_reg'].'" data-jml_diminta="'.$value['jml_diminta'].'">';
				$str .= '<td>'.$value['NAMA_KANDANG'].'</td>';
				$str .= '<td>'.$value['umur'].'</td>';
				if($status == 'RJ'){
					$str .= '<td><input class="form-control number jmlSak" name="jmlSak" onchange="permintaanSak.onchangeSak(this);" id="jmlSak" style="width: 100px" value="'.$value['jml_diminta'].'" type="text"></td>';
				}else{
					$str .= '<td>'. $value['jml_diminta'] .'</td>';
					if($status == 'A'){
						$str .= '<td class="penerima">';
						if($value['user_penerima'] == ''){
							if($level_user == 'AG' && $value['jml_diminta'] > 0){
								$str .= '<input type="button" value="Konfirmasi" onclick="permintaanSak.konfirmasiPengambilan(this);">';
							}
						}else{
							$str .= '['. $value['user_penerima'] .'] '.convertElemenTglWaktuIndonesia($value['tgl_terima'], '-', ' ');
						}
						$str .= '</td>';
					}

				}
			$str .= '</tr>';
		}
		if($json){
			return json_encode(array('status'=>'1', 'content'=>$str));
		}else{
			return $str	;
		}
	}

	public function newForm($html = FALSE){
		$this->load->model('pengembalian_sak/m_glangsing_movement','gm');
		$kodefarm = $this->_farm; //$this->session->userdata('kode_farm');
		$kodeGlangsingBekasPakai = 'GBP';
	//	$sakGudang = $this->sakTersedia($kodefarm);
		$periode_aktif = $this->db->select('periode_siklus, kode_siklus')->where(array('kode_farm' => $this->_farm,'status_periode'=>'A'))->get('M_PERIODE')->row_array();
		$prefix_ppsk = 'PPSK/'+$this->_farm+'/'+$periode_aktif['periode_siklus']+'/';
		$budgettotal = $this->getBudgetTotal($kodefarm,'I','');
		$budgetterpakai = $this->getBudgetTerpakai($prefix_ppsk, $periode_aktif['kode_siklus'],'');
		$budgetsisa = $budgettotal - $budgetterpakai;
		$whereGlangsingMovement = array('kode_barang' => $kodeGlangsingBekasPakai,'kode_farm' => $this->_farm,'kode_siklus' => $periode_aktif['kode_siklus']);
		$stokAkhirGlangsing = $this->gm->get_by($whereGlangsingMovement);
		$sak_tersimpan = $stokAkhirGlangsing->jml_stok;
		$tglSekarang = date('Y-m-d');

		$dataForm = array(
			'no_ppsk' => '',
			'keterangan' => $this->listBudgetGlangsing($kodefarm),
			'tgl_sekarang' => $tglSekarang,
			'tgl_permintaan' => $tglSekarang,
			'tgl_kebutuhan'	 => date('Y-m-d',strtotime($tglSekarang. ' + 2 days')),
			'tgl_permintaan_text' => convertElemenTglIndonesia($tglSekarang),
			'tgl_kebutuhan_text'	 => convertElemenTglIndonesia(date('Y-m-d',strtotime($tglSekarang. ' + 2 days'))),
			'sak_tersimpan' => $sak_tersimpan,//$sakGudang['sak_tersedia'],
			'kode_siklus' => $periode_aktif['kode_siklus'],
			'jml_sak' => null,
			'hrg_sak' => null,
			'user_peminta' => '',
			'prefix_ppsk' => $prefix_ppsk,
			'list_user' => $this->listPegawai($kodefarm),
			'tombol' => $this->tombol['create'],
			'kategori' => 'I',
			'kode_budget'=>'',
			'jml_over'=>'',
			'alasan_over'=>'',
			'sudah_minta' => $this->sakSudahMinta($periode_aktif['kode_siklus']),
		);
		echo $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/form_permintaan',$dataForm,TRUE);
	}

	public function getToleransiBerat(){
		$kodefarm 	 = $this->session->userdata('kode_farm');
		$this->load->model('permintaan_glangsing/m_ppsk','ppsk');
		$result = $this->ppsk->getToleransiBerat($kodefarm);
		if(count($result) > 0){
			$data = $result[0];
			$data['status'] = '1';
		}else{
			$data['status'] = '0';
			$data['message'] = 'Data tidak ditemukan';
		}

		echo json_encode($data);
	}

	public function simpan(){
		$status_save = true;
		$kodefarm 	 = $this->session->userdata('kode_farm');
		$data 		 = $this->input->post('data');
		$tglServer = Modules::run('home/home/getDateServer');
		$tgl_buat = $tglServer->tglserver;
		$this->load->model('permintaan_glangsing/m_permintaan_sak','ppsk');
		$this->load->model('permintaan_glangsing/m_ppsk','mpsk');
		$this->load->model('permintaan_glangsing/m_ppsk_d','mpskd');
		$this->load->model('permintaan_glangsing/m_log_ppsk','mlpsk');
		$this->load->model('forecast/m_kandang_siklus','ks');
		$this->load->model('pengembalian_sak/m_glangsing_movement','gm');
		$this->load->model('pengembalian_sak/m_glangsing_movement_d','gmd');
		$kodeGlangsingBekasPakai = 'GBP';
		$periode = $this->ks->siklus_farm($kodefarm)->result_array();
		$periode_siklus = '';
		if(count($periode)>0){
			$periode_siklus = $periode[0]['periode_siklus'];
		}

		if(isset($data['no_ppsk']) && strlen($data['no_ppsk']) > 14){
			$ref_id = $data['no_ppsk'];
		}else{
			$ref_id = 'PPSK/'.$kodefarm.'/'.$periode_siklus.'/';
		}
		$noPpsk		 = $this->ppsk->no_ppsk($ref_id);
		$no_urut_log_ppsk = $this->ppsk->no_urut_log_ppsk($data['no_ppsk']);

		$message = array(
			'success' => array(
				'D' => 'Permintaan sak kosong berhasil disimpan sebagai draft',
				'N' => 'Permintaan sak kosong berhasil disimpan',
			),
			'error' => array(
				'D' => 'Permintaan sak kosong gagal disimpan',
				'N' => 'Permintaan sak kosong gagal disimpan',
			)
		);

		$this->dbSqlServer->trans_begin();

		$data_ppsk = array();
		//cetak_r($no_ppsk);
		$data_ppsk['NO_PPSK'] 		= $noPpsk;
		if($data['no_ppsk'] != ''){
			$data_ppsk['ref_id'] 		= $data['no_ppsk'];
			$this->result['ref_id'] = $data['no_ppsk'];
		}
		$data_ppsk['jml_diminta'] 		= (isset($data['sakdiminta'])) ? $data['sakdiminta'] : NULL;
		$data_ppsk['tgl_permintaan'] = (isset($data['tglPermintaan'])) ? $data['tglPermintaan'] : NULL;
		$data_ppsk['tgl_kebutuhan'] = (isset($data['tglKebutuhan'])) ? $data['tglKebutuhan'] : NULL;
		$data_ppsk['KODE_BUDGET'] 	= (isset($data['kode_budget'])) ? $data['kode_budget'] : NULL;
		//$data_ppsk['NO_DO']		 	= (isset($data['no_do'])) ? $data['no_do'] : NULL;
		$data_ppsk['jml_over_budget'] 		= (isset($data['jml_over'])) ? $data['jml_over'] : NULL;
		$data_ppsk['keterangan'] 	= (isset($data['alasanOver'])) ? $data['alasanOver'] : NULL;
		$data_ppsk['kode_siklus'] 	= (isset($data['kodeSiklus'])) ? $data['kodeSiklus'] : NULL;
		/* insert ke tabel glangsing_movement_d */
		$kodeSiklus = $data_ppsk['kode_siklus'];
		$kodeBudget = $data_ppsk['KODE_BUDGET'];
		$whereGlangsingMovement = array('kode_barang' => $kodeGlangsingBekasPakai,'kode_farm' => $kodefarm,'kode_siklus' => $kodeSiklus);
		$whereGlangsingBudgetMovement = array('kode_barang' => $kodeBudget,'kode_farm' => $kodefarm,'kode_siklus' => $kodeSiklus);
		$stokAkhirGlangsing = $this->gm->get_by($whereGlangsingMovement);
		$stokAkhirGlangsingBudget = $this->gm->get_by($whereGlangsingBudgetMovement);
		$jmlStokAwalGBP = 0;
		$jmlStokAwalBudget = 0;
		if(!empty($stokAkhirGlangsing)){
			$jmlStokAwalGBP = $stokAkhirGlangsing->jml_stok;
		}
		if(!empty($stokAkhirGlangsingBudget)){
			$jmlStokAwalBudget = $stokAkhirGlangsingBudget->jml_stok;
		}else{
			/* insert ke glangsing_movement */
			$whereGlangsingBudgetMovement['jml_stok'] = 0;
			$this->gm->insert($whereGlangsingBudgetMovement);
		}

		$glangsingMovementGBP = array(
			'kode_farm' => $kodefarm,
			'kode_siklus' => $kodeSiklus,
			'kode_barang' => $kodeGlangsingBekasPakai,
			'no_referensi' => $noPpsk,
			'jml_awal' => 0,
			'jml_order' => 0,
			'jml_akhir' => 0,
			'tgl_transaksi' => $tgl_buat,
			'keterangan1' => 'OUT',
			'keterangan2' => '',
			'user_buat' => $this->_user,
		);

		$glangsingMovementBudget = array(
			'kode_farm' => $kodefarm,
			'kode_siklus' => $kodeSiklus,
			'kode_barang' => $kodeBudget,
			'no_referensi' => $noPpsk,
			'jml_awal' => 0,
			'jml_order' => 0,
			'jml_akhir' => 0,
			'tgl_transaksi' => $tgl_buat,
			'keterangan1' => 'IN',
			'keterangan2' => '',
			'user_buat' => $this->_user,
		);
		$stokMinta = 0;
		$this->dbSqlServer->insert("ppsk_new", $data_ppsk);
		$status_save = ($this->dbSqlServer->affected_rows() > 0) ? $status_save : false;
		$this->result['no_ppsk'] = $noPpsk;
		$data_ppsk_d = array();
		foreach ($data['data'] as $list) {
			$data_ppsk_d = array(
				'no_ppsk' => $noPpsk
				, 'no_reg' => $list['noReg']
				, 'jml_diminta' => $list['value']
			);
			$this->dbSqlServer->insert("ppsk_d", $data_ppsk_d);
			$status_save = ($this->dbSqlServer->affected_rows() > 0) ? $status_save : false;
			$stokMinta += $list['value'];
			/*
			if(!empty($list['value'])){
				$glangsingMovementGBP['keterangan2'] = $list['noReg'];
				$glangsingMovementGBP['jml_order'] = -1 * $list['value'];
				$glangsingMovementGBP['jml_awal'] =  $jmlStokAwalGBP;
				$glangsingMovementGBP['jml_akhir'] =  $jmlStokAwalGBP - $list['value'];
				$jmlStokAwalGBP = $glangsingMovementGBP['jml_akhir'];

				$glangsingMovementBudget['keterangan2'] = $list['noReg'];
				$glangsingMovementBudget['jml_order'] = $list['value'];
				$glangsingMovementBudget['jml_awal'] =  $jmlStokAwalBudget;
				$glangsingMovementBudget['jml_akhir'] =  $jmlStokAwalBudget + $list['value'];
				$jmlStokAwalBudget = $glangsingMovementBudget['jml_akhir'];

				$this->gmd->insert($glangsingMovementGBP);
				$this->gmd->insert($glangsingMovementBudget);
			}	*/
		}
		/** gak usah per kandang, tapi pertransaksi aja */
		$glangsingMovementGBP['keterangan2'] = '';
		$glangsingMovementGBP['jml_order'] = -1 * $stokMinta;
		$glangsingMovementGBP['jml_awal'] =  $jmlStokAwalGBP;
		$glangsingMovementGBP['jml_akhir'] =  $jmlStokAwalGBP - $stokMinta;
		$this->gmd->insert($glangsingMovementGBP);

		$glangsingMovementBudget['keterangan2'] = '';
		$glangsingMovementBudget['jml_order'] = $stokMinta;
		$glangsingMovementBudget['jml_awal'] =  $jmlStokAwalBudget;
		$glangsingMovementBudget['jml_akhir'] =  $jmlStokAwalBudget + $stokMinta;
		$this->gmd->insert($glangsingMovementBudget);

		unset($whereGlangsingBudgetMovement['jml_stok']);
		$this->gm->update_by($whereGlangsingBudgetMovement,array('jml_stok' => $jmlStokAwalBudget + $stokMinta));
		$this->gm->update_by($whereGlangsingMovement,array('jml_stok' => $jmlStokAwalGBP - $stokMinta));

		$data_log_ppsk = array(
			'no_ppsk' => $noPpsk
			, 'user_buat' => $this->_user
			, 'keterangan' => ($data['no_ppsk'] != '') ? 'Revisi '.$data['no_ppsk'] : ''
		);
		$this->dbSqlServer->insert("log_ppsk_new", $data_log_ppsk);
		$status_save = ($this->dbSqlServer->affected_rows() > 0) ? $status_save : false;

		if(isset($data['no_ppsk']) && strlen($data['no_ppsk']) > 14){
			$data_log_ppsk = array(
				'no_ppsk' => $data['no_ppsk']
				, 'user_buat' => $this->_user
				, 'no_urut' => $no_urut_log_ppsk
				, 'status' => 'V'
				, 'keterangan' => 'Telah direvisi dengan No. '.$noPpsk
			);
			$this->dbSqlServer->insert("log_ppsk_new", $data_log_ppsk);
			$status_save = ($this->dbSqlServer->affected_rows() > 0) ? $status_save : false;
		}

		if($status_save){
         	$this->dbSqlServer->trans_commit();
			// $this->mpskd->calculate_glangsing_movement($this->result['no_ppsk']);
			$this->result['result'] = 'success';
			$this->result['status'] = 1;
			$this->result['message'] = $message['success'][$data['status']];
			$this->result['kode_farm'] = $kodefarm;
			$this->result['kode_siklus'] = $kodeSiklus;
			$this->result['kode_barang'] = $kodeBudget;
		}
		else{
			$this->dbSqlServer->trans_rollback();
			$this->result['result'] = 'error';
			$this->result['status'] = 0;
			$this->result['message'] = $message['error'][$data['status']];
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function konfirmasiPengambilan(){
		//$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');

		$where = array(
			'no_ppsk'	=> $this->input->post('no_ppsk')
			, 'no_reg' 	=> $this->input->post('no_reg')
			);
		$value = array(
			'brt_timbang'	=> $this->input->post('brt_timbang')
			, 'user_penerima' 	=> $this->input->post('user_penerima')
			, 'tgl_terima' 	=> $this->input->post('tgl_terima')
			);

		$this->dbSqlServer->trans_begin();
		$this->dbSqlServer->where($where);
		$this->dbSqlServer->update("ppsk_d", $value);
		$status_save = ($this->dbSqlServer->affected_rows() > 0);


		if($status_save){
         $this->dbSqlServer->trans_commit();
			$this->result['result'] = 'success';
			$this->result['no_ppsk'] = $where['no_ppsk'];
			$this->result['no_reg'] = $where['no_reg'];
			$this->result['status'] = 1;
			$this->result['message'] = '';
		}
		else{
         $this->dbSqlServer->trans_rollback();
			$this->result['result'] = 'error';
			$this->result['status'] = 0;
			$this->result['message'] = 'Konfirmasi Gagal';
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));

	}

	public function getBudgetTotal($kode_farm,$kategori,$kode_budget){
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		return $this->mps->getBudgetTotal($kode_farm,$kategori,$kode_budget);
	}

	public function getBudgetTerpakai($prefix_ppsk,$kode_siklus,$kode_budget){
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		return $this->mps->getBudgetTerpakai($prefix_ppsk,$kode_siklus,$kode_budget);
	}

	public function getBudgetSudahDiplot($kode_siklus,$kode_budget){
		//$t = $this->db->where(array('kode_barang' => $kode_budget, 'kode_siklus' => $kode_siklus))->get('glangsing_movement')->row();
		$sudahDiminta = 0;
		$t = $this->db->select('sum(pd.jml_diminta) - sum(coalesce(pd.jml_kembali,0)) jml_stok',FALSE)
					->where(array('pn.kode_budget' => $kode_budget, 'pn.kode_siklus' => $kode_siklus))
					->where('pd.status_retur')
					->join('ppsk_d pd','pn.no_ppsk = pd.no_ppsk')
					->get('ppsk_new pn')
					->row();
		if(!empty($t)){
			$sudahDiminta = $t->jml_stok;
		}
		return $sudahDiminta;
	}
	public function getDaftarPermintaan($no_ppsk = '',$kodefarm = '',$kode_budget = '', $tglKebutuhan = null)
	{
		$list = $this->mps->getDaftarPermintaan($no_ppsk,$kode_budget,$tglKebutuhan);
		$str = '';
		if(count($list) == 0){
			$list = $this->mps->getDaftarPermintaanNew($kodefarm,$tglKebutuhan);
		}
		//cetak_r($list);

		foreach ($list as $value) {
			$str .= '<tr data-noreg="'.$value['no_reg'].'">';
				$str .= '<td>'.$value['NAMA_KANDANG'].'</td>';
				$str .= '<td>'.$value['umur'].'</td>';
				$str .= '<td>'.(($no_ppsk != '') ? $value['jml_diminta'] : '<input class="form-control number jmlSak" name="jmlSak" onchange="permintaanSak.onchangeSak(this);" id="jmlSak" style="width: 100px" value="'.$value['jml_diminta'].'" type="text">').'</td>';
			$str .= '</tr>';
		}

		return $str;

	}

	public function getSakTerpakai($kode_farm,$periode_siklus){
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		return $this->mps->getSakTerpakai($kode_farm,$periode_siklus);
	}

	public function getJumlahSak(){
		// $kodefarm 	 = $this->session->userdata('kode_farm');
		$no_ppsk = $this->input->post('no_ppsk');
		$tglKebutuhan = $this->input->post('tgl_kebutuhan');
		$kategori 	 = $this->input->post('kategori');
		$kode_budget = $this->input->post('kode_budget');
		$kodefarm 	 = $this->input->post('kodefarm');
		$periode_aktif = $this->db->select('periode_siklus, kode_siklus')->where(array('kode_farm' => $this->_farm,'status_periode'=>'A'))->get('M_PERIODE')->row_array();
		$result = $this->getJumlahGlangsing($no_ppsk, $tglKebutuhan, $kategori, $kode_budget, $kodefarm);
		$result['budgetsisa']  = $result['budgettotal'] - $this->getBudgetSudahDiplot($periode_aktif['kode_siklus'],$kode_budget);
		echo json_encode($result);
	}

	public function getJumlahGlangsing($no_ppsk, $tglKebutuhan, $kategori, $kode_budget, $kodefarm){
		$budgettotal 	= $this->getBudgetTotal($kodefarm,$kategori,$kode_budget);
		$periode_aktif = $this->db->select('periode_siklus, kode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode' => 'A'))->get('M_PERIODE')->row_array();
		$budgetterpakai = $this->getBudgetTerpakai($kodefarm,$periode_aktif['kode_siklus'],$kode_budget);
		$daftarpermintaan = $this->getDaftarPermintaan($no_ppsk, $kodefarm, $kode_budget, $tglKebutuhan);
		$budgetsisa 	= $budgettotal - $budgetterpakai;

		$result['daftarpermintaan'] = $daftarpermintaan;
		$result['budgettotal'] = $budgettotal;
		$result['budgetterpakai'] = $budgetterpakai;
		$result['budgetsisa']  = ($budgetsisa > 0) ? $budgetsisa : 0;
		return $result;
	}

	public function getHistoriPermintaanSak($kodefarm = '', $log_ppsk_prop = '')
	{
		$data['histori_permintaan'] = $this->GetListHistori($kodefarm);
		$data['log_ppsk_prop'] = $log_ppsk_prop;
		return $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/histori_permintaan',$data,TRUE);
	}

	public function loadViewHistory()
	{
		$no_ppsk  = $this->input->post('no_ppsk');
		$kodefarm = $this->input->post('kodefarm');
		$data['histori_permintaan'] = $this->GetListHistori($kodefarm,$no_ppsk);
		$data['log_ppsk_prop'] = 'inline';
		echo $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/histori_permintaan',$data,TRUE);
	}
	public function GetListHistori($kodefarm = '', $no_ppsk = '')
	{

		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		$periode_aktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode' => 'A'))->get('M_PERIODE')->row_array();
		/* cari pada periode yang aktif saja */
		return $this->mps->listHistori($kodefarm,$periode_aktif['periode_siklus'],$no_ppsk)->result_array();
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
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		return $this->mps->getListFarm($user_id);
	}
	function loadListPermintaan(){
		$kode_farm = $this->input->post('kode_farm');
		$no_ppsk = $this->input->post('no_ppsk');
        $kode_budget = $this->input->post('kode_budget');
        $tgl_awal = $this->input->post('tgl_awal');
		$tgl_akhir = $this->input->post('tgl_akhir');
		$filter = array(
			'kode_farm' => $kode_farm
		);
		if(!empty($no_ppsk)){
			$filter['ph.no_ppsk'] = $no_ppsk;
		}
		if(!empty($kode_budget)){
			$filter['ph.kode_budget'] = $kode_budget;
		}
		if(!empty($tgl_awal)){
			$filter['tgl_kebutuhan']['tgl_awal'] = $tgl_awal;
		}
		if(!empty($tgl_akhir)){
			$filter['tgl_kebutuhan']['tgl_akhir'] = $tgl_akhir;
		}


		$dataPermintaan = array(
			'list_permintaan' => $this->listPermintaan($filter,$this->_statusPPSK,$this->_orderTabel)
		);
		echo $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/list_permintaan',$dataPermintaan,TRUE);
	}
	function getKodeFarm($no_ppsk = ''){
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		return $this->mps->getKodeFarm($no_ppsk);
	}

	function sakSudahMinta($kode_siklus){
		$this->load->model('permintaan_glangsing/m_ppsk','mpppsk');
		$result = array();
		$t = $this->mpppsk->sudahMintaSak($kode_siklus);
		if(!empty($t)){
			foreach($t as $_t){
				$result[] = $_t['kode_budget'];
			}
		}
		return $result;
	}

	public function cek_rfid_kandang(){
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		$kode_kandang = $this->input->post('kode_kandang');
		$rfid = $this->input->post('rfid');
		$res = $this->mps->getRfidKandang($rfid);
		if(!empty($res)){
			if($res[0]['KODE_KANDANG'] == $kode_kandang){
				$this->result['success'] = true;
			}else {
				$this->result['success'] = false;
				$this->result['msg'] = 'rfid tidak bukan untuk kandang ini!';
			}
		}else {
			$this->result['success'] = false;
			$this->result['msg'] = 'rfid tidak ditemukan!';
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}
}
