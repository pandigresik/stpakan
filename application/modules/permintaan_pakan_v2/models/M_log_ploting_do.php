<?php
class M_log_ploting_do extends MY_Model{
	protected $_table;
	private $_user;
	protected $primary_key;
	protected $before_create = array('no_urut');
	public function __construct(){
		parent::__construct();
		$this->_table = 'LOG_PLOTING_DO';
		//$this->primary_key = 'no_do';
		$this->_user = $this->session->userdata('kode_user');
	}

	public function no_urut($row)
	{
			if (is_object($row))
			{
				$no_do = $row->no_do ;
			}
			else
			{
				$no_do = $row['no_do'];
			}
		/* dapatkan no_urut berdasarkan no_do */
			$tmp = $this->order_by('no_urut','desc')->get_by(array('no_do'=>$no_do));
			if(count($tmp) > 0){
				$no_urut = $tmp->NO_URUT;
			}
			else{
				$no_urut = 0;
			}
			$no_urut++;
			if (is_object($row))
			{
					$row->no_urut = $no_urut;
			}
			else
			{
					$row['no_urut'] = $no_urut;
			}
			return $row;
	}
}
