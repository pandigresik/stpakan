<?php
class M_report_kontrol_stok extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function list_kandang_all($where = array()){
		$this->load->model('forecast/m_kandang_siklus','ks');
		/* cari maximal tgl rhk tiap kandang */
		$this->db->select('max(rhk.tgl_transaksi) tgl_transaksi, ks.no_reg')
			->join('kandang_siklus ks','ks.no_reg = rhk.no_reg and ks.status_siklus <> \'P\'')
		;
		$this->db->from('rhk')->group_by('ks.no_reg');
		if(!empty($where)){
			$this->db->where($where);
		}
		$subquery = $this->db->get_compiled_select();

		$this->db->select('ks.no_reg,ks.kode_kandang,ks.kode_farm,ks.kode_siklus,ks.kode_std_breeding_j,
						ks.kode_std_breeding_b,ks.jml_jantan,ks.jml_betina,ks.tgl_doc_in,ks.tipe_kandang,
						mp.kode_strain,mf.nama_farm,mk.nama_kandang,rhk.tgl_transaksi rhk_terakhir,
						ks.status_siklus,mp.periode_siklus,ks.flok_bdy,ks.kode_farm+\'/\'+mp.periode_siklus periode')
				->from('kandang_siklus ks')
				->join('m_periode mp','ks.kode_siklus = mp.kode_siklus')
				->join('m_farm mf','mf.kode_farm = ks.kode_farm')
				->join('m_kandang mk','mk.kode_kandang = ks.kode_kandang and mk.kode_farm = mf.kode_farm')
				->join('('.$subquery.') as rhk','rhk.no_reg = ks.no_reg','left')
				->where('ks.status_siklus <> \'P\'')
				;

		if(!empty($where)){
			$this->db->where($where);
		}
		
