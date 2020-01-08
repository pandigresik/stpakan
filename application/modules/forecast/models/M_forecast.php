<?php

class M_forecast extends MY_Model
{
    protected $_table;
    private $_user;
    protected $primary_key;

    public function __construct()
    {
        parent::__construct();
        $this->_table = 'forecast';
        $this->primary_key = 'id';
        $this->_user = $this->session->userdata('kode_user');
    }

    public function get_forecast_by_state($status)
    {
        $sql = <<<SQL
		select kode_farm, flok_bdy, KODE_SIKLUS, stamp, cs.[user], [state]
		from cycle_state_transition cs
		join KANDANG_SIKLUS ks on cs.noreg = ks.no_reg
		where stamp in(select max(stamp)
			from cycle_state_transition cs
			join KANDANG_SIKLUS ks on cs.noreg = ks.no_reg
			--where noreg in('BW/2017-4/01','BW/2017-4/02','BW/2017-4/03','BW/2017-4/04')
			group by flok_bdy, KODE_SIKLUS
			) and [state] = '{$status}'
		group by kode_farm, flok_bdy, KODE_SIKLUS, stamp, [user], [state]
		order by stamp desc

SQL;

        return $this->db->query($sql);
    }

    public function get_count_forecast_by_state($status)
    {
        $sql = <<<SQL
		select kode_farm, count(*)  as total
		from (
				select kode_farm, count(*) as total
				from cycle_state_transition cs
				join KANDANG_SIKLUS ks on cs.noreg = ks.no_reg
				where stamp in(select max(stamp)
					from cycle_state_transition cs
					join KANDANG_SIKLUS ks on cs.noreg = ks.no_reg
					--where noreg in('BW/2017-4/01','BW/2017-4/02','BW/2017-4/03','BW/2017-4/04')
					group by flok_bdy, KODE_SIKLUS
					) and [state] = '{$status}'
				group by kode_farm, flok_bdy, KODE_SIKLUS, stamp, [user], [state]
		) a
		group by kode_farm
		-- order by stamp desc

SQL;
        //cetak_r($sql);
        return $this->db->query($sql);
    }

    public function list_farm($grup_farm = null, $id = null, $active = true)
    {
        $where = '';
        if (!empty($grup_farm)) {
            $where .= 'where mf.grup_farm = \''.$grup_farm.'\'';
            $where .= (!empty($id)) ? ' and mf.kode_farm = \''.$id.'\'' : '';
        }
        $aktif_saja = $active ? 'and mp.status_periode = \'A\' and kode_siklus in (select distinct kode_siklus from kandang_siklus where status_siklus = \'O\' )' : '';

        $sql = <<<SQL
		select mf.kode_farm
			,mp.kode_siklus
			,mf.nama_farm
			,mp.kode_strain
			,mp.periode_siklus
			,mf.jml_flok
		from m_farm mf
		inner join m_periode mp
			on mp.kode_farm = mf.kode_farm {$aktif_saja}
		{$where}
		order by mp.KODE_SIKLUS
SQL;

        return $this->db->query($sql);
    }

    public function list_kandang_tutup_siklus($idfarm = null)
    {
        $sql = <<<SQL
		select mk.KODE_KANDANG kode
			,case mk.TIPE_LANTAI
				when 'S' then 'Slate'
				when 'L' then 'Litter'
			end as tipe_lantai
			,case mk.TIPE_KANDANG
				when 'C' then 'Closed'
				when 'O' then 'Open'
			end as tipe_kandang
	--		,mk.NAMA_KANDANG nama
			,replace(replace(convert(varchar,convert(Money, mk.MAX_POPULASI),1),'.00',''),',','.') kapasitas
	--		,replace(replace(convert(varchar,convert(Money, (mk.jml_jantan + mk.jml_betina) ),1),'.00',''),',','.') kapasitas
			,replace(replace(convert(varchar,convert(Money, coalesce(mk.jml_jantan,0)),1),'.00',''),',','.') as jantan
			,replace(replace(convert(varchar,convert(Money, coalesce(mk.jml_betina,0)),1),'.00',''),',','.') as betina
		from m_kandang mk
		left join kandang_siklus ks
			on ks.KODE_KANDANG = mk.KODE_KANDANG
				and ks.kode_farm = mk.kode_farm and ks.KODE_SIKLUS = (select top 1 KODE_SIKLUS from M_PERIODE where KODE_FARM = '{$idfarm}' and STATUS_PERIODE = 'A'  order by kode_siklus)
		--	and ks.STATUS_SIKLUS = 'O'
		where mk.KODE_FARM = '{$idfarm}' and  ks.STATUS_SIKLUS is null

SQL;

        return $this->db->query($sql);
    }

    public function list_kandang_open($idfarm, $status_minimum = null)
    {
        $minimum_approve = '';
        if (!empty($status_minimum)) {
            switch ($status_minimum) {
                case 'R':
                    $minimum_approve = ' and ks.tgl_rilis is not null';
                    break;
                case 'A':
                    $minimum_approve = ' and ks.tgl_approve1 is not null';
                    break;
            }
        }
        $sql = <<<SQL
		select ks.TGL_DOC_IN tgl_chickin
			,ks.kode_farm
			,ks.no_reg
			,case ks.TIPE_LANTAI
					when 'S' then 'Slate'
					when 'L' then 'Litter'
				end as tipe_lantai
			,case ks.TIPE_KANDANG
				when 'C' then 'Closed'
				when 'O' then 'Open'
			end as tipe_kandang
			,ks.KODE_KANDANG kode_kandang
			,replace(replace(convert(varchar,convert(Money, ks.JML_JANTAN),1),'.00',''),',','.') jantan
			,replace(replace(convert(varchar,convert(Money, ks.JML_BETINA),1),'.00',''),',','.') betina
			,replace(replace(convert(varchar,convert(Money, mk.MAX_POPULASI),1),'.00',''),',','.') kapasitas
			,case
				when ks.TGL_APPROVE2 is not null then 'Acc2'
				when ks.TGL_APPROVE1 is not null then 'Acc1'
				when ks.TGL_RILIS is not null then 'Baru'
				else 'Draft'
			end status_approve
		from KANDANG_SIKLUS ks
		inner join m_kandang mk
		on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM
		where ks.KODE_FARM = '{$idfarm}' and ks.status_siklus = 'O'
		{$minimum_approve}
		and ks.KODE_SIKLUS = (select top 1 KODE_SIKLUS from M_PERIODE where KODE_FARM = '{$idfarm}' and STATUS_PERIODE = 'A'  order by kode_siklus)
		order by ks.TGL_DOC_IN
SQL;

        return $this->db->query($sql);
    }

    public function list_kandang_open_bdy($idfarm = null, $status_minimum = null)
    {
        $where_ks_farm = !empty($idfarm) ? ' and ks.kode_farm = \''.$idfarm.'\'' : '';

        $minimum_approve = '';
        if (!empty($status_minimum)) {
            switch ($status_minimum) {
                case 'R':
                    $minimum_approve = ' and ks.tgl_rilis is not null';
                    break;
                case 'A':
                    $minimum_approve = ' and ks.tgl_approve1 is not null';
                    break;
            }
        }
        $sql = <<<SQL
		select mf.nama_farm
			,mf.kode_farm
			,ks.TGL_DOC_IN tgl_chickin
			,ks.no_reg
			,ks.kode_std_budidaya
			,case ks.TIPE_LANTAI
					when 'S' then 'Slate'
					when 'L' then 'Litter'
				end as tipe_lantai
			,case ks.TIPE_KANDANG
				when 'C' then 'Closed'
				when 'O' then 'Open'
			end as tipe_kandang
			,ks.KODE_KANDANG kode_kandang
			,replace(replace(convert(varchar,convert(Money, ks.JML_POPULASI),1),'.00',''),',','.') populasi
			,replace(replace(convert(varchar,convert(Money, mk.MAX_POPULASI),1),'.00',''),',','.') kapasitas
			,case
				when ks.TGL_APPROVE2 is not null then 'Acc2'
				when ks.TGL_APPROVE1 is not null then 'Acc1'
				when ks.TGL_RILIS is not null then 'Baru'
				else 'Draft'
			end status_approve
		from KANDANG_SIKLUS ks
		inner join m_kandang mk
			on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM -- and mk.no_flok = ks.flok_bdy
		inner join m_farm mf
			on mf.kode_farm = mk.kode_farm
		inner join m_periode mp
			on mp.kode_siklus = ks.kode_siklus and mp.status_periode = 'A'
		where ks.kode_std_budidaya is not null and ks.flok_bdy is not null  and ks.status_siklus = 'O'
		{$where_ks_farm}
		{$minimum_approve}
	--	and ks.KODE_SIKLUS in (select KODE_SIKLUS from M_PERIODE where KODE_FARM = '{$idfarm}'and STATUS_PERIODE = 'A')
		order by mf.nama_farm,ks.TGL_DOC_IN
SQL;

        return $this->db->query($sql);
    }

