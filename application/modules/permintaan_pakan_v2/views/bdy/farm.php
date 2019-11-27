<div id="main_pp">
	<div id="daftarPermintaan">
		<div class="row">
		 	<div class="container col-md-12">
				<div class="col-md-1" id="div_tombol_buatpp">
					<?php echo $div_buat_pp ?>
				</div>
				<div class="col-md-9 col-md-offset-2">
						<form class="form form-horizontal">	
						<?php if($tindak_lanjut) { ?>				
							<div class="form-group">
								<div class="checkbox col-md-offset-2 col-md-10">
							       	<label><input name="tindak_lanjut" type="checkbox" checked onchange="Permintaan.enableFilterPP(this)" />Permintaan pakan membutuhkan tindak lanjut</label>
							    </div>
							</div>			
						<?php } ?>	
							<div class="form-group">
								<label class="control-label col-md-2">No. PP</label>
								<div class='col-md-2'>
								   	<input type="text" name="no_lpb" class="form-control" >
						        </div>
							</div>
							<div class="form-group">
								<div class="col-md-2">
									<select name="tanggal_lpb" class="form-control" >									
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
								   	<span class="btn btn-default" id="span_cari_pp" onclick="Permintaan.list_pp_cari(this)">Cari</span>
						        </div>
							</div>

					</form>
				</div>

				<div class="col-md-12" id="daftar_pp_kafarm">
					<?php
						echo $list_pp;
					?>
				</div>
			</div>
		</div>
	</div>
	
	<div id="laporan" class="tab-pane fade">

	</div>
</div>
<div id="transaksi"></div>
<link rel="stylesheet" type="text/css" href="assets/css/permintaan_pakan_v2/farm.css?v=0.3" >
<script type="text/javascript" src="assets/js/permintaan_pakan_v2/farm_bdy.js"></script>
<script type="text/javascript" src="assets/js/jquery.alphanum.js"></script>
