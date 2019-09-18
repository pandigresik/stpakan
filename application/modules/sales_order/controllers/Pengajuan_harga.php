<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Pengajuan_harga extends MY_Controller {
	protected $result;
	protected $_user;
	private $grup_farm;
	private $akses;
	private $tombol;
	private $checkbox;	
	private $indexExp = 5; // hari jumat

	public function __construct() {
		parent::__construct ();
		$this->load->helper('stpakan');
		$level_user = $this->session->userdata('level_user');
		$this->load->model('sales_order/m_pengajuan_harga','ph');

		$this->akses = array(
			'KF' => 'draft',
			'KDLOG' => 'create',
			'KVLOG' => 'review',
			'KDV' => 'approve',
		);
		$this->tombol = array(
			'draft' => '<button class="btn btn-default" onclick="pengajuanHarga.kembali()">Kembali</button><button class="btn btn-primary" onclick="pengajuanHarga.baru(this)">Baru</button><button class="btn btn-primary simpanBtn hide" disabled onclick="pengajuanHarga.submit(this,\'D\')">Simpan</button>',
			'create' => '<button class="btn btn-default" onclick="pengajuanHarga.kembali()">Kembali</button><button class="btn btn-primary" onclick="pengajuanHarga.baru(this)">Rilis</button><button class="btn btn-primary simpanBtn hide" disabled onclick="pengajuanHarga.submit(this,\'N\')">Simpan</button>',
			'review' => '<button class="btn btn-primary" disabled onclick="pengajuanHarga.submit(this,\'R1\')"><i class="glyphicon glyphicon-ok"></i> Approve</button>
						 <button class="btn btn-default" disabled onclick="pengajuanHarga.submit(this,\'RJ\')"><i class="glyphicon glyphicon-remove"></i> Reject</button>',
			'approve' => '<button class="btn btn-primary" disabled onclick="pengajuanHarga.submit(this,\'A\')"><i class="glyphicon glyphicon-ok"></i> Approve</button>
							<button class="btn btn-default" disabled onclick="pengajuanHarga.submit(this,\'RJV\')"><i class="glyphicon glyphicon-remove"></i> Reject</button>',
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
		            'RJV' => "Ditolak",
				),
				'KDLOG' => array(
					'D'  => 'Buat',
					'N'	 => 'Rilis',
					'R1' => 'Review',
					'A'  => 'Disetujui',
		            'RJ' => 'Ditolak',
				),
				'KF' => array(
					'D'  => 'Buat',
					'N'	 => 'Rilis',
					'R1' => 'Review',
					'A'  => 'Disetujui',
		            'RJ' => 'Ditolak',
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
			case 'KDLOG':
				$this->KDLOG();
				break;
			case 'KVLOG':
				$this->KVLOG();
				break;
			case 'KF':
				$this->kafarm();
				break;
			case 'KD':
				$this->kadept();
				break;
			case 'KDV':
				$this->KVLOG();
				break;			
			default :
				echo 'Halaman ini bukan untuk anda ';
		}
	}

	public function KDLOG() {
		$kodefarm = $this->session->userdata('kode_farm');
		$tglSekarang = date('Y-m-d');
		$day = date('N');
		$barang = $this->ph->getBarang();
		$can_write = 1; 										// Jika hari Jumat maka diperbolehkan membuat pengajuan harga
		$list_pengajuan = $this->daftarPengajuan(NULL,TRUE);		
		$data = array(
			'no_pengajuan_harga' => '',
			'list_pengajuan' => $list_pengajuan,
			'tgl_sekarang' => $tglSekarang,
			'tgl_pengajuan' => $tglSekarang,
			'tgl_pengajuan_text' => convertElemenTglIndonesia($tglSekarang),
			'user_peminta' => $this->_user,
			'barang' => $barang,
			'can_write' => $can_write,
			'tombol' => $this->tombol[$this->akses[$this->_user_level]],
			'level_user' => $this->_user_level,
		);

		$this->load->view('sales_order/'.$this->grup_farm.'/pengajuan_harga',$data);
	}

	public function daftarPengajuan($tgl_pengajuan = NULL,$return = FALSE){
		if(empty($tgl_pengajuan)){
			$tgl_pengajuan = $this->input->get('tgl_pengajuan');
		}
		$tglSekarang = date('Y-m-d');
		$day = date('N');
		$barang = $this->ph->getBarang();
		$can_write = 1;
		$statusPengajuan = array(
			'KF' => array('D','N','RJ','R1','RJV','A','V'),
			'KDLOG' => array('D','N','RJ','R1','RJV','A','V'),
			'KVLOG' => array('N','RJ','RJV','R1','A'),
			'KDV' => array('RJV','R1','A'),
		);
		$canApproveStatus = array(
			'KF' => array(),
			'KDLOG' => array(),
			'KVLOG' => array('N'),
			'KDV' => array('R1'),
		);
		$kodeFarm = $this->_user_level == 'KF' ? $this->session->userdata('kode_farm') : NULL;
		$rejectStatus = array('RJ','RJV');
		$whereStatusApproval = empty($tgl_pengajuan) ? '' : ' where CAST(ph.tgl_pengajuan AS DATE) = \''.$tgl_pengajuan.'\'';
		$dataPengajuan = array(
			'list_pengajuan' => $this->ph->listPengajuan($kodeFarm,$tgl_pengajuan,$statusPengajuan[$this->_user_level]),
			'list_estimasi_jumlah' => simpleGrouping($this->ph->getEstimasiStok(),'kode_farm'),
			'list_keterangan' => simpleGrouping($this->ph->listStatusApproval($whereStatusApproval),'no_pengajuan_harga'),
			'list_farm' => $this->ph->getListFarm($this->_user),
			'tgl_sekarang' => $tglSekarang,
			'tgl_pengajuan' => $tglSekarang,
			'tgl_pengajuan_text' => convertElemenTglIndonesia($tglSekarang),
			'user_peminta' => $this->_user,
			'barang' => $barang,
			'can_write' => $can_write,
			'checkbox' => $this->checkbox[$this->_user_level],
			'level_user' => $this->_user_level,
			'canApproveStatus' => $canApproveStatus[$this->_user_level],
			'pengajuan_terakhir' => arr2DToarrKey($this->ph->getPengajuanTerakhir($this->_user_level,$kodeFarm),'kode_farm'),
			'rejectStatus' => $rejectStatus,
			'indexExp' => $this->indexExp
		);
		
		if($return){
			return $this->load->view('sales_order/'.$this->grup_farm.'/list_pengajuan',$dataPengajuan,TRUE);
		}else{
			$this->load->view('sales_order/'.$this->grup_farm.'/list_pengajuan',$dataPengajuan);
		}

	}
	public function kafarm() {
		$kodefarm = $this->session->userdata('kode_farm');
		$tglSekarang = date('Y-m-d');
		$day = date('N');
		$barang = $this->ph->getBarang();
		$can_write = 1; 		// Jika hari Jumat maka diperbolehkan membuat pengajuan harga
		$list_pengajuan = $this->daftarPengajuan(NULL,TRUE);		
		$data = array(
			'no_pengajuan_harga' => '',
			'list_pengajuan' => $list_pengajuan,
			'tgl_sekarang' => $tglSekarang,
			'tgl_pengajuan' => $tglSekarang,
			'tgl_pengajuan_text' => convertElemenTglIndonesia($tglSekarang),
			'user_peminta' => $this->_user,
			'barang' => $barang,
			'can_write' => $can_write,
			'tombol' => $this->tombol[$this->akses[$this->_user_level]],
			'level_user' => $this->_user_level,
		);

		$this->load->view('sales_order/'.$this->grup_farm.'/pengajuan_harga',$data);
	}
	public function KVLOG() {		
		$data['tombol'] = $this->tombol['create'];
		$tglSekarang = date('Y-m-d');		
		$day = date('N');
		$barang = $this->ph->getBarang();
		$can_write = 1; 										// Jika hari Jumat maka diperbolehkan membuat pengajuan harga
		$list_pengajuan = $this->daftarPengajuan(NULL,TRUE);


		$data = array(
			'no_pengajuan_harga' => '',
			'list_pengajuan' => $list_pengajuan,
			'tgl_sekarang' => $tglSekarang,
			'tgl_pengajuan' => $tglSekarang,
			'tgl_pengajuan_text' => convertElemenTglIndonesia($tglSekarang),
			'user_peminta' => $this->_user,
			'barang' => $this->ph->getBarang(),
			'can_write' => 1, 										// Jika hari Jumat maka diperbolehkan membuat pengajuan harga
			'tombol' => $this->tombol[$this->akses[$this->_user_level]],
			'level_user' => $this->_user_level,
		);

		$this->load->view('sales_order/'.$this->grup_farm.'/pengajuan_harga',$data);
	}

	public function simpan(){
		$status_save = true;
		$data 		 = $this->input->post('data');
		$nextStatus  = $this->input->post('nextStatus');
		$nourut = 1;
		$message = array(
			'success' => 'Pengajuan harga berhasil disimpan',
			'error' => 'Pengajuan harga gagal disimpan'
		);
	
		$valFarm = array();
		$valDetail = array();
		$noPengajuanHarga = '';
		$count = 0;
		$updateRef = '';
		$this->db->trans_start();
		foreach ($data as $key => $val) {
			//cetak_r($val, false);						
				if(!$count){				
					$tambahanRV = $nextStatus == 'N' ? '/RV' : '';
					$noPengajuanHarga = $this->ph->no_pengajuan_harga('PH/'.$val['kode_farm'].$tambahanRV.'/'.date('Y').'/');
					$valHeader = array(
						'no_pengajuan_harga' => $noPengajuanHarga						
						, 'kode_farm' => $val['kode_farm']
						, 'tgl_pengajuan' => date('Y-m-d H:i:s') 
					);
					if(!empty($val['id_ref'])){						
						$valHeader['ref_id'] = $val['id_ref'];
						if(!strpos($val['id_ref'],'RV')){
							$phRef = $val['id_ref'];
							$prefix_ph_kf = substr($phRef,0,strlen($phRef) - 3);;
							$this->db->where('keterangan')->where('no_pengajuan_harga like \''.$prefix_ph_kf.'%\'')->update('pengajuan_harga',array('keterangan' => 'review'));
							$updateRef = $phRef;
						}					
					}
					$this->result['no_pengajuan_harga'][] = $noPengajuanHarga;					
					$this->db->insert('pengajuan_harga', $valHeader);
					$status_save = ($this->db->affected_rows() > 0) ? $status_save : false;
					if(!empty($val['id_ref'])){												
						if(!strpos($val['id_ref'],'RV')){
							$nourut++;
							$sql = <<<SQL
							insert into log_pengajuan_harga
							select '{$noPengajuanHarga}',no_urut, status, user_buat, tgl_buat, keterangan from log_pengajuan_harga
							where no_pengajuan_harga = '{$val["id_ref"]}' and no_urut = 1
SQL;
							$this->db->query($sql);	
						}						
					}
					$valLog = array(
						'no_pengajuan_harga' => $noPengajuanHarga
						, 'no_urut' => $nourut
						, 'status' => $nextStatus
						, 'user_buat' => $this->_user
					);
					$this->db->insert('log_pengajuan_harga', $valLog);
					$status_save = ($this->db->affected_rows() > 0) ? $status_save : false;
				}

				$valDetail = array(
					'no_pengajuan_harga' => $noPengajuanHarga
					, 'kode_barang' => $val['kode_barang']
					, 'harga_jual' => isset($val['harga']) ? $val['harga'] : 0
					, 'harga_reg' => isset($val['harga_reg']) ? $val['harga_reg'] : 0
					, 'estimasi_jumlah' => $val['jumlah']
					, 'satuan' => 'SAK'
				);
				
				$this->db->insert('pengajuan_harga_d', $valDetail);
				$status_save = ($this->db->affected_rows() > 0) ? $status_save : false;
				$count++;			
			}								
		
		$this->db->trans_complete();
		if($this->db->trans_status()){
			$this->result['result'] = 'success';
			$this->result['status'] = 1;
			$this->result['message'] = $message['success'];
			$this->result['content'] = array(
				'no_pengajuan_harga' => $noPengajuanHarga,
				'update_ref' => $updateRef,
				'kode_farm' => $valHeader['kode_farm']
			);
		}
		else{
         	//$this->deleteRecord($this->result['no_pengajuan_harga']);
			$this->result['result'] = 'error';
			$this->result['status'] = 0;
			$this->result['message'] = $message['error'];
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	private function deleteRecord($no_pengajuan_harga = array()){
		$where = array();
		for($i=0; $i<count($no_pengajuan_harga); $i++){
			$where[] = "no_pengajuan_harga = '".$no_pengajuan_harga[$i]."'";
		}
		$this->db->where(join(' or ', $where));
		$this->db->delete('pengajuan_harga_d');
		$this->db->where(join(' or ', $where));
		$this->db->delete('log_pengajuan_harga');
		$this->db->where(join(' or ', $where));
		$this->db->delete('pengajuan_harga');
	}

	public function approval(){
		//cetak_r($this->input->post());
		$data = $this->input->post('data');
		$nextStatus = $this->input->post('nextStatus');
		$keterangan = $this->input->post('keterangan_reject');
		$status_save = false;

		$message = array(
			'success' => array(
					'R1' => 'Pengajuan harga berhasil disetujui',
					'A' => 'Pengajuan harga berhasil disetujui',
					'RJ' => 'Pengajuan harga berhasil ditolak',
					'RJV' => 'Pengajuan harga berhasil ditolak',
				)
			, 'error' => array(
					'R1' => 'Pengajuan harga gagal disetujui',
					'A' => 'Pengajuan harga gagal disetujui',
					'RJ' => 'Pengajuan harga gagal ditolak',
					'RJV' => 'Pengajuan harga gagal ditolak',
				)
		);

		$this->db->trans_begin();
		$dataSinkron = array();
		$status_save = true;
		foreach ($data as $val) {
			$value = array(
					'no_pengajuan_harga'	=> $val['no_pengajuan_harga']
					, 'no_urut'	=> $val['no_urut'] + 1
					, 'status'	=> $nextStatus
					, 'user_buat'	=> $this->_user			
			);
			if($nextStatus == 'RJ' || $nextStatus == 'RJV'){
				$value['keterangan'] = $keterangan;
			}
			$this->db->insert('log_pengajuan_harga', $value);
			if(!isset($dataSinkron[$val['kode_farm']])){
				$dataSinkron[$val['kode_farm']] = array();
			}
			array_push($dataSinkron[$val['kode_farm']],$value);			
		}		

		if ($this->db->trans_status() === FALSE )
		{
			$this->db->trans_rollback();
			$this->result['result'] = 'error';
			$this->result['status'] = 0;
			$this->result['message'] = $message['error'][$nextStatus];
		}else{
         	$this->db->trans_commit();
			$this->result['result'] = 'success';
			$this->result['status'] = 1;
			$this->result['content'] = $dataSinkron;
			$this->result['message'] = $message['success'][$nextStatus];
		}
		
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function getHargaPengajuan(){
		$this->load->model('sales_order/m_pengajuan_harga_d','mphd');
		$no_pengajuan = $this->input->get('pengajuan_harga');
		
		$this->result['status'] = 1;
		$this->result['content'] = arr2DToarrKey($this->mphd->as_array()->get_many_by(array('no_pengajuan_harga' => $no_pengajuan)),'kode_barang');
		
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}

	public function hargaRegional(){
		$kodeFarm = $this->input->get('kode_farm');
		$hargaRegional = $this->ph->hargaRegional($kodeFarm);
		if(!empty($hargaRegional)){
			$this->result['status'] = 1;
			$this->result['content'] = arr2DToarrKey($hargaRegional,'kode_barang');
		}
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	
}
