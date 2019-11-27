<?php
class M_ppsk extends MY_Model{
	protected $before_create = array('no_ppsk');
	protected $primary_key;
	public function __construct(){
		parent::__construct();
		$this->_table = 'ppsk_new';
		$this->primary_key= 'NO_PPSK';
	}
	public function no_ppsk($no_ppsk)
	{
		$tmp = $this->db->order_by('no_ppsk','desc')->get('ppsk_new where no_ppsk like substring(\''.$no_ppsk.'\', 1, 14)+\'%\'');
        $tmp = $tmp->row(0);
		//print_r($tmp);

        if(count($tmp) > 0){
           $no_urut_ppsk = (int)substr($tmp->NO_PPSK,-3);
        }
        else{
           $no_urut_ppsk = 0;
        }
        $no_urut_ppsk++;
        $no_urut_ppsk = str_pad($no_urut_ppsk,3,'0',STR_PAD_LEFT);
        //print_r($no_urut_ppsk);

        return substr($no_ppsk, 0, 14).$no_urut_ppsk;
	}
	public function getToleransiBerat($kodeFarm = 'BW'){
		$sql = <<<SQL

			select BATAS_BAWAH, BATAS_ATAS from m_toleransi
			where kode_farm = '{$kodeFarm}' and kategori = 'GLANGSING'

SQL;
		return $this->db->query($sql)->result_array(); //$this->db->query($sql);
	}

	public function getKandangByRFID($rfid){
		/*
			mk m_kandang
			ks kandang_siklus
			pd ppsk_d
			ph ppsk_new
			mb m_budget_pemakaian_glangsing
		*/
		
		$sql = <<<SQL

			select mk.NAMA_KANDANG, ks.NO_REG, ks.FLOK_BDY, pd.no_ppsk, pd.jml_diminta, ph.kode_budget, ph.tgl_kebutuhan, mb.nama_budget
			from m_kandang mk
			join KANDANG_SIKLUS ks on mk.kode_farm = ks.kode_farm and mk.KODE_KANDANG = ks.KODE_KANDANG and ks.STATUS_SIKLUS = 'O'
			join ppsk_d pd on ks.NO_REG = pd.no_reg and pd.tgl_terima is not null and pd.user_pengembali is null
			join ppsk_new ph on pd.no_ppsk = ph.no_ppsk
			join m_budget_pemakaian_glangsing mb on ph.kode_budget = mb.KODE_BUDGET
			where mk.kode_verifikasi = '{$rfid}' 
			and ph.tgl_kebutuhan between dateadd(dd, datediff(dd, 0, getdate())-1, 0) and dateadd(dd, datediff(dd, 0, getdate()), 0)
			order by ph.tgl_kebutuhan ASC

SQL;
		return $this->db->query($sql)->result_array(); //$this->db->query($sql);
	}

	public function getEstimasiStok(){
		$sql = <<<SQL
		select gm.kode_farm, mf.nama_farm, gm.kode_barang, mb.nama_barang, (gm.jml_stok - coalesce(pengurang.jml_diminta,0)) jml_estimasi
		from glangsing_movement gm
		join M_BARANG mb on gm.kode_barang = mb.KODE_BARANG
		JOIN (
			SELECT max(kode_siklus) AS kode_siklus,kode_farm FROM glangsing_movement GROUP BY kode_farm
		)siklus_terakhir ON siklus_terakhir.kode_siklus = gm.kode_siklus AND gm.kode_farm = siklus_terakhir.kode_farm
		join m_farm mf on gm.kode_farm = mf.kode_farm
		LEFT JOIN (
			SELECT sum(pd.jml_diminta) jml_diminta,pn.kode_siklus, pn.kode_budget FROM ppsk_new  pn
			INNER JOIN ppsk_d pd ON pn.no_ppsk = pd.no_ppsk AND pd.tgl_terima IS NULL AND pd.jml_diminta > 0
			WHERE tgl_terima IS null
			GROUP BY pn.kode_siklus, pn.kode_budget
		)pengurang ON pengurang.kode_siklus = gm.kode_siklus AND pengurang.kode_budget = gm.kode_barang
		order by gm.kode_farm, gm.kode_barang
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function sudahMintaSak($kode_siklus){
		$sql = <<<SQL
--			select distinct(kode_budget) kode_budget from ppsk_new pn where cast(pn.tgl_permintaan as date) = cast(getdate() as date) and kode_siklus = {$kode_siklus}
		select distinct(kode_budget) kode_budget 
		from ppsk_new pn
		JOIN (
			SELECT no_ppsk,max(no_urut) no_urut FROM log_ppsk_new  WHERE CAST(tgl_buat AS DATE) = CAST(getdate() AS DATE) GROUP BY no_ppsk
		)lg ON lg.no_ppsk = pn.no_ppsk 
		JOIN log_ppsk_new lpn ON lpn.no_ppsk = pn.no_ppsk AND lpn.no_urut = lg.no_urut AND lpn.status != 'RJ'  
		where cast(pn.tgl_permintaan as date) = cast(getdate() as date) AND pn.kode_siklus = {$kode_siklus}
SQL;
		return $this->db->query($sql)->result_array();
	}
}
