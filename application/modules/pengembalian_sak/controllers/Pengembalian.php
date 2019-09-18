<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Pengembalian extends MY_Controller {
	protected $result;
	protected $_user;
	private $grup_farm;
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'stpakan' );
		$this->_user = $this->session->userdata ( 'kode_user' );
		$this->result = array (
				'status' => 0,
				'content' => ''
		);
		$this->load->model('pengembalian_sak/m_pengembalian_sak','mps');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
	}
	public function index() {
		$kode_farm = $this->session->userdata ( 'kode_farm' );
		$tombol_buat = '<div class="btn btn-default" data-aksi="transaksi_pengembalian" data-no_pengembalian="" onclick="Pengembalian.transaksi(this,\'#transaksi\')">Buat Baru</div>';
		$data['list_pp'] = null;
		$data['buat_baru'] = $tombol_buat;
		$lockTimbangan = $this->db->select(array('kode_config','value'))->where(array('kode_config' => '_lock_timbang_sak','kode_farm' => $kode_farm,'context' => 'pengembalian_sak','status' => '1'))->get('SYS_CONFIG_GENERAL')->row_array();
        $data['lockTimbangan'] = empty($lockTimbangan) ? 1 : $lockTimbangan['value'];
		$this->load->view('pengembalian_sak/'.$this->grup_farm.'/pengembalian',$data);
	}
	public function transaksi(){
		$tgl_server = $this->input->post('tgl_server');
		$no_pengembalian = $this->input->post('no_pengembalian');
		$header = array();
		$perpakan = array();
		$tombol_simpan = '';
		if(empty($no_pengembalian)){
			$jam = date('H:i');
			$data['tgl_pengembalian'] = tglIndonesia($tgl_server,'-',' ').' '.$jam;
			$tombol_simpan = '<div class="btn btn-default" data-aksi="simpan" onclick="Pengembalian.simpan(this)">Simpan</div>';
		}
		else{
			$noreg = substr($no_pengembalian,0,strlen($no_pengembalian) - 4);
			$nourut = substr($no_pengembalian,-3,3);
			$d = $this->mps->view_pengembalian($noreg,$nourut)->result_array();
			foreach($d as $e){
				if(empty($header)){
					$header = array('no_pengembalian' => $no_pengembalian, 'tgl_buat' => $e['TGL_BUAT'], 'kandang' => $e['NAMA_KANDANG']);
				}
				$kodepj = $e['KODE_PAKAN'];
				$jk = $e['JENIS_KELAMIN'];
				if(!isset($perpakan[$kodepj])){
					$perpakan[$kodepj] = array();
				}
				if(!isset($perpakan[$kodepj][$jk])){
					$perpakan[$kodepj][$jk] = array();
					$perpakan[$kodepj][$jk]['detail'] = array();
					$perpakan[$kodepj][$jk]['header'] = array(
							'JML_KIRIM' => $e['JML_KIRIM']
							,'JML_PAKAI' => $e['JML_PAKAI']
							,'HUTANG' => $e['HUTANG']
							,'NAMA_BARANG' => $e['NAMA_BARANG']
							,'JML_SAK' => 0
							,'KETERANGAN' => html_entity_decode($e['KETERANGAN'])
					);
				}
				$perpakan[$kodepj][$jk]['header']['JML_SAK'] += $e['JML_SAK'];
				$tmp = array(
					'JML_SAK' => $e['JML_SAK'],
					'BRT_SAK' => $e['BRT_SAK']
				);
				array_push($perpakan[$kodepj][$jk]['detail'],$tmp);
			}
			$data['tgl_pengembalian'] = convertElemenTglWaktuIndonesia($header['tgl_buat']);
			$data['list_pakan'] = $this->load->view('pengembalian_sak/'.$this->grup_farm.'/view_transaksi',array('perpakan' => $perpakan),true);
		}

		$data['div_tombol_simpan'] = $tombol_simpan;
		$data['header'] = $header;

		$this->load->view('pengembalian_sak/'.$this->grup_farm.'/transaksi',$data);
	}
	
	public function get_berat_timbang (){
		echo file_get_contents(base_url('api/Timbangan/timbang'));
	}
	
	public function simpan(){
		$data = $this->input->post('data');
		$noreg = $this->input->post('noreg');
		$headerpj = $this->input->post('headerpj');
		$tglServer = Modules::run('home/home/getDateServer');
		$kodeGlangsing = 'GBP';
		$kodeFarm = $this->session->userdata('kode_farm');
		/* cek apakah masih bisa melakukan pengembalian atau tidak, max adalah h+1 pada jam 09 dari lhk */
	//	if($this->cek_max_pengembalian($no_reg)){
			$this->load->model('pengembalian_sak/m_retur_sak_kosong','rsk');
			$this->load->model('pengembalian_sak/m_retur_sak_kosong_item_pakan','rskip');
			$this->load->model('pengembalian_sak/m_retur_sak_kosong_item_timbang_pakan','rskitp');
			if(!empty($data)){
				$this->db->trans_start();
				// simpan headernya
				$data_header = array(
					'no_reg' => $noreg,
					'user_buat' => $this->_user,
					'user_verifikasi' => $this->_user
				);
				$id_rsk = $this->rsk->simpan($data_header);

				$punya_hutang = 0;
				$this->load->model('forecast/m_kandang_siklus','mks');
				$dataSiklus = $this->mks->get_by(array('NO_REG' => $noreg));
				$glangsing_movement = array(
					'kode_farm' => $kodeFarm,
					'kode_siklus' => $dataSiklus->KODE_SIKLUS,
					'kode_barang' => $kodeGlangsing,
					'no_referensi' => $id_rsk['id'],
					'jml_awal' => 0,
					'jml_order' => 0,
					'jml_akhir' => 0,
					'tgl_transaksi' => $tglServer->tglserver,
					'keterangan1' => 'IN',
					'keterangan2' => $noreg,
					'user_buat' => $this->_user,
				);
				foreach($data as $kode_barang => $perjeniskelamin){
					foreach($perjeniskelamin as $jk => $detail){
						// simpan header per jenis kelamin
						$data_header_jk['retur_sak_kosong'] = $id_rsk['id'];
						$data_header_jk['kode_pakan'] = $kode_barang;
						$data_header_jk['jenis_kelamin'] = $jk;
						$data_header_jk['keterangan'] = !empty($headerpj[$kode_barang][$jk]['keterangan']) ? htmlentities($headerpj[$kode_barang][$jk]['keterangan']) : null;
						$data_header_jk['jml_kirim'] = $headerpj[$kode_barang][$jk]['kirim'];
						$data_header_jk['jml_pakai'] = $headerpj[$kode_barang][$jk]['pakai'];
						$data_header_jk['hutang'] = $headerpj[$kode_barang][$jk]['hutang'];
						$id_rskip = $this->rskip->simpan($data_header_jk);

						$i = 1;
						foreach($detail as $item){
							$data_item['retur_sak_kosong_item_pakan'] = $id_rskip['id'];
							$data_item['jml_sak'] = $item['jml_k'];
							$data_item['brt_sak'] = $item['brt_k'];
							$data_item['no_urut'] = $i++;
							$this->rskitp->insert($data_item);
							/* kumpulkan data untuk disimpan di glangsing_movement_d */
							$glangsing_movement['jml_order'] += $item['jml_k'];
						}

						if($headerpj[$kode_barang][$jk]['hutang'] > 0){
							$punya_hutang++;
						}
					}
				}
				/* simpan ke glangsing_movement_d */
				$this->load->model('pengembalian_sak/m_glangsing_movement','gm');
				$this->load->model('pengembalian_sak/m_glangsing_movement_d','gmd');
				$whereGlangsingMovement = array('kode_barang' => $kodeGlangsing,'kode_farm' => $kodeFarm,'kode_siklus' => $dataSiklus->KODE_SIKLUS);
				$stokAkhirGlangsing = $this->gm->get_by($whereGlangsingMovement);
				$glangsing_movement['jml_awal'] = $stokAkhirGlangsing->jml_stok;
				$glangsing_movement['jml_akhir'] = $stokAkhirGlangsing->jml_stok + $glangsing_movement['jml_order'];
				$this->gmd->insert($glangsing_movement);
				/* update juga glangsing_movement */
				$this->gm->update_by($whereGlangsingMovement,array('jml_stok' => $glangsing_movement['jml_akhir']));

				/* jika ada hutang tulis ke table review_hutang_retur_sak */
				if($punya_hutang){
					$this->load->model('pengembalian_sak/m_review_hutang_retur_sak','rhrs');
					$data_review = array(
						'no_reg' => $noreg,
						'retur_sak_kosong' => $id_rsk['id']
					);
					$this->rhrs->insert($data_review);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === FALSE){
					$this->result['content'] = 'Gagal menyimpan..';
				}
				else{
					$this->result['status'] = 1;
					$this->result['content'] = array(
										'no_pengembalian' => $noreg.'-'.$id_rsk['no_urut']
										,'tgl_buat' => convertElemenTglWaktuIndonesia($id_rsk['tgl_buat'])
										,'rsk' =>$id_rsk['id']
										,'kode_farm' => $kodeFarm
										,'kode_siklus' => $dataSiklus->KODE_SIKLUS
										,'kode_barang' => $kodeGlangsing
									);
				}
			}
	/*
		}
		else{
			$this->result['content'] = 'Melebihi batas maksimal pengembalian sak';
		}
	*/
	//	echo json_encode($this->result);
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}
	public function detail_transaksi(){
		$data = array();
		$no_reg = $this->input->post('no_reg');
		$tipe = $this->input->post('tipe');
		$rowid = $this->input->post('rowid');
	//	$sudah_input_rhk = $this->cek_input_lhk($no_reg);
	/** dicek ketika  scan rfid */
		$sudah_input_rhk = 1;
		if($sudah_input_rhk){
			/* cek apakah masih bisa melakukan pengembalian atau tidak, max adalah h+1 pada jam 09 dari lhk */
		//	if($this->cek_max_pengembalian($no_reg)){
				$list_pakan = $this->mps->get_list_pakan_terpakai($no_reg)->result_array();
<<<<<<< HEAD
				//log_message('error',$this->db->last_query());
=======
				log_message('error',$this->db->last_query());
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
				$pakan_dikirim = $this->mps->get_list_pakan_dikirim($no_reg)->result_array();
				$stok_akhir = $this->mps->get_list_pakan_akhir($no_reg)->result_array();

				$sak_kembali = $this->mps->get_pengembalian_sak($no_reg)->result_array();
				$data['list_pakan'] = $list_pakan;
				$data['no_reg'] = $no_reg;
				$data['pakan_dikirim'] = $this->grouping_kodepj($pakan_dikirim);
				$data['sak_kembali'] = $this->grouping_kodepj($sak_kembali);
				$data['nama_kandang'] = $this->mps->get_nama_kandang($no_reg);
				$data['maxjmltimbang'] = $this->grouping_sakAkhir($stok_akhir);
				$data['tipe'] = $tipe;
				$data['rowid'] = $rowid;
<<<<<<< HEAD
				$data['lepaskontrol'] = $this->control_lhk();
=======
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
				$this->load->view('pengembalian_sak/'.$this->grup_farm.'/detail_transaksi',$data);
	/*		}
			else{
				echo '<div class="alert alert-danger">Melebihi batas maksimal pengembalian</div>';
			}
	*/
		}
		else {
			echo '<div class="alert alert-danger col-md-12">Laporan harian kandang belum diinput</div>';
		}
	}
	public function getRFID(){
		$rfid = $this->input->post('rfid');
		$result = $this->mps->getKandangByRFID($rfid)->row_array();
		$data = array();
		if(!empty($result)){
			$no_reg = $result['no_reg'];
			$sudah_input_rhk = $this->cek_input_lhk($no_reg);
<<<<<<< HEAD
			$control_lhk = $this->control_lhk();
			if($control_lhk){
				$sudah_input_rhk = 1;
			}
=======
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
			if($sudah_input_rhk){
				$this->result['status'] = 1;
				$this->result['content'] = array('no_reg' => $result['no_reg'],'nama_kandang' => $result['nama_kandang'],'flok_bdy' => $result['flok_bdy'], 'pengawas' => $result['pengawas']);												
			}else{
				$this->result['message'] = 'Mohon cetak LHK terlebih dahulu'; 
			}
		}else{
			$this->result['message'] = 'RFID tidak dikenal';
		}	
		
		echo json_encode($this->result);
	}

	private function cek_max_pengembalian($noreg){
		$t = $this->mps->cek_max_pengembalian($noreg)->row();
		return $t->status;
	}

	/* tampilkan semua kandang pada farm ini saja */
	public function list_kandang(){
		$nama_kandang = $this->input->post('nama_kandang');
		$kodefarm = $this->session->userdata ('kode_farm');

		$list = $this->mps->get_kandang($kodefarm,$nama_kandang)->result_array();
		echo json_encode($list);
	}

	public function list_pengembalian(){
		/*$tanggal_cari = $this->input->post('tanggal');
		$kodefarm = $this->session->userdata ('kode_farm');
		if(!empty($tanggal_cari['operand'])){
			switch($tanggal_cari['operand']){
				case 'between':
					$custom_param = $tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\' and \''.$tanggal_cari['endDate'].'\'';
					break;
				case '<=':
					$custom_param= $tanggal_cari['operand'].' \''.$tanggal_cari['endDate'].'\'';
					break;
				case '>=':
					$custom_param= $tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\'';
					break;
			}
		}
		$list_pengembalian = $this->mps->list_pengembalian_sak($kodefarm,$custom_param)->result_array();
		$data['list_pengembalian'] = $list_pengembalian;
		$this->load->view('pengembalian_sak/'.$this->grup_farm.'/list_pengembalian',$data);*/
		
		$kodefarm = $this->session->userdata('kode_farm');
		$list_pengembalian = $this->mps->list_pengembalian_sak($kodefarm);
		$data['list_pengembalian'] = $list_pengembalian->result_array();
		$this->load->view('pengembalian_sak/'.$this->grup_farm.'/list_pengembalian',$data);
	}

	/* grouping berdasarkan kodepj dan jeniskelamin */
	private function grouping_kodepj($data){
		if(empty($data)) return null;
		$t = array();
		foreach($data as $d){
			$kode_barang = $d['kode_barang'];
			$jk = $d['jenis_kelamin'];
			if(!isset($t[$kode_barang])){
				$t[$kode_barang] = array();
			}
			$t[$kode_barang][$jk] = $d['jml_kirim'];
		}
		return $t;
	}

	private function grouping_sakAkhir($data){
		if(empty($data)) return null;
		//print_r($data);
		$t = array();
		foreach($data as $d){
			$kode_barang = $d['kode_barang'];
			$jk = $d['jenis_kelamin'];
			if(!isset($t[$kode_barang])){
				$t[$kode_barang] = array();
			}
			$t[$kode_barang][$jk]['stok'] = $d['jml_in'] - ($d['jml_out'] + $d['jml_sak']);
			$t[$kode_barang][$jk]['lhk'] = $d['jml_pakai'] - $d['jml_sak'];
		}
		return $t;
	}

	/* grouping berdasarkan kodepj dan jeniskelamin */
	/*private function grouping_stok_akhir($data){
		//print_r($data);
		if(empty($data)) return 0;
		$t = 0;
		foreach($data as $d){

			$t += $d['JML_IN'] - ($d['JML_OUT'] + $d['JML_SAK']);
		}
		return $t;
	}*/

	private function cek_input_lhk($noreg){
		$sudah_input = count($this->mps->cek_input_lhk($noreg)->row_array());
//		$sudah_input = 1;
		return $sudah_input;
	}

	public function check_pengembalian_hari_ini(){
		$flok = $this->input->get('flok');
		$kodefarm = $this->session->userdata('kode_farm');
		$pengembalian = $this->mps->check_pengembalian_hari_ini($kodefarm,$flok)->result();
		$result = array('status' => 0, 'message' => '', 'content' => array());
		if(empty($pengembalian)){
			/* kalau kosong berarti tidak ada sak yang perlu dikembalikan */
			$result['status'] = 1;
		}else{
			foreach($pengembalian as $p){
				if(empty($p->no_reg)){
					array_push($result['content'],$p->ksnoreg);/* berarti belum input pengembalian sak */
				}
			}
			if(!empty($result['content'])){
				$result['status'] = 0;
				/* cek hutang sak */
				$hutang_sak = $this->mps->get_sisa_hutang($kodefarm,$flok)->result();
				$punya_hutang = array();
				foreach($hutang_sak as $hs){
					if($hs->hutang_retur > 0){
						$punya_hutang[$hs->no_reg] = $hs->hutang_retur;
					}
				}
				/* jika gak punya hutang boleh langsung */
				if(empty($punya_hutang)){
					$result['status'] = 1;
				}
			}else{
				$result['status'] = 1;
			}
		}
		echo json_encode($result);
	}

	public function check_pengembalian_noreg_hari_ini(){
		$noreg = $this->input->get('noreg');		
		$pengembalian = $this->mps->check_pengembalian_noreg_hari_ini($noreg)->result();
		$result = array('status' => 0, 'message' => '', 'content' => array());
		if(empty($pengembalian)){
			/* kalau kosong berarti tidak ada sak yang perlu dikembalikan */
			$result['status'] = 1;
		}else{
			foreach($pengembalian as $p){
				if(empty($p->no_reg)){
					array_push($result['content'],$p->ksnoreg);/* berarti belum input pengembalian sak */
				}
			}
			if(!empty($result['content'])){
				$result['status'] = 0;
				/* cek hutang sak */
				$hutang_sak = $this->mps->get_sisa_hutang_noreg($noreg)->result();
				$punya_hutang = array();
				foreach($hutang_sak as $hs){
					if($hs->hutang_retur > 0){
						$punya_hutang[$hs->no_reg] = $hs->hutang_retur;
					}
				}
				/* jika gak punya hutang boleh langsung */
				if(empty($punya_hutang)){
					$result['status'] = 1;
				}
			}else{
				$result['status'] = 1;
			}
		}
		echo json_encode($result);
	}

<<<<<<< HEAD
	private function control_lhk(){
		$result = 0;
		$kode_farm = $this->session->userdata ( 'kode_farm' );
		$lockTimbangan = $this->db->select(array('kode_config','value'))->where(array('kode_config' => '_lepas_kontrol_lhk','kode_farm' => $kode_farm,'context' => 'pengembalian_sak','status' => '1'))->get('SYS_CONFIG_GENERAL')->row_array();
		if(!empty($lockTimbangan)){
			$result = $lockTimbangan['value'];
		}
		return $result;
	}

=======
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
}
