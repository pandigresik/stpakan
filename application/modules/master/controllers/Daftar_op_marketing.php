<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Daftar_op_marketing extends MX_Controller {
	protected $_user;
	protected $_farm;
	protected $_level_user;
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'm_daftar_op_marketing' );
		$this->_user = $this->session->userdata ( 'kode_user' );
		$this->_level_user = $this->session->userdata ( 'level_user' );
        $this->_farm = ($this->_level_user == 'BPM') ? NULL : $this->session->userdata('kode_farm');
		#$this->_farm = $this->session->userdata ( 'kode_farm' );
		$this->load->helper ( 'stpakan' );
	}
	public function index() {
		#echo $this->_level_user;
		$kode_farm = $this->_farm;
		if($this->_level_user == 'BPM'){
			$kode_farm = NULL;
		}
		$data ["grup_farm"] = $this->m_daftar_op_marketing->get_grup_farm ();
		$data ["farm"] = $this->m_daftar_op_marketing->get_nama_farm ($data ["grup_farm"][0]['GRUP_FARM'],$kode_farm);
		$data ["tahun"] = $this->m_daftar_op_marketing->get_tahun ();
		$this->load->view ( "daftar_op_marketing/daftar_op_marketing", $data );
	}
	
	function get_pagination() {
		$offset = 8;
		$page_number = ($this->input->post ( 'page_number' )) ? $this->input->post ( 'page_number' ) : 0;
		$is_search = ($this->input->post ( 'search' )) ? $this->input->post ( 'search' ) : false;
		
		$grup = (($this->input->post ( "grup" )) and $is_search == true) ? $this->input->post ( "grup" ) : null;
		$farm = (($this->input->post ( "farm" )) and $is_search == true) ? $this->input->post ( "farm" ) : null;
		$tahun = (($this->input->post ( "tahun" )) and $is_search == true) ? $this->input->post ( "tahun" ) : null;
		$tanggal_kirim = (($this->input->post ( "tanggal_kirim" )) and $is_search == true) ? date ( 'Y-m-d', strtotime ( convert_month ( $this->input->post ( "tanggal_kirim" ), 2 ) ) ) : null;
		// echo $tanggal_kirim;
		$no_op_awal = (($this->input->post ( "no_op_awal" )) and $is_search == true) ? $this->input->post ( "no_op_awal" ) : null;
		$no_op_akhir = (($this->input->post ( "no_op_akhir" )) and $is_search == true) ? $this->input->post ( "no_op_akhir" ) : null;
		$no_op_pakai = (($this->input->post ( "no_op_pakai" )) and ($this->input->post ( "no_op_pakai" )) != "" and $is_search == true) ? $this->input->post ( "no_op_pakai" ) : null;
		
		$op_marketing_all = $this->m_daftar_op_marketing->get_op_marketing ( NULL, NULL, $grup, $tahun, $tanggal_kirim, $no_op_awal, $no_op_akhir, $no_op_pakai, $farm );
		
		$op_marketing = $this->m_daftar_op_marketing->get_op_marketing ( ($page_number * $offset), ($page_number + 1) * $offset, $grup, $tahun, $tanggal_kirim, $no_op_awal, $no_op_akhir, $no_op_pakai, $farm );
		
		$total = count ( $op_marketing_all );
		$pages = ceil ( $total / $offset );
		
		if (count ( $op_marketing ) > 0) {
			$data = array (
					'TotalRows' => $pages,
					'Rows' => $op_marketing 
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
	function get_op_marketing() {
		$tanggal_kirim = ($this->input->post ( "tanggal_kirim" )) ? $this->input->post ( "tanggal_kirim" ) : null;
		$kode_farm = ($this->input->post ( "kode_farm" )) ? $this->input->post ( "kode_farm" ) : null;
		
		$tanggal_kirim = $this->m_daftar_op_marketing->get_op_marketing_by_id ( $tanggal_kirim, $kode_farm );
		
		echo json_encode ( $tanggal_kirim );
	}
	
	public function get_farm(){
		$kode_farm = $this->_farm;
		if($this->_level_user == 'BPM'){
			$kode_farm = NULL;
		}
		$grup = ($this->input->post ( "grup" )) ? $this->input->post ( "grup" ) : null;
		
		$farms = ($grup == 'BDY') ? $this->m_daftar_op_marketing->get_nama_farm ( $grup,$kode_farm ) : $this->m_daftar_op_marketing->get_nama_farm ( $grup );
		
		$data = array("TotalRows"=>count($farms), "Rows"=>$farms);
		
		echo json_encode ( $data );
	}
	
	function kontrol_op_pakai() {
		$tahun = ($this->input->post ( "tahun" )) ? $this->input->post ( "tahun" ) : null;
		$no_op_awal = ($this->input->post ( "no_op_awal" )) ? $this->input->post ( "no_op_awal" ) : null;
		$no_op_akhir = ($this->input->post ( "no_op_akhir" )) ? $this->input->post ( "no_op_akhir" ) : null;
		$no_op_pakai = ($this->input->post ( "no_op_pakai" )) ? $this->input->post ( "no_op_pakai" ) : null;
		
		$result = $this->m_daftar_op_marketing->kontrol_op_pakai ( $tahun,$no_op_awal,$no_op_akhir,$no_op_pakai );
		
		echo ($result['n_count']>0) ? 0 : 1;
	}
	function kontrol_simpan() {
		$tahun = ($this->input->post ( "tahun" )) ? $this->input->post ( "tahun" ) : null;
		$no_op_awal = ($this->input->post ( "no_op_awal" )) ? $this->input->post ( "no_op_awal" ) : null;
		$no_op_akhir = ($this->input->post ( "no_op_akhir" )) ? $this->input->post ( "no_op_akhir" ) : null;
		$no_op_pakai = ($this->input->post ( "no_op_pakai" )) ? $this->input->post ( "no_op_pakai" ) : null;
		
		$result = $this->m_daftar_op_marketing->kontrol_simpan ( $tahun,$no_op_awal,$no_op_akhir,$no_op_pakai );
		
		echo ($result['n_count']>0) ? 0 : 1;
	}
	function add_op_marketing() {
		$grup = ($this->input->post ( "grup" )) ? $this->input->post ( "grup" ) : null;
		$farm = ($this->input->post ( "farm" )) ? $this->input->post ( "farm" ) : null;
		$tahun = ($this->input->post ( "tahun" )) ? $this->input->post ( "tahun" ) : null;
		$tanggal_kirim = ($this->input->post ( "tanggal_kirim" )) ? date ( 'Y-m-d', strtotime ( convert_month ( $this->input->post ( "tanggal_kirim" ), 2 ) ) ) : null;
		$no_op_awal = ($this->input->post ( "no_op_awal" )) ? $this->input->post ( "no_op_awal" ) : null;
		$no_op_akhir = ($this->input->post ( "no_op_akhir" )) ? $this->input->post ( "no_op_akhir" ) : null;
		$no_op_pakai = ($this->input->post ( "no_op_pakai" )) ? $this->input->post ( "no_op_pakai" ) : null;
		
		$return = array ();
		
		$return ["form_mode"] = "tambah";
		$check = $this->m_daftar_op_marketing->check_op_marketing ( $tanggal_kirim, $farm );
		if ($check ['n_result'] > 0) {
			$return ["result"] = "failed";
			$return ["check"] = "failed";
		} else {
			// $grup_farm = $this->m_daftar_op_marketing->get_grup_farm($this->_farm);


				$data = array (
						"GRUP_FARM" => $grup, // $grup_farm[0]['GRUP_FARM'],
						"KODE_FARM" => $farm, // date('Y'),
						"TAHUN" => $tahun, // date('Y'),
						"TGL_KIRIM" => $tanggal_kirim,
						"NO_OP_AWAL" => $no_op_awal,
						"NO_OP_AKHIR" => $no_op_akhir,
						"NO_OP_PAKAI" => $no_op_pakai,
						"USER_BUAT" => $this->_user,
						"USER_UBAH" => $this->_user 
				);
				$result = $this->m_daftar_op_marketing->insert ( $data );
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
	function kontrol_kirim() {
		$tanggal_kirim = $this->input->post ( "tanggal_kirim" );
		$tanggal_kirim = date('Y-m-d',strtotime($tanggal_kirim));
		$result = $this->m_daftar_op_marketing->kontrol_kirim ($tanggal_kirim);
		
		echo json_encode($result);
	}
	function update_op_marketing() {
		$grup = ($this->input->post ( "grup" )) ? $this->input->post ( "grup" ) : null;
		$tahun = ($this->input->post ( "tahun" )) ? $this->input->post ( "tahun" ) : null;
		$tanggal_kirim = ($this->input->post ( "tanggal_kirim" )) ? date ( 'Y-m-d', strtotime ( convert_month ( $this->input->post ( "tanggal_kirim" ), 2 ) ) ) : null;
		$no_op_awal = ($this->input->post ( "no_op_awal" )) ? $this->input->post ( "no_op_awal" ) : null;
		$no_op_akhir = ($this->input->post ( "no_op_akhir" )) ? $this->input->post ( "no_op_akhir" ) : null;
		$no_op_pakai = ($this->input->post ( "no_op_pakai" )) ? $this->input->post ( "no_op_pakai" ) : null;
		
		$return = array ();
		
		$return ["form_mode"] = "ubah";
			$data = array (
					"GRUP_FARM" => $grup, // $grup_farm[0]['GRUP_FARM'],
					"TAHUN" => $tahun, // date('Y'),
					"TGL_KIRIM" => $tanggal_kirim,
					"NO_OP_AWAL" => $no_op_awal,
					"NO_OP_AKHIR" => $no_op_akhir,
					"NO_OP_PAKAI" => $no_op_pakai,
					"TGL_UBAH" => date ( 'Y-m-d H:i:s' ),
					"USER_BUAT" => $this->_user,
					"USER_UBAH" => $this->_user 
			);
			
			$result = $this->m_daftar_op_marketing->update ( $data, $tanggal_kirim );
			if ($result) {
				$return ["result"] = "success";
			} else {
				$return ["result"] = "failed";
			}
		#echo json_encode ( $return );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}
}
