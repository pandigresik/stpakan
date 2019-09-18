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
	protected $_username;
	private $grup_farm;
	public function __construct() {
		parent::__construct ();
		$this->load->helper ( 'stpakan' );
		$this->_user = $this->session->userdata ( 'kode_user' );
		$this->_username = $this->session->userdata ( 'nama_user' );
		$this->result = array (
				'status' => 0,
				'content' => ''
		);
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->load->model('pengembalian_pakan_rusak/m_pengembalian_pakan_rusak','mppr');
	}
	public function index() {
		$tombol_buat = '<div class="btn btn-default" data-aksi="transaksi_pengembalian" data-no_pengembalian="" onclick="Pengembalianpakan.transaksi(this,\'#for_transaksi\')">Baru</div>';
		$data['list_pp'] = null;
		$data['buat_baru'] = $tombol_buat;
		$this->load->view('pengembalian_pakan_rusak/pengembalian',$data);
	}

	public function get_user_pengawas(){
		$kodefarm = $this->session->userdata('kode_farm');
		echo json_encode($this->mppr->get_user($kodefarm,'PPB'));
	}

	public function transaksi(){
		$tgl_server = $this->input->post('tgl_server');
		$no_pengembalian = $this->input->post('no_pengembalian');
		$header = array();
		$perpakan = array();
		$tombol_simpan = '';
		$data['nama_kandang'] = $this->input->post('nama_kandang');
		$data['admin_gudang'] = $this->_username;
		$data['user_verifikasi'] = '-';
		if(empty($no_pengembalian)){
			$jam = date('H:i');
			$data['tgl_pengembalian'] = tglIndonesia($tgl_server,'-',' ').' '.$jam;
			$tombol_simpan = '<div class="btn btn-default" data-aksi="simpan" onclick="Pengembalianpakan.akan_simpan(this)">Simpan</div>';
		}
		else{
			$noreg = substr($no_pengembalian,0,strlen($no_pengembalian) - 4);
			$nourut = substr($no_pengembalian,-3,3);
			$d = $this->mppr->view_pengembalian($noreg,$nourut)->result_array();
			foreach($d as $e){
				if(empty($header)){
					$data['admin_gudang'] = $e['admin_gudang'];
					$data['user_verifikasi'] = $e['user_verifikasi'];
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
							'JML_RETUR' => $e['JML_RETUR']
							,'JML_STOK' => $e['JML_STOK']
							,'NAMA_BARANG' => $e['NAMA_BARANG']
							,'BENTUK_BARANG' => convertKode('bentuk_barang', $e['BENTUK_BARANG'])
					);
				}
				$tmp = array(
					'BRT_SAK' => $e['BRT_SAK']
					,'KETERANGAN' => $e['KETERANGAN']
				);
				array_push($perpakan[$kodepj][$jk]['detail'],$tmp);
			}
			$data['tgl_pengembalian'] = convertElemenTglWaktuIndonesia($header['tgl_buat']);
			$data['list_pakan'] = $this->load->view('pengembalian_pakan_rusak/view_transaksi',array('perpakan' => $perpakan,'no_reg'=>$noreg,'no_pengembalian' => $no_pengembalian),true);
		}
		$data['no_pengembalian'] = $no_pengembalian;
		$data['div_tombol_simpan'] = $tombol_simpan;
		$data['header'] = $header;


		$this->load->view('pengembalian_pakan_rusak/transaksi',$data);
	}
	public function simpan(){
		$data = json_decode($this->input->post('data'),true);
		$data_detail = $data['data'];
		$noreg = $data['noreg'];
		$headerpj = $data['headerpj'];
		$user_verifikasi = $this->input->post('user_verifikasi');
		$attachment = mssql_escape(file_get_contents($_FILES['attachment']['tmp_name']));

		$tgl_server = Modules::run('home/home/getDateServer');

			$this->load->model('pengembalian_pakan_rusak/m_retur_pakan_rusak','rpr');
			$this->load->model('pengembalian_pakan_rusak/m_retur_pakan_rusak_item','rpri');
			$this->load->model('pengembalian_pakan_rusak/m_retur_pakan_rusak_item_timbang','rprit');
			$this->load->model('pengembalian_pakan_rusak/m_review_penggantian_pakan_rusak','rppr');
			if(!empty($data)){
		//		$this->db->trans_start();
				$this->db->trans_begin();
				// simpan headernya
				$data_header = array(
					'no_reg' => $noreg,
					'user_buat' => $this->_user,
					'user_verifikasi' => $user_verifikasi,
					'attachment' =>	$attachment
				);
				$id_rsk = $this->rpr->simpan($data_header);

				/* insert juga ke tabel review_penggantian_pakan_rusak */
				$data_review = array(
					'no_reg' => $noreg,
					'retur_pakan_rusak' =>  $id_rsk['id']
				);
				$this->rppr->insert($data_review);
				/* untuk menetukan kavling yang digunakan ketika insert ke movement dan movement_d*/
				switch($this->grup_farm){
					case 'brd':
							$kavling = $this->db->query('select top 1 kode_farm,no_kavling from movement where keterangan1 = \''.$noreg.'\'')->row();
							$kode_farm = $kavling->kode_farm;
							$no_kavling = $kavling->no_kavling;
							break;
					case 'bdy':
		//				$kavling = $this->db->query('select top 1 kode_farm,no_kavling from movement where keterangan1 = \''.$noreg.'\'')->row();
							$kode_farm = $this->session->userdata('kode_farm');
							$no_kavling = 'DMG';
							break;
				}
				/* cari RP terakhir */
				$rp_akhir = $this->db->select_max('no_pallet')->where(array('kode_farm' => $kode_farm,'SUBSTRING(no_pallet,0,3)' => 'RP'))->get('movement')->row();
				if(!empty($rp_akhir->no_pallet)){
					$rpterakhir = $this->get_rpterakhir($rp_akhir->no_pallet);
				}
				else{
					$rpterakhir = 'RP00000001';
				}
				foreach($data_detail as $kode_barang => $perjeniskelamin){
					foreach($perjeniskelamin as $jk => $detail){
						// simpan header per jenis kelamin
						$data_header_jk['retur_pakan_rusak'] = $id_rsk['id'];
						$data_header_jk['kode_pakan'] = $kode_barang;
						$data_header_jk['jenis_kelamin'] = $jk;
						$data_header_jk['jml_retur'] = $headerpj[$kode_barang][$jk]['retur'];
						$data_header_jk['jml_stok'] = $headerpj[$kode_barang][$jk]['stok'];
						$id_rskip = $this->rpri->simpan($data_header_jk);

						$stok_awal = $headerpj[$kode_barang][$jk]['stok'];
						$i = 1;
						$jml_retur = count($detail);
						foreach($detail as $item){
							$data_item['retur_pakan_rusak_item'] = $id_rskip['id'];
							$data_item['keterangan'] = $item['ket'];
							$data_item['brt_sak'] = $item['brt'];
							$data_item['no_urut'] = $i++;
							$this->rprit->insert($data_item);
							/* nimbangnya pasti persak */
							$stok_awal++;

						}
						$noref = 'RP/'.$noreg.'-'.$id_rsk['no_urut'];
						/* insert di kandang_movement */
						$data_insert = array(
							'no_reg' => $noreg,
							'kode_barang' => $kode_barang,
							'tgl_transaksi' => $tgl_server->tglserver,
							'jenis_kelamin' => $jk,
							'jml_awal' => $stok_awal,
							'jml_order' => -1 * $jml_retur,
							'jml_akhir' => 	$headerpj[$kode_barang][$jk]['stok'],
							'berat_awal' => $stok_awal * 50,
							'berat_order' => -1 * $jml_retur * 50,
							'berat_akhir' => $headerpj[$kode_barang][$jk]['stok'] * 50,
							'keterangan1' => 'RETUR PAKAN RUSAK',
							'keterangan2' => $noref,
							'tgl_buat' => $tgl_server->saatini,
							'user_buat' => $this->_user
						);
						$this->db->insert('kandang_movement_d',$data_insert);
						/* update stok di kandang */
						$where_data = array('no_reg' => $noreg, 'kode_barang' => $kode_barang, 'jenis_kelamin' => $jk);
						$update_data = array('jml_stok' => $headerpj[$kode_barang][$jk]['stok'], 'berat_stok' => $headerpj[$kode_barang][$jk]['stok'] * 50);
						$this->db->where($where_data)->update('kandang_movement',$update_data);

						/* insert di tabel movement dan movement_d */

						//echo $this->db->last_query();
						$data_movement = array(
							'kode_farm' => $kode_farm,
							'no_kavling' => $no_kavling,
							'no_pallet' => $rpterakhir,
							'kode_barang' => $kode_barang,
							'jenis_kelamin' => $jk,
							'jml_on_hand' => $jml_retur,
							'jml_available' => 0,
							'jml_on_putaway' => 0,
							'berat_on_putaway' => 0,
							'jml_putaway' => $jml_retur,
							'berat_putaway' => $jml_retur * 50,
							'jml_on_pick' => 0,
							'berat_on_pick' => 0,
							'jml_pick' => 0,
							'berat_pick' => 0,
							'put_date' => $tgl_server->saatini,
							'put_name' => $this->_user,
							'picked_date' => NULL,
							'picked_name' =>NULL,
							'status_stok' => 'DM',
							'keterangan1' => $noreg,
							'keterangan2' => 'RETUR'
						);
						if($this->grup_farm == 'bdy'){
							$data_movement['keterangan1'] = NULL;
						}
						$this->db->insert('movement',$data_movement);
						$data_movement['no_referensi'] = $noref;
						if($this->grup_farm == 'bdy'){
							$data_movement['keterangan1'] = 'RETUR';
							$data_movement['keterangan2'] = $noreg;
						}
						$this->db->insert('movement_d',$data_movement);
						$rpterakhir = $this->get_rpterakhir($rpterakhir);
					}

				}

	//			$this->db->trans_complete();
				if($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$this->result['content'] = 'Gagal menyimpan..';
				}
				else{
				//	$this->db->trans_rollback();
					$this->db->trans_commit();
					$this->result['status'] = 1;
					$this->result['content'] = array('no_retur' => $noreg.'-'.$id_rsk['no_urut'],'tgl_buat' => convertElemenTglWaktuIndonesia($id_rsk['tgl_buat']));
				}
			}

	//	echo json_encode($this->result);
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}
	public function detail_transaksi(){

		$data = array();
		$no_reg = $this->input->post('no_reg');
			$list_pakan = $this->mppr->get_stok_pakan($no_reg)->result_array();
			$retur_pakan = $this->mppr->get_retur_pakan($no_reg)->result_array();
			$data['retur_pakan'] = $this->grouping_kodepj($retur_pakan);
			$data['list_pakan'] = $list_pakan;
			$data['no_reg'] = $no_reg;
			$this->load->view('pengembalian_pakan_rusak/detail_transaksi',$data);
	}
	public function list_pengembalian(){
		$kodefarm = $this->session->userdata('kode_farm');
		$tanggal_cari = $this->input->post('tanggal');
		$no_retur = $this->input->post('no_retur');
		$custom_param = NULL;
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
		$list_pengembalian = $this->mppr->list_pengembalian_pakan_rusak($kodefarm,$custom_param,$no_retur)->result_array();
		$data['list_pengembalian'] = $list_pengembalian;
		$this->load->view('pengembalian_pakan_rusak/list_pengembalian',$data);
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

	private function get_rpterakhir($rp){
		$nourut = (int) substr($rp,-8,8) + 1;
		return 'RP'.str_pad($nourut, 8,'0',STR_PAD_LEFT);
	}
}
