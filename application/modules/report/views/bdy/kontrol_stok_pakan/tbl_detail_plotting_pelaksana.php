<table class="table table-bordered custom_table detail_plotting_pelaksana">
	<thead>
		<tr class="bg_biru">
			<th colspan="<?php echo count($plotting_pelaksana) ?>">Detail Pelaksana</th>
		</tr>
		<tr>
		<?php foreach($plotting_pelaksana as $key=>$val): ?>
			<th><?php echo $val['job_desc']=='pengawas' ?  ucfirst($val['job_desc']) : (ucfirst($val['job_desc']) . ' ' . $val['rn']); ?></th>
		<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<tr>
		<?php foreach($plotting_pelaksana as $key=>$val): ?>
			<td><?php echo $val['nama_pegawai']; ?></td>
		<?php endforeach; ?>
		</tr>
	</tbody>
</table>	



