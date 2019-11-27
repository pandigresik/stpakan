<?php

class M_lspm extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getPakanStandart($noreg)
    {
        $sql = <<<SQL
		--select distinct msb.kode_barang
			--		,mb.nama_barang
		--from kandang_siklus ks
		--join m_std_budidaya_d msb
			--on ks.KODE_STD_BUDIDAYA = msb.KODE_STD_BUDIDAYA
		--join m_barang mb
			--on mb.kode_barang = msb.kode_barang
		--where ks.no_reg = '{$noreg}'
		--and len(msb.KODE_BARANG) > 0
		--and msb.kode_barang is not null


		-- TERDAPAT PENYESUAIAN QUERY KARENA ADA PENAMBAHAN PAKAN BROILER 0-18

		select
			md.kode_barang,mb.nama_barang
		from MOVEMENT_D md
		join M_BARANG mb on mb.KODE_BARANG = md.KODE_BARANG
		where md.KETERANGAN2 = '{$noreg}'
		group by md.KODE_BARANG,mb.NAMA_BARANG
		ORDER BY MD.KODE_BARANG DESC
SQL;

        return $this->db->query($sql);
    }

    public function gudangTerima($noreg)
    {
        $sql = <<<SQL
 SELECT  k.tgl_terima
		,k.no_referensi
		,case when k.kode_barang='112A-10-11' then '1126-10-11'
              when k.kode_barang='112B-10-11' then '1126-10-11'
              when k.kode_barang='1127-10A12' then '1127-10-12'
              when k.kode_barang='1127-10B12' then '1127-10-12'
			  else k.kode_barang
         end  kode_barang
		,k.sak
		,k.kg
        from (
select t.tgl_terima
		,t.no_referensi
		,t.kode_barang
		,sum(t.sak) sak
		,sum(t.kg) kg
from(
	select cast(md.put_date as date) tgl_terima
			,case
				when substring(no_referensi,0,4) = 'RTN'
				then (select top 1 mmd.keterangan2
							from RETUR_KANDANG_D rkd
							JOIN RETUR_KANDANG rk ON rk.NO_RETUR = rkd.NO_RETUR 
							join MOVEMENT_D mmd
								on rkd.NO_REG = mmd.NO_REFERENSI
								AND rkd.NO_RETUR = mmd.KETERANGAN2
								and rkd.KODE_BARANG = mmd.KODE_BARANG	
								AND mmd.NO_PALLET = md.no_referensi
								and rk.keterangan1 = '{$noreg}'
						--	WHERE CAST(rk.TGL_APPROVE AS DATE) = cast(md.put_date as date)
				)		
			else no_referensi
			end no_referensi
			,kode_barang
			,sum(jml_putaway) sak
			,sum(berat_putaway) kg
		from movement_d md where KETERANGAN1 = 'PUT'  and KETERANGAN2 = '{$noreg}'
		group by kode_barang,cast(put_date as date),no_referensi
		)t
	group by t.tgl_terima
			,t.no_referensi
			,t.kode_barang
) k order by k.tgl_terima
SQL;
        //log_message('error', $sql);

        return $this->db->query($sql);
    }

    public function kandangTerima($noreg)
    {
        $sql = <<<SQL
 SELECT  k1.tgl_terima
		,k1.no_referensi
		,case when k1.kode_barang='112A-10-11' then '1126-10-11'
			when k1.kode_barang='112B-10-11' then '1126-10-11'
			when k1.kode_barang='1127-10A12' then '1127-10-12'
			when k1.kode_barang='1127-10B12' then '1127-10-12'
			else k1.kode_barang
         end  kode_barang
		,k1.sak
		,k1.kg
        from (

select  y.kode_barang
	,sum(y.sak) sak
	,sum(y.kg) kg
	,y.tgl_terima
	,y.no_referensi
from
(
	select md.kode_barang
			,md.jml_pick sak
			,md.berat_pick kg
			,cast(md.picked_date as date) tgl_terima
		--	,md.no_pallet
			,coalesce(retur.NO_REFERENSI,md.no_referensi) no_referensi
	from movement_d md
	left join (
		select md.NO_PALLET
			,(select top 1 mmd.keterangan2
								from RETUR_KANDANG_D rkd
								join MOVEMENT_D mmd
									on rkd.NO_REG = mmd.NO_REFERENSI
									and rkd.KODE_BARANG = mmd.KODE_BARANG
									and mmd.no_pallet = md.no_referensi
				)
				no_referensi
		from MOVEMENT_D md
		join MOVEMENT_D mmd
		on mmd.NO_PALLET = md.NO_REFERENSI
		where md.NO_REFERENSI like 'RTN%' and md.KETERANGAN2 = '{$noreg}'
		group by md.NO_PALLET
				,md.NO_REFERENSI
	)retur on retur.no_pallet = md.no_pallet
	where md.KETERANGAN1 = 'PICK' and KETERANGAN2 = '{$noreg}'
)y group by y.kode_barang
	,y.tgl_terima
	,y.no_referensi
) k1
SQL;

        return $this->db->query($sql);
    }
}
