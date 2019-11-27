<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Harga_barang extends MX_Controller {
	protected $_user;
	protected $_farm;
	protected $_bentuk_barang;
    protected $_level_user;
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'm_harga_barang' );
		$this->_user = $this->session->userdata ( 'kode_user' );
        $this->_level_user = $this->session->userdata ( 'level_user' );
        $this->_farm = ($this->_level_user == 'BPM') ? NULL : $this->session->userdata('kode_farm');
		#$this->_farm = $this->session->userdata ( 'kode_farm' );
		$this->_bentuk_barang = array(
			'T' => 'TEPUNG',
			'C' => 'CRUMBLE',
			'P' => 'PALLET',
			'A' => 'CAIR'
		);
		$this->load->helper ( 'stpakan' );
	}
	public function index() {
		$data['list_satuan']=$this->m_harga_barang->list_satuan();
		$data['list_bentuk']=$this->_bentuk_barang;
		$this->load->view ( "harga_barang/harga_barang_list",$data );
	}
	function get_master_barang() {
		$data ['list_barang'] = $this->m_harga_barang->list_barang ();
		
		$this->load->view ( "harga_barang/master_barang", $data );
	}
	function cari_barang() {
		$kode_barang = $this->input->post ( "kode_barang" );
		$result = $this->m_harga_barang->list_barang ($kode_barang);
		
		echo (count($result)!=1) ? 0 : json_encode($result[0]);
	}
	function kontrol_efektif() {
		$tanggal_berlaku = $this->input->post ( "tanggal_berlaku" );
		$tanggal_berlaku = date('Y-m-d',strtotime($tanggal_berlaku));
		$result = $this->m_harga_barang->kontrol_efektif ($tanggal_berlaku);
		
		echo json_encode($result);
	}
	function search_data_harga() {
		$pelanggan = $this->input->post ( "pelanggan" );
        $kode_barang = $this->input->post ( "kode_barang" );
        $tanggal_berlaku = $this->input->post ( "tanggal_berlaku" );
		$result = $this->m_harga_barang->search_data_harga ($pelanggan,$kode_barang,$tanggal_berlaku);
		
		echo json_encode($result);
	}
	function get_master_pelanggan() {
		$data ['list_pelanggan'] = $this->m_harga_barang->list_pelanggan ();
		
		$this->load->view ( "harga_barang/master_pelanggan", $data );
	}
	function get_pagination() {
		$offset = 8;
		$page_number = ($this->input->post ( 'page_number' )) ? $this->input->post ( 'page_number' ) : 0;
		$is_search = ($this->input->post ( 'search' )) ? $this->input->post ( 'search' ) : false;
		
		$pelanggan = (($this->input->post ( "pelanggan" )) and $is_search == true) ? $this->input->post ( "pelanggan" ) : null;
		$kode_barang = (($this->input->post ( "kode_barang" )) and $is_search == true) ? $this->input->post ( "kode_barang" ) : null;
		$nama_barang = (($this->input->post ( "nama_barang" )) and $is_search == true) ? $this->input->post ( "nama_barang" ) : null;
		$satuan = (($this->input->post ( "satuan" )) and $is_search == true) ? $this->input->post ( "satuan" ) : null;
		$bentuk_pakan = (($this->input->post ( "bentuk_pakan" )) and $is_search == true) ? $this->input->post ( "bentuk_pakan" ) : null;
		$tanggal_berlaku = (($this->input->post ( "tanggal_berlaku" )) and $is_search == true) ? date ( 'Y-m-d', strtotime ( convert_month ( $this->input->post ( "tanggal_berlaku" ), 2 ) ) ) : null;
		
		$harga_barang_all = $this->m_harga_barang->get_harga_barang ( NULL, NULL, $pelanggan, $kode_barang, $nama_barang, $satuan, $bentuk_pakan, $tanggal_berlaku );
		
		$harga_barang = $this->m_harga_barang->get_harga_barang ( ($page_number * $offset), ($page_number + 1) * $offset, $pelanggan, $kode_barang, $nama_barang, $satuan, $bentuk_pakan, $tanggal_berlaku );
		
		$total = count ( $harga_barang_all );
		$pages = ceil ( $total / $offset );
		
		if (count ( $harga_barang ) > 0) {
			$data = array (
					'TotalRows' => $pages,
					'Rows' => $harga_barang 
			);
			
			$this->output->set_content_type ( 'application/json' );
			echo json_encode ( array (
					$data 
			) );
		} else {
			echo json_encode ( array () );
		}
		
		exit ();
	}
	function get_harga_barang() {
		$kode_pelanggan = ($this->input->post ( "pelanggan" )) ? $this->input->post ( "pelanggan" ) : null;
		$kode_barang = ($this->input->post ( "kode_barang" )) ? $this->input->post ( "kode_barang" ) : null;
		$uom = ($this->input->post ( "satuan" )) ? $this->input->post ( "satuan" ) : null;
		$tanggal_berlaku = ($this->input->post ( "tanggal_berlaku" )) ? date ( 'Y-m-d', strtotime ( convert_month ( $this->input->post ( "tanggal_berlaku" ), 2 ) ) ) : null;
		
		$pelanggan = $this->m_harga_barang->get_harga_barang_by_id ( $kode_pelanggan, $kode_barang, $uom, $tanggal_berlaku );
		
		echo json_encode ( $pelanggan );
	}
	function add_harga_barang() {
		$kode_pelanggan = ($this->input->post ( "pelanggan" )) ? $this->input->post ( "pelanggan" ) : null;
		$kode_barang = ($this->input->post ( "kode_barang" )) ? $this->input->post ( "kode_barang" ) : null;
		$uom = ($this->input->post ( "satuan" )) ? $this->input->post ( "satuan" ) : null;
		$tmp_tanggal_berlaku = ($this->input->post ( "tmp_tanggal_berlaku" )) ? date ( 'Y-m-d', strtotime ( convert_month ( $this->input->post ( "tmp_tanggal_berlaku" ), 2 ) ) ) : null;
		$tanggal_berlaku = ($this->input->post ( "tanggal_berlaku" )) ? date ( 'Y-m-d', strtotime ( convert_month ( $this->input->post ( "tanggal_berlaku" ), 2 ) ) ) : null;
		$harga = ($this->input->post ( "harga" )) ? $this->input->post ( "harga" ) : null;
		
		$return = array ();
		
		$return ["form_mode"] = "tambah";
		$check = $this->m_harga_barang->check_harga_barang ( $kode_pelanggan, $kode_barang, $uom, $tanggal_berlaku );
		if ($check ['n_result'] > 0) {
			$return ["result"] = "failed";
			$return ["check"] = "failed";
		} else {
			$data = array (
					"KODE_PELANGGAN" => $kode_pelanggan,
					"KODE_BARANG" => $kode_barang,
					"UOM" => $uom,
					"TGL_BERLAKU" => $tanggal_berlaku,
					"HARGA" => $harga,
					"TGL_BUAT" => date('Y-m-d'),
					"USER_BUAT" => $this->_user 
			);
			$max_efektif = $this->m_harga_barang->max_efektif($kode_pelanggan,$kode_barang);
			//echo $tanggal_berlaku .'<'.$max_efektif;
			if(empty($max_efektif)){
				$result = $this->m_harga_barang->insert ( $data );
			}
			else if($tanggal_berlaku > $max_efektif){
				$result = $this->m_harga_barang->insert ( $data );
			}
			else{
				$result = $this->m_harga_barang->update ( $harga, $kode_pelanggan, $kode_barang, $uom, $tanggal_berlaku ,$tmp_tanggal_berlaku);
			}
			if ($result) {
				$return ["result"] = "success";
			} else {
				$return ["result"] = "failed";
				$return ["check"] = "success";
			}
		}
		#echo json_encode ( $return );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}
	function update_harga_barang() {
		$kode_pelanggan = ($this->input->post ( "pelanggan" )) ? $this->input->post ( "pelanggan" ) : null;
		$kode_barang = ($this->input->post ( "kode_barang" )) ? $this->input->post ( "kode_barang" ) : null;
		$uom = ($this->input->post ( "satuan" )) ? $this->input->post ( "satuan" ) : null;
		$tanggal_berlaku = ($this->input->post ( "tanggal_berlaku" )) ? date ( 'Y-m-d', strtotime ( convert_month ( $this->input->post ( "tanggal_berlaku" ), 2 ) ) ) : null;
		$tanggal_berlaku_baru = ($this->input->post ( "tanggal_berlaku_baru" )) ? date ( 'Y-m-d', strtotime ( convert_month ( $this->input->post ( "tanggal_berlaku_baru" ), 2 ) ) ) : null;
		$harga = ($this->input->post ( "harga" )) ? $this->input->post ( "harga" ) : null;
		
		$return = array ();
		
		$return ["form_mode"] = "ubah";
		$check ['n_result'] = 0;
		if($tanggal_berlaku != $tanggal_berlaku_baru){
			$check = $this->m_harga_barang->check_harga_barang ( $kode_pelanggan, $kode_barang, $uom, $tanggal_berlaku_baru );
		}
		if ($check ['n_result'] > 0) {
			$return ["result"] = "failed";
			$return ["check"] = "failed";
		} else {			
			

				$data = array (
						"KODE_PELANGGAN" => $kode_pelanggan,
						"KODE_BARANG" => $kode_barang,
						"UOM" => $uom,
						"TGL_BERLAKU" => $tanggal_berlaku_baru,
						"HARGA" => $harga,
						"TGL_BUAT" => date('Y-m-d'),
						"USER_BUAT" => $this->_user 
				);

			$max_efektif = $this->m_harga_barang->max_efektif($kode_pelanggan,$kode_barang);
			//echo $tanggal_berlaku .'<'.$max_efektif;
			if(empty($max_efektif)){

				$result = $this->m_harga_barang->insert ( $data );
			}
			else if($tanggal_berlaku_baru > $max_efektif){
				$result = $this->m_harga_barang->insert ( $data );
			}
			else{
				$result = $this->m_harga_barang->update ( $harga, $kode_pelanggan, $kode_barang, $uom, $tanggal_berlaku_baru, $tanggal_berlaku );
			}
			if ($result) {
				$return ["result"] = "success";
			} else {
				$return ["result"] = "failed";
			}
		}
		#echo json_encode ( $return );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}
}
