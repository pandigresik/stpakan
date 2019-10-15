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

class Fingerprint extends REST_Controller
{
    private $decodedToken;
    private $table = array(1 => 'FINGER_CODE', 2 => 'FINGER_FLEX_CODE');    
    public function __construct(){
        parent::__construct();
        $this->load->helper(array('authorization','jwt','stpakan'));      
    //    $this->checkToken();  
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
	
    public function search_get()
    {   
        $output = array('status' => 0,'content' => '','message' => 'Data tidak ditemukan');
        $kode_farm = $this->get('kode_farm');       
        $kode_pegawai = $this->get('kode_pegawai');       
        $version_finger = $this->get('version');
        if(empty($version_finger)){
            $version_finger = 2;
        }
        $where = array(
            'status_siklus' => 'O'
        );       
        if(!empty($kode_farm)){
            $where['kode_farm'] = $kode_farm;
        } 
        $list_siklus = $this->db->where($where)->select(array('kode_siklus'))->get_compiled_select('kandang_siklus');
        if(!empty($kode_pegawai)){
            $this->db->where(array('kode_pegawai' => $kode_pegawai));
        }
        $content = $this->db->get($this->table[$version_finger])->result_array();

        if($content){
            $output['status'] = 1;
            $output['content'] = $content;
            $output['message'] = 'total data '.count($content);
        }
        $this->response($output, 200);
    }
}