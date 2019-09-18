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

class VerifikasiSJdoc extends REST_Controller
{
    private $decodedToken;
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
	
	public function checksjdoc_post(){
		$output 		= array('status' => 0,'content' => '','message' => '');
		$data 			= json_decode($this->post('data'),1); 
		$no_sj_check	= $this->db->where(array('NO_SJ' => $data['no_sj_doc']))->get('BAP_DOC_SJ')->result();
		$param_kandang	= array(
				'KODE_FARM'			=>	$data['farm'],
				'KODE_KANDANG'		=>	$data['kandang'],
				'STATUS_SIKLUS'		=> 'O'
				//'STATUS_KANDANG'	=>	'A'
			);
		//$kandang_aktif	= $this->db->where($param_kandang)->get('M_KANDANG')->result();
		$kandang_aktif		= $this->db->where($param_kandang)->get('KANDANG_SIKLUS')->result();
		if((count($no_sj_check)==0) && (count($kandang_aktif)>0)){
			$hatchery	= $this->db->select('NAMA_HATCHERY')->where(array('KODE_HATCHERY'=>$data['hatchery']))->get('M_HATCHERY')->result();
			$farm		= $this->db->select('NAMA_FARM')->where(array('KODE_FARM'=>$data['farm']))->get('M_FARM')->result();
			$output 	= array(
				'status'	=> 1, 
				'content'	=> '', 
				'message'	=> '',
				'nama_hc'	=> $hatchery[0]->NAMA_HATCHERY,
				'nama_farm'	=> $farm[0]->NAMA_FARM
			);
		}else{
			if(count($no_sj_check)>0){
				$message	= 'No.SJ sudah pernah disimpan<br>Mohon melakukan scan ulang.';
			}elseif(count($kandang_aktif)==0){
				$message	= 'Data SJ DOC tidak sesuai dengan data kandang.<br>Mohon melakukan scan ulang.';
			}
			$output = array(
				'status'	=> 0, 
				'content'	=> '',
				'message'	=> $message
			);
		}
		$this->response($output, 200);	
	}
	
	public function insertSJdoc_post(){
		$data 	= json_decode($this->post('data'),1); 
		$output = array(
				'status'	=> 0, 
				'content'	=> '',
				'message'	=> ''
			);
		$param_siklus = array(
				'KODE_FARM'		=> $data['kode_farm'],
				'KODE_KANDANG'	=> $data['kode_kandang'],
				'STATUS_SIKLUS'	=> 'O'
			);	
		$noreg = $this->db->select('NO_REG')->where($param_siklus)->get('KANDANG_SIKLUS')->result();
		$data_insert = array(
				'NO_REG'			=>	$noreg[0]->NO_REG,
				'NO_SJ'				=>	$data['no_sj'],
				'KODE_HATCHERY'		=>	$data['kode_hatchery'],
				'TGL_TERIMA'		=>	date('Y-m-d H:i:s'),
				'TGL_GENERATE'		=>	$data['tgl_buat'],
				'NOPOL_QRCODE'		=>	$data['nopol_qrcode'],
				'NOPOL_SCAN'		=>	$data['nopol_scan'],
				'KETERANGAN_NOPOL'	=>	$data['ket_beda_nopol'],
				'JML_BOX'			=>	$data['jml_box'],
				'JML_EKOR'			=>	$data['jml_ekor'],
				'SOPIR'				=>	$data['sopir'],
				'USER_BUAT'			=>	$this->decodedToken->kode_user
			);	
			$image = $data['image'];
			$sekarang  = (new \DateTime())->format('Y-m-d H:i:s');
			//$folder_upload = APPPATH.'third_party/ALPR/images';
			//$path_image_upload = $folder_upload.'/plat_nomer.jpg';      
			$path_baru = 'file_upload/plat_nomer';
			if(!file_exists($path_baru)){
				mkdir($path_baru);
			}
			
			$path_baru_photo = $path_baru.'/'.date('YmdHis').'.jpg'; 
			if(file_put_contents($path_baru_photo,base64_decode($image))){
				$data_insert['FOTO'] = $path_baru_photo;
			}	
		$insert = $this->db->insert('BAP_DOC_SJ', $data_insert);
		if($insert){
			$output['status'] = '1';
		}
		$this->response($output, 200);
	}
	
}