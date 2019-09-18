<div class="row">
<div class="col-md-4 col-md-offset-4 text-center"><h3>LAPORAN STOK PAKAN MINGGUAN</h3></div>
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
					<div class="col-md-5 new-line">
						<div class="btn btn-default hide" id="tampilkan_btn" onclick="Rhk.showLspmTrs(this,'lspm_trs')">Tampilkan</div>
						<select class="form-control" name="range_periode" onchange="Rhk.showLspmTrs(this,'lspm_trs')">
							<option value='M'>Mingguan</option>
							<option value='H'>Harian</option>
						</select>
						<span class="btn btn-default" onclick="Rhk.exportSpreadsheetTrs('Laporan Stok Pakan Mingguan.zip')">Export to xls</span>
						<a href="#" download="rhk_lsam.xls" id="sheetUri" class="hide">Tes</a>
						<div class="btn btn-default" onclick="Rhk.showRhkTrs(this,'rhk_trs')">RHK</div>
						<div class="btn btn-default" onclick="Rhk.showInformasi(this,'lspm_trs')">Informasi</div>
					</div>
				</div>

</form>
	<div class="row col-md-12 new-line" id="div_rhklspmfarm">
		<div class="section">
			<div class="panel">
				<div class="panel-body">
					<div id="detail_lspm"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/report/stok_pakan.css?v=5" >
<script type="text/javascript" src="assets/libs/ExcelExportJs/dist/jquery.techbytarun.excelexportjs.min.js"></script>
<script type="text/javascript" src="assets/libs/jszip/dist/jszip.min.js"></script>
<script type="text/javascript" src="assets/js/FileSaver.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/report/rhk_bdy.js"></script>
<script type="text/javascript" src="assets/js/report/rhk_lsam.js"></script>
