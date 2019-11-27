
<table class="table table-bordered">
	<thead>
		<tr>
			<th>No Surat Jalan</th>
			<th>No. OP</th>
			<th>Jumlah SJ (Zak)</th>
			<th>Jumlah Aktual (Zak)</th>
		</tr>
	</thead>
	<tbody>
					<?php foreach($items as $key => $value){ ?>
					<tr>
			<td><?php echo $value['no_surat_jalan']; ?></td>
			<td><?php echo $value['no_op']; ?></td>
			<td><?php echo $value['jumlah_sj']; ?></td>
			<td><?php echo $value['jumlah_aktual']; ?></td>
			<td></td>
		</tr>
					<?php } ?>
				</tbody>
</table>