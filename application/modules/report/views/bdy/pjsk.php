<div class="col-md-5 col-md-offset-4 text-center"><h3>Laporan PertanggungJawaban Sak Kosong</h3></div>
<div>
	<form class="form form-inline" role="form" id="pjskForm">
				<?php
					if(isset($status_siklus) && $status_siklus == 'C'){
							echo '<div class="col-md-2 hide">
								<select class="form-control" name="status_siklus">
									<option value="O">Siklus Berjalan</option>
									<option value="C" selected>Tutup Siklus</option>
								</select>
								<input type="text" name="farm" value="'.$farm.'" />
								<input type="text" name="siklus" value="'.$siklus.'" />
							</div>';
					}
				?>
					<div class="row container">
						<div class="col-md-5 new-line">
							<div class="btn btn-default hide" id="tampilkan_btn" onclick="PJSK.showPjsk(this,'lsam_trs')">Tampilkan</div>
							<!-- <div class="btn btn-default" id="tampilkan_btn" onclick="PJSK.showLsgas(this,'lspm')">Glangsing Akhir Siklus</div> -->
							<span class="btn btn-default" onclick="PJSK.exportSpreadsheet('Laporan Pertanggungjawaban Sak Kosong.zip')">Export to Excel</span>
							<div class="btn btn-default" onclick="PJSK.showInformasi(this,'pjsk')">Informasi</div>
						</div>
					</div>

	</form>
	<div class="row col-md-12 new-line">
		<div class="detail_stok_pakan" style="width: 100%;">

		</div>
	</div>
</div>
<script type="text/javascript" src="assets/libs/ExcelExportJs/dist/jquery.techbytarun.excelexportjs.min.js"></script>
<script type="text/javascript" src="assets/libs/jszip/dist/jszip.min.js"></script>
<script type="text/javascript" src="assets/js/FileSaver.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/report/pjsk.js"></script>

<link rel="stylesheet" type="text/css" href="assets/css/report/pjsk.css?v=<?=time()?>" >
<!-- <link rel="stylesheet" type="text/css" href="assets/css/forecast/konfirmasi.css" > -->
