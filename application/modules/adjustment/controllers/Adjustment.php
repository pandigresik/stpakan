<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Adjustment extends MX_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model("M_adjustment");
	}
	
	function index(){
		$farm = $this->m_adjustment->get_farm("");
		$data["farm"] = $farm;
		
		$this->load->view("list_adjustment", $data);
	}
	
	function get_pagination(){
		$offset = 10;
		$page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
		$is_search = ($this->input->post('search')) ? $this->input->post('search') : false;
		
		$noadjustment = (($this->input->post("noadjustment")) and $is_search == true) ? $this->input->post("noadjustment") : null;
		$tanggal = (($this->input->post("tanggal")) and $is_search == true) ? $this->input->post("tanggal") : null;
		$namafarm = (($this->input->post("namafarm")) and $is_search == true) ? $this->input->post("namafarm") : null;
		$tipe = (($this->input->post("tipe")) and $is_search == true) ? $this->input->post("tipe") : null;
		$alasan = (($this->input->post("alasan")) and $is_search == true) ? $this->input->post("alasan") : null;
		
		$adjustment_all = $this->m_adjustment->get_adjustment(null, null, $noadjustment, $tanggal, $namafarm, $tipe, $alasan);
		
		$adjustment = $this->m_adjustment->get_adjustment(($page_number*$offset), ($page_number+1)*$offset, $noadjustment, $tanggal, $namafarm, $tipe, $alasan);
		
		$total =  count($adjustment_all);		
		$pages = ceil($total/$offset);
		
		 
		if(count($gudang) > 0){
			$data = array(
				'TotalRows' => $pages,
				'Rows' => $adjustment
			);
		
			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			echo json_encode(array());
		}
		
		exit;
	}
}
