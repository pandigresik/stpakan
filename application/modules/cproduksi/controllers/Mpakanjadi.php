<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mpakanjadi extends Cproduksi_Controller{
	protected $idFarm;
	protected $serverDirektur;
	protected $serverUtama;
	private $result = array(
		'status' => 0,
		'content' => '',
		'message' => ''
	);

	public function __construct() {
		parent::__construct ();
		$this->load->model('sinkronisasi/m_sinkronisasi','sinc');
		$this->load->model('sinkronisasi/m_detail_sinkronisasi','dsinc');
		$this->load->module('sinkronisasi/sinkronisasi','sinkronisasi');
		$this->load->config('stpakan');
		$this->serverDirektur = $this->config->item('serverDirektur');
		$this->serverUtama = $this->config->item('serverUtama');
		$this->idFarm = $this->config->item('idFarm');
	}

	public function listactivepakanjadi($data = array()){
/*		$data = array(
		 'awal' => '2016-02-01',
		 'akhir' => '2016-03-05',
		 'kodepj' => '1127-11E12'
	 );*/

		$tweets = $this->rest->get('produksi/wmpakanjadi/pakanjadiactive',$data,'json');

		if(!empty($tweets)){
			$this->result['status'] = 1;
			$this->result['content'] = $tweets;
		}
		else{
				$this->result['status'] = 0;
				$this->result['content'] = array();
		}
		return $this->result;
	}

	public function listpakanjaditerbaru($data = array()){
/*		$data = array(
		 'awal' => '2016-02-01',
		 'akhir' => '2016-03-05',
		 'kodepj' => '1127-11E12'
	 );*/
	 	/* cari terakhir kali melakukan update */
		$update_terakhir = $this->db->select_max('tgl_update')->get('m_barang')->row_array();

	 	$data = array(
			'update_terakhir' => $update_terakhir['tgl_update']
		);
		$tweets = $this->rest->get('produksi/wmpakanjadi/pakanjaditerbaru',$data,'json');
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
/* ini yang akan dijalankan melalui cronjob secara otomatis setiap hari */
	public function getmpakanjaditerbaru(){
		$data = $this->listpakanjaditerbaru();
		$tglServer = Modules::run('home/home/getDateServer');
		/* simpan ke database */
		if($data['status']){
			$this->db->trans_begin();
			foreach($data['content']->mpakanjadis as $d){
				$tmp = array(
					'kode_barang' => $d->kodepj
					,'alias' => $d->namakomersial
					,'nama_barang' => $d->namapj
					,'jenis_barang' => $d->kelompok
					,'grup_barang' => $d->kodekelompok
					,'uom' => 'SAK'
					,'bentuk_barang' => strtoupper(substr($d->bentukpj,0,1))
					,'tipe_barang' => 'I'
					,'status_barang' => ($d->status) ? 'A' : 'N'
				//	,'tgl_buat' => $tglServer->saatini
					,'user_buat' => 'SYSADMIN'
					,'tgl_update' => $tglServer->saatini
				);
				$sudahAda = $this->db->select('kode_barang')->where(array('kode_barang'=>$d->kodepj))->get('m_barang')->result_array();
				if(!empty($sudahAda)){
						$this->db->update('m_barang',$tmp);
				}
				else{
					$tmp['tgl_buat'] = $tglServer->saatini;
					$this->db->insert('m_barang',$tmp);
				}
			}
			$sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_barang' as tabel
					,'{"tgl_update" : "'+cast(mb.tgl_update as varchar(30))+'"}' as kunci
					, 0 status_identity
				from m_barang mb
				where tgl_update = :key2
SQL;
			$datatransaksi = array(
							'transaksi' => 'update_master_barang',
							'asal' => $this->idFarm,
							'tujuan' => '*',
							'aksi' => 'PUSH'
				);
			$dataKey = array(':key2' => $tglServer->saatini );
			$this->sinkronisasi->insert($datatransaksi,$dataKey,$sqlDetail);
			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				echo 'gagal.....';
				log_message('error','isi tabel sinkronisasi aksi update_master_barang gagal pada '.date('Y-m-d H:i:s'));
			}
			else{
				echo 'success.....';
				$this->db->trans_commit();
			}
		}
		else{
			echo 'sudah sama dengan server .....';
		}
	}
	public function getmpakanjadi(){
		$data = $this->listactivepakanjadi();
		$tglServer = Modules::run('home/home/getDateServer');
		/* simpan ke database */
		if($data['status']){
			$this->db->trans_begin();
			foreach($data['content']->mpakanjadis as $d){
				$tmp = array(
					'kode_barang' => $d->kodepj
					,'alias' => $d->namakomersial
					,'nama_barang' => $d->namapj
					,'jenis_barang' => $d->kelompok
					,'grup_barang' => $d->kodekelompok
					,'uom' => 'SAK'
					,'bentuk_barang' => strtoupper(substr($d->bentukpj,0,1))
					,'tipe_barang' => 'I'
					,'status_barang' => ($d->status) ? 'A' : 'N'
					,'tgl_buat' => $tglServer->saatini
					,'user_buat' => 'SYSADMIN'
					,'tgl_update' => $tglServer->saatini
				);
				$this->db->insert('m_barang',$tmp);
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
