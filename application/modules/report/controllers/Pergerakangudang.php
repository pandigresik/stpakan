<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Pergerakangudang extends MY_Controller {
	protected $result;
	protected $_user;
	protected $grup_farm;

	public function __construct() {
		parent::__construct ();
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
	}
	public function index() {
		$data['list_farm'] = Modules::run('forecast/forecast/list_farm',$this->grup_farm,NULL,false);
		$this->load->view('report/bdy/pergerakangudang',$data);
	}

	public function listPeriode($farm){
		$periode_siklus = $this->db->distinct()->select(array('m_periode.kode_siklus','periode_siklus'))
												->join('kandang_siklus','kandang_siklus.kode_siklus = m_periode.kode_siklus and kandang_siklus.tgl_doc_in <= getdate()')
												->where(array('m_periode.kode_farm'=>$farm))
												->order_by('m_periode.periode_siklus','desc')
												->get('m_periode')
												->result_array();
//		echo $this->db->last_query();
		$this->result['content'] = $periode_siklus;
		$this->result['status'] = 1;

		echo json_encode($this->result);
	}

	public function gerakgudang(){
		$kode_siklus = $this->input->get('kode_siklus');
		$this->load->model('report/m_pergerakangudang','pg');
		$gudangterima = $this->pg->gudangterima($kode_siklus)->result_array();
		$gudangkeluar = $this->pg->gudangkeluar($kode_siklus)->result_array();
		$data['lists'] = $this->groupingnopallet($gudangterima,$gudangkeluar);
		$this->load->view('report/bdy/gerakgudang',$data);
	}

	private function groupingnopallet($gudangterima,$gudangkeluar){
		$tmp = array();
		foreach($gudangkeluar as $gk){
			$nopallet = $gk['no_penyimpanan'];
			if(!isset($tmp[$nopallet])){
				$tmp[$nopallet] = array();
			}
			array_push($tmp[$nopallet],$gk);
		}
		$result = array();
		foreach($gudangterima as $gd){
			$nopallet = $gd['no_penyimpanan'];
			if(!isset($result[$nopallet])){
				$result[$nopallet] = array(
					'penerimaan' => $gd,
					'pengambilan' => isset($tmp[$nopallet]) ? $tmp[$nopallet] : array()
				);				
			}
		}
		return $result;
	}


}
