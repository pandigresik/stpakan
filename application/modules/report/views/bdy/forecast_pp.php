<div class="col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">Laporan Forecast vs PP</div>
		<div class="panel-body">
			<form class="form form-inline" role="form">
				<div class='col-md-6'>
						<label for="nama_farm" class="col-md-3">Nama Farm</label>
						<div class="form-group">
							<?php echo $list_farm ?>
						</div>
						<div class="btn btn-default" onclick="Forecast_pp.tampilkan(this)">Tampilkan</div>
					</div>
			</form>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">Kebutuhan Pakan</div>
		<div class="panel-body" id="div_kebutuhan_pakan">

		</div>
	</div>
</div>
<script type="text/javascript" src="assets/js/report/forecast_pp.js"></script>
