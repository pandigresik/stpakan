<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Terima_pakan extends MY_Controller {
    protected $_user;
    protected $_farm;
    protected $_grup_farm;

    public function __construct() {
        parent::__construct();       
        $this->load->helper('stpakan');
        $this->load->config('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->config->item('idFarm');//$this->session->userdata('kode_farm');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
    }

    public function index() {   
        $data = array();
        $data['barang'] = $this->db->select(array('kode_barang','nama_barang'))->where('KODE_BARANG IN (SELECT DISTINCT KODE_BARANG FROM LPB_E)')->get('m_barang')->result();    
        $data['no_reg']=$this->db->select(array('kode_kandang','no_reg','flok_bdy'))->where(array('kode_farm'=>$this->_farm,'status_siklus' => 'O'))->get('kandang_siklus')->result();
        $this->load->view("penerimaan_pakan/".$this->_grup_farm."/terima_pakan", $data);
    }

    public function simpan(){
        $no_do = $this->input->post('no_do');
        $no_reg = $this->input->post('no_reg');
        $flok = $this->input->post('flok');
        $kode_barang = $this->input->post('kode_barang');
        $kuantitas = $this->input->post('kuantitas');
        /** cari no_pallet dan kavling yang akan dipakai dulu */
        $sql = <<<SQL
        EXEC [GENERATE_NO_KAVLING] '{$this->_farm}','{$flok}',NULL,NULL,'{$kode_barang}','{$kode_barang}'
SQL;
        $pallet = $this->db->query($sql)->row();
        $berat_pallet = (floatval($pallet->berat_pallet) + floatval($pallet->berat_hand_pallet));         
         /** cari no_pallet dan kavling yang akan dipakai dulu */
         $sqlAkhir = <<<SQL
         EXEC [TERIMA_PAKAN_DARI_FARM] '{$kuantitas}','{$no_do}','{$no_reg}','{$kode_barang}','{$this->_farm}','{$pallet->no_pallet}','{$pallet->no_kavling}','{$pallet->kode_pallet}','{$berat_pallet}'
SQL;
        
         $_result = $this->db->query($sqlAkhir)->row_array();
         //SELECT @result result,@no_pallet no_pallet, @no_penerimaan no_penerimaan, @no_order no_order, @V_NO_PENERIMAAN_KANDANG no_penerimaan_kandang,@no_reg no_reg
         $result = array('status' => $_result['result'],'message' => 'Pakan gagal diterima');
         if($_result['result']){
             $result['content'] = $_result;
             $result['message'] = 'Pakan berhasil diterima ke kandang';

         }
         $this->output->set_content_type('application/json')
              ->set_output(json_encode($result));   
    }
}