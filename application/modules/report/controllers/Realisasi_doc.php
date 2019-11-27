<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Realisasi_doc extends MY_Controller {
	
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_report','report');
		$this->load->model('report/m_report_kontrol_stok','mrks');
		$this->load->model('report/m_realisasi_doc', 'm_model');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		
	}

	public function index() {
		$this->result['status'] = 1;
		$eventKandangKlik = 'KSP.showDetailRealisasiDoc(this)';
		$header = '<div class="alert alert-warning text-center" style="font-size:120%;color:#000">Realisasi DOC</div>';
        $this->result['content'] = $header. summaryFarm($this->mrks->list_kandang_all(array('ks.status_siklus' => 'O')),'onclick="KSP.showKandang(this,\''.$eventKandangKlik.'\')"');
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}
	
	public function detail(){
		$noreg			= $this->input->post('noreg');
		$tgl_docin		= $this->input->post('tgl_docin');
		$bapdoc			= $this->m_model->get_bap_doc($noreg)->row();
		$d_kodebox		= $this->m_model->get_kode_box_noreg($noreg)->result_array();
		$status			= $this->m_model->get_status_approval($noreg)->row();
		$log_approval	= $this->m_model->get_log_approval($noreg)->result_array();
		$d_timbangdoc   = $this->m_model->get_timbang_doc_noreg($noreg)->result_array();		
		$data 		= array(
				'noreg'			=> $noreg,
				'tgl_docin'		=> $tgl_docin,
				'bapdoc'		=> $bapdoc,
				'DkodeBox'		=> $d_kodebox,
				'dtimbangdoc'	=> $d_timbangdoc,
				'status'		=> $status,
				'log_approval'	=> $log_approval,
		);
		$this->load->view('bdy/kontrol_stok_pakan/detail_realisasi_doc', $data);
	}
}    
