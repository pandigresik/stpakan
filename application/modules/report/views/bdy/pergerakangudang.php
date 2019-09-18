<h3 class="text-center">LAPORAN PERGERAKAN STOK GUDANG</h3>
<br >
<form class="form form-horizontal" role="form">
	<div class="row">
		<div class="col-md-5">
			<div class="form-group">
				<label class="control-label col-md-4">Farm</label>
				<div class="col-md-6">
					<?php echo $list_farm ?>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-4">Periode Siklus</label>
				<div class="col-md-6">
					<!-- diisi ketika user setelah memilih farm -->
					<select name="kode_siklus" class="form-control">

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
				<div class="col-md-6 col-md-offset-4">
					<div class="btn btn-default" id="divtampilkan">Tampilkan</div>
					<div class="btn btn-default" id="divexcel">Export xls</div>
				</div>
			</div>
		</div>
	</div>
</form>
<div id="divpergerakangudang"></div>
<script type="text/javascript" src="assets/js/report/pergerakangudang.js"></script>
