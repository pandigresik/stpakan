<!--index page penerimaan retur pakan farm-->
<br><br>
<div id="panel_daftar_penerimaan" class="panel panel-default">
	<div class="panel-heading">Daftar Penerimaan Retur Pakan</div>	
	<div class="panel-body">
	
	<div id="list_farm" class="row header text-center">
		<div class="col-md-4 col-md-offset-4"></div>
	</div>
	<div id="main_page_retur">
		<form class="form form-horizontal"> <!--form horizontal-->
		<div class="row"> <!--begin row-->
			<div class="col-md-6">			
					<div class="form-group ckTindaklanjut">
						<div class="col-md-offset-1 col-md-8 checkbox">
							<label><input type="checkbox">Filter SJ yang belum diterima</label>
						</div>
					</div>
					
					<div class="form-group">							
						<label class="control-label col-md-3">Tanggal kirim</label>							
						<div class='col-md-8'>
							<div class='col-md-5'>
								<div class="form-group">
									<div class="input-group date">
										<input type="text" class="form-control parameter" name="startDate" />
										<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
							<div class='col-md-1 vcenter'> s.d </div>
							<div class='col-md-5'>
								<div class="form-group">
									<div class="input-group date" >
										<input type="text" class="form-control parameter" name="endDate" />
										<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
							
					<div class="form-group farm_asal">
						<label class="control-label col-md-3">Farm Asal</label>
						<div class='col-md-6'>
							<?php echo $all_farm ?>
						</div>
					</div>

			</div>
		</div>	<!--end row-->
		<div class='row'>
			<div class='col-md-6'></div>
			<div class='col-md-6'>
			
				<div class="form-group scan_sj" style="text-align:right;">
					<label class="control-label col-md-6">Scan No. SJ</label>
					<div class='col-md-5 offset-md-1'>
						<input type="text" class="form-control" name="no_sj" onBlur="Penerimaanreturpakanfarm.scan_sj(this)">
					</div>
				</div>
			
			</div>
		</div>
		</form> <!--end form horizontal-->
		
		<div id="div_penerimaan_retur_pakan"></div>	
	</div>

	</div>
</div>	

<div id="detail_penerimaan_area" class="panel panel-default hide">
	<div class="panel-heading">Penerimaan Pakan digudang</div>	
	<div class="panel-body">
		<div id="div_detail_penerimaan_sj"></div>
		<div id="div_detail_alokasi_penerimaan"></div>
		<div id="div_pakan_rusak_hilang"></div>
	</div>
</div>

<link type="text/css" href="assets/css/rekap_retur_pakan/retur_pakan_farm.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="assets/js/jquery.redirect.js"></script>
<script type="text/javascript" src="assets/js/rekap_retur_pakan/penerimaan_retur_pakan_farm.js"></script>