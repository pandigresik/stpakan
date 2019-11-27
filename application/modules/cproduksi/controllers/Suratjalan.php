<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Suratjalan extends Cproduksi_Controller{
	private $result = array(
		'status' => 0,
		'content' => '',
		'message' => ''		
	);
	
	/* parameter array adalah 
	 * $data = array(
			'awal' => '2015-08-01',
			'akhir' => '2015-08-05',
			'kodepj' => '1194C10-33'			
		);
	 * */
	public function data_surat_jalan($data = array()){
		$tweets = $this->rest->get('produksi/wpakanjadi/data_surat_jalan',$data,'json');
		if(!empty($tweets)){
			$this->result['status'] = 1;
			$this->result['content'] = $tweets;
		}
		return $this->result; 
	}
	
}
