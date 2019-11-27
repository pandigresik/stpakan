<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Csinkronisasi extends Cstpakan_Controller{
	private $idFarm;
	public function __construct() {
		parent::__construct ();
		$this->load->config('stpakan');
		$this->idFarm = $this->config->item('idFarm');
	}

	public function push_sinkron(){
		/* $idSinkronisasi didapat dari id di server utama */
		$result = array('status' => 0, 'message' => 'proses upload data gagal');
		$asal = $this->idFarm;
		$lanjut  = 1;
		while($lanjut){
			$idSinkronisasi = $this->rest->get('stpakan/wsinkronisasi/lastIdSinkronCenter/',array('farm'=>$asal));
			$idSinkron = empty($idSinkronisasi['content']) ? 0 : $idSinkronisasi['content']->id_ref;
			// echo $idSinkron; die();
			
			$data_kirim = Modules::run('sinkronisasi/sinkronisasi/pushData',$idSinkron);
			// echo '<pre>';print_r($data_kirim);die();
			if(!empty($data_kirim)){
				$kirim = $this->rest->post('stpakan/wsinkronisasi/sinkron_push/',array('data'=>$data_kirim));
				$result = $kirim;
			//	echo '<pre>';print_r($kirim);die();
			}
			else{
				$result['message'] = 'Tidak ada data yang harus diupload';
				$lanjut = 0;
			}

			if($result['status']){
				/* update waktu sinkronisasi di db local */
				$update_data = json_decode($result['content'],1);
				Modules::run('sinkronisasi/sinkronisasi/updateInfo',$update_data);
			}
			else{
				$lanjut = 0;
			}

		}

		return $result;
	}

	public function pull_sinkron(){
		$result = array('status' => 0, 'message' => 'proses download data gagal');
		/* cek di database local data terakhir yang sudah dipull */
		$asal = $this->idFarm;
		$lastId = Modules::run('sinkronisasi/sinkronisasi/lastIdSinkronLocal');
		
		if(empty($lastId)){
			$lastId = 0;
		}
		//echo $lastId; die();
		/* request data ke server apakah ada data yang harus diambil */
		$kirim = $this->rest->post('stpakan/wsinkronisasi/pullData/',array('id'=>$lastId,'farm'=>$asal));
		// print_r($kirim);die();
		if(!empty($kirim['status'])){
			$result['message'] = $kirim['message'];
			/* simpan data ke server local */
			$d = $this->xml2arr($kirim['content']);
			$t = Modules::run('sinkronisasi/sinkronisasi/simpanData',$d['item']);
			if($t['status']){
				$result['status'] = 1;
				$result['message'] = 'terupdate';
				$update_data = json_decode($t['content'],1);
				/* update tanggal sinkron di server utama */
				$s = $this->rest->post('stpakan/wsinkronisasi/updateInfo/',array('waktuSinkron'=>$update_data));
			}
		}
		else{
			if(is_array($kirim)){
				$result['message'] = 'Data di local sudah sama dengan server farm ';
			//	log_message('error',implode(' ',$kirim));
			}else{
				log_message('error',$kirim);
			}

		}
		return $result;
	}
	/* sinkronisasi dilakukan dari pull data lalu push data */
	public function sinkron($pesan = NULL){
		$result = array();
		$result['Proses download data'] = $this->pull_sinkron();
		$result['Proses upload data'] = $this->push_sinkron();

		if(empty($pesan)) {
			echo json_encode($result);
		}
	}
	/* sinkron cadangan */
	public function sinkron2($pesan = NULL){
		$config = $this->config->item('ws_stpakan2');
		$this->init($config);
		
		$result = array();
		$result['Proses download data'] = $this->pull_sinkron();
		$result['Proses upload data'] = $this->push_sinkron();
	
		if(empty($pesan)) {
			echo json_encode($result);
		}
	}
	private function xml2arr($xml){
	//	$simple = simplexml_load_string($xml);
		return json_decode(json_encode($xml) , 1);
	}
}
