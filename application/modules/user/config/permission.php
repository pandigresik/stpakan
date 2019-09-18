<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* mapping id token ke url, key dari array harus sama dengan yang ada didatabase taabel workbook */
$config['workbook'] = array(
	'tiketmasuk' => array('label' => 'tiketmasuk', 'id'=>'kendaraanmasuk'),
	'kendaraantolak' => array('label' => 'kendaraan tolak','id' => 'kendaraantolak'),
	'pemantauankendaraan' => array('label' => 'pemantauankendaraan','id' => 'pemantauankendaraan'),
	'pkendaraanview' => array('label' => 'pemantauankendaraan','id' => 'pkendaraanview'),
	'ambilsample' => array('label' => 'ambilsample','id'=>'pengambilansample'),
	'spabb_entry' => array('label' => 'spabb entry','id' => 'spabb'),
	'spabb_approve' => array('label' => 'spabb admin','id' => 'spabb'),
	'composit_sample' => array('label' => 'komposit sampel','id' => 'composit_sample'),
	'analysis_sample' => array('label' => 'analysis sample','id' => 'analysis_sample'),
	'as_print' => array('label' => 'analysis sample','id' => 'analysis_sample'),
	'as_entry' => array('label' => 'analysis sample','id' => 'analysis_sample'),
	'as_review' => array('label' => 'analysis sample','id' => 'analysis_sample'),
	'as_view' => array('label' => 'analysis sample','id' => 'analysis_sample'),
	'confirmation_qc' => array('label' => 'confirmation qc','id' => 'confirmation_qc'),
	//'21' => array('label' => 'confirmation dirut','id' => 'confirmation_dirut'),	
	'confirmation_driver' => array('label' => 'confirmation driver','id' => 'confirmation_driver'),
	'pk_print' => array('label' => 'pemantauankendaraan','id' => 'pk_print'),
	'pk_checkout' => array('label' => 'pemantauankendaraan','id' => 'pk_checkout'),
	'pemantauanlogistik' => array('label' => 'pemantauanlogistik','id' => 'pemantauanlogistik'),
	'evaluasiprobe'	=> array('label' => 'evaluasiprobe','id' => 'evaluasiprobe'),
	'cek_op'	=> array('label' => 'cekop','id' => 'cek_op'),
	'antriankendaraan'	=> array('label' => 'antriankendaraan','id' => 'antriankendaraan'),
	'evaluasisupplier'	=> array('label' => 'evaluasisupplier','id' => 'evaluasisupplier'),
);

