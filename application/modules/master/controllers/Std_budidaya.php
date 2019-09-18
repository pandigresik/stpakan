<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Std_budidaya extends MX_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model("m_std_budidaya", "m_budidaya");
	}

	function index(){
		$grup_pegawai = $this->session->userdata("level_user");
		$grup_farm = $this->session->userdata("grup_farm");

		$strain = $this->m_budidaya->get_strain();
		$data["strain"] = $strain;

		if($grup_pegawai == "BPM"){
			$data["farm_bdy"] = $this->m_budidaya->get_farm_bdy();
			$this->load->view("std_budidaya/std_budidaya_bdy", $data);
		}else{
			if($grup_farm == "BDY"){
				$data["farm_bdy"] = $this->m_budidaya->get_farm_bdy();
				$this->load->view("std_budidaya/std_budidaya_bdy", $data);
			}else{
				$this->load->view("std_budidaya/std_budidaya", $data);
			}
		}
	}

	function go_to_breeding(){
		$grup_pegawai = $this->session->userdata("level_user");
		$grup_farm = $this->session->userdata("grup_farm");

		$strain = $this->m_budidaya->get_strain();
		$data["strain"] = $strain;

		$this->load->view("std_budidaya/std_budidaya", $data);
	}

	function get_last_std(){
		$kode_strain = ($this->input->post("strain")) ? $this->input->post("strain") : "";
		$jenis_kelamin = ($this->input->post("jenis_kelamin")) ? $this->input->post("jenis_kelamin") : "";
		$tipe_kandang = ($this->input->post("tipe_kandang")) ? $this->input->post("tipe_kandang") : "";
		$m_in = ($this->input->post("m_in")) ? $this->input->post("m_in") : "";
		$m_out = ($this->input->post("m_out")) ? $this->input->post("m_out") : "";

		$std = $this->m_budidaya->get_last_std($kode_strain, $jenis_kelamin, $tipe_kandang, $m_in, $m_out);

		if(count($std) > 0){
			$data = array(
				'Rows' => $std
			);

			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			$data = array(
				'Rows' => 0
			);
			echo json_encode($data);
		}

		exit;
	}

	function get_detail_std(){
		$kode_riwayat = ($this->input->post("kode_riwayat")) ? $this->input->post("kode_riwayat") : "";
		$musim = ($this->input->post("musim")) ? $this->input->post("musim") : "";

		$detail = $this->m_budidaya->get_detail_std($kode_riwayat, $musim);
		$detail_row = $this->m_budidaya->get_detail_std_budidaya($kode_riwayat, $musim);
		$grup_barang = $this->m_budidaya->get_grup_pakan();

		if(count($detail) > 0){
			$data = array(
				'Rows' => $detail,
				'RowsDetail' => $detail_row,
				'GrupBarang' => $grup_barang
			);

			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			$data = array(
				'Rows' => 0,
				'RowsDetail' => 0,
				'GrupBarang' => 0
			);
			echo json_encode($data);
		}

		exit;
	}

	function add_std_budidaya(){
		date_default_timezone_set("Asia/Jakarta");
		$now = date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$year = date("Y", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$user = $this->session->userdata("kode_user");

		$kode_strain = ($this->input->post("kode_strain")) ? $this->input->post("kode_strain") : null;
		$jenis_kelamin = ($this->input->post("jenis_kelamin")) ? $this->input->post("jenis_kelamin") : null;
		$tipe_kandang = ($this->input->post("tipe_kandang")) ? $this->input->post("tipe_kandang") : null;
		$musim = ($this->input->post("musim")) ? $this->input->post("musim") : null;

		$kode_riwayat = ($this->input->post("kode_riwayat")) ? $this->input->post("kode_riwayat") : "";
		$umur_awal = ($this->input->post("umur_awal")) ? $this->input->post("umur_awal") : null;
		$umur_akhir = ($this->input->post("umur_akhir")) ? $this->input->post("umur_akhir") : null;
		$jenis_pakan = ($this->input->post("jenis_pakan")) ? $this->input->post("jenis_pakan") : null;
		$tgl_efektif = ($this->input->post("tgl_efektif")) ? $this->input->post("tgl_efektif") : null;

		if($kode_riwayat != ""){
			$format = $kode_strain.$jenis_kelamin.$tipe_kandang.$year;
			$kode_std_breeding_last = $this->m_budidaya->get_last_std_formated($format);

			if(!empty($kode_std_breeding_last) and isset($kode_std_breeding_last["kode_std_breeding"])){
				//pecah kode_std_breeding untuk hasilkan NEXT Increment kode
				$kd_arr = explode("-", $kode_std_breeding_last["kode_std_breeding"]);
				$last = $kd_arr[1];
				$new = $kd_arr[0] . '-' . str_pad(($last+1), 2, "0", STR_PAD_LEFT);
			}else{
				$new = $format . '-'. '01';
			}

			$data = array();

			for($i=0;$i<count($umur_awal);$i++){
				$jenis_pakan_arr = explode("*", $jenis_pakan[$i]);
				//Ambil data dg key kode_std_breeding && std_umur
				$rows = $this->m_budidaya->get_rows_std($kode_riwayat, $umur_awal[$i], $umur_akhir[$i]);
				foreach($rows as $row){
					$data[] = array(
						"kode_std_breeding"=>$new,
						"kode_strain"=>$row["kode_strain"],
						"musim"=>$row["musim"],
						"tipe_kandang"=>$row["tipe_kandang"],
						"jenis_kelamin"=>$row["jenis_kelamin"],
						"tgl_efektif"=>$tgl_efektif,
						"std_umur"=>$row["std_umur"],
						"mati_prc"=>$row["mati_prc"],
						"afkir_prc"=>$row["afkir_prc"],
						"seleksi_prc"=>$row["seleksi_prc"],
						"dh_prc"=>$row["dh_prc"],
						"target_pkn"=>$row["target_pkn"],
						"energi"=>$row["energi"],
						"total_energi"=>$row["total_energi"],
						"protein"=>$row["protein"],
						"total_protein"=>$row["total_protein"],
						"target_bb"=>$row["target_bb"],
						"bb_prc"=>$row["bb_prc"],
						"kode_barang"=>$jenis_pakan_arr[0],
						"bentuk"=>$jenis_pakan_arr[1],
						"grup_barang"=>$jenis_pakan_arr[2],
						"masa_pertumbuhan"=>$row["masa_pertumbuhan"],
						"tgl_buat"=>$now,
						"user_buat"=>$user
					);
				}
			}

			if(count($data) > 0){
				//Insert to table
				$result = $this->m_budidaya->insert_multiple_std_breeding($data);
				if($result > 0){
					$return["kode_std_breeding"] = $new;
					$return["result"] = "success";
				}else{
					$return["result"] = "failed";
				}
			}
			else
				$return["result"] = "failed";

			echo json_encode($return);
		}
	}

	function add_std_budidaya_total(){
		date_default_timezone_set("Asia/Jakarta");
		$now = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$year = date("Y", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$user = strtoupper("ADMINTEST");

		/*Step
		1.Generate kode_std_breeding sesuai dengan STRAIN, Jenis Kelamin, Tipe Kandang dan Musim
		2.Insert multiple data
		*/

		$kode_strain = ($this->input->post("kode_strain")) ? $this->input->post("kode_strain") : null;
		$jenis_kelamin = ($this->input->post("jenis_kelamin")) ? $this->input->post("jenis_kelamin") : null;
		$tipe_kandang = ($this->input->post("tipe_kandang")) ? $this->input->post("tipe_kandang") : null;
		$in_season = ($this->input->post("in_season") and $this->input->post("in_season")!="") ? $this->input->post("in_season") : null;
		$out_season = ($this->input->post("out_season") and $this->input->post("out_season")!="") ? $this->input->post("out_season") : null;
		$umur_awal = ($this->input->post("umur_awal")) ? $this->input->post("umur_awal") : null;
		$umur_akhir = ($this->input->post("umur_akhir")) ? $this->input->post("umur_akhir") : null;
		$jenis_pakan = ($this->input->post("jenis_pakan")) ? $this->input->post("jenis_pakan") : null;
		$tgl_efektif = ($this->input->post("tgl_efektif")) ? $this->input->post("tgl_efektif") : null;

		$umur = ($this->input->post("col_umur")) ? $this->input->post("col_umur") : null;
		$pengurangan = $this->input->post("col_pengurangan");
		$mati = $this->input->post("col_mati");
		$afkir = $this->input->post("col_afkir");
		$seleksi = $this->input->post("col_seleksi");
		$dayahidup = $this->input->post("col_dayahidup");
		$pakantarget = $this->input->post("col_pakantarget");
		$pakanenergi = $this->input->post("col_pakanenergi");
		$pakancumenergi = $this->input->post("col_pakancumenergi");
		$pakanprotein = $this->input->post("col_pakanprotein");
		$pakancumprotein = $this->input->post("col_pakancumprotein");
		$bbtarget = $this->input->post("col_bbtarget");
		$bbtotal = $this->input->post("col_bbtotal");
		$keterangan = $this->input->post("col_keterangan");

		$musim_arr = array($in_season, $out_season);
		$success = 0;

		for($i=0;$i<count($musim_arr);$i++){
			$musim = $musim_arr[$i];

			if(isset($musim)){
				$masa_pertumbuhan = $this->m_budidaya->get_masa_pertumbuhan($kode_strain);

				$m_pertumbuhan = array();
				foreach($masa_pertumbuhan as $mp){
					for($l=$mp["umur_awal"];$l<=$mp["umur_akhir"];$l++){
						$m_pertumbuhan[$l] = $mp["kode_pertumbuhan"];
					}
				}

				$format = $kode_strain.$jenis_kelamin.$tipe_kandang.$year;

				$kode_std_breeding_last = $this->m_budidaya->get_last_std_formated($format);
				if(!empty($kode_std_breeding_last) and isset($kode_std_breeding_last["kode_std_breeding"])){
					//pecah kode_std_breeding untuk hasilkan NEXT Increment kode
					$kd_arr = explode("-", $kode_std_breeding_last["kode_std_breeding"]);
					$last = $kd_arr[1];
					$new = $kd_arr[0] . '-' . str_pad(($last+1), 2, "0", STR_PAD_LEFT);
				}else{
					$new = $format . '-'. '01';
				}

				$data = array();
				for($j=0;$j<count($umur_awal);$j++){
					$jenis_pakan_arr = explode("*", $jenis_pakan[$j]);

					for($k=$umur_awal[$j];$k<=$umur_akhir[$j];$k++){
						$data[] = array(
							"kode_std_breeding"=>$new,
							"kode_strain"=>$kode_strain,
							"musim"=>$musim,
							"tipe_kandang"=>$tipe_kandang,
							"jenis_kelamin"=>$jenis_kelamin,
							"tgl_efektif"=>$tgl_efektif,
							"std_umur"=>$k,
							"mati_prc"=>$mati[$k],
							"afkir_prc"=>$afkir[$k],
							"seleksi_prc"=>$seleksi[$k],
							"dh_prc"=>$dayahidup[$k],
							"target_pkn"=>$pakantarget[$k],
							"energi"=>$pakanenergi[$k],
							"total_energi"=>$pakancumenergi[$k],
							"protein"=>$pakanprotein[$k],
							"total_protein"=>$pakancumprotein[$k],
							"target_bb"=>$bbtarget[$k],
							"bb_prc"=>$bbtotal[$k],
							"grup_barang"=>$jenis_pakan_arr[2],
							"bentuk"=>$jenis_pakan_arr[1],
							"masa_pertumbuhan"=>$m_pertumbuhan[$k],
							"tgl_buat"=>$now,
							"user_buat"=>$user
						);
					}
				}

				$result = $this->m_budidaya->insert_multiple_std_breeding($data);
				$success = ($result > 0) ? ($success+1) : $success;
			}
		}
		if($success > 0){
			$rs["kode_std_breeding"] = $new;
			$rs["result"] = "success";
		}else{
			$rs["result"] = "failed";
		}

		echo json_encode($rs);

	}

	function update_std_budidaya(){
		$kode_riwayat = ($this->input->post("kode_riwayat")) ? $this->input->post("kode_riwayat") : null;
		$umur = ($this->input->post("col_umur")) ? $this->input->post("col_umur") : null;
		$pengurangan = ($this->input->post("col_pengurangan")) ? $this->input->post("col_pengurangan") : null;
		$mati = ($this->input->post("col_mati")) ? $this->input->post("col_mati") : null;
		$afkir = ($this->input->post("col_afkir")) ? $this->input->post("col_afkir") : null;
		$seleksi = ($this->input->post("col_seleksi")) ? $this->input->post("col_seleksi") : null;
		$dayahidup = ($this->input->post("col_dayahidup")) ? $this->input->post("col_dayahidup") : null;
		$pakantarget = ($this->input->post("col_pakantarget")) ? $this->input->post("col_pakantarget") : null;
		$pakanenergi = ($this->input->post("col_pakanenergi")) ? $this->input->post("col_pakanenergi") : null;
		$pakancumenergi = ($this->input->post("col_pakancumenergi")) ? $this->input->post("col_pakancumenergi") : null;
		$pakanprotein = ($this->input->post("col_pakanprotein")) ? $this->input->post("col_pakanprotein") : null;
		$pakancumprotein = ($this->input->post("col_pakancumprotein")) ? $this->input->post("col_pakancumprotein") : null;
		$bbtarget = ($this->input->post("col_bbtarget")) ? $this->input->post("col_bbtarget") : null;
		$bbtotal = ($this->input->post("col_bbtotal")) ? $this->input->post("col_bbtotal") : null;
		$keterangan = ($this->input->post("col_keterangan")) ? $this->input->post("col_keterangan") : null;

		$detail = array();
		for($i=0;$i<count($umur);$i++){
			$data = array(
				"pengurangan_populasi"=>($pengurangan[$i]!='-' and !empty($pengurangan[$i])) ? $pengurangan[$i] : '0',
				"mati_prc"=>$mati[$i],
				"afkir_prc"=>$afkir[$i],
				"seleksi_prc"=>$seleksi[$i],
				"dh_prc"=>$dayahidup[$i],
				"target_pkn"=>$pakantarget[$i],
				"energi"=>$pakanenergi[$i],
				"total_energi"=>$pakancumenergi[$i],
				"protein"=>$pakanprotein[$i],
				"total_protein"=>$pakancumprotein[$i],
				"target_bb"=>$bbtarget[$i],
				"bb_prc"=>$bbtotal[$i],
				"keterangan"=>$keterangan[$i]
			);

			$detail[] = $data;
		}

		$update = $this->m_budidaya->update_std_breeding($detail, $kode_riwayat, $umur);

		$result = array();
		if($update){
			$result["result"] = "success";
		}else{
			$result["result"] = "failed";
		}

		echo json_encode($result);
	}

	function get_data_kebutuhanpakan(){
		$kode_strain = ($this->input->post("kode_strain")) ? $this->input->post("kode_strain") : null;

		$range_kebutuhan_pakan = $this->m_budidaya->get_range_kebutuhan_pakan($kode_strain);
		$grup_barang = $this->m_budidaya->get_grup_pakan();

		$data = array(
			"kode_strain" => $range_kebutuhan_pakan["kode_strain"],
			"umur_awal" => $range_kebutuhan_pakan["umur_awal"],
			"umur_akhir" => $range_kebutuhan_pakan["umur_akhir"],
			"grup_barang" => $grup_barang
		);

		echo json_encode($data);
	}

	function get_masa_pertumbuhan(){
		$data = array();
		$kode_strain = ($this->input->post("kode_strain")) ? $this->input->post("kode_strain") : null;

		$masa_pertumbuhan = $this->m_budidaya->get_masa_pertumbuhan($kode_strain);

		foreach($masa_pertumbuhan as $mp){
			for($i=$mp["umur_awal"];$i<=$mp["umur_akhir"];$i++){
				$data[] = array(
					"kode_pertumbuhan"=>$mp["kode_pertumbuhan"],
					"deskripsi"=>$mp["deskripsi"],
					"umur"=>$i,
					"umur_awal"=>$mp["umur_awal"],
					"umur_akhir"=>$mp["umur_akhir"]
				);
			}
		}

		echo json_encode($data);
	}

	function cetak_std(){
		$jenis_kelamin = $this->input->get("jenis_kelamin");
		$riwayat_date = $this->input->get("riwayat_date");
		$kode_riwayat = $this->input->get("kode_riwayat");
		$musim = $this->input->get("musim");

		$range = $this->m_budidaya->get_range_detail_std($kode_riwayat, $musim);
		$detail = $this->m_budidaya->get_detail_std($kode_riwayat, $musim);
		$detail_row = $this->m_budidaya->get_detail_std_budidaya($kode_riwayat, $musim);
		$grup_barang = $this->m_budidaya->get_grup_pakan();

		$this->load->library ( 'Pdf' );
		// //Custom size
		$width = 210;
		$height = 297;
		$pagelayout = array($width, $height);
		$pdf = new Pdf ( 'P', PDF_UNIT, $pagelayout, true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );

		$data["range"] = $range;
		$data["detail"] = $detail;
		$data["detail_row"] = $detail_row;
		$data["grup_barang"] = $grup_barang;
		$data["jk"] = $jenis_kelamin;
		$data["tipe"] = $musim;
		$data["tahun"] = $riwayat_date;

		$data["masa_pertumbuhan"] = "GROWER";

		$html = $this->load->view('std_budidaya/print', $data, true );

		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );

		$data["masa_pertumbuhan"] = "LAYER";
		$html2 = $this->load->view('std_budidaya/print', $data, true );
		$pdf->AddPage ();
		$pdf->writeHTML ( $html2, true, false, true, false, '' );

		$pdf->Output ( 'retur_pakan.pdf', 'I' );
	}
}
