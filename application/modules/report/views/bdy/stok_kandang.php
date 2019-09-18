<div class="col-md-12">
	<div><h4>Stok Pakan di Kandang</h4></div>
	<table class="table table-bordered custom_table stok_pakan pakan_kandang" data-tgl_transaksi="<?php echo $tgl_transaksi ?>">
		<thead>
			<tr>
				<th rowspan=4>Kandang</th>
				<th colspan="2" rowspan="2">Umur Ayam</th>
				<th rowspan="2" colspan=2>Pakan</th>
				<th colspan=13>Kandang</th>
			</tr>
			<tr>
				<th colspan="2" rowspan="2">Stok Awal</th>
				<th colspan="3">Terima Pakan</th>
				<th colspan="2" rowspan="2">Pakai</th>
				<th rowspan="2">Retur Sak Kosong</th>
				<th rowspan="2">Sisa Hutang Sak Kosong</th>
				<th colspan="2" rowspan="2">Stok Akhir</th>
				<th rowspan="3">Umur Pakan (hari)</th>
			</tr>
			<tr>
				<th rowspan=2>Minggu</th>
				<th rowspan=2>Hari</th>
				<th rowspan=2 class="nama_bentuk">Nama, Bentuk</th>
				<th rowspan=2>Kode</th>
				<th rowspan=2>Asal Kavling</th>
				<th colspan=2>Jumlah</th>
				<!--th colspan=2>Pakai</th>
				<th rowspan=2>Retur Sak Kosong</th>
				<!--th rowspan=2>Hutang Retur Sak Kosong</th>
				<th rowspan=2>Sisa Hutang Sak Kosong</th>
				<th colspan=2>Stok Akhir</th>
				<th rowspan=2>Umur Pakan (hari)</th-->
			</tr>
			<tr>
				<th>Sak</th>
				<th>Kg</th>
				<th>Sak</th>
				<th>Kg</th>
				<th>Sak</th>
				<th>Kg</th>
				<th>Sak</th>
				<th>Sak</th>
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
			$retur = $stok['retur'];
			$p_index = 0;
			$total_stok_awal_kandang_sak = 0;
			$total_kandang_terima_sak = 0;
			$total_kandang_pakai_sak = 0;
			$total_stok_akhir_sak = 0;
			$total_kandang_terima_sak = 0;

			$total_kandang_pakai_kg = 0;
			$total_stok_awal_kandang_kg = 0;
			$total_kandang_terima_kg = 0;
			$total_stok_akhir_kg = 0;
			$total_kandang_terima_kg = 0;

			$retur_sak = 0;
			$hutang_retur_sak = 0;
			$pelunasan_retur_sak = 0;
			$i = 0;
			foreach ($list_stok as $kv => $l) {
				/* kandang */
				$rowspan_kv = $rowspan[$kv]['rowspan'];
				echo '<tr>';
				echo '<td rowspan="'.$rowspan_kv.'">'.$kv.'</td>';

				foreach($l['data'] as $depan){
					echo '<td rowspan="'.$rowspan_kv.'">'.$depan.'</td>';
				}
				$j = 0;
				foreach ($l['detail'] as $kb => $pakan) {
					/* pakan */
					if($j > 0){
							echo '<tr>';
						}
					$k = 0;
					if($k == 0){
						$rowspan_pakan = $rowspan[$kv]['detail'][$kb]['rowspan'];
						echo '<td rowspan="'.$rowspan_pakan.'">'.$pakan['nama'].'</td>';
						echo '<td rowspan="'.$rowspan_pakan.'">'.$kb.'</td>';
						echo '<td class="number" rowspan="'.$rowspan_pakan.'">'.$pakan['stok_awal_sak'].'</td>';
						echo '<td class="number" rowspan="'.$rowspan_pakan.'">'.formatAngka($pakan['stok_awal_kg'],3).'</td>';
					}


					foreach($pakan['detail'] as $d){
						/* kavling */
						if($k > 0){
							echo '<tr>';
						}
						$class_kavling = ($d['mutasi'] == 1) ? 'abang mutasi' : '';
						$hasTooltip = ($d['mutasi'] == 1) ? '<span class="has-tooltip_bdy">'.$d['no_kavling'].'<span>' : $d['no_kavling'];
						echo '<td class="number '.$class_kavling.'" data-noref="'.$d['no_referensi'].'">'.$hasTooltip.'</td>';
						echo '<td class="number">'.$d['terima_sak'].'</td>';
						echo '<td class="number">'.formatAngka($d['terima_kg'],3).'</td>';
						if($k == 0){
							echo '<td class="number" rowspan="'.$rowspan_pakan.'">'.formatAngka($pakan['pakai_sak'],0).'</td>';
							echo '<td class="number" rowspan="'.$rowspan_pakan.'">'.formatAngka($pakan['pakai_kg'],3).'</td>';
						}
						if($j == 0 && $k == 0){
							foreach($retur[$kv] as $z => $belakang){
								switch($z){
									case 'retur_sak':
												$retur_sak += $belakang;
												$belakang = '<span class="link_span" data-noreg="'.$retur[$kv]['noreg'].'" data-kandang="'.$kv.'" onclick="StokPakan.show_retur_sak(this)">'.$belakang.'</span>';
												$class_retur = '';
												echo '<td class="number '.$class_retur.' '.$z.'" rowspan="'.$rowspan_kv.'">'.$belakang.'</td>';
												break;
									case 'hutang_sak':
												$hutang_retur_sak += $belakang;
												$class_retur = $belakang > 0 ? 'bg_orange' : '';
												echo '<td class="number '.$class_retur.' '.$z.'" rowspan="'.$rowspan_kv.'">'.$belakang.'</td>';
												break;
									case 'pelunasan_retur':
												$pelunasan_retur_sak += $belakang;
												$class_retur = $belakang > 0 ? 'abang' : '';
												//echo '<td class="number '.$class_retur.' '.$z.'" rowspan="'.$rowspan_kv.'">'.$belakang.'</td>';
												break;
									default:
								}
						}
					}
		//			echo '<td class="number">'.$d['stok_akhir'].'</td>';
		//			echo '<td class="number'.($d['umur_pakan_kandang'] > $standart_umur_pakan ? 'abang' : '').'">'.$d['umur_pakan_kandang'].'</td>';

					if($k == 0){
							echo '<td class="number" rowspan="'.$rowspan_pakan.'">'.($pakan['stok_pakan_sak'] - $pakan['pakai_sak']).'</td>';
							echo '<td class="number" rowspan="'.$rowspan_pakan.'">'.formatAngka(($pakan['stok_pakan_kg'] - $pakan['pakai_kg']),3).'</td>';
							echo '<td class="number"'.($pakan['umur_pakan_kandang'] > $standart_umur_pakan ? 'abang' : '').' rowspan="'.$rowspan_pakan.'">'.$pakan['umur_pakan_kandang'].'</td>';
							$total_stok_awal_kandang_sak += $pakan['stok_awal_sak'];
							$total_kandang_terima_sak += $pakan['terima_sak'];
							$total_stok_akhir_sak += ($pakan['stok_pakan_sak'] - $pakan['pakai_sak']);
							$total_kandang_pakai_sak += $pakan['pakai_sak'];

							$total_stok_awal_kandang_kg += $pakan['stok_awal_kg'];
							$total_kandang_terima_kg += $pakan['terima_kg'];
							$total_stok_akhir_kg += ($pakan['stok_pakan_kg'] - $pakan['pakai_kg']);
							$total_kandang_pakai_kg += $pakan['pakai_kg'];

					}
					if($k > 0){
						echo '</tr>';
					}
					$k++;
				}
				if($j > 0){
					echo '</tr>';
				}
				$j++;
			}

			$i++;
		}



			echo '<tr class="number">
				<td colspan=5>Total</td>
				<td>'.formatAngka($total_stok_awal_kandang_sak,0).'</td>
				<td>'.formatAngka($total_stok_awal_kandang_kg,3).'</td>
				<td></td>
				<td>'.formatAngka($total_kandang_terima_sak,0).'</td>
				<td>'.formatAngka($total_kandang_terima_kg,3).'</td>
				<td>'.formatAngka($total_kandang_pakai_sak,0).'</td>
				<td>'.formatAngka($total_kandang_pakai_kg,3).'</td>
				<td>'.formatAngka($retur_sak,0).'</td>
				<td>'.formatAngka($hutang_retur_sak,0).'</td>
				<!--td>'.formatAngka($pelunasan_retur_sak,0).'</td-->
				<td>'.formatAngka($total_stok_akhir_sak,0).'</td>
				<td>'.formatAngka($total_stok_akhir_kg,3).'</td>
				<td></td>
			</tr>';
		}
		?>
		</tbody>
	</table>
</div>
