<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Riwayat_harian_kandang extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('riwayat_harian_kandang/M_riwayat_harian_kandang_bdy', 'm_riwayat');
        $this->load->model('riwayat_harian_kandang/M_rhk', 'm_rhk');
        $this->load->helper('stpakan');
        $this->load->config('stpakan');

        $this->_user = $this->session->userdata('kode_user');
        $this->_namauser = $this->session->userdata('nama_user');
        $this->_farm = $this->session->userdata('kode_farm');
    }

    public function index()
    {
        $kodefarm = $this->_farm;
        $kodepegawai = $this->_user;

        $listFarm = $this->config->item('namaFarm');
        $fulldate = $this->m_riwayat->get_today();
        $date = explode(' ', $fulldate['today']);

        $data['kode_farm'] = $kodefarm;
        $data['nama_farm'] = $listFarm[$kodefarm];
        $data['today'] = $date[0];
        $data['today_full'] = $fulldate['today'];

        $data['kandang_sekat'] = null;

        $data['entri_lhk_penimbangan_sekat'] = $this->load->view('entri_lhk_penimbangan_sekat', $data, true);
        $data['entri_lhk_populasi'] = $this->load->view('entri_lhk_populasi', $data, true);
        $data['entri_lhk_pakan'] = $this->load->view('entri_lhk_pakan', $data, true);
        $data['entri_lhk_permintaan_kandang'] = $this->load->view('entri_lhk_permintaan_kandang', $data, true);
        $data['entri_lhk_step'] = $this->load->view('entri_lhk_step', $data, true);
        $this->load->view('entri_lhk', $data);
    }

    public function check_scan_LHK()
    {
        $barcode = $this->input->post('barcode');
        $kodefarm = $this->_farm;
        $content = array();
        /* cek apakah barcode tersebut dikenal / ada */
        $this->load->model('riwayat_harian_kandang/m_rhk_cetak', 'rhk_cetak');
        $this->load->model('riwayat_harian_kandang/m_ploting_pelaksana', 'mpp');
        $cetakRhk = $this->rhk_cetak->as_array()->get_by(array('barcode' => $barcode));
        if (empty($cetakRhk)) {
            $this->result['message'] = 'Barcode tidak terdaftar';
            display_json($this->result);

            return;
        }
        /** pastikan lhk belum dientry */
        $sudahEntry = $this->m_rhk->as_array()->get_by(array('no_reg' => $cetakRhk['NO_REG'], 'tgl_transaksi' => $cetakRhk['TGL_TRANSAKSI']));
        if (!empty($sudahEntry)) {
            $this->result['message'] = 'LHK sudah pernah di entri';
            display_json($this->result);

            return;
        }
        /** pastikan entry tidak terlambat */
        $terlambat = $this->kontrolTimeline($cetakRhk['TGL_TRANSAKSI']);
        if ($terlambat['content']) {
            if ($terlambat['block']) {
                $this->result['message'] = $terlambat['message'];
                display_json($this->result);

                return;
            }
        }
        /** pastikan yang melakukan entry lhk adalah pengawas yang diplot untuk noreg tersebut 	*/
        $cekPengawas = $this->mpp->as_array()->get_by(array('no_reg' => $cetakRhk['NO_REG'], 'pengawas' => $this->_user));
        if (empty($cekPengawas)) {
            $this->result['message'] = 'Anda tidak bisa entri LHK kandang ini';
            display_json($this->result);

            return;
        }
        $tmp = $this->db->select('DATEDIFF(DAY, ks.TGL_DOC_IN, \''.$cetakRhk['TGL_TRANSAKSI'].'\') AS umur,\''.$cetakRhk['TGL_TRANSAKSI'].'\' as tgl_lhk', false)->select('ks.*')->where(array('no_reg' => $cetakRhk['NO_REG']))->get('kandang_siklus ks')->row_array();

        if (!empty($tmp)) {
            /* pastikan bapd sudah diapprove jika umur 1 */
            if ($tmp['umur'] == 1) {
                $approvalBapd = $this->db->select('count(*) as ada')->where(array('no_reg' => $cetakRhk['NO_REG'], 'status' => 'A'))->get('bap_doc')->row_array();
                if (!$approvalBapd['ada']) {
                    $this->result['message'] = 'BAPD belum diapprove oleh kafarm';
                    display_json($this->result);

                    return;
                }
            }
            /* pastikan sudah entry lhk sebelumnya, kecuali jika umur 1 */
            if ($tmp['umur'] > 1) {
                $entryLhkSebelumnya = $this->db->select('count(*) as ada')->where('tgl_transaksi = dateadd(day,-1,\''.$cetakRhk['TGL_TRANSAKSI'].'\')')->where(array('no_reg' => $cetakRhk['NO_REG']))->get('rhk')->row_array();
                if (!$entryLhkSebelumnya['ada']) {
                    $this->result['message'] = 'LHK tanggal sebelumnya belum dientri';
                    display_json($this->result);

                    return;
                }

                /** pastikan sudah droping pakan untuk tanggal kebutuhan besok, karena rhk dientri di hari H */
                $belumDropping = $this->m_riwayat->belumDropping($cetakRhk['NO_REG'], $tmp['TGL_DOC_IN']);
                if ($belumDropping['ada']) {
                    $this->result['message'] = 'Dropping masih belum selesai dilakukan';
                    display_json($this->result);

                    return;
                }
            }
        }

        $tmp['format_tgl_lhk'] = tglIndonesia($tmp['tgl_lhk'], '-', ' ');
        $tmp['format_tgl_doc_in'] = tglIndonesia($tmp['TGL_DOC_IN'], '-', ' ');

        $this->result['content'] = $tmp;
        $this->result['status'] = 1;
        display_json($this->result);
    }

    public function ajax_entri_step()
    {
        $no_reg = $this->input->post('no_reg');
        $kandang = $this->input->post('kandang');
        $flok = $this->input->post('flok');
        $tgl_doc_in = $this->input->post('tgl_doc_in');
        $tgl_lhk = $this->input->post('tgl_lhk');
        $umur = $this->input->post('umur');
        $kodefarm = $this->_farm;
        $content = array();

        $kandang_sekat = $this->m_rhk->getJumlahSekatPenimbangan($kodefarm, $kandang);
        $jml_sekat = array();
        if (isset($kandang_sekat) && !empty($kandang_sekat)) {
            for ($i = 0; $i < $kandang_sekat[0]['JML_SEKAT']; ++$i) {
                $jml_sekat[] = $i + 1;
            }
        }

        $tglkebutuhan = tglSetelah($tgl_lhk, 2); //$this->m_riwayat->get_tanggal_kebutuhan_LHK($tgl_lhk);
        // $arr_tgl_kebutuhan = explode(" ",$tglkebutuhan[0]['tgl_kebutuhan']);

        $this->load->model('riwayat_harian_kandang/M_rhk_rekomendasi_pakan', 'rekomendasi');
        $data['pakan_rekomendasi'] = array();
        $standartPakan = $this->rekomendasi->rekomendasiPakanStandart($no_reg, $tglkebutuhan);
        /* cari jumlah ayamnya dan stok akhir kandang tgl lhk sebelumnya */
        $tglRhkSebelumnya = tglSebelum($tgl_lhk, 1);
        $rhkLalu = $this->m_rhk->as_array()->get_by(array('no_reg' => $no_reg, 'tgl_transaksi' => $tglRhkSebelumnya));

        $pakanPP = $this->rekomendasi->listPakanGudangRekomendasi($no_reg);
        $jumlahAyam = 0;
        if (!empty($rhkLalu)) {
            $jumlahAyam = $rhkLalu['C_JUMLAH'];
            /* kurangkan jumlah ayam dengan yang sudah dipanen */
            $ayamPanen = $this->db->select('sum(jumlah_aktual) as panen')->where('tgl_buat between \''.$rhkLalu['TGL_BUAT'].'\' and getdate()')->where(array('no_reg' => $no_reg))->get('realisasi_panen')->row_array();
            $jumlahAyam -= $ayamPanen['panen'];
        } else {
            /** cari dari bapdoc */
            $bapdoc = $this->db->select('STOK_AWAL')->where(array('no_reg' => $no_reg))->get('bap_doc')->row_array();
            $jumlahAyam = $bapdoc['STOK_AWAL'];
        }
        $jml_maks_pp_order = $this->m_rhk->maksPPKandangTglKebutuhan($no_reg, $kodefarm, $tglkebutuhan);
        foreach ($pakanPP as $key => $val) {
            //$pengali =  $val['komposisi_pakan'];
            $pengali = 1;
            // $rekomendasiPakan = ceil(($standartPakan['pkn_hr'] * $jumlahAyam / 50000) * $pengali);
            $kode_barang = $val['kode_barang'];
            $stokPakanTerakhir = $this->m_rhk->stokPakanTglTransaksi($no_reg, $tgl_lhk, $kode_barang);
            $data['rekomendasi_pakan'][$kode_barang] = array(
                'tglkebutuhan' => $tglkebutuhan,
                'kebutuhan_pakan' => 0,
                'standart_kebutuhan' => $standartPakan['pkn_hr'],
                'jumlah_ayam' => $jumlahAyam,
                'komposisi' => $pengali,
                'nama_barang' => $val['nama_barang'],
                'stok_pakan' => $stokPakanTerakhir,
                'jml_maks_pp_order' => isset($jml_maks_pp_order[$kode_barang]) ? $jml_maks_pp_order[$kode_barang] : 0,
            );
        }
        $data['pakan_pakai'] = array();
        $pakanBisaDipakai = $this->db->select('m_barang.kode_barang,m_barang.nama_barang,kandang_movement.jml_stok')
                                //	->where('kandang_movement.jml_stok > 0')
                                    ->where(array('no_reg' => $no_reg))
                                    ->join('m_barang', 'm_barang.kode_barang = kandang_movement.kode_barang')
                                    ->get('kandang_movement')->result_array();
        foreach ($pakanBisaDipakai as $key => $val) {
            $data['pakan_pakai'][$val['kode_barang']] = $val;
        }

        $data['jml_sekat'] = $jml_sekat;
        $data['jumlah_ayam'] = $jumlahAyam;
        $data['entri_lhk_penimbangan_sekat'] = $this->load->view('entri_lhk_penimbangan_sekat', $data, true);
        $data['entri_lhk_populasi'] = $this->load->view('entri_lhk_populasi', $data, true);
        $data['entri_lhk_pakan'] = $this->load->view('entri_lhk_pakan', $data, true);
        $data['entri_lhk_permintaan_kandang'] = $this->load->view('entri_lhk_permintaan_kandang', $data, true);
        $data['entri_lhk_step'] = $this->load->view('entri_lhk_step', $data, true);

        $entri_lhk_step = $data['entri_lhk_step'];

        echo $entri_lhk_step;
    }

    public function checkTimeline()
    {
        $tglTransaksi = $this->input->get('tglTransaksi');
        $cek = $this->kontrolTimeline($tglTransaksi);
        echo json_encode($cek);
    }

    private function kontrolTimeline($tglTransaksi)
    {
        $message = 'Entry LHK untuk transaksi '.convertElemenTglIndonesia($tglTransaksi).' dapat dientry pada ';
        //$tglSetelahnya = tglSetelah($tglTransaksi, 1); /** H+1 */
        $sekarang = new \Datetime();
        $maxInput = new \Datetime($tglTransaksi.' 23:59:59');
        $minInput = new \Datetime($tglTransaksi.' 16:00:00');
        
        $tidakBisaEntry = 1;
        if($sekarang >= $minInput){
            if($sekarang <= $maxInput){
                $tidakBisaEntry = 0;
            }
        }

        if($tidakBisaEntry){
            $message .= convertElemenTglWaktuIndonesia($minInput->format('Y-m-d H:i:s')).' s.d '.convertElemenTglWaktuIndonesia($maxInput->format('Y-m-d H:i:s'));
        }
        /** jika  1 maka gak bisa lanjut, jika 0 maka bisa lanjut  */
        $blockEntry = 1;
        $lockEntryRHK = $this->db->select(array('kode_config', 'value'))->where(array('kode_config' => '_lock_entry_rhk', 'kode_farm' => $this->_farm, 'context' => 'rhk', 'status' => '1'))->get('SYS_CONFIG_GENERAL')->row_array();
        if (!empty($lockEntryRHK)) {
            $blockEntry = intval($lockEntryRHK['value']);
        }

        return array('status' => 1, 'content' => $tidakBisaEntry, 'block' => $blockEntry, 'message' => $message);
    }

    public function simpan_lhk()
    {
        $result = 0;
        $this->load->model('riwayat_harian_kandang/m_rhk_lain', 'r_lain');
        $this->load->model('riwayat_harian_kandang/m_rhk_rekomendasi_pakan', 'rekomendasi');
        $this->load->model('riwayat_harian_kandang/m_rhk_pakan', 'pakan');
        $this->load->model('riwayat_harian_kandang/m_rhk_penimbangan_bb', 'penimbangan');

        $data = json_decode($this->input->post('data'), 1);
        $lhk = $data['lhk'];
        $umur = $data['umur'];
        $jumlah_ayam_awal = $data['jumlah_ayam_awal'];
        $timbang_sekat = $data['timbang_sekat'];
        $lhk_pakan = $data['lhk_pakan'];
        $rekomendasi_pakan = $data['rekomendasi_pakan'];
        $keterangan_timeline = $data['keterangan_timeline'];
        $noreg = $lhk['no_reg'];
        $tgl = $lhk['tgl_transaksi'];
        /** cari jumlah awal */
        $jmlAwalArr = $this->getJumlahAwal($umur, $noreg);
        if ($umur <= 7) {
            $lhk['c_awal'] = $jmlAwalArr['c_jumlah_sebelumnya'] - $lhk['c_afkir'];
            $lhk['c_jumlah'] = $jmlAwalArr['c_jumlah_sebelumnya'] - $jmlAwalArr['c_mati'] - $lhk['c_mati'];
        } else {
            $lhk['c_awal'] = $jmlAwalArr['c_awal_sebelumnya'];
            //$lhk['c_jumlah'] = $jmlAwalArr['c_jumlah_sebelumnya'] - $lhk['c_mati'] - $lhk['c_afkir'] ;
            $lhk['c_jumlah'] = $jumlah_ayam_awal - $lhk['c_mati'] - $lhk['c_afkir'];
        }
        $lhk['user_buat'] = $this->_user;
        /** langsung ack saja */
        //$lhk['ack_kf'] = date('Y-m-d H:i:s');
        $upload_attachment = $this->do_upload('attachment');
        if ($upload_attachment['status']) {
            $data_rhk_lain = array(
                'no_reg' => $noreg,
                'tgl_transaksi' => $tgl,
                'tipe' => 'RHK',
                'keterangan' => $keterangan_timeline,
                'attachment' => $upload_attachment['data']['upload_data']['full_path'],
            );
        } else {
            $_r = array('status' => 0, 'message' => json_encode($upload_attachment['data']['error']));
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($_r));

            return;
        }

        $this->db->trans_begin();
        $this->m_rhk->insert($lhk);
        $this->r_lain->insert($data_rhk_lain);
        foreach ($timbang_sekat as $ts) {
            $this->penimbangan->insert($ts);
        }
        foreach ($lhk_pakan as $lp) {
            $lp['jml_terima'] = 0;
            $lp['jml_akhir'] = 0;
            $lp['brt_terima'] = 0;
            $lp['brt_akhir'] = 0;
            $berat_pakan = 0;
            if (!empty($lp['jml_pakai'])) {
                $berat_pakan = $this->get_berat_pakan($lp['jml_pakai'], $noreg, $lp['kode_barang']);
                if (empty($berat_pakan)) {
                    $berat_pakan = $lp['jml_pakai'] * 50;
                }
            }

            /** cari jumlah awal akhir lhk sebelumnya */
            $lhkLalu = $this->db->select('top 1 *', false)->order_by('tgl_buat', 'desc')->where('tgl_transaksi < \''.$tgl.'\'')->where(array('no_reg' => $noreg, 'kode_barang' => $lp['kode_barang'], 'keterangan1' => 'LHK'))->get('kandang_movement_d')->row_array();

            $stokAwal = 0;
            $beratAwal = 0;

            if (!empty($lhkLalu)) {
                $stokAwal = $lhkLalu['JML_AKHIR'];
                $beratAwal = $lhkLalu['BERAT_AKHIR'];
                /** cari total kirim setelah entry lhk terakhir */
                $kirimPakan = $this->db->select('sum(JML_ORDER) as kirim, sum(BERAT_ORDER) as berat_kirim')->where('tgl_buat > \''.$lhkLalu['TGL_BUAT'].'\'')->where(array('no_reg' => $noreg, 'kode_barang' => $lp['kode_barang'], 'keterangan1' => 'PENERIMAAN KANDANG'))->get('kandang_movement_d')->row_array();
            } else {
                $kirimPakan = $this->db->select('sum(JML_ORDER) as kirim, sum(BERAT_ORDER) as berat_kirim')->where(array('no_reg' => $noreg, 'kode_barang' => $lp['kode_barang'], 'keterangan1' => 'PENERIMAAN KANDANG'))->get('kandang_movement_d')->row_array();
            }

            $lp['jml_terima'] = $kirimPakan['kirim'];
            $lp['brt_terima'] = $kirimPakan['berat_kirim'];
            $lp['jml_akhir'] = $stokAwal + $kirimPakan['kirim'] - $lp['jml_pakai'];
            $lp['brt_pakai'] = $berat_pakan;
            $lp['brt_akhir'] = $beratAwal + $kirimPakan['berat_kirim'] - $berat_pakan;
            $this->pakan->insert($lp);
        }
        foreach ($rekomendasi_pakan as $rp) {
            $rp['tgl_transaksi'] = $tgl;
            $this->rekomendasi->insert($rp);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            //$this->db->trans_rollback();
            $this->db->trans_commit();
            $result = 1;
        }

        if ($result) {
            $_r = array('status' => 1, 'no_reg' => $noreg, 'tgl_lhk' => $tgl, 'message' => 'Data berhasil disimpan');
            /*
            $sudahEntry = $this->sudahEntryLhkSemuaFlok($noreg,$tgl);
            $_r['generate_order'] = $sudahEntry;
            */
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($_r));
        } else {
            $_r = array('status' => 0, 'message' => 'Data gagal disimpan');
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($_r));
        }
    }

    private function do_upload($file)
    {
        $config = array(
            'upload_path' => 'file_upload/rhk/',
            'allowed_types' => 'jpg|jpeg',
            'max_size' => 10240,
        );
        $result = array();
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload($file)) {
            $result['status'] = 0;
            $result['data'] = array('error' => $this->upload->display_errors());
        } else {
            $result['status'] = 1;
            $result['data'] = array('upload_data' => $this->upload->data());
        }

        return $result;
    }

    public function get_berat_pakan($stok, $no_reg, $kode_barang)
    {
        $items = $this->m_riwayat->get_kompensasi_stok($stok, $no_reg, $kode_barang);
        $total_berat = 0;
        $total_stok = 0;

        for ($i = 0; $i < count($items); ++$i) {
            $total_berat += $items[$i]['kg_out'];
        }

        return $total_berat;
    }

    private function getJumlahAwal($umur, $noreg)
    {
        /** jika $umur <= 7, maka c_jumlah = c_awal - sum(c_mati), c_awal = c_jumlah_sebelumnya - c_afkir , jika umur > 7 maka c_awal selalu tetap  dan c_jumlah = c_awal - c_mati - c_afkir */
        $totalMati = 0;
        $c_awal_sebelumnya = 0;
        if ($umur <= 7) {
            if ($umur == 1) {
                $tmp = $this->db->where(array('no_reg' => $noreg))->get('bap_doc')->row_array();
                $c_jumlah_sebelumnya = $tmp['STOK_AWAL'];
            } else {
                $_totalMati = $this->db->select_sum('c_mati')->where(array('no_reg' => $noreg))->get('rhk')->row_array();
                $totalMati = $_totalMati['c_mati'];
                $tmp = $this->db->select(array('c_awal', 'c_jumlah', 'c_mati', 'c_afkir'))->where(array('no_reg' => $noreg))->order_by('tgl_transaksi', 'desc')->get('rhk')->row_array();
                $c_jumlah_sebelumnya = $tmp['c_awal'];
            }
        } else {
            $tmp = $this->db->select(array('c_awal', 'c_jumlah', 'c_mati', 'c_afkir'))->where(array('no_reg' => $noreg))->order_by('tgl_transaksi', 'desc')->get('rhk')->row_array();
            $c_jumlah_sebelumnya = $tmp['c_jumlah'];
            $c_awal_sebelumnya = $tmp['c_awal'];
        }

        return array('c_jumlah_sebelumnya' => $c_jumlah_sebelumnya, 'c_mati' => $totalMati, 'c_awal_sebelumnya' => $c_awal_sebelumnya);
    }

    private function sudahEntryLhkSemuaFlok($noreg, $tglTransaksi)
    {
        $result = array('status' => 0, 'flok' => 0);
        $ks = $this->db->select('kode_siklus,flok_bdy')->where(array('no_reg' => $noreg))->get('kandang_siklus')->row_array();
        $query_rhk = $this->db->select(array('no_reg'))->like('no_reg', substr($noreg, 0, 10), 'after')->where(array('tgl_transaksi' => $tglTransaksi))->get_compiled_select('rhk');
        $rhk = $this->db->select('count(*) as total_rhk')->where(array('status_siklus' => 'O'))->where($ks)->where('no_reg not in ('.$query_rhk.')')->get('kandang_siklus')->row_array();

        if (empty($rhk['total_rhk'])) {
            $tglKebutuhan = tglSetelah($tglTransaksi, 2);
            $result['status'] = 1;
            $result['flok'] = $ks['flok_bdy'];
            $result['kode_farm'] = substr($noreg, 0, 2);
            $result['tgl_kebutuhan'] = $tglKebutuhan;
        }

        return $result;
    }

    public function generateOrder($kode_flok, $kode_farm, $tglKebutuhan)
    {
        $user = $this->_user;
        $sql = <<<QUERY
        	EXEC GENERATE_PICKING_LIST_V2
               '{$kode_farm}',
               '{$kode_flok}',
               '',
               '',
               '{$tglKebutuhan}',
               '{$user}'
QUERY;
        $_result = $this->db->query($sql)->row_array();
        switch ($_result['result']) {
            case '1':
                $this->result['message'] = 'Order kandang berhasil dibuat';
                $this->result['status'] = 1;
                break;
            case '2':
                $this->result['message'] = 'Order kandang harus urut atau belum dilakukan dropping tgl kebutuhan sebelumnya';
                break;
            case '7':
                $this->result['message'] = 'Tidak ada stok digudang';
                break;
            default:
                $this->result['message'] = 'Generate nomer order gagal';
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));
    }

    private function uploadImage()
    {
        $upload_attachment = $this->do_upload('attachment');
        if ($upload_attachment['status']) {
            return array('status' => 1, 'content' => $upload_attachment['data']['upload_data']['full_path']);
        } else {
            $_r = array('status' => 0, 'message' => json_encode($upload_attachment['data']['error']));

            return $_r;
        }
    }

    /** file dari convert online */
    public function extractImage()
    {
        $uploadImage = $this->uploadImage();
        $data = json_decode($this->input->post('data'), 1);
        $pakanPakai = $data['pakan_pakai'];
        $rekomendasiPakan = $data['rekomendasi_pakan'];
        if (!$uploadImage['status']) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($uploadImage));

            return;
        }
        $list_image = array();
        /*
        $list_image = array();
        $folder_image = FCPATH.DIRECTORY_SEPARATOR.'file_upload'.DIRECTORY_SEPARATOR.'rhk'.DIRECTORY_SEPARATOR;
        $image_path = $uploadImage['content'];
        $image_width = 90;
        $image_height = 52;
        $new_line = 62;
        $height_header = 79;
        #$y_awal = 278;
        $y_awal = 272;
        $barcode = array(
            'barcode' => array('x_awal' => 810, 'y_awal' => 85, 'width' => 170),
        );
        $sekat = array(
            'jumlah' => array('x_awal' => 653, 'y_awal' => $y_awal, 'width' => $image_width),
            'berat' => array('x_awal' => 950, 'y_awal' => $y_awal, 'width' => $image_width * 2)
        );
        $y_awal_populasi = $y_awal + ($new_line * 4) + $height_header;
        $populasi = array(
            'mati' => array('x_awal' => 480, 'y_awal' => $y_awal_populasi, 'width' => $image_width),
            'afkir' => array('x_awal' => 1170, 'y_awal' => $y_awal_populasi, 'width' => $image_width)
        );
        $y_awal_pakan = $y_awal_populasi + $new_line + $height_header;
        $pakan = array();
        if(!empty($pakanPakai)){
            $tmp_awal_pakan = $y_awal_pakan;
            foreach($pakanPakai as $_kb){
                $pakan[$_kb] = array('x_awal' => 1275, 'y_awal' => $tmp_awal_pakan, 'width' => $image_width);
                $tmp_awal_pakan += $new_line;
            }
        }
        $rekomendasi = array();
        if(!empty($rekomendasiPakan)){
            $tmp_awal_rekomendasi = $y_awal_pakan + ($new_line * count($pakan)) + $height_header;
            foreach($rekomendasiPakan as $_kb){
                $rekomendasi[$_kb] = array('x_awal' => 1340, 'y_awal' => $tmp_awal_rekomendasi, 'width' => $image_width);
                $tmp_awal_rekomendasi += $new_line;
            }
        }

        $this->load->library('image_lib');
        $config_crop['maintain_ratio'] = FALSE;
        $config_crop['source_image'] = $image_path;
        $config_crop['height'] = $image_height;
        //for resising we want 70% image quality
        $config_crop['quality'] = '90%';

        $list_image['sekat'] = array();
        foreach($sekat as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];
            for($i = 0 ; $i < 4 ; $i++){
                $tmp_image = $k.'_'.$i.'.jpg';
                $config_crop['width'] = $width;
                $config_crop['x_axis'] = $x_axis;
                $config_crop['y_axis'] = $y_axis;
                $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
                $this->image_lib->initialize($config_crop);
                if ( ! $this->image_lib->crop())
                {
                    echo $this->image_lib->display_errors();
                }else{
                    $list_image['sekat'][$k.'_'.$i] = $config_crop['new_image'];
                }
                $this->image_lib->clear();
                $y_axis += $new_line;
            }
        }
        $list_image['populasi'] = array();
        foreach($populasi as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];
            $i = 0;
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }else{
                $list_image['populasi'][$k.'_'.$i] = $config_crop['new_image'];
            }
            $this->image_lib->clear();
        }
        if(!empty($pakan)){
            $list_image['pakai'] = array();
            foreach($pakan as $k => $v){
                $x_axis = $v['x_awal'];
                $y_axis = $v['y_awal'];
                $width = $v['width'];
                $i = 0;
                $tmp_image = $k.'_'.$i.'.jpg';
                $config_crop['width'] = $width;
                $config_crop['x_axis'] = $x_axis;
                $config_crop['y_axis'] = $y_axis;
                $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
                $this->image_lib->initialize($config_crop);
                if ( ! $this->image_lib->crop())
                {
                    echo $this->image_lib->display_errors();
                }else{
                    $list_image['pakai'][$k.'_'.$i] = $config_crop['new_image'];
                }
                $this->image_lib->clear();
            }
        }
        if(!empty($rekomendasi)){
            $list_image['rekom'] = array();
            foreach($rekomendasi as $k => $v){
                $x_axis = $v['x_awal'];
                $y_axis = $v['y_awal'];
                $width = $v['width'];
                $i = 0;
                $tmp_image = $k.'_'.$i.'.jpg';
                $config_crop['width'] = $width;
                $config_crop['x_axis'] = $x_axis;
                $config_crop['y_axis'] = $y_axis;
                $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
                $this->image_lib->initialize($config_crop);
                if ( ! $this->image_lib->crop())
                {
                    echo $this->image_lib->display_errors();
                }else{
                    $list_image['rekom'][$k.'_'.$i] = $config_crop['new_image'];
                }
                $this->image_lib->clear();
            }
        }*/
        // proses scanning file jpg
        $this->scanningImage($list_image);
    }

    public function scanningImage($list_image = array())
    {
        $angka = array();
        if (!empty($list_image)) {
            $cli = 'python '.APPPATH.'third_party/scanning.py';
            foreach ($list_image as $g => $_li) {
                $angka[$g] = array();
                foreach ($_li as $k => $li) {
                    //$angka[$g][$k] = shell_exec($cli.' '.$li);
                    //if(is_null($angka[$g][$k])){
                    $angka[$g][$k] = 0;
                    //}
                }
            }
            $this->result['status'] = 1;
            $this->result['message'] = 'Proses scanning berhasil';
        } else {
            $this->result['message'] = 'Proses scanning gagal';
        }

        $this->result['status'] = 1;
        $this->result['message'] = 'Proses scanning berhasil';
        $this->result['content'] = $angka;
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));
    }

    public function lihat()
    {
        $this->load->model('riwayat_harian_kandang/M_rhk_pakan', 'rhk_pakan');
        $this->load->model('riwayat_harian_kandang/M_rhk_penimbangan_bb', 'rhk_bb');
        $this->load->model('riwayat_harian_kandang/M_rhk_rekomendasi_pakan', 'rhk_rekomendasi');
        $noreg = $this->input->post('noreg');
        $tglTransaksi = $this->input->post('tgl_transaksi');
        $keyWhere = array(
            'no_reg' => $noreg,
            'tgl_transaksi' => $tglTransaksi,
        );
        $rhk = $this->m_rhk->as_array()->get_by($keyWhere);
        $pegawai_cari = array($rhk['USER_BUAT']);
        if (!empty($rhk['ACK1'])) {
            array_push($pegawai_cari, $rhk['ACK1']);
        }
        $pegawai = arr2DToarrKey($this->db->select('nama_pegawai,kode_pegawai')->where_in('kode_pegawai', $pegawai_cari)->get('m_pegawai')->result_array(), 'kode_pegawai');
        $data = array(
            'rhk' => $rhk,
            'rhk_pakan' => $this->rhk_pakan->getPemakaian($keyWhere)->result_array(),
            'rhk_penimbangan' => $this->rhk_bb->as_array()->get_many_by($keyWhere),
            'rhk_rekomendasi' => $this->rhk_rekomendasi->as_array()->get_by($keyWhere),
            'tgl_transaksi' => $tglTransaksi,
            'pegawai' => $pegawai,
            'kandang' => $this->db->select('datediff(day,tgl_doc_in,\''.$tglTransaksi.'\') as umur', false)->select('kode_kandang,flok_bdy,tgl_doc_in')->where(array('no_reg' => $noreg))->get('kandang_siklus')->row_array(),
        );
        $this->load->view('riwayat_harian_kandang/viewLhk', $data);
    }
}
