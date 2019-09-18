			<?php 
					if(!empty($list_pp)){
						echo '<table class="table fixed-header-pp">';
						echo '<thead>';
							echo '<tr>';
							echo '<th class="no_pp">No. PP</th>';					
							echo '<th>Tanggal <br /> Permintaan</th>';
							echo '<th>Tanggal <br /> Rilis</th>';
							echo '<th>Tanggal <br /> Approve1</th>';
							echo '<th>Tanggal <br /> Kirim</th>';
							echo '<th class="umur_pakan">Umur Pakan</th>';
							echo '<th class="kuantitas_pp">Kuantitas PP</th>';
							echo '<th>Status</th>';
							echo '<th>No. Ref. PP</th>';
							echo '<th class="aksi">Aksi</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($list_pp as $baris){
							echo '<tr>';
								$status_lpb = $baris['status_lpb'];
								$tgl_buat = tglIndonesia($baris['tgl_buat'], '-', ' ');
								$tgl_rilis = (!empty($baris['tgl_rilis'])) ? tglIndonesia($baris['tgl_rilis'], '-', ' ') : '';
								$tgl_approve1 = (!empty($baris['tgl_approve1'])) ? tglIndonesia($baris['tgl_approve1'], '-', ' ') : '';
							//	$baris['tgl_approve2'] = (!empty($baris['tgl_approve2'])) ? tglIndonesia($baris['tgl_approve2'], '-', ' ') : '';
								$tgl_kirim = '<div>'.implode('</div><div>',array_map('convertElemenTglIndonesia',explode(',',$baris['tgl_kirim']))).'</div>'; 
								$umur_pakan = '<div>'.implode('</div><div>',array_map('addAttr',explode(',',$baris['umur_pakan']))).'</div>';
							//	$umur_pakan = '<div>'.implode('</div><div>',explode(',',$baris['umur_pakan'])).'</div>';
								$ref_id = (!empty($baris['ref_id'])) ? '<span data-status="V" data-no_pp="'.$baris['ref_id'].'" class="link_span" onclick="Permintaan.transaksi_pp(this,\'#for_transaksi\')">'.$baris['ref_id'].'</span>' : '-';
								$status_lpb = convertKode('status_approve',$baris['status_lpb']);
								$tombol_aksi = (!empty($baris['ref_id'])) ? '<span class="btn btn-primary" data-ref_id="'.$baris['ref_id'].'" data-no_pp="'.$baris['no_lpb'].'" onclick="Permintaan.comparePP(this)">Compare</span>' : '';
							//	echo '<td>'.implode('</td><td>',$baris).'</td>';
								echo '<td class="no_pp">'.'<span data-status="'.$baris['status_lpb'].'" data-no_pp="'.$baris['no_lpb'].'" class="link_span" onclick="Permintaan.transaksi_pp(this,\'#for_transaksi\')">'.$baris['no_lpb'].'</span>'.'</td>';
								echo '<td>'.$tgl_buat.'</td>';
								echo '<td>'.$tgl_rilis.'</td>';
								echo '<td>'.$tgl_approve1.'</td>';
								echo '<td>'.$tgl_kirim.'</td>';
								
						//		dateDifference($baris['TGL_KIRIM'],$baris['TGL_KEB_AKHIR'])
								echo '<td class="umur_pakan">'.$umur_pakan.'</td>';
								echo '<td class="number kuantitas_pp">'.$baris['kuantitas_pp'].'</td>';
								echo '<td>'.$status_lpb.'</td>';
								echo '<td class="no_pp">'.$ref_id.'</td>';
								echo '<td class="aksi">'.$tombol_aksi.'</td>';
							echo '</tr>';
						}
						echo '<tbody>';
						echo '</table>';
					}	
					else{
						echo '<div>'.$pesan.'</div>';
					}		
				?>