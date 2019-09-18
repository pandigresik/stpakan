<div class="row">
<div class="col-md-4 col-md-offset-4 text-center"><h3>RIWAYAT HARIAN KANDANG</h3></div>
</div>
<div>
	<form class="form form-inline" role="form">

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
					<div class="col-md-4 new-line">
						<div class="btn btn-default hide" id="tampilkan_btn" onclick="Rhk.showRhkTrs(this,'rhk')">Tampilkan</div>
						<span class="btn btn-default" onclick="Rhk.exportSpreadsheetTrs('Riwayat Harian Kandang')">Export to xls</span>
						<a href="#" download="rhk_lsam.xls" id="sheetUri" class="hide">Tes</a>
						<div class="btn btn-default" onclick="Rhk.showInformasi(this,'rhk')">Informasi</div>
					</div>
				</div>
</form>
	<div class="row col-md-12 new-line" id="div_rhklsamfarm">
		<div class="section">
			<div class="panel">
				<div class="panel-body">
					<div id="div_detail_rhk"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/report/stok_pakan.css?v=3" >
<link rel="stylesheet" type="text/css" href="assets/css/mutasi_pakan/tooltipster.css">

<script type="text/javascript" src="assets/libs/ExcelExportJs/dist/jquery.techbytarun.excelexportjs.min.js"></script>
<script type="text/javascript" src="assets/libs/jszip/dist/jszip.min.js"></script>
<script type="text/javascript" src="assets/js/FileSaver.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/mutasi_pakan/jquery.tooltipster.min.js"></script>

<script type="text/javascript" src="assets/js/report/rhk_bdy.js"></script>
<script type="text/javascript" src="assets/js/report/rhk_lsam.js"></script>
