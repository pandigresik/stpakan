<?php
class M_ppsk_d extends MY_Model{
	protected $before_create = array('no_ppsk');
	protected $primary_key;
	public function __construct(){
		parent::__construct();
		$this->_table = 'ppsk_d';
		$this->primary_key= 'NO_PPSK';
	}
	public function no_ppsk($row)
	{
		$tmp = $this->db->order_by('no_ppsk','desc')->get('ppsk where no_ppsk like \''.$no_ppsk.'%\'');
        $tmp = $tmp->row(0);
		print_r($tmp);
        
        if(count($tmp) > 0){
           $no_urut_ppsk = (int)substr($tmp->NO_PPSK,-3);
        }
        else{
           $no_urut_ppsk = 0;
        }
        $no_urut_ppsk++;
        $no_urut_ppsk = str_pad($no_urut_ppsk,3,'0',STR_PAD_LEFT);
        print_r($no_urut_ppsk);

        return $no_ppsk.$no_urut_ppsk;
	}

	public function getListAck($kodeFarm, $kodeKandang = '', $status = ''){
		$status = str_replace(',', '\',\'', $status);
		$sql = <<<SQL
				select *
				from (
					select ph.no_ppsk, ph.tgl_permintaan, ph.tgl_kebutuhan, ph.kode_budget
						, mbg.nama_budget, pd.no_reg, mk.kode_farm, mk.kode_kandang, mk.nama_kandang
						, pd.jml_diminta, pd.jml_kembali, pd.jml_diminta - pd.jml_kembali as jml_pakai
						, p1.nama_pegawai user_pengembali, pd.tgl_kembali, p2.nama_pegawai user_ack, pd.tgl_ack
						, case when jml_kembali is null then 'BK' -- Belum Kembali
							when user_ack is null then 'BA' -- Belum ACK
							else 'SA' end status
					from ppsk_new ph
					join ppsk_d pd on ph.no_ppsk = pd.no_ppsk and pd.tgl_terima is not null --and pd.user_pengembali is null
					join M_BUDGET_PEMAKAIAN_GLANGSING mbg ON ph.kode_budget = mbg.kode_budget
					left join m_pegawai p1 on pd.user_pengembali = p1.kode_pegawai
					left join m_pegawai p2 on pd.user_ack = p2.kode_pegawai
					left join KANDANG_SIKLUS ks on pd.no_reg = ks.no_reg  and kode_kandang like '{$kodeKandang}'
					left join m_kandang mk on ks.KODE_KANDANG = mk.KODE_KANDANG and ks.KODE_FARM = mk.KODE_FARM
					where ph.tgl_kebutuhan > dateadd(day, -30, getdate())
				) list
				where  status in('{$status}')
				order by no_ppsk desc, no_reg

SQL;

		//cetak_r($sql);
		return $this->db->query($sql)->result_array();
	}

	public function getAllKandang($kodeFarm){
		$kodeFarm = str_replace(',', '\',\'', $kodeFarm);
		$kodeFarm = str_replace('\'\'', '\'', $kodeFarm);
		$sql = <<<SQL
				select *
				from m_kandang
				where kode_farm in('{$kodeFarm}')

SQL;

		return $this->db->query($sql)->result_array(); //$this->db->query($sql);
	}


   function get_today(){
		$sql = <<<QUERY
		select getdate() as [today]
QUERY;
		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function calculate_glangsing_movement($no_ppsk){
		$sql = <<<QUERY
			EXEC dbo.calculate_glangsing_movement '{$no_ppsk}'
QUERY;
		//cetak_r($sql);
		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function retur_in_glangsing_movement($no_ppsk, $no_reg){
		$sql = <<<QUERY
			EXEC dbo.retur_in_glangsing_movement '{$no_ppsk}','{$no_reg}'
QUERY;
		//cetak_r($sql);
		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

}
