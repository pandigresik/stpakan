<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Cetak_form_lhk extends MX_Controller{
	protected $result;
	
	function __construct(){
		parent::__construct();
		
		$this->load->model("riwayat_harian_kandang/M_riwayat_harian_kandang", "m_riwayat");
		$this->load->helper("stpakan");
        $this->load->config('stpakan');
		
		$this->_user = $this->session->userdata('kode_user');
        $this->_namauser = $this->session->userdata('nama_user');
        $this->_farm = $this->session->userdata('kode_farm');
	}
	
	function index(){
		$kodefarm = $this->_farm;
		$kodepegawai = $this->session->userdata('level_user_db') == 'PPB' ? $this->_user : NULL;
		$farm = $this->m_riwayat->get_farm($kodefarm);		
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"]);
		
		$data["kode_farm"] = $kodefarm;
		$data["nama_farm"] = $farm["nama_farm"];
		$data["today"] = $date[0];
		$data["today_full"] = $fulldate["today"];		
		$data["cetak_form_lhk"] = $this->m_riwayat->get_cetak_form_lhk($kodefarm, $kodepegawai);
		$data["list_view_tblcetak_form_lhk"] = $this->load->view('tblcetak_form_lhk',$data,true);
		$this->load->view("cetak_form_lhk", $data);
	}
	
	function get_list_LHK(){
		$kodefarm = $this->_farm;
		$kodepegawai = $this->_user;
		
		$farm = $this->m_riwayat->get_farm($kodefarm);
		
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"]);
		
		$data["kode_farm"] = $kodefarm;
		$data["nama_farm"] = $farm["nama_farm"];
		$data["today"] = $date[0];
		$data["today_full"] = $fulldate["today"];
		
		$data["cetak_form_lhk"] = $this->m_riwayat->get_cetak_form_lhk($kodefarm, $kodepegawai);
		
		return $this->load->view('tblcetak_form_lhk',$data);
	}
	
	//penamabahan untuk desain finger operator cetak LHK
	function detail_finger_LHK() {
		$kodefarm = $this->_farm;
		$kodepegawai = $this->session->userdata('level_user_db') == 'PPB' ? $this->_user : NULL;
		$noreg = $this->input->post("noreg");
		$level = $this->input->post("level");
		$tgllhk = $this->input->post("tgllhk");
		
		$farm = $this->m_riwayat->get_farm($kodefarm);
		
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"]);
		
		$data["kode_farm"] = $kodefarm;
		$data["nama_farm"] = $farm["nama_farm"];
		$data["today"] = $date[0];
		$data["today_full"] = $fulldate["today"];
		
		// $data["cetak_form_lhk"] = $this->m_riwayat->get_cetak_form_lhk($kodefarm, $kodepegawai);
		$data["header_finger_LHK"] = $this->m_riwayat->get_header_finger_lhk($noreg, $tgllhk);
		$umur = $data["header_finger_LHK"]['umur_hari'];
		if ($umur>=1) {
			$data["penimbangan_per_sekat"] = $this->m_riwayat->get_detail_penimbangan_per_sekat($noreg, $tgllhk);
			$data["populasi"] = $this->m_riwayat->get_detail_populasi($noreg, $tgllhk);
			$data["pakan"] = $this->m_riwayat->get_detail_pakan($noreg, $tgllhk);
			$data["max_rowspan"] = $this->m_riwayat->get_max_rowspan(count($data["penimbangan_per_sekat"]), count($data["populasi"]), count($data["pakan"]));		
			
			//pengecekan jml pakai, harus lebih kecil atau sama dengan STOK_kandang
			$data['pakan_pakai'] = array();
			$pakanBisaDipakai = $this->db->select('m_barang.kode_barang,m_barang.nama_barang,kandang_movement.jml_stok')
									//	->where('kandang_movement.jml_stok > 0')
										->where(array('no_reg' => $noreg))
										->join('m_barang','m_barang.kode_barang = kandang_movement.kode_barang')
										->get('kandang_movement')->result_array();		
			foreach ($pakanBisaDipakai as $key=>$val) {
				$data['pakan_pakai'][$val['kode_barang']] = $val;
			}
			
			//pengecekan jml permintaan, harus lebih kecil atau sama dengan STOK_gudang
			$this->load->model("riwayat_harian_kandang/M_rhk", "m_rhk");
			$tglkebutuhan = tglSetelah($tgllhk,2);
			$jml_maks_pp_order = $this->m_rhk->maksPPKandangTglKebutuhan($noreg, $kodefarm, $tglkebutuhan);
			
			foreach ($data["pakan"] as $key=>$val) {
				$data["pakan"][$key]['JML_STOK_PAKAN_PAKAI'] = $data['pakan_pakai'][$val['KODE_BARANG']]['jml_stok'];
				$data["pakan"][$key]['JML_MAKS_PP_ORDER'] = isset($jml_maks_pp_order[$val['KODE_BARANG']]) ? $jml_maks_pp_order[$val['KODE_BARANG']] : 0;
			}
<<<<<<< HEAD

=======
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
			
			//populasi (penjumlahan mati + afkir), harus lebih kecil dari STOK_AYAM
			/* cari jumlah ayamnya dan stok akhir kandang tgl lhk sebelumnya */
			$tglRhkSebelumnya = tglSebelum($tgllhk,1);
			$rhkLalu = $this->m_rhk->as_array()->get_by(array('no_reg' => $noreg, 'tgl_transaksi' => $tglRhkSebelumnya));
			$jumlahAyam	= 0;				
			if(!empty($rhkLalu)){
				$jumlahAyam = $rhkLalu['C_JUMLAH'];	
				/* kurangkan jumlah ayam dengan yang sudah dipanen */		
				$ayamPanen = $this->db->select('sum(jumlah_aktual) as panen')->where('tgl_buat between \''.$rhkLalu['TGL_BUAT'].'\' and getdate()')->where(array('no_reg' => $noreg))->get('realisasi_panen')->row_array();
				$jumlahAyam -= $ayamPanen['panen'];
			}else{
				/** cari dari bapdoc */
				$bapdoc = $this->db->select('STOK_AWAL')->where(array('no_reg' => $noreg))->get('bap_doc')->row_array();
				$jumlahAyam = $bapdoc['STOK_AWAL'];			
			}
			
			$data["populasi"][0]['JUMLAH_AYAM'] = $jumlahAyam;
			
			if (($data["max_rowspan"][0]["max_rowspan"]) % count($data["penimbangan_per_sekat"]) == 0) {
				$rowspan = ($data["max_rowspan"][0]["max_rowspan"]) / count($data["penimbangan_per_sekat"]);
				$sisa_rowspan = 0;
			} else {
				$rowspan = floor(($data["max_rowspan"][0]["max_rowspan"]) / count($data["penimbangan_per_sekat"]));
				$sisa_rowspan = $rowspan;
			}
			$loop = 0;
			foreach ($data["penimbangan_per_sekat"] as $key=>$val) {
				$rowspan_total = $rowspan+($sisa_rowspan*(($key+1==count($data["penimbangan_per_sekat"])) ? 0 : 1));
				$val['rowspan'] = $rowspan_total;
				$data["detail_finger_LHK"]['penimbangan'][] = $val;
				$loop = $rowspan_total;
			}
			
			if (($data["max_rowspan"][0]["max_rowspan"]) % count($data["populasi"]) == 0) {
				$rowspan = ($data["max_rowspan"][0]["max_rowspan"]) / count($data["populasi"]);
				$sisa_rowspan = 0;
			} else {
				$rowspan = floor(($data["max_rowspan"][0]["max_rowspan"]) / count($data["populasi"]));
				$sisa_rowspan = $rowspan;
			}
			$loop = 0;
			foreach ($data["populasi"] as $key=>$val) {
				$rowspan_total = $rowspan+($sisa_rowspan*(($key+1==count($data["populasi"])) ? 0 : 1));
				$val['rowspan'] = $rowspan_total;
				$data["detail_finger_LHK"]['populasi'][] = $val;
				$loop = $rowspan_total;
			}
			
			if (($data["max_rowspan"][0]["max_rowspan"]) % count($data["pakan"]) == 0) {
				$rowspan = ($data["max_rowspan"][0]["max_rowspan"]) / count($data["pakan"]);
				$sisa_rowspan = 0;
			} else {
				$rowspan = floor(($data["max_rowspan"][0]["max_rowspan"]) / count($data["pakan"]));
				$sisa_rowspan = $rowspan;
			}
			$loop = 0;
<<<<<<< HEAD
			
			foreach ($data["pakan"] as $key=>$val) {
				//$rowspan_total = $rowspan+($sisa_rowspan*(($key+1==count($data["pakan"])) ? 0 : 1));
				$val['rowspan'] = 1;//$rowspan_total;
				$data["detail_finger_LHK"]['pakan'][$key] = $val;
				$loop = $rowspan_total;
			}
			
=======
			foreach ($data["pakan"] as $key=>$val) {
				$rowspan_total = $rowspan+($sisa_rowspan*(($key+1==count($data["pakan"])) ? 0 : 1));
				$val['rowspan'] = $rowspan_total;
				$data["detail_finger_LHK"]['pakan'][$loop] = $val;
				$loop = $rowspan_total;
			}
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
		}
		return $this->load->view('detail_finger_LHK',$data);
	}
	
	function simpan_rhk_verificator() {
		$noreg = $this->input->post("noreg");
		$tgllhk_sebelum = $this->input->post("tgllhk_sebelum");
		$verificator = $this->input->post("verificator");
		$level = $this->input->post("level");
		
		$fulldate = $this->m_riwayat->get_today();
		
		$where_rhk = array(
			'no_reg' => $noreg,
			'tgl_transaksi' => $tgllhk_sebelum,
		);
		
		$this->db->trans_begin();
		if ($level=='OPERATOR')
			$this->db->where($where_rhk)->update('RHK',array('ACK1' => $fulldate["today"], 'ACK_KF' => $fulldate["today"],'USER_ACK1' => $verificator));
		else
			$this->db->where($where_rhk)->update('RHK',array('ACK2' => $fulldate["today"], 'USER_ACK2' => $verificator));
		
		if ($this->db->trans_status() === FALSE ){
			$this->db->trans_rollback();
			$result = 0;
		}else{
			$this->db->trans_commit();
			$result = 1;
		}
		
		if ($result) {
			$this->result['result'] = 'success';
			$this->result['status'] = 1;
		} else {
			$this->result['result'] = 'failed';
			$this->result['status'] = 0;
		}
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	
	function print_lhk($seven_segment = 1){
		$this->load->config('stpakan');
		$namaFarm = $this->config->item('namaFarm');
		$barcode = $this->input->get('barcode');			
		$kodefarm = substr($barcode,0,2);				
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"]);
		
		$detailrhk = $this->m_riwayat->get_detail_print_lhk($barcode);				
		$nama_farm = strtoupper($namaFarm[$kodefarm]);
		$no_reg =  $detailrhk['NO_REG'];
		$kandang = $detailrhk['KODE_KANDANG'];
		$tgllhk = $detailrhk['tgl_lhk'];
		$user_cetak = $detailrhk['USER_CETAK'];
		$arr_tgl_lhk = explode("-",$tgllhk);
		$tglkebutuhan = tglSetelah($tgllhk,2); 
		
		$umur = $detailrhk['umur_hari'];
		$nomor_barcode = $barcode; 
		
		$pakanBisaDipakai = $this->db->select('m_barang.kode_barang,m_barang.nama_barang')->where(array('no_reg' => $no_reg))->join('m_barang','m_barang.kode_barang = kandang_movement.kode_barang')->get('kandang_movement')->result_array();
		foreach ($pakanBisaDipakai as $key=>$val) {
			$data['pakan_pakai'][$val['kode_barang']] = $val;
		}
		$this->load->model('riwayat_harian_kandang/M_rhk_rekomendasi_pakan','rekomendasi');
		$this->load->model("riwayat_harian_kandang/M_rhk", "m_rhk");
		$data['pakan_rekomendasi'] = array();
		$standartPakan = $this->rekomendasi->rekomendasiPakanStandart($no_reg,$tglkebutuhan);
		/* cari jumlah ayamnya dan stok akhir kandang tgl lhk sebelumnya */
		$rhkLalu = $this->m_rhk->as_array()->get_by(array('no_reg' => $no_reg, 'tgl_transaksi' => tglSebelum($tgllhk,1)));
		$pakanPP = $this->db->select('lpb_e.kode_barang,m_barang.nama_barang,lpb_e.komposisi_pakan')->where(array('tgl_kebutuhan' => $tglkebutuhan,'no_reg' => $no_reg))
					->join('m_barang','m_barang.kode_barang = lpb_e.kode_barang')
					->join('lpb','lpb.no_lpb = lpb_e.no_lpb and lpb.status_lpb = \'A\'')->get('lpb_e')->result_array();
		$jumlahAyam	= 0;				
		if(!empty($rhkLalu)){
			$jumlahAyam = $rhkLalu['C_JUMLAH'];			
		}else{
			/** cari dari bapdoc */
			$bapdoc = $this->db->select('STOK_AWAL')->where(array('no_reg' => $no_reg))->get('bap_doc')->row_array();
			$jumlahAyam = $bapdoc['STOK_AWAL'];			
		}							
		
		foreach ($pakanPP as $key=>$val) {
			$pengali =  $val['komposisi_pakan'];
			$rekomendasiPakan = ceil(($standartPakan['pkn_hr'] * $jumlahAyam / 50000) * $pengali);
			$data['pakan_rekomendasi'][$val['kode_barang']] = array('tglkebutuhan'=>$tglkebutuhan, 'kebutuhan_pakan'=> $rekomendasiPakan,'nama_barang' => $val['nama_barang']);
		}
		
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'P', PDF_UNIT, 'A4', true, 'UTF-8', false );
		
		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );

		$data['no_reg'] = $no_reg;
		$data['namafarm'] = $nama_farm;
		$data['namakandang'] = $kandang;
		$data['tgllhk'] = $tgllhk;
		$data['umur'] = $umur;
		
		$data['default_sekat'] = array('1', '2', '3', '4');
		$data['operasional_kandang'] = !empty($operasional_kandang) ? $operasional_kandang : "_________________";
		$data['pengawas_kandang'] = !empty($pengawas_kandang) ? $pengawas_kandang : "_________________";
		$data['kepala_farm'] = !empty($kepala_farm) ? $kepala_farm : "_________________";
		
		$nama_user_cetak = $this->m_riwayat->get_nama_pegawai($user_cetak);
		$data["nama_user"] = $nama_user_cetak[0]['NAMA_PEGAWAI'];
		$data["today_date"] = $date[0];
		$data["today_time"] = $date[1];
		
		$style = array(
			'position' => '',
			'align' => 'C',
			'stretch' => false,
			'fitwidth' => true,
			'cellfitalign' => '',
			'border' => true,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255),
			'text' => true,
			'font' => 'helvetica',
			'fontsize' => 5,
			'stretchtext' => 4
		);

		$params = $pdf->serializeTCPDFtagParameters ( array (
					$nomor_barcode,
					'QRCODE,H',
					'',
					'',
					15,
					15,
					$style,
		));
		$b = '<tcpdf method="write2DBarcode" params="' . $params . '" />';
		$data['barcode'] = $b;
		if($seven_segment){
			$html = $this->load->view('riwayat_harian_kandang/form_lhk_seven_segment', $data, true);
		}else{
			$html = $this->load->view('riwayat_harian_kandang/form_lhk', $data, true);
		}
		
		$pdf->AddPage();
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('cetak_form_lhk.pdf', 'I');
	}
	
	function insert_rhk_cetak(){
		$user_login = $this->_user;
		$user_cetak = $this->input->post("user_cetak"); //nantinya diganti dengan user yang melakukan finger print
		$fulldate = $this->m_riwayat->get_today();
		
		$no_reg = $this->input->post("no_reg");
		$farm = $this->input->post("farm");
		$tgllhk = $this->input->post("tgllhk");
		$tgllhk_sebelum = $this->input->post("tgllhk_sebelum");
		$kandang = $this->input->post("kandang");
		$cetak = $this->input->post("cetak");
		$arr_tgl_lhk = explode("-",$tgllhk);
		if ($cetak)
			$nomor_barcode = $farm . $kandang. '-' . $arr_tgl_lhk[2] . $arr_tgl_lhk[1] . substr($arr_tgl_lhk[0],-2,2);
		else
			$nomor_barcode = null;
		
		$perubahan_data_lhk_raw = $this->input->post("perubahan_data_lhk");
		$kandang_movement = $this->input->post("kandang_movement");
		if (isset($perubahan_data_lhk_raw) && !empty($perubahan_data_lhk_raw)) {
			foreach ($perubahan_data_lhk_raw as $key=>$val) {
				if (isset($val['unique_data']) && !empty($val['unique_data'])) {
					$unique_data = $val['unique_data'];
					$perubahan_data_lhk["$val[jenis]"]["$no_reg"]["$tgllhk_sebelum"]["$val[$unique_data]"] = $val;
				} else {
					$perubahan_data_lhk["$val[jenis]"]["$no_reg"]["$tgllhk_sebelum"][] = $val;
				}
			}
		}
				
		try {
			//insert perubahan data LHK
			$result = true;
			if (isset($perubahan_data_lhk['penimbangan']) && !empty($perubahan_data_lhk['penimbangan']))
				$result = $result && $this->m_riwayat->update_detail_penimbangan_rhk($no_reg, $tgllhk_sebelum, $perubahan_data_lhk);
			
			if (isset($perubahan_data_lhk['populasi']) && !empty($perubahan_data_lhk['populasi'])) {
				//get umur terlebih dahulu
				$header_finger_LHK = $this->m_riwayat->get_header_finger_lhk($no_reg, $tgllhk_sebelum);
				$umur = $header_finger_LHK['umur_hari'];
				/** cari jumlah awal */
				$jmlAwalArr = $this->getJumlahAwal($umur,$no_reg);
				$result = $result && $this->m_riwayat->update_detail_populasi_rhk($no_reg, $tgllhk_sebelum, $perubahan_data_lhk, $umur, $jmlAwalArr);
			}	
			
			if (isset($perubahan_data_lhk['pakan']) && !empty($perubahan_data_lhk['pakan'])) {
				foreach ($perubahan_data_lhk['pakan']["$no_reg"]["$tgllhk_sebelum"] as $key=>$val) {
					$berat_pakan[$key] = $this->get_berat_pakan($val['jml_pakai'], $no_reg, $val['kode_barang']);
					if(empty($berat_pakan[$key])){
						$berat_pakan[$key] = $val['jml_pakai'] * 50;
					}
				}
				$result = $result && $this->m_riwayat->update_detail_pakan_rhk($no_reg, $tgllhk_sebelum, $berat_pakan, $perubahan_data_lhk);
			}
			
			/* insert ke kandang movement dan kandang_movement_d*/
			if (isset($kandang_movement) && !empty($kandang_movement)) {
				$this->db->trans_begin();
				foreach ($kandang_movement as $key=>$val) {
					/** cari stok terakhir noreg tersebut */
					$stok_pakan = $this->db->where(array('no_reg' => $no_reg, 'kode_barang' => $val['kode_barang']))->get('kandang_movement')->row_array();
					$berat_pakan = $this->get_berat_pakan($val['jml_pakai'], $no_reg, $val['kode_barang']);
					if(empty($berat_pakan)){
						$berat_pakan = $val['jml_pakai'] * 50;
					}
					$stok_akhir = $stok_pakan['JML_STOK'] - $val['jml_pakai'];
					$berat_akhir = $stok_pakan['BERAT_STOK'] - $berat_pakan;
					
					$kandang_movement_d = array(
						'no_reg' => $no_reg,
						'kode_barang' => $val['kode_barang'],
						'tgl_transaksi' => $tgllhk_sebelum,
						'jenis_kelamin' => $val['jenis_kelamin'],
						'jml_awal' => $stok_pakan['JML_STOK'], 
						'jml_order' => -1 * $val['jml_pakai'],
						'jml_akhir' => $stok_akhir,
						'berat_awal' => $stok_pakan['BERAT_STOK'],
						'berat_order' => -1 * $berat_pakan,
						'berat_akhir' => $berat_akhir,
						'keterangan1' => 'LHK',
						'keterangan2' => $no_reg,
						'user_buat' => $user_login
					);
					$where_kandang_movement = array(
						'no_reg' => $no_reg,
						'kode_barang' => $val['kode_barang'],
					);
					$this->db->insert('kandang_movement_d',$kandang_movement_d);
					$this->db->where($where_kandang_movement)->update('kandang_movement',array('jml_stok' => $stok_akhir, 'berat_stok' => $berat_akhir));
				}
			
				if ($this->db->trans_status() === FALSE ){
					$this->db->trans_rollback();
					$result = 0;
				}else{
					$this->db->trans_commit();
					$result = $result && 1;
				}
			}
			
			if ($result) {
				//insert rhk cetak
				$rhk_cetak = array(
					"NO_REG" => $no_reg,
					"TGL_TRANSAKSI" => $tgllhk,
					"USER_CETAK" => $user_cetak,
					"USER_LOGIN" => $user_login,
					"TGL_CETAK" => $fulldate["today"],
					"BARCODE" => $nomor_barcode,
				);
				$result = $this->m_riwayat->insert_rhk_cetak($rhk_cetak);
				if ($result) {
					$this->result['result'] = 'success';
					$this->result['status'] = 1;
					$this->result['content'] = $nomor_barcode;
					$sudahEntry = $this->sudahEntryLhkSemuaFlok($no_reg, $tgllhk_sebelum);
					$this->result['generate_order'] = $sudahEntry;
				} else {
					$this->result['result'] = 'failed';
					$this->result['status'] = 0;
				}
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
			} else {
				$this->result['result'] = 'failed';
				$this->result['status'] = 0;
				$this->output
				->set_content_type('application/json')
				->set_output(json_encode($this->result));
			}
		} catch(Exception $ex){
			return false;
		}
	}
	
	function get_berat_pakan($stok, $no_reg, $kode_barang){
		$this->load->model("riwayat_harian_kandang/M_riwayat_harian_kandang_bdy", "m_riwayat_bdy");
		$items = $this->m_riwayat_bdy->get_kompensasi_stok($stok, $no_reg, $kode_barang);
		$total_berat = 0;
		$total_stok = 0;

		for($i=0;$i<count($items);$i++){
			$total_berat += $items[$i]["kg_out"];
		}

		return $total_berat;
	}
	
	private function sudahEntryLhkSemuaFlok($noreg,$tglTransaksi){		
		$result = array('status'=> 0, 'flok' => 0);
		$ks = $this->db->select('kode_siklus,flok_bdy')->where(array('no_reg' => $noreg))->get('kandang_siklus')->row_array();
		$query_rhk = $this->db->select(array('no_reg'))->like('no_reg',substr($noreg,0,10),'after')->where(array('tgl_transaksi' => $tglTransaksi))->where('ack1 is not null')->get_compiled_select('rhk');
		$rhk = $this->db->select('count(*) as total_rhk')->where(array('status_siklus' => 'O'))->where($ks)->where('no_reg not in ('.$query_rhk.')')->get('kandang_siklus')->row_array();
	
		if(empty($rhk['total_rhk'])){
			$tglKebutuhan = tglSetelah($tglTransaksi,2);
			$result['status'] = 1;
			$result['flok'] = $ks['flok_bdy'];
			$result['kode_farm'] = substr($noreg,0,2);
			$result['tgl_kebutuhan'] = $tglKebutuhan;
		}
		return $result;
	}
	
	private function getJumlahAwal($umur,$noreg){
		/** jika $umur <= 7, maka c_jumlah = c_awal - sum(c_mati), c_awal = c_jumlah_sebelumnya - c_afkir , jika umur > 7 maka c_awal selalu tetap  dan c_jumlah = c_awal - c_mati - c_afkir */
		$totalMati = 0;
		$c_awal_sebelumnya = 0;
		if($umur <= 7){			
			if($umur == 1){
				$tmp = $this->db->where(array('no_reg' => $noreg))->get('bap_doc')->row_array();
				$c_jumlah_sebelumnya = $tmp['STOK_AWAL'];
			}else{
				$_totalMati = $this->db->select_sum('c_mati','mati')->where(array('no_reg' => $noreg))->get('rhk')->row_array();
				$totalMati = $_totalMati['mati'];
				// $tmp = $this->db->select(array('c_awal','c_jumlah','c_mati','c_afkir'))->where(array('no_reg' => $noreg))->order_by('tgl_transaksi','desc')->get('rhk')->row_array();				
				$tmp = $this->m_riwayat->get_populasi_pakan_rhk($noreg);
				$c_jumlah_sebelumnya = $tmp['C_AWAL'];
			}
		}else{
			// $tmp = $this->db->select(array('c_awal','c_jumlah','c_mati','c_afkir'))->where(array('no_reg' => $noreg))->order_by('tgl_transaksi','desc')->get('rhk')->row_array();
			$tmp = $this->m_riwayat->get_populasi_pakan_rhk($noreg);
			$c_jumlah_sebelumnya = $tmp['c_jumlah'];
			$c_awal_sebelumnya = $tmp['c_awal'];
		}
		return array('c_jumlah_sebelumnya' => $c_jumlah_sebelumnya, 'c_mati' => $totalMati, 'c_awal_sebelumnya' => $c_awal_sebelumnya);
	}
}
