<?php
class M_verifikasi_do_pakan extends MY_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'Verifikasi_DO_Pakan';
	}
}
