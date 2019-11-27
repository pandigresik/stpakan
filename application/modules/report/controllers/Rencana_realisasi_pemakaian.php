<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Rencana_realisasi_pemakaian extends MY_Controller {
	protected $grup_farm = 'bdy';	
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_report','report');
		$this->load->model('report/m_report_kontrol_stok','mrks');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		
	}

	public function index() {
		$this->result['status'] = 1;
		$eventKandangKlik = 'KSP.showDetailRencanaRealisasi(this)';
		$header = '<div class="alert alert-warning text-center" style="font-size:120%;color:#000">Rencana dan Realisasi Pemakaian Pakan</div>';
        $this->result['content'] = $header. summaryFarm($this->mrks->list_kandang_all(array('ks.status_siklus' => 'O')),'onclick="KSP.showKandang(this,\''.$eventKandangKlik.'\')"');
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	
	public function detail(){
		$this->load->model('riwayat_harian_kandang/m_ploting_pelaksana','mpp');
		$this->load->model('report/m_kontrol_stok_pakan','mksp');
		$noreg = $this->input->post('noreg');
		$kode_farm = substr($noreg,0,2);
		$dataNoreg = $this->db->where(array('no_reg' => $noreg))->get('kandang_siklus')->row_array();
		$kode_siklus = $dataNoreg['KODE_SIKLUS'];
		$tgl_docin = $this->input->post('tgl_docin');
		$pengawas = $this->mpp->as_array()->get_by(array('no_reg' => $noreg));
		$namaPengawas = $this->db->where(array('kode_pegawai' => $pengawas['PENGAWAS']))->get('m_pegawai')->row_array();
		$data_pp = $this->mksp->data_pp($noreg);
		$data_dropping = $this->mksp->dropping_pakan($noreg);
		$data_rhk = $this->mksp->rhk_pakan($noreg);
		$data_retur_sak = $this->mksp->pengembalian_sak($noreg);
		$data_panen = arr2DToarrKey($this->mksp->realisasi_panen($noreg),'tgl_panen');
		$allPp = $this->groupingPpKodeBarang($data_pp);
		$dropping = $this->groupingTglKodebarang($data_dropping);
		$rhk = $this->groupingTglKodebarang($data_rhk);
		$retur_sak = $this->groupingTglKodebarang($data_retur_sak);
		$stokHarianGudang = $this->stokGudangPerTgl($this->mksp->stokHarianGudang($noreg));
		$stokHarianKandang = $this->stokPerTglLhk($this->mksp->stokHarianKandang($noreg));
		$log_do = simpleGrouping($this->mksp->log_do($noreg),'no_lpb');
		
		$pps = $allPp['perbarang'];
		$pps_tgl = $allPp['pertglKebutuhan'];
		$pp_perpp = $allPp['perpp'];		
		$data_penerimaan_do = $this->mksp->data_penerimaan_do($noreg);
		$do_pps = $this->groupingDataDO($data_penerimaan_do);
		
		$data = array(
			'page1' => $this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan/page1',array('pps' => $pps,'perpp' => $pp_perpp, 'do_pps' => $do_pps['do_perpp'], 'log_do' => $log_do,'pengawas' => $namaPengawas['NAMA_PEGAWAI'],'tgldocin' => $tgl_docin, 'kode_farm' => $kode_farm, 'kode_siklus' => $kode_siklus),TRUE),
			'page2' => $this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan/page2',array('pps' => $pps, 'perpp' => $pp_perpp,'stok_gudang' => $stokHarianGudang, 'do_pps' => $do_pps['do_penerimaan'], 'kode_farm' => $kode_farm, 'kode_siklus' => $kode_siklus),TRUE),
			'page3' => $this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan/page3',array('pps' => $pps_tgl, 'stok_gudang' => $stokHarianGudang, 'dropping' => $dropping, 'retur_sak' => $retur_sak, 'pengawas' => $namaPengawas['NAMA_PEGAWAI'], 'noreg' => $noreg,'tgldocin' => $tgl_docin, 'kode_farm' => $kode_farm, 'kode_siklus' => $kode_siklus),TRUE),
			'page4' => $this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan/page4',array('pps' => $pps_tgl,'pp_lhk' => $allPp['perlhk'], 'stok_kandang' => $stokHarianKandang, 'rhk' => $rhk, 'retur' => $retur_sak, 'pengawas' => $namaPengawas['NAMA_PEGAWAI'], 'noreg' => $noreg, 'data_panen' => $data_panen, 'kode_farm' => $kode_farm, 'kode_siklus' => $kode_siklus),TRUE),
			'tgldocin' => $tgl_docin,
			'maxPage' => 4
		);
		$this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan/detail_pp',$data);
	}

	private function groupingPpKodeBarang($data_pp){
		$result = array();
		$pp_perbarang = array();
		$pp_pertglKebutuhan = array();
		$pp_perpp = array();
		$pp_tgl_lhk = array();
		if(!empty($data_pp)){
			foreach($data_pp as $dp){
				$kb = $dp['kode_barang'];
				$no_pp = $dp['no_lpb'];
				$tgl_lhk = $dp['tgl_lhk'];
				$tgl_kebutuhan = $dp['tgl_kebutuhan'];

				if(!isset($pp_perpp[$no_pp])){
					$pp_perpp[$no_pp] = array('tgl_kebutuhan' => array(),'umur' => array(), 'tgl_kirim' => array());
				}
				$tgl_kirim_forecast = $dp['tgl_kirim_forecast'];
				array_push($pp_perpp[$no_pp]['tgl_kebutuhan'],convertElemenTglWaktuIndonesia($tgl_kebutuhan));
				array_push($pp_perpp[$no_pp]['umur'],$dp['umur']);
				array_push($pp_perpp[$no_pp]['tgl_kirim'],convertElemenTglWaktuIndonesia($tgl_kirim_forecast));

				if(!isset($pp_perbarang[$no_pp])){
					$pp_perbarang[$no_pp] = $dp;
					$pp_pertglKebutuhan[$no_pp] = array('data' => $dp,'total_row' => 0, 'detail' => array());
					$pp_perbarang[$no_pp]['detail_barang'] = array();
				}

				if(!isset($pp_perbarang[$no_pp]['detail_barang'][$kb])){
					$pp_perbarang[$no_pp]['detail_barang'][$kb] = array('nama_barang' => $dp['nama_barang'], 'total' => 0);
				}
				$pp_perbarang[$no_pp]['detail_barang'][$kb]['total'] += $dp['jml_order']; 


				if(!isset($pp_pertglKebutuhan[$no_pp]['detail'][$tgl_kebutuhan])){
					$pp_pertglKebutuhan[$no_pp]['detail'][$tgl_kebutuhan] = array('detail' => array(),'umur' => $dp['umur']);
				}
				$pp_pertglKebutuhan[$no_pp]['total_row'] += 1;
				$pp_pertglKebutuhan[$no_pp]['detail'][$tgl_kebutuhan]['detail'][$kb] = array('nama_barang' => $dp['nama_barang'], 'total' => $dp['jml_order']);


				if(!empty($tgl_lhk)){
					if(!isset($pp_tgl_lhk[$tgl_lhk])){
						$pp_tgl_lhk[$tgl_lhk] = array('no_lpb' => $dp['no_lpb'],'tgl_kebutuhan' => array(),'barang' => array(), 'tgl_kirim' => $dp['tgl_kirim'], 'status' => $dp['status_lpb'], 'tgl_rilis' => $dp['tgl_rilis']);
					}
					if(!isset($pp_tgl_lhk[$tgl_lhk]['barang'][$kb])){
						$pp_tgl_lhk[$tgl_lhk]['barang'][$kb] = array('jml_order' => 0, 'jml_rekomendasi' => 0, 'nama_barang' => $dp['nama_barang']);
					}
					
					array_push($pp_tgl_lhk[$tgl_lhk]['tgl_kebutuhan'],$tgl_kebutuhan);
					$pp_tgl_lhk[$tgl_lhk]['barang'][$kb]['jml_order'] += $dp['jml_order'];
					$pp_tgl_lhk[$tgl_lhk]['barang'][$kb]['jml_rekomendasi'] += $dp['jml_rekomendasi'];
				}
			}
		}
		$result['perbarang'] = $pp_perbarang;
		$result['pertglKebutuhan'] = $pp_pertglKebutuhan;
		$result['perlhk'] = $pp_tgl_lhk;
		$result['perpp'] = $pp_perpp; /** menyimpan data tgl kebutuhan saja */
		return $result;
	}
	/** grouping berdasarkan lpb dan kode_barang */
	private function groupingDataDO($data_do){
		$do_perpp = array();
		$do_penerimaan = array();
		$result = array();
		if(!empty($data_do)){
			foreach($data_do as $dp){
				$kb = $dp['kode_barang'];
				$no_pp = $dp['no_lpb'];
				$no_do = $dp['no_do'];
				if(!isset($do_perpp[$no_pp])){
					$do_perpp[$no_pp] = array('total_row' => 0);
					$do_penerimaan[$no_pp]['total_row_penerimaan'] = 0;
				}
				if(!isset($do_perpp[$no_pp][$kb])){
					$do_perpp[$no_pp][$kb] = array();
					$do_perpp[$no_pp][$kb]['total_row'] = 0;
					$do_perpp[$no_pp][$kb]['detail'] = array();

					$do_penerimaan[$no_pp][$kb]['detail'] = array();
					$do_penerimaan[$no_pp][$kb]['total_row'] = 0;
				}
				if(!isset($do_perpp[$no_pp][$kb]['detail'][$no_do])){
					$do_perpp[$no_pp][$kb]['detail'][$no_do] = $dp; 
					$do_perpp[$no_pp][$kb]['total_row'] += 1;
					$do_perpp[$no_pp]['total_row'] += 1;
					
					$do_penerimaan[$no_pp][$kb]['detail'][$no_do] = array(
						'total_row' => 0,
						'verifikasi' => array('nopol' => $dp['nopol'], 'tgl_verifikasi' => $dp['tgl_verifikasi'],'user_verifikasi' => $dp['user_verifikasi'], 'photo' => $dp['photo']),
						'penerimaan' => array()
					);
				}
				$do_penerimaan[$no_pp]['total_row_penerimaan'] += 1;
				$do_penerimaan[$no_pp][$kb]['detail'][$no_do]['penerimaan'][] = array('tgl_terima' => $dp['tgl_terima'], 'jumlah' => $dp['jumlah'],'berat' => $dp['berat'],'kode_pallet' => $dp['kode_pallet'], 'user_buat' => $dp['user_buat']);
				$do_penerimaan[$no_pp][$kb]['total_row'] += 1;
				$do_penerimaan[$no_pp][$kb]['detail'][$no_do]['total_row'] += 1;
			}
		}
		$result = array('do_perpp' => $do_perpp,'do_penerimaan' => $do_penerimaan);
		return $result;
	}

	public function groupingTglKodebarang($data){
		$result = array();
		if(!empty($data)){
			foreach($data as $d){
				$tgl = $d['tgl_kebutuhan'];
				$kb = $d['kode_barang'];
				if(!isset($result[$tgl])){
					$result[$tgl] = array();
				}
				if(!isset($result[$tgl][$kb])){
					$result[$tgl][$kb] = array();
				}
				$result[$tgl][$kb][] = $d;
			}
		}
		return $result;
	}

	public function stokPerTglLhk($arr){
		$result  = array();
		if(!empty($arr)){
			$stok = array();
			foreach($arr as $r){
				$tgl = $r['tgl_kebutuhan'];
				$kb = $r['kode_barang'];
				if(!isset($stok[$kb])){
					$stok[$kb] = 0;
				}
				if(!isset($result[$tgl])){
					$result[$tgl] = array();
				}
				$stok[$kb] += $r['jml_order'];
				if($r['keterangan1'] == 'LHK'){
					$result[$tgl][$kb] = $stok[$kb];
				}
			}
		}
		return $result;
	}

	private function stokGudangPerTgl($arr){
		$result  = array();
		if(!empty($arr)){
			$stok = array();
			foreach($arr as $r){
				$tgl = $r['tgl_buat'];
				$kb = $r['kode_barang'];
				if(!isset($stok[$kb])){
					$stok[$kb] = 0;
				}
				if(!isset($result[$tgl])){
					$result[$tgl] = array();
				}
				$stok[$kb] += $r['stok'];
				$result[$tgl][$kb] = $stok[$kb];
			}
		}
		
		return $result;
	}

	public function detail_panen(){
		$cari = array(
			'rp.no_reg' => $this->input->post('noreg'),
			'rp.tgl_panen' => $this->input->post('tgl_panen')
		);
		
		$data_panen = $this->db->select('rp.no_surat_jalan,rp.tgl_panen,rp.umur_panen,rp.berat_aktual,rp.jumlah_aktual,rp.berat_badan_rata2,rp.tgl_datang,rp.tgl_mulai,rp.tgl_selesai,rp.tgl_buat,rpd.kode_pelanggan,rpd.no_do,rpd.berat,rpd.jumlah')
								->select('mp.nama_pegawai user_buat')
								->where($cari)
								->join('m_pegawai mp','mp.kode_pegawai = rp.user_buat')
								->join('realisasi_panen_do rpd','rpd.no_do = rp.no_do')
								->get('realisasi_panen rp')
								->result_array();
		
		$this->load->view('report/bdy/kontrol_stok_pakan/detail_realisasi_panen',array('data' => $data_panen));

	}
}    
