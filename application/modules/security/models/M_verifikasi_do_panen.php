<?php
class M_verifikasi_do_panen extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'Verifikasi_DO_Panen vdp';
	}

	public function withPanen(){
		$this->db->select('vdp.*,rpd.NO_REG,rpd.TGL_PANEN');
		$this->db->join('realisasi_panen_do rpd','rpd.no_do = vdp.no_do');
		return $this;
	}

	public function hapusAlias(){
		$this->_table = 'Verifikasi_DO_Panen';
		return $this;
	}
}
