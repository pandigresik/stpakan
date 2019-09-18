<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class SinkronisasiControllerHook{
  private $CI;
  public function __construct(){
    $this->CI = & get_instance();
  }
  public function entry_sinkronisasi(){
    $route = $this->CI->session->userdata('current_route');
    $output = $this->CI->output->get_output();
    $this->CI->config->load('sinkronisasi');
    $route_sinc = $this->CI->config->item('route_sinkronisasi');
    $method_sinc = $this->CI->config->item('methode_sinkronisasi');
    $params = $this->getParams();
<<<<<<< HEAD
  
=======

>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
    if(in_array($route,$route_sinc)){
        $this->CI->load->module('sinkronisasi/entrysinkronisasi','entrysinkronisasi');
        $func = $method_sinc[$route];
        $this->CI->entrysinkronisasi->$func($params,$output);
    }
  }

  public function get_route(){
    $module = $this->CI->router->fetch_module();
    $class = $this->CI->router->fetch_class();
    $method = $this->CI->router->fetch_method();
    $route = $module.'/'. $class.'/'.$method;
    $this->CI->session->set_userdata('current_route',$route);
  }
  private function getParams(){
		$result = array();
		if(isset($_POST)){
			$method = 'POST';
			$result[$method] = $_POST;
			/*foreach($_POST as $_key => $p){

			}*/
		}
		if(isset($_GET)){
			$method = 'GET';
			$result[$method] = $_GET;
			/*
			foreach($_GET as $_key => $g){

			}*/
		}
		return $result;
<<<<<<< HEAD
  }
  
  function log_message($message){
    $filepath = APPPATH . 'logs/hooks-log-' . date('Y-m-d') . '.php'; // Creating Query Log file with today's date in application/logs folder
    $handle = fopen($filepath, "a+");                 // Opening file with pointer at the end of the file
    fwrite($handle, $message . "\n\n");  
  }
  
=======
	}
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
}
