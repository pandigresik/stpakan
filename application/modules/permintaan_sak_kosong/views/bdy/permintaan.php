<div class="row" style="margin-bottom:10px; display:<?php echo $list_farm_prop?>">
	<div id="div_list_farm">
		<div class="row col-md-12">
			<div class="col-md-5 col-md-offset-4 text-center">
				<label class="control-label col-md-2">Farm</label>
		      <div class="col-md-6">
		        <select class="form-control" name="list_farm" onchange="permintaanSak.loadListPermintaan(this)">
					  <!-- <option value="">Pilih Farm</option> -->
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
<div class="row">
	<div id="div_permintaan">
		<?php echo $form_permintaan ?>
	</div>
	<div id="histori">
		<?php echo $historiPermintaanSak ?>
	</div>
</div>
<div class="row col-md-12" >
	<div id="div_list_permintaan" style="display:<?php echo $list_permintaan_prop?>">
		<?php echo $list_permintaan ?>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/permintaan_sak_kosong/permintaan.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/tooltipster.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-light.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-noir.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-punk.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-shadow.css" >
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/permintaan_sak_kosong/permintaanSak.js"></script>
<script type="text/javascript" src="assets/libs/jquery/tooltipster/jquery.tooltipster.min.js"></script>
