<?php
class M_transaksi extends MY_Model {

    private $dbSqlServer;

    public function __construct() {
        parent::__construct();        
    }

	function doGet_data_farm($kode_farm) {		
		$sql = <<<QUERY
			select * from m_farm where kode_farm = '$kode_farm'

QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetch(0);

		return $hasil['NAMA_FARM'];
	}
	function doRead_periode() {
		$nama_farm 	= ($this->input->post("nama_farm")) ? $this->input->post("nama_farm") : '';
		$id_budget 	= ($this->input->post("search[value]")) ? $this->input->post("search[value]") : '';
		$status 	= ($this->input->post("status")) ? $this->input->post("status") : '';
		$siklus 	= ($this->input->post("siklus")) ? $this->input->post("siklus") : '';
		$user_level = $this->session->userdata('level_user');

		$kodeBaru = array(
			'KDV' => 'KDV',
			'KDV' => 'WKDV', /* memiliki hak akses yang sama dengan kadiv*/
			'DP' => 'KDP',
			'KD' => 'WKDP',
			'KF'  => 'KFM',
			'P' => 'PPB',
			'P' => 'KPPB',
			'AG' => 'AGF',
			'KA' => 'KBA',
			'KA' => 'ABP',
         'KDB'=> 'WKBA'
		);

		switch ($user_level) {
		 	case 'KF':
			 	if($status == ''){
		 			$status = "'','N','D','RJ','C','A','R'";
			 	}else{
			 		$status = "'".$status."'";
			 	}
		 		$grup_user = "";
		 		break;
		 	case 'KD':
		 		if($status == ''){
		 			$status = "'A','N','R','RJ','C'";
			 	}else{
			 		$status = "'".$status."'";
			 	}
		 		$grup_user = "";
		 		break;
		 	case 'KDV':
		 		if($status == ''){
		 			$status = "'A','R','RJ','C'";
			 	}else{
			 		$status = "'".$status."'";
			 	}
		 		$grup_user = $kodeBaru[$user_level];
		 		break;
		 	case 'KDB':
		 		if($status == ''){
		 			$status = "'A','R','RJ','C'";
			 	}else{
			 		$status = "'".$status."'";
			 	}
		 		$grup_user = $kodeBaru[$user_level];
		 		break;
		 	default:
		 		# code...
		 		break;
		}


		$number = 1;
		$result['aaData'] = array();
		$sql = <<<QUERY
			select M_PERIODE.*,M_FARM.KODE_FARM,M_FARM.NAMA_FARM,
			case
				when bg.STATUS = 'D' then 'Draft'
				when bg.STATUS = 'N' then 'New (Rilis)'
				when bg.STATUS = 'R' then 'Review'
				when bg.STATUS = 'RJ' then 'Rejected'
				when bg.STATUS = 'A' then 'Approved'
				when bg.STATUS = 'C' then 'Closed'
			end STATUS_DESC,
			bg.TGL_BUAT,bg.STATUS,
			lbgrj.NAMA_PEGAWAI,
			lbgrj.GRUP_PEGAWAI
			from M_PERIODE
			left join BUDGET_GLANGSING bg on M_PERIODE.KODE_SIKLUS = bg.KODE_SIKLUS
			LEFT JOIN (
				SELECT lbg.*,mp.* FROM(
					SELECT kode_siklus, max(NO_URUT_APPROVE) NO_URUT_APPROVE FROM LOG_BUDGET_GLANGSING
					GROUP BY KODE_SIKLUS
				)lbgmax
				JOIN LOG_BUDGET_GLANGSING lbg ON lbg.KODE_SIKLUS = lbgmax.KODE_SIKLUS AND lbg.NO_URUT_APPROVE = lbgmax.NO_URUT_APPROVE
				LEFT JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = lbg.USER_BUAT
			) lbgrj
			ON bg.KODE_SIKLUS = lbgrj.KODE_SIKLUS AND bg.status = 'RJ' AND lbgrj.GRUP_PEGAWAI LIKE '%$grup_user%'
			left join M_FARM on M_FARM.KODE_FARM = M_PERIODE.KODE_FARM
			WHERE M_FARM.NAMA_FARM LIKE '%$nama_farm%' AND
		    M_PERIODE.kode_siklus in ( select kandang_siklus.kode_siklus from kandang_siklus where kandang_siklus.status_siklus<>'P' and m_periode.kode_farm=kandang_siklus.kode_farm)
		    and isnull(bg.STATUS,'') in ($status)
		    and M_PERIODE.PERIODE_SIKLUS like '%$siklus%'
			ORDER BY coalesce(bg.TGL_BUAT,getdate()) DESC, M_PERIODE.KODE_SIKLUS DESC
QUERY;
					
		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);


