<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Forecast extends MY_Controller{
	protected $result;
	protected $_user;
	protected $_nama_user;
	protected $grup_farm;
	private $autoApprove = 1;
	private $_canSetFlock = array();
	private $_canSetACK = array();
	public function __construct(){
		parent::__construct();
		$this->result = array('status' => 0, 'content'=> '', 'message' => '');
		$this->load->model('forecast/m_forecast','mf');
		$this->load->helper('stpakan');
		$this->_user = $this->session->userdata('kode_user');
		$this->_nama_user = $this->session->userdata('nama_user');
		$this->_canSetFlock = array('KF');
		$this->_canSetACK = array('PPC');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
	}
	public function index(){

	}
	public function main($farm = NULL){
		$user_level = $this->session->userdata('level_user');
		//echo $user_level;
		switch ($user_level){
			case 'KF' :
				$this->kepalafarm();
				break;
			case 'KA' :
				$this->kabagadmin();
				break;
			case 'KD' :
				$this->kadept();
				break;
			case 'KDV' :
				$this->kadiv($farm);
				break;
			case 'DB' :
				$this->direktur();
				break;
			case 'KDB' :
				$this->kepalafarm();
				break;
		}
	}
	public function kepalafarm($farm = NULL){
		$kode_farm = (!empty($farm)) ? $farm : $this->session->userdata('kode_farm');
		$user_level = $this->session->userdata('level_user');
		if(in_array($user_level,$this->_canSetFlock)){
			$data['flock'] = true;
		}
		else{
			$data['flock'] = false;
		}
		switch($this->grup_farm){
			case 'bdy':
				$data['kandang_pending'] = '';
				$data['resume_siklus'] = $this->datafarm_bdy($kode_farm,$this->grup_farm);
				break;
			default:
		}
		$data['list_farm'] = $this->list_farm($this->grup_farm,$kode_farm,false);

		$data['view_flock'] = $this->load->view('flock',array('data_flock'=> ''),true);
		$view_url = 'forecast/'.$this->grup_farm.'/forecast';
		$this->load->view($view_url,$data);
	}

	public function kadiv($farm = NULL){
		$data['flock'] = false;
		//cetak_r($farm);
		$data_approve['list_farm'] = $this->list_farm($this->grup_farm,$farm,false);
		$data_approve['farm'] = $farm;
		$bisa_konfirmasi = 0;
		$kandang_pending = $this->load->view('forecast/'.$this->grup_farm.'/approvaldocin',$data_approve,true);
		$data['kandang_pending'] = $this->kandang_pending($bisa_konfirmasi);
		$data['approval_siklus'] = $kandang_pending;//$this->datafarm_bdy(NULL,$this->grup_farm);
		$data['resume_siklus'] = $this->datafarm_bdy(NULL,$this->grup_farm);
		$data['approval_aktivasi_siklus'] = 1;
		$data['breadcomb'] = 'hide';
		$view_url = 'forecast/'.$this->grup_farm.'/forecast';


		$this->load->view($view_url,$data);
	}

	public function kabagadmin(){
		$data['flock'] = false;
		$data['list_farm'] = $this->list_farm($this->grup_farm,NULL,false);
		$bisa_konfirmasi = 1;
		$data['kandang_pending'] = $this->kandang_pending($bisa_konfirmasi);
		$data['resume_siklus'] = $this->datafarm_bdy(NULL,$this->grup_farm);
		$view_url = 'forecast/'.$this->grup_farm.'/forecast';

		$this->load->view($view_url,$data);
	}

	public function kadept(){
		$data['flock'] = false;
		$data['list_farm'] = $this->list_farm($this->grup_farm,NULL,false);
		$bisa_konfirmasi = 0;
		$data['kandang_pending'] = $this->kandang_pending($bisa_konfirmasi);
		$data['resume_siklus'] = $this->datafarm_bdy(NULL,$this->grup_farm);
		$view_url = 'forecast/'.$this->grup_farm.'/forecast';
		$this->load->view($view_url,$data);
	}
	public function direktur(){
		$data['flock'] = false;
		$data['list_farm'] = $this->list_farm($this->grup_farm);
		$view_url = 'forecast/'.$this->grup_farm.'/forecast';
		$this->load->view($view_url,$data);
	}
	public function presdir(){
		$data['flock'] = false;
		$data['list_farm'] = $this->list_farm($this->grup_farm);

		$this->load->view('forecast/forecast_presdir',$data);
	}

	public function ppic(){
		$user_level = $this->session->userdata('level_user');
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
		$data['flock'] = false;
		$data['hide'] = '';
		$data['list_farm'] = $this->list_farm();
		$this->load->view('forecast/forecast_ppic',$data);
	}
	public function list_farm($grup_farm = NULL, $id = null, $active = true){
		$arr = $this->mf->list_farm($grup_farm,$id,$active)->result_array();
		$s = '<select class="form-control">';
		$s .= '<option value="">Pilih Farm</option>';
		$selected = '';
		$farm_unique = array();
		foreach($arr as $val){
			if(!empty($id)){
				if($id == $val['kode_farm']){
					$selected = 'selected = "selected"';
				}
				else{
					$selected = '';
				}
			}
			if($active){
					$s .= '<option  data-jmlflok="'.$val['jml_flok'].'" data-kode_siklus="'.$val['kode_siklus'].'" value="'.$val['kode_farm'].'" '.$selected.'>'.$val['nama_farm'].' ('.$val['kode_strain'].')</option>';
			}
			else{
				if(!in_array($val['kode_farm'],$farm_unique)){
						$s .= '<option  data-jmlflok="'.$val['jml_flok'].'" value="'.$val['kode_farm'].'" '.$selected.'>'.$val['nama_farm'].' ('.$val['kode_strain'].')</option>';
						array_push($farm_unique,$val['kode_farm']);
				}
			}

		}
		$s .= '</select>';
		return $s;
	}
	public function master_farm($id,$active = true){
		$arr = $this->mf->list_farm($this->grup_farm,$id,$active)->row();

		echo json_encode($arr);
	}

	public function datafarm($idfarm){
		$gf = $this->grup_farm;
		switch($gf){
			case 'bdy':
				$this->datafarm_bdy($idfarm,$gf);
				break;
			case 'brd':
				$this->datafarm_brd($idfarm,$gf);
				break;
		}
	}

	public function datafarm_brd($idfarm,$gf){
		$user_level = $this->session->userdata('level_user');
		$canCreate = array('KF');
		$canApprove = array('DB');
		$canEditPakan = array();
		$lockEditDocIn = array(
			'KF' => 'Baru,Acc1,Acc2',
			'AB' => 'Draft,Baru,Acc1,Acc2,*',
			'KDB' => 'Draft,Baru,Acc1,Acc2,*',
			'DB' => 'Acc1,Acc2',
		);

		$currentYear = date('Y');
		$nextYear = $currentYear + 1;
		$showYear = array($currentYear , $nextYear);
		$status_minimum = array('KDB' => 'A', 'DB' => 'R','PD' => 'A', 'KF' => null,'AB' => null);

		$arrTmp = $this->mf->list_kandang_open($idfarm,$status_minimum[$user_level])->result_array();

		$tahun = $bulan = $tgl = $_tmp = '';
		$arr = array();
		foreach($arrTmp as $row){
			$_tmp = explode('-',$row['tgl_chickin']);
			$tahun = $_tmp[0];
			$bulan = convert_ke_bulan($_tmp[1]);
			$tgl = $_tmp[2];
			if(!isset($arr[$tahun])){
				$arr[$tahun] = array();
			}
			if(!isset($arr[$tahun][$bulan])){
				$arr[$tahun][$bulan] = array();
			}
			if(!isset($arr[$tahun][$bulan][$tgl])){
				$arr[$tahun][$bulan][$tgl] = array();
			}
			$text_kandang = 'Kandang '.$row['kode_kandang'].' (J : '.$row['jantan'].', B : '.$row['betina'].' )#'.$row['kode_farm'].'/'.$row['kode_kandang'].'/'.$row['tipe_lantai'].'/'.$row['tipe_kandang'].'/'.$row['kapasitas'].'/'.$row['jantan'].'/'.$row['betina'].'#'.$row['status_approve'].'#'.$row['no_reg'];
			array_push($arr[$tahun][$bulan][$tgl],$text_kandang);
		}
		foreach($showYear as $year){
			if(!isset($arr[$year])) $arr[$year] = array();
		}
		$header = array('Kandang','Lantai','Tipe','Kapasitas','Jantan','Betina');

		$kandang = $this->mf->list_kandang_tutup_siklus($idfarm)->result_array();


		$tombol_simpan = '';
		if(!in_array($user_level,$canCreate)){
			array_pop($header);
			array_pop($header);

			foreach($kandang as $i => &$row){
				array_pop($row);
				array_pop($row);
			}
			if(in_array($user_level,$canApprove)){
				$tombol_simpan = '<div class="btn btn-default" data-aksi="approve">Approve</div>';
			}
		}
		else{
			$tombol_simpan = '<div class="btn btn-default" data-aksi="simpan">Simpan</div>&nbsp;<div class="btn btn-default" data-aksi="rilis">Rilis</div>';
		}

		if(in_array($user_level,$canEditPakan)){
			if(in_array($user_level,$canCreate)){
				$data['lockEditPakan'] = 'Acc1,Acc2,Baru';
			}
			else{
				$data['lockEditPakan'] = 'Acc1,Acc2';
			}
		}
		else{
			$data['lockEditPakan'] = 'Acc1,Acc2,Baru,Draft';
		}

		$data['kandang_tutup_siklus'] = create_table_div($header,$kandang);
		$data['tree'] = create_tree($arr);
		$data['lockEditDocIn'] = $lockEditDocIn[$user_level];
		$data['canCreateForecast'] = (in_array($user_level,$canCreate)) ? 1 : 0;
		$data['div_tombol_simpan'] = $tombol_simpan;
		$this->load->view('forecast/'.$gf.'/farm',$data);
	}

	public function datafarm_bdy($idfarm,$gf){
		$user_level = $this->session->userdata('level_user');
		$canCreate = array('KF');
		$canApprove = array('DB');
		$canEditPakan = array();

		$lockEditDocIn = array(
				'KDV' => '0',
				'KF' => '1',
				'KD' => '1',
				'KA' => '1',
				'KDB'=>'1'
		);
		$status_minimum = array('KDB' => 'A', 'DB' => 'R','PD' => 'A', 'KF' => null,'AB' => null,'KD' => null, 'KDV'=> null, 'KA'=> null);

		$arrTmp = $this->mf->list_kandang_open_bdy($idfarm,$status_minimum[$user_level])->result_array();

		$farm = $tahun = $bulan = $tgl = $_tmp = '';
		$arr = array();
		foreach($arrTmp as $row){
			$_tmp = explode('-',$row['tgl_chickin']);
			$farm = $row['nama_farm'];
			$tahun = $_tmp[0];
			$bulan = convert_ke_bulan($_tmp[1]);
			$tgl = $_tmp[2];
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
			$text_kandang = 'Kandang '.$row['kode_kandang'].' ('.$row['populasi'].')#'.$row['kode_farm'].'/'.$row['kode_kandang'].'/'.$row['tipe_lantai'].'/'.$row['tipe_kandang'].'/'.$row['kapasitas'].'/'.$row['populasi'].'#'.$row['status_approve'].'#'.$row['no_reg'];
			array_push($arr[$farm][$tahun][$bulan][$tgl],$text_kandang);
		}

		if(in_array($user_level,$canEditPakan)){
			if(in_array($user_level,$canCreate)){
				$data['lockEditPakan'] = 'Acc1,Acc2,Baru';
			}
			else{
				$data['lockEditPakan'] = 'Acc1,Acc2';
			}
		}
		else{
			$data['lockEditPakan'] = 'Acc1,Acc2,Baru,Draft';
		}
		$standard_baru = $this->mf->check_standart_baru($idfarm)->row_array();
		$data['tree'] = create_tree($arr);
		$data['lockEditDocIn'] = $lockEditDocIn[$user_level];
		$data['canCreateForecast'] = (in_array($user_level,$canCreate)) ? 1 : 0;
		$data['minTglDocInStandartBaru'] = (!empty($standard_baru)) ? $standard_baru['tgl_doc_in'] : '';
	//	$data['div_tombol_simpan'] = $tombol_simpan;
		$html = $this->load->view('forecast/'.$gf.'/farm',$data,true);
		return $html;
	}
	private function groupingOpen($arr){
		$result = array();
		if(!empty($arr)){
			foreach($arr as $r){
				$kode_farm = $r['kode_farm'];
				$kode_siklus = $r['kode_siklus'];
				if(!isset($result[$kode_farm])){
					$result[$kode_farm] = array();
				}
				if(empty($result[$kode_farm])){
					$result[$kode_farm] = $kode_siklus;
				}
				
			}
		}
		return $result;
	}
	private function kandang_pending($bisa_konfirmasi = 1){
		$tglServer = Modules::run('home/home/getDateServer');
		$tglSekarang = $tglServer->saatini;
		$user_level = $this->session->userdata('level_user');
		$lockEditDocIn = array(
				'KDV' => '0',
				'KF' => '1',
				'KD' => '1',
				'KA' => '1',
				'KDB' => '1',
		);
		$minimum_approve = array(
			'KDB' => array('P1'),
			'KD' => array('P1'),
			'KA' => array('RJ','1'),
			'KDV' => array('RL')
		);

		$arrTmp = $this->mf->list_kandang_pending()->result_array();
		$farm = $tahun = $bulan = $tgl = $_tmp = '';
		$arr = array();
		/* jika masih ada kandang yang masih aktif pada periode siklus sebelumnya maka tidak bisa konfirmasi */
		$masihOpen = $this->db->query('select distinct kode_siklus,kode_farm from kandang_siklus where STATUS_SIKLUS = \'O\' and FLOK_BDY is not null order by kode_siklus')->result_array();
		$masihOpenArr = $this->groupingOpen($masihOpen);
		//log_message('error',json_encode($masihOpenArr));
		$sudahDipilih = array();

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
			if($row['tgl_chickin'] > $tglSekarang){
				if($bisa_konfirmasi){
					if(!isset($sudahDipilih[$kode_farm])){
						$sudahDipilih[$kode_farm] = array();
					}

					if(!isset($sudahDipilih[$kode_farm][$row['kode_kandang']])){
						$sudahDipilih[$kode_farm][$row['kode_kandang']] = $row['tgl_chickin'];
						$bisaDipilih = $state == 'RJ' ? $state : 1;

						/* periksa bila kode_siklus > dari yang masih open maka rubah menjadi 0*/
						if(isset($masihOpenArr[$kode_farm])){
							if($row['kode_siklus'] > $masihOpenArr[$kode_farm]){
								$bisaDipilih = 0;
							}
						}
					}
				}
			}

			$text_kandang = 'Kandang '.$row['kode_kandang'].' ('.$row['populasi'].' ekor)#'.$kode_farm.'/'.$row['kode_kandang'].'/'.$row['tipe_lantai'].'/'.$row['tipe_kandang'].'/'.$row['kapasitas'].'/'.$row['populasi'].'#'.$bisaDipilih.'#'.$row['no_reg'];
			array_push($arr[$farm][$tahun][$bulan][$tgl],$text_kandang);
		}

		$data['ganti_info'] = $user_level == 'KDV' ? 1 : 0;
		$data['bisa_konfirmasi'] = $bisa_konfirmasi;
		$data['minimum_approve'] = implode(',',$minimum_approve[$user_level]);
		$data['tree'] = create_tree($arr);
		$data['kandang_konfirmasi'] = ($user_level == 'KDV') ? '' : $this->kandang_konfirmasi();
		$data['lockEditDocIn'] = $lockEditDocIn[$user_level];
		$html = $this->load->view('forecast/bdy/kandang_pending',$data,true);
		return $html;
	}

	private function kandang_konfirmasi(){
		$user_level = $this->session->userdata('level_user');
		$arrTmp = $this->mf->list_kandang_konfirmasi()->result_array();
		$farm = $tahun = $bulan = $tgl = $_tmp = '';
		$arr = array();
		foreach($arrTmp as $row){
			$_tmp = explode('-',$row['tgl_chickin']);
			$farm = $row['nama_farm'];
			$kode_farm = $row['kode_farm'];
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

			$text_kandang = 'Kandang '.$row['kode_kandang'].' ('.$row['populasi'].' ekor)#'.$kode_farm.'/'.$row['kode_kandang'].'/'.$row['tipe_lantai'].'/'.$row['tipe_kandang'].'/'.$row['kapasitas'].'/'.$row['populasi'].'#'.$row['state'].'#'.$row['no_reg'];
			array_push($arr[$farm][$tahun][$bulan][$tgl],$text_kandang);
		}

		$html = create_tree($arr);
	//	$data['div_tombol_simpan'] = $tombol_simpan;
		return $html;
	}

	public function get_standart_budidaya($gf){
		$strain = $this->input->post('strain');
		$tipe_kandang = $this->input->post('tipe_kandang');
		$tglDocIn = $this->input->post('tglDocIn');
		switch($gf){
			case 'brd':
				$musim = get_musim($tglDocIn);
				$standart_j = $this->mf->get_standart_pakan($strain,$tipe_kandang,$musim,'J',$tglDocIn)->result_array();
				$standart_b = $this->mf->get_standart_pakan($strain,$tipe_kandang,$musim,'B',$tglDocIn)->result_array();
				$arr = array('j' => array(), 'b' => array());
				break;
			case 'bdy':
				$kodeFarm = $this->input->post('kodeFarm');
				$standart_j = $this->mf->get_standart_pakan_bdy($tglDocIn,$kodeFarm)->result_array();
				$standart_b = array();
				$arr = array('j' => array());
				break;
		}

		if(!empty($standart_j)){
			foreach($standart_j as $row){
				$arr[$row['jenis_kelamin']][$row['umur']]=$row;
			}
		}

		if(!empty($standart_b)){
			foreach($standart_b as $row){
				$arr[$row['jenis_kelamin']][$row['umur']]=$row;
			}
		}

		return $arr;
	}

	public function standart_budidaya(){
		$arr = $this->get_standart_budidaya('brd');
		echo json_encode($arr);
	}

	public function standart_budidaya_bdy(){
		$arr = $this->get_standart_budidaya('bdy');
		echo json_encode($arr);
	}

	public function cetak_breakdown_pakan(){
		$data['nama_farm'] = $this->input->post('nama_farm');
		$data['nama_kandang'] = $this->input->post('nama_kandang');
		$data['tipe_kandang'] = $this->input->post('tipe_kandang');
		$data['kapasitas'] = $this->input->post('kapasitas');
		$data['jantan'] = $this->input->post('jantan');
		$data['betina'] = $this->input->post('betina');
		$data['data_html'] = $this->input->post('data_html');

        $this->load->library('Pdf');
        $pdf = new Pdf('P', PDF_UNIT, 'A5', true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $html = $this->load->view('forecast/breakdown_pakan_pdf', $data, true);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('breakdown_pakan.pdf', 'I');
	}

	public function cetak_konfirmasi_forecast(){
		$data['nama_farm'] = $this->input->post('nama_farm');
		$data['no_permintaan'] = $this->input->post('no_permintaan');
		$data['data_html'] = $this->input->post('data_html');

        $this->load->library('Pdf');
        $pdf = new Pdf('L', PDF_UNIT, 'A5', true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $html = $this->load->view('forecast/detail_konfirmasi_forecast_pdf', $data, true);

        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('detail_konfirmasi_forecast.pdf', 'I');
	}

	public function get_pakan(){
		$group_pakan = $this->input->post('group_pakan');
		$group = $this->mf->get_master_pakan($group_pakan)->result_array();

		$_tmp = array();
		foreach($group as $i => $val){
			if(!isset($_tmp[$val['grup_barang']])){
				$_tmp[$val['grup_barang']] = array();
			}
			if(!isset($_tmp[$val['grup_barang']][$val['bentuk_barang']])){
				$_tmp[$val['grup_barang']][$val['bentuk_barang']] = array();
			}
			array_push($_tmp[$val['grup_barang']][$val['bentuk_barang']],array('kodepj' => $val['kodepj'],'namapj' => $val['namapj']));
		}
		$tmp = array();
		foreach($group_pakan as $i => $val){
			if(isset($_tmp[$val['group']][$val['bentuk']])){
				$tmp[$val['group']][$val['bentuk']] = $_tmp[$val['group']][$val['bentuk']];
			}
		}
		echo json_encode($tmp);
	}

	public function flock(){
		$filterFlok = $this->input->post('filterFlok');
		$startDate = $this->input->post('startDate');
		$endDate = $this->input->post('endDate');
		$data['header'] = array('Tanggal DOC-In','Kandang','Tipe Kandang','Flock','Tanggal Tetas','Lantai','Kapasitas (ekor)','Jantan (ekor)','Betina (ekor)');
		$cari = array(
			'ks.kode_flok' => ($filterFlok) ? 'is null' : null ,
			'ks.TGL_DOC_IN'	=> array('>=' => $startDate,'<=' => $endDate)
		);
		$kode_farm = $this->session->userdata('kode_farm');
		$data['tbody'] = $this->mf->get_kandang_flock($kode_farm,$cari)->result_array();

		$this->load->view('forecast/tabel_flock',$data);
	}

	public function update_flok(){
		$tgldocin = $this->input->post('tgldocin');
		$tgltetas = $this->input->post('tgltetas');
		$namaflok = strtoupper($this->input->post('namaflok'));
		$kodeflok = $this->input->post('kodeflok');
		$noreg = $this->input->post('noreg');
		$kodefarm = $this->input->post('kodefarm');
		/*
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		*/
    	$this->load->model('forecast/m_kandang_siklus', 'ks');
    	$this->db->trans_begin();
    	if(empty($kodeflok)){
    		$this->load->model('forecast/m_flok','fk');
    		$validasi_flok = $this->fk->validasi_flok($namaflok);
    		if($validasi_flok == 0){
    			/* buat kode flok terlebih dahulu */
  //  			$this->load->model('forecast/m_flok','fk');
    			$arr = array(
    				'kode_farm' => $kodefarm,
    				'kode_pegawai' => $this->_user,
    				'nama_flok' => $namaflok,
    				'tgl_terima' => $tgldocin,
    				'tgl_tetas' => $tgltetas
    			);
    			$this->fk->insert($arr);
    			$kodeflok = $this->db->insert_id();
    			/* update kandang_siklus */
    			foreach($noreg as $n){
    				$this->ks->update_flok($n,$kodeflok);
    			}
    		}
    		else{
    			$this->result['message'] = 'Nama flok sudah ada';
    		}
    	}
    	else{
    		/* update kandang_siklus */
    		foreach($noreg as $n){
    			$this->ks->update_flok($n,$kodeflok);
    		}
    	}

    		if ($this->db->trans_status() === FALSE)
    		{
    			$this->db->trans_rollback();
    		}
    		else{
    			$this->db->trans_commit();
    			$this->result['status'] = 1;
    		}
        if(!empty($this->result['message'])){
        	$this->result['status'] = 0;
        }
		echo json_encode($this->result);
	}

	public function simpan(){
		$result = array('status' => 0);
		$insertKandang = $this->input->post('insertKandang');
		$updateKandang = $this->input->post('updateKandang');
		$pakanJantan = $this->input->post('pakanJantan');
		$pakanBetina = $this->input->post('pakanBetina');
		$dataFarm = $this->input->post('dataFarm');
		$docIn = $this->input->post('_docIn');
		$pakanJantanBerubah = $this->input->post('pakanJantanBerubah');
		$pakanBetinaBerubah = $this->input->post('pakanBetinaBerubah');

		$this->load->model('forecast/m_kandang_siklus', 'ks');
		$this->load->model('forecast/m_log_kandang_siklus', 'kls');
	//	$this->load->model('forecast/m_forecast_d', 'mfd');

		$this->db->trans_begin();

		if(!empty($insertKandang)){
			$this->ks->simpan($insertKandang,$dataFarm,$docIn);
			$this->kls->simpan($insertKandang,$dataFarm);
		//	$this->mf->simpan($insertKandang,$dataFarm);
		//	$this->mfd->simpan($insertKandang,$dataFarm,$pakanJantan,$pakanBetina);


		}

		if(!empty($updateKandang)){
			$noreg = array();
			foreach($updateKandang as $row){
				array_push($noreg,$row['no_reg']);

			}
			if(!empty($pakanJantanBerubah)){
			//	$this->mfd->update_pakan($updateKandang,$dataFarm,$pakanJantanBerubah,'j');
				$result['pakan_berubah'] = 0;
			}
			if(!empty($pakanBetinaBerubah)){
			//	$this->mfd->update_pakan($updateKandang,$dataFarm,$pakanBetinaBerubah,'b');
				$result['pakan_berubah'] = 0;
			}
			$this->ks->update_simpan($updateKandang,$docIn);
			$this->kls->approve($noreg,'D');
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();

		}
		else{
			$this->db->trans_commit();
			if(isset($result['pakan_berubah'])){
				$result['pakan_berubah'] = 1;
			}
			$result['status'] = 1;
		}
		echo json_encode($result);
	}

	public function rilis(){
		$result = array('status' => 0);
		$insertKandang = $this->input->post('insertKandang');
		$updateKandang = $this->input->post('updateKandang');
		$pakanJantan = $this->input->post('pakanJantan');
		$pakanBetina = $this->input->post('pakanBetina');
		$dataFarm = $this->input->post('dataFarm');
		$docIn = $this->input->post('_docIn');
		$pakanJantanBerubah = $this->input->post('pakanJantanBerubah');
		$pakanBetinaBerubah = $this->input->post('pakanBetinaBerubah');

		$this->load->model('forecast/m_kandang_siklus', 'ks');
		$this->load->model('forecast/m_log_kandang_siklus', 'kls');
	//	$this->load->model('forecast/m_forecast_d', 'mfd');
		$this->db->trans_begin();
		/* isi tabel sinkronisasi */
	//	$this->load->module('sinkronisasi/sinkronisasi','sinkronisasi');

		if(!empty($insertKandang)){
			$this->ks->simpan($insertKandang,$dataFarm,$docIn,'rilis');
			$this->kls->simpan($insertKandang,$dataFarm,1);

			/* isi tabel sinkronisasi
			foreach($insertKandang as $_r){
				$dataKey = array(':key2' => $_r['no_reg']);
				$this->sinkronisasi->insert('forecast_rilis',$dataKey);
			}
			*/
		}

		if(!empty($updateKandang)){
			$noreg = array();
			foreach($updateKandang as $row){
				array_push($noreg,$row['no_reg']);
			}
			if(!empty($pakanJantanBerubah)){
				$result['pakan_berubah'] = 0;
			}
			if(!empty($pakanBetinaBerubah)){
				$result['pakan_berubah'] = 0;
			}
			$this->ks->rilis($updateKandang,$docIn);
			$this->kls->approve($noreg,'R');


			/* isi tabel sinkronisasi
			foreach($noreg as $nr){
				$dataKey = array(':key2' => $nr);
				$this->sinkronisasi->insert('forecast_rilis',$dataKey);
			}
			*/
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
		//	$this->db->trans_rollback();
			if(isset($result['pakan_berubah'])){
				$result['pakan_berubah'] = 1;
			}
			$result['status'] = 1;
		}

		echo json_encode($result);
	}

	public function approve(){

		$dataKandang = $this->input->post('dataKandang');
		$docIn = $this->input->post('_docIn');
		$pakanJantanBerubah = $this->input->post('pakanJantanBerubah');
		$pakanBetinaBerubah = $this->input->post('pakanBetinaBerubah');
		$kodeFarm = $this->input->post('kodeFarm');
		/* kondisi ketika tidak ada perubahan data sama sekali */
		$this->load->model('forecast/m_kandang_siklus', 'ks');
		$this->load->model('forecast/m_log_kandang_siklus', 'kls');
	//	$this->load->model('forecast/m_forecast_d', 'mfd');

		/* isi tabel sinkronisasi */
	//	$this->load->module('sinkronisasi/sinkronisasi','sinkronisasi');

		$noreg = array();
		foreach($dataKandang as $row){
			array_push($noreg,$row['no_reg']);

		}
		$this->db->trans_begin();
		$this->ks->approve($dataKandang,$docIn);
		$this->kls->approve($noreg,'A');
		if(!empty($pakanJantanBerubah)){
		//	$this->mfd->update_pakan($dataKandang,$pakanJantanBerubah,'j');
			$result['pakan_berubah'] = 0;
		}
		if(!empty($pakanBetinaBerubah)){
		//	$this->mfd->update_pakan($dataKandang,$pakanBetinaBerubah,'b');
			$result['pakan_berubah'] = 0;
		}

		/* isi tabel sinkronisasi
		foreach($noreg as $nr){
			$dataKey = array(':key2' => $nr);
			$this->sinkronisasi->insert('forecast_approve',$dataKey,$kodeFarm);
		}
		*/

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			echo json_encode(array('status'=>0));
		}
		else{
			$this->db->trans_commit();
			$kode_farm = $this->session->userdata('kode_farm');
			echo json_encode(array('status'=>1,'kode_farm'=>$kode_farm));
		}

	}

	public function approve_presdir(){
		$no_reg = $this->input->post('no_reg');

		/* kondisi ketika tidak ada perubahan data sama sekali */
		$this->load->model('forecast/m_kandang_siklus', 'ks');
		$this->load->model('forecast/m_log_kandang_siklus', 'kls');
		$noreg = array($no_reg);

		$this->db->trans_begin();
		$this->ks->approve_presdir($no_reg);
		$this->kls->approve($noreg,'C');


		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			echo json_encode(array('status'=>0));
		}
		else{
			$this->db->trans_commit();
			echo json_encode(array('status'=>1));
		}
	}

	public function reject_presdir(){
		$no_reg = $this->input->post('no_reg');

		/* kondisi ketika tidak ada perubahan data sama sekali */
		$this->load->model('forecast/m_kandang_siklus', 'ks');
		$this->load->model('forecast/m_log_kandang_siklus', 'kls');
		$noreg = array($no_reg);

		$this->db->trans_begin();
		$this->ks->reject_presdir($no_reg);
		$this->kls->approve($noreg,'R');


		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			echo json_encode(array('status'=>0));
		}
		else{
			$this->db->trans_commit();
			echo json_encode(array('status'=>1));
		}
	}

	public function get_pakan_tersimpan(){
		$tglDocIn = $this->input->post('tglDocIn');
		$idFarm = $this->input->post('idFarm');
		$pakan = $this->mf->get_pakan_tersimpan($tglDocIn,$idFarm)->result_array();
		if(!empty($pakan)){
			$arr = array('j' => array(), 'b' => array());
			foreach($pakan as $row){
				$arr[$row['umur']] = $row;
			}
			$this->result['status'] = 1;
			$this->result['content'] = $arr;
		}
		echo json_encode($this->result);
	}

	public function list_farm_approval2(){
		$filter = $this->input->post('filter');
		$farm = $this->input->post('farm');
		$this->load->model('forecast/m_kandang_siklus', 'ks');
		$arr = $this->ks->list_farm_approval2($farm,$filter)->result_array();

		/* grouping berdasarkan bulan, pekan ke */
		$result = $header = array();
		$bulan = $pekan = '';
		if(!empty($arr)){
			foreach($arr as $row){
				$bulan = substr($row['tgl_doc_in'],0,7);
				$pekan = WeekSequenceInMonth($row['tgl_doc_in']);
				if(!isset($result[$bulan])){
					$result[$bulan] = array();
					$header[$bulan]=convert_ke_bulan(substr($bulan,5,2));
				}
				if(!isset($result[$bulan][$pekan])){
					$result[$bulan][$pekan] = array();
				}
				array_push($result[$bulan][$pekan],$row);
			}
		}
		$data['max_week'] = 5;

		$data['header'] = $header;
		$data['tbody'] = $result;
		$this->load->view('forecast/tabel_approval',$data);
	}

	public function kebutuhan_pakan_ppic(){
		$tanggal = $this->input->post('tanggal');
		$farm = $this->input->post('farm');

		$arr = $this->mf->kebutuhan_pakan_ppic($tanggal,implode(',',$farm));

		/* grouping berdasarkan pekan, tglkebutuhan dan pakan */
		$result = $header = $list_pekan = array();
		$pekan = $kodepj_b = $kodepj_j ='';
		if(!empty($arr)){
			foreach($arr as $row){
				$bulan = substr($row['tglkebutuhan'],0,7);
				$tglkebutuhan = $row['tglkebutuhan'];
				$kodepj_b = $row['b_barang'];
				$kodepj_j = $row['j_barang'];

				$pekan = WeekSequenceInMonth($row['tglkebutuhan']);
				if(!isset($header[$kodepj_b])){
					$header[$kodepj_b] = array('nama' => $row['b_namabarang'], 'bentuk' => $row['b_bentuk']);
				}
				if(!isset($header[$kodepj_j])){
					$header[$kodepj_j] = array('nama' => $row['j_namabarang'], 'bentuk' => $row['j_bentuk']);
				}
				if(!isset($result[$bulan])){
					$result[$bulan] = array();
				}
				if(!isset($result[$bulan][$pekan])){
					$result[$bulan][$pekan] = array();
					$list_pekan[$bulan][$pekan] = array('awal' => $tglkebutuhan, 'akhir' => $tglkebutuhan);
				}
				if(!isset($result[$bulan][$pekan][$tglkebutuhan][$kodepj_j])){
					$result[$bulan][$pekan][$tglkebutuhan][$kodepj_j]['jantan'] = array();
					$result[$bulan][$pekan][$tglkebutuhan][$kodepj_j]['betina'] = array();
				}
				if(!isset($result[$bulan][$pekan][$tglkebutuhan][$kodepj_b])){
					$result[$bulan][$pekan][$tglkebutuhan][$kodepj_b]['betina'] = array();
					$result[$bulan][$pekan][$tglkebutuhan][$kodepj_b]['jantan'] = array();
				}
				$list_pekan[$bulan][$pekan]['akhir'] = $tglkebutuhan;
				array_push($result[$bulan][$pekan][$tglkebutuhan][$kodepj_j]['jantan'],array('forecast_pakan' => $row['j_forecast_pakan'], 'kebutuhan_pakan' => $row['j_kebutuhan_pakan'] ,'pp' => $row['pp_jantan']));
				array_push($result[$bulan][$pekan][$tglkebutuhan][$kodepj_b]['betina'],array('forecast_pakan' => $row['b_forecast_pakan'], 'kebutuhan_pakan' => $row['b_kebutuhan_pakan'] ,'pp' => $row['pp_betina']));
			}
		}
		$data['rekap'] = $result;
		$data['header_pakan'] = $header;
		$data['list_pekan'] = $list_pekan;
		$this->load->view('forecast/kebutuhan_pakan_ppic',$data);
	}

	public function kebutuhan_pakan_ppic_bdy(){
		$ack = $this->input->post('ack');
		$nama_farm = $this->input->post('nama_farm');
		$docin = $this->input->post('docin');
		$populasi = $this->input->post('populasi');
		$arr = $this->mf->kebutuhan_pakan_ppic_bdy($ack);
		$data['nama_farm'] = $nama_farm;
		$data['docin'] = $docin;
		$data['populasi'] = $populasi;
		$data['ack'] = $arr;
		$data['grouping'] = $this->grouping_keb_pakan_ppic($arr);
		/* grouping berdasarkan pekan, tglkebutuhan dan pakan */
		$this->load->view('forecast/bdy/kebutuhan_pakan_ppic',$data);
	}
	/* grouping berdasarkan forecast,pp,master_barang per tanggal kirim*/
	private function grouping_keb_pakan_ppic($arr){
		$result = array('foreacst' => array(), 'pp' => array(),'mb' => array());
		foreach($arr as $r){
			$tglKirim = $r['tgl_kirim'];
			$tglKirimPP = $r['tgl_kirim_pp'];
			$namaBarang = $r['nama_barang'];
			$kodeBarang = $r['kode_barang'];
			$tglKebutuhan = $r['tgl_kebutuhan'];
			if(!isset($result['forecast'][$tglKebutuhan])){
				$result['forecast'][$tglKebutuhan] = array();
			}
			$result['forecast'][$tglKebutuhan][$kodeBarang] = $r['total_kebutuhan'];
			if(!empty($tglKirimPP)){
				if(!isset($result['pp'][$tglKebutuhan])){
					$result['pp'][$tglKebutuhan] = array();
				}
				if(!isset($result['pp'][$tglKirimPP][$kodeBarang])){
					$result['pp'][$tglKirimPP][$kodeBarang] = 0;
				}
				$result['pp'][$tglKirimPP][$kodeBarang] += $r['jml_order'];
			}
			if(!isset($result['mb'][$kodeBarang])){
				$result['mb'][$kodeBarang] = $namaBarang;
			}
		}

		return $result;
	}

	public function data_konfirmasi_ppic(){
		$tanggal = $this->input->post('tanggal');
		$farm = $this->input->post('farm');
		$konfirmasi = $this->input->post('konfirmasi');
		$sudah_konfirmasi = $this->input->post('sudah_konfirmasi');
		$grup_farm = $this->input->post('grup_farm');
        $user_level = $this->session->userdata('level_user');
        if(in_array($user_level,$this->_canSetACK)){
            $data['ack'] = true;
        }
        else{
            $data['ack'] = false;
        }

		#$data['konfirmasi_ppic'] = $this->mf->data_konfirmasi_ppic($tanggal,implode("','",$farm),$konfirmasi);
		$data['konfirmasi_ppic'] = ($grup_farm == 'bdy') ? $this->mf->data_konfirmasi_ppic_bdy($tanggal,implode("','",$farm),$konfirmasi,$sudah_konfirmasi) : $this->mf->data_konfirmasi_ppic($tanggal,implode("','",$farm),$konfirmasi);

		#echo print_r($data);
		$this->load->view('forecast/'.$grup_farm.'/konfirmasi_forecast_ppic',$data);
		#$this->load->view('forecast/konfirmasi_forecast_ppic',$data);
	}

	public function ack_forecast(){
		$kode_pegawai = $this->_user;
		$nama_pegawai = $this->_nama_user;
		$no_reg = $this->input->post('no_reg');
		$result = $this->mf->ack_forecast($kode_pegawai, $nama_pegawai, $no_reg);
		echo json_encode($result);
	}

	public function konfirmasi_rp(){
		$this->load->view('forecast/konfirmasi_rencana_produksi');

	}

	public function konfirmasi_rp_bdy(){
		$data['list_pakan'] = $this->mf->list_pakan_bdy()->result_array();
		$this->load->view('forecast/bdy/konfirmasi_rencana_produksi',$data);

	}

	public function tabel_konfirmasi_rp(){
		$realisasi = $this->input->post('realisasi');
		$tgl_awal = $this->input->post('tgl_awal');
		$tgl_akhir = $this->input->post('tgl_akhir');

		$where_kirim = '';
		if(!empty($tgl_awal)){
			if(!empty($tgl_akhir)){
				$where_kirim = ' and le.TGL_KIRIM between \''.$tgl_awal.'\' and \''.$tgl_akhir.'\'';
			}
			else{
				$where_kirim = ' and le.TGL_KIRIM >= \''.$tgl_awal.'\'';
			}
		}
		else{
			if(!empty($tgl_akhir)){
				$where_kirim = ' and le.TGL_KIRIM <= \''.$tgl_akhir.'\'';
			}
		}
		$where_realisasi = '';
		if(!empty($realisasi)){
			$where_realisasi = $realisasi == 'I' ? ' and (kp.realisasi_produksi = \'I\' or kp.realisasi_produksi is null)' : ' and kp.realisasi_produksi = \'C\'';
		}
		$kp = $this->mf->konfirmasi_rp($where_kirim,$where_realisasi)->result_array();

		$data['kp'] = $kp;
		$this->load->view('forecast/tabel_konfirmasi_rencana_produksi',$data);

	}


	public function tabel_konfirmasi_rp_bdy(){
		$kode_pakan = $this->input->post('kode_pakan');
		$tgl_awal = $this->input->post('tgl_awal');
		$tgl_akhir = $this->input->post('tgl_akhir');
		$tgl_server = $this->input->post('tgl_server');

		$kode_pakan_estimasi = !empty($kode_pakan) ? '\''.implode('\',\'', $kode_pakan).'\'' : '';
		$kode_pakan = !empty($kode_pakan) ? implode(',', $kode_pakan) : '';
		/* cari kode rencana produksi, kelolosan pakan */
		$kode_rencana_produksi = array();
		$kelolosan_pakan_produksi = array();
		/* cari estimasi yang sudah tersimpan */
		$estimasi = array();
		$est = $this->mf->estimasi_rencana_produksi($tgl_awal,$tgl_akhir,$kode_pakan_estimasi)->result_array();
		$kp = $this->mf->konfirmasi_rp_bdy($tgl_awal,$tgl_akhir,$kode_pakan)->result_array();

		/* grouping berdasarkan tanggal kirim dan kode_pakan */
		$tmp_est = array();
		$total_alokasi = array();
		if(!empty($est)){
			foreach($est as $e){
				$tgl_kirim = $e['tanggal_kirim'];
				$kode_pakan = $e['kode_pakan'];
				if(!isset($tmp_est[$tgl_kirim])){
					$tmp_est[$tgl_kirim] = array();
					$total_alokasi[$tgl_kirim] = array();
				}
				if(!isset($tmp_est[$tgl_kirim][$kode_pakan])){
					$tmp_est[$tgl_kirim][$kode_pakan] = array();
					$total_alokasi[$tgl_kirim][$kode_pakan] = array('jml' => 0, 'revisi' => 0);
				}
				array_push($tmp_est[$tgl_kirim][$kode_pakan],$e);
				/* jika kode rencana produksi masih kosong, tampilkan list rencana produksi */
				if(empty($e['kode_rencana_produksi'])){
					if(!isset($kode_rencana_produksi[$kode_pakan])){
						$kode_rencana_produksi[$kode_pakan] = array();
					}
					array_push($kode_rencana_produksi[$kode_pakan],$e['tanggal_produksi_estimasi']);
				}
				if(!empty($e['kode_rencana_produksi']) && empty($e['total_pakan_lolos'])){
					if(!isset($kelolosan_pakan_produksi[$kode_pakan])){
						$kelolosan_pakan_produksi[$kode_pakan] = array();
					}
					array_push($kelolosan_pakan_produksi[$kode_pakan],$e['tanggal_produksi_estimasi']);
				}

				$total_alokasi[$tgl_kirim][$kode_pakan]['jml'] += $e['alokasi_pakan_lolos_untuk_farm'];
				$total_alokasi[$tgl_kirim][$kode_pakan]['revisi'] = $total_alokasi[$tgl_kirim][$kode_pakan]['revisi'] < $e['revisi'] ? $e['revisi'] : $total_alokasi[$tgl_kirim][$kode_pakan]['revisi'];
			}
		}
		$list_rp = array();
		/*
		print_r($kode_rencana_produksi);
		$kode_rencana_produksi = array('1126-10-12'=>array('2016-02-11','2016-02-16','2016-02-19'),'1127-11E12'=>array('2016-02-19'));
		print_r($kode_rencana_produksi);
	*/
		if(!empty($kode_rencana_produksi)){
			foreach($kode_rencana_produksi as $pj => $krp){

				$r = null;
				$max = max($krp);
				$min = min($krp);
				$data_rp = array('awal'=>$min,'akhir'=>$max,'kodepj'=>$pj);
				$r = Modules::run('cproduksi/rencanaproduksi/listrencanaproduksi',$data_rp);

				if($r['status']){
					foreach($r['content']->rps as $rps){
						$tgl_produksi = $rps->tgl_produksi;
						if(!isset($list_rp[$pj])){
							$list_rp[$pj] = array();
						}
						if(!isset($list_rp[$pj][$tgl_produksi])){
							$list_rp[$pj][$tgl_produksi] = array();
						}
						array_push($list_rp[$pj][$tgl_produksi],$rps);
					}
				}

			}

		}
		$rp_lolos_pakan = array();

		if(!empty($kelolosan_pakan_produksi)){
			$this->load->model('forecast/m_item_rencana_produksi','irp');
			foreach($kelolosan_pakan_produksi as $pj => $krp){
				$max = max($krp);
				$min = min($krp);
				$data_rp = array('awal'=>$min,'akhir'=>$max,'kodepj'=>$pj);
				$r = Modules::run('cproduksi/serahterimapj/pakanjadi',$data_rp);

				if($r['status']){
					foreach($r['content']->pjs as $rps){
						$koderencanaproduksi = $rps->koderencanaproduksi;
						if(!isset($rp_lolos_pakan[$pj])){
							$rp_lolos_pakan[$pj] = array();
						}
						if(!isset($rp_lolos_pakan[$pj][$koderencanaproduksi])){
							$rp_lolos_pakan[$pj][$koderencanaproduksi] = $rps->pakanjadi_hasilproduksi;
							/* update jumlah pakan lolos disini saja */
							$where_lolos = array('rencana_produksi'=>$koderencanaproduksi,'pakan'=>$pj);
							$this->irp->update_by($where_lolos,array('total_pakan_lolos'=>$rps->pakanjadi_hasilproduksi));
						}
					}
				}
			}
		}
		/* grouping perkode pakan, per tanggal produksi */

		$estimasi = $tmp_est;

		/* grouping berdasarkan tanggal kirim dan kode_pakan */
		$tmp = array();

		foreach($kp as $k){
			$tgl_kirim =$k['tgl_kirim'];
			$kode_pakan = $k['kode_barang'];

			if(!isset($tmp[$tgl_kirim])){
				$tmp[$tgl_kirim] = array();
			}
			if(!isset($tmp[$tgl_kirim][$kode_pakan])){
				$tmp[$tgl_kirim][$kode_pakan] = array('header' => array('nama_pakan'=>$k['nama_barang'], 'jml'=> 0, 'pp' => 0,'tooltip'=> array()), 'detail'=> isset($estimasi[$tgl_kirim][$kode_pakan]) ? $estimasi[$tgl_kirim][$kode_pakan]: array());
			}

			$tmp[$tgl_kirim][$kode_pakan]['header']['jml'] += (int)$k['total_kebutuhan'];
			$tmp[$tgl_kirim][$kode_pakan]['header']['pp'] += (int)$k['jml_pp'];
			array_push($tmp[$tgl_kirim][$kode_pakan]['header']['tooltip'], '<tr><td>'.$k['nama_farm'].'</td><td class="number">'.($k['total_kebutuhan'] > 0 ? angkaRibuan((int)$k['total_kebutuhan']) : '-').'</td><td class="number">'.(!empty($k['jml_pp']) ? angkaRibuan((int)$k['jml_pp']) : '-').'</td></tr>');
		}

		/*cari rowspan untuk tanggal kirim dan nama pakan */
		$rowspan = array();
		$sekarangDate = new \DateTime($tgl_server);
		foreach($tmp as $tk => $tk_arr){
			$rowspan[$tk]['rowspan'] = 0;
			$paramKirimDate = new \DateTime($tk);
			$paramKirimDate->sub(new \DateInterval('P4D'));
			foreach($tk_arr as $p =>$p_arr){
				$jml_detail = count($p_arr['detail']);
				if($sekarangDate > $paramKirimDate){
					if(empty($jml_detail)){
						$jml_detail = 1;
					}
				}
				$rowspan[$tk][$p]['rowspan'] = $jml_detail + 1;
				$rowspan[$tk]['rowspan'] += $jml_detail + 1;
		}
	}
		$data['plot_sebelumnya'] = $this->plot_rp_sebelumnya($tgl_awal);
		$data['list_rp'] = $list_rp;
//		$data['rp_lolos_pakan'] = $rp_lolos_pakan;
		$data['rowspan'] = $rowspan;
		$data['estimasi'] = $estimasi;
		$data['kp'] = $tmp;
		$data['hari_libur'] = $this->get_hari_libur($tgl_server);
		$data['total_alokasi'] = $total_alokasi;
		$data['hari_ini'] = $tgl_server;
		$this->load->view('forecast/bdy/tabel_konfirmasi_rencana_produksi',$data);

	}
	private function get_hari_libur($minDate){
		$this->load->model('permintaan_pakan/m_permintaan_pakan','mpp');
		$hariLibur = $this->mpp->get_hari_libur($minDate)->result_array();

		$r = array();
		foreach($hariLibur as $h){
			array_push($r,$h['tanggal']);
		}
		return $r;
	}
	private function plot_rp_sebelumnya($tgl_kirim){
		$result = array();
		$rp_plot = $this->mf->plot_pakan_sebelumnya($tgl_kirim)->result_array();
		if(!empty($rp_plot)){
			foreach($rp_plot as $p){
				$rp = $p['rencana_produksi'];
				$pakan = $p['pakan'];
				if(!isset($result[$rp])){
					$result[$rp] = array();
				}
				$result[$rp][$pakan] = array('alokasi_pakan' => $p['alokasi_pakan'],'alokasi_pakan_lolos' => $p['alokasi_pakan_lolos']);
			}
		}
		return $result;
	}
	public function get_alokasi_sisa_kebutuhan(){
		$min = $this->input->post('tglawal');
		$max = $this->input->post('tglakhir');
		$kodepj = $this->input->post('kodepj');
		$data_rp = array('awal'=>$min,'akhir'=>$max,'kodepj'=>$kodepj);
		$plotPakan = $this->mf->alokasi_sisa_kebutuhan($min,$max,$kodepj)->result_array();
		$gPlot = array();
		if(!empty($plotPakan)){
			foreach($plotPakan as $pk){
				$rencana_produksi = $pk['rencana_produksi'];
				$gPlot[$rencana_produksi] = array(
					'sisa_kebutuhan' => $pk['sisa_kebutuhan'],
					'alokasi_pakan_untuk_farm' => $pk['alokasi_pakan_untuk_farm'],
					'plot_lolos_pakan' => $pk['plot_lolos_pakan'],
					'pengurang_belum_alokasi' => $pk['pengurang_belum_alokasi']
				);
			}
		}

		$r = Modules::run('cproduksi/rencanaproduksi/listrencanaproduksi',$data_rp);
		$tabel = array();
		$tbody = array();
		$thead = array('Tgl. Produksi','Kode RP','Total Produksi (sak)','Pakan Belum Dialokasikan (sak)','Kebutuhan Farm (sak)');
		$tfoot = array('<tr>','<td colspan="4" class="number">Total</td>','<td><input type="text" readonly name="total_plot_tambahan" /></td></tr>');
		if($r['status']){
			foreach($r['content']->rps as $rps){
			//	$koderencanaproduksi = $rps->koderencanaproduksi;
				$telahPlot = isset($gPlot[$rps->rp]) ?  $gPlot[$rps->rp]['alokasi_pakan_untuk_farm'] : 0;
				$telahPlotPakanLolos = isset($gPlot[$rps->rp]) ?  $gPlot[$rps->rp]['plot_lolos_pakan'] : 0;
				$sisaKebutuhan = isset($gPlot[$rps->rp]) ?  $gPlot[$rps->rp]['sisa_kebutuhan'] : 0;
				$pengurangBelumAlokasi = isset($gPlot[$rps->rp]) ?  $gPlot[$rps->rp]['pengurang_belum_alokasi'] : 0;
				$plotBebas = $rps->jml_produksi - $telahPlot;
				$bisaPlot = $plotBebas + $sisaKebutuhan - $pengurangBelumAlokasi;
				$bisaPlotPakanLolos = $rps->pakanjadi_hasilproduksi - $telahPlotPakanLolos;
				$tr = array(
					'<tr>',
					'<td>'.tglIndonesia($rps->tgl_produksi,'-',' ').'</td>',
					'<td>'.$rps->rp.'</td>',
					'<td class="number">'.angkaRibuan($rps->jml_produksi).'</td>',
					'<td class="number" data-sisa_kebutuhan="'.$sisaKebutuhan.'" data-plot_bebas="'.$plotBebas.'" data-jmlproduksi="'.$rps->jml_produksi.'" data-lolospakan="'.$rps->pakanjadi_hasilproduksi.'" data-bisaplotlolospakan="'.$bisaPlotPakanLolos.'">'.angkaRibuan($bisaPlot).'</td>',
					'<td><input type="text" name="alokasi_sisa" /></td>',
					'</tr>'
				);
				array_push($tbody,implode('',$tr));

			}
		}
		array_push($tabel,'<thead><tr><th>'.implode('</th><th>',$thead).'</th></tr></thead>');
		array_push($tabel,'<tbody>'.implode('',$tbody).'</tbody>');
		array_push($tabel,'<tfoot>'.implode('',$tfoot).'</tfoot>');
		echo '<table class="table table-bordered">'.implode('',$tabel).'</table>';
	}

	public function simpan_konfirmasi_rp(){
		$data_rp = $this->input->post('data_konfirmasi');
		$this->load->model('forecast/m_konfirmasi_ppic','kppic');
		$this->load->model('forecast/m_konfirmasi_rencana_produksi','krp');
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$this->db->trans_begin();
		$t = array();
		foreach($data_rp as $ppic){
			$kode_konfirmasi = $ppic['kode_konfirmasi'];
			$detail = isset($ppic['rencana_produksi']) ? $ppic['rencana_produksi'] : null;
			$header = array('no_op' => $ppic['no_op'], 'kode_pakan' => $ppic['kode_pakan'], 'tgl_kirim' => $ppic['tgl_kirim'], 'tgl_akhir_rencana_produksi' => $ppic['tgl_akhir_rencana_produksi'], 'realisasi_produksi' => $ppic['realisasi_produksi']);

			$header['tgl_buat'] = $tglserver;
			$header['user_buat'] = $this->_user;

			if(empty($kode_konfirmasi)){
				if($header['realisasi_produksi'] == 'C'){
					$header['tgl_selesai'] = $tglserver;
					$header['user_selesai'] = $this->_user;
				}
				$id = $this->kppic->insert($header);
			}
			else{
				/* update datakonfirmasi */
				if($header['realisasi_produksi'] == 'C'){
					$update = array();
					$update['tgl_selesai'] = $tglserver;
					$update['user_selesai'] = $this->_user;
					$update['realisasi_produksi'] = $header['realisasi_produksi'];
					$this->kppic->update($kode_konfirmasi,$update);
				}
				$id = $kode_konfirmasi;
			}
			if(!empty($detail)){
				foreach($detail as $d){
					$t['konfirmasi_ppic'] = $id;
					$t['tgl_buat'] = $tglserver;
					$t['user_buat'] = $this->_user;
					$t['rencana_produksi'] = $d;
					$this->krp->insert($t);
				}
			}

		}
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
		}
		echo json_encode($this->result);
	//	$this->load->view('forecast/tabel_konfirmasi_rencana_produksi',$data);

	}

	public function simpan_konfirmasi_rp_bdy(){
		$data_rp = $this->input->post('data_konfirmasi');
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$this->db->trans_begin();
		$t = array();
		foreach($data_rp as $rp){
			$kode_pakan = $rp['kode_pakan'];
			$tgl_kirim = $rp['tgl_kirim'];
			$aksi = $rp['aksi'];
			switch($aksi){
				case 'estimasi_tanggal_produksi':
					$this->simpan_estimasi_tanggal_produksi($kode_pakan,$tgl_kirim,$rp['tglproduksi'],$tglserver);
					break;
				case 'rencana_produksi':
				/* periksa dulu apakah rencana produksi ini sudah ada atau belum */
					$data_rencana = array('tanggal_produksi'=> $rp['tanggal_produksi'],'id'=>$rp['kode_rencana_produksi']);
					$data_item_rencana = array('rencana_produksi'=>$rp['kode_rencana_produksi'],'pakan'=>$kode_pakan, 'total_produksi'=>$rp['total_produksi']);
					$this->simpan_rencana_produksi($data_rencana);
					$id_item_rencana = $this->simpan_item_rencana_produksi($data_item_rencana);
					$data_alokasi_hasil_produksi = array('rencana_kirim'=>$rp['rencana_kirim'],'item_rencana_produksi'=>$id_item_rencana,'alokasi_pakan_untuk_farm'=>$rp['alokasi_pakan_untuk_farm']);
					$this->simpan_alokasi_hasil_produksi($data_alokasi_hasil_produksi,$tglserver);
					break;
				case 'kelolosan_pakan':
					$data_alokasi_pakan_lolos = array('alokasi_hasil_produksi'=>$rp['id_hasil_produksi'],'jumlah_sak'=>$rp['alokasi_pakan_lolos_untuk_farm']);
					$this->simpan_alokasi_pakan_lolos($data_alokasi_pakan_lolos,$tglserver);
					break;
				case 'revisi_rencana_produksi':
					$data = array(
						'tanggal_kirim'=>$tgl_kirim,
						'kode_pakan'=> $kode_pakan,
						'tanggal_produksi'=>$rp['tanggal_produksi'],
						'rencana_kirim'=>$rp['rencana_kirim'],
						'kode_rencana_produksi'=>$rp['kode_rencana_produksi'],
						'jumlah_sak'=>$rp['alokasi_pakan_lolos_untuk_farm'],
						'total_produksi'=>$rp['total_produksi'],
						'total_pakan_lolos' => !empty($rp['lolos_pakan']) ? $rp['lolos_pakan'] : NULL,
						'alokasi_pakan_untuk_farm' => isset($rp['alokasi_pakan_untuk_farm']) ? $rp['alokasi_pakan_untuk_farm'] : NULL
					);

					$this->insert_revisi_rencana_produksi($data,$tglserver);
					break;
			}

		}
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
		}
		echo json_encode($this->result);

	}

	private function simpan_rencana_produksi($data_rencana){
		$this->load->model('forecast/m_rencana_produksi','rp');
		/* periksa sudah ada atau belum */
		$sudahAda = $this->rp->get($data_rencana['id']);
		if(empty($sudahAda)){
				$this->rp->insert($data_rencana);
		}
	}

	private function simpan_item_rencana_produksi($data_rencana){
		$this->load->model('forecast/m_item_rencana_produksi','irp');
		/* periksa sudah ada atau belum */
		$sudahAda = $this->irp->get_by(array('rencana_produksi'=>$data_rencana['rencana_produksi'],'pakan'=>$data_rencana['pakan']));
		if(empty($sudahAda)){
				$id = $this->irp->insert($data_rencana);
		}
		else{
			$id = $sudahAda->id;
		}
		return $id;
	}


	private function simpan_alokasi_hasil_produksi($data_rencana,$tglserver){
		$this->load->model('forecast/m_alokasi_hasil_produksi','ahp');
		$data_rencana['user_entry'] = $this->_user;
		$data_rencana['tgl_entry'] = $tglserver;
		return $this->ahp->insert($data_rencana);
	}

	private function simpan_alokasi_pakan_lolos($data_rencana,$tglserver){
		$this->load->model('forecast/M_alokasi_pakan_lolos_untuk_farm','plf');
		$data_rencana['user_entry'] = $this->_user;
		$data_rencana['tgl_entry'] = $tglserver;
		return $this->plf->insert($data_rencana);
	}

	private function insert_revisi_rencana_produksi($data,$tglserver){
		$this->load->model('forecast/m_rencana_kirim','rk');
		$this->load->model('forecast/m_estimasi_tanggal_produksi','etp');
		if($data['rencana_kirim'] == 'undefined'){
			$id = $this->rk->get_by(array('tanggal_kirim'=>$data['tanggal_kirim'],'kode_pakan'=>$data['kode_pakan']));
			if(empty($id)){
				$this->load->library('uuid');
				$uuid = $this->uuid->v4();
				$this->rk->insert(array('id'=>$uuid,'tanggal_kirim'=>$data['tanggal_kirim'],'kode_pakan'=>$data['kode_pakan']));
				$data['rencana_kirim'] = $uuid ;
			}
			else{
				$data['rencana_kirim'] = $id->id;
			}
		}
		/* untuk tambah RP gak perlu insert ke estimasi_tglproduksi */
		/* cari apakah sudah ada di estimasi rencana produksi atau belum
		$idEstimasi = $this->etp->get_by(array('rencana_kirim'=>$data['rencana_kirim'],'tanggal_produksi'=>$data['tanggal_produksi']));
		if(empty($idEstimasi)){
			$this->simpan_estimasi_tanggal_produksi($data['kode_pakan'],$data['tanggal_kirim'],$data['tanggal_produksi'],$tglserver);
		}
		*/
		/* cari rencana produksi */
		$data_rencana = array('tanggal_produksi'=> $data['tanggal_produksi'],'id'=>$data['kode_rencana_produksi']);
		$rp = $this->simpan_rencana_produksi($data_rencana);
		$data_item_rencana = array('rencana_produksi'=>$data['kode_rencana_produksi'],'pakan'=>$data['kode_pakan'],'total_produksi'=>$data['total_produksi'],'total_pakan_lolos' => $data['total_pakan_lolos']);
		$irp = $this->simpan_item_rencana_produksi($data_item_rencana);
		$data_alokasi_hasil_produksi = array('rencana_kirim'=>$data['rencana_kirim'],'item_rencana_produksi'=>$irp,'alokasi_pakan_untuk_farm' => $data['alokasi_pakan_untuk_farm']);
		$ahp = $this->simpan_alokasi_hasil_produksi($data_alokasi_hasil_produksi,$tglserver);

		$data_alokasi_pakan_lolos = array('alokasi_hasil_produksi'=>$ahp,'jumlah_sak'=>$data['jumlah_sak']);
		$this->simpan_alokasi_pakan_lolos($data_alokasi_pakan_lolos,$tglserver);
	}

	private function simpan_estimasi_tanggal_produksi($kode_pakan,$tgl_kirim,$tglproduksi,$tglserver){
		$this->load->model('forecast/m_rencana_kirim','rk');
		$this->load->model('forecast/m_estimasi_tanggal_produksi','etp');
		/* cari apakah sudah ada id nya di rencana kirim */
		$id = $this->rk->get_by(array('tanggal_kirim'=>$tgl_kirim,'kode_pakan'=>$kode_pakan));
		if(empty($id)){
			$this->load->library('uuid');
			$uuid = $this->uuid->v4();
			$this->rk->insert(array('id'=>$uuid,'tanggal_kirim'=>$tgl_kirim,'kode_pakan'=>$kode_pakan));
			$rencana_kirim = $uuid ;
		}
		else{
			$rencana_kirim = $id->id;
		}

		$data = array(
				'rencana_kirim' => $rencana_kirim,
				'tanggal_produksi' => $tglproduksi,
				'user_buat' => $this->_user,
				'tgl_buat' => $tglserver
			);
		return $this->etp->insert($data);
	}

	public function get_rencana_produksi(){
		$data = array(
				'awal' => $this->input->post('awal'),
				'akhir' => $this->input->post('akhir'),
				'kodepj' => $this->input->post('kodepj')
		);
		$r = Modules::run('cproduksi/rencanaproduksi/listrencanaproduksi',$data);
	/*	$r = array(
			array('rp' => 'RP02512352','tgl_produksi'=> tglIndonesia('2015-05-06','-',' '),'jml_produksi'=> 20),
			array('rp' => 'RP02512354','tgl_produksi'=> tglIndonesia('2015-05-06','-',' '),'jml_produksi'=> 50),
			array('rp' => 'RP02512356','tgl_produksi'=> tglIndonesia('2015-05-06','-',' '),'jml_produksi'=> 40),
		);

		$this->result['status'] = 1;
		$this->result['content'] = $r;
	*/
		$this->result = $r;
		echo json_encode($this->result);
	}

	public function get_pakanjadi(){
		$data = array(
				'awal' => $this->input->post('awal'),
				'akhir' => $this->input->post('akhir'),
				'kodepj' => $this->input->post('kodepj')
		);
	//	$data['kodepj'] = '1111-10-13';
		$r = Modules::run('cproduksi/serahterimapj/pakanjadi',$data);
		/*	$r = array(
		 array('rp' => 'RP02512352','tgl_produksi'=> tglIndonesia('2015-05-06','-',' '),'jml_produksi'=> 20),
				array('rp' => 'RP02512354','tgl_produksi'=> tglIndonesia('2015-05-06','-',' '),'jml_produksi'=> 50),
				array('rp' => 'RP02512356','tgl_produksi'=> tglIndonesia('2015-05-06','-',' '),'jml_produksi'=> 40),
		);

		$this->result['status'] = 1;
		$this->result['content'] = $r;
		*/
		$this->result = $r;
		echo json_encode($this->result);
	}

	public function get_serah_terimapj(){
		$data = array(
				'rp' => $this->input->post('rp'),
				'kodepj' => $this->input->post('kodepj')
		);
		$r = Modules::run('cproduksi/serahterimapj/kavlingpakanjadi',$data);
		$this->result = $r;
		echo json_encode($this->result);
	}

	public function import_rencana_docin(){
		$user_level = $this->session->userdata('level_user');
		$canApprove = array('KD','KDV');
		$canImport = array('KA');
		$statusApprove = array(
			'KA' => 'N',
			'KD' => 'RV',
			'KDV' => 'A'
		);
		$this->load->model('forecast/m_import_docin','mi');
		$data['siklusTahunan'] = $this->mi->siklus_tahunan()->result_array();

		$data['canApprove'] = in_array($user_level, $canApprove) ? 1 : 0;
		$data['canImport'] = in_array($user_level, $canImport) ? 1 : 0;
		$data['statusApprove'] = $statusApprove[$user_level];
		$this->load->view('forecast/bdy/rencana_docin',$data);
	}

	public function simpan_rencana_docin(){
		$periodeSiklus = $this->input->post('periodeSiklus');

		$thnSiklus = $this->input->post('thnSiklus');
		$this->load->model('forecast/m_import_docin','mi');
		/* cek apakah masih bisa diganti atau tidak
		 * jika tidak ditemukan maka boleh insert
		 * jika statusnya draft maka hapus yang lama
		 * insert yang baru, jika selainnya gak boleh
		 * */
		$error = 0;
		$hapus = 0;

		$thnSiklusDb = $this->mi->siklus_tahunan($thnSiklus)->row_array();


		if(!empty($thnSiklusDb)){
			if($thnSiklusDb['status'] != 'DRAFT'){
				$error++;
				$this->result['status'] = 0;
				$this->result['message'] = array('Status pengajuan DOC In untuk tahun '.$thnSiklusDb['tahun'].' sudah '.$thnSiklusDb['status'].' tidak bisa merubah lagi');
			}
			else{
				$hapus = 1; /* hapus yang lamu lalu insert lagi */
			}
		}


		if(!$error){
			$this->db->trans_begin();

			if($hapus){
				$this->delete_doc_in_bdy($thnSiklus);
			}
			/* insert ke kandang siklus */

			$t = $this->insert_doc_in_bdy($periodeSiklus);

			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
			}
			else{
				if($t['err'] > 0){
					$this->db->trans_rollback();
					$this->result['message'] = $t['msg'];
					$this->result['status'] = 0;
				}
				else{
					$this->db->trans_commit();
					$this->result['status'] = 1;
					$this->result['message'] = 'Upload data rencana DOC In berhasil disimpan';
					$this->result['content'] = array('tahun' => $thnSiklus, 'status' => 'DRAFT');
				}

			}
		}


		echo json_encode($this->result);
	}
	private function delete_doc_in_bdy($tahun){
		$this->load->model('forecast/m_kandang_siklus', 'ks');
		$farm_bdy = $this->ks->farm_budidaya();
		$this->ks->delete_by('kode_farm in ('.$farm_bdy.') and year(tgl_doc_in) = \''.$tahun.'\'');
	}

	private function insert_doc_in_bdy($periodeSiklus){
		$this->load->model('forecast/m_kandang_siklus', 'ks');
		$this->load->model('forecast/m_log_kandang_siklus_bdy', 'lks');
		$this->load->model('forecast/m_periode', 'mp');
		$this->load->model('forecast/m_import_docin','mi');
		$this->load->model('forecast/m_kandang', 'mk');
		$error = 0;
		$message = array();
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$std_budidaya = array();
		$data_mkandang = array();
		$umur_panen_std = array();
		$farm_siklus_flok = array(); /* untuk menyimpan tanggal docin per flok per siklus per farm */
		foreach($periodeSiklus as $kf => $perfarm){
			foreach($perfarm as $ks => $siklus){
				/* cari kode siklus yang berlaku */
				$where_mp = array('kode_farm'=>$kf,'periode_siklus'=>$ks);
				$arrSiklus = $this->mp->as_array()->get_by($where_mp);
				if(!empty($arrSiklus)){
					$kodeSiklus = $arrSiklus['KODE_SIKLUS'];
					/*update kode strain*/
					$this->mp->update_by($where_mp,array('kode_strain'=> $siklus['strain']));
				}
				else{
					$kodeSiklus = $this->mp->insert(array('kode_farm'=>$kf,'periode_siklus'=>$ks,'kode_strain' => $siklus['strain'],'status_periode'=>'N'));
				}
				/* insert ke kandang siklus */
				foreach($siklus['kandang'] as $kandang){
					$tgl_doc_in =  $this->convertDateImport($kandang['Tanggal_Docin']);
					$tgl_panen = $this->convertDateImport($kandang['Tanggal_Panen']);
					$kode_farm = $kandang['Farm'];
					$kode_kandang = $kandang['Kandang'];
					/* mencari standard budidaya yang akan digunakan */
					if(!isset($std_budidaya[$kode_farm])){
						$std_budidaya[$kode_farm] = array();
					}
					if(!isset($std_budidaya[$kode_farm][$siklus['strain']])){
						$std_budidaya[$kode_farm][$siklus['strain']] = array();
					}
					if(!isset($std_budidaya[$kode_farm][$siklus['strain']][$tgl_doc_in])){
						$tmp_budidaya = $this->mi->get_std_budidaya("KODE_FARM = '".$kode_farm."' and KODE_STRAIN = '".$siklus['strain']."' and tgl_efektif <= '".$tgl_doc_in."'")->row_array();
						if(count($tmp_budidaya) == 0){
							$error++;
							array_push($message,'Standart budidaya untuk farm '.$kode_farm.' strain '.$siklus['strain']. ' dengan tanggal efektif <= '.$tgl_doc_in.' tidak ditemukan');
						}
						else{
							if(!isset($umur_panen_std[$tmp_budidaya['kode_std_budidaya']])){
									$umur_panen_std[$tmp_budidaya['kode_std_budidaya']] = array('umur_panen' => $tmp_budidaya['target_umur_panen']);
							}
							$std_budidaya[$kode_farm][$siklus['strain']][$tgl_doc_in] = $tmp_budidaya['kode_std_budidaya'];
						}

					}
					/* keluar dari looping */
					if($error){
						break;
					}

					/* mencari data master kandang */
					if(!isset($data_mkandang[$kode_farm])){
						$data_mkandang[$kode_farm] = array();
					}
					if(!isset($data_mkandang[$kode_farm][$kode_kandang])){
						$tmp_mkandang = $this->mk->as_array()->get_by(array("KODE_FARM " => $kode_farm,"KODE_KANDANG" => $kode_kandang, "STATUS_KANDANG" => "A"));
						if(count($tmp_mkandang) == 0){
							$error++;
							array_push($message,'Data untuk kandang '.$kode_kandang.' pada farm '.$kode_farm.' tidak ditemukan di data master');
						}
						else{
							$data_mkandang[$kode_farm][$kode_kandang] = array(
								'tipe_kandang' => $tmp_mkandang['TIPE_KANDANG'],
								'tipe_lantai' => $tmp_mkandang['TIPE_LANTAI'],
								'no_flok' => $tmp_mkandang['NO_FLOK'],
								'max_populasi' => $tmp_mkandang['MAX_POPULASI']
							);
						}
					}

					/* keluar dari looping */
					if($error){
						break;
					}

					/* periksa apakah populasi yang diset perkandang harus <= max_populasi*/
					if($kandang['Populasi'] > $data_mkandang[$kode_farm][$kode_kandang]['max_populasi']){
						$error++;
						array_push($message,'Jumlah maximal populasi kandang '.$kode_kandang.' pada farm '.$kode_farm.' lebih besar dari data master ');
					}

					/* periksa apakah umur panen yang diset harus >= target umur panen */
		//		log_message('error',dateDifference($tgl_doc_in,$tgl_panen));
					if(!$error){
						if(dateDifference($tgl_doc_in,$tgl_panen) < $umur_panen_std[$std_budidaya[$kode_farm][$siklus['strain']][$tgl_doc_in]]['umur_panen']){
							$error++;
							array_push($message,'Tanggal panen kandang '.$kode_kandang.' pada farm '.$kode_farm.' lebih kecil dari target umur panen pada standart');
						}
					}

					/* periksa apakah tanggal doc in sudah sama pada semua kandang berdasarkan farm dan siklus */
					$flok_bdy = $data_mkandang[$kode_farm][$kode_kandang]['no_flok'];
					if(!isset($farm_siklus_flok[$kode_farm])){
						$farm_siklus_flok[$kode_farm] = array();
					}
					if(!isset($farm_siklus_flok[$kode_farm][$kodeSiklus])){
						$farm_siklus_flok[$kode_farm][$kodeSiklus] = array();
					}
					if(!isset($farm_siklus_flok[$kode_farm][$kodeSiklus][$flok_bdy])){
						/* periksa apakah tanggal doc ini sudah pernah diplot oleh flok lain */
						if(in_array($tgl_doc_in,$farm_siklus_flok[$kode_farm][$kodeSiklus])){
							$error++;
							array_push($message,'Tanggal DOC In kandang '.$kode_kandang.' pada farm '.$kode_farm.' dengan flok '.$flok_bdy.' sudah diplot pada flok lain');
						}
						$farm_siklus_flok[$kode_farm][$kodeSiklus][$flok_bdy] = $tgl_doc_in;
					}
					if($farm_siklus_flok[$kode_farm][$kodeSiklus][$flok_bdy] != $tgl_doc_in){
						$error++;
						array_push($message,'Tanggal DOC In kandang '.$kode_kandang.' pada farm '.$kode_farm.' dengan flok '.$flok_bdy.' tidak sama');
					}
					/* keluar dari looping */
					if($error){
						break;
					}

					if(!$error){
						$dataKandang = array(
								'kode_siklus'=> $kodeSiklus,
								'kode_farm' => $kode_farm,
								'kode_kandang' => $kode_kandang,
								'no_reg' => $kandang['Farm'].'/'.$ks.'/'.$kode_kandang,
								'kode_pegawai' => $this->_user,
								'tgl_doc_in' => $tgl_doc_in,
								'tgl_buat' => $tglserver,
								'user_buat' => $this->_user,
								'jml_populasi' => $kandang['Populasi'],
								'kode_std_budidaya' =>$std_budidaya[$kode_farm][$siklus['strain']][$tgl_doc_in],
								'flok_bdy' => $data_mkandang[$kode_farm][$kode_kandang]['no_flok'],
								'tipe_kandang' => $data_mkandang[$kode_farm][$kode_kandang]['tipe_kandang'],
								'tipe_lantai' => $data_mkandang[$kode_farm][$kode_kandang]['tipe_lantai'],
								'tgl_panen'	=> $tgl_panen,
								'status_siklus' => 'P'
						);
						$this->ks->insert($dataKandang);
						/* insert ke log_kandang_siklus_bdy */
						$no_urut = $this->lks->get_no_urut(array('kode_siklus'=> $kodeSiklus,'kode_farm' => $kandang['Farm'],'kode_kandang' => $kandang['Kandang']));

						$data_log = array(
								'kode_siklus'=> $kodeSiklus,
								'kode_farm' => $kandang['Farm'],
								'kode_kandang' => $kandang['Kandang'],
								'status_approve' => 'D',
								'no_urut' => $no_urut,
								'tgl_buat' => $tglserver,
								'user_buat' => $this->_user
						);
						$this->lks->insert($data_log);
					}
				}
			}
			/* keluar dari looping */
			if($error){
				break;
			}
		}
		return array('err' => $error , 'msg' => $message);

	}

	public function reject_rencanadocin(){
		$status = $this->input->post('status');
		$tahun = $this->input->post('tahun');
		$keterangan = $this->input->post('keterangan');
		$keterangan = !empty($keterangan) ? $keterangan : NULL;
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$this->load->model('forecast/m_log_kandang_siklus_bdy', 'lks');
		$this->load->model('forecast/m_kandang_siklus', 'ks');
		$s_msg = 'direject';
		switch($status){
			case 'RV':
				$status_str = 'DRAFT';
				$data_update = array('tgl_approve1'=>NULL,'user_approve1'=>NULL);
				break;
			case 'A':
				$status_str = 'DRAFT';
				$data_update = array('tgl_approve1'=>NULL,'user_approve1'=>NULL,'tgl_approve2'=>NULL,'user_approve2'=>NULL);
				break;
		}

		$this->db->trans_begin();
		/* update data pada kandang siklus */
		$where_ks = 'year(tgl_doc_in) = \''.$tahun.'\' and kode_std_budidaya is not null and flok_bdy is not null';
		$this->ks->update_by($where_ks,$data_update);

		$update_log = $this->ks->as_array()->get_many_by($where_ks);
		foreach($update_log as $ul){
			$kodeSiklus = $ul['KODE_SIKLUS'];
			$kodeFarm = $ul['KODE_FARM'];
			$kodeKandang = $ul['KODE_KANDANG'];

			/* insert ke log_kandang_siklus_bdy */
			$no_urut = $this->lks->get_no_urut(array('kode_siklus'=> $kodeSiklus,'kode_farm' => $kodeFarm,'kode_kandang' => $kodeKandang));

			$data_log = array(
					'kode_siklus'=> $kodeSiklus,
					'kode_farm' => $kodeFarm,
					'kode_kandang' => $kodeKandang,
					'status_approve' => 'D',
					'no_urut' => $no_urut,
					'tgl_buat' => $tglserver,
					'user_buat' => $this->_user,
					'keterangan'=>$keterangan
			);
			$this->lks->insert($data_log);
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = 'Perencanaan DOC In tahun '.$tahun.' berhasil '.$s_msg;
			$this->result['content'] = $status_str;
		}
		echo json_encode($this->result);
	}

	public function approve_rilis_rencanadocin(){
		$status = $this->input->post('status');
		$tahun = $this->input->post('tahun');
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;
		$this->load->model('forecast/m_log_kandang_siklus_bdy', 'lks');
		$this->load->model('forecast/m_kandang_siklus', 'ks');

		switch($status){
			case 'N':
				$s_msg = 'dirilis';
				$status_str = 'PENGAJUAN';
				$data_update = array('tgl_rilis'=>$tglserver,'user_rilis'=>$this->_user);
				break;
			case 'RV':
				$s_msg = 'direview';
				$status_str = 'REVIEW';
				$data_update = array('tgl_approve1'=>$tglserver,'user_approve1'=>$this->_user);
				break;
			case 'A':
				$s_msg = 'diapprove';
				$status_str = 'APPROVE';
				$data_update = array('tgl_approve2'=>$tglserver,'user_approve2'=>$this->_user);
				break;
			default :
				$s_msg = '';
		}

		$this->db->trans_begin();
		/* update data pada kandang siklus */
		$where_ks = 'year(tgl_doc_in) = \''.$tahun.'\' and kode_std_budidaya is not null and flok_bdy is not null';
		$this->ks->update_by($where_ks,$data_update);

		$update_log = $this->ks->as_array()->get_many_by($where_ks);
		foreach($update_log as $ul){
			$kodeSiklus = $ul['KODE_SIKLUS'];
			$kodeFarm = $ul['KODE_FARM'];
			$kodeKandang = $ul['KODE_KANDANG'];

			/* insert ke log_kandang_siklus_bdy */
			$no_urut = $this->lks->get_no_urut(array('kode_siklus'=> $kodeSiklus,'kode_farm' => $kodeFarm,'kode_kandang' => $kodeKandang));
			$data_log = array(
					'kode_siklus'=> $kodeSiklus,
					'kode_farm' => $kodeFarm,
					'kode_kandang' => $kodeKandang,
					'status_approve' => $status,
					'no_urut' => $no_urut,
					'tgl_buat' => $tglserver,
					'user_buat' => $this->_user
			);
			$this->lks->insert($data_log);
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = 'Perencanaan DOC In tahun '.$tahun.' berhasil '.$s_msg;
			$this->result['content'] = $status_str;
		}
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	//	echo json_encode($this->result);
	}

	/* list farm khusus budidaya berdasarkan tahun yang dipilih*/
	public function list_farm_preview(){
		$tahun = $this->input->post('tahun');
		$this->load->model('forecast/m_import_docin','mi');
		$where_ks = 'year(tgl_doc_in) = \''.$tahun.'\' and kode_std_budidaya is not null and flok_bdy is not null';
		$data = $this->mi->farm_preview($where_ks)->result_array();
		if(!empty($data)){
			$this->result['status'] = 1;
			$this->result['content'] = $data;
		}
		else{
			$this->result['message'] = 'Data tidak ditemukan';
		}

		echo json_encode($this->result);
	}

	public function detail_docin_bdy(){
		$this->load->model('forecast/m_kandang_siklus','ks');
		$this->load->model('forecast/m_std_budidaya','std');
		$tahun = $this->input->post('tahun');
		$farm = $this->input->post('farm');
		$where_ks = 'kode_farm = \''.$farm.'\' and year(tgl_doc_in) = \''.$tahun.'\' and kode_std_budidaya is not null and flok_bdy is not null';
		$data = $this->ks->as_array()->get_many_by($where_ks);

		$tmp = array();
		if(!empty($data)){
			$kode_std = $data[0]['KODE_STD_BUDIDAYA'];
			$std_budidaya = $this->std->as_array()->get($kode_std);
			foreach($data as $d){
				$t = array($d['KODE_KANDANG'],tglIndonesia($d['TGL_DOC_IN'],'-',' '),$this->getSiklusBdy($d['NO_REG']),$std_budidaya['KODE_STRAIN'],$d['JML_POPULASI'],tglIndonesia($d['TGL_PANEN'],'-',' '));
				array_push($tmp,$t);
			}
			$this->result['status'] = 1;
			$this->result['content'] = array( 'tabel' => $tmp, 'header' => $std_budidaya);
		}
		else{
			$this->result['message'] = 'Data tidak ditemukan';
		}

		echo json_encode($this->result);
	}

	public function detail_kandang_bdy(){
		$kodeFarm = $this->input->post('kodeFarm');
		$tglDocIn = $this->input->post('tglDocIn');
		$kandang = $this->input->post('kandang');
		$this->load->model('forecast/m_forecast','mf');
		$data['list'] = $this->mf->get_detail_docin_bdy($kodeFarm,$tglDocIn,$kandang)->result_array();
		if(!empty($data['list'])){
			$tb = $this->load->view('forecast/bdy/detail_kandang',$data,true);
			$this->result['status'] = 1;
			$this->result['content'] = $tb;
		}
		else{
			$this->result['message'] = 'Data tidak ditemukan';
		}

		echo json_encode($this->result);
	}

	/* update doc in oleh kadiv */
	public function update_tgl_docin(){
		$kodeFarm = $this->input->post('kodeFarm');
		$tglDocIn = $this->input->post('tglDocIn');
		$tglDocInAsal = $this->input->post('tglDocInAsal');

		$this->load->model('forecast/m_kandang_siklus','ks');
		$where = array('kode_farm'=> $kodeFarm, 'tgl_doc_in' => $tglDocInAsal);
		$update = array('tgl_doc_in' => $tglDocIn);

		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;

		$this->db->trans_begin();
		/* update log_doc_in */
		$this->load->model('forecast/m_log_doc_in','mld');
		/* kumpulkan data dari kandang siklus */
		$no_reg_update = $this->ks->as_array()->get_many_by($where);
		foreach($no_reg_update as $tmp){
				$insert_data = array(
					'NO_REG' => $tmp['NO_REG'],
					'BACKUP_TGL_DOC_IN' => $tglDocInAsal,
					'TGL_BUAT' => $tglserver,
					'USER_BUAT' => $this->_user
				);
				$this->mld->insert($insert_data);
		}

		$t = $this->ks->update_by($where,$update);

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
		}
	//	echo json_encode($this->result);
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	/* detail standart budidaya */
	public function detail_std(){
		$std = $this->input->post('std');
		$data['list'] = $this->db->select('target_dh_prc \'Daya Hidup\',target_bb_prc \'Berat Badan\',target_fcr_prc FCR,target_umur_panen \'Umur Panen\',target_ip IP')->from('m_std_budidaya')->where('kode_std_budidaya',$std)->get()->result_array();

		$this->load->view('forecast/bdy/detail_standart',$data);
	}

	public function update_std_farm(){
		$std = $this->input->post('std');
		$kodefarm = $this->input->post('kodefarm');
		$tglefektif = $this->input->post('tgl_efektif');
		//$u = $this->db->where('kode_farm = \''.$kodefarm.'\' and tgl_doc_in > getdate() + 6')->update('kandang_siklus',array('kode_std_budidaya'=>$std));
		$u = $this->db->where('kode_farm = \''.$kodefarm.'\' and tgl_doc_in >= \''.$tglefektif.'\'')->update('kandang_siklus',array('kode_std_budidaya'=>$std));
		if($u){
			$this->result['status'] = 1;
			$this->result['message'] = 'Kode standart budidaya berhasil diupdate';
		}
		else{
			$this->result['message'] = 'Kode standart budidaya gagal diupdate';
		}
		//echo json_encode($this->result);
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function simpanRencanaKirim(){
		$data = $this->input->post('data');
		$detail = $this->input->post('detail');
		$kodefarm = $this->input->post('kode_farm');
		$tgldocin = $this->input->post('tgl_docin');
		$noreg = $this->input->post('noreg');
	

		$this->load->model('forecast/m_cycle_state_transition','cst');
		$this->load->model('forecast/m_forecast_d','mfd');
		$this->load->model('forecast/m_kandang_siklus','ks');

		$kode_siklus = $this->db->select('kode_siklus,flok_bdy')->where(array('kode_farm'=>$kodefarm,'tgl_doc_in'=>$tgldocin))->get('kandang_siklus')->row_array();
		$this->db->trans_begin();
		/* get uuid dari forecast dulu */
		$forecast_uuid = $this->db->select('id')->where(array('kode_flok_bdy' => $kode_siklus['flok_bdy'],'kode_siklus' => $kode_siklus['kode_siklus']))->get_compiled_select('forecast');
		$this->mfd->delete_by('forecast in ('.$forecast_uuid.')');
		/* hapus dulu data di forecast */
		$this->mf->delete_by(array('kode_flok_bdy' => $kode_siklus['flok_bdy'],'kode_siklus' => $kode_siklus['kode_siklus']));

		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;

		foreach($data as $d){
			$d['kode_siklus'] = $kode_siklus['kode_siklus'];
			$d['kode_flok_bdy'] = $kode_siklus['flok_bdy'];
			$this->mf->insert($d);
		}

		$fuuid = $this->mf->as_array()->get_many_by(array('kode_flok_bdy' => $kode_siklus['flok_bdy'],'kode_siklus' => $kode_siklus['kode_siklus']));
		$gfuuid = array();
		foreach($fuuid as $fu){
			$gfuuid[$fu['TGL_KIRIM']] = $fu['id'];
		}

		foreach($detail as $t => $de){
			foreach($de as $_de){
				$_de['forecast'] = $gfuuid[$t];
				$this->mfd->insert($_de);
			}
		//	print_r($de);die();

		}

		/* insert ke tabel cycle_state_transition */
		foreach($noreg as $n){
			$z = array(
				'cycle' => $kode_siklus['kode_siklus'],
				'flock' => $kode_siklus['flok_bdy'],
				'noreg' => $n,
				'stamp' => $tglserver,
				'user'  => $this->_user,
				'state' => 'P1'
			);
			$this->cst->insert($z);
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->result['message'] = 'Rencana kirim gagal disimpan';
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$periode_siklus= $this->db->select('periode_siklus')->where(array('kode_siklus'=>$kode_siklus['kode_siklus']))->get('m_periode')->row_array();
			$this->result['message'] = 'Perencanaan DOC In siklus '.$periode_siklus['periode_siklus'].' berhasil dilakukan';
		}

	//	echo json_encode($this->result);
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));

	}

	public function rencanaKirim(){
		$kodefarm = $this->input->post('kodefarm');
		$tgldocin = $this->input->post('tgldocin');
		$this->load->model('forecast/m_kandang_siklus','ks');
		$kodesiklus = $this->ks->as_array()->get_by(array('kode_farm' => $kodefarm, 'tgl_doc_in' => $tgldocin));
		$dataKirim = $this->db->select('f.tgl_kirim,fd.tgl_kebutuhan,fd.kode_barang,fd.jml_standar,fd.jml_forecast')
				->from('forecast f')
				->join('forecast_d fd','f.id = fd.forecast')
				->where(array('kode_siklus' => $kodesiklus['KODE_SIKLUS'],'kode_flok_bdy' => $kodesiklus['FLOK_BDY']))
				->order_by('fd.tgl_kebutuhan')
				->get()
				->result_array();
		if(!empty($dataKirim)){
			$this->result['status'] = 1;
			$this->result['content'] = $dataKirim;
		}

		echo json_encode($this->result);
	}

	public function approveKonfirmasiDOCIn(){
		$kodefarm = $this->input->post('kode_farm');
		$tgldocin = $this->input->post('tgl_docin');
		$this->load->model('forecast/m_cycle_state_transition','cst');
		$user_level = $this->session->userdata('level_user');
		$kode_siklus = $this->db->select('no_reg,kode_siklus,flok_bdy')->where(array('kode_farm'=>$kodefarm,'tgl_doc_in'=>$tgldocin))->get('kandang_siklus')->row_array();
		$max_stamp = $this->db->select_max('stamp')->where(array('cycle'=>$kode_siklus['kode_siklus'],'flock'=>$kode_siklus['flok_bdy']))->get_compiled_select('cycle_state_transition');
		$noreg_konfirmasi = $this->db->select('cycle,flock,noreg')->where(array('cycle'=>$kode_siklus['kode_siklus'],'flock'=>$kode_siklus['flok_bdy']))->where('stamp = ('.$max_stamp.')')->get('cycle_state_transition')->result_array();
		$this->db->trans_begin();
		$state = array('KD'=> 'P2', 'KDV' => 'RL');
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = $tgl->saatini;

		/* insert ke tabel cycle_state_transition */
		foreach($noreg_konfirmasi as $n){
			$z = array(
				'cycle' => $n['cycle'],
				'flock' => $n['flock'],
				'noreg' => $n['noreg'],
				'stamp' => $tglserver,
				'user'  => $this->_user,
				'state' => $state[$user_level]
			);
			$this->cst->insert($z);
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->result['message'] = 'Rencana kirim gagal disimpan';
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$periode_siklus= $this->db->select('periode_siklus')->where(array('kode_siklus'=>$kode_siklus['kode_siklus']))->get('m_periode')->row_array();
			$this->result['message'] = 'Perencanaan DOC In siklus '.$periode_siklus['periode_siklus'].' berhasil dilakukan';
		}

//		echo json_encode($this->result);
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));

	}
	public function approveRejectKonfirmasiDOCIn(){
		$kodefarm = $this->input->post('kode_farm');
		$tgldocin = $this->input->post('tgl_docin');
		$aksi = $this->input->post('aksi');
		$ket = $this->input->post('ket');
		$note = empty($ket) ? NULL : $ket;
		$this->load->model('forecast/m_cycle_state_transition','cst');
		$user_level = $this->session->userdata('level_user');
		$kode_siklus = $this->db->select('no_reg,kode_siklus,flok_bdy')->where(array('kode_farm'=>$kodefarm,'tgl_doc_in'=>$tgldocin))->get('kandang_siklus')->row_array();
		$max_stamp = $this->db->select_max('stamp')->where(array('cycle'=>$kode_siklus['kode_siklus'],'flock'=>$kode_siklus['flok_bdy']))->get_compiled_select('cycle_state_transition');
		$noreg_konfirmasi = $this->db->select('cycle,flock,noreg')->where(array('cycle'=>$kode_siklus['kode_siklus'],'flock'=>$kode_siklus['flok_bdy']))->where('stamp = ('.$max_stamp.')')->get('cycle_state_transition')->result_array();
		$this->db->trans_begin();
		$state = array(
			'approve' => array('KD'=> 'P2', 'KDV' => 'RL'),
			'reject' => array('KD'=> 'RJ', 'KDV' => 'RJ'),
		);
		$tgl = Modules::run('home/home/getDateServer');
		$tglserver = detikSetelah($tgl->saatini,2);

		/* insert ke tabel cycle_state_transition */
		$stateKonfirmasi = $this->autoApprove ? $state[$aksi]['KDV'] : $state[$aksi][$user_level];
		foreach($noreg_konfirmasi as $n){
			$z = array(
				'cycle' => $n['cycle'],
				'flock' => $n['flock'],
				'noreg' => $n['noreg'],
				'stamp' => $tglserver,
				'user'  => $this->_user,
				'state' => $stateKonfirmasi
			);
			if(!empty($note)){
				$z['note'] = $note;
			}
			$this->cst->insert($z);
		}

		if($aksi == 'approve'){
			if($user_level == 'KDV' || $this->autoApprove){
				/* ubah statusnya kode_siklus pada m_periode menjadi A */
				$this->load->model('forecast/m_periode','mp');
				$this->mp->update_by(array('kode_siklus'=>$kode_siklus['kode_siklus'],'status_periode'=>'N'),array('status_periode'=>'A'));
				/* ubah statusnya menjadi open pada kandang siklus */
				$this->load->model('forecast/m_kandang_siklus','ks');
				foreach($noreg_konfirmasi as $n){
					$cari = array('no_reg'=>$n['noreg'], 'status_siklus'=>'P');
					$update = array('status_siklus'=>'O');
					$this->ks->update_by($cari,$update);
				}
				
				$this->result['insert_pallet'] = 0;
			}
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->result['message'] = 'Rencana kirim gagal disimpan';
		}
		else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['autoApprove'] = $this->autoApprove;
			$periode_siklus= $this->db->select('periode_siklus')->where(array('kode_siklus'=>$kode_siklus['kode_siklus']))->get('m_periode')->row_array();
			if($aksi == 'approve'){
					$this->result['message'] = 'Perencanaan DOC In siklus '.$periode_siklus['periode_siklus'].' berhasil dilakukan';
			}
			else{
				$this->result['message'] = 'Perencanaan DOC In siklus '.$periode_siklus['periode_siklus'].' berhasil direject';
			}

			$this->result['content'] = convertElemenTglWaktuIndonesia($tglserver);
		}

		
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function listKonfirmasiDocin(){
		 $farm = $this->input->post('_farm');
		 $status = $this->input->post('_status');
		 $tanggal_cari = $this->input->post('_tanggal');
		 $custom_param = '';
		 if(!empty($tanggal_cari['operand'])){
 			switch($tanggal_cari['operand']){
 				case 'between':
 					$custom_param = 'cast('.$tanggal_cari['fieldname'].' as date) '.$tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\' and \''.$tanggal_cari['endDate'].'\'';
 					break;
 				case '<=':
 					$custom_param = 'cast('.$tanggal_cari['fieldname'].' as date) '.$tanggal_cari['operand'].' \''.$tanggal_cari['endDate'].'\'';
 					break;
 				case '>=':
 					$custom_param = 'cast('.$tanggal_cari['fieldname'].' as date) '.$tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\'';
 					break;
 			}
		}
		 $data['list_konfirmasi'] = $this->mf->list_kandang_approvalkadiv($farm,$status,$custom_param)->result_array();

		 $this->load->view('forecast/bdy/list_konfirmasi_docin',$data);
	}

	public function revisiDOCInKadiv(){
		$noreg = $this->input->post('noreg');
		$this->load->model('forecast/m_log_doc_in','mld');
		$docInAsal = $this->mld->as_array()->get_by(array('no_reg'=>$noreg, 'no_urut'=> 1));

		if(!empty($docInAsal)){
			$this->result['status'] = 1;
			$this->result['content'] = $docInAsal['BACKUP_TGL_DOC_IN'];
		}
	
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function plot_pakan_rencana_produksi(){
			$rp = $this->input->post('rp');
			$kode_pakan = $this->input->post('kode_pakan');
			$hp = $this->mf->plot_pakan_farm($rp,$kode_pakan)->result();
			$sudahPlot = 0;
			$sudahPlotPakanLolos = 0;
			if(!empty($hp)){
				foreach($hp as $h){
					$sudahPlot += $h->alokasi_pakan;
					$sudahPlotPakanLolos += $h->alokasi_pakan_lolos;
				}
			}
			$this->result['status'] = 1;
			$this->result['content'] = array('plotPakan' => $sudahPlot, 'plotPakanLolos'=> $sudahPlotPakanLolos);
			echo json_encode($this->result);
	}

	public function detail_rencana_produksi(){
		$rp = $this->input->post('rencana_produksi');
		$tabel = array();
		$tbody = array();
		$thead = array('Pakan','Total Produksi Pakan (sak)','Total Produksi Pakan Lolos QC (sak)');

		$rencana_produksi = $this->mf->detail_rencana_produksi($rp)->result();
		if(!empty($rencana_produksi)){
			foreach($rencana_produksi as $rps){
				$plus = '<i class="glyphicon glyphicon-plus-sign" onclick="Konfirmasi_rp.detailAlokasiPakanLolosQC(this,\''.$rp.'\',\''.$rps->pakan.'\')"></i>';
				$tr = array(
					'<tr>',
					'<td>'.$plus.' '.$rps->nama_barang.'</td>',
					'<td class="number">'.angkaRibuan($rps->total_produksi).'</td>',
					'<td class="number">'.angkaRibuan($rps->total_pakan_lolos).'</td>',
					'</tr>',
					'<tr class="detail_alokasi_lolos_pakan">',
					'<td colspan=3></td>',
					'</tr>'
				);
				array_push($tbody,implode('',$tr));
			}
		}
		array_push($tabel,'<thead><tr><th>'.implode('</th><th>',$thead).'</th></tr></thead>');
		array_push($tabel,'<tbody>'.implode('',$tbody).'</tbody>');
//		array_push($tabel,'<tfoot>'.implode('',$tfoot).'</tfoot>');
		echo '<table class="table table-bordered custom_table">'.implode('',$tabel).'</table>';
	}

	public function detail_alokasi_lolos_pakan(){
		$rp = $this->input->post('rencana_produksi');
		$pakan = $this->input->post('pakan');
		$tabel = array();
		$tbody = array();
		$tcaption = array('Alokasi Pakan Lolos QC');
		$thead = array('Tanggal Kirim','Pakan Lolos QC untuk Kebutuhan Farm(sak)');
	//	$tfoot = array('');
		$rencana_produksi = $this->mf->detail_alokasi_lolos_pakan($rp,$pakan)->result();
		if(!empty($rencana_produksi)){
			foreach($rencana_produksi as $rps){
				$tr = array(
					'<tr>',
					'<td class="number">'.tglIndonesia($rps->tanggal_kirim,'-',' ').'</td>',
					'<td class="number">'.angkaRibuan($rps->jumlah_sak).'</td>',
					'</tr>'
				);
				array_push($tbody,implode('',$tr));
			}
		}
		else{
			$tbody = array('<tr><td>Data tidak ditemukan </td></tr>');
		}
		array_push($tabel,'<caption>'.implode('</th><th>',$tcaption).'</caption>');
		array_push($tabel,'<thead><tr><th>'.implode('</th><th>',$thead).'</th></tr></thead>');
		array_push($tabel,'<tbody>'.implode('',$tbody).'</tbody>');
//		array_push($tabel,'<tfoot>'.implode('',$tfoot).'</tfoot>');
		echo '<table class="table table-bordered custom_table">'.implode('',$tabel).'</table>';
	}


		public function detail_riwayat_alokasi_lolos_pakan(){
			$tglkirim = $this->input->post('tglkirim');
			$pakan = $this->input->post('pakan');
			$tabel = array();
			$tbody = array();

			$thead = array('<tr>',
									'<th rowspan=2>Tgl Produksi</th>',
									'<th rowspan=2>Kode RP</th>',
									'<th colspan=3>Kebutuhan Farm</th>',
									'<th rowspan=2>Kelebihan Pakan (sak)</th>',
								'</tr>',
								'<tr>',
									'<th>Rencana Produksi (sak)</th>',
									'<th>Pakan Lolos QC (sak)</th>',
									'<th>Revisi (sak)</th>',
								'</tr>',
							);
		//	$tfoot = array('');
			$rencana_produksi = $this->mf->detail_riwayat_alokasi_lolos_pakan($tglkirim,$pakan)->result();
			if(!empty($rencana_produksi)){
				foreach($rencana_produksi as $rps){
					$revisi = empty($rps->sebelumnya) ? '-' : angkaRibuan($rps->revisi);
					$asli =  !empty($rps->sebelumnya) ? angkaRibuan($rps->sebelumnya) : angkaRibuan($rps->revisi);
					$selisih = !empty($rps->sebelumnya) ? ($rps->sebelumnya - $rps->revisi) : 0 ;
					$plus = '<i class="glyphicon glyphicon-plus-sign" onclick="Konfirmasi_rp.detailSisaPakanLolos(this,\''.$rps->rencana_produksi.'\',\''.$pakan.'\')"></i>';
					$tr = array(
						'<tr>',
						'<td>'.tglIndonesia($rps->tanggal_produksi,'-',' ').'</td>',
						'<td>'.$rps->rencana_produksi.'</td>',
						'<td class="number">'.angkaRibuan($rps->alokasi_pakan_untuk_farm).'</td>',
						'<td class="number">'.$asli.'</td>',
						'<td class="number">'.$revisi.'</td>',
						'<td class="">'.$plus.' '.$selisih.'</td>',
						'</tr>',
						'<tr class="detail_sisa_lolos_pakan">',
						'<td colspan=4></td>',
						'<td colspan=2></td>',
						'</tr>'
					);
					array_push($tbody,implode('',$tr));
				}
			}
			else{
				$tbody = array('<tr><td>Data tidak ditemukan </td></tr>');
			}

			array_push($tabel,'<thead>'.implode('',$thead).'</thead>');
			array_push($tabel,'<tbody>'.implode('',$tbody).'</tbody>');
	//		array_push($tabel,'<tfoot>'.implode('',$tfoot).'</tfoot>');
			echo '<table class="table table-bordered custom_table">'.implode('',$tabel).'</table>';
		}
	public function detail_sisa_lolos_pakan(){
		$rp = $this->input->post('rencana_produksi');
		$pakan = $this->input->post('pakan');
		$tabel = array();
		$tbody = array();
		$tcaption = array('Alokasi Kelebihan Pakan');
		$thead = array('<tr>',
								'<th>Tgl Kirim</th>',
								'<th>Digunakan sebanyak (sak)</th>',
							'</tr>',
						);
	//	$tfoot = array('');
		$rencana_produksi = $this->mf->detail_sisa_lolos_pakan($rp,$pakan)->result();
		if(!empty($rencana_produksi)){
			foreach($rencana_produksi as $rps){
				$tr = array(
					'<tr>',
					'<td>'.tglIndonesia($rps->tanggal_kirim,'-',' ').'</td>',
					'<td class="number">'.angkaRibuan($rps->jumlah_sak).'</td>'
				);
				array_push($tbody,implode('',$tr));
			}
		}
		else{
			$tbody = array('<tr><td colspan=2>Data tidak ditemukan </td></tr>');
		}
		array_push($tabel,'<caption>'.implode('</th><th>',$tcaption).'</caption>');
		array_push($tabel,'<thead>'.implode('',$thead).'</thead>');
		array_push($tabel,'<tbody>'.implode('',$tbody).'</tbody>');
//		array_push($tabel,'<tfoot>'.implode('',$tfoot).'</tfoot>');
		echo '<table class="table table-bordered custom_table">'.implode('',$tabel).'</table>';
	}

	private function getSiklusBdy($noreg){
		if(!empty($noreg)){
			$n = explode('/',$noreg);
			$siklus = explode('-',$n[1]);
			return $siklus[1];
		}
		else{
			return null;
		}

	}

	private function convertDateImport($tglStr){
		$n = explode('/',$tglStr);
		$m = array_reverse($n);
		return implode('-',$m);
	}

	/* generate pallet */
	private function generate_pallet($kodefarm,$kodesiklus){
		/* periksa apakah sudah ada atau belum di m_kavling*/
		$this->load->model('forecast/m_pallet','mpal');
		$ada = $this->mpal->count_by(array('kode_siklus' => $kodesiklus, 'kode_farm'=> $kodefarm));
		$result = 0;
		if(!$ada){
			/* cari data di m_kavling */
			$this->load->model('forecast/m_kavling','mkav');
			$kav = $this->mkav->get_many_by(array('status_kavling' => 'A', 'kode_farm'=> $kodefarm));
			if(!empty($kav)){
				/* buat query untuk insert ke m_pallet*/
				$data_insert = array();
				foreach($kav as $k){
					$jml_pallet = $k->JML_PALLET;
					$i = 1;
					while($i <= $jml_pallet){
						$kode_pallet = $k->NO_KAVLING.'-'.str_pad($i,2,'0',STR_PAD_LEFT);
						$d = array('kode_farm' => $kodefarm,'kode_siklus' => $kodesiklus,'kode_pallet' => $kode_pallet);
						array_push($data_insert,$d);
						$i++;
					}
				}
				$result = $this->mpal->insert_many($data_insert);
			}
		}
		return $result;
	}

	public function getPegawai(){
		$pelanggan = $this->mf->get_pegawai_browse();
		//kode_pelanggan, nama_pelanggan, alamat, kota, no_telp, term_pembayaran
		$return = array();
		foreach ($pelanggan as $key => $val) {
			$return[] = array(
				'id' => $val['kode_pegawai'],
				'name' => $val['nama_pegawai']
			);
		}

		echo json_encode($return);
	}
	public function getPegawaiAktivasi(){
		$pengawas = $this->mf->get_pegawai_aktivasi();
		//kode_pelanggan, nama_pelanggan, alamat, kota, no_telp, term_pembayaran
		// $return = array();
		foreach ($pengawas as $key => $val) {
			$result = array(
				'pengawas1' 	=> $val['pengawas1'],
				'nik_pengawas1' => $val['nik_pengawas1'],
				'pengawas2' 	=> $val['pengawas2'],
				'nik_pengawas2' => $val['nik_pengawas2'],
			);
		}
		echo json_encode($result);
	}
	public function cekPegawai(){
		$result = array();
		$nik1 = $this->input->post('nik1');
		$nik2 = $this->input->post('nik2');

		$pegawai1 = $this->mf->get_pegawai($nik1);
		$pegawai2 = $this->mf->get_pegawai($nik2);
		// cetak_r(empty($pegawai2));
		if(empty($pegawai1)){
			$result['success'] = false;
			$result['message'] = "Data Pegawai tidak ditemukan! Mohon input ulang kolom pengawas 1";
		}elseif(empty($pegawai2)){
			$result['success'] = false;
			$result['message'] = "Data Pegawai tidak ditemukan! Mohon input ulang kolom pengawas 2";
		}else {
			$result['success'] = true;
		}
		echo json_encode($result);
	}

	public function getFlokKandang(){
		$noreg = $this->input->get('no_reg');
		$result = $this->db->distinct()->select('flok_bdy,tgl_doc_in,tgl_panen')
				->where('kode_siklus = (select kode_siklus from kandang_siklus where no_reg = \''.$noreg.'\') and flok_bdy != (select flok_bdy from kandang_siklus where no_reg = \''.$noreg.'\')')
				->get('kandang_siklus')
				->result_array();
		
		echo json_encode(
			array('content' =>array('flok' => $result))	  
		);
	}

	public function updateFlokNoreg(){
		$this->load->model('forecast/m_kandang_siklus','ks');
		$noreg = $this->input->post('no_reg');
		$flok_bdy = $this->input->post('flok_bdy');
		$tgl_doc_in = $this->input->post('tgl_doc_in');
		$tgl_panen = $this->input->post('tgl_panen');
		$update = $this->ks->update($noreg,array('flok_bdy' => $flok_bdy, 'tgl_doc_in' => $tgl_doc_in,'tgl_panen' => $tgl_panen));
		if($update){
			$this->result['status'] = 1;
			$this->result['message'] = 'Tanggal DOC In berhasil diubah';
		}		
		$this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));

	}
}
