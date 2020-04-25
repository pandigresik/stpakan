<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Report extends MY_Controller {
	protected $result;
	protected $_user;
	protected $grup_farm;
	protected $adg_tampil = array(7,14,21,28);
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_report','report');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_user = $this->session->userdata('kode_user');
		$this->adg_tampil = array(7,14,21,28);
	}
	public function index() {

	}

	public function stok_pakan(){
		$user_level = $this->session->userdata('level_user');
		switch($user_level){
			case 'KF':
				$kode_farm = $this->session->userdata('kode_farm');
				$pilih_farm = 0;
				break;
			case 'KD':
				$kode_farm = NULL;
				$pilih_farm = 1;
				break;
			case 'KDV':
				$kode_farm = NULL;
				$pilih_farm = 1;
				break;
			case 'DB':
				$kode_farm = NULL;
				$pilih_farm = 1;
				break;
			case 'KA':
				$kode_farm = NULL;
				$pilih_farm = 1;
				break;
			default:
				$kode_farm = $this->session->userdata('kode_farm');
				$pilih_farm = 0;
		}
	//	$data['detail_stok_pakan'] = $this->load->view('report/detail_stok_pakan','',true);
		$data['list_farm'] = $this->list_farm($kode_farm);
		$data['nama_farm'] = ($user_level == 'KF') ? strip_tags($data['list_farm']): null;
		$data['pilih_farm'] = $pilih_farm;
		$this->load->view('report/stok_pakan',$data);
	}
	/* list farm berdasarkan user */
	public function userFarm(){
	//	$this->_user = 'PG0001';
		$r = $this->db->select('mf.kode_farm,mf.nama_farm')
						->join('pegawai_d pd','pd.kode_farm = mf.kode_farm and pd.kode_pegawai = \''.$this->_user.'\'')
						->group_by(array('mf.kode_farm','mf.nama_farm'))
						->get('m_farm mf')
						->result_array();

		$this->result['status'] = 1;
		$this->result['content'] = $r;
		echo json_encode($this->result);
	}

	public function detailInformasi(){
		$tahun = $this->input->get('tahun');
		$data['tipe'] = $this->input->get('tipe');
		$data['pilih_farm'] = $this->input->get('farm');
		$farm = $this->report->listFarm($this->input->get('farm'));

		$data['kandang'] = $this->report->detailInformasi($farm,$tahun)->result_array();
		$this->load->view('report/bdy/detailInformasi',$data);
	}

	private function list_farm($id = null){

		$arr = $this->report->list_farm($id,$this->grup_farm)->result_array();
		$tmp = array();
		$t = '';
		$checked = '';
		/* check langsung jika yang login kepala farm */
		if(!empty($id)){
			$checked = 'checked';
		}
		if(!empty($arr)){
			foreach($arr as $cb){
				$t = '<div class="col-md-6"><div class="checkbox"><label><input type="checkbox" value="'.$cb['kode_farm'].'" '.$checked.' />'.$cb['nama_farm'].' ('.$cb['kode_strain'].')</label></div></div>';
				array_push($tmp,$t);
			}
		}
		return implode(' ',$tmp);

	}

	public function list_kandang(){
		$user_level = $this->session->userdata('level_user');
		$nama_kandang = $this->input->post('nama_kandang');
		$grup_farm = $this->input->post('grup_farm') || 'brd';
		switch($user_level){
			case 'KF':
				$kode_farm = $this->session->userdata('kode_farm');
				break;
			default:
				$kode_farm = NULL;
				break;
		}
		/* cari semua kandang yang terdaftar */
		if(!empty($kode_farm)){
			$this->db->where(array('kode_farm' => $kode_farm));
		}
		if(!empty($nama_kandang)){
			$this->db->where('nama_kandang like \'%'.$nama_kandang.'%\'');
		}

		/* cari berdasarkan grup farm */
		switch($grup_farm){
			case 'bdy':
				$this->db->where('no_flok is not null');
				break;
			case 'brd':
				$this->db->where('no_flok is null');
				break;
		}
		echo json_encode($this->db->select('kode_farm,kode_kandang,nama_kandang')->get('m_kandang')->result());

	}

	public function detail_stok_pakan($kode_farm,$tgl_transaksi,$tgl_akses,$nama_farm = NULL){
		$user_level = $this->session->userdata('level_user');
		switch($this->grup_farm){
			case 'brd':
				$list_stok = $this->report->stok_pakan($kode_farm,$tgl_transaksi,$tgl_akses);
				$data['list_stok'] = $this->data_stok_pakan_brd($list_stok);
				$view_data = 'report/brd/detail_stok_pakan';
				break;
			case 'bdy':
				$stok_kavling = $this->report->stok_kavling_bdy($kode_farm,$tgl_transaksi);
				$stok_kandang = $this->report->stok_kandang_bdy($kode_farm,$tgl_transaksi);

				/* grouping dulu per kavling, perpakan*/
				// cetak_r($stok_kavling);
				$skv = $this->grouping_stok_kavling($stok_kavling);
				// cetak_r($skv);

				$skg = $this->grouping_stok_kandang($stok_kandang);
				$skp = $this->grouping_summary_stok($skv['data'],$skg['data']);

	//			$stok_pakan = $this->grouping_stok_pakan($stok_kavling,$stok_kandang);
				$data['stok_kavling'] = $this->load->view('report/bdy/stok_kavling',array('kode_farm'=>$kode_farm,'stok'=>$skv,'tgl_transaksi'=>$tgl_transaksi),true);
				$data['stok_kandang'] = $this->load->view('report/bdy/stok_kandang',array('stok'=>$skg,'tgl_transaksi'=>$tgl_transaksi),true);
				$data['summary_stok'] = $this->load->view('report/bdy/summary_stok',array('stok'=>$skp,'tgl_transaksi'=>$tgl_transaksi),true);
				$view_data = 'report/bdy/detail_stok_pakan';
				break;
		}

		$data['tgl_akses'] = $tgl_akses;
		$data['tgl_transaksi'] = $tgl_transaksi;
		$data['nama_farm'] = ($user_level == 'KF') ? null : $nama_farm;
		return $this->load->view($view_data,$data,true);
	}
	/* grouping per pakan */
	private function grouping_summary_stok($skv,$skg){

		$d_kavling = array();
		$d_kandang = array();
		$result = array();
		foreach($skv as $kv => $perkavling){
			foreach($perkavling as $kb => $perbarang){
				if(!isset($d_kavling[$kb])){
					$d_kavling[$kb] = array();
				}

				if(!isset($result[$kb])){
					$result[$kb] = array(
						'nama_barang' => $perbarang['data']['nama_barang'],
						'gudang_awal_sak' => 0,
						'gudang_awal_kg' => 0,
						'gudang_terima_sak' => 0,
						'gudang_terima_kg' => 0,
						'gudang_keluar_sak' => 0,
						'gudang_keluar_kg' => 0,
						'gudang_akhir_sak' => 0,
						'gudang_akhir_kg' => 0,
						'kandang_awal_sak' => 0,
						'kandang_awal_kg' => 0,
						'kandang_terima_sak' => 0,
						'kandang_terima_kg' => 0,
						'kandang_pakai_sak' => 0,
						'kandang_pakai_kg' => 0,
						'kandang_akhir_sak' => 0,
						'kandang_akhir_kg' => 0,
					);
				}

				/* untuk detail kandang */
				if(!isset($d_kavling[$kb][$kv])){
					$d_kavling[$kb][$kv] = array(
						'stok_awal_sak' => 0,
						'stok_awal_kg' => 0,
						'terima_sak' => 0,
						'terima_kg' => 0,
						'keluar_sak' => 0,
						'keluar_kg' => 0,
						'stok_akhir_sak' => 0,
						'stok_akhir_kg' => 0,
						'umur_pakan' => $perbarang['data']['umur_pakan_gudang']
					);
				}
				$d_kavling[$kb][$kv]['stok_awal_sak'] += $perbarang['data']['stok_awal_sak'];
				$d_kavling[$kb][$kv]['terima_sak'] += $perbarang['data']['gudang_terima_sak'];
				$d_kavling[$kb][$kv]['keluar_sak'] += $perbarang['data']['gudang_keluar_sak'];
				$d_kavling[$kb][$kv]['stok_akhir_sak'] += $perbarang['data']['stok_akhir_sak'];

				$d_kavling[$kb][$kv]['stok_awal_kg'] += $perbarang['data']['stok_awal_kg'];
				$d_kavling[$kb][$kv]['terima_kg'] += $perbarang['data']['gudang_terima_kg'];
				$d_kavling[$kb][$kv]['keluar_kg'] += $perbarang['data']['gudang_keluar_kg'];
				$d_kavling[$kb][$kv]['stok_akhir_kg'] += $perbarang['data']['stok_akhir_kg'];

				$result[$kb]['gudang_awal_sak'] += $perbarang['data']['stok_awal_sak'];
				$result[$kb]['gudang_terima_sak'] += $perbarang['data']['gudang_terima_sak'];
				$result[$kb]['gudang_keluar_sak'] += $perbarang['data']['gudang_keluar_sak'];
				$result[$kb]['gudang_akhir_sak'] += $perbarang['data']['stok_akhir_sak'];

				$result[$kb]['gudang_awal_kg'] += $perbarang['data']['stok_awal_kg'];
				$result[$kb]['gudang_terima_kg'] += $perbarang['data']['gudang_terima_kg'];
				$result[$kb]['gudang_keluar_kg'] += $perbarang['data']['gudang_keluar_kg'];
				$result[$kb]['gudang_akhir_kg'] += $perbarang['data']['stok_akhir_kg'];

				if(!isset($d_kandang[$kb])){
					$d_kandang[$kb] = array();
				}

			}
		}
		foreach($d_kandang as $_kb => $sum_kandang){
			foreach($skg as $_kd => $perkandang){
					$perpakan = isset($perkandang['detail'][$_kb]) ? $perkandang['detail'][$_kb] : array('stok_awal_kg'=>0,'terima_kg'=>0,'pakai_kg'=>0,'stok_pakan_kg'=>0,'stok_awal_sak'=>0,'terima_sak'=>0,'pakai_sak'=>0,'stok_pakan_sak'=>0);

					if(!isset($d_kandang[$_kb][$_kd])){
						$d_kandang[$_kb][$_kd] = array();
						$d_kandang[$_kb][$_kd]['stok_awal_sak'] = formatAngka($perpakan['stok_awal_sak'],0);
						$d_kandang[$_kb][$_kd]['stok_awal_kg'] = formatAngka($perpakan['stok_awal_kg'],3);
						$d_kandang[$_kb][$_kd]['terima_sak'] = formatAngka($perpakan['terima_sak'],0);
						$d_kandang[$_kb][$_kd]['terima_kg'] = formatAngka($perpakan['terima_kg'],3);
						$d_kandang[$_kb][$_kd]['pakai_sak'] = formatAngka($perpakan['pakai_sak'],0);
						$d_kandang[$_kb][$_kd]['pakai_kg'] = formatAngka($perpakan['pakai_kg'],3);
						$d_kandang[$_kb][$_kd]['stok_akhir_sak'] = formatAngka(($perpakan['stok_pakan_sak'] - $perpakan['pakai_sak']),0);
						$d_kandang[$_kb][$_kd]['stok_akhir_kg'] = formatAngka(($perpakan['stok_pakan_kg'] - $perpakan['pakai_kg']),3);
						$d_kandang[$_kb][$_kd]['umur_pakan_kandang'] = isset($perpakan['umur_pakan_kandang']) ? $perpakan['umur_pakan_kandang'] : 0;

						$result[$_kb]['kandang_awal_sak'] += $perpakan['stok_awal_sak'];
						$result[$_kb]['kandang_terima_sak'] += $perpakan['terima_sak'];
						$result[$_kb]['kandang_pakai_sak'] +=$perpakan['pakai_sak'];
						$result[$_kb]['kandang_akhir_sak'] += ($perpakan['stok_pakan_sak'] - $perpakan['pakai_sak']);

						$result[$_kb]['kandang_awal_kg'] += $perpakan['stok_awal_kg'];
						$result[$_kb]['kandang_terima_kg'] += $perpakan['terima_kg'];
						$result[$_kb]['kandang_pakai_kg'] +=$perpakan['pakai_kg'];
						$result[$_kb]['kandang_akhir_kg'] += ($perpakan['stok_pakan_kg'] - $perpakan['pakai_kg']);
					}

			}
		}

		return array('result' => $result, 'd_kavling' => $d_kavling, 'd_kandang' => $d_kandang);
	}

	private function grouping_stok_kavling($arr){
		$result = array();
		$rowspan = array();
		if(!empty($arr)){
			foreach($arr as $r){
					$kav = $r['no_kavling'];
					$kb = $r['kode_barang'];
					if(!isset($result[$kav])){
						$result[$kav] = array();
						$rowspan[$kav]['rowspan'] = 0;
					}
					if(!isset($result[$kav][$kb])){
						$result[$kav][$kb] = array();
						$result[$kav][$kb]['data'] = array(
								'umur_pakan_gudang' => $r['umur_pakan_gudang'],
								'stok_awal_sak' => $r['stok_awal_sak'],
								'stok_awal_kg' => $r['stok_awal_kg'],
								'nama_barang'=> $r['nama_barang'],
								'gudang_terima_sak' => $r['gudang_terima_sak'],
								'gudang_terima_kg' => $r['gudang_terima_kg'],
								'gudang_keluar_sak' => $r['gudang_keluar_sak'],
								'gudang_keluar_kg' => $r['gudang_keluar_kg'],
								'stok_akhir_sak' => $r['stok_awal_sak'] + $r['gudang_terima_sak'] - $r['gudang_keluar_sak'],
								'stok_akhir_kg' => $r['stok_awal_kg'] + $r['gudang_terima_kg'] - $r['gudang_keluar_kg']
							);
						$result[$kav][$kb]['detail'] = array();
						$rowspan[$kav][$kb]['rowspan'] = 0;
					}
					$tmp = array(
						'kandang' => !empty($r['kode_kandang']) ? $r['kode_kandang'] : '-',
						'kandang_terima_sak' => !empty($r['kandang_terima_sak']) ? $r['kandang_terima_sak'] : '-',
						'kandang_terima_kg' => !empty($r['kandang_terima_kg']) ? $r['kandang_terima_kg'] : '-',
						'hari' => !empty($r['umur']) ? $r['umur'] : '-',
						'minggu' => !empty($r['umur']) ? ceil($r['umur']/7) : '-',
						'mutasi' => isMutasi($r['no_referensi']) ?  1 : 0,
						'no_referensi' => $r['no_referensi']
					);
					array_push($result[$kav][$kb]['detail'],$tmp);
					$rowspan[$kav][$kb]['rowspan']++;
					$rowspan[$kav]['rowspan']++;
			}
		}
		return array('data' => $result, 'rowspan'=> $rowspan);
	}

	private function grouping_stok_kandang($arr){
		$result = array();
		$rowspan = array();
		$retur = array();
		if(!empty($arr)){
			foreach($arr as $r){
					$kav = $r['kode_kandang'];
					$kb = $r['kode_barang'];
					if(!isset($result[$kav])){
						$result[$kav] = array();
						$rowspan[$kav]['rowspan'] = 0;
						$result[$kav]['data'] = array(
									'minggu' => !empty($r['umur']) && $r['umur'] > 0 ? ceil($r['umur']/7) : '-',
									'hari' => !empty($r['umur']) && $r['umur'] > 0 ? $r['umur'] : '-',
							);
							$retur[$kav] = array(
								'retur_sak' => $r['retur_sak'],
								'hutang_sak' => $r['hutang_sak'],
								'pelunasan_retur' => $r['pelunasan_retur'],
								'noreg' => $r['no_reg']
							);
						$result[$kav]['detail'] = array();
					}
					if(!isset($result[$kav]['detail'][$kb])){
						$result[$kav]['detail'][$kb] = array();
						$rowspan[$kav]['detail'][$kb]['rowspan'] = 0;
						$result[$kav]['detail'][$kb]['nama'] = $r['nama_barang'];
						$result[$kav]['detail'][$kb]['stok_awal_sak'] = $r['stok_awal_sak'];
						$result[$kav]['detail'][$kb]['stok_awal_kg'] = $r['stok_awal_kg'];
						$result[$kav]['detail'][$kb]['stok_pakan_sak'] = $r['stok_awal_sak'];
						$result[$kav]['detail'][$kb]['stok_pakan_kg'] = $r['stok_awal_kg'];
						$result[$kav]['detail'][$kb]['umur_pakan_kandang'] = $r['umur_pakan_kandang'];
						$result[$kav]['detail'][$kb]['pakai_sak'] = $r['pakai_sak'];
						$result[$kav]['detail'][$kb]['pakai_kg'] = $r['pakai_kg'];
						$result[$kav]['detail'][$kb]['terima_sak'] = 0;
						$result[$kav]['detail'][$kb]['terima_kg'] = 0;
						$result[$kav]['detail'][$kb]['detail'] = array();
					}
					$result[$kav]['detail'][$kb]['stok_pakan_kg'] += $r['terima_kg'];
					$result[$kav]['detail'][$kb]['stok_pakan_sak'] += $r['terima_sak'];

					$tmp = array(
						'no_kavling' => !empty($r['no_kavling']) ? $r['no_kavling'] : '-',
						'terima_sak' => $r['terima_sak'],
						'terima_kg' => $r['terima_kg'],
						'mutasi' => isMutasi($r['no_referensi']) ?  1 : 0,
						'no_referensi' => $r['no_referensi'],
						'nama_barang'=> $r['nama_barang'],
						'kode_barang' => $r['kode_barang'],
						'stok_akhir_sak' => $r['stok_awal_sak'] + $r['terima_sak'] - $r['pakai_sak'],
						'stok_akhir_kg' => $r['stok_awal_kg'] + $r['terima_kg'] - $r['pakai_kg'],
					);
					array_push($result[$kav]['detail'][$kb]['detail'],$tmp);
					$rowspan[$kav]['detail'][$kb]['rowspan']++;
					$rowspan[$kav]['rowspan']++;
					$result[$kav]['detail'][$kb]['terima_sak'] += $r['terima_sak'];
					$result[$kav]['detail'][$kb]['terima_kg'] += $r['terima_kg'];
			}
		}
		return array('data' => $result, 'rowspan'=> $rowspan, 'retur' => $retur);
	}

	private function data_stok_pakan_brd($list_stok){
		/* grouping berdasarkan kavling, kode_barang, jenis_kelamin */
		$r = array();

		if(!empty($list_stok)){
			foreach($list_stok as $s){
				$k_kandang = $s['KODE_KANDANG'];
				$kavling = $s['NO_KAVLING'];
				if(!isset($r[$kavling])){
					$r[$kavling]['data'] = array(
							'depan' => array(
									'kavling' => $kavling,
									'kandang' => $k_kandang,
									'minggu' => $s['UMUR'] >= 0 ? (int) ($s['UMUR'] / 7) : '-',
									'hari' => $s['UMUR'] >= 0 ? $s['UMUR'] % 7 : '-',
							),
							'belakang' => array(
									'noreg' => $s['NO_REG'],
									'retur' => $s['RETUR'],
									'hutang_retur' => $s['HUTANG_RETUR'],
									'sisa_retur' => $s['SISA_HUTANG'] < 0 ? abs($s['SISA_HUTANG']) : 0
							)
					);
					$r[$kavling]['detail'] = array();
				}
				$kodepj = $s['KODE_BARANG'];
				if(!isset($r[$kavling]['detail'][$kodepj])){
					$r[$kavling]['detail'][$kodepj]['sum'] = array(
							'nama_pakan' => $s['NAMA_PAKAN'],
							'kode_barang' => $s['KODE_BARANG'],
							'no_kavling' => $s['NO_KAVLING'],
							'stok_awal' => 0,
							'terima_gudang' => 0,
							'keluar_gudang' => 0,
							'stok_akhir' => 0,
							'umur_pakan' => 0,
							'stok_awal_kandang' => 0,
							'terima_kandang' => 0,
							'pakai_kandang' => 0,
							'stok_akhir_kandang' => 0,
							'umur_pakan_kandang' => 0
					);
					$r[$kavling]['detail'][$kodepj]['detail'] = array();

				}
				$r[$kavling]['detail'][$kodepj]['detail'][] = array(
						'stok_awal' => $s['STOK_AWAL_GUDANG'],
						'terima_gudang' => !empty($s['TERIMA']) ? $s['TERIMA'] : 0 ,
						'keluar_gudang' => !empty($s['KELUAR']) ? $s['KELUAR'] : 0,
						'stok_akhir' => $s['STOK_AKHIR'],
						'umur_pakan' => $s['UMUR_PAKAN'],
						'stok_awal_kandang' => $s['STOK_AWAL_KANDANG'],
						'terima_kandang' => !empty($s['TERIMA_KANDANG']) ? $s['TERIMA_KANDANG'] : 0,
						'pakai_kandang' => !empty($s['PAKAI']) ? $s['PAKAI'] : 0,
						'stok_akhir_kandang' => $s['STOK_AKHIR_KANDANG'],
						'jenis_kelamin' => $s['JENIS_KELAMIN'],
						'umur_pakan_kandang' => $s['UMUR_PAKAN_KANDANG']
				);

				//		array_push($r[$kavling]['detail'][$kodepj]['detail'],$s);
				$r[$kavling]['detail'][$kodepj]['sum']['stok_awal'] += $s['STOK_AWAL_GUDANG'];
				$r[$kavling]['detail'][$kodepj]['sum']['terima_gudang'] += $s['TERIMA'];
				$r[$kavling]['detail'][$kodepj]['sum']['keluar_gudang'] += $s['KELUAR'];
				$r[$kavling]['detail'][$kodepj]['sum']['stok_akhir'] += $s['STOK_AKHIR'];
				$r[$kavling]['detail'][$kodepj]['sum']['umur_pakan'] = $r[$kavling]['detail'][$kodepj]['sum']['umur_pakan'] < $s['UMUR_PAKAN'] ? $s['UMUR_PAKAN'] : $r[$kavling]['detail'][$kodepj]['sum']['umur_pakan'];

				$r[$kavling]['detail'][$kodepj]['sum']['stok_awal_kandang'] += $s['STOK_AWAL_KANDANG'];
				$r[$kavling]['detail'][$kodepj]['sum']['terima_kandang'] += $s['TERIMA_KANDANG'];
				$r[$kavling]['detail'][$kodepj]['sum']['pakai_kandang'] += $s['PAKAI'];
				$r[$kavling]['detail'][$kodepj]['sum']['stok_akhir_kandang'] += $s['STOK_AKHIR_KANDANG'];
				$r[$kavling]['detail'][$kodepj]['sum']['umur_pakan_kandang'] = $r[$kavling]['detail'][$kodepj]['sum']['umur_pakan_kandang'] < $s['UMUR_PAKAN_KANDANG'] ? $s['UMUR_PAKAN_KANDANG'] : $r[$kavling]['detail'][$kodepj]['sum']['umur_pakan_kandang'];


			}

		}
		return $r;
	}
	private function data_stok_pakan_bdy($list_stok){
		/* grouping berdasarkan kavling, kode_barang, jenis_kelamin */
		$r = array();

		if(!empty($list_stok)){
			foreach($list_stok as $s){
				$k_kandang = $s['KODE_KANDANG'];
				$kodepj = $s['KODE_BARANG'];
				$kavling = $s['NO_KAVLING'];
				if(!isset($r[$kavling])){
					$r[$kavling] = array('rowspan' => 0);
				}
				$r[$kavling]['rowspan']++;
				if(!isset($r[$kavling]['data'][$kodepj])){
					$r[$kavling]['data'][$kodepj]['data'] = array(
						'kodepj' => $kodepj,
						'nama_pakan' => $s['NAMA_PAKAN'],
						'stok_awal' => 0,
						'terima_gudang' => 0,
						'keluar_gudang' => 0,
						'stok_akhir' => 0,
						'umur_pakan' => $s['UMUR_PAKAN'],
					);
				}
				$r[$kavling]['data'][$kodepj]['data']['stok_awal'] += $s['STOK_AWAL_GUDANG'];
				$r[$kavling]['data'][$kodepj]['data']['terima_gudang'] += !empty($s['TERIMA']) ? $s['TERIMA'] : 0 ;
				$r[$kavling]['data'][$kodepj]['data']['keluar_gudang'] += !empty($s['KELUAR']) ? $s['KELUAR'] : 0;
				$r[$kavling]['data'][$kodepj]['data']['stok_akhir'] += $s['STOK_AKHIR'];
				$r[$kavling]['data'][$kodepj]['data']['umur_pakan'] = $s['UMUR_PAKAN'] > $r[$kavling]['data'][$kodepj]['data']['umur_pakan'] ?$s['UMUR_PAKAN'] : $r[$kavling]['data'][$kodepj]['data']['umur_pakan'];

				$r[$kavling]['data'][$kodepj]['detail'][] = array(
						'noreg' => $s['NO_REG'],
						'kandang' => $k_kandang,
						'minggu' => $s['UMUR'] >= 0 ? (int) ($s['UMUR'] / 7) : '-',
						'hari' => $s['UMUR'] >= 0 ? $s['UMUR'] % 7 : '-',
						'stok_awal_kandang' => $s['STOK_AWAL_KANDANG'],
						'terima_kandang' => !empty($s['TERIMA_KANDANG']) ? $s['TERIMA_KANDANG'] : 0,
						'pakai_kandang' => !empty($s['PAKAI']) ? $s['PAKAI'] : 0,
						'stok_akhir_kandang' => $s['STOK_AKHIR_KANDANG'],
						'jenis_kelamin' => $s['JENIS_KELAMIN'],
						'umur_pakan_kandang' => $s['UMUR_PAKAN_KANDANG'],
						'retur' => $s['RETUR'],
						'hutang_retur' => $s['HUTANG_RETUR'],
						'sisa_retur' => $s['SISA_HUTANG'] < 0 ? abs($s['SISA_HUTANG']) : 0,
						'noreg' => $s['NO_REG']
				);



			}

		}
		return $r;
	}

	public function multi_stok_pakan(){
		$kode_farm = $this->input->post('kode_farm');
		$nama_farm = $this->input->post('nama_farm');
		$tgl_akses = $this->input->post('tgl_akses');
		$tgl_transaksi = $this->input->post('tgl_transaksi');
		$t = array();
		foreach($kode_farm as $i => $kf){
			array_push($t,$this->detail_stok_pakan($kf,$tgl_transaksi,$tgl_akses,$nama_farm[$i]));
		}
		$data['list_stok'] = $t;
		$this->load->view('report/multi_stok_pakan',$data);
	}

	function detail_terima(){
		$kode_barang = $this->input->post('kode_barang');
		$tgl_terima = $this->input->post('tgl_terima');
		$no_kavling = $this->input->post('no_kavling');
		$kode_farm = $this->input->post('kode_farm');
		$noreg = $this->input->post('noreg');

		$detail_terima = $this->report->detail_terima($kode_barang,$tgl_terima,$no_kavling,$noreg, $kode_farm)->result_array();
		if(!empty($detail_terima)){
			$this->result['content'] = $this->load->view('report/detail_terima',array('list' =>$detail_terima),true);
			$this->result['status'] = 1;
		}
		else{
			$this->result['message'] = 'Data tidak ditemukan';
		}

		echo json_encode($this->result);
	}

	public function detail_retur_sak(){
		$noreg = $this->input->post('noreg');
		$kandang = $this->input->post('kandang');
		$tgl_akses = $this->input->post('tgl_akses');
		$tgl_transaksi = $this->input->post('tgl_transaksi');
	/*	$tgl_akses = '2015-11-10';
		$tgl_transaksi = '2015-11-07';
	*/	$list_retur = $this->report->detail_retur_sak($noreg,$tgl_transaksi);

		$header_pakan = array();
		$hutang_awal = array();
		$retur_sak = array();
		$hutang_retur = array();
		$kode_pakan_arr = array();
		$pelunasan_hutang = array();
		$sisa_hutang = array();
		$ada_retur = 0;
		$ada_pelunasan = 0;
		foreach($list_retur as $l){
			$h_retur = $l['hutang_awal'] - $l['retur_hari_ini'];
			$s_hutang = $l['retur_pelunasan'];
			$p_hutang = $l['retur_pelunasan'];
			array_push($header_pakan,$l['nama_barang']);
			array_push($kode_pakan_arr,$l['kode_barang']);
			array_push($hutang_awal,$l['hutang_awal']);
			array_push($retur_sak,$l['retur_hari_ini']);
			array_push($hutang_retur,$h_retur);
			array_push($pelunasan_hutang,$p_hutang);
			array_push($sisa_hutang, ($h_retur - $p_hutang));

			if( $l['retur_hari_ini'] > 0 ){
				$ada_retur++;
			}

			if( $p_hutang > 0 ){
				$ada_pelunasan++;
			}

		}
		$data['header_pakan'] = $header_pakan;
		$data['kode_pakan_arr'] = $kode_pakan_arr;
		$data['hutang_awal'] = $hutang_awal;
		$data['retur_sak'] = $retur_sak;
		$data['hutang_retur'] = $hutang_retur;
		$data['pelunasan_hutang'] = $pelunasan_hutang;
		$data['sisa_hutang'] = $sisa_hutang;
		$data['ada_retur'] = $ada_retur;
		$data['ada_pelunasan'] = $ada_pelunasan;

		$data['kandang'] = $kandang;
		$data['tgl_transaksi'] = $tgl_transaksi;
		$data['tgl_akses'] = $tgl_akses;
		$data['noreg'] = $noreg;
		$this->load->view('report/detail_retur',$data);

	}

	public function rinci_retur_sak(){
		$noreg = $this->input->post('noreg');
		$tgl_transaksi = $this->input->post('tgl_transaksi');
		$data_tambahan = $this->input->post('data_tambahan');
		$rinci = $this->report->rinci_retur_sak($noreg,$tgl_transaksi)->result_array();
		if(!empty($rinci)){
			/* grouping berdasarkan kodepj dan jam pengembalian */
			$t = array();
			foreach($rinci as $r){
				if(!isset($t[$r['no_retur']])){
					$t[$r['no_retur']] = array();
					$t[$r['no_retur']]['jam'] = $r['tgl_buat'];
				}
				if(!isset($t[$r['no_retur']]['detail'][$r['kode_barang']])){
					$t[$r['no_retur']]['detail'][$r['kode_barang']] = array();
					$t[$r['no_retur']]['detail'][$r['kode_barang']]['total'] = 0;
				}
				$t[$r['no_retur']]['detail'][$r['kode_barang']]['total'] += $r['retur'];
			}

			$this->result['status'] = 1;
			$this->result['content'] = $t;
		}


		echo json_encode($this->result);
	}

	public function pelunasan_retur_sak(){
		$noreg = $this->input->post('noreg');
		$tgl_transaksi = $this->input->post('tgl_transaksi');
		$tgl_akses = $this->input->post('tgl_akses');
	//	$tgl_akses = '2015-11-11';
		$data_tambahan = $this->input->post('data_tambahan');
		$rinci = $this->report->pelunasan_retur_sak($noreg,$tgl_akses,$tgl_transaksi,$data_tambahan);

		if(!empty($rinci)){
			/* grouping berdasarkan kodepj dan jam pengembalian */
			$t = array();
			foreach($rinci as $r){
				if(!isset($t[$r['no_retur']])){
					$t[$r['no_retur']] = array();
					$t[$r['no_retur']]['jam'] = convertElemenTglWaktuIndonesia($r['tgl_retur']);
				}
				if(!isset($t[$r['no_retur']]['detail'][$r['kode_barang']])){
					$t[$r['no_retur']]['detail'][$r['kode_barang']] = array();
					$t[$r['no_retur']]['detail'][$r['kode_barang']]['total'] = 0;
				}
				$t[$r['no_retur']]['detail'][$r['kode_barang']]['total'] += $r['jml_retur'];
			}

			$this->result['status'] = 1;
			$this->result['content'] = $t;
		}
		echo json_encode($this->result);
	}

	/* untuk report rhk */
	public function rhk(){
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$user_level = $this->session->userdata('level_user');
		$data['level_user'] = $user_level;
		$this->load->view('report/'.$this->grup_farm.'/rhk',$data);
	}

	public function detail_kandang(){
		$kode_kandang = $this->input->post('kode_kandang');
		$kode_farm = $this->input->post('kode_farm');
		switch($this->grup_farm){
			case 'bdy':
				$data = $this->report->detail_kandang_bdy($kode_kandang,$kode_farm)->result();
				break;
			case 'brd':
				$data = $this->report->detail_kandang($kode_kandang,$kode_farm)->result();
				break;
		}

		if(!empty($data)){
			$this->result['status'] = 1;
			$this->result['content'] = $data;
		}
		echo json_encode($this->result);
	}

	public function detail_rhk(){
		$tgl_docin = $this->input->post('tgl_docin');
		$noreg = $this->input->post('noreg');
		$jml_jantan = $this->input->post('jml_jantan');
		$jml_betina = $this->input->post('jml_betina');
		$tanggal = $this->input->post('tanggal');

		$tgl_timbang_sebelumnya = isset($tanggal['startDate']) ? tglSebelum($tanggal['startDate'],14) : NULL;
		$tgl_param = '';
		if(!empty($tanggal['operand'])){
			switch($tanggal['operand']){
				case 'between':
					$custom_param = ' cast(r.tgl_transaksi as date) '.$tanggal['operand'].' \''.$tanggal['startDate'].'\' and \''.$tanggal['endDate'].'\'';
					$bb_param = ' cast(r.tgl_transaksi as date) '.$tanggal['operand'].' \''.$tgl_timbang_sebelumnya.'\' and \''.$tanggal['endDate'].'\'';
					$tgl_param = $tanggal['operand'].' \''.$tanggal['startDate'].'\' and \''.$tanggal['endDate'].'\'';
					break;
				case '<=':
					$custom_param= ' cast(r.tgl_transaksi as date) '.$tanggal['operand'].' \''.$tanggal['endDate'].'\'';
					$bb_param= ' cast(r.tgl_transaksi as date) '.$tanggal['operand'].' \''.$tanggal['endDate'].'\'';
					$tgl_param = $tanggal['operand'].' \''.$tanggal['endDate'].'\'';
					break;
				case '>=':
					$custom_param = ' cast(r.tgl_transaksi as date) '.$tanggal['operand'].' \''.$tanggal['startDate'].'\'';
					$bb_param = ' cast(r.tgl_transaksi as date) '.$tanggal['operand'].' \''.$tgl_timbang_sebelumnya.'\'';
					$tgl_param = $tanggal['operand'].' \''.$tanggal['startDate'].'\'';
					break;
			}
		}
		$rhk = $this->report->report_rhk($tgl_docin,$noreg,$custom_param)->result_array();
		$bb_sebelumnya = $this->report->bb_sebelumnya($tgl_docin,$noreg,$bb_param)->result_array();
		$pakai_pakan = $this->report->get_pemakaian_pakan($noreg,$custom_param)->result_array();
		$p_pakan = $this->grouping_pakan($pakai_pakan);
		$retur_pakan = $this->report->get_penggantian_pakan($noreg,$tgl_param)->result_array();
		$retur_sak_terakhir = $this->report->get_retur_sak_terakhir($noreg)->result_array();
		$retur_sak = $this->report->get_retur_sak($noreg,$tgl_param)->result_array();

		$r_sakterakhir = $this->grouping_retursakterakhir($retur_sak_terakhir);
		$r_pakan = $this->grouping_returpakan($retur_pakan);
		$r_sak = $this->grouping_retursak($retur_sak,$r_sakterakhir);
		$data_bb_sebelumnya = $this->gabung_bb_sebelumnya($bb_sebelumnya);
		$this->gabung_retur_pakan($p_pakan,$r_pakan,$r_sak);
		/* grouping berdasarkan tgl_transaksi*/

		$data['rhk'] = $this->grouping_pertgltransaksi($rhk,$data_bb_sebelumnya);
		$data['j_jumlah'] = $jml_jantan;
		$data['b_jumlah'] = $jml_betina;
		$data['p_pakan'] = $p_pakan;
		$data['r_pakan'] = $r_pakan;
		$data['noreg'] = $noreg;
		$data['r_sakterakhir'] = $r_sakterakhir;

		$this->load->view('report/detail_rhk',$data);
	}

	public function detail_rhk_bdy(){

		$req = $this->input->post();
		$tgl_docin = $req['tgl_docin'];
		$tipe = $req['tipe'];
		//if(!isset($req['farm'])){
		if($tipe == 'lsam'){
			$noreg = $req['noreg'];
			$result = $this->report->lsamKandang($noreg);
			$data['rhk'] = $result;

		}elseif($tipe == 'lsam_flock' || $tipe == 'lsam_farm'){
			$flock = isset($req['flock']) ? $req['flock'] : '';
			$periode = $req['periode'];
			$farm = $req['farm'];
			//print_r($req);
			if($flock != ''){
				$result = $this->report->lsamFlock($farm, $flock, $periode);
			}else{
				$result = $this->report->lsamFarm($farm, $periode);
			}

			$data['rhk'] = $result;

		}else{
			$noreg = $req['noreg'];
			$stok_kandang = $this->report->stok_awal_kandang($tgl_docin,$noreg)->row_array();
			$rhk = $this->report->report_rhk_bdy($tgl_docin,$noreg)->result_array();
			$bb_sebelumnya = $this->report->bb_sebelumnya_bdy($tgl_docin,$noreg)->result_array();
			$pakai_pakan = $this->report->get_pemakaian_pakan($noreg)->result_array();
			$p_pakan = $this->grouping_pakan($pakai_pakan);
			$retur_pakan = $this->report->get_penggantian_pakan($noreg)->result_array();
			$retur_sak_terakhir = $this->report->get_retur_sak_terakhir($noreg)->result_array();
			$retur_sak = $this->report->get_retur_sak_bdy($noreg)->result_array();

			$r_sakterakhir = $this->grouping_retursakterakhir($retur_sak_terakhir);
			$r_pakan = $this->grouping_returpakan($retur_pakan);
			$r_sak = $this->grouping_retursak($retur_sak,$r_sakterakhir);
			$data_bb_sebelumnya = $this->gabung_bb_sebelumnya($bb_sebelumnya);
			$jml_panen = $this->report->jumlah_panen($noreg)->result_array();
			$this->gabung_retur_pakan($p_pakan,$r_pakan,$r_sak);

			$data['rhk'] = $this->grouping_pertgltransaksi_bdy($rhk,$data_bb_sebelumnya,$stok_kandang);
			$data['jml_panen'] = $this->groupingjmlpanen($jml_panen);
			//$data['c_jumlah'] = $populasi;
			$data['p_pakan'] = $p_pakan;
			$data['r_pakan'] = $r_pakan;
			$data['r_sakterakhir'] = $r_sakterakhir;
			$data['noreg'] = $noreg;
			$data['tgl_docin'] = tglIndonesia($tgl_docin,'-',' ');
		}
		//print_r($data);

		switch($tipe){
			case 'lsam':
				//$data['terimadocin'] = $this->report->populasiAwal($noreg)->row_array();
				$dview = 'report/bdy/detail_lsam_flock';
				break;
			case 'lsam_flock':
				//$data['terimadocin'] = $this->report->populasiAwal($noreg)->row_array();
				$dview = 'report/bdy/detail_lsam_flock';
				break;
			case 'lsam_farm':
				//$data['terimadocin'] = $this->report->populasiAwal($noreg)->row_array();
				$dview = 'report/bdy/detail_lsam_flock';
				break;
			default:
                $data['populasi_awal'] = $this->report->populasiSetelahUmur7($noreg)->row_array();
			 	$dview = 'report/bdy/detail_rhk';
		}
		//print_r($data);
		$this->load->view($dview,$data);
	}

	public function detaillspm(){
		$noreg = $this->input->post('noreg');
		$populasi = $this->input->post('populasi');
		$tgl_docin = $this->input->post('tgl_docin');
		$tipe = $this->input->post('tipe');
		$range_periode = $this->input->post('range_periode');

		/* kode barang yang digunakan */
		$this->load->model('report/m_lspm','lspm');
		$data['kode_barang'] = $this->lspm->getPakanStandart($noreg)->result_array();

		$_gdterima = $this->lspm->gudangTerima($noreg)->result_array();
		$data['gterima'] = array();
		$data['rtanggal'] = array();
		if(!empty($_gdterima)){
			$gudangTerima = $this->groupingGudangTerima($_gdterima);
			$kandangTerima = $this->groupingKandangTerima($this->lspm->kandangTerima($noreg)->result_array());
			$rangeTanggal = $this->getRangeTanggal($gudangTerima,$kandangTerima);

			$data['gterima'] = $gudangTerima;
			$data['kterima'] = $kandangTerima;
			$data['rtanggal'] = $rangeTanggal;
		}
		switch($range_periode){
			case 'M':
				$vlspm = 'report/bdy/detail_lspm_mingguan';
				break;
			default:
				$vlspm = 'report/bdy/detail_lspm';
		}
		$this->load->view($vlspm,$data);
	}
	/*grouping berdasarkan tanggal dan kode_barang*/
	private function groupingGudangTerima($arr){
		$result = array();
		foreach($arr as $r){
			$kb = $r['kode_barang'];
			$tgl = $r['tgl_terima'];
			if(!isset($result[$tgl])){
				$result[$tgl] = array();
			}
			if(!isset($result[$tgl][$kb])){
				$result[$tgl][$kb] = array();
			}
			array_push($result[$tgl][$kb],$r);
		}
		return $result;
	}

	private function groupingKandangTerima($arr){
		$result = array();
		foreach($arr as $r){
			$kb = $r['kode_barang'];
			$tgl = $r['tgl_terima'];
			if(!isset($result[$tgl])){
				$result[$tgl] = array();
			}
			if(!isset($result[$tgl][$kb])){
				$result[$tgl][$kb] = array();
			}
			array_push($result[$tgl][$kb],$r);
		}
		return $result;
	}


	private function getRangeTanggal($gterima,$kterima){
		$g = array_keys($gterima);
		$k = array_keys($kterima);
		$gmin = min($g);
		$gmax = max($g);
		$kmin = min($k);
		$kmax = max($k);
		$min = $gmin;
		if(!empty($kmin)){
			$min = min($gmin,$kmin);
		}
		$max = $gmax;
		if(!empty($kmax)){
			$max = max($gmax,$kmax);
		}
		return array('min' => $min, 'max'=>$max);
	}

	/* abaikan element pertama
	 * element curent index memiliki data timbang sebelumnya berasal dari index sebelumnya
	 * */
	private function gabung_bb_sebelumnya($arr){
		$tmp = array();
		$i = 1;
		$sebelumnya = array();
		foreach($arr as $r){
			$hari = $r['hari'];
			if($i > 1){
				$tmp[$hari] = $r;
				$tmp[$hari]['timbang_sebelumnya'] = $sebelumnya;
			}
			$sebelumnya = $r;
			$i++;
		}

		return $tmp;
	}

	/* gabung antara retur pakan dan kontrol pakan */
	private function gabung_retur_pakan(&$pakan,$returpakan,$retursak){
		foreach($returpakan as $tgl => $tanggal){
			foreach($tanggal as $jk => $jenis){
				foreach($jenis as $kb => $x){

					if(isset($pakan[$tgl][$jk][$kb])){
						$pakan[$tgl][$jk][$kb]['pakan_diganti'] = isset($x['diganti']) ? $x['diganti'] : '-';
						$pakan[$tgl][$jk][$kb]['pakan_retur'] = isset($x['retur']) ? $x['retur'] : '-';
					}
					else{
						if(!isset($pakan[$tgl])) $pakan[$tgl] = array();
						if(!isset($pakan[$tgl][$jk])) $pakan[$tgl][$jk] = array();
						if(!isset($pakan[$tgl][$jk][$kb])) $pakan[$tgl][$jk][$kb] = array('tgl_transaksi' =>$tgl,'nama_pakan' => $x['nama_pakan'],'kode_barang' => $kb,'jenis_kelamin'=>$jk,'jml_terima'=>0,'brt_terima'=>0,'jml_pakai' => 0,'brt_pakai'=>0,'jml_akhir'=>0,'brt_akhir'=>0,'sak_hutang' => '-','sak_retur'=>'-','sisa_hutang'=>'-','komposisi_pakan'=>0);
						$pakan[$tgl][$jk][$kb]['pakan_diganti'] = isset($x['diganti']) ? $x['diganti'] : '-';
						$pakan[$tgl][$jk][$kb]['pakan_retur'] = isset($x['retur']) ? $x['retur'] : '-';
					}
				}
			}
		}

		foreach($retursak as $tgl => $tanggal){
			foreach($tanggal as $jk => $jenis){
				foreach($jenis as $kb => $x){

					if(isset($pakan[$tgl][$jk][$kb])){
						$pakan[$tgl][$jk][$kb]['sak_hutang'] = $x['hutang'];
						$pakan[$tgl][$jk][$kb]['sak_retur'] = $x['jml_retur'];
						$pakan[$tgl][$jk][$kb]['sisa_hutang'] = $x['sisa_hutang'];
					}
					else{
						if(!isset($pakan[$tgl])) $pakan[$tgl] = array();
						if(!isset($pakan[$tgl][$jk])) $pakan[$tgl][$jk] = array();
						if(!isset($pakan[$tgl][$jk][$kb])) $pakan[$tgl][$jk][$kb] = array('tgl_transaksi' =>$tgl,'nama_pakan' => $x['nama_pakan'],'kode_barang' => $kb,'jenis_kelamin'=>$jk,'jml_terima'=>0,'brt_terima'=>0,'jml_pakai' => 0,'brt_pakai'=>0,'jml_akhir'=>0,'brt_akhir'=>0,'sisa_hutang'=>'-','komposisi_pakan'=>0);
						$pakan[$tgl][$jk][$kb]['sak_hutang'] = $x['hutang'];
						$pakan[$tgl][$jk][$kb]['sak_retur'] = $x['jml_retur'];
						$pakan[$tgl][$jk][$kb]['sisa_hutang'] = $x['sisa_hutang'];
					}
				}
			}
		}
	}

	private function grouping_retursakterakhir($arr){
		$t = array();
		foreach($arr as $s){
			$jk = $s['jenis_kelamin'];
			$kb = $s['kode_barang'];
			$tgl = $s['tgl_transaksi'];

			if(!isset($t[$jk])){
				$t[$jk] = array();
			}
			if(!isset($t[$jk][$kb])){
				$t[$jk][$kb] = array();
			}
			$t[$jk][$kb]['hutang'] = $s['hutang'];
			$t[$jk][$kb]['jml_retur'] = $s['jml_retur'];
			$t[$jk][$kb]['jml_pakai'] = $s['jml_pakai'];
			$t[$jk][$kb]['tgl_transaksi'] = $tgl;
		}
		return $t;
	}

	/*grouping retur sak per tgltransaksi, perjeniskelamin */
	private function grouping_retursak($arr,$retur_sakterakhir){
		$t = array();
		foreach($arr as $s){
			$jk = $s['jenis_kelamin'];
			$kb = $s['kode_barang'];
			$tgl = $s['tgl_transaksi'];

			if(!isset($t[$tgl])){
				$t[$tgl] = array();
			}
			if(!isset($t[$tgl][$jk])){
				$t[$tgl][$jk] = array();
			}
			if(!isset($t[$tgl][$jk][$kb])){
				$t[$tgl][$jk][$kb] = array();
			}
			$t[$tgl][$jk][$kb]['hutang'] = $s['hutang'];
			$t[$tgl][$jk][$kb]['jml_retur'] = $s['jml_retur'];
			$t[$tgl][$jk][$kb]['nama_pakan'] = $s['nama_pakan'];
			$t[$tgl][$jk][$kb]['sisa_hutang'] = $s['sisa_hutang'];
			/*$t[$tgl][$jk][$kb]['sisa_hutang'] = '-';

			if($tgl < $retur_sakterakhir[$jk][$kb]['tgl_transaksi']){
				$t[$tgl][$jk][$kb]['sisa_hutang'] = 0;
			}
			if($tgl >= $retur_sakterakhir[$jk][$kb]['tgl_transaksi']){
				$t[$tgl][$jk][$kb]['sisa_hutang'] = $retur_sakterakhir[$jk][$kb]['hutang'];
			}*/
		}
		//cetak_r($t);
		return $t;
	}
	/*grouping retur pakan per tgltransaksi, perjeniskelamin */
	private function grouping_returpakan($arr){
		$t = array();
		foreach($arr as $s){
			$jk = $s['jenis_kelamin'];
			$kb = $s['kode_barang'];
			$tgl = $s['tgl_transaksi'];

			if(!isset($t[$tgl])){
				$t[$tgl] = array();
			}
			if(!isset($t[$tgl][$jk])){
				$t[$tgl][$jk] = array();
			}
			if(!isset($t[$tgl][$jk][$kb])){
				$t[$tgl][$jk][$kb] = array();
			}
			$t[$tgl][$jk][$kb][$s['keterangan']] = $s['jml'];
			$t[$tgl][$jk][$kb]['nama_pakan'] = $s['nama_pakan'];
		}
		return $t;
	}

	/* grouping per tgltransaksi, perjeniskelamin */
	private function grouping_pakan($arr){
		$t = array();
		foreach($arr as $s){
			$jk = $s['jenis_kelamin'];
			$kb = $s['kode_barang'];
			$tgl = $s['tgl_transaksi'];

			if(!isset($t[$tgl])){
				$t[$tgl] = array();
			}
			if(!isset($t[$tgl][$jk])){
				$t[$tgl][$jk] = array();
			}
			if(!isset($t[$tgl][$jk][$kb])){
				$t[$tgl][$jk][$kb] = array();
			}
			$t[$tgl][$jk][$kb] = $s;
			$t[$tgl][$jk][$kb]['pakan_diganti'] = '-';
			$t[$tgl][$jk][$kb]['pakan_retur'] = '-';
			$t[$tgl][$jk][$kb]['sak_hutang'] = '-';
			$t[$tgl][$jk][$kb]['sak_retur'] = '-';
			$t[$tgl][$jk][$kb]['sisa_hutang'] = '-';
		}
		return $t;
	}

	/* grouping per tgltransaksi, perjeniskelamin */
	private function grouping_approval_pp($arr){
		$t = array();
		if(!empty($arr)){
			foreach($arr as $s){
				$jk = $s['jenis_kelamin'];
				$kb = $s['kode_barang'];
				$tgl = $s['tgl_transaksi'];
	
				if(!isset($t[$tgl])){
					$t[$tgl] = array();
				}
				if(!isset($t[$tgl][$jk])){
					$t[$tgl][$jk] = array();
				}
				if(!isset($t[$tgl][$jk][$kb])){
					$t[$tgl][$jk][$kb] = array();
				}
				$t[$tgl][$jk][$kb] = array('jml_order' => $s['jml_order'], 'class_elm' => $s['kadiv_approve'] ? 'ijo' : '');
			}
		}
		return $t;
	}

	private function grouping_pertgltransaksi_bdy($arr,$data_bb,$stok_kandang){
		$t = array();
		if(empty($arr)){
			return $t;
		}
		$kekum = 0;
		$tmp_stok_sebelum_umur7 = isset($stok_kandang['stok_awal']) ? $stok_kandang['stok_awal'] : $arr[0]['c_awal'];
		$tmp_populasi_awal = $tmp_stok_sebelum_umur7;
		foreach($arr as $s){
			$tgl = $s['tgl_transaksi'];
			if(!isset($t[$tgl])){
				$t[$tgl] = array();
			}
			$jk = strtolower($s['jenis_kelamin']);
			$umur = $s['hari'];
			$bb = $s[$jk.'_berat_badan'];
			
			$ip_std_umur = '';
			if($umur % 7 == 0){
				$ip_std_umur = $this->hitungIP($s[$jk.'_dh_prc'],($s[$jk.'_target_bb']/1000),$s[$jk.'_target_fcr'],$umur);
			}
            $mati = !is_null($s[$jk.'_mati']) ? $s[$jk.'_mati'] : 0;
            $deplesi = !is_null($s[$jk.'_deplesi']) ? $s[$jk.'_deplesi'] : 0;
            if($umur > 7){
            	$tmp_stok_sebelum_umur7 -= $s[$jk.'_afkir'] ;
            }
			$tmp_stok_sebelum_umur7 -= ($s[$jk.'_mati'] + $s['jml_panen']) ;
			
			
			$dh = (($tmp_stok_sebelum_umur7) / $tmp_populasi_awal) * 100;
            
			$adg = isset($data_bb[$s['hari']]['timbang_sebelumnya']) ? $this->hitungADG($data_bb[$s['hari']],$jk) : '-';
			//	$s[$jk.'_berat_badan'] = 90;
			$kekum += $s['brt_pakai'] * 1000 ; //dalam gram
			$fcr = isset($data_bb[$s['hari']]['timbang_sebelumnya']) ? $this->hitungFCR($s[$jk.'_jumlah'], $bb, $kekum) : null;
			$ip = isset($data_bb[$s['hari']]['timbang_sebelumnya']) ? $this->hitungIP($dh,$bb,$fcr,$umur) : null;
			
			$tmp = array(
					'tgl' => $tgl ,
					'hari' => $umur,
					'jk' => $s['jenis_kelamin']
					//,'jml' => $umur < 8 ? $tmp_stok_sebelum_umur7 - $s[$jk.'_mati'] : $s[$jk.'_jumlah']
                                        ,'jml' =>  $tmp_stok_sebelum_umur7
					,'deplesi' => $umur < 8 ? $mati : $deplesi
					,'mati' => $mati
					,'afkir' => !is_null($s[$jk.'_afkir']) ? $s[$jk.'_afkir'] : 0
					,'awal' => $s[$jk.'_awal']
					,'brt_pakai' => $s['brt_pakai'] * 1000 // dalam gram
					,'jml_pakai' => $s['jml_pakai']
					,'berat_badan' => $bb * 1000 // dalam gram
					,'adg' => $adg
					,'fcr'=> !empty($fcr) ? $fcr : '-'
					,'ip' => !empty($ip) ? $ip : '-'
					,'fcr_std' => $s[$jk.'_target_fcr']
					,'ip_std' => $s[$jk.'_target_ip']
					,'ip_std_umur' => $ip_std_umur
					,'dh_std' => $s[$jk.'_dh_prc']
					,'pkn_std' => $s[$jk.'_target_pkn']
					,'pkn_kum_std' => $s[$jk.'_pkn_kum_std']
					,'bb_std' => $s[$jk.'_target_bb']
					,'adg_std' => $s[$jk.'_target_adg']
					,'deplesi_std' => $s[$jk.'_target_deplesi']
					//,'dh' => $umur < 8 ? (($tmp_stok_sebelum_umur7 - $s[$jk.'_mati']) / $tmp_stok_sebelum_umur7) * 100 : $dh
                    ,'dh' => $dh

			);
			$t[$tgl] = $tmp;
		//	$tmp_stok_sebelum_umur7 -= $s[$jk.'_mati'];
		}
		return $t;
	}

	private function grouping_pertgltransaksi($arr,$data_bb){
		$t = array();
		$kekum = 0;
		foreach($arr as $s){
			$tgl = $s['tgl_transaksi'];
			if(!isset($t[$tgl])){
				$t[$tgl] = array();
				$t[$tgl]['header'] = array('tgl' => $tgl ,'hari' => $s['hari'] % 7, 'minggu' => (int) ($s['hari'] / 7));

			}
			if(!isset($t[$tgl]['detail'])){
				$t[$tgl]['detail'] = array();
			}
			$jk = strtolower($s['jenis_kelamin']);
			$bb = $s[$jk.'_berat_badan'] > 0 ? $s[$jk.'_berat_badan'] : null;
			$adg = isset($data_bb[$s['hari']]['timbang_sebelumnya']) ? $this->hitungADG($data_bb[$s['hari']],$jk) : '-';
		//	$s[$jk.'_berat_badan'] = 90;
	  	$kekum += $s['brt_pakai'] * 1000 ; //dalam gram
		  $fcr = $this->hitungFCR($s[$jk.'_jumlah'], $s[$jk.'_berat_badan'], $kekum);
	//		$fcr = $this->hitungFCR($s[$jk.'_jumlah'], $s[$jk.'_berat_badan'], $s['brt_pakai']);
			$fcr = !empty($fcr) ? formatAngka($fcr,3) : null;
			$produksi = $jk == 'b' ? $s['produksi'] : null;
			$tmp = array(
					'jk' => $s['jenis_kelamin']
					,'jml' => $s[$jk.'_jumlah']
					,'mati' => $s[$jk.'_mati']
					,'brt_pakai' => $s['brt_pakai']
					,'berat_badan' => $bb
					,'produksi' => $produksi
					,'rasio' => $s['rasio']
					,'fcr'=> !empty($fcr) ? $fcr : '-'
					,'adg' => $adg
					,'dh_std' => $s[$jk.'_dh_prc']
					,'pkn_std' => $s[$jk.'_target_pkn']
					,'bb_std' => $s[$jk.'_target_bb']
					,'deplesi_std' => $s[$jk.'_target_deplesi']
					,'fcr_std' => $s[$jk.'_target_fcr']
					,'dh' => $s[$jk.'_dh']
				);
			array_push($t[$tgl]['detail'],$tmp);
		}
		return $t;
	}

	public function lsam(){
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$this->load->view('report/'.$this->grup_farm.'/lsam',$data);
	}

	public function lsam_flock(){
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$this->load->view('report/'.$this->grup_farm.'/lsam_flock',$data);
	}

	public function lspm(){
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$this->load->view('report/'.$this->grup_farm.'/lspm',$data);
	}

	public function pjsk(){
		$data['farm'] = $this->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->listSiklus($this->input->get('siklus'),$data['farm']);
		$this->load->view('report/'.$this->grup_farm.'/pjsk',$data);
	}

	public function lsgas(){
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$user_level = $this->session->userdata('level_user');
		switch ($user_level) {
			case 'KF':
				$data['button'] = '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>';
				break;
			case 'KD':
				$data['button'] = '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>';
				break;
			case 'KDV':
				$data['button'] = '<button class="btn btn-primary" onclick="permintaanSak.update(this,\'N\')">Rilis</button>';
				break;
			default:
				# code...
				break;
		}
		$this->load->view('report/'.$this->grup_farm.'/lsgas',$data);
	}

	private function hitungFCR($jml,$bb_rata,$kg_pakan){
		$keb_per_ekor = $jml > 0 ? round($kg_pakan/$jml) : 0;
		$t = $bb_rata > 0 ? ($keb_per_ekor / ($bb_rata * 1000)) : null;
		return $t;
	}

	private function hitungADG($data_bb,$jk){
		$bb = $data_bb[$jk.'_berat_badan'];
		$umur = $data_bb['hari'];
		$bbLalu = $data_bb['timbang_sebelumnya'][$jk.'_berat_badan'];
		$umurLalu = $data_bb['timbang_sebelumnya']['hari'];
		return ($bb - $bbLalu) / ($umur - $umurLalu) * 1000;
	}

	private function hitungIP($dh,$bb,$fcr,$umur){
		$result = null;
		if(!empty($bb) && !empty($fcr)){
		//	$result = round((($dh / 100) * $bb * 100) / ($fcr * $umur));
			//$result = round((($dh / 100) * $bb * 10000) / ($fcr * $umur));
			$dh = $dh / 100;
			$bb = $bb * 100;
			$result = hitungIP($dh,$bb,$fcr,$umur);
		}
		
		return $result;
	}
	public function getKandangAsalMutasi(){
		$noref = $this->input->get('noref');
		$asal = $this->report->kandangAsalMutasi($noref)->row();
		echo json_encode(array('kandang' => $asal->kode_kandang));
	}
	private function listFarm($farm){
		if($farm == 'ALL'){
			$r = $this->db->select('mf.kode_farm,mf.nama_farm')
							->join('pegawai_d pd','pd.kode_farm = mf.kode_farm and pd.kode_pegawai = \''.$this->_user.'\'')
							->group_by(array('mf.kode_farm','mf.nama_farm'))
							->get('m_farm mf')
							->result_array();
			$result = array();
			foreach($r as $y){
				array_push($result,$y['kode_farm']);
			}
			$farm = implode('\',\'',$result);
		}
		return $farm;
	}
	private function listSiklus($siklus,$listfarm){
		if(empty($siklus)){
			return null;
		}
		$r = $this->db->select('kode_siklus')
						->where(array('periode_siklus' => $siklus))
						->where('kode_farm in (\''.$listfarm.'\')')
						->get('m_periode')
						->result_array();
	  $result = array();
		foreach($r as $y){
			array_push($result,$y['kode_siklus']);
			}
		return implode('\',\'',$result);
	}

	private function groupingjmlpanen($arr){
		$r = array();
		foreach($arr as $s){
			$r[$s['hari']] = array('total' => $s['total'], 'bb' => $s['bb']);
		}
		return $r;
	}