    public function list_kandang_pending($kodefarm = null)
    {
        $whereFarm = '';
        if (!empty($kodefarm)) {
            $whereFarm = 'mf.kode_farm = \''.$kodefarm.'\'';
        }
        $sql = <<<SQL
		select mf.nama_farm
			,mf.kode_farm
			,ks.TGL_DOC_IN tgl_chickin
			,ks.no_reg
			,ks.kode_std_budidaya
			,ks.kode_siklus
			,case ks.TIPE_LANTAI
					when 'S' then 'Slate'
					when 'L' then 'Litter'
				end as tipe_lantai
			,case ks.TIPE_KANDANG
				when 'C' then 'Closed'
				when 'O' then 'Open'
			end as tipe_kandang
			,ks.KODE_KANDANG kode_kandang
			,replace(replace(convert(varchar,convert(Money, ks.JML_POPULASI),1),'.00',''),',','.') populasi
			,replace(replace(convert(varchar,convert(Money, mk.MAX_POPULASI),1),'.00',''),',','.') kapasitas
			,cst.state
		from KANDANG_SIKLUS ks
		inner join m_kandang mk
			on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM and mk.STATUS_KANDANG = 'A'
		inner join m_farm mf
			on mf.KODE_FARM = ks.KODE_FARM
		inner join m_periode mp
			on mp.KODE_SIKLUS = ks.KODE_SIKLUS
		-- rdit yang sudah diapprove
		inner join (
		select distinct lks.KODE_SIKLUS from
			(select kode_farm,kode_siklus,max(no_urut) urut from LOG_KANDANG_SIKLUS_BDY
				group by kode_farm,kode_siklus
			)tt inner join LOG_KANDANG_SIKLUS_BDY lks
				on lks.kode_farm = tt.KODE_FARM and lks.KODE_SIKLUS = tt.KODE_SIKLUS and lks.NO_URUT = tt.urut
				and lks.STATUS_APPROVE = 'A'
	)rdit on rdit.KODE_SIKLUS = mp.KODE_SIKLUS
		left join cycle_state_transition cst
				on cst.flock = ks.FLOK_BDY
				and cst.cycle = ks.kode_siklus
		where ks.kode_std_budidaya is not null and ks.flok_bdy is not null and ks.status_siklus = 'P'
  	and ks.TGL_DOC_IN >= cast(getdate() - 50 as date)
--	and ks.KODE_SIKLUS in (select KODE_SIKLUS from M_PERIODE where KODE_FARM and STATUS_PERIODE = 'A')
		and cst.state is null
		union
	select mf.nama_farm
			,mf.kode_farm
			,ks.TGL_DOC_IN tgl_chickin
			,ks.no_reg
			,ks.kode_std_budidaya
			,ks.kode_siklus
			,case ks.TIPE_LANTAI
					when 'S' then 'Slate'
					when 'L' then 'Litter'
				end as tipe_lantai
			,case ks.TIPE_KANDANG
				when 'C' then 'Closed'
				when 'O' then 'Open'
			end as tipe_kandang
			,ks.KODE_KANDANG kode_kandang
			,replace(replace(convert(varchar,convert(Money, ks.JML_POPULASI),1),'.00',''),',','.') populasi
			,replace(replace(convert(varchar,convert(Money, mk.MAX_POPULASI),1),'.00',''),',','.') kapasitas
			,cst.state
		from KANDANG_SIKLUS ks
		inner join m_kandang mk
			on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM
		inner join m_farm mf
			on mf.KODE_FARM = ks.KODE_FARM
		inner join m_periode mp
			on mp.KODE_SIKLUS = ks.KODE_SIKLUS
		inner join (select max(stamp) update_terakhir,flock,cycle from cycle_state_transition
			group by flock,cycle
		)tt on tt.flock = ks.FLOK_BDY and tt.cycle = ks.KODE_SIKLUS
		inner join cycle_state_transition cst
			on cst.cycle = tt.cycle
			and cst.flock = tt.flock
			and cst.stamp = tt.update_terakhir
			and cst.state = 'RJ'
		where ks.STATUS_SIKLUS = 'P' and ks.TGL_DOC_IN >= cast(getdate() as date)
		order by mf.nama_farm,ks.TGL_DOC_IN
SQL;

        return $this->db->query($sql);
    }

    public function list_kandang_konfirmasi()
    {
        $sql = <<<SQL
		select mf.nama_farm
				,mf.kode_farm
				,ks.TGL_DOC_IN tgl_chickin
				,ks.no_reg
				,ks.kode_std_budidaya
				,case ks.TIPE_LANTAI
						when 'S' then 'Slate'
						when 'L' then 'Litter'
					end as tipe_lantai
				,case ks.TIPE_KANDANG
					when 'C' then 'Closed'
					when 'O' then 'Open'
				end as tipe_kandang
				,ks.KODE_KANDANG kode_kandang
				,replace(replace(convert(varchar,convert(Money, ks.JML_POPULASI),1),'.00',''),',','.') populasi
				,replace(replace(convert(varchar,convert(Money, mk.MAX_POPULASI),1),'.00',''),',','.') kapasitas
				,cst.state
			from KANDANG_SIKLUS ks
			inner join m_kandang mk
				-- on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.NO_FLOK = ks.FLOK_BDY and mk.KODE_FARM = ks.KODE_FARM
				on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM
			inner join m_farm mf
				on mf.KODE_FARM = mk.KODE_FARM
			inner join m_periode mp
				on mp.KODE_SIKLUS = ks.KODE_SIKLUS
			inner join (
				select max(stamp) update_terakhir,flock,cycle from cycle_state_transition
				group by flock,cycle
			)tt on tt.flock = ks.FLOK_BDY and tt.cycle = ks.KODE_SIKLUS
			inner join cycle_state_transition cst
				on cst.flock = tt.flock
				and tt.cycle = cst.cycle
				and cst.stamp = tt.update_terakhir
				and cst.noreg = ks.no_reg
				and cst.state in ('P1','P2','RL')
			where ks.kode_std_budidaya is not null and ks.flok_bdy is not null
			and ks.status_siklus != 'C'
	--		and ks.tgl_doc_in >= getdate()
	--		and ks.KODE_SIKLUS in (select KODE_SIKLUS from M_PERIODE where KODE_FARM and STATUS_PERIODE = 'A')
			order by mf.nama_farm,ks.TGL_DOC_IN
SQL;

        return $this->db->query($sql);
    }