/* list method yang diperlukan untuk menjalankan modul */
$config['dependency_workbook'] = array(
	'tiketmasuk' => array(
			'probe/kendaraanmasuk/index'
			,'probe/kendaraanmasuk/getdataop'
			,'probe/kendaraanmasuk/kuantitasOPLocal'
			,'probe/kendaraanmasuk/simpansj'
			,'probe/kendaraanmasuk/checkNopolReject'
			,'probe/kendaraanmasuk/getBarcode'
			,'probe/kendaraanmasuk/simpanKendaraanDitolak'
			,'probe/kendaraanmasuk/generateTiketMasuk'
			,'probe/kendaraanmasuk/nopolDalamFM'
	),
	'ambilsample' => array(
			'probe/pengambilansample/index'
			,'probe/pengambilansample/listAntrianSampel'
	),
	'ambilsample_save' => array(
			'probe/pengambilansample/getDataTiketMasuk'
			,'probe/pengambilansample/mulai'
			,'probe/pengambilansample/selesai'
			,'probe/pengambilansample/updateWaktuProbe'
		
	),
	'pemantauanlogistik' => array(
			'probe/pemantauanlogistik/index'
			,'probe/pemantauanlogistik/get_data'
			,'probe/pemantauanlogistik/get_jeniskendaraan'
			,'confirmation_qc/confirmation_qc/detail'
	),
	'pemantauankendaraan' => array(
			'probe/pemantauankendaraan/index'
			,'probe/pemantauankendaraan/get_data'
			,'probe/pemantauankendaraan/get_jeniskendaraan'
			,'probe/pemantauankendaraan/get_tiketmasuk'
	),
	'pk_print' => array(
			'probe/pemantauankendaraan/index'
			,'probe/pemantauankendaraan/get_data'
			,'probe/pemantauankendaraan/get_jeniskendaraan'
			,'probe/pemantauankendaraan/get_tiketmasuk'
			,'probe/pemantauankendaraan/print_tolak'
			,'probe/pemantauankendaraan/print_tolak_bb'
	),
	'pk_checkout' => array(
			'probe/pemantauankendaraan/index'
			,'probe/pemantauankendaraan/get_data'
			,'probe/pemantauankendaraan/get_jeniskendaraan'
			,'probe/pemantauankendaraan/get_tiketmasuk'
			,'probe/pemantauankendaraan/checkout'
	),
	'spabb_entry' => array(
			'spabb/spabb/index'
			,'spabb/spabb/new_register'
			,'spabb/spabb/edit_register'
			,'spabb/spabb/delete_parameter'
			,'spabb/spabb/delete_parameter_standar'
			,'spabb/spabb/new_fill'
			,'spabb/spabb/new_fill_insidentil'
			,'spabb/spabb/edit_fill'
			,'spabb/spabb/open_file'
	),
	'spabb_approve' => array(
			'spabb/spabb/index'
			,'spabb/spabb/approve_register'
			,'spabb/spabb/approve_fill'
			,'spabb/spabb/open_file'
	),
	'analysis_sample' => array(
			'analysis_sample/analysis_sample/index'
			,'analysis_sample/analysis_sample/filter'
			,'analysis_sample/analysis_sample/entry'
			,'analysis_sample/analysis_sample/detail'
			,'analysis_sample/analysis_sample/review'
			,'analysis_sample/analysis_sample/save'
			,'analysis_sample/analysis_sample/approve_sp'
			,'analysis_sample/analysis_sample/re_approve_sp'
			,'analysis_sample/analysis_sample/approve_nonsp'
			,'analysis_sample/analysis_sample/print_sample'
			,'analysis_sample/analysis_sample/printed_sample_children'
			,'analysis_sample/analysis_sample/get_detail_komposit'
	),
	'as_print' => array(
			'analysis_sample/analysis_sample/index'
			,'analysis_sample/analysis_sample/filter'
			,'analysis_sample/analysis_sample/detail'
			,'analysis_sample/analysis_sample/print_sample'
			,'analysis_sample/analysis_sample/printed_sample_children'
			,'analysis_sample/analysis_sample/get_detail_komposit'
	),
	'as_entry' => array(
			'analysis_sample/analysis_sample/index'
			,'analysis_sample/analysis_sample/filter'
			,'analysis_sample/analysis_sample/entry'
			,'analysis_sample/analysis_sample/detail'
			,'analysis_sample/analysis_sample/save'
			,'analysis_sample/analysis_sample/get_detail_komposit'
	),
	'as_review' => array(
			'analysis_sample/analysis_sample/index'
			,'analysis_sample/analysis_sample/filter'
			,'analysis_sample/analysis_sample/detail'
			,'analysis_sample/analysis_sample/review'
			,'analysis_sample/analysis_sample/approve_sp'
			,'analysis_sample/analysis_sample/re_approve_sp'
			,'analysis_sample/analysis_sample/approve_nonsp'
			,'analysis_sample/analysis_sample/get_detail_komposit'
	),
	'as_view' => array(
			'analysis_sample/analysis_sample/index'
			,'analysis_sample/analysis_sample/filter'
			,'analysis_sample/analysis_sample/detail'
			,'analysis_sample/analysis_sample/get_detail_komposit'
	),
	'confirmation_qc' => array(
			'confirmation_qc/confirmation_qc/index'
			,'confirmation_qc/confirmation_qc/filter'
			,'confirmation_qc/confirmation_qc/confirmation'
			,'confirmation_qc/confirmation_qc/detail'
			,'confirmation_qc/confirmation_qc/save'
	),
	'confirmation_driver' => array(
			'confirmation_driver/confirmation_driver/index'
			,'confirmation_driver/confirmation_driver/get_data_for_confirm'
			,'confirmation_driver/confirmation_driver/lanjut'
			,'confirmation_driver/confirmation_driver/get_segment_status'
			,'confirmation_driver/confirmation_driver/show_dialog'
			,'confirmation_driver/confirmation_driver/get_data_for_dialog'
	),
	'kendaraantolak' => array(
			'probe/listkendaraanditolak/index'
			,'probe/listkendaraanditolak/filterKendaraan'
			,'probe/listkendaraanditolak/filterKendaraanExcel'
	),
	'composit_sample' => array(
			'composit_sample/composit_sample/index',
			'composit_sample/composit_sample/get_selected',
			'composit_sample/composit_sample/generate',
			'composit_sample/composit_sample/get_main',
			'composit_sample/composit_sample/get_main_html'
	),
	'evaluasiprobe' => array(
			'probe/evaluasiprobe/index'
			,'probe/evaluasiprobe/getResume'
			,'confirmation_qc/confirmation_qc/detail'
			,'probe/pemantauankendaraan/print_tolak_bb'
			,'probe/kendaraanmasuk/generateTiketMasuk'
			,'probe/kendaraanmasuk/pernahRegistrasiUlang'
			,'probe/evaluasiprobe/getListCallNumber'
			,'spabb/spabb/open_file'
			,'analysis_sample/analysis_sample/get_detail_komposit'
	),	
	'cek_op' => array(
			'probe/cekop/index'
			,'probe/cekop/getdataop'
			,'probe/cekop/kuantitasOP'
			,'probe/cekop/kuantitasOPLocal'
			,'probe/cekop/cekspabb'
	),
	'antriankendaraan' => array(
			'probe/antriankendaraan/index'
			,'probe/antriankendaraan/panggilKendaraan'
			,'probe/antriankendaraan/registrasiUlang'
			,'probe/antriankendaraan/listAntrian'
			,'probe/kendaraanmasuk/getBarcode'
	),
	'evaluasisupplier' => array(
			'probe/evaluasisupplier/index'
			,'probe/evaluasisupplier/getResume'
	),
		
);

