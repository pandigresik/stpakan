<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class User extends MX_Controller {
	private $user;
	private $permission;
	private $username;
	private $password;
	private $isLogin = FALSE;
	public function __construct(){
		parent::__construct();
		$this->load->model('user/m_user');
	}

	public function login()
	{
		$message = null;
		if(isset($_GET['m'])){
			$data['message'] = $this->input->get('m');
		}
		$data['base_url'] = base_url();
		$this->load->view('user/login',$data);
	}

	public function checkLogin()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$result = array(
			'status'  => 0,
			'message' => 'Proses login',
			'content' => ''
		);
		$this->user = $this->m_user->login($username,$password);/* data yang diambil adalah data baris pertama saja */
		if(!empty($this->user)){
			$this->isLogin = TRUE;
		//	$this->password = $password;
			$dataUser = array(
				'isLogin' => $this->isLogin,
				'kode_user' => $this->user['KODE_PEGAWAI'],
				'level_user' => $this->mappingUserLevel($this->user['GRUP_PEGAWAI']),
				'level_user_db' => $this->user['GRUP_PEGAWAI'],
				'kode_farm' => $this->user['KODE_FARM'],
				'nama_user' => $this->user['NAMA_PEGAWAI'],
				'grup_farm'	=> $this->user['GRUP_FARM']
		//		'permission'=> serialize($this->getPermission())
			);

			$this->session->set_userdata($dataUser);
			$result['status'] = 1;
		}

		echo json_encode($result);
	}
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('user/user/login');
	}
	public function isLogin()
	{
		return $this->session->userdata('isLogin');
	}

/* $route adalah url yang akan diakses misal pobb/probe/kendaraanmasuk/index */
	public function hasPermission($route)
	{
		$permission = unserialize($this->session->userdata('permission'));
		$listUrl = $this->listPermission($permission);
		
		return in_array($route,$listUrl);
	}
	public function listPermission($listIdMenu = array())
	{
		$result = 'array gak boleh kosong';
		$this->load->config('permission');
		$listUrl = $this->config->item('dependency_workbook');
		if(!empty($listIdMenu)){
			$tmpArr = array();
			foreach($listIdMenu as $idMenu){
				$tmpArr = isset($listUrl[$idMenu]) ? array_merge($tmpArr,$listUrl[$idMenu]):$tmpArr;
			}
			$result = $tmpArr;
		}
		return $result;
	}
	public function getUsername()
	{
		return $this->session->userdata('kode_user');
	}

	public function changePassword()
	{
		if(isset($_POST['newPassword'])){
			$username = $this->getUsername();
			$newPassword = $this->input->post('newPassword');
			$oldPassword = $this->input->post('oldPassword');
			$result = array(
				'status'=>0,
				'message'=>''
			);
			if(!empty($username)){
				$this->m_user->changePassword($username,$oldPassword,$newPassword);
				if($this->m_user->affectedRow() > 0){
					$result['status'] = 1;
					$result['message'] = 'Password telah berhasil dirubah.';
				}
				else {
					$result['status'] = 0;
					$result['message'] = 'Password gagal dirubah, password lama mungkin tidak sesuai.';
				}
			}
			else{
				$result['status'] = 0;
				$result['message'] = 'Login terlebih dahulu. ';
			}
			echo json_encode($result);
		}
		else{
			$data['nama'] =$this->input->post('nama_user');
			$this->load->view('changePassword',$data);
		}

	}

	public function getPassword()
	{
		return $this->password;
	}
	/* jadikan nilai id dari element array sebagai key-nya */
	public function getPermission()
	{
		$username = $this->user->row();
		$this->permission = $this->m_user->getPermission($username->id)->result_array();
		return $this->arr2to1D($this->permission,'token');
	}
	/* bangun daftar menu berdasarkan data dari workbook yang bisa diakses database
	 * dan data config['workbook'] dari file user/config/permission.php
	 */
	public function listMenu(){
		$permission = unserialize($this->session->userdata('permission'));
		$this->load->config('permission');
		$listWorkbook = $this->config->item('workbook');
		$listTmp = array();
		foreach($listWorkbook as $id => $menu){
			/* pakai isset karena bisa jadi permissionnya untuk aplikasi lain bukan untuk aplikasi ini */
			if(in_array($id,$permission)){
				array_push($listTmp,$menu);
			}
		}
		/*
		foreach($permission as $id){
			/* pakai isset karena bisa jadi permissionnya untuk aplikasi lain bukan untuk aplikasi ini
			if(isset($listWorkbook[$id])) array_push($listTmp,$listWorkbook[$id]);
		}
		*/
		return $listTmp;
	}

	public function setUsername($username)
	{
		$this->username = $username;
	}
	public function setPassword($password)
	{
		$this->password = $password;
	}
	public function setPermission()
	{

	}
	/* bangun menu untuk navigasi */
	public function nav(){
		$menu = $this->listMenu();
		$listMenu = $this->buildMenu($menu);
		$data['menu'] = $listMenu;
		$data['username'] = $this->session->userdata('username');
		return $this->load->view('user/nav',$data,true);
	}

	private function buildMenu($menu = array()){
		$CI =& get_instance();
		$cur_control = $CI->router->class;
		$this->load->config('permission');
		$nav = $this->config->item('list_menu');

		$listMenu = array();
		array_push($listMenu,$nav['home']);
		if(!empty($menu)){
			foreach($menu as $item){
				/* tampilkan yang memiliki label menu saja */
				if(isset($nav[$item['id']])){
					array_push($listMenu,$nav[$item['id']]);
				}
			}
		}
		foreach($listMenu as $key => $value){
			$compared_str = str_replace('nav_','',$value['id']);
			if($cur_control == $compared_str)
				$listMenu[$key]['class'] = 'active';
			else
				$listMenu[$key]['class'] = '';
		}

		return $listMenu;
	}

	private function arr2to1D($arr,$key){
		$tmp = array();
		foreach($arr as $k){
			array_push($tmp,$k[$key]);
		}
		return $tmp;
	}
	private function convertArr($arr,$key){
		$tmp = array();
		foreach($arr as $val){
			$id = $val[$key];
			$tmp[$id] = $val;
		}
		return $tmp;
	}

	private function mappingUserLevel($userLevel){
		$kodeBaru = array(
			'KDV' => 'KDV',
			'WKDV' => 'KDV', /* memiliki hak akses yang sama dengan kadiv*/
			'KDP' => 'KD',
			'WKDP' => 'KD',
			'KFM'  => 'KF',
			'PPB' => 'P',
			'KPPB' => 'P',
			'AGF' => 'AG',
			'KBA' => 'KA',
			'ABP' => 'KA',
			'KDBA'=> 'KDB',
			'WKKEU'=> 'KDKEU',
			'WKBA'=> 'KDB'
		);
		return isset($kodeBaru[$userLevel]) ? $kodeBaru[$userLevel] : $userLevel;
	}

}
