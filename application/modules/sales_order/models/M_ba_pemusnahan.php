<?php
class M_ba_pemusnahan extends MY_Model{
	protected $primary_key;
	public function __construct(){
		parent::__construct();
		$this->_table = 'ba_pemusnahan';
		$this->primary_key= 'no_berita_acara';
	}
	public function no_berita_acara($kode_farm)
	{
		$tmp = $this->db->select("substring(max(no_berita_acara),4,3) as no_urut",false)->where(array('kode_farm' => $kode_farm))->where('year(tgl_buat) = year(getdate())')->get($this->_table)->row();
        $urut = empty($tmp->no_urut) ? '1' : intval($tmp->no_urut) + 1;
        $no_urut = str_pad($urut,3,'0',STR_PAD_LEFT);
        return 'BA-'.$no_urut.'/'.$kode_farm.'/'.date('m').'/'.date('Y');
	}

	public function list_ba($params){
		$whereParams = ' ba.no_berita_acara IS null';
		if(!empty($params["start_date"])){
			$whereParams = ' pn.tgl_kebutuhan BETWEEN \''.$params["start_date"].'\' AND \''.$params["end_date"].'\'';
		}
		$whereParams .= ' and pn.no_ppsk like \'PPSK/'.$params["kode_farm"].'%\'';
		$sql = <<<SQL
		SELECT pn.no_ppsk,pn.tgl_kebutuhan
			,sum(pd.jml_diminta) - sum(coalesce(pd.jml_kembali,0)) pakai
			, ba.jml
			, ba.no_berita_acara
		FROM ppsk_new pn
		JOIN ppsk_d pd ON pn.no_ppsk = pd.no_ppsk and pd.tgl_kembali is not null
		LEFT JOIN ba_pemusnahan ba ON ba.no_ppsk = pn.no_ppsk
		WHERE {$whereParams}
		AND pn.kode_budget = 'GB'
		GROUP BY pn.no_ppsk,pn.tgl_kebutuhan, ba.jml, ba.no_berita_acara
		order by pn.tgl_kebutuhan
SQL;
		// echo $sql;
		return $this->db->query($sql);
	}
	public function detail_kandang($no_ppsk = ''){
		$sql = <<<SQL
			SELECT
				ps.*,
				coalesce(ps.jml_diminta,0) - coalesce(ps.jml_kembali,0) jml_akhir,
				ks.KODE_KANDANG
			FROM ppsk_d ps
			LEFT JOIN KANDANG_SIKLUS ks on ps.no_reg = ks.no_reg
			WHERE ps.no_ppsk = '$no_ppsk'
SQL;
		// echo $sql;
		return $this->db->query($sql);
	}
}
