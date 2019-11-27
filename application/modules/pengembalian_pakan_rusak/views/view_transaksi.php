<table data-no_reg=<?php echo $no_reg ?> id="tabel_detail_pengembalian_pakan_rusak" class="table table-bordered">
	<thead>
		<tr>
			<th>Nama Pakan</th>
			<th>Jumlah Retur</th>
			<th>Jumlah Stok Akhir</th>
		</tr>
	</thead>
	<tbody>

		<?php
		foreach($perpakan as $kodepj => $jkpakan){
			foreach($jkpakan as $jk =>$pakan){
				$header = $pakan['header'];
				echo '<tr>
						<td>'.$header['NAMA_BARANG'] .' - '.$header['NAMA_BARANG'].' - ('.$jk.')'.'</td>
						<td class="number">'.$header['JML_RETUR'].'</td>
						<td class="number">'.$header['JML_STOK'].'</td>

					</tr>';
				echo '<tr>
						<td colspan="8">
						<table class="table pull-right" style="max-width:50%">
							<thead>
								<tr>
									<th>Timbangan (Kg) </th>
									<th>Keterangan </th>
									<th></th>
								</tr>
							</thead>
						<tbody>';
				foreach($pakan['detail'] as $timbang){
					echo '
							<tr>
								<td>
									<input type="text" readonly class="number" data-field="Jumlah pengembalian" value="'.$timbang['BRT_SAK'].'" name="jml_pengembalian" />
								</td>
								<td>
									<input type="text" readonly data-field="Berat pengembalian" value="'.$timbang['KETERANGAN'].'" name="brt_pengembalian" />
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
		<tr>
			<td colspan = "8">
				<div class="col-md-12">
					<div class="col-md-12 ">
						<div class="form-group form-horizontal">
					        <div class="form-inline new-line">
					            <label class="col-md-2" for="tanggal-kirim">Lampiran</label>
					            <div class="form-group">
					                <a href="review_pakan_rusak/review/download?no_retur=<?php echo $no_pengembalian ?>" >RP/<?php echo $no_pengembalian.'.doc'?></a>
					            </div>

					        </div>
			        		<div id='format-file'></div>
			        	</div>
					</div>
				</div>
		    </td>
		</tr>
	</tfoot>
</table>
