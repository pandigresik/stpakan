<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Home extends MX_Controller{
	public function index(){
		$data["nav"] = Modules::run('user/user/nav');
		$this->smartyii->view('home', $data);
	}
	/*
	public function nav(){
		return Modules::run('user/user/nav');
	}
	*/
}
