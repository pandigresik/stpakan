<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Import_box extends MY_Controller{
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
		
	}
	public function index(){
		$this->load->model('penerimaan_docin/m_bapd','bapd');
		$kodefarm = $this->session->userdata('kode_farm');
		$user_level = $this->session->userdata('level_user');
		$data = array('nama_farm' => $this->config->item('namaFarm'));
		$data['listBapd'] = $this->bapd->listBapdImport()->result_array();
		$data['list_farm'] = array_column($this->db->distinct()->select(array('kode_farm'))->order_by('kode_farm')->get('m_farm')->result_array(),'kode_farm');
		$data['list_siklus'] = array_column($this->db->distinct()->select(array('periode_siklus'))->order_by('periode_siklus','desc')->get('m_periode')->result_array(),'periode_siklus');
		$this->load->view('penerimaan_docin/bdy/import_bapd',$data);
	}

	public function list_farm_preview(){
		$this->load->model('penerimaan_docin/m_bapdocbox','bapdocdox');
		$kode_farm = $this->input->post('kode_farm');
		$periode_siklus = $this->input->post('periode_siklus');
		$namaFarm = $this->config->item('namaFarm');
		$kode_siklus_sql = $this->db->select('kode_siklus')->where(array('kode_farm' => $kode_farm, 'periode_siklus' => $periode_siklus))->get_compiled_select('m_periode');
		$list_noreg_sql = $this->db->select('no_reg')->where('kode_siklus in ('.$kode_siklus_sql.')')->get_compiled_select('kandang_siklus');
		$data = array(
			'periode_siklus' => $periode_siklus
		);
		$data['boxs'] = $this->bapdocdox->as_array()->get_many_by('no_reg in ('.$list_noreg_sql.')');
		$data['jml_kandang'] = $this->db->distinct()->select('no_reg')->group_by('no_reg')->where('no_reg in ('.$list_noreg_sql.')')->get('bap_doc_box')->num_rows();
		$data['jml_sj'] = $this->db->distinct()->select('no_sj')->group_by('no_sj')->where('no_reg in ('.$list_noreg_sql.')')->get('bap_doc_box')->num_rows();
		$data['jml_box'] = $this->db->select_sum('jml_box')->where('no_reg in ('.$list_noreg_sql.')')->get('bap_doc_box')->row_array();
		$data['nama_farm'] = $namaFarm[$kode_farm];
		$this->result['content'] = $this->load->view('penerimaan_docin/bdy/list_kodebox',$data,true);
		$this->result['status'] = 1;
		$this->output
				->set_content_type('application/json')
				->set_output(json_encode($this->result));
	}

	public function simpan_box(){
		$dataKirim = $this->input->post('data');
		$farm = $this->input->post('farm');
		$siklus = $this->input->post('siklus');
		$_query = array();
		$error = 0;
		/** buat query untuk ngecek datanya */
		foreach($dataKirim as $_q){
			$_select = array();
			foreach($_q as $_k => $_v){
				array_push($_select,'\''.$_v .'\' as '.$_k);
			}
			array_push($_query,'select '.implode(',',$_select));
		}
		
		$queryCek = implode(' union all ',$_query);
		/** periksa kode_farm */
		$farmSql = <<<SQL
		SELECT * FROM(
			SELECT DISTINCT substring(no_reg,1,2) kode_farm FROM
				(
					{$queryCek}
			)v
		)z WHERE z.kode_farm NOT IN ('{$farm}')
SQL;
					
		$farmResult = $this->db->query($farmSql)->num_rows();
		if(!empty($farmResult)){
			$this->result['message'] = 'Kode farm tidak terdaftar';			
			$error++;
		}
			

	if(!$error){
		/** pastikan siklus dalam excel adalah noreg yang aktif di siklus tersebut  */
		$siklusCek = <<<SQL
		SELECT * FROM(
			SELECT DISTINCT substring(no_reg,4,6) siklus FROM
				(
					{$queryCek}
			)v
		)z WHERE z.siklus NOT IN (SELECT periode_siklus FROM M_PERIODE WHERE KODE_FARM = '{$farm}')
SQL;
		$noregResult = $this->db->query($siklusCek)->num_rows();
		if(!empty($noregResult)){
			$this->result['message'] = 'Siklus tidak terdaftar ';			
			$error++;
		}	
	}

	if(!$error){
		/** pastikan noreg dalam excel adalah noreg yang aktif di siklus tersebut  */
		$noregCek = <<<SQL
		SELECT x.no_reg,y.no_reg  FROM(
			SELECT DISTINCT v.no_reg FROM (
				{$queryCek}
			)v
		)x LEFT JOIN (
				SELECT ks.no_reg FROM KANDANG_SIKLUS ks WHERE ks.KODE_SIKLUS IN (SELECT kode_siklus FROM M_PERIODE WHERE KODE_FARM = '{$farm}' AND PERIODE_SIKLUS = '{$siklus}') AND ks.STATUS_SIKLUS = 'O'
			)y ON x.NO_REG = y.no_reg
		WHERE y.no_reg IS null
SQL;
			
		$noregResult = $this->db->query($noregCek)->num_rows();
		if(!empty($noregResult)){
			$this->result['message'] = 'Kandang tidak aktif pada siklus '.$siklus;			
			$error++;
		}	
	}
	
	if(!$error){
		/** pastikan semua noreg yang aktif sudah diupload  */
		$noregCek = <<<SQL
		SELECT ks.no_reg,y.no_reg FROM KANDANG_SIKLUS ks 
		LEFT JOIN (
			SELECT DISTINCT v.no_reg FROM (
				{$queryCek}
				)v
		)y ON y.no_reg = ks.no_reg
		WHERE ks.KODE_SIKLUS IN (SELECT kode_siklus FROM M_PERIODE WHERE KODE_FARM = '{$farm}' AND PERIODE_SIKLUS = '{$siklus}') 
		AND ks.STATUS_SIKLUS = 'O' AND y.no_reg IS null
SQL;
		$noregResult = $this->db->query($noregCek)->num_rows();
		if(!empty($noregResult)){
			$this->result['message'] = 'Terdapat kandang yang belum diupload';			
			$error++;
		}	
	}

	if(!$error){
		/** pastikan semua sj yang diexcel sudah ada  */
		$noregCek = <<<SQL
		SELECT x.*,sj.no_sj FROM(
			SELECT DISTINCT v.no_reg,v.no_sj FROM (
				{$queryCek}
				)v
		)x	
		LEFT join BAP_DOC_SJ sj ON sj.no_reg = x.no_reg AND sj.no_sj = x.no_sj	
		WHERE sj.no_sj IS null
SQL;
		$noregResult = $this->db->query($noregCek)->result_array();
		if(!empty($noregResult)){
			$noregResultStr = array_unique(array_column($noregResult,'no_reg'));
			$this->result['message'] = 'Surat jalan tidak terdaftar untuk kandang '.implode(', ',$noregResultStr);			
			$error++;
		}	
	}

	if(!$error){
		/** pastikan semua sj sudah diupload  */
		$noregCek = <<<SQL
		SELECT sj.* FROM BAP_DOC_SJ sj
		LEFT  JOIN (
			SELECT DISTINCT v.no_reg,v.no_sj FROM (
				{$queryCek}
				)v
		)x ON x.no_reg = sj.no_reg  AND x.no_sj = sj.no_sj
		WHERE sj.NO_REG IN (SELECT no_reg FROM KANDANG_SIKLUS WHERE KODE_SIKLUS = (SELECT KODE_SIKLUS FROM M_PERIODE WHERE periode_siklus = '{$siklus}' AND KODE_FARM = '{$farm}'))
		AND x.no_sj IS null
SQL;
		$noregResult = $this->db->query($noregCek)->num_rows();
		if(!empty($noregResult)){
		//	$noregResultStr = array_unique(array_column($noregResult,'no_reg'));
			$this->result['message'] = 'Terdapat surat jalan yang belum diupload';			
			$error++;
		}	
	}

	if(!$error){
		/** pastikan semua sj sudah diupload  */
		$noregCek = <<<SQL
		select * from(
			SELECT sum(sj.jml_box) total_box,(
				SELECT sum(CAST(v.jml_box AS INTEGER)) FROM (
					{$queryCek}
					)v
			)total FROM BAP_DOC_SJ sj
			WHERE sj.NO_REG IN (SELECT no_reg FROM KANDANG_SIKLUS WHERE KODE_SIKLUS = (SELECT KODE_SIKLUS FROM M_PERIODE WHERE periode_siklus = '{$siklus}' AND KODE_FARM = '{$farm}'))
		)z WHERE z.total_box != z.total
SQL;
		$noregResult = $this->db->query($noregCek)->num_rows();
		if(!empty($noregResult)){
		//	$noregResultStr = array_unique(array_column($noregResult,'no_reg'));
			$this->result['message'] = 'Total box tidak sesuai dengan surat jalan';			
			$error++;
		}	
	}
	
	if(!$error){
		/** simpan ke database */
		$insertSql = <<<SQL
		INSERT INTO BAP_DOC_BOX (NO_SJ,KODE_BOX,JML_BOX,NO_REG,TGL_TERIMA)
		SELECT z.*,coalesce((SELECT TOP 1 tgl_terima FROM BAP_DOC_SJ WHERE no_reg = z.no_reg AND NO_SJ = z.no_sj ),getdate()) tgl_terima FROM (
			{$queryCek}
		)z
SQL;
		$insertQuery = $this->db->query($insertSql);
		if($insertQuery){
			$this->result['status'] = 1;
			$this->result['message'] = 'Kode box berhasil diunggah';
		}	
	}

	$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
}
