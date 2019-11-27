<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Verifikasi_kendaraan extends MY_Controller {
    protected $_user;
    protected $_farm;
    protected $_grup_farm;
    private $_limit = 10;
    public function __construct() {
        parent::__construct();
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
    }

    public function index() {
        $data['grup_farm']=$this->_grup_farm;
        $this->load->view("index", $data);
    }

    public function lists($offset = 0){
        $filters = $this->input->get('filter');
        $tindaklanjut = $filters['tindaklanjut'];
        $pencarian = array();
        foreach($filters as $key => $val){
            switch($key){
                case 'tindaklanjut':
                    if($val){
                        array_push($pencarian,'(vdp.tgl_verifikasi is null or vdp.tgl_verifikasi_sj is null)');
                    }
                    break;
                case 'tgl_panen':
                    if($val){
                        array_push($pencarian,'rpd.tgl_panen = \''.$val.'\'');
                    }
                    break;
                case 'nopol':
                    if($val){
                        array_push($pencarian,'vdp.nopol like \'%'.$val.'%\'');
                    }
                    break;    
                case 'sopir':
                    if($val){
                        array_push($pencarian,'vdp.nama_sopir like \'%'.$val.'%\'');
                    }
                    break;        
                case 'kandang':
                    if($val){
                        array_push($pencarian,'substring(rpd.no_reg,len(rpd.no_reg)-1,2) like \'%'.$val.'%\'');
                    }
                    break;        
                case 'no_do':
                    if($val){
                        array_push($pencarian,'vdp.no_do like \'%'.$val.'%\'');
                    }
                    break;                 
                case 'no_sj':
                    if($val){
                        array_push($pencarian,'vdp.no_sj like \'%'.$val.'%\'');
                    }
                    break;            
            }
        }
        
        if(!empty($filters['awal_panen'])){
            if(!empty($filters['akhir_panen'])){
                array_push($pencarian,'(rpd.tgl_panen between \''.$filters['akhir_panen'].'\' and  \''.$filters['akhir_panen'].'\')');
            }else{
                array_push($pencarian,'rpd.tgl_panen >= \''.$filters['awal_panen'].'\'');
            }
        }else{
            if(!empty($filters['akhir_panen'])){
                array_push($pencarian,'rpd.tgl_panen <= \''.$filters['akhir_panen'].'\'');
            }
        }

        $pencarian_str = implode(' and ',$pencarian);
        
        $this->load->model('security/m_verifikasi_do_panen','mvdp');
        if(!empty($pencarian)){
            $data = $this->mvdp->limit($this->_limit,$offset)->order_by('rpd.tgl_panen')->withPanen()->as_array()->get_many_by($pencarian_str);
            $countData = $this->mvdp->withPanen()->as_array()->count_by($pencarian_str);
        }else{
            $data = $this->mvdp->limit($this->_limit,$offset)->order_by('rpd.tgl_panen')->withPanen()->as_array()->get_all();
            $countData = $this->mvdp->withPanen()->as_array()->count_all();
        }
        
        $this->result['status'] = 1;
        $this->result['content'] = array(
            'data' => $this->load->view('security/do_panen_list',array('data' => $data),TRUE),
            'pagination' => $this->generate_paging($countData,$this->_limit)
        );
        $this->output
		    ->set_content_type('application/json')
		    ->set_output(json_encode($this->result));
    }

    private function generate_paging($jml,$limit){
        $this->load->library('pagination');
        $this->load->config('pagination');
        $config = $this->config->item('pagination');
		$config['total_rows'] = $jml;
        $config['per_page'] = $limit;
        $config['uri_segment'] = 4;
		
		$this->pagination->initialize ( $config );
		return $this->pagination->create_links ();
    }

}
