<table class="table table-bordered custom_table text-center">
	<thead>
			<tr>
				<th rowspan="2">Jam Terima</th>
				<th rowspan="2">No. SJ</th>
				<th rowspan="2">No. DO</th>
				<th rowspan="2">No. Ref</th>
				<th colspan="2">Terima</th>
				<th rowspan="2">Penerima</th>
			</tr>
			<tr>
				<th>Sak</th>
				<th>Kg</th>
			</tr>
		</thead>
		<tbody>
		<?php
	//	echo '<pre>';print_r($list_stok);
		if(!empty($list)){
			foreach($list as $l){
				echo '<tr>';
				$l['TGL_TERIMA'] = substr(convertElemenTglWaktuIndonesia($l['TGL_TERIMA']),-5);
				$l['BERAT_PUTAWAY'] = formatAngka($l['BERAT_PUTAWAY'],3);
				foreach($l as $td){
					echo '<td>'.$td.'</td>';
				}
				echo '</tr>';
			}
		}
		?>
		</tbody>
	</table>
