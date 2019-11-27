<?php
class M_pembelian_pakan extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function list_order($startDate, $endDate,$cari,$rekap){
		$do_akan_panen = 25;
		if($rekap){
			$join = 'inner';
		}
		else{
			$join = 'left';
		}
		$param = array(
			'no_op' => 'op.no_op',
			'no_pp' => 'op.no_lpb',
			'nama_farm'  => 'mf.nama_farm',
			'no_do_ekspedisi' => 'me.nama_ekspedisi'
		);
		$where_cari = '';
		if(!empty($cari)){
			foreach($cari as $n => $v){
				$where_cari .= ' and '.$param[$n].' like \'%'.$v.'%\'';
			}
		}

		$sql = <<<QUERY
	SELECT *,
		CASE WHEN datediff(day,getdate(),h.tgl_kirim) >= h.timeline_plotting THEN 1
		ELSE 0 END status_plotting			
	FROM (	
		select op.NO_OP no_op
			,op.NO_LPB no_pp
			, mf.NAMA_FARM farm
			, mf.KODE_FARM kode_farm
			, mf.GRUP_FARM grup_farm
			, me.NAMA_EKSPEDISI ekspedisi
			, ks.no_reg
			, me.kode_ekspedisi kode_ekspedisi
			, op_d.TGL_KIRIM tgl_kirim
			, (SELECT sum(JML_ORDER) FROM OP_D WHERE NO_OP = OP.NO_OP) jml_order
			, stuff (
				(select distinct ','+ d.NO_DO
				from op_vehicle opv
				inner join do d
				on d.NO_OP = opv.NO_OP and d.NO_URUT = opv.NO_URUT and d.status_do != 'D'
				where opv.NO_OP = op.NO_OP and opv.KODE_EKSPEDISI = me.KODE_EKSPEDISI
				for xml path (''))
				,1,1,'') no_do
			,CASE 
				WHEN datediff(day,ks.TGL_DOC_IN,(select top 1 TGL_KEB_AWAL from lpb_d where no_lpb = op.no_lpb)) < {$do_akan_panen} THEN (SELECT VALUE FROM SYS_CONFIG_GENERAL WHERE KODE_CONFIG = '_max_plot_do' and CONTEXT = 'Plotting_do' AND KODE_FARM = mf.KODE_FARM AND STATUS = 1)
				ELSE (SELECT VALUE FROM SYS_CONFIG_GENERAL WHERE KODE_CONFIG = '_max_plot_do_panen' and CONTEXT = 'Plotting_do' AND KODE_FARM = mf.KODE_FARM AND STATUS = 1)
			END timeline_plotting	
		from op_d
		inner join op
			on op.NO_OP = op_d.NO_OP
		inner join kandang_siklus ks 
			on ks.no_reg = op.keterangan1	
		inner join M_FARM mf
			on mf.KODE_FARM = op.KODE_FARM
		{$join} join do
			on do.NO_OP = op.NO_OP and do.TGL_KIRIM = op_d.TGL_KIRIM and do.status_do != 'D'
		{$join} join OP_VEHICLE opv
			on opv.NO_OP = do.NO_OP and opv.NO_URUT = do.NO_URUT
		{$join} join M_EKSPEDISI me
			on me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
		where op_d.tgl_kirim between '$startDate' and '$endDate'
		{$where_cari}
		group by op.NO_OP,op.NO_LPB,mf.NAMA_FARM,op_d.TGL_KIRIM
				, me.NAMA_EKSPEDISI
				, me.KODE_EKSPEDISI
				, mf.KODE_FARM
				, mf.GRUP_FARM
				, ks.TGL_DOC_IN
				, ks.no_reg
	)h			
QUERY;
		
