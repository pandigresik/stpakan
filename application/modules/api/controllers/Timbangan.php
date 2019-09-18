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

class Timbangan extends REST_Controller
{   
    protected $result = array('status' => 0, 'message' => '', 'content' => '');
    public function __construct(){
        parent::__construct();
        $this->load->config('timbangan');
    }

    public function timbang_get(){
        $line = '';
        $filename = $this->config->item('filetimbang');        
        $message = '';
        if (file_exists($filename)) {
            $fh = fopen($filename,'r');
            $line = fgets($fh);
            fclose($fh);
            unlink($filename);
            $this->result['content'] = $line;                   
            $this->result['message'] = $message;
            $this->result['status'] = 1;
        }else{
          $this->result['message'] = 'Hasil penimbangan tidak ditemukan ';
        }
        
        $this->response($this->result, 200);        
    }

    public function settimbang_get($nilaiTimbang){
        $line = '';
        $filename = $this->config->item('filetimbang');        
        $message = '';
        
        $fh = fopen($filename,'w');
        fwrite($fh,$nilaiTimbang);
        fclose($fh);            
        $this->result['content'] = $line;                   
        $this->result['message'] = $message;
        $this->result['status'] = 1;       
        
        $this->response($this->result, 200);        
    }

}