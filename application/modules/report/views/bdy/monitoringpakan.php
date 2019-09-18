<h3>Monitoring Pakan</h3>
<form class="form form-horizontal" role="form">
	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<label class="control-label col-md-2">Farm</label>
				<div class="col-md-6">
					<?php echo $list_farm ?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-2">Doc In</label>
				<div class="col-md-6">
					<!-- diisi ketika user setelah memilih farm -->
					<select name="docin" class="form-control">

					</select>
				</div>
			</div>
		<!--	
			<div class="form-group">
				<label class="control-label col-md-2">Tampilan</label>
				<div class="col-md-6">
					<div class="checkbox">
						<label><input type="checkbox" value="detail" />Detail</label>
					</div>
				</div>
			</div>
		-->
			<div class="form-group">
				<div class="col-md-6 col-md-offset-2">
					<div class="btn btn-default" id="divtampilkan">Tampilkan</div>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<label class="control-label col-md-2">Populasi </label>
				<div class="col-md-6">
					<input name="populasi" class="no_border form-control" readonly>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-2">Flock</label>
				<div class="col-md-6">
					<input name="flock" class="form-control no_border" readonly>
				</div>
			</div>
		</div>
	</div>
</form>
<div id="divmonitoringpakan"></div>
<script type="text/javascript" src="assets/js/report/monitoringpakan.js"></script>
<link rel="stylesheet" type="text/css" href="assets/css/report/monitoringpakan.css" >
