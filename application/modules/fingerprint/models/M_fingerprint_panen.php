<?php
class M_fingerprint_panen extends MY_Model{
	
	public function __construct(){
		parent::__construct();
		$this->_table = 'fingerprint_verification_panen';
	}
}