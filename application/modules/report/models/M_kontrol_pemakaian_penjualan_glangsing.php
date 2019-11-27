<?php class M_kontrol_pemakaian_penjualan_glangsing extends CI_Model{

	public function __construct(){
		parent::__construct();						
    }
    
    public function listSiklus($farm){
        $sql = <<<SQL
        SELECT count(ks.kode_kandang) jml_kandang,ks.kode_siklus,mp.periode_siklus ,substring(ks.KODE_STD_BUDIDAYA,0,5) strain 
        FROM KANDANG_SIKLUS ks 
        JOIN M_PERIODE mp ON mp.KODE_SIKLUS = ks.KODE_SIKLUS 
        WHERE STATUS_SIKLUS != 'P' and ks.KODE_FARM = '{$farm}'
        GROUP BY ks.kode_siklus, mp.PERIODE_SIKLUS,substring(ks.KODE_STD_BUDIDAYA,0,5)    
        order by ks.kode_siklus desc
SQL;
        return $this->db->query($sql);
    }
        
    public function listBudget($siklus){
        $sql = <<<SQL
        SELECT mb.nama_barang,bg.kode_budget,bg.jml_order FROM BUDGET_GLANGSING_d bg
        JOIN m_barang mb ON mb.KODE_BARANG = bg.KODE_BUDGET
        WHERE KODE_SIKLUS = {$siklus} AND NO_URUT = (
            SELECT max(no_urut) FROM BUDGET_GLANGSING_d WHERE KODE_SIKLUS = {$siklus}
        )
        order by bg.KODE_BUDGET
SQL;
        return $this->db->query($sql);
    }
    
    public function resumePpsk($siklus,$kode_budget){
        $sql = <<<SQL
        SELECT sum(pd.jml_diminta) minta, sum(coalesce(pd.jml_kembali,0)) kembali, sum(coalesce(pd.jml_diminta,0)) - sum(coalesce(pd.jml_kembali,0)) pakai   FROM ppsk_d pd
        JOIN ppsk_new pn ON pn.no_ppsk = pd.no_ppsk AND pn.kode_siklus = {$siklus} AND pn.kode_budget = '{$kode_budget}'
        JOIN LOG_PPSK_NEW lp ON lp.NO_PPSK = pn.no_ppsk AND lp.STATUS = 'A'
        WHERE pd.tgl_terima IS NOT null
SQL;
        
        return $this->db->query($sql);
    }

    public function getAwalSiklus($siklus){
        $sql = <<<SQL
        SELECT min(cst.stamp) awal_siklus FROM cycle_state_transition cst
        JOIN kandang_siklus ks ON cst.noreg = ks.NO_REG AND ks.KODE_SIKLUS = {$siklus}
        WHERE cst.state = 'RL'
SQL;
        return $this->db->query($sql);
    }

    public function getSO($awal,$akhir,$kodeBudget,$farm){
        $sql = <<<SQL
        SELECT so.status_order, sum(sod.jumlah) jumlah, sum(sjd.jumlah) jumlah_sj
        FROM sales_order so
        JOIN sales_order_d sod ON sod.no_so = so.no_so AND sod.kode_barang = '{$kodeBudget}'
        LEFT JOIN surat_jalan sj ON sj.no_so = so.no_so and sj.tgl_realisasi is not null
        LEFT JOIN surat_jalan_d sjd ON sjd.no_sj = sj.no_sj AND sjd.kode_barang = sod.kode_barang
        WHERE tgl_so >= '{$awal}' AND tgl_so < '{$akhir}' and so.kode_farm = '{$farm}'
        GROUP BY status_order
SQL;
        
        return $this->db->query($sql);
    }
    
    
    public function detailPpsk($siklus,$kodeBudget){
        $sql = <<<SQL
    SELECT pn.no_ppsk,pn.tgl_permintaan,pn.tgl_kebutuhan
        , pn.jml_diminta
        , convert(date,min(pd.tgl_terima)) tgl_terima
        , sum(pd.jml_diminta) terima
        , convert(date,min(pd.tgl_kembali)) tgl_kembali        
    --    , min(pd.tgl_terima) tgl_terima
        , sum(pd.jml_kembali) jml_kembali
        , sum(CASE WHEN pd.tgl_terima IS NULL THEN 0 ELSE pd.jml_diminta END) jml_diambil
        , min(pd.tgl_ack) ack_kembali
        , mp.NAMA_PEGAWAI user_ack
        , mp_review.NAMA_PEGAWAI user_review
        , lpn_review.tgl_buat tgl_review
        , mp_approve.NAMA_PEGAWAI user_approve
        , lpn_approve.tgl_buat tgl_approve
    FROM ppsk_new pn
    JOIN ppsk_d pd ON pn.no_ppsk = pd.no_ppsk AND brt_timbang IS NOT null
    LEFT JOIN log_ppsk_new lpn_review ON lpn_review.no_ppsk = pn.no_ppsk AND lpn_review.status = 'R'
    LEFT JOIN log_ppsk_new lpn_approve ON lpn_approve.no_ppsk = pn.no_ppsk AND lpn_approve.status = 'A'
    LEFT JOIN m_pegawai mp_review ON mp_review.KODE_PEGAWAI = lpn_review.user_buat
    LEFT JOIN m_pegawai mp_approve ON mp_approve.KODE_PEGAWAI = lpn_review.user_buat
    LEFT JOIN m_pegawai mp ON mp.KODE_PEGAWAI = pd.user_ack
    WHERE pn.kode_siklus = {$siklus} AND pn.kode_budget = '{$kodeBudget}'
    GROUP BY pn.no_ppsk
                ,pn.tgl_permintaan
                ,pn.tgl_kebutuhan
                ,pn.jml_diminta
                ,mp.NAMA_PEGAWAI
                , mp.NAMA_PEGAWAI
                , mp_review.NAMA_PEGAWAI
                , lpn_review.tgl_buat
                , mp_approve.NAMA_PEGAWAI
                , lpn_approve.tgl_buat
SQL;
        
        return $this->db->query($sql);
    }

    public function detailSO($awal,$akhir,$kodeBudget,$farm){
        $sql = <<<SQL
            SELECT so.no_so
                , so.tgl_so 		
                , mp.nama_pelanggan
                , so.jumlah_total
                , so.harga_total / so.jumlah_total harga
                , so.harga_total
                , mp.term_pembayaran  	
                , p.kode_pembayaran
                , p.nominal_bayar
                , p.lampiran	 		
                , mp_kasir.nama_pegawai verifikasi
                , lso_kasir.tgl_buat tgl_verifikasi
                , mp_ack.nama_pegawai user_ack
                , lso_ack.tgl_buat tgl_ack
                , sj.no_do
                , sj.no_sj
                , sj.tgl_verifikasi tgl_verifikasi_do
                , mp_verifikasi.nama_pegawai user_verifikasi_do 
                , sj.tgl_realisasi
                , sjd.jumlah
                , sj.tgl_buat
                , sj.no_kendaraan
                , sj.nama_sopir
                , mp_realisasi.nama_pegawai user_realisasi 
                , mp_verifikasi_sj.nama_pegawai user_verifikasi_sj 
                , sj.tgl_verifikasi_security
            FROM sales_order so
            JOIN sales_order_d sod ON sod.no_so = so.no_so AND sod.kode_barang = '{$kodeBudget}'
            JOIN m_pelanggan mp ON mp.KODE_PELANGGAN = so.kode_pelanggan
            LEFT JOIN pembayaran p ON p.no_so = so.no_so
            LEFT JOIN log_sales_order lso_kasir ON lso_kasir.no_so = so.no_so AND lso_kasir.status = 'U'
            LEFT JOIN log_sales_order lso_ack ON lso_ack.no_so = so.no_so AND lso_ack.status = 'A'
            LEFT JOIN surat_jalan sj ON sj.no_so = so.no_so and sj.tgl_realisasi is not null
            LEFT JOIN surat_jalan_d sjd ON sjd.no_sj = sj.no_sj AND sjd.kode_barang = sod.kode_barang
            LEFT JOIN m_pegawai mp_kasir ON mp_kasir.KODE_PEGAWAI = lso_kasir.user_buat
            LEFT JOIN m_pegawai mp_ack ON mp_ack.KODE_PEGAWAI = lso_ack.user_buat
            LEFT JOIN m_pegawai mp_verifikasi ON mp_verifikasi.KODE_PEGAWAI = sj.user_verifikasi
            LEFT JOIN m_pegawai mp_realisasi ON mp_realisasi.KODE_PEGAWAI = sj.user_realisasi
            LEFT JOIN m_pegawai mp_verifikasi_sj ON mp_verifikasi_sj.KODE_PEGAWAI = sj.user_verifikasi_security
            WHERE tgl_so >= '{$awal}' AND tgl_so < '{$akhir}' AND so.kode_farm = '{$farm}'
       
SQL;
        
        return $this->db->query($sql);
    }
    
    

    public function detailPengembalian($ppsk){
        $sql = <<<SQL
            SELECT * from ppsk_d where no_ppsk = '{$ppsk}'
            and brt_timbang is not null
       
SQL;
        
        return $this->db->query($sql);
    }    
    
}

