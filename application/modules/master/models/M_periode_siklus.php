<?php
class M_periode_siklus extends CI_Model {
	private $dbSqlServer;
	public function __construct() {
		parent::__construct ();
		
	}
	function get_periode_siklus($kode_farm, $start = null, $offset = null, $periodesiklus = null, $namafarm = null, $namastrain = null, $status = null) {
		$filter_str = "";
		$filter_arr = array ();

		$filter_bottom_str = "";
		$filter_bottom_arr = array ();

		if (isset ( $kode_farm ))
		//	$filter_arr [] = "mp.KODE_FARM = '" . $kode_farm . "'";

		if (isset ( $periodesiklus ))
			$filter_arr [] = "mp.PERIODE_SIKLUS like '%" . $periodesiklus . "%'";
		if (isset ( $namafarm ))
			$filter_arr [] = "mf.NAMA_FARM like '%" . $namafarm . "%'";
		if (isset ( $namastrain ))
			$filter_arr [] = "ms.NAMA_STRAIN like '%" . $namastrain . "%'";
		if (isset ( $status ))
			$filter_arr [] = "mp.STATUS_PERIODE = '" . $status . "'";

		if (count ( $filter_arr ) > 0) {
			$filter_str .= " where ";
			$filter_str .= implode ( " and ", $filter_arr );
		}

		if (isset ( $start ) and isset ( $offset ))
			$filter_bottom_arr [] = "row > {$start} and row <= {$offset}";

		if (count ( $filter_bottom_arr ) > 0) {
			$filter_bottom_str .= " where ";
			$filter_bottom_str .= implode ( " and ", $filter_bottom_arr );
		}

		$sql = <<<QUERY
			select * from (
				select
					ROW_NUMBER() OVER (ORDER BY mp.PERIODE_SIKLUS) AS ROW
					, mp.KODE_SIKLUS kode_siklus
					, mp.PERIODE_SIKLUS periode_siklus
					, mf.NAMA_FARM nama_farm
					, ms.NAMA_STRAIN nama_strain
					, case
						when mp.STATUS_PERIODE = 'A' then 'AKTIF'
						when mp.STATUS_PERIODE = 'N' then 'TIDAK AKTIF'
						else '-'
					end status_periode_siklus
				from M_PERIODE mp
				join M_FARM mf on mf.KODE_FARM = mp.KODE_FARM
				join M_STRAIN ms on ms.KODE_STRAIN = mp.KODE_STRAIN
				$filter_str
	) mainqry
	$filter_bottom_str
QUERY;
		// echo $sql;
		$stmt = $this->db->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );
	}
	function get_periode_siklus_by_id($kodeperiodesiklus) {
		$sql = <<<QUERY
		select top 1
			mp.KODE_SIKLUS kode_siklus
			, mp.PERIODE_SIKLUS periode_siklus
			, mf.NAMA_FARM nama_farm
			, mf.KODE_FARM kode_farm
			, ms.NAMA_STRAIN nama_strain
			, ms.KODE_STRAIN kode_strain
			, mp.STATUS_PERIODE status_periode_siklus
			, mp.TGL_UBAH tanggal_tutup
			, ks.STATUS_SIKLUS status_siklus
			from M_PERIODE mp
			join M_FARM mf on mf.KODE_FARM = mp.KODE_FARM
			join M_STRAIN ms on ms.KODE_STRAIN = mp.KODE_STRAIN
			left join KANDANG_SIKLUS ks on mp.KODE_SIKLUS = ks.KODE_SIKLUS
				where mp.KODE_SIKLUS = '{$kodeperiodesiklus}'

QUERY;

		$stmt = $this->db->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
    function check_periode_siklus_old($kodefarm, $periodesiklus) {
        $sql = <<<QUERY
            select count(*) n_result
            from M_PERIODE
            where KODE_FARM = '{$kodefarm}' and PERIODE_SIKLUS = '{$periodesiklus}'
QUERY;

        $stmt = $this->db->conn_id->prepare ( $sql );
        $stmt->execute ();
        return $stmt->fetch ( PDO::FETCH_ASSOC );
    }
    function check_periode_siklus($kodefarm, $periodesiklus) {
        $sql = <<<QUERY
            SELECT TOP 1
                CASE
                    WHEN charindex('-',PERIODE_SIKLUS) = 0 THEN '$periodesiklus'+'-1'
                    ELSE '$periodesiklus'+'-'+CAST(CAST(SUBSTRING(PERIODE_SIKLUS,charindex('-',PERIODE_SIKLUS)+1,1000) AS INT) + 1 AS VARCHAR(MAX))
                END new_periode_siklus
                , PERIODE_SIKLUS periode_siklus
            FROM M_PERIODE
            WHERE KODE_FARM = '$kodefarm' AND PERIODE_SIKLUS LIKE '$periodesiklus%'
            ORDER BY PERIODE_SIKLUS DESC
QUERY;
        #echo $sql;
        $stmt = $this->db->conn_id->prepare ( $sql );
        $stmt->execute ();
        $data = $stmt->fetch ( PDO::FETCH_ASSOC );
        $newperiodesiklus = (empty($data['new_periode_siklus'])) ? $periodesiklus.'-1' : $data['new_periode_siklus'] ;
        $periode_siklus = (empty($data['new_periode_siklus'])) ? $periodesiklus.'-1' : $data['periode_siklus'] ;
        $result = (empty($data['new_periode_siklus'])) ? 0 : 1 ;

        return array(
            'result'                => $result,
            'new_periode_siklus'   => $newperiodesiklus,
            'periode_siklus'       => $periode_siklus
        );
    }
	function check_digitcheck_kandang($kodefarm, $digit_check) {
		$sql = <<<QUERY
			select count(*) n_result
			from m_kandang
			where kode_farm = '{$kodefarm}' kode_verifikasi = '{$digit_check}'

QUERY;

		$stmt = $this->db->conn_id->prepare ( $sql );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );
	}
	function insert($data) {
		$this->db->insert ( "M_PERIODE", $data );

		return ($this->db->affected_rows () != 1) ? false : true;
	}
	function update($data, $kodefarm, $periodesiklus) {
		$this->db->where ( "KODE_FARM", $kodefarm );
		$this->db->where ( "PERIODE_SIKLUS", $periodesiklus );
		$this->db->update ( "M_PERIODE", $data );

		return ($this->db->affected_rows () != 1) ? false : true;
	}

	function cekVerifikasiPanen($kodesiklus,$kodefarm){
		$sql = <<<SQL
		select ks.no_reg,count(rp.no_surat_jalan) sudah_panen -- ,count(rpd.no_surat_jalan) jml_sj
		from kandang_siklus ks
		left join REALISASI_PANEN rp on ks.no_reg = rp.no_reg
--		left join REALISASI_PANEN_DETAIL rpd on rp.NO_SURAT_JALAN = rpd.NO_SURAT_JALAN
		where ks.KODE_FARM = '{$kodefarm}' and ks.KODE_SIKLUS = '{$kodesiklus}'
		and ks.STATUS_SIKLUS = 'O'
		group by ks.no_reg
SQL;
	return $this->db->query($sql);
	}

	public function noreg_list($kode_farm){
		$arr_push = array();
		$sql = <<<SQL
			select no_reg from kandang_siklus where status_siklus = 'O' and kode_farm = '$kode_farm'
SQL;
		$arr = $this->db->query($sql)->result_array();
		foreach ($arr as $key => $value) {
			array_push($arr_push,$value['no_reg']);
		}
		return $arr_push;
	}
	public function kandang_list($kode_farm){
		$sql = <<<SQL
			select * from kandang_siklus where status_siklus = 'O' and kode_farm = '$kode_farm'
SQL;
		return $this->db->query($sql);
	}

	public function stok_ayam($kode_farm)
	{		
		$sql = <<<SQL
		SELECT * FROM (
			SELECT umur_7.no_reg
				,umur_7.c_awal - pengurang.jml jml_akhir
				,panen.jml
				,(umur_7.c_awal - pengurang.jml - panen.jml - ((SELECT sum(c_afkir) FROM RHK WHERE rhk.NO_REG = umur_7.no_reg AND rhk.TGL_TRANSAKSI > umur_7.tgl_transaksi))) sisa
			FROM 
			(	SELECT r.no_reg, r.tgl_transaksi,r.c_awal	
				FROM RHK r
				INNER JOIN KANDANG_SIKLUS ks ON ks.NO_REG = r.NO_REG AND ks.STATUS_SIKLUS = 'O' 
					AND datediff(day,ks.TGL_DOC_IN,r.TGL_TRANSAKSI) = 7
					AND ks.KODE_FARM = '{$kode_farm}') umur_7
			INNER JOIN (   
				SELECT r.no_reg , sum(c_mati)	jml
				FROM RHK r
				INNER JOIN KANDANG_SIKLUS ks ON ks.NO_REG = r.NO_REG AND ks.STATUS_SIKLUS = 'O'  AND ks.KODE_FARM = '{$kode_farm}'
				GROUP BY r.no_reg	
				)pengurang ON umur_7.no_reg = pengurang.no_reg
			INNER JOIN (	
				SELECT rp.no_reg ,sum(jumlah_aktual) jml
				FROM REALISASI_PANEN rp
				INNER JOIN KANDANG_SIKLUS ks ON ks.NO_REG = rp.NO_REG AND ks.STATUS_SIKLUS = 'O' AND ks.KODE_FARM = '{$kode_farm}'
				GROUP BY rp.no_reg		
				)panen ON panen.no_reg = umur_7.no_reg	
			)yy WHERE yy.sisa > 0
			order by yy.no_reg

SQL;
		return $this->db->query($sql);
	}
	public function belum_realisasi($kode_farm){
		$sql = <<<SQL
		SELECT ks.no_reg
		FROM KANDANG_SIKLUS ks 
		LEFT JOIN REALISASI_PANEN rp ON ks.NO_REG = rp.NO_REG
		WHERE ks.STATUS_SIKLUS = 'O' AND ks.KODE_FARM = '{$kode_farm}' and rp.NO_REG IS NULL 
		order by ks.no_reg
SQL;
		return $this->db->query($sql);
	}
	public function stk_pakan_gudang($kode_farm){
		$no_reg = implode("','",$this->noreg_list($kode_farm));
		$sql = <<<SQL
		SELECT coalesce(sum(jml_on_hand),0) sisa_pakan
			FROM movement
			WHERE jml_on_hand > 0 AND kode_farm = '$kode_farm' AND STATUS_STOK = 'NM' AND NO_PALLET >= (
			SELECT min(no_pallet) FROM MOVEMENT_D WHERE KETERANGAN2 in (
				'$no_reg'
			) AND KETERANGAN1 = 'PUT'
<<<<<<< HEAD
		)r
=======
		)
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526

