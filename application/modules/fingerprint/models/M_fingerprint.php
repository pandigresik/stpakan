<?php

class M_fingerprint extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'fingerprint_verification';
    }

    public function getListKaryawanFinger($kode_farm)
    {
        return $this->db->distinct()->select('mp.nama_pegawai,mp.kode_pegawai,mgp.deskripsi')
                    ->join('finger_code fc', 'fc.kode_pegawai = mp.kode_pegawai')
                    ->join('pegawai_d pd', 'pd.kode_pegawai = mp.kode_pegawai and pd.kode_farm = \''.$kode_farm.'\'')
                    ->join('m_grup_pegawai mgp', 'mgp.grup_pegawai = mp.grup_pegawai')
                    ->get('m_pegawai mp');
    }

    public function simpan_transaksi_verifikasi($kode_farm, $user, $transaction, $kode_flok = '')
    {
        $query = <<<QUERY
            insert into fingerprint_verification (
                kode_farm,
                [transaction],
                date_transaction,
                [user],
                kode_flok
            )
            output left(cast(inserted.date_transaction as date),10)+' '+left(cast(inserted.date_transaction as time),12) date_transaction
            values (
                '$kode_farm',
                '$transaction',
                getdate(),
                '$user',
                '$kode_flok'
            )
QUERY;
        
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cek_verifikasi($kode_farm, $user, $date_transaction, $kode_flok = '')
    {
        $str = '';
        /* nunggu aplikasi fingernya di sesuaikan */
        if ($kode_flok != '') {
            //$str .= "INNER JOIN KANDANG_SIKLUS ks ON fv.kode_flok = ks.FLOK_BDY AND ks.KODE_FARM = '$kode_farm' AND ks.STATUS_SIKLUS = 'O' AND fv.verificator = ks.PENGAWAS1 OR fv.verificator = ks.PENGAWAS2";
        }
        $query = <<<QUERY
            select top 1
                fv.*
                , mp.KODE_PEGAWAI kode_pegawai
                , mp.NAMA_PEGAWAI nama_pegawai
                , mp.GRUP_PEGAWAI grup_pegawai
            from fingerprint_verification fv
            $str
            left join M_PEGAWAI mp
                on mp.kode_pegawai = fv.verificator
            where fv.date_transaction = '$date_transaction'
            and fv.kode_farm = '$kode_farm'
QUERY;

        return $this->db->query($query)->row_array();
    }
}
