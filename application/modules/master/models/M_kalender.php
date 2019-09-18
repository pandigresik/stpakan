<?php
class M_kalender extends MY_Model{
	protected $_table = 'm_kalender';
	public function __construct(){
		parent::__construct();
	}

	public function getDayoffs($date){
		$sql = <<<sql
			select * from excelbbreport.dbo.dayoffs
			where datename(dw,dayoff) != 'Sunday'
			and dayoff > '{$date}'
sql;
	return $this->db->query($sql);
	}

	public function getLastDay(){
		return $this->db->select_max('TANGGAL')->get($this->_table);
	}


	}
