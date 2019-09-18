<?php
class M_log_sales_order extends MY_Model{
	public function __construct(){
		parent::__construct();
		$this->_table = 'log_sales_order';
	}

	public function getLogSO($so_list = array())
	{
		if(!empty($so_list)){
			$no_so = array();
			foreach ($so_list as $key => $data) {
				array_push($no_so,$data['no_so']);
			}
			// cetak_r($no_so);

			return $this->db->select('log_sales_order.*, sales_order.*, m_pegawai.nama_pegawai, convert(date,log_sales_order.tgl_buat) AS tgl ,convert(date,getdate()) AS tgl_skrg',false)
			->join('sales_order','log_sales_order.no_so = sales_order.no_so','left')
			->join('m_pegawai','log_sales_order.user_buat = m_pegawai.kode_pegawai','left')
			->where_in('log_sales_order.no_so',$no_so)->order_by('log_sales_order.no_urut','desc')->get('log_sales_order')->result_array();
		}
	}
}
