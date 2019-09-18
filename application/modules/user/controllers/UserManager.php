<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class UserManager extends MX_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('m_user','us');
		$this->load->model('m_workbook','wk');
	}
	
	public function listUser(){
	 	$users = $this->us->get(array('id','published'))->result_array();
	 	$data['users'] = $users;
	 	$this->smartyii->view('listuser',$data);
	 	
	 }
	
	public function listWorkbook(){
	 	$workbooks = $this->wk->get()->result_array();
	 	$data['workbooks'] = $workbooks;
	 	$this->smartyii->view('listworkbook',$data);
	 }
	 
	public function listAccess($userid){
		$data['access'] = $this->us->listAccess($userid);
		$this->smartyii->view('listaccess',$data);
	}
}
