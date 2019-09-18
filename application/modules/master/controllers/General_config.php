<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** halaman untuk setting timeline general */
class General_config extends MY_Controller{

	function __construct(){
		parent::__construct();	
		error_reporting(0);
		$this->load->model('master/m_general_config','mgc');	
	}

	function index(){
		$this->load->model('master/m_farm','m_farm');
		$farm = $this->m_farm->get_farm_browse();
		$data = array( 
			'farm' => $farm,
			'context' => $this->mgc->listContext()
		);
		
		$this->load->view("master/config_general/index", $data);
	}

	function get_pagination(){		
		$dataCari = $this->input->post('cari');
		$page = isset($dataCari['page']) ? $dataCari['page'] : 1;
		unset($dataCari['page']);
		$limit = 10;
		$offset = ($page - 1) * $limit;

		if(empty($dataCari)){
			$total = $this->mgc->count_all();
		}else{
			$total = $this->mgc->count_by($dataCari);
		}
		if(empty($dataCari)){
			$data = $this->mgc->as_array()->limit($limit,$offset)->get_all();				
		}else{
			$data = $this->mgc->as_array()->limit($limit,$offset)->get_many_by($dataCari);				
		}		
		
		$pages = ceil($total/$limit);		
		$this->result['TotalRows'] = $pages;
		$this->result['Rows'] = $data;		
		$this->result['limit'] = $limit;	
		$this->result['status'] = 1;		
		$this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));
	}

	function simpan(){
		$data = $this->input->post('data');
		$sinkron = $data['sinkron'];
		$value = $data['value'];
		unset($data['sinkron']);
		unset($data['value']);
		
		$this->mgc->update_by($data,array('value' => $value));
		$this->result['status'] = 1;
		$this->result['content'] = $data;
		$this->result['message'] = 'Data berhasil disimpan';
		$this->result['sinkron'] = $sinkron;
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));

	}

}
