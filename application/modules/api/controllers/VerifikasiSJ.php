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

class VerifikasiSJ extends REST_Controller
{
    private $decodedToken;
    public function __construct(){
        parent::__construct();
        $this->load->helper(array('authorization','jwt'));      
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
        $output = array('status' => 0,'content' => '','message' => 'Data tidak ditemukan');
        $data = json_decode($this->post('data'),1);                
        $suratJalan = $this->db->where($data)->where('tgl_verifikasi_security')->get('surat_jalan')->row();        
        if(!empty($suratJalan)){
            /* ambil datanya sekalian */
            $suratJalanD = $this->db
                                ->select('m_barang.nama_barang,surat_jalan_d.jumlah')
                                ->where(array('no_sj' => $suratJalan->no_sj))
                                ->join('m_barang','m_barang.kode_barang = surat_jalan_d.kode_barang')
                                ->get('surat_jalan_d')
                                ->result();
            $output['status'] = 1;
            $output['content'] = json_encode(array('sj' => $suratJalan, 'detail' => $suratJalanD));

        }
        //log_message("error",json_encode($output));
        $this->response($output, 200);
    }


    public function verify_post()
    {   
        $output = array('status' => 0,'content' => '','message' => 'Data tidak ditemukan');
        $data = json_decode($this->post('data'),1);              
        $usernameVerifikasi = $data['user_verifikasi'];
        unset($data['user_verifikasi']);        
        $sysUser = $this->db->where(array('username' => $usernameVerifikasi))->get('m_pegawai')->row();        
        if(empty($sysUser)){
            $output['message'] = 'Username '.$usernameVerifikasi.' tidak ditemukan';
            $this->response($output, 200);
            return;
        }
        $kode_pegawai = $sysUser->KODE_PEGAWAI;        
        $sekarang  = (new \DateTime())->format('Y-m-d H:i:s');      
        $update = $this->db->where($data)->where('tgl_verifikasi_security')->update('surat_jalan',array('tgl_verifikasi_security' => $sekarang, 'user_verifikasi_security' => $kode_pegawai));        
        
        if($update){
            $output['status'] = 1;
            $output['message'] = 'SJ berhasil diverifikasi';
        }
        $this->response($output, 200);
    }
}