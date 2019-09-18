<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Plotting_pelaksana extends MY_Controller
{
    protected $result;
    protected $_user;
    protected $_idFarm;
    private $grup_farm;

    private $_jabatanPlottingPelaksana;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array(
            'm_transaksi',
        ));
        $this->load->model('riwayat_harian_kandang/M_riwayat_harian_kandang', 'm_riwayat');
        $this->load->helper('stpakan');
        $this->_user = $this->session->userdata('kode_user');
        $this->_farm = $this->session->userdata('kode_farm');
        $this->_grup_farm = strtolower($this->session->userdata('grup_farm'));
        $this->_jabatanPlottingPelaksana = array(
            'Koordinator Pengawas Produksi',
            'Pengawas Produksi',
            'Operator Kandang',
        );
    }

    public function kafarm($kode_farm = null)
    {
        $data['farm'] = $this->m_transaksi->getFarm($this->_farm);
        $data['siklus'] = $this->m_transaksi->getSiklus($this->_farm);
        $kode_siklus = '';
        foreach ($data['siklus'] as $key => $val) {
            if ($val['STATUS_PERIODE'] == 'A') {
                $kode_siklus = $val['KODE_SIKLUS'];
            }
        }
        $data['koodinator'] = $this->m_transaksi->listPegawai($this->_farm, 'KPPB');
        $data['complete_ploting'] = 0;
        $data['list_plotting'] = array();

        if (!empty($kode_siklus)) {
            $data['complete_ploting'] = $this->m_transaksi->isCompletePloting($kode_siklus);
            $data['list_plotting'] = $this->m_transaksi->listPlottingPegawai($kode_siklus);
        }

        $this->load->view('kandang/list_kandang', $data);
    }

    public function kadept($kode_farm = null)
    {
        $data['farm'] = $this->db->select(array('KODE_FARM', 'NAMA_FARM'))->get('m_farm')->result_array();
        if (empty($kode_farm)) {
            $kode_farm = $data['farm'][0]['KODE_FARM'];
        }
        $data['siklus'] = $this->m_transaksi->getSiklus($kode_farm);
        /*foreach ($data['siklus'] as $key=>$val) {
            if ($val['STATUS_PERIODE']=='A')
                $kode_siklus = $val['KODE_SIKLUS'];
        }
        */
        $data['farm_terpilih'] = $kode_farm;
        $this->load->view('kandang/list_kandang_ack', $data);
    }

    public function kadiv($kode_farm)
    {
        $data['farm'] = $this->db->select(array('KODE_FARM', 'NAMA_FARM'))->get('m_farm')->result_array();
        if (empty($kode_farm)) {
            $kode_farm = $data['farm'][0]['KODE_FARM'];
        }
        $data['siklus'] = $this->m_transaksi->getSiklus($kode_farm);
        /*foreach ($data['siklus'] as $key=>$val) {
            if ($val['STATUS_PERIODE']=='A')
                $kode_siklus = $val['KODE_SIKLUS'];
        }
        */
        $data['farm_terpilih'] = $kode_farm;
        $this->load->view('kandang/list_kandang_ack', $data);
    }

    public function main($kode_farm = null)
    {
        $kode_siklus = '';
        $user_level = $this->session->userdata('level_user');

        switch ($user_level) {
            case 'KDV':
                $this->kadiv($kode_farm);
                break;
            case 'KD':
                $this->kadept($kode_farm);
                break;
            default:
                $this->kafarm($kode_farm);
        }
    }

    public function get_pagination()
    {
        $offset = 10;
        $page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
        $is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

        $periode1 = $this->input->post('periode1');
        $periode2 = $this->input->post('periode2');

        $siklus = (($this->input->post('siklus')) and $is_search == true) ? $this->input->post('siklus') : null;
        $flock = (($this->input->post('flock')) and $is_search == true) ? $this->input->post('flock') : null;
        $kandang = (($this->input->post('kandang')) and $is_search == true) ? $this->input->post('kandang') : null;
        $koordinator = (($this->input->post('koordinator')) and $is_search == true) ? $this->input->post('koordinator') : null;
        $pengawas = (($this->input->post('pengawas')) and $is_search == true) ? $this->input->post('pengawas') : null;
        $operator = (($this->input->post('operator')) and $is_search == true) ? $this->input->post('operator') : null;
        $tgl_doc_in = (($this->input->post('tgl_doc_in')) and $is_search == true) ? $this->input->post('tgl_doc_in') : null;

        $data_semua_do = $this->m_transaksi->get_data_kandang(null, null, $this->_farm, $siklus, $flock, $kandang, $koordinator, $pengawas, $operator, $tgl_doc_in, $periode1, $periode2);

        $data_do = $this->m_transaksi->get_data_kandang(($page_number * $offset), ($page_number + 1) * $offset, $this->_farm, $siklus, $flock, $kandang, $koordinator, $pengawas, $operator, $tgl_doc_in, $periode1, $periode2);

        $total = count($data_semua_do);
        $pages = ceil($total / $offset);

        if (count($data_do) > 0) {
            $data = array(
                'TotalRows' => $pages,
                'Rows' => $data_do,
            );

            $this->output->set_content_type('application/json');
            echo json_encode(array(
                $data,
            ));
        } else {
            $data = array(
                'TotalRows' => $pages,
                'Rows' => $data_do,
            );
            echo json_encode(array($data));
        }
        exit();
    }

    public function get_pagination_ack()
    {
        $user_level = $this->session->userdata('level_user');
        $list_status_akan_plotting = array(
            'KDV' => 'RV',
            'KD' => 'N',
        );
        $status_akan_plotting = $list_status_akan_plotting[$user_level];

        $offset = 10;
        $page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
        $is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

        $kode_farm = $this->input->post('kode_farm');
        $periode1 = $this->input->post('periode1');
        $periode2 = $this->input->post('periode2');

        $siklus = (($this->input->post('siklus')) and $is_search == true) ? $this->input->post('siklus') : null;
        $flock = (($this->input->post('flock')) and $is_search == true) ? $this->input->post('flock') : null;
        $kandang = (($this->input->post('kandang')) and $is_search == true) ? $this->input->post('kandang') : null;
        $koordinator = (($this->input->post('koordinator')) and $is_search == true) ? $this->input->post('koordinator') : null;
        $pengawas = (($this->input->post('pengawas')) and $is_search == true) ? $this->input->post('pengawas') : null;
        $operator = (($this->input->post('operator')) and $is_search == true) ? $this->input->post('operator') : null;
        $tgl_doc_in = (($this->input->post('tgl_doc_in')) and $is_search == true) ? $this->input->post('tgl_doc_in') : null;

        $data_plotting = $this->m_transaksi->get_data_kandang(null, null, $kode_farm, $siklus, $flock, $kandang, $koordinator, $pengawas, $operator, $tgl_doc_in, $periode1, $periode2);

        $data_persiklus_perflok = $this->grouping_persiklus_perflok($data_plotting);
        $pegawai_keterangan = $this->getPegawaiKeterangan();
        $html = $this->load->view('kandang/ack_plotting', array('pegawai_keterangan' => $pegawai_keterangan, 'data' => $data_persiklus_perflok, 'status_akan_plotting' => $status_akan_plotting), true);

        $this->result = array(
            'status' => 1,
            'html' => $html,
        );
        echo json_encode($this->result);
    }

    public function loadPlotting()
    {
        $kode_siklus = $this->input->post('kode_siklus');
        $data['list_pengawas'] = $this->m_transaksi->listPegawai($this->_farm, 'PPB');
        $data['list_operator'] = $this->m_transaksi->listPegawai($this->_farm, 'OK');

        $farm = $this->_farm;
        $list_jabatan_plotting_pelaksana = $this->_jabatanPlottingPelaksana;

        $data['complete_ploting'] = $this->m_transaksi->isCompletePloting($kode_siklus);
        $data['automatic_flock_plotting'] = $this->m_transaksi->AutomaticallyInsertFlock($farm, $kode_siklus, 'Koordinator Pengawas Produksi');
        $data['list_plotting'] = $this->m_transaksi->listPlottingPegawai($farm, $list_jabatan_plotting_pelaksana, $kode_siklus);
        foreach ($data['list_plotting'] as $key => &$val) {
            if (isset($val['KODE_FLOK']) && !empty($val['KODE_FLOK'])) {
                $data_flok = $val['KODE_FLOK'];
                $arr_data_flok = explode(',', $val['KODE_FLOK']);
                $data_flok = array_unique($arr_data_flok);
                $imp_data_flok = implode(',', $data_flok);
                $val['KODE_FLOK'] = $imp_data_flok;
            }
            if (isset($val['KODE_KANDANG']) && !empty($val['KODE_KANDANG'])) {
                $data_kandang = $val['KODE_KANDANG'];
                $arr_data_kandang = explode(',', $val['KODE_KANDANG']);
                $data_kandang = array_unique($arr_data_kandang);
                $imp_data_kandang = implode(',', $data_kandang);
                $val['KODE_KANDANG'] = $imp_data_kandang;
            }
        }
        // cetak_r($data);
        $this->load->view('kandang/list_plotting', $data);
    }

    public function getPegawai()
    {
        // $nama_pegawai = $_GET['q'];
        echo $this->m_transaksi->getPegawai($this->_farm, 'OK');
    }

    public function cekPengawas()
    {
        $kode_farm = $this->_farm;
        $kode_siklus = $this->input->post('kode_siklus');
        $flock = $this->input->post('flock');
        $pengawas = $this->input->post('pengawas');
        $result = $this->m_transaksi->cekPengawas($kode_farm, $kode_siklus, $flock, $pengawas);

        if (count($result) == 0) {
            $data['success'] = true;
        } else {
            $data['success'] = false;
            $data['message'] = 'Pengawas '.$result[0]['NAMA_PEGAWAI'].' sudah di plotting di Flock '.$result[0]['FLOK_BDY'].'';
        }
        echo json_encode($data);
    }

    public function cekOperator()
    {
        $kode_farm = $this->_farm;
        $kode_siklus = $this->input->post('kode_siklus');
        $operator = $this->input->post('operator');
        $result = $this->m_transaksi->cekOperator($kode_farm, $kode_siklus, $operator);

        if (count($result) == 0) {
            $data['success'] = true;
        } else {
            $data['success'] = false;
            $data['message'] = 'Operator '.$result[0]['NAMA_PEGAWAI'].' sudah di plotting pada Kandang '.$result[0]['KODE_KANDANG'].'';
        }
        echo json_encode($data);
    }

    public function save()
    {
        $data_params = $this->input->post('data');
        //$flok = $data_params['flok'];
        $siklus = $data_params['siklus'];
        /** periksa apakah semua kandang untuk flok yang diploting sudah diplot semua */
        $hasilInsert = $this->insert($data_params);
        $this->result['status'] = $hasilInsert['status'];
        $this->result['message'] = $hasilInsert['message'];

        $this->result['content'] = $siklus;
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));
    }

    public function ack()
    {
        $user_level = $this->session->userdata('level_user');
        $nextStatus = array(
            'KDV' => 'A',
            'KD' => 'RV',
        );
        $data = $this->input->post('data');
        $status = $nextStatus[$user_level];
        $dataUpdate = array(
            'status' => $status,
        );
        if ($status == 'RV') {
            $dataUpdate['tgl_review'] = date('Y-m-d H:i:s');
            $dataUpdate['user_review'] = $this->_user;
        }

        if ($status == 'A') {
            $dataUpdate['tgl_ack'] = date('Y-m-d H:i:s');
            $dataUpdate['user_ack'] = $this->_user;
        }
        $kode_siklus = null;
        $kode_farm = null;
        foreach ($data as $d) {
            $this->db->where('no_reg in (select no_reg from kandang_siklus where kode_siklus = \''.$d['kode_siklus'].'\' and FLOK_BDY = \''.$d['flok'].'\')')->update('m_ploting_pelaksana', $dataUpdate);
            if (empty($kode_siklus)) {
                $kode_siklus = $d['kode_siklus'];
                $kode_farm_arr = $this->db->where(array('kode_siklus' => $kode_siklus))->get('m_periode')->row_array();
                if (!empty($kode_farm_arr)) {
                    $kode_farm = $kode_farm_arr['KODE_FARM'];
                }
            }
        }
        $this->result['status'] = 1;
        $this->result['content'] = $data;
        $this->result['kode_siklus'] = $kode_siklus;
        $this->result['kode_farm'] = $kode_farm;
        $this->result['message'] = 'Plotting pelaksana berhasil di-ack';
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));
    }

    private function insert($data_params)
    {
        $detail_plotting = $data_params['detail_plotting'];
        $user_login = $this->_user;
        $fulldate = $this->m_riwayat->get_today();
        foreach ($detail_plotting as $key => $perkandang) {
            foreach ($perkandang as $val) {
                $val['tgl_buat'] = $fulldate['today'];
                $val['user_buat'] = $user_login;
                $insert_plotting_pelaksana[] = $val;
            }
        }

        $result = $this->m_transaksi->insert_plotting_pelaksana($insert_plotting_pelaksana);
        if ($result) {
            $this->result['result'] = 'success';
            $this->result['message'] = 'Data berhasil disimpan';
            $this->result['status'] = 1;
        } else {
            $this->result['result'] = 'failed';
            $this->result['message'] = 'Data gagal disimpan';
            $this->result['status'] = 0;
        }

        return $this->result;
    }

    public function plotted_all_kandang_flok()
    {
        $data_params = $this->input->post('data');
        $flok = $data_params['flok'];
        $kode_siklus = $data_params['siklus'];
        $detail_plotting = isset($data_params['detail_plotting']) && !empty($data_params['detail_plotting']) ? $data_params['detail_plotting'] : null;

        if (!empty($detail_plotting)) {
            foreach ($detail_plotting as $key => $val) {
                $kode_kandang = $val[0]['kode_kandang'];
                $qKodeKandang[] = "select '{$kode_kandang}' as kandang ";
            }

            $str_kode_kandang = implode(' UNION ', $qKodeKandang);

            $tmp = $this->m_transaksi->plotted_all_kandang_flok($kode_siklus, $flok, $str_kode_kandang);
            $result = $tmp['jumlah'];
        } else {
            $result = 0;
        }

        if (!$result) {
            $this->result['status'] = 1;
        } else {
            $this->result['message'] = 'Data tidak valid, kandang pada flock tersebut belum diploting secara lengkap';
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));
    }

    /** grouping persiklus, perflok */
    private function grouping_persiklus_perflok($data_plotting)
    {
        $result = array();
        foreach ($data_plotting as $dp) {
            $kode_siklus = $dp['periode'];
            $flok = $dp['flok_bdy'];
            if (!isset($result[$kode_siklus])) {
                $result[$kode_siklus] = array();
            }
            if (!isset($result[$kode_siklus][$flok])) {
                $result[$kode_siklus][$flok] = array();
            }

            array_push($result[$kode_siklus][$flok], $dp);
        }

        return $result;
    }

    public function getPegawaiKeterangan()
    {
        $arr = $this->db->select(array('KODE_PEGAWAI', 'NAMA_PEGAWAI'))->where_in('grup_pegawai', array('KFM', 'KDP', 'KDV'))->get('m_pegawai')->result_array();

        return arr2DToarrKey($arr, 'KODE_PEGAWAI');
    }
}
