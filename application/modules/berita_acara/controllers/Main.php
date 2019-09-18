<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Main extends MY_Controller {

    protected $_user;
    protected $_farm;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array(
            'm_main'
        ));
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->session->userdata('kode_farm');
    }

    public function index() {
        $kode_farm = $this->_farm; // $this->input->post('kode_farm');
        $data ['no_ba'] = ''; #$this->m_main->generate_no_ba ( $kode_farm );
        $this->load->view('main', $data);
    }

    public function list_surat_jalan() {
        $data['list'] = $this->m_main->list_surat_jalan($this->_farm);

        $this->load->view('list_surat_jalan', $data);
    }

    public function list_berita_acara() {
        $data['list'] = $this->m_main->list_berita_acara($this->_farm);

        $this->load->view('list_berita_acara', $data);
    }

    public function get_data() {
        $kode_farm = $this->_farm; // $this->input->post('kode_farm');
        $no_sj = $this->input->post('no_sj');
        $tipe_ba = $this->input->post('tipe_ba');
        $result = $this->data($kode_farm, $no_sj, $tipe_ba);
        echo json_encode($result);
    }

    public function print_preview() {
        $kode_farm = $this->_farm; // $this->input->post('kode_farm');
        $no_sj = $this->input->post('no_sj');
        $tipe_ba = $this->input->post('tipe_ba');
        $data ['result'] = $this->data($kode_farm, $no_sj, $tipe_ba);
        $this->load->view('print', $data);
    }

    public function cetak_daftar_penerimaan() {
        $kode_farm = $this->_farm;
        $no_sj = $this->input->get('no_sj');
        $tipe_ba = $this->input->get('tipe_ba');

        $this->load->library('Pdf');
        $pdf = new Pdf('P', PDF_UNIT, 'A5', true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $data = $this->data($kode_farm, $no_sj, $tipe_ba);

        $html = $this->load->view('berita_acara/cetak_berita_acara_pdf', array(
            'items' => $data,
            'tipe_ba' => $tipe_ba
                ), true);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('berita_acara.pdf', 'I');
    }

    public function data($kode_farm, $no_sj, $tipe_ba) {
        return $this->m_main->get_data($kode_farm, $no_sj, $tipe_ba);
    }

    public function simpan() {
        $kode_farm = $this->_farm; // $this->input->post('kode_farm');
        $no_sj = $this->input->post('no_sj');
        // $no_ba = $this->input->post ( 'no_ba' );
        $tipe_ba = $this->input->post('tipe_ba');
        $keterangan1 = $this->input->post('keterangan1');
        $user = $this->_user;
        $result = $this->m_main->simpan($kode_farm, $no_sj, /* $no_ba, */ $tipe_ba, $keterangan1, $user);
        echo json_encode($result);
    }

}
