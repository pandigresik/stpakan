<div class="row">
<div class="col-md-6 col-md-offset-3 text-center"><h3>Kontrol Pemakaian dan Penjualan Glangsing</h3></div>
</div>
<div class="col-md-4 col-md-offset-4">
	<form class="form form-horizontal" role="form">
		<div class="form-group">
			<label class="control-label col-md-2">Farm</label>
			<div class="col-md-8">
				<select class="form-control" id="search_farm" data-required="1" placeholder="Status" onchange="KPPG.showListSiklusFarm(this)">
					<option value="">- Pilih Farm -</option>	
				<?php 					
				foreach ($list_farm as $key => $farm_data): ?>
				  <option value="<?php echo $farm_data->kode_farm ?>"><?php echo $farm_data->nama_farm ?></option>
				<?php endforeach; ?>
			  </select>
			</div>
		</div>										
	</form>	
</div>
<div class="row col-md-12 new-line" id="divListSiklus">
	<div class="section">
		<div class="panel">
			<div class="panel-body">

			</div>
		</div>
	</div>
</div>
<div id="divleveluserinfo" class="hide">
	<?php echo $level_user ?>
</div>

<script type="text/javascript" src="assets/js/report/kontrol_pemakaian_penjualan_glangsing.js"></script>
<link rel="stylesheet" type="text/css" href="assets/css/report/kontrol_pemakaian_penjualan_glangsing.css?v=1.09" >