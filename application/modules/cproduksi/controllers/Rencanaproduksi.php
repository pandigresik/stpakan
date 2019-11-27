<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rencanaproduksi extends Cproduksi_Controller{
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
	public function listrencanaproduksi($data = array()){
/*		$data = array(
		 'awal' => '2016-02-01',
		 'akhir' => '2016-03-05',
		 'kodepj' => '1127-11E12'
	 );*/

		$tweets = $this->rest->get('produksi/whrencanaproduksi/listproduksi',$data,'json');

		if(!empty($tweets)){
			$this->result['status'] = 1;
			$this->result['content'] = $tweets;
		}
		else{
				$this->result['status'] = 0;
				$this->result['content'] = array();
		}
		//print_r($this->result);
		return $this->result;
	}

}
