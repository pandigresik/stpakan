<div class="panel panel-default">
  <div class="panel-heading">Pemantauan LHK</div>
  <div class="panel-body">
	<div class="col-md-12">
		<center><h1><span id="lbl_pemantauan_lhk">&nbsp;</span></h1></center>
		<br/><br/>
		<div class="row">
			<div style="width:200px;float:left">
				<form class="form-inline">
					<div class="form-group" style="color:#FF0000;">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_lhk_tidak_sesuai_timeline">LHK tidak sesuai timeline
							</label>
						</div>							
					</div>
				</form>
			</div>
			<div style="width:150px;float:left">
				<form class="form-inline">
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_lhk_sesuai_timeline"> LHK sesuai timeline
							</label>
						</div>
					</div>
				</form>
			</div>
			<div style="width:150px;float:left">
				<form class="form-inline">
					<div class="form-group" style="color:#E6A205">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_lhk_belum_dientry"> LHK belum dientry
							</label>
						</div>
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group" style="color:#0C4EE8;">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_lhk_pakan_berlebih"> Konsumsi Pakan Berlebih
							</label>
						</div>
					</div>
				</form>
			</div>
		</div>
		
		<div class="row">
			<div style="width:200px;float:left">
				<form class="form-inline">
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_belum_konfirmasi"> Belum konfirmasi
							</label>
						</div>							
					</div>
				</form>
			</div>
			<div style="width:150px;float:left">
				<form class="form-inline">
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_sudah_konfirmasi"> Sudah konfirmasi
							</label>
						</div>
					</div>
				</form>
			</div>
		</div>
		
		<div class="row">
			<div style="width:300px;float:left">
				<form class="form-inline">
					<span>Tanggal LHK : </span>
					<div class="form-group">	
						<input type="hidden" name="leveluser" style="width:150px;" class="form-control" id="q_leveluser" value="<?php echo $level;?>"/>
						<input type="hidden" name="namafarmtrue" style="width:150px;" class="form-control" id="q_farm_true" value="<?php echo $farms[0]["nama_farm"];?>"/>
						<input type="hidden" name="namafarm" style="width:150px;" class="form-control" id="q_farm" value="<?php echo $farms[0]["kode_farm"];?>"/>
							
						<div class="input-group date" id="div_q_start_tgl_lhk">
							<input type="text" name="startDate" style="width:150px;" class="form-control disabled" id="q_start_tgl_lhk" readonly />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</form>
			</div>
			<div style="width:250px;float:left">
				<form class="form-inline">
					<span>s/d&nbsp;&nbsp;&nbsp;&nbsp;</span>
					<div class="form-group">						
						<div class="input-group date" id="div_q_end_tgl_lhk">
							<input type="text" name="endDate" style="width:150px;" class="form-control disabled" id="q_end_tgl_lhk" readonly />
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</form>
			</div>
			<div>
				<button type="button" name="tombolCari" id="btnCari" class="btn btn-primary">Cari</button>
			</div>
		</div>
		
		<br/>
		
		<div class="row">
			<div id="column-left" class="col-md-2" style="margin:0px;padding:1px;display:none">
				<div class="panel panel-default">
					<div class="panel-heading">Daftar Farm</div>
					<div class="panel-body" id="daftar_farm">
						<?php
						foreach($farms as $farm){
							$badge = ($farm["jml"] > 0) ? "<span class='badge'>".$farm["jml"]."</span>" : "";
							echo "<div data-farm='".$farm["kode_farm"]."' class='menu_farm' onclick='change_farm(this)'>".strtoupper($farm["nama_farm"]) . $badge . "</div>";
						}
						
						?>
					</div>
				</div>
			</div>
			<div id="content" class="col-md-10"  style="margin:0px;padding:1px;">
				<div class="panel panel-default">
					<div class="panel-heading" id="lbl_nama_farm" style="display:none"> 
						<span class="glyphicon glyphicon-align-justify" id="btn" aria-hidden="true" style="display:none"></span>
						<?php echo "FARM <span id='span_lbl_farm'>".strtoupper($farms[0]["nama_farm"])."</span>";?>
					</div>
					<div class="panel-body">
						<table id="tb_lhk" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<td><input type="text" class="form-control search" name="q_kandang" id="q_kandang" placeholder="Kandang"></td>
									<td><input type="text" class="form-control search" name="q_noreg" id="q_noreg" placeholder="No. Reg"></td>
									<td colspan="5"></td>
								</tr>
								<tr>
									<th class="vert-align" rowspan="2" style="width:150px;">Kandang</th>
									<th class="vert-align" rowspan="2" style="width:150px;">No. Reg</th>
									<th class="vert-align" rowspan="2" style="width:150px;">Tanggal LHK</th>
									<th class="vert-align" rowspan="2" style="width:200px;">Tanggal entry LHK</th>
									<th class="vert-align" rowspan="2" style="width:200px;">Status LHK</th>
									<th class="vert-align" colspan="2">Acknowledge keterlambatan</th>
									<th class="vert-align" rowspan="2">Keterangan</th>
								</tr>
								<tr>
									<th class="vert-align" style="width:200px;">Kepala Farm</th>
									<th class="vert-align" style="width:200px;">Direktur Breeding</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>	
  </div>
</div>

<style type="text/css">
	hr {
		-moz-border-bottom-colors: none;
		-moz-border-image: none;
		-moz-border-left-colors: none;
		-moz-border-right-colors: none;
		-moz-border-top-colors: none;
		border-color: #EEEEEE -moz-use-text-color #FFFFFF;
		border-style: solid none;
		border-width: 1px 0;
		margin: 18px 0;
	}
	
	.table thead>tr>th.vert-align{
		vertical-align: middle;
		text-align : center;
	}
	.table tbody>tr>td.vert-align{
		vertical-align: middle;
		text-align : center;
	}
	.table tbody>tr>td.vert-align-sm{
		vertical-align: middle;
		text-align : center;
		font-size:12px;
		padding:2px;
	}
	.table tbody>tr>td.right-align-sm{
		vertical-align: middle;
		text-align : right;
		font-size:12px;
		padding:2px;
	}
	.table tbody tr.highlight td {
		background-color: #CBE8F7;
	}
	
	.table tbody>tr.rasio {
		background-color: #CBE8F7;
	}
	
	.table tbody>tr>td.borderless{
		border: none;
	}

	.link:hover{
		cursor:pointer;
	}
	
	.col-centered {
		display:inline-block;
		float:none;
		/* reset the text-align */
		text-align:left;
		/* inline-block space fix */
		margin-right:-4px;
	}
	
	.inp-numeric{
		text-align:right;
	}
	
	.glyphicon:hover{
		cursor:pointer;
	}
	
	.menu_farm{
		margin:0px;
		display:block;
		padding:3px;
	}
	.menu_farm:hover{
		cursor:pointer;
		background-color:#F5F5F5;
	}
</style>

<link type="text/css" href="assets/libs/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="assets/libs/bootstrap/js/moment.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/riwayat_harian_kandang/pemantauan_lhk.js"></script>