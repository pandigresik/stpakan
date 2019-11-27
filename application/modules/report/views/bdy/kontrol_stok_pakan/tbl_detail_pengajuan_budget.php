<table class="table table-bordered custom_table detail_pengajuan_budget">
	<thead>
		<tr class="bg_biru">
			<th colspan="2">Pengajuan Budget / Farm</th>
		</tr>
		<tr>
			<th>Kategori Glangsing</th>
			<th>Jumlah Sak</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($pengajuan_budget as $key=>$val): ?>
		<tr>
			<td><?php echo $val['NAMA_BUDGET']; ?></td>
			<td><?php echo angkaRibuan($val['JML_ORDER']); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>	