		foreach ($hasil as $key=>$data) {
			//echo "update t_users set user_password = '".base64_encode($data->MEMBER_ID)."' where user_name = '".$data->MEMBER_ID."';";
			$array = array(
				$number++,
				$data['KODE_SIKLUS'],
				$data['KODE_FARM'],
				$data['PERIODE_SIKLUS'],
				$data['KODE_STRAIN'],
				$data['STATUS_PERIODE'],
				$data['STATUS_DESC'],
				$data['STATUS'],
				$data['TGL_BUAT'],
				$data['NAMA_FARM'],
			);

			if($data['STATUS'] != 'RJ'){
				array_push($result['aaData'],$array);
			} else if($data['STATUS'] == 'RJ' && $data['GRUP_PEGAWAI'] != ''){
				array_push($result['aaData'],$array);
			}
		}

			return json_encode($result);

	}

	function doLoad_budget_glangsing() {
		$kategori 	= ($this->input->post("kategori")) ? $this->input->post("kategori") : '';
		$number = 1;
		$result = array();
		$sql = <<<QUERY
			select *
			from M_BUDGET_PEMAKAIAN_GLANGSING where KATEGORI_BUDGET = '$kategori'
			and STATUS = 'A'
			order by tgl_buat desc
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($hasil as $key=>$data){			
			$result[] = $data;
		}
		$main = array('success'=>true,'rows'=>$result);
		return json_encode($main);
	}

	function doCek_status_siklus($kode_farm,$kode_siklus) {
		$result = array();
		$sql = <<<QUERY
			SELECT * FROM BUDGET_GLANGSING
			LEFT JOIN M_PERIODE ON BUDGET_GLANGSING.KODE_SIKLUS = M_PERIODE.KODE_SIKLUS
			WHERE BUDGET_GLANGSING.STATUS != 'C' AND M_PERIODE.KODE_FARM = '$kode_farm' AND BUDGET_GLANGSING.KODE_SIKLUS < '$kode_siklus'
QUERY;

		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($hasil) > 0){			
			foreach($hasil as $key=>$data){
				$periode_siklus = $data['PERIODE_SIKLUS'];
			}
			return array('denied',$periode_siklus);
		}
		else{
			return array('allowed','');
		}
	}

	function doGet_budget_data($kategori = '') {
		$kode_siklus 	= ($this->input->post("kode_siklus")) ? $this->input->post("kode_siklus") : '';
		$status_siklus = ($this->input->post("status_siklus")) ? $this->input->post("status_siklus") : '';
		$arr = array();
		$arr_eksternal = array();

		$count = 0;
      if($status_siklus == '' || $status_siklus == 'D' || $status_siklus == 'RJ'){
         $sql = <<<QUERY
            SELECT mbpg.*, coalesce(bgd.JML_ORDER,0) JML_ORDER FROM M_BUDGET_PEMAKAIAN_GLANGSING mbpg
            left JOIN BUDGET_GLANGSING_D bgd ON mbpg.KODE_BUDGET = bgd.KODE_BUDGET AND bgd.NO_URUT = (
               SELECT MAX(NO_URUT) FROM BUDGET_GLANGSING_D WHERE KODE_SIKLUS = bgd.KODE_SIKLUS
            ) AND bgd.KODE_SIKLUS = '$kode_siklus'
            WHERE mbpg.STATUS = 'A' AND mbpg.KATEGORI_BUDGET = '$kategori'
QUERY;
      }else{
         $sql = <<<QUERY
            SELECT * FROM BUDGET_GLANGSING_D bgd
            JOIN M_BUDGET_PEMAKAIAN_GLANGSING BPG ON BPG.KODE_BUDGET = bgd.KODE_BUDGET AND BPG.KATEGORI_BUDGET = '$kategori'
            WHERE bgd.NO_URUT = (
               SELECT max(NO_URUT) FROM BUDGET_GLANGSING_D bgd2 WHERE bgd2.KODE_SIKLUS = bgd.KODE_SIKLUS
            ) AND KODE_SIKLUS = '$kode_siklus'
            order by NO_URUT ASC
QUERY;
      }


		$stmt = $this->db->conn_id->prepare($sql);
      $stmt->execute();
      $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($hasil) > 0){
			foreach($hasil as $key=>$data){
            $result['kode_budget'] = $data['KODE_BUDGET'];
				$result['nama_budget'] = $data['NAMA_BUDGET'];
				$result['value']       = $data['JML_ORDER'];

				array_push($arr,$result);

				$count++;
			}
		}		
		return json_encode($arr);
	}	

	function doSave_budget() {
		$action 	       = ($this->input->post("action")) ? $this->input->post("action") : '';
		$kd_siklus 	    = ($this->input->post("kd_siklus")) ? $this->input->post("kd_siklus") : '';
		$tf_budget_name = ($this->input->post("tf_budget_name")) ? $this->input->post("tf_budget_name") : null;
		$tf_budget_val  = ($this->input->post("tf_budget_val")) ? $this->input->post("tf_budget_val") : null;
		$tgl_buat 	    = ($this->input->post("tgl_buat")) ? $this->input->post("tgl_buat") : '';
		$keterangan     = ($this->input->post("keterangan")) ? $this->input->post("keterangan") : '';
		$kode_farm      = ($this->input->post("kode_farm")) ? $this->input->post("kode_farm") : '';
		$count_updated  = ($this->input->post("count_updated")) ? $this->input->post("count_updated") : 0;
		$user_id	    = $this->session->userdata('kode_user');
		$periode        = $this->getPeriodeData($kd_siklus,$kode_farm)->PERIODE_SIKLUS;	

		$generateBudget = $this->input->post("generatebudget");
		$message_success = '';
		$message_failed = '';
		$message        = '';
      	$tgl_buat       = $this->get_today();
      	$MaxNoUrut      = $this->getMaxNoUrut($kd_siklus);
	  	$MaxNoUrutApprove = $this->getMaxNoUrutApprove($kd_siklus);
		$noUrut = $MaxNoUrut + 1;
		$noUrutApprove = $MaxNoUrutApprove + 1;
      	$error = false;
		$triggerClick = '';
		$insertBudgetGlangsingD = !empty($count_updated) ? 1 : 0;
		$insertDefaultGlangsingSiklus = 0;
		$dataSinkronisasi = array('kode_siklus' => $kd_siklus, 'kode_farm' => $kode_farm, 'no_urut' => $noUrut, 'no_urut_approve' => $noUrutApprove, 'action' => $action);
		switch ($action) {
			case 'D':
				$message_success = "Penyimpanan data Budget Pemakaian Glangsing untuk Siklus ".$periode." berhasil dilakukan.";
				$message_failed = "Penyimpanan data Budget Pemakaian Glangsing gagal dilakukan";
				break;
			case 'N':
				$message_success = "Penyimpanan data Budget Pemakaian Glangsing untuk Siklus ".$periode." berhasil dilakukan.";
				$message_failed = "Penyimpanan data Budget Pemakaian Glangsing gagal dilakukan";
				break;
			case 'C':
				$message_success = "Penutupan Budget Pemakaian Glangsing untuk Siklus ".$periode." berhasil dilakukan.";
				$message_failed = "Penutupan Budget Pemakaian Glangsing gagal dilakukan";
				$triggerClick = 'true';
				break;
			case 'R':
				$message_success = "Proses Approval data Budget Pemakaian Glangsing untuk Siklus ".$periode." berhasil dilakukan.";
				$message_failed = "Proses Approval data Budget Pemakaian Glangsing untuk Siklus ".$periode."gagal dilakukan";
				break;
			case 'RJ':
				$message_success = "Proses Reject data Budget Pemakaian Glangsing untuk Siklus ".$periode." berhasil dilakukan.";
				$message_failed = "Proses Reject data Budget Pemakaian Glangsing untuk Siklus ".$periode."gagal dilakukan";
				break;
			case 'A':
				$message_success = "Proses Approval data Budget Pemakaian Glangsing untuk Siklus ".$periode." berhasil dilakukan.";
				$message_failed = "Proses Approval data Budget Pemakaian Glangsing untuk Siklus ".$periode."gagal dilakukan";
				break;
			default:
				# code...
				break;
		}

      $this->db->trans_begin();
      if(count($this->getBudgetGlangsing($kd_siklus)) == 0){
			$insertBudgetGlangsingD = 1;
			
			if($action == 'N'){
				/** periksa apakah ada perubahan pengajuan dengan siklus kemarin, jika gak ada langsung approve aja */
				$dataSiklusBaru = array(
					'tf_budget_name' => $tf_budget_name,
					'tf_budget_val' => $tf_budget_val
				);	
				$adaPerubahan = $this->cekPengajuanBudget($kode_farm,$kd_siklus,$dataSiklusBaru);
				if(!$adaPerubahan){
					$action = 'A';
					$data_budget_glangsing['TGL_APPROVE'] = $tgl_buat['today'];
					$insertDefaultGlangsingSiklus = 1;
					$message_success = "Data Budget Pemakaian Glangsing untuk Siklus ".$periode." berhasil disimpan dan diapprove.";
				}
			}
			$data_budget_glangsing = array();
			$data_budget_glangsing['KODE_SIKLUS'] = $kd_siklus;
			$data_budget_glangsing['STATUS'] = $action;
			$data_budget_glangsing['TGL_BUAT'] = $tgl_buat['today'];
			$this->db->insert("BUDGET_GLANGSING", $data_budget_glangsing);			    
			$dataSinkronisasi['budget_glangsing'] = 'I';
      }else{
			$status = $this->getBudgetGlangsing($kd_siklus)->STATUS;
			
			if($action == 'N'){
				/** periksa apakah ada perubahan pengajuan dengan siklus kemarin, jika gak ada langsung approve aja */
				$dataSiklusBaru = array(
					'tf_budget_name' => $tf_budget_name,
					'tf_budget_val' => $tf_budget_val
				);	
				$adaPerubahan = $this->cekPengajuanBudget($kode_farm,$kd_siklus,$dataSiklusBaru);
				if(!$adaPerubahan){
					$action = 'A';
					$message_success = "Data Budget Pemakaian Glangsing untuk Siklus ".$periode." berhasil disimpan dan diapprove.";
				}
			}

			$data_budget_glangsing = array();
			$data_budget_glangsing['STATUS'] = $action;
			if($action == 'A'){
				$data_budget_glangsing['TGL_APPROVE'] = $tgl_buat['today'];
				/** set nilai default stok glangsing siklus yang diapprove */
				$insertDefaultGlangsingSiklus = 1;
			}
			if($action == 'C'){
				$data_budget_glangsing['TGL_CLOSING'] = $tgl_buat['today'];
			}
			$this->db->where('KODE_SIKLUS',$kd_siklus);
			$this->db->update("BUDGET_GLANGSING", $data_budget_glangsing);
			$dataSinkronisasi['budget_glangsing'] = 'U';	
	}

		if($insertBudgetGlangsingD){
			for ($i=0; $i < count($tf_budget_name); $i++) {
				$budget_d = array(
					'KODE_SIKLUS' => $kd_siklus, 
					'NO_URUT' => $noUrut, 
					'KODE_BUDGET' => $tf_budget_name[$i], 
					'JML_ORDER' => 	$tf_budget_val[$i]
				);
				$this->db->insert("BUDGET_GLANGSING_D", $budget_d);		          
			}
			$dataSinkronisasi['budget_glangsing_d'] = 'I';
		}

		if($action == 'N'){
			$dataSinkronisasi['budget_glangsing_d'] = 'I';
		}

		if($insertDefaultGlangsingSiklus){				
			$dataDefaultGlangsing = array(
				'kode_farm' => $kode_farm,
				'tgl_buat' => $tgl_buat['today'],
				'kode_siklus' => $kd_siklus,
				'tf_budget_name' => $tf_budget_name,
				'tf_budget_val' => $tf_budget_val
			);
			$this->insertDefaultGlangsing($dataDefaultGlangsing);
			$dataSinkronisasi['glangsing_movement'] = 'I';          
			$dataSinkronisasi['siklus_lalu'] = isset($stokGBP['kode_siklus']) ? $stokGBP['kode_siklus'] : 0;
		}
	  	/** insert log */
		$data_log_budget_glangsing = array();
		$data_log_budget_glangsing['KODE_SIKLUS'] = $kd_siklus;
		$data_log_budget_glangsing['NO_URUT']     = $noUrut;
		$data_log_budget_glangsing['NO_URUT_APPROVE'] = $noUrutApprove;
		$data_log_budget_glangsing['STATUS']      = $action;
		$data_log_budget_glangsing['USER_BUAT']   = $user_id;
		$data_log_budget_glangsing['TGL_BUAT']    = $tgl_buat['today'];
		$data_log_budget_glangsing['KETERANGAN']  = $keterangan;

		$this->db->insert("LOG_BUDGET_GLANGSING", $data_log_budget_glangsing);  
		$dataSinkronisasi['log_budget_glangsing'] = 'I';
		
		if($generateBudget){
			$nextPeriode = $this->getNextPeriode($kode_farm,$periode,'A');
			if(!empty($nextPeriode)){
				$dataBudgetNextPeriode = array(
					'kode_farm' => $kode_farm,
					'tgl_buat' => $tgl_buat['today'],
					'kode_siklus' => $nextPeriode['kode_siklus'],
					'tf_budget_name' => $tf_budget_name,
					'tf_budget_val' => $tf_budget_val
				);
				$this->generateBudgetNextPeriode($dataBudgetNextPeriode);	
				$dataSinkronisasi['next_siklus'] = $nextPeriode['kode_siklus'];
			}
		}

		if ($this->db->trans_status() === FALSE ){
			$this->db->trans_rollback();
			$success = false;
			$status = 0;
			$message = $message_failed;
		}
		else{
			$this->db->trans_commit();
			$success = true;
			$status = 1;
			$message = $message_success;         	
		}

		$main = array('success'=>$success,'status' => $status,'message'=>$message,'kode_siklus'=>$kd_siklus, 'content' => $dataSinkronisasi);
		if(!empty($triggerClick)){
			$main['trigger'] = $triggerClick;
		}
		return $main;
	}

	private function insertDefaultGlangsing($defaultGlangsing){
		$user_id = $this->session->userdata('kode_user');
		$kode_farm = $defaultGlangsing['kode_farm'];
		$tgl_buat = $defaultGlangsing['tgl_buat'];
		$kd_siklus = $defaultGlangsing['kode_siklus'];
		$tf_budget_name = $defaultGlangsing['tf_budget_name'];
		$tf_budget_val = $defaultGlangsing['tf_budget_val'];
		$stokGlangsingLalu = $this->getStokAkhirSiklusLalu($kode_farm,$kd_siklus);
		if(!empty($stokGlangsingLalu)){
			$stokGlangsingLalu = arr2DToarrKey($stokGlangsingLalu,'kode_barang');
		}

		for ($i=0; $i < count($tf_budget_name); $i++) {
			$stokGlangsingTmp = isset($stokGlangsingLalu[$tf_budget_name[$i]]) ? 	$stokGlangsingLalu[$tf_budget_name[$i]] : array('jml_stok' => 0);
			$glangsing_d = array(
				'KODE_SIKLUS' => $kd_siklus, 
				'KODE_FARM' => $kode_farm, 
				'KODE_BARANG' => $tf_budget_name[$i], 
				'JML_STOK' => $tf_budget_name[$i] == 'GB' ? 0 : $stokGlangsingTmp['jml_stok']
			);
			$this->db->insert("GLANGSING_MOVEMENT", $glangsing_d);		          
		}
		$stokGlangsingTmp = isset($stokGlangsingLalu['GBP']) ? 	$stokGlangsingLalu['GBP'] : array('jml_stok' => 0);
		$glangsing_d = array(
			'KODE_SIKLUS' => $kd_siklus, 
			'KODE_FARM' => $kode_farm, 
			'KODE_BARANG' => 'GBP', 
			'JML_STOK' => $stokGlangsingTmp['jml_stok']					
		);
		$stokGBP = isset($stokGlangsingLalu['GBP']) ? 	$stokGlangsingLalu['GBP'] : array();
		/** update stok GBP siklus kemarin menjadi 0 */

		/* diaktifkan nanti jika fitur sales order sudah persiklus		
		if(!empty($stokGBP)){
			$this->db->where(array('kode_siklus' => $stokGBP['kode_siklus'], 'kode_barang' => 'GBP'))->update("glangsing_movement", array('jml_stok' => 0));
			// insert ke glangsing_movement_d untuk data transaksi pengurang 
			$glangsing_movement_d_transfer = array(
				'kode_farm' => $kode_farm,
				'kode_siklus' => $stokGBP['kode_siklus'],
				'kode_barang' => 'GBP',
				'no_referensi' => 'TRANSFER_STOK',
				'jml_awal' => $stokGBP['jml_stok'],
				'jml_order' => -1 * $stokGBP['jml_stok'],
				'jml_akhir' => 0,
				'tgl_transaksi' => $tgl_buat,
				'keterangan1' => 'TRANSFER_OUT',
			//	'keterangan2' => ,
				'user_buat' => $this->session->userdata ('kode_user'),
			);
			$this->db->insert('glangsing_movement_d',$glangsing_movement_d_transfer);
		}*/
		$this->db->insert("GLANGSING_MOVEMENT", $glangsing_d);
	}

	function generateBudgetNextPeriode($dataBudgetNextPeriode){
			$user_id = $this->session->userdata('kode_user');
			$tgl_buat = $dataBudgetNextPeriode['tgl_buat'];
			$kd_siklus = $dataBudgetNextPeriode['kode_siklus'];
			$tf_budget_name = $dataBudgetNextPeriode['tf_budget_name'];
			$tf_budget_val = $dataBudgetNextPeriode['tf_budget_val'];

			$data_budget_glangsing = array();
			$data_budget_glangsing['KODE_SIKLUS'] = $kd_siklus;
			$data_budget_glangsing['STATUS'] = 'A';
			$data_budget_glangsing['TGL_BUAT'] = $tgl_buat;
			$data_budget_glangsing['TGL_APPROVE'] = $tgl_buat;
			$this->db->insert("BUDGET_GLANGSING", $data_budget_glangsing);			    

			for ($i=0; $i < count($tf_budget_name); $i++) {
				$budget_d = array(
					'KODE_SIKLUS' => $kd_siklus, 
					'NO_URUT' => 1, 
					'KODE_BUDGET' => $tf_budget_name[$i], 
					'JML_ORDER' => 	$tf_budget_val[$i]
				);
				$this->db->insert("BUDGET_GLANGSING_D", $budget_d);		          
			}

			/** insert log */
			$data_log_budget_glangsing = array();
			$data_log_budget_glangsing['KODE_SIKLUS'] = $kd_siklus;
			$data_log_budget_glangsing['NO_URUT']     = 1;
			$data_log_budget_glangsing['NO_URUT_APPROVE'] = 1;
			$data_log_budget_glangsing['STATUS']      = 'A';
			$data_log_budget_glangsing['USER_BUAT']   = $user_id;
			$data_log_budget_glangsing['TGL_BUAT']    = $tgl_buat;
			$data_log_budget_glangsing['KETERANGAN']  = 'generate otomatis';

			$this->db->insert("LOG_BUDGET_GLANGSING", $data_log_budget_glangsing);  

			$this->insertDefaultGlangsing($dataBudgetNextPeriode);

	}
	
	function cek_syarat_tutup_budget(){
		
	}	
   function getBudgetGlangsing($kode_siklus = ''){
      $this->db->where('kode_siklus',$kode_siklus);
      $sql = $this->db->get('BUDGET_GLANGSING');

      return $sql->row();
   }
	function doCek_status_siklus_home($user_level,$kode_farm) {
		$result['message'] = '';
		switch ($user_level){
			case 'KF' :
				$sql = <<<QUERY
					SELECT * FROM BUDGET_GLANGSING
					LEFT JOIN LOG_BUDGET_GLANGSING ON LOG_BUDGET_GLANGSING.KODE_SIKLUS = BUDGET_GLANGSING.KODE_SIKLUS AND LOG_BUDGET_GLANGSING.STATUS = BUDGET_GLANGSING.STATUS
					LEFT JOIN M_PEGAWAI ON LOG_BUDGET_GLANGSING.USER_BUAT = M_PEGAWAI.KODE_PEGAWAI
					JOIN M_PERIODE ON M_PERIODE.KODE_SIKLUS = BUDGET_GLANGSING.KODE_SIKLUS AND M_PERIODE.KODE_FARM = '$kode_farm'
					WHERE BUDGET_GLANGSING.STATUS = 'RJ'
					order by LOG_BUDGET_GLANGSING.no_urut_approve desc
QUERY;

				$stmt = $this->db->conn_id->prepare($sql);
		        $stmt->execute();
		        $hasil = $stmt->fetch(0);
		        if($hasil != false){
		        	$result['message'] = "Pengajuan Budget Pemakaian Glangsing di-Reject <br>
		        	<b>Oleh:</b><br>
		        	".$hasil['NAMA_PEGAWAI']."<br>
		        	<b>Keterangan:</b><br>
		        	".$hasil['KETERANGAN']."";
		        }

		        $sql = <<<QUERY
					SELECT * FROM BUDGET_GLANGSING
					JOIN M_PERIODE ON M_PERIODE.KODE_SIKLUS = BUDGET_GLANGSING.KODE_SIKLUS AND M_PERIODE.KODE_FARM = '$kode_farm' AND M_PERIODE.STATUS_PERIODE = 'N'
					WHERE BUDGET_GLANGSING.STATUS = 'A'
QUERY;

				$stmt = $this->db->conn_id->prepare($sql);
		        $stmt->execute();
		        $hasil = $stmt->fetch(0);
		       	if($hasil != false){
		        	$result['message'] = "Siklus ".$hasil['PERIODE_SIKLUS']." sudah dilakukan persetujuan oleh <b>Admin Budidaya.</b><br>
		        	Pada Tanggal : ".$hasil['TGL_APPROVE']."<br>
		        	Mohon segera dilakukan penutupan Budget Pemakaian Glangsing<br>
		        	Untuk Siklus ".$hasil['PERIODE_SIKLUS']."<br>";
		        }
				break;

			case 'KD' :
				$sql = <<<QUERY
					SELECT * FROM BUDGET_GLANGSING
					LEFT JOIN LOG_BUDGET_GLANGSING ON LOG_BUDGET_GLANGSING.KODE_SIKLUS = BUDGET_GLANGSING.KODE_SIKLUS AND LOG_BUDGET_GLANGSING.STATUS = BUDGET_GLANGSING.STATUS
					LEFT JOIN M_PEGAWAI ON LOG_BUDGET_GLANGSING.USER_BUAT = M_PEGAWAI.KODE_PEGAWAI
					JOIN M_PERIODE ON M_PERIODE.KODE_SIKLUS = BUDGET_GLANGSING.KODE_SIKLUS
					WHERE BUDGET_GLANGSING.STATUS = 'RJ'
					order by LOG_BUDGET_GLANGSING.no_urut_approve desc
QUERY;

				$stmt = $this->db->conn_id->prepare($sql);
		        $stmt->execute();
		        $hasil = $stmt->fetch(0);
		        if($hasil != false){
		        	$result['message'] = "Pengajuan Budget Pemakaian Glangsing di-Reject <br>
		        	<b>Oleh:</b><br>
		        	".$hasil['NAMA_PEGAWAI']."<br>
		        	<b>Keterangan:</b><br>
		        	".$hasil['KETERANGAN']."";
		        }

		        $sql = <<<QUERY
					SELECT * FROM BUDGET_GLANGSING
					JOIN M_PERIODE ON M_PERIODE.KODE_SIKLUS = BUDGET_GLANGSING.KODE_SIKLUS
                    JOIN M_FARM ON M_FARM.KODE_FARM = M_PERIODE.KODE_FARM
					WHERE BUDGET_GLANGSING.STATUS = 'N'
QUERY;

				$stmt = $this->db->conn_id->prepare($sql);
		        $stmt->execute();
		        $hasil = $stmt->fetch(0);
		        if($hasil != false){
		        	$result['message'] = "Pengajuan Budget Pemakaian Glangsing <br>
		        	<b>Farm:</b><br>
		        	".$hasil['NAMA_FARM']."<br>
		        	<b>Siklus:</b><br>
		        	".$hasil['PERIODE_SIKLUS']."<br>
		        	Mohon segera dilakukan proses Approve";
		        }
				break;				
		}
		return json_encode($result);
	}
	function getPeriodeData($kode_siklus,$kode_farm){
		$query = $this->db->query("
			select * from m_periode where kode_siklus = '$kode_siklus' and kode_farm = '$kode_farm'
		");
		return $query->row();
	}
   function get_today(){
		$sql = <<<QUERY
		select getdate() as [today]
QUERY;
		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
   function getMaxNoUrut($kode_siklus = ''){
      $query = $this->db->query("
		    SELECT MAX(NO_URUT) NO_URUT FROM BUDGET_GLANGSING_D WHERE KODE_SIKLUS = '$kode_siklus'
		");
		if($query->row()->NO_URUT == ''){
         return 0;
      }else{
         return $query->row()->NO_URUT;
      }
   }
   function getMaxNoUrutApprove($kode_siklus = ''){
      $query = $this->db->query("
		    SELECT MAX(NO_URUT_APPROVE) NO_URUT_APPROVE FROM LOG_BUDGET_GLANGSING WHERE KODE_SIKLUS = '$kode_siklus' AND NO_URUT = '".$this->getMaxNoUrut($kode_siklus)."'
		");
      if($query->row()->NO_URUT_APPROVE == ''){
         return 0;
      }else{
         return $query->row()->NO_URUT_APPROVE;
      }
   }
   /** maksimal h-3 dari tgl doc-in awal */
   public function cekTimeline($kode_siklus,$batasTimeline){
	$sql = <<<SQL
	SELECT min(tgl_doc_in) tgl_doc_in, CASE WHEN cast(getdate() as date) > dateadd(day,{$batasTimeline},min(tgl_doc_in)) THEN 1 ELSE 0 END terlambat  FROM KANDANG_SIKLUS WHERE KODE_SIKLUS = {$kode_siklus} -- AND STATUS_SIKLUS = 'O'	 
SQL;
	
	$result = $this->db->query($sql)->row();			
	return $result->terlambat;
   }

   function getStokAkhirSiklusLalu($kode_farm,$kode_siklus){
	$sql = <<<SQL
	SELECT jml_stok,periode_siklus,glangsing_movement.kode_siklus,glangsing_movement.kode_barang  FROM glangsing_movement
	JOIN M_PERIODE ON M_PERIODE.KODE_SIKLUS = glangsing_movement.kode_siklus 
	WHERE glangsing_movement.kode_siklus = ( SELECT TOP 1 kode_siklus FROM glangsing_movement WHERE kode_farm = '{$kode_farm}' AND kode_siklus < {$kode_siklus} ORDER BY kode_siklus desc )
SQL;
	return  $this->db->query($sql)->result_array();

   }

   public function getNextPeriode($kode_farm,$periode,$status = ''){
	$whereStatus = !empty($status) ? ' and status_periode = \''.$status.'\'' : '';  
	$sql = <<<SQL
	select top 1 periode_siklus,kode_siklus from m_periode where kode_siklus > (
		 select kode_siklus from m_periode where PERIODE_SIKLUS = '{$periode}' and kode_farm = '{$kode_farm}'
	 ) and kode_farm = '{$kode_farm}' {$whereStatus}
SQL;
	 return $this->db->query($sql)->row_array();

	}

	private function cekPengajuanBudget($kode_farm,$kd_siklus,$dataSiklusBaru){
		$result = 0;
		$sql = <<<SQL
		SELECT bgd.kode_budget, bgd.jml_order 
		FROM budget_glangsing_d bgd 
		JOIN ( 
			SELECT kode_siklus,max(no_urut) no_urut FROM BUDGET_GLANGSING_d 
			WHERE KODE_SIKLUS = (SELECT TOP 1 kode_siklus FROM glangsing_movement WHERE KODE_SIKLUS < {$kd_siklus} AND kode_farm = '{$kode_farm}' ORDER BY KODE_SIKLUS desc)
			GROUP BY KODE_SIKLUS
		)tmp ON tmp.kode_siklus = bgd.kode_siklus AND tmp.no_urut = bgd.no_urut
SQL;
		$budgetLama = $this->db->query($sql)->result_array();
		if(!empty($budgetLama)){ 
			$budgetLama = arr2DToarrKey($budgetLama,'kode_budget');
			$tf_budget_name = $dataSiklusBaru['tf_budget_name'];
			$tf_budget_val = $dataSiklusBaru['tf_budget_val'];
			for($i = 0; $i < count($tf_budget_name); $i++){
				$kode_budget = $tf_budget_name[$i];
				if(isset($budgetLama[$kode_budget])){
					if($budgetLama[$kode_budget]['jml_order'] != $tf_budget_val[$i]){
						$result++;
					}
				}
			}
		}	
		return $result;
	}
}
