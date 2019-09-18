<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Monitoringpakan extends MY_Controller {
	protected $result;
	protected $_user;
	protected $grup_farm;

	public function __construct() {
		parent::__construct ();
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
	}
	public function index() {
		$data['list_farm'] = Modules::run('forecast/forecast/list_farm',$this->grup_farm,NULL,false);
		$this->load->view('report/bdy/monitoringpakan',$data);
	}

	public function listpakan(){
		$kodefarm = $this->input->get('farm');
		$docin = $this->input->get('docin');
		$flock = $this->input->get('flock');
		$this->load->model('report/m_monitoringpakan','mmp');
		$listpp = $this->mmp->listpp($docin,$kodefarm)->result_array();
		$listdo = $this->mmp->listdo($docin,$kodefarm)->result_array();
		$terimakandang = $this->mmp->listterimakandang($docin,$kodefarm)->result_array();
		$terimagudang = $this->mmp->listterimagudang($docin,$kodefarm)->result_array();
		$rhk = $this->mmp->listrhk($docin,$kodefarm)->result_array();
		$sakkembali = $this->mmp->listsakkembali($docin,$kodefarm)->result_array();
		$forecast = $this->mmp->listforecast($docin,$kodefarm,$flock)->result_array();

		$begin = new DateTime($docin);
		$end = new DateTime($docin);
		date_add($end,date_interval_create_from_date_string('32 days'));
		$interval = DateInterval::createFromDateString('1 day');
		$listpakan = array('1126-10-11' => 'BR 1 Super','1127-10-12' => 'BR 2 Super');
		$data['tglkebutuhan'] = new DatePeriod($begin, $interval, $end);
		$data['listpakan'] = $listpakan;
		$data['orderpp'] = $this->getOrderPP($listpp,$listpakan);
		$data['listpp'] = $this->groupingpp($listpp,$listpakan);
		$data['listdo'] = $this->groupingDo($listdo,$listpakan);
		$data['terimagudang'] = $this->groupingDo($terimagudang,$listpakan);
		$data['terimakandang'] = $this->groupingTerimaKandangRhk($terimakandang,'TGL_KEBUTUHAN');
		$data['rhk'] = $this->groupingTerimaKandangRhk($rhk,'TGL_TRANSAKSI');
		$data['sakkembali'] = $this->groupingTerimaKandangRhk($sakkembali,'TGL_KEBUTUHAN');
		$data['forecast'] = $this->groupingforecast($forecast,$listpakan);
		$this->load->view('report/bdy/list_monitoringpakan',$data);
	}
	/* cari kebutuhan berdasarkan pp */
	private function getOrderPP($arr,$listpakan){
		$result = array();
		$tmp = array();
		foreach($arr as $r){
			$tglkebutuhan = $r['TGL_KEBUTUHAN'];
			if(!isset($result[$tglkebutuhan])){
				$result[$tglkebutuhan] = array();
				foreach($listpakan as $k => $val){
					$result[$tglkebutuhan][$k] = 0;
				}
			}
			$result[$tglkebutuhan][$r['KODE_BARANG']] += $r['JML_ORDER'];
		}
		return $result;
	}

	/* grouping pp dengan key adalah tgl kebutuhan awal*/
	private function groupingpp($arr,$listpakan){
		$result = array();
		$tmp = array();
		foreach($arr as $r){
			$tglkebutuhan = $r['TGL_KEBUTUHAN'];
			$pp = $r['NO_LPB'];
			if(!isset($tmp[$pp])){
				$tmp[$pp] = $tglkebutuhan;
			}
			if(!isset($result[$tmp[$pp]])){
				$result[$tmp[$pp]] = array(
					'no_lpb' => $pp,
					'tgl_kirim' => $r['TGL_KIRIM'],
					'jml_pp' => array(),
					'jml_order' => array(),
					'tglkebutuhan' => array(),
					'tgl_approve' => $r['TGL_APPROVE1'],
					'tgl_rilis' => $r['TGL_RILIS'],
					'tgl_review' => $r['TGL_REVIEW'],
				);
				foreach($listpakan as $k => $v){
					$result[$tmp[$pp]]['jml_order'][$k] = array();
				}
			}
			if(!isset($result[$tmp[$pp]]['jml_pp'][$r['KODE_BARANG']])){
				$result[$tmp[$pp]]['jml_pp'][$r['KODE_BARANG']] = 0;
			}
			$result[$tmp[$pp]]['jml_pp'][$r['KODE_BARANG']] += $r['JML_ORDER'];
			array_push($result[$tmp[$pp]]['tglkebutuhan'],$tglkebutuhan);
			array_push($result[$tmp[$pp]]['jml_order'][$r['KODE_BARANG'] ],$r['JML_ORDER']);
		}
		return $result;
	}

	/* grouping pp dengan key adalah tgl kebutuhan awal*/
	private function groupingforecast($arr,$listpakan){
		$result = array();
		$tmp = array();
		foreach($arr as $r){
			$tglkebutuhan = $r['TGL_KEBUTUHAN'];
			$kirim = $r['TGL_KIRIM'];
			if(!isset($tmp[$kirim])){
				$tmp[$kirim] = $tglkebutuhan;
			}
			if(!isset($result[$tmp[$kirim]])){
				$result[$tmp[$kirim]] = array(
					'tgl_kirim' => $r['TGL_KIRIM'],
					'jml_order' => array(),
					'tglkebutuhan' => array(),
					'jml_forecast' => array()
				);
				foreach($listpakan as $k => $v){
					$result[$tmp[$kirim]]['jml_order'][$k] = array();
				}
			}
			if(!isset($result[$tmp[$kirim]][$r['TGL_KIRIM']])){
				$result[$tmp[$kirim]][$r['TGL_KIRIM']] = array();
				foreach($listpakan as $k => $val){
					$result[$tmp[$kirim]][$r['TGL_KIRIM']][$k] = array();
				}
			}
			foreach($listpakan as $k => $val){
				if(!isset($result[$tmp[$kirim]][$r['TGL_KIRIM']][$k][$tglkebutuhan])){
					$result[$tmp[$kirim]][$r['TGL_KIRIM']][$k][$tglkebutuhan] = 0;
				}
				if($r['KODE_BARANG'] == $k){
					$result[$tmp[$kirim]][$r['TGL_KIRIM']][$k][$tglkebutuhan] = $r['JML_FORECAST'];
				}
			}
			array_push($result[$tmp[$kirim]]['tglkebutuhan'],$tglkebutuhan);
		}
		return $result;
	}

	private function groupingTerimaKandangRhk($arr,$key){
		$result = array();
		$tmp = array();
		foreach($arr as $r){
			$tglkebutuhan = $r[$key];
			if(!isset($result[$tglkebutuhan])){
				$result[$tglkebutuhan] = array(
					'jml' => array(),
					'tgl_buat' => $r['TGL_BUAT'],
				);
			}

			$result[$tglkebutuhan]['jml'][$r['KODE_BARANG']] = $r['JML'];
		}
		return $result;
	}

	/* grouping do dengan key adalah no_lpb*/
	private function groupingDo($arr,$listpakan){
		$result = array();
		$kolom = 'NO_LPB';
		foreach($arr as $r){
			$pp = $r[$kolom];
			if(!isset($result[$pp])){
				$result[$pp] = array(
					'NO_DO' => array(),
					'TGL_BUAT' => array()
				);
			}
			$do = $r['NO_DO'];
			$tgl_buat = $r['TGL_BUAT'];
			if(!isset($result[$pp][$do])){
				array_push($result[$pp]['NO_DO'],$do);
				array_push($result[$pp]['TGL_BUAT'],$tgl_buat);
				$result[$pp][$do] = array();
				foreach($listpakan as $k => $val){
					$result[$pp][$do][$k] = 0;
				}
			}
			foreach($listpakan as $k => $val){
				if($r['KODE_BARANG'] == $k){
					$result[$pp][$do][$k] = $r['JML'];
				}
			}
		}
		return $result;
	}

	public function listDocin(){
		$kode_farm = $this->input->get('farm');
		$this->load->model('forecast/m_kandang_siklus','ks');
		$ld = $this->ks->as_array()->order_by('tgl_doc_in','desc')->get_many_by('kode_farm = \''.$kode_farm.'\' and tgl_doc_in <= getdate()');
		/* ambil tgl_doc_in saja */
		$result = array();
		foreach($ld as $l){
			$tgldocin = $l['TGL_DOC_IN'];
			if(!isset($result[$tgldocin])){
				$result[$tgldocin] = array(
					'populasi' => 0,
					'flock' => $l['FLOK_BDY'],
					'jmlkandang' => 0
				);
			}
			$result[$tgldocin]['populasi'] += $l['JML_POPULASI'];
			$result[$tgldocin]['jmlkandang']++;
		}
		echo json_encode($result);
	}
}
