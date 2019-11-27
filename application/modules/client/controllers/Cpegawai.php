<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cpegawai extends Cstpakan_Controller{

	public function get_pegawai(){
		$pegawai = $this->rest->get('stpakan/wmpegawai/mpegawai/',array(),'json');
		return $pegawai;
	}

	public function create_pegawai($data){
		$pegawai = $this->rest->post('stpakan/wmpegawai/mpegawai',$data);
		return $pegawai;
	}

	public function delete_pegawai($data){
		$pegawai = $this->rest->delete('stpakan/wmpegawai/mpegawai',$data);
		return $pegawai;
	}
}
