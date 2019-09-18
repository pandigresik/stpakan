<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Csms extends MX_Controller{			
	public function __construct(){
		parent::__construct();
		$this->load->library('rest');
		$config = array('server'    => 'http://192.168.0.21/ws',
		//'api_key'         => 'Setec_Astronomy'
		//'api_name'        => 'X-API-KEY'
		//'http_user'       => 'username',
		//'http_pass'       => 'password',
		//'http_auth'       => 'basic',
		//'ssl_verify_peer' => TRUE,
		//'ssl_cainfo'      => '/certs/cert.pem'
		);
		$this->init($config);				
	}

	public function init($config = NULL){		
		if(!empty($config)){
			$this->rest->initialize($config);
		}

	}

	public function sendNotifikasi($pesan,$nomer){										
		$dataKirim = array(
			'nomer' => $nomer,
			'pesan' => $pesan
		);		
		$result = $this->rest->get('opSms/Stpakan/kirimNotifikasi',$dataKirim);				
		return $result;
	}

	public function kirim(){
		$hari_ini = date('d/m/Y');
		$nama_farm = 'Gondang';
	/*	$pesan = <<<SQL
	Pelanggan Yth, Pesanan dpt diambil tgl {$hari_ini} di Farm {$nama_farm}, dg menunjukkan data sbb :
	No. DO : GD18-G0001
	Pin    : 72734732
	Info   : 0312956000 ext. 1348
SQL;*/
		$parameterNoLpb = '000791/CJ/IX/2018';	
		$tglKirimLpb = '2019-09-19';
	$pesan = <<<EOT
Kadept PI Yth, PP Farm 	{$nama_farm} nomer {$parameterNoLpb} 
untuk tanggal kirim {$tglKirimLpb} 
yang dibuat pada 2019-09-17 telah dirilis.
EOT;

	$nomer = array('085733659400');
		//echo Modules::run('client/csms/sendNotifikasi',$pesan,$nomer);
		//echo 'tes';
		echo $this->sendNotifikasi($pesan,$nomer);
	}
	
}
