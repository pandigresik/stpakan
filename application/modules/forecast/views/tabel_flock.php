<table class="table">
	<thead>
		<tr>
			<th>&nbsp;</th>
			<?php echo '<th>'.implode('</th><th>',$header).'</th>'?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($tbody as $i => $val){ ?>
		<tr>
			<td>
				<?php if(empty($val['flok'])) {?>
				<input type="checkbox" value="<?php echo $val['doc_in'] ?>" data-no_reg="<?php echo $val['no_reg'] ?>" />
				<?php } ?>
			</td>
			<?php 
				/* hapus data no_registrasi */
				array_pop($val);
				$val['tipe_kandang'] = convertKode('tipe_kandang',$val['tipe_kandang']);
				$val['tipe_lantai'] = convertKode('tipe_lantai',$val['tipe_lantai']);
				$val['doc_in'] = tglIndonesia($val['doc_in'],'-',' ');
				$val['tanggal_tetas'] = !empty($val['tanggal_tetas']) ? tglIndonesia($val['tanggal_tetas'],'-',' ') : '';
				$val['kapasitas'] = angkaRibuan($val['kapasitas']);
				$val['jantan'] = angkaRibuan($val['jantan']);
				$val['betina'] = angkaRibuan($val['betina']);
				echo '<td>'.implode('</td><td>',$val).'</td>' 
			?>
		</tr>
		<?php } ?>		
	</tbody>
</table>
