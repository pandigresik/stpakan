<?php
	$thisData 	= '';
	
	if($periode != '' && $kodefarm != ''){
		$thisData = ' data-kodefarm="'.$kodefarm.'" data-periode="'.$periode.'"';
	}
?>
<style>
	.table_kode_box thead tr{background:#dfdfdf;}
	.table_kode_box tr th,
		.table_kode_box tr td{
		border : 1px solid #adadad;
	}
</style>

<div class="panel panel-default">
		<div class="panel-heading">
			Laporan Berita Acara Penerimaan DOC In
		</div>
		<div class="panel-body">
		
		<div>
			<div class="row">
				<div class="col-md-2">
					<select class="form-control" id="select_tipe_informasi">
						<option value='bap_doc'>BAP DOC</option>
						<option value='kodebox'>Kode Box</option>
					</select>
				</div>
				<?php if($user_level != 'KF' && $kodefarm == '' && $periode == ''){ ?>
				<div class="col-md-2">
					<select class="form-control" id="select_farm" onChange="get_bapd(this)">
						<option value="">Semua Farm</option>
						<?php 
							foreach($list_farm as $farm){
								echo '<option value="'.$farm['kode_farm'].'">'.$farm['nama_farm'].'</option>';
							}
						?>
					</select>
				</div>
				<?php }else{
					echo '<input type="hidden" id="select_farm" value="">';
				} ?>
				<div class="col-md-6">
					<button type="button" class="btn btn-primary" onClick="BAPD.export_pdf(this)" <?=$thisData?>>Export PDF</button>
					<button type="button" class="btn btn-primary" onClick="BAPD.show_bap_doc(this, 'rhk')" data-userlevel="<?=$user_level?>">Informasi</button>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div id="list_bapdocin"></div>
		</div>
	</div>
</div>
<link rel="stylesheet" media="screen" type="text/css" href="assets/libs/jquery-ui/css/jquery-ui-timepicker-addon.min.css">
<script type="text/javascript" src="assets/libs/jquery-ui/js/jquery-ui-timepicker-addon.min.js"></script>
<script type="text/javascript" src="assets/libs/jquery-ui/js/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="assets/js/report/laporan_bapd.js"></script>
<script type="text/javascript" src="assets/js/jquery.redirect.js"></script>
<script type="text/javascript">
	var periode 	= "<?=$periode?>";
	var kodefarm 	= "<?=$kodefarm?>";
	if(periode != '' && kodefarm != ''){
		BAPD.list_bapd(periode, kodefarm);
	}
</script>