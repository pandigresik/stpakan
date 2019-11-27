<?php

class M_std_budidaya_bdy extends CI_Model
{
    private $dbSqlServer;

    public function __construct()
    {
        parent::__construct();
        $this->dbSqlServer = $this->load->database('default', true);
    }

    public function get_strain()
    {
        $sql = <<<QUERY
			select kode_strain, nama_strain, umur_awal_layer
			from m_strain
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_last_std($kode_strain, $kode_farm)
    {
        $sql = <<<QUERY
			select m_std.kode_std_budidaya
				   ,m_std.kode_strain
				   ,m_std.tgl_efektif
				   ,coalesce(convert(varchar(20),(select DATEADD(DAY, -1, tgl_efektif) from m_std_budidaya where kode_std_budidaya = substring(m_std.kode_std_budidaya, 0, CHARINDEX('-', m_std.kode_std_budidaya)+1)+convert(varchar(4),substring(m_std.kode_std_budidaya, (CHARINDEX('-', m_std.kode_std_budidaya)+1),2)+1))),'-') tgl_akhir
				   ,replace(convert(varchar(11),m_std.tgl_efektif,106),' ',' ') tgl_efektif_formated
				   ,coalesce(convert(varchar(20),replace(convert(varchar(11),(select DATEADD(DAY, -1, tgl_efektif) from m_std_budidaya where kode_std_budidaya = substring(m_std.kode_std_budidaya, 0, CHARINDEX('-', m_std.kode_std_budidaya)+1)+convert(varchar(4),substring(m_std.kode_std_budidaya, (CHARINDEX('-', m_std.kode_std_budidaya)+1),2)+1)),106),' ',' ')),'-') tgl_akhir_formated
				   ,mf.nama_farm
			from m_std_budidaya m_std
			left join m_farm mf on mf.kode_farm = m_std.kode_farm
			where m_std.kode_strain = '{$kode_strain}' and m_std.kode_farm in ({$kode_farm})
			order by m_std.tgl_efektif
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_masa_pertumbuhan($kode_strain)
    {
        $sql = <<<QUERY
			select b.kode_pertumbuhan, b.deskripsi, a.umur_awal, a.umur_akhir
			from masa_pertumbuhan a
			inner join m_pertumbuhan b on a.kode_pertumbuhan = b.kode_pertumbuhan
			where a.kode_strain = '{$kode_strain}'

QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_head_std($kode_std)
    {
        $sql = <<<QUERY
			select * from m_std_budidaya where kode_std_budidaya = '{$kode_std}'
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_detail_std($kode_std)
    {
        $sql = <<<QUERY
			select * from (
				select c.kode_std_budidaya, min(c.std_umur) umur_awal, max(c.std_umur) umur_akhir, c.bentuk_barang, nama_barang
				from (
				select a.kode_std_budidaya, a.std_umur, (CONVERT(varchar(10), a.kode_barang)+'*'+d.bentuk_barang) bentuk_barang, d.nama_barang
				from m_std_budidaya_d a
				left join m_barang d on a.kode_barang = d.kode_barang
				where a.kode_std_budidaya = '{$kode_std}' and a.kode_barang is not null and a.kode_barang != ''
				) c
				group by c.kode_std_budidaya, c.bentuk_barang, nama_barang
			) d
		  order by d.umur_awal
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_detail_std_budidaya($kode_std)
    {
        $sql = <<<QUERY
			select a.*, b.nama_barang
			from m_std_budidaya_d a
			left join m_barang b on a.kode_barang = b.kode_barang
			where a.kode_std_budidaya = '{$kode_std}'
			order by a.std_umur asc
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_grup_pakan()
    {
        $sql = <<<QUERY
			select c.kode_barang, c.nama_barang, c.bentuk_barang grup_barang, c.bentuk deskripsi from(
				select
			  a.nama_barang,
			  a.kode_barang,
			  (CONVERT(varchar(10), a.kode_barang)+'*'+a.bentuk_barang) bentuk_barang,
			  (a.nama_barang + ' ' + (select dbo.bentuk_convertion(a.bentuk_barang))) bentuk
				from m_barang a
				inner join m_grup_barang b on b.grup_barang = a.grup_barang
				where a.bentuk_barang is not null
			) c
			group by c.kode_barang, c.nama_barang, c.bentuk_barang, c.bentuk
			order by c.nama_barang

QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_last_std_formated($format)
    {
        $sql = <<<QUERY
			select top 1 kode_std_budidaya
			from m_std_budidaya
			where kode_std_budidaya like '{$format}%'
			order by convert(int,substring(kode_std_budidaya,CHARINDEX('-', kode_std_budidaya)+1,len(kode_std_budidaya))) desc
QUERY;

        // $sql = <<<QUERY
        // select max(kode_std_budidaya) kode_std_budidaya
        // from m_std_budidaya
        // where kode_std_budidaya like '{$format}%'
        // QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_rows_std($kode_std, $umur_awal, $umur_akhir)
    {
        $sql = <<<QUERY
			select
				kode_std_budidaya, std_umur, dh_kum_prc, dh_hr_prc, pkn_kum_std, pkn_hr_std,
				pkn_kum, pkn_hr, target_bb, kode_barang
			from m_std_budidaya_d
			where kode_std_budidaya = '{$kode_std}' and std_umur >= {$umur_awal} and std_umur <= {$umur_akhir}
			order by std_umur
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_multiple_std_budidaya($data_h, $data)
    {
        $this->dbSqlServer->trans_begin();
        $tgl_buat = $this->get_today();

        $success = 0;
        $this->dbSqlServer->insert('m_std_budidaya', $data_h);
        if ($this->dbSqlServer->affected_rows() > 0) {
            $sinkronisasi = array();
            $sinkronisasi['transaksi'] = 'master_standart_budidaya';
            $sinkronisasi['asal'] = 'FM';
            $sinkronisasi['tujuan'] = $data_h['kode_farm'];
            $sinkronisasi['aksi'] = 'PUSH';
            $sinkronisasi['tgl_buat'] = $tgl_buat['today'];

            $this->dbSqlServer->insert('sinkronisasi', $sinkronisasi);

            if ($this->dbSqlServer->affected_rows() > 0) {
                $idSinkronisasi = $this->dbSqlServer->insert_id();
                foreach ($data as $d) {
                    $this->dbSqlServer->insert('m_std_budidaya_d', $d);
                    if ($this->dbSqlServer->affected_rows() > 0) {
                        ++$success;

                        $detail_sinkronisasi = array();
                        $detail_sinkronisasi['sinkronisasi'] = $idSinkronisasi;
                        $detail_sinkronisasi['aksi'] = 'I';
                        $detail_sinkronisasi['tabel'] = 'M_STD_BUDIDAYA_D';
                        $detail_sinkronisasi['kunci'] = '{"KODE_STD_BUDIDAYA":"'.$d['kode_std_budidaya'].'","DH_KUM_PRC":"'.$d['dh_kum_prc'].'"}';
                        $detail_sinkronisasi['status_identity'] = 0;
                        $this->dbSqlServer->insert('detail_sinkronisasi', $detail_sinkronisasi);
                    }
                }

                $detail_sinkronisasi = array();
                $detail_sinkronisasi['sinkronisasi'] = $idSinkronisasi;
                $detail_sinkronisasi['aksi'] = 'I';
                $detail_sinkronisasi['tabel'] = 'M_STD_BUDIDAYA';
                $detail_sinkronisasi['kunci'] = '{"KODE_STD_BUDIDAYA":"'.$data_h['kode_std_budidaya'].'"}';
                $detail_sinkronisasi['status_identity'] = 0;
                $this->dbSqlServer->insert('detail_sinkronisasi', $detail_sinkronisasi);
            }

            if ($success == count($data)) {
                $this->dbSqlServer->trans_commit();

                return 1;
            } else {
                $this->dbSqlServer->trans_rollback();

                return 0;
            }
        } else {
            $this->dbSqlServer->trans_rollback();

            return 0;
        }
    }

    //------------------------------------

    public function get_range_detail_std($kode_std, $musim)
    {
        $sql = <<<QUERY
			select * from (
				select min(c.std_umur) umur_awal, max(c.std_umur) umur_akhir, c.deskripsi
				from (
				select a.kode_std_breeding, a.std_umur, a.masa_pertumbuhan, e.deskripsi
				from m_std_breeding a
				inner join m_grup_barang b on a.grup_barang = b.grup_barang
				left join m_barang d on a.kode_barang = d.kode_barang
				left join m_pertumbuhan e on e.kode_pertumbuhan = a.masa_pertumbuhan
				where a.kode_std_breeding = '{$kode_std}' and a.musim = '{$musim}'
				) c
				group by c.kode_std_breeding, c.masa_pertumbuhan, c.deskripsi
			) d order by d.umur_awal
QUERY;

        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_std_breeding($data, $kode, $umur)
    {
        $this->dbSqlServer->trans_begin();

        $success = 0;
        for ($i = 0; $i < count($umur); ++$i) {
            $detail = $data[$i];

            $this->dbSqlServer->where('kode_std_breeding', $kode);
            $this->dbSqlServer->where('std_umur', $umur[$i]);
            $this->dbSqlServer->update('m_std_breeding', $detail);

            if ($this->dbSqlServer->affected_rows() > 0) {
                ++$success;
            }
        }

        if ($success == count($umur)) {
            $this->dbSqlServer->trans_commit();

            return true;
        } else {
            $this->dbSqlServer->trans_rollback();

            return false;
        }
    }

    public function get_today()
    {
        $sql = <<<QUERY
		select getdate() as [today]
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
