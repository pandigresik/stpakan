<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Barang extends MX_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('stpakan');
		$this->load->model("m_barang");
	}
	
	function index(){
		$satuan = $this->m_barang->get_satuanbarang();
		
		$data["satuan"] = $satuan;
		$this->load->view("barang/barang_list", $data);
	}
	
	function get_pagination(){
		$offset = 10;
		$page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
		$is_search = ($this->input->post('search')) ? $this->input->post('search') : false;
		
		$jenisbarang = (($this->input->post("jenisbarang")) and $this->input->post("jenisbarang") != "" and $is_search == true) ? $this->input->post("jenisbarang") : null;
		$tipebarang = (($this->input->post("tipebarang")) and $this->input->post("tipebarang") != "" and $is_search == true) ? $this->input->post("tipebarang") : null;
		$kodebarang = (($this->input->post("kodebarang")) and $is_search == true) ? $this->input->post("kodebarang") : null;
		$namabarang = (($this->input->post("namabarang")) and $is_search == true) ? $this->input->post("namabarang") : null;
		$bentukbarang = (($this->input->post("bentukbarang")) and $this->input->post("bentukbarang") != "" and $is_search == true) ? $this->input->post("bentukbarang") : null;
		$satuan = (($this->input->post("satuan")) and $this->input->post("satuan") != "" and $is_search == true) ? $this->input->post("satuan") : null;
		$status = (($this->input->post("status")) and $this->input->post("status") != "" and $is_search == true) ? $this->input->post("status") : null;

		$barang_all = $this->m_barang->get_barang(null, null, $jenisbarang, $tipebarang, $kodebarang, $namabarang, $bentukbarang, $satuan, $status);
		
		$barang = $this->m_barang->get_barang(($page_number*$offset), ($page_number+1)*$offset, $jenisbarang, $tipebarang, $kodebarang, $namabarang, $bentukbarang, $satuan, $status);
		
		$total =  count($barang_all);		
		$pages = ceil($total/$offset);
		
		if(count($barang) > 0){
			$data = array(
				'TotalRows' => $pages,
				'Rows' => $barang
			);
		
			$this->output->set_content_type('application/json');
			echo json_encode(array($data));
		}else{
			$data = array(
				'TotalRows' => 0,
				'Rows' => 0
			);
			echo json_encode($data);
		}
		
		exit;
	}
	
	function get_barang(){
		$kodebarang = ($this->input->post("kodebarang")) ? $this->input->post("kodebarang") : null;
		
		$barang = $this->m_barang->get_barang_by_id($kodebarang);
				
		echo json_encode($barang);
	}
	
	function get_grupbarang(){
		$grupbarang = $this->m_barang->get_mastergrupbarang();
				
		echo json_encode($grupbarang);
	}
	
	function add_master_grupbarang(){
		$grupjenisbaru = $this->input->post("deskripsi");
		$id = $this->m_barang->add_masterbarang($grupjenisbaru);
		
		echo json_encode(array("id"=>$id));
	}
		
	function add_barang(){
		date_default_timezone_set("Asia/Jakarta"); 
		$now = date("m/d/Y H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")));
		$user = strtoupper("ADMINTEST");
		
		$jenisbarang = ($this->input->post("jenisbarang")) ? $this->input->post("jenisbarang") : null;
		$tipepakan = ($this->input->post("tipepakan")) ? $this->input->post("tipepakan") : null;
		$kodebarang = ($this->input->post("kodebarang")) ? $this->input->post("kodebarang") : null;
		$jenisgrupbarang = ($this->input->post("jenisgrupbarang")) ? $this->input->post("jenisgrupbarang") : null;
		$namabarang = ($this->input->post("namabarang")) ? $this->input->post("namabarang") : null;
		$namaaliasbarang = ($this->input->post("namaaliasbarang")) ? $this->input->post("namaaliasbarang") : null;
		$bentukbarang = ($this->input->post("bentukbarang")) ? $this->input->post("bentukbarang") : null;
		$satuanbarang = ($this->input->post("satuanbarang")) ? $this->input->post("satuanbarang") : null;
		$jeniskelaminternakbetina = ($this->input->post("jeniskelaminternakbetina")) ? $this->input->post("jeniskelaminternakbetina") : null;
		$jeniskelaminternakjantan = ($this->input->post("jeniskelaminternakjantan")) ? $this->input->post("jeniskelaminternakjantan") : null;
		$usiaawal = ($this->input->post("usiaawal")) ? $this->input->post("usiaawal") : null;
		$usiaakhir = ($this->input->post("usiaakhir")) ? $this->input->post("usiaakhir") : null;
		$status = ($this->input->post("status")) ? $this->input->post("status") : null;
		$flag_tipepakan = ($this->input->post("flag_tipepakan")) ? $this->input->post("flag_tipepakan") : null;
		
		$data = array(
			"kode_barang"=>$kodebarang,
			"alias"=>$namaaliasbarang,
			"nama_barang"=>$namabarang,
			"jenis_barang"=>$jenisbarang,
			"grup_barang"=>$jenisgrupbarang,
			"uom"=>$satuanbarang,
			"bentuk_barang"=>$bentukbarang,
			"tipe_barang"=>$tipepakan,
			"pakan_betina"=>($flag_tipepakan == "pakan") ? $jeniskelaminternakbetina : null,
			"pakan_jantan"=>($flag_tipepakan == "pakan") ? $jeniskelaminternakjantan : null,
			"usia_awal_ternak"=>($flag_tipepakan == "pakan") ? $usiaawal : null,
			"usia_akhir_ternak"=>($flag_tipepakan == "pakan") ? $usiaakhir : null,
			"status_barang"=>$status,
			"tgl_buat"=>$now,
			"user_buat"=>$user
		);
		
		$return = array();
		$return["form_mode"] = "tambah";
		
		$barang = $this->m_barang->get_barang_by_id($kodebarang);
		
		if(count($barang) > 0 and isset($barang["kode_barang"])){
			$return["result"] = "failed";
			$return["msg"] = "Kode barang sudah digunakan.";
		}else{
			//cetak_r($data);
			$result = $this->m_barang->insert($data);
			if($result){
				$return["result"] = "success";
			}else{
				$return["result"] = "failed";
				$return["msg"] = "Penyimpanan data Barang dengan kode " + $kodebarang + " gagal dilakukan";
			}
		}
		
		
		#echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
	}
	
	function update_barang(){
		$jenisbarang = ($this->input->post("jenisbarang")) ? $this->input->post("jenisbarang") : null;
		$tipepakan = ($this->input->post("tipepakan")) ? $this->input->post("tipepakan") : null;
		$kodebarang = ($this->input->post("kodebarang")) ? $this->input->post("kodebarang") : null;
		$jenisgrupbarang = ($this->input->post("jenisgrupbarang")) ? $this->input->post("jenisgrupbarang") : null;
		$namabarang = ($this->input->post("namabarang")) ? $this->input->post("namabarang") : null;
		$namaaliasbarang = ($this->input->post("namaaliasbarang")) ? $this->input->post("namaaliasbarang") : null;
		$bentukbarang = ($this->input->post("bentukbarang")) ? $this->input->post("bentukbarang") : null;
		$satuanbarang = ($this->input->post("satuanbarang")) ? $this->input->post("satuanbarang") : null;
		$jeniskelaminternakbetina = ($this->input->post("jeniskelaminternakbetina")) ? $this->input->post("jeniskelaminternakbetina") : null;
		$jeniskelaminternakjantan = ($this->input->post("jeniskelaminternakjantan")) ? $this->input->post("jeniskelaminternakjantan") : null;
		$usiaawal = ($this->input->post("usiaawal")) ? $this->input->post("usiaawal") : null;
		$usiaakhir = ($this->input->post("usiaakhir")) ? $this->input->post("usiaakhir") : null;
		$status = ($this->input->post("status")) ? $this->input->post("status") : null;
		$flag_tipepakan = ($this->input->post("flag_tipepakan")) ? $this->input->post("flag_tipepakan") : null;
		
		$data = array(
			"alias"=>$namaaliasbarang,
			"nama_barang"=>$namabarang,
			"jenis_barang"=>$jenisbarang,
			"grup_barang"=>$jenisgrupbarang,
			"uom"=>$satuanbarang,
			"bentuk_barang"=>$bentukbarang,
			"tipe_barang"=>$tipepakan,
			"pakan_betina"=>($flag_tipepakan == "pakan") ? $jeniskelaminternakbetina : null,
			"pakan_jantan"=>($flag_tipepakan == "pakan") ? $jeniskelaminternakjantan : null,
			"usia_awal_ternak"=>($flag_tipepakan == "pakan") ? $usiaawal : null,
			"usia_akhir_ternak"=>($flag_tipepakan == "pakan") ? $usiaakhir : null,
			"status_barang"=>$status
		);
		
		$result = $this->m_barang->update($data, $kodebarang);
		$return = array();
		$return["form_mode"] = "ubah";
		if($result){
			$return["result"] = "success";
		}else{
			$return["result"] = "failed";
		}
		
		#echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
		
	}
	
	function konfigurasi_pakan_ternak(){
		$this->load->view("master/barang/konfigurasi_barang");
	}
	
	function listKonfigurasiBarang(){
		$paramCari = $this->input->post('paramCari');
		$data['list_cari'] = $paramCari;
		$data["list_barang"] = $this->m_barang->get_sinkron_barang($paramCari)->result_array();
		$data["list_grup_barang"] = $this->m_barang->get_mastergrupbarang();
		$this->load->view("master/barang/list_konfigurasi_barang",$data);
	}
	
	function updateStatusBarang(){
		$kodeBarang = $this->input->post('kodeBarang');
		$result = array('status' => 0);
		$data = array('status_barang' => 'A');
		if(!empty($kodeBarang)){
			if($this->m_barang->update_in('kode_barang',$kodeBarang,$data)){
				$result['status'] = 1;
			}
		}
		echo json_encode($result);
	}

	function cek_kodebarang(){
		$kodebarang = ($this->input->post("kodebarang")) ? $this->input->post("kodebarang") : null;
	
		$barang = $this->m_barang->cek_kode_barang($kodebarang);
		
		echo json_encode(array("jumlah"=>$barang["n_result"]));
	}
}
	