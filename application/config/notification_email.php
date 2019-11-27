<?php defined('BASEPATH') OR exit('No direct script access allowed');
/* daftarkan route yang ada kemungkinan dilakukan sinkronisasi datanya */
$config['route_notification'] = array(
	/* forecast */
			'forecast/forecast/approveRejectKonfirmasiDOCIn'

	/* permintaan pakan */
			,'permintaan_pakan/permintaan_pakan/simpan_pp'
			,'permintaan_pakan/permintaan_pakan/reset_review_pp'
			,'permintaan_pakan/permintaan_pakan/reset_approve_pp'
			,'permintaan_pakan/permintaan_pakan/approve_pp_budidaya'
			,'permintaan_pakan/permintaan_pakan/reject_pp_kadiv'

);
