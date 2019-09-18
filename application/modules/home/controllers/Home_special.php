<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Home_special extends MX_Controller{
	protected $grup_farm;
	private $user;
	private $permission;
	private $username;
	private $password;
	private $isLogin = FALSE;
	private $_canSetACK = array();
	public function __construct(){
		parent::__construct();
		$this->load->helper('stpakan');
		$isLogin = Modules::run('user/user/isLogin');   
        
		/* set auto login untuk pak joyo */
		if(!$isLogin){
            
			$username = 'presdir';
			$password = 'presdir';
			$this->user = $this->m_user->login($username,$password);/* data yang diambil adalah data baris pertama saja */
			if(!empty($this->user)){
				$this->isLogin = TRUE;
				$dataUser = array(
					'isLogin' => $this->isLogin,
					'kode_user' => $this->user['KODE_PEGAWAI'],
					'level_user' => 'PD',//$this->mappingUserLevel($this->user['GRUP_PEGAWAI']),
					'level_user_db' => $this->user['GRUP_PEGAWAI'],
					'kode_farm' => $this->user['KODE_FARM'],
					'nama_user' => $this->user['NAMA_PEGAWAI'],
					'grup_farm'	=> $this->user['GRUP_FARM']
				);
				/* set session */
                  
				$this->session->set_userdata($dataUser);
			}
		}
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
	}
	public function index(){
		$data['project_name'] = 'ST Pakan';
		$data['base_url'] = base_url();
		$level_user = $this->session->userdata('level_user');
		$data['nama_user'] = $this->session->userdata('nama_user');
		$level_user_db = $this->session->userdata('level_user_db');
//		$data['menu'] = $this->build_menu($level_user_db);
//		$tgl = $this->getDateServer();
		$tgl = Modules::run('home/home/getDateServer');
		$data['tanggal_server'] = $tgl->tglserver;
		$data['user'] = array('level' => $level_user, 'farm' => $this->session->userdata('kode_farm'));
		$data['content'] = '';
		//echo '<pre>'.print_r($data['menu']);
		$this->load->view('home_special',$data);
	}
}
