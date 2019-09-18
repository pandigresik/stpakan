<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Budget_glangsing extends MY_Controller {

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
        $this->load->model('m_budget_glangsing');
    }

    public function index() {
        $data['list_konversi'] = array(); #$this->m_budget_glangsing->list_konversi();
        $this->load->view("budget_glangsing/budget_glangsing_list",$data);
    }

    function get_pagination() {
        $page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
        $is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

        $id_budget = (($this->input->post("id_budget")) and $is_search == true) ? $this->input->post("id_budget") : null;
        $nama_budget = (($this->input->post("nama_budget")) and $is_search == true) ? $this->input->post("nama_budget") : null;
        $kategori = (($this->input->post("kategori")) and $is_search == true) ? $this->input->post("kategori") : null;
        $status = (($this->input->post("status")) and $is_search == true) ? $this->input->post("status") : null;

        $data_all_pallet = $this->m_budget_glangsing->get_data_budget($id_budget, $nama_budget, $kategori, $status);
        echo json_encode($data_all_pallet);
    }

    public function simpan_berat_pallet() {
        $data = $this->input->post("params");
        $result = $this->m_budget_glangsing->simpan_berat_pallet($this->_farm, $data);
        echo json_encode($result);
    }

    function get_pallet() {
        $id_budget = ($this->input->post("id_budget")) ? $this->input->post("id_budget") : null;

        $id_budget = $this->m_budget_glangsing->get_pallet_by_id($id_budget);

        echo json_encode($id_budget);
    }

    function get_master_pallet() {
        $data ['pallet'] = $this->m_budget_glangsing->get_all_pallet();

        $this->load->view("pallet/master_pallet", $data);
    }
	
	function cek_budget() {
        $id_budget = ($this->input->post("id_budget")) ? $this->input->post("id_budget") : null;
		echo $this->m_budget_glangsing->doCek_budget();
    }
	
    function save_budget() {
		echo $this->m_budget_glangsing->doSave_budget();
    }

    function update_pallet() {
        $id_budget = ($this->input->post("id_budget")) ? $this->input->post("id_budget") : null;
        $tara = ($this->input->post("tara")) ? $this->input->post("tara") : null;
        $siklus = ($this->input->post("siklus")) ? $this->input->post("siklus") : null;
        $konversi = ($this->input->post("konversi")) ? $this->input->post("konversi") : null;

        $return = array();

        $return ["form_mode"] = "ubah";
        $data = array(
            "tara" => $tara,
            "BASE_pallet" => $siklus,
            "KONVERSI" => $konversi
        );

        $result = $this->m_budget_glangsing->update($data, $id_budget);
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

    function add_budget() {
        $kode_pallet = $this->input->post("kode_budget");

        $this->load->view("budget_glangsing/form_budget");
    }

    function edit_budget() {
        $kode_budget = $this->input->post("kode_budget");

        $this->load->view("budget_glangsing/form_budget");
    }
    function load_budget() {
        $kode_budget = $this->input->post("kode_budget");
		echo $this->m_budget_glangsing->doLoad_budget($kode_budget);
    }

    function ubah_status_pallet() {
        $kode_pallet = $this->input->post("kode_pallet");
        $status_pallet = $this->input->post("status_pallet");
        $keterangan = $this->input->post("keterangan");
        $tanggal_penimbangan = $this->input->post("tanggal_penimbangan");
        $data = $this->m_budget_glangsing->ubah_status_pallet($this->_farm, $kode_pallet, $status_pallet, $keterangan, $tanggal_penimbangan);
        echo json_encode($data);
    }

    function check_stok(){
      $idpallet = $this->input->get('idpallet');
      $stok = $this->m_budget_glangsing->check_stok($this->_farm,$idpallet)->row_array();
      $result = empty($stok['stok']) ? 0 : $stok['stok'];
      echo json_encode(array('stok' => $result));
    }
}
