<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Pembelian_pakan extends MY_Controller {
	protected $result;
	protected $_user;
	public function __construct() {
		parent::__construct ();

		$this->load->model ( 'permintaan_pakan_v3/m_pembelian_pakan', 'mpp' );
		$this->load->helper ( 'stpakan' );
		$this->load->config('stpakan');
		$this->_user = $this->session->userdata ( 'kode_user' );
		$this->result = array (
				'status' => 0,
				'content' => ''
		);
	}
	public function index() {
	}

	public function approve($farm = NULL) {		
		
		$user_level = $this->session->userdata('level_user');	
		$data['list_farm'] = Modules::run('forecast/forecast/list_farm','bdy',$farm);
		$data['rekap'] = 0;		
		$data['access'] = 'approve';
		$data['tglKirim'] = tglSetelah(date('Y-m-d'),1);			
		$data['pencarian'] = array(
			
		);
		
		$this->load->view ( 'permintaan_pakan_v3/bdy/approve_do', $data );
	}

	public function order($rekap = 0) {		
		$namaFarm = $this->config->item('namaFarm');
		$data['rekap'] = 0;		
		$data['tglKirim'] = tglSetelah(date('Y-m-d'),1);			
		$data['pencarian'] = array(
			'kodefarm' => $this->input->get('kodefarm'),
			'tglawal' => $this->input->get('tglawal'),
			'tglakhir' => $this->input->get('tglakhir')
		);
		$data['farm'] = $namaFarm;
		$this->load->view ( 'permintaan_pakan_v3/order_pembelian', $data );
	}

	public function list_order() {
		$this->load->model('permintaan_pakan_v3/m_do','mdo');
		$cari = $this->input->post ( 'cari' );
		$tglKirim = $this->input->post ( 'tglKirim' );
		$rekap = $this->input->post ( 'rekap' );
		$startDate = $tglKirim ['startDate'];
		$endDate = $tglKirim ['endDate'];
		
		$list_order = $this->mpp->list_order ( $startDate, $endDate, $cari, $rekap )->result_array ();
		/* buat array untuk mencari expedisi per op pertglkirim */
		$perfarm_tglkirim = array ();		
		$unique_op_order = array ();
		$list_no_do = array();
		foreach ( $list_order as $order ) {
			$kode_farm = $order['kode_farm'];			
			$tgl_kirim = $order ['tgl_kirim'];						
			if (! isset ( $perfarm_tglkirim[$tgl_kirim])) {
				$perfarm_tglkirim[$tgl_kirim] = array();				
			}
			if (! isset ( $perfarm_tglkirim[$tgl_kirim][$kode_farm])) {
				$perfarm_tglkirim[$tgl_kirim][$kode_farm] = array(					
					'opUnique' => array(),
					'detail' => array()
				);				
			}
			$perfarm_tglkirim[$tgl_kirim][$kode_farm]['opUnique'][$order['no_op']] = $order['jml_order'];									
			array_push($perfarm_tglkirim[$tgl_kirim][$kode_farm]['detail'],$order);
			$tmp = explode(',',$order['no_do']);
			foreach($tmp as $_t){
				array_push($list_no_do,$_t);
			}
			
		}
		
		$data['cari'] = $cari;
		$data['rekap'] = $rekap;
		$data['list_order'] = $perfarm_tglkirim;
		
		$data['riwayat'] = $this->group_log_ploting($this->mdo->riwayat($list_no_do)->result_array());
		$this->load->view ( 'permintaan_pakan_v3/list_order', $data );
	}

	public function list_order_approve() {
		$this->load->model('permintaan_pakan_v3/m_do','mdo');
		$user_level = $this->session->userdata('level_user');
		$statusTindakLanjut = array(
			'KD' => array('D'),
			'KDV' => array('R')
		);
		$cari = array(
			'tindaklanjut' => $this->input->post('tindaklanjut'),
			'status_do' => $statusTindakLanjut[$user_level]
		);
		$namaFarms = $this->config->item('namaFarm');								
		$data['nama_farm'] = $namaFarms;	
		$tglKirim = $this->input->post ( 'tglKirim' );
		$list_order = $this->mpp->list_order_approve ($cari)->result_array ();
		/* buat array untuk mencari expedisi per op pertglkirim */
		$perfarm_tglkirim = array ();		
		$list_no_do = array();
		$list_no_op = array();
		foreach ( $list_order as $order ) {
			$kode_farm = $order['kode_farm'];			
			$tgl_kirim = $order ['tgl_kirim'];
			$no_op = $order['no_op'];
			$rit = $order ['rit'];						
			if (! isset ( $perfarm_tglkirim[$tgl_kirim])) {
				$perfarm_tglkirim[$tgl_kirim] = array();				
			}
			if (! isset ( $perfarm_tglkirim[$tgl_kirim][$kode_farm])) {
				$perfarm_tglkirim[$tgl_kirim][$kode_farm] = array();				
			}
			if (! isset ( $perfarm_tglkirim[$tgl_kirim][$kode_farm]['detail'])) {
				$perfarm_tglkirim[$tgl_kirim][$kode_farm]['detail'] = array();				
				$perfarm_tglkirim[$tgl_kirim][$kode_farm]['rowspan'] = 0;
				$perfarm_tglkirim[$tgl_kirim][$kode_farm]['total'] = 0;
			}

			if (! isset ( $perfarm_tglkirim[$tgl_kirim][$kode_farm]['detail'][$rit])) {
				$perfarm_tglkirim[$tgl_kirim][$kode_farm]['detail'][$rit] = array();				
			}
			$perfarm_tglkirim[$tgl_kirim][$kode_farm]['total'] += $order['jml_kirim'];
			$perfarm_tglkirim[$tgl_kirim][$kode_farm]['rowspan']++;
			array_push($perfarm_tglkirim[$tgl_kirim][$kode_farm]['detail'][$rit],$order);

			array_push($list_no_op,$no_op);
		}
		$data['riwayat'] = array();
		if(!empty($list_no_op)){
			$list_do_semua = $this->db->select(array('no_do'))->where_in('no_op',$list_no_op)->get('do')->result_array();
			$list_no_do = array_column($list_do_semua,'no_do');
			$data['riwayat'] = $this->group_log_ploting($this->mdo->riwayat($list_no_do)->result_array());
		}
		
		$data['cari'] = $cari;
		$data['list_order'] = $perfarm_tglkirim;
		
		$this->load->view ( 'permintaan_pakan_v3/list_order_approve', $data );
	}

	public function detail_pp() {
		/* dapatkan semua informasi pp berdasarkan nomer pp */
		$this->load->model ( 'permintaan_pakan_v3/m_op_vehicle', 'mopv' );				
		$kode_farm = $this->input->post ( 'kode_farm' );
		$tgl_kirim = $this->input->post ( 'tgl_kirim' );		
		$list_pakan = $this->mpp->detail_pp_tglkirim ( $kode_farm, $tgl_kirim )->result_array();
		$total_jml_pakan = 0;
		$list_op = array ();						
		foreach ( $list_pakan as $pakan ) {
			$no_op = $pakan['no_op'];
			$total_jml_pakan += $pakan['jumlah'];
			if(!isset($list_op[$no_op])){
				$list_op[$no_op] = array();				
			}			
			array_push($list_op[$no_op],$pakan);
		}
		
		$kendaraan_ekspedisi = null;
		/* cari detail do dari op_vehicle */
		$list_do = $this->mopv->detail_ekspedisi_tglkirim( $kode_farm, $tgl_kirim )->result_array();
		$do_perop = array();
		$_list_kode_ekspedisi = array();
		/* grouping berdasarkan op,kode_ekspedisi dan kodepj */
		if(!empty($list_do)){
			foreach ( $list_do as $baris ) {
				$no_op = $baris['no_op'];
				$kode_ekspedisi = $baris ['kode_ekspedisi'];
				array_push($_list_kode_ekspedisi,$kode_ekspedisi);
				$kode_barang = $baris ['kode_barang'];
				if(!isset($do_perop[$no_op])){
					$do_perop[$no_op] = array();
				}
				if($baris['status_do'] == 'N'){
					array_push($do_perop[$no_op],'<div class="link_span" onclick="Plotting.do_pdf(\''.$baris['no_do'].'\')">'.$baris['no_do'].'</div>');
				}

				if (! isset ( $kendaraan_ekspedisi [$no_op])) {
					$kendaraan_ekspedisi [$no_op] = array ();
				}
				if (! isset ( $kendaraan_ekspedisi [$no_op][$kode_ekspedisi] )) {
					$kendaraan_ekspedisi [$no_op][$kode_ekspedisi] = array ();
				}
				
				if (! isset ( $kendaraan_ekspedisi [$no_op][$kode_ekspedisi][$kode_barang] )) {
					$kendaraan_ekspedisi [$no_op][$kode_ekspedisi] [$kode_barang] = array ();					
					//$kendaraan_ekspedisi [$no_op][$kode_ekspedisi] ['max_urut'] = 0;
				}
				$rit = $baris['rit'];
				$kendaraan_ekspedisi [$no_op][$kode_ekspedisi] [$kode_barang][$rit] = array (
						'no_do' => $baris ['no_do'],
						'no_urut' => $baris ['no_urut'],
						'jml_kirim' => $baris ['jml_kirim'],
						'status_do' => $baris ['status_do'] 
				);
				
				//$kendaraan_ekspedisi [$no_op][$kode_ekspedisi] ['max_urut'] = $baris ['no_urut'] > $kendaraan_ekspedisi [$no_op][$kode_ekspedisi] ['max_urut'] ? $baris ['no_urut'] : $kendaraan_ekspedisi [$no_op][$kode_ekspedisi] ['max_urut'];
			}
		}
		if(!empty($_list_kode_ekspedisi)){
			$list_ekspedisi = $this->mpp->list_ekspedisi($kode_farm, array_unique($_list_kode_ekspedisi))->result_array();
		}else{
			$list_ekspedisi = array($this->mpp->list_ekspedisi($kode_farm, array_unique($_list_kode_ekspedisi))->row_array());
		}
		
		$data ['rekap'] = 0;
		$data ['do_perop'] = $do_perop;
		$data['total_pakan'] = $total_jml_pakan;
	//	$data ['tgl_kirim'] = tglIndonesia ( $tgl_kirim, '-', ' ' );
		$data ['list_op'] = $list_op;
		$data ['list_ekspedisi'] = $list_ekspedisi;
		$data ['kendaraan_ekspedisi'] = $kendaraan_ekspedisi;
		$data['kode_farm'] = $kode_farm;
		$this->load->view ( 'permintaan_pakan_v3/transaksi_detail_pp', $data );
	}
	public function simpan_do() {		
		$do = $this->input->post ('do');		
		$tgl_kirim = $this->input->post('tgl_kirim');
		$kode_farm = $this->input->post('kode_farm');
		$dibawah_ritase = $this->input->post('dibawah_ritase');
		$status_do = $dibawah_ritase ? 'D' : 'N';
		
		/* insert ke tabel op */
		$this->load->model ( 'permintaan_pakan_v3/m_do', 'mdo' );
		$this->load->model ( 'permintaan_pakan_v3/m_do_d', 'mdod' );
		$this->load->model ( 'permintaan_pakan_v3/m_op_vehicle', 'mopv' );				
		$this->load->model('permintaan_pakan_v3/m_log_ploting_do', 'lpd');
		$tglServer = Modules::run('home/home/getDateServer');
		$tgl_sekarang = $tglServer->saatini;
		$do_ekspedisi_email = array();
		$list_op = array();
		$this->db->trans_begin ();		
		/** hapus dulu lalu insert lagi */
		$log_do_lama = $this->hapus_ploting_lama($kode_farm,$tgl_kirim);
		if (!empty ( $do )) {
			foreach ( $do as $no_op => $do_perop){
				array_push($list_op,$no_op);
				$no_urut = 1;												
				foreach($do_perop as $kode_ekspedisi => $do_perekspedisi){
					if(!isset($do_ekspedisi_email[$kode_ekspedisi])){
						$do_ekspedisi_email[$kode_ekspedisi] = array();
					}
					foreach($do_perekspedisi as $rit => $_perrit){
						$result = $this->mdo->simpan_pertglkirim($no_op,$kode_farm,$no_urut,$tgl_kirim,$this->_user,$tgl_sekarang,$status_do);
						array_push($do_ekspedisi_email[$kode_ekspedisi],$result['no_do']);
						
						foreach($_perrit as $_do){
							//$rit = $_do['rit'];
							//unset($_do['rit']);
								$insert_do_d = array (
									'kode_farm' => $kode_farm,
									'no_do' => $result ['no_do'],
									'no_op' => $no_op,
									'kode_barang' => $_do['kodepj'],
									'jml_muat' => $_do['jumlah'],
									'tgl_kirim' => $tgl_kirim
								);
								$insert_op_vehicle = array (
									'no_op' => $no_op,
									'tgl_kirim' => $tgl_kirim,
									'no_urut' => $no_urut,
									'kode_barang' => $_do['kodepj'],
									'kode_ekspedisi' => $kode_ekspedisi,
									'jml_kirim' => $_do ['jumlah'],
									'berat_kirim' => $_do ['jumlah'] * 50,
									'tgl_buat' => $tgl_sekarang,
									'user_buat' => $this->_user,
									'no_polisi' => $rit
								);					
								//log_message('error',json_encode($insert_op_vehicle));
								$this->mdod->insert ( $insert_do_d );
								$this->mopv->insert ( $insert_op_vehicle );							
								
								if(!empty($log_do_lama)){
									foreach($log_do_lama as $do_lama){
										$this->lpd->insert(array(
											'no_do' => $result ['no_do'],
											'status' => $do_lama['status'],
											'tgl_buat' => $do_lama['tgl_buat'],
											'user_buat' => $do_lama['user_buat']
										));		
									}
								}
								$this->lpd->insert(array(
									'no_do' => $result ['no_do'],
									'status' => $status_do,
									'tgl_buat' => $tgl_sekarang,
									'user_buat' => $this->_user,
								));
													
							
						}
						$no_urut++;							
					
					}					
										
				}															
			}
		}

		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			//$this->db->trans_rollback ();
			$this->db->trans_commit ();
			$this->result ['status'] = 1;			
			$this->result ['content'] = array('no_op' => array(), 'kode_farm' => $kode_farm);
			if(!$dibawah_ritase){
				$this->result ['content']['no_op'] = $list_op;
				$nama_farm = $this->db->select(array('nama_farm'))->where(array('kode_farm' => $kode_farm))->get('m_farm')->row_array();
				$periode_siklus = $this->db->select(array('periode_siklus'))->where(array('kode_farm' => $kode_farm, 'status_periode' => 'A'))->get('m_periode')->row_array();			 
				foreach($do_ekspedisi_email as $_kd => $perekspedisi){
					$nama_ekspedisi = $this->db->select(array('nama_ekspedisi'))->where(array('kode_ekspedisi' => $_kd))->get('m_ekspedisi')->row_array();			 
					$this->kirim_email($kode_farm,$nama_farm['nama_farm'],$perekspedisi,$nama_ekspedisi['nama_ekspedisi'],$periode_siklus['periode_siklus'],$tgl_kirim);
				}			
			}
		}

	$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	public function rekap() {
		$rekap = 1;
		$this->order ( $rekap );
	}
	public function detail_do() {
		$no_op = $this->input->post ( 'no_op' );
		$tgl_kirim = $this->input->post ( 'tgl_kirim' );
		$kode_ekspedisi = $this->input->post ( 'kode_ekspedisi' );
		$list_do = $this->mpp->cetak_do ( $no_op, $tgl_kirim, $kode_ekspedisi )->result_array ();
		/* gabung berdasarkan no_do */
		$result = array ();
		foreach ( $list_do as $perdo ) {
			$no_do = $perdo ['no_do'];
			if (! isset ( $result [$no_do] )) {
				$result [$no_do] = array ();
			}
			array_push ( $result [$no_do], $perdo );
		}
		$data ['list_do'] = $result;
		$this->load->view ( 'permintaan_pakan_v3/cetak_do', $data );
	}
	public function cetak_order_penjualan() {
		$this->load->view ( 'permintaan_pakan_v3/order_penjualan_marketing' );
	}
	public function cetak_order_pembelian() {
		$this->load->view ( 'permintaan_pakan_v3/order_pembelian_logistik' );
	}
	public function list_order_penjualan_marketing() {
		$cari = $this->input->post ( 'cari' );
		$tglKirim = $this->input->post ( 'tglKirim' );

		$startDate = $tglKirim ['startDate'];
		$endDate = $tglKirim ['endDate'];
		$list_order_logistik = $this->mpp->list_order_penjualan_marketing ( $startDate, $endDate, $cari )->result_array ();
		$data ['order_pembelian'] = $list_order_logistik;
		$this->load->view ( 'permintaan_pakan_v3/list_order_penjualan_marketing', $data );
	}
	public function list_order_pembelian_logistik() {
		$cari = $this->input->post ( 'cari' );
		$tglKirim = $this->input->post ( 'tglKirim' );

		$startDate = $tglKirim ['startDate'];
		$endDate = $tglKirim ['endDate'];
		$list_order_logistik = $this->mpp->list_order_pembelian_logistik ( $startDate, $endDate, $cari )->result_array ();
		$data ['order_pembelian'] = $list_order_logistik;
		$this->load->view ( 'permintaan_pakan_v3/list_order_pembelian_logistik', $data );
	}
	public function order_pembelian_print() {
		$no_op_logistik = $this->input->get ('no_op_logistik');
		$no_lpb = $this->input->get ('no_lpb');
		$data_pembelian = $this->mpp->cetak_order_pembelian_logistik ( $no_op_logistik,$no_lpb )->result_array ();
		$this->load->view ( 'permintaan_pakan_v3/cetak_order_pembelian_print', array (
				'data_do' => $data_pembelian
		) );
	}
	public function order_penjualan_pdf() {
		$no_op_logistik = $this->input->get ( 'no_op_logistik' );
		$no_op = $this->input->get ( 'no_op' );
		$data_penjualan = $this->mpp->cetak_order_penjualan_marketing ( $no_op_logistik,$no_op )->result_array ();

		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A5', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );
		$html = $this->load->view ( 'permintaan_pakan_v3/cetak_order_penjualan_pdf', array (
				'data_do' => $data_penjualan
		), true );
		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );

		$pdf->Output ( 'Order Penjualan.pdf', 'I' );
	}
	public function do_pdf($_no_do = NULL,$asAttachment = 0) {
		$no_do = $this->input->get ('no_do' );
		if(!empty($_no_do)){
			$asAttachment = 1;
			$no_do = $_no_do;
		}
		$tgl_kirim = $this->input->get ( 'tgl_kirim' );
		$kode_ekspedisi = $this->input->get ( 'kode_ekspedisi' );
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'P', PDF_UNIT, 'A5', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );
		// $pdf->SetHeaderData($image_file, 8, 'PT. WONOKOYO', 'Gempol', array(0,14,255), array(0,64,128));
		$list_do = $this->mpp->cetak_do ( $no_do, $tgl_kirim, $kode_ekspedisi )->result_array ();
		/* gabung berdasarkan no_do */
		$result = array ();
		foreach ( $list_do as $perdo ) {
			$no_do = $perdo ['no_do'];
			if (! isset ( $result [$no_do] )) {
				$result [$no_do] = array ();
			}
			array_push ( $result [$no_do], $perdo );
		}
		/* $code, $type, $x='', $y='', $w='', $h='', $style='', $align='', $distort=false */
		foreach ( $result as $do ) {
			$params = $pdf->serializeTCPDFtagParameters ( array (
					$do [0] ['no_do'],
					'QRCODE,H',
					'',
					'',
					15,
					15
			) );
			$b = '<tcpdf method="write2DBarcode" params="' . $params . '" />';
			$html = $this->load->view ( 'permintaan_pakan_v3/cetak_do_pdf', array (
					'data_do' => $do,
					'barcode' => $b
			), true );
			$pdf->AddPage ();
			$pdf->writeHTML ( $html, true, false, true, false, '' );
		}
		$filename = 'DO '.$no_do.'.pdf';
		if($asAttachment){
			$fileatt = $pdf->Output ( $filename, 'S' );
			//$data = chunk_split($fileatt);
			return $fileatt;			
		}else{
			$pdf->Output ( $filename, 'I' );
		}		
	}
	public function kirim_email($kode_farm, $nama_farm , $dos = array(),$ekspedisi = '', $periode, $tglkirim)
	{
		$this->load->config('email');
		$config = $this->config->item('email_from');		
		$this->load->library('email',$config);				
		$this->email->initialize($config);		
		$this->load->library('zip');
		$_tmpTgl = explode('-',$tglkirim);
		$_tmpTglKirim = $_tmpTgl[2].$_tmpTgl[1].$_tmpTgl[0];
		$strTglKirim = convertElemenTglWaktuIndonesia($tglkirim);
		$message = <<<SQL
		<strong><u>Kepada Yth. Ekspedisi {$ekspedisi}</u></strong>	
		<br /><br /><br />
		Dengan ini kami lampirkan dokumen delivery order untuk proses pengambilan dan pengiriman pakan dari Feedmill ke Farm {$nama_farm} pada tanggal <strong><u>{$strTglKirim}</u></strong>.<br />
		Mohon dokumen ini dapat dicetak  dan diserahkan kepada Admin Plant untuk proses verifikasi DO.<br />
		Demikian informasi dari kami.
		<br />
		<br />
		Hormat kami,
		<br /><br />
		Divisi Admin Budidaya<br />
		PT. Wonokoyo Jaya Corporindo<br />
		Jl. Taman Bungkul 1-3-5-7<br />
		Surabaya<br />
		Telp. 031 2956000 ext. 1608<br />
SQL;
		//$dos = array('DO/JD/2018-0052');	
		$subject = <<<sbj
			[WJC-ST Pakan] Dokumen DO {$kode_farm}/{$periode}/{$_tmpTglKirim} 
sbj;
		$from = "budidaya@wonokoyo.co.id";
		$alias_from = "WJC ST-Pakan";
		$to = $this->config->item('email_plotting_kendaraan')[$kode_farm];							
		$this->zip->clear_data();
		$this->zip->add_dir('DO');		
		foreach($dos as $do){
			$filename = 'DO/'.substr($do,6).'.pdf';
			$this->zip->add_data($filename, $this->do_pdf($do));						
		}		
		$filenameZip = 'DO/'.$kode_farm.'/'.$periode.'/'.$_tmpTglKirim.'.zip';
		$this->email->clear(TRUE);
		$this->email->attach($this->zip->get_zip(),'attachment',$filenameZip,'application/zip');				
		$send = Modules::run('client/email/send_email',$subject,$alias_from,$from,$to,$message);		
				
	}

	public function downloadPdfDO(){
		$dos = $this->input->post('dos');
		$this->load->library('zip');
		$this->zip->clear_data();
		$this->zip->add_dir('DO');		
		foreach($dos as $do){
			$filename = 'DO/'.substr($do,6).'.pdf';
			$this->zip->add_data($filename, $this->do_pdf($do));						
		}		
		$filenameZip = 'DO_'.date('YmdHis').'.zip';
		$this->zip->download();
	}

	public function approvereject(){
	$approveStatus = array(
		'KD' => 'R',
		'KDV' => 'N'
	);
	$rejectStatus = array(
		'KD' => 'T',
		'KDV' => 'T'
	);
	$user_level = $this->session->userdata('level_user');	
	$farmkirim = $this->input->post('farmkirim');
	$keterangan = $this->input->post('keterangan');	
	$tgl_kirim = array();
	
	$nextstatus = empty($keterangan) ? $approveStatus : $rejectStatus;
	$this->load->model ( 'permintaan_pakan_v3/m_do', 'mdo' );	
	$this->load->model('permintaan_pakan_v3/m_log_ploting_do', 'lpd');
	$tgl = Modules::run('home/home/getDateServer');
	$tglserver = $tgl->saatini;
	$createSinkron = $nextstatus[$user_level] == 'N' ? 1 : 0;
	$do_ekspedisi_email = array();
	$doSinkron = array();
	$this->db->trans_begin();
		foreach($farmkirim as $f){
			$kode_farm = $f['kode_farm'];
			$tgl_kirim_farm = $f['tgl_kirim'];
			if($createSinkron){
				if(!isset($doSinkron[$kode_farm])){
					$doSinkron[$kode_farm] = array();
				}
				$tmp_op_updated = $this->db->distinct()->select(array('no_op'))->where("status_do != 'N'")->where(array('tgl_kirim' => $tgl_kirim_farm, 'kode_farm' => $kode_farm))->get('do')->result_array();
				//$doSinkron[$kode_farm] = array_merge($doSinkron[$kode_farm],$tmp_op_updated);	
				foreach($tmp_op_updated as $_tmp){
					array_push($doSinkron[$kode_farm],$_tmp);
				}
			}
			$this->db->where("status_do != 'N'")->where(array('tgl_kirim' => $tgl_kirim_farm, 'kode_farm' => $kode_farm))->update('do',array('status_do'=>$nextstatus[$user_level]));
			//$this->mdo->update($f,array('status_do'=>$nextstatus[$user_level]));
			$list_do = $this->mdo->get($f)->result_array();
			foreach($list_do as $_do){
				$this->lpd->insert(array(
					'no_do' => $_do['NO_DO'],
					'status' => $nextstatus[$user_level],
					'tgl_buat' => $tglserver,
					'keterangan' => !empty($keterangan) ? $keterangan : NULL,
					'user_buat' => $this->_user,
				));
			}
			array_push($tgl_kirim,$f['tgl_kirim']);
		}		
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->result['message'] = 'Plotting DO gagal disimpan';
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['content'] = $doSinkron;
			$keteranganAction = empty($keterangan) ? 'diapprove' : 'direject';
			$this->result['message'] = 'Plotting DO untuk tanggal kirim '.implode(' , ',array_map('convertElemenTglWaktuIndonesia',array_unique($tgl_kirim))).' berhasil '.$keteranganAction.'.';
			if($createSinkron){
				if(!empty($doSinkron)){
					//log_message('error',json_encode($doSinkron));
					foreach($doSinkron as $kf => $perfarm){
						$tmp_op = array();
						//log_message('error',json_encode($perfarm));
						foreach($perfarm as $_opnya){
							array_push($tmp_op,$_opnya['no_op']);
						}
						$ekspedisi_plot = $this->db->distinct()->select(array('do.no_do','do.tgl_kirim','ov.kode_ekspedisi'))
												->where(array('do.kode_farm' => $kf ))
												->where_in('do.no_op',$tmp_op)
												->join('op_vehicle ov','ov.no_op = do.no_op and ov.no_urut = do.no_urut')
												->get('do')->result_array();
						if(!empty($ekspedisi_plot)){
							foreach($ekspedisi_plot as $ep){
								if(!isset($do_ekspedisi_email[$ep['kode_ekspedisi']])){
									$do_ekspedisi_email[$ep['kode_ekspedisi']] = array('tgl_kirim' => $ep['tgl_kirim'], 'dos' => array());
								}
								array_push($do_ekspedisi_email[$ep['kode_ekspedisi']]['dos'],$ep['no_do']);
							}
						}						
					}

					$nama_farm = $this->db->select(array('nama_farm'))->where(array('kode_farm' => $kode_farm))->get('m_farm')->row_array();
					$periode_siklus = $this->db->select(array('periode_siklus'))->where(array('kode_farm' => $kode_farm, 'status_periode' => 'A'))->get('m_periode')->row_array();			 
					foreach($do_ekspedisi_email as $_kd => $perekspedisi){
						$nama_ekspedisi = $this->db->select(array('nama_ekspedisi'))->where(array('kode_ekspedisi' => $_kd))->get('m_ekspedisi')->row_array();

						$this->kirim_email($kode_farm,$nama_farm['nama_farm'],$perekspedisi['dos'],$nama_ekspedisi['nama_ekspedisi'],$periode_siklus['periode_siklus'],$perekspedisi['tgl_kirim']);
					}
				}
			}
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	private function group_log_ploting($arr){
		$result = array();
		if(!empty($arr)){
			foreach($arr as $r){
				$kode_farm = $r['kode_farm'];
				$tgl_kirim = $r['tgl_kirim'];
				if(!isset($result[$tgl_kirim])){
					$result[$tgl_kirim] = array();
				}

				if(!isset($result[$tgl_kirim][$kode_farm])){
					$result[$tgl_kirim][$kode_farm] = array();
				}
				array_push($result[$tgl_kirim][$kode_farm],$r);
			}
		}
		
		return $result;
	}

	/** hapus op_vehicle,do_e,do_d,do dan log_ploting_do, returnnya log_ploting_do yang lama */		
	private function hapus_ploting_lama($kode_farm,$tgl_kirim){
		$log_do = array();
		$dos = $this->db->select(array('no_do','no_op'))->where(array('kode_farm'=>$kode_farm, 'tgl_kirim' => $tgl_kirim,'status_do' => 'T'))->get('do')->result_array();
		if(!empty($dos)){
			$dos_arr = array_column($dos,'no_do');
			$op_arr = array_column($dos,'no_op');
			$log_do = $this->db->distinct()
								->select(array('no_urut','status','keterangan','tgl_buat','user_buat'))
								->where_in('no_do',$dos_arr)
								->get('log_ploting_do')
								->result_array();
			$this->db->where_in('no_do',$dos_arr)->delete('log_ploting_do');
			$this->db->where_in('no_op',$op_arr)->delete('op_vehicle');
			$this->db->where_in('no_do',$dos_arr)->delete('do_e');
			$this->db->where_in('no_do',$dos_arr)->delete('do_d');
			$this->db->where_in('no_do',$dos_arr)->delete('do');					
		}
		return $log_do;
	}
	
	
}
