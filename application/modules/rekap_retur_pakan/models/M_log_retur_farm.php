<?php
class M_log_retur_farm extends MY_Model{	
	protected $_table;
	protected $before_create = array('setUrutan');
	public function __construct(){
		parent::__construct();
		$this->_table = 'LOG_RETUR_FARM';
	}

	protected function setUrutan($data){		
		if(is_array($data)){
			$urutanTerakhir = $this->limit(1)->order_by('no_urut','desc')->as_array()->get_by(array('no_retur'=>$data['no_retur']));			
			$noUrutSelanjutnya = empty($urutanTerakhir) ? 1 : intval($urutanTerakhir['NO_URUT']) + 1;			
			$data['no_urut'] = $noUrutSelanjutnya;
		}
		return $data;
	}

	public function history($noRetur){
		$sql = <<<SQL
		select rfd.*,mb.NAMA_PEGAWAI 
		from {$this->_table} rfd
		join m_pegawai mb on mb.kode_pegawai = rfd.user_buat		
		where no_retur = '{$noRetur}'
		order by no_urut desc
SQL;
		return $this->db->query($sql)->result_array();		
	}

}
