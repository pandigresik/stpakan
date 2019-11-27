<div class="table-responsive">
<table class="table table-bordered custom_table">
		<thead>
			<tr>
				<th>Kandang</th>
				<th>Populasi (ekor)</th>
				<th>Daya Hidup (%)</th>
				<th>Berat Badan (g)</th>
				<th>FCR</th>
				<th>Umur Panen</th>
				<th>IP</th>
			</tr>
		</thead>
	<tbody>
		<?php foreach($list as $i => $val){ ?>
		<tr>

			<?php
				echo '<tr>
					<td> Kandang '.$val['kode_kandang'].'</td>
					<td class="number">'.angkaRibuan($val['populasi']).'</td>
					<td class="number">'.formatAngka($val['dayahidup'] * 100,2).'</td>
					<td class="number">'.formatAngka($val['beratbadan'],3).'</td>
					<td class="number">'.formatAngka($val['fcr'],3).'</td>
					<td class="number">'.$val['umurpanen'].'</td>
					<td class="number">'.formatAngka($val['ip'],0).'</td>

				</tr>'
			?>
		</tr>
		<?php } ?>
	</tbody>
</table>
</div>