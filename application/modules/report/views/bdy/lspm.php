<div class="row">
<div class="col-md-4 col-md-offset-4 text-center"><h3>LAPORAN STOK PAKAN MINGGUAN</h3></div>
</div>
<div>
	<form class="form form-inline" role="form">
<!--
				<label class="control-label col-md-2">Periode Doc In</label>
				<div class="col-md-10">
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
							<div class="col-md-2">
								<select class="form-control" name="range_periode">
									<option value='M'>Mingguan</option>
									<option value='H'>Harian</option>
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
					<div class="col-md-5 new-line">
						<div class="btn btn-default hide" id="tampilkan_btn" onclick="Rhk.showListFarm(this,'lspm')">Tampilkan</div>
						<select class="form-control" name="range_periode" onchange="Rhk.showListFarm(this,'lspm')">
							<option value='M'>Mingguan</option>
							<option value='H'>Harian</option>
						</select>
						<span class="btn btn-default" onclick="Rhk.exportSpreadsheet('Laporan Stok Pakan Mingguan.zip')">Export to xls</span>
						<a href="#" download="rhk_lsam.xls" id="sheetUri" class="hide">Tes</a>
						<div class="btn btn-default" onclick="Rhk.showListFarm(this,'rhk')">RHK</div>
						<div class="btn btn-default" onclick="Rhk.showInformasi(this,'lspm')">Informasi</div>
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

<link rel="stylesheet" type="text/css" href="assets/css/report/stok_pakan.css?v=5" >
<<<<<<< HEAD
<script type="text/javascript" src="assets/libs/ExcelJs/dist/jquery.techbytarun.excelexportjs.min.js"></script>
=======
<script type="text/javascript" src="assets/libs/ExcelExportJs/dist/jquery.techbytarun.excelexportjs.min.js"></script>
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
<script type="text/javascript" src="assets/libs/jszip/dist/jszip.min.js"></script>
<script type="text/javascript" src="assets/js/FileSaver.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/report/rhk_bdy.js"></script>
<script type="text/javascript" src="assets/js/report/rhk_lsam.js"></script>
