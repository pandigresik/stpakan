<?php
class M_ppsk extends MY_Model{
	protected $before_create = array('no_ppsk');
	protected $primary_key;
	public function __construct(){
		parent::__construct();
		$this->_table = 'PPSK';
		$this->primary_key= 'NO_PPSK';
	}
	public function no_ppsk($row)
	{
			if (is_object($row))
			{
				$no_ppsk = $row->NO_PPSK ;
			}
			else
			{
				$no_ppsk = $row['no_ppsk'];
			}
		/* dapatkan no_urut berdasarkan no_reg */
			$tmp = $this->order_by('no_ppsk','desc')->get_by('no_ppsk like \''.$no_ppsk.'%\'');
			if(count($tmp) > 0){
				$no_urut_ppsk = (int)substr($tmp->NO_PPSK,-3);
			}
			else{
				$no_urut_ppsk = 0;
			}
			$no_urut_ppsk++;
			$no_urut_ppsk = str_pad($no_urut_ppsk,3,'0',STR_PAD_LEFT);
			if (is_object($row))
			{
					$row->no_ppsk = $no_ppsk.$no_urut_ppsk;
					//$row->status = 'D';
			}
			else
			{
					$row['no_ppsk'] = $no_ppsk.$no_urut_ppsk;
					//$row['status'] = 'D';
			}
			return $row;
	}
}
