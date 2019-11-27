<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Penerimaan_retur_pakan_farm extends MY_Controller{
	protected $grup_farm;
	private $tombol;
	private $akses;
	protected $_farm;
	function __construct(){
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');		
		$this->load->helper('stpakan');
		$this->load->config('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));	
		$this->_farm = $this->session->userdata('kode_farm');
		$this->_berat = $this->config->item('berat_standart');
		$this->load->model('rekap_retur_pakan/m_retur_farm','rf');
		$this->load->model('rekap_retur_pakan/m_retur_farm_d','rfd');
		$this->load->model('rekap_retur_pakan/m_log_retur_farm','lrf');
		$this->load->model('penerimaan_pakan/m_transaksi', 'm_transaksi');
		$this->load->model('rekap_retur_pakan/m_penerimaan_retur_farm', 'prf');
	}
	
	function index(){
		$farm = NULL;
		$kodefarm = $this->session->userdata('kode_farm');
		$user_level = $this->session->userdata('level_user');
		$list_farm = array(
			'KF' => $kodefarm,
			'AG' => $kodefarm,			
			'KDV' => $farm,
			'KD' => $farm,			
		);
		$akses = array(	
			'KF' => array($this->tombol['create'],$this->tombol['update']),	
			'AG' => '',								
			'KD' => array($this->tombol['review'],$this->tombol['reject']),			
			'KDV' => array($this->tombol['approve'],$this->tombol['reject'])			
		);
		//$data['list_farm'] = empty($list_farm[$user_level]) ? '' : Modules::run('forecast/forecast/list_farm',$this->grup_farm,$list_farm[$user_level]);
		$data['all_farm'] = $this->dropdownFarm();
		$viewPage = 'rekap_retur_pakan/penerimaan_retur_pakan_farm/index';
		$this->load->view($viewPage,$data);				
	}
	
	public function tabel_penerimaan_pakan(){
		$user_level = $this->session->userdata('level_user');
		$belumTindakLanjut = $this->input->get('belumTindakLanjut'); 
		$farmAsal = $this->input->get('farmAsal');
		$startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		$noSJ = $this->input->get('no_sj');
		
		$parameter = array(
			'belumTindakLanjut' => $belumTindakLanjut, 			
		);
		
		if(!empty($farmAsal)){
			$parameter['farm_asal'] = $farmAsal;
		}
		
		$tglKirim = array(
			'startDate' => $startDate,
			'endDate' => $endDate
		);
		if(!empty($noSJ)){
			array('NO_RETUR' => $noSJ);
		}
		//$data['terima'] = $this->getListRetur($parameter,$tglKirim);
		$data['terima'] = array();
		$dataretur = $this->getListRetur($parameter, $tglKirim);
		foreach($dataretur as $dretur){
			$thisData = array(
				'NO_RETUR' 		=> $dretur['NO_RETUR'],
				'FARM_ASAL' 	=> $dretur['FARM_ASAL'],
				'TGL_KIRIM' 	=> convertElemenTglIndonesia($dretur['TGL_KIRIM']),
			);
			array_push($data['terima'], $thisData);
		}
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm'); 
		$this->load->view('rekap_retur_pakan/penerimaan_retur_pakan_farm/list_penerimaan',$data);
	}
	
	public function detail_sj(){
		$noRetur = $this->input->get('no_retur');
		//$data['surat_jalan'] = $this->rf->as_array()->get($noRetur);
		$detailsj = $this->rf->as_array()->get($noRetur);
		$data['surat_jalan'] = array(
				'NOPOL'			=> $detailsj['NOPOL'],
				'NO_RETUR' 		=> $detailsj['NO_RETUR'],
				'FARM_ASAL' 	=> $detailsj['FARM_ASAL'],
				'TGL_KIRIM' 	=> convertElemenTglIndonesia($detailsj['TGL_KIRIM']),
				'TGL_TERIMA'	=> convertElemenTglIndonesia(date('Y-m-d')),
			);
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm');
		$data['list_kendaraan'] = $this->listNopol();
		$this->load->view('rekap_retur_pakan/penerimaan_retur_pakan_farm/tabel_sj_retur', $data);
	}
	
	/*set tabel alokasi penerimaan retur*/
	public function alokasi_penerimaan_retur(){
		$no_retur = $this->input->get('no_retur');
		$sql = <<<SQL
				select rf.NO_RETUR , md.KODE_BARANG, md.JML_PICK, mb.NAMA_BARANG, mb.BENTUK_BARANG from retur_farm rf
	join movement_d md on rf.NO_RETUR = md.NO_REFERENSI
	join m_barang mb on mb.KODE_BARANG = md.KODE_BARANG
	where rf.NO_RETUR = '{$no_retur}'
SQL;
		$kode_farm = $this->_farm;
		$data['detail_pallet'] = $this->db->query($sql)->result_array();
		$data['kodefarm'] = $kode_farm;
		$data['mpallet'] = arr2DToarrKey($this->get_m_pallet(), 'kode_pallet');
		$data['kode_siklus'] =$this->db->select('KODE_SIKLUS')->get_where('M_PERIODE', array('KODE_FARM' => $kode_farm, 'STATUS_PERIODE'=>'A'))->result_array();
		$data['hpallet'] = $this->db->get_where('M_HAND_PALLET', array('STATUS_PALLET'=>'N', '_DEFAULT'=>'1', 'KODE_FARM'=>$this->_farm))->result_array();
		$data['kandang'] = $this->prf->kandang_farm($kode_farm);
		$this->load->view('rekap_retur_pakan/penerimaan_retur_pakan_farm/alokasi_penerimaan_retur', $data);
	}
	/*end set tabel alokasi retur*/
	
	/*get list kandang*/
	public function get_kandang_farm(){
		$farm = $this->_farm;
		$return = $this->prf->kandang_farm($farm);
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	/*end get list kandang*/
	
	public function pakan_rusak_hilang(){
		$this->load->view('rekap_retur_pakan/penerimaan_retur_pakan_farm/pakan_rusak_hilang');
	}
	
	/*ganti hand pallet*/
	public function ganti_hand_pallet(){
		//$data['data_ke_detail_pakan'] = $this->input->post('data_ke_detail_pakan');
        $data['data_ke_detail_pakan'] = '123';
		//$data['data_ke_detail'] = $this->input->post('data_ke_detail');
        $data['data_ke_detail'] = '123';
		$data['data_hand_pallet'] = $this->m_transaksi->ganti_hand_pallet($this->_farm);
        $this->load->view('rekap_retur_pakan/penerimaan_retur_pakan_farm/ganti_hand_pallet',$data);
    }
	/*end ganti hand pallet*/
	
	/*ganti pallet*/
	public function ganti_pallet(){
		$data['kavling'] = $this->prf->ganti_pallet($this->_farm, '1127-10-12');
		$this->load->view('rekap_retur_pakan/penerimaan_retur_pakan_farm/ganti_pallet', $data);
	}
	/*end ganti pallet*/
	
	/*get kavling*/
	public function set_default_kavling(){
		$kode_farm = $this->_farm;
		$kode_kandang = $this->input->post('kode_kandang'); 
		$kode_siklus = $this->input->post('kode_siklus');
		$kode_flok = $this->input->post('kode_flok');
		$kode_pakan = $this->input->post('kode_pakan');
		
		$sql = <<<SQL
			select top(1) m.KODE_PALLET kode_kavling, mp.brt_bersih brt_pallet, mp.no_kavling no_kavling, mhp.brt_bersih brt_hand_pallet 
            from MOVEMENT m
			join m_pallet mp on m.kode_pallet = mp.kode_pallet and mp.STATUS_PALLET = 'N'
			join m_hand_pallet mhp on m.kode_farm = mhp.kode_farm and mhp.status_pallet = 'N' and mhp._default = '1'
            where KETERANGAN1 = '{$kode_flok}'
            and m.KODE_FARM = '{$kode_farm}'
            and kode_barang = '{$kode_pakan}'
            and no_pallet like 'SYS%'
            and JML_AVAILABLE > 0
            order by m.KODE_PALLET ASC
SQL;
		$return = $this->db->query($sql)->result_array();
		$this->output->set_content_type('application/json')->set_output(json_encode($return));
	}
	/*end get kavling*/

	/*public function alokasi_retur(){
		$kodefarm = $this->_farm;
		$noRetur = $this->input->get('no_retur');
		$data['listPakanSisa'] = $this->sisaPakan($kodefarm);		
		$data['tombol'] = array($this->tombol['kembali']);	
		$idFarmTujuan = NULL;
		$data['tglKirim'] = date('Y-m-d');
		if(!empty($noRetur)){
			$data['retur'] = $this->rf->as_array()->get($noRetur);
			$data['logRetur'] = $this->lrf->as_array()->order_by('no_urut','desc')->limit(1)->get_by(array('no_retur' => $noRetur));
			$idFarmTujuan = $data['retur']['FARM_TUJUAN'];
			if($data['retur']['TGL_KIRIM'] >= $data['tglKirim']){
				$data['tglKirim'] = $data['retur']['TGL_KIRIM'];
			}	
			$data['listPakan'] = arr2DToarrKey($this->rfd->as_array()->get_many_by(array('no_retur'=>$noRetur)),'KODE_PAKAN');					
		}
		$data['kode_siklus'] = '123';
		//$data['farmTujuan'] = $this->dropdownFarm($idFarmTujuan, array($kodefarm));
		$this->load->view('rekap_retur_pakan/retur_pakan_farm/alokasi_retur',$data);
	}*/

	/*public function detail_pengiriman_retur(){
		$noRetur = $this->input->get('no_retur');
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm');
		$data['listPakanSisa'] = $this->rfd->listPakan($noRetur);
		$data['retur'] = $this->rf->as_array()->get($noRetur);
		$data['listStatus'] = $this->listStatus();
		$data['logRetur'] = $this->lrf->history($noRetur);
		$this->load->view('rekap_retur_pakan/retur_pakan_farm/detail_retur',$data);
	}*/

	
	
	/*simpan penerimaan retur farm*/
	private function generate_no_pallet($flok, $kode_barang){
		$kode_farm = $this->_farm;
		/*$sql = <<<SQL
			EXEC [GENERATE_NO_KAVLING] '{$kode_farm}','{$flok}',NULL,NULL,'{$kode_barang}','{$kode_barang}'
SQL;*/
		$sql = <<<SQL
			SELECT 'SYS'+ISNULL(RIGHT('00000000'+ISNULL(CAST(SUBSTRING(MAX(NO_PALLET),4,8)+1 AS VARCHAR(8)),'1'),8), 'SYS00000001') no_pallet
			from MOVEMENT 
			WHERE KODE_FARM = '{$kode_farm}' and kode_barang = '{$kode_barang}' and keterangan1 = '{$flok}'
				AND left(NO_PALLET,3) = 'SYS'
SQL;
		$result = $this->db->query($sql)->row();
		return $result->no_pallet;
	}
	
	public function simpan(){
		$kodefarm = $this->_farm;
		$sql = <<<SQL
			select RIGHT('00000000' + CAST(CONVERT(INT, ISNULL(MAX(no_penerimaan), '0000')) + 1 as varchar), 8) no_penerimaan from penerimaan where KODE_FARM = '{$kodefarm}' 
SQL;
		$generate = $this->db->query($sql)->row();
		if(count($generate)>0){
			$flok = 2;
			$kodepakan = '1127-10-12';
			$no_pallet = $this->generate_no_pallet($flok, $kodepakan);
			$data = array(
				'no_retur'			=> 'Rl/',
				'sopir'				=> 'Wagiman',
				'noplat_kirim'		=> 'N 5150 AO',
				'noplat_terima'		=> 'N 5150 AO',
				'kodepallet'		=> 'A1-01-01',
				'beratpallet'		=> 11.8,
				'no_kavling'		=> 'A1-01',
				'flok'				=> $flok,
				'qty_berat'			=> 350,
				'qty_sak'			=> 5,
				'qty_berat_rusak'	=> 0,
				'qty_sak_rusak'		=> 0,
				'datetime_now'		=> date('Y-m-d H:i:s'),
				'date_now'			=> date('Y-m-d'),
				'kodefarm'			=> $kodefarm,
				'no_penerimaan'		=> $generate->no_penerimaan,
				'kodepakan'			=> $kodepakan,
				'no_pallet'			=> $no_pallet,
				'kandang'			=> '01',
				'user'				=> $this->session->userdata('kode_user')
			);
			$this->insert_penerimaan($data);
		}
	}
	
	private function insert_penerimaan($data){
		$data_insert = array(
			'NO_PENERIMAAN' 		=> $data['no_penerimaan'],
			'KODE_FARM'				=> $data['kodefarm'],
			'NAMA_SOPIR'			=> $data['sopir'],
			'NO_KENDARAAN_KIRIM'	=> $data['noplat_kirim'],
			'NO_KENDARAAN_TERIMA'	=> $data['noplat_terima'],
			'NO_SPM'				=> '',
			'TGL_TERIMA'			=> $data['datetime_now'],
			'KETERANGAN1'			=> $data['no_retur'],
			'STATUS_TERIMA'			=> 'N',
			'TGL_BUAT'				=> $data['datetime_now'],
			'TGL_UBAH'				=> $data['datetime_now'],
			'USER_BUAT'				=> $data['user'],
			'USER_UBAH'				=> $data['user'],
			'KUANTITAS_KG'			=> $data['qty_berat'],
			'KUANTITAS_SAK'			=> $data['qty_sak'],
		);
		
		$this->db->trans_begin();
		$this->db->insert('PENERIMAAN', $data_insert);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			echo "<br>insert penerimaan rollback";
		}else{
			$this->db->trans_commit();
			echo "<br>insert penerimaan success";
			$this->insert_penerimaan_d($data);
		}
	}
	private function insert_penerimaan_d($data){
		$data_insert = array(
			'KODE_FARM'			=> $data['kodefarm'],
			'NO_PENERIMAAN'		=> $data['no_penerimaan'],
			'KODE_BARANG'		=> $data['kodepakan'],
			'JML_SJ'			=> $data['qty_berat'],
			'JML_TERIMA'		=> $data['qty_sak'],
			'JML_RUSAK'			=> $data['qty_sak_rusak'],
			'JML_KURANG'		=> 0,
			'BERAT_TERIMA'		=> $data['qty_berat'],
			'BERAT_RUSAK'		=> $data['qty_berat_rusak'],
			'JML_PUTAWAY'		=> 0,
			'TGL_BUAT'			=> $data['date_now'],
			'TGL_UBAH'			=> $data['datetime_now'],
			'USER_BUAT'			=> $data['user'],
			'USER_UBAH'			=> $data['user'],
		);
		
		$this->db->trans_begin();
		$this->db->insert('PENERIMAAN_D', $data_insert);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			echo "<br>insert penerimaan_d rollback";
		}else{
			$this->db->trans_commit();
			echo "<br>insert penerimaan_d success";
			$this->insert_penerimaan_e($data);
		}
	}
	
	private function insert_penerimaan_e($data){
		$data_insert = array(
			'NO_PALLET'			=> $data['no_pallet'],
			'KODE_FARM'			=> $data['kodefarm'],
			'NO_PENERIMAAN'		=> $data['no_penerimaan'],
			'KODE_BARANG'		=> $data['kodepakan'],
			'JUMLAH'			=> $data['qty_sak'],
			'BERAT'				=> $data['qty_berat'],
			'STATUS_STOK'		=> 'NM'
		);
		$this->db->trans_begin();
		$this->db->insert('PENERIMAAN_E', $data_insert);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			echo "<br>insert penerimaan_e rollback";
		}else{
			$this->db->trans_commit();
			echo "<br>insert penerimaan_e success";
			$this->insert_mv($data);
		}
	}
	
	private function insert_mv($data){
		$data_insert = array(
			'KODE_FARM'				=> $data['kodefarm'],
			'NO_KAVLING'			=> $data['no_kavling'],
			'NO_PALLET'				=> $data['no_pallet'],
			'KODE_BARANG'			=> $data['kodepakan'],
			'JENIS_KELAMIN'			=> 'C',
			'JML_ON_HAND'			=> $data['qty_sak'],
			'JML_AVAILABLE'			=> $data['qty_sak'],
			'BERAT_AVAILABLE'		=> $data['qty_berat'],
			'JML_PUTAWAY'			=> $data['qty_sak'],
			'BERAT_PUTAWAY'			=> $data['qty_berat'],
			'PUT_DATE'				=> $data['datetime_now'],
			'PUT_NAME'				=> $data['user'],
			'STATUS_STOK'			=> 'NM',
			'KETERANGAN1'			=> $data['flok'],
			'KETERANGAN2'			=> 'tes penerimaan',
			'BERAT_PALLET'			=> $data['beratpallet'],
			'KODE_PALLET'			=> $data['kodepallet']
		);
		
		$this->db->trans_begin();
		$this->db->insert('MOVEMENT', $data_insert);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			echo "<br>insert movement rollback";
		}else{
			$this->db->trans_commit();
			echo "<br>insert movement success";
			$this->insert_mvd($data);
		}
	}
	
	private function insert_mvd($data){
		$data_insert = array(
			'KODE_FARM'			=> $data['kodefarm'],
			'NO_KAVLING'		=> $data['no_kavling'],	
			'NO_PALLET'			=> $data['no_pallet'],
			'KODE_BARANG'		=> $data['kodepakan'],
			'JENIS_KELAMIN'		=> 'C',
			'NO_REFERENSI'		=> $data['no_penerimaan'],
			'JML_ON_HAND'		=> $data['qty_sak'],
			'JML_AVAILABLE'		=> $data['qty_sak'],
			'BERAT_AVAILABLE'	=> $data['qty_berat'],
			'JML_PUTAWAY'		=> $data['qty_sak'],
			'BERAT_PUTAWAY'		=> $data['qty_berat'],
			'PUT_DATE'			=> $data['datetime_now'],
			'PUT_NAME'			=> $data['user'],
			'STATUS_STOK'		=> 'NM',
			'KETERANGAN1'		=> 'PUT',
			'KETERANGAN2'		=> 'JD/2018-2/01',
			'KODE_PALLET'		=> $data['kodepallet']
		);
		
		$this->db->trans_begin();
		$this->db->insert('MOVEMENT_D', $data_insert);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			echo "<br>insert movement_d rollback";
		}else{
			$this->db->trans_commit();
			echo "<br>insert movement_d success";
			$this->insert_order_kandang($data);
		}
	}
	
	private function insert_order_kandang($data){
		$sql = <<<SQL
			declare @max_no_order varchar(11) = (select MAX(no_order) from order_kandang where kode_farm = 'JD') 
			select SUBSTRING(@max_no_order,0,8) + RIGHT('0000'+ISNULL(CAST(SUBSTRING(MAX(@max_no_order),9,12)+1 AS VARCHAR(4)),'1'),4)  no_order
SQL;

		$no_order_row = $this->db->query($sql)->row();
		$data['no_order'] = $no_order_row->no_order;
		
		$data_insert = array(
			'KODE_FARM'		=> $data['kodefarm'],
			'NO_ORDER'		=> $data['no_order'],
			'NO_REFERENSI'	=> $data['no_retur'],
			'TGL_KIRIM'		=> $data['date_now'],
			'TGL_KEB_AWAL'	=> $data['date_now'],
			'TGL_KEB_AKHIR'	=> $data['date_now'],
			'STATUS_ORDER'	=> 'N',
			'TGL_BUAT'		=> $data['date_now'],
			'USER_BUAT'		=> $data['user']
		);
		
		$this->db->trans_begin();
		$this->db->insert('ORDER_KANDANG', $data_insert);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			echo "<br>insert order_kandang rollback";
		}else{
			$this->db->trans_commit();
			echo "<br>insert order_kandang success";
			$this->insert_order_kandang_d($data);
		}
	}
	
	private function insert_order_kandang_d($data){
		$kodefarm = $data['kodefarm'];
		$sql_periode_siklus = <<<SQL
			select periode_siklus from m_periode where kode_farm = '{$kodefarm}' and status_periode = 'A'
SQL;
		$row_periode_siklus = $this->db->query($sql_periode_siklus)->row();
		$data['noreg'] = $kodefarm.'/'.$row_periode_siklus->periode_siklus.'/'.$data['kandang'];
		$noreg = $data['noreg'];
		
		$sql_tgl_rhk = <<<SQL
			select max(TGL_TRANSAKSI) tgl_rhk from RHK where no_reg = '{$noreg}'
SQL;
		$row_tgl_rhk = $this->db->query($sql_tgl_rhk)->row();
		$tgl_rhk = $row_tgl_rhk->tgl_rhk;
		
		$data_insert = array(
			'NO_ORDER'		=> $data['no_order'],
			'NO_REG'		=> $data['noreg'],
			'KODE_FARM'		=> $data['kodefarm'],
			'TGL_LHK'		=> $tgl_rhk,
			'UMUR'			=> 18,
			'STATUS_ORDER'	=> 'N',
			'TGL_BUAT'		=> $data['datetime_now'],
			'USER_BUAT'		=> $data['user']
		);
		
		$this->db->trans_begin();
		$this->db->insert('ORDER_KANDANG_D', $data_insert);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			echo "<br>insert order_kandang_d rollback";
		}else{
			$this->db->trans_commit();
			$this->insert_order_kandang_e($data);
			echo "<br>insert order_kandang_d success";
		}
	}
	
	private function insert_order_kandang_e($data){
		$data_insert = array(
			'NO_ORDER'			=> $data['no_order'],
			'NO_REG'			=> $data['noreg'],
			'KODE_BARANG'		=> $data['kodepakan'],
			'TGL_KEBUTUHAN'		=> $data['date_now'],
			'JENIS_KELAMIN'		=> 'N',
			'JML_ORDER'			=> $data['qty_sak'],
			'JML_STOK_AKHIR'	=> $data['qty_sak']
		);
		
		$this->db->trans_begin();
		$this->db->insert('ORDER_KANDANG_E', $data_insert);
		if($this->db->trans_status() == false){
			echo "<br>insert movement_d rollback";
			$this->db->trans_rollback();
		}else{
			$this->db->trans_commit();
			echo "<br>insert order_kandang_e success";
		}
	}
	/*end simpan penerimaan retur farm*/

	
	public function updateStatus(){		
		$user_buat = $this->session->userdata('kode_user');
		$noRetur = $this->input->post('no_retur'); 
		$kdStatus = $this->input->post('kd_status');
		$keterangan = $this->input->post('keterangan');

		$nextApproveStatus = array(
			'N' => 'RV',
			'RV' => 'A'
		);
		$nextRejectStatus = array(
			'N' => 'RJ1',
			'RV' => 'RJ2'
		);
		$nexStatus = !empty($keterangan) ? $nextRejectStatus : $nextApproveStatus;
		$this->db->trans_begin();
		$this->rf->update($noRetur,array('status' => $nexStatus[$kdStatus]));								
		/** insert log */
		$logRetur = array(
			'no_retur' => $noRetur,					
			'status' => $nexStatus[$kdStatus],
			'user_buat' => $user_buat,			
		);
		if(!empty($keterangan)){
			$logRetur['keterangan'] = $keterangan;
		}
		$this->lrf->insert($logRetur);
		if ($this->db->trans_status() === FALSE )
		{
			$this->db->trans_rollback();						
			$this->result['message'] = 'Penyimpanan gagal';
		}
		else{
			$this->db->trans_commit();
			$listStatus = $this->listStatus();
			$labelStatus = strtolower($listStatus[$nexStatus[$kdStatus]]);
			$this->result['status'] = 1;
			$this->result['message'] = 'Pengajuan retur no. '.$noRetur.' berhasil di'.$labelStatus;
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));

	}
	
	public function check_admin_verifikator(){
		$date_trans = $this->input->post('date_transaction');
		$result = array('status'=>'0', 'match'=>'0');
		$getVerify = $this->db->where(array('date_transaction'=>$date_trans));
		$getVerify = $this->db->get('fingerprint_verification')->result_array();
		if(count($getVerify)>0){
			$verificator = $getVerify[0]['verificator'];
			if(!empty($verificator)){
				$result['status'] = '1';
				$sql = <<<SQL
					select pd.kode_farm, pd.kode_pegawai, mp.nama_pegawai from pegawai_d pd 
					join m_pegawai mp on mp.kode_pegawai = pd.kode_pegawai 
					where mp.grup_pegawai = 'AGF' and pd.kode_pegawai = '{$verificator}'
SQL;
				$adminCheck = $this->db->query($sql)->result_array();
				if(count($adminCheck)>0){
					if($this->_farm == $adminCheck[0]['kode_farm']){
						$result['match'] = '1';
						$result['nama_pegawai'] = $adminCheck[0]['nama_pegawai'];
					}
				}
			}
		}else{
			$result['status'] = '0';
		}
		
		echo json_encode($result);
	}

	//buat tes
	public function set_detail_pallet(){
		$sql = <<<SQL
			select * from movement where NO_PALLET >= (
		select min(no_pallet) from movement_d where kode_farm = 'BW' and KETERANGAN1 = 'PUT' and KETERANGAN2 = 'BW/2018/4/01'
		)) and kode_farm = 'BW' and JML_ON_HAND > 0 order by kode_pallet ASC 
