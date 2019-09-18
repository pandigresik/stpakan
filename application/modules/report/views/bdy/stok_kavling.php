<div class="col-md-12">
	<div><h4>Stok Pakan di Kavling</h4></div>
	<table class="table table-bordered custom_table stok_pakan pakan_kavling" data-tgl_transaksi="<?php echo $tgl_transaksi ?>">
		<thead>
			<tr>
				<th rowspan=3>Kavling</th>
				<th colspan=2>Pakan</th>
				<th colspan=9>Gudang</th>
				<th rowspan=3>Kandang</th>
				<th colspan=2>Umur Ayam</th>
				<th rowspan=2  colspan=2>Terima</th>
			</tr>
			<tr>
				<th rowspan=2 class="nama_bentuk">Nama, Bentuk</th>
				<th rowspan=2 >Kode</th>
				<th colspan=2>Stok Awal</th>
				<th colspan=2>Terima</th>
				<th colspan=2>Keluar</th>
				<th colspan=2>Stok Akhir</th>
				<th rowspan=2>Umur Pakan (Hari)</th>

				<th rowspan=2>Minggu</th>
				<th rowspan=2>Hari</th>
			</tr>
			<tr>
				<th>Sak</th>
				<th>Kg</th>
				<th>Sak</th>
				<th>Kg</th>
				<th>Sak</th>
				<th>Kg</th>
				<th>Sak</th>
				<th>Kg</th>
				<th>Sak</th>
				<th>Kg</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$standart_umur_pakan = 5;
		$p_index = 1;
		if(!empty($stok['data'])){
			$list_stok = $stok['data'];
			$rowspan = $stok['rowspan'];
			$p_index = 0;
			$total_stok_awal_gudang_sak = 0;
			$total_gudang_terima_sak = 0;
			$total_gudang_keluar_sak = 0;
			$total_stok_akhir_sak = 0;
			$total_kandang_terima_sak = 0;

			$total_stok_awal_gudang_kg = 0;
			$total_gudang_terima_kg = 0;
			$total_gudang_keluar_kg = 0;
			$total_stok_akhir_kg = 0;
			$total_kandang_terima_kg = 0;
			foreach($list_stok as $kv => $l){
				$p_index++;
				$rowspan_kv = $rowspan[$kv]['rowspan'];
				echo '<tr>';
				echo '<td class="parent_'.$p_index.'" data-rowspan_asli="'.$rowspan_kv.'" rowspan="'.$rowspan_kv.'">'.$kv.'</td>';
				$i = 0;
				foreach($l as $kd => $d){
					$rowspan_pakan = $rowspan[$kv][$kd]['rowspan'];
					if($i > 0){
							echo '<tr>';
						}
						$detail_terima = $d['data']['gudang_terima_sak'] > 0 ? '<span class="glyphicon glyphicon-plus plus_sign"  data-tglterima="'.$tgl_transaksi.'" data-kodebarang="'.$kd.'" data-parent="parent_'.$p_index.'" data-kodefarm="'.$kode_farm.'" data-nokavling="'.$kv.'" onclick="StokPakan.detail_terima_pakan_bdy(this)"></span>' : '';
						$classTerimaSak = '';
						if(empty($detail_terima)){
							$classTerimaSak = 'terima_sak';
						}
						echo '<td rowspan="'.$rowspan_pakan.'">'.$d['data']['nama_barang'].'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'">'.$kd.'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'" class="number">'.$d['data']['stok_awal_sak'].'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'" class="number">'.formatAngka($d['data']['stok_awal_kg'],3).'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'" class="number '.$classTerimaSak.'">'.$detail_terima.' '.$d['data']['gudang_terima_sak'].'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'" class="number">'.formatAngka($d['data']['gudang_terima_kg'],3).'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'" class="number">'.$d['data']['gudang_keluar_sak'].'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'" class="number">'.formatAngka($d['data']['gudang_keluar_kg'],3).'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'" class="number">'.$d['data']['stok_akhir_sak'].'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'" class="number">'.formatAngka($d['data']['stok_akhir_kg'],3).'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'" class="number '.($d['data']['umur_pakan_gudang'] > $standart_umur_pakan ? 'abang' : '').'">'.$d['data']['umur_pakan_gudang'].'</td>';
						$total_stok_awal_gudang_sak += $d['data']['stok_awal_sak'];
						$total_gudang_terima_sak += $d['data']['gudang_terima_sak'];
						$total_stok_akhir_sak += $d['data']['stok_akhir_sak'];
						$total_gudang_keluar_sak += $d['data']['gudang_keluar_sak'];

						$total_stok_awal_gudang_kg += $d['data']['stok_awal_kg'];
						$total_gudang_terima_kg += $d['data']['gudang_terima_kg'];
						$total_stok_akhir_kg += $d['data']['stok_akhir_kg'];
						$total_gudang_keluar_kg += $d['data']['gudang_keluar_kg'];
						$j = 0;
					foreach($d['detail'] as $pj){
						if($j > 0){
							echo '<tr>';
						}
		//					$retur = '<span class="link_span" data-noreg="'.$pj['noreg'].'" data-kandang="'.$pj['kandang'].'" onclick="StokPakan.show_retur_sak(this)">'.$pj['retur'].'</span>';
							$class_kandang = ($pj['mutasi'] == 1) ? 'abang mutasi' : '';
							$hasTooltip = ($pj['mutasi'] == 1) ? '<span class="has-tooltip_bdy">'.$pj['kandang'].'<span>' : $pj['kandang'];
							echo '<td class="text-center kavling_kandang '.$class_kandang.'" data-noref="'.$pj['no_referensi'].'" data-kode_barang="'.$kd.'" data-kavling="'.$kv.'">'.$hasTooltip.'</td>';
							echo '<td class="text-center">'.$pj['minggu'].'</td>';
							echo '<td class="text-center">'.$pj['hari'].'</td>';
							echo '<td class="number">'.$pj['kandang_terima_sak'].'</td>';
							echo '<td class="number">'.formatAngka($pj['kandang_terima_kg'],3).'</td>';
						if($j > 0){
							echo '</tr>';
						}
						$total_kandang_terima_sak += $pj['kandang_terima_sak'];
						$total_kandang_terima_kg += $pj['kandang_terima_kg'];
						$j++;
					}
					if($i > 0){
						echo '</tr>';
					}
					$i++;
				}
				echo '</tr>';
			}
			$class_total_terima_kandang = $total_gudang_keluar_sak != $total_kandang_terima_sak ?'abang' : '';
			echo '<tr class="number">
				<td colspan=3>Total</td>
				<td>'.$total_stok_awal_gudang_sak.'</td>
				<td>'.formatAngka($total_stok_awal_gudang_kg,3).'</td>
				<td>'.$total_gudang_terima_sak.'</td>
				<td>'.formatAngka($total_gudang_terima_kg,3).'</td>
				<td>'.$total_gudang_keluar_sak.'</td>
				<td>'.formatAngka($total_gudang_keluar_kg,3).'</td>
				<td>'.$total_stok_akhir_sak.'</td>
				<td>'.formatAngka($total_stok_akhir_kg,3).'</td>
				<td colspan=4></td>
				<td class="'.$class_total_terima_kandang.'">'.$total_kandang_terima_sak.'</td>
				<td class="'.$class_total_terima_kandang.'">'.formatAngka($total_kandang_terima_kg,3).'</td>
			</tr>';
		}
		?>
		</tbody>
	</table>
</div>
