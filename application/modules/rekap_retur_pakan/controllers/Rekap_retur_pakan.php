<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Rekap_retur_pakan extends MX_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model("M_rekap_retur_pakan", "m_rekap");
		$this->load->model("riwayat_harian_kandang/M_riwayat_harian_kandang", "m_riwayat");
	}
	
	function index(){
		$kodefarm = $this->session->userdata("kode_farm");
		$farm = $this->m_riwayat->get_farm($kodefarm);
		$fulldate = $this->m_riwayat->get_today();
		$date = explode(" ",$fulldate["today"]);
		
		$data["kode_farm"] = $kodefarm;
		$data["nama_farm"] = $farm["nama_farm"];
		$data["today"] = $date[0];
		
		$data["retur_pakan"] = $this->m_rekap->get_retur_pakan($kodefarm);
		
		$this->load->view("rekap_retur_pakan", $data);
	}
	
	function get_retur_pakan_list(){
		$kode_farm = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : null;
		$tgl_awal = ($this->input->post("tgl_awal") != "") ? $this->input->post("tgl_awal") : null;
		$tgl_akhir = ($this->input->post("tgl_akhir") != "") ? $this->input->post("tgl_akhir") : null;
		
		$data = $this->m_rekap->get_retur_pakan($kode_farm, $tgl_awal, $tgl_akhir);
		
		echo json_encode($data);
	}
	
	function get_retur_pakan(){
		$no_retur = ($this->input->post("no_retur")) ? $this->input->post("no_retur") : null;
		$no_reg = ($this->input->post("no_reg")) ? $this->input->post("no_reg") : null;
		
		$pakan = $this->m_rekap->get_retur_pakan_detail($no_retur, $no_reg);
		
		echo json_encode($pakan);
	}
	
	function proses_pengajuan(){
		$fulldate = $this->m_riwayat->get_today();
		
		$no_retur = ($this->input->post("no_retur")) ? $this->input->post("no_retur") : null;
		$no_reg = ($this->input->post("no_reg")) ? $this->input->post("no_reg") : null;
		
		$result = $this->m_rekap->proses_pengajuan_retur($no_retur, $no_reg, $this->session->userdata("kode_user"));
		
		if($result)
			echo json_encode(array("result"=>"success"));
		else
			echo json_encode(array("result"=>"failed"));
	}
	
	function proses_persetujuan(){
		$fulldate = $this->m_riwayat->get_today();
		
		$no_retur = ($this->input->post("no_retur")) ? $this->input->post("no_retur") : null;
		$no_reg = ($this->input->post("no_reg")) ? $this->input->post("no_reg") : null;
		
		$result = $this->m_rekap->proses_persetujuan_retur($no_retur, $no_reg, $this->session->userdata("kode_user"), $this->session->userdata("level_user"));
		
		if($result !== "faield")
			echo json_encode(array("result"=>"success", "nama_pegawai"=>$result));
		else
			echo json_encode(array("result"=>"failed"));
	}	
	
	function print_sj(){
		$tgl_tutup_siklus = $this->input->post("inp_print_tgl_lhk");
		$nama_farm = $this->input->post("inp_print_nama_farm");
		$nama_kandang = $this->input->post("inp_print_nama_kandang");
		$no_retur = $this->input->post("inp_print_no_retur");
		$no_reg = $this->input->post("inp_print_no_reg");
		$nama_retur = $this->input->post("inp_print_nama_retur");
		$nama_approve = $this->input->post("inp_print_nama_approve");
		$nama_terima = $this->input->post("inp_print_nama_terima");
		
		$pakan = $this->m_rekap->get_retur_pakan_detail($no_retur, $no_reg);
		
		$items = array();
		for($i=0;$i<count($pakan);$i++){
			$items[] = array(
				"kodebarang"=>$pakan[$i]["KODE_BARANG"],
				"namabarang"=>$pakan[$i]["NAMA_BARANG"],
				"jumlah"=>$pakan[$i]["JML"],
				"berat"=>$pakan[$i]["BRT"],
				"bentuk"=>$pakan[$i]["BENTUK_BARANG"]
			);
		}
		
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'P', PDF_UNIT, 'A5', true, 'UTF-8', false );
		
		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );

		$data['namafarm'] = $nama_farm;
		$data['noretur'] = $no_retur;
		$data['namakandang'] = $nama_kandang;
		$data['tgltutupsiklus'] = $tgl_tutup_siklus;
		$data['namaretur'] = $nama_retur;
		$data['namaapprove'] = !empty($nama_approve) ? $nama_approve : "_________________";
		$data['namaterima'] = !empty($nama_terima) ? $nama_terima : "_________________";
		$data['items'] = $items;
		$style = array(
			'position' => '',
			'align' => 'C',
			'stretch' => false,
			'fitwidth' => true,
			'cellfitalign' => '',
			'border' => true,
			'hpadding' => 'auto',
			'vpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255),
			'text' => true,
			'font' => 'helvetica',
			'fontsize' => 8,
			'stretchtext' => 4
		);

		// PRINT VARIOUS 1D BARCODES

		// CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
		$params = $pdf->serializeTCPDFtagParameters ( array (
					$no_retur,
					'C128',
					'',
					'',
					27,
					5 
			) );
		$b = '<tcpdf method="write1DBarcode" params="' . $params . '" />';
		$data['barcode'] = $b;
		$html = $this->load->view ( 'rekap_retur_pakan/retur', $data, true );

		// echo $html;
		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );
		$pdf->Output ( 'rekap_retur_pakan.pdf', 'I' );
	}
}
