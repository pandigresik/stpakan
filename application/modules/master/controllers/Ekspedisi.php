<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class ekspedisi extends MY_Controller {

    protected $_tipe_kendaraan;
    protected $_kota;
    protected $_user;
    protected $_farm;
    protected $_level_user;

    function __construct() {
        parent::__construct();
        $this->load->model('m_ekspedisi');
        $this->load->model('m_farm');
        $this->_tipe_kendaraan = array(
            'TB' => 'TRUK BAK',
            'TE' => 'TRUK ENGKEL',
            'TG' => 'TRUK GANDENG'
        );
        $this->_kota = array(
            'LWG' => 'LAWANG',
            'LMJ' => 'LUMAJANG',
            'PSR' => 'PASURUAN',
            'MLG' => 'MALANG'
        );
        $this->_user = $this->session->userdata('kode_user');
        $this->_level_user = $this->session->userdata ( 'level_user' );
        $this->_farm = ($this->_level_user == 'BPM') ? NULL : $this->session->userdata('kode_farm');
        #$this->_farm = $this->session->userdata('kode_farm');
    }

    public function get_last_tpkendaraan()
    {
        $tp_kendaraan = $this->input->post('tp_kendaraan');

        $qry = "SELECT TOP 1* FROM M_EKPEDISI_VEHICLE_NEW mev WHERE mev.TIPE_KENDARAAN = '". $tp_kendaraan ."' ORDER BY mev.KODE_EKSPEDISI DESC";

        echo json_encode($this->m_ekspedisi->queries($qry)->fetch(PDO::FETCH_ASSOC));
        exit;
    }

    public function get_kode_farm()
    {

        $kfavail = $this->input->post('kfavail');
        $strwherenotin = "";
        foreach ($kfavail as $key => $value) {
            $sp = '';
            if (!empty($kfavail[$key+1])) {
                $sp = ', ';
            }

            $strwherenotin .= "'" . $value . "'" . $sp;
        }

        $qry = 'SELECT * FROM M_FARM mf WHERE mf.KODE_FARM NOT IN(' . $strwherenotin . ')';

        echo json_encode($this->m_farm->queries($qry));
        exit;
    }

    public function index() {
        $data ['data_tipe_kendaraan'] = $this->_tipe_kendaraan;
        //$data ['data_kota'] = $this->_kota;
        $data ['data_kota'] = $this->m_ekspedisi->get_all_kota();
        $data ['gen_kode_ekspedisi'] = $this->m_ekspedisi->generate_kode_ekspedisi();
        $this->load->view("ekspedisi/ekspedisi_list", $data);
    }

    function get_pagination() {
        $offset = 8;
        $page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
        $is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

        $nama_ekspedisi = (($this->input->post("nama_ekspedisi")) and $is_search == true) ? $this->input->post("nama_ekspedisi") : null;
        $alamat = (($this->input->post("alamat")) and $is_search == true) ? $this->input->post("alamat") : null;
        $kota = (($this->input->post("kota")) and $is_search == true) ? $this->input->post("kota") : null;
        $jumlah_kendaraan = (($this->input->post("jumlah_kendaraan")) and ($this->input->post("jumlah_kendaraan")) != "" and $is_search == true) ? $this->input->post("jumlah_kendaraan") : null;

        $data_ekspedisi_all = $this->m_ekspedisi->get_ekspedisi(NULL, NULL, $nama_ekspedisi, $alamat, $kota, $jumlah_kendaraan,$this->_farm);

        $data_ekspedisi = $this->m_ekspedisi->get_ekspedisi(($page_number * $offset), ($page_number + 1) * $offset, $nama_ekspedisi, $alamat, $kota, $jumlah_kendaraan,$this->_farm);

        $total = count($data_ekspedisi_all);
        $pages = ceil($total / $offset);

        if (count($data_ekspedisi) > 0) {
            $data = array(
                'TotalRows' => $pages,
                'Rows' => $data_ekspedisi
            );

            $this->output->set_content_type('application/json');
            echo json_encode(array(
                $data
            ));
        } else {
            echo json_encode(array());
        }

        exit();
    }

    function get_ekspedisi() {
        $kode_ekspedisi = ($this->input->post("kode_ekspedisi")) ? $this->input->post("kode_ekspedisi") : null;
		$data_ekspedisi = $this->m_ekspedisi->get_ekspedisi_by_id($kode_ekspedisi);
        $data_ekspedisi_vehicle = $this->m_ekspedisi->get_ekspedisi_vehicle_by_kode_ekspedisi($kode_ekspedisi);
		
		foreach ($data_ekspedisi_vehicle as $key => $value) {
            $detil = $this->m_ekspedisi->get_ekspedisi_vehicle_detil_by_kode_ekspedisi(
                $value['KODE_EKSPEDISI'],
                $value['NO_KENDARAAN'],
                $value['TIPE_KENDARAAN'],
                $value['MAX_KUANTITAS'],
                $value['MAX_BERAT']);

            $data_ekspedisi_vehicle[$key]['DETIL'] = $detil;
        }

        $data_farm = $this->m_farm->get_farm();

        $data = [
            'data_ekspedisi' => $data_ekspedisi,
            'data_ekspedisi_vehicle' => $data_ekspedisi_vehicle,
            'data_farm' => $data_farm
        ];

        echo json_encode($data);
    }

    function get_farm() {
        $data_farm = $this->m_farm->get_farm();
        $data = ['data_farm' => $data_farm];

        echo json_encode($data);
    }

    function add_ekspedisi() {
        $data = $this->input->post("data");
        $result = $this->m_ekspedisi->simpan_ekspedisi($data[0]);
        #echo json_encode($result);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    function hapus_kendaraan() {
        $no_pol = $this->input->post("no_pol");
        $result = $this->m_ekspedisi->hapus_kendaraan($no_pol);
        echo (empty($result ['no_pol'])) ? json_encode(0) : json_encode(1);
    }

    function update_ekspedisi() {
        $data = $this->input->post("data");/*

        echo "<pre>";
        print_r($data);
        exit;*/

        $result = $this->m_ekspedisi->simpan_update_ekspedisi($data[0]);
        #echo json_encode($result);
		
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

}
