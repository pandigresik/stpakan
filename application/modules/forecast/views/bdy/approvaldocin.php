<div class="tab-content new-line">
	<div id="daftarPermintaan" class="">
		<div class="row">
				<div class="col-md-12 col-md-offset-3">
						<form class="form form-horizontal">
							<div class="form-group">
								<div class="col-md-6 col-md-offset-2">
									<div class="checkbox ">
							          	<label><input name="belumApprove" value="P2" checked type="checkbox" />Farm yang membutuhkan tindak lanjut aktivasi siklus</label>
							        </div>

						        </div>
							</div>
							<div class="form-group">
								<label class="control-label col-md-2">Nama Farm</label>
									<div class='col-md-2' name="divFarm">
							        <?php echo $list_farm?>
							    </div>
							</div>
							<div class="form-group">
								<div class="col-md-2">
									<select name="tanggal_docin" class="form-control" >
										<option value="z.tgl_chickin">Tanggal DOC In</option>
										<option value="z.approve_kadept">Tanggal Approve Kadept</option>
										<option value="z.tgl_approvekadiv">Tanggal Aktivasi</option>
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
								   	<span name="btnCari" class="disabled btn btn-default" onclick="AktivasiKandang.approval_cari(this)">Cari</span>
						        </div>
							</div>
							<!-- hidden saja karena sebagai informasi status lpb yang seharusnya ditampilkan  -->
							<div class="hide">
								<input name="status" value="RL" type="checkbox" />
								<input name="status" value="P2" type="checkbox" />
							</div>
					</form>
				</div>

				<div class="col-md-12" >
					<div class="panel panel-default">
						<div class="panel-heading">Daftar Farm yang membutuhkan tindak lanjut aktivasi siklus</div>
						<div class="panel-body">
							<div id="daftar_konfirmasi_docin"></div>
						</div>
					</div>
				</div>

		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/permintaan_pakan/farm.css?v=1" >
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/forecast/aktivasiKandang.js"></script>
<script type="text/javascript" src="assets/js/forecast/approvaldocin.js"></script>
