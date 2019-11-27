<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Kontrol_stok_pakan extends MY_Controller {
	protected $result;
	protected $_user;
	protected $grup_farm;
	
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_report','report');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_user = $this->session->userdata('kode_user');
	}

	public function index($farm = null) {
		$data = array();
		$this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan/index',$data);
	}

    public function kontrol($farm = null) {
		$data = array();
		$data['farm'] = $this->report->listFarm($this->input->get('farm'));
		$data['status_siklus'] = $this->input->get('status_siklus');
		$data['siklus'] = $this->report->listSiklus($this->input->get('siklus'),$data['farm']);
		$this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan',$data);
	}

	public function timbang_kandang()
    {
        $this->load->model('api/m_timbang_pakan_detail', 'mtpd');
        
        $noreg = $this->input->post('noreg');
        $tglTransaksi = $this->input->post('tgl_transaksi');
        $keyWhere = array(
			'cast(tgl_buat as date) = \''.$tglTransaksi.'\'',
            'no_reg' => $noreg
        );
		$mtpd = $this->mtpd->as_array()->get_many_by($keyWhere);
        $this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan/detail_timbang_pakan',['data' => $mtpd]);
    }

	public function timbang_silo()
    {
        $this->load->model('api/m_timbang_pakan_silo_detail', 'mtpd');
        
        $noreg = $this->input->post('noreg');
        $tglTransaksi = $this->input->post('tgl_transaksi');
        $keyWhere = array(
			'cast(tgl_buat as date) = \''.$tglTransaksi.'\'',
            'no_reg' => $noreg
        );
		$mtpd = $this->mtpd->as_array()->get_many_by($keyWhere);
        $this->load->view('report/'.$this->grup_farm.'/kontrol_stok_pakan/detail_timbang_pakan_silo',['data' => $mtpd]);
    }
}
