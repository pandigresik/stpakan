<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* controller user ini akan digunakan untuk autentikasi dan otorisasi 
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Baca extends MY_Controller{

	public function index(){
		$urlFile = 'file_upload/'.$this->input->get('f');
		$this->output->set_content_type('application/pdf')->set_output(file_get_contents(base_url($urlFile)));			
	}

	
}
