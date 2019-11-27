<?php
class M_sales_order extends MY_Model{
	protected $primary_key;
	private $dbSqlServer;
	public function __construct(){
		parent::__construct();
		$this->_table = 'sales_order';
		$this->primary_key= 'no_so';
		$this->dbSqlServer = $this->load->database('default', TRUE);
	}
	public function no_so($kode_farm)
	{
		$no_urut = 0;
		$tmp = $this->db->order_by('no_so','desc')->where('no_so like \''.$kode_farm.'\'+substring(CONVERT(VARCHAR(6), getdate(), 112), 3,2)+\'-G%\'')->get($this->_table);
        $tmp = $tmp->row(0);
        $prefix = $this->db->query('SELECT \''.$kode_farm.'\'+substring(CONVERT(VARCHAR(6), getdate(), 112), 3,2)+\'-G\' prefix')->result_array()[0]['prefix'];
        if(count($tmp) > 0){
           $no_urut = (int)substr($tmp->no_so,-4);
        }
        $no_urut++;
        $no_urut = str_pad($no_urut,4,'0',STR_PAD_LEFT);

        return $prefix.$no_urut;
	}

	public function getBarang($kode_farm = null,$kode_siklus = NULL){
		$sql = <<<SQL
				select gh.*, mb.nama_barang
				from glangsing_movement_kp gh				
				join M_BARANG mb on gh.kode_barang = mb.KODE_BARANG and mb.TIPE_BARANG = 'E'
				where gh.kode_farm = '{$kode_farm}' and gh.kode_siklus = '{$kode_siklus}' and gh.jml_stok > 0

SQL;

	    return $this->db->query($sql)->result_array();
	}

