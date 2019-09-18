<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Pelanggan extends MX_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model("m_pelanggan");
	}
	
	function index(){
		$this->load->view("pelanggan/pelanggan_list");
	}
	
	function get_pagination(){
		$offset = 10;
		$page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
		$is_search = ($this->input->post('search')) ? $this->input->post('search') : false;
		
		$kodepelanggan = (($this->input->post("kodepelanggan")) and $is_search == true) ? $this->input->post("kodepelanggan") : null;
		$namapelanggan = (($this->input->post("namapelanggan")) and $is_search == true) ? $this->input->post("namapelanggan") : null;
		$alamat = (($this->input->post("alamat")) and $is_search == true) ? $this->input->post("alamat") : null;
		$kota = (($this->input->post("kota")) and $is_search == true) ? $this->input->post("kota") : null;

		$pelanggan_all = $this->m_pelanggan->get_pelanggan(null, null, $kodepelanggan, $namapelanggan, $alamat, $kota);
		
		$pelanggan = $this->m_pelanggan->get_pelanggan(($page_number*$offset), ($page_number+1)*$offset, $kodepelanggan, $namapelanggan, $alamat, $kota);
		
		$total =  count($pelanggan_all);		
		$pages = ceil($total/$offset);
		
		 
		if(count($pelanggan) > 0){
			$data = array(
				'TotalRows' => $pages,
				'Rows' => $pelanggan
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
	
	function add_pelanggan(){
		$kodepelanggan = ($this->input->post("kodepelanggan")) ? $this->input->post("kodepelanggan") : null;
		$namapelanggan = ($this->input->post("namapelanggan")) ? $this->input->post("namapelanggan") : null;
		$alamat = ($this->input->post("alamat")) ? $this->input->post("alamat") : null;
		$kota = ($this->input->post("kota")) ? $this->input->post("kota") : null;
		
		$data = array(
			"kode_pelanggan"=>$kodepelanggan,
			"nama_pelanggan"=>$namapelanggan,
			"alamat"=>$alamat,
			"kota"=>$kota
		);
		
		$result = $this->m_pelanggan->insert($data);
		
		$return = array();
		$return["form_mode"] = "tambah";
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
	
	function update_pelanggan(){
		$kodepelanggan = ($this->input->post("kodepelanggan")) ? $this->input->post("kodepelanggan") : null;
		$namapelanggan = ($this->input->post("namapelanggan")) ? $this->input->post("namapelanggan") : null;
		$alamat = ($this->input->post("alamat")) ? $this->input->post("alamat") : null;
		$kota = ($this->input->post("kota")) ? $this->input->post("kota") : null;
		
		$data = array(
			"nama_pelanggan"=>$namapelanggan,
			"alamat"=>$alamat,
			"kota"=>$kota
		);
		
		$result = $this->m_pelanggan->update($data, $kodepelanggan);
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

	function check_kodepelanggan(){
		$kodepelanggan = ($this->input->post("kodepelanggan")) ? $this->input->post("kodepelanggan") : null;
		$result = $this->m_pelanggan->check_kodepelanggan($kodepelanggan);
		
		echo json_encode(array("jumlah"=>$result["n_result"]));
	}
}
