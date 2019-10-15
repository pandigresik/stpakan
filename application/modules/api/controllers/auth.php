<?php

defined('BASEPATH') or exit('No direct script access allowed');

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

class Auth extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('authorization', 'jwt'));
        $this->load->model('user/m_user');
    }

    /**
     * URL: http://localhost/CodeIgniter-JWT-Sample/auth/token
     * Method: GET.
     */
    public function token_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');
        $tokenData = array();
        $output = array('status' => 0, 'content' => '', 'message' => '');
        $user = $this->m_user->login($username, $password);
        if (!empty($user)) {
            $tokenData['kode_user'] = $user['KODE_PEGAWAI'];
            $tokenData['level_user'] = $user['GRUP_PEGAWAI'];
            $tokenData['kode_farm'] = $user['KODE_FARM'];
            $output['content'] = AUTHORIZATION::generateToken($tokenData);
            $output['role'] = $user['GRUP_PEGAWAI'];
			$output['access_noreg'] = array_column($this->listNoreg($user['KODE_PEGAWAI']),'no_reg'); 
            $output['status'] = 1;
        } else {
            $output['message'] = 'Username atau password salah';
        }
        $this->response($output, 200);
    }

    public function tokenSCF_post()
    {
        $username = $this->post('username');
        $password = $this->post('password');

        $tokenData = array();
        $output = array('status' => 0, 'content' => '', 'message' => '');
        $user = $this->m_user->login($username, $password);
        if (!empty($user)) {
            $tokenData['kode_user'] = $user['KODE_PEGAWAI'];
            $tokenData['level_user'] = $user['GRUP_PEGAWAI'];
            $tokenData['kode_farm'] = $user['KODE_FARM'];
            if ($tokenData['level_user'] == 'SCF') {
                $output['farm'] = $user['KODE_FARM'];
                $output['content'] = AUTHORIZATION::generateToken($tokenData);
                $output['status'] = 1;
            } else {
                $output['message'] = 'Anda bukan security';
            }
        } else {
            $output['message'] = 'Username atau password salah';
        }
        $this->response($output, 200);
    }

    private function listNoreg($kode_pegawai){
	return $this->db->select(['mpp.no_reg'])
	->join('kandang_siklus ks','ks.no_reg = mpp.no_reg and ks.status_siklus = \'O\'','inner')
	->where(['mpp.status' => 'A'])	
	->where('mpp.koordinator = \''.$kode_pegawai.'\' or mpp.pengawas = \''.$kode_pegawai.'\' or mpp.operator = \''.$kode_pegawai.'\'')
	->get('M_PLOTING_PELAKSANA mpp')
	->result_array();
    }
}
