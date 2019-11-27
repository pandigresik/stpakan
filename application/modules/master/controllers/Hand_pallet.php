<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Hand_pallet extends MY_Controller {

    protected $_user;
    protected $_farm;
    protected $_berat;
    protected $_grup_farm;
    function __construct() {
        parent::__construct();
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_berat = $this->config->item('berat_standart');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
        $this->load->model('m_hand_pallet');
    }

    public function index() {
        $data['kode_farm'] = $this->_farm;
        $data['list_konversi'] = array(); #$this->m_hand_pallet->list_konversi();
        $this->load->view("hand_pallet/hand_pallet_list",$data);
    }

    function get_pagination() {
        $page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
        $is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

        $id_hand_pallet = (($this->input->post("id_hand_pallet")) and $is_search == true) ? $this->input->post("id_hand_pallet") : null;
        $tanggal_penimbangan = (($this->input->post("tanggal_penimbangan")) and $is_search == true) ? $this->input->post("tanggal_penimbangan") : null;
        $hand_pallet_aktif = (($this->input->post("hand_pallet_aktif")) and $is_search == true) ? $this->input->post("hand_pallet_aktif") : null;
        $hand_pallet_tidak_aktif = (($this->input->post("hand_pallet_tidak_aktif")) and $is_search == true) ? $this->input->post("hand_pallet_tidak_aktif") : null;

        $data_all_hand_pallet = $this->m_hand_pallet->get_data_hand_pallet($this->_farm, $id_hand_pallet, $tanggal_penimbangan, $hand_pallet_aktif, $hand_pallet_tidak_aktif);
        echo json_encode($data_all_hand_pallet);
    }

    public function simpan_berat_hand_pallet() {
        $data = $this->input->post("params");
        $result = $this->m_hand_pallet->simpan_berat_hand_pallet($this->_farm, $data);
        #echo json_encode($result);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    function get_hand_pallet() {
        $id_hand_pallet = ($this->input->post("id_hand_pallet")) ? $this->input->post("id_hand_pallet") : null;

        $id_hand_pallet = $this->m_hand_pallet->get_hand_pallet_by_id($id_hand_pallet);

        echo json_encode($id_hand_pallet);
    }

    function get_master_hand_pallet() {
        $data ['hand_pallet'] = $this->m_hand_pallet->get_all_hand_pallet();

        $this->load->view("hand_pallet/master_hand_pallet", $data);
    }

    function add_hand_pallet() {
        $id_hand_pallet = ($this->input->post("id_hand_pallet")) ? $this->input->post("id_hand_pallet") : null;
        $tara = ($this->input->post("tara")) ? $this->input->post("tara") : null;
        $siklus = ($this->input->post("siklus")) ? $this->input->post("siklus") : null;
        $konversi = ($this->input->post("konversi")) ? $this->input->post("konversi") : null;

        $return = array();

        $return ["form_mode"] = "tambah";
        $check = $this->m_hand_pallet->check_hand_pallet($id_hand_pallet);
        if ($check ['n_result'] > 0) {
            $return ["result"] = "failed";
            $return ["check"] = "failed";
        } else {
            $data = array(
                "hand_pallet" => $id_hand_pallet,
                "tara" => $tara,
                "BASE_hand_pallet" => $siklus,
                "KONVERSI" => $konversi
            );
            //print_r($data);
            $result = $this->m_hand_pallet->insert($data);
            if ($result) {
                $return ["result"] = "success";
            } else {
                $return ["result"] = "failed";
                $return ["check"] = "success";
            }
        }
        #echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
    }

    function update_hand_pallet() {
        $id_hand_pallet = ($this->input->post("id_hand_pallet")) ? $this->input->post("id_hand_pallet") : null;
        $tara = ($this->input->post("tara")) ? $this->input->post("tara") : null;
        $siklus = ($this->input->post("siklus")) ? $this->input->post("siklus") : null;
        $konversi = ($this->input->post("konversi")) ? $this->input->post("konversi") : null;

        $return = array();

        $return ["form_mode"] = "ubah";
        $data = array(
            "tara" => $tara,
            "BASE_hand_pallet" => $siklus,
            "KONVERSI" => $konversi
        );

        $result = $this->m_hand_pallet->update($data, $id_hand_pallet);
        if ($result) {
            $return ["result"] = "success";
        } else {
            $return ["result"] = "failed";
        }
        #echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
    }

    function history_hand_pallet() {
        $kode_hand_pallet = $this->input->post("kode_hand_pallet");
        $data ['hand_pallet'] = $this->m_hand_pallet->history_hand_pallet($this->_farm, $kode_hand_pallet);
        $data['status'] = array(
            'N'=>'Aktif',
            'C'=>'Tidak Aktif',
        );

        $this->load->view("hand_pallet/history_hand_pallet", $data);
    }

    function ubah_status_hand_pallet() {
        $kode_hand_pallet = $this->input->post("kode_hand_pallet");
        $status_pallet = $this->input->post("status_hand_pallet");
        $keterangan = $this->input->post("keterangan");
        $tanggal_penimbangan = $this->input->post("tanggal_penimbangan");
        $data = $this->m_hand_pallet->ubah_status_hand_pallet($this->_farm, $kode_hand_pallet, $status_pallet, $keterangan, $tanggal_penimbangan);
        #echo json_encode($data);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    function ubah_default_hand_pallet() {
        $kode_hand_pallet = $this->input->post("kode_hand_pallet");
        $status_pallet = $this->input->post("status_hand_pallet");
        $tanggal_penimbangan = $this->input->post("tanggal_penimbangan");
        $default = $this->input->post("_default");
        $data = $this->m_hand_pallet->ubah_default_hand_pallet($this->_farm, $kode_hand_pallet, $status_pallet, $tanggal_penimbangan, $default);
        #echo json_encode($data);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    function generate_kode_hand_pallet() {
        $data = $this->m_hand_pallet->generate_kode_hand_pallet($this->_farm);
        echo json_encode($data);
    }

    function hand_pallet_aktif(){
      $data = $this->m_hand_pallet->get_many_by(array('status_pallet'=>'N','kode_farm'=>$this->_farm));
      echo json_encode($data);
    }
}
