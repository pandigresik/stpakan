<div class="panel panel-default">
	<div class="panel-heading">Pengembalian Sak Kosong</div>
	<div class="panel-body">
		<div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="presentation">
				<li class="active">
					<a href="#daftarPengembalian" role="tab" data-toggle="tab" id="for_daftarPermintaan">Daftar Pengembalian Sak<span class='help'></span></a>
				</li>
				<li>
					<a href="#dtransaksi" role="tab" data-toggle="tab" id="for_transaksi">Transaksi<span class='help'></span></a>
				</li>
			</ul>
		</div>
		
		<div class="tab-content new-line">
			<div id="daftarPengembalian" class="tab-pane fade active in ">
				<div class="row">				
					<div class="container col-md-12">
						<!--<div class="col-md-10">
								<form class="form form-horizontal">
									<div class="form-group">
										<div class="col-md-1">
											<label class="control-label col-md-1">Tanggal</label>
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
													<div class='col-md-1'>
														<span class="btn btn-default" onclick="Pengembalian.list_cari(this)">Cari</span>
													</div>
										</div>

									</div>
									<div class="form-group">
										<div class='col-md-2 col-md-offset-1'>

										</div>
									</div>

							</form>
						</div>-->

						<div class="col-md-12" id="list_pengembalian">
						</div>
						<div class="col-md-12" id="transaksi">
						</div>
					</div>
				</div>
			</div>
			<div id="dtransaksi" class="tab-pane fade">			
				<div class="form form-horizontal row col-md-12">
					<div class="row">
						<div class="col-md-6">
							<div class="col-md-3">
								<label for="no_pp">Scan RFID</label>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<div class="">
										<input type="text" id="input_kandang_pengembalian" name="kandang_pengembalian" onchange="Pengembalian.scanRFID(this)" class="form-control" value="">										
										<span class="help-block"></span>
									</div>
								</div>
							</div>
							<div class="col-md-2" id="div_tombol_simpan">
								<div class="btn btn-default disabled" data-aksi="simpan" onclick="Pengembalian.simpan(this)">Simpan</div>
							</div>
						</div>
					</div>
				</div>
				<div id="tabel_pengembalian_sak">
					<?php echo isset($list_pakan) ? $list_pakan : ''; ?>
				</div>				
			</div>
		</div>
	</div>
</div>
<div id="status_lock_timbang" class="hide"><?php echo $lockTimbangan ? 0 : 1 ?></div>
<link rel="stylesheet" type="text/css" href="assets/css/pengembalian_sak/pengembalian.css?v=1" >
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/pengembalian_sak/pengembalian.js"></script>
