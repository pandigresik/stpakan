<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Changes:
 * 1. This project contains .htaccess file for windows machine.
 *    Please update as per your requirements.
 *    Samples (Win/Linux): http://stackoverflow.com/questions/28525870/removing-index-php-from-url-in-codeigniter-on-mandriva
 *
 * 2. Change 'encryption_key' in application\config\config.php
 *    Link for encryption_key: http://jeffreybarke.net/tools/codeigniter-encryption-key-generator/
 * 
 * 3. Change 'jwt_key' in application\config\jwt.php
 *
 */

class VerifikasiDO extends REST_Controller
{
    private $decodedToken;
    private $nextStep = array(
        '1' => 'Verifikasi nopol',
        '2' => 'Verifikasi pin',
        '3' => 'Verifikasi success' 
    );
    public function __construct(){
        parent::__construct();
        $this->load->helper(array('authorization','jwt','stpakan'));      
        $this->checkToken();  
    }
    
    private function checkToken(){
        $headers = $this->input->request_headers();
        $result = false;        
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken != false) {                    
                $this->decodedToken = $decodedToken;
                $result = true;
            }
        }

        if(!$result){
            $this->response("Unauthorized", 401);
            return;
        }
    
        
    }

    public function check_post()
	{
		$bulan = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des');	
        $headers = $this->input->request_headers();               
        //$output = array('status' => 0,'content' => '','message' => 'Data tidak ditemukan');
        $output = array('status' => 0,'content' => '','message' => 'Data DO ditolak. Mohon melakukan scan ulang');
        $data = json_decode($this->post('data'),1);                
        $suratJalan = $this->db->where($data)->where('tgl_verifikasi')->get('surat_jalan')->row(); 
	
		if(!empty($suratJalan)){
			/*$exp = explode(" ", $suratJalan->tgl_buat);
			$getmonth = array_search($exp[1], $bulan)+1;
			$date = $exp[2].'-'.$getmonth.'-'.$exp[0].' 00:00:00';*/
			$begin = new DateTime($suratJalan->tgl_buat);
			$end = new DateTime('now');
			$interval = $end->diff($begin);
			$timeReamining = $interval->format('%a total days');
			
			if($timeReamining <= 1){
				$output['status'] = 1;
				//$suratJalan->tgl_buat = tglIndonesia($suratJalan->tgl_buat,'-',' ');
				//$output['content'] = json_encode(array('do' => $suratJalan));
				if($suratJalan->tgl_realisasi == NULL || $suratJalan->tgl_realisasi == ''){
					$suratJalan->tgl_realisasi = tglIndonesia((new \DateTime())->format('Y-m-d H:i:s'),'-',' ');
				}else{
					$suratJalan->tgl_realisasi = tglIndonesia($suratJalan->tgl_realisasi,'-',' ');
				}
				$suratJalan->tgl_buat = tglIndonesia($suratJalan->tgl_buat,'-',' ');
				$output['content'] = json_encode(array('do' => $suratJalan));
			}else{
				$output['message'] = 'Data DO ditolak melebihi batas timeline';
			}
        }
        //log_message("error",json_encode($output));
        $this->response($output, 200);
    }

	public function checkQR_post()
    {   
        $headers = $this->input->request_headers();  	
        $output = array('status' => 0,'content' => '','message' => 'Data tidak ditemukan');
        $data = json_decode($this->post('data'),1);                
        $cekData = $this->db->where($data)->where('tgl_verifikasi')->get('surat_jalan')->row();        
        if(!empty($cekData)){
            //if(isset($data['kode_verifikasi'])){
                $kode_pegawai = $this->decodedToken->kode_user;
                $sekarang  = (new \DateTime())->format('Y-m-d H:i:s');      
                $update = $this->db->where($data)->where('tgl_verifikasi')->update('surat_jalan',array('tgl_verifikasi' => $sekarang, 'user_verifikasi' => $kode_pegawai));                
                if($update){  
                    $output['message'] = 'DO berhasil diverifikasi';
                }
            //}
            $output['status'] = 1;
        }
        //$output['content'] = $this->nextStep[count($data)];
        $this->response($output, 200);
    }
	
    public function verify_post()
    {   
        $output = array('status' => 0,'content' => '','message' => 'Data tidak ditemukan');
        $data = json_decode($this->post('data'),1);              
        
        $kode_pegawai = $this->decodedToken->kode_user;
        $sekarang  = (new \DateTime())->format('Y-m-d H:i:s');      
        $update = $this->db->where($data)->where('tgl_verifikasi')->update('surat_jalan',array('tgl_verifikasi' => $sekarang, 'user_verifikasi' => $kode_pegawai));        
        
        if($update){
            $output['status'] = 1;
            $output['message'] = 'DO berhasil diverifikasi';
        }
        $this->response($output, 200);
    }
}