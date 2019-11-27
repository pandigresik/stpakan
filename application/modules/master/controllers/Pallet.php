<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Pallet extends MY_Controller {

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
        $this->load->model('m_pallet');
    }

    public function index() {
        $data['list_konversi'] = array(); #$this->m_pallet->list_konversi();
        $this->load->view("pallet/pallet_list",$data);
    }

    function get_pagination() {
        $page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
        $is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

        $id_pallet = (($this->input->post("id_pallet")) and $is_search == true) ? $this->input->post("id_pallet") : null;
        $tanggal_penimbangan = (($this->input->post("tanggal_penimbangan")) and $is_search == true) ? $this->input->post("tanggal_penimbangan") : null;
        $pallet_aktif = (($this->input->post("pallet_aktif")) and $is_search == true) ? $this->input->post("pallet_aktif") : null;
        $pallet_tidak_aktif = (($this->input->post("pallet_tidak_aktif")) and $is_search == true) ? $this->input->post("pallet_tidak_aktif") : null;

        $data_all_pallet = $this->m_pallet->get_data_pallet($this->_farm, $id_pallet, $tanggal_penimbangan, $pallet_aktif, $pallet_tidak_aktif);
        echo json_encode($data_all_pallet);
    }

    public function simpan_berat_pallet() {
        $data = $this->input->post("params");
        $result = $this->m_pallet->simpan_berat_pallet($this->_farm, $data);
        //echo json_encode($result);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    function get_pallet() {
        $id_pallet = ($this->input->post("id_pallet")) ? $this->input->post("id_pallet") : null;

        $id_pallet = $this->m_pallet->get_pallet_by_id($id_pallet);

        echo json_encode($id_pallet);
    }

    function get_master_pallet() {
        $data ['pallet'] = $this->m_pallet->get_all_pallet();

        $this->load->view("pallet/master_pallet", $data);
    }

    function add_pallet() {
        $id_pallet = ($this->input->post("id_pallet")) ? $this->input->post("id_pallet") : null;
        $tara = ($this->input->post("tara")) ? $this->input->post("tara") : null;
        $siklus = ($this->input->post("siklus")) ? $this->input->post("siklus") : null;
        $konversi = ($this->input->post("konversi")) ? $this->input->post("konversi") : null;

        $return = array();

        $return ["form_mode"] = "tambah";
        $check = $this->m_pallet->check_pallet($id_pallet);
        if ($check ['n_result'] > 0) {
            $return ["result"] = "failed";
            $return ["check"] = "failed";
        } else {
            $data = array(
                "pallet" => $id_pallet,
                "tara" => $tara,
                "BASE_pallet" => $siklus,
                "KONVERSI" => $konversi
            );
            //print_r($data);
            $result = $this->m_pallet->insert($data);
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

    function update_pallet() {
        $id_pallet = ($this->input->post("id_pallet")) ? $this->input->post("id_pallet") : null;
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

        $result = $this->m_pallet->update($data, $id_pallet);
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

    function history_pallet() {
        $kode_pallet = $this->input->post("kode_pallet");
        $data ['pallet'] = $this->m_pallet->history_pallet($this->_farm, $kode_pallet);
        $data['status'] = array(
            'N'=>'Aktif',
            'C'=>'Tidak Aktif',
        );

        $this->load->view("pallet/history_pallet", $data);
    }

    function ubah_status_pallet() {
        $kode_pallet = $this->input->post("kode_pallet");
        $status_pallet = $this->input->post("status_pallet");
        $keterangan = $this->input->post("keterangan");
        $tanggal_penimbangan = $this->input->post("tanggal_penimbangan");
        $data = $this->m_pallet->ubah_status_pallet($this->_farm, $kode_pallet, $status_pallet, $keterangan, $tanggal_penimbangan);
        echo json_encode($data);
    }

    function check_stok(){
      $idpallet = $this->input->get('idpallet');
      $stok = $this->m_pallet->check_stok($this->_farm,$idpallet)->row_array();
      $result = empty($stok['stok']) ? 0 : $stok['stok'];
      echo json_encode(array('stok' => $result));
    }

    public function cetak_pallet(){
		ob_start();
	//	error_reporting(0);
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'P', PDF_UNIT, 'A4', true, 'UTF-8', false );
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Wonokoyo Jaya Corp.');
		$pdf->SetTitle('Generate Barcode ID Tray');
		$pdf->SetSubject('ID Tray');

		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(0,0,0,0);
		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );
		// set auto page breaks
		// $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$pdf->SetFont('helvetica', '', 6);
		$pdf->AddPage();

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		// set style for barcode
		$style = array(
		    'vpadding' => 0,
		    'hpadding' => 0,
			'margin'  =>0,
            'position' => 'C',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255)
		    'module_width' => 1, // width of a single module in points
		    'module_height' => 1 // height of a single module in points
		);

		$arr = $this->input->post('generate_data');
// 		 $arr = array('A1-01-01',
// 'A1-01-02',
// 'A2-01-01',
// 'A2-01-02',
// 'B1-01-01',
// 'B2-01-01',
// 	);
		$count = 0;
		$y = 5;
		$x = 0;
		foreach ($arr as $key => $value) {
			if($count == 2){ //untuk menentukan batasan jumlah item per halaman
				$count = 0;
				$y = 5;
				$x = 0;
				$pdf->AddPage();
			}
			if($x == 1){
				$y = $pdf->getY();
				$y += 7;
				$x = 0;
			}
			// $pdf->write2DBarcode($value, 'PDF417', $x*25+5, $y, 0, 9, $style, 'N');
            // aturannya seperti ini : write1DBarcode($code, $type, $x='', $y='', $w='', $h='', $xres='', $style='', $align='')
            // aturannya seperti ini : writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=false, $reseth=true, $align='', $autopadding=true)
            $pdf->write1DBarcode($value, 'C39', $x*30+5, $y+5, 200, 60, 1, $style, 'N');
			$pdf->writeHTMLCell(180, 95, 15, $y, '', 'LRTB', 1, 0, true, '', true);
            $pdf->SetFont('helvetica', 'B', 50);
			$pdf->Text(65, $y+70, $value);
			$pdf->ln();
			$x++;
			$count++;
		}


		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output('barcode_tray.pdf', 'I');
		ob_end_flush();
	}
}
