<table data-no_reg=<?php echo $no_reg ?> id="tabel_detail_pengembalian_sak" class="table table-bordered">
	<thead>
		<tr>
			<th>Nama Pakan</th>
			<th>Jenis Kelamin</th>
			<th>Jumlah Kirim</th>
			<th>Jumlah Pakai</th>
			<th>Target Pengembalian (Sak)</th>
			<th>Jumlah Aktual (Sak)</th>
			<th>Outstanding Pengembalian (Sak)</th>
		</tr>
	</thead>
	<tbody>
	
		<?php  
		foreach($list_pakan as $pakan){
			$kode_barang = $pakan['kode_barang'];
			$jk = $pakan['jenis_kelamin'];
			$retur_sak = !empty($sak_kembali[$kode_barang][$jk]) ? $sak_kembali[$kode_barang][$jk] : 0;
			$outstanding = $pakan['jml_pakai'] - $retur_sak;
			echo '<tr class="'.$pakan['kode_barang'].'" data-jenis_kelamin='.$pakan['jenis_kelamin'].' onclick="Pengembalian.show_detail_timbang(this)">
				<td data-kode_barang='.$pakan['kode_barang'].'>'.$pakan['nama_barang'].'</td>
				<td class="text-center">'.$pakan['jenis_kelamin'].'</td>
				<td class="number jml_kirim">'.$pakan_dikirim[$kode_barang][$jk].'</td>
				<td class="number jml_pakai">'.$pakan['jml_pakai'].'</td>		
				<td class="number">'.$pakan['jml_pakai'].'</td>
				<td class="number jml_aktual">'.$retur_sak.'</td>
				<td class="number outstanding_sak">'.$outstanding.'</td>
					
			</tr>
			<tr class="detail_timbang" style="display:none">';
			if($outstanding > 0){			
			echo '<td colspan="8">
					<table class="table pull-right" style="max-width:50%">
						<thead>
							<tr>
								<th>Jumlah Pengembalian (Sak) </th>
								<th>Berat Sak (Gr) </th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr data-kode_barang='.$pakan['kode_barang'].' data-jenis_kelamin='.$pakan['jenis_kelamin'].'>
								<td>
									<input type="text" class="number" data-field="Jumlah pengembalian" value=0 name="jml_pengembalian" maxlength="3" />
								</td>
								<td>
									<input type="text" class="number" data-field="Berat pengembalian" value=0 name="brt_pengembalian" />
								</td>
								<td>
									<span class="btn btn-default" onclick="Pengembalian.timbang_lagi(this)">Selesai</span>
								</td>
							</tr>
						</tbody>
					</table>	
				</td>';
			}
			echo '</tr>';
		}
		 ?>
	</tbody>
	<tfoot>
	</tfoot>
</table>