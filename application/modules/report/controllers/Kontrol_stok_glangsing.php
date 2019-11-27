<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Kontrol_stok_glangsing extends MY_Controller {
	protected $result;
	protected $_user;
	protected $grup_farm;
	protected $akses;
	protected $level_user;
	protected $level_user_db;
	protected $adg_tampil = array(7,14,21,28);
	public function __construct() {
		parent::__construct ();
		$this->load->helper('stpakan');
		$this->load->model('report/m_lsgas','report');
		$this->load->model('report/M_kontrol_stok_glangsing','m_ksg');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_user = $this->session->userdata('kode_user');
		$this->level_user_db = $this->session->userdata('level_user_db');
		$this->adg_tampil = array(7,14,21,28); 


		$this->btn_ksg = array(
			'KF' => array(
					'P'	 => 'In Progress',
					'D'	 => "<input onclick='KontrolStokGlangsing.check_button(this)' class='check_ksg' type='checkbox'/>",
					'N'  => 'Dibuat',
		            'R1' => 'Dikoreksi Kadept PI',
		            'R2' => 'Disetujui Kadept Admin budidaya',
		            'A'  => 'Disetujui Kadiv Budidaya',
		            'RJ' => "<input onclick='KontrolStokGlangsing.check_button(this)' class='check_ksg' type='checkbox'/>",
				),
			'KD' => array(
					'P'	 => 'In Progress',
					'D'	 => 'Draft',
					'N'  => "<input onclick='KontrolStokGlangsing.check_button(this)' class='check_ksg' type='checkbox'/>",
		            'R1' => 'Dikoreksi Kadept PI',
		            'R2' => 'Disetujui Kadept Admin budidaya',
		            'A'  => 'Disetujui Kadiv Budidaya',
		            'RJ' => 'Rejected',
				),
			'KDB' => array(
					'P'	 => 'In Progress',
					'D'	 => 'Draft',
					'N'  => 'Dibuat',
		            'R1' => "<input onclick='KontrolStokGlangsing.check_button(this)' class='check_ksg' type='checkbox'/>",
		            'R2' => 'Disetujui Kadept Admin budidaya',
		            'A'  => 'Disetujui Kadiv Budidaya',
		            'RJ' => 'Rejected',
				),
			'KDV' => array(
					'P'	 => 'In Progress',
					'D'	 => 'Draft',
					'N'  => 'Dibuat',
		            'R1' => 'Dikoreksi Kadept PI',
		            'R2' => "<input onclick='KontrolStokGlangsing.check_button(this)' class='check_ksg' type='checkbox'/>",
		            'A'  => 'Disetujui Kadiv Budidaya',
		            'RJ' => 'Rejected',
				)
		);

		$this->btn_ppsk = array(
			'KF' => array(
					'D'	 => 'Draft',
					'N'  => 'Dibuat',
		            'R1' => 'Dikoreksi Kadept PI',
					'R2' => 'Disetujui Kadept Admin budidaya',
					'A0'  => 'Disetujui Kadept Admin budidaya',
		            'A'  => 'Disetujui Kadiv Budidaya',
		            'RJ' => 'Ditolak',
		            'V' => 'Direvisi',
				),
			'KD' => array(
					'D'	 => 'Draft',
					'N'  => '<input onclick="KontrolStokGlangsing.check_button(this)" class="check_ppsk" type="checkbox"/>',
		            'R1' => 'Dikoreksi Kadept PI',
					'R2' => 'Disetujui Kadept Admin budidaya',
					'A0' => 'Disetujui Kadept Admin budidaya',
		            'A'  => 'Disetujui Kadiv Budidaya',
		            'RJ' => 'Ditolak',
		            'V' => 'Direvisi',
				),
			'KDB' => array(
					'D'	 => 'Draft',
					'N'  => 'Dibuat',
		            'R1' => '<input onclick="KontrolStokGlangsing.check_button(this)" class="check_ppsk" type="checkbox"/>',
					'R2' => 'Disetujui Kadept Admin budidaya',
					'A0' => 'Disetujui Kadept Admin budidaya',
		            'A'  => 'Disetujui Kadiv Budidaya',
		            'RJ' => 'Ditolak',
		            'V' => 'Direvisi',
				),
			'KDV' => array(
					'D'	 => 'Draft',
					'N'  => 'Dibuat',
		            'R1' => 'Dikoreksi Kadept PI',
					'R2' => 'Disetujui Kadept Admin budidaya',
					'A0' => '<input onclick="KontrolStokGlangsing.check_button(this)" class="check_ppsk" type="checkbox"/>',
		            'A'  => 'Disetujui Kadiv Budidaya',
		            'RJ' => 'Ditolak',
		            'V' => 'Direvisi',
				)
		);
		$this->btn_simpan = array(
			'KF'  => "<button type='button' class='btn btn-primary btn_simpan' disabled onclick=KontrolStokGlangsing.update(this,'N')> <i class='glyphicon glyphicon-ok'></i> 	  Rilis 	</button>",
			'KD'  => "<button type='button' class='btn btn-primary btn_simpan' disabled onclick=KontrolStokGlangsing.update(this,'R1')> <i class='glyphicon glyphicon-ok'></i> 	  Review 	</button>
				&nbsp;<button type='button' class='btn btn-danger btn_simpan' disabled onclick=KontrolStokGlangsing.update(this,'RJ')> <i class='glyphicon glyphicon-remove'></i> Reject	</button>",
			'KDB' => "<button type='button' class='btn btn-primary btn_simpan' disabled onclick=KontrolStokGlangsing.update(this,'R2')> <i class='glyphicon glyphicon-ok'></i> 	  Review 	</button>
				&nbsp;<button type='button' class='btn btn-danger btn_simpan' disabled onclick=KontrolStokGlangsing.update(this,'RJ')> <i class='glyphicon glyphicon-remove'></i> Reject	</button>",
			'KDV' => "<button type='button' class='btn btn-primary btn_simpan' disabled onclick=KontrolStokGlangsing.update(this,'A')> <i class='glyphicon glyphicon-ok'></i> 	  Approve 	</button>
				&nbsp;<button type='button' class='btn btn-danger btn_simpan' disabled onclick=KontrolStokGlangsing.update(this,'RJ')> <i class='glyphicon glyphicon-remove'></i> Reject	</button>",
		);
	}
    public function index($farm = null) {
        $data['status'] = array(
            'N'  => 'Dibuat',
            'R1' => 'Dikoreksi Kadept PI',
            'R2' => 'Disetujui Kadept Admin budidaya',
            'A'  => 'Disetujui Kadiv Budidaya',
            'RJ' => 'Ditolak',
        // 'REJECTED' => 'Rejected'
        );
		$data['button_simpan'] = $this->btn_simpan;
		$data['list_farm'] = $this->m_ksg->getListFarm($this->_user);
		$data['level_user'] = $this->session->userdata('level_user');
		$data['level_user_db'] = $this->level_user_db;
		$data['kode_farm'] = $farm;
		$this->load->view('report/'.$this->grup_farm.'/kontrol_stok_glangsing',$data);
	}

	public function get_list_report($kode_farm = '', $req_status = '', $cetak = null)
    {
        try {
			$result = simpleGrouping($this->m_ksg->getStokGlangsingData($kode_farm, $req_status, $this->level_user_db),'kode_siklus');

			if(count($result) > 0){
				$list_response_html = array();
				foreach ($result as $key => $value) {
					$countBudget = 0;
					$countPemakaian = 0;
					$countall = 0;
					foreach ($value as $key => $data) {
						//echo $key .' => '. $data .'<br>';
						if(strpos($key,"budget") !== false){
							$countBudget++;
						}
						if(strpos($key,"pakaiOver_") !== false || strpos($key,"pakaiReal_") !== false || strpos($key,"pakaiHargaJual") !== false){
							$countPemakaian++;
						}
						$countall++;
					}

				}
				$content['data_list_ksg'] = $result;
				$content['button_ksg'] = $this->btn_ksg;
				$content['level_user'] = $this->session->userdata('level_user');
				$response_html = $this->load->view('report/bdy/list_kontrol_stok_glangsing_header',$content, TRUE);
				array_push($list_response_html, $response_html);
			}else{
				$list_response_html = array();
				$header_list = '<div class="col-sm-12"><center><h3 class="text-center">Data tidak ditemukan.</h3></center></div>';
			}

			$this->result['status'] = 1;
            $this->result['message'] = 'Sukses';
            $this->result['content'] = array(
              'data_list' => $list_response_html,
            );
        } catch (Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = '<b>Error : </b>' . $e-> getMessage();
        }

        if (! empty($cetak)) {
            // cetak_r( $this->result['content']['data_list'] );
          	header("Content-type: application/xls");
            header("Content-Disposition: attachment; filename=report-ksg.xls");
            echo  implode(" ", $this->result['content']['data_list']);
        } else {
            display_json($this->result);
        }
    }
	public function get_detail_ppsk(){
    	try {
    		$kode_siklus = $this->input->post('siklus');
    		$saldo = $this->input->post('saldo');
			$result = $this->m_ksg->getDetailPermintaanSak($kode_siklus, $saldo);
			$ppsk = $this->m_ksg->getDetailPerDate($kode_siklus);
			
			if(count($result) > 0){
				$list_response_html = array();
				$content['data_list_date'] = $result;
				$content['data_list_ppsk'] = $ppsk;
				$content['button_ppsk'] = $this->btn_ppsk;
				$content['level_user'] = $this->session->userdata('level_user');
				$response_html = $this->load->view('report/bdy/list_report_ppsk_new',$content, TRUE);
				array_push($list_response_html, $response_html);
				$header_list = '';
			}else{
				$list_response_html = array();
				$header_list = '<div class="col-sm-12" style="text-align:left;padding-left:50px">Data tidak ditemukan.</div>';
			}

			$this->result['status'] = 1;
            $this->result['message'] = 'Sukses';
            $this->result['content'] = array(
              'data_list' => $list_response_html,
              'message' => $header_list
            );
        } catch (Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = '<b>Error : </b>' . $e-> getMessage();
        }
    	display_json($this->result);
    }
	public function updateKsg(){
		$data = $this->input->post('data');
		$nextStatus = $this->input->post('nextStatus');
		$keterangan_reject = $this->input->post('keterangan_reject');
		$error = 0;
		//cetak_r($data,false);
		echo $this->m_ksg->doUpdateKsg($data, $nextStatus, $keterangan_reject);
		//cetak_r($data);
	}
	public function updatePpsk(){
		$data 		= $this->input->post('data');
		$nextStatus = $this->input->post('nextStatus');
		$keterangan_reject = $this->input->post('keterangan_reject');

		$error 		= 0;
		$this->output
					->set_content_type('application/json')
					->set_output($this->m_ksg->doUpdatePpsk($data, $nextStatus, $keterangan_reject));

	}
}
