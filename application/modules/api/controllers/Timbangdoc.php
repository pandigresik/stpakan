<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Changes:
 * 1. This project contains .htaccess file for windows machine.
 *    Please update as per your requirements.
 *    Samples (Win/Linux): http://stackoverflow.com/questions/28525870/removing-index-php-from-url-in-codeigniter-on-mandriva
 *
 * 2. Change 'encryption_key' in application\config\config.php
 *    Link for encryption_key: http://jeffreybarke.net/tools/codeigniter-encryption-key-generator/
 *
 * 3. Change 'jwt_key' in application\config\jwt.php
 *
 */

class Timbangdoc extends REST_Controller
{
    protected $result = array('status' => 0, 'message' => '', 'content' => '');

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('authorization','jwt','stpakan'));      
        $this->checkToken();  
    }
	
    private function checkToken(){
        $headers = $this->input->request_headers();
        $result = false;        
    //    log_message('error',json_encode($headers));
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken != false) {                    
                $this->decodedToken = $decodedToken;
                $result = true;
            }
        }

        if(!$result){
            $this->response("Unauthorized", 401);
            return;
        }
        
    }

    public function kandang_get()
    {
        $content = $this->db->select(['kandang_siklus.kode_kandang','jml_populasi','kode_siklus', 'kandang_siklus.no_reg', 'status_siklus', 'coalesce(timbang_doc.status,0) as status_timbang','kode_verifikasi','ssid','ip_timbangan'])->where('kode_siklus in (select kode_siklus from m_periode where status_periode = \'A\')')
->join('m_kandang','m_kandang.kode_kandang = kandang_siklus.kode_kandang and m_kandang.kode_farm = kandang_siklus.kode_farm','inner')
->join('sys_ssid','sys_ssid.kode_kandang = kandang_siklus.kode_kandang and sys_ssid.kode_farm = kandang_siklus.kode_farm','inner')
->join('timbang_doc','timbang_doc.no_reg = kandang_siklus.no_reg','left')->get('kandang_siklus')->result_array();

//log_message('error',$this->db->last_query());
        if (!empty($content)) {
            $this->result['status'] = 1;
            $this->result['content'] = $content;
        } else {
            $this->result['message'] = 'Data tidak ditemukan';
        }
        $this->response($this->result, 200);
    }

    public function detail_get()
    {
	$this->load->model('m_timbang_doc','md');
        $this->load->model('m_timbang_doc_detail','mdtl');
        $rata = $status_timbang = 0;
	$noreg = $this->get('noreg');
        $content = $this->mdtl->as_array()->get_many_by(['no_reg' => $noreg]);
        if (!empty($content)) {
	    $resume = $this->md->get($noreg);
	    $rata = $resume->BB_RATA2;
            $status_timbang = $resume->STATUS;
	    	
        } 
	$this->result['status'] = 1;
        $this->result['rata'] = $rata;
        $this->result['status_timbang'] = $status_timbang;
        $this->result['content'] = $content;
        $this->response($this->result, 200);
    }

    public function resume_get()
    {
	$this->load->model('m_timbang_doc','md');
        $rata = 0;
	$noreg = $this->get('noreg');
        $content = $this->md->as_array()->get($noreg);
        $resume = $this->md->get($noreg);	    
            $this->result['status'] = 1;
            $this->result['content'] = $content;

        
        $this->response($this->result, 200);
    }

    public function simpan_post()
    {
        $noreg = $this->post('noreg');
        $jmlekor = $this->post('jmlekor');
        $jmlbox = $this->post('jmlbox');
        $tarabox = $this->post('tarabox');
        $beratboxdoc = $this->post('beratboxdoc');
        $selesai = $this->post('selesai');
        $berat = ($beratboxdoc - $tarabox);

        $kode_pegawai = $this->decodedToken->kode_user;
        $this->load->model('m_timbang_doc','md');
        $this->load->model('m_timbang_doc_detail','mdtl');
        
        $ada = $this->md->as_array()->get_by(['no_reg' => $noreg]);
        if(!empty($ada)){
            $this->md->update($noreg,[
                'jml_box' => $ada['JML_BOX'] + $jmlbox,
                'jml_ekor' => $ada['JML_EKOR'] + $jmlekor,
                'total_berat' => $ada['TOTAL_BERAT'] + $berat,
                'bb_rata2' => (($ada['TOTAL_BERAT'] + $berat) * 1000) / ($ada['JML_EKOR'] + $jmlekor), 
		'jml_timbang' => $ada['JML_TIMBANG'] + 1,
                'status' => $selesai ? 1 : 0
            ]);
        }else{
            $this->md->insert([
                'no_reg' => $noreg,
                'jml_box' => $jmlbox,
                'jml_ekor' => $jmlekor,
                'total_berat' => $berat,
                'bb_rata2' => ($berat / $jmlekor), 
                'status' => $selesai ? 1 : 0
            ]);
        }

        $this->mdtl->insert([
            'no_reg' => $noreg,
            'jml_box' => $jmlbox,
            'jml_ekor' => $jmlekor,
            'tara_box' => $tarabox,
            'berat' =>  $berat,
            'user_buat' => $kode_pegawai
        ]);

        $this->result['status'] = 1;
        $this->result['message'] = 'Data penimbangan berhasil disimpan';
        
        $this->response($this->result, 200);
    }

    public function simpanDataTimbang_post(){
	$detailTimbang = json_decode($this->post('detail'),1);
        $kode_pegawai = $this->decodedToken->kode_user;
        $this->load->model('m_timbang_doc','md');
        $this->load->model('m_timbang_doc_detail','mdtl');
	$listNoreg = array();
	$dataDetail = array();        
	foreach($detailTimbang as $dt){
		$noreg = $dt['NO_REG'];
		$jmlbox = $dt['JML_BOX'];
		$jmlekor = $dt['JML_EKOR'];
		$tarabox = $dt['TARA_BOX'];
		$berat = $dt['BERAT'];
		if(!isset($listNoreg[$noreg])){
			$listNoreg[$noreg] = [
				'no_reg' => $noreg,	
		        'jml_box' => 0,
				'jml_ekor' => 0,
				'total_berat' => 0,
				'bb_rata2' => 0, 
				'jml_timbang' => 0,
				'status' => 1
			];
		}
		array_push($dataDetail,[
			'no_reg' => $noreg,
			'jml_box' => $jmlbox,
			'jml_ekor' => $jmlekor,
			'tara_box' => $tarabox,
			'berat' =>  $berat,
			'user_buat' => $kode_pegawai
		]);
		$listNoreg[$noreg]['jml_box'] += $jmlbox;
		$listNoreg[$noreg]['jml_ekor'] += $jmlekor;
		$listNoreg[$noreg]['total_berat'] += $berat;
		$listNoreg[$noreg]['jml_timbang'] += 1;
		$listNoreg[$noreg]['bb_rata2'] = round((($listNoreg[$noreg]['total_berat'] * 1000) / $listNoreg[$noreg]['jml_ekor']),2);
	}
	/* hapus dulu datanya untuk memastikan saja*/
	$this->mdtl->delete_by(['no_reg' => array_keys($listNoreg)]);
	$this->md->delete_by(['no_reg' => array_keys($listNoreg)]);
	
	$this->md->insert_many($listNoreg);
	$this->mdtl->insert_many($dataDetail);
//	log_message('error',json_encode($listNoreg));
	
        $this->result['status'] = 1;
        $this->result['detail'] = array_keys($listNoreg);
        $this->result['message'] = 'Data penimbangan berhasil disimpan';
        
        $this->response($this->result, 200);
    }
}
