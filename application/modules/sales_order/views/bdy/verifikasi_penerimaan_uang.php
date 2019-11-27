<div id="div_content">
	<div class="row col-md-10">
		<div class="btn-group div_btn" style="margin-bottom:10px">
			<?php echo $tombol ?>
		</div>
	</div>
	<input type="hidden" id="kode_farm" value="<?php echo $kode_farm?>">
	<div class="row">		
		<div class="col-md-6">
			<div class="form-group">
		        <label class="control-label col-md-2 col-sm-2 col-xs-2" for="fm_user_role" style="min-width: 17%;">
		            Tanggal SO
		        </label>
		        <div class="col-md-4 col-sm-4 col-xs-4">
					<div class="input-group date">
						<input disabled type="text" class="form-control" name="startDate">
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
					</div>
		        </div>
		        <label class="control-label col-md-1 col-sm-1 col-xs-1" for="fm_user_role">
		            sd
		        </label>
		        <div class="col-md-4 col-sm-4 col-xs-4">
					<div class="input-group date">
						<input disabled type="text" class="form-control" name="endDate" onchange="VerifikasiPU.loadData(this)">
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
					</div>
		        </div>
		    </div>

		</div>
		<div class="clearfix"></div>
		<div class="col-md-12">
			<div class="form-group">			
				<div class="col-md-1 col-sm-1 col-xs-1 text-right" style="min-width:11.5%">
					<input type="checkbox" style="margin-top:13px" name="tampilkan1" checked onclick="VerifikasiPU.checkedList(this)">
				</div>
				<div class="col-md-3 col-sm-3 col-xs-3" style="margin-top:10px;min-width:26%;padding-left:0px;">
					<span style="padding: 6px 12px;">Tampilkan seluruh SO yang belum diverifikasi</span>
				</div>
			</div>
		</div>

	</div>
	<div class="row col-md-12" >
		<div id="div_list_sales_order" style="padding-top:20px">
			<?php echo $sales_order_header ?>
		</div>
		<div id="div_list_detail" style="padding-top:20px">
		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/permintaan_sak_kosong/permintaan.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/tooltipster.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-light.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-noir.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-punk.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-shadow.css" >
<script type="text/javascript" src="assets/js/common.js"></script>
<script type="text/javascript" src="assets/js/sales_order/verifikasi_penerimaan_uang.js"></script>
<!--script type="text/javascript" src="assets/js/sales_order/config1.js"></script-->
<script type="text/javascript" src="assets/libs/jquery/tooltipster/jquery.tooltipster.min.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
