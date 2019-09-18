<div class="col-md-12">
	<div><h4>Summary Stok Pakan</h4></div>
	<table class="table table-bordered custom_table stok_pakan" data-tgl_transaksi="<?php echo $tgl_transaksi ?>">
		<thead>
			<tr>
				<th colspan=2>Pakan</th>
				<th colspan=8>Gudang</th>
				<th colspan=10>Kandang</th>
			</tr>

			<tr>
				<th rowspan="2" class="nama_bentuk">Nama, Bentuk</th>
				<th rowspan="2">Kode</th>
				<th colspan="2">Stok Awal</th>
				<th colspan="2">Terima</th>
				<th colspan="2">Keluar</th>
				<th colspan="2">Stok Akhir</th>
				<th colspan="2">Stok Awal</th>
				<th colspan="2">Terima</th>
				<th colspan="2">Pakai</th>
				<th colspan="2">Stok Akhir</th>
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

		if(!empty($stok['result'])){
			$p_index = 0;
			$total_stok_awal_gudang_sak = 0;
			$total_gudang_terima_sak = 0;
			$total_gudang_keluar_sak = 0;
			$total_stok_akhir_gudang_sak = 0;

			$total_stok_awal_kandang_sak = 0;
			$total_kandang_terima_sak = 0;
			$total_kandang_pakai_sak = 0;
			$total_stok_akhir_kandang_sak = 0;

			$total_stok_awal_gudang_kg = 0;
			$total_gudang_terima_kg = 0;
			$total_gudang_keluar_kg = 0;
			$total_stok_akhir_gudang_kg = 0;

			$total_stok_awal_kandang_kg = 0;
			$total_kandang_terima_kg = 0;
			$total_kandang_pakai_kg = 0;
			$total_stok_akhir_kandang_kg = 0;

			$list_stok = $stok['result'];
			$d_kavling = $stok['d_kavling'];
			$d_kandang = $stok['d_kandang'];
			foreach($list_stok as $kb => $l){
				$t_kavling = array('
					<table class="table table-bordered">
					<thead>
						<tr>
							<th rowspan="2">Kavling</th>
							<th colspan="2">Stok Awal</th>
							<th colspan="2">Terima</th>
							<th colspan="2">Keluar</th>
							<th colspan="2">Stok Akhir</th>
							<th rowspan="2">Umur (hari)</th>
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
						</tr>
					</thead>
					'
				);
				$tbody = array('<tbody>');
				foreach($d_kavling[$kb] as $_k => $h){
					$tr = array(
						'<tr>',
						'<td>'.$_k.'</td>',
						'<td class="number">'.formatAngka($h['stok_awal_sak'],0).'</td>',
						'<td class="number">'.formatAngka($h['stok_awal_kg'],3).'</td>',
						'<td class="number">'.formatAngka($h['terima_sak'],0).'</td>',
						'<td class="number">'.formatAngka($h['terima_kg'],3).'</td>',
						'<td class="number">'.formatAngka($h['keluar_sak'],0).'</td>',
						'<td class="number">'.formatAngka($h['keluar_kg'],3).'</td>',
						'<td class="number">'.formatAngka($h['stok_akhir_sak'],0).'</td>',
						'<td class="number">'.formatAngka($h['stok_akhir_kg'],3).'</td>',
						'<td class="number">'.formatAngka($h['umur_pakan'],0).'</td>',
						'</tr>'
					);
					array_push($tbody,implode('',$tr));
				}
				array_push($tbody,'</tbody>');
				array_push($t_kavling,implode('',$tbody));
				array_push($t_kavling,'</table>');

				$t_kandang = array('
					<table class="table table-bordered">
					<thead>
					<tr>
						<th rowspan="2">Kandang</th>
						<th colspan="2">Stok Awal</th>
						<th colspan="2">Terima</th>
						<th colspan="2">Keluar</th>
						<th colspan="2">Stok Akhir</th>
						<th rowspan="2">Umur (hari)</th>
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
					</tr>
					</thead>
					'
				);
				$tbody_k = array('<tbody>');

				foreach($d_kandang[$kb] as $_k => $h){
					$tr = array(
						'<tr>',
						'<td>'.$_k.'</td>',
						'<td class="number">'.implode('</td><td class="number">',$h).'</td>',
						'</tr>'
					);
					array_push($tbody_k,implode('',$tr));
				}
				array_push($tbody_k,'</tbody>');
				array_push($t_kandang,implode('',$tbody_k));
				array_push($t_kandang,'</table>');


				echo '<tr>';
				echo '<td><span class="glyphicon glyphicon-plus plus_sign" data-kodebarang="'.$kb.'" onclick="StokPakan.detail_pakan_kavling_kandang(this)"></span>'.$l['nama_barang'].'</td>';
				echo '<td>'.$kb.'</td>';
				echo '<td class="number">'.formatAngka($l['gudang_awal_sak'],0).'</td>';
				echo '<td class="number">'.formatAngka($l['gudang_awal_kg'],3).'</td>';
				echo '<td class="number">'.formatAngka($l['gudang_terima_sak'],0).'</td>';
				echo '<td class="number">'.formatAngka($l['gudang_terima_kg'],3).'</td>';
				echo '<td class="number">'.formatAngka($l['gudang_keluar_sak'],0).'</td>';
				echo '<td class="number">'.formatAngka($l['gudang_keluar_kg'],3).'</td>';
				echo '<td class="number">'.formatAngka($l['gudang_akhir_sak'],0).'</td>';
				echo '<td class="number">'.formatAngka($l['gudang_akhir_kg'],3).'</td>';
				echo '<td class="number">'.formatAngka($l['kandang_awal_sak'],0).'</td>';
				echo '<td class="number">'.formatAngka($l['kandang_awal_kg'],3).'</td>';
				echo '<td class="number">'.formatAngka($l['kandang_terima_sak'],0).'</td>';
				echo '<td class="number">'.formatAngka($l['kandang_terima_kg'],3).'</td>';
				echo '<td class="number">'.formatAngka($l['kandang_pakai_sak'],0).'</td>';
				echo '<td class="number">'.formatAngka($l['kandang_pakai_kg'],3).'</td>';
				echo '<td class="number">'.formatAngka($l['kandang_akhir_sak'],0).'</td>';
				echo '<td class="number">'.formatAngka($l['kandang_akhir_kg'],3).'</td>';
				echo '</tr>';
				echo '<tr class="detail" style="display:none">
					<td colspan=2></td>
					<td colspan=8>'.implode('',$t_kavling).'</td>
					<td colspan=8>'.implode('',$t_kandang).'</td>
				</tr>';
				$total_stok_awal_gudang_sak += $l['gudang_awal_sak'];
				$total_gudang_terima_sak += $l['gudang_terima_sak'];
				$total_gudang_keluar_sak += $l['gudang_keluar_sak'];
				$total_stok_akhir_gudang_sak += $l['gudang_akhir_sak'];

				$total_stok_awal_kandang_sak += $l['kandang_awal_sak'];
				$total_kandang_terima_sak += $l['kandang_terima_sak'];
				$total_kandang_pakai_sak += $l['kandang_pakai_sak'];
				$total_stok_akhir_kandang_sak += $l['kandang_akhir_sak'];

				$total_stok_awal_gudang_kg += $l['gudang_awal_kg'];
				$total_gudang_terima_kg += $l['gudang_terima_kg'];
				$total_gudang_keluar_kg += $l['gudang_keluar_kg'];
				$total_stok_akhir_gudang_kg += $l['gudang_akhir_kg'];

				$total_stok_awal_kandang_kg += $l['kandang_awal_kg'];
				$total_kandang_terima_kg += $l['kandang_terima_kg'];
				$total_kandang_pakai_kg += $l['kandang_pakai_kg'];
				$total_stok_akhir_kandang_kg += $l['kandang_akhir_kg'];
			}

			echo '<tr class="number">
				<td colspan=2>Total</td>
				<td>'.formatAngka($total_stok_awal_gudang_sak,0).'</td>
				<td>'.formatAngka($total_stok_awal_gudang_kg,3).'</td>
				<td>'.formatAngka($total_gudang_terima_sak,0).'</td>
				<td>'.formatAngka($total_gudang_terima_kg,3).'</td>
				<td>'.formatAngka($total_gudang_keluar_sak,0).'</td>
				<td>'.formatAngka($total_gudang_keluar_kg,3).'</td>
				<td>'.formatAngka($total_stok_akhir_gudang_sak,0).'</td>
				<td>'.formatAngka($total_stok_akhir_gudang_kg,3).'</td>
				<td>'.formatAngka($total_stok_awal_kandang_sak,0).'</td>
				<td>'.formatAngka($total_stok_awal_kandang_kg,3).'</td>
				<td>'.formatAngka($total_kandang_terima_sak,0).'</td>
				<td>'.formatAngka($total_kandang_terima_kg,3).'</td>
				<td>'.formatAngka($total_kandang_pakai_sak,0).'</td>
				<td>'.formatAngka($total_kandang_pakai_kg,3).'</td>
				<td>'.formatAngka($total_stok_akhir_kandang_sak,0).'</td>
				<td>'.formatAngka($total_stok_akhir_kandang_kg,3).'</td>
			</tr>';
		}
		?>
		</tbody>
	</table>
</div>
