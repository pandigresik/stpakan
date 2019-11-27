<div class="panel panel-default">
	<div class="panel-heading">Detail Pengembalian Sak Kosong </div>
	<div class="panel-body">
		<div class="form form-horizontal row col-md-12">
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-3">
						<label for="no_pp">Kandang</label>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<div class="input-group">
								<input type="text" name="kandang_pengembalian" class="form-control" value="<?php echo isset($header['kandang']) ? $header['kandang'] : '' ?>" <?php echo isset($header['kandang']) ? 'readonly' : '' ?>>
							</div>
						</div>
					</div>
					<div class="col-md-2" id="div_tombol_simpan">
						<?php echo $div_tombol_simpan ?>
					</div>
				</div>
			</div>
		</div>
		<div id="tabel_pengembalian_sak">
			<?php echo isset($list_pakan) ? $list_pakan : ''; ?>
		</div>
	</div>
</div>
