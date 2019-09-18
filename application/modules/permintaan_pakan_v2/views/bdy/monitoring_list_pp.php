<div class="row">
 	<div class="container col-md-12">
 		<table class="table table-bordered custom_table monitoring_pp" id="tabelmonitoringpp">
 			<thead>
 				<tr>
 					<th colspan="3" class="ftl">Permintaan Kebutuhan Pakan</th>
 					<th colspan="3">Order Pembelian</th>
 					<th rowspan="3">No. Order Penjualan</th>
 					<th colspan="5">Delivery Order</th>
 					<th colspan="4">Surat Jalan</th>
 					<th colspan="5">Penerimaan di Gudang Farm</th>
 				</tr>
 				<tr>
 					<th rowspan="2" class="ftl">No. PP</th>
 					<th rowspan="2" class="ftl">Tanggal/Jam PP</th>
 					<th rowspan="2" class="ftl">Kuantitas</th>
 					<th rowspan="2">No. Order Pembelian</th>
 					<th rowspan="2">Tanggal/Jam OP</th>
 					<th rowspan="2">Kuantitas</th>
 					<th rowspan="2">No. DO</th>
 					<th rowspan="2">Ekspedisi</th>
 					<th rowspan="2">Target Tanggal Kirim</th>
 					<th rowspan="2">Kuantitas</th>
 					<th rowspan="2">Verifikasi DO</th>
 					<th rowspan="2">No. SJ</th>
 					<th rowspan="2">Tanggal/Jam SJ </th>
 					<th colspan="2">Kuantitas</th>
 					<th rowspan="2">Tanggal/Jam Terima</th>
 					<th colspan="2">Kuantitas</th>
 					<th rowspan="2">No. Berita Acara</th>
 				</tr>
 				<tr>
 					<th>Kg</th>
 					<th>Sak</th>
 					<th>Kg</th>
 					<th>Sak</th>
 				</tr>
 			</thead>
 			<tbody>
	 	<?php
	 	if(!empty($list_pp)){
	 		$total_penerimaan_sak = 0;
	 		$total_permintaan_sak = 0;
	 		$total_penerimaan_kg = 0;
			foreach($list_pp as $pp){
				echo '<tr>';
				$class_pp = !empty($pp['data']['ref_id']) ? 'kuning': '';
				echo '<td class="ftl '.$class_pp.' no_pp" rowspan="'.$pp['data']['rowspan'].'"><span data-flok="'.$pp['data']['flok_bdy'].'" data-no_pp="'.$pp['data']['no_pp'].'" data-status="A" class="link_span" onclick="Permintaan.detail_pp_popup(this)">'.$pp['data']['no_pp'].'</span></td>';
				echo '<td class="ftl tgl_jam" rowspan="'.$pp['data']['rowspan'].'">'.convertElemenTglWaktuIndonesia($pp['data']['tgl_pp']).'</td>';
				echo '<td class="ftl number" rowspan="'.$pp['data']['rowspan'].'">'.$pp['data']['kuantitas_pp'].'</td>';
				$j = 0;
				$i_terima = 0;
				foreach($pp['do'] as $do){
					if($j != 0){
						echo '<tr>';
					}
					echo '<td>'.$do['no_op_logistik'].'</td>';
					echo '<td class="tgl_jam">'.convertElemenTglWaktuIndonesia($do['tgl_op']).'</td>';
					echo '<td class="number">'.$do['kuantitas_op'].'</td>';
					echo '<td>'.$do['no_op'].'</td>';
					echo '<td>'.$do['no_do'].'</td>';
					echo '<td class="nama_do">'.$do['ekspedisi'].'</td>';
					echo '<td class="tgl_jam">'.convertElemenTglWaktuIndonesia($do['tgl_kirim']).'</td>';
					echo '<td class="number">'.$do['kuantitas_do'].'</td>';
					echo '<td class="tgl_jam">'.convertElemenTglWaktuIndonesia($do['tgl_verifikasi']).'</td>';
					echo '<td>'.$do['no_sj'].'</td>';
					echo '<td class="tgl_jam">'.convertElemenTglWaktuIndonesia($do['tgl_sj']).'</td>';
					echo '<td class="number">'.(!empty($do['kg_sj']) ? formatAngka($do['kg_sj'],3) : '').'</td>';
					echo '<td class="number">'.$do['sak_sj'].'</td>';

					/* tampilkan penerimaan jika ada*/
					$terima = $do['no_penerimaan'];
					if(!empty($terima) && empty($i_terima)){
						$d_terima = $pp['penerimaan'][$terima];
						$i_terima = $d_terima['rowspan'];

						echo '<td rowspan="'.$d_terima['rowspan'].'" class="tgl_jam">'.convertElemenTglWaktuIndonesia($d_terima['tgl_terima']).'</td>';
						echo '<td rowspan="'.$d_terima['rowspan'].'" class="number">'.formatAngka($d_terima['kg_terima'],3).'</td>';
						echo '<td rowspan="'.$d_terima['rowspan'].'" class="number">'.formatAngka($d_terima['sak_terima'],0).'</td>';
						$ba = !empty($d_terima['berita_acara']) ? '<span class="link_span" data-no_berita_acara="'.$d_terima['berita_acara'].'" onclick="Permintaan.view_berita_acara(this)">'.$d_terima['berita_acara'].'</span>':'';
						echo '<td rowspan="'.$d_terima['rowspan'].'">'.$ba.'</td>';
						$total_penerimaan_sak += $d_terima['sak_terima'];
						$total_penerimaan_kg += $d_terima['kg_terima'];
						$i_terima--;
					}
					else{
						if(!empty($i_terima)){
							$i_terima--;
						}
						else{
							echo '<td></td>';
							echo '<td></td>';
							echo '<td></td>';
							echo '<td></td>';
						}
					}
					if($j != 0){
						echo '</tr>';
					}
					$j++;

				}

				echo '</tr>';
				$total_permintaan_sak += $pp['data']['kuantitas_pp'];


			}

			/* untuk rekap total penerimaan */
			echo '<tr>
				<td colspan="2" class="number ftl"> Total Permintaan </td>
				<td class="number ftl">'.$total_permintaan_sak.'</td>
				<td colspan="14" class="number"> Total Penerimaan </td>
				<td class="number">'.formatAngka($total_penerimaan_kg,3).'</td>
				<td class="number">'.formatAngka($total_penerimaan_sak,0).'</td>
				<td></td>
			</tr>';

	 	}
	 	?>
	 		</tbody>
 		</table>
	</div>
</div>
