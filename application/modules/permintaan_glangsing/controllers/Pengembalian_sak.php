<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Pengembalian_sak extends MY_Controller {
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
	//	$this->db = $this->load->database('default', TRUE);
		$level_user = $this->session->userdata('level_user');
		$this->load->model('permintaan_glangsing/m_permintaan_sak','mps');
		$this->load->model('permintaan_glangsing/m_ppsk','ppsk');
		$this->load->model('permintaan_glangsing/m_ppsk_d','ppskd');

		$this->_user = $this->session->userdata ( 'kode_user' );
        $this->_user_level = $this->session->userdata('level_user');
        $this->_farm = $this->session->userdata('kode_farm');

		$this->result = array (
			'status' => 0,
			'content' => ''
		);

		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));

	}
	public function index(){
		$data = array();
		$data['lockTimbangan'] = $this->config->item('lockTimbangan');
		$this->load->view('permintaan_glangsing/'.$this->grup_farm.'/pengembalian_sak',$data);
	}

	public function getRFID(){
		$rfid = $this->input->post('rfid');
		$result = $this->ppsk->getKandangByRFID($rfid);
		$data = array();
		if(count($result)>0){
			$data['status'] = 1;
			$data['result'] = 'success';
			$data['no_reg'] = $result[0]['NO_REG'];
			$data['nama_kandang'] = $result[0]['NAMA_KANDANG'];
			$data['kode_flok'] = $result[0]['FLOK_BDY'];
			$ppsk = array();
			$budget = array();
			foreach ($result as $val) {
				$ppsk[] = array(
					'no_ppsk' => $val['no_ppsk']
					, 'jml_diminta' => $val['jml_diminta']
					, 'kode_budget' => $val['kode_budget']
					, 'tgl_kebutuhan' => $val['tgl_kebutuhan']
					, 'tgl_kebutuhan_text' => convertElemenTglIndonesia($val['tgl_kebutuhan'])
					, 'nama_budget' => $val['nama_budget']
				);
				//$budget .= '<option value="">'
			}
			$data['data'] = $ppsk;
			//cetak_r($data);
			if(count($data['data']) == 0){
				$data['message'] = 'Data pengembalian glangsing tidak ditemukan/melebihi batas pengembalian H+1 tanggal kebutuhan';
			}
			
			echo json_encode($data); 
		}else{
			$data['status'] = 0;
			$data['result'] = 'error';
			//$data['message'] = 'Data tidak ditemukan';
			$data['message'] = 'Data pengembalian glangsing tidak ditemukan/melebihi batas pengembalian H+1 tanggal kebutuhan';
			echo json_encode($data);
			//cetak_r($result);
		}

	}

	public function konfirmasiPengembalian(){
		$no_ppsk = $this->input->post('no_ppsk'); 
		$no_reg = $this->input->post('no_reg');
		$where = array(
				'no_ppsk' => $no_ppsk
				, 'no_reg' => $no_reg
			);
		$data = array(
				'brt_kembali' => $this->input->post('brt_kembali')
				, 'jml_kembali' => $this->input->post('jml_kembali')
				, 'tgl_kembali' => $this->input->post('tgl_kembali')
				, 'user_pengembali' => $this->input->post('user_pengembali')
			);

		$this->db->trans_begin();
		$this->ppskd->update_by($where,$data);
		$dataOutputSinkron = array();
		if(!empty($data['jml_kembali'])){
			$jml_kembali = $this->input->post('jml_kembali');
			$dataMovement = array(
				'no_ppsk' => $no_ppsk
				,'no_reg' => $no_reg
				,'jml_kembali' => $jml_kembali
			);
			$dataOutputSinkron = $this->updateMovementD($dataMovement);
		}

		if ($this->db->affected_rows() > 0) {
			$this->db->trans_commit();
		//	$this->ppskd->retur_in_glangsing_movement($where['no_ppsk'], $where['no_reg']);
			$this->result['result'] = 'success';
			$this->result['no_ppsk'] = $where['no_ppsk'];
			$this->result['no_reg'] = $where['no_reg'];
			$this->result['status'] = 1;
			$this->result['message'] = 'Pengembalian glangsing berhasil disimpan';
			$this->result['movement_d'] = $dataOutputSinkron;
		}else{
			$this->db->trans_rollback();
			$this->result['result'] = 'error';
			$this->result['status'] = 0;
			$this->result['message'] = 'Pengembalian glangsing gagal disimpan';
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function ack1(){
		$data['level_user'] = $this->session->userdata('level_user');
		$data['kode_farm'] = $this->session->userdata('kode_farm');

		$data['kandang'] = $this->ppskd->getAllKandang($data['kode_farm']);
		//$data['list_pengembalian'] = $this->load->view('permintaan_glangsing/'.$this->grup_farm.'/pengembalian_sak_ack',$data);
		$this->load->view('permintaan_glangsing/'.$this->grup_farm.'/pengembalian_sak_ack',$data);
	}

	public function getListPengembalianAck(){
		$kodeFarm = $this->session->userdata('kode_farm');
		$req = $this->input->post();
		$result = $this->ppskd->getListAck($kodeFarm, $req['kode_kandang'], $req['status']);
		//cetak_r($result);
		$no_ppsk = ''; $strPpsk = 0;
		$str = '';
		if(count($result) > 0){
			foreach ($result as $key => $val) {
				$str .= '<tr>';
				if($no_ppsk != $val['no_ppsk']){
					$no_ppsk = $val['no_ppsk'];
					$str = str_replace('~~~', $strPpsk, $str);
					$str .= '<td rowspan="~~~">'.$val['no_ppsk'].'</td>
						<td rowspan="~~~">'.convertElemenTglIndonesia($val['tgl_kebutuhan']).'</td>
						<td rowspan="~~~">'.$val['nama_budget'].'</td>';
					$strPpsk = 0;
				}
				$strPpsk ++;

				$str .= '<td>'.$val['nama_kandang'].'</td>
					<td>'.$val['jml_diminta'].'</td>
					<td>'.$val['jml_kembali'].'</td>
					<td>'.$val['jml_pakai'].'</td>';
					$str .= '<td>';
					if($val['status'] == 'BA'){
						$str .= '<input type="checkbox" name="ack" data-no_ppsk="'.$val['no_ppsk'].'" data-no_reg="'.$val['no_reg'].'" onclick="pengembalianSakAck.check_button(this)">';
					}

				$str .= '</td><td>';
				if(!empty($val['user_pengembali'])){
					$str .= '<div style="text-align:left">['.$val['user_pengembali'].'] - Simpan, '.convertElemenTglWaktuIndonesia($val['tgl_kembali']).'</div>';
				}
				if(!empty($val['user_ack'])){
					$str .= '<div style="text-align:left">['.$val['user_ack'].'] - Mengetahui, '.convertElemenTglWaktuIndonesia($val['tgl_ack']).'</div>';
				}
				$str .= '</td>
					</tr>';
			}
			$str = str_replace('~~~', $strPpsk, $str);
			$data['status'] = 1;
			$data['data'] = $str;
		}else{

			$data['status'] = 0;
			$data['message'] = 'Data tidak ditemukan';
		}
		echo json_encode($data);
	}

	public function simpanAck(){
		$req = $this->input->post('data');
		$user_id = $this->session->userdata('kode_user');
		$today = $this->ppskd->get_today();

		$this->db->trans_begin();
		$status = true;
		foreach ($req as $key => $val) {
			$where = array(
					'no_reg' => $val['no_reg']
					, 'no_ppsk' => $val['no_ppsk']
				);
			$data = array(
					'user_ack' => $user_id
					, 'tgl_ack' => $today['today']
				);


			$this->result['detail'][] = $where;

			$this->db->where($where);
			$this->db->update('ppsk_d',$data);
			$status = ($this->db->affected_rows() > 0) ? $status : false;
		}

		if ($status) {
			$this->db->trans_commit();
			$this->result['result'] = 'success';
			$this->result['status'] = 1;
			$this->result['message'] = 'Proses ACK berhasil disimpan';
		}else{
			$this->db->trans_rollback();
			$this->result['result'] = 'error';
			$this->result['status'] = 0;
			$this->result['message'] = 'Proses ACK gagal disimpan';
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));

		//cetak_r($data);
	}

	function updateMovementD($data = NULL){
		$this->load->model('pengembalian_sak/m_glangsing_movement','gm');
		$this->load->model('pengembalian_sak/m_glangsing_movement_d','gmd');
		$tglServer = Modules::run('home/home/getDateServer');
		$tgl_buat = $tglServer->tglserver;
		$kodeGlangsingBekasPakai = 'GBP';
		$ppskData = $this->ppsk->get($data['no_ppsk']); 
		$kodeSiklus = $ppskData->kode_siklus;
		$kodeBudget = $ppskData->kode_budget;
		$kodefarm = $this->_farm;
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
		}

		$glangsingMovementGBP = array(
			'kode_farm' => $kodefarm,
			'kode_siklus' => $kodeSiklus,
			'kode_barang' => $kodeGlangsingBekasPakai,
			'no_referensi' => $data['no_ppsk'],
			'jml_awal' => 0,
			'jml_order' => 0,
			'jml_akhir' => 0,
			'tgl_transaksi' => $tgl_buat,
			'keterangan1' => 'RETUR_IN',
			'keterangan2' => $data['no_reg'],
			'user_buat' => $this->_user,
		);

		$glangsingMovementBudget = array(
			'kode_farm' => $kodefarm,
			'kode_siklus' => $kodeSiklus,
			'kode_barang' => $kodeBudget,
			'no_referensi' => $data['no_ppsk'],
			'jml_awal' => 0,
			'jml_order' => 0,
			'jml_akhir' => 0,
			'tgl_transaksi' => $tgl_buat,
			'keterangan1' => 'RETUR_OUT',
			'keterangan2' => $data['no_reg'],
			'user_buat' => $this->_user,
		);

		$glangsingMovementGBP['jml_order'] = $data['jml_kembali'];
		$glangsingMovementGBP['jml_awal'] =  $jmlStokAwalGBP;
		$glangsingMovementGBP['jml_akhir'] =  $jmlStokAwalGBP + $data['jml_kembali'];
		$jmlStokAwalGBP = $glangsingMovementGBP['jml_akhir'];

		$glangsingMovementBudget['jml_order'] = $data['jml_kembali'];
		$glangsingMovementBudget['jml_awal'] =  $jmlStokAwalBudget;
		$glangsingMovementBudget['jml_akhir'] =  $jmlStokAwalBudget - $data['jml_kembali'];
		$jmlStokAwalBudget = $glangsingMovementBudget['jml_akhir'];

		$this->gmd->insert($glangsingMovementGBP);
		$this->gmd->insert($glangsingMovementBudget);
		$this->gm->update_by($whereGlangsingBudgetMovement,array('jml_stok' => $jmlStokAwalBudget));
		$this->gm->update_by($whereGlangsingMovement,array('jml_stok' => $jmlStokAwalGBP));
		return $whereGlangsingBudgetMovement;
	}
}
