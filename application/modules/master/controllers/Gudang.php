<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Gudang extends MX_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model('m_farm');
		$this->load->model('m_gudang');
	}

	function index(){
		$farm = $this->m_farm->get_farm_browse();

		$data["farm"] = $farm;
		$this->load->view("gudang/gudang_list", $data);
	}

	function get_pagination(){
		$offset = 10;
		$page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
		$is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

		$namafarm = (($this->input->post("namafarm")) and $is_search == true) ? $this->input->post("namafarm") : null;
		$kodegudang = (($this->input->post("kodegudang")) and $is_search == true) ? $this->input->post("kodegudang") : null;
		$namagudang = (($this->input->post("namagudang")) and $is_search == true) ? $this->input->post("namagudang") : null;
		$beratmaksimal = ($this->input->post("beratmaksimal")) ? $this->input->post("beratmaksimal") : null;
		$qtymaksimal = ($this->input->post("qtymaksimal")) ? $this->input->post("qtymaksimal") : null;

		$gudang_all = $this->m_gudang->get_gudang(null, null, $namafarm, $kodegudang, $namagudang,$beratmaksimal,$qtymaksimal);

		$gudang = $this->m_gudang->get_gudang(($page_number*$offset), ($page_number+1)*$offset, $namafarm, $kodegudang, $namagudang,$beratmaksimal,$qtymaksimal);

		$total =  count($gudang_all);
		$pages = ceil($total/$offset);


		if(count($gudang) > 0){
			$data = array(
				'TotalRows' => $pages,
				'Rows' => $gudang
			);

			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			echo json_encode(array());
		}

		exit;
	}

	function get_gudang(){
		$kodefarm = ($this->input->post("kodefarm")) ? $this->input->post("kodefarm") : null;
		$kodegudang = ($this->input->post("kodegudang")) ? $this->input->post("kodegudang") : null;

		$gudang = $this->m_gudang->get_gudang_by_id($kodefarm, $kodegudang);

		echo json_encode($gudang);
	}

	function cek_kodegudang(){
		$kodefarm = $this->input->post("kodefarm");
		$kodegudang = $this->input->post("kodegudang");

		$gudang = $this->m_gudang->check_kode_gudang($kodefarm, $kodegudang);

		echo json_encode(array("jumlah"=>$gudang["n_result"]));
	}

	function add_gudang(){
		$kodefarm = ($this->input->post("kodefarm")) ? $this->input->post("kodefarm") : null;
		$kodegudang = ($this->input->post("kodegudang")) ? $this->input->post("kodegudang") : null;
		$namagudang = ($this->input->post("namagudang")) ? $this->input->post("namagudang") : null;
		$beratmaksimal = ($this->input->post("beratmaksimal")) ? $this->input->post("beratmaksimal") : null;
		$qtymaksimal = ($this->input->post("qtymaksimal")) ? $this->input->post("qtymaksimal") : null;

		$return = array();
		$data = array(
			"kode_farm"=>$kodefarm,
			"kode_gudang"=>$kodegudang,
			"nama_gudang"=>$namagudang,
			"max_berat" => $beratmaksimal,
			"max_kuantitas" => $qtymaksimal
		);

		$result = $this->m_gudang->insert($data);
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

	function update_gudang(){
		$kodefarm = ($this->input->post("kodefarm")) ? $this->input->post("kodefarm") : null;
		$kodegudang = ($this->input->post("kodegudang")) ? $this->input->post("kodegudang") : null;
		$namagudang = ($this->input->post("namagudang")) ? $this->input->post("namagudang") : null;
		$beratmaksimal = ($this->input->post("beratmaksimal")) ? $this->input->post("beratmaksimal") : null;
		$qtymaksimal = ($this->input->post("qtymaksimal")) ? $this->input->post("qtymaksimal") : null;

		$data = array(
			"nama_gudang"=>$namagudang,
			"max_berat" => $beratmaksimal,
			"max_kuantitas" => $qtymaksimal
		);

		$result = $this->m_gudang->update($data, $kodefarm, $kodegudang);
		$return = array();
		$return["form_mode"] = "ubah";
		if($result){
			$return["result"] = "success";
		}else{
			$return["result"] = "failed";
		}

		#echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));

	}

}
