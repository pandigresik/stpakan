<div class="col-md-4 col-md-offset-4 text-center"><h3><?php echo !empty($nama_farm) ? strtoupper($nama_farm) : '' ?></h3></div>
<div>
	<form class="form form-inline" role="form">
		<div class='col-md-6'>
	    	<label for="tglAkses" class="col-md-2">Tanggal</label>
	    	<div class="form-group">
				<div class="input-group">
					<input type="text" class="form-control" name="tglTransaksi" readonly />
					<span class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</span>
				</div>
			</div>

			<div class="form-group">
			<?php if($pilih_farm){?>
				<div class="btn btn-default" onclick="StokPakan.show_list_farm(this)">Pilih Farm</div>
			<?php } ?>
				<div class="list_checkbox" style="display:none ">
					<div class="row col-md-12">
						<?php echo $list_farm ?>
					</div>
				</div>

				<div class="btn btn-default" id="tampilkan_btn" onclick="StokPakan.list_stok_pakan(this)">Tampilkan</div>
			</div>

		</div>
	</form>
	<div class="row col-md-12 new-line">
		<div class="detail_stok_pakan">

		</div>
	</div>
</div>

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/report/stok_pakan.js"></script>

<link rel="stylesheet" type="text/css" href="assets/css/report/stok_pakan.css?v=4" >
<link rel="stylesheet" type="text/css" href="assets/css/forecast/konfirmasi.css" >
