<?php
class M_surat_jalan extends MY_Model{
	protected $primary_key;

	public function __construct(){
		parent::__construct();
		$this->_table = 'surat_jalan';
		$this->primary_key= 'no_sj';
	}

	public function list_do($params){
		if($params['status'] == 'semua'){
			$sql = <<<SQL
			select sj.*,(SELECT sum(jumlah) FROM surat_jalan_d WHERE no_sj = sj.no_sj) jml_sak from surat_jalan sj where sj.tgl_verifikasi is not null and sj.tgl_realisasi is null or (cast(sj.tgl_realisasi as date) between '{$params['start_date']}' and '{$params['end_date']}')
SQL;
		}
		if($params['status'] == 'sudah'){
			$sql = <<<SQL
			select sj.*,(SELECT sum(jumlah) FROM surat_jalan_d WHERE no_sj = sj.no_sj) jml_sak from surat_jalan sj where sj.tgl_verifikasi is not null and cast(sj.tgl_realisasi as date) between '{$params['start_date']}' and '{$params['end_date']}'
SQL;
		}
		if($params['status'] == 'belum'){
			$sql = <<<SQL
			select sj.*,(SELECT sum(jumlah) FROM surat_jalan_d WHERE no_sj = sj.no_sj) jml_sak from surat_jalan sj where sj.tgl_verifikasi is not null and sj.tgl_realisasi is null
SQL;
		}

		return $this->db->query($sql);
	}
	public function check_nomor_sj($no_sj = '')
	{
		$sql = <<<SQL
		select * from surat_jalan where no_sj = '$no_sj' and tgl_realisasi is not null and tgl_verifikasi_security is null
SQL;
		return $this->db->query($sql);
	}

	
}
