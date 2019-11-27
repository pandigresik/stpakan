<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
    /*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Panen_do extends MY_Controller
{
    protected $kode_farm;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('stpakan');
        $this->kode_farm = $this->session->userdata('kode_farm');
    }

    public function index()
    {
        $this->load->view('sinkronisasi/panen_do/index');
    }

    public function ambilDO()
    {
        $tglPanen = $this->input->get('tglPanen');
        $this->load->config('serverws');
        $config = $this->config->item('ws_stpakan');
        $this->_ambilDO($tglPanen, $config);
    }

    public function ambilDO2()
    {
        $tglPanen = $this->input->get('tglPanen');
        $this->load->config('serverws');
        $config = $this->config->item('ws_stpakan2');
        $this->_ambilDO($tglPanen, $config);
    }

    private function _ambilDO($tglPanen, $config)
    {
        $this->load->library('rest');
        $this->rest->initialize($config);
        $response = $this->rest->get('stpakan/ayamhidup/dopanen', array('tglPanen' => $tglPanen), 'json');
        $tmp = json_decode(json_encode($response), 1);
        $data = isset($tmp['content']) ? $tmp['content'] : array();
        $data['kode_farm'] = $this->kode_farm;
        $this->load->view('sinkronisasi/panen_do/listDO', $data);
    }

    public function simpanDO()
    {
        $data = $this->input->post('data');
        $content = array();
        if (!empty($data)) {
            foreach ($data as $kodeFarm => $kf) {
                $content[$kodeFarm] = array();
                foreach ($kf as $do) {
                    $sudahAda = $this->db->where(array('no_do' => $do['no_do']))->get('REALISASI_PANEN_DO')->num_rows();
                    if (!$sudahAda) {
                        $do['kode_pelanggan'] = 'RPA';
                        $this->db->set('tgl_buat', 'getdate()', false);
                        $this->db->insert('REALISASI_PANEN_DO', $do);
                        array_push($content[$kodeFarm], $do['no_do']);
                    }
                }
            }
        }
        $this->result['status'] = 1;
        $this->result['content'] = $content;
        $this->result['message'] = 'DO sudah disimpan';

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));
    }
}
