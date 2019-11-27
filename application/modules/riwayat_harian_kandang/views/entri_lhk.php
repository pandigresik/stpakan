<div class="panel panel-default">
	<div class="panel-heading">Laporan Harian Kandang</div>
	<div class="panel-body">
		<div class="col-md-12">
			<center><h1><?php echo $nama_farm;?></h1></center>
			<div class="row">
				<button type="button" name="tombolSimpan" id="btnSimpan" class="btn btn-primary" onclick="EntriLHK.akanSimpan();" disabled>Simpan</button>
				<br/><br/>
			</div>
			<div class="row">
				<div class="col-md-3">
					<form class="form-inline">
						<div class="form-group">
							<label for="inp_kandang">Kandang</label>
							<input type="text" class="form-control input-sm field_input" name="kandang" id="inp_kandang" disabled>
						</div>
					</form>
				</div>
				<div class="col-md-3">
					<form class="form-inline">
						<div class="form-group">
							<label for="inp_flock">Flock</label>
							<input type="text" class="form-control input-sm field_input" name="flock" id="inp_flock" disabled>
						</div>
					</form>
				</div>
				<div class="col-md-3">
					<form class="form-inline">
						<div class="form-group">
							<label for="inp_doc_in">Tanggal DOC-In</label>
							<input type="text" class="form-control input-sm field_input" name="tglDOC-In" id="inp_doc_in" disabled>
						</div>
					</form>
				</div>
				<div class="col-md-3">
					<form class="form-inline">
						<div class="form-group">
							<label for="inp_umur">Umur</label>
							<input type="text" class="form-control input-sm field_input" name="umur" id="inp_umur" disabled>
						</div>
					</form>
				</div>
			</div>
			<hr>
			<div class="row">
				<div class="col-md-12">
					<center>
						<form class="form-inline">
							<div class="form-group col-sm-6 text-right">
								<label for="inp_flock">Tgl. LHK</label>
								<div class="form-group">
									<div class="input-group date" id="div_tgl_lhk">
										<input type="text" name="tglLHK" style="width:120px;" class="form-control disabled" id="inp_tgl_lhk" disabled readonly />
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group col-sm-6 text-left">
								<label for="inp_flock">Lampiran</label>
								<div class="form-group">
									<div class="input-group">
										<span class="input-group-addon"><i class="glyphicon glyphicon-paperclip"></i></span>
                                        <input type="text" class="form-control" id="lhkfile" name="docinfile" data-mandatory=1 readonly>
                                    	    <span class="btn btn-default btn-file input-group-addon">
                                             <b>...</b> <input type="file" id="lhkfileupload" />
                                            </span>
                                    </div>								
								</div>
							</div>
						</form>
					</center>
				</div>
			</div>
			
			<br/>
			
			<div id="entri_lhk_step">
				<div class="step-app">
					<ul class="step-steps">
						<li><a href="#step1">Penimbangan per Sekat</a></li>
						<li><a href="#step2">Populasi</a></li>
						<li><a href="#step3">Pakan</a></li>
						<li><a href="#step4">Permintaan Kandang</a></li>
					</ul>
					<div class="step-content">
						<?php echo $entri_lhk_step; ?>
					</div>
					<div class="step-footer">
						<button class="btn btn-primary" data-direction="prev">Kembali</button>
						<button class="btn btn-primary" data-direction="next">Berikutnya</button>
						<!--button class="btn btn-primary" data-direction="finish">Selesai & Simpan</button-->
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>

<link type="text/css" href="assets/libs/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />
<link rel="stylesheet" href="assets/libs/jquery-steps-master/dist/jquery-steps.css">

<script type="text/javascript" src="assets/libs/jquery-steps-master/dist/jquery-steps.js"></script>
<script type="text/javascript" src="assets/js/riwayat_harian_kandang/entri_lhk.js"></script>