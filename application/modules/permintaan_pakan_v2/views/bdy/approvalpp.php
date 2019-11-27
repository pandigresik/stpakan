<div class="row" id="row_approval">							
	<div class="col-md-2" id="div_tombol_simpan">
		<?php echo $tombol_simpan ?>
	</div>		

</div>
<div class="tab-content new-line">
	<div id="daftarPermintaan" class="">
		<div class="row">
				<div class="col-md-9 col-md-offset-3">
						<form class="form form-horizontal">
							<div class="form-group">
								<div class="col-md-6 col-md-offset-2">
									<div class="checkbox ">
							          	<label><input name="tindak_lanjut" value="RV" checked type="checkbox" />Permintaan pakan membutuhkan tindak lanjut</label>
							        </div>
						        </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-2">No. PP</label>
								<div class='col-md-4'>
								   	<input type="text" name="no_lpb" class="form-control" >
						        </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-2">Nama Farm</label>
									<div class='col-md-4' name="divFarm">
							            <?php echo $list_farm?>
							        </div>							    	
							</div>
							<div class="form-group">
								<div class="col-md-2">
									<select name="tanggal_lpb" class="form-control" >
										<option value="lpb.tgl_buat">Tanggal Permintaan</option>
										<option value="lpb.tgl_rilis">Tanggal Rilis</option>
										<option value="lpb.tgl_approve1">Tanggal Approve</option>
										<option value="le.tgl_kirim">Tanggal Kirim</option>
									</select>
								</div>
								<div class='col-md-8'>
									<div class='col-md-3'>
							            <div class="form-group">
							                <div class="input-group date">
							                    <input type="text" class="form-control parameter" name="startDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>
							                </div>
							            </div>
							        </div>
							    	<div class='col-md-1 vcenter'>s.d.</div>
							        <div class='col-md-3'>
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
							<div class="form-group">
								<div class='col-md-2 col-md-offset-2'>
								   	<span name="btnCari" class="disabled btn btn-default" onclick="Permintaan.list_pp_cari(this)">Cari</span>
						        </div>
							</div>
							
					</form>
				</div>
				<div class="col-sm-12">
					<div class="row">
						<a class="tu-float-btn tu-float-btn-right tu-table-next" >
							<i class="glyphicon glyphicon-circle-arrow-right my-float"></i>
						</a>

						<a class="tu-float-btn tu-float-btn-left tu-table-prev" >
							<i class="glyphicon glyphicon-circle-arrow-left my-float"></i>
						</a>
					</div>
				</div>

				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">Daftar Permintaan Pakan</div>
						<div class="panel-body">
							<div id="daftar_pp_kafarm"  class="table-responsive" style="overflow-x:auto"></div>
						</div>
					</div>
				</div>

				
		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/permintaan_pakan_v2/farm.css?v=0.2" >
<link rel="stylesheet" type="text/css" href="assets/css/jquery.stickytable.css?v=0.2" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tupage-table/jquery.tupage.table.css" >
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/jquery.stickytable.js"></script>
<script type="text/javascript" src="assets/js/permintaan_pakan_v2/ppHandler.js"></script>
<script type="text/javascript" src="assets/js/permintaan_pakan_v2/approvalpp.js"></script>
