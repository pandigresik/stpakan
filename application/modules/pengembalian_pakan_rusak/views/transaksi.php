		
		<div class="new-line">
            <div class="col-md-5">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label text-right">No. Retur Pakan</label>

                        <div class="col-md-5">
                            <input type="text" readonly="readonly" class="form-control" id="no_retur_pakan" name="no-retur-pakan" placeholder="No. Retur Pakan" value="<?php echo $no_pengembalian ?>">
                        </div>

                    </div>
                    
                    
                    
                    
                <div class="form-group">
                        <label class="col-md-4 control-label text-right" for="">Kandang</label>

                        <div class="col-md-5">
                            <input type="text" placeholder="Kandang" name="kandang" id="kandang" class="form-control" value="<?php echo $nama_kandang ?>" <?php echo !empty($nama_kandang) ? 'disabled' : ''; ?> >
                        </div>

                    </div></div>
            </div>
            <div class="col-md-5">
                <div class="form-horizontal"><div class="form-group">
                        <label class="col-md-4 control-label text-right" for="label">Tanggal dan Waktu Retur</label>

                        <div class="col-md-5">
                            <input type="text" readonly="readonly" placeholder="Tanggal dan Waktu Retur" name="tanggal-waktu-retur" id="tanggal_waktu_retur" class="form-control validasi" value="<?php echo $tgl_pengembalian ?>">
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right">Pengawas Kandang</label>

                        <div class="col-md-6">
                            <label for="input" class="control-label text-right" id="pengawas-kandang"><?php echo $user_verifikasi ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right">Admin Gudang</label>

                        <div class="col-md-6">
                            <label for="input" class="control-label text-right" id="admin-gudang"><?php echo $admin_gudang ?></label>
                        </div>

                    </div>
                    
                    
                    
                </div>
            </div>
        </div>
	
		<div class="row">
			<div class="panel panel-default">
				<div class="panel-heading">Detail Pengembalian Pakan Rusak </div>
				<div class="panel-body">
					<div class="col-md-12" id="tabel_pengembalian_pakan_rusak">
						<?php if(isset($list_pakan)){
							echo $list_pakan;	
						}
						?>
					</div>
					<div class="row col-md-12">
						<div class="col-md-2 col-md-offset-5" id="div_tombol_simpan">
							<?php echo $div_tombol_simpan ?>
						</div>
					</div>
				</div>	
			</div>
		</div>
		