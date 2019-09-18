<div id="div_content">
	<div class="row col-md-10">
		<div class="btn-group div_btn" style="margin-bottom:30px">
			<?php echo $tombol ?>
	        
	    </div>
		<div class="form form-horizontal" id="form_input">		
			<div class="form-group">
				<label class="control-label col-md-2" style="text-align: right;">Farm</label>
				<div class="col-md-3">
					<select class="form-control" name="farm" <?php //echo $_disable ?> >
						<option value="">Pilih</option>
						<?php
						if(!empty($list_farm)){
							foreach($list_farm as $ls){
								$selected = '';
								echo '<option '.$selected.' value="'.$ls['KODE_FARM'].'">'.$ls['NAMA_FARM'].'</option>';
							}
						}
						?>
						</select>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label col-md-2" style="text-align: right;">Tanggal</label>
				<div class="col-md-10">
					<div class="form-inline">
						<div class="form-group col-md-3">
							<div class="input-group">
								<input type="text" class="form-control" id="startDate" name="startDate" placeholder="" value="<?php echo $tgl_sekarang ?>" />
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</div>
							</div>
						</div>
				
						<div class="checkbox col-md-3" style="margin-left:3px">
							<label><input type="checkbox" value="" name="show_all" /> Tampilkan seluruh stok hari ini</label>
						</div>
						<div class="checkbox col-md-3">
							<label><input type="checkbox" name="show_outstanding" checked value="" /> Tampilkan stok outstanding</label>
						</div>							
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row col-md-12" >
		<div id="div_list_laporan" style="padding-top:20px">
			<?php echo $list_laporan ?>
		</div>
	</div>
	<div class="row col-md-12" >
		<div id="div_detail_so" style="padding-top:20px">
			
		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/permintaan_sak_kosong/permintaan.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/tooltipster.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-light.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-noir.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-punk.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-shadow.css" >

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/sales_order/laporan_stok_glangsing.js"></script>
<script type="text/javascript" src="assets/libs/jquery/tooltipster/jquery.tooltipster.min.js"></script>
