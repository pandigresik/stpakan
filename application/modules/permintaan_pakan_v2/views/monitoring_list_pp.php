<div class="row">
 	<div class="container col-md-12">
 		<table class="table table-bordered custom_table monitoring_pp">
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
				echo '<td class="ftl '.$class_pp.' no_pp" rowspan="'.$pp['data']['rowspan'].'"><span data-no_pp="'.$pp['data']['no_pp'].'" data-status="A" class="link_span" onclick="Permintaan.detail_pp_popup(this)">'.$pp['data']['no_pp'].'</span></td>';
				echo '<td class="ftl tgl_jam" rowspan="'.$pp['data']['rowspan'].'">'.convertElemenTglWaktuIndonesia($pp['data']['tgl_pp']).'</td>';
				echo '<td class="ftl number" rowspan="'.$pp['data']['rowspan'].'">'.$pp['data']['kuantitas_pp'].'</td>';
				$i = 0;
				foreach($pp['op'] as $op){
				if($i != 0){
					echo '<tr>';
				}
					echo '<td rowspan="'.$op['data']['rowspan'].'">'.$op['data']['no_op_logistik'].'</td>';
					echo '<td class="tgl_jam" rowspan="'.$op['data']['rowspan'].'">'.convertElemenTglWaktuIndonesia($op['data']['tgl_op']).'</td>';
					echo '<td class="number" rowspan="'.$op['data']['rowspan'].'">'.$op['data']['kuantitas_op'].'</td>';
					echo '<td rowspan="'.$op['data']['rowspan'].'">'.$op['data']['no_op'].'</td>';
				

					$j = 0;
					foreach($op['do'] as $do){
						if($j != 0){
							echo '<tr>';
						}
						
						echo '<td>'.$do['no_do'].'</td>';
						echo '<td class="nama_do">'.$do['ekspedisi'].'</td>';
						echo '<td class="tgl_jam">'.convertElemenTglWaktuIndonesia($do['tgl_kirim']).'</td>';
						echo '<td class="number">'.$do['kuantitas_do'].'</td>';
						echo '<td class="tgl_jam">'.convertElemenTglWaktuIndonesia($do['tgl_verifikasi']).'</td>';
						echo '<td>'.$do['no_sj'].'</td>';
						echo '<td class="tgl_jam">'.convertElemenTglWaktuIndonesia($do['tgl_sj']).'</td>';
						echo '<td class="number">'.$do['kg_sj'].'</td>';
						echo '<td class="number">'.$do['sak_sj'].'</td>';
						
						echo '<td class="tgl_jam">'.convertElemenTglWaktuIndonesia($do['tgl_terima']).'</td>';
						echo '<td class="number">'.formatAngka($do['kg_terima'],3).'</td>';
						echo '<td class="number">'.formatAngka($do['sak_terima'],0).'</td>';
						$ba = !empty($do['berita_acara']) ? '<span class="link_span" data-no_berita_acara="'.$do['berita_acara'].'" onclick="Permintaan.view_berita_acara(this)">'.$do['berita_acara'].'</span>':'';
						echo '<td>'.$ba.'</td>';
						
						$total_penerimaan_sak += $do['sak_terima'];
						$total_penerimaan_kg += $do['kg_terima'];
						if($j != 0){
							echo '</tr>';
						}
						$j++;
						
					}
					if($i != 0 ){
						echo '</tr>';
					}
					$i++;
				}
				echo '</tr>';
				if($total_permintaan){
					$total_permintaan_sak += $pp['data']['kuantitas_pp'];
				}
				
			}
			
			/* untuk rekap total penerimaan */
			if($total_permintaan){
				echo '<tr>
					<td colspan="2" class="number ftl"> Total Permintaan </td>
					<td class="number ftl">'.$total_permintaan_sak.'</td>
					<td colspan="14" class="number"> Total Penerimaan </td>
					<td class="number">'.formatAngka($total_penerimaan_kg,3).'</td>
					<td class="number">'.formatAngka($total_penerimaan_sak,0).'</td>
					<td></td>
				</tr>';
			}
			else{
				echo '<tr>
					<td colspan="17" class="number"> Total Penerimaan </td>
					<td class="number">'.formatAngka($total_penerimaan_kg,3).'</td>
					<td class="number">'.formatAngka($total_penerimaan_sak,0).'</td>
					<td></td>
				</tr>';				
			}
	 	}
	 	?>
	 		</tbody>
 		</table>
	</div>		
</div>
	


