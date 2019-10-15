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

class PanenDO extends REST_Controller
{
    private $decodedToken;
    private $table = 'REALISASI_PANEN_DO';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('authorization', 'jwt', 'stpakan'));
        //    $this->checkToken();
    }

    private function checkToken()
    {
        $headers = $this->input->request_headers();
        $result = false;
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken != false) {
                $this->decodedToken = $decodedToken;
                $result = true;
            }
        }

        if (!$result) {
            $this->response('Unauthorized', 401);

            return;
        }
    }

    public function search_get()
    {
        $output = array('status' => 0, 'content' => '', 'message' => 'Data tidak ditemukan');
        $kode_farm = $this->get('kode_farm');
        $no_reg = $this->get('no_reg');
        $tgl_panen = $this->get('tgl_panen');
        if (empty($tgl_panen)) {
            $tgl_panen = date('Y-m-d');
        }
        $where = array(
            'status_siklus' => 'O',
        );
        if (!empty($kode_farm)) {
            $where['kode_farm'] = $kode_farm;
        }
        $list_noreg = $this->db->where($where)->select(array('no_reg'))->get_compiled_select('kandang_siklus');
        if (!empty($no_reg)) {
            $this->db->where(array('no_reg' => $no_reg));
        }
        $content = $this->db->where(array('tgl_panen' => $tgl_panen))->where('no_reg in ('.$list_noreg.')')->get($this->table)->result_array();

        if ($content) {
            $output['status'] = 1;
            $output['content'] = $content;
            $output['message'] = 'total data '.count($content);
        }
        $this->response($output, 200);
    }
}