$config['list_menu'] = array(
				'home'=> array(
					'id' => 'nav_home',
					'class' => '',
					'title' => 'Home',
					'url' => '#'
				),
				'kendaraanmasuk'=> array(
					'id' => 'nav_kendaraanmasuk',
					'class' => '',
					'title' => 'Tiket Masuk',
					'url' => 'probe/kendaraanmasuk'
				),
				'pengambilansample' => array(
					'id' => 'nav_pengambilansample',
					'class' => '',
					'title' => 'Ambil Sampel',
					'url' => 'probe/pengambilansample'
				),
				'pemantauanlogistik' => array(
					'id' => 'nav_pemantauanlogistik',
					'class' => '',
					'title' => 'Pemantauan Logistik',
					'url' => 'probe/pemantauanlogistik'
				),
				'pemantauankendaraan' => array(
					'id' => 'nav_pemantauankendaraan',
					'class' => '',
					'title' => 'Pemantauan Kendaraan',
					'url' => 'probe/pemantauankendaraan'
				),
				'pkendaraanview' => array(
					'id' => 'nav_pemantauankendaraan',
					'class' => '',
					'title' => 'Pemantauan Kendaraan',
					'url' => 'probe/pemantauankendaraan'
				),
		        'spabb'=> array(
			          'id' => 'nav_spabb',
			          'class' => '',
			          'title' => 'SPABB',
			          'url' => 'spabb/'
		        ),
		        'analysis_sample' => array(
			          'id' => 'nav_analysis_sample',
			          'class' => '',
			          'title' => 'Analisis Sampel',
			          'url' => 'analysis_sample/'
		        ),
		        'confirmation_qc' => array(
			          'id' => 'nav_confirmation_qc',
			          'class' => '',
			          'title' => 'Keputusan Penerimaan',
			          'url' => 'confirmation_qc/'
		        ),/*
		        'confirmation_dirut' => array(
			          'id' => 'nav_confirmation_dirut',
			          'class' => '',
			          'title' => 'Konfirmasi Dirut',
			          'url' => 'confirmation_dirut/'
		        ),*/
				'confirmation_driver'=> array(
			          'id' => 'nav_confirmation_driver',
			          'class' => '',
			          'title' => 'Konfirmasi Sopir',
			          'url' => 'confirmation_driver/'
		        ),
				'kendaraantolak'=> array(
						'id' => 'nav_kendaraantolak',
						'class' => '',
						'title' => 'Kendaraan Tolak',
						'url' => 'probe/listkendaraanditolak/'
				),
				'composit_sample'=> array(
						'id' => 'nav_composit_sample',
						'class' => '',
						'title' => 'Sampel Komposit',
						'url' => 'composit_sample/'
				),
				'evaluasiprobe'=> array(
						'id' => 'nav_evaluasiprobe',
						'class' => '',
						'title' => 'Evaluasi Probe',
						'url' => 'probe/evaluasiprobe'
				),
				'cek_op'=> array(
						'id' => 'nav_cekop',
						'class' => '',
						'title' => 'Periksa Data OP',
						'url' => 'probe/cekop'
				),
				'antriankendaraan'=> array(
						'id' => 'nav_antriankendaraan',
						'class' => '',
						'title' => 'Antrian Kendaraan',
						'url' => 'probe/antriankendaraan'
				),
				'evaluasisupplier'=> array(
						'id' => 'nav_evaluasisupplier',
						'class' => '',
						'title' => 'Evaluasi Supplier',
						'url' => 'probe/evaluasisupplier'
				),
		);

/* End of file permission.php */
/* Location: ./user/config/permission.php */
