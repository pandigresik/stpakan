<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Std_budidaya_bdy extends MX_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model("m_std_budidaya_bdy", "m_budidaya");
	}
	
	function get_last_std(){
		$kode_strain = ($this->input->post("strain")) ? $this->input->post("strain") : "";
		$kode_farm = ($this->input->post("kodefarm")) ? $this->input->post("kodefarm") : "";
		$kode_farm = "'".implode("','", $kode_farm)."'";
		
		$std = $this->m_budidaya->get_last_std($kode_strain, $kode_farm);

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
	
	function get_data_kebutuhanpakan(){
		$kode_strain = ($this->input->post("kode_strain")) ? $this->input->post("kode_strain") : null;
		
		$grup_barang = $this->m_budidaya->get_grup_pakan();
		
		$data = array(
			"grup_barang" => $grup_barang
		);
		
		echo json_encode($data);
	}
	
	function get_detail_std(){
		$kode_riwayat = ($this->input->post("kode_riwayat")) ? $this->input->post("kode_riwayat") : "";
		
		$head = $this->m_budidaya->get_head_std($kode_riwayat);
		$detail = $this->m_budidaya->get_detail_std($kode_riwayat);
		$detail_row = $this->m_budidaya->get_detail_std_budidaya($kode_riwayat);
		$grup_barang = $this->m_budidaya->get_grup_pakan();
		
		if(count($detail) > 0){
			$data = array(
				'Head' => $head,
				'Rows' => $detail,
				'RowsDetail' => $detail_row,
				'GrupBarang' => $grup_barang
			);
		
			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			$data = array(
				'Head' => 0,
				'Rows' => 0,
				'RowsDetail' => 0,
				'GrupBarang' => 0
			);
			echo json_encode($data);
		}
		
		exit;
	}
	
	function get_std_pakan(){
		
	}
	
	function add_std_budidaya(){
		date_default_timezone_set("Asia/Jakarta"); 
		$now = date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$year = date("Y", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$user = $this->session->userdata("kode_user");
		
		$kode_strain = ($this->input->post("kode_strain")) ? $this->input->post("kode_strain") : null;
		
		$bp_daya_hidup = ($this->input->post("bp_daya_hidup")) ? $this->input->post("bp_daya_hidup") : null;
		$bp_berat_hidup = ($this->input->post("bp_berat_hidup")) ? $this->input->post("bp_berat_hidup") : null;
		$bp_fcr = ($this->input->post("bp_fcr")) ? $this->input->post("bp_fcr") : null;
		$bp_umur_panen = ($this->input->post("bp_umur_panen")) ? $this->input->post("bp_umur_panen") : null;
		$bp_ip = ($this->input->post("bp_ip")) ? $this->input->post("bp_ip") : null;
		$bp_kum = ($this->input->post("bp_kum")) ? $this->input->post("bp_kum") : null;
		
		$kode_riwayat = ($this->input->post("kode_riwayat")) ? $this->input->post("kode_riwayat") : "";
		$umur_awal = ($this->input->post("umur_awal")) ? $this->input->post("umur_awal") : null;
		$umur_akhir = ($this->input->post("umur_akhir")) ? $this->input->post("umur_akhir") : null;
		$jenis_pakan = ($this->input->post("jenis_pakan")) ? $this->input->post("jenis_pakan") : null;
		$tgl_efektif = ($this->input->post("tgl_efektif")) ? $this->input->post("tgl_efektif") : null;
		
		if($kode_riwayat != ""){
			$format_arr = explode("-", $kode_riwayat);
			$format = $format_arr[0];
			$kode_std_budidaya_last = $this->m_budidaya->get_last_std_formated($format);
			
			$format_arr = explode("/", $format_arr[0]);
			
			if(!empty($kode_std_budidaya_last) and isset($kode_std_budidaya_last["kode_std_budidaya"])){
				//pecah kode_std_breeding untuk hasilkan NEXT Increment kode
				$kd_arr = explode("-", $kode_std_budidaya_last["kode_std_budidaya"]);
				$last = $kd_arr[1];
				$new = $kd_arr[0] . '-' . ($last+1);
			}else{
				$new = $format . '-'. '1'; 
			}
			
			$data = array();
			
			for($i=0;$i<count($umur_awal);$i++){
				$jenis_pakan_arr = explode("*", $jenis_pakan[$i]);
				//Ambil data dg key kode_std_breeding && std_umur
				$rows = $this->m_budidaya->get_rows_std($kode_riwayat, $umur_awal[$i], $umur_akhir[$i]);
				foreach($rows as $row){
					$data[] = array(
						"kode_std_budidaya"=>$new,
						"std_umur"=>$row["std_umur"], 
						"dh_kum_prc"=>$row["dh_kum_prc"], 
						"dh_hr_prc"=>$row["dh_hr_prc"], 
						"pkn_kum_std"=>$row["pkn_kum_std"], 
						"pkn_hr_std"=>$row["pkn_hr_std"],	
						"pkn_kum"=>$row["pkn_kum"], 
						"pkn_hr"=>$row["pkn_hr"], 
						"target_bb"=>$row["target_bb"], 
						"kode_barang"=>$row["kode_barang"]
					);
				}
			}
			
			if(count($data) > 0){
				//Insert to table
				$data_h["kode_std_budidaya"] = $new;
				$data_h["kode_farm"] = $format_arr[1];
				$data_h["kode_strain"] = $kode_strain;
				$data_h["tgl_efektif"] = $tgl_efektif;
				$data_h["target_dh_prc"] = $bp_daya_hidup;
				$data_h["target_bb_prc"] = $bp_berat_hidup;
				$data_h["target_fcr_prc"] = $bp_fcr;
				$data_h["target_umur_panen"] = $bp_umur_panen;
				$data_h["target_ip"] = $bp_ip;
				$data_h["target_kum"] = $bp_kum;
				$data_h["tgl_buat"] = $now;
				$data_h["user_buat"] = $user;
				
				$result = $this->m_budidaya->insert_multiple_std_budidaya($data_h, $data);
				if($result > 0){
					$return["kode_std_budidaya"] = $new;
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

	function simpan_std_budidaya(){
		date_default_timezone_set("Asia/Jakarta"); 
		$now 			= date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$year 			= date("Y", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$user 			= $this->session->userdata("kode_user");
		
		$kode_riwayat 	= ($this->input->post("kode_riwayat") and $this->input->post("kode_riwayat") != '') ? $this->input->post("kode_riwayat") : "";
		$kode_strain 	= ($this->input->post("kode_strain")) ? $this->input->post("kode_strain") : null;
		$kode_farm 		= ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		
		$bp_daya_hidup	= ($this->input->post("budget_daya_hidup")) ? $this->input->post("budget_daya_hidup") : null;
		$bp_berat_hidup = ($this->input->post("budget_berat_hidup")) ? $this->input->post("budget_berat_hidup") : null;
		$bp_fcr 		= ($this->input->post("budget_fcr")) ? $this->input->post("budget_fcr") : null;
		$bp_umur_panen 	= ($this->input->post("budget_umur_panen")) ? $this->input->post("budget_umur_panen") : null;
		$bp_ip 			= ($this->input->post("budget_ip")) ? $this->input->post("budget_ip") : null;
		$bp_kum 		= ($this->input->post("budget_kum")) ? $this->input->post("budget_kum") : null;
				
		$umur_awal 		= ($this->input->post("umur_awal")) ? $this->input->post("umur_awal") : null;
		$umur_akhir 	= ($this->input->post("umur_akhir")) ? $this->input->post("umur_akhir") : null;
		$jenis_pakan 	= ($this->input->post("jenis_pakan")) ? $this->input->post("jenis_pakan") : null;
		$tgl_efektif 	= ($this->input->post("tgl_efektif")) ? $this->input->post("tgl_efektif") : null;
		
		$col_umur 		= $this->input->post("col_umur");
		$col_dh_kum 	= $this->input->post("dh_kum");
		$col_dh_hr 		= $this->input->post("dh_hr");
		$col_sp_kum 	= $this->input->post("sp_kum");
		$col_sp_hr 		= $this->input->post("sp_hr");
		$col_bp_kum 	= $this->input->post("bp_kum");
		$col_bp_hr 		= $this->input->post("bp_hr");
		$col_bb 		= $this->input->post("bb");
		$col_fcr 		= $this->input->post("fcr");
		
		if($kode_riwayat != ""){
			$format_arr = explode("-", $kode_riwayat);
			$format = $format_arr[0];
			$kode_std_budidaya_last = $this->m_budidaya->get_last_std_formated($format);
			
			$format_arr = explode("/", $format_arr[0]);
			
			if(!empty($kode_std_budidaya_last) and isset($kode_std_budidaya_last["kode_std_budidaya"])){
				//pecah kode_std_breeding untuk hasilkan NEXT Increment kode
				$kd_arr = explode("-", $kode_std_budidaya_last["kode_std_budidaya"]);
				$last = $kd_arr[1];
				$new = $kd_arr[0] . '-' . ($last+1);
			}else{
				$new = $format . '-'. '1'; 
			}
			
			$data = array();
			for($i=0;$i<count($col_umur);$i++){
				$kode_barang = '';
				for($j=0;$j<count($umur_awal);$j++)
				{
					if($col_umur[$i] >= $umur_awal[$j] and $col_umur[$i] <= $umur_akhir[$j])
						$kode_barang = explode('*', $jenis_pakan[$j])[0];
				}
				
				$data[] = array(
					"kode_std_budidaya"=>$new,
					"std_umur"=>$col_umur[$i], 
					"dh_kum_prc"=>$col_dh_kum[$i], 
					"dh_hr_prc"=>$col_dh_hr[$i], 
					"pkn_kum_std"=>$col_sp_kum[$i], 
					"pkn_hr_std"=>$col_sp_hr[$i],	
					"pkn_kum"=>$col_bp_kum[$i], 
					"pkn_hr"=>$col_bp_hr[$i], 
					"target_bb"=>$col_bb[$i], 
					"fcr"=>$col_fcr[$i], 
					"kode_barang"=>$kode_barang
				);
			}
						
			if(count($data) > 0){
				//Insert to table
				$data_h = array();
				$data_h["kode_std_budidaya"] = $new;
				$data_h["kode_farm"] = $format_arr[1];
				$data_h["kode_strain"] = $kode_strain;
				$data_h["tgl_efektif"] = $tgl_efektif;
				$data_h["target_dh_prc"] = $bp_daya_hidup;
				$data_h["target_bb_prc"] = $bp_berat_hidup;
				$data_h["target_fcr_prc"] = $bp_fcr;
				$data_h["target_umur_panen"] = $bp_umur_panen;
				$data_h["target_ip"] = $bp_ip;
				$data_h["target_kum"] = $bp_kum;
				$data_h["tgl_buat"] = $now;
				$data_h["user_buat"] = $user;
								
				$result = $this->m_budidaya->insert_multiple_std_budidaya($data_h, $data);
				if($result > 0){
					$return["kode_std_budidaya"] = $new;
					$return["result"] = "success";
				}else{
					$return["result"] = "failed";
				}
			}
			else
				$return["result"] = "failed";
			
			echo json_encode($return);
		}else{
			if(isset($kode_farm) and count($kode_farm) > 0){
				$failed = 0;
				
				for($k=0;$k<count($kode_farm);$k++){
					if($failed == 0){
						$format = $kode_strain.'/'.$kode_farm[$k].'/'.$year;
					
						$kode_std_budidaya_last = $this->m_budidaya->get_last_std_formated($format);
				
						if(!empty($kode_std_budidaya_last) and isset($kode_std_budidaya_last["kode_std_budidaya"])){
							//pecah kode_std_breeding untuk hasilkan NEXT Increment kode
							$kd_arr = explode("-", $kode_std_budidaya_last["kode_std_budidaya"]);
							$last = $kd_arr[1];
							$new = $kd_arr[0] . '-' . ($last+1);
						}else{
							$new = $format . '-'. '1'; 
						}
						
						$data = array();
						for($i=0;$i<count($col_umur);$i++){
							$kode_barang = '';
							for($j=0;$j<count($umur_awal);$j++)
							{
								if($col_umur[$i] >= $umur_awal[$j] and $col_umur[$i] <= $umur_akhir[$j])
									$kode_barang = explode('*', $jenis_pakan[$j])[0];
							}
							
							$data[] = array(
								"kode_std_budidaya"=>$new,
								"std_umur"=>$col_umur[$i], 
								"dh_kum_prc"=>$col_dh_kum[$i], 
								"dh_hr_prc"=>$col_dh_hr[$i], 
								"pkn_kum_std"=>$col_sp_kum[$i], 
								"pkn_hr_std"=>$col_sp_hr[$i],	
								"pkn_kum"=>$col_bp_kum[$i], 
								"pkn_hr"=>$col_bp_hr[$i], 
								"target_bb"=>$col_bb[$i], 
								"fcr"=>$col_fcr[$i], 
								"kode_barang"=>$kode_barang
							);
						}
						
						if(count($data) > 0){
							//Insert to table
							$data_h = array();
							$data_h["kode_std_budidaya"] = $new;
							$data_h["kode_farm"] = $kode_farm[$k];
							$data_h["kode_strain"] = $kode_strain;
							$data_h["tgl_efektif"] = $tgl_efektif;
							$data_h["target_dh_prc"] = $bp_daya_hidup;
							$data_h["target_bb_prc"] = $bp_berat_hidup;
							$data_h["target_fcr_prc"] = $bp_fcr;
							$data_h["target_umur_panen"] = $bp_umur_panen;
							$data_h["target_ip"] = $bp_ip;
							$data_h["target_kum"] = $bp_kum;
							$data_h["tgl_buat"] = $now;
							$data_h["user_buat"] = $user;
											
							$result = $this->m_budidaya->insert_multiple_std_budidaya($data_h, $data);
							
							if($result > 0){
							}else{
								$failed = 1;
							}
						}else{
							$failed = 1;
						}
					}
				}
				
				if($failed == 0){
					$return["result"] = "success";
				}else{
					$return["result"] = "failed";
				}
			}
			
			echo json_encode($return);
		}
	}

	function cetak_std(){
		$riwayat_date = $this->input->get("riwayat_date");
		$kode_riwayat = $this->input->get("kode_riwayat");
				
		$detail_row = $this->m_budidaya->get_detail_std_budidaya($kode_riwayat);
		
		$this->load->library ( 'Pdf' );
		// //Custom size  
		$width = 210;  
		$height = 297; 
		$pagelayout = array($width, $height); 
		$pdf = new Pdf ( 'P', PDF_UNIT, $pagelayout, true, 'UTF-8', false );
		
		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );
		
		$data["detail_row"] = $detail_row;
		$data["masa_berlaku"] = $riwayat_date;
		
		$html = $this->load->view('std_budidaya/print_bdy', $data, true );
		
		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );
		
		$pdf->Output ( 'std_budidaya.pdf', 'I' );
	}
}
