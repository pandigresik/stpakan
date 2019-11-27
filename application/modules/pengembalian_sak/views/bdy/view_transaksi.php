<table id="tabel_detail_pengembalian_sak" class="table table-bordered custom_table">
	<thead>
		<tr>
			<th>Nama Pakan</th>
			<th>Target Pengembalian (Sak)</th>
			<th>Berat Sak (Gr)</th>
			<th>Jumlah Pengembalian (Sak)</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($perpakan as $kodepj => $jkpakan){
				foreach($jkpakan as $jk =>$pakan){
					$header = $pakan['header'];
					echo '<tr>
						<td>'.$header['NAMA_BARANG'].'</td>';
						foreach($pakan['detail'] as $timbang){
							echo '
								<td>'.formatAngka($timbang['JML_SAK'],0).'</td>
								<td>'.formatAngka($timbang['BRT_SAK'],0).'</td>
								<td>'.formatAngka($timbang['JML_SAK'],0).'</td>';																
						}						
					echo '</tr>';
					
				}

		}
		 ?>
	</tbody>
	<tfoot>
	</tfoot>
</table>
