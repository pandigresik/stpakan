<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cop extends Cstpakan_Controller{
	public function __construct(){
		parent::__construct();
		$config = array('server'    => 'http://localhost/CI_3');
		$this->init($config);		
	}
	
	public function get_op(){
		/* contoh untuk between pada date_param
		 * array('field'=> 'TGL_UBAH','operator'=> 'Between','value'=>array('2015-08-01','2015-09-01'))
		 */
		$data = array(
			'where' => array(),
			'date_param' => array('field'=> 'TGL_UBAH','operator'=> 'Kurang','value'=>'2015-10-01'),		
		);
	
		$tweets = $this->rest->get('/webservice/wop/op/',$data,'json');
		print_r($tweets); 
	}
	
	public function create_op($data){	
		$op = $this->rest->post('webservice/wop/op',$data);
		return $op;
	}
	
	public function delete_op($data){
		$op = $this->rest->delete('webservice/wop/op',$data);
		return $op;
	}
	
	public function approve_pp(){
		$no_pp = $this->input->get('pp');
		$no_op = $this->input->get('op');
		/* ambil semua data yang berhubungan dengan proses approve pp tersebut 
		 * lpb,lpbd,lpbe dan op 
		 */
		$this->load->model('permintaan_pakan/m_lpb','lpb');
		$this->load->model('permintaan_pakan/m_lpbd','lpbd');
		$this->load->model('permintaan_pakan/m_lpbe','lpbe');
		$this->load->model('permintaan_pakan/m_op','op');
		$this->load->model('permintaan_pakan/m_op_d','opd');
		$lpb = $this->lpb->get(array('no_lpb' => $no_pp))->result_array();
		$lpbd = $this->lpbd->get(array('no_lpb' => $no_pp))->result_array();
		$lpbe = $this->lpbe->get(array('no_lpb' => $no_pp))->result_array();
		$op = $this->op->get(array('no_op' => $no_op))->result_array();
		$opd = $this->opd->get(array('no_op' => $no_op))->result_array();
		$data = array('data_pp' => array(
				'lpb' => $lpb,
				'lpbd' => $lpbd,
				'lpbe' => $lpbe,
				'op' => $op,
				'opd' => $opd
			)
		);
		
		$pp = $this->rest->post('webservice/wapprovepp/pp',$data);
		print_r($pp);
	}
}
