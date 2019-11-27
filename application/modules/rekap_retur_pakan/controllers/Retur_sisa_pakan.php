<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Retur_sisa_pakan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('M_retur_sisa_pakan', 'm_retur');
        $this->load->model('riwayat_harian_kandang/M_riwayat_harian_kandang', 'm_riwayat');
    }

    public function index()
    {
        $kodefarm = $this->session->userdata('kode_farm');
        $farm = $this->m_riwayat->get_farm($kodefarm);
        $fulldate = $this->m_riwayat->get_today();
        $date = explode(' ', $fulldate['today']);

        $data['kode_farm'] = $kodefarm;
        $data['nama_farm'] = $farm['nama_farm'];
        $data['today'] = $date[0];
        $data['today_full'] = $fulldate['today'];

        //$data['retur_pakan'] = $this->m_retur->get_retur_sisa_pakan($kodefarm);

        $this->load->view('retur_sisa_pakan', $data);
    }

    public function get_table_retur_sisa_pakan_list()
    {
        $kode_farm = ($this->input->post('kode_farm')) ? $this->input->post('kode_farm') : null;
        $tgl_awal = ($this->input->post('tgl_awal') != '') ? $this->input->post('tgl_awal') : null;
        $tgl_akhir = ($this->input->post('tgl_akhir') != '') ? $this->input->post('tgl_akhir') : null;
        $data = $this->m_retur->get_retur_sisa_pakan($kode_farm, $tgl_awal, $tgl_akhir);
        $this->load->view('rekap_retur_pakan/table_list_retur', array('retur_pakan' => $data));
    }

    public function get_kandang_farm()
    {
        $kode_farm = $this->input->post('kode_farm');
        $kandang = $this->m_retur->get_kandang_siklus($kode_farm);

        echo json_encode($kandang);
    }

    public function get_kandang_tujuan()
    {
        $kode_farm = $this->input->post('kode_farm');
        $no_reg = $this->input->post('no_reg');
        $kandang = $this->m_retur->get_kandang_tujuan($kode_farm, $no_reg);

        echo json_encode($kandang);
    }

    public function get_gudang_tujuan()
    {
        $kode_farm = $this->input->post('kode_farm');
        $gudang = $this->m_retur->get_gudang_tujuan($kode_farm);

        echo json_encode($gudang);
    }

    public function get_sisa_pakan()
    {
        $no_reg = $this->input->post('no_reg');
        $pakan = $this->m_retur->get_sisa_pakan($no_reg);

        echo json_encode($pakan);
    }

    public function proses_pengajuan_retur_ubah()
    {
        $fulldate = $this->m_riwayat->get_today();

        $no_reg = $this->input->post('no_reg');
        $no_retur = $this->input->post('no_retur');
        // $tgl_retur = $this->input->post('tgl_retur');
        $kode_barang = $this->input->post('kode_barang');
        $jml_retur = $this->input->post('jml_retur');
        $brt_retur = $this->input->post('brt_retur');
        $keterangan1 = $this->input->post('keterangan1');

        // $retur_kandang['tgl_retur'] = $tgl_retur;
        $retur_kandang['tgl_buat'] = $fulldate['today'];
        $retur_kandang['keterangan1'] = $keterangan1;
        // $retur_kandang['tgl_retur'] = $tgl_retur;

        $retur_kandang_d = array();
        for ($i = 0; $i < count($kode_barang); ++$i) {
            $retur_kandang_d[$i]['jml_retur'] = $jml_retur[$i];
            $retur_kandang_d[$i]['brt_retur'] = $brt_retur[$i];
        }

        $result = $this->m_retur->ubah_retur($no_reg, $no_retur, $kode_barang, $retur_kandang, $retur_kandang_d);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('result' => 'success', 'content' => $no_retur)));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('result' => 'failed')));
        }
    }

    public function proses_pengajuan_retur()
    {
        $tgl_buat = $this->input->post('tgl_buat');
        $kode_farm = $this->input->post('kode_farm');
        $no_reg = $this->input->post('no_reg');
        $tgl_retur = $this->input->post('tgl_retur');
        $alokasi_retur = $this->input->post('alokasi_retur');
        $alokasi_tujuan = $this->input->post('alokasi_tujuan');
        $alokasi_pakan = $this->input->post('alokasi_pakan');
        $alokasi_sak = $this->input->post('alokasi_sak');
        $sisa_kode_pakan = $this->input->post('sisa_kode_pakan');
        $sisa_jml_pakan = $this->input->post('sisa_jml_pakan');
        $sisa_brt_pakan = $this->input->post('sisa_brt_pakan');

        $sisa_pakan = array();
        for ($i = 0; $i < count($sisa_kode_pakan); ++$i) {
            $sisa_pakan[] = array(
                'kode_barang' => $sisa_kode_pakan[$i],
                'jml_barang' => $sisa_jml_pakan[$i],
                'brt_barang' => $sisa_brt_pakan[$i],
            );
        }

        $retur_pakan = array();
        for ($i = 0; $i < count($alokasi_retur); ++$i) {
            $retur_pakan[] = array(
                'tujuan' => $alokasi_tujuan[$i],
                'pakan' => $alokasi_pakan[$i],
                'sak' => $alokasi_sak[$i],
                'berat' => $this->get_berat_retur_pakan($sisa_pakan, $alokasi_pakan[$i], $alokasi_sak[$i]),
            );
        }

        $result = $this->m_retur->simpan_retur($no_reg, $this->session->userdata('kode_user'), $tgl_retur, $tgl_buat, $retur_pakan);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('result' => 'success', 'content' => $result)));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array('result' => 'failed')));
        }
    }

    public function get_berat_retur_pakan($sisa_pakan, $kode_barang_retur, $jml_retur)
    {
        $brt_retur = 0;
        foreach ($sisa_pakan as $sisa) {
            if ($sisa['kode_barang'] == $kode_barang_retur) {
                $rata2 = $sisa['brt_barang'] / $sisa['jml_barang'];
                $brt_retur = $jml_retur * $rata2;
            }
        }

        return $brt_retur;
    }

    public function get_retur_sisa_pakan_list()
    {
        $kode_farm = ($this->input->post('kode_farm')) ? $this->input->post('kode_farm') : null;
        $tgl_awal = ($this->input->post('tgl_awal') != '') ? $this->input->post('tgl_awal') : null;
        $tgl_akhir = ($this->input->post('tgl_akhir') != '') ? $this->input->post('tgl_akhir') : null;
        $no_retur = ($this->input->post('no_retur') and $this->input->post('no_retur') != '') ? $this->input->post('no_retur') : null;

        $data = $this->m_retur->get_retur_sisa_pakan($kode_farm, $tgl_awal, $tgl_akhir, $no_retur);
        $can_edit = 0;
        $sisa_pakan = array();
        if (!empty($data)) {
            $no_reg = $data[0]['no_reg'];
            $kode_tujuan_retur = [$data[0]['kode_tujuan_retur']];
            $can_edit = $data[0]['can_edit'];
            if ($can_edit) {
                $kode_farm = $this->input->post('kode_farm');
                $kandang_tujuan = $this->m_retur->get_kandang_tujuan($kode_farm, $no_reg);
                if (!empty($kandang_tujuan)) {
                    $kode_tujuan_retur = array_merge($kode_tujuan_retur, array_column($kandang_tujuan, 'no_reg'));
                }

                $gudang_tujuan = array_column($this->m_retur->get_gudang_tujuan($kode_farm), 'kode_gudang');
                $kode_tujuan_retur = array_merge($kode_tujuan_retur, $gudang_tujuan);

                $kode_tujuan_retur = array_unique($kode_tujuan_retur);
                asort($kode_tujuan_retur);
            }
            $data[0]['list_kode_tujuan_retur'] = $kode_tujuan_retur;
            /* cari sisa pakan dikandang */
            $sisa_pakan = $this->db
                        ->select(array('kandang_movement.kode_barang', 'm_barang.nama_barang', 'kandang_movement.jml_stok'))
                        ->where(array('no_reg' => $no_reg))
                        ->join('m_barang', 'm_barang.kode_barang = kandang_movement.kode_barang')
                        ->get('kandang_movement')
                        ->result_array();
        }

        echo json_encode(array('data' => $data, 'sisa_pakan' => $sisa_pakan));
    }

    public function get_retur_pakan_list()
    {
        $kode_farm = ($this->input->post('kode_farm')) ? $this->input->post('kode_farm') : null;
        $tgl_awal = ($this->input->post('tgl_awal') != '') ? $this->input->post('tgl_awal') : null;
        $tgl_akhir = ($this->input->post('tgl_akhir') != '') ? $this->input->post('tgl_akhir') : null;

        $data = $this->m_retur->get_retur_pakan($kode_farm, $tgl_awal, $tgl_akhir);

        echo json_encode($data);
    }

    public function get_retur_pakan()
    {
        $no_retur = ($this->input->post('no_retur')) ? $this->input->post('no_retur') : null;
        $no_reg = ($this->input->post('no_reg')) ? $this->input->post('no_reg') : null;

        $pakan = $this->m_retur->get_retur_pakan_detail($no_retur, $no_reg);

        echo json_encode($pakan);
    }

    public function proses_pengajuan()
    {
        $fulldate = $this->m_riwayat->get_today();

        $no_retur = ($this->input->post('no_retur')) ? $this->input->post('no_retur') : null;
        $no_reg = ($this->input->post('no_reg')) ? $this->input->post('no_reg') : null;

        $result = $this->m_retur->proses_pengajuan_retur($no_retur, $no_reg, $this->session->userdata('kode_user'));

        if ($result) {
            echo json_encode(array('result' => 'success'));
        } else {
            echo json_encode(array('result' => 'failed'));
        }
    }

    public function proses_persetujuan()
    {
        $fulldate = $this->m_riwayat->get_today();
        $kodefarm = $this->session->userdata('kode_farm');
        $no_retur = ($this->input->post('no_retur')) ? $this->input->post('no_retur') : null;
        $lvl_user = ($this->input->post('lvl_user')) ? $this->input->post('lvl_user') : null;
        $dataRetur = $this->input->post('data_retur');
        if (!empty($dataRetur)) {
            $kandang_tujuan = $dataRetur['kandang_tujuan'];
            $detail_retur = $dataRetur['detail'];

            $this->db->where(array('no_retur' => $no_retur))->update('retur_kandang', array('keterangan1' => $kandang_tujuan));
            foreach ($detail_retur as $kb => $jml_stok) {
                $this->db->set('brt_retur', '(brt_retur/jml_retur) * '.$jml_stok, false)->where(array('no_retur' => $no_retur, 'kode_barang' => $kb))->update('retur_kandang_d', array('jml_retur' => $jml_stok));
            }
        }
        $result = $this->m_retur->proses_persetujuan_retur_sisa($no_retur, $this->session->userdata('kode_user'), $this->session->userdata('level_user'), $kodefarm);

        if ($result !== 'faield') {
            echo json_encode(array('result' => 'success', 'nama_pegawai' => $result, 'content' => $no_retur));
        } else {
            echo json_encode(array('result' => 'failed'));
        }
    }

    public function reject_retur()
    {
        $kodefarm = $this->session->userdata('kode_farm');
        $no_retur = ($this->input->post('no_retur')) ? $this->input->post('no_retur') : null;
        $user = $this->session->userdata('kode_user');
        $keterangan2 = ($this->input->post('keterangan2')) ? $this->input->post('keterangan2') : null;
        $result = $this->db->where(array('no_retur' => $no_retur))->set('tgl_approve', 'getdate()', false)->update('retur_kandang', array('keterangan2' => $keterangan2, 'user_approve' => $user));

        if ($result) {
            $this->result['status'] = 1;
            $this->result['message'] = 'Pertanggungjawaban Sisa Pakan di Kandang berhasil di-reject';
        } else {
            $this->result['message'] = 'Pertanggungjawaban Sisa Pakan di Kandang gagal di-reject';
        }

        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($this->result));
    }

    public function print_sj()
    {
        $tgl_tutup_siklus = $this->input->post('inp_print_tgl_lhk');
        $nama_farm = $this->input->post('inp_print_nama_farm');
        $nama_kandang = $this->input->post('inp_print_nama_kandang');
        $no_retur = $this->input->post('inp_print_no_retur');
        $no_reg = $this->input->post('inp_print_no_reg');
        $nama_retur = $this->input->post('inp_print_nama_retur');
        $nama_approve = $this->input->post('inp_print_nama_approve');
        $nama_terima = $this->input->post('inp_print_nama_terima');

        $pakan = $this->m_retur->get_retur_pakan_detail($no_retur, $no_reg);

        $items = array();
        for ($i = 0; $i < count($pakan); ++$i) {
            $items[] = array(
                'kodebarang' => $pakan[$i]['KODE_BARANG'],
                'namabarang' => $pakan[$i]['NAMA_BARANG'],
                'jumlah' => $pakan[$i]['JML'],
                'berat' => $pakan[$i]['BRT'],
                'bentuk' => $pakan[$i]['BENTUK_BARANG'],
            );
        }

        $this->load->library('Pdf');
        $pdf = new Pdf('P', PDF_UNIT, 'A5', true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        $data['namafarm'] = $nama_farm;
        $data['noretur'] = $no_retur;
        $data['namakandang'] = $nama_kandang;
        $data['tgltutupsiklus'] = $tgl_tutup_siklus;
        $data['namaretur'] = $nama_retur;
        $data['namaapprove'] = !empty($nama_approve) ? $nama_approve : '_________________';
        $data['namaterima'] = !empty($nama_terima) ? $nama_terima : '_________________';
        $data['items'] = $items;
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4,
        );

        // PRINT VARIOUS 1D BARCODES

        // CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9.
        $params = $pdf->serializeTCPDFtagParameters(array(
                    $no_retur,
                    'C128',
                    '',
                    '',
                    27,
                    5,
            ));
        $b = '<tcpdf method="write1DBarcode" params="'.$params.'" />';
        $data['barcode'] = $b;
        $html = $this->load->view('rekap_retur_pakan/retur', $data, true);

        // echo $html;
        $pdf->AddPage();
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('rekap_retur_pakan.pdf', 'I');
    }

    public function generate_noretur(&$no_retur, $asal, $tujuan)
    {
        $temp = '';
        if (count($no_retur) > 0) {
            echo count($no_retur);
            for ($i = 0; $i < count($no_retur); ++$i) {
                if ($no_retur[$i]['asal'] == $asal and $no_retur[$i]['tujuan'] != $tujuan) {
                    $temp = array('asal' => $asal, 'tujuan' => $tujuan, 'no_retur' => (($no_retur[$i]['no_retur'] * 1) + 1));
                }
            }
        } else {
            $temp = array('asal' => $asal, 'tujuan' => $tujuan, 'no_retur' => '1');
        }

        if (!empty($temp)) {
            $no_retur[] = $temp;
        }
    }
}
