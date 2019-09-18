<?php
class M_report_forecast_pp extends CI_Model{
	public function __construct(){
		parent::__construct();
	}
	public function forecast_vs_pp($kodefarm){
		$sql = <<<SQL
		exec forecast_pp '{$kodefarm}'
SQL;
		return $this->db->query($sql);
	}
}
