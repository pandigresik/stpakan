<div id="list_farm" class="row header text-center">
	<div class="col-md-4 col-md-offset-4"><h3><?php echo $list_farm ?></h3></div>
</div>
<div  id="main_page_retur">
	<div class="row">		
		<div class="col-md-6 col-md-offset-3">			
						<form class="form form-horizontal">
							<div class="form-group ckTindaklanjut">
								<div class="col-md-offset-3 col-md-8 checkbox">
									<label>
										<input type="checkbox" data-tipe="gudang">Belum Selesai Proses
									</label>
								</div>
							</div>								
							<div class="form-group farm_tujuan">
								<label class="control-label col-md-3">Farm Tujuan</label>
								<div class='col-md-6'>
									<?php echo $all_farm ?>
						        </div>
							</div>
							<div class="form-group">							
								<label class="control-label col-md-3">Tanggal kirim</label>							
								<div class='col-md-8'>
									<div class='col-md-5'>
							            <div class="form-group">
							                <div class="input-group date">
							                    <input type="text" class="form-control parameter" name="startDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>
							                </div>
							            </div>
							        </div>
							    	<div class='col-md-1 vcenter'> s.d </div>
							        <div class='col-md-5'>
							            <div class="form-group">
							                <div class="input-group date" >
							                    <input type="text" class="form-control parameter" name="endDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>
							                </div>
							            </div>
							        </div>
								</div>
							</div>
							<div class="form-group" style="margin-top:-20px">
								<div class='col-md-1 col-md-offset-3'>
								 	<span class="btn btn-default" onclick="Returpakanfarm.cariReturTimbang()">Cari</span>
						   	 	</div>								
							</div>

					</form>
				</div>
			</div>			
	<div class="panel panel-primary">
		<div class="panel-heading">Daftar Pengiriman Retur Pakan </div>
		<div class="panel-body">
			<div id="div_retur_pakan"></div>
		</div>
	</div>
	<div id="div_detail_retur_pakan"></div>		
</div>
</div>

<link type="text/css" href="assets/css/rekap_retur_pakan/retur_pakan_farm.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="assets/js/jquery.redirect.js"></script>
<script type="text/javascript" src="assets/js/rekap_retur_pakan/retur_pakan_farm.js"></script>