<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Kavling extends MX_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model("m_kavling");
		$this->load->helper("form");
	}

	function index(){
		$kode_farm = $this->session->userdata("kode_farm");
		$farms = $this->m_kavling->get_farm();
		$gudangs = $this->m_kavling->get_gudang_in_farm($kode_farm);

		$data["farm"] = $farms;
		$data["gudang"] = $gudangs;

		$this->load->view("kavling/kavling_list", $data);
	}

	function get_pagination(){
		$offset = 10;
		$page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
		$is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

		$namafarm = (($this->input->post("namafarm")) and $this->input->post("namafarm") != "" and $is_search == true) ? $this->input->post("namafarm") : null;
		$namagudang = (($this->input->post("namagudang")) and $this->input->post("namagudang") != "" and $is_search == true) ? $this->input->post("namagudang") : null;
		$nomorkavling = (($this->input->post("nomorkavling")) and $this->input->post("nomorkavling") != "" and $is_search == true) ? $this->input->post("nomorkavling") : null;
//		$beratmak = (($this->input->post("beratmak")) and $this->input->post("beratmak") != "" and $is_search == true) ? $this->input->post("beratmak") : null;
//		$jumlahmak = (($this->input->post("jumlahmak")) and $this->input->post("jumlahmak") != "" and $is_search == true) ? $this->input->post("jumlahmak") : null;
		$jmlpallet = (($this->input->post("jmlpallet")) and $this->input->post("jmlpallet") != "" and $is_search == true) ? $this->input->post("jmlpallet") : null;
		$status = (($this->input->post("status")) and $this->input->post("status") != "" and $is_search == true) ? $this->input->post("status") : null;

		$kavling_all = $this->m_kavling->get_kavling(null, null, $namafarm, $namagudang, $nomorkavling, $jmlpallet, $status);

		$kavling = $this->m_kavling->get_kavling(($page_number*$offset), ($page_number+1)*$offset, $namafarm, $namagudang, $nomorkavling, $jmlpallet, $status);

		$total =  count($kavling_all);
		$pages = ceil($total/$offset);

		if(count($kavling) > 0){
			$data = array(
				'TotalRows' => $pages,
				'Rows' => $kavling
			);

			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			$data = array(
				'TotalRows' => 0,
				'Rows' => 0
			);
			echo json_encode($data);
		}

		exit;
	}

	function simpanKavling(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$kode_gudang = ($this->input->post("kode_gudang")) ? $this->input->post("kode_gudang") : null;
		$baris = ($this->input->post("baris")) ? $this->input->post("baris") : null;
		$nomorposisi = ($this->input->post("nomorposisi")) ? $this->input->post("nomorposisi") : null;
		$namaposisi = ($this->input->post("namaposisi")) ? $this->input->post("namaposisi") : null;
		$kolom1 = ($this->input->post("kolom1")) ? $this->input->post("kolom1") : null;
		$kolom2 = ($this->input->post("kolom2")) ? $this->input->post("kolom2") : null;
		$step = ($this->input->post("step")) ? $this->input->post("step") : null;
//		$berat_maks = ($this->input->post("berat_maks")) ? $this->input->post("berat_maks") : null;
//		$jml_maks = ($this->input->post("jml_maks")) ? $this->input->post("jml_maks") : null;
		$jmlpallet = ($this->input->post("jmlpallet")) ? $this->input->post("jmlpallet") : null;

		$kode_verifikasi = ($this->input->post("kode_verifikasi")) ? $this->input->post("kode_verifikasi") : null;
		$status = ($this->input->post("status")) ? $this->input->post("status") : null;

		$kavling = array();
		$no_kolom = array();
		for($i=$kolom1;$i<=$kolom2;$i+=$step){
			$kavling[] = $baris . $nomorposisi . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);
			$no_kolom[] = $i;
		}

		$data_kavling = array();
		for($i=0;$i<count($kavling);$i++){
			$data_kavling[] = array(
				"kode_farm" => $kode_farm,
				"kode_gudang" => $kode_gudang,
				"no_kavling" => $kavling[$i],
				"no_baris" => $baris,
				"no_posisi" => $nomorposisi,
				"no_kolom" => $no_kolom[$i],
				"layout_posisi" => $namaposisi,
			//	"max_berat" => $berat_maks,
				"jml_pallet" => $jmlpallet,
				"kode_verifikasi" => $kode_verifikasi,
				"status_kavling" => $status,
				"jml_pallet" => $jmlpallet
			);
		}

		$result = $this->m_kavling->insert($data_kavling);
		if($result){
			$return["result"] = "success";
		}else{
			$return["result"] = "failed";
			$return["err"] = "failed";
		}

		#echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}

	function ubahKavling(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$kode_gudang = ($this->input->post("kode_gudang")) ? $this->input->post("kode_gudang") : null;
		$baris = ($this->input->post("baris")) ? $this->input->post("baris") : null;
		$nomorposisi = ($this->input->post("nomorposisi")) ? $this->input->post("nomorposisi") : null;
		$namaposisi = ($this->input->post("namaposisi")) ? $this->input->post("namaposisi") : null;
		$kolom1 = ($this->input->post("kolom1")) ? $this->input->post("kolom1") : null;
//		$berat_maks = ($this->input->post("berat_maks")) ? $this->input->post("berat_maks") : null;
//		$jml_maks = ($this->input->post("jml_maks")) ? $this->input->post("jml_maks") : null;
		$jmlpallet = ($this->input->post("jmlpallet")) ? $this->input->post("jmlpallet") : null;

		$kode_verifikasi = ($this->input->post("kode_verifikasi")) ? $this->input->post("kode_verifikasi") : null;
		$status = ($this->input->post("status")) ? $this->input->post("status") : null;

	//	$data["max_berat"] =  $berat_maks;
		$data["jml_pallet"] =  $jmlpallet;
		$data["kode_verifikasi"] =  $kode_verifikasi;
		$data["status_kavling"] =  $status;
		$data["jml_pallet"] = $jmlpallet;
		$result = $this->m_kavling->update($data, $kode_farm, $kode_gudang, $baris, $nomorposisi, $kolom1, $namaposisi);
		if($result){
			$return["result"] = "success";
		}else{
			$return["result"] = "failed";
			$return["err"] = "failed";
		}

		#echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}

	function getDataKavling(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$kode_gudang = ($this->input->post("kode_gudang")) ? $this->input->post("kode_gudang") : null;
		$no_kavling = ($this->input->post("no_kavling")) ? $this->input->post("no_kavling") : null;
		$baris = ($this->input->post("baris")) ? $this->input->post("baris") : null;
		$no_posisi = ($this->input->post("no_posisi")) ? $this->input->post("no_posisi") : null;
		$no_kolom = ($this->input->post("no_kolom")) ? $this->input->post("no_kolom") : null;
		$lay_posisi = ($this->input->post("lay_posisi")) ? $this->input->post("lay_posisi") : null;

		log_message("error", "ambil data kavling");
		$result = $this->m_kavling->get_data_kavling($kode_farm, $kode_gudang, $no_kavling, $baris, $no_posisi, $no_kolom, $lay_posisi);

		echo json_encode($result);
	}

	function getGudangInFarm(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$gudang = $this->m_kavling->getGudangInFarm($kode_farm);

		echo json_encode($gudang);
	}

	function cekNomorKavling(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$kode_gudang = ($this->input->post("kode_gudang")) ? $this->input->post("kode_gudang") : null;
		$baris = ($this->input->post("baris")) ? $this->input->post("baris") : null;
		$nomorposisi = ($this->input->post("nomorposisi")) ? $this->input->post("nomorposisi") : null;
		$step = ($this->input->post("step")) ? $this->input->post("step") : null;
		$kolom1 = ($this->input->post("kolom1")) ? $this->input->post("kolom1") : null;
		$kolom2 = ($this->input->post("kolom2")) ? $this->input->post("kolom2") : null;

		$kavlingExisting = array();

		$kavlings = $this->m_kavling->get_kavling_existing($kode_farm, $kode_gudang);
		foreach($kavlings as $kav){
			$kavlingExisting[] = $kav["no_kavling"];
		}

		$kavling = array();
		for($i=$kolom1;$i<=$kolom2;$i+=$step){
			$kavling[] = $baris . $nomorposisi . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);
		}

		$kavlingIntersect = array();
		$kavlingIntersect=array_intersect($kavlingExisting,$kavling);

		echo json_encode($kavlingIntersect);
	}

	function getDataFarm(){
		$kavling = $this->m_kavling->get_farm();

		echo json_encode($kavling);
	}

	function getDataFarmGudang(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$gudang = $this->m_kavling->get_farm_gudang($kode_farm);

		echo json_encode($gudang);
	}

	function setLayoutKavling(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$kode_gudang = ($this->input->post("kode_gudang")) ? $this->input->post("kode_gudang") : null;

		$alldata = $this->m_kavling->group_layout_kavling($kode_farm, $kode_gudang);
        $data ['layout'] = (!isset($alldata['data_kavling'])) ? array() : $alldata['data_kavling'];
        $data ['data_kolom'] = (!isset($alldata['data_kolom'])) ? array() : $alldata['data_kolom'];
        $data ['max_no_baris'] = (!isset($alldata['max_no_baris'])) ? array() : $alldata['max_no_baris'];
        $this->load->view('master/kavling/layout_kavling_master', $data);
	}
}
