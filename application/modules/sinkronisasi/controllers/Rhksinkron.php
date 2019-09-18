<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Rhksinkron extends MX_Controller {
	protected $result;
	protected $_user;
	private $_total_op;
	public function __construct() {
		parent::__construct ();
		$this->load->library('format');
	}
	public function index() {
		$this->load->model('riwayat_harian_kandang/m_rhk','rhk');
		$this->load->helper('file');
		// $tglRhk = $this->HariKemarin(2);
		$tglRhk = '2015-11-18';
		$kode_farm = 'SG5';
		$no_op = array();
		$data = $this->rhk->header_rhk($kode_farm,$tglRhk)->result_array();
		$data_vaksin = $this->rhk->rhk_vaksin($kode_farm,$tglRhk)->result_array();
		$data_produksi = $this->rhk->rhk_produksi($kode_farm,$tglRhk)->result_array();
		$group_vaksin = $this->grouping_vaksin_produksi($data_vaksin);
		$group_produksi = $this->grouping_vaksin_produksi($data_produksi);
		$rhk = $this->grouping_arr($data);
		$bddrhk = $this->mapping_detail($rhk,$group_vaksin,$group_produksi);
		$bddrhk2 = $this->mapping_header($data_produksi);
		
		$format_d = new Format;
		$format_d->set_data($bddrhk);
		write_file('sinkron_file/bddrhk.csv', $format_d->to_csv(),'w');
				
		$format = new Format;
		$format->set_data($bddrhk2);
		$this->load->helper('file');
		write_file('sinkron_file/bddrhk2.csv', $format->to_csv(),'w');
			
	}
	/* grouping berdasarkan no_reg,kode_pakan jadikan satu baris per no_reg */
	private function grouping_arr($data){
		if(empty($data)) return null;
		$tmp = array();
		$sum_arr = array(
			'BRT_TERIMA'
			,'BRT_PAKAI'
			,'STOK_AWAL'
		);		
		foreach($data as $d){
			$jk = $d['JENIS_KELAMIN'];
			$noreg = $d['NO_REG'];
			$kodepj = $d['KODE_BARANG'];
			if(!isset($tmp[$noreg])){
				$tmp[$noreg] = array();
			}
			if(!isset($tmp[$noreg][$kodepj])){
				$tmp[$noreg][$kodepj] = array(
						'B_BRT_TERIMA' => 0,
						'B_BRT_PAKAI' => 0,
						'B_STOK_AWAL' => 0,
						'J_BRT_TERIMA' => 0,
						'J_BRT_PAKAI' => 0,
						'J_STOK_AWAL' => 0,
						'BRT_TERIMA' => 0
				);
			}
			foreach($d as $k => $v){
				if(in_array($k,$sum_arr)){
					$tmp[$noreg][$kodepj][$jk.'_'.$k] += $v;
					if($k == 'BRT_TERIMA'){
						$tmp[$noreg][$kodepj][$k] += $v;
					}
				}
				else{
					$tmp[$noreg][$kodepj][$k] = $v;
				}
			}
			
		}
		return $tmp;
	}
	
	
	private function grouping_vaksin_produksi($data){
		if(empty($data)) return null;
		$r = array();
		foreach($data as $d){
			$noreg = $d['NO_REG'];
			if(!isset($r[$noreg])){
				$r[$noreg] = array();
			}
			array_push($r,$d);
		}
		return $r;
	}
	
	private function mapping_header($data){
		$csv = array(
				'rd2kdunit' => 'KODE_FARM',
				'rd2kdflok' => 'NAMA_FLOK',
				'rd2kandan' => 'KODE_KANDANG',
				'rd2period' => NULL,
				'rd2tanggl' => 'TGL_TRANSAKSI',
				'rd2urutan' => 'NO_URUT',
				'rd2tlbaik' => 'PROD_BAIK',
				'rd2lantai' => 'PROD_LANTAI',
				'rd2tpecah' => 'PROD_PECAH',
				'rd2tbesar' => 'PROD_BESAR',
				'rd2ttipis' => 'PROD_TIPIS',
				'rd2tkecil' => 'PROD_KECIL',
				'rd2tkotor' => 'PROD_KOTOR',
				'rd2tabnor' => 'PROD_ABNORMAL',
				'rd2tib' 	=> 'PROD_IB',
				'rd2tretak' => 'PROD_RETAK',
				'rd2thancr' => 'PROD_HANCUR',
				'rd2ketrng' => NULL,
				'rd2update' => NULL,
				'rd2usrubh' => 'USER_BUAT',
				'rd2tglubh' =>'TGL_BUAT'
				
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
				 
				$z[$key] = $val;
			}
			array_push($r,$z);	
		}
		
		return $r;
	}
	/* sekalian cari jumlah total per op */
	private function mapping_detail($data,$group_vaksin,$group_produksi){
		
		$csv = array(
				'rdkdunit' => 'KODE_FARM',
				'rdkdflok' => 'NAMA_FLOK',
				'rdkandang' => 'KODE_KANDANG',
				'rdperiode' => NULL,
				'rdtanggal' => 'TGL_TRANSAKSI',
				'rdbmati' => 'B_MATI',
				'rdjmati' => 'J_MATI',
				'rdbafkir' => 'B_AFKIR',
				'rdjafkir' => 'J_AFKIR',
				'rdbpndah' => 'B_PINDAH',
				'rdjpndah' => 'J_PINDAH',
				'rdbsexsl' => 'B_SEXSLIP',
				'rdjsexsl' => 'J_SEXSLIP',
				'rdbslksi' => 'B_SELEKSI',
				'rdjslksi' => 'J_SELEKSI',
				'rdblain2' => 'B_LAIN2',
				'rdjlain2' => 'J_LAIN2',
				'rdbtrima' => 'B_TERIMA',
				'rdjtrima' => 'J_TERIMA',
				'rdbtlain' => 'B_TERIMA_LAIN',
				'rdjtlain' => 'J_TERIMA_LAIN',
				'rdbknbl' => 'B_KANIBAL',
				'rdjknbl' => 'J_KANIBAL',
				'rdbcmpr' => 'B_CAMPUR',
				'rdjcmpr' => 'J_CAMPUR',
				'rdket' => NULL,
					
				'rdbbrtbd' => 'B_BERAT_BADAN',
				'rdbjekor' => 'B_JUMLAH',
				'rdjbrtbd' => 'J_BERAT_BADAN',
				'rdjjekor' => 'J_JUMLAH',
				'rdbrttlr' => 'BERAT_TELUR',
				'rdbsak' => NULL,
				'rdjsak' => NULL,
				'rdsakp1' => NULL,
				'rdupdate' => NULL,
				'rdusrubh' => 'USER_BUAT',
				'rdtglubh' => 'TGL_BUAT',
				'transfer2' => NULL,
				'transfer' => NULL,
				'rdurutan' => NULL,
				'rdcvbtn' => NULL,
				'rdunifbt' => NULL,
				'rdcvjtn' => NULL,
				'rdunifjt' => NULL,
				'rdplmknb' => NULL,
				'rdplmknj' => NULL,
				'rdcahyab' => NULL,
				'rdcahyaj' => NULL,
				'rdtglkor' => NULL,
				'rdjamkor' => NULL,
				'rdenerbtn' => NULL,
				'rdenerjtn' => NULL,
				'rdprotbtn' => NULL,
				'rdprotjtn' =>NULL				
		);
		$l_pakan = array(
				'rdkdpkn' => 'KODE_BARANG',
				'rdjumpk' => 'BRT_TERIMA', /* brt_terima jantan dan betina */
				'rdjtrmgd' => 'BRT_TERIMA',/* brt_terima jantan dan betina */
				'rdjtrmkd' => NULL,
				'rdbpaka' => 'B_BRT_PAKAI',/* brt_pakai betina */
				'rdbpk' => 'B_STOK_AWAL',
				'rdbpke' => NULL,
				'rdjpaka' => 'J_BRT_PAKAI',
				'rdjpk' => 'J_STOK_AWAL',
				'rdjpke' => NULL,
				'rdkelua' => NULL,
				'rdket' => 'NAMA_BARANG',
		);
		$l_vaksin = array(				
				'rdvak' => 'KODE_BARANG',
				'rdvakpak' => 'BERAT_PAKAI',
		);
		$l_obat = array(
				'rdobat' => 'KODE_BARANG',
				'rdobtpk' => 'BERAT_PAKAI'
		);
		
		$r = array();
		foreach($data as $noreg => $d){
			$jmlpakan = count($d);
			$kodepj_awal = null;
			$rdcvbtn = !empty($group_produksi[$noreg]['CV_BETINA']) ? $group_produksi[$noreg]['CV_BETINA'] : NULL;;
			$rdcvjtn = !empty($group_produksi[$noreg]['CV_JANTAN']) ? $group_produksi[$noreg]['CV_JANTAN'] : NULL;
			foreach($d as $kpj => $ss){
				$kodepj_awal = $kpj;
				
			}
			
			$z = array();
			foreach($csv as $k => $val){
				if(!empty($val)){
					$z[$k] = $d[$kodepj_awal][$val];		
				}
				else{
					$z[$k] = $val;
					if($k == 'rdcvbtn'){
						$z[$k] = $rdcvbtn;
					}
					else if($k == 'rdcvjtn'){
						$z[$k] = $rdcvjtn;
					}
					
					
				}
			
				if($k == 'rdket'){
					/* loop pakan */
					$i = 1;
					foreach($d as $pakan){
						foreach($l_pakan as $lp => $vp){
							if(!empty($vp)){
								$z[$lp.$i] = $pakan[$vp];
							}
							else{
								$z[$lp.$i] = $vp;
							}
							
						}
						$i++;
					}
					
					/* loop obat */
					if(!empty($group_vaksin[$noreg])){
						foreach($group_vaksin[$noreg] as $v){
							$j = 1;
							foreach($l_obat as $lv => $vv){
								if(!empty($vv)){
									$z[$lv.$j] = $v[$vv];
								}
								else{
									$z[$lv.$j] = NULL;
								}
							}
							$j++;
						}
					}
					else{
						$j = 1;
						foreach($l_obat as $lv => $vv){
							$z[$lv.$j] = NULL;							
						}
						$j++;
					}

					/* loop vaksin */
					if(!empty($group_vaksin[$noreg])){
						foreach($group_vaksin[$noreg] as $v){
							$j = 1;
							foreach($l_vaksin as $lv => $vv){
								if(!empty($vv)){
									$z[$lv.$j] = $v[$vv];
								}
								else{
									$z[$lv.$j] = NULL;
								}
							}
							$j++;
						}
					}
					else{
						$j = 1;
						foreach($l_vaksin as $lv => $vv){
							$z[$lv.$j] = NULL;
						}
						$j++;
					}
				}
				
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
