<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Sales_order extends MY_Controller {
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
		$this->load->helper('stpakan');
		$level_user = $this->session->userdata('level_user');
		$this->load->model('sales_order/m_pengajuan_harga','ph');
		$this->load->model('permintaan_glangsing/m_ppsk','ppsk');
		$this->load->model('master/m_farm','mf');
		$this->load->model('master/m_pelanggan','mp');
		$this->load->model('sales_order/m_sales_order','so');

		$this->akses = array(
			'LOG' => 'create',
			'KDLOG' => 'review',
			'KVLOG' => 'approve',
			'KDV' => 'approve',
		);
		$this->tombol = array(
			'create' => '<div id="btn1"><button class="btn btn-default" onclick="salesOrder.openLaporanStokGlangsingPage()">Kembali</button>
						<button class="btn btn-primary" id="addNewSO" onclick="salesOrder.addNewSO()">Tambah SO</button>
						<button class="btn btn-default" id="cancelSO" onclick="salesOrder.cancelSO()">Batalkan SO</button>
						<button class="btn btn-default" disabled onclick="salesOrder.cetakSO(this)">Cetak SO</button>
						<button class="btn btn-default" disabled onclick="salesOrder.cetakDO(this)">Cetak DO</button></div>
						<div id="btn2"><button class="btn btn-default" onclick="salesOrder.kembali()">Kembali</button>
						<button class="btn btn-primary" id="addNewSO" onclick="salesOrder.clickSubmit()">Simpan</button></div>',
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
			);
		$this->_user = $this->session->userdata ( 'kode_user' );
        $this->_user_level = $this->session->userdata('level_user');
		$this->result = array (
			'status' => 0,
			'content' => ''
		);


		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));

	}

	public function index($kode_farm = null, $kode_siklus = null){
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
				$this->LOG($kode_farm,$kode_siklus);
				break;
			default :
				echo 'Halaman ini bukan untuk anda ';
		}
	}

	public function LOG($kode_farm = null,$kode_siklus = null){
		$this->load->model('sales_order/m_sales_order','mso');
		$this->load->config('stpakan');
		$tglSekarang = date('Y-m-d');
		$datalist = array(
			'tgl_sekarang' => $tglSekarang,
			'kode_farm' => $kode_farm,
			'nama_farm' => $this->config->item('namaFarm'),			
			'kode_siklus' => $kode_siklus,
			'user_peminta' => $this->_user,
			'tombol' => $this->tombol[$this->akses[$this->_user_level]],
			'level_user' => $this->_user_level,
			'list_so' => $this->mso->listSalesOrderHarian($kode_farm,$kode_siklus),
			'keterangan_so' => simpleGrouping($this->mso->listKeteranganSO($kode_farm),'no_so')
		);
		$sales_order_header = $this->load->view('sales_order/'.$this->grup_farm.'/list_sales_order_header',$datalist,TRUE);

		$data = array(
			'sales_order_header'	=> $sales_order_header,
		);

		$this->load->view('sales_order/'.$this->grup_farm.'/sales_order',$data);
	}

	public function addNewROwSO(){
		$kode_farm = $this->input->post('kode_farm');
		$kode_siklus = $this->input->post('kode_siklus');
		$tglSekarang = date('Y-m-d');
		$farm = $this->mf->get_farm_browse();
		$barang = $this->getSisaStokTerakhir($kode_farm,$kode_siklus);
		$harga = $this->ph->getPengajuanHargaAktif($kode_farm);
		$str = '';
		foreach ($farm as $key => $val) {
			if($val['kode_farm'] == $kode_farm){
				$str = '<tr class="new">';
				$str .= '<td class="no_so"></td>';
				$str .= '<td class="tgl_so">'.convertElemenTglIndonesia($tglSekarang).'</td>';
				$str .= '<td class="no_do"></td>';
				$str .= '<td class="nama_farm"><input type="hidden" name="kode_pelanggan"/>'.$val['nama_farm'].'</td>';
				$str .= '<td class="nama_pelanggan"><input class="form-control" type="text" name="nama_pelanggan"/></td>';
				$str .= '<td class="alamat"><input class="form-control" type="text" name="alamat"/></td>';
				$str .= '<td class="no_telp"><input class="form-control telepon" type="text" name="no_telp"/></td>';
				$str .= '<td class="term_pembayaran"><input class="form-control" style="width:50px" type="text" name="term_pembayaran"/></td>';
				$str .= '<td class="jumlah_total"></td>';
				$str .= '<td class="harga_total"></td>';
				$str .= '<td class="keterangan"></td>';
				$str .= '</tr>';
			}
		}
		if($str != ''){
			echo json_encode(array('status'=>1,'content'=>$str, 'barang'=>$barang, 'harga'=>$harga));
		}else{
			echo json_encode(array('status'=>0,'message'=>'Terjadi kesalahan. Klik kembali dan ulangi pilih Farm yang dituju.'));
		}

	}

	public function addProduct(){
		$kode_farm = $this->input->post('kode_farm');
		$kode_siklus = $this->input->post('kode_siklus');
		$barang = $this->so->getBarang($kode_farm,$kode_siklus);

		$option = '<option value="">Pilih</option>';
		foreach ($barang as $key => $val) {
			$option .= '<option value="'.$val['kode_barang'].'">'.$val['nama_barang'].'</option>';
		}

		$str = '<tr data-row_id="'.time().'">';
		$str .= '<td class="jenis_barang"><div class="form-group">
					<div class="col-md-11">
						<select class="form-control" onchange="salesOrder.onchangeProduct(this)">'.$option.'</select>
					</div>
					<div class="col-md-1" style="padding:5px 5px;">
						<i class="glyphicon glyphicon-minus" onclick="salesOrder.deleteRow(this)"></i>
					</div>
			    </div></td>';
		$str .= '<td class="jumlah"><input class="form-control" type="text" name="jumlah" onchange="salesOrder.onchangeJumlah(this)" value="0"/></td>';
		$str .= '<td class="satuan" style="padding:15px 10px; text-align:center"></td>';
		$str .= '<td class="harga" style="padding:15px 10px; text-align:right"></td>';
		$str .= '<td class="harga_total" style="padding:15px 10px; text-align:right"></td>';
		$str .= '</tr>';

		if($str != ''){
			echo json_encode(array('status'=>1,'content'=>$str));
		}else{
			echo json_encode(array('status'=>0,'message'=>'Terjadi kesalahan. Klik kembali dan ulangi pilih Farm yang dituju.'));
		}

	}

	public function getPelanggan(){
		$pelanggan = $this->mp->get_pelanggan_browse();
		//kode_pelanggan, nama_pelanggan, alamat, kota, no_telp, term_pembayaran
		$return = array();
		foreach ($pelanggan as $key => $val) {
			$return[] = array(
				'id' => $val['kode_pelanggan']
				, 'name' => $val['nama_pelanggan']
				, 'alamat' => (empty($val['alamat']))? '' : $val['alamat']
				, 'kota' => (empty($val['kota']))? '' : $val['kota']
				, 'no_telp' => (empty($val['no_telp']))? '' : $val['no_telp']
				, 'term_pembayaran' => (empty($val['term_pembayaran']))? '' : $val['term_pembayaran']
			);
		}

		echo json_encode($return);
	}

	public function getListDetail($kode_farm,$kode_siklus = NULL,$tgl_transaksi = NULL){
		$this->load->model('sales_order/m_laporan_stok_glangsing','mlsg');
		$datalist = array(
			'stokAwal' => $this->getSisaStokTerakhir($kode_farm,$kode_siklus,$tgl_transaksi)
			,'soHariIni' => arr2DToarrKey($this->so->getSalesOrderPengurang($kode_farm,$tgl_transaksi),'kode_barang')
		);
		echo json_encode(
			array(
				'status' => 1,
				'content' => $this->load->view('sales_order/'.$this->grup_farm.'/list_sales_order_detail',$datalist,TRUE)
			)
		);

	}

	public function simpan(){
		$data = $this->input->post();
		$status_save = true;
		$tglSekarang = date('Y-m-d');
		$header = $data['header'];
		$detail = $data['detail'];
		$checkNama = $this->mp->checkNamaPelanggan($header['nama_pelanggan']);

		if(count($checkNama) == 0){
			$kode_pelanggan = $this->mp->kode_pelanggan();
			$dataPelanggan = array(
				'KODE_PELANGGAN' => $kode_pelanggan
				, 'NAMA_PELANGGAN' => $header['nama_pelanggan']
				, 'ALAMAT' => $header['alamat']
				, 'NO_TELP' => $header['no_telp']
				, 'TERM_PEMBAYARAN' => $header['term_pembayaran']
			);
			$this->db->insert("m_pelanggan", $dataPelanggan);
		}else{
			$kode_pelanggan = $checkNama[0]['KODE_PELANGGAN'];
		}
		/* periksa apakah ada SO untuk pelanggan tersebut yang belum diverifikasi atau belum batal */
		//$soLamaPelanggan = $this->so->get_by(array('kode_pelanggan'=>$kode_pelanggan, 'status_order' => 'N', 'tgl_so' => $tglSekarang));
		$soLamaPelanggan = 0;
		if(empty($soLamaPelanggan)){
			$this->db->trans_begin();
			$noDo = $this->so->no_so($header['kode_farm']);
			$this->result['result'] = $noDo;
			$dataHeader = array(
				'no_so' => $noDo
				,'no_do' => $noDo
				,'kode_farm' => $header['kode_farm']
				,'kode_siklus' => $header['kode_siklus']
				, 'kode_pelanggan' => $kode_pelanggan
				, 'alamat' => $header['alamat']
				, 'no_telp' => $header['no_telp']
				, 'term_pembayaran' => $header['term_pembayaran']
				, 'jumlah_total' => $header['jumlah_total']
				, 'harga_total' => $header['harga_total']
			);
			$this->db->insert("sales_order", $dataHeader);
			$status_save = ($this->db->affected_rows() > 0) ? $status_save : false;

			foreach ($detail as $key => $val) {
				$dataDetail = array(
					'no_so' => $noDo
					, 'no_urut' => $val['index']
					, 'kode_barang' => $val['kode_barang']
					, 'jumlah' => $val['jumlah']
					, 'harga_jual' => $val['harga_jual']
					, 'harga_total' => $val['harga_total']
				);
				$dataGlangsing = array(
					'no_so' => $noDo
					,'no_urut' => $val['index']
					,'kode_barang' => $val['kode_barang']
					,'kode_farm' => $header['kode_farm']
					,'kode_siklus' => $header['kode_siklus']
					,'jumlah' => $val['jumlah']
				);
				$this->db->insert("sales_order_d", $dataDetail);
				$this->updateGlangsing($dataGlangsing,'SO_OUT');
				$status_save = ($this->db->affected_rows() > 0) ? $status_save : false;
			}

			$dataLog = array(
				'no_so' => $noDo
				, 'user_buat' => $this->_user
			);
			$this->db->insert("log_sales_order", $dataLog);
			$status_save = ($this->db->affected_rows() > 0) ? $status_save : false;
			$_nama_farm = $this->db->select(array('NAMA_FARM'))->where(array('kode_farm' => $header['kode_farm']))->get('m_farm')->row();
			$nama_farm = (!empty($_nama_farm)) ?  $_nama_farm->NAMA_FARM : ' undefined ';
		if($status_save){
			$this->db->trans_commit();
			$harga_total = angkaRibuan($dataHeader['harga_total']);
			$hari_ini = date('d/m/Y');
			$pesan = <<<SQL
Pelanggan Yth. pesanan {$noDo} telah dibuat tgl {$hari_ini} di Farm {$nama_farm}.
Lakukan pembayaran Rp. {$harga_total}.

SQL;

				$nomer = array($header['no_telp']);
			//	$nomer = array("085733659400");
			//	Modules::run('client/csms/sendNotifikasi',$pesan,$nomer);
				$this->result['result'] = 'success';
				$this->result['status'] = 1;
				$this->result['message'] = 'Data SO berhasil disimpan.';
			}
			else{
				$this->db->trans_rollback();
				$this->result['result'] = 'error';
				$this->result['status'] = 0;
				$this->result['message'] = 'SO baru gagal disimpan.';
			}
		}else{
			$this->result['message'] = 'Masih ada SO yang belum diverifikasi untuk pelanggan ini, dengan nomer SO '.$soLamaPelanggan->no_so;
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function getSisaStokTerakhir($kode_farm,$kode_siklus,$tglTransaksi = NULL){
		$result = array();
		$this->load->model('sales_order/m_laporan_stok_glangsing','lsg');
		$this->load->model('sales_order/m_sales_order','mso');
		$list_estimasi_jumlah = $this->lsg->getEstimasiStokTerakhir($kode_farm,$kode_siklus);
	//	$so_do_harian = arr2DToarrKey($this->mso->getSalesOrderHarian($kode_farm,$kode_siklus),'kode_barang');
		//$so_do_pengurang = arr2DToarrKey($this->mso->getSalesOrderPengurang($kode_farm,$kode_siklus,$tglTransaksi),'kode_barang');
		
		if(!empty($list_estimasi_jumlah)){
			foreach($list_estimasi_jumlah as $k){
				$tmp = array(
					'kode_barang' => $k['kode_barang']
					,'nama_barang' => $k['nama_barang']
					,'jml_stok' => $k['jml_estimasi']
				);
		/*		if(isset($so_do_pengurang[$k['kode_barang']])){
					$tmp['jml_stok'] -= $so_do_pengurang[$k['kode_barang']]['jumlah'];
				}
						
				if(isset($so_do_harian[$k['kode_barang']])){
					$tmp['jml_stok'] -= $so_do_harian[$k['kode_barang']]['jumlah'];
				}
		*/				
				$result[$k['kode_barang']] = $tmp;
			}
		}
		return $result;
	}

	public function cetakDO(){
		$no_so = $this->input->post('no_so');
		$this->do_pdf($no_so);
	}

	public function cetakSO(){
		$no_so = $this->input->post('no_so');
		$this->so_pdf($no_so);
	}

	public function do_pdf($no_so) {
		$this->load->model('sales_order/m_sales_order','mso');
		$this->load->model('sales_order/m_sales_order_d','msod');
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'P', PDF_UNIT, 'A4', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );
		$pdf->SetFontSize(16);
		$pdf->SetMargins(8, 8, 8, 8);
		$header_do = $this->mso->get($no_so);
		$detail_do = $this->msod->get_many_by(array('no_so' => $no_so));
		$barang = $this->db->select(array('nama_barang','kode_barang'))->where(array('grup_barang' => '000117'))->get('m_barang')->result_array();
		$sj = $this->db->select(array('nama_pelanggan','no_kendaraan','nama_sopir','alamat_pelanggan'))->where(array('no_so' => $no_so))->get('surat_jalan')->row();
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
		//$infoCetak = 'Information and Technology Division '.convertElemenTglWaktuIndonesia(date('Y-m-d H:i:s'),1);
	//	$pdf->Cell(60, 1, $infoCetak, 0, 0, 'R', 0, '', 0);
		$marginPage = 3;
		$pdf->Line($marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,$marginPage);
		$pdf->Line($pdf->getPageWidth()-$marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,  $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage, $pdf->getPageHeight()-$marginPage, $pdf->getPageWidth()-$marginPage, $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage,$marginPage,$marginPage, $pdf->getPageHeight()-$marginPage);
		$pdf->Output ( 'Delivery Order.pdf', 'I' );
	}

	private function so_pdf($no_so) {
		$this->load->model('sales_order/m_sales_order','mso');
		$this->load->model('sales_order/m_sales_order_d','msod');

		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A5', true, 'UTF-8', false );
		$pdf->SetFontSize(10);
		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );
		$pdf->SetMargins(4, 5, 5,5);
		$header_do = $this->mso->get($no_so);
		/* pastikan do sudah berstatus A */
		if($header_do->status_order != 'A'){
			echo 'SO nomer '.$no_so.' belum diapprove';
			exit();
		}
		$detail_do = $this->msod->get_many_by(array('no_so' => $no_so));
		$barang = $this->db->select(array('nama_barang','kode_barang'))->where(array('grup_barang' => '000117'))->get('m_barang')->result_array();
		$pelanggan = $this->db->select(array('nama_pelanggan'))->where(array('kode_pelanggan' => $header_do->kode_pelanggan))->get('m_pelanggan')->row();

		$html = $this->load->view ( 'sales_order/bdy/cetak_so_pdf', array (
				'header_do' => $header_do,
				'detail_do' => $detail_do,
				'barang' => arr2DToarrKey($barang,'kode_barang'),
				'pelanggan' => $pelanggan
		), true );
		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );
		$pdf->setXY(110,122);
		$pdf->SetFontSize(7);
		$infoCetak = 'Information and Technology Division '.convertElemenTglWaktuIndonesia(date('Y-m-d H:i:s'),1);
		$pdf->Cell(80, 5, $infoCetak, 0, 0, 'R', 0, '', 0);
		$marginPage = 3;
		$pdf->Line($marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,$marginPage);
		$pdf->Line($pdf->getPageWidth()-$marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,  $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage, $pdf->getPageHeight()-$marginPage, $pdf->getPageWidth()-$marginPage, $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage,$marginPage,$marginPage, $pdf->getPageHeight()-$marginPage);
		$pdf->Output ( 'Sales Order.pdf', 'I' );
	}

	public function detail_so_view(){
		$this->load->model('sales_order/m_sales_order_d','msod');
		$no_so = $this->input->get('no_so');
		$so = $this->so->as_array()->get($no_so);
		$barang = $this->db->select(array('nama_barang','kode_barang'))->where(array('grup_barang' => '000117'))->get('m_barang')->result_array();
		$detail_do = $this->msod->get_many_by(array('no_so' => $no_so));
		$data = array(
			'detail_do' => $detail_do,
			'barang' => arr2DToarrKey($barang,'kode_barang'),
			'no_so' => $no_so,
			'no_ref' => $so['no_ref']

		);
		$this->load->view('sales_order/bdy/list_sales_order_detail_view',$data);
	}

	public function printDO($no_so){
		$this->do_pdf($no_so);
	}

	public function printSO($no_so){
		$this->so_pdf($no_so);
	}
	public function batalSO(){
		$renew = $this->input->post('renew');
		$no_so = $this->input->post('no_so');
		$this->db->trans_begin();
		$so = $this->so->as_array()->get_by(array('no_so' => $no_so));
		/** periksa apakah sudah dilakukan verifikasi DO atau belum  */
		$this->load->model('sales_order/m_surat_jalan','msj');
		$sj = $this->msj->as_array()->get_by(array('no_so' => $no_so));
		if(!empty($sj)){
			if(!empty($sj['tgl_verifikasi'])){
				$this->result['message'] = 'SO tidak dapat dibatalkan, sudah dilakukan verifikasi DO pada '.convertElemenTglWaktuIndonesia($sj['tgl_verifikasi']);
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
				return;	
			}
		}
		$kode_farm = $so['kode_farm'];
		$kode_siklus = $so['kode_siklus'];
		if($renew){
			$soBaru = $this->buatUlangSO($so);
			$this->result['content'] = array (
				'no_so' => $soBaru
			);
		}else{
			/** kembalikan stoknya */
			$this->load->model('sales_order/m_sales_order_d','msod');
			$sod = $this->msod->as_array()->get_many_by(array('no_so' => $no_so));
			foreach($sod as $_sod){
				$dataGlangsing = array(
					'no_so' => $no_so				
					,'kode_barang' => $_sod['kode_barang']
					,'kode_farm' => $kode_farm
					,'kode_siklus' => $kode_siklus
					,'jumlah' => $_sod['jumlah']
				);			
				$this->updateGlangsing($dataGlangsing,'SO_IN');
			}
		}		
		/** update status sales_order */
		$this->so->update($no_so,array('status_order'=>'V'));
		$this->load->model('sales_order/m_log_sales_order','lso');		
		$no_urut = $this->so->no_urut_log_so($no_so);
		$data_log_so = array(
			'no_so' => $no_so,
			'no_urut' => $no_urut,
			'user_buat' => $this->_user,
			'status' => 'V'
		);
        $this->lso->insert($data_log_so);			
							

		if ($this->db->trans_status() === FALSE )
		{			
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = 'Pembatalan SO berhasil dilakukan';
			$harga_total = angkaRibuan($so['harga_total']);
			$hari_ini = convertElemenTglWaktuIndonesia(date('Y-m-d H:i:s'));
			$pesan = <<<SQL
Pelanggan Yth. pesanan {$no_so} telah dibatalkan pada {$hari_ini}.

SQL;

				$nomer = array($so['no_telp']);
			//	$nomer = array("085733659400");
			//	Modules::run('client/csms/sendNotifikasi',$pesan,$nomer);
		}

		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
		
	}

	private function buatUlangSO($no_so){		
		$noDo = $this->so->no_so($no_so['kode_farm']);		
		$no_ref = $no_so['no_so'];
		$no_so['no_so'] = $noDo;
		$no_so['no_do'] = $noDo;
		$no_so['no_ref'] = $no_ref;
		$no_so['tgl_so'] = date('Y-m-d');
		$this->so->insert($no_so);
		$sql = <<<SQL
		insert into sales_order_d
		select '{$noDo}', no_urut, kode_barang, jumlah, harga_jual, harga_total 
		from sales_order_d where no_so = '{$no_ref}'
SQL;
		$sqlLog = <<<SQL
		insert into log_sales_order
		select '{$noDo}', no_urut, status, user_buat, getdate(), keterangan
		from log_sales_order where no_so = '{$no_ref}'
SQL;
		$sqlPembayaran = <<<SQL
		INSERT INTO pembayaran
		SELECT '{$noDo}','{$noDo}',nominal_harga,nominal_bayar,status_bayar,lampiran,tgl_buat,user_buat FROM pembayaran WHERE no_so = '{$no_ref}'
SQL;
		$this->db->query($sql);
		$this->db->query($sqlLog);
		$this->db->query($sqlPembayaran);
		return $noDo;
	}

	public function updateGlangsing($data,$tipe = 'SO_OUT'){
		$this->load->model('sales_order/m_glangsing_movement_kp','gm');
		$this->load->model('sales_order/m_glangsing_movement_kp_d','gmd');
		$no_so = $data['no_so']; 					
		$kodeBudget = $data['kode_barang'];
		$kodefarm = $data['kode_farm'];
		$kodeSiklus = $data['kode_siklus'];		
		$whereGlangsingBudgetMovement = array('kode_barang' => $kodeBudget,'kode_farm' => $kodefarm,'kode_siklus' => $kodeSiklus);		
		$stokAkhirGlangsingBudget = $this->gm->get_by($whereGlangsingBudgetMovement);		
		$jmlStokAwalBudget = 0;		
		if(!empty($stokAkhirGlangsingBudget)){
			$jmlStokAwalBudget = $stokAkhirGlangsingBudget->jml_stok;
		}		
		$tgl_buat = date('Y-m-d H:i:s');
		$glangsingMovementBudget = array(
			'kode_farm' => $kodefarm,
			'kode_siklus' => $kodeSiklus,
			'kode_barang' => $kodeBudget,
			'no_referensi' => $no_so,
			'jml_awal' => 0,
			'jml_order' => 0,
			'jml_akhir' => 0,
			'tgl_transaksi' => $tgl_buat,
			'keterangan1' => $tipe,
			'keterangan2' => '',
			'user_buat' => $this->_user,
		);
		$jumlah = $data['jumlah'];
		$stokMinta = ($tipe == 'SO_OUT') ? -1 * $jumlah : $jumlah;
		$glangsingMovementBudget['keterangan2'] = '';
		$glangsingMovementBudget['jml_order'] = $stokMinta;
		$glangsingMovementBudget['jml_awal'] =  $jmlStokAwalBudget;
		$glangsingMovementBudget['jml_akhir'] =  $jmlStokAwalBudget + $stokMinta;
		$this->gmd->insert($glangsingMovementBudget);
		unset($whereGlangsingBudgetMovement['jml_stok']);
		$this->gm->update_by($whereGlangsingBudgetMovement,array('jml_stok' => $jmlStokAwalBudget + $stokMinta));		
	}
}
