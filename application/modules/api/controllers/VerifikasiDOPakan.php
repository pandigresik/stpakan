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

class VerifikasiDOPakan extends REST_Controller
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
        $headers = $this->input->request_headers();                       
        $output = array('status' => 0,'content' => '','message' => 'Data DO ditolak. Mohon melakukan scan ulang');        
        $data = json_decode($this->post('data'),1);      
        $nomerDO = $data['no_do'];          
        $prefixDo = substr($nomerDO,0,2);
        switch($prefixDo){
            case 'DO':
                $do = $this->db->where($data)->get('do')->row(); 
                break;
            case 'RL':
                $do = $this->db->where(array('no_retur'))->get('retur_farm')->row(); 
                break;
            default:
                break;    
        }
        
	
		if(!empty($do)){			
            $output['content'] = json_encode(array('do' => $do));
            $output['status'] = 1;					
        }
        //log_message("error",json_encode($output));
        $this->response($output, 200);
    }
	
    public function verify_post()
    {   
        $output = array('status' => 0,'content' => '','message' => 'Data tidak ditemukan');
        $data = json_decode($this->post('data'),1); 
        
        $sopir = $data['nama_sopir'];
        $nopol = $data['nopol'];                     
        $image = $data['image'];      
        $kode_pegawai = $this->decodedToken->kode_user;
        $kode_farm = $this->decodedToken->kode_farm;
        $sekarang  = (new \DateTime())->format('Y-m-d H:i:s');
        //$folder_upload = APPPATH.'third_party/ALPR/images';
        //$path_image_upload = $folder_upload.'/plat_nomer.jpg';      
        $path_baru = 'file_upload/plat_nomer';
        if(!file_exists($path_baru)){
            mkdir($path_baru);
        }
        
        $nopol = trim(preg_replace('/\s+/', '', $nopol));
        $insert = array(
            'kode_farm' => $kode_farm,
            'nopol' => strtoupper($nopol),
            'nama_sopir' => strtoupper($sopir),
            'user_verifikasi' => $kode_pegawai,
            'tgl_verifikasi' => $sekarang,
        );
        $path_baru_photo = $path_baru.'/'.date('YmdHis').'.jpg'; 
        if(file_put_contents($path_baru_photo,base64_decode($image))){
			$insert['photo'] = $path_baru_photo;
		}
        $insertData = 0;
        foreach($data['no_do'] as $do){
            $action = 0;
            $prefixDo = substr($do,0,2);
            switch($prefixDo){
                case 'DO':                    
                    $insert['asal'] = 'FM';
                    break;
                case 'RL':
                    $do = $this->db->where(array('no_retur'))->get('retur_farm')->row(); 
                    $insert['asal'] = $do['FARM_ASAL'];
                    break;
                default:
                    break;    
            }
            $ada = $this->db->where(array('no_do' => $do))->get('Verifikasi_DO_Pakan')->row();
            if(!empty($ada)){
                $action = $this->db->where(array('no_do' => $do))->update('Verifikasi_DO_Pakan',$insert);
            }else{
                $insert['no_do'] = $do;
                $action = $this->db->insert('Verifikasi_DO_Pakan',$insert);
            }            
            if($action){
                $insertData++;
            }
        }
                        
        $output['status'] = 1;
        $output['message'] = 'DO berhasil diverifikasi';        
        $this->response($output, 200);
    }
}
