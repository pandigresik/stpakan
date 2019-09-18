<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Simulasi extends MY_Controller{
	protected $result;
	private $grup_farm;
	public function __construct(){
		parent::__construct();
		$this->load->helper('stpakan');
		$this->result = array('status' => 0, 'content'=> '', 'message' => '');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
	}
	public function index(){
		$data['list_farm'] = Modules::run('forecast/forecast/list_farm',$this->grup_farm,null,false);
		$this->load->view('forecast/bdy/simulasi',$data);
	}

	public function kandang_pending($kodeFarm){
		$this->load->model('forecast/m_forecast','mf');
		$arrTmp = $this->mf->list_kandang_pending($kodeFarm)->result_array();

		$farm = $tahun = $bulan = $tgl = $_tmp = '';
		$arr = array();
		foreach($arrTmp as $row){
			$_tmp = explode('-',$row['tgl_chickin']);
			$farm = $row['nama_farm'];
			$kode_farm = $row['kode_farm'];
			$state = $row['state'];
			$tahun = $_tmp[0];
			$bulan = convert_ke_bulan($_tmp[1]);
			$tgl = $_tmp[2];
			$bisaDipilih = 0;
			if(!isset($arr[$farm])){
				$arr[$farm] = array();
			}
			if(!isset($arr[$farm][$tahun])){
				$arr[$farm][$tahun] = array();
			}
			if(!isset($arr[$farm][$tahun][$bulan])){
				$arr[$farm][$tahun][$bulan] = array();
			}
			if(!isset($arr[$farm][$tahun][$bulan][$tgl])){
				$arr[$farm][$tahun][$bulan][$tgl] = array();
			}

			$text_kandang = 'Kandang '.$row['kode_kandang'].' ('.$row['populasi'].' ekor)#'.$kode_farm.'/'.$row['kode_kandang'].'/'.$row['tipe_lantai'].'/'.$row['tipe_kandang'].'/'.$row['kapasitas'].'/'.$row['populasi'].'#'.$row['no_reg'];
			array_push($arr[$farm][$tahun][$bulan][$tgl],$text_kandang);
		}
		$data['tree'] = create_tree($arr);
		print_r($data['tree']);
	}
}
