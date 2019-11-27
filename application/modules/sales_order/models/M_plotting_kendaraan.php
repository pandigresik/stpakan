<?php
class M_plotting_kendaraan extends MY_Model{
	public function getSalesOrder($nomor_so = NULL, $status_so = NULL, $kode_farm = NULL, $tglTransaksi){
		$sql_conf = <<<SQL
		select top 1 * from SYS_CONFIG_GENERAL
		where KODE_CONFIG = '_max_plot_kendaraan'
SQL;
		$conf = $this->db->query($sql_conf)->result_array();
		if (count($conf) > 0) {
			$max_time = $conf[0]['VALUE'];
		}else {
			$max_time = '09:00';
		}

		$whereFarm = '';
		$whereNomorSO = '';
		$whereStatusSO = '';
		if(!empty($kode_farm)){
			$whereFarm = " and so.kode_farm = '".$kode_farm."'";
		}
		if(!empty($nomor_so)){
			$whereNomorSO = " and so.no_so like '%".$nomor_so."%'";
		}
		if(!empty($status_so) && $status_so=='true'){
			$whereStatusSO = " and sj.no_sj is not null";
		}
		$sql = <<<SQL
		SELECT so.no_so, so.tgl_so, so.no_do, sj.no_sj, farm.KODE_FARM, farm.NAMA_FARM,
		plgn.KODE_PELANGGAN, plgn.NAMA_PELANGGAN, so.ALAMAT AS alamat_pelanggan, plgn.KOTA as kota_pelanggan,
		so.NO_TELP AS telp_pelanggan, sj.no_kendaraan, sj.nama_sopir, sj.no_telp_sopir,
		DATEADD(day, 1, convert(varchar(10), tgl_so,120)) as tgl_batas_so,
		CASE
		WHEN sj.no_sj is null or sj.no_sj='' THEN '0'
		ELSE '1'
		END as status_plotting,
		CASE
			--WHEN convert(varchar(10), tgl_so,120)>=DATEADD(day, -1, convert(varchar(10), getdate(),120))
				--AND DATEADD(day, 1, convert(varchar(10), tgl_so,120))<=DATEADD(day, 0, convert(varchar(10), getdate(),120))
				--AND FORMAT(GETDATE(),'HH:mm') <= '$max_time' THEN '0'
			WHEN format(getdate(),'yyyy-MM-dd HH:mm') between convert(varchar(10), tgl_so,120)
				and convert(varchar(10), DATEADD(day, +1, tgl_so),120) + ' $max_time'
			THEN '0'
			ELSE '1'
		END as limited_timeline_plotting,
		'$max_time' as max_time
		FROM sales_order so
		LEFT OUTER JOIN log_sales_order log ON log.no_so = so.no_so AND status='A'
		LEFT OUTER JOIN surat_jalan sj on sj.no_so = so.no_so
		INNER JOIN M_FARM farm ON farm.KODE_FARM = so.kode_farm
		INNER JOIN M_PELANGGAN plgn ON plgn.KODE_PELANGGAN = so.kode_pelanggan
		WHERE so.status_order='A' {$whereFarm} {$whereNomorSO} {$whereStatusSO}
		order by status_plotting, log.tgl_buat desc
SQL;
	// echo $sql;
		return $this->db->query($sql)->result_array();
	}

	public function get_sopir_browse($nama_sopir = null){
		$whereNamaSopir = '';
		if(!empty($nama_sopir)){
			$whereNamaSopir = " where nama_sopir like '%".$nama_sopir."%'";
		}
		$sql = <<<SQL
		SELECT sj.nama_sopir, sj.no_telp_sopir
		FROM surat_jalan sj
		{$whereNamaSopir}
		GROUP BY sj.nama_sopir, sj.no_telp_sopir
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function getIDSuratJalan($nomor_so){
		$sql = <<<SQL
		select LEFT('{$nomor_so}',CHARINDEX('-G','{$nomor_so}')+1)+right(replicate('0',5) + CAST((RIGHT(isnull(max(no_sj),0),5)+1) AS varchar(5)),5) as no_sj
		FROM surat_jalan
		WHERE LEFT(no_so,CHARINDEX('-G','{$nomor_so}')+1) = LEFT('{$nomor_so}',CHARINDEX('-G','{$nomor_so}')+1)
		AND substring(no_sj,0,7) = substring(no_so,0,7)

SQL;
		return $this->db->query($sql)->result_array();
	}
}
