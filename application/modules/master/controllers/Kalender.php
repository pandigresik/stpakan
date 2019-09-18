<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Kalender extends MX_Controller{
	protected $idFarm;
	protected $serverDirektur;
	protected $serverUtama;
	public function __construct(){
		parent::__construct();
		$this->load->model("m_kalender");
		$this->load->model('sinkronisasi/m_sinkronisasi','sinc');
		$this->load->model('sinkronisasi/m_detail_sinkronisasi','dsinc');
		$this->load->module('sinkronisasi/sinkronisasi','sinkronisasi');
		$this->load->config('stpakan');
		$this->serverDirektur = $this->config->item('serverDirektur');
		$this->serverUtama = $this->config->item('serverUtama');
		$this->idFarm = $this->config->item('idFarm');
	}

	public function getDayoffs(){
		$lastDay = $this->m_kalender->getLastDay()->row_array();
		$date = empty($lastDay['TANGGAL']) ? date('Y-m-d') : $lastDay['TANGGAL'];

		$df = $this->m_kalender->getDayoffs($date)->result_array();
		if(!empty($df)){
			$this->db->trans_begin();
			foreach($df as $d){
				$tmp = array('TANGGAL' => $d['dayoff'],'KETERANGAN' => 'HARI LIBUR NASIONAL');
				$this->m_kalender->insert($tmp);
			}
			$sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_kalender' as tabel
					,'{"tanggal" : "'+cast(tanggal as varchar(30))+'"}' as kunci
					, 0 status_identity
				from m_kalender
				where tanggal > :key2
SQL;
			$datatransaksi = array(
							'transaksi' => 'insert_kalender',
							'asal' => $this->idFarm,
							'tujuan' => '*',
							'aksi' => 'PUSH'
				);
			$dataKey = array(':key2' => $date);
			$this->sinkronisasi->insert($datatransaksi,$dataKey,$sqlDetail);
			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				echo 'gagal.....';
				log_message('error','isi tabel sinkronisasi aksi insert_kalender gagal pada '.date('Y-m-d H:i:s'));
			}
			else{
				echo 'success.....';
				$this->db->trans_commit();
			}
		}
		else{
			echo 'Sudah sama dengan server ......';
		}
	}
}
