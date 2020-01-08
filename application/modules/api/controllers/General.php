<?php

defined('BASEPATH') or exit('No direct script access allowed');

class General extends REST_Controller
{
    protected $result = array('status' => 0, 'message' => '', 'content' => '');

    public function __construct()
    {
        parent::__construct();
    }

    /** ambil nilai config dari database */
    public function config_get()
    {
        $context = $this->get('context');
        $kodefarm = $this->get('kodefarm');
        
        $this->result['content'] = $this->db->select(array('kode_config', 'value'))->where(array('kode_farm' => $kodefarm, 'context' => $context, 'status' => '1'))->get('SYS_CONFIG_GENERAL')->result_array();
        $this->result['status'] = 1;
        $this->response($this->result, 200);
    }

    /** ambil nilai config dari database */
    public function kandang_post()
    {
        $rfid = $this->post('rfid');
        if (!empty($rfid)) {
            $data = $this->db->select(array('ks.no_reg', 'ks.flok_bdy', 'ks.kode_kandang', 'mk.nama_kandang'))->join('kandang_siklus ks', 'ks.kode_kandang = mk.kode_kandang and ks.kode_farm = mk.kode_farm and ks.status_siklus = \'O\'')->where(array('kode_verifikasi' => $rfid))->get('m_kandang as mk')->row_array();
            if (!empty($data)) {
                $this->result['content'] = $data;
                $this->result['status'] = 1;
            }
        }
        $this->response($this->result, 200);
    }

    /** ambil nilai config dari database */
    public function ekspedisi_post()
    {
        $kode_farm = $this->post('kode_farm');
        $this->load->model('permintaan_pakan_v2/m_pembelian_pakan', 'mpp');
        $data = $this->mpp->list_ekspedisi($kode_farm)->result_array();
        if (!empty($data)) {
            $this->result['content'] = $data;
            $this->result['status'] = 1;
        }

        $this->response($this->result, 200);
    }

    public function setFingerprint_post()
    {
        $this->load->config('stpakan');
        $kode_farm = $this->config->item('idFarm');
        $bacaFingerAbsensi = $this->db->select(array('kode_config', 'value'))->where(array('kode_config' => '_finger_absensi', 'kode_farm' => $kode_farm, 'context' => 'fingerprint_absensi', 'status' => '1'))->get('SYS_CONFIG_GENERAL')->row_array();

        if (!$bacaFingerAbsensi['value']) {
            $this->result['status'] = 1;
            $this->response($this->result, 200);

            return;
        }

        $date_transaction = $this->post('date_transaction');
        //$rangeWaktuValid = -6; /**checktime > 6 detik dari sekarang */
        $sql = <<<SQL
        SELECT TOP 1 pa.kode_pegawai FROM attendance.dbo.CHECKINOUT ck
        JOIN attendance.dbo.USERINFO u ON u.USERID = ck.USERID
        JOIN PEGAWAI_ATTENDANCE pa ON pa.badge_number = u.badgenumber
        WHERE ck.CREATED_AT > '{$date_transaction}'  
        ORDER BY ck.CREATED_AT desc
SQL;
        $kodePegawai = $this->db->query($sql)->row();
        if ($kodePegawai) {
            $sql = <<<SQL
            update fingerprint_verification set verificator = '{$kodePegawai->kode_pegawai}', date_verification = getdate() where date_transaction = '{$date_transaction}'
SQL;
            $this->db->query($sql);
            $this->result['status'] = 1;
        }
        $this->response($this->result, 200);
    }

    /** ambil tgl_server dari database */
    public function tanggalserver_get()
    {
        $this->result['content'] = date('Y-m-d');
        $this->result['status'] = 1;
        $this->response($this->result, 200);
    }
}
