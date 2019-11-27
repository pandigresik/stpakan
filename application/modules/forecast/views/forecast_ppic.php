<div class="panel panel-default">
	<div class="panel-heading">
		Forecast PPIC
	</div>
	<div class="panel-body">

		<div class="bs-example bs-example-tabs" role="tabpanel" data-example-id="togglable-tabs">
			<ul id="myTab" class="nav nav-tabs" role="tablist">
				<li role="presentation" class="estimasi <?php echo empty($hide) ? 'active' : ''; ?> <?php echo $hide; ?>">
					<a onclick="clear_content(this)" href="#estimasi" id="estimasi-tab" role="tab" data-toggle="tab" aria-controls="estimasi" aria-expanded="true">Estimasi Kebutuhan Pakan Breeding</a>
				</li>
				<?php foreach ($tab_ack as $grup_farm => $label_ack) { ?>
				<li class="konfirmasi <?php #echo ($ack) ? '' : 'hide' ;?> <?php echo (!empty($hide) && $grup_farm == $active_tab) ? 'active' : ''; ?>" role="presentation">
					<a onclick="clear_content(this)" aria-expanded="false" href="#konfirmasi" data-grup-farm="<?php echo $grup_farm; ?>" role="tab" id="konfirmasi-tab" data-toggle="tab" aria-controls="konfirmasi">
						<?php echo $label_ack; ?>
					</a>
				</li>
				<?php } ?>
			</ul>
			<div id="myTabContent" class="tab-content">
				<div role="tabpanel" class="tab-pane fade <?php echo empty($hide) ? 'active' : ''; ?> in" id="estimasi" aria-labelledby="estimasi-tab">

					<div class="row <?php echo $hide; ?>">
						<div class="form">
							<div class="form-group">
								<label class="control-label col-md-1">Kebutuhan Pakan</label>
								<div class='col-md-8'>
									<div class='col-md-3'>
										<div class="form-group">
											<div class="input-group date">
												<input type="text" class="form-control parameter kebutuhan" name="startDate" readonly />
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span>
											</div>
										</div>
									</div>
									<div class='col-md-1 vcenter'>
										s.d.
									</div>
									<div class='col-md-3'>
										<div class="form-group">
											<div class="input-group date" >
												<input type="text" class="form-control parameter kebutuhan" name="endDate" readonly />
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="row <?php echo $hide; ?>">
						<div class="col-md-1">
							<span class="btn btn-default" id="filter_farm">Filter Farm</span>
						</div>
					</div>
					<div id="div_kebutuhan_pakan_ppic" style="margin-top:5px"></div>

				</div>
				<div role="tabpanel" class="tab-pane fade <?php echo !empty($hide) ? 'active' : ''; ?> in" id="konfirmasi" aria-labelledby="konfirmasi-tab">
					<div class="row <?php echo $hide; ?>">
						<div class="col-md-2">
							<label>
								<input type="checkbox" id="checkbox_belum_konfirmasi" value="0" checked>
								Belum Konfirmasi
							</label>
						</div>
						<div class="col-md-4">
							<label>
								<input type="checkbox" id="checkbox_sudah_konfirmasi" value="0">
								Sudah Konfirmasi dan Belum Tutup Siklus
							</label>
						</div>
					</div>
					<div class="row tanggal-chick-in <?php echo $hide; ?>">
						<div class="form">
							<div class="form-group">
								<label class="control-label col-md-1">Tanggal Chick-in</label>
								<div class='col-md-8'>
									<div class='col-md-3'>
										<div class="form-group">
											<div class="input-group date">
												<input type="text" class="form-control parameter chickin" name="startDate" disabled readonly />
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span>
											</div>
										</div>
									</div>
									<div class='col-md-1 vcenter'>
										s.d.
									</div>
									<div class='col-md-3'>
										<div class="form-group">
											<div class="input-group date" >
												<input type="text" class="form-control parameter chickin" name="endDate" disabled readonly />
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="row <?php echo $hide; ?>">
						<div class="col-md-1">
							<span class="btn btn-default" id="filter_konfirmasi_ppic">Filter Farm</span>
						</div>
					</div>
					<div id="div_konfirmasi_ppic" style="margin-top:5px"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
echo '<div id="data-notif" class="hide">';
	if(isset($notif)){
		echo json_encode($notif);
	}
echo '</div>';
?>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/forecast/forecastHandler.js"></script>
<script type="text/javascript" src="assets/js/forecast/aktivasiKandang.js"></script>
<script type="text/javascript" src="assets/js/forecast/forecast_ppic.js"></script>

<script type="text/javascript" src="assets/libs/js-xlsx/dist/xlsx.full.min.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/Blob.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/FileSaver.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/Export2Excel.js"></script>

<style>
.konfirmasi_table th{
	background-color: #f5f5f5;
}
.very-large>.modal-dialog {
	width: 95% !important;
}
</style>
