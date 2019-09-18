<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends REST_Controller {
	
	public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('authorization', 'jwt'));
		$this->load->model('user/m_user','m_user');
		$this->load->config('stpakan');
    }
    /* dapatkan semua user yang aktif untuk disimpan di db local android
     * yang disimpan data user ( username, password, nama, grup_pegawai, token, access_noreg )
     * */
	public function active_get(){
		$result = [];
		$output = ['status' => 0];
		
		$pengawas = $this->db->distinct()
					->select(['pengawas','koordinator','no_reg'])
					->where(' kode_siklus in (select kode_siklus from m_periode where status_periode = \'A\')')
					->get('m_ploting_pelaksana')->result_array();
		$accessPegawai = [];
		if(!empty($pengawas)){
			foreach($pengawas as $p){
				if(!isset($accessPegawai[$p['pengawas']])){
					$accessPegawai[$p['pengawas']] = [];
				}
				
				if(!isset($accessPegawai[$p['koordinator']])){
					$accessPegawai[$p['koordinator']] = [];
				}
				array_push($accessPegawai[$p['pengawas']],$p['no_reg']);
				array_push($accessPegawai[$p['koordinator']],$p['no_reg']);
			}
		}
		
		$listKodePegawai = [];
		if(!empty($accessPegawai)){
			$listKodePegawai = array_merge($listKodePegawai,array_keys($accessPegawai));
		}
		
		$pegawais = $this->m_user->as_array()->get_many_by(['kode_pegawai' => $listKodePegawai, 'status_pegawai' => 'A']);
		$pegawaiDh = $this->m_user->as_array()->get_many_by(['grup_pegawai' => 'DH', 'status_pegawai' => 'A']);
		$pegawais = array_merge($pegawais,$pegawaiDh);

		if(!empty($pegawais)){
			foreach($pegawais as $p){
				$tokenData['kode_user'] = $p['KODE_PEGAWAI'];
				$tokenData['level_user'] = $p['GRUP_PEGAWAI'];
				$tokenData['kode_farm'] = $this->config->item('idFarm');
				$p['token'] = AUTHORIZATION::generateToken($tokenData);
				$p['access_noreg'] = isset($accessPegawai[$p['KODE_PEGAWAI']]) ? implode(',',array_unique($accessPegawai[$p['KODE_PEGAWAI']])) : "";
				array_push($result,$p);
			}
			$output['user'] = $result;
			$output['status'] = 1;
		}
		
		$this->response($output, 200);	
	}
}
