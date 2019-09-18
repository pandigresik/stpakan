<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Realisasi_panen extends MX_Controller{

	function __construct(){
		parent::__construct();

		$this->load->model("M_riwayat_harian_kandang_bdy", "m_riwayat");
		$this->load->model("M_realisasi_panen", "m_panen");
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
		
		$this->load->view("realisasi_panen", $data);
	}

	function get_kandang_farm(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;

		$kandang = $this->m_riwayat->get_kandang_siklus($kode_farm);

		echo json_encode($kandang);
	}
	
	function get_available_do(){
		$max_berat_rit = 3000;
		$noreg = $this->input->post('no_reg');
		$do = $this->m_panen->get_data_do($noreg);
		$tmp_do_nyeser = $this->m_panen->get_do_nyeser($noreg);
		$do_nyeser = array();
		if(!empty($tmp_do_nyeser)){
			/** tandai do_nyeser dan jumlah berat yang masih bisa diambil */
			foreach($tmp_do_nyeser as $_ny){
				$do_nyeser[$_ny['no_do']] = $max_berat_rit - $_ny['sudah_panen'];
			}
		}
		echo json_encode(array('do' => $do, 'do_nyeser' => $do_nyeser));
	}
	
	function get_realisasi_panen(){
		$no_reg = $this->input->post("no_reg");
		
		$data["panen"] = $this->m_panen->get_realisasi_panen($no_reg);
		
		echo json_encode($data);
	}
	
	function compare_data(){
		$no_reg = $this->input->post('no_reg');
		$no_sj = $this->input->post('no_sj');
		$no_do = $this->input->post('no_do');
		$tot_timbang_ayam = $this->input->post('tot_timbang_ayam');
		$tot_timbang_netto = $this->input->post('tot_timbang_netto');
		
		$panen = $this->m_panen->get_realisasi_panen_spec($no_reg, $no_do, $no_sj);
		$akt_timbang_ayam = $panen["JUMLAH_AKTUAL"];
		$akt_timbang_netto = $panen["BERAT_AKTUAL"];
		
		$result = array();
		$tot_timbang_ayam = $tot_timbang_ayam  * 1;
		$akt_timbang_ayam = $akt_timbang_ayam * 1;  
		$tot_timbang_netto = round(($tot_timbang_netto*1000/1000),3);
		$akt_timbang_netto = round(($akt_timbang_netto*1000/1000),3);
		
		if(($tot_timbang_ayam-$akt_timbang_ayam) != 0 or ($tot_timbang_netto-$akt_timbang_netto) != 0){
			$result = array("result"=>"not ok","akt_timbang_ayam"=>$akt_timbang_ayam,"akt_timbang_netto"=>$akt_timbang_netto);
		}else{
			$result = array("result"=>"ok","akt_timbang_ayam"=>$akt_timbang_ayam,"akt_timbang_netto"=>$akt_timbang_netto);
		}
		
		echo json_encode($result);
	}

	function simpan_detil_penimbangan(){		
		$no_reg = $this->input->post('no_reg');
		$no_sj = $this->input->post('no_sj');
		$no_do = $this->input->post('no_do');
		$user_buat = $this->session->userdata('kode_user');
		
		$jumlah_akhir = $this->input->post('jumlah_akhir');
		$berat_akhir = $this->input->post('berat_akhir');
		$berat_tara = $this->input->post('berat_tara');
		$arr_tara_berat = $this->input->post('arr_tara_berat');
		$arr_tara_box = $this->input->post('arr_tara_box');
		$arr_ayam_jumlah = $this->input->post('arr_ayam_jumlah');
		$arr_ayam_tonase = $this->input->post('arr_ayam_tonase');
		$tot_timbang_ayam = $this->input->post('tot_timbang_ayam');
		$tot_timbang_netto = $this->input->post('tot_timbang_netto');
		
		$berat_timbang = $this->input->post('berat_timbang');
		$jumlah_timbang = $this->input->post('jumlah_timbang');
		
		$panen = array();
		$panen_tara = array();
		$panen_detail = array();
		
		$panen_filter = array("NO_REG"=>$no_reg, "NO_SURAT_JALAN"=>$no_sj, "NO_DO"=>$no_do);
		$panen["BERAT_TARA"] = $berat_tara;
		$panen["JUMLAH_AKHIR"] = $jumlah_akhir;
		$panen["BERAT_AKHIR"] = $berat_akhir;
		$panen["JUMLAH_TIMBANG"] = $jumlah_timbang;
		$panen["BERAT_TIMBANG"] = $berat_timbang;
		
		for($i=0;$i<count($arr_tara_berat);$i++){
			$panen_tara[] = array(
				"NO_SURAT_JALAN"=>$no_sj,
				"NO_URUT"=> ($i+1),
				"JUMLAH"=>$arr_tara_box[$i],
				"BERAT_TARA"=>$arr_tara_berat[$i]
			);
		}
		
		for($j=0;$j<count($arr_ayam_jumlah);$j++){
			$panen_detail[] = array(
				"NO_SURAT_JALAN"=>$no_sj,
				"NO_URUT"=> ($j+1),
				"JUMLAH"=>$arr_ayam_jumlah[$j],
				"BERAT_BRUTO"=>$arr_ayam_tonase[$j]
			);
		}
		
		$result = $this->m_panen->simpan_panen($panen, $panen_filter, $panen_tara, $panen_detail);
		if($result){
			// echo json_encode(array("result"=>"success"));
			$_r = array("result"=>"success");
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode($_r));
		}else{
			echo json_encode(array("result"=>"failed"));
			$_r = array("result"=>"failed");
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode($_r));
		}
	}
	
	function get_detil_panen(){
		$no_reg = $this->input->post('no_reg');
		$no_do  = $this->input->post('no_do');
		$no_sj  = $this->input->post('no_sj');
		
		$data["tara"] = $this->m_panen->get_tara_panen($no_reg, $no_do, $no_sj);
		$data["ayam"] = $this->m_panen->get_ayam_panen($no_reg, $no_do, $no_sj);
		
		echo json_encode($data);
	}

	function simpan_admin_farm(){		
		$no_reg= $this->input->post("no_reg");
		$no_sj= $this->input->post("no_sj");
		$no_do= $this->input->post("no_do");
		$tgl_panen= $this->input->post("tgl_panen");
		$umur_panen= $this->input->post("umur_panen");
		$berat_aktual= $this->input->post("berat_aktual");
		$jumlah_aktual= $this->input->post("jumlah_aktual");
		$tgl_datang= $this->input->post("tgl_datang");
		$tgl_mulai= $this->input->post("tgl_mulai");
		$tgl_selesai= $this->input->post("tgl_selesai");
		$tgl_buat= date('Y-m-d H:i:s'); //$this->input->post("tgl_buat");
		$user_buat = $this->session->userdata('kode_user');
		
		$panen["NO_SURAT_JALAN"] = $no_sj;
		$panen["NO_DO"] = $no_do;
		$panen["NO_REG"] = $no_reg;
		$panen["TGL_PANEN"] = $tgl_panen;
		$panen["UMUR_PANEN"] = $umur_panen;
		$panen["BERAT_AKTUAL"] = $berat_aktual;
		$panen["JUMLAH_AKTUAL"] = $jumlah_aktual;
		$panen["BERAT_TIMBANG"] = $berat_aktual;
		$panen["JUMLAH_TIMBANG"] = $jumlah_aktual;
		$panen["BERAT_AKHIR"] = $berat_aktual;
		$panen["JUMLAH_AKHIR"] = $jumlah_aktual;
		$panen["BERAT_BADAN_RATA2"] = $berat_aktual/$jumlah_aktual;
		$panen["TGL_DATANG"] = $tgl_datang;
		$panen["TGL_MULAI"] = $tgl_mulai;
		$panen["TGL_SELESAI"] = $tgl_selesai;
		$panen["TGL_BUAT"] = $tgl_buat;
		$panen["USER_BUAT"] = $user_buat;
		
		$result = $this->m_panen->simpan_admin_farm($panen);
		
		if($result){
			$_r = array("result"=>"success");
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode($_r));
		}else{
			echo json_encode(array("result"=>"failed"));
			$_r = array("result"=>"failed");
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode($_r));
		}
	}
	
	function simpan_do_susulan(){
		$no_reg= $this->input->post("no_reg");
		$no_sj= $this->input->post("no_sj");
		$no_do= $this->input->post("no_do");
		$tgl_panen= $this->input->post("tgl_panen");
		$umur_panen= $this->input->post("umur_panen");
		
		$panen["NO_DO"] = $no_do;
		$panen["TGL_PANEN"] = $tgl_panen;
		$panen["UMUR_PANEN"] = ($umur_panen + 0) * 1;
		
		$result = $this->m_panen->simpan_do_susulan($panen, $no_reg, $no_sj);
		if($result){
			$_r = array("result"=>"success");
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode($_r));
		}else{
			echo json_encode(array("result"=>"failed"));
			$_r = array("result"=>"failed");
			$this->output
						->set_content_type('application/json')
						->set_output(json_encode($_r));
		}
	}

	function cek_sj(){
		$no_sj = $this->input->post('no_sj');
		$result = $this->m_panen->cek_sj($no_sj);
		
		if($result["result"] > 0){
			echo json_encode(array("result"=>"error"));
		}else{
			echo json_encode(array("result"=>"success"));
		}
	}
}
