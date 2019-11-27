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
			case 'L':
				$data['content'] = $this->logistik();
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
			default :
				$data['content'] = '';
		}
		//echo '<pre>'.print_r($data['menu']);
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
		$data = array();
		$data['notif'] = array_merge($standart_baru,$docin_reject);

		return $this->load->view('home/kabagadmin',$data,true);
	}

	private function kadept(){
		$level_user = $this->session->userdata('level_user');
		$data['notif'] = array_merge($this->notif_mutasi_pakan($level_user),$this->notif_pp_reject(),$this->notif_rencana_docin_reject($level_user));
		return $this->load->view('home/kadept',$data,true);
	}

	private function kadiv(){
		$level_user = $this->session->userdata('level_user');
		$result = array();

		$this->load->model('permintaan_pakan/m_lpb','lpb');
		$this->load->model('forecast/m_forecast','forecast');
		$this->load->model('forecast/m_kandang_siklus','ks');
		
		
		$pp = count($this->lpb->get(array('status_lpb'=>'RV'))->result());
		$forecast = count($this->forecast->get_forecast_by_state('P2')->result());
		$rdit = count($this->ks->get_rdit_by_state('TGL_APPROVE2 is null')->result());

		if($pp > 0){
			$result['Permintaan Pakan'] = array('jumlah'=>$pp, 'link'=>'permintaan_pakan/permintaan_pakan/main');
		}
		if($forecast > 0){
			$result['Aktivasi Siklus Kandang'] = array('jumlah'=>$forecast, 'link'=>'forecast/forecast/main');
		}
		if($rdit > 0){
			$result['Import Rencana DOC In'] = array('jumlah'=>$rdit, 'link'=>'forecast/forecast/import_rencana_docin');
		}
		
		$data['dashboard'] = $result;
		$data['notif'] = $this->notif_mutasi_pakan($level_user);
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
		$this->load->library('gantt');
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
				/* data tgl_kirim */
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
					/* isi data content untuk tampilan grafik */

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
			/* kumpulkan data untuk grafik notifikasi permintaan pakan */
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

	//		$param = array('first' => min($doc_in));
	/*		$gantt = new Gantt($data,$param);
			$grafik = $gantt->render();
			$data['grafik'] = $grafik;
	*/
			$param_perpp = array('aside' => false);
			$gantt_pp = new Gantt($data_perpakan,$param_perpp);
			$grafik_pp = $gantt_pp->render();
			$data['grafik_pp'] = $grafik_pp;
		}
		else{
		//	$data['grafik'] = '<div class="alert alert-warning">Belum ada permintaan pakan</div>';
			$data['grafik_pp'] = '<div class="alert alert-warning">Belum ada permintaan pakan</div>';
		}

		$level_user = $this->session->userdata('level_user');
		$data['notif'] = $this->grup_farm == 'bdy' ? $this->notif_mutasi_pakan($level_user) : $this->notif_konfirmasi_rp($tglserver,$kode_farm);
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
		// $tglServer = '2016-01-02';
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
				if(empty($m['LINK'])){
					if($cur_parent == $prev_parent && empty($prev_link)){
						array_push($k,'</ul></li>');
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
			$prev_link = $m['LINK'];
		}
		if(!empty($prev_parent)) array_push($k,'</ul></li>');
		return implode(' ',$k);
	}

	public function getDateServer(){
		$this->load->model('home/m_config','mc');
		$tgl = $this->mc->getDate()->row();
		return $tgl;
	//	return (object)array('tglserver' => '2015-12-29','saatini'=>'2015-12-29 11:00:00');
	}

	private function create_sub_menu($arr = array()){
		return	array(
				'label' => $arr['VALUE'],
				'url' => $arr['LINK']
		);
	}

	public function tesSQL(){
		$no_pp = '000001/JD/IV/2016';
		$k = $this->db->select('kode_farm')->where(array('no_lpb'=>$no_pp))->get('lpb')->row_array();
		echo $this->db->last_query();
		echo $k['kode_farm'];
	}
}