/* untuk report rhk  versi trs*/
	public function rhk_trs(){
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$user_level = $this->session->userdata('level_user');
		$this->load->view('report/'.$this->grup_farm.'/rhk_trs',$data);
	}

	public function rhk_farm_trs(){
		$farms = $this->input->post('farm');
		$status_siklus = $this->input->post('status_siklus');
		$siklus = $this->input->post('siklus');
		$tipe = $this->input->post('tipe');
		$namafarm = $this->input->post('namafarm');
		$dataRhk = array();
		foreach($farms as $f){
			$dataRhk[$f] = array();
			switch($status_siklus){
				case 'O':
					$kandangs = $this->db->select(array('no_reg','tgl_doc_in','jml_populasi','flok_bdy'))->where(array('kode_farm' => $f, 'status_siklus' => 'O'))->get('kandang_siklus')->result_array();
				 	break;
				default:
					$kandangs = $this->db->select(array('no_reg','tgl_doc_in','jml_populasi','flok_bdy'))->where(array('kode_farm' => $f, 'kode_siklus' => $siklus))->get('kandang_siklus')->result_array();
			}
			foreach($kandangs as $k){
				$noreg = $k['no_reg'];
				$populasi = $k['jml_populasi'];
				$tgl_docin = $k['tgl_doc_in'];
				$_index = 'Kandang '.substr($noreg,-2).' Flock '.$k['flok_bdy'];
				$dataRhk[$f][$_index] = $this->detail_rhk_trs_bdy($noreg,$populasi,$tgl_docin,$tipe);
			}
		}
		switch($tipe){
			case 'lsam_trs':
				$dview = 'report/bdy/detail_lsam_trs';
				break;
			default:
			 	$dview = 'report/bdy/detail_rhk_trs';
		}
		$this->load->view($dview,array('farms' => $dataRhk, 'nama_farm' => $namafarm));
	}
	/* tipe adalah jenis laporan, rhk atau lsam */
	public function detail_rhk_trs_bdy($noreg,$populasi,$tgl_docin,$tipe){
		$stok_kandang = $this->report->stok_awal_kandang($tgl_docin,$noreg)->row_array();
		$rhk = $this->report->report_rhk_bdy($tgl_docin,$noreg)->result_array();
		$bb_sebelumnya = $this->report->bb_sebelumnya_bdy($tgl_docin,$noreg)->result_array();
//print_r($bb_sebelumnya);die();
		$pakai_pakan = $this->report->get_pemakaian_pakan($noreg)->result_array();
		$report_approval_pp = $this->report->reportApprovalPp($noreg)->result_array();
		$p_pakan = $this->grouping_pakan($pakai_pakan);
		$retur_pakan = $this->report->get_penggantian_pakan($noreg)->result_array();
		$retur_sak_terakhir = $this->report->get_retur_sak_terakhir($noreg)->result_array();
		$retur_sak = $this->report->get_retur_sak_bdy($noreg)->result_array();

		$r_sakterakhir = $this->grouping_retursakterakhir($retur_sak_terakhir);
		$r_pakan = $this->grouping_returpakan($retur_pakan);
		$r_sak = $this->grouping_retursak($retur_sak,$r_sakterakhir);
		$data_bb_sebelumnya = $this->gabung_bb_sebelumnya($bb_sebelumnya);
		$jml_panen = $this->report->jumlah_panen($noreg)->result_array();
		$this->gabung_retur_pakan($p_pakan,$r_pakan,$r_sak);
		/* grouping berdasarkan tgl_transaksi*/
		$data['rhk'] = $this->grouping_pertgltransaksi_bdy($rhk,$data_bb_sebelumnya,$stok_kandang);
		$data['jml_panen'] = $this->groupingjmlpanen($jml_panen);
		$data['c_jumlah'] = $populasi;
		$data['p_pakan'] = $p_pakan;
		$data['r_pakan'] = $r_pakan;
		$data['approval_pp'] = $this->grouping_approval_pp($report_approval_pp);
		
		$data['r_sakterakhir'] = $r_sakterakhir;
		$data['tgl_docin'] = tglIndonesia($tgl_docin,'-',' ');
		$data['jml_baris'] = count($pakai_pakan);
		switch($tipe){
			case 'lsam_trs':
				$data['terimadocin'] = $this->report->populasiAwal($noreg)->row_array();
		//		$dview = 'report/bdy/detail_lsam';
				break;
			default:
        		$data['populasi_awal'] = $this->report->populasiSetelahUmur7($noreg)->row_array();
		//	 	$dview = 'report/bdy/detail_rhk';
		}
		return $data;
		// $this->load->view($dview,$data);
	}

	public function lsam_trs(){
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$this->load->view('report/'.$this->grup_farm.'/lsam_trs',$data);
	}

	public function lspm_trs(){
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$this->load->view('report/'.$this->grup_farm.'/lspm_trs',$data);
	}

