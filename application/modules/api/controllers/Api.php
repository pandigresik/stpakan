<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions

class Api extends REST_Controller
{
	function __construct()
    {
        // Construct our parent class
        parent::__construct();
        
        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; //500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; //100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key
    }
    
    function flock_post(){
    	$this->load->model('forecast/m_flok','fl');
    	$nama_flok = $this->post('nama_flok');
    	$kode_farm = $this->post('kode_farm');
    	$tgl_docin = $this->post('tgl_docin');
    	$cari = array('kode_farm' => $kode_farm, 'nama_flok' => $nama_flok,'tgl_terima' => $tgl_docin);
    	$arr = $this->fl->get($cari)->result_array();
    	
    	$this->response($arr,200);	
    }
    
    function farm_get(){
    	$result = array('status'=> 0, 'content' => '');
    	$this->load->model('forecast/m_forecast','mf');
    	$id_farm = $this->get('id_farm');
    	$kode_farm = !empty($id_farm) ? $id_farm : NULL;
    	$arr = $this->mf->list_farm($id_farm)->result_array();
    	if(!empty($arr)){
    		$result['status'] = 1;
    		$result['content'] = $arr;
    	}
    	$this->response($result,200);
    }
    
    function farmlist_get(){
    	$result = array();
    	$this->load->model('forecast/m_forecast','mf');
    	$arr = $this->mf->list_farm()->result_array();
    	if(!empty($arr)){
    		$result = $arr;
    	}
    	$this->response($result,200);
    }
    
    function analisa_performance_kandang_get() {
        $this->load->model('analisa_performance_kandang/m_main');
        $kode_farm = $this->input->get ( 'kode_farm' );
        $tanggal_kebutuhan_awal = $this->input->get ( 'tanggal_kebutuhan_awal' );
        #$tanggal_kebutuhan_awal = date ( 'Y-m-d', strtotime ( $tanggal_kebutuhan_awal ) );
        $tanggal_kebutuhan_akhir = $this->input->get ( 'tanggal_kebutuhan_akhir' );
        #$tanggal_kebutuhan_akhir = date ( 'Y-m-d', strtotime ( $tanggal_kebutuhan_akhir ) );
        $result = $this->m_main->group_daftar_barang_api ( $kode_farm, $tanggal_kebutuhan_awal, $tanggal_kebutuhan_akhir );
        
        #print_r($result);

        $this->response($result,200);

        #$this->load->view ( 'daftar_barang', $data );
    }
    
    function authenticate_post(){
    	$username = $this->post('username');
    	$password = $this->post('password');
    	$isLogin = Modules::run('user/user/isLogin');
    	$this->response(array('username'=> $username,'login'=> $isLogin),200);
    }
    
    function user_get()
    {
        if(!$this->get('id'))
        {
        	$this->response(NULL, 400);
        }

        // $user = $this->some_model->getSomething( $this->get('id') );
    	$users = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!', array('hobbies' => array('fartings', 'bikes'))),
		);
		
    	$user = @$users[$this->get('id')];
    	
        if($user)
        {
            $this->response($user, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }
    
    function user_post()
    {
        //$this->some_model->updateUser( $this->get('id') );
        $message = array('id' => $this->get('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function user_delete()
    {
    	//$this->some_model->deletesomething( $this->get('id') );
        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function users_get()
    {
        //$users = $this->some_model->getSomething( $this->get('limit') );
        $users = array(
			array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com'),
			array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => array('hobbies' => array('fartings', 'bikes'))),
		);
        
        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any users!'), 404);
        }
    }


	public function send_post()
	{
		var_dump($this->request->body);
	}


	public function send_put()
	{
		var_dump($this->put('foo'));
	}
	
	/* kebutuhan pakan untuk ppic */
	public function kebutuhan_pakan_ppic_get(){
		$kodeFarm = $this->get('kodeFarm');
		$tglAwal = $this->get('tglAwal');
		$tglAkhir = $this->get('tglAkhir');
		$tanggal = array('startDate' => $tglAwal, 'endDate' => $tglAkhir);
		$this->load->model('forecast/m_forecast','mf');
		$result =  $this->mf->kebutuhan_pakan_ppic($tanggal,$kodeFarm);
		$this->response($result, 200);
		
	}
	
	/* untuk mendapatkan data yang harus disimpan ke server local farm */
	public function data_farm_get(){
		$asal = $this->get('kodeFarm');
		$lastId = $this->get('lastId');
		$data_kirim = Modules::run('sinkronisasi/sinkronisasi/getPullData',$lastId,$asal);
		$this->response($data_kirim, 200);
	}
}