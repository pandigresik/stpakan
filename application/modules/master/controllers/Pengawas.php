<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Pengawas extends MX_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model("m_pengawas");
	}
	
	function index(){
		$grups = $this->m_pengawas->get_gruppegawai();
		
		$data["grups"] = $grups;
		$data["farm"] = $this->db->select(array('kode_farm','nama_farm'))->get('m_farm')->result();
		$this->load->view("pengawas/pengawas_list", $data);
	}
	
	function get_pagination(){
		$offset = 10;
		$page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
		$is_search = ($this->input->post('search')) ? $this->input->post('search') : false;
		
		$kodepengawas = (($this->input->post("kodepengawas")) and $is_search == true) ? $this->input->post("kodepengawas") : null;
		$namapengawas = (($this->input->post("namapengawas")) and $is_search == true) ? $this->input->post("namapengawas") : null;
		$jeniskelamin = (($this->input->post("jeniskelamin")) and $this->input->post("jeniskelamin") != "" and $is_search == true) ? $this->input->post("jeniskelamin") : null;
		$status = (($this->input->post("status")) and $this->input->post("status") != "" and $is_search == true) ? $this->input->post("status") : null;

		$pengawas_all = $this->m_pengawas->get_pengawas(null, null, $kodepengawas, $namapengawas, $jeniskelamin, $status);
		
		$pengawas = $this->m_pengawas->get_pengawas(($page_number*$offset), ($page_number+1)*$offset, $kodepengawas, $namapengawas, $jeniskelamin, $status);
		
		$total =  count($pengawas_all);		
		$pages = ceil($total/$offset);
		
		 
		if(count($pengawas) > 0){
			$data = array(
				'TotalRows' => $pages,
				'Rows' => $pengawas
			);
		
			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			echo json_encode(array());
		}
		
		exit;
	}
	
	function get_pengawas(){
		$kodepengawas = ($this->input->post("kodepengawas")) ? $this->input->post("kodepengawas") : null;
		
		$pengawas = $this->m_pengawas->get_pengawas_by_id($kodepengawas);
		$tmp_pegawai_d = $this->db->where('kode_pegawai',$kodepengawas)->get('pegawai_d')->result();		
		$pegawai_d = array();
		if(!empty($tmp_pegawai_d)){
			foreach($tmp_pegawai_d as $d){
				$pegawai_d[] = $d->KODE_FARM;
			}
		}
		$pengawas['list_farm'] = $pegawai_d;
		
		echo json_encode($pengawas);
	}
	
	function get_next_kode_pengawas(){
		$pengawas = $this->m_pengawas->get_next_kode_pengawas();
				
		echo json_encode($pengawas);
	}
	
	function add_pengawas(){
		$kodepengawas = ($this->input->post("kodepengawas")) ? $this->input->post("kodepengawas") : null;
		$namapengawas = ($this->input->post("namapengawas")) ? $this->input->post("namapengawas") : null;
		$jeniskelamin = ($this->input->post("jeniskelamin")) ? $this->input->post("jeniskelamin") : null;
		$telp = ($this->input->post("telp")) ? $this->input->post("telp") : null;
		$gruppegawai = ($this->input->post("gruppegawai")) ? $this->input->post("gruppegawai") : null;
		$username = ($this->input->post("username")) ? $this->input->post("username") : null;
		$password = ($this->input->post("password")) ? $this->input->post("password") : null;
		$status = ($this->input->post("status")) ? $this->input->post("status") : null;
		$list_farm = ($this->input->post("list_farm")) ? $this->input->post("list_farm") : null;
		
		$data = array(
			"kode_pegawai"=>$kodepengawas,
			"nama_pegawai"=>$namapengawas,
			"jenis_kelamin"=>$jeniskelamin,
			"no_telp"=>$telp,
			"grup_pegawai"=>$gruppegawai,
			"username"=>$username,
			"password"=>$password,
			"status_pegawai"=>$status
		);
		
		$result = $this->m_pengawas->insert($data);
		/* tambahkan ke pegawai_d juga */		
		if(!empty($list_farm)){
			
			foreach($list_farm as $lf){
				$pegawai_d = array('kode_pegawai' => $kodepengawas,'kode_farm' => $lf);
				$this->db->insert('pegawai_d',$pegawai_d);
			}
			 
			
		}
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
	
	function update_pengawas(){
		$kodepengawas = ($this->input->post("kodepengawas")) ? $this->input->post("kodepengawas") : null;
		$namapengawas = ($this->input->post("namapengawas")) ? $this->input->post("namapengawas") : null;
		$jeniskelamin = ($this->input->post("jeniskelamin")) ? $this->input->post("jeniskelamin") : null;
		$telp = ($this->input->post("telp")) ? $this->input->post("telp") : null;
		$gruppegawai = ($this->input->post("gruppegawai")) ? $this->input->post("gruppegawai") : null;
		$username = ($this->input->post("username")) ? $this->input->post("username") : null;
		$password = ($this->input->post("password")) ? $this->input->post("password") : null;
		$status = ($this->input->post("status")) ? $this->input->post("status") : null;
		$list_farm = ($this->input->post("list_farm")) ? $this->input->post("list_farm") : null;

		$data = array(
			"nama_pegawai"=>$namapengawas,
			"jenis_kelamin"=>$jeniskelamin,
			"no_telp"=>$telp,
			"grup_pegawai"=>$gruppegawai,
			"username"=>$username,
			"password"=>$password,
			"status_pegawai"=>$status
		);
		$this->db->where(array('kode_pegawai' => $kodepengawas))->delete('pegawai_d');
		foreach ($list_farm as $lf) {
			$pegawai_d = array('kode_pegawai' => $kodepengawas, 'kode_farm' => $lf);
			$this->db->insert('pegawai_d', $pegawai_d);
		}
		$result = $this->m_pengawas->update($data, $kodepengawas);
		
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
	
	function check_username(){
		$username = ($this->input->post("username")) ? $this->input->post("username") : null;
		$result = $this->m_pengawas->get_username($username);
		
		echo json_encode(array("result"=>$result["eksis"]));
	}
}
