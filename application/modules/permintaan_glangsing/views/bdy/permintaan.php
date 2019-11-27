<div class="row" style="margin-bottom:10px; display:<?php echo $list_farm_prop?>">
	<div id="div_list_farm">
		<div class="row col-md-12">
			<div class="col-md-5 col-md-offset-4 text-center">
				<label class="control-label col-md-2">Farm</label>
		      <div class="col-md-6">
		        <select class="form-control" name="list_farm" onchange="permintaanSak.loadListPermintaan(this)">
					  <?php foreach ($list_farm as $key => $value):?>
					  		<option value="<?php echo $value->KODE_FARM?>" <?php echo (($kodefarm == $value->KODE_FARM) ? 'selected' : '')?>><?php echo $value->NAMA_FARM?></option>
				  	  <?php endforeach; ?>
				  </select>
		      </div>
				<button class="btn btn-primary" style="float:left" onclick="permintaanSak.loadListPermintaan(this)">Cari</button>
			</div>
		</div>
	</div>
</div>

<div id="div_content">
	<div class="row col-md-10">
		<div class="btn-group div_btn" style="margin-bottom:10px">
			<?php
			if($level_user == 'KF'){
				echo '<button class="btn btn-primary" type="button" onclick="permintaanSak.NewForm()">Tambah</button>';
			} ?>
	        
	    </div>
		<div class="form form-horizontal" id="form_input">
			<div class="form-group">
				<label class="control-label col-md-2" style="text-align: right;">No. Permintaan Sak</label>
				<div class="col-md-3">
					<input type="text" name="no_ppsk" id="no_ppsk" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-2" style="text-align: right;">Kategori</label>
				<div class="col-md-3">
					<select class="form-control" name="kode_budget" id="kode_budget">
						<option value="">Pilih</option>
						<?php foreach ($kategori as $key => $value) { ?>
							<option value="<?php echo $value->KODE_BUDGET ?>"><?php echo $value->NAMA_BUDGET ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label style="text-align: right;" class="control-label col-md-2">Tgl Kebutuhan</label>
				<div class="col-md-10">
					<div class="form-inline">
						<div class="form-group col-md-3">
							<div class="input-group">
								<input type="text" class="form-control" id="tanggal_awal" name="tanggal_awal" placeholder="">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</div>
							</div>
						</div>
						<div class="form-group  col-md-1">
							<label style="text-align: left;" class="control-label col-md-3">s/d</label>
						</div>
						<div class="form-group  col-md-3">
							<div class="input-group">
								<input type="text" class="form-control" id="tanggal_akhir" name="tanggal_akhir" placeholder="">
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 col-md-offset-2">
					<button class="btn btn-primary" style="float:left" onclick="permintaanSak.cariPermintaan(this)">Cari</button>
				</div>
			</div>
		</div>
	</div>
	<div class="row col-md-12" >
		<div id="div_list_permintaan">
			<?php echo $list_permintaan ?>
		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/permintaan_sak_kosong/permintaan.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/tooltipster.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-light.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-noir.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-punk.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-shadow.css" >
<script type="text/javascript" src="assets/js/permintaan_glangsing/permintaan_glangsing.js"></script>
<script type="text/javascript" src="assets/js/permintaan_glangsing/config1.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/libs/jquery/tooltipster/jquery.tooltipster.min.js"></script>
