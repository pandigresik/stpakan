<div class="row" id="row_approval">		
	<div class="col-md-1">
		<div onclick="Permintaan.kembali(this)" class="col-md-12 btn btn-default">Kembali</div> 			
	</div>								
	<div class="col-md-2" id="div_tombol_simpan">
		<?php echo $div_tombol_simpan ?>
	</div>		

</div>
		<div class="row col-md-12">
			<form class="form form-horizontal new-line">				
				<div class="row">
					<div class="col-md-6">	
						<div class="form-group">
							<div class="col-md-2">
								<label class="control-label">Kandang</label>
							</div>
							<div class="col-md-6">
								<input type="text" name="scan_rfid" class="form-control" onchange="Permintaan.cariKandang(this)">
								<?php 
								echo '<select name="no_reg" class="form-control hide" onchange="Permintaan.buat_pp_bdy(this)">';
										if(!empty($kandang)){
											if(count($kandang) > 1){
												echo '<option value="">Pilih kandang</option>';
											}
											foreach($kandang as $kd){
												echo '<option data-flok_bdy="'.$kd['flok_bdy'].'" value="'.$kd['no_reg'].'">Kandang '.$kd['kode_kandang'].'</option>';												
											}
										}										
								echo '</select>';
								?>
							</div>
							<div class="col-md-1">
								<label class="control-label"> Flock </label>
							</div>
							<div class="col-md-2">
								<input type="text"  class="form-control" size="1" readonly name="flock" value="<?php echo $flok ?>" />
							</div>
						</div>
					
						<div class="form-group">
							<div class="col-md-2">
								<label class="control-label">No. PP</label>
							</div>
							<div class="col-md-6">
								<input type="text" readonly data-status="<?php echo isset($pp['STATUS_LPB']) ? $pp['STATUS_LPB'] : '' ?>" name="no_pp" value="<?php echo isset($pp['NO_LPB']) ? $pp['NO_LPB'] : '' ?>" class="form-control">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-2">
								<label class="control-label">No. Ref. PP</label>
							</div>
							<div class="col-md-6">
								<label class="control-label">
									<?php if(isset($pp['NO_LPB'])){ ?>
										<span class="link_span span_ref_id" data-status="V"	data-no_pp="<?php echo isset($pp['REF_ID']) ? $pp['REF_ID'] : $pp['NO_LPB'] ?>" id="ref_id" onclick="Permintaan.detail_pp_popup(this)"><?php echo isset($pp['REF_ID']) ? $pp['REF_ID'] : '' ?></span>	
									<?php }	?>
									
								</label>
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-md-2">
								<label class="control-label">Tanggal Permintaan</label>
							</div>
							<div class="col-md-6">
								<input type="text" readonly="" name="tgl_permintaan" class="form-control" value="<?php echo isset($pp['TGL_BUAT']) ? tglIndonesia($pp['TGL_BUAT'],'-',' ') : '' ?>">
							</div>
						</div>																

						<div class="form-group hide showNext">
							<div class="col-md-2">
								<label class="control-label">Tanggal Kirim</label>
							</div>
							<div class="col-md-4">
								<div class="input-group">
									<input readonly="" name="tgl_kirim" class="form-control" type="text" value="<?php echo isset($data_pp['TGL_KIRIM']) ? tglIndonesia($data_pp['TGL_KIRIM'],'-',' ') : '' ?>">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group hide showNext">
							<div class="col-md-2">
								<label class="control-label">Tanggal Kebutuhan</label>
							</div>
							<div class="col-md-9">								
								<div class="col-sm-5">
									<div class="form-group">
										<div class="input-group date">
											<input readonly name="tgl_keb_awal" class="form-control" type="text" value="<?php echo isset($data_pp['TGL_KEB_AWAL']) ? tglIndonesia($data_pp['TGL_KEB_AWAL'],'-',' ') : '' ?>"> 
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span>
										</div>
									</div>
								</div>
								<div class="col-sm-2 vcenter">s.d.</div>
								<div class="col-sm-5">
									<div class="form-group">
										<div class="input-group date">
											<?php $tgl_kebutuhan_akhir = isset($data_pp['TGL_KEB_AKHIR']) ? $data_pp['TGL_KEB_AKHIR'] : '' ?>
											<input readonly onchange="Permintaan.show_detail_list_kebutuhan_pakan(this)" data-bisa_ubah_keb_akhir="<?php echo $editReview ?>" data-keb_akhir_lama="<?php echo $tgl_kebutuhan_akhir ?>" name="tgl_keb_akhir" class="form-control" type="text" value="<?php echo tglIndonesia($tgl_kebutuhan_akhir,'-',' ') ?>"> 
											<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span>
										</div>
									</div>
								</div>							
							</div>
						</div>

						<div class="form-group hide showNext">
							<div class="col-md-2">
								<label class="control-label">Umur Pakan</label>
							</div>
							<div class="col-md-6">
								<label class="control-label umur_pakan"></label>
								<label class="control-label">Hari</label>
							</div>
							
						</div>
						<div class="form-group hide">
							<div class="col-md-2">
								<label class="control-label"><u>Rencana Panen</u></label>
							</div>
							<div class="col-md-6" id="tableRencanaPanen" >
								
							</div>
							
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group hide">
							<div class="col-md-4">
								<label class="control-label"><span onclick="Permintaan.showInfoHistory(this)" id="btnHistoryInfo" class="btn btn-default">Informasi Kandang <i class="glyphicon glyphicon-chevron-right"></i></span></label>
							</div>						
						</div>
						<div class="detailHistory hide">
							<div class="form-group">
								<div class="col-md-2">
									<label class="control-label">Performa Kandang</label>
								</div>
								<div class="col-md-10" id="tablePerformaKandang" style="max-height:150">
									
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-2">
									<label class="control-label">Riwayat Budget</label>
								</div>								
								<div class="col-md-10" id="tableBudgetPakan">
									
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-2">
									<label class="control-label">Riwayat PP</label>
								</div>
								<div class="col-md-10" id="tableRiwayatPP">
									
								</div>
							</div>	
						</div>			
					</div>					
				</div>		
			</form>					
</div>

<div class="row">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="col-md-12" id="kebutuhan_pakan_internal"></div>
		</div>
	</div>
</div>
<link rel="stylesheet" type="text/css" href="assets/css/permintaan_pakan/transaksi.css?v=1">
<link rel="stylesheet" type="text/css" href="assets/css/home/kertaskerja.css">

<script type="text/javascript" src="assets/js/permintaan_pakan_v3/transaksi_bdy.js"></script>
