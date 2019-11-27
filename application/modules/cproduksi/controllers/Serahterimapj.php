<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Serahterimapj extends Cproduksi_Controller{
	private $result = array(
		'status' => 0,
		'content' => '',
		'message' => ''
	);

	/* parameter array adalah
	 * $data = array(
			'rp' => 'RP201505032',
			'kodepj' => '1194C10-33'
		);
	 * */
	public function kavlingpakanjadi($data = array()){
		$tweets = $this->rest->get('produksi/whrencanaproduksi/kavlingpakanjadi',$data,'json');
		if(!empty($tweets)){
			$this->result['status'] = 1;
			$this->result['content'] = $tweets;
		}else{
			$this->result['status'] = 0;
			$this->result['content'] = '';
		}
		return $this->result;
	}

	public function pakanjadi($data = array()){
		$tweets = $this->rest->get('produksi/wpakanjadi/listproduksi',$data,'json');
		if(!empty($tweets)){
			$this->result['status'] = 1;
			$this->result['content'] = $tweets;
		}
		else{
			$this->result['status'] = 0;
			$this->result['content'] = '';
		}
		return $this->result;
	}
}