SQL;
		return $this->db->query($sql);
	}
	public function stk_pakan_kandang($kode_farm){		
		$sql = <<<SQL
		select km.no_reg from kandang_movement km
		JOIN kandang_siklus ks ON ks.NO_REG = km.NO_REG AND ks.STATUS_SIKLUS = 'O' AND ks.KODE_FARM = '{$kode_farm}'
		where  km.JML_STOK > 0
		order by ks.no_reg
SQL;
				
		return $this->db->query($sql);
	}
	public function budget_status($kode_farm){
		$sql = <<<SQL
			select * from budget_glangsing
			where KODE_SIKLUS = (select kode_siklus from m_periode where STATUS_PERIODE = 'A' and KODE_FARM = '$kode_farm')
SQL;
		return $this->db->query($sql);
	}
	public function get_selisih_hari($kode_siklus,$kode_farm){
		$sql = <<<SQL
		select top 1 ks.TGL_DOC_IN,DATEDIFF(day,(SELECT COALESCE(TGL_UBAH,getdate()) FROM M_PERIODE WHERE KODE_SIKLUS = $kode_siklus),ks.TGL_DOC_IN) selisih_hari
		from KANDANG_SIKLUS ks
		left join M_PERIODE mp on ks.KODE_SIKLUS = mp.KODE_SIKLUS and ks.KODE_FARM = mp.KODE_FARM
		where ks.KODE_SIKLUS > $kode_siklus and ks.kode_farm = '$kode_farm' order by ks.TGL_DOC_IN asc

SQL;
		return $this->db->query($sql);
	}

	public function check_glangsing($kode_siklus){
		$result = 0;
		$sql = <<<SQL
		SELECT sum(jml_stok) jml_stok FROM glangsing_movement WHERE kode_barang IN (
			SELECT kode_barang FROM M_BARANG WHERE TIPE_BARANG = 'E'
		) AND jml_stok >= 0 AND kode_siklus = {$kode_siklus}
SQL;

		$tmp = $this->db->query($sql)->row_array();
		if(!empty($tmp)){
			$result = $tmp['jml_stok'] > 0 ? 1 : 0;
		}
		return $result;
	}
}