SQL;
	
		$data = $this->db->query($sql)->result_array();
		$echo_kode_pallet = '';
		$last_kode_pallet = '';
		$temp_jumlah = 0;
		
		foreach($data as $pallet){
			$temp_jumlah += $pallet['JML_ON_HAND']; 
			if($last_kode_pallet != $pallet['KODE_PALLET']){
				echo $pallet['KODE_PALLET'];
				$last_kode_pallet = $pallet['KODE_PALLET'];
				echo ' - '.$temp_jumlah;
				echo "<br>";
				$temp_jumlah = 0;
			}
		}
	}
	//end buat tes
	
	/*get m_pallet*/
	private function get_m_pallet($kodepallet = NULL){
		$farm = $this->_farm;
		$q = $this->db->select(array('kode_pallet', 'brt_bersih'))->where(array('kode_farm'=>$farm, 'status_pallet'=>'N'));
		if(!empty($kodepallet)){
			$q->where('kode_pallet',$kodepallet);
		}
		return $q->get('M_PALLET')->result_array();
	}
	/*end get m_pallet*/
	
	public function list_retur_timbang(){
		$belumTindakLanjut = $this->input->get('belumTindakLanjut');
		$kodefarm = $this->session->userdata('kode_farm');		
		$farmTujuan = $this->input->get('farmTujuan');
		$startDate = $this->input->get('startDate');
		$endDate = $this->input->get('endDate');
		$parameter = array(
			'belumTindakLanjut' => $belumTindakLanjut, 			
		);
		$parameter['rf.farm_asal'] = $kodefarm;				
		if(!empty($farmTujuan)){
			$parameter['rf.farm_tujuan'] = $farmTujuan;
		}
		$parameter['farm_asal'] = $parameter['farm_asal'];
		$parameter['farm_tujuan'] = $parameter['farm_tujuan'];
		$tglKirim = array(
			'startDate' => $startDate,
			'endDate' => $endDate
		);
		$data['returs'] = $this->rf->listReturTimbang($parameter,$tglKirim);				
		$data['farm'] = arr2DToarrKey($this->listFarm(),'kode_farm');
		$this->load->view('rekap_retur_pakan/retur_pakan_farm/list_retur_timbang',$data);
	}
	private function getListRetur($parameter,$tglKirim){ 
		if($parameter['belumTindakLanjut']){
			$this->db->where_in('status', 'A');
		}else{
			unset($parameter['belumTindakLanjut']);
			$this->db->where($parameter);
		}
		$this->db->where(array('SOPIR != ' => '', 'NOPOL != ' => '', 'FARM_TUJUAN' => $this->_farm));
		if(!empty($tglKirim['startDate'])){
			if(!empty($tglKirim['endDate'])){
				$this->db->where('tgl_kirim between \''.$tglKirim['startDate'].'\' and \''.$tglKirim['endDate'].'\'');
			}else{
				$this->db->where('tgl_kirim >=\''.$tglKirim['startDate'].'\'');
			}
		}else{
			if(!empty($tglKirim['endDate'])){
				$this->db->where('tgl_kirim <=\''.$tglKirim['endDate'].'\'');
			}
		}

		return $this->rf->as_array()->get_all();		
	}
	
	
	/* sisa pakan adalah pakanReturGudang - jml yang akan diretur namun belum dilakukan proses generate dan statusnya bukan void
	*/		
	private function sisaPakan($kodefarm){
		$pakanReturGudang = $this->rf->getSisaPakan($kodefarm);
		$periodeAktif = $this->db->select('periode_siklus')->where(array('kode_farm' => $kodefarm,'status_periode'=>'A'))->get('M_PERIODE')->row_array();		
		if(!empty($periodeAktif) && !empty($pakanReturGudang)){			
			$pengurangPakan = $this->rf->pengurangPakan($kodefarm,$periodeAktif['periode_siklus']);			
			if(!empty($pengurangPakan)){
				$pengurangPakan = arr2DToarrKey($pengurangPakan,'kode_pakan');			
			}			
		}
		
		if(!empty($pakanReturGudang)){
			$pakanReturGudang = arr2DToarrKey($pakanReturGudang,'kode_pakan');
			if(!empty($pengurangPakan)){
				foreach($pakanReturGudang as $kp => &$pr){					
					if(isset($pengurangPakan[$kp])){
						$pr['jumlah'] -= $pengurangPakan[$kp]['jumlah'];
						$pr['berat'] -= $pengurangPakan[$kp]['jumlah'] * 50;
					}					
				}
			}
		}
		return $pakanReturGudang;
	}	

	public function generate(){
		$kodeFarm = $this->session->userdata('kode_farm');		
		$noRetur = $this->input->get('no_retur');
		$result = $this->rf->generate($kodeFarm,$noRetur);
		$this->result['status'] = $result->result == 1 ? 1 : 0;		
		$this->result['message'] = $result->result ? 'Generate pengiriman berhasil' : $result->pesan;
		if($result->result){
			$this->result['content'] = array('no_pengiriman' => $noRetur, 'jml_kebutuhan' => $result->total);
		}
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	
	public function timbang(){
		$kodeFarm = $this->session->userdata('kode_farm');
		$noRetur = $this->input->get('no_retur');
		$noReg = str_replace('RL/', '', $noRetur); echo $noReg;
		$data = array( 
			'detail' => simpleGrouping($this->rf->detailTimbang($noRetur,$kodeFarm),'KODE_BARANG'),
			'lockTimbangan' => $this->config->item('lockTimbangan')
		);
		$this->load->view('rekap_retur_pakan/pengiriman_retur_pakan_farm/detail_timbang',$data);
	}
	
	public function tabel_timbang(){
		$no_retur = $this->input->get('no_retur'); 
		$kode_pakan = $this->input->get('kode_pakan');
		$kirim = $this->input->get('kirim');
		$noExp = explode('/', $no_retur);
		$kode_farm = $noExp[1];
		$no_reg = substr($no_retur, 3, strlen($no_retur));
		switch($kirim){
		case 0:	
		$sql = <<<SQL
			select m.*, mb.NAMA_BARANG from movement m
	join M_barang mb on mb.kode_barang = m.kode_barang 
	where NO_PALLET >= (
		select min(no_pallet) from movement_d where kode_farm = '{$kode_farm}' 
		and KETERANGAN1 = 'PUT' and KETERANGAN2 in (
			select no_reg from KANDANG_SIKLUS where STATUS_SIKLUS = 'O' and no_reg = '{$no_reg}'
		)
		) and kode_farm = '{$kode_farm}' and JML_ON_HAND > 0 and m.kode_barang = '{$kode_pakan}' order by kode_barang ASC, kode_pallet ASC 

SQL;
		break;
		case 1:
		$sql = <<<SQL
				select md.*, mb.NAMA_BARANG, mp.NAMA_PEGAWAI from movement_d md 
				join M_BARANG mb on md.KODE_BARANG = mb.KODE_BARANG
				join M_PEGAWAI mp on md.PICKED_NAME = mp.KODE_PEGAWAI
				where NO_REFERENSI = '{$no_retur}' and KODE_FARM = '{$kode_farm}'
				order by KODE_BARANG ASC, KODE_PALLET ASC
SQL;
		break;
		}

		$dataMovement = $this->db->query($sql)->result_array();
		$data = array( 
			'detail' 		=> $dataMovement,
			'lockTimbangan' => $this->config->item('lockTimbangan'),
			'kirim'			=> $kirim 
		);
		
		$this->load->view('rekap_retur_pakan/pengiriman_retur_pakan_farm/tabel_timbang_pallet', $data);
	}
	
	//simpan pengiriman retur
	/*public function simpan_pengiriman(){
		$sopir = $this->input->post('sopir');
		$nopol = $this->input->post('nopol');
		$noRetur = $this->input->post('no_retur');
		$jmlRetur = $this->input->post('jml_retur');
		$noPallet = $this->input->post('no_pallet');
		$stokPallet = $this->input->post('stok_pallet');
		$kodePallet = $this->input->post('kode_pallet');
		$return['status'] = 0;
		
		/*update retur_farm*
		$this->db->trans_begin();
		$rfBaru = array(
				'NOPOL' => $nopol,
				'SOPIR' => $sopir
			);	
		$this->db->where('NO_RETUR', $noRetur);	
		$this->db->update('RETUR_FARM', $rfBaru);
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
		}else{
			$this->db->trans_commit();
		}
		/*end update retur_farm*
		
		$parameter = array(
			'KODE_FARM' => $this->_farm,
			'NO_PALLET'	=> $noPallet
		);
		
		/*update movement*
		$hitungSisa = $stokPallet - $jmlRetur;
		$updateMV = array(
			'JML_ON_HAND'	=> $hitungSisa,
			'JML_AVAILABLE'	=> $hitungSisa
		);
		$this->db->trans_begin();
		$this->db->where($parameter);
		$this->db->update('MOVEMENT', $updateMV);
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
		}else{
			$this->db->trans_commit();
		}
		/*end update movement*
		
		/*insert movement_d*
		$mv = $this->db->where($parameter);
		$mv = $this->db->get('MOVEMENT')->result_array();
		if(count($mv)>0){
			for($i=0;$i<count($mv);$i++){
				$dataMD = array(
					'KODE_FARM'			=> $mv[$i]['KODE_FARM'],
					'NO_KAVLING'		=> $mv[$i]['NO_KAVLING'],
					'NO_PALLET'			=> $mv[$i]['NO_PALLET'],
					'KODE_BARANG'		=> $mv[$i]['KODE_BARANG'],
					'JENIS_KELAMIN'		=> $mv[$i]['JENIS_KELAMIN'],
					'NO_REFERENSI'		=> $noRetur,
					'JML_ON_HAND'		=> $mv[$i]['JML_ON_HAND'],
					'JML_AVAILABLE'		=> $mv[$i]['JML_AVAILABLE'],
					'BERAT_AVAILABLE'	=> $mv[$i]['BERAT_AVAILABLE'],
					'JML_ON_PUTAWAY'	=> $mv[$i]['JML_ON_PUTAWAY'],
					'BERAT_ON_PUTAWAY'	=> $mv[$i]['BERAT_ON_PUTAWAY'],
					'JML_PUTAWAY'		=> $mv[$i]['JML_PUTAWAY'],
					'BERAT_PUTAWAY'		=> $mv[$i]['BERAT_ON_PUTAWAY'],
					'JML_ON_PICK'		=> $mv[$i]['JML_ON_PICK'],
					'BERAT_ON_PICK'		=> $mv[$i]['BERAT_ON_PICK'],
					'JML_PICK'			=> $mv[$i]['JML_PICK'],
					'BERAT_PICK'		=> $mv[$i]['BERAT_PICK'],
					'PUT_DATE'			=> NULL,
					'PUT_NAME'			=> '',
					'PICKED_DATE'		=> NULL,
					'PICKED_NAME'		=> '',
					'STATUS_STOK'		=> '',
					'KETERANGAN1'		=> 'PICK',
					'KETERANGAN2'		=> 'TES INSERT',
					'KODE_PALLET'		=> $mv[$i]['KODE_PALLET'],
					'BERAT_PALLET'		=> $mv[$i]['BERAT_PALLET'],
				);
				$this->db->trans_begin();
				$this->db->insert('MOVEMENT_D', $dataMD);
				if($this->db->trans_status() == false){
					$this->db->trans_rollback();
				}else{
					$this->db->trans_commit();
				}
			}
		}
		/*end insert movement_d*
		
	}*/
	//end simpan pengiriman retur
	
	
	public function simpanTimbang(){
		$user_buat = $this->session->userdata('kode_user');
		$data = $this->input->post('data');
		$kendaraan = json_encode($this->input->post('kendaraan'));
		$kodeFarm = $this->session->userdata('kode_farm');
		$this->db->trans_begin();
		foreach($data as $d){
			$berat_pick = ($d['berat_bersih'] / $d['jumlah_aktual']) * $d['jml_pick'];
			$whereMovement = array('no_pallet' => $d['no_pallet'], 'kode_farm' => $kodeFarm);
			$whereMovementD = array('no_pallet' => $d['no_pallet'], 'kode_farm' => $kodeFarm, 'no_referensi' => $d['no_referensi']);
			/** update movement dan movement_d */
			$dataMovement = array('picked_name' => $user_buat);
			$dataMovementD = array('jml_on_hand' => $d['jumlah_aktual'] - $d['jml_pick'], 'jml_pick' => $d['jml_pick'], 'jml_on_pick' => 0, 'berat_pick' => $berat_pick,'picked_name' => $user_buat);
			$this->db->where($whereMovement)
					->set('jml_on_pick','jml_on_pick - '.$d['jml_pick'],false)
					->set('jml_pick','jml_pick + '.$d['jml_pick'],false)
					->set('jml_on_hand','jml_on_hand - '.$d['jml_pick'],false)
					->set('berat_pick','berat_pick + '.$berat_pick,false)
					->set('picked_date','getdate()',false)
					->update('movement',$dataMovement);
			$this->db->where($whereMovementD)
					->set('keterangan2','keterangan2 + \'#'.$kendaraan.'\'',false)
					->set('picked_date','getdate()',false)->update('movement_d',$dataMovementD);
		}				
		if ($this->db->trans_status() === FALSE ){
			$this->db->trans_rollback();						
			$this->result['message'] = 'Penyimpanan gagal';
		}else{
			$this->db->trans_commit();
			$this->result['status'] = 1;
			$this->result['message'] = 'Pengiriman retur pakan antar farm berhasil disimpan';
		}

        $this->output
					->set_content_type('application/json')
					->set_output(json_encode($this->result));
	}

	public function get_rekomendasi_nopol(){
		$parameter = array(
				'FARM_ASAL' => $this->_farm,
				'NOPOL !=' => ''
			);
		$this->db->trans_begin();
		$this->db->select('NOPOL');
		$this->db->where($parameter);
		$this->db->group_by('NOPOL');
		$this->output->set_content_type('application/json')
			->set_output(json_encode($this->db->get('RETUR_FARM')->result_array()));
		$this->db->trans_commit();		
	}

	public function cetakSJ(){
		$no_retur = $this->input->post('no_retur');	
		$this->sj_pdf($no_retur);
	}
	
	/*private function sj_pdf($no_retur) {	
		error_reporting(0);		
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A4', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );				
		$pdf->SetFontSize(15);		
		$pdf->SetMargins(8, 8, 8, 8); 			
		$sj = $this->rf->get($no_retur);		
		$sjd = $this->rfd->listPakanTimbang($no_retur);		
		$dataFarmAsal = $this->db->select('NAMA_FARM,ALAMAT_FARM,KOTA')->where(array('kode_farm' => $sj->FARM_ASAL))->get('m_farm')->row();		
		$dataFarmTujuan = $this->db->select('NAMA_FARM,ALAMAT_FARM,KOTA')->where(array('kode_farm' => $sj->FARM_TUJUAN))->get('m_farm')->row();		
		$params = $pdf->serializeTCPDFtagParameters ( array (
			$no_retur,
			'QRCODE,H',
			'',
			'',
			32,
			32
		) );
		$b = '<tcpdf method="write2DBarcode" params="' . $params . '" />';				
		$html = $this->load->view ( 'rekap_retur_pakan/pengiriman_retur_pakan_farm/cetak_sj_pdf', array (
				'suratJalan' 		=> $sjd[0],
				'detail_sj'			=> $sjd,				
				'barcode' 			=> $b,				
				'dataFarmAsal' 		=> $dataFarmAsal,
				'dataFarmTujuan' 	=> $dataFarmTujuan,
				'sopir'				=> $sj->SOPIR,
				'nopol'				=> $sj->NOPOL,
		), true );		
        				
		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );	
		$marginPage = 3;			
		$pdf->Line($marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,$marginPage);
		$pdf->Line($pdf->getPageWidth()-$marginPage,$marginPage, $pdf->getPageWidth()-$marginPage,  $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage, $pdf->getPageHeight()-$marginPage, $pdf->getPageWidth()-$marginPage, $pdf->getPageHeight()-$marginPage);
		$pdf->Line($marginPage,$marginPage,$marginPage, $pdf->getPageHeight()-$marginPage);			
					
		$pdf->Output ( 'Surat Jalan.pdf', 'I' );
	}*/
	
	private function listFarm($id = NULL){
		$q = $this->db->select(array('kode_farm','nama_farm'))->where('grup_farm','BDY');
		if(!empty($id)){
			$q->where(array('kode_farm' => $id));
		}
		return $q->get('m_farm')->result_array();
	}
	
	private function listNopol(){
		$q = $this->db->select('NOPOL');
		$q->where(array('NOPOL != ' => ''));
		$q->group_by('NOPOL');
		return $q->get('retur_farm')->result_array();
	}

	public function dropdownFarm($id = NULL, $exclude = array()){
		$arr = $this->listFarm($id);
		$s = '<select class="form-control">';
		$s .= '<option value="">Pilih Farm</option>';
		$selected = '';		
		foreach($arr as $val){
			if(!empty($id)){
				if($id == $val['kode_farm']){
					$selected = 'selected = "selected"';
				}
				else{
					$selected = '';
				}
			}
			if(!empty($exclude)){
				if(!in_array($val['kode_farm'],$exclude)){
					$s .= '<option value="'.$val['kode_farm'].'" '.$selected.'>'.$val['nama_farm'].'</option>';
				}
			}else{
				$s .= '<option value="'.$val['kode_farm'].'" '.$selected.'>'.$val['nama_farm'].'</option>';
			}
			
			

		}
		$s .= '</select>';
		return $s;
	}

	private function listStatus(){
		return array(
			'N' => 'Rilis',
			'V' => 'Void',
			'RV' => 'Review',
			'RJ1' => 'Reject',
			'RJ2' => 'Reject',
			'A' => 'Approve'
		);
	}
	private function rejectedStatus(){
		return array(			
			'RJ1' => 'Reject',
			'RJ2' => 'Reject',			
		);
	}
	
	
	public function list_kendaraan(){
		$q = $this->db->select(array('NO_KENDARAAN'));
		return $q->get('M_EKPEDISI_VEHICLE_NEW')->result_array();
	}
	
	/*
	public function kendaraan_terakhir(){
		$nama_sopir = $this->input->post('nama_sopir');
		echo json_encode(array('data'=>$nama_sopir));
	}
	*/
	
	public function get_berat_timbang (){
		echo file_get_contents(base_url('api/Timbangan/timbang'));
	}
	
	//json sorting
	function setOrderBy(array &$arr, $order = null) {
		if (is_null($order)) {
			return $arr;
		}
		usort($arr, function($a, $b) use($order) {
			$result = array();
			list($field, $sort) = array_map('trim', explode(' ', trim($order)));
			if (!(isset($a[$field]) && isset($b[$field]))) {
				continue;
			}
			if (strcasecmp($sort, 'desc') === 0) {
				$tmp = $a;
				$a = $b;
				$b = $tmp;
			}
			if (is_numeric($a[$field]) && is_numeric($b[$field]) ) {
				$result[] = $a[$field] - $b[$field];
			} else {
				$result[] = strcmp($a[$field], $b[$field]);
			}
			return implode('', $result);
		});
		return $arr;
	}
	//end json sorting
	
}
