<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Opsinkron extends MX_Controller {
	protected $result;
	protected $_user;
	private $_total_op;
	public function __construct() {
		parent::__construct ();
		$this->load->library('format');
	}
	public function index() {
		$this->load->model('permintaan_pakan/m_op','op');
		$this->load->model('permintaan_pakan/m_op_d','op_d');
		$this->load->helper('file');
		$minDate = $this->HariKemarin(2);
		$where = array(
			'tgl_ubah >= \''.$minDate.'\'',
			'tgl_lpb >= \''.$minDate.'\'',
		);
		$no_op = array();
		$data = $this->op->get_op_sinkron($where)->result_array();
		$data_detail = $this->op_d->get_op_sinkron($where)->result_array();
		
		$p = $this->mapping_detail($data_detail);
		$format_d = new Format;
		$format_d->set_data($p);
		write_file('sinkron_file/detail_op.csv', $format_d->to_csv(),'w');
		/* mapping detail harus dilakukan terlebih dahulu */
		$r = $this->mapping_header($data);
		$format = new Format;
		$format->set_data($r);
		$this->load->helper('file');
		write_file('sinkron_file/header_op.csv', $format->to_csv(),'w');
			
	}
	
	private function mapping_header($data){
		$csv = array(
			'ophtgglop' => 'TGL_OP',
			'ophnomoop'=> 'NO_OP' ,
			'ophcabang' => NULL,
			'ophkdlang' => 'KODE_PELANGGAN',
			'ophtglakh' => 'TGL_KADALUARSA_OP',
			'ophttzako' => NULL,
			'ophttqtyo' => NULL,
			'ophttjmlh' => NULL,
			'ophnokont' => NULL,
			'ophkandng' => NULL,
			'ophkdpwk' => NULL,
			'ophnopol' => NULL,
			'ophexpkd' => NULL,
			'ophjenis' => 'JENIS_TRANSAKSI',
			'ophkdsup' => NULL,
			'ophtglrhk' => NULL	
		);
		$columTanggal = array(
			'TGL_OP','TGL_KADALUARSA_OP'
		);
		$colum_sum = array('ophttzako','ophttqtyo');
		$map_colum_sum = array('ophttzako' => 'jml_order' ,'ophttqtyo'=> 'kg_order');
		$r = array();
		foreach($data as $d){
			$z = array();
			foreach($csv as $key => $header){
				$val = NULL;
				if(!empty($d[$header])){
					if(in_array($header,$columTanggal)){
						$d[$header] = $this->convertTgl($d[$header]);	
					}	
					$val = $d[$header];
				}
				else{
					if(in_array($key,$colum_sum)){
						$val = $this->_total_op[$d['NO_OP']][$map_colum_sum[$key]];	
					}	
				}
				if($key == 'ophjenis'){
					$val = trim($val);
				} 
				$z[$key] = $val;
			}
			array_push($r,$z);	
		}
		
		return $r;
	}
	/* sekalian cari jumlah total per op */
	private function mapping_detail($data){
		$this->_total_op = array();
		$csv = array(
				'ophnomoop'=> 'NO_OP' ,
				'opdkodbrg' => 'KODE_BARANG',
				'opdzakord' => 'jml_order',
				'opdjmlord' => 'kg_order',
				'opdhrgbrg' => 'harga',
		);
		$colum_sum = array('jml_order','kg_order');
		$r = array();
		foreach($data as $d){
			$z = array();
			foreach($csv as $key => $header){
				$val = NULL;
				if(!empty($d[$header])){
					$val = $d[$header];
					if(in_array($header, $colum_sum)){
						if(!isset($this->_total_op[$d['NO_OP']])){
							$this->_total_op[$d['NO_OP']] = array('jml_order' => 0,'kg_order' => 0);
						}
						$this->_total_op[$d['NO_OP']][$header] += $val; 
					}
				}
					
				$z[$key] = $val;
			}
			array_push($r,$z);
		}
		
		return $r;
	}
	private function convertTgl($tgl){
		$hasil = NULL;
		if(!empty($tgl)){
			$r = explode('-',$tgl);
			$j = count($r);
			$t = array();
			while($j > 0){
				$index = $j - 1;
				array_push($t,$r[$index]);
				$j--;
			}
			$hasil = implode('/',$t);
		}
		return $hasil;
	}
	
	private function HariKemarin($x) {
		$date = new \DateTime();
		$param = 'P'.$x.'D';
		return $date->sub(new \DateInterval($param))->format('Y-m-d');
	}
	
}
