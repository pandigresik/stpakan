<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Riwayat_harian_kandang_bdy extends MX_Controller{

	function __construct(){
		parent::__construct();

		$this->load->model("M_riwayat_harian_kandang_bdy", "m_riwayat");
		$this->load->model("Pengambilan_barang/M_transaksi", "m_transaksi");
		$this->load->helper("stpakan");
	}

	function index(){
		$kodefarm = $this->session->userdata("kode_farm");
		$grup_farm = $this->session->userdata("grup_farm");

		$farm = $this->m_riwayat->get_farm($kodefarm);
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"]);

		$data["kode_farm"] = $kodefarm;
		$data["nama_farm"] = $farm["nama_farm"];
		$data["today"] = $date[0];

		if($grup_farm == "BDY"){
			$this->load->view("lhk_bdy", $data);
		}else{
			$this->load->view("lhk", $data);
		}
	}

	function get_kandang_farm(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		// $cek_bapd = $this->m_riwayat->cek_bap($kode_farm)
		$kandang = $this->m_riwayat->get_kandang_siklus($kode_farm);

		echo json_encode($kandang);
	}

	function get_last_lhk(){
		$no_reg = ($this->input->post("no_reg")) ? $this->input->post("no_reg") : null;

		$lhk = $this->m_riwayat->get_last_lhk($no_reg);

		echo json_encode($lhk);
	}

	function get_data_last_lhk(){
		$no_reg = ($this->input->post("no_reg")) ? $this->input->post("no_reg") : null;
		$tgl_lhk = ($this->input->post("tgl_lhk")) ? $this->input->post("tgl_lhk") : null;
		$tgl_doc_in = ($this->input->post("tgl_doc_in")) ? $this->input->post("tgl_doc_in") : null;

		$populasi = $this->m_riwayat->get_jumlah_bj_last_lhk($no_reg, $tgl_lhk, $tgl_doc_in); // return no_reg, tgl, jml
		$populasi_awal = $this->m_riwayat->get_populasi_awal($no_reg, $tgl_lhk);
		$pakan = $this->m_riwayat->get_pakan_last_lhk($no_reg);		
		$batas_pakai_pakan = $this->m_riwayat->get_batas_pakai_pakan($no_reg, $tgl_lhk);
		$jumlah_panen = $this->m_riwayat->get_jumlah_panen($no_reg, $tgl_lhk);
		$bb_rata_last = $this->m_riwayat->get_last_bb_rata($no_reg);

		$batas_pakai = array();
		if(is_array($batas_pakai_pakan) and count($batas_pakai_pakan) >= 1){
			$c_batas = 0;
			foreach($batas_pakai_pakan as $ba){
				if($ba["jenis_kelamin"] == 'C'){
					$c_batas = ($ba["jml_performance"] > $ba["detail_order"]) ? $ba["jml_performance"] : $ba["detail_order"];
				}
			}

			$batas_pakai = array("C"=>$c_batas);
		}
		$data = array(
			"populasi" => $populasi,
			"populasi_awal" => $populasi_awal,
			"pakan" => $pakan,
			"batas_pakai_pakan" => $batas_pakai,
			"bb_rata_last" => ($bb_rata_last["bb_rata_last"]*1),
			"jumlah_panen" => $jumlah_panen["jumlah_akhir"]
		);

		echo json_encode($data);
	}

	function get_bb_std(){
		$umur = $this->input->post("umur");
		$kode_std_budidaya = $this->input->post("kode_std_budidaya");

		$row = $this->m_riwayat->get_bb_std($umur, $kode_std_budidaya);

		$json = array("kode_std_budidaya"=>$row["kode_std_budidaya"], "target_bb"=>$row["target_bb"]);

		echo json_encode($json);
	}

	function simpan_lhk(){
		// echo "<pre>";
		// print_r($this->input->post());
		// die();
		$fulldate = $this->m_riwayat->get_today();

		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$kode_kandang = ($this->input->post("kode_kandang")) ? $this->input->post("kode_kandang") : null;
		$noreg = ($this->input->post("noreg")) ? $this->input->post("noreg") : null;
		$tgl_lhk = ($this->input->post("tgl_lhk")) ? $this->input->post("tgl_lhk") : null;
		$umur = ($this->input->post("umur") and $this->input->post("umur") != '-') ? $this->input->post("umur") : 0;
		$bb_rata = ($this->input->post("bb_rata")) ? $this->input->post("bb_rata") : null;
		$tutup_siklus = ($this->input->post("tutup_siklus") and $this->input->post("tutup_siklus") == 'Y') ? $noreg : null;
		$populasi_awal = ($this->input->post("populasi_awal")) ? $this->input->post("populasi_awal") : 0;

		$sekat_no = ($this->input->post("sekat_no")) ? $this->input->post("sekat_no") : null;
		$sekat_jml = ($this->input->post("sekat_jml")) ? $this->input->post("sekat_jml") : null;
		$sekat_bb = ($this->input->post("sekat_bb")) ? $this->input->post("sekat_bb") : null;
		$sekat_ket = ($this->input->post("sekat_ket")) ? $this->input->post("sekat_ket") : null;

		$rhk_penimbangan = array();
		for($i=0;$i<count($sekat_no);$i++){
			$rhk_penimbangan[] = array(
				"no_reg"=>$noreg,
				"tgl_transaksi"=>$tgl_lhk,
				"berat"=>$sekat_bb[$i],
				"jumlah"=>$sekat_jml[$i],
				"jenis_kelamin"=>'C',
				"sekat"=>$sekat_no[$i],
				"keterangan"=>$sekat_ket[$i]

			);
		}

		$populasi_awal_campur = ($this->input->post("populasi_awal_campur")) ? $this->input->post("populasi_awal_campur") : 0;
		$tambah_campurLain = ($this->input->post("tambah_campurLain")) ? $this->input->post("tambah_campurLain") : 0;
		$kurangCampurMati = ($this->input->post("kurangMati")) ? $this->input->post("kurangMati") : 0;
		$kurang_campurAfkir = ($this->input->post("kurang_campurAfkir")) ? $this->input->post("kurang_campurAfkir") : 0;
		$kurang_campurLain = ($this->input->post("kurang_campurLain")) ? $this->input->post("kurang_campurLain") : 0;
		$populasi_akhir_campur = ($this->input->post("populasi_akhir_campur")) ? $this->input->post("populasi_akhir_campur") : 0;
		$populasi_dh_campur = ($this->input->post("populasi_dh_campur") and $this->input->post("populasi_dh_campur") != '-') ? $this->input->post("populasi_dh_campur") : 0;
		$ket_kematian = ($this->input->post("ket_kematian")) ? $this->input->post("ket_kematian") : null;

		$lhk_header = array(
			"no_reg" => $noreg,
			"tgl_transaksi" => $tgl_lhk,
			"c_terima_lain" => $tambah_campurLain,
			"c_mati" => $kurangCampurMati,
			"c_afkir" => $kurang_campurAfkir,
			"c_kurang_lain" => $kurang_campurLain,
			"c_berat_badan" => $bb_rata,
			"c_jumlah" => $populasi_akhir_campur,
			"c_daya_hidup" => $populasi_dh_campur,
			"c_awal" => $populasi_awal,
			"user_buat"=>$this->session->userdata('kode_user'),
			"keterangan1"=>$ket_kematian
		);

		$c_pakan = ($this->input->post("c_pakan")) ? $this->input->post("c_pakan") : null;
		$c_stokAwalKg = ($this->input->post("c_stokAwalKg")) ? $this->input->post("c_stokAwalKg") : null;
		$c_stokAwalSak = ($this->input->post("c_stokAwalSak")) ? $this->input->post("c_stokAwalSak") : null;
		$c_kirimKg = ($this->input->post("c_kirimKg")) ? $this->input->post("c_kirimKg") : null;
		$c_kirimSak = ($this->input->post("c_kirimSak")) ? $this->input->post("c_kirimSak") : null;
		$c_terpakaiKg = ($this->input->post("c_terpakaiKg")) ? $this->input->post("c_terpakaiKg") : null;
		$c_terpakaiSak = ($this->input->post("c_terpakaiSak")) ? $this->input->post("c_terpakaiSak") : null;
		$c_stokAkhirKg = ($this->input->post("c_stokAkhirKg")) ? $this->input->post("c_stokAkhirKg") : null;
		$c_stokAkhirSak = ($this->input->post("c_stokAkhirSak")) ? $this->input->post("c_stokAkhirSak") : null;

		$kompensasi_stok_h = array();
		$kompensasi_stok_d = array();
		$kandang_movement = array();
		$kandang_movement_d = array();
		$lhk_pakan = array();
		$pakanKg_terpakai = 0;

		for($i=0;$i<count($c_pakan);$i++){
			$kandang_movement[] = array(
				"no_reg"=>$noreg,
				"kode_barang"=>$c_pakan[$i],
				"jenis_kelamin"=>'C',
				"jml_stok"=>$c_stokAkhirSak[$i],
				"berat_stok"=>$c_stokAkhirKg[$i]
			);

			$kandang_movement_d[] = array(
				"no_reg"=>$noreg,
				"kode_barang"=>$c_pakan[$i],
				"tgl_transaksi"=>$tgl_lhk,
				"keterangan1"=>'LHK',
				"keterangan2"=>$noreg,
				"jenis_kelamin"=>'C',
				//"jml_awal"=>$c_stokAkhirSak[$i] - ($c_terpakaiSak[$i] * -1),
				"jml_awal"=>$c_stokAwalSak[$i],
				"jml_order"=>($c_terpakaiSak[$i] * -1),
				"jml_akhir"=>$c_stokAkhirSak[$i],
				//"berat_awal"=>$c_stokAkhirKg[$i] - ($c_terpakaiKg[$i] * -1),
				"berat_awal"=>$c_stokAwalKg[$i],
				"berat_order"=>($c_terpakaiKg[$i] * -1),
				"berat_akhir"=>$c_stokAkhirKg[$i],
				"user_buat"=>$this->session->userdata('kode_user')
			);

			$lhk_pakan[] = array(
				"no_reg"=>$noreg,
				"tgl_transaksi"=>$tgl_lhk,
				"kode_barang"=>$c_pakan[$i],
				"jenis_kelamin"=>'C',
				"jml_terima"=>$c_kirimSak[$i],
				"jml_pakai"=>($c_terpakaiSak[$i] * 1),
				"jml_akhir"=>$c_stokAkhirSak[$i],
				"brt_terima"=>$c_kirimKg[$i],
				"brt_pakai"=>($c_terpakaiKg[$i] * 1),
				"brt_akhir"=>$c_stokAkhirKg[$i]
			);

			$pakanKg_terpakai += $c_terpakaiKg[$i];

			$result_kompensasi_stok = $this->m_riwayat->get_kompensasi_stok($c_terpakaiSak[$i], $noreg, $c_pakan[$i]);
			if(count($result_kompensasi_stok) >0){
				for($j=0;$j<count($result_kompensasi_stok);$j++){
					$t_kodebarang = $result_kompensasi_stok[$j]["kode_barang"];
					$t_nopengiriman = $result_kompensasi_stok[$j]["no_pengiriman"];
					$t_tgl_transaksi = $result_kompensasi_stok[$j]["tgl_transaksi"];
					$t_sak_awal = $result_kompensasi_stok[$j]["sak_awal"];
					$t_sak_out = $result_kompensasi_stok[$j]["sak_out"];
					$t_kg_sisa = $result_kompensasi_stok[$j]["kg_sisa"];
					$t_kg_rata2 = $result_kompensasi_stok[$j]["kg_rata2"];
					$t_kg_awal = $result_kompensasi_stok[$j]["kg_awal"];
					$t_kg_out = $result_kompensasi_stok[$j]["kg_out"];

					$t_kg_rata2_baru = (($t_sak_awal - $t_sak_out) > 0) ? ($t_kg_sisa - $t_kg_out)/($t_sak_awal - $t_sak_out) : 0;

					$kompensasi_stok_h[] = array(
						"no_pengiriman" => $t_nopengiriman,
						"no_reg" => $noreg,
						"tgl_transaksi" => $t_tgl_transaksi,
						"kode_barang" => $t_kodebarang,
						"sak_sisa" => ($t_sak_awal - $t_sak_out),
						"kg_sisa" => ($t_kg_sisa - $t_kg_out),
						"kg_rata2" => $t_kg_rata2_baru
					);

					$kompensasi_stok_d[] = array(
						"no_reg" => $noreg,
						"tgl_transaksi" => $tgl_lhk,
						"kode_barang" => $t_kodebarang,
						"keterangan1" => 'LHK',
						"keterangan2" => $t_nopengiriman,
						"sak_awal" => $t_sak_awal,
						"sak_in" => 0,
						"sak_out" => $t_sak_out,
						"sak_sisa" => ($t_sak_awal - $t_sak_out),
						"kg_awal" => $t_kg_awal,
						"kg_in" => 0,
						"kg_out" => $t_kg_out,
						"kg_sisa" => ($t_kg_sisa - $t_kg_out)
					);
				}
			}
		}

		$result = $this->m_riwayat->insert_lhk_awal($lhk_header, $kandang_movement, $kandang_movement_d, $lhk_pakan, $rhk_penimbangan, $tutup_siklus, $kompensasi_stok_h, $kompensasi_stok_d);

		if($result){

			$lhk_old = $this->m_riwayat->get_yesterday_lhk($noreg, $tgl_lhk);
			// $lhk_pakan_sum = $this->m_riwayat->get_accumulated_pakan($noreg, $tgl_lhk, true);
			$lhk_pakan_sum = $this->m_riwayat->get_accumulated_pakan($noreg, $tgl_lhk, false);
			$total_pakan = isset($lhk_pakan_sum) ? $lhk_pakan_sum["total_pakai"] : 0;
			// $total_pakan = $total_pakan + $pakanKg_terpakai;
			$total_pakai = $total_pakan;

			$fcr = 0;
			$ip = 0;
			$adg = 0;
			if($populasi_akhir_campur > 0 and $bb_rata > 0){
				$fcr = ($total_pakai / $populasi_akhir_campur) / $bb_rata;
				if($fcr > 0 and $umur > 0){
					$ip = round(($populasi_dh_campur * $bb_rata / 1000) * 10000 / ($fcr * $umur));
					// $ip = round(($populasi_dh_campur * $bb_rata / 100) * 100 / ($fcr * $umur));

					if(($umur-($umur-1)) > 0 and isset($lhk_old["c_berat_badan"])){
						if(isset($lhk_old["c_berat_badan"]) and !empty($lhk_old["c_berat_badan"])){
							$adg = (($bb_rata*1000)-($lhk_old["c_berat_badan"]*1000)) / ($umur-($umur-1));
						}else{
							$adg = "";
						}
					}
				}
			}else{
				$fcr = 0;
			}

		//	echo json_encode(array("msg"=>"success", "tutup_siklus"=>$tutup_siklus, "ip"=>$ip, "fcr"=>$fcr, "adg"=>$adg));
			$_r = array("msg"=>"success", "tutup_siklus"=>$tutup_siklus, "ip"=>$ip, "fcr"=>$fcr, "adg"=>$adg);
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode($_r));
		}
		else{
		//	echo json_encode(array("msg"=>"failed"));
			$_r = array("msg"=>"failed");
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode($_r));
		}
	}

	function printRetur(){

		$kodefarm = $this->input->post("inp_print_kodefarm");
		$namafarm = $this->input->post("inp_print_farm");
		$namakandang = $this->input->post("inp_print_kandang");
		$tgl = $this->input->post("inp_print_tgl");

		$kodebarang = $this->input->post('inp_print_kodebarang');
		$namabarang = $this->input->post('inp_print_namabarang');
		$jml = $this->input->post('inp_print_jml');
		$berat = $this->input->post('inp_print_berat');
		$bentuk = $this->input->post('inp_print_bentuk');

		$items = array();
		for($i=0;$i<count($kodebarang);$i++){
			$items[] = array(
				"kodebarang"=>$kodebarang[$i],
				"namabarang"=>$namabarang[$i],
				"jumlah"=>$jml[$i],
				"berat"=>$berat[$i],
				"bentuk"=>$bentuk[$i]
			);
		}

		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'P', PDF_UNIT, 'A5', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );

		$data['namafarm'] = $namafarm;
		$data['namakandang'] = $namakandang;
		$data['tgltutupsiklus'] = $tgl;
		$data['items'] = $items;
		$html = $this->load->view ( 'riwayat_harian_kandang/retur', $data, true );

		// echo $html;
		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );
		$pdf->Output ( 'retur_pakan.pdf', 'I' );
	}

	function buat_pengajuan_retur(){
		$fulldate = $this->m_riwayat->get_today();

		$no_reg = ($this->input->post("no_reg")) ? $this->input->post("no_reg") : null;
		$setuju = ($this->input->post("setuju")) ? $this->input->post("setuju") : null;

		if(isset($no_reg) and isset($setuju)){
			$sisa_pakan = $this->m_riwayat->buat_persetujuan_retur($no_reg, $this->session->userdata("kode_user"), $setuju);

		//	echo json_encode(array("result"=>"success"));
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode(array("result"=>"success")));
		}
		else{
		//	echo json_encode(array("result"=>"failed"));

		$this->output
					->set_content_type('application/json')
					->set_output(json_encode(array("result"=>"failed")));
		}
	}

	function go_view_lhk(){
		$no_reg = $this->input->post('no_reg');
		$tgl_lhk = $this->input->post('tgl_lhk');
		$doc_in = $this->input->post('tgl_doc_in');

		$this->load->model('riwayat_harian_kandang/m_rhk','rhk');
		$this->load->model('riwayat_harian_kandang/m_rhk_pakan','rhk_pakan');
		$this->load->model('riwayat_harian_kandang/m_riwayat_harian_kandang','mrhk');

		$batas_pakan = $this->mrhk->get_batas_pakai_pakan($no_reg,$tgl_lhk);

		$where = array('no_reg'=> $no_reg,'tgl_transaksi'=> $tgl_lhk);
		$rhk = $this->m_riwayat->get_lhk($no_reg, $tgl_lhk); /* ini pasti satu baris */
		$rhk_pakan = $this->m_riwayat->get_lhk_pakan($no_reg, $tgl_lhk);
		$jumlah_panen = $this->m_riwayat->get_jumlah_panen($no_reg, $tgl_lhk);
		$date1=date_create($doc_in);
		$date2=date_create($tgl_lhk);
		$diff=date_diff($date1,$date2);
		$umur_hari = ($diff->days*1);
		// $umur_hari = ($diff->days*1) + 1;

		$rhk_penimbangan = $this->m_riwayat->get_lhk_penimbangan($no_reg, $tgl_lhk);
		$lhk_pakan_sum = $this->m_riwayat->get_accumulated_pakan($no_reg, $tgl_lhk, false);
		$total_pakai = isset($lhk_pakan_sum) ? $lhk_pakan_sum["total_pakai"] : 0;

		/* periksa apakah pemakaian pakan melebihi batas atau tidak */
		$class_pakan = array('C' => '');
		$total_pakan = array('C' => 0);
		foreach($rhk_pakan as $rp){
			$total_pakan[$rp["JENIS_KELAMIN"]] += $rp["BRT_PAKAI"] ;
		}

		foreach($batas_pakan as $bp){
			$arr = array($bp['jml_performance'],$bp['detail_order']);
			$class_pakan[$bp['jenis_kelamin']] = $total_pakan[$bp['jenis_kelamin']] - max($arr) > 0 ? 'style="color:#FF0000;"' : 'style="color:#000;"';
		}

		$fcr = 0;
		$ip = 0;
		$adg = 0;

		// $lhk_old = $this->m_riwayat->get_yesterday_lhk($no_reg, $tgl_lhk);
		$lhk_old = $this->m_riwayat->get_pastday_timbang_bb($no_reg, $tgl_lhk);

		$pakanKg_terpakai = 0;
		for($j=0;$j<count($rhk_pakan);$j++){
			$pakanKg_terpakai += $rhk_pakan[$j]["BRT_PAKAI"];
		}

		// if($rhk["C_BERAT_BADAN"] > 0 and $pakanKg_terpakai > 0){
		// if($rhk["C_BERAT_BADAN"] > 0 and $pakanKg_terpakai > 0){
			// $fcr = ($pakanKg_terpakai / $rhk["C_JUMLAH"]) / $rhk["C_BERAT_BADAN"];
		if($rhk["C_BERAT_BADAN"] > 0 and $total_pakai > 0){
			$fcr = ($total_pakai / $rhk["C_JUMLAH"]) * 1000 / ($rhk["C_BERAT_BADAN"]*1000);

			$valid_dh = $rhk["C_DAYA_HIDUP"]/100;
			$valid_bb_rata = $rhk["C_BERAT_BADAN"] * 1000;

			$ip = round((($valid_dh) * ($valid_bb_rata) / 1000) * 10000 / ($fcr * $umur_hari));
			log_message("error", "-----------------------------");
			log_message("error", "valid_dh:".$valid_dh);
			log_message("error", "valid_bb_rata:".$valid_bb_rata);
			log_message("error", "fcr:".$fcr);
			log_message("error", "umur_hari:".$umur_hari);

			// $ip = round(($rhk["C_DAYA_HIDUP"] * $rhk["C_BERAT_BADAN"] / 100) * 100 / ($fcr * $umur_hari));

			if(isset($lhk_old["C_BERAT_BADAN"]) and !empty($lhk_old["C_BERAT_BADAN"])){
				$adg = (($rhk["C_BERAT_BADAN"]*1000)-($lhk_old["C_BERAT_BADAN"]*1000)) / ($umur_hari-($umur_hari-1));
			}else{
				$adg = "-";
			}
		}else{
			$fcr = "-";
			$ip = "-";
			$adg = "-";
		}

		$data = array(
				'rhk' => $rhk,
				'rhk_penimbangan' => $rhk_penimbangan,
				'rhk_pakan' => $rhk_pakan,
				'tgl_doc_in' => $doc_in,
				'tgl_lhk' => $tgl_lhk,
				'umur' => $umur_hari,
				'fcr' => $fcr,
				'ip' => $ip,
				'adg' => $adg,
				'class_pakan' => $class_pakan,
				'jumlah_panen' => $jumlah_panen["jumlah_akhir"]
		);

		echo json_encode($data);
	}

	function view_lhk($no_reg,$tgl_lhk,$doc_in){
		$this->load->model('riwayat_harian_kandang/m_rhk','rhk');
		$this->load->model('riwayat_harian_kandang/m_rhk_pakan','rhk_pakan');
		$this->load->model('riwayat_harian_kandang/m_rhk_produksi','rhk_produksi');
		$this->load->model('riwayat_harian_kandang/m_rhk_vaksin','rhk_vaksin');
		$this->load->model('riwayat_harian_kandang/m_rhk_pindah','rhk_pindah');
		$this->load->model('riwayat_harian_kandang/m_riwayat_harian_kandang','mrhk');

		$batas_pakan = $this->mrhk->get_batas_pakai_pakan($no_reg,$tgl_lhk);

		$where = array('no_reg'=> $no_reg,'tgl_transaksi'=> $tgl_lhk);
		$rhk = $this->rhk->get_by($where); /* ini pasti satu baris */
		$rhk_pakan = $this->rhk_pakan->get_many_by($where);
		$rhk_produksi = $this->rhk_produksi->get_by($where);
		$rhk_vaksin = $this->rhk_vaksin->get_many_by($where);
		$rhk_pindah = $this->rhk_pindah->get_many_by($where);
		$umur = dateDifference($doc_in,$tgl_lhk);
		$umur_minggu = $umur/7;
		$umur_hari = $umur%7;

		/* periksa apakah pemakaian pakan melebihi batas atau tidak */
		$class_pakan = array('B' => '', 'J'=> '');
		$total_pakan = array('B' => 0, 'J' => 0);
		foreach($rhk_pakan as $rp){
			$total_pakan[$rp->JENIS_KELAMIN] += $rp->BRT_PAKAI ;
		}
		foreach($batas_pakan as $bp){
			$arr = array($bp['jml_performance'],$bp['detail_order']);
			$class_pakan[$bp['jenis_kelamin']] = $total_pakan[$bp['jenis_kelamin']] - max($arr) > 0 ? 'bg_orange' : '';
		}

		$data = array(
				'rhk' => $rhk,
				'rhk_pakan' => $rhk_pakan,
				'rhk_produksi' => $rhk_produksi,
				'rhk_vaksin' => $rhk_vaksin,
				'rhk_pindah' => $rhk_pindah,
				'tgl_doc_in' => $doc_in,
				'tgl_lhk' => $tgl_lhk,
				'umur' => (int) $umur_minggu.' + '.$umur_hari,
				'class_pakan' => $class_pakan
		);

		$this->load->view('riwayat_harian_kandang/view_lhk',$data);
	}

	function get_target_bb(){
		$umur = $this->input->post("umur");
		$noreg = $this->input->post("no_reg");

		$result = $this->m_riwayat->get_target_bb($umur, $noreg);

		echo json_encode($result);
	}

	function cek_panen_exist(){
		$noreg = $this->input->post("no_reg");

		$result = $this->m_riwayat->check_panen_exist($noreg);

		if($result["n_panen"] > 0){
			echo json_encode(array("result"=>"success"));
		}else{
			echo json_encode(array("result"=>"failed"));
		}
	}

	/**
	*TEST UPLOAD DATA
	*/
	function test_load(){
		$dbSqlServer = $this->load->database("default", true);

		$sql = <<<QUERY
				select KURANG_ATTACHMENT, KURANG_ATTACHMENT_FORMAT from test_lhk
QUERY;
			$stmt  = $dbSqlServer->conn_id->prepare($sql);
			$stmt->bindColumn(2, $type, PDO::PARAM_STR, 256);
			if($stmt->execute()){
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

				header('Content-type: text/plain');
				echo $result[1]["KURANG_ATTACHMENT"];
			}
	}

	function tambahLain2(){
		$file_element_add = 'uploadFileTambahLain';
		$file_element_sub = 'lampiran';

		$files_add_fix = array();

		$no_reg       	= $this->input->post('no_reg');
		$tgl_transaksi  = $this->input->post('tgl_transaksi');
		$tambah_j       = $this->input->post('tambah_lain_jml_j');
		$tambah_b       = $this->input->post('tambah_lain_jml_b');
		$tambah_ket     = $this->input->post('tambah_lain_ket');
		$tambah_nomemo  = $this->input->post('tambah_lain_nomemo');
		$tambah_isimemo = array();
		$tambah_format = array();


		foreach($_FILES[$file_element_add] as $key=>$val){
			$i = 1;
			foreach($val as $v){
				$field_name = "file_".$i;
				$_FILES[$field_name][$key] = $v;
				$i++;
			}
		}

		unset($_FILES[$file_element_add]);
		foreach($_FILES as $field_name => $file){
			$attachment = $this->mssql_escape(file_get_contents($_FILES[$field_name]['tmp_name']));
			$tambah_isimemo[] = $attachment;
			$tambah_format[] = $_FILES[$field_name]['type'];
		}

		if(count($tambah_j) > 0 or count($tambah_b) > 0){
			$result = $this->m_riwayat->simpanLain2($no_reg, $tgl_transaksi, 'TAMBAH', $tambah_j[0], $tambah_b[0], $tambah_ket[0], $tambah_nomemo[0], $tambah_isimemo[0], $tambah_format[0]);

			if($result){
				echo json_encode(array("msg"=>"Penambahan Lain-lain SUKSES"));
			}else{
				echo json_encode(array("msg"=>"Penambahan Lain-lain GAGAL"));
			}

		}

	}

	function kurangLain2(){
		$file_element_add = 'uploadFileKurangLain';
		$file_element_sub = 'lampiran';

		$files_add_fix = array();

		$no_reg       	= $this->input->post('no_reg');
		$tgl_transaksi  = $this->input->post('tgl_transaksi');
		$kurang_j       = $this->input->post('kurang_lain_jml_j');
		$kurang_b       = $this->input->post('kurang_lain_jml_b');
		$kurang_ket     = $this->input->post('kurang_lain_ket');
		$kurang_nomemo  = $this->input->post('kurang_lain_nomemo');
		$kurang_isimemo = array();
		$kurang_format = array();


		foreach($_FILES[$file_element_add] as $key=>$val){
			$i = 1;
			foreach($val as $v){
				$field_name = "file_".$i;
				$_FILES[$field_name][$key] = $v;
				$i++;
			}
		}

		unset($_FILES[$file_element_add]);
		foreach($_FILES as $field_name => $file){
			$attachment = $this->mssql_escape(file_get_contents($_FILES[$field_name]['tmp_name']));
			$kurang_isimemo[] = $attachment;
			$kurang_format[] = $_FILES[$field_name]['type'];
		}

		if(count($kurang_j) > 0 or count($kurang_b) > 0){
			$result = $this->m_riwayat->simpanLain2($no_reg, $tgl_transaksi, 'KURANG', $kurang_j[0], $kurang_b[0], $kurang_ket[0], $kurang_nomemo[0], $kurang_isimemo[0], $kurang_format[0]);

			if($result){
				echo json_encode(array("msg"=>"Pengurangan Lain-lain SUKSES"));
			}else{
				echo json_encode(array("msg"=>"Pengurangan Lain-lain GAGAL"));
			}
		}

	}

	function dateDiff($d1,$d2){
		$date1=strtotime($d1);
		$date2=strtotime($d2);
		$seconds = $date1 - $date2;
		$weeks = floor($seconds/604800);
		$seconds -= $weeks * 604800;
		$days = floor($seconds/86400);
		$seconds -= $days * 86400;
		$hours = floor($seconds/3600);
		$seconds -= $hours * 3600;
		$minutes = floor($seconds/60);
		$seconds -= $minutes * 60;
		$months=round(($date1-$date2) / 60 / 60 / 24 / 30);
		$years=round(($date1-$date2) /(60*60*24*365));
		$diffArr=array("Seconds"=>$seconds,
					  "minutes"=>$minutes,
					  "Hours"=>$hours,
					  "Days"=>$days,
					  "Weeks"=>$weeks,
					  "Months"=>$months,
					  "Years"=>$years
					 ) ;
	   return $diffArr;
	}

	function mssql_escape($data) {
        if(is_numeric($data))
          return $data;
        $unpacked = unpack('H*hex', $data);
        return '0x' . $unpacked['hex'];
    }

	function get_berat_pakan(){
		$stok = $this->input->post("stok");
		$no_reg = $this->input->post("no_reg");
		$kode_barang = $this->input->post("kode_barang");

		$items = $this->m_riwayat->get_kompensasi_stok($stok, $no_reg, $kode_barang);

		$total_berat = 0;
		$total_stok = 0;

		for($i=0;$i<count($items);$i++){
			$total_berat += $items[$i]["kg_out"];
		}
		
		echo json_encode(array("berat"=>$total_berat));
	}
}
