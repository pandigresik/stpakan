<?php

defined('BASEPATH') or exit('No direct script access allowed');
/* daftarkan route yang ada kemungkinan dilakukan sinkronisasi datanya */
$config['route_sinkronisasi'] = array(
    /* forecast */
            'forecast/forecast/update_flok', 'forecast/forecast/approve_rilis_rencanadocin', 'forecast/forecast/update_tgl_docin', 'forecast/forecast/update_std_farm', 'forecast/forecast/approveRejectKonfirmasiDOCIn'

    /* permintaan pakan */
            , 'permintaan_pakan/permintaan_pakan/simpan_pp', 'permintaan_pakan/permintaan_pakan/reset_review_pp', 'permintaan_pakan/permintaan_pakan/reset_approve_pp', 'permintaan_pakan/permintaan_pakan/approve_pp_budidaya', 'permintaan_pakan/permintaan_pakan/reject_pp_kadiv', 'permintaan_pakan_v2/permintaan_pakan/simpan_pp', 'permintaan_pakan_v2/permintaan_pakan/approve_pp_budidaya'
    /* pembelian_pakan */
        , 'permintaan_pakan_v2/pembelian_pakan/simpan_do', 'permintaan_pakan_v2/pembelian_pakan/approvereject'
    /* retur pakan rusak, pengembalian sak kosong */
        , 'pengembalian_sak/pengembalian/simpan', 'pengembalian_sak/approval/approve_retur_sak', 'pengembalian_sak/approval/review_retur_sak', 'pengembalian_pakan_rusak/pengembalian/simpan'

    /* Penerimaan Pakan */
            , 'penerimaan_pakan/transaksi/simpan_penerimaan'

    /* Pengambilan Barang */
            , 'pengambilan_barang/main/simpan_generate_permintaan', 'pengambilan_barang/transaksi/simpan_data'

    /* Review Pakan Rusak */
            , 'review_pakan_rusak/review/simpan'

    /* Mutasi Pakan */
            , 'mutasi_pakan/transaksi/simpan_mutasi', 'mutasi_pakan/main/tindak_lanjut'

    /* Master */
            , 'master/periode_siklus/add_periode_siklus', 'master/periode_siklus/update_periode_siklus', 'master/ekspedisi/add_ekspedisi', 'master/ekspedisi/update_ekspedisi', 'master/uom/add_uom', 'master/uom/update_uom', 'master/daftar_op_marketing/add_op_marketing', 'master/daftar_op_marketing/update_op_marketing', 'master/harga_barang/add_harga_barang', 'master/harga_barang/update_harga_barang', 'master/barang/add_barang', 'master/barang/update_barang', 'master/farm/add_farm', 'master/farm/update_farm', 'master/gudang/add_gudang', 'master/gudang/update_gudang', 'master/kandang/add_kandang', 'master/kandang/update_kandang', 'master/kavling/simpanKavling', 'master/kavling/ubahKavling', 'master/pelanggan/add_pelanggan', 'master/pelanggan/update_pelanggan', 'master/pengawas/add_pengawas', 'master/pengawas/update_pengawas', 'master/std_budidaya_bdy/simpan_std_budidaya', 'master/kalenderlibur/add', 'master/kalenderlibur/edit', 'master/kalenderlibur/delete', 'master/pallet/simpan_berat_pallet', 'master/pallet/ubah_status_pallet', 'master/hand_pallet/simpan_berat_hand_pallet', 'master/hand_pallet/ubah_status_hand_pallet', 'master/hand_pallet/ubah_default_hand_pallet'
        /* riwayat harian kandang */
            , 'riwayat_harian_kandang/riwayat_harian_kandang/simpan_lhk'
            //,'riwayat_harian_kandang/riwayat_harian_kandang_bdy/buat_pengajuan_retur'
            , 'riwayat_harian_kandang/pemantauan_lhk_bdy/simpan_ack', 'riwayat_harian_kandang/pemantauan_lhk_bdy/simpan_kadep', 'riwayat_harian_kandang/pemantauan_lhk_bdy/simpan_kadiv', 'riwayat_harian_kandang/cetak_form_lhk/insert_rhk_cetak'
        /*realisasi panen*/
<<<<<<< HEAD
            , 'riwayat_harian_kandang/realisasi_panen/simpan', 'riwayat_harian_kandang/realisasi_panen/simpan_admin_farm', 'api/verifikasiDOPanen/verifikasiDOkeluar'
        /*berita acara penerimaan doc in*/
            , 'penerimaan_docin/berita_acara/ackapprove', 'penerimaan_docin/import_box/simpan_box'

=======
            , 'riwayat_harian_kandang/realisasi_panen/simpan', 'riwayat_harian_kandang/realisasi_panen/simpan_admin_farm'

        /*berita acara penerimaan doc in*/
            , 'penerimaan_docin/berita_acara/ackapprove'
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
        /*pengajuan permintaan glangsing*/
        , 'permintaan_glangsing/pengajuan/simpan', 'permintaan_glangsing/pengajuan/konfirmasiPengambilan', 'permintaan_glangsing/pengembalian_sak/konfirmasiPengembalian', 'permintaan_glangsing/pengembalian_sak/simpanAck', 'report/kontrol_stok_glangsing/updatePpsk'

        /* realisasi penjualan glangsing */
        , 'sales_order/realisasi_penjualan/simpan'
        /* plotting kendaraan so_do */
        , 'sales_order/plotting_kendaraan/save'
        /* pemusnahan bangkai */
        , 'sales_order/pemusnahan_bangkai/simpan'

        /* retur pakan kandang */
        , 'rekap_retur_pakan/retur_sisa_pakan/proses_pengajuan_retur', 'rekap_retur_pakan/retur_sisa_pakan/proses_pengajuan_retur_ubah', 'rekap_retur_pakan/retur_sisa_pakan/proses_persetujuan'
        /* pengembalian budget otomatis */
        , 'api/pengembalian_budget_otomatis/resetBudget'
        /* terima pakan farm lain */
        , 'penerimaan_pakan/terima_pakan/simpan'

        /* pengajuan harga simpan dan approval */
        , 'sales_order/pengajuan_harga/simpan', 'sales_order/pengajuan_harga/approval'
        /* import realisasi panen do */
        , 'sinkronisasi/panen_do/simpanDO'
        /* update sys_config_general */
        , 'master/general_config/simpan'
        /* batal SO */
        , 'sales_order/sales_order/batalSO'
        /* simpan pengajuan budget glangsing save_budget  */
        , 'budget_pengembalian_glangsing/main/save_budget'
        /* plotting pelaksana */
        , 'kandang/plotting_pelaksana/save', 'kandang/plotting_pelaksana/ack',
<<<<<<< HEAD
        /** timbang doc */
        'api/timbangDoc/simpanDataTimbang',
        /** timbang pakan */
        'api/timbangPakan/simpanTimbang',
=======
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
);
$config['methode_sinkronisasi'] = array(
    /* forecast */
            'forecast/forecast/update_flok' => null, 'forecast/forecast/approve_rilis_rencanadocin' => 'approve_rilis_rencanadocin', 'forecast/forecast/update_tgl_docin' => 'update_tgl_docin', 'forecast/forecast/update_std_farm' => 'update_std_farm', 'forecast/forecast/approveRejectKonfirmasiDOCIn' => 'approveRejectKonfirmasiDOCIn', 'permintaan_pakan/permintaan_pakan/reject_pp_kadiv' => 'reject_pp_kadiv'
    /* permintaan pakan */
            , 'permintaan_pakan/permintaan_pakan/simpan_pp' => 'simpan_pp', 'permintaan_pakan/permintaan_pakan/reset_review_pp' => 'reset_review_pp', 'permintaan_pakan/permintaan_pakan/reset_approve_pp' => 'reset_approve_pp', 'permintaan_pakan/permintaan_pakan/approve_pp_budidaya' => 'approve_pp_budidaya', 'permintaan_pakan_v2/permintaan_pakan/simpan_pp' => 'simpan_pp_v2', 'permintaan_pakan_v2/permintaan_pakan/approve_pp_budidaya' => 'approve_pp_budidaya_v2'
    /* pembelian_pakan */
            , 'permintaan_pakan_v2/pembelian_pakan/simpan_do' => 'simpan_do', 'permintaan_pakan_v2/pembelian_pakan/approvereject' => 'approvereject_do'

    /* retur pakan rusak, pengembalian sak kosong */
            , 'pengembalian_sak/pengembalian/simpan' => 'pengembalian_sak_simpan', 'pengembalian_sak/approval/approve_retur_sak' => 'approve_retur_sak', 'pengembalian_sak/approval/review_retur_sak' => 'review_retur_sak', 'pengembalian_pakan_rusak/pengembalian/simpan' => 'pengembalian_pakan_rusak_simpan'

    /* Penerimaan Pakan */
            , 'penerimaan_pakan/transaksi/simpan_penerimaan' => 'simpan_penerimaan_pakan'

    /* Pengambilan Barang */
            , 'pengambilan_barang/main/simpan_generate_permintaan' => 'simpan_generate_pengambilan', 'pengambilan_barang/transaksi/simpan_data' => 'simpan_pengambilan_barang'

    /* Review Pakan Rusak */
            , 'review_pakan_rusak/review/simpan' => 'simpan_review_pakan_rusak'

    /* Mutasi Pakan */
            , 'mutasi_pakan/transaksi/simpan_mutasi' => 'simpan_mutasi', 'mutasi_pakan/main/tindak_lanjut' => 'review_mutasi_pakan'

    /* Master */
            , 'master/periode_siklus/add_periode_siklus' => 'add_periode_siklus', 'master/periode_siklus/update_periode_siklus' => 'update_periode_siklus', 'master/ekspedisi/add_ekspedisi' => 'add_ekspedisi', 'master/ekspedisi/update_ekspedisi' => 'update_ekspedisi', 'master/uom/add_uom' => 'add_uom', 'master/uom/update_uom' => 'update_uom', 'master/daftar_op_marketing/add_op_marketing' => 'add_op_marketing', 'master/daftar_op_marketing/update_op_marketing' => 'update_op_marketing', 'master/harga_barang/add_harga_barang' => 'add_harga_barang', 'master/harga_barang/update_harga_barang' => 'update_harga_barang', 'master/barang/add_barang' => 'add_barang', 'master/barang/update_barang' => 'update_barang', 'master/farm/add_farm' => 'add_farm', 'master/farm/update_farm' => 'update_farm', 'master/gudang/add_gudang' => 'add_gudang', 'master/gudang/update_gudang' => 'update_gudang', 'master/kandang/add_kandang' => 'add_kandang', 'master/kandang/update_kandang' => 'update_kandang', 'master/kavling/simpanKavling' => 'add_kavling', 'master/kavling/ubahKavling' => 'update_kavling', 'master/pelanggan/add_pelanggan' => 'add_pelanggan', 'master/pelanggan/update_pelanggan' => 'update_pelanggan', 'master/pengawas/add_pengawas' => 'add_pengawas', 'master/pengawas/update_pengawas' => 'update_pengawas', 'master/std_budidaya_bdy/simpan_std_budidaya' => 'simpan_std_budidaya', 'master/pallet/simpan_berat_pallet' => 'simpan_berat_pallet', 'master/pallet/ubah_status_pallet' => 'ubah_status_pallet', 'master/hand_pallet/simpan_berat_hand_pallet' => 'simpan_berat_hand_pallet', 'master/hand_pallet/ubah_status_hand_pallet' => 'ubah_status_hand_pallet', 'master/hand_pallet/ubah_default_hand_pallet' => 'ubah_default_hand_pallet', 'master/kalenderlibur/add' => 'tambah_hari_libur', 'master/kalenderlibur/edit' => 'edit_hari_libur', 'master/kalenderlibur/delete' => 'hapus_hari_libur'
            /* riwayat harian kandang */
            , 'riwayat_harian_kandang/riwayat_harian_kandang/simpan_lhk' => 'simpan_lhk_bdy'
            //,'riwayat_harian_kandang/riwayat_harian_kandang_bdy/buat_pengajuan_retur' => 'lhk_buat_pengajuan_retur'
            , 'riwayat_harian_kandang/pemantauan_lhk_bdy/simpan_ack' => 'lhk_simpan_ack', 'riwayat_harian_kandang/pemantauan_lhk_bdy/simpan_kadep' => 'lhk_simpan_kadep', 'riwayat_harian_kandang/pemantauan_lhk_bdy/simpan_kadiv' => 'lhk_simpan_kadiv', 'riwayat_harian_kandang/cetak_form_lhk/insert_rhk_cetak' => 'lhk_cetak'
            /*realisasi panen*/
<<<<<<< HEAD
            , 'riwayat_harian_kandang/realisasi_panen/simpan' => 'realisasi_panen', 'riwayat_harian_kandang/realisasi_panen/simpan_admin_farm' => 'simpan_admin_farm', 'api/verifikasiDOPanen/verifikasiDOkeluar' => 'verifikasi_panen_keluar'
            /*berita acara penerimaan doc in*/
            , 'penerimaan_docin/berita_acara/ackapprove' => 'approvebapd', 'penerimaan_docin/import_box/simpan_box' => 'importbapdbox'
=======
            , 'riwayat_harian_kandang/realisasi_panen/simpan' => 'realisasi_panen', 'riwayat_harian_kandang/realisasi_panen/simpan_admin_farm' => 'simpan_admin_farm'
            /*berita acara penerimaan doc in*/
            , 'penerimaan_docin/berita_acara/ackapprove' => 'approvebapd'
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526

            /*pengajuan permintaan glangsing*/
            , 'permintaan_glangsing/pengajuan/simpan' => 'pengajuan_glangsing_simpan', 'permintaan_glangsing/pengajuan/konfirmasiPengambilan' => 'pengajuan_glangsing_pengambilan', 'permintaan_glangsing/pengembalian_sak/konfirmasiPengembalian' => 'pengajuan_glangsing_pengembalian_sak', 'permintaan_glangsing/pengembalian_sak/simpanAck' => 'pengajuan_glangsing_pengembalian_sak_ack', 'report/kontrol_stok_glangsing/updatePpsk' => 'kontrol_stok_glangsing_updateppsk'

            /*realisasi penjualan glangsing */
            , 'sales_order/realisasi_penjualan/simpan' => 'realisasi_penjualan_glangsing'
            /* plotting kendaraan so_do */
            , 'sales_order/plotting_kendaraan/save' => 'plotting_kendaraan_so_do'
            /* pemusnahan bangkai */
            , 'sales_order/pemusnahan_bangkai/simpan' => 'pemusnahan_bangkai'
            /* retur pakan kandang */
            , 'rekap_retur_pakan/retur_sisa_pakan/proses_pengajuan_retur' => 'pengajuan_retur_pakan', 'rekap_retur_pakan/retur_sisa_pakan/proses_pengajuan_retur_ubah' => 'ubah_pengajuan_retur_pakan', 'rekap_retur_pakan/retur_sisa_pakan/proses_persetujuan' => 'proses_persetujuan_retur_pakan'

                /* pengembalian budget otomatis */
            , 'api/pengembalian_budget_otomatis/resetBudget' => 'reset_budget_otomatis'
            /* terima pakan farm */
            , 'penerimaan_pakan/terima_pakan/simpan' => 'terima_pakan_farm'
            /* pengajuan harga simpan dan approval */
            , 'sales_order/pengajuan_harga/simpan' => 'simpan_pengajuan_harga', 'sales_order/pengajuan_harga/approval' => 'approve_reject_pengajuan_harga'
            /* import realisasi panen do */
            , 'sinkronisasi/panen_do/simpanDO' => 'import_panen_do'
            /* update sys_config_general */
            , 'master/general_config/simpan' => 'general_config_simpan'
            /* batal SO */
            , 'sales_order/sales_order/batalSO' => 'batalSO'
            /* simpan pengajuan budget glangsing save_budget  */
            , 'budget_pengembalian_glangsing/main/save_budget' => 'simpanBudgetGlangsing'
            /* plotting pelaksana */
<<<<<<< HEAD
            , 'kandang/plotting_pelaksana/save' => 'plotting_pelaksana', 'kandang/plotting_pelaksana/ack' => 'plotting_pelaksana_ack'
            /** timbang doc */    
            ,'api/timbangDoc/simpanDataTimbang' => 'timbang_doc_simpan'
                /** timbang pakan */
            ,'api/timbangPakan/simpanTimbang' => 'timbang_pakan_simpan'
=======
            , 'kandang/plotting_pelaksana/save' => 'plotting_pelaksana', 'kandang/plotting_pelaksana/ack' => 'plotting_pelaksana_ack',
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
);
