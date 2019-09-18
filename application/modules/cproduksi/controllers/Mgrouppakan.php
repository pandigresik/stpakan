<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mgrouppakan extends Cproduksi_Controller{
	private $result = array(
		'status' => 0,
		'content' => '',
		'message' => ''
	);

	public function listgrouppakan($data = array()){
/*		$data = array(
		 'awal' => '2016-02-01',
		 'akhir' => '2016-03-05',
		 'kodepj' => '1127-11E12'
	 );*/

		$tweets = $this->rest->get('produksi/wgrouppakan/grouppakan',$data,'json');

		if(!empty($tweets)){
			$this->result['status'] = 1;
			$this->result['content'] = $tweets;
		}
		else{
				$this->result['status'] = 0;
				$this->result['content'] = array();
		}
//		print_r($this->result);
		return $this->result;
	}

	public function getgrouppakan(){
		$data = $this->listgrouppakan();
		$tglServer = Modules::run('home/home/getDateServer');
		/* simpan ke database */
		if($data['status']){
			$this->db->trans_begin();
			foreach($data['content']->mgrouppakans as $d){
				$tmp = array(
					'grup_barang' => $d->kodekelompok
					,'deskripsi' => $d->namagrouppakan
				);
				$this->db->insert('m_grup_barang',$tmp);
			}
			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
			}
			else{
				$this->db->trans_commit();
			}
		}
	}

}
