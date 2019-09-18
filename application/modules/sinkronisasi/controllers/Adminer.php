<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Adminer extends MY_Controller {
	protected $result;
	protected $_user;
	protected $idFarm;

	public function __construct() {
		parent::__construct ();		
		$this->load->config('stpakan');
		$this->load->helper('text');		
		$this->idFarm = $this->config->item('idFarm');
		error_reporting(0);
	}

	public function index(){
		$data = array(
			'tables' => $this->db->list_tables(),
			'page' => $this->load->view('sinkronisasi/adminer/query',array(),TRUE)
		);
		$this->load->view('adminer/index',$data);
	}
	public function run(){
		$query = $this->input->post('query');		
		$t = $this->db->query($query);		
		$this->generateResult($t);
		
	}
	public function getQuery(){
		$tabel = $this->input->get('tabel');
		$aksi = $this->input->get('aksi');
		$fields = $this->db->list_fields($tabel);
		$column = array();
		$keys = array();
		$maxColumn = count($fields);
		$minColumn = 2 > $maxColumn ? $maxColumn : 2;
		if(!empty($fields)){
			$index = 0;
			foreach($fields as $f){
				$column[$f] = NULL;
				if($index <= $minColumn){
					$keys[$f] = '';
					$index++;
				}				
			}
		}
		
		$query = '';
		switch($aksi){
			case 'C':
				$query = $this->db->set($column)->get_compiled_insert($tabel);
				break;
			case 'R':
				$query = $this->db->limit(50)->get_compiled_select($tabel);
				break;				
			case 'U':
				$query = $this->db->where($keys)->set($column)->get_compiled_update($tabel);
				break;
			case 'D':
				$query = $this->db->where($keys)->set($column)->get_compiled_delete($tabel);
				break;				
		}
		$this->result['content'] = $query;
		$this->result['status'] = 1;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	function generateResult($result){
		if(is_bool($result)){
			echo $this->db->affected_rows().' baris terpengaruh';
		}else{
			$data = array(
				'data' => $result->result_array()
			);
			$this->load->view('sinkronisasi/adminer/result',$data);
		}		
	}
	function formSinkron(){
		$tabel = $this->input->get('tabel');
		$fields = $this->db->list_fields($tabel);
		$farms = $this->idFarm != 'FM' ? array(array('KODE_FARM' => 'FM', 'NAMA_FARM' => 'SERVER UTAMA FM')) :$this->db->select(array('KODE_FARM','NAMA_FARM'))->get('m_farm')->result_array();
		$aksi = array(
			array('kode' => 'I', 'label' => 'Insert'),
			array('kode' => 'U', 'label' => 'Update'),
			array('kode' => 'D', 'label' => 'Delete'),			
		);
		$form = $this->load->view('sinkronisasi/adminer/form_sinkronisasi',array('fields' => $fields, 'farms' => $farms, 'aksi' => $aksi),TRUE);
		$this->result['content'] = $form;
		$this->result['status'] = 1;
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}

	function simpanSinkron(){
		$this->load->model('sinkronisasi/m_sinkronisasi','sinc');		
		$transaksi = $this->input->post('transaksi');
		$kode_farm = $this->input->post('kode_farm');
		$detail_sinkron = $this->input->post('detail_sinkron');	
		
		$datatransaksi = array(
			'transaksi' => $transaksi,		
			'asal' => $this->idFarm,
			'tujuan' => $kode_farm,
			'aksi' => 'PUSH'
		);
		$this->db->trans_begin();
		$idSinkron = $this->sinc->insert($datatransaksi);
		foreach($detail_sinkron as $sql){
			$sql['kunci'] = json_encode($sql['kunci']);
			$sql['sinkronisasi'] = $idSinkron;
			$this->db->insert('detail_sinkronisasi',$sql);			
		}
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();			
			$this->result['message'] = 'Data sinkronisasi gagal dibuat';
		}else{
			$this->db->trans_commit();			
			$this->result['status'] = 1;		
			$this->result['message'] = 'Data sinkronisasi telah dibuat';
		}	
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
}
