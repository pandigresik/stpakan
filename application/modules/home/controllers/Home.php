<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Home extends MY_Controller{
	protected $grup_farm;
	private $_canSetACK = array();
	public function __construct(){
		parent::__construct();
		$this->load->helper('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_canSetACK = array('PPC');
	}
	public function index(){
		//$this->db->db_select($this->session->db);
		$data['project_name'] = 'ST Pakan';
		$data['base_url'] = base_url();
		$level_user = $this->session->userdata('level_user');
		$data['nama_user'] = $this->session->userdata('nama_user');
		$level_user_db = $this->session->userdata('level_user_db');
		$data['menu'] = $this->build_menu($level_user_db);
		$tgl = $this->getDateServer();
		$data['tanggal_server'] = $tgl->tglserver;
		$data['user'] = array('level' => $level_user, 'farm' => $this->session->userdata('kode_farm'));		
		switch ($level_user){
			case 'DB':
				$data['content'] = $this->direktur_breeding();
				break;
			case 'KF':
				$data['content'] = $this->kepala_farm($tgl->tglserver);
				break;
			case 'PD':
				$data['content'] = $this->presdir();
				break;
			case 'AB':
				$data['content'] = $this->admin_breeding();
				break;
			case 'P':
				$data['content'] = $this->pengawas();
				break;
			case 'PPC':
				$data['content'] = $this->ppic($tgl->tglserver);
				break;
			case 'KD':
				$data['content'] = $this->kadept();
				break;
			case 'KDV':
				$data['content'] = $this->kadiv();
				break;
			case 'KA':
				$data['content'] = $this->kabagadmin();
				break;
			case 'KDB':
				$data['content'] = $this->kadeptadmin();
				break;	
			default :
				$data['content'] = '';
		}
		
		$this->load->view('home',$data);
	}

	public function view_pp(){
		$no_pp = $this->input->get('no_pp');
		$status_pp = $this->input->get('status_pp');
		$no_flok = $this->input->get('flok');
		$data['project_name'] = 'ST Pakan';
		$data['base_url'] = base_url();
		$level_user = $this->session->userdata('level_user');
		$data['nama_user'] = $this->session->userdata('nama_user');
		$data['content'] =  Modules::run('permintaan_pakan/permintaan_pakan/transaksi_pp',$no_pp,$status_pp,$no_flok);
		$tgl = $this->getDateServer();
		$data['tanggal_server'] = $tgl->tglserver;
		$this->load->view('home/view_pp_void',$data);
	}

	public function view_lhk(){
		$no_reg = $this->input->get('no_reg');
		$tgl_lhk = $this->input->get('tgl_lhk');
		$doc_in = $this->input->get('doc_in');
		$data['project_name'] = 'ST Pakan';
		$data['base_url'] = base_url();
		$level_user = $this->session->userdata('level_user');
		$data['nama_user'] = $this->session->userdata('nama_user');
		$data['content'] =  Modules::run('riwayat_harian_kandang/view_lhk',$no_reg,$tgl_lhk,$doc_in);

		$this->load->view('home/view_lhk',$data);
	}

	public function view_lhk_bdy(){
		$no_reg = $this->input->get('no_reg');
		$tgl_lhk = $this->input->get('tgl_lhk');
		$doc_in = $this->input->get('doc_in');
		$data['project_name'] = 'ST Pakan';
		$data['base_url'] = base_url();
		$level_user = $this->session->userdata('level_user');
		$data['nama_user'] = $this->session->userdata('nama_user');
	//	$data['content'] =  Modules::run('riwayat_harian_kandang/view_lhk',$no_reg,$tgl_lhk,$doc_in);
		$tgl = $this->getDateServer();
		$data['today'] = $tgl->tglserver;
		/* cari flok, nama kandang dan nama farm */
		$d = $this->db->select('ks.flok_bdy,ks.kode_farm,mf.nama_farm,mk.nama_kandang')
					->join('m_farm mf','mf.kode_farm = ks.kode_farm')
					->join('m_kandang mk','ks.kode_kandang = mk.kode_kandang and mk.kode_farm = ks.kode_farm')
					->where(array('ks.no_reg' => $no_reg))
					->get('kandang_siklus ks')
					->row();
		$data['nama_farm'] = $d->nama_farm;
		$data['kode_farm'] = $d->kode_farm;
		$flock = $d->flok_bdy;
		$nama_kandang = $d->nama_kandang ;

		$content = $this->load->view('riwayat_harian_kandang/lhk_bdy',$data,true);
		$this->load->view('home/bdy/view_lhk',array('content'=>$content,'tgl_docin'=>$doc_in,'tgl_lhk'=>$tgl_lhk,'noreg'=>$no_reg,'nama_kandang'=>$nama_kandang,'flock'=>$flock));
	}

	private function presdir(){
	//	$data['list_farm'] = Modules::run('home/kertas_kerja/list_farm_kandang');
		$data['list_farm'] = null;
		$data['show_output'] = 1;
		return $this->load->view('home/kertas_kerja',$data,true);
	}

	private function kabagadmin(){
		$level_user = $this->session->userdata('level_user');
		/* cek apakah ada standart yang baru */
		$docin_reject = $this->notif_rencana_docin_reject($level_user);
		$standart_baru = $this->notif_standart_baru();
		$ploting_do_reject = $this->notif_ploting_do_reject();
		$data = array();
		$data['notif'] = array_merge($standart_baru,$docin_reject,$ploting_do_reject);		
		
		$data_dashboard['op'] = $this->plotting_do();		
		$data['farm'] = $this->db->select(array('kode_farm','nama_farm'))->from('m_farm')->get()->result_array();	
		$data['dashboard'] = $this->getDashboard($data_dashboard);	
		return $this->load->view('home/kabagadmin',$data,true);
	}


	private function pengawas(){
		$level_user = $this->session->userdata('level_user');
		$kode_pegawai = $this->session->userdata('kode_user');
		$kode_farm = $this->session->userdata('kode_farm');
		/* cek apakah ada standart yang baru */
		$data = array();
		$data['notif'] = $this->notif_bapd($kode_farm,$kode_pegawai);		
		
		return $this->load->view('home/content',$data,true);
	}

	private function kadept(){
		$level_user = $this->session->userdata('level_user');
		$this->load->model('forecast/m_forecast','forecast');
		$this->load->model('forecast/m_import_docin','mi');
		$this->load->model('report/M_kontrol_stok_glangsing','m_ksg');
		$data['notif'] = array_merge($this->notif_mutasi_pakan($level_user),$this->notif_pp_reject(),$this->notif_rencana_docin_reject($level_user));
		$data['farm'] = $this->db->select(array('kode_farm','nama_farm'))->from('m_farm')->get()->result_array();	
		$select =   array('kode_farm','count(kode_farm) as total');  
		$kode_siklus_aktif = $this->db->select(array('kode_siklus'))->where(array('status_periode' => 'A'))->get_compiled_select('m_periode');
		$data_dashboard['pp'] = $this->db->select($select)->from('lpb')
				->where_in('status_lpb',array('RJ','N'))
				->where('kode_siklus in ('.$kode_siklus_aktif.')')
				->group_by('kode_farm')
				->get()->result_array();
						
		$data_dashboard['forecast'] = $this->forecast->get_count_forecast_by_state('P1')->result_array(); //P2
		$data_dashboard['rdit'] = $this->mi->siklus_tahunan()->result_array();		
	/*	$data_dashboard['kontrol_stok_glangsing'] = array();
		foreach ($data['farm'] as $key => $val) {
			$return = $this->m_ksg->getStokGlangsingData($val['kode_farm'], '', $level_user);
			foreach ($return as $key2 => $val2) {
				$data_dashboard['kontrol_stok_glangsing'][] = $val2;
			}	
		}*/
		$data_dashboard['pengajuan_glangsing'] = $this->permintaan_glangsing('N');		
		$data_dashboard['budget_glangsing'] = $this->budget_glangsing('N');	
		$data_dashboard['retur_pakan_farm'] = $this->retur_pakan_farm('N');			
		$data_dashboard['ploting_pelaksana'] = $this->ploting_pelaksana('N');
		$data_dashboard['ploting_do'] = $this->ploting_do('D');
		$data['dashboard'] = $this->getDashboard($data_dashboard);	
		return $this->load->view('home/kadept',$data,true);
	}

	private function kadeptadmin(){
		$level_user = $this->session->userdata('level_user');
		$this->load->model('forecast/m_forecast','forecast');
		$this->load->model('forecast/m_import_docin','mi');
		$this->load->model('report/M_kontrol_stok_glangsing','m_ksg');
	//	$data['notif'] = array_merge($this->notif_mutasi_pakan($level_user),$this->notif_pp_reject(),$this->notif_rencana_docin_reject($level_user));
		$data['farm'] = $this->db->select(array('kode_farm','nama_farm'))->from('m_farm')->get()->result_array();	
		$select =   array('kode_farm','count(kode_farm) as total');  	
		$data_dashboard['pengajuan_glangsing'] = $this->permintaan_glangsing('R');	
		$data_dashboard['budget_glangsing'] = $this->budget_glangsing('R');	
		$data_dashboard['ploting_do'] = $this->ploting_do('D');
		$data['dashboard'] = $this->getDashboard($data_dashboard);	
		return $this->load->view('home/kadept',$data,true);
	}

	private function kadiv(){
		$level_user = $this->session->userdata('level_user');
		$result = array();

		$this->load->model('permintaan_pakan/m_lpb','lpb');
		$this->load->model('forecast/m_forecast','forecast');
		$this->load->model('forecast/m_import_docin','mi');
		$this->load->model('report/M_kontrol_stok_glangsing','m_ksg');
		$this->load->model('sales_order/m_pengajuan_harga','ph');				
		$data['farm'] = $this->db->select(array('kode_farm','nama_farm'))->from('m_farm')->get()->result_array();	
		$select =   array('kode_farm','count(kode_farm) as total');  
		$kode_siklus_aktif = $this->db->select(array('kode_siklus'))->where(array('status_periode' => 'A'))->get_compiled_select('m_periode');
		$data_dashboard['pp'] = $this->db->select($select)->from('lpb')
				->where('status_lpb','RV')
				->where('kode_siklus in ('.$kode_siklus_aktif.')')
				->group_by('kode_farm')
		        ->get()->result_array();
		$data_dashboard['forecast'] = $this->forecast->get_count_forecast_by_state('P2')->result_array(); //P2
		$data_dashboard['rdit'] = $this->mi->siklus_tahunan()->result_array();		
		$data_dashboard['pengajuan_harga'] = $this->ph->get_count_by_status('R1'); //R1
		$data_dashboard['retur_pakan_farm'] = $this->retur_pakan_farm('RV');			
		$data_dashboard['pengajuan_glangsing'] = $this->permintaan_glangsing('A0');	
		$data_dashboard['ploting_pelaksana'] = $this->ploting_pelaksana('RV');
		$data_dashboard['ploting_do'] = $this->ploting_do('R');
		$data['dashboard'] = $this->getDashboard($data_dashboard);			
		return $this->load->view('home/kadiv',$data,true);

	}

	private function ppic($tglserver){
		$this->load->model('forecast/m_forecast','mf');
		$data['list_farm'] = $this->mf->list_farm()->result_array();
		$user_level = $this->session->userdata('level_user');
		$active_tab = $this->input->get('active');

		if(in_array($user_level,$this->_canSetACK)){
			$data['ack'] = true;
		}
		else{
			$data['ack'] = false;
		}
		$data['tab_ack'] = array(
			'brd' => 'Konfirmasi Forecast Breeding',
			'bdy' => 'Konfirmasi Forecast Budidaya'
		);
		$data['active_tab'] = !empty($active_tab) ? $active_tab : 'brd';
		$tglserver_plus1 = new \DateTime($tglserver);
		$tglserver_plus1->add(new \DateInterval('P1D'));

		$data['notif'] = array_merge($this->notif_konfirmasi_rp($tglserver),$this->notif_konfirmasi_rp_bdy($tglserver_plus1->format('Y-m-d')));
		$data['flock'] = false;
		$data['hide'] = 'hide';
		$data_view = $this->load->view('forecast/forecast_ppic',$data,true);

		return $data_view;


	}

	private function kepala_farm($tglserver){
		/*$this->load->library('gantt');
		$this->load->model('permintaan_pakan/m_permintaan_pakan','mpp');
		$kode_farm = $this->session->userdata('kode_farm');
		$permintaan_pakan = $this->mpp->detail_lpb($kode_farm)->result_array();
		if(!empty($permintaan_pakan)){
			$data_kandang = $this->mpp->docin_rhk_perkandang($kode_farm)->result_array();
			$data = array();
			$loop = array('tgl_kebutuhan','tgl_kirim');
			$tmp = array();
			$doc_in = array();
			$rhk = array();
			$end_permintaan = end($permintaan_pakan);
			$keb_akhir_min_satu =  new DateTime($end_permintaan['tgl_keb_akhir']);

			$diff1Day = new DateInterval('P1D');
			$keb_akhir_min_satu->sub($diff1Day);

			$max_minta_pp = new DateTime($end_permintaan['tgl_keb_akhir']);
			$diff5Day = new DateInterval('P4D');
			$max_minta_pp->sub($diff5Day);

			foreach($data_kandang as $k){
				
				foreach($loop as $l){
					$tmp['label'] = $k['nama'];
					$tmp['mark'] = array();

					if(!empty($k['rhk'])){
						array_push($tmp['mark'],array('date' => $k['rhk'] ,'class'=>'sudah_input_rhk','param' => '>'));
					}
					if(!empty($k['entry'])){
						array_push($tmp['mark'],array('date' => $k['entry'] ,'class'=>'hari_input_rhk','param' => '>'));
					}
					if(!empty($k['doc_in'])){
						array_push($tmp['mark'],array('date' => $k['doc_in'] ,'class'=>'belum_doc_in','param' => '>'));
					}
					array_push($tmp['mark'],array('date' => $keb_akhir_min_satu->format('Y-m-d') ,'class'=>'akhir_analisa_performance','param' => '='));


					$tmp['content'] = array();
					// isi data content untuk tampilan grafik 

					if($l == 'tgl_kirim'){
						$i = 1;
						foreach($permintaan_pakan as $p){
							array_push($tmp['content'],array(
									'start' => $p['tgl_kirim'],
									'end' => $p['tgl_kirim'],
									'class' => 'tgl_kirim',
									'text'  => 'K'.$i,
								)
							);
						$i++;
						}
					}
					else{
						$i = 1;
						foreach($permintaan_pakan as $p){
							array_push($tmp['content'],array(
								'start' => $p['tgl_keb_awal'],
								'end' => $p['tgl_keb_akhir'],
								'class' => 'tgl_kebutuhan',
								'text'  => 'K'.$i,
								)
							);
							$i++;
						}
					}

					array_push($data,$tmp);
				}
				if(!empty($k['rhk'])){
					array_push($rhk,$k['rhk']);
				}
				array_push($doc_in,$k['doc_in']);
			}
			// kumpulkan data untuk grafik notifikasi permintaan pakan 
			$data_perpermintaan_pakan = array();
			$i = $j = 0;
			foreach($permintaan_pakan as $p){

				if(!isset($data_perpermintaan_pakan[$p['no_lpb']])){
					$data_perpermintaan_pakan[$p['no_lpb']]['tgl_kirim'] = array('label' => $p['no_lpb'],'content' => array());
					$data_perpermintaan_pakan[$p['no_lpb']]['tgl_kebutuhan'] = array('label' => $p['no_lpb'],'content' => array());
					$i++;
					$j = 0;
				}
				$j++;
				foreach($loop as $l){
					if($l == 'tgl_kirim'){
						array_push($data_perpermintaan_pakan[$p['no_lpb']]['tgl_kirim']['content'],array(
								'start' => $p['tgl_kirim'],
								'end' => $p['tgl_kirim'],
								'class' => 'tgl_kirim kirim_'.$p['tgl_kirim'],
								'text'  => 'P'.$i.'_'.$j,
							)
						);
					}
					else{
						array_push($data_perpermintaan_pakan[$p['no_lpb']]['tgl_kebutuhan']['content'],array(
								'start' => $p['tgl_keb_awal'],
								'end' => $p['tgl_keb_akhir'],
								'class' => 'tgl_kebutuhan kirim_'.$p['tgl_kirim'],
								'text'  => 'P'.$i.'_'.$j,
							)
						);
					}
				}

			}
			$data_perpakan = array();
			$mark = array();
			if(!empty($rhk)){
				array_push($mark,array('date' => min($rhk) ,'class'=>'sudah_input_rhk','param' => '>'));
			}

			array_push($mark,array('date' => $max_minta_pp->format('Y-m-d') ,'class'=>'akhir_permintaan','param' => '='));
			foreach($data_perpermintaan_pakan as $no_pp => $perpp){
				array_push($data_perpakan,array('label'=> $no_pp,'content' => $perpp['tgl_kebutuhan']['content'],'mark' => $mark));
				array_push($data_perpakan,array('label'=> $no_pp,'content' => $perpp['tgl_kirim']['content'],'mark' => $mark));
			}

			$param_perpp = array('aside' => false);
			$gantt_pp = new Gantt($data_perpakan,$param_perpp);
			$grafik_pp = $gantt_pp->render();
			$data['grafik_pp'] = $grafik_pp;
		}
		else{		
			$data['grafik_pp'] = '<div class="alert alert-warning">Belum ada permintaan pakan</div>';
		}
		*/
		$kode_farm = $this->session->userdata('kode_farm');
		$data['grafik_pp'] = '';
		$level_user = $this->session->userdata('level_user');
		$data['notif'] = $this->notif_ploting_pelaksana($kode_farm);
		return $this->load->view('home/kepala_farm',$data,true);
	}
	private function logistik(){
		$this->load->model('permintaan_pakan/m_permintaan_pakan','mpp');
		$belum_ada_do = $this->mpp->op_belum_ada_do()->result_array();
		$data['list_op'] = $belum_ada_do;
		return $this->load->view('home/logistik',$data,true);
	}

	private function direktur_breeding(){
		$this->load->model('permintaan_pakan/m_permintaan_pakan','mpp');
		$butuh_approval = $this->mpp->butuh_approval()->result_array();
		$data['butuh_approval'] = $butuh_approval;
		return $this->load->view('home/direktur_breeding',$data,true);
	}

	private function admin_breeding(){
		$this->load->model('permintaan_pakan/m_permintaan_pakan','mpp');
		$list_ack = $this->mpp->ack_admin_breeding()->result_array();
		$data['list_ack'] = $list_ack;
		return $this->load->view('home/admin_breeding',$data,true);
	}

	private function notif_mutasi_pakan($level_user){
		$this->load->model('mutasi_pakan/m_main','mp');
		$result = array('mutasi_pakan' => '');
		$mutasi_pakan = $this->mp->notif_mutasi_pakan($level_user);
		$result['mutasi_pakan'] = $mutasi_pakan;
		return $result;
	}
	private function notif_ploting_pelaksana($kode_farm){
		$result = array();
		$this->load->model('kandang/m_transaksi','m_ploting');
		$belum_ploting = $this->m_ploting->notifikasi($kode_farm);
		if(!empty($belum_ploting)){
			$belum_ploting_arr = array_column($belum_ploting,'flok_bdy');
			$flok_str = implode(', ',$belum_ploting_arr);
			$result['ploting_pelaksana'] = array('title' => 'Ploting Pelaksana','message' => array('Flok '.$flok_str.' sudah diaktivasi, harap melakukan ploting pelaksana untuk flok '.$flok_str));
		}
		return $result;
	}

	private function notif_bapd($kode_farm,$kode_pegawai){
		$result = array();
		$this->load->model('penerimaan_docin/m_bapdoc','bap');
		$rejectBapd = $this->bap->get_by('status = \'RJ\' and no_reg in (select no_reg  from m_ploting_pelaksana where pengawas = \''.$kode_pegawai.'\' and kode_siklus = (select kode_siklus from m_periode where status_periode = \'A\' and kode_farm = \''.$kode_farm.'\')) ');
		if(!empty($rejectBapd)){
			$result['reject_bapd'] = array('title' => 'Informasi','message' => array('Terdapat pengajuan BAPD yang ditolak. Mohon segera menindaklanjuti pengajuan'));
		}
		return $result;
	}

	private function notif_konfirmasi_rp($tglserver,$kode_farm = null){
		$this->load->model('forecast/m_forecast','mf');
		/* yang belum saja */
		$where_realisasi = ' and (kp.realisasi_produksi = \'I\' or kp.realisasi_produksi is null)';
		$list_notif = $this->mf->konfirmasi_rp('',$where_realisasi,$kode_farm)->result_array();
		$belum_ada_rp = array();
		$belum_memenuhi_pp = array();
		$result = array('belum_ada_rp' => null, 'belum_memenuhi_pp' => '');
		if(!empty($list_notif)){
			foreach($list_notif as $n){
			//	$tmp  = array('no_pp' => $n['no_lpb'], 'nama_farm' => $n['nama_farm'], 'nama_barang' => $n['nama_barang']);
				$tmp = 'Permintaan No. '.$n['no_lpb'].' dengan jenis pakan '.$n['nama_barang'].' nama farm '.$n['nama_farm'];
				if(empty($n['rencana_produksi'])){
					array_push($belum_ada_rp,$tmp);
				}
				else{
					if($n['realisasi_produksi'] != 'Sudah'){
						if($n['tgl_akhir_rencana_produksi'] <= $tglserver){
							array_push($belum_memenuhi_pp,$tmp);
						}
					}
				}
			}

			$result['belum_ada_rp'] = array('title'=>'Pakan Belum Memiliki Rencana Produksi','message'=>$belum_ada_rp);
			$result['belum_memenuhi_pp'] = array('title'=>'Jumlah Permintaan Belum Terpenuhi','message'=> $belum_memenuhi_pp);
		}
		return $result;
	}

	private function notif_pp_reject(){
		$this->load->model('permintaan_pakan/m_lpb','lpb');
		$pp_reject = $this->lpb->notif_pp_reject()->result_array();
		$result = array();
		$result['pp_reject'] = array('title'=>'Alasan Reject','message'=>array());
		if(!empty($pp_reject)){
			foreach($pp_reject as $pr){
				array_push($result['pp_reject']['message'],htmlentities('No. PP '.$pr['no_lpb'].'<br >'.$pr['ket_reject']));
			}
		}
		return $result;
	}
	private function notif_konfirmasi_rp_bdy($tglServer){		
		/* cari kodepakan yang digunakan oleh budidaya */
		$rp = array();
		$list_pj = $this->db->query("select distinct kode_barang
									from M_STD_BUDIDAYA_D
								where kode_barang is not null and kode_barang != ''")
							->result();
		$plot_rp = $this->db->query("select distinct irp.rencana_produksi
					from estimasi_tanggal_produksi etp
					left join alokasi_hasil_produksi ahp
						on etp.rencana_kirim = ahp.rencana_kirim
					left join item_rencana_produksi irp
						on irp.id = ahp.item_rencana_produksi
				where etp.tanggal_produksi = '".$tglServer."'")
				->result();
		$sudah_plot = array();
		$belum_plot = 0;

		if(!empty($plot_rp)){
			foreach($plot_rp as $pt){
				if(!empty($pt->rencana_produksi)){
						array_push($sudah_plot,$pt->rencana_produksi);
				}
				else{
					$belum_plot++;
				}
			}
		}

		if(!empty($list_pj) && $belum_plot){
			foreach($list_pj as $p){
				$data_rp = array('awal'=>$tglServer,'akhir'=>$tglServer,'kodepj'=>$p->kode_barang);
				$r = Modules::run('cproduksi/rencanaproduksi/listrencanaproduksi',$data_rp);
				if($r['status']){
					foreach($r['content']->rps as $rps){
						if(!in_array($rps->rp,$sudah_plot)){
							if(!in_array($rps->rp,$rp)){
									array_push($rp,$rps->rp);
							}
						}
					}
				}
			}
		}
		$result = array();
		$result['konfirmasi_rp_bdy'] = array('title'=>'Rencana Produksi','message'=>array());
		if(!empty($rp)){
			foreach($rp as $_rencanaproduksi){
				array_push($result['konfirmasi_rp_bdy']['message'],'Rencana Produksi '.$_rencanaproduksi.' tanggal '.tglIndonesia($tglServer,'-',' ').' dapat diplot.');
			}
		}
		return $result;
	}
	private function notif_rencana_docin_reject($level_user){
		$this->load->model('forecast/m_forecast','mf');
		$docin_reject = $this->mf->notif_rencana_docin_reject($level_user)->result_array();
		$result = array();
		$result['docin_reject'] = array('title'=>'Alasan Reject Rencana DOC In','message'=>array());
		if(!empty($docin_reject)){
			foreach($docin_reject as $pr){
				array_push($result['docin_reject']['message'],'Rencana DOC In tahun '.$pr['tahun'].' direject karena '.$pr['keterangan']);
			}
		}
		return $result;
	}

	private function notif_ploting_do_reject(){
		$ploting_do_reject = $this->ploting_do('T');
		$result = array();
		$result['ploting_do_reject'] = array('title'=>'Notifikasi','message'=>array());
		if(!empty($ploting_do_reject['total'])){
			array_push($result['ploting_do_reject']['message'],'Terdapat ploting DO yang ditolak. Mohon segera ditindaklanjuti');
		}
		return $result;
	}

	private function notif_standart_baru(){
		$this->load->model('forecast/m_forecast','mf');
		$notif = $this->mf->check_standart_baru()->result_array();
		$result = array();
		if(!empty($notif)){
			$result['standart_baru'] = array('title'=>'Informasi','message'=>array());
			foreach($notif as $k => $n){
				$tmp = '<strong>Farm '.$n['nama_farm'].'</strong><br >
				Terdapat standart budidaya baru dengan effective date '.convertElemenTglWaktuIndonesia($n['tgl_efektif']).
				'<br /> Apakah anda akan melakukan perubahan rencana DOC In ?
				<div class=\'row\'><button onclick=\'DocIn.hideToastr('.$k.')\' class=\'btn clear col-md-4 btn-default\'>Tidak</button>&nbsp;<button class=\'col-md-4 col-md-offset-2 btn btn-default\' data-tgl_efektif=\''.$n['tgl_efektif'].'\' data-farm=\''.$n['nama_farm'].'\' data-std=\''.$n['std_baru'].'\' data-kode_farm=\''.$n['kode_farm'].'\' data-populasi=\''.$n['jml_populasi'].'\' onclick=\'DocIn.showStandartBaru(this,'.$k.')\'>Ya</button></div>
	<!--			<div class=\'row new-line\'><div onclick=\'DocIn.lihatDataStandart(this)\' data-std=\''.$n['std_baru'].'\' class=\'col-md-10 btn btn-default\'>Lihat Standart Budidaya</div> -->
				</div>';
				array_push($result['standart_baru']['message'],htmlentities($tmp));
			}
		}
		return $result;
	}

	private function build_menu($level_user){
		$this->load->model('user/m_user','mu');

		$list = $this->mu->get_menu($level_user)->result_array();		
		$k = array();
		$prev_parent = null;
		$prev_link = null;
		foreach($list as $l => $m){
			/* parent menu */
			$cur_parent = $m['PARENT_ID'];
			if(empty($m['PARENT_ID'])){
				if(!empty($prev_parent)){
					if($cur_parent != $prev_parent){
						array_push($k,'</ul></li>');
					}
				}
				$c_link = trim($m['LINK']);
				if(empty($c_link)){
					if($cur_parent == $prev_parent && empty($prev_link)){
						//array_push($k,'</ul></li>');
					}
					array_push($k,'<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">'.$m['VALUE'].' <span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">');
				}
				else{
					array_push($k,'<li><a class="ajax" href="#'.$m['LINK'].'">'.$m['VALUE'].'</a></li>');
				}
			}
			else{
				array_push($k,'<li><a class="ajax" href="#'.$m['LINK'].'">'.$m['VALUE'].'</a></li>');
				if(!empty($prev_parent)){
					if($cur_parent != $prev_parent){
						array_push($k,'</ul></li>');
					}
				}

			}
			$prev_parent = $cur_parent;
			$prev_link = trim($m['LINK']);
		}
		if(!empty($prev_parent)) array_push($k,'</ul></li>');
		return implode(' ',$k);
	}

	public function getDateServer(){
		$this->load->model('home/m_config','mc');
		$tgl = $this->mc->getDate()->row();
		return $tgl;	
	}

	private function create_sub_menu($arr = array()){
		return	array(
				'label' => $arr['VALUE'],
				'url' => $arr['LINK']
		);
	}	

	public function getDashboard($data){
		$result = array();		
		if(isset($data['pp'])){
			foreach ($data['pp'] as $key => $val) {
				$result[$val['kode_farm']]['Permintaan Pakan'] = array('jumlah'=>$val['total'], 'link'=>'permintaan_pakan_v2/permintaan_pakan/main/'.$val['kode_farm']);
			}			
		}
		if(isset($data['pengajuan_glangsing'])){
			foreach ($data['pengajuan_glangsing'] as $key => $val) {
				$result[$val['kode_farm']]['Pengajuan Permintaan Glangsing'] = array('jumlah'=>$val['total'], 'link'=>'report/kontrol_stok_glangsing/index/'.$val['kode_farm']);
			}	
		}
		if(isset($data['retur_pakan_farm'])){
			foreach ($data['retur_pakan_farm'] as $key => $val) {
				$result[$val['kode_farm']]['Retur Pakan Antar Farm'] = array('jumlah'=>$val['total'], 'link'=>'rekap_retur_pakan/retur_pakan_farm/index/');
			}	
		}	
		if(isset($data['forecast'])){
			foreach ($data['forecast'] as $key => $val) {
				$result[$val['kode_farm']]['Aktivasi Siklus Kandang'] = array('jumlah'=>$val['total'], 'link'=>'forecast/forecast/main/'.$val['kode_farm']);
			}	
		}		
		if(isset($data['budget_glangsing'])){
			foreach ($data['budget_glangsing'] as $key => $val) {
				$result[$val['kode_farm']]['Budget Pemakaian Glangsing'] = array('jumlah'=>$val['total'], 'link'=>'budget_pengembalian_glangsing/main/index/'.$val['kode_farm']);
			}	
		}
		if(isset($data['pengajuan_harga'])){
			foreach ($data['pengajuan_harga'] as $key => $val) {
				$result[$val['kode_farm']]['Pengajuan Harga Glangsing'] = array('jumlah'=>$val['total'], 'link'=>'sales_order/pengajuan_harga/index/'.$val['kode_farm']);
			}	
		}
		if(isset($data['op'])){
			foreach ($data['op'] as $key => $val) {
				$result[$val['kode_farm']]['Plotting DO'] = array('jumlah'=>$val['total'], 'link'=>'permintaan_pakan_v2/pembelian_pakan/order?kodefarm='.$val['kode_farm'].'&tglawal='.$val['tglawal'].'&tglakhir='.$val['tglakhir']);
			}			
		}

		if(isset($data['ploting_pelaksana'])){
			foreach ($data['ploting_pelaksana'] as $key => $val) {
				$result[$val['kode_farm']]['Plotting Pelaksana'] = array('jumlah'=>$val['total'], 'link'=>'kandang/plotting_pelaksana/main/'.$val['kode_farm']);
			}			
		}
		
		if(isset($data['kontrol_stok_glangsing'])){
			$farm = array();
			foreach ($data['kontrol_stok_glangsing'] as $key => $val) {
				if($val['jmlOutstanding'] > 0){
					if(!isset($farm[$val['kode_farm']])){
						$farm[$val['kode_farm']] = 0;
					}
					$farm[$val['kode_farm']] += $val['jmlOutstanding'];					
				}				
			}	
			foreach ($farm as $key => $val) {
				$result[$key]['Kontrol Stok Glangsing'] = array('jumlah'=>$val, 'link'=>'report/kontrol_stok_glangsing/index/'.$key);
			}
		}

		
		
		if(isset($data['rdit'])){
			$jml = 0;
			$level_user = $this->session->userdata('level_user');
			$status_rdit = array(
				'KD' => array('PENGAJUAN'),
				'KDV' => array('REVIEW')
			);
			$filter_status = isset($status_rdit[$level_user]) ? $status_rdit[$level_user] : array();
			foreach ($data['rdit'] as $key => $val) {
				if(in_array($val['status'],$filter_status)){
					$jml++;
				}
			}	
			if($jml > 0){
				$result['ALL']['Import Rencana DOC In'] = array('jumlah'=>$jml, 'link'=>'forecast/forecast/import_rencana_docin/');
			}
		}

		if(isset($data['ploting_do'])){
			foreach ($data['ploting_do'] as $key => $val) {
				if(!empty($val['total'])){
					$result['ALL']['Plotting DO'] = array('jumlah'=>$val['total'], 'link'=>'permintaan_pakan_V2/pembelian_pakan/approve');
				}
			}			
		}
		return $result;
	}

	private function plotting_do(){
		$result = array();
		$sql = <<<SQL
		select 	op_d.TGL_KIRIM tgl_kirim	   
				,ks.kode_farm
				,count(*) AS total
				from op
				JOIN kandang_siklus ks ON ks.no_reg = OP.KETERANGAN1 AND ks.STATUS_SIKLUS = 'O'
				inner join op_d
					on op.NO_OP = op_d.NO_OP			
				left join do
					on op.NO_OP = do.NO_OP and do.TGL_KIRIM = op_d.TGL_KIRIM
				where do.NO_OP is NULL
		GROUP BY ks.kode_farm,op_d.TGL_KIRIM
		union all 
		select tgl_kirim,kode_farm,count(*) AS total from (
			SELECT do.kode_farm,do.tgl_kirim 
			FROM DO WHERE status_do = 'T'
			GROUP BY DO.KODE_FARM,DO.TGL_KIRIM
		)x group by x.tgl_kirim,x.kode_farm
		ORDER BY TGL_KIRIM
SQL;
		$tmp = $this->db->query($sql)->result_array();
		if(!empty($tmp)){
			foreach($tmp as $t){
				$kf = $t['kode_farm'];
				if(!isset($result[$kf])){
					$result[$kf] = array('total' => 0, 'tglawal' => '', 'tglakhir' => '','kode_farm' => $kf);
				}
				$result[$kf]['total']++;
				if(empty($result[$kf]['tglawal'])){
					$result[$kf]['tglawal'] = $t['tgl_kirim'];
				}
				if(empty($result[$kf]['tglakhir'])){
					$result[$kf]['tglakhir'] = $t['tgl_kirim'];
				}else{
					if($result[$kf]['tglakhir'] < $t['tgl_kirim']){
						$result[$kf]['tglakhir'] = $t['tgl_kirim'];
					}
				} 
			}
		}
		return $result;
	}

	private function permintaan_glangsing($status){
		$sql = <<<SQL
		SELECT count(*) as total,substring(ln.no_ppsk,6,2) kode_farm FROM log_ppsk_new ln 
		INNER JOIN (
			SELECT pn.no_ppsk,max(lpn.no_urut) no_urut FROM ppsk_new pn
			INNER JOIN m_periode mp ON mp.KODE_SIKLUS = pn.kode_siklus AND mp.STATUS_PERIODE = 'A'
			INNER JOIN log_ppsk_new lpn ON lpn.no_ppsk = pn.no_ppsk
			GROUP BY pn.no_ppsk
		)terakhir ON ln.no_ppsk = terakhir.no_ppsk AND ln.no_urut = terakhir.no_urut
		WHERE ln.status = '{$status}'
		GROUP BY substring(ln.no_ppsk,6,2)
SQL;
		return $this->db->query($sql)->result_array();;	
	}

	private function budget_glangsing($status){
		$sql = <<<SQL
		SELECT count(*) AS total, mp.kode_farm
		FROM BUDGET_GLANGSING bg 
		JOIN M_PERIODE mp ON mp.KODE_SIKLUS = bg.KODE_SIKLUS AND mp.STATUS_PERIODE = 'A'
		WHERE bg.STATUS = '{$status}'
		GROUP BY mp.kode_farm
SQL;
		return $this->db->query($sql)->result_array();;	
	}
	
	private function retur_pakan_farm($status){
		$sql = <<<SQL
		SELECT count(*) AS total, rf.farm_asal kode_farm
		FROM RETUR_FARM rf 		
		WHERE rf.STATUS = '{$status}'
		GROUP BY rf.farm_asal
SQL;
		return $this->db->query($sql)->result_array();;	
	}

	private function ploting_pelaksana($status){
		$sql = <<<SQL
		select count(*) as total,zz.kode_farm from(
			SELECT ks.flok_bdy as total,ks.kode_farm 
			FROM m_ploting_pelaksana mpp 
			join kandang_siklus ks on ks.no_reg = mpp.no_reg
			where mpp.status = '{$status}'
			group by mpp.kode_siklus,ks.kode_farm,ks.flok_bdy
		)zz group by zz.kode_farm
SQL;
		return $this->db->query($sql)->result_array();;	
	}

	private function ploting_do($status){
		$sql = <<<SQL
		select count(*) AS total from (
			SELECT do.kode_farm,do.tgl_kirim 
			FROM DO WHERE status_do = '{$status}'
			GROUP BY DO.KODE_FARM,DO.TGL_KIRIM
		)x
SQL;
		return $this->db->query($sql)->result_array();;	
	}
}
