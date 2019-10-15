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

class RealisasiPanen extends REST_Controller
{
    private $decodedToken;
    private $table = 'REALISASI_PANEN';
    private $tableTara = 'tara';
    private $tableTimbang = 'timbang';

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
        $no_do = $this->get('no_do');
        if (!empty($kode_farm)) {
            $where['kode_farm'] = $kode_farm;
        }

        $content = $this->db->where(array('no_do' => $no_do))->get($this->table)->result_array();

        if ($content) {
            $output['status'] = 1;
            $output['content'] = $content;
            $output['message'] = 'total data '.count($content);
        }
        $this->response($output, 200);
    }

    public function save_post()
    {
        $output = array('status' => 0, 'content' => '', 'message' => 'Data DO tidak ditemukan');
        $no_do = $this->post('no_do');
        $data_realisasi = $this->post('realisasi'); /** array 1 dimensi */
        $data_timbang_tara = $this->post('timbang_tara'); /** array 2 dimensi */
        $data_timbang = $this->post('timbang'); /** array 2 dimensi */
        $content = $this->db->select('REALISASI_PANEN_DO.*')->select('datediff(day,kandang_siklus.tgl_doc_in,REALISASI_PANEN_DO.tgl_panen) as umur_panen', false)->where(array('no_do' => $no_do))->join('kandang_siklus', 'kandang_siklus.no_reg = REALISASI_PANEN_DO.no_reg')->get('REALISASI_PANEN_DO')->row_array();
        if ($content) {
            $this->db->trans_begin();
            /** insert ke realisasi_panen_do */
            $data_tambahan = array(
                'no_reg' => $content['NO_REG'],
                'no_sj' => $content['NO_SJ'],
                'tgl_panen' => $content['TGL_PANEN'],
                'tgl_datang' => null,
                'tgl_buat' => date('Y-m-d H:i:s'),
                'user_buat' => null,
                'umur_panen' => $content['umur_panen'],
            );
            $realisasi = array_merge($data_tambahan, $data_realisasi);
            $this->db->insert($this->table, $realisasi);
            /* insert ke tabel timbang_tara */
            foreach ($data_timbang_tara as $tara) {
                $this->db->insert($this->tableTara, $tara);
            }

            /* insert ke tabel timbang_tara */
            foreach ($data_timbang as $timbang) {
                $this->db->insert($this->tableTimbang, $timbang);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $output['message'] = 'Data DO '.$no_do.' gagal disimpan';
            } else {
                $this->db->trans_commit();
                $output['status'] = 1;
                $output['message'] = 'Data DO '.$no_do.' telah disimpan';
            }
        }
        $this->response($output, 200);
    }

    public function tes_get()
    {
        $output = array('status' => 0, 'content' => '', 'message' => 'Data DO tidak ditemukan');
        $no_do = 'BDY006689/18';
        $t = $this->db->select('REALISASI_PANEN_DO.*')->select('datediff(day,kandang_siklus.tgl_doc_in,REALISASI_PANEN_DO.tgl_panen) as umur_panen', false)->where(array('no_do' => $no_do))->join('kandang_siklus', 'kandang_siklus.no_reg = REALISASI_PANEN_DO.no_reg')->get('REALISASI_PANEN_DO')->row_array();
        $output['content'] = $t;
        $this->response($output, 200);
    }
}
