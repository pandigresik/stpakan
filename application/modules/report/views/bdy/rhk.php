<div class="row">
<div class="col-md-4 col-md-offset-4 text-center"><h3>RIWAYAT HARIAN KANDANG</h3></div>
</div>
<div>
	<form class="form form-inline" role="form">
<!--
				<label class="control-label col-md-2">Periode DOC In</label>
				<div class="col-md-9">
					<div class="col-md-3">
									<div class="form-group">
											<div class="input-group date">
													<input type="text" readonly="" name="startDate" class="form-control parameter" >
													<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
													</span>
											</div>
									</div>
							</div>
						<div class="col-md-1 vcenter">s.d.</div>
							<div class="col-md-3">
									<div class="form-group">
											<div class="input-group date">
													<input type="text" readonly="" name="endDate" class="form-control parameter" >
													<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
													</span>
											</div>
									</div>
							</div>
							<div class="col-md-2">
								<select class="form-control" name="status_siklus">
									<option value='O'>Siklus Berjalan</option>
									<option value='C'>Tutup Siklus</option>
								</select>
							</div>
				</div>
		-->
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
						<div class="btn btn-default hide" id="tampilkan_btn" onclick="Rhk.showListFarm(this,'rhk')">Tampilkan</div>
						<span class="btn btn-default" onclick="Rhk.exportSpreadsheet('Riwayat Harian Kandang.zip')">Export to xls</span>
						<a href="#" download="rhk_lsam.xls" id="sheetUri" class="hide">Tes</a>
						<div class="btn btn-default" onclick="Rhk.showInformasi(this,'rhk')">Informasi</div>
					</div>
				</div>
</form>
	<div class="row col-md-12 new-line" id="div_rhklsamfarm">
		<div class="section">
			<div class="panel">
				<div class="panel-body">

				</div>
			</div>
		</div>
	</div>
</div>
<div id="divleveluserinfo" class="hide">
	<?php echo $level_user ?>
</div>
<link rel="stylesheet" type="text/css" href="assets/css/report/stok_pakan.css?v=2" >
<link rel="stylesheet" type="text/css" href="assets/css/mutasi_pakan/tooltipster.css">

<script type="text/javascript" src="assets/libs/ExcelExportJs/dist/jquery.techbytarun.excelexportjs.min.js"></script>
<script type="text/javascript" src="assets/libs/jszip/dist/jszip.min.js"></script>
<script type="text/javascript" src="assets/js/FileSaver.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/mutasi_pakan/jquery.tooltipster.min.js"></script>

<script type="text/javascript" src="assets/js/report/rhk_bdy.js"></script>
<script type="text/javascript" src="assets/js/report/rhk_lsam.js"></script>
