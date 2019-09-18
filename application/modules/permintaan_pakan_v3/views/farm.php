<div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#daftarPermintaan" role="tab" data-toggle="tab" id="for_daftarPermintaan">Daftar Permintaan<span class='help'></span></a>
		</li>

		<li>
			<a href="#transaksi" role="tab" data-toggle="tab" id="for_transaksi">Transaksi<span class='help'></span></a>
		</li>

	</ul>
</div>

<div class="tab-content new-line">
	<div id="daftarPermintaan" class="tab-pane fade active in">
		<div class="row">
		 	<div class="container col-md-12">
				<div class="col-md-1" id="div_tombol_buatpp" onclick="Permintaan.transaksi_pp(this,'#for_transaksi')">
					<?php echo $div_buat_pp ?>
				</div>
				<div class="col-md-11">
						<form class="form form-horizontal">
							<div class="form-group">
								<div class="col-md-8">
									<div class="row">
							           <div class="checkbox col-md-2 col-md-offset-2">
							             	<label><input value="D" <?php echo isset($status_checkbox['D']) && $status_checkbox['D'] == 0  ? 'disabled' : '' ; echo $default_checkbox['D'] ?> type="checkbox"/>Draft</label>
							           </div>
							           <div class="checkbox col-md-2">
							             	<label><input value="N" <?php echo isset($status_checkbox['N']) && $status_checkbox['N'] == 0 ? 'disabled' : '' ; echo $default_checkbox['N']  ?> type="checkbox" />Baru</label>
							           </div>
							           <div class="checkbox col-md-2">
							             	<label><input value="A" <?php echo isset($status_checkbox['A']) && $status_checkbox['A'] == 0 ? 'disabled' : '' ; echo $default_checkbox['A']  ?> type="checkbox" />Approved</label>
							           </div>
							           <div class="checkbox col-md-2">
							             	<label><input value="V" <?php echo isset($status_checkbox['V']) && $status_checkbox['V'] == 0 ? 'disabled' : '' ; echo $default_checkbox['V']  ?> type="checkbox" />Void</label>
							           </div>
						           </div>
						        </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-2">No. LPB</label>
								<div class='col-md-2'>
								   	<input type="text" name="no_lpb" class="form-control" >
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
	<div id="transaksi" class="tab-pane fade">

	</div>
	<div id="laporan" class="tab-pane fade">

	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/permintaan_pakan/farm.css?v=2" >
<script type="text/javascript" src="assets/js/permintaan_pakan_v3/farm.js"></script>
