<div class="row">
	<div class="col-md-12 text-center"><h2>Monitoring Permintaan Pakan</h2></div>

</div>
<div id="list_farm" class="header text-center"><div class="col-md-4 col-md-offset-4"><h3><?php echo $list_farm ?></h3></div></div>
<div>
	<div class="row">
		<div class="col-md-8 col-md-offset-4">
						<form class="form form-horizontal">
							<div class="form-group">
								<label class="control-label col-md-3">Periode DOC-In</label>
								<div class='col-md-2'>
									<?php
									echo '<select name="periode_doc_in"  class="form-control" >';


									echo '</select>';

									?>

						        </div>
							</div>
							<div class="form-group">
								<div class="col-md-3">
									<select name="tanggal_cari" class="form-control" >
										<option value="tgl_buat">Tanggal Permintaan</option>
										<option value="tgl_verifikasi">Tanggal Verifikasi DO</option>
										<option value="tgl_sj">Tanggal Surat Jalan</option>
										<option value="tgl_terima">Tanggal Terima</option>
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
								<div class='col-md-1 col-md-offset-3'>
								 	<span class="btn btn-default" onclick="Permintaan.list_monitoring_pp_cari(this)">Cari</span>
						    </div>
								<div class='col-md-2'>
								 	<span class="btn btn-default" data-idtabel="tabelmonitoringpp" onclick="Permintaan.exportExcel(this)">Export to excel</span>
						    </div>
							</div>

					</form>
				</div>
			</div>
</div>
<div id="div_monitroing_pp"></div>


<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/permintaan_pakan_v3/ppHandler.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/dist/xlsx.full.min.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/Blob.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/FileSaver.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/Export2Excel.js"></script>
<script type="text/javascript" src="assets/js/permintaan_pakan_v3/monitoring.js"></script>

<link rel="stylesheet" type="text/css" href="assets/css/permintaan_pakan/monitoringpp.css" >
