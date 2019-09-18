<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Kertas_kerja extends MY_Controller{
	private $grup_farm;
	public function __construct(){
		parent::__construct();
		$this->load->helper('stpakan');
		$this->load->helper('kertaskerja');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->load->model('home/m_kertaskerja','mkk');
	}
	public function index(){
		$data['project_name'] = 'ST Pakan';
		$data['base_url'] = base_url();
		$level_user = $this->session->userdata('level_user');
		$data['nama_user'] = $this->session->userdata('nama_user');
		switch ($level_user){
			case 'KF':
				$kode_farm = $this->session->userdata('kode_farm');
//				$data['list_farm'] = $this->list_farm_kandang($kode_farm);
				$data['list_farm'] = null;
				$data['show_output'] = 0;
				break;
			case 'PD':
			//	$data['list_farm'] = $this->list_farm_kandang();
				$data['list_farm'] = null;
				$data['content'] = '';
				$data['show_output'] = 1;
				break;
			case 'KD':
			//	$data['list_farm'] = $this->list_farm_kandang();
				$data['list_farm'] = null;
				$data['content'] = '';
				$data['show_output'] = 1;
				break;
			case 'KDV':
			//	$data['list_farm'] = $this->list_farm_kandang();
				$data['list_farm'] = null;
				$data['content'] = '';
				$data['show_output'] = 1;
				break;
			default :
				$data['list_farm'] = null;
				$data['content'] = '';
				$data['show_output'] = 0;
		}
		/* tampilkan daftar kandang */

		$this->load->view('home/'.$this->grup_farm.'/kertas_kerja',$data);
	}

	public function list_kandang(){
		$kode_farm = $this->input->post('kodefarm');
		$status_minimum = $this->input->post('status_minimum');
		$status_minimum = empty($status_minimum) ? NULL : $status_minimum;
		$this->load->model('forecast/m_forecast','mf');
		$data['list_kandang'] = $this->mf->list_kandang_open($kode_farm,$status_minimum)->result_array();
		$this->load->view('home/list_kandang',$data);
	}

	public function list_kertas_kerja($header = 1){

		$level_user = $this->session->userdata('level_user');
		switch($level_user){
			case 'PD':
				$view_kertas_kerja = 'home/list_kertas_kerja_presdir';
				break;
			default:
				$view_kertas_kerja = 'home/'.$this->grup_farm.'/list_kertas_kerja';
		}
		$noreg = $this->input->post('noreg');
		$konversi = $this->input->post('konversi');
		if(empty($konversi)){
			$konversi = array(
					'b_kons' => 'sak',
					'b_skp' => 'sak',
					'j_kons' => 'sak',
					'j_skp' => 'sak',
					'c_kons' => 'sak',
					'c_skp' => 'sak',
					'kons' => 'sak',
					'skp' => 'sak',
			);
		}
		$kebutuhan_awal = $this->input->post('kebutuhanawal');
		$kebutuhan_akhir = $this->input->post('kebutuhanakhir');
		$kebutuhan_awal = empty($kebutuhan_awal) ? NULL : $kebutuhan_awal;
		$kebutuhan_akhir = empty($kebutuhan_akhir) ? NULL : $kebutuhan_akhir;
		$this->load->model('permintaan_pakan/m_permintaan_pakan','mpp');

		$list_kertas_kerja = $this->mpp->kertas_kerja($noreg,$kebutuhan_awal,$kebutuhan_akhir);
		$awalKebutuhan = $akhirKebutuhan = array();
		if(!empty($list_kertas_kerja)){
			$awalKebutuhan = $list_kertas_kerja[0];
			$akhirKebutuhan = end($list_kertas_kerja);
		}


		$data['list_kertas_kerja'] = $list_kertas_kerja;
		$data['header'] = $header;
		$data['konversi'] = $konversi;
		/* dapatkan rowspannya berdasarkan no_pp pada tanggal kebutuhan tersebut */
	//	$data['timeline'] = $this->getRowspan($list_kertas_kerja);
		$result['kebutuhan_awal'] = isset($awalKebutuhan['tglkebutuhan']) ? $awalKebutuhan['tglkebutuhan'] : NULL;
		$result['kebutuhan_akhir'] = isset($akhirKebutuhan['tglkebutuhan']) ? $akhirKebutuhan['tglkebutuhan'] : NULL;
		$result['content'] = $this->load->view($view_kertas_kerja,$data,true);
		$result['level'] = $level_user;
		echo json_encode($result);
	}

	public function list_kertas_kerja_bdy(){
		$level_user = $this->session->userdata('level_user');
		switch($level_user){
			case 'PD':
				$view_kertas_kerja = 'home/list_kertas_kerja_presdir';
				break;
			default:
				$view_kertas_kerja = 'home/'.$this->grup_farm.'/list_kertas_kerja';
		}
		$noreg = $this->input->post('noreg');
		$tgldocin = $this->input->post('docin');
		if(empty($tgldocin)){
			$tt = $this->db->select('tgl_doc_in')->where(array('no_reg' => $noreg))->get('kandang_siklus')->row();
			$tgldocin = $tt->tgl_doc_in;
		}

		$s_konversi = $this->input->post('konversi');
		if(empty($s_konversi)){
			$s_konversi = array();
		}
		$d_konversi = array(
			'b_kons' => 'sak',
			'b_skp' => 'sak',
			'j_kons' => 'sak',
			'j_skp' => 'sak',
			'c_kons' => 'sak',
			'c_skp' => 'sak',
			'kons' => 'sak',
			'skp' => 'sak',
		);
		$konversi = array_replace($d_konversi, $s_konversi);

		$this->load->model('permintaan_pakan/m_permintaan_pakan','mpp');

		$list_kertas_kerja = $this->mpp->kertas_kerja_bdy($noreg,$tgldocin);
		$data['noreg'] = $noreg;
		$data['docin'] = $tgldocin;
		$data['list_kertas_kerja'] = $list_kertas_kerja;
		$data['konversi'] = $konversi;
		/* dapatkan rowspannya berdasarkan no_pp pada tanggal kebutuhan tersebut */
		$result['content'] = $this->load->view($view_kertas_kerja,$data,true);
		$result['level'] = $level_user;
		echo json_encode($result);
	}


	private function getRowspan($arr){
		$result = array();
		if(!empty($arr)){
			foreach($arr as $r){
				$no_pp = $r['no_pp_tgl_kebutuhan'];
				if(!empty($no_pp)){
					if(!isset($result[$no_pp])){
						$rencana_kirim = $r['rencana_kirim'];
						if(!empty($rencana_kirim)){
							$tmp = array(
									'rencana_kirim' => $rencana_kirim
									,'approve_pp' => $r['approve_pp']
									,'entry_do' => $r['entry_do']
									,'sisa_do' => $r['sisa_do']
									,'sj_terakhir' => $r['sj_terakhir']
									,'terima_terakhir' => $r['terima_terakhir']
									,'status_penerimaan' => $r['status_penerimaan']
									, 'rowspan' => 1
							);
							$result[$no_pp] = $tmp;
						}

					}else{
						$result[$no_pp]['rowspan']++;
					}
				}
			}
		}
		return $result;
	}
	public function list_kertas_kerja_sebagian(){
		$this->list_kertas_kerja(0);
	}

	public function list_farm_kandang($kode_farm = NULL){
		$this->load->model('forecast/m_kandang_siklus','ks');
		return $this->ks->daftar_farm_kandang($kode_farm)->result_array();
	}

	public function riwayat_pp(){
		$no_pp = $this->input->post('no_pp');
		$no_reg = $this->input->post('no_reg');
		$jk = $this->input->post('jk');
		$this->load->model('permintaan_pakan/m_permintaan_pakan','mpp');
		$this->load->model('permintaan_pakan/m_op','mop');
		$data['header'] = $this->mop->header_op_kk(array(array('no_lpb' => $no_pp)))->row_array();
		$data['detail_pp'] = $this->mpp->riwayat_pp($no_pp,$no_reg,$jk)->result_array();
		$this->load->view('home/riwayat_pp',$data);
	}
	/* daftar kandang tanpa memperhatikan statusnya */
	private function _list_kandang_all($where = array()){
		$this->load->model('forecast/m_kandang_siklus','ks');
		/* cari maximal tgl rhk tiap kandang */
		$this->db->select('max(rhk.tgl_transaksi) tgl_transaksi, ks.no_reg')
			->join('kandang_siklus ks','ks.no_reg = rhk.no_reg and ks.status_siklus <> \'P\'')
		;
		$this->db->from('rhk')->group_by('ks.no_reg');
		if(!empty($where)){
			$this->db->where($where);
		}
		$subquery = $this->db->get_compiled_select();

		$this->db->select('ks.no_reg,ks.kode_kandang,ks.kode_farm,ks.kode_siklus,ks.kode_std_breeding_j,
						ks.kode_std_breeding_b,ks.jml_jantan,ks.jml_betina,ks.tgl_doc_in,ks.tipe_kandang,
						mp.kode_strain,mf.nama_farm,mk.nama_kandang,rhk.tgl_transaksi rhk_terakhir,
						ks.status_siklus,mp.periode_siklus,ks.flok_bdy,ks.kode_farm+\'/\'+mp.periode_siklus periode')
				->from('kandang_siklus ks')
				->join('m_periode mp','ks.kode_siklus = mp.kode_siklus')
				->join('m_farm mf','mf.kode_farm = ks.kode_farm')
				->join('m_kandang mk','mk.kode_kandang = ks.kode_kandang and mk.kode_farm = mf.kode_farm')
				->join('('.$subquery.') as rhk','rhk.no_reg = ks.no_reg','left')
				->where('ks.status_siklus <> \'P\'')
				;

		if(!empty($where)){
			$this->db->where($where);
		}
		
		return $this->db->get();
	}
	/* daftar farm tanpa memperhatikan statusnya */
	public function list_farm_all(){
		$where = $this->input->post('where');
		$data = $this->_list_kandang_all($where)->result_array();
		echo json_encode(array('status'=>1, 'content' => $data));
	}

	public function list_kandang_all(){
		$where = $this->input->post('where');
		$action = $this->input->post('action');
		$tipe = $this->input->post('tipe');
		$data['tipe'] = $tipe;
		$data['action'] = empty($action) ? 'KertasKerja.showKertasKerjaBdy(this)' : $action;
		$level_user = $this->session->userdata('level_user');
		$data['grafik'] = $level_user == 'PD' ? 1 : 0;

		$data['list_kandang'] = $this->_list_kandang_all($where)->result_array();
		$this->load->view('home/'.$this->grup_farm.'/list_kandang',$data);
	}

	public function list_flock_all(){
		$where = $this->input->post('where');
		$action = $this->input->post('action');
		$tipe = $this->input->post('tipe');
		$data['tipe'] = $tipe;
		$data['action'] = empty($action) ? 'KertasKerja.showKertasKerjaBdy(this)' : $action;
		$level_user = $this->session->userdata('level_user');
		$data['grafik'] = $level_user == 'PD' ? 1 : 0;

		$data['list_flock'] = $this->mkk->list_flock_all($where)->result_array();
		$this->load->view('home/'.$this->grup_farm.'/list_flock',$data);
	}

	public function grafik(){
		$hasil = array('data' => null, 'std' => null, 'x' => null, 'status' => 0, 'message' => 'Data tidak ditemukan');
		$umur_awal = $this->input->post('umur_awal');
		$umur_akhir = $this->input->post('umur_akhir');
		$noreg = $this->input->post('noreg');
		$standard_jantan = $this->input->post('standard_jantan');
		$standard_betina = $this->input->post('standard_betina');
		$keb_awal = $this->input->post('keb_awal');
		$keb_akhir = $this->input->post('keb_akhir');
		$jenisChart = $this->input->post('grafik');
		$doc_in = $this->input->post('doc_in');
		$tabel = 'rhk';
		$tabel2 = NULL;
		$std = NULL;
		$label = array('Umur Tanggal');
		$where_data = array();
		switch($jenisChart){
			case 'grafikBB':
				$select_std = array('jenis_kelamin','std_umur','target_bb');
				$std_kolom = 'target_bb';
				$select_data = 'tgl_transaksi,(b_berat_badan * 1000)  b_berat_badan,(j_berat_badan * 1000)  j_berat_badan';
				$where_data = ' b_berat_badan is not null and j_berat_badan is not null';
				$kolom_data = 'berat_badan';
				$tooltip = 'berat badan';
				$title = 'Pencapaian Berat Badan Ayam ';
				$legend_y = 'Berat Badan (gram)';
				break;
			case 'grafikKE':
				$select_std = array('jenis_kelamin','std_umur','target_pkn');
				$std_kolom = 'target_pkn';
				$select_data = array('tgl_transaksi','b_jumlah','j_jumlah');
				$select_data2 = array('tgl_transaksi','jenis_kelamin','abs(brt_pakai) brt_pakai');
				$tabel2 = 'rhk_pakan';
				$kolom_data = 'jumlah';
				$tooltip = 'Jumlah konsumsi';
				$title = 'Konsumsi Pakan per Ekor Ayam ';
				$legend_y = 'Konsumsi (gram)';
				break;
			case 'grafikDH':
				$select_std = array('jenis_kelamin','std_umur','dh_prc');
				$std_kolom = 'dh_prc';
				$select_data = array('tgl_transaksi','b_daya_hidup','j_daya_hidup');
				$kolom_data = 'daya_hidup';
				$tooltip = 'daya hidup';
				$title = 'Daya Hidup Ayam ';
				$legend_y = 'Daya Hidup';
				break;
			case 'grafikPA':
				$select_std = NULL;
				$select_data = array('tgl_transaksi','b_jumlah','j_jumlah');
				$kolom_data = 'jumlah';
				$title = 'Populasi Ayam ';
				$tooltip = 'Jumlah populasi';
				$legend_y = 'Populasi (ekor)';
				break;
			default:
		}
		if(!empty($select_std)){
			$std = $this->db->select($select_std)
				->from('m_std_breeding')
				->where('std_umur between '.$umur_awal.' and '.$umur_akhir)
				->where_in('kode_std_breeding',array($standard_betina,$standard_jantan))
				->order_by('std_umur')
				->get()
				->result_array();
		}

		$this->db->select($select_data)
				->from($tabel)
				->where('tgl_transaksi between \''.$keb_awal.'\' and \''.$keb_akhir.'\'')
				->where(array('no_reg' => $noreg));
		if(!empty($where_data)){
			$this->db->where($where_data);
		}
		$data = $this->db
				->order_by('tgl_transaksi')
				->get()
				->result_array();

		$jenis_kelamin = array('b','j');
		$tmp = array();
		$std_tmp = array();
		$std_data = array();
		$_tmpke = array();
		foreach($jenis_kelamin as $jk){
			$tmp[$jk] = array();
			$std_tmp[$jk] = array();
			$std_data[$jk] = array();
			$_tmpke[$jk] = array();
		}
		if(!empty($std)){
			foreach($std as $s){
				$jk = strtolower($s['jenis_kelamin']);
				$umur = $s['std_umur'];
				$std_tmp[$jk][$umur] = $s[$std_kolom];
			}
			/*tambahkan untuk label*/
			array_push($label,'Standar '.$tooltip);
		}

		$axis_x = array();
		if(!empty($data)){
			/*tambahkan untuk label*/
			array_push($label,$tooltip);
			/*
			foreach($jenis_kelamin as $jk){
				array_push($tmp[$jk],$tooltip);
				if(isset($std_data[$jk])) array_push($std_data[$jk],'Standar '.$kolom_data);
			}
			*/
			foreach($data as $d){
				$umur = dateDifference($d['tgl_transaksi'],$doc_in);
				$umur_hari = $umur % 7;
				$umur_minggu = (int) ($umur / 7) ; /* dalam minggu */
				foreach($jenis_kelamin as $jk){
					$tmp_data = array($d['tgl_transaksi']);
					if(!empty($select_std)){
						if(isset($std_data[$jk])) array_push($tmp_data,$std_tmp[$jk][$umur_minggu]);
					}
					array_push($tmp_data,$d[$jk.'_'.$kolom_data]);
					array_push($tmp[$jk],$tmp_data);
				}

			//	array_push($axis_x,$umur_minggu.'M '.$umur_hari.'H ' . tglIndonesia($d['tgl_transaksi'],'-',' '));

			}

			if(!empty($tabel2)){
				$dataKE = $this->db->select($select_data2)
				->from($tabel2)
				->where('tgl_transaksi between \''.$keb_awal.'\' and \''.$keb_akhir.'\'')
				->where(array('no_reg' => $noreg))
				->get()
				->result_array();
				/* cari konsumsi per ekor */
				$jml_ayam = array();
				foreach($data as $t){
					foreach($jenis_kelamin as $jk){
						if(isset($jml_ayam[$jk])){
							$jml_ayam[$jk] = array();
						}
						$jml_ayam[$jk][$t['tgl_transaksi']] = $t[$jk.'_'.$kolom_data];
					}
				}
				/*
				foreach($jenis_kelamin as $jk){
					array_push($_tmpke[$jk],$tooltip);
				}*/
				foreach($dataKE as $ke){
					$jk = strtolower($ke['jenis_kelamin']);
					$tgl = $ke['tgl_transaksi'];
					$pakai_ekor = isset($jml_ayam[$jk][$tgl]) ? ($ke['brt_pakai'] * 1000)/$jml_ayam[$jk][$tgl] : 0;
					$_tmpke[$jk][$tgl] = $pakai_ekor;

				}
				foreach($tmp as $jk => &$perjk){
					foreach($perjk as &$t){
						$tgl = $t[0];
						array_pop($t);
						$ke = isset($_tmpke[$jk][$tgl]) ? $_tmpke[$jk][$tgl] : 0;
						array_push($t,$ke);
					}

				}

			}
			$result = $tmp;
			$hasil['data'] = $result;
	//		$hasil['std'] = $std_data;
	//		$hasil['x'] = $axis_x;
			$hasil['title'] = $title;
			$hasil['legend_y'] = $legend_y;
			$hasil['label'] = $label;
			$hasil['status'] = 1;
		}

		echo json_encode($hasil);
	}
}
