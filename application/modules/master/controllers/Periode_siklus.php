<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

class Periode_siklus extends MY_Controller {

    protected $_user;
    protected $_level_user;
    protected $_farm;
	function __construct() {
		parent::__construct ();
		$this->load->model ( 'm_periode_siklus' );
		$this->load->model ( 'm_farm' );
		$this->load->model ( 'm_strain' );
		$this->load->helper('stpakan');
	    $this->_user = $this->session->userdata('kode_user');
	    $this->_level_user = $this->session->userdata('level_user');
	    $this->_farm = ($this->_level_user == 'BPM') ? NULL : $this->session->userdata('kode_farm');
	}
	function cetak_r($value, $die = TRUE) {
        echo "<pre>";
        print_r($value);
        if ($die) {
          die();
        }
    }
	public function index() {
	    $year = date('Y');
        for($i=$year; $i<=$year+2; $i++){
            $data['year'][] = $i;
        }
		$farm = $this->m_farm->get_farm_browse ();
		$strain = $this->m_strain->get_strain_browse ();

		$data ["farm"] = $farm;
		$data ["strain"] = $strain;
		$this->load->view ( "periode_siklus/periode_siklus_list", $data );
	}
	function get_pagination() {
		$offset = 8;
		$page_number = ($this->input->post ( 'page_number' )) ? $this->input->post ( 'page_number' ) : 0;
		$is_search = ($this->input->post ( 'search' )) ? $this->input->post ( 'search' ) : false;

		$periodesiklus = (($this->input->post ( "periodesiklus" )) and $is_search == true) ? $this->input->post ( "periodesiklus" ) : null;
		$namafarm = (($this->input->post ( "namafarm" )) and $is_search == true) ? $this->input->post ( "namafarm" ) : null;
		$namastrain = (($this->input->post ( "namastrain" )) and $is_search == true) ? $this->input->post ( "namastrain" ) : null;
		$status = (($this->input->post ( "status" )) and ($this->input->post ( "status" )) != "" and $is_search == true) ? $this->input->post ( "status" ) : null;

		$periodesiklus_all = $this->m_periode_siklus->get_periode_siklus ($this->_farm, NULL, NULL, $periodesiklus, $namafarm, $namastrain, $status );

		$periodesiklus = $this->m_periode_siklus->get_periode_siklus ($this->_farm, ($page_number * $offset), ($page_number + 1) * $offset, $periodesiklus, $namafarm, $namastrain, $status );

		$total = count ( $periodesiklus_all );
		$pages = ceil ( $total / $offset );

		if (count ( $periodesiklus ) > 0) {
			$data = array (
					'TotalRows' => $pages,
					'Rows' => $periodesiklus
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
	function get_periode_siklus() {
		$kodeperiodesiklus = ($this->input->post ( "kodeperiodesiklus" )) ? $this->input->post ( "kodeperiodesiklus" ) : null;
		$msg = '';
		$periodesiklus = $this->m_periode_siklus->get_periode_siklus_by_id ( $kodeperiodesiklus );
		if($periodesiklus['status_siklus'] == 'C'){
			$msg = '*) Siklus '.$periodesiklus['periode_siklus'].' telah ditutup pada tanggal '.tglIndonesia($periodesiklus['tanggal_tutup']).'. ';
			$selisih_hari = $this->m_periode_siklus->get_selisih_hari($periodesiklus['kode_siklus'],$periodesiklus['kode_farm']);
			if ($selisih_hari->row_array()['selisih_hari'] < 9) {
				$msg .= 'Penutupan mengalami keterlambatan.';
			}
			$periodesiklus['msg'] = $msg;
		}else {
			$periodesiklus['msg'] = '';
		}
		// $this->cetak_r($periodesiklus);
		echo json_encode ( $periodesiklus );
	}
	function check_periode_siklus() {
        $periodesiklus = ($this->input->post ( "periodesiklus" )) ? $this->input->post ( "periodesiklus" ) : null;
        $kodefarm = ($this->input->post ( "kodefarm" )) ? $this->input->post ( "kodefarm" ) : null;
        $check = $this->m_periode_siklus->check_periode_siklus ( $kodefarm, $periodesiklus );
        echo json_encode($check);

    }
	function add_periode_siklus() {
		$periodesiklus = ($this->input->post ( "periodesiklus" )) ? $this->input->post ( "periodesiklus" ) : null;
		$kodefarm = ($this->input->post ( "kodefarm" )) ? $this->input->post ( "kodefarm" ) : null;
		$kodestrain = ($this->input->post ( "kodestrain" )) ? $this->input->post ( "kodestrain" ) : null;
		$status = ($this->input->post ( "status" )) ? $this->input->post ( "status" ) : null;

		$return = array ();

		$return ["form_mode"] = "tambah";
		#$check = $this->m_periode_siklus->check_periode_siklus ( $kodefarm, $periodesiklus );
		#if ($check ['n_result'] > 0) {
		#	$return ["result"] = "failed";
		#	$return ["check"] = "failed";
		#} else {
			$data = array (
					"PERIODE_SIKLUS" => $periodesiklus,
					"KODE_FARM" => $kodefarm,
					"KODE_STRAIN" => $kodestrain,
					"STATUS_PERIODE" => $status
			);
			$result = $this->m_periode_siklus->insert ( $data );
			if ($result) {
				$return ["result"] = "success";
			} else {
				$return ["result"] = "failed";
				$return ["check"] = "success";
			}
		#}
		#echo json_encode ( $return );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}
	function cek_aktivasi() {
		$kode_farm = $this->input->post('kode_farm');
		$nama_farm = $this->input->post('nama_farm');
		$periode   = $this->input->post('periode');
		$return    = array();

		//$kandang_list 	  = $this->m_periode_siklus->kandang_list($kode_farm)->num_rows();		
		$belum_realisasi 	  = $this->m_periode_siklus->belum_realisasi($kode_farm)->result_array();
		$stok_ayam 		  = $this->m_periode_siklus->stok_ayam($kode_farm)->result_array();
		$stk_pakan_gudang = $this->m_periode_siklus->stk_pakan_gudang($kode_farm)->row_array();
		$stk_pakan_kandang = $this->m_periode_siklus->stk_pakan_kandang($kode_farm)->result_array();		
	
		if (!empty($belum_realisasi )) {
			$return ["success"] = false;
			$return ["msg"] = "Penyimpanan perubahan periode siklus Farm $nama_farm, siklus $periode gagal. Terdapat kandang yang belum mengentri realisasi panen : <span class='text-center'>".$this->generateListKandang($belum_realisasi,'no_reg').'</span>';
		}elseif (!empty($stok_ayam)) {
			$return ["success"] = false;
			$return ["msg"] = "Penyimpanan perubahan periode siklus Farm $nama_farm, siklus $periode gagal. Terdapat ekor yang tersisa di kandang berikut : <span class='text-center'>".$this->generateListKandang($stok_ayam,'no_reg').'</span>';
		}elseif (!empty($stk_pakan_kandang)) {
			$return ["success"] = false;
			$return ["msg"] = "Penyimpanan perubahan periode siklus Farm $nama_farm, siklus $periode gagal. Terdapat stok pakan yang tersisa di kandang berikut : <span class='text-center'>".$this->generateListKandang($stk_pakan_kandang,'no_reg').'</span>';
		}elseif ($stk_pakan_gudang['sisa_pakan'] > 0) {
			$return ["success"] = false;
			$return ["msg"] = "Penyimpanan perubahan periode siklus Farm $nama_farm, siklus $periode gagal. Terdapat stok pakan yang tersisa di gudang.";
		}else {
			$return ["success"] = true;
			$return ["msg"] = "";
		}
		
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}

	function generateListKandang($kandangs,$field){
		$result = array();
		if(!empty($kandangs)){
			foreach($kandangs as $k){
				array_push($result,substr($k[$field],-2));
			}
		}
		return '<div> - Kandang '.implode('</div><div> - Kandang ',$result).'</div>';
	}
	function update_periode_siklus() {
		$periodesiklus = ($this->input->post ( "periodesiklus" )) ? $this->input->post ( "periodesiklus" ) : null;
		$kodefarm = ($this->input->post ( "kodefarm" )) ? $this->input->post ( "kodefarm" ) : null;
		$kodestrain = ($this->input->post ( "kodestrain" )) ? $this->input->post ( "kodestrain" ) : null;
		$status = ($this->input->post ( "status" )) ? $this->input->post ( "status" ) : null;
		$kodesiklus = $this->input->post('kodesiklus');
		$return = array ();

		$return ["form_mode"] = "ubah";
		$data = array (
				"PERIODE_SIKLUS" => $periodesiklus,
				"KODE_FARM" => $kodefarm,
				"KODE_STRAIN" => $kodestrain,
				"STATUS_PERIODE" => $status,
				"TGL_UBAH" => date('Y-m-d')
		);
		/* periksa apakah realisasi panen sudah diverifikasi atau belum, khusus untuk level  */
		$sudahVerifikasi = $this->sudahVerifikasiPanen($this->_level_user,$kodesiklus,$kodefarm,$status);
		$sudahVerifikasi['status'] = 1;
		if($sudahVerifikasi['status']){
			/* ubah status kandang siklus menjadi C */
			$this->load->model('forecast/m_kandang_siklus','ks');
			$this->ks->update_by(array('kode_siklus' => $kodesiklus, 'kode_farm' => $kodefarm),array('status_siklus' => 'C'));
			$result = $this->m_periode_siklus->update($data, $kodefarm, $periodesiklus );

			$selisih_hari = $this->m_periode_siklus->get_selisih_hari($kodesiklus,$kodefarm);
			if ($result) {
				$return ["result"] = "success";
				if ($selisih_hari->row_array()['selisih_hari'] >= 9) {
					$return ["message"] = "Perubahan data Periode Siklus $periodesiklus berhasil disimpan.";
				}else {
					$return ["message"] = "Perubahan data Periode Siklus $periodesiklus berhasil disimpan. Proses penutupan siklus mengalami keterlambatan.";
				}
				/** kirim email ke logistik jika pupuk,sekam atau glangsing jumlahnya > 0 */
				$ada_glangsing = $this->m_periode_siklus->check_glangsing($kodesiklus);
				if($ada_glangsing){
					$this->kirim_email($kodefarm,$periodesiklus);
				}
			} else {
				$return ["result"] = "failed";
			}
		}
		else{
			$return ["result"] = "failed";
			$return ["message"] = $sudahVerifikasi['message'];
		}
		#echo json_encode ( $return );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}

	private function sudahVerifikasiPanen($level_user,$kodesiklus,$kodefarm,$status){
		$result = 0;
		$message = '';
		$levelVerifikasi = array('KA'); /* jika yang login dalam list ini maka harus cek verifikasi realisasi panen */
		if(empty($level_user)){
			return array('status' => $result, 'message' => $message);
		}
		if($status != 'N'){
			$result = 1;
			return array('status' => $result, 'message' => $message);
		}

		if(in_array($level_user,$levelVerifikasi)){
			$adaPanen = $this->m_periode_siklus->cekVerifikasiPanen($kodesiklus,$kodefarm)->result_array();
			if(empty($adaPanen)){
				$message = 'Belum ada realisasi panen';
			}else{
				$belumverifikasi = array();
				$belumpanen = array();
				foreach($adaPanen as $ap){
					if(empty($ap['sudah_panen'])){
						array_push($belumpanen,$ap['no_reg']);
					}
			/*		if(empty($ap['jml_sj'])){
						array_push($belumverifikasi,$ap['no_surat_jalan']);
					}
			*/
				}
				if(empty($belumpanen)){
						$result = 1;
					/*
					if(empty($belumverifikasi)){
						$result = 1;
					} else{
						$message .= 'Masih ada surat jalan realisasi panen yang belum diverifikasi '.implode(' ,',$belumverifikasi);
					}
					*/
				} else{
					$message .= 'Masih ada kandang yang belum panen '.implode(' ,',$belumpanen);
				}
			}
		}else{
			$result = 1;
		}
		return array('status' => $result, 'message' => $message);

	}

	public function kirim_email($kode_farm,$periode_siklus)
	{
		$this->load->config('email');
		$this->load->config('stpakan');
		$config = $this->config->item('email_from');		
		$this->load->library('email',$config);				
		$this->email->initialize($config);					
		$namaFarm = $this->config->item('namaFarm');			
		$nama_farm = isset($namaFarm[$kode_farm]) ? $namaFarm[$kode_farm] : 'not defined';
		$message = <<<SQL
		<strong><u>Dear Tim Logistik,</u></strong>	
		<br /><br /><br />
		Dengan ini kami lampirkan informasikan bahwa status siklus Farm {$nama_farm} {$periode_siklus} telah ditutup.<br />
		Mohon bantuannya untuk segera menyelesaikan proses penjualan glangsing untuk siklus tersebut. <br />
		Demikian informasi dari kami.
		<br />
		<br />
		Hormat kami,
		<br /><br />
		Divisi Admin Budidaya<br />
		PT. Wonokoyo Jaya Corporindo<br />
		Jl. Taman Bungkul 1-3-5-7<br />
		Surabaya<br />
		Telp. 031 2956000 ext. 1608<br />
SQL;

		$subject = <<<sbj
			[WJC-ST Pakan] Reminder Penjualan Glangsing Farm {$nama_farm} {$periode_siklus} 
sbj;
		$from = "budidaya@wonokoyo.co.id";
		$alias_from = "WJC ST-Pakan";
		$to = $this->config->item('tutup_siklus');														
		$send = Modules::run('client/email/send_email',$subject,$alias_from,$from,$to,$message);						
	}
}
