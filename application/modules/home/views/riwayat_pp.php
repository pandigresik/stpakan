<div>
	<div class="row">
		<div class="col-md-7">
			<div class="row">
				<div class="col-md-5 text-right">No. OP</div>
				<div class="col-md-2"><?php echo $header['NO_OP'] ?></div>
			</div>
			<div class="row">
				<div class="col-md-5 text-right">Tanggal / Jam Terbit OP</div>
				<div class="col-md-3"><?php echo convertElemenTglWaktuIndonesia($header['TGL_BUAT']) ?></div>
			</div>
		</div>
		<div class="col-md-5">
			<div class="row">
				<div class="col-md-3">Farm</div>
				<div class="col-md-2"><?php echo $header['NAMA_FARM'] ?></div>
			</div>
		</div>
	</div>
	<div>
		<table class="table table-bordered custom_table">
			<thead>
				<tr>
					<th rowspan="2">No. PP</th>
					<th rowspan="2">Tanggal / Jam Terbit PP</th>
					<th rowspan="2">Penerbit</th>
					<th colspan="2">Periode Kebutuhan</th>
					<th rowspan="2">Kuantitas PP (Sak) </th>
					<th rowspan="2">Status</th>
				</tr>
				<tr>
					<th>Awal</th>
					<th>Akhir</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($detail_pp)){
					foreach($detail_pp as $pp){
						$class_tr = $pp['status'] == 'BATAL' ? 'abuabu' : '';
						echo '<tr class="'.$class_tr.'">
							<td><span class="link_span" onclick="Permintaan.detail_pp_popup(this)" data-flok="'.$pp['flok'].'" data-status="'.$pp['status_lpb'].'" data-no_pp="'.$pp['no_lpb'].'">'.$pp['no_lpb'].'</span></td>
							<td>'.convertElemenTglWaktuIndonesia($pp['tgl_rilis']).'</td>
							<td>'.$pp['nama_pegawai'].'</td>
							<td>'.tglIndonesia($pp['tgl_keb_awal'],'-',' ').'</td>
							<td>'.tglIndonesia($pp['tgl_keb_akhir'],'-',' ').'</td>
							<td class="number">'.$pp['kuantitas_pp'].'</td>
							<td>'.$pp['status'].'</td>
						</tr>';
					}

				}?>
			</tbody>
		</table>
	</div>
</div>
