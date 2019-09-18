<div class="row">
<div class="col-md-6 col-md-offset-3 text-center"><h3>LAPORAN STOK GLANGSING AKHIR SIKLUS</h3></div>
</div>
<div class="row" style="margin-bottom:10px; margin-top:50px; display:<?php echo $list_farm_prop?>">
	<div id="div_list_farm">
		<div class="row col-md-12">
			<div class="col-md-5 col-md-offset-4 text-center">
				<label class="control-label col-md-2">Farm</label>
		      <div class="col-md-6">
		        <select class="form-control" name="list_farm" onchange="lsgas.refresh_table()">
					  <!-- <option value="">Pilih Farm</option> -->
					  <?php foreach ($list_farm as $key => $value):?>
					  		<option value="<?php echo $value->KODE_FARM?>" <?php echo (($kodefarm == $value->KODE_FARM) ? 'selected' : '')?>><?php echo $value->NAMA_FARM?></option>
				  	  <?php endforeach; ?>
				  </select>
		      </div>
				<!-- <button class="btn btn-primary" style="float:left" onclick="permintaanSak.loadListPermintaan(this)">Cari</button> -->
			</div>
		</div>
	</div>
</div>
<div>
	<div class="row col-md-12 new-line" id="div_rhklsamfarm">
		<div class="section">
			<div class="panel">
				<div class="panel-body">
					<table class="table table-bordered table-striped" id="tb_lsgas">
						<thead>
							<tr>
								<th class="text-center table-header" style="width: 170px">Siklus</th>
								<th class="text-center table-header" style="width: 115px">Stok Awal</th>
								<th class="text-center table-header">Glangsing Diterima</th>
								<th class="text-center table-header">Glangsing Pakai</th>
								<th class="text-center table-header">Glangsing Dijual</th>
								<th class="text-center table-header">Sisa Akhir</th>
								<th class="text-center table-header">Status</th>
								<th class="text-center table-header" style="width:170px;"></th>
								<th class="text-center table-header" style="width:170px;display:none"></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php //echo $button?>
	<input type="hidden" name="kode_siklus" id="kode_siklus">
	<input type="hidden" name="no_urut" id="no_urut">
</div>

<link rel="stylesheet" type="text/css" href="assets/css/report/stok_pakan.css?v=5" >
<style>
	#td_nama_budget{
		width:500px;
	}
	#td_jumlah_glangsing{
		width:70px;
	}
	#td_total_internal{
		width:70px;
	}
	#td_total_eksternal{
		width:70px;
	}
	#internal_budget td{
		padding:3px;
	}
	#eksternal_budget td{
		padding:3px;
	}
	table.dataTable tbody tr.selected {
	    background-color: #b0bed9;
	}
</style>
<iframe id="print_frame" style="width:100%;height:500px;margin:0;border:0">
</iframe>
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/tooltipster.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-light.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-noir.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-punk.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-shadow.css" >
<script type="text/javascript" src="assets/libs/jquery/plugin/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/libs/jquery/plugin/datatables/fnReloadAjax.js"></script>
<script type="text/javascript" src="assets/js/report/lsgas.js"></script>
<script type="text/javascript" src="assets/libs/jquery/tooltipster/jquery.tooltipster.min.js"></script>
