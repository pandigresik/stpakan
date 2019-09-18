<div class="row">
	<div class="col-md-2" id="divTombol">
		<?php
			echo implode(' ',$tombol);
		?>
	</div>	
</div>
<br />
	<div class="panel panel-primary">
		<div class="panel-heading">Stok Pakan Gudang</div>
		<div class="panel-body">
			<div class="container">
				<table class="table table-bordered custom_table">
					<thead>
						<tr>
							<th>Kode Pakan</th>
							<th>Nama Pakan</th>
							<th>Jumlah <br /> (Zak)</th>
							<th>Berat <br /> (Kg)</th>
							<th>Bentuk Pakan</th> 					
						</tr> 			
					</thead>
					<tbody>
					<?php
						if(!empty($listPakanSisa)){
							foreach($listPakanSisa as $r){
								if($r['jumlah'] > 0){
									echo '<tr>
										<td class="kode_pakan">'.$r['kode_pakan'].'</td>
										<td class="nama_pakan">'.$r['nama_pakan'].'</td>
										<td class="jumlah">'.formatAngka($r['jumlah'],0).'</td>
										<td class="berat">'.formatAngka($r['berat'],2).'</td>
										<td class="bentuk">'.convertKode('bentuk_barang',$r['bentuk']).'</td>
									</tr>';
								}								
							}
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading">Alokasi Retur Sisa Pakan</div>
		<div class="panel-body">
			<form class="form form-horizontal">
				<?php 
					if(isset($retur)){
						echo '<div class="form-group" data-name="no_referensi"><input type="hidden" name="NO_REFERENSI" value="'.$retur['NO_RETUR'].'"></div>';
					}
				?>		
				<div class="col-md-6">
					<div class="form-group" data-name="farm_tujuan">
						<label class="control-label col-md-3">Farm Tujuan</label>
						<div class='col-md-6'>
							<?php echo $farmTujuan ?>
						</div>
					</div>
					<div class="form-group" data-name="tgl_kirim">
						<label class="control-label col-md-3">Tanggal Kirim</label>
						<div class='col-md-6'>
							<div class="input-group date">							
								<input type="text" class="form-control parameter" value="<?php echo tglIndonesia($tglKirim,'-',' ') ?>" name="tglKirim" readonly />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>
					<div class="form-group" data-name="keterangan">
						<label class="control-label col-md-3">Keterangan</label>
						<div class='col-md-6'>
							<!--<textarea name="keterangan" cols="40" rows="3" maxlength="100"><?php echo isset($logRetur) ? $logRetur['KETERANGAN'] : '' ?></textarea>-->
							<textarea name="keterangan" cols="40" rows="3" maxlength="100"></textarea>
						</div>
					</div>
					<div class="form-group" data-name="lampiran">
						<label class="control-label col-md-3">Unggah Lampiran</label>
						<div class='col-md-6'>
							<div class="input-group">
                    			<input type="text" readonly id="lampirkan-file" class="form-control" value="<?php echo isset($retur) ? $retur['LAMPIRAN'] : '' ?>" >                    
                				<span class="btn btn-default btn-file input-group-addon">
                                	<b>...</b> <input type="file" name="lampiran" id="lampiran" onChange="Returpakanfarm.attachmentCheck(this)">
                            	</span>
							</div>
						</div>
					</div>		
				</div>		
				<div class="col-md-6">
					<table class="table table-bordered custom-table">
						<thead>
							<tr>
								<th>Nama Pakan</th>
								<th>Jumlah (Zak)</th>
							</tr>
						</thead>
						<tbody>
						<?php
							if(!empty($listPakanSisa)){
								foreach($listPakanSisa as $r){
									$jumlahPakan = 0;
									if(isset($listPakan)){
										if(isset($listPakan[$r['kode_pakan']])){
											$jumlahPakan = $listPakan[$r['kode_pakan']]['JUMLAH'];	
										}
									}
									echo '<tr>										
										<td class="nama_pakan">'.$r['nama_pakan'].'</td>
										<td class="jumlah"><div class="col-md-3"><input name="jmlPakan" class="form-control" type="text" value="'.formatAngka($jumlahPakan,0).'" data-kodepakan="'.$r['kode_pakan'].'" data-max="'.$r['jumlah'].'" /></div></td>										
									</tr>';
								}
							}
						?>	
						</tbody>
					</table>	
				</div>
				<div class="col-md-12">
					<div class="btn btn-default col-md-4 col-md-offset-4" onclick="Returpakanfarm.simpan(this)">Simpan</div>
				</div>
			</form>
		</div>
	</div>
	