    public function list_kandang_approvalkadiv($farm = null, $status = array(), $custom_param = null)
    {
        $cari_tanggal = '';
        $cari_status = array();
        $cari_farm = !empty($farm) ? ' and ks.kode_farm = \''.$farm.'\'' : '';
        if (!empty($custom_param)) {
            $cari_tanggal = ' where '.$custom_param;
        }

        if (empty($status)) {
            $status = array('P2');
        }

        $cari_status = ' (\''.implode('\',\'', $status).'\')';

        $sql = <<<SQL
	select * from(
		select y.nama_farm
				,y.kode_farm
				,y.tgl_chickin
				,y.periode_siklus
				,case y.status
					when 'P2' then 'Review'
					when 'RL' then 'Rilis'
					else y.status
					end status
				,case y.status
					when 'P2' then y.approve
					else (select max(stamp) from cycle_state_transition where cycle = y.cycle and flock = y.flock and state = 'P2')
				end approve_kadept
				,case y.status
					when 'RL' then y.approve
					else NULL
				end tgl_approvekadiv
				,y.umur_panen
				,dateadd(day,y.umur_panen,y.tgl_chickin) akhir_kebutuhan				
		from (
			select mf.nama_farm
					,ks.kode_farm
					,ks.TGL_DOC_IN tgl_chickin
					,mp.PERIODE_SIKLUS periode_siklus
					,cst.state status
					,cst.stamp approve_kadept
					,msb.TARGET_UMUR_PANEN umur_panen
					,cst.stamp approve
					,cst.cycle
					,cst.flock				
				from KANDANG_SIKLUS ks
				inner join m_kandang mk
					--on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.NO_FLOK = ks.FLOK_BDY and mk.KODE_FARM = ks.KODE_FARM
					on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM
				inner join m_farm mf
					on mf.KODE_FARM = mk.KODE_FARM
				inner join m_periode mp
					on mp.KODE_SIKLUS = ks.KODE_SIKLUS
				inner join m_std_budidaya msb
					on msb.kode_std_budidaya = ks.kode_std_budidaya
				inner join (select max(stamp) update_terakhir,flock,cycle from cycle_state_transition
					group by flock,cycle
				)tt on tt.flock = ks.FLOK_BDY and tt.cycle = ks.KODE_SIKLUS
				inner join cycle_state_transition cst
					on cst.cycle = tt.cycle
					and cst.flock = tt.flock
					and cst.stamp = tt.update_terakhir
					and cst.state in {$cari_status}
					{$cari_farm}
				group by mf.nama_farm
					,ks.kode_farm
					,ks.TGL_DOC_IN
					,mp.PERIODE_SIKLUS
					,cst.state
					,cst.stamp
					,msb.TARGET_UMUR_PANEN
					,cst.cycle
					,cst.flock				
		)y
	)z				
	{$cari_tanggal}

SQL;

        return $this->db->query($sql);
    }

    public function get_standart_pakan($strain, $tipe_kandang, $musim, $jk, $tglDocIn)
    {
        /* query perlu diperbaiki untuk menentukan tgl efektif yang digunakan */
        $sql = <<<SQL
		select lower(msb.JENIS_KELAMIN) jenis_kelamin
			,msb.kode_std_breeding
			,msb.STD_UMUR umur
			,msb.deplesi_prc dh
			,msb.TARGET_PKN target_pakan
			,msb.GRUP_BARANG grup_barang
			,msb.BENTUK bentuk
			,msb.nama_barang nama_barang
			,msb.kode_barang kode_barang
		from v_STD_BREEDING msb
		where msb.KODE_STD_BREEDING = (
			select top 1 KODE_STD_BREEDING from m_std_breeding msb
			where msb.KODE_STRAIN = '{$strain}'
			and msb.TIPE_KANDANG = '{$tipe_kandang}'
			and msb.musim = '{$musim}'
			and msb.jenis_kelamin = '{$jk}'
			and msb.TGL_EFEKTIF <= '{$tglDocIn}'
			order by TGL_EFEKTIF desc
		) 	order by msb.std_umur
SQL;

        return $this->db->query($sql);
    }

    public function get_standart_pakan_bdy($tglDocIn, $kodeFarm)
    {
        /* query perlu diperbaiki untuk menentukan tgl efektif yang digunakan */
        $sql = <<<SQL
		select 'j' jenis_kelamin
			,msb.kode_std_budidaya
			,msb.STD_UMUR umur
			,msb.PKN_HR_STD target_pakan
			,dbo.bentuk_convertion(mb.bentuk_barang) bentuk
			,'bdy' grup_barang
			,mb.nama_barang
			,msb.kode_barang kode_barang
		from m_std_budidaya_d msb
		inner join m_barang mb
			on mb.kode_barang = msb.kode_barang
		inner join M_STD_BUDIDAYA ms
			on msb.KODE_STD_BUDIDAYA = ms.KODE_STD_BUDIDAYA and msb.STD_UMUR <= ms.TARGET_UMUR_PANEN
		where msb.KODE_STD_budidaya = (
			select top 1 KODE_STD_budidaya from kandang_siklus ks
			where ks.tgl_doc_in = '{$tglDocIn}' and ks.kode_farm = '{$kodeFarm}'
		) 	order by msb.std_umur
SQL;

        return $this->db->query($sql);
    }

    public function get_master_pakan($group_pakan)
    {
        $s = array();
        foreach ($group_pakan as $i => $val) {
            array_push($s, 'select \''.$val['group'].'\' as grup_barang, \''.$val['bentuk'].'\' as bentuk');
        }
        $gp = implode(' union ', $s);
        $sql = <<<SQL
		select mb.grup_barang
			,mb.bentuk_barang
			,mb.kode_barang kodepj
		  	,mb.nama_barang namapj
		from m_barang mb
		inner join (
			{$gp}
		)t
		on t.grup_barang = mb.grup_barang  and t.bentuk = mb.bentuk_barang
SQL;

        return $this->db->query($sql);
    }

    public function get_pakan_tersimpan($tglDocIn, $idFarm)
    {
        $sql = <<<SQL
		select fd.umur
			,fd.j_kode_barang kodepjjantan
			,fd.b_kode_barang kodepjbetina
			,mb.nama_barang namapjjantan
			,mb2.nama_barang namapjbetina
		from FORECAST_d fd
		inner join m_barang mb
		on mb.KODE_BARANG = fd.J_KODE_BARANG
		inner join m_barang mb2
		on mb2.KODE_BARANG = fd.B_KODE_BARANG
		where fd.forecast_no = (
			select top 1 f.FORECAST_NO
			from KANDANG_SIKLUS ks
			inner join forecast f
			on f.NO_REG = ks.NO_REG
			where ks.TGL_DOC_IN = '{$tglDocIn}'
			and ks.kode_farm = '{$idFarm}'
		)
SQL;

        return $this->db->query($sql);
    }

    public function get_kandang_flock($kode_farm, $cari = array())
    {
        $arr = array();
        foreach ($cari as $i => $val) {
            if (!empty($val)) {
                if ($i == 'ks.TGL_DOC_IN') {
                    foreach ($val as $j => $tgl) {
                        if (!empty($tgl)) {
                            array_push($arr, $i.' '.$j.' \''.$tgl.'\'');
                        }
                    }
                } elseif ($i == 'ks.kode_flok') {
                    array_push($arr, $i.'  '.$val);
                }
            }
        }
        $where = '';
        if (!empty($arr)) {
            $where = ' and '.implode(' and ', $arr);
        }
        $sql = <<<SQL
		select ks.TGL_DOC_IN doc_in
			,mk.KODE_KANDANG kode_kandang
			,ks.TIPE_KANDANG tipe_kandang
			,mf.NAMA_FLOK flok
			,mf.TGL_TETAS tanggal_tetas
			,ks.TIPE_Lantai	tipe_lantai
			,mk.MAX_POPULASI kapasitas
			,ks.JML_JANTAN jantan
			,ks.JML_BETINA betina
			,ks.no_reg no_reg
		from KANDANG_SIKLUS ks
		inner join M_KANDANG mk
			on mk.KODE_KANDANG = ks.KODE_KANDANG and mk.KODE_FARM = ks.KODE_FARM
		left join m_flok mf
			on mf.KODE_FLOK = ks.kode_flok
		where USER_APPROVE1 is not null
		and ks.STATUS_SIKLUS = 'O'	and ks.kode_farm = '{$kode_farm}'
		{$where}
SQL;

        return $this->db->query($sql);
    }

