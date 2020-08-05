<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Main extends MY_Controller {

    protected $_level_user;
    protected $_user;
    protected $_farm;
    protected $_grup_farm;

    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array(
            'm_transaksi'
        ));
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_user_level = $this->session->userdata('level_user');
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
    }

    public function index($kode_farm = NULL) {
		// echo $this->_farm;		
		$user_level = $this->session->userdata('level_user');
		$this->load->config('stpakan');		
		$namaFarm = $this->config->item('namaFarm');
		switch ($user_level){
			case 'KF' :
				$data['nama_farm'] = $namaFarm[$this->_farm];
				$data['jabatan'] = $user_level;
				break;
			case 'KD' :
				$data['nama_farm'] =  !empty($kode_farm) ? $namaFarm[$kode_farm] : '';		
				$data['jabatan'] = $user_level;
				break;
			case 'KDV' :
				$data['nama_farm'] =  !empty($kode_farm) ? $namaFarm[$kode_farm] : '';		
				$data['jabatan'] = $user_level;
				break;
         case 'KDB' :
            $data['nama_farm'] =  !empty($kode_farm) ? $namaFarm[$kode_farm] : '';		
            $data['jabatan'] = $user_level;
            break;
		}	  	
		$data['list_budget'] = $this->load->view($this->_grup_farm.'/form_pengembalian',$data,TRUE);
		$this->load->view($this->_grup_farm.'/main', $data);
    }

	function read_periode(){
		echo $this->m_transaksi->doRead_periode();
	}
	function cek_syarat_tutup_budget(){
		$result = $this->m_transaksi->cek_syarat_tutup_budget();
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($result));		
	}
	function save_budget(){
		$result = $this->m_transaksi->doSave_budget();
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($result));		
	}
	function load_budget_glangsing(){
		echo $this->m_transaksi->doLoad_budget_glangsing();
	}
	function get_budget_data(){
      $data['internal_data'] = json_decode($this->m_transaksi->doGet_budget_data('I'));
      $data['eksternal_data'] = json_decode($this->m_transaksi->doGet_budget_data('E'));
	  $this->load->view($this->_grup_farm.'/form_pengembalian',$data);
	}
	function cek_status_siklus(){
		$user_level = $this->session->userdata('level_user');
		$kode_siklus 	= ($this->input->post("kode_siklus")) ? $this->input->post("kode_siklus") : '';
		$status_siklus 	= ($this->input->post("status_siklus")) ? $this->input->post("status_siklus") : '';
		$terlambat = 0;
		/** jika status siklus tidak sama dengan A dan C maka cek timelinenya */
		if(in_array($status_siklus,array('','D'))){
			/** ambil nilai dari database config general */
			$sqlFarm = $this->db->select(array('kode_farm'))->where(array('kode_siklus' => $kode_siklus))->get_compiled_select('m_periode');
			$timelinePengajuan = $this->db->where(array('STATUS' => 1, 'KODE_CONFIG' => '_min_pengajuan'))->where('kode_farm in ('.$sqlFarm.')')->get('sys_config_general')->row();			
			
			$batasTimeline = -3;
			if(!empty($timelinePengajuan)){
				$batasTimeline = $timelinePengajuan->VALUE * -1;
			}
			$terlambat = $this->m_transaksi->cekTimeline($kode_siklus,$batasTimeline);					
		}
		
		switch ($user_level){
			case 'KF' :
				$this->kepalafarm($kode_siklus,$status_siklus,$terlambat);
				break;
			case 'KD' :
				$this->kadept($kode_siklus,$status_siklus,$terlambat);
				break;
			case 'KDV' :
				$this->kadeptBdy($kode_siklus,$status_siklus,$terlambat);
				break;
			case 'KDB' :
				$this->kadeptBdy($kode_siklus,$status_siklus,$terlambat);
				break;
		}
	}
	function kepalafarm($kode_siklus,$status_siklus,$terlambatTimeline){		
		$tutup_bugdet = 'none';
		$simpan = '';
		$rilis	= '';
		$field	= '';
		$cetak = 'none';
		$message= false;
		$periode_siklus = '';
		$result['pesan_keterlambatan'] = '';
		if($status_siklus == 'A'){
			$status_periode = $this->getStatusPeriode($kode_siklus)->STATUS_PERIODE;			
			if($status_periode != 'A'){
				$tutup_bugdet = 'initial';
			}
			$simpan = 'none';
			$rilis	= 'none';
			$field	= 'read';
		}else if($status_siklus == '' || $status_siklus == 'D' || $status_siklus == 'RJ'){
			$data = $this->m_transaksi->doCek_status_siklus($this->_farm,$kode_siklus);
			if($data[0] == 'denied'){
				$simpan = 'none';
				$rilis	= 'none';
				$field	= 'read';
				$message = true;
				$periode_siklus = $data[1];
			}else if($data[0] == 'allowed'){
				$simpan = 'initial';
				$rilis	= 'initial';
				$field	= 'write';
			}
		}else{
			$simpan = 'none';
			$rilis	= 'none';
			$field	= 'read';			
			if($status_siklus == 'C'){
				$cetak = 'initial';
				/* dapatkan tanggal tutupnya */
				$terlambat = $this->cekKeterlambatanTutupBudget($kode_siklus);
				if($terlambat['status']){					
					$result['pesan_keterlambatan'] = '*) Budget pemakaian glangsing ditutup pada tanggal '.tglIndonesia($terlambat['tgl_closing'],'-',' ').'.<br /> Penutupan mengalami keterlambatan ';
				}				
			}
			/* periksa apakah ketika tutup budget mengalami keterlambatan , max h - 4 dari tgl Doc-In siklus berikutnya */
		}
		$result['periode_siklus'] = $periode_siklus;
		$result['message'] = $message;
		$result['field'] = $field;
		$result['simpan'] = !$terlambatTimeline ? $simpan : 'none';
		$result['rilis'] = !$terlambatTimeline ? $rilis : 'none';
		$result['tutup_bugdet'] = $tutup_bugdet;
		$result['approve'] = 'none';
		$result['review'] = 'none';
		$result['reject'] = 'none';		
		$result['print'] = $cetak;
		$result['messageTimeline'] = $terlambatTimeline ? 'Pengajuan Budget Pemakaian Glangsing untuk Siklus '.$periode_siklus.' Melebihi Batas Timeline (max. [H-3 dari tanggal DOC Flock 1])' : '';			
		echo json_encode($result);
	}

	function kadeptBdy($kode_siklus,$status_siklus,$terlambatTimeline){		
		$tutup_bugdet = 'none';
		$review = '';
		$reject	= '';
		$field	= '';
		$cetak = 'none';
		$message= false;
		$periode_siklus = '';
		$result['pesan_keterlambatan'] = '';
		if($status_siklus == 'R'){
			$approve = 'initial';
			$reject	= 'initial';
			$field	= 'read';
		}else{
			$approve = 'none';
			$reject	= 'none';
			$field	= 'read';
			if($status_siklus == 'C'){
				$cetak = 'initial';
				/* dapatkan tanggal tutupnya */
				$terlambat = $this->cekKeterlambatanTutupBudget($kode_siklus);
				if($terlambat['status']){
					$result['pesan_keterlambatan'] = '*) Budget pemakaian glangsing ditutup pada tanggal '.tglIndonesia($terlambat['tgl_closing'],'-',' ').'.<br /> Penutupan mengalami keterlambatan ';
				}				
			}
		}
		$result['periode_siklus'] = $periode_siklus;
		$result['message'] = $message;
		$result['field'] = $field;
		$result['simpan'] = 'none';
		$result['rilis'] = 'none';
		$result['tutup_bugdet'] = 'none';
		$result['approve'] = !$terlambatTimeline ? $approve : 'none'; 
		$result['review'] = 'none';
		$result['reject'] = !$terlambatTimeline ? $reject : 'none';
		$result['print'] = $cetak;
		$result['messageTimeline'] = $terlambatTimeline ? 'Review Budget Pemakaian Glangsing untuk Siklus '.$periode_siklus.' Melebihi Batas Timeline (max. [H-3 dari tanggal DOC Flock 1])' : '';			
		echo json_encode($result);
	}

	function kadept($kode_siklus,$status_siklus,$terlambatTimeline){		
		$tutup_bugdet = 'none';
		$review = '';
		$reject	= '';
		$field	= '';
		$cetak = 'none';
		$message= false;
		$periode_siklus = '';
		$result['pesan_keterlambatan'] = '';
		if($status_siklus == 'N'){
			$review = 'initial';
			$reject	= 'initial';
			$field	= 'read';
		}else{
			$review = 'none';
			$reject	= 'none';
			$field	= 'read';
			if($status_siklus == 'C'){
				$cetak = 'initial';
				/* dapatkan tanggal tutupnya */
				$terlambat = $this->cekKeterlambatanTutupBudget($kode_siklus);
				if($terlambat['status']){
					$result['pesan_keterlambatan'] = '*) Budget pemakaian glangsing ditutup pada tanggal '.tglIndonesia($terlambat['tgl_closing'],'-',' ').'.<br /> Penutupan mengalami keterlambatan ';
				}				
			}
		}
		$result['periode_siklus'] = $periode_siklus;
		$result['message'] = $message;
		$result['field'] = $field;
		$result['simpan'] = 'none';
		$result['rilis'] = 'none';
		$result['tutup_bugdet'] = 'none';
		$result['approve'] = 'none';
		$result['review'] = !$terlambatTimeline ? $review : 'none';
		$result['reject'] = !$terlambatTimeline ? $reject : 'none';
		$result['print'] = $cetak;
		$result['messageTimeline'] = $terlambatTimeline ? 'Approval Budget Pemakaian Glangsing untuk Siklus '.$periode_siklus.' Melebihi Batas Timeline (max. [H-3 dari tanggal DOC Flock 1])' : '';			
		echo json_encode($result);
	}

	function cek_status_siklus_home(){
		echo $this->m_transaksi->doCek_status_siklus_home($this->_user_level,$this->_farm);
	}

	public function getStatusPeriode($kode_siklus = '')
	{
		$query = $this->db->query("
			select * from m_periode where kode_siklus = '$kode_siklus'
		");
		return $query->row();
	}
   public function cekKontrolTutupBudget(){
		$kode_farm = $this->input->post('kode_farm');
		$periode = $this->input->post('periode');
		$result = array('status' => 1, 'message' => '');
	   	/** cek permintaan sak */
		$mintaSak = $this->cekPermintaanSak($kode_farm,$periode);
		if(empty($mintaSak)){
			/** cek stok glangsing yang bisa dijual */
			$stokGlangsing = $this->cekGlangsingJual($kode_farm,$periode)->result_array();
			if(!empty($stokGlangsing)){
				if(!empty($stokGlangsing[0]['kontrol'])){
					$result['status'] = 0;
					$result['message'] = 'Proses tutup budget tidak dapat dilanjutkan. Terdapat glangsing yang belum diproses';
				}
			}
		}else{
			$result['status'] = 0;
			$result['message'] = 'Proses tutup budget tidak dapat dilajutkan. Terdapat permintaan sak yang belum di-Approve';
		}

		if($result['status']){
			$nextperiodeTmp = $this->m_transaksi->getNextPeriode($kode_farm,$periode,'A');
			if(!empty($nextperiodeTmp)){
				$nextperiode = $nextperiodeTmp['periode_siklus'];
				$result['konfirmasi'] = 'Apakah budget glangsing pada siklus <strong>'.$nextperiode.'</strong> sama dengan budget glanding pada siklus <strong>'.$periode.'</strong>';
				$result['konfirmasi'] .= '<br ><br >Pilih <strong>ya</strong>, apabila tidak terdapat perubahan (budget siklus baru akan aktif otomatis)';
				$result['konfirmasi'] .= '<br >Pilih <strong>Tidak</strong>, apabila terdapat perubahan (user harus aktivasi budget)';
			}
		}
		
		echo json_encode($result);
		   
   }
   
   private function cekGlangsingJual($kode_farm,$periode){
	   $sql = <<<SQL
	   	SELECT x.jml_stok 
			,(SELECT VALUE FROM SYS_CONFIG_GENERAL WHERE KODE_FARM = '{$kode_farm}' AND KODE_CONFIG = '_kontrol_stok' AND STATUS = 1) kontrol
		FROM (
		SELECT CASE WHEN kode_barang = 'GB' THEN jml_stok - coalesce((SELECT sum(jml) FROM ba_pemusnahan WHERE no_ppsk LIKE 'PPSK/{$kode_farm}/{$periode}%'),0)
			else jml_stok END jml_stok	 
		FROM glangsing_movement
		where kode_siklus = (SELECT kode_siklus FROM m_periode WHERE kode_farm = '{$kode_farm}' AND PERIODE_SIKLUS = '{$periode}') and kode_barang != 'GBP'
		)x WHERE jml_stok > 0	   
SQL;
		return $this->db->query($sql);
   }	
   private function cekPermintaanSak($kode_farm,$periode){
      
	  $sql = <<<SQL
		SELECT * FROM log_ppsk_new lpn 
		JOIN (
			SELECT no_ppsk,max(no_urut) no_urut FROM log_ppsk_new WHERE no_ppsk LIKE 'PPSK/$kode_farm/$periode%' GROUP BY no_ppsk
		)ppsk_terakhir ON ppsk_terakhir.no_ppsk = lpn.no_ppsk AND ppsk_terakhir.no_urut = lpn.no_urut
	--	WHERE lpn.status not in ('A') and lpn.no_ppsk LIKE 'PPSK/$kode_farm/$periode%'
		WHERE lpn.status not in ('A','RJ') and lpn.no_ppsk LIKE 'PPSK/$kode_farm/$periode%'
SQL;
		
      $query = $this->db->query($sql);
      return $query->num_rows();
   }
   /** cek apakah penutupan budget terlambat atau tidak, maksimal doc-In - 4 */
   private function cekKeterlambatanTutupBudget($kode_siklus){
	   $sql = <<<SQL
	   SELECT (SELECT CAST(TGL_CLOSING AS DATE) FROM BUDGET_GLANGSING WHERE KODE_SIKLUS = {$kode_siklus}) tgl_closing
	   		, dateadd(day,-4,min(tgl_doc_in)) tgl_doc_in_min  FROM KANDANG_SIKLUS WHERE KODE_SIKLUS = (
			SELECT TOP 1 kode_siklus FROM M_PERIODE WHERE KODE_SIKLUS > {$kode_siklus} AND KODE_FARM = (SELECT KODE_FARM FROM M_PERIODE WHERE KODE_SIKLUS = {$kode_siklus})
		)
SQL;
	   $result = $this->db->query($sql)->row();	
	   $terlambat = 0;
	   if(!empty($result->tgl_doc_in_min)){
		if($result->tgl_closing > $result->tgl_doc_in_min){
			$terlambat = 1;
		}	
	   }
	   return array('status'=>$terlambat, 'tgl_closing' => $result->tgl_closing);
   }

    public function cetakHistori(){		
		$kode_siklus = $this->input->get('kode_siklus');			
		$kode_farm = $this->input->get('kode_farm');
		$periode = $this->getPeriode($kode_siklus);		
		$pakanTerima = $this->getTotalPakanTerima($kode_siklus);
		$pakanPakai = $this->getTotalPakanPakai($kode_siklus);	
		$dataPemakaian = $this->getGroupingPemakaian($kode_siklus);
		$stokLalu = $this->getStokAkhirSiklusLalu($kode_siklus,$kode_farm);
		$budgetGlangsing = arr2DToarrKey($this->getBudgetGlangsing($kode_siklus),'kategori');
	
		$data['periode_siklus'] = $periode->PERIODE_SIKLUS;
		$data['siklus_lalu'] = $stokLalu->periode_siklus;//$periode['SIKLUS_LALU'];
		$data['tahun'] = substr($periode->PERIODE_SIKLUS,0,4);
		$data['bulan'] = substr($periode->PERIODE_SIKLUS,5,2);
		$data['nama_farm'] = $this->getDataFarm($kode_farm)->NAMA_FARM;
		$data['total_pakan_terima'] = $pakanTerima->JML_TERIMA;
		$data['total_pakan_pakai'] = $pakanPakai->JML_PAKAI;
		$data['sisa_pakan'] = $pakanTerima->JML_TERIMA - $pakanPakai->JML_PAKAI;
		$data['stok_lalu'] = $stokLalu->jml_stok; //$result['SAK_AWAL'];
		$data['pemasukan_siklus_ini'] = $pakanPakai->JML_PAKAI;
		$data['glangsing_saat_ini'] = $pakanPakai->JML_PAKAI + $stokLalu->jml_stok;
		$data['pemakaian_internal'] = $dataPemakaian['total']['I'];
		$data['pemakaian_eksternal'] = $dataPemakaian['total']['E'];
		$data['dijual'] = 0;//$result['SAK_DIJUAL'];
		$data['sisa'] = ($pakanPakai->JML_PAKAI + $stokLalu->jml_stok) - ($dataPemakaian['total']['I'] + $dataPemakaian['total']['E']);
		$data['budget_internal'] = $dataPemakaian['I'];
		$data['budget_eksternal'] = $dataPemakaian['E'];
		$data['sisa_budget_internal'] =  $budgetGlangsing['I']['jml_budget'];
		$data['sisa_budget_eksternal'] = $budgetGlangsing['E']['jml_budget'];								
	
		$this->load->library ( 'Pdf' );
		$pdf = new Pdf ( 'L', PDF_UNIT, 'A4', true, 'UTF-8', false );

		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );	
		$html = $this->load->view ( 'budget_pengembalian_glangsing/bdy/cetak_histori',$data, true );
		$pdf->AddPage ();
		$pdf->writeHTML ( $html, true, false, true, false, '' );

		$pdf->Output ( 'Laporan Glangsing Akhir Siklus.pdf', 'I' );
	}
	
	public function getDataFarm($kode_farm = ''){
		$query = $this->db->query("
			select * from m_farm where kode_farm = '$kode_farm'
		");
		return $query->row();
	}	

	public function getPeriode($kode_siklus = ''){
		$query = $this->db->query("
			select * from m_periode where kode_siklus = '$kode_siklus'
		");
		return $query->row();
	}

	public function getStokAkhirSiklusLalu($kode_siklus,$kode_farm){
		$sql = <<<SQL
		SELECT jml_stok,periode_siklus  FROM glangsing_movement
		JOIN M_PERIODE ON M_PERIODE.KODE_SIKLUS = glangsing_movement.kode_siklus 
		WHERE glangsing_movement.kode_siklus = ( SELECT TOP 1 kode_siklus FROM glangsing_movement WHERE kode_farm = '{$kode_farm}' AND kode_siklus < {$kode_siklus} ORDER BY kode_siklus desc )
		AND kode_barang = 'GBP'
SQL;
		$hasil = $this->db->query($sql)->row();
		if(empty($hasil)){
			$hasil = new stdClass();
			$hasil->jml_stok = 0;
			$hasil->periode_siklus = 'not defined';
		}
		return $hasil;
	}

	public function getTotalPakanTerima($kode_siklus){
		$sql = <<<SQL
		SELECT  sum(md.jml_putaway) JML_TERIMA
		FROM MOVEMENT_D  md
		join penerimaan p on p.no_penerimaan = md.no_referensi and p.kode_farm = md.kode_farm
		JOIN kandang_siklus ks ON ks.NO_REG = md.keterangan2 AND ks.KODE_SIKLUS = {$kode_siklus}		
		WHERE md.KETERANGAN1 = 'PUT' AND md.NO_PALLET LIKE 'SYS%'
SQL;
		$query = $this->db->query($sql);
		return $query->row();
	}

	public function getTotalPakanPakai($kode_siklus){
		$sql = <<<SQL
		SELECT sum(rp.JML_PAKAI) JML_PAKAI FROM rhk_pakan rp
		JOIN kandang_siklus ks ON ks.NO_REG = rp.NO_REG AND ks.KODE_SIKLUS = {$kode_siklus}
SQL;

		$query = $this->db->query($sql);
		return $query->row();
	}
	
	public function getBudgetGlangsing($kode_siklus){
		$sql = <<<SQL
			SELECT mbpg.KATEGORI_BUDGET kategori,sum(jml_order) jml_budget 
			FROM BUDGET_GLANGSING_D bg
			JOIN M_BUDGET_PEMAKAIAN_GLANGSING mbpg ON mbpg.KODE_BUDGET = bg.KODE_BUDGET
			WHERE bg.KODE_SIKLUS = {$kode_siklus}
			GROUP BY mbpg.KATEGORI_BUDGET			
SQL;

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getPemakaianGlangsing($kode_siklus){
		$sql = <<<SQL
			SELECT sum(pd.jml_diminta - coalesce(pd.jml_kembali,0)) jml_dipakai, mpg.KATEGORI_BUDGET kategori, mpg.NAMA_BUDGET nama  FROM PPSK_NEW pn 
			JOIN ppsk_d pd ON pd.no_ppsk = pn.no_ppsk AND pd.tgl_terima IS NOT null
			JOIN M_BUDGET_PEMAKAIAN_GLANGSING mpg ON mpg.KODE_BUDGET = pn.kode_budget			
			WHERE pn.kode_siklus = $kode_siklus
			GROUP BY mpg.KATEGORI_BUDGET, mpg.NAMA_BUDGET			
SQL;

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function getGroupingPemakaian($kode_siklus){
		$result = $this->getPemakaianGlangsing($kode_siklus);
		$hasil = array(
			'I' => array(),
			'E' => array(),
			'total' => array(
				'I' => 0,
				'E' => 0
			)
		);
		if(!empty($result)){
			foreach($result as $r){
				$kategori = $r->kategori;
				$hasil[$kategori][] = $r;
				$hasil['total'][$kategori] += $r->jml_dipakai;
			}
		}
		return $hasil;
	}

}
