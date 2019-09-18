<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Permintaan_pakan extends MY_Controller
{
    protected $result;
    protected $_user;
    protected $_idFarm;
    private $_canCreatePP;
    private $_kgPersak;
    private $_levelApprove;
    private $_canEditKuantitas;
    private $_lockPP;
    private $_tglSekarang;
    private $grup_farm;
    private $_canReview;
    private $_showReview;

    public function __construct()
    {
        parent::__construct();
        $this->result = array('status' => 0, 'content' => '', 'message' => '');
        $this->_user = $this->session->userdata('kode_user');
        $this->load->helper('stpakan');
        $this->load->model('permintaan_pakan_v2/m_permintaan_pakan', 'mpp');

        $this->_canCreatePP = array('KF', 'P');
        $this->_canReview = array('KD' => 1);
        $this->_kgPersak = 50;
        $this->_levelApprove = array(
            'DB' => 'approve1',
            'PD' => 'approve2',
            'KD' => 'review',
        );
        $this->_editKuantitas = array(
            'P' => 0,
            'KF' => 0,
            'DB' => 0,
            'PD' => 0,
        );
        $this->_editTanggal = array(
                'P' => 0,
                'KF' => 0,
                'DB' => 1,
                'PD' => 0,
                'KD' => 1,
        );
        $this->_lockPP = array(
            'P' => array('N', 'RV', 'RJ', 'A', 'V'),
            'KF' => array('N', 'RV', 'RJ', 'A', 'V'),
            'KD' => array('RV', 'V', 'A'),
            'KDV' => array(),
        );
        $this->_showReview = array(
            'P' => array('RV', 'RJ', 'A', 'V'),
            'KF' => array('RV', 'RJ', 'A', 'V'),
            'KD' => array('N', 'RV', 'RJ', 'V', 'A'),
            'KDV' => array(),
        );

        $this->grup_farm = strtolower($this->session->userdata('grup_farm'));
    }

    public function index()
    {
    }

    public function main($farm = null)
    {
        $user_level = $this->session->userdata('level_user');
        switch ($user_level) {
            case 'P':
            case 'KF':
                $this->kepala_farm();
                break;
            case 'DB':
                $this->direktur();
                break;
            case 'KD':
                $this->kadept($farm);
                break;
            case 'KDV':
                $this->kadiv();
                break;
        }
    }

    public function kepala_farm($farm = null)
    {
        $kodefarm = (!empty($farm)) ? $farm : $this->session->userdata('kode_farm');
        $data['list_farm'] = Modules::run('forecast/forecast/list_farm', $this->grup_farm, $kodefarm);
        $this->load->view('permintaan_pakan_v2/permintaan_pakan', $data);
    }

    public function kadiv($farm = null)
    {
        $data['list_farm'] = Modules::run('forecast/forecast/list_farm', $this->grup_farm);
        $data['tombol_simpan'] = '<div class="btn btn-default" onclick="Permintaan.approveKadiv(this)">Approve</div>&nbsp;<div  onclick="Permintaan.rejectKadiv(this)" class="btn btn-default" data-aksi="reject">Reject</div>';
        $this->load->view('permintaan_pakan_v2/bdy/approvalpp', $data);
    }

    public function kadept($farm = null)
    {
        $this->load->config('stpakan');
        $data['versi_baru'] = $this->config->item('versiBaru');
        $data['list_farm'] = Modules::run('forecast/forecast/list_farm', $this->grup_farm, $farm);
        $this->load->view('permintaan_pakan_v2/permintaan_pakan', $data);
    }

    public function direktur()
    {
        $data['list_farm'] = Modules::run('forecast/forecast/list_farm', $this->grup_farm);
        $this->load->view('permintaan_pakan_v2/permintaan_pakan', $data);
    }

    public function presdir()
    {
        $data['list_farm'] = Modules::run('forecast/forecast/list_farm', $this->grup_farm);
        $this->load->view('permintaan_pakan_v2/permintaan_pakan', $data);
    }

    public function datafarm($idFarm, $kodeSiklus)
    {
        $this->session->set_userdata(array('kode_farm' => $idFarm));
        $this->session->set_userdata(array('kode_siklus' => $kodeSiklus));
        $user_level = $this->session->userdata('level_user');

        $tombol_buat = null;
        if (in_array($user_level, $this->_canCreatePP)) {
            $tombol_buat = '<div onclick="Permintaan.transaksi_pp_bdy(this,\'#for_transaksi\')" class="btn btn-primary" data-aksi="buat_pp" data-no_pp="">Buat PP</div>';
        }

        $data['list_pp'] = null; //$this->list_pp();
        $data['div_buat_pp'] = $tombol_buat;
        $data['tindak_lanjut'] = isset($this->_canReview[$user_level]) ? $this->_canReview[$user_level] : 0;
        $this->load->view('permintaan_pakan_v2/'.$this->grup_farm.'/farm', $data);
    }

    public function list_pp($return = 'html')
    {
        $user_level = $this->session->userdata('level_user');
        $idfarm = $this->session->userdata('kode_farm');
        $tindak_lanjut = $this->input->post('_tindak_lanjut');
        $tanggal_cari = $this->input->post('_tanggal');
        $no_lpb_cari = $this->input->post('_no_lpb');
        $no_flok_cari = $this->input->post('_no_flok');
        $farm_cari = $this->input->post('_farm');
        if ($user_level != 'KDV') {
            $farm_cari = !empty($farm_cari) ? $farm_cari : $idfarm;
        }
        $ada_keterangan = array(
            'bdy' => array('KD'),
        );
        /* cari semua pp yang telah dibuat */
        $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
        $pesan = 'Belum ada PP yang diajukan';
        $status_tindak_lanjut = array(
            'P' => array('D', 'N', 'RV', 'RJ', 'A', 'V'),
            'KF' => array('D', 'N', 'RV', 'RJ', 'A', 'V'),
            'KD' => array('N', 'RJ'),
            'KDV' => array('RV'),
        );
        $status_all = array(
            'P' => array('D', 'N', 'RV', 'RJ', 'A', 'V'),
            'KF' => array('D', 'N', 'RV', 'RJ', 'A', 'V'),
            'KD' => array('N', 'RV', 'RJ', 'A', 'V'),
            'KDV' => array('RV', 'A', 'RJ'),
        );
        $param = array();

        if (!empty($no_lpb_cari)) {
            $param['lpb.no_lpb'] = $no_lpb_cari;
        }

        $custom_param = array();
        if (!$tindak_lanjut) {
            if (!empty($tanggal_cari['operand'])) {
                switch ($tanggal_cari['operand']) {
                    case 'between':
                        $custom_param[] = 'cast('.$tanggal_cari['fieldname'].' as date) '.$tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\' and \''.$tanggal_cari['endDate'].'\'';
                        break;
                    case '<=':
                        $custom_param[] = 'cast('.$tanggal_cari['fieldname'].' as date) '.$tanggal_cari['operand'].' \''.$tanggal_cari['endDate'].'\'';
                        break;
                    case '>=':
                        $custom_param[] = 'cast('.$tanggal_cari['fieldname'].' as date) '.$tanggal_cari['operand'].' \''.$tanggal_cari['startDate'].'\'';
                        break;
                }
            }
        }

        if ($tindak_lanjut) {
            $custom_param[] = ' lpb.status_lpb in (\''.implode('\',\'', $status_tindak_lanjut[$user_level]).'\')';
        } else {
            $custom_param[] = ' lpb.status_lpb in (\''.implode('\',\'', $status_all[$user_level]).'\')';
        }
        $keterangan_reject = array();
        switch ($this->grup_farm) {
            case 'brd':
                if (!empty($farm_cari)) {
                    $param['lpb.kode_farm'] = $farm_cari;
                }
                $list_pp = $this->lpb->get_pp($param, $custom_param)->result_array();
                $data['ada_keterangan'] = '';
                $view_pp = 'list_pp';
                break;
            case 'bdy':
                if (!empty($farm_cari)) {
                    $param['lpb.kode_farm'] = $farm_cari;
                }
                $data['ada_keterangan'] = in_array($user_level, $ada_keterangan[$this->grup_farm]) ? 1 : 0;
                $list_pp = $this->lpb->get_pp_bdy($param, $custom_param, $no_flok_cari)->result_array();
                $view_pp = $user_level == 'KDV' ? 'list_pp_kadiv' : 'list_pp';
                break;
        }

        $data['keterangan_reject'] = $keterangan_reject;
        if (in_array($user_level, $this->_canCreatePP)) {
            $pesan = 'Belum ada PP yang dibuat';
        }
        if ($return == 'data') {
            return $list_pp;
        } else {
            $data['list_pp'] = $list_pp;
            $data['pesan'] = $pesan;
            $this->load->config('stpakan');
            $data['versi_baru'] = $this->config->item('versiBaru');
            $data['kode_list_farm'] = $this->config->item('kodeFarm');
            if ($return == 'ajax') {
                $data['pesan'] = 'Data tidak ditemukan';
                $data['lockPP'] = isset($this->_lockPP[$user_level]) ? $this->_lockPP[$user_level] : array();
                $this->load->view('permintaan_pakan_v2/'.$this->grup_farm.'/'.$view_pp, $data);
            } else {
                return $this->load->view('permintaan_pakan_v2/'.$this->grup_farm.'/'.$view_pp, $data, true);
            }
        }
    }

    public function list_pp_farm()
    {
        $this->result['status'] = 1;
        $this->result['content'] = $this->list_pp();
        echo json_encode($this->result);
    }

    public function get_last_pp($kodeflok = null)
    {
        $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
        $this->load->model('permintaan_pakan_v2/m_lpbd', 'lpbd');
        $idfarm = $this->session->userdata('kode_farm');
        /* cari apakah masih ada pp yang belum diapprove */
        $pending = $this->lpb->get_pending_pp($idfarm, $kodeflok);
        if (!empty($pending)) {
            $this->result['pp_pending'] = $pending;
        }
        $arr = $this->lpbd->get_last_pp($idfarm, $kodeflok)->row();
        if (!empty($arr->tgl_keb_akhir)) {
            $this->result['status'] = 1;
            $this->result['content'] = $arr;
        }

        echo json_encode($this->result);
    }

    public function get_last_pp_noreg()
    {
        $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
        $this->load->model('permintaan_pakan_v2/m_lpbd', 'lpbd');
        $idfarm = $this->session->userdata('kode_farm');
        $noreg = $this->input->get('noreg');

        /* cari apakah masih ada pp yang belum diapprove */
        $pending = $this->lpb->get_pending_pp_noreg($noreg);
        if (!empty($pending)) {
            $this->result['pp_pending'] = $pending;
        }
        $arr = $this->lpbd->get_last_pp_noreg($noreg)->row();
        if (!empty($arr->tgl_keb_akhir)) {
            $this->result['status'] = 1;
            $this->result['content'] = $arr;
        } else {
            /* periksa apakah sudah diapprove plottingnya */
            $this->result['ploting_pelaksana'] = '';
            $sudahApprove = $this->db->where(array('no_reg' => $noreg, 'status' => 'A'))->get('m_ploting_pelaksana')->row_array();
            if (empty($sudahApprove)) {
                $this->result['ploting_pelaksana'] = 'Ploting pelaksana belum diapprove';
            }
        }

        echo json_encode($this->result);
    }

    public function get_first_docin($kodefarm = null)
    {
        $this->load->model('forecast/m_kandang_siklus', 'ks');
        $idfarm = empty($kodefarm) ? $this->session->userdata('kode_farm') : $kodefarm;
        $param = array('kandang_siklus.kode_farm' => $idfarm, 'kandang_siklus.status_siklus' => 'O');
        $kodeflok = $this->input->get('kodeflok') || null;
        $perflok = $this->input->get('perflok');
        if (!empty($perflok)) {
            $arr = $this->ks->get_first_docin_perflok($param)->result_array();
        } else {
            $arr = $this->ks->get_first_docin($param, $kodeflok)->row_array();
        }

        if (!empty($arr)) {
            $this->result['status'] = 1;
            $this->result['content'] = $arr;
        }
        echo json_encode($this->result);
    }

    public function sisa_pakan()
    {
        $kodefarm = $this->session->userdata('kode_farm');
        $sisa_pakan = $this->mpp->sisa_pakan($kodefarm)->result_array();
        /* grouping berdasarkan no_reg, kode_barang dan jenis_kelamin */
        $result = array();
        if (!empty($sisa_pakan)) {
            foreach ($sisa_pakan as $sisa) {
                if (!isset($result[$sisa['no_reg']])) {
                    $result[$sisa['no_reg']] = array();
                }
                if (!isset($result[$sisa['no_reg']][$sisa['kode_barang']])) {
                    $result[$sisa['no_reg']][$sisa['kode_barang']] = array();
                }
                $result[$sisa['no_reg']][$sisa['kode_barang']][$sisa['jenis_kelamin']] = $sisa['sisa_pakan'];
            }
        }

        return $result;
    }

    private function mapping_obj($arr)
    {
        $tmp = array();
        $convert = array(
            'tk' => 'tgl_kebutuhan',
            'kb' => 'kode_barang',
            'nr' => 'no_reg',
            'jf' => 'jml_forecast',
            'jp' => 'jml_performance',
            'jo' => 'jml_order',
            'jk' => 'jenis_kelamin',
            'od' => 'detail_order',
            'rh' => 'tgl_lhk',
            'k' => 'keterangan',
            'oa' => 'jml_order_tanpa_pembulatan',
            'mp' => 'pengurang_pp',
            'kp' => 'komposisi_pakan',
        );
        foreach ($arr as $i => $d) {
            $tmp[$i] = array();
            foreach ($d as $k => $v) {
                $t = array();
                foreach ($v as $j => $l) {
                    $t[$convert[$j]] = $l;
                }
                array_push($tmp[$i], $t);
            }
        }

        return $tmp;
    }

    public function simpan_pp()
    {
        $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
        $tglServer = Modules::run('home/home/getDateServer');
        $this->_tglSekarang = $tglServer->saatini;
        $kodefarm = $this->session->userdata('kode_farm');
        $kodeSiklus = $this->session->userdata('kode_siklus');
        $no_pp = $this->input->post('no_pp');
        $dataHeader = $this->input->post('_dh');
        $dataDetail = $this->mapping_obj(array($this->input->post('_dd')));
        $grup_farm = $this->input->post('_gf');
        $review = $this->input->post('review');
        $statusLama = $this->input->post('_sl');
        $statusLpb = $this->input->post('statusLpb');
        $autoApprove = $this->input->post('autoApprove');
        $kirimSMS = 0;
        $createOP = 0;
        $this->db->trans_begin();
        if (empty($no_pp)) {
            /* buat no_pp baru, insert ke table lpb */
            $result = $this->lpb->simpan($kodefarm, $kodeSiklus, $this->_user, $this->_tglSekarang, $statusLpb);
            $no_pp = $result['no_lpb'];
        } else {
            if ($statusLama == 'RJ') {
                $ref_id = $no_pp;
                $this->result['ref_id'] = $ref_id; // untuk proses sinkronisasi
                $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
                $result = $this->lpb->simpan($kodefarm, $kodeSiklus, $this->_user, $this->_tglSekarang, $statusLpb, $ref_id);
                $no_pp = $result['no_lpb'];
                /** update status ref pp menjadi V */
                $dataUpdate = array(
                    'status_lpb' => 'V',
                );
                /* jika kuantitas = 0, maka status_lpb otomatis menjadi A */
                $summaryKuantitasPP = $this->cekKuantitasPP($dataDetail);
                $cekKuantitasPP = $summaryKuantitasPP['jmlPP'];
                $cekJumlahKodeBarang = $summaryKuantitasPP['jmlKodeBarang'];

                $autoApprove = $this->isAutoApprove($cekKuantitasPP, $cekJumlahKodeBarang, $autoApprove);

                if ($autoApprove) {
                    $_dataBaru['status_lpb'] = 'A';
                    $_dataBaru['tgl_approve1'] = $this->_tglSekarang;
                    $kirimSMS = 1;
                    $this->lpb->update($no_pp, $_dataBaru);
                    if ($cekKuantitasPP > 0) {
                        $createOP = 1;
                        $kirimSMS = 0; /* dikirimkan sms ketika generate op */
                    }
                }
                $this->lpb->update($ref_id, $dataUpdate);
            } else {
                /** update data lpb berdasarkan */
                $dataUpdate = array(
                    'status_lpb' => $statusLpb,
                );
                if ($statusLpb == 'N') {
                    $dataUpdate['tgl_rilis'] = $this->_tglSekarang;
                }
                if ($statusLpb == 'RV') {
                    /** mencegah agar tidak dobel, bisa jadi karena buka 2 tab */
                    $cekStatusPPSebelumReview = $this->db->where(array('no_lpb' => $no_pp))->where_in('status_lpb', array('N'))->get('lpb')->row();
                    if (empty($cekStatusPPSebelumReview)) {
                        $this->result['message'] = 'PP sudah tidak bisa direview, status saat ini sudah bukan Rilis';
                        $this->output
                            ->set_content_type('application/json')
                            ->set_output(json_encode($this->result));

                        return;
                    }
                    $dataUpdate['user_ubah'] = $this->_user;
                    $dataUpdate['tgl_ubah'] = $this->_tglSekarang;

                    /* jika kuantitas = 0, maka status_lpb otomatis menjadi A */
                    $summaryKuantitasPP = $this->cekKuantitasPP($dataDetail);
                    $cekKuantitasPP = $summaryKuantitasPP['jmlPP'];
                    $cekJumlahKodeBarang = $summaryKuantitasPP['jmlKodeBarang'];

                    $autoApprove = $this->isAutoApprove($cekKuantitasPP, $cekJumlahKodeBarang, $autoApprove);

                    if ($autoApprove) {
                        $dataUpdate['status_lpb'] = 'A';
                        $dataUpdate['tgl_approve1'] = $this->_tglSekarang;
                        $kirimSMS = 1;
                        if ($cekKuantitasPP > 0) {
                            $createOP = 1;
                            $kirimSMS = 0; /* dikirimkan sms ketika generate op */
                        }
                    }
                }
                $this->lpb->update($no_pp, $dataUpdate);
                /* hapus detailnya saja */
                $this->_hapusDetail($no_pp);
            }
        }
        /* insert detailnya */
        $this->_simpanDetail($no_pp, $dataHeader, $dataDetail, $review, $kodefarm, $statusLpb, $autoApprove);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
            $this->result['status'] = 1;
            $this->result['content'] = $no_pp;
            $message = array(
                    'D' => 'Pengajuan permintaan pakan berhasil disimpan',
                    'N' => 'Pengajuan permintaan pakan dengan NoPP : '.$no_pp.' berhasil dirilis',
                    'RV' => 'Permintaan kebutuhan pakan dengan NoPP : '.$no_pp.' berhasil direview',
            );
            $this->result['message'] = $message[$statusLpb];
            $lockpp = 1;
            $user_level = $this->session->userdata('level_user');
            if (isset($this->_lockPP[$user_level])) {
                $lockpp = in_array($statusLpb, $this->_lockPP[$user_level]) ? 1 : 0;
            }
            $this->result['lockpp'] = $lockpp;
            if ($kirimSMS) {
                $this->load->config('stpakan');
                $smsDO = $this->config->item('smsDO');
                $namaFarm = $this->config->item('namaFarm');
                $kandang = substr($review[0]['no_reg'], -2);
                $tgl_kirim = tglIndonesia($dataHeader['tgl_kirim'], '-', ' ');
                /** kirim sms ke kafarm */
                $pesan = <<<SQL
		KaFarm {$namaFarm[$kodefarm]} Yth, PP kandang {$kandang} untuk tgl kirim {$tgl_kirim} telah di-approve. PP selanjutnya dapat diajukan. 
SQL;

                $nomerTelpFarm = isset($smsDO[$kodefarm]) ? $smsDO[$kodefarm] : array();
                $nomer = $nomerTelpFarm;
                if (!empty($nomer)) {
                    Modules::run('client/csms/sendNotifikasi', $pesan, $nomer);
                }
            }

            $this->result['createop'] = $createOP;
        }
        //echo json_encode($this->result);
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($this->result));
    }

    private function cekKuantitasPP($dataDetail)
    {
        $jmlPP = 0;
        $jenisBarang = array();
        foreach ($dataDetail[0] as $baris) {
            $kodeBarang = $baris['kode_barang'];
            if (!isset($jenisBarang[$kodeBarang])) {
                $jenisBarang[$kodeBarang] = 1;
            }
            $jmlPP += $baris['jml_order'];
        }

        return array('jmlPP' => $jmlPP, 'jmlKodeBarang' => count($jenisBarang));
    }

    private function isAutoApprove($cekKuantitasPP, $cekJumlahKodeBarang, $autoApprove = 0)
    {
        /* jika jenis_pakan > 1, maka cancel autoApprove */
        if ($cekJumlahKodeBarang > 1) {
            $autoApprove = 0;
        }

        if ($cekKuantitasPP <= 0) {
            $autoApprove = 1;
        }

        return $autoApprove;
    }

    private function _simpanDetail($no_pp, $dataHeader, $dataDetail, $review, $kodefarm, $statusLpb, $autoApprove)
    {
        /* dataHeader disimpan di lpb_d */
        $this->load->model('permintaan_pakan_v2/m_lpbd', 'lpbd');
        $dataHeader['no_lpb'] = $no_pp;
        $dataHeader['kode_farm'] = $kodefarm;
        $this->lpbd->insert($dataHeader);
        /* dataDetail disimpan di lpb_e */
        /* update nilai dari kode_farm,no_lpb,berat_forecast,berat_performance,berat_order */
        $this->load->model('permintaan_pakan_v2/m_lpbe', 'lpbe');
        foreach ($dataDetail[0] as $baris) {
            $baris['kode_farm'] = $kodefarm;
            $baris['kode_barang'] = $baris['kode_barang'];
            $baris['no_lpb'] = $no_pp;
            $baris['berat_forecast'] = $baris['jml_forecast'] * $this->_kgPersak;
            $baris['jml_performance'] = $baris['jml_forecast'];
            $baris['berat_performance'] = $baris['jml_forecast'] * $this->_kgPersak;
            $baris['berat_order'] = $baris['jml_order'] * $this->_kgPersak;
            $baris['tgl_kirim'] = $dataHeader['tgl_kirim'];

            $this->lpbe->insert($baris);
        }
        /*insert review */
        foreach ($review as $rv) {
            $rv['no_lpb'] = $no_pp;
            if ($statusLpb == 'RV') {
                $rv['user_review'] = $this->_user;
                $rv['tgl_review'] = $this->_tglSekarang;
                if ($autoApprove) {
                    $rv['user_reject'] = $this->_user;
                }
            }
            $this->simpan_review($rv);
        }
    }

    private function _hapusDetail($no_pp)
    {
        $this->load->model('permintaan_pakan_v2/m_lpbd', 'lpbd');
        $this->load->model('permintaan_pakan_v2/m_lpbe', 'lpbe');
        $this->load->model('permintaan_pakan_v2/m_review_lpb', 'review');

        $this->review->delete_by(array('no_lpb' => $no_pp));
        $this->lpbe->delete(array('no_lpb' => $no_pp));
        $this->lpbd->delete(array('no_lpb' => $no_pp));
    }

    public function approve_pp_budidaya()
    {
        $tglServer = Modules::run('home/home/getDateServer');
        $this->_tglSekarang = $tglServer->saatini;

        $list_pp = $this->input->post('no_pp');
        $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
        $this->load->config('stpakan');
        $smsDO = $this->config->item('smsDO');
        $namaFarm = $this->config->item('namaFarm');
        $approved_pp = array();
        foreach ($list_pp as $no_pp) {
            /** kalau pakai trans_begin gagal terus, saling nunggu prosesnya */
            //	$this->db->trans_begin();
            $kodefarm = $this->getKodePP($no_pp, '/', 2);
            $no_op_pakai = $this->mpp->no_op_terakhir_bdy($kodefarm, $no_pp)->row_array();
            if (empty($no_op_pakai)) {
                $no_op_ditemukan = false;
                $this->result['message'] = 'no op yang tersedia tidak ditemukan';
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode($this->result));

                return;
            } else {
                /** apapun yang terjadi tetep isi user reject meskipun status approve */
                $rv = array();
                $rv['user_reject'] = $this->_user;
                $rv['no_lpb'] = $no_pp;
                $this->reject_review($rv);

                $data = array(
                    'status_lpb' => 'A',
                    'tgl_approve1' => $this->_tglSekarang,
                );
                $this->lpb->update($no_pp, $data);
                /* setelah diapprove, maka OP langsung terbentuk */
                $no_op_baru = $this->mpp->approve_pp_budidaya($no_pp, 'bdy', $this->_user)->row();
                /* kirim email ke logistik
                if(Modules::run('client/email/email_op_budidaya',$no_pp,true)){
                    $this->result['email'] = array('status'=> 1,'message'=>'Email berhasil dikirim ke logistik');
                }
                else{
                    $this->result['email'] = array('status'=> 0,'content'=>$no_op_baru->op_logistik,'message'=>'Email gagal dikirim ke logistik');
                }*/
                $infoKandang = $this->db->select(array('no_reg', 'tgl_kirim'))->where(array('no_lpb' => $no_pp))->get('lpb_e')->row_array();
                $kandang = substr($infoKandang['no_reg'], -2);
                $tgl_kirim = tglIndonesia($infoKandang['tgl_kirim'], '-', ' ');
                /** kirim sms ke kafarm */
                $pesan = <<<SQL
		KaFarm {$namaFarm[$kodefarm]} Yth, PP kandang {$kandang} untuk tgl kirim {$tgl_kirim} telah di-approve.  PP selanjutnya dapat diajukan. 
SQL;

                $nomerTelpFarm = isset($smsDO[$kodefarm]) ? $smsDO[$kodefarm] : array();
                $nomer = $nomerTelpFarm;
                if (!empty($nomer)) {
                    Modules::run('client/csms/sendNotifikasi', $pesan, $nomer);
                }
            }
            array_push($approved_pp, $no_pp);
            /*
            if ($this->db->trans_status() === FALSE )
            {
                $this->db->trans_rollback();
            }else{
                $this->db->trans_commit();
                $this->result['status'] = 1;
                array_push($approved_pp,$no_pp);
            }*/
        }
        $this->result['status'] = 1;
        $this->result['message'] = 'Pengajuan permintaan pakan berhasil diapprove, dengan rincian no. PP sebagai berikut : <br /><div>'.implode('</div><div>', $approved_pp).'</div>';
        $this->output
        ->set_content_type('application/json')
        ->set_output(json_encode($this->result));
    }

    public function reject_pp_kadiv()
    {
        $tglServer = Modules::run('home/home/getDateServer');
        $this->_tglSekarang = $tglServer->saatini;
        /** nomer PP berupa array */
        $no_pp = $this->input->post('no_pp');
        $ket = $this->input->post('ket');
        $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
        $this->db->trans_begin();
        $data = array(
                'status_lpb' => 'RJ',
        );
        $this->db->where_in('no_lpb', $no_pp)->update('lpb', $data);

        $rv = array();
        $rv['user_reject'] = $this->_user;
        $rv['tgl_reject'] = $this->_tglSekarang;
        $rv['ket_reject'] = $ket;
        $rv['no_lpb'] = $no_pp;
        $this->reject_review($rv);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
            $this->result['status'] = 1;
            $this->result['content'] = convertElemenTglWaktuIndonesia($this->_tglSekarang);
            $this->result['message'] = 'Pengajuan permintaan pakan berhasil di-tolak, dengan rincian no. PP sebagai berikut : <br /><div>'.implode('</div><div>', $no_pp).'</div>';
        }
        //	echo json_encode($this->result);
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($this->result));
    }

    public function transaksi_pp($no_lpb = null, $status_pp = null, $no_flok_pp = null)
    {
        $kodefarm = $this->session->userdata('kode_farm');
        $no_pp = $this->input->post('no_pp');
        $status = $this->input->post('status');
        $no_flok = $this->input->post('no_flok');

        $no_pp = !empty($no_pp) ? $no_pp : $no_lpb;
        $status = !empty($status) ? $status : $status_pp;
        $no_flok = !empty($no_flok) ? $no_flok : $no_flok_pp;

        $user_level = $this->session->userdata('level_user');
        $tambah_pengiriman = 0;
        $tombol_ubah_tanggal = '';
        $tombol_simpan = '';
        //	$first_doc_in = '';
        $pp_awal = 0;
        $lock_pp = 0;
        if (!in_array($user_level, $this->_canCreatePP)) {
            if ($status == 'N' || $status == 'RJ') {
                if (isset($this->_canReview[$user_level]) && $this->_canReview[$user_level]) {
                    $tombol_simpan = '<div class="btn btn-default" data-aksi="'.$this->_levelApprove[$user_level].'">Ubah PP</div>';
                }
            }
        } else {
            if (!empty($status)) {
                if ($status == 'D') {
                    $tombol_simpan = '<div class="btn btn-default" data-aksi="simpan">Simpan Draft</div>&nbsp;<div class="btn btn-default" data-aksi="rilis">Rilis</div>';
                }
            } else {
                $tombol_simpan = '<div class="btn btn-default" data-aksi="simpan">Simpan Draft</div>&nbsp;<div class="btn btn-default" data-aksi="rilis">Rilis</div>';
            }
        }
        $data_pp = $header_pp = array();
        if (!empty($no_pp)) {
            $data_pp = $this->mpp->list_pp($no_pp)->row_array();
            $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
            $header_pp = $this->lpb->get(array('no_lpb' => $no_pp))->row_array();
            if (isset($this->_canReview[$user_level]) && $this->_canReview[$user_level]) {
                if ($header_pp['STATUS_LPB'] == 'RJ') {
                    $header_pp['REF_ID'] = $no_pp;
                    $header_pp['NO_LPB'] = '';
                }
            }

            // cari doc_in_pertama kali
            /*
                $this->load->model('forecast/m_kandang_siklus','ks');
                $idfarm = $header_pp['KODE_FARM'];
                $param = array('kandang_siklus.kode_farm'=>$idfarm,'status_siklus'=>'O');
                $arr = $this->ks->get_first_docin($param,$no_flok)->row();
                $first_doc_in = $arr->tgl_doc_in;
            */
            $no_reg_sql = $this->db->select('no_reg')->where(array('no_lpb' => $no_pp))->get_compiled_select('lpb_e');
            $data['kandang'] = $this->db->select(array('flok_bdy', 'no_reg', 'kode_kandang'))->where('no_reg in ('.$no_reg_sql.')')->where(array('kode_farm' => $kodefarm, 'status_siklus' => 'O'))->get('kandang_siklus')->result_array();
            $data['flok'] = !empty($data['kandang']) ? $data['kandang'][0]['flok_bdy'] : '';
        } else {
            $data['flok'] = '';
            $data['kandang'] = $this->db->select(array('flok_bdy', 'no_reg', 'kode_kandang'))->where(array('kode_farm' => $kodefarm, 'status_siklus' => 'O'))->get('kandang_siklus')->result_array();
            $header_pp['TGL_BUAT'] = date('Y-m-d');
        }
        if ($lock_pp) {
            $tombol_simpan = '';
            $tombol_ubah_tanggal = '';
        }
        /* jika grup_farm = bdy maka cari jumlah flock */

        $data['lock_pp'] = $lock_pp;
        $data['editReview'] = isset($this->_canReview[$user_level]) ? $this->_canReview[$user_level] : 0;
        $data['tombol_ubah_tanggal'] = $tombol_ubah_tanggal;
        $data['pp'] = $header_pp;
        $data['pp_awal'] = $pp_awal;
        $data['data_pp'] = $data_pp;
        $data['div_tombol_simpan'] = $tombol_simpan;
        $data['status_keterangan'] = !$lock_pp ? '' : 'readonly';
        $data['grup_farm'] = $this->grup_farm;
        $data['status'] = $status;

        $this->load->view('permintaan_pakan_v2/'.$this->grup_farm.'/transaksi', $data);
    }

    public function list_kebutuhan_pakan()
    {
        $grup_farm = $this->input->post('_grup_farm');
        switch ($grup_farm) {
            case 'brd':
                $this->list_kebutuhan_pakan_brd();
                break;
            case 'bdy':
                $this->list_kebutuhan_pakan_bdy();
                break;
        }
    }

    private function list_kebutuhan_pakan_bdy()
    {
        /* yang bisa edit review */
        $editRekomendasi = array(
            'P' => array('', 'D'),
            'KF' => array('', 'D'),
        );
        /* hanya kadept yang bisa mengubah review */
        $editReview = array(
            'KD' => array('RJ', 'N'),
            'KF' => array(),
            'KDV' => array(),
        );

        $kirim = $this->input->post('tgl_kirim');
        $awal = $this->input->post('tgl_keb_awal');
        $akhir = $this->input->post('tgl_keb_akhir');
        $no_lpb = $this->input->post('no_lpb');
        $no_reg = $this->input->post('no_reg');
        $keb_akhir_lama = $this->input->post('keb_akhir_lama');
        $lock_pp = 0;
        $new = $this->input->post('_new');
        $grup_farm = $this->input->post('_grup_farm');
        $flock = $this->input->post('_flock');
        $status = $this->input->post('status');
        $user_level = $this->session->userdata('level_user');
        if (!empty($status)) {
            $statusLock = $this->_lockPP[$user_level];
            if (in_array($status, $statusLock)) {
                $lock_pp = 1;
            }
        }
        /* jika no_reg kosong dan no_lpb ada, maka ambil no_reg dari lpb saja */
        if (!empty($no_lpb)) {
            if (empty($no_reg)) {
                $this->load->model('permintaan_pakan_v2/m_lpbe', 'lpbe');
                $_noregTmp = $this->lpbe->get(array('no_lpb' => $no_lpb))->row_array();
                $no_reg = $_noregTmp['NO_REG'];
            }
        }
        $show_review = 0;
        $review = array();
        /* cari juga sisa pakan yang masih ada difarm sebagai pengurang total yang bisa diminta */
        $kode_farm = substr($no_reg, 0, 2);
        $sisa_konsumsi_pakan = $this->mpp->sisa_konsumsi_pakan($kode_farm, $no_lpb, $no_reg);

        /* grouping berdasarkan kodekandang dan jeniskelamin */
        $tmp_sisa_konsumsi = array();
        $info_pp = array();
        $sisa_konsumsi_jeniskelamin = array(); /* jumlah total perjenis kelamin per kandang tidak per jenis barang */
        if (!empty($sisa_konsumsi_pakan)) {
            foreach ($sisa_konsumsi_pakan as $sp) {
                $kode_barang = $sp['kode_barang'];

                if (!isset($tmp_sisa_konsumsi[$no_reg])) {
                    $tmp_sisa_konsumsi[$no_reg] = array();
                }

                $tmp_sisa_konsumsi[$no_reg][$kode_barang] = array(
                        'sisa_konsumsi' => $sp['sisa_konsumsi'],
                        'sisa_kandang' => $sp['sisa_kandang'],
                        'sisa_gudang' => $sp['sisa_gudang'],
                        'nama_barang' => $sp['nama_barang'],
                        'hutang_pp_sebelumnya' => $sp['hutang_pp_sebelumnya'],
                        'hutang_retur_sak' => $sp['hutang_retur_sak'],
                        'pengurang_pp' => $sp['pengurang_pp'],
                );
            }
        } else {
            $listPakanBudget = $this->mpp->budgetPakanNoReg($no_reg)->result_array();
            foreach ($listPakanBudget as $sp) {
                $kode_barang = $sp['kode_barang'];
                if (!isset($tmp_sisa_konsumsi[$no_reg])) {
                    $tmp_sisa_konsumsi[$no_reg] = array();
                }
                $tmp_sisa_konsumsi[$no_reg][$kode_barang] = array(
                    'sisa_konsumsi' => 0,
                    'sisa_kandang' => 0,
                    'sisa_gudang' => 0,
                    'nama_barang' => $sp['nama_barang'],
                    'hutang_pp_sebelumnya' => 0,
                    'hutang_retur_sak' => 0,
                    'pengurang_pp' => 0,
                );
            }
        }

        $sisa_pakan = $tmp_sisa_konsumsi;
        if (!empty($no_lpb)) {
            /* cari data review */
            $this->load->model('permintaan_pakan_v2/m_review_lpb', 'rl');
            $review = $this->grouping_review($this->rl->get_many_by(array('no_lpb' => $no_lpb)));
        }
        if ($lock_pp) {
            $arr = $this->mpp->list_kebutuhan_pakan_bdy_approve($no_lpb);
        } else {
            if (!empty($no_lpb)) {
                if ($keb_akhir_lama == $akhir) {
                    $arr = $this->mpp->list_kebutuhan_pakan_bdy_approve($no_lpb);
                } else {
                    $arr = $this->mpp->list_kebutuhan_pakan_bdy($kode_farm, $no_reg, $awal, $akhir);
                }
            } else {
                $arr = $this->mpp->list_kebutuhan_pakan_bdy($kode_farm, $no_reg, $awal, $akhir);
            }
        }
        $summary_perpakan = array();
        $urut_pakan = array();
        $tmp = array();
        $view_resume = $this->view_kebutuhan_internal($arr, $sisa_pakan, $lock_pp);

        $tmp = $view_resume['view'];
        $summary_perpakan = $view_resume['summary'];
        $data['statusLpb'] = $status;
        $editRekomendasi = isset($editRekomendasi[$user_level]) ? ((in_array($status, $editRekomendasi[$user_level])) ? 1 : 0) : 0;
        $editReview = isset($editReview[$user_level]) ? ((in_array($status, $editReview[$user_level]) && !$lock_pp) ? 1 : 0) : 0;
        /* jika ganti pakan di set maka dianggap baru saja */
        $data['_new'] = $new;

        switch ($user_level) {
            case 'KDV':
                $data = array(
                    'kebutuhan_pakan' => '',
                    'lock_pp' => 1,
                );
                $data['kebutuhan_pakan'] = $this->load->view('permintaan_pakan_v2/'.$grup_farm.'/kebutuhan_pakan', array(
                    'kebutuhan_pakan' => $tmp,
                    'edit_rekomendasi' => $editRekomendasi,
                    'edit_review' => $editReview,
                    'review' => $review,
                    'show_review' => 1,
                ), true);
                $kebutuhan_internal = $this->load->view('permintaan_pakan_v2/'.$grup_farm.'/permintaan_kebutuhan_internal', $data, true);
                break;
            default:
                $data = array(
                    'kebutuhan_pakan' => '',
                    'lock_pp' => $lock_pp,
                );
                if (!empty($tmp)) {
                    $data['kebutuhan_pakan'] = $this->load->view('permintaan_pakan_v2/'.$grup_farm.'/kebutuhan_pakan',array(
                            'kebutuhan_pakan' => $tmp,
                            'edit_rekomendasi' => $editRekomendasi,
                            'edit_review' => $editReview,
                            'review' => $review,
                            'show_review' => isset($this->_showReview[$user_level]) ? in_array($status, $this->_showReview[$user_level]) : 0,
                    ),
                    true);
                }

                $kebutuhan_internal = $this->load->view('permintaan_pakan_v2/'.$grup_farm.'/permintaan_kebutuhan_internal', $data, true);
        }

        echo json_encode(array(
                'status' => 1,
                'content' => array('kebutuhan_internal' => $kebutuhan_internal),
            )
        );
    }

    public function getSisaBudget()
    {
        $result = array();
        $no_reg = $this->input->get('no_reg');
        $info_pp = arr2DToarrKey($this->mpp->realisasi_pp_noreg($no_reg)->result_array(), 'kode_barang');
        $budgetPakan = $this->mpp->budgetPakanNoReg($no_reg)->result_array();
        $data_sisa_pakan = array(
            'budget' => $budgetPakan,
            'info_pp' => $info_pp,
        );
        if (!empty($budgetPakan)) {
            foreach ($budgetPakan as $b) {
                $kb = $b['kode_barang'];
                $jmlPP = isset($info_pp[$kb]) ? $info_pp[$kb]['kuantitas'] : 0;
                $sisaBudget = $b['budget'] - $jmlPP;
                $result[$kb] = $sisaBudget < 0 ? 0 : $sisaBudget;
            }
        }
        $this->result['status'] = 1;
        $this->result['content'] = $result;
        echo json_encode($this->result);
    }

    public function historyInfoPP()
    {
        $no_reg = $this->input->get('no_reg');
        $tgl_docin = $this->input->get('tgldocin');
        $no_lpb = $this->input->get('no_lpb');
        $tgl_rilis_pp = null;
        if (!empty($no_lpb)) {
            $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
            $tmp_pp = $this->lpb->get(array('no_lpb' => $no_lpb))->row_array();
            $tgl_rilis_pp = $tmp_pp['TGL_RILIS'];
        }
        $info_pp = arr2DToarrKey($this->mpp->realisasi_pp_noreg($no_reg, $tgl_rilis_pp)->result_array(), 'kode_barang');
        $budgetPakan = arr2DToarrKey($this->mpp->budgetPakanNoReg($no_reg)->result_array(), 'kode_barang');
        $data_sisa_pakan = array(
            'budget' => $budgetPakan,
            'info_pp' => $info_pp,
        );

        $budget_pakan = $this->load->view('permintaan_pakan_v2/bdy/permintaan_sisa_pakan', $data_sisa_pakan, true);
        $this->load->model('permintaan_pakan_v2/m_lpbe', 'lpbe');
        $ppLama = $this->lpbe->riwayat_pp($no_reg, 3, $tgl_rilis_pp);

        /** grouping berdasarkan tgl_kebutuhan */
        $data_riwayat = array(
            'pplama' => array_reverse($ppLama),
        );
        $data_performa = array(
            'lhks' => $this->dataLHK($tgl_docin, $no_reg, $tgl_rilis_pp),
            'noreg' => $no_reg,
        );
        $riwayat_pp = $this->load->view('permintaan_pakan_v2/bdy/riwayat_pp', $data_riwayat, true);
        $performa_kandang = $this->load->view('permintaan_pakan_v2/bdy/performa_kandang', $data_performa, true);
        echo json_encode(array(
            'status' => 1,
            'content' => array('budget_pakan' => $budget_pakan, 'performa_kandang' => $performa_kandang, 'riwayat_pp' => $riwayat_pp),
            )
        );
    }

    public function performaKandang()
    {
        $no_lpb = $this->input->get('no_lpb');
        $no_reg = $this->input->get('no_reg');
        $tgl_docin = $this->input->get('tgldocin');
        /** cari no_reg dan tgldocin */
        $no_reg_sql = $this->db->select('no_reg')->where(array('no_lpb' => $no_lpb))->get_compiled_select('lpb_e');
        $this->load->model('permintaan_pakan_v2/m_lpb', 'lpb');
        $tmp_pp = $this->lpb->get(array('no_lpb' => $no_lpb))->row_array();
        $tgl_rilis_pp = $tmp_pp['TGL_RILIS'];
        $dataKandang = $this->db->select(array('no_reg', 'tgl_doc_in'))->where('no_reg in ('.$no_reg_sql.')')->get('kandang_siklus')->row_array();
        $data_performa = array(
            'lhks' => $this->dataLHK($dataKandang['tgl_doc_in'], $dataKandang['no_reg'], $tgl_rilis_pp),
            'noreg' => $dataKandang['no_reg'],
        );
        $performa_kandang = $this->load->view('permintaan_pakan_v2/bdy/performa_kandang', $data_performa, true);
        echo json_encode(array(
            'status' => 1,
            'content' => array('performa_kandang' => $performa_kandang),
            )
        );
    }

    private function dataLHK($tgl_docin, $noreg, $tgl_rilis_pp = null, $jmlBaris = 3)
    {
        $this->load->model('report/m_report', 'report');
        $this->load->model('permintaan_pakan_v2/m_lpbe', 'lpbe');
        $rhk_sql = $this->db->select('datediff(day,\''.$tgl_docin.'\',tgl_transaksi) as umur', false)
                        ->select(array('tgl_transaksi', 'c_jumlah', 'c_berat_badan', 'c_awal', 'c_afkir', 'c_mati'))
                        ->order_by('tgl_transaksi')
                        ->where(array('no_reg' => $noreg))
                        ;
        if (!empty($tgl_rilis_pp)) {
            $rhk_sql->where('rhk.tgl_buat < \''.$tgl_rilis_pp.'\'');
        }
        $rhk = $rhk_sql->get('rhk')->result_array();
        $result = array();
        $gr_pakan = 0;

        if (!empty($rhk)) {
            $bb_awal = $this->db->select('bb_rata2/1000 as berat_badan', false)->where(array('no_reg' => $noreg))->get('bap_doc')->row_array();
            $pakai_pakan = arr2DToarrKey($this->lpbe->rhk_pakan($noreg), 'tgl_transaksi');
            $berat_pakai = arr2DToarrKey($this->db->select('tgl_transaksi')->select('sum(brt_pakai) as brt_pakai', false)->where(array('no_reg' => $noreg))->group_by('tgl_transaksi')->get('rhk_pakan')->result_array(), 'tgl_transaksi');
            $panen = arr2DToarrKey($this->db->select('tgl_panen')->select('sum(jumlah_aktual) jml_panen')->where(array('no_reg' => $noreg))->group_by('tgl_panen')->get('realisasi_panen')->result_array(), 'tgl_panen');
            /** umur 7 itu pada index ke 6 */
            $tmp_stok_sebelum_umur7 = isset($rhk[6]) ? $rhk[6]['c_awal'] : $rhk[0]['c_awal'];
            $tmp_populasi_awal = $tmp_stok_sebelum_umur7;
            $bbLalu = array(
                'berat_badan' => !empty($bb_awal['berat_badan']) ? $bb_awal['berat_badan'] : 0,
                'hari' => '0',
            );
            $bbStandartLalu = 0;
            foreach ($rhk as $r) {
                $jmlPanen = isset($panen[$r['tgl_transaksi']]) ? $panen[$r['tgl_transaksi']]['jml_panen'] : 0;
                if ($r['umur'] > 7) {
                    $tmp_stok_sebelum_umur7 -= $r['c_afkir'];
                }

                $tmp_stok_sebelum_umur7 -= ($r['c_mati'] + $jmlPanen);
                $dh = round((($tmp_stok_sebelum_umur7) / $tmp_populasi_awal), 4) * 100;
                $jml = $r['c_jumlah'];
                $umur = $r['umur'];
                $bb_rata = $r['c_berat_badan'];
                $brt_pakai = isset($berat_pakai[$r['tgl_transaksi']]) ? $berat_pakai[$r['tgl_transaksi']]['brt_pakai'] * 1000 : 0;
                $gr_pakan += $brt_pakai;
                $fcr = FCR(hitungFCR($jml, $bb_rata, $gr_pakan));
                $data_bb = array(
                    'berat_badan' => $bb_rata,
                    'hari' => $umur,
                );

                $adg = hitungADG($data_bb, $bbLalu);
                $ip = hitungIP($dh, $bb_rata, round($fcr, 3), $umur);
                $tmp = array(
                    'tgl_transaksi' => $r['tgl_transaksi'],
                    'umur' => $umur,
                    'jumlah' => $tmp_stok_sebelum_umur7,
                    'bb' => $bb_rata * 1000,
                    'pakai' => isset($pakai_pakan[$r['tgl_transaksi']]) ? $pakai_pakan[$r['tgl_transaksi']] : 0,
                    'dh' => $dh,
                    'fcr' => round($fcr, 3),
                    'adg' => $adg,
                    'ip' => $ip,
                );
                array_push($result, $tmp);
                $bbLalu = $data_bb;
            }
        }

        return array_slice(array_reverse($result), 0, $jmlBaris);
    }

    private function view_kebutuhan_internal($arr, $sisa_pakan, $lock_pp = 0)
    {
        $tmp = array();
        $summary_perpakan = array();
        $urut_pakan = array();
        $pengurang_pp = array(); // pengurang pp perkandang
        $sudah_ditambahkan = array(); // penandan untuk menghitun penguran pp, sekali saja per kodepj perkandang
        $hutang_pp_sebelumnya = array();
        $sisa_gudang = array();
        $sisa_kandang = array();
        $sisa_gudang_kandang = array();
        $pakan_farm_lain_arr = $this->getPakanFarmLain($arr);

        /* cari total pengurang pp per kandang */
        foreach ($sisa_pakan as $nreg => $v) {
            if (!isset($pengurang_pp[$nreg])) {
                $pengurang_pp[$nreg] = 0;
            }
            /* hardcode dulu */
            foreach ($v as $_kode_barang => $vpp) {
                $_sisaGudang = $vpp['sisa_gudang'] < 0 ? 0 : $vpp['sisa_gudang'];
                $_sisaKandang = $vpp['sisa_kandang'] < 0 ? 0 : $vpp['sisa_kandang'];
                $pengurang_pp[$nreg] += $vpp['pengurang_pp'];
                $sisa_gudang[$_kode_barang] = $_sisaGudang;
                $sisa_kandang[$_kode_barang] = $_sisaKandang;
                $sisa_gudang_kandang[$_kode_barang] = $_sisaGudang + $_sisaKandang;
            }
        }

        foreach ($arr as $baris) {
            $kodepj = $baris['kode_barang'];
            $no_reg = $baris['no_reg'];
            $pakan_farm_lain = 0;
            if (isset($pakan_farm_lain_arr[$kodepj])) {
                if (isset($pakan_farm_lain_arr[$kodepj][$baris['tglkebutuhan']])) {
                    $pakan_farm_lain = $pakan_farm_lain_arr[$kodepj][$baris['tglkebutuhan']['jml_order']];
                }
            }
            /* null kan nilai dari $baris['pp'], jika kadept melakukan hitung ulang */
            $kode_kandang = $baris['kode_kandang'];
            if (!isset($urut_pakan[$kode_kandang])) {
                $urut_pakan[$kode_kandang] = array();
            }

            if (!empty($kodepj)) {
                if (!in_array($kodepj, $urut_pakan[$kode_kandang])) {
                    array_push($urut_pakan[$kode_kandang], $kodepj);
                }
            }

            if (!empty($kodepj)) {
                if (!isset($tmp[$kodepj])) {
                    $tmp[$kodepj] = array(
                        'summary' => array(
                            'nama_barang' => $baris['nama_barang'],
                            'populasi' => $baris['populasi'],
                            'lhk_terakhir' => $baris['lhk_terakhir'],
                            'sisa_gudang' => isset($sisa_gudang[$kodepj]) ? $sisa_gudang[$kodepj] : 0,
                            'sisa_kandang' => isset($sisa_kandang[$kodepj]) ? $sisa_kandang[$kodepj] : 0,
                        ),
                        'pertanggal' => array(),
                    );
                }

                if (!isset($hutang_pp_sebelumnya[$no_reg])) {
                    $hutang_pp_sebelumnya[$no_reg] = array();
                }
                if (!isset($hutang_pp_sebelumnya[$no_reg][$kodepj])) {
                    $hutang_pp_sebelumnya[$no_reg][$kodepj] = isset($sisa_pakan[$baris['no_reg']][$kodepj]['hutang_pp_sebelumnya']) ? $sisa_pakan[$baris['no_reg']][$kodepj]['hutang_pp_sebelumnya'] : 0;
                }

                $asli = $baris['kebutuhan_pakan'] - $hutang_pp_sebelumnya[$no_reg][$kodepj];
                $hutang_retur_sakpp = $pengurang_pp[$no_reg] < $asli ? $pengurang_pp[$no_reg] : ceil($asli);
                $asli = $asli - $hutang_retur_sakpp; /* untuk menyimpan jml_order_tanpa_pembulatan*/
                $asli = $asli < 0 ? 0 : $asli;
                $optimasi_pakan = ceil($asli);
                $pengurang_pp[$no_reg] = $pengurang_pp[$no_reg] > 0 ? ($pengurang_pp[$no_reg] - $hutang_retur_sakpp) : 0;

                $tmp_optimasi_pakan = $optimasi_pakan - $pakan_farm_lain;
                $tmp_sisa_gudang_kandang = isset($sisa_gudang_kandang[$kodepj]) ? $sisa_gudang_kandang[$kodepj] : 0;

                if ($tmp_optimasi_pakan > $tmp_sisa_gudang_kandang) {
                    $rekomendasi_pp = $tmp_optimasi_pakan - $tmp_sisa_gudang_kandang;
                    $sisa_gudang_kandang[$kodepj] = 0;
                } else {
                    $rekomendasi_pp = 0;
                    $sisa_gudang_kandang[$kodepj] = $tmp_sisa_gudang_kandang - $tmp_optimasi_pakan;
                }
                $jml_pp = isset($baris['pp']) ? $baris['pp'] : $rekomendasi_pp;

                $pakan = array(
                    'tgl_kebutuhan' => tglIndonesia($baris['tglkebutuhan'], '-', ' '),
                    'kuantitas' => ceil($baris['kebutuhan_pakan']),
                    'umur' => $baris['umur'],
                    'forecast' => $baris['kebutuhan_pakan'],
                    'komposisi' => $baris['komposisi_pakan'],
                    'optimasi_pakan' => $optimasi_pakan,
                    'pengurang_pp' => $hutang_retur_sakpp,
                    'rekomendasi_pp' => $rekomendasi_pp,
                    'jml_asli' => $asli,
                    'jml_pp' => $jml_pp,
                    'pakan_farm_lain' => $pakan_farm_lain,
                    'tgl_asli' => $baris['tglkebutuhan'],
                );
                //	$hutang_pp_sebelumnya[$no_reg][$kodepj] = $optimasi_pakan - $asli;
                array_push($tmp[$kodepj]['pertanggal'], $pakan);
            }
        }

        return array('view' => $tmp, 'summary' => $summary_perpakan);
    }

    private function grouping_review($arr)
    {
        $tmp = array();
        foreach ($arr as $r) {
            //	$noreg = $r['NO_REG'];
            $kodepj = $r['KODE_BARANG'];
            $tgl_kebutuhan = $r['TGL_KEBUTUHAN'];
            if (!isset($tmp[$kodepj])) {
                $tmp[$kodepj] = array();
            }
            if (!isset($tmp[$kodepj])) {
                $tmp[$kodepj] = array();
            }
            $tmp[$kodepj][$tgl_kebutuhan] = $r;
        }

        return $tmp;
    }

    private function grouping_realisasi_pp($arr)
    {
        $tmp = array();
        foreach ($arr as $r) {
            $noreg = $r['no_reg'];
            if (!isset($tmp[$noreg])) {
                $tmp[$noreg] = array();
            }
            $tmp[$noreg] = $r;
        }

        return $tmp;
    }

    public function cek_ganti_pakan()
    {
        $no_reg = $this->input->post('no_reg');
        $jenis_kelamin = $this->input->post('jenis_kelamin');
        $umur = $this->input->post('umur');
        /* umur sebelum dan sesudahnya dalam minggu */
        $umur_sebelumnya = $umur > 0 ? ($umur - 1) : 0;
        $umur_sesudahnya = $umur + 1;
        $p_umur = array($umur_sebelumnya, $umur, $umur_sesudahnya);
        $ganti_pakan = $this->mpp->get_pakan_ganti($no_reg, $jenis_kelamin, implode(',', $p_umur))->result_array();
        $ganti = 0;
        $tmp = array();
        $cur_pakan = '';
        if (!empty($ganti_pakan)) {
            foreach ($ganti_pakan as $g => $pakan) {
                if ($pakan['umur'] == $umur) {
                    $cur_pakan = $pakan['kode_barang'];
                }
                if (!isset($tmp[$pakan['kode_barang']])) {
                    $tmp[$pakan['kode_barang']] = array('umur' => $pakan['umur'], 'kode_barang' => $pakan['kode_barang'], 'nama_barang' => $pakan['nama_barang'], 'bentuk' => convertKode('bentuk_barang', $pakan['bentuk']));
                }
            }
            /* hapus array yang kode_pakannya = $cur_pakan */
            $ganti = array();
            foreach ($tmp as $r => $pakan) {
                if ($r != $cur_pakan) {
                    $ganti[$pakan['kode_barang']] = array('kode_barang' => $pakan['kode_barang'], 'nama_barang' => $pakan['nama_barang'], 'bentuk' => convertKode('bentuk_barang', $pakan['bentuk']));
                }
            }

            /* jika $tmp nilainya > 1 maka boleh mengganti pakan */
            if (!empty($ganti)) {
                $this->result['status'] = 1;
                $this->result['content'] = $ganti;
            } else {
                $this->result['status'] = 0;
                $this->result['message'] = 'Tidak bisa mengganti pakan';
            }
        }
        echo json_encode($this->result);
    }

    public function kertas_kerja($noreg, $max_week = 69)
    {
        return $this->mpp->kertas_kerja($noreg, $max_week);
    }

    public function get_hari_libur()
    {
        $minDate = $this->input->post('minDate');
        $hariLibur = $this->mpp->get_hari_libur($minDate)->result_array();

        $r = array();
        foreach ($hariLibur as $h) {
            array_push($r, $h['tanggal']);
        }

        $this->result['status'] = 1;
        $this->result['content'] = $r;
        echo json_encode($this->result);
    }

    public function cek_input_lhk($kodeflok = null)
    {
        $idfarm = $this->session->userdata('kode_farm');
        $all = true; /* di set true karena perlu cek acknya juga */
        $inputRhk = $this->mpp->cek_input_lhk($idfarm, $kodeflok, $all)->result_array();
        $r = array();
        $belumInputRhk = array();
        $belumAckRhk = array();
        if (!empty($inputRhk)) {
            foreach ($inputRhk as $ks) {
                if ($ks['sudahentrylhk']) {
                    if (empty($ks['ack_kf'])) {
                        array_push($belumAckRhk, $ks['nama']);
                    }
                } else {
                    array_push($belumInputRhk, $ks['nama']);
                }
            }
            $this->result['content'] = array('belumAckRhk' => $belumAckRhk, 'belumInputRhk' => $belumInputRhk);
        }
        $this->result['status'] = 1;
        $this->result['content'] = array();
        echo json_encode($this->result);
    }

    public function cek_input_lhk_noreg()
    {
        $idfarm = $this->session->userdata('kode_farm');
        $no_reg = $this->input->post('no_reg');
        $all = true; /* di set true karena perlu cek acknya juga */
        $inputRhk = $this->mpp->cek_input_lhk_noreg($no_reg, $all)->result_array();
        $r = array();
        $belumInputRhk = array();
        $belumAckRhk = array();

        if (!empty($inputRhk)) {
            foreach ($inputRhk as $ks) {
                if ($ks['sudahentrylhk']) {
                    if (empty($ks['ack_kf'])) {
                        array_push($belumAckRhk, $ks['nama']);
                    }
                } else {
                    array_push($belumInputRhk, $ks['nama']);
                }
            }
            $this->result['content'] = array('belumAckRhk' => $belumAckRhk, 'belumInputRhk' => $belumInputRhk);
        }
        $this->result['status'] = 1;
        echo json_encode($this->result);
    }

    public function comparepp()
    {
        $pp = implode('\',\'', $this->input->post('pp'));
        $flok = $this->input->post('flok');
        $compare = $this->mpp->comparepp($pp)->result_array();

        if (!empty($compare)) {
            /* grouping berdasarkan no_lpb,tgl_kirim */
            $kf = array();
            $db = array();
            foreach ($compare as $pp) {
                $tgl_kirim = $pp['TGL_KIRIM'];
                if ($pp['STATUS_LPB'] == 'V') {
                    if (!isset($kf[$tgl_kirim])) {
                        $kf[$tgl_kirim] = array();
                        $kf[$tgl_kirim]['totalpp'] = 0;
                        $kf[$tgl_kirim]['detailpp'] = array();
                    }
                    $kf[$tgl_kirim]['totalpp'] += $pp['jml_pp'];
                    array_push($kf[$tgl_kirim]['detailpp'], $pp);
                } else {
                    if (!isset($db[$tgl_kirim])) {
                        $db[$tgl_kirim] = array();
                        $db[$tgl_kirim]['totalpp'] = 0;
                        $db[$tgl_kirim]['detailpp'] = array();
                    }
                    $db[$tgl_kirim]['totalpp'] += $pp['jml_pp'];
                    array_push($db[$tgl_kirim]['detailpp'], $pp);
                }
            }
            $data = array('kf' => $kf, 'db' => $db);
            $data['flok'] = !empty($flok) ? $flok : '';
            $this->load->view('permintaan_pakan_v2/'.$this->grup_farm.'/comparepp', $data);
        }
    }

    public function monitoring_pp($farm = null)
    {
        $user_level = $this->session->userdata('level_user');
        $kodefarm = (!empty($farm)) ? $farm : $this->session->userdata('kode_farm');
        $list_farm = array(
            'KF' => $kodefarm,
            'DB' => $farm,
            'KDV' => $farm,
            'KD' => $farm,
            'KA' => $farm,
        );
        $data['list_farm'] = Modules::run('forecast/forecast/list_farm', $this->grup_farm, $list_farm[$user_level]);
        $this->load->view('permintaan_pakan_v2/monitoring', $data);
    }

    public function list_monitoring_pp()
    {
        $periode_docin = $this->input->post('_periode_doc_in');
        $kode_farm = $this->input->post('kode_farm');
        /* update session farm jika memilih kode_farm */
        if (!empty($kode_farm)) {
            $this->session->set_userdata(array('kode_farm' => $kode_farm));
        }
        $kode_farm = !empty($kode_farm) ? $kode_farm : $this->session->userdata('kode_farm');
        $tanggal = $this->input->post('_tanggal');
        $gf = $this->grup_farm;
        switch ($gf) {
            case 'brd':
                $list_pp = $this->mpp->monitoring_pp($periode_docin, $kode_farm, $tanggal)->result_array();
                $tmp_pp = $this->data_monitoring_brd($list_pp);
                break;
            case 'bdy':
                $list_pp = $this->mpp->monitoring_pp_bdy($periode_docin, $kode_farm, $tanggal)->result_array();
        //		$list_pp = $this->mpp->monitoring_pp($periode_docin,$kode_farm,$tanggal)->result_array();
                $tmp_pp = $this->data_monitoring_bdy($list_pp);
                break;
        }

        $data['list_pp'] = $tmp_pp;
        $data['kode_farm'] = $kode_farm;

        $this->load->view('permintaan_pakan_v2/'.$gf.'/monitoring_list_pp', $data);
    }

    private function data_monitoring_brd($list_pp)
    {
        /* grouping perpp, perop */
        $tmp_pp = array();
        $data['list_pp'] = null;
        if (!empty($list_pp)) {
            foreach ($list_pp as $pp) {
                $no_pp = $pp['no_lpb'];
                $no_op = $pp['no_op'];
                if (!isset($tmp_pp[$no_pp])) {
                    $tmp_pp[$no_pp]['op'] = array();
                    $tmp_pp[$no_pp]['data'] = array(
                            'no_pp' => $no_pp,
                            'ref_id' => $pp['ref_id'],
                            'tgl_pp' => $pp['tgl_rilis'],
                            'kuantitas_pp' => $pp['kuantitas_pp'],
                            'rowspan' => 0,
                    );
                }
                if (!isset($tmp_pp[$no_pp]['op'][$no_op])) {
                    $tmp_pp[$no_pp]['op'][$no_op]['do'] = array();
                    $tmp_pp[$no_pp]['op'][$no_op]['data'] = array(
                            'no_op' => $no_op,
                            'no_op_logistik' => $pp['no_op_logistik'],
                            'tgl_op' => $pp['tgl_op'],
                            'kuantitas_op' => $pp['kuantitas_op'],
                            'rowspan' => 0,
                    );
                }
                $do = array(
                        'no_do' => $pp['no_do'],
                        'ekspedisi' => $pp['nama_ekspedisi'],
                        'tgl_kirim' => $pp['tgl_kirim'],
                        'kuantitas_do' => $pp['kuantitas_do'],
                        'tgl_verifikasi' => $pp['tgl_verifikasi'],
                        'no_sj' => $pp['surat_jalan'],
                        'kg_sj' => $pp['sj_kg'],
                        'sak_sj' => $pp['sj_sak'],
                        'tgl_sj' => $pp['tgl_sj'],
                        'tgl_timbang' => null,
                        'tgl_terima' => $pp['tgl_terima'],
                        'kg_terima' => $pp['berat_terima'],
                        'sak_terima' => $pp['total_terima'],
                        'berita_acara' => $pp['berita_acara'],
                );

                ++$tmp_pp[$no_pp]['op'][$no_op]['data']['rowspan'];
                ++$tmp_pp[$no_pp]['data']['rowspan'];

                array_push($tmp_pp[$no_pp]['op'][$no_op]['do'], $do);
            }
        }

        return $tmp_pp;
    }

    private function data_monitoring_bdy($list_pp)
    {
        /* grouping perpp, perpenerimaan */
        $tmp_pp = array();
        $data['list_pp'] = null;
        if (!empty($list_pp)) {
            foreach ($list_pp as $pp) {
                $no_pp = $pp['no_lpb'];
                $no_op = $pp['no_op'];
                $no_penerimaan = $pp['no_penerimaan'];
                if (!isset($tmp_pp[$no_pp])) {
                    $tmp_pp[$no_pp]['do'] = array();
                    $tmp_pp[$no_pp]['data'] = array(
                            'no_pp' => $no_pp,
                            'flok_bdy' => $pp['flok_bdy'],
                            'ref_id' => $pp['ref_id'],
                            'tgl_pp' => $pp['tgl_rilis'],
                            'kuantitas_pp' => $pp['kuantitas_pp'],
                            'rowspan' => 0,
                    );
                }
                if (!isset($tmp_pp[$no_pp]['penerimaan'][$no_penerimaan])) {
                    $tmp_pp[$no_pp]['penerimaan'][$no_penerimaan] = array(
                            'tgl_terima' => $pp['tgl_terima'],
                            'kg_terima' => $pp['berat_terima'],
                            'sak_terima' => $pp['total_terima'],
                            'berita_acara' => $pp['berita_acara'],
                            'rowspan' => 0,
                    );
                }
                $do = array(
                        'no_penerimaan' => $no_penerimaan,
                        'no_op' => $no_op,
                        'no_op_logistik' => $pp['no_op_logistik'],
                        'tgl_op' => $pp['tgl_op'],
                        'kuantitas_op' => $pp['kuantitas_op'],
                        'no_do' => $pp['no_do'],
                        'ekspedisi' => $pp['nama_ekspedisi'],
                        'tgl_kirim' => $pp['tgl_kirim'],
                        'kuantitas_do' => $pp['kuantitas_do'],
                        'tgl_verifikasi' => $pp['tgl_verifikasi'],
                        'no_sj' => $pp['surat_jalan'],
                        'kg_sj' => $pp['sj_kg'],
                        'sak_sj' => $pp['sj_sak'],
                        'tgl_sj' => $pp['tgl_sj'],
                        'tgl_timbang' => null,
                );
                if (!empty($no_penerimaan)) {
                    ++$tmp_pp[$no_pp]['penerimaan'][$no_penerimaan]['rowspan'];
                }

                ++$tmp_pp[$no_pp]['data']['rowspan'];
                array_push($tmp_pp[$no_pp]['do'], $do);
            }
        }

        return $tmp_pp;
    }

    /* untuk menyimpan review_lpb_budidaya */
    public function simpan_review($dh, $status = 'I')
    {
        $this->load->model('permintaan_pakan_v2/m_review_lpb', 'rl');
        switch ($status) {
            case 'U':
                $where = array('no_lpb' => $dh['no_lpb'], 'no_reg' => $dh['no_reg'], 'kode_barang' => $dh['kode_barang'], 'tgl_kebutuhan' => $dh['tgl_kebutuhan']);
                $r = $this->rl->update_by($where, $dh);
                break;
            default:
                /*hapus dulu jika sudah ada*/
                $this->rl->delete_by(array('no_lpb' => $dh['no_lpb'], 'no_reg' => $dh['no_reg'], 'kode_barang' => $dh['kode_barang'], 'tgl_kebutuhan' => $dh['tgl_kebutuhan']));
                $r = $this->rl->insert($dh);
        }

        return $r;
    }

    public function reject_review($dh)
    {
        $this->load->model('permintaan_pakan_v2/m_review_lpb', 'rl');
        //$where = array('no_lpb'=> $dh['no_lpb']);
        // $r = $this->rl->update_by($where,$dh);
        $no_lpb = $dh['no_lpb'];
        unset($dh['no_lpb']);
        $r = $this->db->where_in('no_lpb', $no_lpb)->update('review_lpb_budidaya', $dh);

        return $r;
    }

    /* function ini untuk mendapatkan data tgl_kirim yang telah disimpan dalam tabel forecast*/
    public function tanggal_kirim_forecast()
    {
        $kodeSiklus = $this->session->userdata('kode_siklus');
        $flok = $this->input->post('flok');
        $kebutuhanAwal = $this->input->post('kebutuhan_awal');
        $this->load->model('forecast/m_forecast', 'mf');
        $cari = array('kode_siklus =\''.$kodeSiklus.'\' and kode_flok_bdy = \''.$flok.'\' and tgl_keb_awal >=\''.$kebutuhanAwal.'\'');
        $tgl_kirim = $this->mf->as_array()->order_by('tgl_keb_awal')->get_many_by($cari);

        /* jika elemen pertama tidak sama dengan tanggal kebutuhan awal maka dianggap hasilnya tidak ditemukan */
        if (!empty($tgl_kirim)) {
            $awal = $tgl_kirim[0];
            $akhir = isset($tgl_kirim[1]) ? $tgl_kirim[1] : $tgl_kirim[0];
            $status = $awal['TGL_KEB_AWAL'] == $kebutuhanAwal ? 1 : 0;
            if ($status) {
                $tgl_kirim_keb = $awal['TGL_KIRIM'];
                $tgl_keb_awal = $awal['TGL_KEB_AWAL'];
                $tgl_keb_akhir = $akhir['TGL_KEB_AWAL'] > $awal['TGL_KEB_AWAL'] ? tglSebelum($akhir['TGL_KEB_AWAL'], 1) : $awal['TGL_KEB_AWAL'];
                $this->result['content'] = array('tgl_kirim' => $tgl_kirim_keb, 'tgl_keb_awal' => $tgl_keb_awal, 'tgl_keb_akhir' => $tgl_keb_akhir);
            }
            $this->result['status'] = $status;
        }
        echo json_encode($this->result);
    }

    public function get_pakan_tambahan()
    {
        $noreg = $this->input->post('noreg');
        $kodePakan = $this->input->post('kodepakan');
        $pakanSelanjutnya = $this->mpp->get_pakan_standart($noreg)->result_array();
        if (!empty($pakanSelanjutnya)) {
            foreach ($pakanSelanjutnya as $ps) {
                $kb = $ps['kode_pakan'];
                if (!in_array($kb, $kodePakan)) {
                    $_tmp[$kb] = $ps;
                }
            }
            /** jika perlu pakan tambahan diluar pakan standart maka tambahkan saja list pakannya kedalam $_tmp */
            //$_tmp['1121S10M13'] = array('kode_pakan' => '1121S10M13', 'nama_barang' => 'BROILER 0 - 212');
            if (!empty($_tmp)) {
                $this->result['list_pakan'] = $_tmp;
                $this->result['status'] = 1;
            }
        }
        echo json_encode($this->result);
    }

    /* untuk mendapatkan pakan selanjutnya, ketika penambahan pakan */
    public function get_pakan_selanjutnya()
    {
        $noreg = $this->input->post('noreg');
        $kodePakan = $this->input->post('kodepakan');
        $pakanSelanjutnya = $this->mpp->get_pakan_standart($noreg)->result_array();
        if (!empty($pakanSelanjutnya)) {
            foreach ($pakanSelanjutnya as $ps) {
                $kb = $ps['kode_pakan'];
                $_tmp[$kb] = $ps;
            }
            $this->result['list_pakan'] = $_tmp;
            $ketemu = 0;
            $nextPakan = null;

            foreach ($_tmp as $k => $v) {
                if ($k != $kodePakan) {
                    ++$ketemu;
                }
                if ($ketemu > 0) {
                    $nextPakan = $k;
                    $this->result['status'] = 1;
                    $this->result['content'] = array('kodepakan' => $nextPakan, 'namapakan' => $v['nama_barang']);
                    /*	array('kodepakan' => $nextPakan,'namapakan'=> $v['nama_barang']),
                        array('kodepakan' => '1127-10A12','namapakan'=> 'BR 2 SUPER A'),
                        array('kodepakan' => '1127-10B12','namapakan'=> 'BR 2 SUPER B'));*/
                }
                if (!empty($nextPakan)) {
                    break;
                }
            }
        }
        echo json_encode($this->result);
    }

    private function getKodePP($nopp, $delimiter, $bag)
    {
        $t = explode($delimiter, $nopp);

        return $t[$bag - 1];
    }

    /* yang dicek cukup komposisi dan tanggal_kebutuhan saja*/
    private function differencePP($pp_lama, $pp_baru)
    {
        $beda = 0;
        foreach ($pp_lama as $kp => $perpakan) {
            foreach ($perpakan as $kk => $perkandang) {
                foreach ($perkandang as $i => $pk) {
                    if (isset($pp_baru[$kp][$kk][$i])) {
                        if ($pp_baru[$kp][$kk][$i]['tgl_kebutuhan'] != $pk['tgl_kebutuhan']) {
                            ++$beda;
                            break;
                        }
                        if ($pp_baru[$kp][$kk][$i]['komposisi'] != $pk['komposisi']) {
                            ++$beda;
                            break;
                        }
                    } else {
                        ++$beda;
                        break;
                    }
                }
            }
        }

        return $beda;
    }

    public function pakan_tambahan()
    {
        $awal = $this->input->post('awal');
        $akhir = $this->input->post('akhir');
        $kodepj = $this->input->post('kodepj');
        $noreg = $this->input->post('noreg');
        $editRekomendasi = array(
            'KF' => array('', 'D'),
            'P' => array('', 'D'),
        );
        $sisa_pakan = array();
        $arr = $this->mpp->list_kebutuhan_pakan_tambahan_bdy($noreg, $awal, $akhir, $kodepj);
        /* generate sisa pakan */
        $sisa_pakan = array(
            $noreg => array(
                $kodepj => array(
                'sisa_konsumsi' => 0,
                'hutang_pp_sebelumnya' => 0,
                'hutang_retur_sak' => 0,
                'pengurang_pp' => 0,
                'sisa_kandang' => 0,
                'sisa_gudang' => 0,
                ),
            ),
        );
        $user_level = $this->session->userdata('level_user');
        $view = $this->view_kebutuhan_internal($arr, $sisa_pakan);
        $this->load->view('permintaan_pakan_v2/bdy/kebutuhan_pakan',
            array(
                'kebutuhan_pakan' => $view['view'],
                'edit_rekomendasi' => isset($editRekomendasi[$user_level]) ? 1 : 0,
                'edit_review' => isset($this->_canReview[$user_level]) ? $this->_canReview[$user_level] : 0,
                'pakan_tambahan' => 1,
                'show_review' => $user_level == 'KD' ? 1 : 0,
                )
            );
    }

    private function getPakanFarmLain($arr)
    {
        $result = array();
        $barisPertama = $arr[0];
        $noreg = $barisPertama['no_reg'];
        $tgl_keb_awal = $barisPertama['tglkebutuhan'];
        $tgl_keb_akhir = $tgl_keb_awal;
        foreach ($arr as $r) {
            $tgl_kebutuhan = $r['tglkebutuhan'];
            if ($tgl_kebutuhan > $tgl_keb_akhir) {
                $tgl_keb_akhir = $tgl_kebutuhan;
            }
        }

        return arr2DToarrKey($this->mpp->pakan_farm_lain($noreg, $tgl_keb_awal, $tgl_keb_akhir)->result_array(), 'kode_barang');
    }

    public function rencanaPanen()
    {
        $noreg = $this->input->get('noreg');
        $tglkebutuhan = $this->input->get('tglkebutuhan');
        /*array(
            array('tgl_panen' => '2018-05-02', 'status' => 'Konfirmasi H-1'),
            array('tgl_panen' => '2018-05-04', 'status' => 'Konfirmasi H-1'),
            array('tgl_panen' => '2018-05-06', 'status' => 'Konfirmasi H-1'),
            array('tgl_panen' => '2018-05-06', 'status' => 'Konfirmasi H-1'),
        )*/
        $data = array(
            'rencanaPanen' => array(),
        );

        $this->load->view('permintaan_pakan_v2/bdy/rencana_panen', $data);
    }
}