    public function simpan($dataKandang, $dataFarm)
    {
        foreach ($dataKandang as $row) {
            $tmp = array();
            $tmp['no_reg'] = $dataFarm['kodeFarm'].'-'.$dataFarm['periodeSiklus'].'/'.$row['kandang'];
            $tmp['forecast_no'] = $tmp['no_reg'];
            $tmp['no_urut'] = 1;
            $tmp['user_buat'] = $this->_user;

            $this->db->insert($this->_table, $tmp);
        }
    }

    public function kebutuhan_pakan($tanggal, $farm)
    {
        $startDate = $tanggal['startDate'];
        $endDate = $tanggal['endDate'];
        $status_lpb = 'A';
        $no_lpb = null;
        $kode_farm = $farm;
        //	$status_lpb = empty($params['status_lpb']) ? NULL : $params['status_lpb'];
        $grouping = 'T';
        $hitung_ulang = 0;
        $sql = <<<SQL
			exec dbo.PERMINTAAN_PAKAN_PP_COBA :kode_farm,:start_date,:end_date,:status_pp,:no_lpb,:hitung_ulang,:grouping
SQL;
        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->bindParam(':kode_farm', $kode_farm);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':status_pp', $status_lpb);
        $stmt->bindParam(':no_lpb', $no_lpb);
        $stmt->bindParam(':grouping', $grouping);
        $stmt->bindParam(':hitung_ulang', $hitung_ulang);

