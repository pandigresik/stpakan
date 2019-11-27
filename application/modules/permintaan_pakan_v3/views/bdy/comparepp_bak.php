
<div class="row">
	<div class="col-md-6">
		<fieldset>
			<legend>Pengajuan oleh Kepala Farm</legend>
			<div  style="max-height:500px;overflow:auto;overflow-x:hidden">
			<?php
				foreach($kf as $tglkirim){
					
					$header = $tglkirim['detailpp'][0];
					echo '<div style="border:1px solid #999;margin: 5px; padding: 5px">';
					echo '<form class="form form-horizontal">';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">No. PP</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.$header['no_lpb'].'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Flock</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.$flok.'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Tanggal Kirim</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.tglIndonesia($header['TGL_KIRIM'],'-',' ').'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Tanggal Kebutuhan Awal</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.tglIndonesia($header['TGL_KEB_AWAL'],'-',' ').'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Tanggal Kebutuhan Akhir</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.tglIndonesia($header['TGL_KEB_AKHIR'],'-',' ').'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Kuantitas PP</label>
						<div class="col-md-6">
							<div class="input-group">
								<input type="text" class="form-control" value="'.$tglkirim['totalpp'].'" readonly />
								<span class="input-group-addon">Sak</span>
							</div>
						</div>
					</div>';
					echo '</form>';
					echo '<table class="table table-bordered">';
					echo '<thead>';
					echo '<tr><th class="text-center">Kode <br > Barang</th><th class="text-center">Nama <br > Barang</th><th class="text-center">Optimasi PP</th><th class="text-center">Rekomendasi PP</th></tr>';
					echo '</thead>';
					echo '<tbody>';
					foreach($tglkirim['detailpp'] as $pakan){
						echo '<tr>
							<td>'.$pakan['KODE_BARANG'].'</td>
							<td>'.$pakan['NAMA_BARANG'].'</td>
							<td class="text-center">'.$pakan['optimasi_pp'].'</td>
							<td class="text-center">'.$pakan['rekomendasi_pp'].'</td>
						</tr>';
					}
					echo '</tbody>';
					echo '</table>';
					echo '</div>';
				}
			?>
			</div>
		</fieldset>
	</div>
	<div class="col-md-6">
		<fieldset>
			<legend>Revisi oleh Kepala Departemen</legend>
			<div  style="max-height:500px;overflow:auto;overflow-x:hidden">
			<?php
				foreach($db as $tglkirim){
					$header = $tglkirim['detailpp'][0];
					echo '<div style="border:1px solid #999;margin: 5px; padding: 5px">';
					echo '<form class="form form-horizontal">';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">No. PP</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.$header['no_lpb'].'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Flock</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.$flok.'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Tanggal Kirim</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.tglIndonesia($header['TGL_KIRIM'],'-',' ').'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Tanggal Kebutuhan Awal</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.tglIndonesia($header['TGL_KEB_AWAL'],'-',' ').'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Tanggal Kebutuhan Akhir</label>
						<div class="col-md-6">
							<input type="text" class="form-control" value="'.tglIndonesia($header['TGL_KEB_AKHIR'],'-',' ').'" readonly />
						</div>
					</div>';
					echo '<div class="form-group">
						<label  class="col-sm-4 control-label">Kuantitas PP</label>
						<div class="col-md-6">
							<div class="input-group">
								<input type="text" class="form-control" value="'.$tglkirim['totalpp'].'" readonly />
								<span class="input-group-addon">Sak</span>
							</div>
						</div>
					</div>';
					echo '</form>';
					echo '<table class="table table-bordered">';
					echo '<thead>';
					echo '<tr><th class="text-center">Kode <br > Barang</th><th class="text-center">Nama <br > Barang</th><th class="text-center">Optimasi PP</th><th class="text-center">Persetujuan PP</th></tr>';
					echo '</thead>';
					echo '<tbody>';
					foreach($tglkirim['detailpp'] as $pakan){
						echo '<tr>
							<td>'.$pakan['KODE_BARANG'].'</td>
							<td>'.$pakan['NAMA_BARANG'].'</td>
							<td class="text-center">'.$pakan['optimasi_pp'].'</td>
							<td class="text-center">'.$pakan['persetujuan_pp'].'</td>
						</tr>';
					}
					echo '</tbody>';
					echo '</table>';
					echo '</div>';
				}
			?>
			</div>
		</fieldset>
	</div>
</div>