	public function getSalesOrder($kode_farm = NULL){
		$whereFarm = '';
		if(!empty($kode_farm)){
			$whereFarm = " and so.kode_farm = '".$kode_farm."'";
		}
		$sql = <<<SQL
		SELECT  so.kode_farm,sum(sod.jumlah) AS jumlah, sod.kode_barang
		FROM sales_order so
		JOIN sales_order_d sod ON sod.no_so = so.no_so
		WHERE so.tgl_so = CAST(getdate() AS DATE)
		{$whereFarm}
		GROUP BY so.kode_farm,sod.kode_barang
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function getSalesOrderHarian($kode_farm = NULL,$kode_siklus= NULL, $tgl_transaksi = NULL, $status_order = NULL){
		$whereTgl = 'CAST(getdate() AS DATE)';
		if(!empty($tgl_transaksi)){
			$whereTgl = '\''.$tgl_transaksi.'\'';
		}
		$whereFarm = '';
		if(!empty($kode_farm)){
			$whereFarm = " and so.kode_farm = '".$kode_farm."'";
		}
		$whereStatus = '';
		if(!empty($status_order)){
			$whereStatus = " and so.status_order = '".$status_order."'";
		}
		$whereSiklus = '';
		if(!empty($kode_siklus)){
			$whereSiklus = " and so.kode_siklus = '".$kode_siklus."'";
		}
		$sql = <<<SQL
		SELECT  so.kode_farm,sum(sod.jumlah) AS jumlah,so.kode_siklus, sod.kode_barang
		FROM sales_order so
		JOIN sales_order_d sod ON sod.no_so = so.no_so
		WHERE so.tgl_so = {$whereTgl} {$whereStatus} and so.status_order != 'V'
		{$whereFarm} {$whereSiklus}
		GROUP BY so.kode_farm,sod.kode_barang,so.kode_siklus
SQL;
		
		return $this->db->query($sql)->result_array();
	}

	public function getSalesOrderSJ($kode_farm = NULL,$kode_siklus = NULL, $tgl_transaksi = NULL){
		$whereTgl = 'CAST(getdate() AS DATE)';
		if(!empty($tgl_transaksi)){
			$whereTgl = '\''.$tgl_transaksi.'\'';
		}
		$whereParams = array();
		
		if(!empty($kode_farm)){
			array_push($whereParams,"so.kode_farm = '".$kode_farm."'");
		}		
		if(!empty($kode_siklus)){
			array_push($whereParams,"so.kode_siklus = '".$kode_siklus."'");			
		}
		$paramStr = '';
		if(!empty($whereParams)){
			$paramStr .= ' where '.implode(' and ',$whereParams);
		}
		$sql = <<<SQL
		SELECT so.kode_farm,sum(sjd.jumlah) AS jumlah,so.kode_siklus,sjd.kode_barang
		FROM surat_jalan sj
		JOIN surat_jalan_d sjd ON sj.no_sj = sjd.no_sj
		JOIN sales_order so ON so.no_so = sj.no_so AND so.tgl_so = {$whereTgl} and so.status_order = 'A'
		{$paramStr}
		GROUP BY sjd.kode_barang,so.kode_farm,so.kode_siklus
SQL;
		log_message('error',$sql);
		return $this->db->query($sql)->result_array();
	}


	public function getSalesOrderPengurang($kode_farm = NULL,$kode_siklus = NULL, $tgl_transaksi = NULL){
		$whereTgl = 'CAST(getdate() AS DATE)';
		if(!empty($tgl_transaksi)){
			$whereTgl = '\''.$tgl_transaksi.'\'';
		}
		$whereFarm = '';
		if(!empty($kode_farm)){
			$whereFarm = " and so.kode_farm = '".$kode_farm."'";
		}
		$whereSiklus = '';
		if(!empty($kode_siklus)){
			$whereSiklus = " and so.kode_siklus = '".$kode_siklus."'";
		}
		$sql = <<<SQL
		SELECT  so.kode_farm,sum(sod.jumlah) AS jumlah,so.kode_siklus, sod.kode_barang
		FROM sales_order so
		JOIN sales_order_d sod ON sod.no_so = so.no_so
		WHERE so.status_order = 'A' and so.tgl_so < {$whereTgl}
		and so.no_so not in (select no_so from surat_jalan where tgl_realisasi is not null)
		{$whereFarm} {$whereSiklus}
		GROUP BY so.kode_farm,sod.kode_barang,so.kode_siklus
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function listSalesOrderHarian($kode_farm = NULL,$kode_siklus = NULL, $tgl_transaksi = NULL){
		$whereTgl = 'CAST(getdate() AS DATE)';
		if(!empty($tgl_transaksi)){
			$whereTgl = '\''.$tgl_transaksi.'\'';
		}
		$whereFarm = '';
		if(!empty($kode_farm)){
			$whereFarm = " and so.kode_farm = '".$kode_farm."'";
		}
		$whereSiklus = '';
		if(!empty($kode_siklus)){
			$whereSiklus = " and so.kode_siklus = '".$kode_siklus."'";
		}
		$sql = <<<SQL
		SELECT so.*,mp.NAMA_PELANGGAN, mf.nama_farm
		FROM sales_order so
		JOIN m_pelanggan mp ON mp.KODE_PELANGGAN = so.kode_pelanggan
		JOIN m_farm mf on mf.kode_farm = so.kode_farm
		where so.tgl_so = {$whereTgl}
		{$whereFarm} {$whereSiklus}
SQL;
		
		return $this->db->query($sql)->result_array();
	}

	public function getSOList($start_date = '', $end_date = '', $user_level = ''){
		$where = '';
		if ($start_date != '') {
			$where .= "WHERE tgl_so between '$start_date' and '$end_date'";
		}else {
			$where .= "WHERE tgl_so = convert(date,getdate())";
		}
		$where .= ' and sales_order.status_order != \'V\'';
		switch ($user_level) {
			case 'KDKEU':
				$where .= "AND sales_order.status_order != 'N'";
				break;
		}
		$sql = <<<SQL
		SELECT sales_order.*, M_PELANGGAN.*,
			case when sales_order.status_order = 'N' then 'Belum Diverifikasi' end status_desc,
			pembayaran.lampiran, convert(date,getdate()) AS tgl_skrg, pembayaran.nominal_bayar
		FROM sales_order
		LEFT JOIN M_PELANGGAN ON sales_order.kode_pelanggan = M_PELANGGAN.KODE_PELANGGAN
		LEFT JOIN pembayaran ON sales_order.no_so = pembayaran.no_so
		$where
SQL;

	    return $this->db->query($sql)->result_array();
	}

	public function no_urut_log_so($no_so){
		/* dapatkan no_urut berdasarkan no_reg */
           $tmp = $this->db->order_by('no_urut','desc')->where(array('no_so'=>$no_so))->get('log_sales_order');
           $tmp = $tmp->row(0);

           if(count($tmp) > 0){
              $no_urut = $tmp->no_urut;
           }
           else{
              $no_urut = 0;
           }
           $no_urut++;
           return $no_urut;
    }
	public function simpan_verifikasi_pembayaran($so_data = null){
		$error		 = false;
		$this->dbSqlServer->trans_begin();
		foreach ($so_data as $key => $value) {
			$status_order = $value['status_order'];
			$no_urut = $this->no_urut_log_so($value['no_so']);
			// cetak_r($ext,false);
			if($status_order == 'A'){
				$data_so = array();
				$data_so['status_order'] = $status_order;
				$this->dbSqlServer->where("no_so", $value['no_so']);
				$this->dbSqlServer->update("sales_order", $data_so);

				if($this->dbSqlServer->affected_rows() > 0){
					$data_pembayaran = array();
					$data_pembayaran['status_bayar'] 	= $status_order;

					$this->dbSqlServer->where("no_so", $value['no_so']);
					$this->dbSqlServer->update("pembayaran", $data_pembayaran);

					if($this->dbSqlServer->affected_rows() > 0){
						$data_log_so = array();
						$data_log_so['no_so']     = $value['no_so'];
						$data_log_so['no_urut']   = $no_urut;
						$data_log_so['status'] 	  = $status_order;
						$data_log_so['user_buat'] = $this->session->userdata('kode_user');

						$this->dbSqlServer->insert("log_sales_order", $data_log_so);

						if($this->dbSqlServer->affected_rows() <= 0){
							$error = true;
						}
					}
					else {
						$error = true;
					}
				}else {
					$error = true;
				}
			}else {
				$upload_data = $_FILES['fileToUpload'];
				$path = $upload_data['name'][$key];
				$ext = pathinfo($path, PATHINFO_EXTENSION);
				if(move_uploaded_file($upload_data["tmp_name"][$key], 'file_upload/verifikasi_so/'.$value['kode_pembayaran'].'.'.$ext)){
					$data_so = array();
					$data_so['status_order'] = $status_order;
					$this->dbSqlServer->where("no_so", $value['no_so']);
					$this->dbSqlServer->update("sales_order", $data_so);

					if($this->dbSqlServer->affected_rows() > 0){
						$data_pembayaran = array();
						$data_pembayaran['kode_pembayaran'] = $value['kode_pembayaran'];
						$data_pembayaran['no_so'] 			= $value['no_so'];
						$data_pembayaran['nominal_harga']  	= $value['nominal_harga'];
						$data_pembayaran['nominal_bayar'] 	= $value['nominal_bayar'];
						$data_pembayaran['status_bayar'] 	= $status_order;
						$data_pembayaran['lampiran']		= 'file_upload/verifikasi_so/'.$value['kode_pembayaran'].'.'.$ext;
						$data_pembayaran['user_buat'] 		= $this->session->userdata('kode_user');

						$this->dbSqlServer->insert("pembayaran", $data_pembayaran);

						if($this->dbSqlServer->affected_rows() > 0){
							$data_log_so = array();
							$data_log_so['no_so']     = $value['no_so'];
							$data_log_so['no_urut']   = $this->no_urut_log_so($value['no_so']);
							$data_log_so['status'] 	  = $status_order;
							$data_log_so['user_buat'] = $this->session->userdata('kode_user');

							$this->dbSqlServer->insert("log_sales_order", $data_log_so);

							if($this->dbSqlServer->affected_rows() <= 0){
								$error = true;
							}
						}
						else {
							$error = true;
						}
					}else {
						$error = true;
					}
				}
			}

		}



		if(!$error){
         	$this->dbSqlServer->trans_commit();
			$result['status'] = 1;
		}
		else{
         	$this->dbSqlServer->trans_rollback();
			$result['status'] = 0;
		}
		return $result;
	}

	public function listKeteranganSO($kode_farm = NULL, $tgl_transaksi = NULL){
		$whereSO = array();
		$whereTgl = 'tgl_so = CAST(getdate() AS DATE)';
		if(!empty($tgl_transaksi)){
			$whereTgl = 'tgl_so = \''.$tgl_transaksi.'\'';
		}
		$whereSO[] = $whereTgl;
		$whereFarm = '';
		if(!empty($kode_farm)){
			$whereFarm = " kode_farm = '".$kode_farm."'";
			$whereSO[] = $whereFarm;
		}
		$whereStr = '';
		if(!empty($whereSO)){
			$whereStr = ' where '.implode(' and ',$whereSO);
		}
		$sql = <<<SQL
		SELECT lso.*,mp.nama_pegawai
		FROM log_sales_order lso
		JOIN m_pegawai mp ON mp.KODE_PEGAWAI = lso.user_buat
		WHERE no_so IN (
			SELECT no_so FROM sales_order {$whereStr}
		)
SQL;

	    return $this->db->query($sql)->result_array();
	}
}
