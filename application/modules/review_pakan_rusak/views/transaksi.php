		
		<div class="new-line">
            <div class="col-md-5">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label text-right">No. Retur Pakan</label>

                        <div class="col-md-5">
                            <input type="text" readonly="readonly" class="form-control" id="no-retur-pakan" name="no-retur-pakan" placeholder="No. Retur Pakan">
                        </div>

                    </div>
                    
                    
                    
                    
                <div class="form-group">
                        <label class="col-md-4 control-label text-right" for="">Kandang</label>

                        <div class="col-md-5">
                            <input type="text" placeholder="Kandang" name="kandang" id="kandang" class="form-control">
                        </div>

                    </div></div>
            </div>
            <div class="col-md-5">
                <div class="form-horizontal"><div class="form-group">
                        <label class="col-md-4 control-label text-right" for="label">Tanggal dan Waktu Retur</label>

                        <div class="col-md-5">
                            <input type="text" readonly="readonly" placeholder="Tanggal dan Waktu Retur" name="tanggal-waktu-retur" id="tanggal-waktu-retur" class="form-control validasi">
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right">Pengawas Kandang</label>

                        <div class="col-md-6">
                            <label for="input" class="control-label text-right" id="pengawas-kandang">-</label>
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
	
		<div class="row" >
			

    
            <div class="panel panel-default">
                <div class="panel-heading">Detail Pengembalian Pakan Rusak </div>
                <div class="panel-body">
                    <div class="col-md-12" id="tabel_pengembalian_pakan_rusak">
    
                    </div>
                </div>  
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">&nbsp;</div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="col-md-12 ">
                            <div class="form-group form-horizontal">        
                                <div class="form-inline new-line">
                                    <label class="col-md-2" for="tanggal-kirim">Lampirkan Foto</label>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="lampirkan-foto" name="lampirkan-foto" value="<?php echo empty($value['attachment_name']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : $value['attachment_name']; ?>" readonly>
                                             <span class="btn btn-default btn-file input-group-addon">
                                                <b>...</b> <input type="file" id="file-upload" <?php echo empty($value['attachment_name']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : 'disabled'; ?>>
                                             </span>                                        
                                        </div>                                   
                                    </div>
                                    <div class="col-md-offset-2">
                                    <span class="help-block abang">* wajib diisi</span>
                                    </div>
                                </div>
                                <div id='format-file'></div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-md-2 col-md-offset-5" id="div_tombol_simpan">
                            <?php echo $div_tombol_simpan ?>
                        </div>
                    </div>
                </div>
                </div>  
            </div>


		</div>
		