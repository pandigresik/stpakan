<div class="">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th colspan="2">Detail Kandang</th>
				</tr>
				<tr>
					<th class="text-center">Kandang</th>
					<th class="text-center">Jumlah Pemakaian (Sak)</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if(!empty($detail_kandang)){
						foreach($detail_kandang as $dk){
							echo '<tr>
								<td class="text-center">Kandang '.$dk->KODE_KANDANG.'</td>
								<td class="text-center">'.$dk->jml_akhir.'</td>
							</tr>';
						}
					}
				?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
</div>
