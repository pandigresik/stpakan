<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Controller.php";

class MY_Controller extends MX_Controller {
	protected $result = array(
		'status' => 0,
		'message' => '',
		'content' => ''
	);
	public function __construct(){
		$CI = & get_instance();
		$module = $CI->router->fetch_module();
		$class = $CI->router->fetch_class();
		$method = $CI->router->fetch_method();
		$route = $module.'/'. $class.'/'.$method;	
		//$hasPermission = Modules::run('user/user/hasPermission',$route);
		$hasPermission = 1;
		//if(!$hasPermission)	$CI->firephp->log($route);

		/* cek harus login dulu aja */
		$isLogin = Modules::run('user/user/isLogin');
		if(!$isLogin){
			if ($CI->input->is_ajax_request()){
				$CI->output->set_status_header(401, 'login disek cak...');
				die();
			}
			else{
				redirect('user/user/login?#'.$route);
			}
		}

		/* sudah login namun tidak memiliki hak untuk mengakses menu / route tertentu */
		else{
			if(!$hasPermission){
				$message = 'Anda tidak mempunyai hak untuk mengakses url '.$route;
				if ($CI->input->is_ajax_request()){
					$CI->output->set_status_header(403, $message);
					die();
				}
				else{
					redirect('user/user/login?m='.$message);
				}
			}
		}
	}

	public function getNav(){
		return Modules::run('user/user/nav');
	}
}
/* konfigurasi untuk client koneksi ke webservice oracle*/
class Cstpakan_Controller extends MX_Controller{
	public function __construct(){
		// Load the library
		parent::__construct();
		$this->load->library('rest');
		$this->load->config('serverws');
		$config = $this->config->item('ws_stpakan');
		$this->init($config);
	}

	public function init($config = NULL){
		if(!empty($config)){
			$this->rest->initialize($config);
		}

	}
}
/* konfigurasi untuk client koneksi ke webservice oracle*/
class Cproduksi_Controller extends MX_Controller{
	public function __construct(){
		// Load the library
		parent::__construct();
		$this->load->library('rest');
		$this->load->config('serverws');
		$config = $this->config->item('ws_produksi');
		$this->init($config);
	}

	public function init($config = NULL){
		if(!empty($config)){
			$this->rest->initialize($config);
		}

	}
}
