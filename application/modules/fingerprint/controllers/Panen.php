<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* Controller untuk simulasi fingerprint dan bisa digunakan ketika alat fingerprint rusak
 **/
class Panen extends MY_Controller{
	private $grup_farm;
	private $_farm;
	private $_user;
	public function __construct(){
		parent::__construct();
		$this->load->helper('stpakan');
		$this->grup_farm = strtolower($this->session->userdata('grup_farm'));
		$this->_farm = $this->session->userdata('kode_farm');
		$this->_user = $this->session->userdata('kode_user');
		$this->load->model('fingerprint/m_fingerprint_panen','finger');
	}

	function simpan_transaksi_verifikasi(){
        $kode_farm = $this->_farm;
        $user = $this->_user;
        $transaction = $this->input->post('transaction');
        $kode_sopir = $this->input->post('kode_sopir');
        $date_transaction = date('Y-m-d H:i:s');
        
        $this->finger->insert(array(
                        'kode_farm'			=> $kode_farm,
                        'transaction'		=> $transaction,
                        'date_transaction'	=> $date_transaction,
                        'kode_sopir'		=> $kode_sopir
                    ));
        
            $this->result['status'] = 1;
            $this->result['content'] = array('date_transaction' => $date_transaction);   
           

        $this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
    }

	function scan_kendaraan_baru(){
        $ada = $this->finger->as_array()->order_by('date_transaction','desc')->get_by('date_verification is null and kode_farm = \''.$this->_farm.'\'');
        if(!empty($ada)){
            $this->load->model('security/m_verifikasi_do_panen','mvdp');
            if($ada['transaction'] == 'verifikasi_do_panen_masuk'){
                $cari_str = 'rpd.id_sopir = \''.$ada['kode_sopir'].'\' and vdp.tgl_verifikasi is null  and vdp.kode_farm = \''.$this->_farm.'\'';
            }else{
                $cari_str = 'rpd.id_sopir = \''.$ada['kode_sopir'].'\' and vdp.tgl_verifikasi is not null and vdp.tgl_verifikasi_sj is null  and vdp.kode_farm = \''.$this->_farm.'\'';
            }
            $data_kendaraan = $this->mvdp->withPanen()->as_array()->get_many_by($cari_str);
            if($data_kendaraan){
                $this->result['status'] = 1;
                $kendaraan = array();
                $listDO = array();
                foreach($data_kendaraan as $dk){
                    if(empty($kendaraan)){
                        $kendaraan = $dk;
                        $kendaraan['TGL_PANEN'] = convertElemenTglIndonesia($dk['TGL_PANEN']);
                        $kendaraan['JAM_MASUK'] = !empty($dk['TGL_VERIFIKASI']) ? convertElemenTglWaktuIndonesia($dk['TGL_VERIFIKASI']) : '-';
                        $kendaraan['JAM_KELUAR'] = !empty($dk['TGL_VERIFIKASI_SJ']) ? convertElemenTglWaktuIndonesia($dk['TGL_VERIFIKASI_SJ']) : '-';
                    }
                    
                    array_push($listDO,array('no_do' => $dk['NO_DO'],'no_sj' => $dk['NO_SJ']));
                }
                
                $this->result['content'] = array('do' => $listDO,'kendaraan' => $kendaraan, 'finger' => $ada);
            }else{
                $this->result['message'] = 'Data kendaraan tidak ditemukan';
            }
        }
        $this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
    }

    function cek_verifikasi($update_verifikasi = 0){
        $this->result['match'] = 0;
        $kode_farm = $this->_farm;
        $user = $this->_user;
        $date_transaction = $this->input->post('date_transaction');
        $dataFinger = $this->finger->as_array()->get_by(array('date_transaction' => $date_transaction));        
        if(!empty($dataFinger['verificator'])){
            $this->result['status'] = 1;
            if($dataFinger['verificator'] == $dataFinger['kode_sopir']){
                $this->result['match'] = 1;
                if($update_verifikasi){
                    $dataUpdate = array();
                    $dos = $this->input->post('do');
                    $whereUpdate = 'no_do in (\''.implode('\',\'',$dos).'\')';
                    if($dataFinger['transaction'] == 'verifikasi_do_panen_masuk'){
                        $dataUpdate['tgl_verifikasi'] = $dataFinger['date_verification'];
                    }else{
                        $dataUpdate['tgl_verifikasi_sj'] = $dataFinger['date_verification'];
                    }
                    
                    $this->load->model('security/m_verifikasi_do_panen','mvdp');
                    $this->mvdp->hapusAlias()->update_by($whereUpdate,$dataUpdate);
                }
            }
        }
        
        $this->output
			->set_content_type('application/json')
			->set_output(json_encode($this->result));
    }
}
