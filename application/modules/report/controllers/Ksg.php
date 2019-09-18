<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Ksg extends MY_Controller {
	protected $result;
	protected $_user;
	protected $grup_farm;
	protected $akses;
	protected $level_user;
	protected $level_user_db;
	protected $adg_tampil = array(7,14,21,28);
	public function __construct() {
		parent::__construct ();
		$this->load->model('report/m_lsgas','report');
		$this->load->model('report/M_ksg','m_ksg');
		$this->result = array('status' => 0, 'content'=>'', 'message'=> '');
		$this->load->helper('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_user = $this->session->userdata('kode_user');
		$this->level_user_db = $this->session->userdata('level_user_db');
		$this->adg_tampil = array(7,14,21,28);


		$this->btn_ksg = array(
			'KF' => array(
					'P'	 => 'In Progress',
					'D'	 => "<input onclick='KSG.check_button(this)' class='check_ksg' type='checkbox'/>",
					'N'  => 'New (Rilis)',
		            'R1' => 'Reviewed Kadept PI',
		            'R2' => 'Reviewed Kadept Adm. budidaya',
		            'A'  => 'Approved',
		            'RJ' => "<input onclick='KSG.check_button(this)' class='check_ksg' type='checkbox'/>",
				),
			'KD' => array(
					'P'	 => 'In Progress',
					'D'	 => 'Draft',
					'N'  => "<input onclick='KSG.check_button(this)' class='check_ksg' type='checkbox'/>",
		            'R1' => 'Reviewed Kadept PI',
		            'R2' => 'Reviewed Kadept Adm. budidaya',
		            'A'  => 'Approved',
		            'RJ' => 'Rejected',
				),
			'KDB' => array(
					'P'	 => 'In Progress',
					'D'	 => 'Draft',
					'N'  => 'New (Rilis)',
		            'R1' => "<input onclick='KSG.check_button(this)' class='check_ksg' type='checkbox'/>",
		            'R2' => 'Reviewed Kadept Adm. budidaya',
		            'A'  => 'Approved',
		            'RJ' => 'Rejected',
				),
			'KDV' => array(
					'P'	 => 'In Progress',
					'D'	 => 'Draft',
					'N'  => 'New (Rilis)',
		            'R1' => 'Reviewed Kadept PI',
		            'R2' => "<input onclick='KSG.check_button(this)' class='check_ksg' type='checkbox'/>",
		            'A'  => 'Approved',
		            'RJ' => 'Rejected',
				)
		);

		$this->btn_ppsk = array(
			'KF' => array(
					'D'	 => 'Draft',
					'N'  => 'New (Rilis)',
		            'R1' => 'Reviewed',
		            'R2' => 'Reviewed',
		            'A'  => 'Approved',
		            'RJ' => 'Rejected',
				),
			'KD' => array(
					'D'	 => 'Draft',
					'N'  => "<input onclick='KSG.check_button(this)' class='check_ppsk' type='checkbox'/>",
		            'R1' => 'Reviewed',
		            'R2' => 'Reviewed',
		            'A'  => 'Approved',
		            'RJ' => 'Rejected',
				),
			'KDB' => array(
					'D'	 => 'Draft',
					'N'  => 'New (Rilis)',
		            'R1' => "<input onclick='KSG.check_button(this)' class='check_ppsk' type='checkbox'/>",
		            'R2' => 'Reviewed',
		            'A'  => 'Approved',
		            'RJ' => 'Rejected',
				),
			'KDV' => array(
					'D'	 => 'Draft',
					'N'  => 'New (Rilis)',
		            'R1' => 'Reviewed',
		            'R2' => "<input onclick='KSG.check_button(this)' class='check_ppsk' type='checkbox'/>",
		            'A'  => 'Approved',
		            'RJ' => 'Rejected',
				)
		);
		$this->btn_simpan = array(
			'KF'  => "<button type='button' class='btn btn-primary btn_simpan' disabled onclick=KSG.update(this,'N')> <i class='glyphicon glyphicon-ok'></i> 	  Rilis 	</button>",
			'KD'  => "<button type='button' class='btn btn-primary btn_simpan' disabled onclick=KSG.update(this,'R1')> <i class='glyphicon glyphicon-ok'></i> 	  Review 	</button>
				&nbsp;<button type='button' class='btn btn-danger btn_simpan' disabled onclick=KSG.update(this,'RJ')> <i class='glyphicon glyphicon-remove'></i> Reject	</button>",
			'KDB' => "<button type='button' class='btn btn-primary btn_simpan' disabled onclick=KSG.update(this,'R2')> <i class='glyphicon glyphicon-ok'></i> 	  Review 	</button>
				&nbsp;<button type='button' class='btn btn-danger btn_simpan' disabled onclick=KSG.update(this,'RJ')> <i class='glyphicon glyphicon-remove'></i> Reject	</button>",
			'KDV' => "<button type='button' class='btn btn-primary btn_simpan' disabled onclick=KSG.update(this,'A')> <i class='glyphicon glyphicon-ok'></i> 	  Approve 	</button>
				&nbsp;<button type='button' class='btn btn-danger btn_simpan' disabled onclick=KSG.update(this,'RJ')> <i class='glyphicon glyphicon-remove'></i> Reject	</button>",
		);
	}
    public function index() {
        $data['status'] = array(
            'N'  => 'New (Rilis)',
            'R1' => 'Reviewed Kadept PI',
            'R2' => 'Reviewed Kadept Admin budidaya',
            'A'  => 'Approved',
            'RJ' => 'Rejected',
        // 'REJECTED' => 'Rejected'
        );
		$data['button_simpan'] = $this->btn_simpan;
		$data['list_farm'] = $this->m_ksg->getListFarm($this->_user);
		$data['level_user'] = $this->session->userdata('level_user');
		$data['level_user_db'] = $this->level_user_db;
		$this->load->view('report/'.$this->grup_farm.'/ksg',$data);
	}

	public function get_list_report_ksg($kode_farm = '', $req_status = '', $cetak = null)
    {
        try {
			$result = $this->m_ksg->getStokGlangsingData($kode_farm, $req_status, $this->level_user_db);

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

					// echo $countBudget;
					// echo $countPemakaian;
					// echo $countall;
					// echo ceil(($countall - 10)/10);
					// echo '----------------------------------------------------------------------------------------------<br>';
				}
				$content['data_list_ksg'] = $result;
				$content['button_ksg'] = $this->btn_ksg;
				$content['level_user'] = $this->session->userdata('level_user');
				$response_html = $this->load->view('report/bdy/list_report_ksg',$content, TRUE);
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

			if(count($result) > 0){
				$list_response_html = array();
				$content['data_list_ppsk'] = $result;
				$content['button_ppsk'] = $this->btn_ppsk;
				$content['level_user'] = $this->session->userdata('level_user');
				$response_html = $this->load->view('report/bdy/list_report_ppsk',$content, TRUE);
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

		echo $this->m_ksg->doUpdatePpsk($data, $nextStatus, $keterangan_reject);

	}
}
