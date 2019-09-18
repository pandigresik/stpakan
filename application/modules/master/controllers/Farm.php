<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Farm extends MX_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->model('m_farm');
		$this->load->model('m_pelanggan');
	}
	
	function index(){
		$pelanggan = $this->m_pelanggan->get_pelanggan_browse();
		$kota = $this->m_farm->get_kota_browse();
		
		$data["pelanggan"] = $pelanggan;
		$data["kota"] = $kota;
		$this->load->view("farm/farm_list", $data);
	}
	
	function get_pagination(){
		$offset = 10;
		$page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
		$is_search = ($this->input->post('search')) ? $this->input->post('search') : false;
		
		$kodefarm = (($this->input->post("kodefarm")) and $is_search == true) ? $this->input->post("kodefarm") : null;
		$namafarm = (($this->input->post("namafarm")) and $is_search == true) ? $this->input->post("namafarm") : null;
		$alamat = (($this->input->post("alamat")) and $is_search == true) ? $this->input->post("alamat") : null;
		$kota = (($this->input->post("kota")) and $is_search == true) ? $this->input->post("kota") : null;
		$tipefarm = (($this->input->post("tipefarm")) and $this->input->post("tipefarm") != "" and $is_search == true) ? $this->input->post("tipefarm") : null;
		$grup = (($this->input->post("grup")) and $this->input->post("grup") != "" and $is_search == true) ? $this->input->post("grup") : null;
		$gruppelanggan = (($this->input->post("gruppelanggan")) and $is_search == true) ? $this->input->post("gruppelanggan") : null;
		
		$farm_all = $this->m_farm->get_farm(null, null, $kodefarm, $namafarm, $alamat, $kota, $tipefarm, $grup, $gruppelanggan);
		
		$farm = $this->m_farm->get_farm(($page_number*$offset), ($page_number+1)*$offset, $kodefarm, $namafarm, $alamat, $kota, $tipefarm, $grup, $gruppelanggan);
		
		$total =  count($farm_all);		
		$pages = ceil($total/$offset);
		
		 
		if(count($farm) > 0){
			$data = array(
				'TotalRows' => $pages,
				'Rows' => $farm
			);
		
			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			echo json_encode(array());
		}
		
		exit;
	}
	
	function add_farm(){
		$kodefarm = ($this->input->post("kodefarm")) ? $this->input->post("kodefarm") : null;
		$namafarm = ($this->input->post("namafarm")) ? $this->input->post("namafarm") : null;
		$alamat = ($this->input->post("alamat")) ? $this->input->post("alamat") : null;
		$kota = ($this->input->post("kota")) ? $this->input->post("kota") : null;
		$tipefarm = ($this->input->post("tipefarm")) ? $this->input->post("tipefarm") : null;
		$grupfarm = ($this->input->post("grupfarm")) ? $this->input->post("grupfarm") : null;
		$gruppelanggan = ($this->input->post("gruppelanggan")) ? $this->input->post("gruppelanggan") : null;
		$jmlFlok = $this->input->post('jmlFlok');
		$data = array(
			"kode_farm"=>$kodefarm,
			"nama_farm"=>$namafarm,
			"alamat_farm"=>$alamat,
			"kota"=>$kota,
			"tipe_farm"=>$tipefarm,
			"grup_farm"=>$grupfarm,
			"kode_pelanggan"=>$gruppelanggan
		);
		if(!empty($jmlFlok)){
			$data['jml_flok'] = $jmlFlok;
		}
		$result = $this->m_farm->insert($data);
		
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
	
	function update_farm(){
		$kodefarm = ($this->input->post("kodefarm")) ? $this->input->post("kodefarm") : null;
		$namafarm = ($this->input->post("namafarm")) ? $this->input->post("namafarm") : null;
		$alamat = ($this->input->post("alamat")) ? $this->input->post("alamat") : null;
		$kota = ($this->input->post("kota")) ? $this->input->post("kota") : null;
		$tipefarm = ($this->input->post("tipefarm")) ? $this->input->post("tipefarm") : null;
		$grupfarm = ($this->input->post("grupfarm")) ? $this->input->post("grupfarm") : null;
		$gruppelanggan = ($this->input->post("gruppelanggan")) ? $this->input->post("gruppelanggan") : null;
		$jmlFlok = ($this->input->post("jmlFlok")) ? $this->input->post("jmlFlok") : null; 
		
		$data = array(
			"nama_farm"=>$namafarm,
			"alamat_farm"=>$alamat,
			"kota"=>$kota,
			"tipe_farm"=>$tipefarm,
			"grup_farm"=>$grupfarm,
			"kode_pelanggan"=>$gruppelanggan,
			"jml_flok" => $jmlFlok	
		);
		
		
		$result = $this->m_farm->update($data, $kodefarm);
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
	
	function cek_kodefarm(){
		$kodefarm = $this->input->post("kode_farm");
		$farm = $this->m_farm->cek_kode_farm($kodefarm);
		
		echo json_encode(array("jumlah"=>$farm["jml"]));
	}
}
