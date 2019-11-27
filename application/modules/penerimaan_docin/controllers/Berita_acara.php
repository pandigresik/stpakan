<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Berita_acara extends MY_Controller{
	protected $result;
	protected $_user;
	protected $_idFarm;
	private $grup_farm;
	private $listAccess;
	public function __construct(){
		parent::__construct();
		$this->result = array('status' => 0, 'content'=> '', 'message' => '');
		$this->_user = $this->session->userdata('kode_user');
		$this->load->helper('stpakan');
		$this->load->config('stpakan');
		$this->load->model('penerimaan_docin/m_bapd','bapd');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->listAccess = array(
			'P' => 'create',
			'AG' => 'create',
			'KF' => 'approve'
		);
	}
	public function index(){
		$kodefarm = $this->session->userdata('kode_farm');
		$user_level = $this->session->userdata('level_user');
		$this->load->model('penerimaan_docin/m_hatchery','mh');
		$data['list_kandang'] = $this->db->where(array('kode_farm' => $kodefarm))->get('m_kandang')->result_array();
		$data['list_hatchery'] = $this->mh->as_array()->get_all();
		$data['list_siklus'] = $this->db->select('mp.kode_siklus,mp.periode_siklus')
								->join('budget_glangsing bg','bg.kode_siklus = mp.kode_siklus')
								->where(array('kode_farm' => $kodefarm))
								->order_by('mp.kode_siklus','desc')
								->get('m_periode mp')->result_array();
								
		$namaFarms = $this->config->item('namaFarm');								
		$data['nama_farm'] = $namaFarms[$kodefarm];								
		switch($user_level){
			case 'P':
				$view_user = 'daftarbapd';
				$data['access'] = $this->listAccess[$user_level];
				break;
			case 'AG':
				$view_user = 'daftarbapd';
				$data['access'] = $this->listAccess[$user_level];
				break;
			case 'KF':
				$view_user = 'daftarbapd';
				$data['access'] = $this->listAccess[$user_level];
				break;
			default:
				$view_user = 'allfarmbapd';
		}
		$data['disabled'] = 'disabled';
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/'.$view_user,$data);
	}

	public function list_bapd(){
		$kode_siklus = $this->input->get('kode_siklus');
		$kode_kandang = $this->input->get('kode_kandang');
		$kode_hatchery = $this->input->get('kode_hatchery');
		$tindak_lanjut = $this->input->get('tindak_lanjut');
		$kode_farm = $this->session->userdata('kode_farm');
		$user_level = $this->session->userdata('level_user');
		$data['user_level'] = $user_level ;
		
		$pencarian = array(
			'tindak_lanjut' => $tindak_lanjut,
			'kode_siklus' => $kode_siklus,
			'kode_kandang' => $kode_kandang,
			'kode_hatchery' => $kode_hatchery,
			'kode_farm' => $kode_farm,
			'level_user' => $user_level,
			'kode_pegawai' => $this->_user	
		);
		
		$data['list_bapd'] = $this->bapd->listBapdSJ($pencarian)->result_array();
		$list_no_reg = array_column($data['list_bapd'],'no_reg');
		$data['riwayat'] = simpleGrouping($this->bapd->riwayatbap($list_no_reg)->result_array(),'no_reg');
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/list_bapd',$data);
	}

	public function detailbapddoc(){
		$noreg = $this->input->get('noreg');
		$this->load->model('penerimaan_docin/m_bapdoc', 'bd');
		$this->load->model('penerimaan_docin/m_bapdocbox', 'bdb');
//		$this->load->model('penerimaan_docin/m_logbapdoc', 'lbd');
		$user_level = $this->session->userdata('level_user');
		$access = isset($this->listAccess[$user_level]) ? $this->listAccess[$user_level] : '';
		$allsj = $this->bdb->as_array()->get_many($noreg);
		$performance = $this->bd->as_array()->get($noreg);
		/* dapatkan jmlbox */
		$jmlbox = 0;
		$resumeSj = array();
		foreach ($allsj as $sj) {
			$nosj = $sj['NO_SJ'];
			if(!isset($resumeSj[$nosj])){
				$resumeSj[$nosj] = array('no_sj' => $sj['NO_SJ'],'jmlbox' => 0, 'tgl_terima' => $sj['TGL_TERIMA'], 'no_reg' => $sj['NO_REG'], 'list_sj' => array());
			}
			$resumeSj[$nosj]['jmlbox'] += $sj['JML_BOX'];
			array_push($resumeSj[$nosj]['list_sj'], array('kodebox' =>$sj['KODE_BOX'], 'jml' => $sj['JML_BOX']));
			$jmlbox += $sj['JML_BOX'];
		}
		$data['sj'] = $resumeSj;
		$tombolbapd = array(
			'D' => '<span class="btn btn-default" data-noreg="'.$noreg.'" data-status="D" onclick="BAPD.ubahbapddoc(this)">Revisi >></span>',
			'N' => '',
			'RV' => '',
			'A' =>  '<span class="btn btn-default" data-noreg="'.$noreg.'" data-status="A" onclick="BAPD.cetakbapddoc(this)">Cetak BAPD >></span>',
			'RJ' => '<span class="btn btn-default" data-noreg="'.$noreg.'" data-status="RJ" onclick="BAPD.ubahbapddoc(this)">Revisi >></span>'
		);
		$d_performance = array(
			'jmlbox' => $jmlbox,
			'jmlekor' => $jmlbox * 102,
			'stokawal' => $performance['STOK_AWAL'],
			'jmlafkir' =>  $performance['JML_AFKIR'],
			'bbrata2' =>  $performance['BB_RATA2'],
			'uniformity' =>  $performance['UNIFORMITY'],
			'tombolbapd' => $access == 'create' ? $tombolbapd[$performance['STATUS']] : ''
		);
		$data['noreg'] = $noreg;
		$data['status'] = $performance['STATUS'];
		$data['performance'] = $this->load->view('penerimaan_docin/'.$this->grup_farm.'/performancedocin',$d_performance,true);
		$data['riwayat'] = $this->bapd->riwayatbap($noreg)->result_array();
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/detail_bapd',$data);
	}

	public function detailsj(){
		$noreg = $this->input->get('noreg');
		$this->load->model('penerimaan_docin/m_bapdocsj', 'sj');

		$user_level = $this->session->userdata('level_user');
		$data['sj'] = $this->sj->as_array()->get_many_by(array('no_reg' => $noreg));
		
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/detail_sj',$data);
	}

	public function riwayat(){
		$noreg = $this->input->get('noreg');
		$data['riwayat'] = $this->bapd->riwayatbap($noreg)->result_array();
		$data['noreg'] = $noreg;
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/riwayat',$data);
	}

	public function form_bapd(){
		/* cari kandang yang aktif saja */
		$this->load->model('forecast/m_kandang_siklus','ks');
		$this->load->model('penerimaan_docin/m_hatchery','mh');
		$kodefarm = $this->session->userdata('kode_farm');
		$sudah_ada = $this->db->distinct()->select('lbd.no_reg')->join('kandang_siklus ks','ks.no_reg = lbd.no_reg')->where(array('ks.status_siklus' => 'O', 'ks.kode_farm' =>$kodefarm))->get_compiled_select('log_bap_doc lbd');
		$kandangBelumInput = $this->db->select('no_reg')->where('no_reg not in ('.$sudah_ada.')')->where(array('status_siklus' => 'O', 'kode_farm' =>$kodefarm))->get_compiled_select('kandang_siklus');
		$data['list_kandang'] = $this->ks->as_array()->get_many_by('no_reg in ('.$kandangBelumInput.')');
		$data['list_hatchery'] = $this->mh->as_array()->get_all();
		$data['revisi'] = 0;
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/form_bapd',$data);
	}

	public function ubahbapdoc(){
		/* cari kandang yang aktif saja */
		$this->load->model('forecast/m_kandang_siklus','ks');
		$this->load->model('penerimaan_docin/m_hatchery','mh');
		$noreg = $this->input->get('noreg');
		$this->load->model('penerimaan_docin/m_bapdoc', 'bd');
		$this->load->model('penerimaan_docin/m_bapdocbox', 'bdb');
//		$this->load->model('penerimaan_docin/m_logbapdoc', 'lbd');
//		$ddata['sj'] = $this->bapd->list_sj($noreg)->result_array();
		$allsj = $this->bdb->as_array()->get_many($noreg);
		$performance = $this->bd->as_array()->get($noreg);
		$jmlbox = 0;
		$resumeSj = array();
		foreach ($allsj as $sj) {
			$nosj = $sj['NO_SJ'];
			if(!isset($resumeSj[$nosj])){
				$resumeSj[$nosj] = array('no_sj' => $sj['NO_SJ'],'jmlbox' => 0, 'tgl_terima' => $sj['TGL_TERIMA'], 'no_reg' => $sj['NO_REG'], 'list_sj' => array());
			}
			$resumeSj[$nosj]['jmlbox'] += $sj['JML_BOX'];
			array_push($resumeSj[$nosj]['list_sj'], array('kodebox' =>$sj['KODE_BOX'], 'jml' => $sj['JML_BOX']));
			$jmlbox += $sj['JML_BOX'];
		}
		$ddata['sj'] = $resumeSj;

		$d_performance = array(
			'jmlbox' => $jmlbox,
			'jmlekor' => $jmlbox * 102,
			'stokawal' => $performance['STOK_AWAL'],
			'jmlafkir' =>  $performance['JML_AFKIR'],
			'bbrata2' =>  $performance['BB_RATA2'],
			'uniformity' =>  $performance['UNIFORMITY'],
			'tombolbapd' => ''
		);

		$ddata['noreg'] = $noreg;
		$ddata['status'] = $performance['STATUS'];
		$ddata['performance'] = $this->load->view('penerimaan_docin/'.$this->grup_farm.'/performancedocin',$d_performance,true);
		$ddata['showheader'] = 1;
		$data['no_reg'] = $noreg;
		$data['tgl_doc_in'] = tglIndonesia($performance['TGL_DOC_IN'],'-',' ');
		$data['list_kandang'] = $this->ks->as_array()->get_many_by(array('status_siklus' => 'O','no_reg' => $noreg));
		$data['list_hatchery'] = $this->mh->as_array()->get_all();
		$data['detail_bap'] = $this->load->view('penerimaan_docin/'.$this->grup_farm.'/detail_bapd',$ddata,true);
		$data['kode_hatchery'] = $performance['KODE_HATCHERY'];
		$data['ubahbap'] = 1;
		$data['revisi'] = 1;
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/form_bapd',$data);
	}

	public function list_suratjalan(){
		$data['list_suratjalan'] = array();
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/list_suratjalan',$data);
	}

	public function performancedocin(){
		$perbox = 102;
		$data['jmlbox'] = $this->input->get('jmlbox');
		$data['jmlekor'] = $data['jmlbox'] * $perbox;
		$data['performancedocin'] = array();
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/performancedocin',$data);
	}

	public function simpanbapd(){
		$bapddoc = $this->input->post('bapddoc');
		$bapddocbox = $this->input->post('bapddocbox');
		$status = $bapddoc['status'];
		$noreg =  $bapddoc['no_reg'];
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$kodekandang = substr($noreg,-2);
		$this->load->model('penerimaan_docin/m_bapdoc', 'bd');
		$this->load->model('penerimaan_docin/m_bapdocbox', 'bdb');
		$this->load->model('penerimaan_docin/m_logbapdoc', 'lbd');
		$message = array(
			'D' => 'disimpan sebagai draft',
			'N' => 'dirilis'
		);
		$this->db->trans_begin();
		/* remove tabel bap_doc dan bap_doc_box setiap kali ada proses penyimpanan */
		$sudahada = count($this->bd->get($noreg));
		if($sudahada){
			$message['D'] = ' diupdate';
			$this->bdb->delete_by(array('no_reg' => $noreg));
			$this->bd->update($noreg,$bapddoc);
		}else{
			$this->bd->insert($bapddoc);
		}
		foreach($bapddocbox as $bx){
			$this->bdb->insert($bx);
		}
		$this->lbd->insert(array(
			'no_reg' => $noreg,
			'status' => $status,
			'tgl_buat' => $tglserver,
			'user_buat' => $this->_user,
		));
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->result['message'] = 'BAP Doc untuk Kandang '.$kodekandang.' (no. reg '.$noreg.') gagal disimpan';
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = 'BAP Doc untuk Kandang '.$kodekandang.' (no. reg '.$noreg.') berhasil '.$message[$status];
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function rilisbapd(){
		$bapddoc = $this->input->post('bapddoc');
		$status = $bapddoc['status'];
		$noreg =  $bapddoc['no_reg'];
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$kodekandang = substr($noreg,-2);
		$this->load->model('penerimaan_docin/m_bapdoc', 'bd');
		$this->load->model('penerimaan_docin/m_bapdocbox', 'bdb');
		$this->load->model('penerimaan_docin/m_logbapdoc', 'lbd');
		$bapddocbox = isset($_POST['bapddocbox']) ? $this->input->post('bapddocbox') : '';
		$message = array(
			'D' => 'disimpan sebagai draft',
			'N' => 'dirilis'
		);
		$this->db->trans_begin();
		/* remove tabel bap_doc dan bap_doc_box setiap kali ada proses penyimpanan */
		$this->bd->update($noreg,$bapddoc);
		$this->lbd->insert(array(
			'no_reg' => $noreg,
			'status' => $status,
			'tgl_buat' => $tglserver,
			'user_buat' => $this->_user,
		));
		if($status == 'N'){
			if($bapddocbox != ''){
				$this->bdb->delete_by(array('no_reg' => $noreg));
				foreach($bapddocbox as $bx){
					$this->bdb->insert($bx);
				}
			}
		}
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->result['message'] = 'BAP Doc untuk Kandang '.$kodekandang.' (no. reg '.$noreg.') gagal disimpan';
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = 'BAP Doc untuk Kandang '.$kodekandang.' (no. reg '.$noreg.') berhasil '.$message[$status];
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function rilisbapdsj(){
		$bapddoc = $this->input->post('data');
		$noreg =  $bapddoc['no_reg'];
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$this->load->model('penerimaan_docin/m_bapdoc', 'bd');
		$this->load->model('penerimaan_docin/m_bapdocsj', 'bds');
		$this->load->model('penerimaan_docin/m_logbapdoc', 'lbd');
		
		$this->db->trans_begin();
		/* remove tabel bap_doc dan bap_doc_box setiap kali ada proses penyimpanan */
		$ada = $this->bd->get($noreg);
		$bapddoc['status'] = 'N';
		$bapddoc['bb_rata2'] = $bapddoc['bb_rata2'] * 1000;
		if($ada){
			$this->bd->update($noreg,$bapddoc);
		}else{
			$hatchery = $this->bds->as_array()->get_by(array('no_reg' => $noreg));
			$bapddoc['kode_hatchery'] = $hatchery['KODE_HATCHERY'];
			$this->bd->insert($bapddoc);
		}
		$this->bd->update($noreg,$bapddoc);
		$this->lbd->insert(array(
			'no_reg' => $noreg,
			'status' => 'N',
			'tgl_buat' => $tglserver,
			'user_buat' => $this->_user,
		));
		$kodekandang = substr($noreg,-2);
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->result['message'] = 'BAP Doc untuk Kandang '.$kodekandang.' (no. reg '.$noreg.') gagal disimpan';
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = 'BAPD Kandang '.$kodekandang.' berhasil dirilis';
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function ackapprove(){
		$noregs = $this->input->post('noreg');
		$nextstatus = 'A';
		$this->load->model('penerimaan_docin/m_bapdoc', 'bd');
		$this->load->model('penerimaan_docin/m_logbapdoc', 'lbd');
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$this->db->trans_begin();
		foreach($noregs as $noreg){
			$this->bd->update($noreg,array('status'=>$nextstatus));
			$this->lbd->insert(array(
				'no_reg' => $noreg,
				'status' => $nextstatus,
				'tgl_buat' => $tglserver,
				'user_buat' => $this->_user,
			));
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->result['message'] = 'BAP Doc gagal disimpan';
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['content'] = array('tgl_buat' => convertElemenTglWaktuIndonesia($tglserver), 'status' => convertKode('berita_acara',$nextstatus));
			$this->result['message'] = 'BAP Doc berhasil diapprove';
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function reject(){
		$noregs = $this->input->post('noreg');
		$nextstatus = $this->input->post('nextstatus');
		$keterangan = $this->input->post('keterangan');
		$this->load->model('penerimaan_docin/m_bapdoc', 'bd');
		$this->load->model('penerimaan_docin/m_logbapdoc', 'lbd');
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$this->db->trans_begin();
		foreach($noregs as $noreg){
			$this->bd->update($noreg,array('status'=>$nextstatus));
			$this->lbd->insert(array(
				'no_reg' => $noreg,
				'status' => $nextstatus,
				'tgl_buat' => $tglserver,
				'keterangan' => $keterangan,
				'user_buat' => $this->_user,
			));
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->result['message'] = 'BAP Doc gagal direject';
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['content'] = array('tgl_buat' => convertElemenTglWaktuIndonesia($tglserver), 'status' => convertKode('berita_acara',$nextstatus));
			$this->result['message'] = 'BAP Doc berhasil direject';
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	function cetakbapd(){
		$noreg = $this->input->get('noreg');
		$hatchery = $this->input->get('hatchery');
		$this->load->model('penerimaan_docin/m_bapdoc', 'bd');
		$this->load->model('penerimaan_docin/m_bapdocbox', 'bdb');

		$allsj = $this->bdb->as_array()->get_many($noreg);
		$performance = $this->bd->as_array()->get($noreg);
		/* dapatkan jmlbox */
		$jmlbox = 0;
		$resumeSj = array();

		foreach ($allsj as $sj) {
			$nosj = $sj['NO_SJ'];
			if(!isset($resumeSj[$nosj])){
				$resumeSj[$nosj] = array('no_sj' => $sj['NO_SJ'],'jmlbox' => 0, 'tgl_terima' => $sj['TGL_TERIMA'], 'no_reg' => $sj['NO_REG']);
				$tgl_terima = $sj['TGL_TERIMA'];
			}
			$resumeSj[$nosj]['jmlbox'] += $sj['JML_BOX'];
			$jmlbox += $sj['JML_BOX'];

		}

		$d_performance = array(
			'jmlbox' => $jmlbox,
			'jmlekor' => $jmlbox * 102,
			'stokawal' => $performance['STOK_AWAL'],
			'jmlafkir' =>  $performance['JML_AFKIR'],
			'bbrata2' =>  $performance['BB_RATA2'],
			'uniformity' =>  $performance['UNIFORMITY']
		);
		$kodefarm = $this->session->userdata('kode_farm');
		$namafarm = $this->db->select('nama_farm')->where(array('kode_farm' => $kodefarm))->get('m_farm')->row_array();
		$data['nama_farm'] = $namafarm['nama_farm'];
		$data['tgl_terima'] = convertElemenTglIndonesia($tgl_terima,'-',' ');
		$data['sj'] = $resumeSj;
		$data['noreg'] = $noreg;
		$data['hatchery'] = $hatchery;
		$hari_ini = date('Y-m-d');
		$data['index_hari'] = date('w',strtotime($hari_ini));
		$data['tgl_docin'] = tglIndonesia($performance['TGL_DOC_IN'],'-',' ');
		$data['hari_ini'] = tglIndonesia($hari_ini,'-',' ');
		$data['performance'] = $d_performance;
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/cetakbapd',$data);

	}

	function resumebapd(){
		$where = $this->input->post('where');
		$data['rbapd'] = $this->bapd->resumebapd($where)->result_array();
		$this->load->view('penerimaan_docin/'.$this->grup_farm.'/resumebapd',$data);
	}
}
