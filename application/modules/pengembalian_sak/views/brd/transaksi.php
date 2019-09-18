		<div class="row">
			<div class="col-md-2" id="div_tombol_simpan">
				<?php echo $div_tombol_simpan ?>
			</div>
		</div>
		<div class="form form-horizontal row col-md-12">
			<div class=" row">
				<div class="col-md-6">
					<div class="col-md-3">
						<label for="no_pp">No. Pengembalian</label>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" readonly name="no_pengembalian" value="<?php echo isset($header['no_pengembalian']) ? 'RS/'.$header['no_pengembalian'] : '' ?>" class="form-control">
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-5">
					<div class="row">
						<div class="col-md-3">
							<label for="tgl_permintaan">Tanggal</label>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<div class="input-group">
									<input type="date" readonly name="tgl_pengembalian" class="form-control"  value="<?php echo $tgl_pengembalian ?>">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-3">
						<label for="no_pp">Kandang</label>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="kandang" class="form-control" value="<?php echo isset($header['kandang']) ? $header['kandang'] : '' ?>" <?php echo isset($header['kandang']) ? 'readonly' : '' ?>>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="row">
			<div class="panel panel-default">
				<div class="panel-heading">Detail Pengembalian Sak Kosong </div>
				<div class="panel-body">
					<div class="col-md-12" id="tabel_pengembalian_sak">
						<?php if(isset($list_pakan)){
							echo $list_pakan;	
						}
						?>
					</div>
				</div>	
			</div>
		</div>
		