<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Riwayat_harian_kandang extends MX_Controller{

	function __construct(){
		parent::__construct();

		$this->load->model("M_riwayat_harian_kandang", "m_riwayat");
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

		$populasi = $this->m_riwayat->get_jumlah_bj_last_lhk($no_reg, $tgl_lhk, $tgl_doc_in);
		$pakan = $this->m_riwayat->get_pakan_last_lhk($no_reg);
		$batas_pakai_pakan = $this->m_riwayat->get_batas_pakai_pakan($no_reg, $tgl_lhk);
		// $pakan = $this->m_riwayat->get_pakan_last_lhk_dummy();

		$batas_pakai = array();
		if(is_array($batas_pakai_pakan) and count($batas_pakai_pakan) > 1){
			$j_batas = 0;
			$b_batas = 0;
			foreach($batas_pakai_pakan as $ba){
				if($ba["jenis_kelamin"] == 'B'){
					$b_batas = ($ba["jml_performance"] > $ba["detail_order"]) ? $ba["jml_performance"] : $ba["detail_order"];
				}else{
					$j_batas = ($ba["jml_performance"] > $ba["detail_order"]) ? $ba["jml_performance"] : $ba["detail_order"];
				}
			}

			$batas_pakai = array("B"=>$b_batas, "J"=>$j_batas);
		}

		$data = array(
			"populasi" => $populasi,
			"pakan" => $pakan,
			"batas_pakai_pakan" => $batas_pakai
		);

		echo json_encode($data);
	}

	function get_obat_vaksin(){
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"]);

		$obat = $this->m_riwayat->get_obat();
		$vaksin = $this->m_riwayat->get_vaksin();

		$data = array(
			"date_now"=>$date,
			"obat" => $obat,
			"vaksin" => $vaksin
		);

		echo json_encode($data);
	}

	function simpan_lhk(){
		$fulldate = $this->m_riwayat->get_today();

		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$kode_kandang = ($this->input->post("kode_kandang")) ? $this->input->post("kode_kandang") : null;
		$noreg = ($this->input->post("noreg")) ? $this->input->post("noreg") : null;
		$tgl_lhk = ($this->input->post("tgl_lhk")) ? $this->input->post("tgl_lhk") : null;
		$umur = ($this->input->post("umur")) ? $this->input->post("umur") : 0;
		$bb_jantan = ($this->input->post("bb_jantan")) ? $this->input->post("bb_jantan") : null;
		$bb_betina = ($this->input->post("bb_betina")) ? $this->input->post("bb_betina") : null;
		$tutup_siklus = ($this->input->post("tutup_siklus") and $this->input->post("tutup_siklus") == 'Y') ? $noreg : null;

		$populasi_awal_jantan = ($this->input->post("populasi_awal_jantan")) ? $this->input->post("populasi_awal_jantan") : 0;
		$tambah_jantan = ($this->input->post("tambah_jantan")) ? $this->input->post("tambah_jantan") : 0;
		$tambah_jantanLain = ($this->input->post("tambah_jantanLain")) ? $this->input->post("tambah_jantanLain") : 0;
		$kurang_jantanMati = ($this->input->post("kurang_jantanMati")) ? $this->input->post("kurang_jantanMati") : 0;
		$kurang_jantanAfkir = ($this->input->post("kurang_jantanAfkir")) ? $this->input->post("kurang_jantanAfkir") : 0;
		$kurang_jantanPindah = ($this->input->post("kurang_jantanPindah")) ? $this->input->post("kurang_jantanPindah") : 0;
		$kurang_jantanSexslip = ($this->input->post("kurang_jantanSexslip")) ? $this->input->post("kurang_jantanSexslip") : 0;
		$kurang_jantanKanibal = ($this->input->post("kurang_jantanKanibal")) ? $this->input->post("kurang_jantanKanibal") : 0;
		$kurang_jantanCampur = ($this->input->post("kurang_jantanCampur")) ? $this->input->post("kurang_jantanCampur") : 0;
		$kurang_jantanSeleksi = ($this->input->post("kurang_jantanSeleksi")) ? $this->input->post("kurang_jantanSeleksi") : 0;
		$kurang_jantanLain = ($this->input->post("kurang_jantanLain")) ? $this->input->post("kurang_jantanLain") : 0;
		$populasi_akhir_jantan = ($this->input->post("populasi_akhir_jantan")) ? $this->input->post("populasi_akhir_jantan") : 0;

		$populasi_awal_betina = ($this->input->post("populasi_awal_betina")) ? $this->input->post("populasi_awal_betina") : 0;
		$tambah_betina = ($this->input->post("tambah_betina")) ? $this->input->post("tambah_betina") : 0;
		$tambah_betinaLain = ($this->input->post("tambah_betinaLain")) ? $this->input->post("tambah_betinaLain") : 0;
		$kurang_betinaMati = ($this->input->post("kurang_betinaMati")) ? $this->input->post("kurang_betinaMati") : 0;
		$kurang_betinaAfkir = ($this->input->post("kurang_betinaAfkir")) ? $this->input->post("kurang_betinaAfkir") : 0;
		$kurang_betinaPindah = ($this->input->post("kurang_betinaPindah")) ? $this->input->post("kurang_betinaPindah") : 0;
		$kurang_betinaSexslip = ($this->input->post("kurang_betinaSexslip")) ? $this->input->post("kurang_betinaSexslip") : 0;
		$kurang_betinaKanibal = ($this->input->post("kurang_betinaKanibal")) ? $this->input->post("kurang_betinaKanibal") : 0;
		$kurang_betinaCampur = ($this->input->post("kurang_betinaCampur")) ? $this->input->post("kurang_betinaCampur") : 0;
		$kurang_betinaSeleksi = ($this->input->post("kurang_betinaSeleksi")) ? $this->input->post("kurang_betinaSeleksi") : 0;
		$kurang_betinaLain = ($this->input->post("kurang_betinaLain")) ? $this->input->post("kurang_betinaLain") : 0;
		$populasi_akhir_betina = ($this->input->post("populasi_akhir_betina")) ? $this->input->post("populasi_akhir_betina") : 0;

		$pindah_kandang = ($this->input->post("pindah_kandang")) ? $this->input->post("pindah_kandang") : 0;
		$pindah_jantan = ($this->input->post("pindah_jantan")) ? $this->input->post("pindah_jantan") : 0;
		$pindah_betina = ($this->input->post("pindah_betina")) ? $this->input->post("pindah_betina") : 0;
		$pindah_keterangan = ($this->input->post("pindah_keterangan")) ? $this->input->post("pindah_keterangan") : 0;
		$pindah_ba = ($this->input->post("pindah_ba")) ? $this->input->post("pindah_ba") : 0;

		$pro_baik = ($this->input->post("pro_baik")) ? $this->input->post("pro_baik") : null;
		$pro_besar = ($this->input->post("pro_besar")) ? $this->input->post("pro_besar") : null;
		$pro_tipis = ($this->input->post("pro_tipis")) ? $this->input->post("pro_tipis") : null;
		$pro_kecil = ($this->input->post("pro_kecil")) ? $this->input->post("pro_kecil") : null;
		$pro_kotor = ($this->input->post("pro_kotor")) ? $this->input->post("pro_kotor") : null;
		$pro_abnormal = ($this->input->post("pro_abnormal")) ? $this->input->post("pro_abnormal") : null;
		$pro_ib = ($this->input->post("pro_ib")) ? $this->input->post("pro_ib") : null;
		$pro_retak = ($this->input->post("pro_retak")) ? $this->input->post("pro_retak") : null;
		$pro_hancur = ($this->input->post("pro_hancur")) ? $this->input->post("pro_hancur") : null;
		$pro_jumlah = ($this->input->post("pro_jumlah")) ? $this->input->post("pro_jumlah") : null;
		$pro_keterangan = ($this->input->post("pro_keterangan")) ? $this->input->post("pro_keterangan") : null;

		$berat_telur = ($this->input->post("berat_telur")) ? $this->input->post("berat_telur") : 0;
		$cv_jantan = ($this->input->post("cv_jantan")) ? $this->input->post("cv_jantan") : 0;
		$cv_betina = ($this->input->post("cv_betina")) ? $this->input->post("cv_betina") : 0;
		$uniformity_jantan = ($this->input->post("uniformity_jantan")) ? $this->input->post("uniformity_jantan") : 0;
		$uniformity_betina = ($this->input->post("uniformity_betina")) ? $this->input->post("uniformity_betina") : 0;

		$doc_in_jantan = ($this->input->post("doc_in_jantan")) ? $this->input->post("doc_in_jantan") : 0;
		$doc_in_betina = ($this->input->post("doc_in_betina")) ? $this->input->post("doc_in_betina") : 0;
		$j_pindah_semu = ($this->input->post("j_pindah_semu")) ? $this->input->post("j_pindah_semu") : 0;
		$j_daya_hidup = ($this->input->post("j_daya_hidup")) ? $this->input->post("j_daya_hidup") : 100;
		$j_jml_pembagi = ($this->input->post("j_jml_pembagi")) ? $this->input->post("j_jml_pembagi") : 0;
		$b_pindah_semu = ($this->input->post("b_pindah_semu")) ? $this->input->post("b_pindah_semu") : 0;
		$b_daya_hidup = ($this->input->post("b_daya_hidup")) ? $this->input->post("b_daya_hidup") : 100;
		$b_jml_pembagi = ($this->input->post("b_jml_pembagi")) ? $this->input->post("b_jml_pembagi") : 0;

		$j_uni_bb = $this->input->post("j_uni_bb");
		$j_uni_jml = $this->input->post("j_uni_jml");
		$b_uni_bb = $this->input->post("b_uni_bb");
		$b_uni_jml = $this->input->post("b_uni_jml");

		$rhk_penimbangan = array();
		for($i=0;$i<count($j_uni_bb);$i++){
			$rhk_penimbangan[] = array(
				"no_reg"=>$noreg,
				"tgl_transaksi"=>$tgl_lhk,
				"berat"=>$j_uni_bb[$i],
				"jumlah"=>$j_uni_jml[$i],
				"jenis_kelamin"=>'J'
			);
		}

		for($i=0;$i<count($b_uni_bb);$i++){
			$rhk_penimbangan[] = array(
				"no_reg"=>$noreg,
				"tgl_transaksi"=>$tgl_lhk,
				"berat"=>$b_uni_bb[$i],
				"jumlah"=>$b_uni_jml[$i],
				"jenis_kelamin"=>'B'
			);
		}

		// echo "<pre>";
		// print_r($rhk_penimbangan);
		// die();

		$pindah_ayam = array();
		$j_pindah_semu_total = 0;
		$b_pindah_semu_total = 0;
		for($i=0;$i<count($pindah_kandang);$i++){
			if(!empty($pindah_kandang[$i])){
				if(!empty($pindah_jantan[$i])){
					$pindah_semu = $pindah_jantan[$i]/($j_daya_hidup/100); //dibagi 100 karena dalam bentuk persen
					$j_pindah_semu_total = $j_pindah_semu_total + $pindah_semu;
					$pindah_ayam[] = array(
						"no_reg"=>$noreg,
						"tgl_transaksi"=>$tgl_lhk,
						"jenis_kelamin"=>'J',
						"no_reg_tujuan"=>$pindah_kandang[$i],
						"jumlah"=>$pindah_jantan[$i],
						"jumlah_semu"=>$pindah_semu,
						"keterangan"=>$pindah_keterangan[$i],
						"no_berita_acara"=>$pindah_ba[$i]
					);
				}

				if(!empty($pindah_betina[$i])){
					$pindah_semu = $pindah_betina[$i]/($b_daya_hidup/100); //dibagi 100 karena dalam bentuk persen
					$b_pindah_semu_total = $b_pindah_semu_total + $pindah_semu;
					$pindah_ayam[] = array(
						"no_reg"=>$noreg,
						"tgl_transaksi"=>$tgl_lhk,
						"jenis_kelamin"=>'B',
						"no_reg_tujuan"=>$pindah_kandang[$i],
						"jumlah"=>$pindah_betina[$i],
						"jumlah_semu"=>$pindah_semu,
						"keterangan"=>$pindah_keterangan[$i],
						"no_berita_acara"=>$pindah_ba[$i]
					);
				}
			}
		}

		$j_jml_pembagi_total = 0;
		$b_jml_pembagi_total = 0;

		//penerima jantan
		if($tambah_jantan > 0){
			$j_jml_pembagi = ($j_jml_pembagi > 0) ? $j_jml_pembagi : $doc_in_jantan;
			$j_jml_pembagi_total = $j_jml_pembagi + $j_pindah_semu;
		}
		//pengirim jantan
		elseif($kurang_jantanPindah > 0){
			$j_jml_pembagi = ($j_jml_pembagi > 0) ? $j_jml_pembagi : $doc_in_jantan;
			$j_jml_pembagi_total = $j_jml_pembagi - $j_pindah_semu_total;
		}
		//tidak kirim dan tidak pindah jantan
		else{
			$j_jml_pembagi_total = ($j_jml_pembagi > 0) ? $j_jml_pembagi : $doc_in_jantan;
		}

		//penerima betina
		if($tambah_betina > 0){
			$b_jml_pembagi = ($b_jml_pembagi > 0) ? $b_jml_pembagi : $doc_in_betina;
			$b_jml_pembagi_total = $b_jml_pembagi + $b_pindah_semu;
		}
		//pengirim betina
		elseif($kurang_betinaPindah > 0){
			$b_jml_pembagi = ($b_jml_pembagi > 0) ? $b_jml_pembagi : $doc_in_betina;
			$b_jml_pembagi_total = $b_jml_pembagi - $b_pindah_semu_total;
		}
		//tidak kirim dan tidak pindah betina
		else{
			$b_jml_pembagi_total = ($b_jml_pembagi > 0) ? $b_jml_pembagi : $doc_in_betina;
		}

		$j_daya_hidup_total = $populasi_akhir_jantan/$j_jml_pembagi_total*100;
		$b_daya_hidup_total = $populasi_akhir_betina/$b_jml_pembagi_total*100;



		$lhk_header = array(
			"no_reg" => $noreg,
			"tgl_transaksi" => $tgl_lhk,
			"b_mati" => $kurang_betinaMati,
			"j_mati" => $kurang_jantanMati,
			"b_afkir" => $kurang_betinaAfkir,
			"j_afkir" => $kurang_jantanAfkir,
			"b_pindah" => $kurang_betinaPindah,
			"j_pindah" => $kurang_jantanPindah,
			"b_sexslip" => $kurang_betinaSexslip,
			"j_sexslip" => $kurang_jantanSexslip,
			"b_seleksi" => $kurang_betinaSeleksi,
			"j_seleksi" => $kurang_jantanSeleksi,
			"b_lain2" => $kurang_betinaLain,
			"j_lain2" => $kurang_jantanLain,
			"b_terima" => $tambah_betina,
			"j_terima" => $tambah_jantan,
			"b_terima_lain" => $tambah_betinaLain,
			"j_terima_lain" => $tambah_jantanLain,
			"b_kanibal" => $kurang_betinaKanibal,
			"j_kanibal" => $kurang_jantanKanibal,
			"b_campur" => $kurang_betinaCampur,
			"j_campur" => $kurang_jantanCampur,
			"b_berat_badan" => $bb_betina,
			"j_berat_badan" => $bb_jantan,
			"b_jumlah" => $populasi_akhir_betina,
			"j_jumlah" => $populasi_akhir_jantan,
			"user_buat" => $this->session->userdata('kode_user'),
			"berat_telur" => $berat_telur,
			"b_uniformity" => $uniformity_betina,
			"j_uniformity" => $uniformity_jantan,
			"b_daya_hidup"=> $b_daya_hidup_total,
			"j_daya_hidup"=> $j_daya_hidup_total,
			"b_jumlah_pembagi"=> $b_jml_pembagi_total,
			"j_jumlah_pembagi"=> $j_jml_pembagi_total,
			"b_cv"=> $cv_betina,
			"j_cv"=> $cv_jantan
		);

		$j_pakan = ($this->input->post("j_pakan")) ? $this->input->post("j_pakan") : null;
		$j_stokAwalKg = ($this->input->post("j_stokAwalKg")) ? $this->input->post("j_stokAwalKg") : null;
		$j_stokAwalSak = ($this->input->post("j_stokAwalSak")) ? $this->input->post("j_stokAwalSak") : null;
		$j_kirimKg = ($this->input->post("j_kirimKg")) ? $this->input->post("j_kirimKg") : null;
		$j_kirimSak = ($this->input->post("j_kirimSak")) ? $this->input->post("j_kirimSak") : null;
		$j_terpakaiKg = ($this->input->post("j_terpakaiKg")) ? $this->input->post("j_terpakaiKg") : null;
		$j_terpakaiSak = ($this->input->post("j_terpakaiSak")) ? $this->input->post("j_terpakaiSak") : null;
		$j_stokAkhirKg = ($this->input->post("j_stokAkhirKg")) ? $this->input->post("j_stokAkhirKg") : null;
		$j_stokAkhirSak = ($this->input->post("j_stokAkhirSak")) ? $this->input->post("j_stokAkhirSak") : null;

		$b_pakan = ($this->input->post("b_pakan")) ? $this->input->post("b_pakan") : null;
		$b_stokAwalKg = ($this->input->post("b_stokAwalKg")) ? $this->input->post("b_stokAwalKg") : null;
		$b_stokAwalSak = ($this->input->post("b_stokAwalSak")) ? $this->input->post("b_stokAwalSak") : null;
		$b_kirimKg = ($this->input->post("b_kirimKg")) ? $this->input->post("b_kirimKg") : null;
		$b_kirimSak = ($this->input->post("b_kirimSak")) ? $this->input->post("b_kirimSak") : null;
		$b_terpakaiKg = ($this->input->post("b_terpakaiKg")) ? $this->input->post("b_terpakaiKg") : null;
		$b_terpakaiSak = ($this->input->post("b_terpakaiSak")) ? $this->input->post("b_terpakaiSak") : null;
		$b_stokAkhirKg = ($this->input->post("b_stokAkhirKg")) ? $this->input->post("b_stokAkhirKg") : null;
		$b_stokAkhirSak = ($this->input->post("b_stokAkhirSak")) ? $this->input->post("b_stokAkhirSak") : null;

		$kandang_movement = array();
		$kandang_movement_d = array();
		$lhk_pakan = array();
		for($i=0;$i<count($j_pakan);$i++){
			$kandang_movement[] = array(
				"no_reg"=>$noreg,
				"kode_barang"=>$j_pakan[$i],
				"jenis_kelamin"=>'J',
				"jml_stok"=>$j_stokAkhirSak[$i],
				"berat_stok"=>$j_stokAkhirKg[$i]
			);

			$kandang_movement_d[] = array(
				"no_reg"=>$noreg,
				"kode_barang"=>$j_pakan[$i],
				"tgl_transaksi"=>$tgl_lhk,
				"keterangan1"=>'LHK',
				"keterangan2"=>$noreg,
				"jenis_kelamin"=>'J',
				"jml_awal"=>$j_stokAkhirSak[$i] - ($j_terpakaiSak[$i] * -1),
				"jml_order"=>($j_terpakaiSak[$i] * -1),
				"jml_akhir"=>$j_stokAkhirSak[$i],
				"berat_awal"=>$j_stokAkhirKg[$i] - ($j_terpakaiKg[$i] * -1),
				"berat_order"=>($j_terpakaiKg[$i] * -1),
				"berat_akhir"=>$j_stokAkhirKg[$i],
				"user_buat"=>$this->session->userdata('kode_user')
			);

			$lhk_pakan[] = array(
				"no_reg"=>$noreg,
				"tgl_transaksi"=>$tgl_lhk,
				"kode_barang"=>$j_pakan[$i],
				"jenis_kelamin"=>'J',
				"jml_terima"=>$j_kirimSak[$i],
				//"jml_pakai"=>($j_terpakaiSak[$i] * -1),
				"jml_pakai"=>($j_terpakaiSak[$i] * 1),
				"jml_akhir"=>$j_stokAkhirSak[$i],
				"brt_terima"=>$j_kirimKg[$i],
				//"brt_pakai"=>($j_terpakaiKg[$i] * -1),
				"brt_pakai"=>($j_terpakaiKg[$i] * 1),
				"brt_akhir"=>$j_stokAkhirKg[$i]
			);
		}

		for($i=0;$i<count($b_pakan);$i++){
			$kandang_movement[] = array(
				"no_reg"=>$noreg,
				"kode_barang"=>$b_pakan[$i],
				"jenis_kelamin"=>'B',
				"jml_stok"=>$b_stokAkhirSak[$i],
				"berat_stok"=>$b_stokAkhirKg[$i]
			);

			$kandang_movement_d[] = array(
				"no_reg"=>$noreg,
				"kode_barang"=>$b_pakan[$i],
				"tgl_transaksi"=>$tgl_lhk,
				"jenis_kelamin"=>'B',
				"keterangan1"=>'LHK',
				"keterangan2"=>$noreg,
				"jml_awal"=>$b_stokAkhirSak[$i] - ($b_terpakaiSak[$i] * -1),
				"jml_order"=>($b_terpakaiSak[$i] * -1),
				"jml_akhir"=>$b_stokAkhirSak[$i],
				"berat_awal"=>$b_stokAkhirKg[$i] - ($b_terpakaiKg[$i] * -1),
				"berat_order"=>($b_terpakaiKg[$i] * -1),
				"berat_akhir"=>$b_stokAkhirKg[$i],
				"user_buat"=>$this->session->userdata('kode_user')
			);

			$lhk_pakan[] = array(
				"no_reg"=>$noreg,
				"tgl_transaksi"=>$tgl_lhk,
				"kode_barang"=>$b_pakan[$i],
				"jenis_kelamin"=>'B',
				"jml_terima"=>$b_kirimSak[$i],
				//"jml_pakai"=>($b_terpakaiSak[$i] * -1),
				"jml_pakai"=>($b_terpakaiSak[$i] * 1),
				"jml_akhir"=>$b_stokAkhirSak[$i],
				"brt_terima"=>$b_kirimKg[$i],
				// "brt_pakai"=>($b_terpakaiKg[$i] * -1),
				"brt_pakai"=>($b_terpakaiKg[$i] * 1),
				"brt_akhir"=>$b_stokAkhirKg[$i]
			);
		}

		$obat_kodebarang = ($this->input->post("obat_kodebarang")) ? $this->input->post("obat_kodebarang") : null;
		$obat_pakaijantan = ($this->input->post("obat_pakaijantan")) ? $this->input->post("obat_pakaijantan") : null;
		$obat_pakaibetina = ($this->input->post("obat_pakaibetina")) ? $this->input->post("obat_pakaibetina") : null;
		$obat_keterangan = ($this->input->post("obat_keterangan")) ? $this->input->post("obat_keterangan") : null;

		$lhk_obat = array();
		for($i=0;$i<count($obat_kodebarang);$i++){
			if(!empty($obat_pakaijantan[$i])){
				$lhk_obat[] = array(
					"no_reg" =>$noreg,
					"tgl_transaksi" => $tgl_lhk,
					"kode_barang" => $obat_kodebarang[$i],
					"jenis_kelamin" => 'J',
					"berat_pakai" => $obat_pakaijantan[$i]
				);
			}

			if(!empty($obat_pakaibetina[$i])){
				$lhk_obat[] = array(
					"no_reg" => $noreg,
					"tgl_transaksi" => $tgl_lhk,
					"kode_barang" => $obat_kodebarang[$i],
					"jenis_kelamin" =>'B',
					"berat_pakai" => $obat_pakaibetina[$i]
				);
			}
		}

		$vaksin_kodebarang = ($this->input->post("vaksin_kodebarang")) ? $this->input->post("vaksin_kodebarang") : null;
		$vaksin_pakaijantan = ($this->input->post("vaksin_pakaijantan")) ? $this->input->post("vaksin_pakaijantan") : null;
		$vaksin_pakaibetina = ($this->input->post("vaksin_pakaibetina")) ? $this->input->post("vaksin_pakaibetina") : null;
		$vaksin_keterangan = ($this->input->post("vaksin_keterangan")) ? $this->input->post("vaksin_keterangan") : null;

		for($i=0;$i<count($vaksin_kodebarang);$i++){
			if(!empty($vaksin_pakaijantan[$i])){
				$lhk_obat[] = array(
					"no_reg" =>$noreg,
					"tgl_transaksi" => $tgl_lhk,
					"kode_barang" => $vaksin_kodebarang[$i],
					"jenis_kelamin" => 'J',
					"berat_pakai" => $vaksin_pakaijantan[$i]
				);
			}

			if(!empty($vaksin_keterangan[$i])){
				$lhk_obat[] = array(
					"no_reg" => $noreg,
					"tgl_transaksi" => $tgl_lhk,
					"kode_barang" => $vaksin_kodebarang[$i],
					"jenis_kelamin" =>'B',
					"berat_pakai" => $vaksin_pakaibetina[$i]
				);
			}
		}

		$lhk_produksi = array();
		for($i=0;$i<count($pro_baik);$i++){
			if($pro_jumlah[$i] > 0){
				$lhk_produksi[] = array(
					"no_reg" => $noreg,
					"tgl_transaksi" => $tgl_lhk,
					"no_urut" => ($i+1),
					"prod_baik" => $pro_baik[$i],
					"prod_besar" => $pro_besar[$i],
					"prod_tipis" => $pro_tipis[$i],
					"prod_kecil" => $pro_kecil[$i],
					"prod_kotor" => $pro_kotor[$i],
					"prod_abnormal" => $pro_abnormal[$i],
					"prod_ib" => $pro_ib[$i],
					"prod_retak" => $pro_retak[$i],
					"prod_hancur" => $pro_hancur[$i],
					"keterangan1" => $pro_keterangan[$i],
					"berat_total" => $pro_jumlah[$i]
				);
			}
		}

		 // $log = array();
		 // $log[] = "LHK HEADER";
		 // foreach ($lhk_header as $key => $value){
			 // $log[] = $key.'->'.$value.'';
		 // }

		 // $log[] = "PINDAH AYAM";
		 // for($i=0;$i<count($pindah_ayam);$i++){
			 // $log[] = $i;
			 // foreach ($pindah_ayam[$i] as $key => $value){
				 // $log[] = '     '.$key.'->'.$value.'';
			 // }
		 // }


		 // $log[] = "KANDANG MOVEMENT";
		 // for($i=0;$i<count($kandang_movement);$i++){
			 // $log[] = $i;
			 // foreach ($kandang_movement[$i] as $key => $value){
				 // $log[] = '     '.$key.'->'.$value.'';
			 // }
		 // }

		 // $log[] = "KANDANG MOVEMENT D";
		 // for($i=0;$i<count($kandang_movement_d);$i++){
			 // $log[] = $i;
			 // foreach ($kandang_movement_d[$i] as $key => $value){
				 // $log[] = '     '.$key.'->'.$value.'';
			 // }
		 // }

		 // $log[] = "LHK OBAT";
		 // for($i=0;$i<count($lhk_obat);$i++){
			 // $log[] = $i;
			 // foreach ($lhk_obat[$i] as $key => $value){
				 // $log[] = '     '.$key.'->'.$value.'';
			 // }
		 // }

		 // $log[] = "LHK PAKAN";
		 // for($i=0;$i<count($lhk_pakan);$i++){
			 // $log[] = $i;
			 // foreach ($lhk_pakan[$i] as $key => $value){
				 // $log[] = '     '.$key.'->'.$value.'';
			 // }
		 // }

		 // $log[] = "LHK PRODUKSI";
		 // for($i=0;$i<count($lhk_produksi);$i++){
			 // $log[] = $i;
			 // foreach ($lhk_produksi[$i] as $key => $value){
				 // $log[] = '     '.$key.'->'.$value.'';
			 // }
		 // }

		 //log_message("error", implode("\n",$log));

		 //echo "<pre>";
		 //print_r($this->input->post());
		 //echo "</pre>";
		 //die();
		$result = $this->m_riwayat->insert_lhk($lhk_header, $pindah_ayam, $kandang_movement, $kandang_movement_d, $lhk_obat, $lhk_pakan, $lhk_produksi, $rhk_penimbangan, $tutup_siklus);

		 if($result){
			if(isset($tutup_siklus)){
				$this->m_riwayat->tutup_siklus($noreg, $kode_farm, $this->session->userdata("kode_user"));
			}
			echo json_encode(array("msg"=>"success", "tutup_siklus"=>$tutup_siklus, "b_daya_hidup"=>$b_daya_hidup_total, "j_daya_hidup"=>$j_daya_hidup_total));
		}else{
			echo json_encode(array("msg"=>"failed"));
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

			echo json_encode(array("result"=>"success"));
		}
		else{
			echo json_encode(array("result"=>"failed"));
		}
	}

	function go_view_lhk(){
		$no_reg = $this->input->post('no_reg');
		$tgl_lhk = $this->input->post('tgl_lhk');
		$doc_in = $this->input->post('tgl_doc_in');

		$this->load->model('riwayat_harian_kandang/m_rhk','rhk');
		$this->load->model('riwayat_harian_kandang/m_rhk_pakan','rhk_pakan');
		$this->load->model('riwayat_harian_kandang/m_rhk_produksi','rhk_produksi');
		$this->load->model('riwayat_harian_kandang/m_rhk_vaksin','rhk_vaksin');
		$this->load->model('riwayat_harian_kandang/m_rhk_pindah','rhk_pindah');
		$this->load->model('riwayat_harian_kandang/m_riwayat_harian_kandang','mrhk');

		$batas_pakan = $this->mrhk->get_batas_pakai_pakan($no_reg,$tgl_lhk);

		$where = array('no_reg'=> $no_reg,'tgl_transaksi'=> $tgl_lhk);
		$rhk = $this->m_riwayat->get_lhk($no_reg, $tgl_lhk); /* ini pasti satu baris */
		$rhk_pakan = $this->m_riwayat->get_lhk_pakan($no_reg, $tgl_lhk);
		$rhk_produksi = $this->m_riwayat->get_lhk_produksi($no_reg, $tgl_lhk);
		$rhk_vaksin = $this->m_riwayat->get_lhk_vaksin_obat($no_reg, $tgl_lhk);
		$rhk_pindah = $this->rhk_pindah->get_many_by($where);
		// $umur = dateDifference($doc_in,$tgl_lhk);
		//$umur = $this->dateDiff($doc_in,$tgl_lhk);
		$date1=date_create($doc_in);
		$date2=date_create($tgl_lhk);
		$diff=date_diff($date1,$date2);
		$umur_minggu = floor($diff->days / 7);
		$umur_hari = $diff->days % 7;

		$rhk_penimbangan = $this->m_riwayat->get_lhk_penimbangan($no_reg, $tgl_lhk, $umur_minggu);

		/* periksa apakah pemakaian pakan melebihi batas atau tidak */
		$class_pakan = array('B' => '', 'J'=> '');
		$total_pakan = array('B' => 0, 'J' => 0);
		foreach($rhk_pakan as $rp){
			$total_pakan[$rp["JENIS_KELAMIN"]] += $rp["BRT_PAKAI"] ;
		}
		foreach($batas_pakan as $bp){
			$arr = array($bp['jml_performance'],$bp['detail_order']);
			$class_pakan[$bp['jenis_kelamin']] = $total_pakan[$bp['jenis_kelamin']] - max($arr) > 0 ? 'style="color:#FF0000;"' : 'style="color:#000;"';
		}

		$data = array(
				'rhk' => $rhk,
				'rhk_penimbangan' => $rhk_penimbangan,
				'rhk_pakan' => $rhk_pakan,
				'rhk_produksi' => $rhk_produksi,
				'rhk_vaksin' => $rhk_vaksin,
				'rhk_pindah' => $rhk_pindah,
				'tgl_doc_in' => $doc_in,
				'tgl_lhk' => $tgl_lhk,
				'umur' => $umur_minggu.' + '.$umur_hari,
				'class_pakan' => $class_pakan
		);
		// echo "<pre>";
		// print_r($data);
		// die();
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
}
