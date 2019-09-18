<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Pemantauan_lhk extends MX_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model("M_riwayat_harian_kandang_bdy", "m_riwayat");
	}
	
	function index(){
		$kodefarm = $this->session->userdata("kode_farm");
		$kodeuser = $this->session->userdata("kode_user");
		$farms = $this->m_riwayat->get_farm_for_pemantauan($kodeuser);
		$grup_farm = $this->session->userdata("grup_farm");
		
		$level = $this->session->userdata("level_user");
		$farm = $this->m_riwayat->get_farm($kodefarm);
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"]);
		
		$data["level"] = $level;
		$data["farms"] = $farms;
		$data["kode_farm"] = $kodefarm;
		$data["nama_farm"] = $farm["nama_farm"];
		$data["today"] = $date[0];
		
		
		if($grup_farm == "BDY"){
			$this->load->view("pemantauan_bdy", $data);
		}else{
			$this->load->view("pemantauan", $data);
		}
	}
	
	function get_data(){
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"])[0];
		
		$q_farm = $this->input->post('q_farm');
		$first_doc_in = $this->m_riwayat->get_min_doc_in($q_farm)["tgl_doc_in"];
		
		$q_tidak_sesuai_timeline = $this->input->post('q_tidak_sesuai_timeline');
		$q_sesuai_timeline = $this->input->post('q_sesuai_timeline');
		$q_belum_dientry = $this->input->post('q_belum_dientry');
		$q_belum_konfirmasi = $this->input->post('q_belum_konfirmasi');
		$q_sudah_konfirmasi = $this->input->post('q_sudah_konfirmasi');
		$q_pakan_berlebih = $this->input->post('q_pakan_berlebih');
		
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
			$param1[] = "stTemp = 'TIDAK SESUAI TIMELINE'";
		}
		if($q_sesuai_timeline > 0){
			$param1[] = "stTemp = 'SESUAI TIMELINE'";
		}
		if($q_belum_dientry > 0){
			$param1[] = "(stTemp = 'BELUM ENTRY - TELAT' or stTemp = 'BELUM ENTRY')";
		}
		if($q_pakan_berlebih > 0){
			$param1[] = "qc.TGL_TRANSAKSI2 is not null";
		}
				
		if($q_belum_konfirmasi > 0){
			$param6[] = "ack_kf is null";
			$param6[] = "ack_dir is null";
		}
		if($q_sudah_konfirmasi > 0){
			$param2[] = "ack_kf is not null";
			$param2[] = "ack_dir is not null";
		}
		
		if(!empty($q_kandang)){
			$param3[] = " nama_kandang like '%" . $q_kandang . "%' ";
		}
		
		if(!empty($q_noreg)){
			$param3[] = " noReg like '%" . $q_noreg . "%' ";
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
	
	function get_menu_farm(){
		$kodeuser = $this->session->userdata("kode_user");
		$farms = $this->m_riwayat->get_farm_for_pemantauan($kodeuser);
		
		echo json_encode(array("items"=>$farms));
	}
	
	function simpan_ack(){
		$no_reg = $this->input->post('no_reg');
		$tgl_transaksi = $this->input->post('tgl_transaksi');
		$ack_desc = ($this->input->post('ack_desc')) ? $this->input->post('ack_desc') : "";
		
		if(!empty($ack_desc) or $ack_desc != ""){
			$result = $this->m_riwayat->simpan_ack_kf($ack_desc, $no_reg, $tgl_transaksi);
		}else{
			$result = $this->m_riwayat->simpan_ack_dir($no_reg, $tgl_transaksi);
		}
		
		if($result){
			$fulldate = $this->m_riwayat->get_today();
			$datetime = $fulldate["today"];
			
			echo json_encode(array("msg"=>"success", "tgl_ack"=>$datetime));
		}else{
			echo json_encode(array("msg"=>"failed"));
		}
	}
	
}