		return $this->db->get()->result_array();
	}
	
	public function getRencanaPengiriman($noreg){
		$sql = <<<SQL
			SELECT FORECAST.TGL_KIRIM, FORECAST_D.TGL_KEBUTUHAN, DATEDIFF(DAY, siklus.TGL_DOC_IN, FORECAST_D.TGL_KEBUTUHAN) AS umur, FORECAST_D.KODE_BARANG, FORECAST_D.JML_FORECAST
			FROM FORECAST
			INNER JOIN FORECAST_D ON FORECAST_D.FORECAST = FORECAST.id
			INNER JOIN KANDANG_SIKLUS siklus ON siklus.KODE_SIKLUS=FORECAST.KODE_SIKLUS AND siklus.FLOK_BDY=FORECAST.KODE_FLOK_BDY
			WHERE siklus.NO_REG='{$noreg}'
			ORDER BY TGL_KIRIM, TGL_KEBUTUHAN
SQL;
		return $this->db->query($sql)->result_array();
	}
	
	public function getJenisBarangRencanaPengiriman(){
		$sql = <<<SQL
			SELECT FORECAST_D.KODE_BARANG, grup.DESKRIPSI
			FROM FORECAST_D
			INNER JOIN M_BARANG ON M_BARANG.KODE_BARANG=FORECAST_D.KODE_BARANG
			INNER JOIN M_GRUP_BARANG grup ON grup.GRUP_BARANG = M_BARANG.GRUP_BARANG
			GROUP BY FORECAST_D.KODE_BARANG, grup.DESKRIPSI
			ORDER BY grup.DESKRIPSI
SQL;
		return $this->db->query($sql)->result_array();
	}
	
	public function getKandangSiklus($noreg){
		$sql = <<<SQL
			SELECT * FROM KANDANG_SIKLUS
			WHERE NO_REG='{$noreg}'
SQL;
		return $this->db->query($sql)->result_array();
	}
	
	public function getPengajuanBudget($kode_siklus){
		$sql = <<<SQL
			SELECT trans_budget.KODE_BUDGET, master_budget.NAMA_BUDGET, trans_budget.JML_ORDER FROM (
				SELECT *, ROW_NUMBER() OVER (PARTITION BY KODE_BUDGET ORDER BY NO_URUT desc) AS rn
				FROM BUDGET_GLANGSING_D 
				WHERE KODE_SIKLUS='{$kode_siklus}'
			) trans_budget
			INNER JOIN M_BUDGET_PEMAKAIAN_GLANGSING master_budget ON master_budget.KODE_BUDGET = trans_budget.KODE_BUDGET
			WHERE rn=1
SQL;
		return $this->db->query($sql)->result_array();
	}
	
	public function getPlottingPelaksana($noreg){
		$sql = <<<SQL
			SELECT *,ROW_NUMBER() OVER (PARTITION BY job_desc ORDER BY kode_pegawai) AS rn FROM (
				SELECT M_PLOTING_PELAKSANA.PENGAWAS AS kode_pegawai, M_PEGAWAI.NAMA_PEGAWAI AS nama_pegawai, 'pengawas' AS job_desc
				FROM M_PLOTING_PELAKSANA
				INNER JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = M_PLOTING_PELAKSANA.PENGAWAS
				WHERE NO_REG='{$noreg}'
				GROUP BY M_PLOTING_PELAKSANA.PENGAWAS, M_PEGAWAI.NAMA_PEGAWAI
			) plotting
			UNION
			SELECT *,ROW_NUMBER() OVER (PARTITION BY job_desc ORDER BY kode_pegawai) AS rn FROM (
				SELECT M_PLOTING_PELAKSANA.OPERATOR AS kode_pegawai, M_PEGAWAI.NAMA_PEGAWAI AS nama_pegawai, 'operator' AS job_desc
				FROM M_PLOTING_PELAKSANA
				INNER JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = M_PLOTING_PELAKSANA.OPERATOR
				WHERE NO_REG='{$noreg}'
				GROUP BY M_PLOTING_PELAKSANA.OPERATOR, M_PEGAWAI.NAMA_PEGAWAI
			) plotting	
			ORDER BY job_desc DESC, rn
SQL;
		return $this->db->query($sql)->result_array();
	}
	
	public function getAktivasiSiklus($noreg, $kode_siklus){
		$sql = <<<SQL
			SELECT prev_cycle.siklus_sebelumnya, prev_cycle.status_tutup_siklus_sebelumnya, state.last_state,
			CASE  
				WHEN state.last_state='P1' THEN 'Release'
				WHEN state.last_state='P2' THEN 'Review'
				WHEN state.last_state='RL' THEN 'Approve'
				ELSE '' 
			END as keterangan_last_state, 
			cycle_state_transition.state, M_PEGAWAI.NAMA_PEGAWAI,
			CASE  
				WHEN cycle_state_transition.state='P1' THEN 'Dirilis'
				WHEN cycle_state_transition.state='P2' THEN 'Dikoreksi'
				WHEN cycle_state_transition.state='RL' THEN 'Disetujui'
				ELSE '' 
			END as keterangan_state, 
			cycle_state_transition.stamp
			FROM cycle_state_transition 
			INNER JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = cycle_state_transition.[user]
			INNER JOIN (
				SELECT TOP 1 '{$noreg}' AS noreg, M_PERIODE.PERIODE_SIKLUS AS siklus_sebelumnya, M_PERIODE.STATUS_PERIODE AS status_tutup_siklus_sebelumnya
				FROM cycle_state_transition
				INNER JOIN M_PERIODE ON M_PERIODE.KODE_SIKLUS = cycle_state_transition.cycle
				WHERE cycle<$kode_siklus AND noreg LIKE concat(LEFT('{$noreg}',2),'%')
				ORDER BY cycle DESC
			) prev_cycle ON prev_cycle.noreg=cycle_state_transition.noreg
			CROSS JOIN (
				SELECT max(state) AS last_state
				from cycle_state_transition
				WHERE noreg='{$noreg}'
			) state
			WHERE cycle_state_transition.noreg='{$noreg}'
			ORDER BY stamp DESC
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function list_ppsk($noreg,$tgldocin){
		return $this->db->select('datediff(day,\''.$tgldocin.'\',pn.tgl_kebutuhan) umur',false)
					->select('pn.tgl_kebutuhan,pn.no_ppsk,mbpg.nama_budget,pd.jml_diminta,pd.tgl_terima,mp.nama_pegawai user_penerima,pd.tgl_kembali,mp2.nama_pegawai user_pengembali,mp3.nama_pegawai user_ack,pd.tgl_ack')
					->select('coalesce(pd.jml_kembali,0) jml_kembali',false)
					->select('bp.no_berita_acara,pn.jml_over_budget,pn.keterangan')
					->join('ppsk_new pn','pn.no_ppsk = pd.no_ppsk')
					->join('m_budget_pemakaian_glangsing mbpg','mbpg.kode_budget = pn.kode_budget')
					->join('m_pegawai mp','mp.kode_pegawai = pd.user_penerima','left')
					->join('m_pegawai mp2','mp2.kode_pegawai = pd.user_pengembali','left')
					->join('m_pegawai mp3','mp3.kode_pegawai = pd.user_ack','left')
					->join('ba_pemusnahan bp','bp.no_ppsk = pd.no_ppsk','left')
					->where(array('no_reg' => $noreg))
					->where('pd.jml_diminta > 0')
					->get('ppsk_d pd')
					->result_array();
	}

	public function log_ppsk($noreg){
		$sql_subquery = $this->db->select('pn.no_ppsk')
							->join('ppsk_new pn','pn.no_ppsk = pd.no_ppsk')
							->where(array('no_reg' => $noreg))
							->where('pd.jml_diminta > 0')
							->get_compiled_select('ppsk_d pd');
		return $this->db->select('lpn.*,mp.nama_pegawai')->where('no_ppsk in ('.$sql_subquery.')')
					->join('m_pegawai mp','mp.kode_pegawai = lpn.user_buat')
					->order_by('no_urut','desc')->get('log_ppsk_new lpn')->result_array(); 				
					
	}

	public function sales_order($tgldocin,$kode_farm){
		return $this->db->select('so.no_so,so.tgl_so, sd.jumlah, sd.harga_jual,sd.harga_total ,p.nominal_bayar,p.lampiran,mbpg.nama_budget, mp.nama_pelanggan')
						->select('sj.no_sj,sj.no_kendaraan,sj.nama_sopir,sj.tgl_buat,sj.tgl_verifikasi,mpg.nama_pegawai user_verifikasi,sj.tgl_realisasi,mpg2.nama_pegawai user_realisasi,sj.tgl_verifikasi_security,mpg3.nama_pegawai user_verifikasi_security')
						->join('sales_order_d sd','sd.no_so = so.no_so')
						->join('m_budget_pemakaian_glangsing mbpg','mbpg.kode_budget = sd.kode_barang')
						->join('m_pelanggan mp','mp.kode_pelanggan = so.kode_pelanggan')
						->join('pembayaran p','p.no_so = so.no_so','left')
						->join('surat_jalan sj','sj.no_so = so.no_so','left')
						->join('m_pegawai mpg','mpg.kode_pegawai = sj.user_verifikasi','left')
						->join('m_pegawai mpg2','mpg2.kode_pegawai = sj.user_realisasi','left')
						->join('m_pegawai mpg3','mpg3.kode_pegawai = sj.user_verifikasi_security','left')
						->where('so.tgl_so >= \''.$tgldocin.'\'')
						->where(array('kode_farm' => $kode_farm))
						->order_by('tgl_so')
						->get('sales_order so')
						->result_array();
	}

	public function log_sales_order($tgldocin,$kode_farm){
		return $this->db->select('lso.*,mp.nama_pegawai')
						->join('sales_order so','lso.no_so = so.no_so and so.kode_farm =\''.$kode_farm.'\' and so.tgl_so >= \''.$tgldocin.'\'')
						->join('m_pegawai mp','mp.kode_pegawai = lso.user_buat')
						->order_by('no_urut','desc')
						->order_by('no_so')
						->get('log_sales_order lso')
						->result_array();
	}

	public function pengajuan_harga($tgldocin,$kode_farm){
		return $this->db->select('ph.no_pengajuan_harga')
						->select('cast(ph.tgl_pengajuan as date) tgl_pengajuan',false)
						->where('ph.tgl_pengajuan >= \''.$tgldocin.'\'')
						->where(array('kode_farm' => $kode_farm))
						->get('pengajuan_harga ph')
						->result_array();
	}

	public function pengajuan_harga_d($tgldocin,$kode_farm){
		return $this->db->select('lph.*,mbpg.nama_budget')
						->join('pengajuan_harga ph','ph.no_pengajuan_harga = lph.no_pengajuan_harga and ph.kode_farm = \''.$kode_farm.'\' and ph.tgl_pengajuan >= \''.$tgldocin.'\'')
						->join('m_budget_pemakaian_glangsing mbpg','mbpg.kode_budget = lph.kode_barang')
						->get('pengajuan_harga_d lph')
						->result_array();
	}

	public function log_pengajuan_harga($tgldocin,$kode_farm){
		return $this->db->select('lph.*,mp.nama_pegawai')
						->join('pengajuan_harga ph','ph.no_pengajuan_harga = lph.no_pengajuan_harga and ph.kode_farm = \''.$kode_farm.'\' and ph.tgl_pengajuan >= \''.$tgldocin.'\'')
						->join('m_pegawai mp','mp.kode_pegawai = lph.user_buat')
						->order_by('no_urut','desc')
						->order_by('no_pengajuan_harga')
						->get('log_pengajuan_harga lph')
						->result_array();
	}

	public function log_budget_glangsing($kode_siklus){
		$sql_subquery = $this->db->select_max('no_urut')->where(array('kode_siklus' => $kode_siklus))->get_compiled_select('log_budget_glangsing');
		return $this->db->select('lbg.*,mp.NAMA_PEGAWAI')
					->where(array('kode_siklus' => $kode_siklus))
					->where('no_urut = ('.$sql_subquery.')')
					->join('m_pegawai mp','mp.kode_pegawai = lbg.user_buat')
					->order_by('no_urut_approve','desc')
					->get('log_budget_glangsing lbg')
					->result_array();
	}

	public function log_ploting_pelaksana($noreg){
		return $this->db->select('top 1 mpp.*,mp.nama_pegawai USER_BUAT,mp2.nama_pegawai USER_REVIEW,mp3.nama_pegawai USER_ACK',false)
					->where(array('no_reg'=>$noreg))
					->join('m_pegawai mp','mp.kode_pegawai = mpp.user_buat','left')
					->join('m_pegawai mp2','mp2.kode_pegawai = mpp.user_review','left')
					->join('m_pegawai mp3','mp3.kode_pegawai = mpp.user_ack','left')
					->get('m_ploting_pelaksana mpp')
					->row_array();
	}

	public function status_timbang_pallet($kode_siklus,$kode_farm){
		$sql = <<<SQL
		SELECT count(*) jumlah,'pallet' AS jenis FROM M_PALLET WHERE KODE_FARM = '{$kode_farm}' AND STATUS_PALLET = 'N'
		AND TGL_TIMBANG >= (SELECT cast(min(stamp) as date) FROM cycle_state_transition WHERE state = 'RL' AND cycle = {$kode_siklus} )
		UNION ALL 
		SELECT count(*) jumlah,'hand_pallet' AS jenis FROM M_hand_PALLET WHERE KODE_FARM = '{$kode_farm}' AND STATUS_PALLET = 'N'
		AND TGL_TIMBANG >= (SELECT cast(min(stamp) as date) FROM cycle_state_transition WHERE state = 'RL' AND cycle = {$kode_siklus} )
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function detail_timbang_pallet($kode_siklus,$kode_farm,$jenis){
		if($jenis == 'pallet'){
			$sql = <<<SQL
		SELECT * FROM M_PALLET WHERE KODE_FARM = '{$kode_farm}' AND STATUS_PALLET = 'N'
		AND TGL_TIMBANG >= (SELECT cast(min(stamp) as date) FROM cycle_state_transition WHERE state = 'RL' AND cycle = {$kode_siklus} )
SQL;
		}else{
			$sql = <<<SQL
		SELECT * FROM M_hand_PALLET WHERE KODE_FARM = '{$kode_farm}' AND STATUS_PALLET = 'N'
		AND TGL_TIMBANG >= (SELECT cast(min(stamp) as date) FROM cycle_state_transition WHERE state = 'RL' AND cycle = {$kode_siklus} )
SQL;
		}
		return $this->db->query($sql)->result_array();
	}
}
