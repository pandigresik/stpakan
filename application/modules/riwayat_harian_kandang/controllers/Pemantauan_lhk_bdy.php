<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Pemantauan_lhk_bdy extends MX_Controller{

	function __construct(){
		parent::__construct();

		$this->load->model("M_riwayat_harian_kandang_bdy", "m_riwayat");
	}

	function index(){
		$kodefarm = $this->session->userdata("kode_farm");
		$kodeuser = $this->session->userdata("kode_user");
		$farms = $this->m_riwayat->get_farm_for_pemantauan($kodeuser);

		$level = $this->session->userdata("level_user");
		$farm = $this->m_riwayat->get_farm($kodefarm);
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"]);

		$data["level"] = $level;
		$data["farms"] = $farms;
		$data["kode_farm"] = $kodefarm;
		$data["nama_farm"] = $farm["nama_farm"];
		$data["today"] = $date[0];

		$this->load->view("pemantauan", $data);
	}

	function get_data(){
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"])[0];

		$q_farm = $this->input->post('q_farm');
		// $q_farm = 'BT1';
		$first_doc_in = $this->m_riwayat->get_min_doc_in($q_farm)["tgl_doc_in"];

		$q_tidak_sesuai_timeline = $this->input->post('q_tidak_sesuai_timeline');
		$q_sesuai_timeline = $this->input->post('q_sesuai_timeline');
		$q_belum_dientry = $this->input->post('q_belum_dientry');
		$q_belum_konfirmasi = $this->input->post('q_belum_konfirmasi');
		$q_sudah_konfirmasi = $this->input->post('q_sudah_konfirmasi');
		// $q_pakan_berlebih = $this->input->post('q_pakan_berlebih');

		$q_tgl_start = $this->input->post('q_tgl_start');
		$q_tgl_end = $this->input->post('q_tgl_end');

		$q_kandang = $this->input->post('q_kandang');
		$q_noreg = $this->input->post('q_noreg');

		$param1 = array();
		$param2 = array();
		$param3 = array();
		$param4 = array();
		$param5 = array();
		$param6 = array();
		$param7 = array();
		$param8 = array();
		$param1_str = "";
		$param2_str = "";

		if($q_tidak_sesuai_timeline > 0){
			$param1[] = "qry.stTemp = 'TIDAK SESUAI TIMELINE'";
		}
		if($q_sesuai_timeline > 0){
			$param1[] = "qry.stTemp = 'SESUAI TIMELINE'";
		}
		if($q_belum_dientry > 0){
			$param1[] = "(qry.stTemp = 'BELUM ENTRY - TELAT' or qry.stTemp = 'BELUM ENTRY')";
		}
		// if($q_pakan_berlebih > 0){
			// $param1[] = "qc.TGL_TRANSAKSI2 is not null";
		// }

		if($q_belum_konfirmasi > 0){
			$param6[] = "ack_kf is null";
			$param6[] = "ack1 is null";
			$param6[] = "ack2 is null";
		}
		if($q_sudah_konfirmasi > 0){
			$param2[] = "ack_kf is not null";
			$param2[] = "ack1 is not null";
			$param2[] = "ack2 is not null";
		}

		if(!empty($q_kandang)){
			$param3[] = " qry.nama_kandang like '%" . $q_kandang . "%' ";
		}

		if(!empty($q_noreg)){
			$param3[] = " qry.noReg like '%" . $q_noreg . "%' ";
		}

		if(count($param1) > 0)
			$param5[] = "(".implode(' or ', $param1).")";
		if(count($param2) > 0)
			$param7[] = "(".implode(' and ', $param2).")";
		if(count($param3) > 0)
			$param5[] = "(".implode(' and ', $param3).")";
		if(count($param4) > 0)
			$param5[] = "(".implode(' and ', $param4).")";
		if(count($param6) > 0)
			$param7[] = "(".implode(' or ', $param6).")";

		if(count($param7) > 0)
			$param8[] = "(".implode(' or ', $param7).")";
		if(count($param5) > 0)
			$param8[] = implode(" and ", $param5);

		$paramfull_str = implode(" and ", $param8);

		$q_tgl_start = (trim($q_tgl_start) == "") ? $first_doc_in : $q_tgl_start;
		$q_tgl_end = (trim($q_tgl_end) == "") ? $date : $q_tgl_end;

		$result = $this->m_riwayat->get_data_lhk($q_tgl_start, $q_tgl_end, $q_farm, $paramfull_str);

		if(isset($result))
			echo json_encode(array("msg"=>"success", "items"=>$result, "tgl_start"=>$first_doc_in, "tgl_end"=>$q_tgl_end, "level_user"=>$this->session->userdata("level_user")));
		else
			echo json_encode(array("msg"=>"failed", "notif"=>"Gagal mengambil data", "level_user"=>$this->session->userdata("level_user")));
	}

	function get_data_lhk(){
		$no_reg = $this->input->post('no_reg');
		$tgl_lhk = $this->input->post('tgl_lhk');
		$doc_in = $this->input->post('tgl_doc_in');

		$batas_pakan = $this->m_riwayat->get_batas_pakai_pakan($no_reg,$tgl_lhk);
		$bb_rata_last = $this->m_riwayat->get_last_bb_rata_pemantauan($no_reg, $tgl_lhk);

		$rhk = $this->m_riwayat->get_lhk($no_reg, $tgl_lhk);
		$rhk_pakan = $this->m_riwayat->get_lhk_pakan($no_reg, $tgl_lhk);
		$jumlah_panen = $this->m_riwayat->get_jumlah_panen($no_reg, $tgl_lhk);
		$date1=date_create($doc_in);
		$date2=date_create($tgl_lhk);
		$diff=date_diff($date1,$date2);
		$umur_hari = ($diff->days*1);

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

		$lhk_old = $this->m_riwayat->get_pastday_timbang_bb($no_reg, $tgl_lhk);

		$pakanKg_terpakai = 0;
		for($j=0;$j<count($rhk_pakan);$j++){
			$pakanKg_terpakai += $rhk_pakan[$j]["BRT_PAKAI"];
		}

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
				'bb_rata_last' => $bb_rata_last["bb_rata_last"],
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

	function get_menu_farm(){
		$kodeuser = $this->session->userdata("kode_user");
		$farms = $this->m_riwayat->get_farm_for_pemantauan($kodeuser);

		echo json_encode(array("items"=>$farms));
	}

	function simpan_ack(){
		$fulldate = $this->m_riwayat->get_today();
		$datetime = $fulldate["today"];

		$noreg = $this->input->post('no_reg');
		$tgl_lhk = $this->input->post('tgl_transaksi');
		$bb_rata = $this->input->post('bb_rata');
		$tgl_entri = $this->input->post('tgl_entri');

		$populasi_awal = $this->input->post('populasi_awal');
		$sekat_no = $this->input->post('sekat_no');
		$sekat_jml = $this->input->post('sekat_jml');
		$sekat_bb = $this->input->post('sekat_bb');
		$sekat_ket = $this->input->post('sekat_ket');

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
		$ack_desc = ($this->input->post('ack_desc')) ? $this->input->post('ack_desc') : null;
		$user_buat = ($this->input->post('user_buat')) ? $this->input->post('user_buat') : null;
		$tgl_buat = ($this->input->post('tgl_buat')) ? $this->input->post('tgl_buat') : null;

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
			"user_buat"=>$user_buat,
			"tgl_buat"=>$tgl_buat,
			"keterangan1"=>$ket_kematian,
			"ack_kf"=>$datetime,
			"ack_desc"=>$ack_desc
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
				"jml_awal"=>$c_stokAkhirSak[$i] - ($c_terpakaiSak[$i] * -1),
				"jml_order"=>($c_terpakaiSak[$i] * -1),
				"jml_akhir"=>$c_stokAkhirSak[$i],
				"berat_awal"=>$c_stokAkhirKg[$i] - ($c_terpakaiKg[$i] * -1),
				"berat_order"=>($c_terpakaiKg[$i] * -1),
				"berat_akhir"=>$c_stokAkhirKg[$i],
				"user_buat"=>$this->session->userdata('kode_user'),
				"tgl_buat" => $tgl_buat
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

		$result = $this->m_riwayat->insert_lhk($lhk_header, $kandang_movement, $kandang_movement_d, $lhk_pakan, $rhk_penimbangan, null, $kompensasi_stok_h, $kompensasi_stok_d);

		// if(!empty($ack_desc) or $ack_desc != ""){
			// $result = $this->m_riwayat->simpan_ack_kf($ack_desc, $noreg, $tgl_transaksi);
		// }else{
			// $result = $this->m_riwayat->simpan_ack_dir($noreg, $tgl_transaksi);
		// }

		if($result){
			$fulldate = $this->m_riwayat->get_today();
			$datetime = $fulldate["today"];

		//	echo json_encode(array("msg"=>"success", "tgl_ack"=>$datetime));
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode(array("msg"=>"success", "tgl_ack"=>$datetime)));
		}else{
		//	echo json_encode(array("msg"=>"failed"));
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode(array("msg"=>"failed")));
		}
	}

	function simpan_kadep(){
		$kodeuser = $this->session->userdata("kode_user");
		$no_reg = $this->input->post('no_reg');
		$tgl_transaksi = $this->input->post('tgl_transaksi');

		$result = $this->m_riwayat->simpan_ack_kadep($no_reg, $tgl_transaksi, $kodeuser);

		if($result){
			$fulldate = $this->m_riwayat->get_today();
			$datetime = $fulldate["today"];

			//	echo json_encode(array("msg"=>"success", "tgl_ack"=>$datetime));
				$this->output
							->set_content_type('application/json')
							->set_output(json_encode(array("msg"=>"success", "tgl_ack"=>$datetime)));
			}else{
			//	echo json_encode(array("msg"=>"failed"));
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode(array("msg"=>"failed")));
			}
	}

	function simpan_kadiv(){
		$kodeuser = $this->session->userdata("kode_user");
		$no_reg = $this->input->post('no_reg');
		$tgl_transaksi = $this->input->post('tgl_transaksi');

		$result = $this->m_riwayat->simpan_ack_kadiv($no_reg, $tgl_transaksi, $kodeuser);

		if($result){
			$fulldate = $this->m_riwayat->get_today();
			$datetime = $fulldate["today"];

			//	echo json_encode(array("msg"=>"success", "tgl_ack"=>$datetime));
				$this->output
							->set_content_type('application/json')
							->set_output(json_encode(array("msg"=>"success", "tgl_ack"=>$datetime)));
			}else{
			//	echo json_encode(array("msg"=>"failed"));
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode(array("msg"=>"failed")));
			}
	}

}