        $stmt->execute();
        //	print_r($stmt->errorInfo());
        return $stmt->fetchAll(2);
    }

    public function kebutuhan_pakan_ppic($tanggal, $farm)
    {
        $startDate = $tanggal['startDate'];
        $endDate = $tanggal['endDate'];
        $sql = <<<SQL
			exec dbo.forecast_ppic :kode_farm,:start_date,:end_date
SQL;

        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->bindParam(':kode_farm', $farm);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();

        return $stmt->fetchAll(2);
    }

    public function kebutuhan_pakan_ppic_bdy($ack)
    {
        $sql = <<<SQL
			exec dbo.forecast_ppic_lpb :ack
SQL;

        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->bindParam(':ack', $ack);
        $stmt->execute();

        return $stmt->fetchAll(2);
    }

    public function data_konfirmasi_ppic($tanggal, $farm, $konfirmasi)
    {
        $data = $this->get_data_konfirmasi_ppic($tanggal, $farm, $konfirmasi);
        $result = array();
        foreach ($data as $key => $value) {
            $result[$value['KODE_FARM']][$value['STAMP']] = array(
                'no_konfirmasi' => $value['NO_KONFIRMASI'],
                'kode_farm' => $value['KODE_FARM'],
                'nama_farm' => $value['NAMA_FARM'],
                'populasi_betina' => $value['POPULASI_BETINA'],
                'populasi_jantan' => $value['POPULASI_JANTAN'],
                'user' => $value['USER'],
                'tanggal' => $value['TANGGAL'],
            );
        }
        foreach ($data as $key => $value) {
            $result[$value['KODE_FARM']][$value['STAMP']]['detail'][] = array(
                'no_reg' => $value['NO_REG'],
                'nama_kandang' => $value['NAMA_KANDANG'],
                'tipe_kandang' => $value['TIPE_KANDANG'],
                'kode_strain' => $value['KODE_STRAIN'],
                'tanggal_chickin' => $value['TANGGAL_CHICKIN'],
                'jml_betina' => $value['JML_BETINA'],
                'jml_jantan' => $value['JML_JANTAN'],
            );
        }
        foreach ($result as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $populasi_jantan = 0;
                $populasi_betina = 0;
                foreach ($value2['detail'] as $key3 => $value3) {
                    $populasi_jantan = $populasi_jantan + $value3['jml_jantan'];
                    $populasi_betina = $populasi_betina + $value3['jml_betina'];
                }
                $result[$key1][$key2]['populasi_jantan'] = $populasi_jantan;
                $result[$key1][$key2]['populasi_betina'] = $populasi_betina;
            }
        }

        return $result;
    }

    public function get_data_konfirmasi_ppic($tanggal, $farm, $konfirmasi)
    {
        $filter_str = '';
        $filter_arr = array();

        $startDate = $tanggal['startDate'];
        $endDate = $tanggal['endDate'];

        $main_filter = "AND KS.KODE_FARM IN ('$farm')
            AND KS.TGL_APPROVE1 IS NOT NULL
            AND KS.STATUS_SIKLUS = 'O'";

        if ($konfirmasi == 0) {
            $filter = 'AFI.ID IS NULL';
        } else {
            $filter = "CAST(KS.TGL_DOC_IN AS DATE) BETWEEN '$startDate' AND '$endDate'";
        }

        $sql = <<<QUERY
            SELECT DISTINCT
                KS.KODE_SIKLUS
                , KS.NO_REG
                , MSB.KODE_STRAIN
                , AFI.ID NO_KONFIRMASI
                , MF.KODE_FARM
                , MF.NAMA_FARM
                , MK.JML_BETINA POPULASI_BETINA
                , MK.JML_JANTAN POPULASI_JANTAN
                , MP.NAMA_PEGAWAI [USER]
                , AFI.STAMP TANGGAL
                , MK.NAMA_KANDANG
                , MK.TIPE_KANDANG
                , KS.TGL_DOC_IN TANGGAL_CHICKIN
                , KS.JML_BETINA
                , KS.JML_JANTAN
                , AFI.STAMP
            FROM (
                select DISTINCT
                    KODE_SIKLUS
                    , KS.TGL_DOC_IN
                    , KS.NO_REG
                    , AFI.ID
                from KANDANG_SIKLUS KS
                LEFT JOIN ACK_FORECAST AF ON AF.KANDANG_SIKLUS = KS.NO_REG
                LEFT JOIN ACK_FORECAST_ITEM AFI ON AFI.ID = AF.ACK_FORECAST_ITEM
                WHERE $filter
                $main_filter
            ) KS2
            JOIN KANDANG_SIKLUS KS ON KS.KODE_SIKLUS = KS2.KODE_SIKLUS
                $main_filter
            JOIN M_STD_BREEDING MSB ON MSB.KODE_STD_BREEDING = KS.KODE_STD_BREEDING_B
            JOIN M_KANDANG MK ON MK.KODE_FARM = KS.KODE_FARM AND MK.KODE_KANDANG = KS.KODE_KANDANG
            JOIN M_FARM MF ON MF.KODE_FARM = KS.KODE_FARM
            LEFT JOIN ACK_FORECAST AF ON AF.KANDANG_SIKLUS = KS.NO_REG
            LEFT JOIN ACK_FORECAST_ITEM AFI ON AFI.ID = AF.ACK_FORECAST_ITEM
                --AND AFI.ID = KS2.ID
            LEFT JOIN M_PEGAWAI MP ON MP.KODE_PEGAWAI = AFI.KODE_PEGAWAI
            ORDER BY KS.KODE_SIKLUS ASC, KS.TGL_DOC_IN ASC, MK.NAMA_KANDANG ASC

QUERY;
        //echo $sql;
        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function data_konfirmasi_ppic_bdy($tanggal, $farm, $konfirmasi, $sudah_konfirmasi = null)
    {
        $data = $this->get_data_konfirmasi_ppic_bdy($tanggal, $farm, $konfirmasi, $sudah_konfirmasi);
        $result = array();
        foreach ($data as $key => $value) {
            $result[$value['KODE_FARM']] = array(
                'kode_farm' => $value['KODE_FARM'],
                'nama_farm' => $value['NAMA_FARM'],
                'populasi_campuran' => $value['POPULASI_CAMPURAN'],
            );
        }
        foreach ($data as $key => $value) {
            $result[$value['KODE_FARM']]['detail'][$value['KODE_FLOK']][$value['STAMP']]['detail'][] = array(
                'no_reg' => $value['NO_REG'],
                'nama_kandang' => $value['NAMA_KANDANG'],
                'tipe_kandang' => $value['TIPE_KANDANG'],
        'maks_populasi' => $value['POPULASI_CAMPURAN'],
                'kode_strain' => $value['KODE_STRAIN'],
                'tanggal_chickin' => $value['TANGGAL_CHICKIN'],
                'jml_campuran' => $value['JML_CAMPURAN'],
                'no_konfirmasi' => $value['NO_KONFIRMASI'],
                'user' => $value['USER'],
                'tanggal' => $value['TANGGAL'],
                'kode_kandang' => $value['KODE_KANDANG'],
            );
        }
        foreach ($result as $key1 => $value1) {
            $rowspan_farm = 0;
            $populasi_campuran = 0;
            foreach ($value1['detail'] as $key2 => $value2) {
                foreach ($value2 as $key3 => $value3) {
                    foreach ($value3['detail'] as $key4 => $value4) {
                        $populasi_campuran = $populasi_campuran + $value4['jml_campuran'];
                        ++$rowspan_farm;
                        $result[$key1]['detail'][$key2][$key3]['no_konfirmasi'] = $value4['no_konfirmasi'];
                        $result[$key1]['detail'][$key2][$key3]['user'] = $value4['user'];
                        $result[$key1]['detail'][$key2][$key3]['tanggal'] = $value4['tanggal'];
                    }
                }
                $result[$key1]['populasi_campuran'] = $populasi_campuran;
                //echo $populasi_campuran;
                $result[$key1]['rowspan_farm'] = $rowspan_farm;
            }
        }
        /*
        foreach ($result as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                foreach ($value2['detail'] as $key3 => $value3) {

                }
            }
        }
        */
        return $result;
    }

    public function get_data_konfirmasi_ppic_bdy($tanggal, $farm, $konfirmasi, $sudah_konfirmasi = null)
    {
        $filter_str = '';
        $filter_arr = array();
        $filter = '1 = 1';
        $startDate = $tanggal['startDate'];
        $endDate = $tanggal['endDate'];

        $main_filter = "AND KS.KODE_FARM IN ('$farm')
            AND KS.TGL_APPROVE1 IS NOT NULL
            AND KS.STATUS_SIKLUS = 'O'";
        if ($sudah_konfirmasi == 0) {
            if ($konfirmasi == 1) {
                $filter = 'AFI.ID IS NOT NULL';
            } /* dicentang */
        } else {
            if ($konfirmasi == 0) {
                $filter = 'AFI.ID IS NULL';
            }
        }
        if ($konfirmasi == 1 && $sudah_konfirmasi == 1) {
            $filter = "CAST(KS.TGL_DOC_IN AS DATE) BETWEEN '$startDate' AND '$endDate'";
        }

        $sql = <<<QUERY
            SELECT DISTINCT
                KS.KODE_SIKLUS
                , KS.NO_REG
                , MSB.KODE_STRAIN
                , AFI.ID NO_KONFIRMASI
                , MF.KODE_FARM
                , MF.NAMA_FARM
                , MK.MAX_POPULASI POPULASI_CAMPURAN
                , MP.NAMA_PEGAWAI [USER]
                , AFI.STAMP TANGGAL
								, KS.FLOK_BDY KODE_FLOK
                , MK.NAMA_KANDANG
                , MK.TIPE_KANDANG
                , KS.TGL_DOC_IN TANGGAL_CHICKIN
                , KS.JML_POPULASI JML_CAMPURAN
                , AFI.STAMP
								, KS.KODE_KANDANG
            FROM (
                select DISTINCT
                    KODE_SIKLUS
                    , KS.TGL_DOC_IN
                    , KS.NO_REG
                    , AFI.ID
                from KANDANG_SIKLUS KS
                LEFT JOIN ACK_FORECAST AF ON AF.KANDANG_SIKLUS = KS.NO_REG
                LEFT JOIN ACK_FORECAST_ITEM AFI ON AFI.ID = AF.ACK_FORECAST_ITEM
                WHERE $filter
                $main_filter
            ) KS2
            JOIN KANDANG_SIKLUS KS ON KS.KODE_SIKLUS = KS2.KODE_SIKLUS
				AND KS.NO_REG = KS2.NO_REG
                $main_filter
            JOIN M_STD_BUDIDAYA MSB ON MSB.KODE_STD_BUDIDAYA = KS.KODE_STD_BUDIDAYA
            JOIN M_KANDANG MK ON MK.KODE_FARM = KS.KODE_FARM AND MK.KODE_KANDANG = KS.KODE_KANDANG
            JOIN M_FARM MF ON MF.KODE_FARM = KS.KODE_FARM
            LEFT JOIN ACK_FORECAST AF ON AF.KANDANG_SIKLUS = KS.NO_REG
            LEFT JOIN ACK_FORECAST_ITEM AFI ON AFI.ID = AF.ACK_FORECAST_ITEM
                --AND AFI.ID = KS2.ID
            LEFT JOIN M_PEGAWAI MP ON MP.KODE_PEGAWAI = AFI.KODE_PEGAWAI
            ORDER BY KS.KODE_SIKLUS ASC, KS.TGL_DOC_IN ASC, MK.NAMA_KANDANG ASC

QUERY;
        //echo $sql;
        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ack_forecast($kode_pegawai, $nama_pegawai, $no_reg)
    {
        $count = 0;
        $this->db->conn_id->beginTransaction();

        $ack_forecast_item = $this->insert_ack_forecast_item($kode_pegawai);

        $id_ack_forecast_item = $ack_forecast_item['ID'];
        $tanggal = '';

        if (!empty($id_ack_forecast_item)) {
            $tanggal = tglIndonesia(date('Y-m-d', strtotime($ack_forecast_item['STAMP'])), '-', ' ').' '.date('H:i', strtotime($ack_forecast_item['STAMP']));
            foreach ($no_reg as $key => $value) {
                $ack_forecast = $this->insert_ack_forecast($id_ack_forecast_item, $value);
                $count = (!empty($ack_forecast['ID'])) ? $count + 1 : $count;
            }
        }
        if ($count == count($no_reg)) {
            $this->db->conn_id->commit();
            $return = 1;
        } else {
            $this->db->conn_id->rollback();
            $return = 0;
        }

        $result = array(
            'result' => $return,
            'no_konfirmasi' => $id_ack_forecast_item,
            'nama_pegawai' => $nama_pegawai,
            'tanggal' => $tanggal,
        );

        return $result;
    }

    public function insert_ack_forecast_item($kode_pegawai)
    {
        $sql = <<<QUERY
			INSERT INTO ACK_FORECAST_ITEM
			OUTPUT INSERTED.ID
				 , INSERTED.STAMP
			SELECT
				dbo.next_id(
					(
						MAX(ID)
					)
					, GETDATE()
				)
				, '$kode_pegawai'
				, GETDATE()
			FROM ACK_FORECAST_ITEM

QUERY;
        //echo $sql;
        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_ack_forecast($ack_forecast_item, $no_reg)
    {
        $sql = <<<QUERY
			INSERT INTO ACK_FORECAST
			OUTPUT INSERTED.ID
			VALUES (
				'$ack_forecast_item'
				, '$no_reg'
			)

QUERY;
        //echo $sql;
        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function konfirmasi_rp($where_kirim, $where_realisasi, $kode_farm = null)
    {
        $farm = empty($kode_farm) ? '' : ' and op.kode_farm =\''.$kode_farm.'\'';
        $sql = <<<SQL
		select l.NO_LPB no_lpb
				,op.NO_OP no_op
				,mb.NAMA_BARANG nama_barang
				,mb.KODE_BARANG kode_barang
				,mf.NAMA_FARM nama_farm
				,sum(le.JML_ORDER) jml_permintaan
				,min(le.TGL_KIRIM) tgl_kirim
				,case kp.realisasi_produksi
					when 'C' then 'Sudah'
					when 'I' then 'Belum'
					else null
				 end realisasi_produksi
				,kp.tgl_akhir_rencana_produksi
				,kp.id id_konfirmasi
				,stuff(
					(select distinct ','+ krp.rencana_produksi
				from konfirmasi_rencana_produksi krp
				where krp.konfirmasi_ppic = kp.id
				for xml path (''))
				,1,1,'') rencana_produksi
		from lpb l
		inner join op
			on op.NO_LPB = l.NO_LPB {$farm}
		inner join lpb_e le
			on le.NO_LPB = l.NO_LPB
		inner join m_farm mf
			on mf.KODE_FARM = l.KODE_FARM and mf.grup_farm = 'brd'
		inner join m_barang mb
			on mb.KODE_BARANG = le.KODE_BARANG
		left join do
			on do.NO_OP = op.no_op
		left join konfirmasi_ppic kp
			on kp.no_op = op.NO_OP
				and kp.tgl_kirim = le.TGL_KIRIM
				and kp.kode_pakan = le.KODE_BARANG
		where (do.STATUS_DO = 'N' or do.STATUS_DO is null)
				{$where_kirim}
				{$where_realisasi}
		group by l.NO_LPB
				,op.NO_OP
				,le.KODE_BARANG
				,mb.NAMA_BARANG
				,mf.NAMA_FARM
				,mb.KODE_BARANG
				,kp.realisasi_produksi
				,kp.tgl_akhir_rencana_produksi
				,kp.id
SQL;

        return $this->db->query($sql);
    }

    public function konfirmasi_rp_bdy($tglawal, $tglakhir, $kodepakan)
    {
        $sql = <<<SQL
		exec rencana_kirim_ppic '{$tglawal}','{$tglakhir}','{$kodepakan}'
SQL;

        return $this->db->query($sql);
    }

    public function get_detail_docin_bdy($kodefarm, $tgldocin, $kandang = null)
    {
        $where = !empty($kandang) ? 'and ks.KODE_KANDANG = \''.$kandang.'\'' : '';
        $sql = <<<SQL
		select ks.KODE_KANDANG kode_kandang
				,ks.JML_POPULASI populasi
				,msb.TARGET_DH_PRC dayahidup
				,msb.TARGET_BB_PRC beratbadan
				,msb.TARGET_FCR_PRC fcr
				,DATEDIFF(day,ks.tgl_doc_in,ks.tgl_panen) umurpanen
				,msb.TARGET_IP ip
		from kandang_siklus ks
		inner join M_STD_BUDIDAYA msb
			on ks.KODE_STD_BUDIDAYA = msb.KODE_STD_BUDIDAYA
		where ks.KODE_FARM = '{$kodefarm}'
			and ks.tgl_doc_in = '{$tgldocin}'
			{$where}


SQL;

        return $this->db->query($sql);
    }

    public function check_standart_baru($idfarm = null)
    {
        $where = !empty($idfarm) ? ' and ks.kode_farm = \''.$idfarm.'\'' : '';
        $sql = <<<SQL
		select
		min(ks.tgl_doc_in) tgl_doc_in
			,ks.KODE_FARM kode_farm
			,msb.KODE_STRAIN strain
			,ks.KODE_STD_BUDIDAYA std_lama
			,d.KODE_STD_BUDIDAYA std_baru
			,d.TGL_EFEKTIF tgl_efektif
			,mf.NAMA_FARM nama_farm
			,sum(ks.jml_populasi) jml_populasi
		from kandang_siklus ks
		inner join m_farm mf
			on mf.KODE_FARM = ks.KODE_FARM
		inner join M_STD_BUDIDAYA msb
			on ks.KODE_STD_BUDIDAYA = msb.KODE_STD_BUDIDAYA
		left join (
		select ms.KODE_STD_BUDIDAYA
			,ms.KODE_STRAIN
			,ms.KODE_FARM
			,ms.TGL_EFEKTIF
		from m_std_budidaya ms
		inner join (select max(tgl_efektif) tgl_efektif,kode_farm,kode_strain from M_STD_BUDIDAYA
			group by kode_farm,kode_strain
		)c
		on c.KODE_FARM = ms.KODE_FARM and c.KODE_STRAIN = ms.KODE_STRAIN and c.tgl_efektif = ms.TGL_EFEKTIF
	)d on d.KODE_FARM = ks.KODE_FARM and d.KODE_STRAIN = msb.KODE_STRAIN
		where ks.TGL_DOC_IN >= d.TGL_EFEKTIF
		 and ks.tgl_doc_in > getdate() + 6
		 and d.KODE_STD_BUDIDAYA <> ks.KODE_STD_BUDIDAYA
		{$where}
	group by ks.KODE_FARM
			,msb.KODE_STRAIN
			,ks.KODE_STD_BUDIDAYA
			,d.KODE_STD_BUDIDAYA
			,d.TGL_EFEKTIF
			,mf.NAMA_FARM

SQL;

        return $this->db->query($sql);
    }

    public function list_pakan_bdy()
    {
        $sql = <<<SQL
		select distinct md.kode_barang
			,mb.NAMA_BARANG nama_barang
		from M_STD_BUDIDAYA_d md
		inner join m_barang mb
		on md.KODE_BARANG = mb.KODE_BARANG
SQL;

        return $this->db->query($sql);
    }

    public function estimasi_rencana_produksi($tglkirimawal, $tglkirimakhir, $kodepakan)
    {
        $kp = !empty($kodepakan) ? ' and et.kode_pakan in ('.$kodepakan.')' : '';
        $sql = <<<SQL
		select coalesce(es.tanggal_kirim,et.tanggal_kirim) tanggal_kirim
			,es.tanggal_produksi tanggal_produksi_estimasi
			,et.tanggal_produksi
			,coalesce(es.rencana_kirim,et.rencana_kirim) rencana_kirim
			,et.rencana_produksi kode_rencana_produksi
			,et.total_produksi
			,et.id_hasil_produksi
			,et.alokasi_pakan_untuk_farm
			,et.total_pakan_lolos
			,et.jumlah_sak alokasi_pakan_lolos_untuk_farm
			,coalesce(es.kode_pakan,et.kode_pakan) kode_pakan
			,(select count(*) from alokasi_pakan_lolos_untuk_farm where alokasi_hasil_produksi = et.id_hasil_produksi) revisi
		from
		(
		select rk.tanggal_kirim
			, rk.id rencana_kirim
			, rk.kode_pakan
			,etp.tanggal_produksi
		from rencana_kirim rk
		inner join estimasi_tanggal_produksi etp
			on etp.rencana_kirim = rk.id
		where rk.tanggal_kirim between '{$tglkirimawal}' and '{$tglkirimakhir}'
		)es full join (
		select rp.tanggal_produksi
			, irp.rencana_produksi
			, irp.total_produksi
			, irp.total_pakan_lolos
			, ahp.alokasi_pakan_untuk_farm
			, ahp.id id_hasil_produksi
			, ahp.rencana_kirim
			, apl.jumlah_sak
			, rk.tanggal_kirim
			, rk.kode_pakan
			, apl.tgl_entry
		from alokasi_hasil_produksi ahp
		inner join rencana_kirim rk
			on rk.id = ahp.rencana_kirim and rk.tanggal_kirim between '{$tglkirimawal}' and '{$tglkirimakhir}'
		inner join item_rencana_produksi irp
			on irp.id = ahp.item_rencana_produksi
		inner join rencana_produksi rp
			on rp.id = irp.rencana_produksi
		left join ( select max(tgl_entry) tgl_entry,alokasi_hasil_produksi
					from alokasi_pakan_lolos_untuk_farm aplf
					group by alokasi_hasil_produksi
					)apl_treakhir
						on apl_treakhir.alokasi_hasil_produksi = ahp.id
					left join alokasi_pakan_lolos_untuk_farm apl
					on apl.alokasi_hasil_produksi = apl_treakhir.alokasi_hasil_produksi and apl.tgl_entry = apl_treakhir.tgl_entry
		)et on es.rencana_kirim = et.rencana_kirim and es.tanggal_produksi = et.tanggal_produksi
		{$kp}
		order by et.tanggal_produksi,et.tgl_entry
SQL;

        return $this->db->query($sql);
    }

    public function plot_pakan_sebelumnya($tgl_kirim_awal)
    {
        $sql = <<<SQL
		select sum(ahp.alokasi_pakan_untuk_farm) alokasi_pakan
			,sum(apl.jumlah_sak) alokasi_pakan_lolos
			,irp.rencana_produksi
			,irp.pakan
		from rencana_kirim rk
		inner join alokasi_hasil_produksi ahp
			on ahp.rencana_kirim = rk.id
		inner join item_rencana_produksi irp
			on ahp.item_rencana_produksi = irp.id and irp.pakan = rk.kode_pakan
		left join ( select max(tgl_entry) tgl_entry,alokasi_hasil_produksi from alokasi_pakan_lolos_untuk_farm aplf
				 group by alokasi_hasil_produksi
				)apl_treakhir on apl_treakhir.alokasi_hasil_produksi = ahp.id
		left join alokasi_pakan_lolos_untuk_farm apl
			on apl.alokasi_hasil_produksi = apl_treakhir.alokasi_hasil_produksi and apl.tgl_entry = apl_treakhir.tgl_entry
		where rk.tanggal_kirim >= dateadd(day,-8,'{$tgl_kirim_awal}') and rk.tanggal_kirim < '{$tgl_kirim_awal}'
		group by irp.rencana_produksi, irp.pakan
SQL;

        return $this->db->query($sql);
    }

    public function plot_pakan_farm($rp, $kode_pakan)
    {
        $sql = <<<SQL
		select sum(ahp.alokasi_pakan_untuk_farm) alokasi_pakan
				,sum(apl.jumlah_sak) alokasi_pakan_lolos
		from rencana_produksi rp
		inner join item_rencana_produksi irp
			on irp.rencana_produksi = rp.id and irp.pakan = '{$kode_pakan}'
		inner join alokasi_hasil_produksi ahp
			on ahp.item_rencana_produksi = irp.id
		left join ( select max(tgl_entry) tgl_entry,alokasi_hasil_produksi from alokasi_pakan_lolos_untuk_farm aplf
					 group by alokasi_hasil_produksi
					)apl_treakhir on apl_treakhir.alokasi_hasil_produksi = ahp.id
		left join alokasi_pakan_lolos_untuk_farm apl
				on apl.alokasi_hasil_produksi = apl_treakhir.alokasi_hasil_produksi and apl.tgl_entry = apl_treakhir.tgl_entry
		where rp.id = '{$rp}'
		group by irp.id
SQL;

        return $this->db->query($sql);
    }

    public function alokasi_sisa_kebutuhan($min, $max, $kodepj)
    {
        $sql = <<<SQL
		select y.id rencana_produksi
				,sum(y.alokasi_pakan_untuk_farm) alokasi_pakan_untuk_farm
				,sum(y.sisa_kebutuhan) - sum(y.pakai_sisa_kebutuhan) sisa_kebutuhan
				,sum(y.pengurang_belum_alokasi) pengurang_belum_alokasi
				,sum(y.jumlah_sak) plot_lolos_pakan
			from (
			select rp.id
				, ahp.alokasi_pakan_untuk_farm
				, apl.jumlah_sak
				, case
					when apl.jumlah_sak is null then 0
					when (select count(*) from alokasi_pakan_lolos_untuk_farm where alokasi_hasil_produksi = ahp.id) > 1
						then (select top 1 jumlah_sak from alokasi_pakan_lolos_untuk_farm where alokasi_hasil_produksi = ahp.id and tgl_entry < apl.tgl_entry) - apl.jumlah_sak
					else 0
				end sisa_kebutuhan
				, case
				when ahp.alokasi_pakan_untuk_farm is null and apl.jumlah_sak is not null then apl.jumlah_sak
			--	when ahp.alokasi_pakan_untuk_farm is not null and apl.jumlah_sak is not null then ahp.alokasi_pakan_untuk_farm
				else 0
				end pengurang_belum_alokasi
				,case
				when apl.jumlah_sak is not null and ahp.alokasi_pakan_untuk_farm is null then apl.jumlah_sak
				else 0
			end pakai_sisa_kebutuhan
			from rencana_produksi rp
			inner join item_rencana_produksi irp
				on rp.id = irp.rencana_produksi and irp.pakan = '{$kodepj}'
			inner join alokasi_hasil_produksi ahp
				on irp.id = ahp.item_rencana_produksi
			left join ( select max(tgl_entry) tgl_entry,alokasi_hasil_produksi from alokasi_pakan_lolos_untuk_farm aplf
					group by alokasi_hasil_produksi
			)apl_treakhir
			on apl_treakhir.alokasi_hasil_produksi = ahp.id
			left join alokasi_pakan_lolos_untuk_farm apl
			on apl.alokasi_hasil_produksi = apl_treakhir.alokasi_hasil_produksi and apl.tgl_entry = apl_treakhir.tgl_entry
			where rp.tanggal_produksi between '{$min}' and '{$max}'
			)y
			group by y.id
SQL;

        return $this->db->query($sql);
    }

    public function plot_pakan_farm_tanggal($tglawal, $tglakhir, $kodepj)
    {
        $sql = <<<SQL
		select sum(ahp.alokasi_pakan_untuk_farm) alokasi_pakan
				,sum(apl.jumlah_sak) alokasi_pakan_lolos
				,irp.rencana_produksi
		from rencana_produksi rp
		inner join item_rencana_produksi irp
			on irp.rencana_produksi = rp.id  and irp.pakan = '{$kodepj}'
		inner join alokasi_hasil_produksi ahp
			on ahp.item_rencana_produksi = irp.id
		left join ( select max(tgl_entry) tgl_entry,alokasi_hasil_produksi from alokasi_pakan_lolos_untuk_farm aplf
					 group by alokasi_hasil_produksi
					)apl_treakhir on apl_treakhir.alokasi_hasil_produksi = ahp.id
		left join alokasi_pakan_lolos_untuk_farm apl
				on apl.alokasi_hasil_produksi = apl_treakhir.alokasi_hasil_produksi and apl.tgl_entry = apl_treakhir.tgl_entry
		where rp.tanggal_produksi between '{$tglawal}' and '{$tglakhir}'
		group by irp.id
				,irp.rencana_produksi
SQL;

        return $this->db->query($sql);
    }

    public function notif_rencana_docin_reject($user_level = null)
    {
        $reject_user = array(
            'KA' => array('KDP', 'KDV'),
            'KD' => array('KDV'),
        );
        $status_appprove = array(
            'KA' => array('N', 'RV'),
            'KD' => array('RV'),
        );
        $status_appprove_str = implode('\',\'', $status_appprove[$user_level]);
        $where = '';
        //		$where .= !empty($kode_farm) ? ' and t.kode_farm =\''.$kode_farm.'\'' : '';
        $where .= !empty($user_level) ? ' and pg.grup_pegawai in (\''.implode('\',\'', $reject_user[$user_level]).'\')' : '';
        $sql = <<<SQL
		select distinct t.keterangan
			,pg.grup_pegawai reject_user
			,t.tahun
		from(
			select lks.kode_siklus
				,case
					when lks.STATUS_APPROVE = 'D' then (select count(*) from LOG_KANDANG_SIKLUS_BDY lb where lb.KODE_SIKLUS = lks.KODE_SIKLUS and lb.NO_URUT = lks.NO_URUT - 1 and lb.status_approve in ('{$status_appprove_str}'))
					else 0
					end status_reject
					,lks.keterangan
					,lks.user_buat
					,lks.NO_URUT
					,ll.tahun
				from LOG_KANDANG_SIKLUS_BDY  lks
				inner join	(
					select bdy.kode_siklus,substring(mp.PERIODE_SIKLUS,0,5) tahun,max(bdy.no_urut) urut
					from LOG_KANDANG_SIKLUS_BDY bdy
					inner join m_periode mp
						on mp.kode_siklus =  bdy.kode_siklus
					group by substring(mp.PERIODE_SIKLUS,0,5),bdy.kode_siklus
				)ll	on ll.urut = lks.NO_URUT and ll.KODE_SIKLUS = lks.KODE_SIKLUS
				 group by lks.kode_siklus
						,lks.STATUS_APPROVE
						,lks.no_urut
						,lks.keterangan
						,lks.USER_BUAT
						,ll.tahun
			)t
			inner join m_pegawai pg
				on pg.kode_pegawai = t.user_buat
			where t.status_reject > 0
		{$where}
SQL;

        return $this->db->query($sql);
    }

    public function detail_rencana_produksi($rp)
    {
        $sql = <<<SQL
		select rp.tanggal_produksi
				,irp.rencana_produksi
				,irp.total_produksi
				,irp.total_pakan_lolos
				,irp.pakan
				,mb.nama_barang
		from rencana_produksi rp
		inner join item_rencana_produksi irp
			on irp.rencana_produksi = rp.id
		inner join m_barang mb
			on mb.KODE_BARANG = irp.pakan
		where rp.id = '{$rp}'
SQL;

        return $this->db->query($sql);
    }

    public function detail_alokasi_lolos_pakan($rp, $pakan)
    {
        $sql = <<<SQL
		select rk.tanggal_kirim
				,apl.jumlah_sak
		from item_rencana_produksi irp
		inner join alokasi_hasil_produksi ahp
			on ahp.item_rencana_produksi = irp.id
		inner join ( select max(tgl_entry) tgl_entry,alokasi_hasil_produksi from alokasi_pakan_lolos_untuk_farm aplf
				  group by alokasi_hasil_produksi
			)apl_treakhir
		on apl_treakhir.alokasi_hasil_produksi = ahp.id
		inner join alokasi_pakan_lolos_untuk_farm apl
			on apl.alokasi_hasil_produksi = apl_treakhir.alokasi_hasil_produksi and apl.tgl_entry = apl_treakhir.tgl_entry
		inner join rencana_kirim rk
			on rk.id = ahp.rencana_kirim and rk.kode_pakan = irp.pakan
		where irp.rencana_produksi = '{$rp}' and irp.pakan = '{$pakan}'
			and apl.jumlah_sak > 0
		order by rk.tanggal_kirim
SQL;

        return $this->db->query($sql);
    }

    public function detail_riwayat_alokasi_lolos_pakan($tglkirim, $pakan)
    {
        $sql = <<<SQL
		select rp.id rencana_produksi
			,rp.tanggal_produksi
			,ahp.alokasi_pakan_untuk_farm
			,apl.jumlah_sak revisi
			,(select top 1 jumlah_sak from alokasi_pakan_lolos_untuk_farm af where af.alokasi_hasil_produksi = ahp.id and tgl_entry < apl_treakhir.tgl_entry) sebelumnya
	from item_rencana_produksi irp
	inner join rencana_produksi rp
		on rp.id = irp.rencana_produksi
	inner join alokasi_hasil_produksi ahp
		on ahp.item_rencana_produksi = irp.id and ahp.alokasi_pakan_untuk_farm is not null
	inner join (select max(tgl_entry) tgl_entry,alokasi_hasil_produksi from alokasi_pakan_lolos_untuk_farm aplf
			  group by alokasi_hasil_produksi
		)apl_treakhir
	on apl_treakhir.alokasi_hasil_produksi = ahp.id
	inner join alokasi_pakan_lolos_untuk_farm apl
		on apl.alokasi_hasil_produksi = apl_treakhir.alokasi_hasil_produksi and apl.tgl_entry = apl_treakhir.tgl_entry
	inner join rencana_kirim rk
		on rk.id = ahp.rencana_kirim and rk.kode_pakan = irp.pakan and rk.tanggal_kirim = '{$tglkirim}'
	where irp.pakan = '{$pakan}'
SQL;

        return $this->db->query($sql);
    }

    public function detail_sisa_lolos_pakan($rp, $pakan)
    {
        $sql = <<<SQL
		select apl.jumlah_sak
				,rk.tanggal_kirim
		from item_rencana_produksi irp
		inner join rencana_produksi rp
			on rp.id = irp.rencana_produksi
		inner join alokasi_hasil_produksi ahp
			on ahp.item_rencana_produksi = irp.id and ahp.alokasi_pakan_untuk_farm is  null
		inner join (select max(tgl_entry) tgl_entry,alokasi_hasil_produksi from alokasi_pakan_lolos_untuk_farm aplf
				  group by alokasi_hasil_produksi
			)apl_treakhir
		on apl_treakhir.alokasi_hasil_produksi = ahp.id
		inner join alokasi_pakan_lolos_untuk_farm apl
			on apl.alokasi_hasil_produksi = apl_treakhir.alokasi_hasil_produksi and apl.tgl_entry = apl_treakhir.tgl_entry
		inner join rencana_kirim rk
			on rk.id = ahp.rencana_kirim and rk.kode_pakan = irp.pakan
		where irp.rencana_produksi = '{$rp}' and irp.pakan = '{$pakan}'
SQL;

        return $this->db->query($sql);
    }

    public function get_pegawai_browse()
    {
        $kode_farm = $this->input->post('kode_farm');
        $sql = <<<QUERY
			select M_PEGAWAI.kode_pegawai,nama_pegawai from M_PEGAWAI
			inner join PEGAWAI_D on M_PEGAWAI.KODE_PEGAWAI = PEGAWAI_D.KODE_PEGAWAI and PEGAWAI_D.KODE_FARM = '$kode_farm'
			where GRUP_PEGAWAI = 'PPB' and STATUS_PEGAWAI = 'A'
			and M_PEGAWAI.kode_pegawai not in (select coalesce(pengawas1,'') from KANDANG_SIKLUS where KODE_FARM = '$kode_farm' and STATUS_SIKLUS = 'P')
			and M_PEGAWAI.kode_pegawai not in (select coalesce(pengawas2,'') from KANDANG_SIKLUS where KODE_FARM = '$kode_farm' and STATUS_SIKLUS = 'P')
QUERY;
        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_pegawai_aktivasi()
    {
        $kode_farm = $this->input->post('kode_farm');
        $tgl_docin = $this->input->post('tgl_docin');
        $sql = <<<QUERY
		select top 1 p1.NAMA_PEGAWAI as pengawas1,p2.NAMA_PEGAWAI as pengawas2,KANDANG_SIKLUS.pengawas1 as nik_pengawas1, KANDANG_SIKLUS.pengawas2 as nik_pengawas2
		from KANDANG_SIKLUS
		left join m_pegawai p1 on KANDANG_SIKLUS.pengawas1 = p1.KODE_PEGAWAI
		left join m_pegawai p2 on KANDANG_SIKLUS.pengawas2 = p2.KODE_PEGAWAI
		where KODE_FARM = '$kode_farm' and TGL_DOC_IN = '$tgl_docin'
QUERY;
        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_pegawai($nik = '')
    {
        $sql = <<<QUERY
		select * from m_pegawai where kode_pegawai = '$nik'
QUERY;
        $stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
