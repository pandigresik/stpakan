<div id="div_content">
	<div class="row col-md-10">
		<div class="btn-group div_btn" style="margin-bottom:30px">
			<?php echo $tombol ?>
	        
	    </div>
		<div class="form form-horizontal" id="form_input">					
			<div class="form-group">
				<label class="control-label col-md-2" style="text-align: right;">Tanggal Pengeluaran</label>
				<div class="col-md-10">
					<div class="form-inline">
						<div class="form-group col-md-3">
							<div class="input-group">
								<input type="text" class="form-control" id="startDate" name="startDate" placeholder="" value="<?php echo $tgl_sekarang ?>" />
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</div>
							</div>
						</div>
						 
						<label class="control-label col-md-1" style="text-align: center;">s/d</label>
						<div class="form-group col-md-3">
							<div class="input-group">
								<input type="text" class="form-control" id="endDate" name="endDate" placeholder=""  value="<?php echo $tgl_sekarang ?>" />								
								<div class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</div>
							</div>
						</div>
                        <label class="control-label col-md-2" style="text-align: center;">Status DO</label>
						<div class="col-md-3">							
                            <select class="form-control" name="status_do">
                                <option value="belum">DO Belum Diproses</option>
                                <option value="sudah">DO Terproses</option>
                                <option value="semua">Semua DO</option>
                            </select>
						</div>						
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row col-md-12" >
		<div id="div_list_laporan" style="padding-top:20px">
			
		</div>
	</div>
	<div class="row col-md-12" >
		<div id="div_detail_do" style="padding-top:20px">
			
		</div>
	</div>
</div>

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/jquery.redirect.js"></script>
<script type="text/javascript" src="assets/js/sales_order/realisasi_penjualan.js"></script>

