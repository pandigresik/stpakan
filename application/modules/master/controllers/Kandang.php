<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Kandang extends MX_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('m_kandang');
		$this->load->model('m_farm');
	}

	public function index(){
		$farm = $this->m_farm->get_farm_browse();

		$data["farm"] = $farm;
		$this->load->view("kandang/kandang_list", $data);
	}

	function get_pagination(){
		$offset = 10;
		$page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
		$is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

		$namafarm = (($this->input->post("namafarm")) and $is_search == true) ? $this->input->post("namafarm") : null;
		$namakandang = (($this->input->post("namakandang")) and $is_search == true) ? $this->input->post("namakandang") : null;
		$kapasitaskandangjantan = (($this->input->post("kapasitaskandangjantan")) and $is_search == true) ? $this->input->post("kapasitaskandangjantan") : null;
		$kapasitaskandangbetina = (($this->input->post("kapasitaskandangbetina")) and $is_search == true) ? $this->input->post("kapasitaskandangbetina") : null;
		$kapasitaskandang = (($this->input->post("kapasitaskandang")) and $is_search == true) ? $this->input->post("kapasitaskandang") : null;

		$tipekandang = (($this->input->post("tipekandang")) and ($this->input->post("tipekandang")) != "" and $is_search == true) ? $this->input->post("tipekandang") : null;
		$tipelantai = (($this->input->post("tipelantai")) and ($this->input->post("tipelantai")) != "" and $is_search == true) ? $this->input->post("tipelantai") : null;
		$status = (($this->input->post("status")) and ($this->input->post("status")) != "" and $is_search == true) ? $this->input->post("status") : null;

		$kandang_all = $this->m_kandang->get_kandang(null, null, $namafarm, $namakandang, $kapasitaskandangjantan, $kapasitaskandangbetina,$kapasitaskandang, $tipekandang, $tipelantai, $status);

		$kandang = $this->m_kandang->get_kandang(($page_number*$offset), ($page_number+1)*$offset, $namafarm, $namakandang, $kapasitaskandangjantan, $kapasitaskandangbetina,$kapasitaskandang, $tipekandang, $tipelantai, $status);

		$total =  count($kandang_all);
		$pages = ceil($total/$offset);


		if(count($kandang) > 0){
			$data = array(
				'TotalRows' => $pages,
				'Rows' => $kandang
			);

			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			echo json_encode(array());
		}

		exit;
	}

	function get_kandang(){
		$kodekandang = ($this->input->post("kodekandang")) ? $this->input->post("kodekandang") : null;

		if(isset($kodekandang)){
			$arr = explode('-', $kodekandang);
			$kode_frm = $arr[0];
			$kode_kdg = $arr[1];

			$kandang = $this->m_kandang->get_kandang_by_id($kode_frm, $kode_kdg);
		}else{
			$kandang = array();
		}

		echo json_encode($kandang);
	}

	function cek_kodekandang(){
		$kodefarm = ($this->input->post("kodefarm")) ? $this->input->post("kodefarm") : null;
		$kodekandang = ($this->input->post("kodekandang")) ? $this->input->post("kodekandang") : null;

		$kandang = $this->m_kandang->cek_kode_kandang($kodefarm, $kodekandang);

		echo json_encode(array("jumlah"=>$kandang["n_result"]));
	}

	function add_kandang(){
		$kodefarm = ($this->input->post("kodefarm")) ? $this->input->post("kodefarm") : null;
		$kodekandang = ($this->input->post("kodekandang")) ? $this->input->post("kodekandang") : null;
		$namakandang = ($this->input->post("namakandang")) ? $this->input->post("namakandang") : null;
		$digitcek = ($this->input->post("digitcek")) ? $this->input->post("digitcek") : null;
		$jmljantan = ($this->input->post("jmljantan")) ? $this->input->post("jmljantan") : null;
		$jmlbetina = ($this->input->post("jmlbetina")) ? $this->input->post("jmlbetina") : null;
		$luaskandangbetina = ($this->input->post("luaskandangbetina")) ? $this->input->post("luaskandangbetina") : null;
		$luaskandangjantan = ($this->input->post("luaskandangjantan")) ? $this->input->post("luaskandangjantan") : null;
		$tipekandang = ($this->input->post("tipekandang")) ? $this->input->post("tipekandang") : null;
		$tipelantai = ($this->input->post("tipelantai")) ? $this->input->post("tipelantai") : null;
		$status = ($this->input->post("status")) ? $this->input->post("status") : null;
		/* tambahan untuk budidaya */
		$luaskandang = ($this->input->post("luaskandang")) ? $this->input->post("luaskandang") : null;
		$kapasitaskandang = ($this->input->post("kapasitaskandang")) ? $this->input->post("kapasitaskandang") : null;
		$jmlsekat = ($this->input->post("jmlsekat")) ? $this->input->post("jmlsekat") : null;
		$noflok = ($this->input->post("noflok")) ? $this->input->post("noflok") : null;
		$return = array();

		// $kode = $this->m_kandang->check_digitcheck_kandang($kodefarm, $digitcek);
		$digit_check = $this->m_kandang->check_digitcheck_kandang($kodefarm, $digitcek);
		$return["form_mode"] = "tambah";
		if($digit_check["n_result"] > 0){
			$return["result"] = "failed";
			$return["err"] = "digitcek";
		}
		// elseif($kode["n_result"] > 0){
			// $return["result"] = "failed";
			// $return["err"] = "kodecek";
		// }
		else{
			$data = array(
				"kode_farm"=>$kodefarm,
				"kode_kandang"=>$kodekandang,
				"nama_kandang"=>$namakandang,
				"jml_jantan"=>$jmljantan,
				"jml_betina"=>$jmlbetina,
				"max_populasi"=> !empty($kapasitaskandang) ? $kapasitaskandang : (($jmljantan * 1) + ($jmlbetina * 1)),
				"luas_kandang_betina"=>$luaskandangbetina,
				"luas_kandang_jantan"=>$luaskandangjantan,
				"tipe_kandang"=>$tipekandang,
				"tipe_lantai"=>$tipelantai,
				"status_kandang"=>$status,
				"kode_verifikasi"=>$digitcek,
				"luas_kandang" => $luaskandang,
				"jml_sekat" => $jmlsekat ,
				"no_flok" => $noflok
			);

			$result = $this->m_kandang->insert($data);
			if($result){
				$return["result"] = "success";
			}else{
				$return["result"] = "failed";
				$return["err"] = "failed";
			}
		}
		#echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}

	function update_kandang(){
		$kodefarm = ($this->input->post("kodefarm")) ? $this->input->post("kodefarm") : null;
		$kodekandang = ($this->input->post("kodekandang")) ? $this->input->post("kodekandang") : null;
		$namakandang = ($this->input->post("namakandang")) ? $this->input->post("namakandang") : null;
		$digitcek = ($this->input->post("digitcek")) ? $this->input->post("digitcek") : null;
		$jmljantan = ($this->input->post("jmljantan")) ? $this->input->post("jmljantan") : null;
		$jmlbetina = ($this->input->post("jmlbetina")) ? $this->input->post("jmlbetina") : null;
		$luaskandangbetina = ($this->input->post("luaskandangbetina")) ? $this->input->post("luaskandangbetina") : null;
		$luaskandangjantan = ($this->input->post("luaskandangjantan")) ? $this->input->post("luaskandangjantan") : null;
		$tipekandang = ($this->input->post("tipekandang")) ? $this->input->post("tipekandang") : null;
		$tipelantai = ($this->input->post("tipelantai")) ? $this->input->post("tipelantai") : null;
		$status = ($this->input->post("status")) ? $this->input->post("status") : null;
		/* tambahan untuk budidaya */
		$luaskandang = ($this->input->post("luaskandang")) ? $this->input->post("luaskandang") : null;
		$kapasitaskandang = ($this->input->post("kapasitaskandang")) ? $this->input->post("kapasitaskandang") : null;
		$jmlsekat = ($this->input->post("jmlsekat")) ? $this->input->post("jmlsekat") : null;
		$noflok = ($this->input->post("noflok")) ? $this->input->post("noflok") : null;
		$return = array();

		$digit_check = $this->m_kandang->check_digitcheck_kandang($kodefarm, $digitcek);
		$return["form_mode"] = "ubah";
		if($digit_check["n_result"] > 0){
			$return["result"] = "failed";
			$return["err"] = "digitcek";
		}
		else{
			$data = array(
				"nama_kandang"=>$namakandang,
				"jml_jantan"=>$jmljantan,
				"jml_betina"=>$jmlbetina,
				"max_populasi"=>!empty($kapasitaskandang) ? $kapasitaskandang : (($jmljantan * 1) + ($jmlbetina * 1)),
				"luas_kandang_betina"=>$luaskandangbetina,
				"luas_kandang_jantan"=>$luaskandangjantan,
				"tipe_kandang"=>$tipekandang,
				"tipe_lantai"=>$tipelantai,
				"status_kandang"=>$status,
				"kode_verifikasi"=>$digitcek,
				"luas_kandang" => $luaskandang,
				"jml_sekat" => $jmlsekat,
				"no_flok" => $noflok
			);

			$result = $this->m_kandang->update($data, $kodefarm, $kodekandang);
			if($result){
				$return["result"] = "success";
			}else{
				$return["result"] = "failed";
				$return["err"] = "failed";
			}
		}
		#echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}

}
