<div class="sticky-table">
<table class="table table-bordered custom_table detail_rencana_pengiriman">
	<thead>
		<tr class="bg_biru">
			<th colspan="5">Rencana Pengiriman <?php echo $periode_siklus ?></th>
		</tr>
		<tr class="sticky-header">
			<th rowspan=2>Tgl Kirim</th>
			<th rowspan=2>Tgl Kebutuhan</th>
			<th rowspan=2>Umur</th>
			<th colspan=2>Jenis Pakan</th>
		</tr>
		<tr class="sticky-header">
			<?php foreach($jenisBarangRencanaPengiriman as $key=>$val): ?>
				<th><?php echo $val['DESKRIPSI']; ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
	
	<?php foreach($rencanaPengiriman as $keyTglKirim=>$valTglKirim): ?>
		<?php 	
			$counter_tglkirim = 1;
			$rowspan_tglkirim = count($valTglKirim);
		?>
		<?php foreach($valTglKirim as $keyTglKebutuhan=>$valTglKebutuhan): ?>
			<tr>
				<?php if ($counter_tglkirim==1): ?>
				<td rowspan="<?php echo $rowspan_tglkirim;?>"><?php echo tglIndonesia($keyTglKirim,'-',' '); ?></td>
				<?php endif; ?>
				<td><?php echo tglIndonesia($keyTglKebutuhan,'-',' '); ?></td>
				<td><?php echo $valTglKebutuhan['umur']; ?></td>
				<?php foreach($jenisBarangRencanaPengiriman as $keyBarangRencanaPengiriman=>$valBarangRencanaPengiriman): ?>
					<td> 
						<?php echo isset($valTglKebutuhan[$valBarangRencanaPengiriman['KODE_BARANG']]) && !empty($valTglKebutuhan[$valBarangRencanaPengiriman['KODE_BARANG']]) ? formatAngka($valTglKebutuhan[$valBarangRencanaPengiriman['KODE_BARANG']]['jumlah'],2) : '0' ; ?>
					</td>
				<?php endforeach; ?>
			</tr>
			<?php $counter_tglkirim++; ?>
		<?php endforeach; ?>
	<?php endforeach; ?>
	</tbody>
</table>	
</div>


