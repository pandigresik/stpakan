<div class="panel panel-primary">
	<div class="panel-heading">Detail Retur Sisa Pakan</div>	
	<div class="panel-body">
			<form class="form form-horizontal">
				<div class="col-md-6">
					<div class="form-group" data-name="farm_tujuan">
						<label class="control-label col-md-3">Farm Tujuan</label>
						<div class='col-md-6'>						
							<label class="control-label"><?php echo $farm[$retur['FARM_TUJUAN']]['nama_farm'] ?></label>
						</div>
					</div>
					<div class="form-group" data-name="tgl_kirim">
						<label class="control-label col-md-3">Tanggal Kirim</label>
						<div class='col-md-6'>
							<label class="control-label"><?php echo tglIndonesia($retur['TGL_KIRIM'],'-',' ') ?></label>
						</div>
					</div>
					<div class="form-group" data-name="keterangan">
						<label class="control-label col-md-3">Keterangan</label>
						<div class='col-md-6'>							
							<label class="control-label"><?php echo end($logRetur)['KETERANGAN'] ?></label>						
						</div>
					</div>
					<div class="form-group" data-name="lampiran">
						<label class="control-label col-md-3">Unggah Lampiran</label>
						<div class='col-md-6'>
							<label class="control-label"><i class="glyphicon glyphicon-paperclip"> </i> <a target="_blank" href="<?php echo site_url('file_upload/'.$retur['LAMPIRAN']) ?>"><?php echo $retur['LAMPIRAN']?></a></label>	
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
									echo '<tr>										
										<td class="nama_pakan">'.$r['NAMA_PAKAN'].'</td>
										<td class="jumlah">'.$r['JUMLAH'].'</td>										
									</tr>';
								}
							}
						?>	
						</tbody>
					</table>	
				</div>				
			</form>
			<div class="row col-md-12">
				<h5><u><strong>Riwayat Pengajuan Retur</strong></u></h5>
				<?php 
					if(!empty($logRetur)){
						foreach($logRetur as $lrf){
							$keterangan = $lrf['STATUS'] != 'N' ? $lrf['KETERANGAN'] : '';
							echo '<div>['.$lrf['NAMA_PEGAWAI'].'] '.$keterangan.' - <i>'.$listStatus[$lrf['STATUS']].'</i> , '.convertElemenTglWaktuIndonesia($lrf['TGL_BUAT']).'</div>';
						}
					}
				?>
			</div>
		</div>		
	
</div>