<?php
class M_overview extends CI_Model{

	public function __construct(){
		parent::__construct();
	}

	public function plotting_pelaksana($kode_siklus,$status = 'RV'){
		$sql = <<<SQL
			SELECT count(distinct flok_bdy) jumlah,ks.no_reg,ks.flok_bdy,mp.nama_pegawai koordinator,mp1.nama_pegawai pengawas
			, stuff (
				(select distinct ','+ mp2.nama_pegawai
				from m_ploting_pelaksana
				join m_pegawai mp2 on mp2.kode_pegawai = operator
				where  no_reg = ks.no_reg
				for xml path (''))
				,1,1,'') operator
			FROM m_ploting_pelaksana mpp 
			join m_pegawai mp on mp.kode_pegawai = mpp.koordinator
			join m_pegawai mp1 on mp1.kode_pegawai = mpp.pengawas
			join kandang_siklus ks on ks.no_reg = mpp.no_reg
			where mpp.status = '{$status}' and mpp.kode_siklus = $kode_siklus
			group by ks.no_reg,ks.flok_bdy,mp.nama_pegawai,mp1.nama_pegawai
			order by ks.no_reg
	
SQL;
		return $this->db->query($sql);
	}

	public function permintaan_pakan($kode_siklus,$status = 'RV'){
		$sql = <<<SQL
		SELECT ld.no_lpb,ld.tgl_kirim,ld.tgl_keb_awal,ld.tgl_keb_akhir,mb.nama_barang,ks.no_reg
				,datediff(day,ks.TGL_DOC_IN,ld.TGL_KEB_AWAL) umur_awal
				,datediff(day,ks.TGL_DOC_IN,ld.TGL_KEB_AKHIR) umur_akhir
				,sum(rlb.JML_OPTIMASI) rekomendasi 
				,sum(rlb.JML_REKOMENDASI) kafarm
				,sum(rlb.JML_REVIEW) kadept
		FROM LPB l
		JOIN LPB_D ld ON ld.KODE_FARM = l.KODE_FARM AND ld.NO_LPB = l.NO_LPB 
		JOIN review_lpb_budidaya rlb ON rlb.no_lpb = l.no_lpb
		JOIN kandang_siklus ks ON ks.NO_REG = rlb.NO_REG
		JOIN m_barang mb ON mb.KODE_BARANG = rlb.KODE_BARANG
		WHERE l.STATUS_LPB = '{$status}' AND l.KODE_SIKLUS = {$kode_siklus}
		GROUP BY ld.NO_LPB,ld.TGL_KIRIM,ld.TGL_KEB_AWAL,ld.TGL_KEB_AKHIR,mb.NAMA_BARANG,ks.TGL_DOC_IN,ks.no_reg
		order by ld.tgl_keb_awal,ks.no_reg
SQL;
		return $this->db->query($sql);
	}

	public function plotting_do_pakan($kode_siklus,$status = 'R'){
		$sql = <<<SQL
		SELECT d.no_do,d.tgl_kirim,opv.no_polisi rit 
				,me.nama_ekspedisi, mb.nama_barang,dd.jml_muat
		FROM DO d
		JOIN DO_D dd ON d.NO_DO = dd.no_do  
		JOIN OP ON OP.no_op = d.no_op
		JOIN KANDANG_SIKLUS ks ON ks.no_reg = OP.KETERANGAN1 AND ks.kode_siklus = {$kode_siklus}  
		JOIN OP_VEHICLE opv ON opv.no_op = d.no_op AND opv.no_urut = d.no_urut
		JOIN M_BARANG mb ON mb.kode_barang = dd.kode_barang
		join M_EKSPEDISI me on me.KODE_EKSPEDISI = opv.KODE_EKSPEDISI
		where d.status_do = '{$status}'
SQL;
		
		return $this->db->query($sql);	
	}

	public function permintaan_glangsing($kode_farm,$status){
		$sql = <<<SQL
		SELECT pn.no_ppsk, pn.tgl_kebutuhan, mb.nama_barang,pn.jml_diminta, pn.jml_over_budget,pn.keterangan FROM log_ppsk_new ln 
		INNER JOIN (
			SELECT pn.no_ppsk,max(lpn.no_urut) no_urut FROM ppsk_new pn
			INNER JOIN m_periode mp ON mp.KODE_SIKLUS = pn.kode_siklus AND mp.STATUS_PERIODE = 'A'
			INNER JOIN log_ppsk_new lpn ON lpn.no_ppsk = pn.no_ppsk
			where substring(pn.no_ppsk,6,2) = '{$kode_farm}'
			GROUP BY pn.no_ppsk
		)terakhir ON ln.no_ppsk = terakhir.no_ppsk AND ln.no_urut = terakhir.no_urut
		JOIN ppsk_new pn ON pn.no_ppsk = terakhir.no_ppsk
		JOIN m_barang mb ON mb.KODE_BARANG  = pn.kode_budget
		WHERE ln.status = '{$status}' and substring(pn.no_ppsk,6,2) = '{$kode_farm}'
SQL;
		return $this->db->query($sql);	
	}

	public function pengajuan_harga($kode_farm,$status = 'R1'){
		$sql = <<<SQL
				select ph.no_pengajuan_harga,ph.kode_farm,ph.tgl_pengajuan,phd.harga_jual, mb.nama_barang, mb.kode_barang,phd.kode_barang,lh.no_urut
				from log_pengajuan_harga lh
				join (
					select no_pengajuan_harga, max(no_urut) no_urut
					from log_pengajuan_harga
					group by no_pengajuan_harga
				) ld on lh.no_pengajuan_harga = ld.no_pengajuan_harga and lh.no_urut = ld.no_urut
				join pengajuan_harga ph on lh.no_pengajuan_harga = ph.no_pengajuan_harga
				JOIN pengajuan_harga_d phd ON phd.no_pengajuan_harga = ph.no_pengajuan_harga 
				JOIN M_BARANG mb ON mb.KODE_BARANG = phd.kode_barang
				where lh.status = '{$status}' AND ph.kode_farm = '{$kode_farm}'

SQL;
		return $this->db->query($sql);	
	}

	public function harga_lama($kode_farm,$tgl_pengajuan){
		$sql = <<<SQL
		select phd.harga_jual,phd.kode_barang
				from log_pengajuan_harga lh
				join (
					select no_pengajuan_harga, max(no_urut) no_urut
					from log_pengajuan_harga
					group by no_pengajuan_harga
				) ld on lh.no_pengajuan_harga = ld.no_pengajuan_harga and lh.no_urut = ld.no_urut
				join pengajuan_harga ph on lh.no_pengajuan_harga = ph.no_pengajuan_harga AND ph.tgl_pengajuan < '{$tgl_pengajuan}'
				JOIN pengajuan_harga_d phd ON phd.no_pengajuan_harga = ph.no_pengajuan_harga 
				where lh.status = 'A' AND ph.kode_farm = '{$kode_farm}' 
				ORDER BY ph.tgl_pengajuan desc
SQL;
		return $this->db->query($sql);				
	}
	
}
