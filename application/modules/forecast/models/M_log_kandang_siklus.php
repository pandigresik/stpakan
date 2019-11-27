<?php
class M_log_kandang_siklus extends CI_Model{
	protected $_table; 
	private $_user;
	public function __construct(){
		parent::__construct();
		$this->_table = 'log_kandang_siklus';
		$this->_user = $this->session->userdata('kode_user');
	}
	public function simpan($dataKandang,$dataFarm,$rilis = false){
		$approve = ($rilis) ? 'R' : 'D';
		
		foreach($dataKandang as $row){
			$log = array();
			$log['no_reg'] = $row['no_reg']; //$dataFarm['kodeFarm'].'-'.$dataFarm['periodeSiklus'].'/'.$row['kandang'];
			$log['no_urut'] = 1;
			$log['status_approve'] = $approve;
			$log['user_buat'] = $this->_user;
			$this->db->insert($this->_table,$log);
		}
	}
	
	
	public function approve($noreg = array(),$text){
		
		foreach($noreg as $n){
			$no_urut = $this->db->select_max('no_urut','max')->where('no_reg',$n)->get($this->_table)->row();
			$data = array(
				'no_reg' => $n,
				'no_urut' => $no_urut->max + 1,
				'status_approve' => $text,
				'tgl_buat' => date('Y-m-d H:i:s'),
				'user_buat' => $this->_user,	 
			);
			$this->db->insert($this->_table,$data);
		}
	}
}