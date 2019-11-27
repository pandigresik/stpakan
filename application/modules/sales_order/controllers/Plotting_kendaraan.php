<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Plotting_kendaraan extends MY_Controller {
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
		$this->load->config('email');
		$config = $this->config->item('email_from');
		$config['_bit_depths'] = array('7bit', '8bit','base64');
		$config['_encoding'] = 'base64';
		$this->load->library('email',$config);
		// $this->load->library('email');
		$this->load->helper('stpakan');
		$this->email->initialize($config);

		$this->load->model('sales_order/m_pengajuan_harga','ph');
		$level_user = $this->session->userdata('level_user');
		$this->load->model('sales_order/M_plotting_kendaraan','pk');
	
		$this->akses = array(
			'LOG'   => 'create',
			'KDLOG' => 'create',
			'KVLOG' => 'review',
			'KDV'   => 'approve',
		);
		$this->tombol = array(
			'create' => '<button class="btn btn-default" onclick="plottingKendaraan.save()" disabled>Simpan</button>',
			'review' => '<button class="btn btn-primary" disabled onclick="pengajuanHarga.submit(this,\'R1\')"><i class="glyphicon glyphicon-ok"></i> Approve</button>
						 <button class="btn btn-default" disabled onclick="pengajuanHarga.submit(this,\'RJ\')"><i class="glyphicon glyphicon-remove"></i> Reject</button>',
			'approve'=> '<button class="btn btn-primary" disabled onclick="pengajuanHarga.submit(this,\'A\')"><i class="glyphicon glyphicon-ok"></i> Approve</button>
						 <button class="btn btn-default" disabled onclick="pengajuanHarga.submit(this,\'RJ\')"><i class="glyphicon glyphicon-remove"></i> Reject</button>',
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
				'RJ' => "Ditolak",
			),
			'KDLOG' => array(
				'N'	 => 'Dibuat',
				'R1' => 'Dikoreksi',
				'A'  => 'Disetujui',
				'RJ' => "Ditolak",
			),
			'LOG' => array(
				'N'	 => 'Dibuat',
				'R1' => 'Dikoreksi',
				'A'  => 'Disetujui',
				'RJ' => "Ditolak",
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
		$list_plotting = $this->listPlotting($kode_farm,NULL,TRUE);
		$tglSekarang = date('Y-m-d');
		$data = array(
			'list_plotting'	=> $list_plotting,
			'tgl_sekarang'  => tglIndonesia($tglSekarang,'-',' ')
		);

		$this->load->view('sales_order/'.$this->grup_farm.'/plotting_kendaraan',$data);
	}

	public function KDLOG($kode_farm = null){
		echo 'masih development';
	}

	public function listPlotting($kode_farm = NULL,$tglTransaksi = NULL, $return = FALSE){
		if(empty($tglTransaksi)){
			$tglTransaksi = date('Y-m-d');
		}
		$nomor_so = null;
		$status_so = null;
		$data['list_sales_order'] = $this->pk->getSalesOrder($nomor_so, $status_so, $kode_farm, $tglTransaksi);
		$datalist = array(
			'list_sales_order' => $data['list_sales_order'],
			'tbl_plotting_kendaraan' => $this->load->view('sales_order/'.$this->grup_farm.'/tbl_plotting_kendaraan',$data,TRUE),
			'list_farm' => $this->ph->getListFarm($this->_user),

			'tombol' => $this->tombol[$this->akses[$this->_user_level]],
			'level_user' => $this->_user_level,
		);

		if($return){
			return $this->load->view('sales_order/'.$this->grup_farm.'/list_plotting_kendaraan',$datalist,TRUE);
		}else{
			$this->load->view('sales_order/'.$this->grup_farm.'/list_plotting_kendaraan',$datalist);
		}
	}

	public function getSopir(){
		$sopir = $this->pk->get_sopir_browse();
		//kode_pelanggan, nama_pelanggan, alamat, kota, no_telp, term_pembayaran
		$return = array();
		if (isset($sopir) && !empty($sopir)) {
			$id = 1;
			foreach ($sopir as $key => $val) {
				$return[] = array(
					'id' => $id,
					'name' => $val['nama_sopir'] . ' - ' . $val['no_telp_sopir'],
					'nama_sopir' => $val['nama_sopir'],
					'no_telp_sopir' => $val['no_telp_sopir'],
				);
				$id +=1;
			}
		}		
		echo json_encode($return);
	}

	public function searchSO(){
		$nomor_so = $this->input->post('nomor_so');
		$status_plotting_so = $this->input->post('status_so');
		$tglTransaksi = date('Y-m-d');
		$kode_farm = null;

		$data['list_sales_order'] = $this->pk->getSalesOrder($nomor_so, $status_plotting_so, $kode_farm, $tglTransaksi);
		$datalist = array(
			'list_sales_order' => $data['list_sales_order'],
			'list_farm' => $this->ph->getListFarm($this->_user),

			'tombol' => $this->tombol[$this->akses[$this->_user_level]],
			'level_user' => $this->_user_level,
		);

		$this->load->view('sales_order/'.$this->grup_farm.'/tbl_plotting_kendaraan',$datalist);
	}

	public function save(){
		$this->load->config('stpakan');
		$smsDO = $this->config->item('smsDO');
		$data_params = $this->input->post('data_params');
		$data_params = json_decode($data_params, true);
		$dtl_plotting_kendaraan = $data_params['dtl_plotting_kendaraan'];

		$status_save = true;
		$tglSekarang = date('Y-m-d');

		if(true){
			//insert surat jalan header + detail
			foreach ($dtl_plotting_kendaraan as $key => $val) {
				$this->db->trans_begin();
				//get surat jalan
				$kode_farm = $val['kode_farm'];
				$nomor_sj_arr = $this->pk->getIDSuratJalan($val['nomor_so']);
				$nomor_sj = $nomor_sj_arr[0]['no_sj'];
				$dataHeader = array(
					'no_sj' => $nomor_sj,
					'no_so' => $val['nomor_so'],
					'no_do' => $val['nomor_do'],
					'no_kendaraan' => $val['nomor_kendaraan'],
					'nama_sopir' => $val['nama_sopir'],
					'no_telp_sopir' => $val['telp_sopir'],
					'nama_pelanggan' => $val['nama_pelanggan'],
					'no_telp_pelanggan' => $val['telp_pelanggan'],
					'alamat_pelanggan' => $val['alamat_pelanggan'],
					'kota_pelanggan' => $val['kota_pelanggan'],
					'tgl_buat' => $tglSekarang,
					'user_buat' => $this->_user,
				);
				$this->db->insert("surat_jalan", $dataHeader);
				$status_save = ($this->db->affected_rows() > 0) && $status_save;

				$select = $this->db->select("'$nomor_sj' as no_sj, kode_barang, jumlah")->where(array('no_so' => $val['nomor_so']))->get('sales_order_d');
				if ($select->num_rows()) {
					foreach ($select->result_array() as $keySelect => $valSelect) {
						$this->db->insert('surat_jalan_d', $valSelect);
						$status_save = ($this->db->affected_rows() > 0) && $status_save;
					}
				}

				$suratjalan = $this->db->select(array('KODE_VERIFIKASI'))->where(array('no_sj' => $nomor_sj))->get('surat_jalan')->row();
				$kode_verifikasi = $suratjalan->KODE_VERIFIKASI;

				$_nama_farm = $this->db->select(array('NAMA_FARM'))->where(array('kode_farm' => $val['kode_farm']))->get('m_farm')->row();
				$nama_farm = (!empty($_nama_farm)) ?  $_nama_farm->NAMA_FARM : ' undefined ';

				if($status_save){
					$this->db->trans_commit();
					$hari_ini = date('d/m/Y');
 					$pesan = <<<SQL
 						Pelanggan Yth, Pesanan dpt diambil tgl {$hari_ini} di Farm {$nama_farm}, dg menunjukkan data sbb :
 						No. DO : {$val['nomor_do']}
 						Pin    : {$kode_verifikasi}
 						Info   : 0312956000 ext. 1348
SQL;

 					$nomerTelpFarm = isset($smsDO[$kode_farm]) ? $smsDO[$kode_farm] : array();
 					$nomer = array_merge(array($val['telp_pelanggan']),$nomerTelpFarm);
					//Modules::run('client/csms/sendNotifikasi',$pesan,$nomer);
					//Send Email
					$this->load->model('sales_order/m_sales_order','mso');
					$tgl_so = $this->mso->get($val['nomor_do'])->tgl_so;
					$msg_data = array(
						'farm' 			  => $nama_farm,
						'nama_pelanggan'  => $val['nama_pelanggan'],
						'telp_pelanggan'  => $val['telp_pelanggan'],
						'tgl_so'	  => $tgl_so,
						'nomor_kendaraan' => $val['nomor_kendaraan'],
						'no_pin' => $kode_verifikasi
					);
					$message = $this->load->view('sales_order/bdy/email_do',$msg_data,true);
					$this->kirim_email($val['kode_farm'], $val['nomor_do'], $message);
					//End Send
					$this->result['result'] = 'success';
					$this->result['status'] = 1;
					$this->result['message'] = 'SJ berhasil disimpan';
					$this->result['content'][] = $nomor_sj; //kembalikan data sj yang diproses
				}
				else{
					$this->db->trans_rollback();
					$this->result['result'] = 'error';
					$this->result['status'] = 0;
					$this->result['message'] = 'SJ baru gagal disimpan.';
				}
			}
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}
	private function do_pdf($no_so) {
		$this->load->model('sales_order/m_sales_order','mso');
		$this->load->model('sales_order/m_sales_order_d','msod');
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A4', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );
		$pdf->SetFontSize(16);
		$pdf->SetMargins(8, 8, 8, 8);
		$header_do = $this->mso->get($no_so);
		$detail_do = $this->msod->get_many_by(array('no_so' => $no_so));
		$barang = $this->db->select(array('nama_barang','kode_barang'))->where(array('grup_barang' => '000117'))->get('m_barang')->result_array();
		$sj = $this->db->select(array('nama_pelanggan','no_kendaraan','nama_sopir'))->where(array('no_so' => $no_so))->get('surat_jalan')->row();
		/* pastikan surat jalan sudah dibuat */
		if(empty($sj)){
			echo 'SO nomer '.$no_so.' belum dilakukan plotting kendaraan';
			exit();
		}
		$tgl_so_date = new \DateTime($header_do->tgl_so);
		$dataFarm = $this->db->select('NAMA_FARM,ALAMAT_FARM,KOTA')->where(array('kode_farm' => $header_do->kode_farm))->get('m_farm')->row();
		$berlakuDo =  $tgl_so_date->add(new \DateInterval('P1D'))->format('Y-m-d');
		$params = $pdf->serializeTCPDFtagParameters ( array (
			$no_so,
			'QRCODE,H',
			'',
			'',
			32,
			32
		) );

		$b = '<tcpdf method="write2DBarcode" params="' . $params . '" />';
		$html = $this->load->view ( 'sales_order/bdy/cetak_do_pdf', array (
				'header_do' => $header_do,
				'detail_do' => $detail_do,
				'barang' => arr2DToarrKey($barang,'kode_barang'),
				'barcode' => $b,
				'berlakuDo' => $berlakuDo,
				'dataFarm' => $dataFarm,
				'suratJalan' => $sj
		), true );

		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );
		$pdf->setXY(233,186);
		$pdf->SetFontSize(7);
		$infoCetak = 'Information and Technology Division '.convertElemenTglWaktuIndonesia(date('Y-m-d H:i:s'),1);
		$pdf->Cell(60, 1, $infoCetak, 0, 0, 'R', 0, '', 0);
		$marginPage = 3;
		$pdf->Line($marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,$marginPage);
		$pdf->Line($pdf->getPageWidth()-$marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,  $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage, $pdf->getPageHeight()-$marginPage, $pdf->getPageWidth()-$marginPage, $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage,$marginPage,$marginPage, $pdf->getPageHeight()-$marginPage);
		//$fileatt = $pdf->Output ($name='yourfilename.pdf', $dest='E' );
		//$data = chunk_split($fileatt);
		$fileatt = $pdf->Output( $no_so, 'S' );
		return $fileatt;
	}
	public function kirim_email($kode_farm = '', $no_so = 'GD18-G0063', $message = '')
	{
		$message = 'testing';
		//echo Modules::run('client/email/email_op','00003/15');
		// cetak_r($this->do_pdf('GD18-G0063'));
		// cetak_r($this->config->item('email_plotting_kendaraan')[$kode_farm]);
		$subject = <<<sbj
			[WJC-ST Pakan] Dokumen DO no. $no_so
sbj;
		$from = "budidaya@wonokoyo.co.id";
		$alias_from = "WJC ST-Pakan";
		$to = $this->config->item('email_plotting_kendaraan')[$kode_farm];	
		$this->email->clear(TRUE);
		$this->email->attach($this->do_pdf($no_so),'attachment',$no_so.'.pdf','application/pdf');
		$send = Modules::run('client/email/send_email',$subject,$alias_from,$from,$to,$message);		
		if(!$send){
			log_message('error','Notifikasi Plotting Kendaraan gagal');
		}
	}

}
