<table id="tabel_detail_pengembalian_sak" class="table table-bordered">
	<thead>
		<tr>
			<th>Nama Pakan</th>
			<th>Jumlah Aktual (Sak)</th>
			<th>Keterangan</th>

		</tr>
	</thead>
	<tbody>
		<?php foreach($perpakan as $kodepj => $jkpakan){
				foreach($jkpakan as $jk =>$pakan){
					$header = $pakan['header'];
					echo '<tr>
						<td>'.$header['NAMA_BARANG'].'</td>
						<td class="number">'.formatAngka($header['JML_SAK'],0).'</td>
						<td class="keterangan">'.$header['KETERANGAN'].'</td>

					</tr>';
					echo '<tr>
						<td colspan="8">
						<table class="table pull-right" style="max-width:50%">
							<thead>
								<tr>
									<th>Jumlah Pengembalian (Sak) </th>
									<th>Berat Sak (Gr) </th>
									<th></th>
								</tr>
							</thead>
						<tbody>';
					foreach($pakan['detail'] as $timbang){
						echo '
							<tr>
								<td>
									<input type="text" readonly class="number" data-field="Jumlah pengembalian" value="'.formatAngka($timbang['JML_SAK'],0).'" name="jml_pengembalian" maxlength="3" />
								</td>
								<td>
									<input type="text" readonly class="number" data-field="Berat pengembalian" value="'.formatAngka($timbang['BRT_SAK'],0).'" name="brt_pengembalian" />
								</td>
								<td>

								</td>
							</tr>';

					}
					echo '
						</tbody>
					</table>
					</td>
					</tr>';
				}

		}
		 ?>
	</tbody>
	<tfoot>
	</tfoot>
</table>
