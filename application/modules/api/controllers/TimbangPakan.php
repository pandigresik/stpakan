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

class TimbangPakan extends REST_Controller
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

    public function dropping_get()
    {
		$no_reg = $this->get('noreg');
        $content = $this->db->select(['ok.tgl_kirim','oke.tgl_kebutuhan','okd.no_order', 'okd.no_reg',' oke.kode_barang','mb.nama_barang','oke.jml_order', 'coalesce(tp.status,0) as status_timbang'])
						->where_in('oke.no_reg',$no_reg)
						->join('order_kandang_d okd','okd.no_order = ok.no_order and okd.kode_farm = ok.kode_farm','inner')
						->join('order_kandang_e oke','oke.no_order = okd.no_order and oke.no_reg = okd.no_reg','inner')
						->join('m_barang mb','mb.kode_barang = oke.kode_barang','inner')
						->join('timbang_pakan tp','tp.no_reg = oke.no_reg and tp.no_order = oke.no_order','left')
						->get('order_kandang ok')
						->result_array();
//log_message('error',$this->db->last_query());
        if (!empty($content)) {
            $this->result['status'] = 1;
            $this->result['content'] = $content;
        } else {
            $this->result['message'] = 'Data tidak ditemukan';
        }
        $this->response($this->result, 200);
    }
    
    public function simpanTimbang_post(){
        $detailTimbang = json_decode($this->post('detail'),1);
        $detailTimbangSilo = json_decode($this->post('detailSilo'),1);
        /*$detailTimbang = [
            ["berat"=>149.9,"tgl_buat"=>"2019/08/10 09:26:27","id"=>1,"jml"=>3,"no_order"=>"2019-1/0001","no_reg"=>"WK/2019-1/01","no_urut"=>1],
            ["berat"=>149.9,"tgl_buat"=>"2019/08/10 09:32:56","id"=>2,"jml"=>3,"no_order"=>"2019-1/0003","no_reg"=>"WK/2019-1/01","no_urut"=>1]
        ];*/
        $kode_pegawai = $this->decodedToken->kode_user;
        $this->load->model('m_timbang_pakan','md');
        $this->load->model('m_timbang_pakan_detail','mdtl');
        $this->load->model('m_timbang_pakan_silo_detail','mdtls');
		$listNoreg = array();
		$dataDetail = array();        
        $details = [];
        if(!empty($detailTimbang)){
            foreach($detailTimbang as $dt){
                $noreg = $dt['no_reg'];
                $no_order = $dt['no_order'];
                if(!isset($listNoreg[$noreg])){
                    $listNoreg[$noreg] = [];
                }
                
                if(!isset($listNoreg[$noreg][$no_order])){
                        $listNoreg[$noreg][$no_order] = [
                            'no_reg' => $noreg,	
                            'no_order' => $no_order,
                            'user_buat' => $kode_pegawai,
                            'tgl_buat' => $dt['tgl_buat'],
                            'status' => 1,
                            'berat' => 0
                        ];
                    }
                $listNoreg[$noreg][$no_order]['tgl_buat'] = $dt['tgl_buat'];
                $listNoreg[$noreg][$no_order]['berat'] += $dt['berat'];
                unset($dt['id']);
                array_push($dataDetail,$dt);
                
            }
            
            foreach($listNoreg as $noreg => $timbang){
                /* hapus dulu datanya untuk memastikan saja*/
                $this->mdtl->delete_by(['no_reg' => $noreg,'no_order' => array_keys($timbang)]);
                $this->md->delete_by(['no_reg' => $noreg,'no_order' => array_keys($timbang)]);
                $details[$noreg] = 	array_keys($timbang);
                $this->md->insert_many($timbang);
            }
            
            $this->mdtl->insert_many($dataDetail);
        }	
        
        if(!empty($detailTimbangSilo)){
            foreach($detailTimbangSilo as $dts){
                $this->mdtls->delete_by(['no_reg' => $dts['no_reg'], 'no_urut' => $dts['no_urut']]);
                unset($dts['id']);
                unset($dts['uploaded']);
                $this->mdtls->insert($dts);
            }
        }

        $this->result['status'] = 1;
        $this->result['detail'] = $details;
        $this->result['detailSilo'] = $detailTimbangSilo;
        
        $this->result['message'] = 'Data penimbangan berhasil disimpan';
        
        $this->set_response($this->result, 200);
    }
}
