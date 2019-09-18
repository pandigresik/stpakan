<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* Controller untuk simulasi fingerprint dan bisa digunakan ketika alat fingerprint rusak
 **/
class Fingerprint extends MY_Controller{
	private $grup_farm;
	private $_farm;
	private $_user;
	public function __construct(){
		parent::__construct();
		$this->load->helper('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_farm = $this->session->userdata('kode_farm');
		$this->_user = $this->session->userdata('kode_user');
		$this->load->model('fingerprint/m_fingerprint','finger');
	}

	public function index(){
		$lockSimulasi = 1;
		$simulasi = $this->db->select(array('kode_config','value'))->where(array('kode_config' => '_lock_simulasi','kode_farm' => $this->_farm,'context' => 'simulasi_finger','status' => '1'))->get('SYS_CONFIG_GENERAL')->row_array();        
        if(!empty($simulasi)){
			$lockSimulasi = $simulasi['value'];
        }
        
		if(!$lockSimulasi){
			/* ambil semua karyawan yang sudah didaftarkan fingernya dan masih aktif */
			$listPegawai = $this->finger->getListKaryawanFinger($this->_farm)->result_array();
			$this->load->view('fingerprint/index',array('listPegawai' => $listPegawai));
		}else{
			echo 'Halaman simulasi finger di non-aktifkan untuk sementara waktu';
		}
	}

	public function verification(){
		$user = $this->input->post('kode_user');
		$this->db->set('date_verification','getdate()',false);
		$update = $this->finger->update_by('verificator is null',array('verificator' => $user));
		if($update){
			$this->result['status'] = 1;
			$this->result['message'] = 'Verifikasi finger berhasil';
		}else{
			$this->result['message'] = 'Verifikasi finger gagal';
		}
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
	}

	function simpan_transaksi_verifikasi(){
        $kode_farm = $this->_farm;
        $user = $this->_user;
        $transaction = $this->input->post('transaction');
        $kode_flok = $this->input->post('kode_flok');
        $result = $this->finger->simpan_transaksi_verifikasi($kode_farm, $user, $transaction, $kode_flok);
        
        echo json_encode($result);
    }

	function cek_verifikasi(){
		$this->load->config('stpakan');
        $kode_farm = $this->_farm;
        $user = $this->_user;
        $date_transaction = $this->input->post('date_transaction');
        $kode_flok = $this->input->post('kode_flok');
        /** jika ada parameter noreg, maka pastikan yang melakukan finger adalah operator kandang tersebut */
        $no_reg = $this->input->post('noreg');
		$level = $this->input->post('level');
		$verificator = $this->input->post('verificator'); /** verificatornya harus sama */
        $lockFinger = $this->config->item('lockFinger');
        if(!$lockFinger){
            $result = array('kode_pegawai' => 'PG0001', 'nama_pegawai' => 'BPM', 'verificator' => 'PG0001');
            if(!empty($level)){
                $result['status'] = 1;
                $result['match'] = 1;
            }
        }else{
            if(!empty($level)){
                $result = $this->finger->cek_verifikasi($kode_farm, $user, $date_transaction, $kode_flok);
                if(!empty($result['kode_pegawai'])){
                    $result['status'] = 1;
                    $result['match'] = 0;
                    /** cek diplotting pelaksana */
                    $cari_petugas = array(
                        'no_reg' => $no_reg                         
                    );
                    /** jika $level == KAFARM, maka bandingkan dengan user yang sedang login */
                    if($level == 'KAFARM'){
                        if($result['kode_pegawai'] == $this->_user){
                            $result['match'] = 1;
                        }
                    }else{
                        $plotting = $this->db->where($cari_petugas)->get('m_ploting_pelaksana')->result_array();
                        if(!empty($plotting)){
                            foreach($plotting as $_p){
                                if($result['kode_pegawai'] == $_p[$level]){
                                    $result['match'] = 1;
                                }
                            }
                        }
                    }
                    
                }else{
                    $result = array('status' => 0);
                }                
            }else{
				$result = $this->finger->cek_verifikasi($kode_farm, $user, $date_transaction, $kode_flok);	
					$result['status'] = 0;
					if(!empty($result['verificator'])){
						$result['status'] = 1;
						$result['match'] = 1;
						if(!empty($verificator)){
							if($verificator != $result['verificator']){
								$result['match'] = 0;
							}
						}
					}
				
            }            
        }
                
        echo json_encode($result);
    }
}