		return $this->db->query($sql);
	}

	public function list_order_approve($cari){
		$tindaklanjut = $cari['tindaklanjut'];
		$status_do = $cari['status_do'];
		$status_do_str = "('".implode("','",$status_do)."')";
		$sql = <<<QUERY
		select distinct op.NO_OP no_op
			,op.NO_LPB no_pp
			, mf.NAMA_FARM farm
			, mf.KODE_FARM kode_farm
			, me.NAMA_EKSPEDISI ekspedisi
			, me.kode_ekspedisi kode_ekspedisi
			, op_d.TGL_KIRIM tgl_kirim
			, opv.jml_kirim
			, opv.no_polisi as rit
		from op_d
		inner join op
			on op.NO_OP = op_d.NO_OP
		inner join M_FARM mf
			on mf.KODE_FARM = op.KODE_FARM
		inner join do
			on do.NO_OP = op.NO_OP and do.TGL_KIRIM = op_d.TGL_KIRIM and do.status_do in {$status_do_str}
			inner join OP_VEHICLE opv
			on opv.NO_OP = do.NO_OP and opv.NO_URUT = do.NO_URUT
			inner join M_EKSPEDISI me
			on me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
		
		group by op.NO_OP,op.NO_LPB,mf.NAMA_FARM,op_d.TGL_KIRIM
				, me.NAMA_EKSPEDISI
				, me.KODE_EKSPEDISI
				, mf.KODE_FARM
				, opv.no_polisi
				, opv.jml_kirim
		order by op_d.TGL_KIRIM
				, mf.KODE_FARM
				, opv.no_polisi
				

QUERY;
		
		return $this->db->query($sql);
	}

	public function detail_pp($no_op,$tgl_kirim){
		$sql = <<<QUERY
		select op_d.KODE_BARANG kode_pakan
			,mb.NAMA_BARANG nama_pakan
			,cast(op_d.JML_ORDER as int) jumlah
		from op_d
		inner join M_BARANG mb
		on mb.KODE_BARANG = op_d.KODE_BARANG
		where no_op = '{$no_op}'
		and TGL_KIRIM = '{$tgl_kirim}'
QUERY;

		return $this->db->query($sql);
	}

	public function detail_pp_tglkirim($kode_farm,$tgl_kirim){
		$do_akan_panen = 25;
		$sql = <<<QUERY
		SELECT *,
			CASE WHEN datediff(day,getdate(),h.tgl_kirim) >= h.timeline_plotting THEN 1
			ELSE 0 END status_plotting			
		FROM (
			select op_d.KODE_BARANG kode_pakan
				,mb.NAMA_BARANG nama_pakan
				,op_d.tgl_kirim
				,cast(op_d.JML_ORDER as int) jumlah
				,OP.no_op
				,CASE 
					WHEN datediff(day,ks.TGL_DOC_IN,(select top 1 TGL_KEB_AWAL from lpb_d where no_lpb = op.no_lpb)) < {$do_akan_panen} THEN (SELECT VALUE FROM SYS_CONFIG_GENERAL WHERE KODE_CONFIG = '_max_plot_do' and CONTEXT = 'Plotting_do' AND KODE_FARM = op.KODE_FARM AND STATUS = 1)
					ELSE (SELECT VALUE FROM SYS_CONFIG_GENERAL WHERE KODE_CONFIG = '_max_plot_do_panen' and CONTEXT = 'Plotting_do' AND KODE_FARM = op.KODE_FARM AND STATUS = 1)
				END timeline_plotting
			from op_d
			INNER JOIN OP ON OP.NO_OP = op_d.NO_OP AND OP.KODE_FARM = '{$kode_farm}'
			inner join kandang_siklus ks on ks.no_reg = OP.keterangan1
			inner join M_BARANG mb on mb.KODE_BARANG = op_d.KODE_BARANG
			where op_d.TGL_KIRIM = '{$tgl_kirim}'
		)h
QUERY;
		
		return $this->db->query($sql);
	}

	public function list_ekspedisi($kode_farm = NULL, $_list_ekspedisi = array()){
		$where_ekspedisi = !empty($kode_farm) ? ' and mv.kode_farm = \''.$kode_farm.'\'' : '';
		$whereListEkspedisi = !empty($_list_ekspedisi) ? ' and me.kode_ekspedisi in (\''.implode("','",$_list_ekspedisi).'\')' : '';
		$sql = <<<QUERY
		select  me.KODE_EKSPEDISI kode
				,me.NAMA_EKSPEDISI nama
				,max(mv.max_rit) max
				,min(mv.MIN_RIT) min
		from M_EKSPEDISI me
		inner join M_EKPEDISI_VEHICLE_NEW mv
			on me.KODE_EKSPEDISI = mv.KODE_EKSPEDISI {$where_ekspedisi}
		where GRUP_EKSPEDISI = 'E' {$whereListEkspedisi}
		group by me.KODE_EKSPEDISI,me.NAMA_EKSPEDISI
QUERY;
		return $this->db->query($sql);
	}

	public function detail_do($no_do){
		$sql = <<<SQL
		select d.NO_OP no_op
			,d.NO_DO no_do
			,mf.NAMA_FARM nama_farm
			,opv.TGL_KIRIM tgl_kirim
			,opv.KODE_BARANG kode_barang
			,mb.NAMA_BARANG nama_barang
			,dbo.bentuk_convertion(mb.bentuk_barang) bentuk_barang
			,opv.JML_KIRIM jml_kirim
			,opv.JML_KIRIM * 50 berat
			,me.NAMA_EKSPEDISI nama_ekspedisi
			,d.status_do
		from  do d
		inner join op
			on d.no_op = op.no_op
		inner join op_vehicle opv
			on d.NO_OP = opv.NO_OP and d.NO_URUT = opv.NO_URUT
		inner join M_EKSPEDISI me
			on me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
		inner join m_barang mb
			on mb.KODE_BARANG = opv.KODE_BARANG
		inner join m_farm mf
			on mf.KODE_FARM = d.KODE_FARM
		where d.no_do = '{$no_do}'
			and d.status_do != 'D'
			and op.TGL_KADALUARSA_OP >= current_timestamp

SQL;

		return $this->db->query($sql);
	}

	public function cetak_do($no_do){
		$sql = <<<SQL
		select d.NO_DO no_do
			,d.NO_OP no_op
			,d.TGL_KIRIM tgl_kirim
			,opv.JML_KIRIM jml_kirim
			,opv.JML_KIRIM * 50 berat
			,opv.KODE_BARANG kode_barang
			,mb.NAMA_BARANG nama_barang
			,me.NAMA_EKSPEDISI nama_ekspedisi
			,me.ALAMAT+ ' '+me.KOTA alamat_ekspedisi
			,mf.NAMA_FARM nama_farm
			,mf.ALAMAT_FARM+ ' '+mf.KOTA alamat_farm
		from do d
		inner join OP_VEHICLE opv
			on opv.NO_OP = d.NO_OP and opv.NO_URUT = d.NO_URUT 
		inner join M_EKSPEDISI me
			on me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
		inner join m_barang mb
			on mb.KODE_BARANG = opv.KODE_BARANG
		inner join M_FARM mf
			on mf.KODE_FARM = d.KODE_FARM
		where d.NO_DO = '{$no_do}'			
			and d.status_do != 'D'
SQL;
		return $this->db->query($sql);

	}

	public function list_order_penjualan_marketing($startDate, $endDate,$cari){
		$param = array(
				'no_op_logistik' => 'op.no_op_logistik',
				'no_op' => 'op.no_op',
				'no_pp' => 'op.no_lpb',
				'tgl_op' => 'op.tgl_op',
		);
		$where_cari = '';
		if(!empty($cari)){
			foreach($cari as $n => $v){
				$where_cari .= ' and '.$param[$n].' like \'%'.$v.'%\'';
			}
		}
		$sql = <<<SQL
		select distinct op.NO_LPB no_pp
			,op.TGL_OP tgl_op
			,op.TGL_KADALUARSA_OP tgl_kadaluarsa_op
			,op.NO_OP_LOGISTIK no_op_logistik
			,op.no_op no_op
		from op
		inner join op_d
			on op.NO_OP = op_d.NO_OP
		where op.tgl_op between '$startDate' and '$endDate'
		{$where_cari}
SQL;
	return $this->db->query($sql);
	}

	public function cetak_order_penjualan_marketing($no_op_logistik,$no_op){
		$sql = <<<SQL
		select op.TGL_OP tgl_op
			,op.TGL_KADALUARSA_OP tgl_kadaluarsa_op
			,op.NO_OP_LOGISTIK no_op_logistik
			,op.no_op no_op
			,op_d.JML_ORDER jml_kirim
			,op_d.HARGA_SATUAN harga_satuan
			,op_d.KODE_BARANG kode_barang
			,mf.NAMA_FARM nama_farm
			,mb.NAMA_BARANG nama_barang
		from op
		inner join op_d
			on op.NO_OP = op_d.NO_OP
		inner join m_farm mf
			on mf.KODE_FARM = op.KODE_FARM
		inner join M_BARANG mb
			on mb.KODE_BARANG = op_d.KODE_BARANG
		where op.no_op_logistik = '{$no_op_logistik}'
				and op.no_op = '{$no_op}'
SQL;

		return $this->db->query($sql);
	}

	public function cetak_order_pembelian_logistik($no_op_logistik,$no_lpb){
		$sql = <<<SQL
		select op.NO_OP_LOGISTIK no_op_logistik
			,op.no_op no_op
			,op.no_lpb no_pp
			,op_d.JML_ORDER jml_kirim
			,op_d.HARGA_SATUAN harga_satuan
			,op_d.HARGA_SATUAN harga_total
			,mb.NAMA_BARANG nama_barang
			,upper(mp.NAMA_PELANGGAN) penerima
			,upper(mp.ALAMAT +' ' + mp.KOTA) alamat_penerima
		from op
		inner join op_d
			on op.NO_OP = op_d.NO_OP
		inner join m_farm mf
			on mf.KODE_FARM = op.KODE_FARM
		inner join m_pelanggan mp
			on mp.KODE_PELANGGAN = mf.KODE_PELANGGAN
		inner join M_BARANG mb
			on mb.KODE_BARANG = op_d.KODE_BARANG
		where op.no_op_logistik = '{$no_op_logistik}'
				and op.no_lpb = '{$no_lpb}'
SQL;

		return $this->db->query($sql);
	}

	public function list_order_pembelian_logistik($startDate, $endDate,$cari){
		$param = array(
				'no_op_logistik' => 'op.no_op_logistik',
				'no_pp' => 'op.no_lpb',
				'tgl_op' => 'op.tgl_op',
				'nama_farm'  => 'mf.nama_farm'
		);
		$where_cari = '';
		if(!empty($cari)){
			foreach($cari as $n => $v){
				$where_cari .= ' and '.$param[$n].' like \'%'.$v.'%\'';
			}
		}
		$sql = <<<SQL
		select distinct op.NO_LPB no_pp
			, op.NO_OP_LOGISTIK no_op
			, mf.nama_farm
			, op.tgl_op
		--	,op_d.JML_ORDER jml_order
		--	,op_d.KODE_BARANG kode_barang
		--	,op_d.HARGA_SATUAN harga_satuan
		--	,op_d.HARGA_TOTAL harga_total
		from op
		inner join op_d
			on op.NO_OP = op_d.NO_OP
		inner join m_farm mf
			on mf.kode_farm = op.kode_farm
		where op.tgl_op between '$startDate' and '$endDate'
		{$where_cari}
SQL;
		return $this->db->query($sql);
	}
}