/*

public function detailspm_trs(){
		$farms = $this->input->post('farm');
		$status_siklus = $this->input->post('status_siklus');
		$siklus = $this->input->post('siklus');
		$tipe = $this->input->post('tipe');
		$range_periode = $this->input->post('range_periode');
		$namafarm = $this->input->post('namafarm');
		$dataLspm = array('kode_barang' => array());
		foreach($farms as $f){
			$dataRhk[$f] = array();
			switch($status_siklus){
				case 'O':
					$kandangs = $this->db->select(array('no_reg','tgl_doc_in','jml_populasi','flok_bdy'))->where(array('kode_farm' => $f, 'status_siklus' => 'O'))->get('kandang_siklus')->result_array();
					break;
				default:
					$kandangs = $this->db->select(array('no_reg','tgl_doc_in','jml_populasi','flok_bdy'))->where(array('kode_farm' => $f, 'kode_siklus' => $siklus))->get('kandang_siklus')->result_array();
			}
			foreach($kandangs as $k){
				$noreg = $k['no_reg'];
				$populasi = $k['jml_populasi'];
				$tgl_docin = $k['tgl_doc_in'];
				$_index = 'Kandang '.substr($noreg,-2).' Flock '.$k['flok_bdy'];
				$kode_barang = array();
				$dataLspm[$f][$_index] = $this->datadetaillspm_trs($noreg,$populasi,$tgl_docin,$tipe);
				if(count($dataLspm[$f][$_index]['kode_barang']) > count($kode_barang))
				{
						$kode_barang = $dataLspm[$f][$_index]['kode_barang'];
				}
			}
		}
		switch($range_periode){
			case 'M':
				$vlspm = 'report/bdy/detail_lspm_mingguan_trs';
				break;
			default:
				$vlspm = 'report/bdy/detail_lspm_trs';
		}
		$this->load->view($vlspm,array('farms' => $dataLspm, 'kode_barang' => $kode_barang));
	}
*/

	public function detailspm_trs(){
		$farms = $this->input->post('farm');
		$status_siklus = $this->input->post('status_siklus');
		$siklus = $this->input->post('siklus');
		$range_periode = $this->input->post('range_periode');
		$tipe = $this->input->post('tipe');
		$namafarm = $this->input->post('namafarm');
		$dataLspm = array();
		$kode_barang = array();
		foreach($farms as $f){
			$dataRhk[$f] = array();
			switch($status_siklus){
				case 'O':
					$kandangs = $this->db->select(array('no_reg','tgl_doc_in','jml_populasi','flok_bdy'))->where(array('kode_farm' => $f, 'status_siklus' => 'O'))->get('kandang_siklus')->result_array();
					break;
				default:
					$kandangs = $this->db->select(array('no_reg','tgl_doc_in','jml_populasi','flok_bdy'))->where(array('kode_farm' => $f, 'kode_siklus' => $siklus))->get('kandang_siklus')->result_array();
			}
			
			//echo json_encode($dataLspm[$f][$_index]).'<br>';
			foreach($kandangs as $k){
				$noreg = $k['no_reg'];
				$populasi = $k['jml_populasi'];
				$tgl_docin = $k['tgl_doc_in'];
				$_index = 'Kandang '.substr($noreg,-2).' Flock '.$k['flok_bdy'];

				$dataLspm[$f][$_index] = $this->datadetaillspm_trs($noreg,$populasi,$tgl_docin,$tipe);
				//echo '<pre>';
				//print_r($dataLspm[$f][$_index]);die();
				//echo $noreg.'<br>'.json_encode($dataLspm[$f][$_index]).'<br>';
				if(count($dataLspm[$f][$_index]['kode_barang']) > count($kode_barang)){
						$kode_barang = $dataLspm[$f][$_index]['kode_barang'];
				}
			}
		}
		switch($range_periode){
			case 'M':
				$vlspm = 'report/bdy/detail_lspm_mingguan_trs';
				break;
			default:
				$vlspm = 'report/bdy/detail_lspm_trs';
		}
		$this->load->view($vlspm,array('farms' => $dataLspm, 'kode_barang' => $kode_barang, 'namafarm' => $namafarm));
	}


	public function datadetaillspm_trs($noreg,$populasi,$tgl_docin,$tipe){
			/* kode barang yang digunakan */
			$this->load->model('report/m_lspm','lspm');
			$data['kode_barang'] = $this->lspm->getPakanStandart($noreg)->result_array();
			$_gdterima = $this->lspm->gudangTerima($noreg)->result_array();
			$data['gterima'] = array();
			$data['rtanggal'] = array();
			if(!empty($_gdterima)){
				$gudangTerima = $this->groupingGudangTerima($_gdterima);
				$kandangTerima = $this->groupingKandangTerima($this->lspm->kandangTerima($noreg)->result_array());
				$rangeTanggal = $this->getRangeTanggal($gudangTerima,$kandangTerima);
				$data['tgl_docin'] = $tgl_docin;
				$data['populasi_awal'] = $populasi;
				$data['gterima'] = $gudangTerima;
				$data['kterima'] = $kandangTerima;
				$data['rtanggal'] = $rangeTanggal;

			}
			return $data;
		}
	public function getSGAS($kode_siklus,$no_urut)
		{
			$query = $this->db->query("SELECT mp.PERIODE_SIKLUS,sgas.*
				FROM STOK_GLANGSING_AKHIR_SIKLUS sgas
				LEFT JOIN M_PERIODE mp ON mp.KODE_SIKLUS = sgas.KODE_SIKLUS
				JOIN (
					SELECT
						lsgas.KODE_SIKLUS kode,
						lsgas.NO_URUT urut,
						max(lsgas.NO_URUT_APPROVE) max_urut
					FROM LOG_STOK_GLANGSING_AKHIR_SIKLUS lsgas
					GROUP BY
					lsgas.KODE_SIKLUS, lsgas.NO_URUT
				) ts ON ts.kode = sgas.KODE_SIKLUS AND ts.urut = sgas.NO_URUT
				JOIN LOG_STOK_GLANGSING_AKHIR_SIKLUS lsgas ON lsgas.KODE_SIKLUS = ts.kode AND lsgas.NO_URUT = ts.urut AND lsgas.NO_URUT_APPROVE = ts.max_urut
				where sgas.KODE_SIKLUS = '$kode_siklus' and sgas.NO_URUT = '$no_urut'
				");

			return $query->row();
		}

	public function getBudgetData($kode_siklus,$kategori)
		{
			$query = $this->db->query("
				SELECT * FROM BUDGET_GLANGSING_D bgd
				JOIN M_BUDGET_PEMAKAIAN_GLANGSING mbpg ON mbpg.KODE_BUDGET = bgd.KODE_BUDGET AND mbpg.KATEGORI_BUDGET = '$kategori'
				AND bgd.KODE_SIKLUS = $kode_siklus
			");

			return $query->result();
		}

	public function getBudgetTotal($kode_siklus,$kategori)
		{
			$query = $this->db->query("
				SELECT sum(JML_ORDER) JML_ORDER FROM BUDGET_GLANGSING_D bgd
				JOIN M_BUDGET_PEMAKAIAN_GLANGSING mbpg ON mbpg.KODE_BUDGET = bgd.KODE_BUDGET AND mbpg.KATEGORI_BUDGET = '$kategori'
				AND bgd.KODE_SIKLUS = $kode_siklus
			");

			return $query->row();
		}
	public function PJSKListBarang()
	{
		echo $this->report->PJSKListBarang();
	}
	public function getPJSK()
	{
		$sql = <<<QUERY

			EXEC dbo.generate_pjsk
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	function getPJSKDetail(){
		$arr = array();

		foreach ($this->getPJSK() as $key => $value) {
			$arr[$value['nama_farm']][$value['tgl']][]=$value;
		}
		$data['result'] = $arr;
		echo $this->load->view('report/'.$this->grup_farm.'/detail_pjsk',$data,TRUE);
	}

	public function showImage(){
        //$this->load->model('penerimaan_pakan/m_verifikasi_do_pakan','mvdo');
		$noreg = $this->input->get('noreg');
		$tgl = $this->input->get('tgl');
		$lampiran = $this->db->where(array('no_reg'=>$noreg,'tgl_transaksi' => $tgl))->get('RHK_LAIN2')->row_array();
		
        $this->output
            ->set_content_type('jpeg')
            ->set_output(file_get_contents($lampiran['ATTACHMENT']));
    }
}
