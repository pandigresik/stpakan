<div class="row">
	<div class="panel panel-primary">
		<div class="panel-heading">Laporan Harian Kandang - Permintaan Kandang</div>
		<div class="panel-body">
			<div class="col-md-12">
				<table id="lhk_permintaan_kandang" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th class="vert-align" >Nama Pakan</th>
							<th class="vert-align" >Tanggal Kebutuhan</th>
							<th class="vert-align" >Rekomendasi Kebutuhan (Sak)</th>
							<th class="vert-align" >Stok Gudang (Sak)</th>
							<th class="vert-align" >Rekomendasi Permintaan (Sak)</th>
						</tr>
					</thead>
					<tbody>
						<?php if (isset($rekomendasi_pakan) && !empty($rekomendasi_pakan)): ?>
							<?php foreach($rekomendasi_pakan as $key=>$val): ?>
								<tr>
									<td class="vert-align td_kode_barang"  data-kode_barang="<?php echo $key; ?>"><?php echo $val['nama_barang']; ?></td>
									<td class="vert-align td_tgl_kebutuhan" data-tglkebutuhan="<?php echo $val['tglkebutuhan'] ?>" ><?php echo tglIndonesia($val['tglkebutuhan'],'-',' '); ?></td>
									<td class="vert-align td_rekomendasi_kebutuhan" data-stokPakan="<?php echo $val['stok_pakan'] ?>" data-komposisi="<?php echo $val['komposisi'] ?>" data-standartKebutuhan="<?php echo $val['standart_kebutuhan'] ?>" data-jumlahAyam="<?php echo $val['jumlah_ayam'] ?>" ><?php echo $val['kebutuhan_pakan'] ?></td>
									<td class="vert-align " ><?php echo $val['jml_maks_pp_order']; ?></td>
									<td class="vert-align td_rekomendasi_permintaan" ><input type="text" class="form-control input-sm inp-numeric" id="rekomendasi_permintaan" value="" data-min="0" min="0" max="<?php echo $val['jml_maks_pp_order']; ?>" onchange="EntriLHK.validatorMaxPP(this);"  data-mandatory=1 data-maks_pp="<?php echo $val['jml_maks_pp_order']; ?>"></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>		
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>