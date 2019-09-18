			<?php
				if(!empty($list_pp)){
					//echo '<div class="table-responsive">';
						echo '<table class="table header_center">';
						echo '<thead>';
							echo '<tr>';
							echo '<th></th>';
							echo '<th class=" no_pp">No. PP</th>';
							echo '<th class="" >Farm</th>';
							echo '<th class="">Flock</th>';
							echo '<th class="">Kandang</th>';							
							echo '<th>Tanggal <br /> Kirim</th>';
							echo '<th class="tgl_kebutuhan">Tanggal <br /> Kebutuhan</th>';
							echo '<th>Umur Pakan</th>';							
							echo '<th class="data_pp">Rekomendasi PP <br /> ( Sak )</th>';
							echo '<th class="data_pp">Pengajuan <br />Kepala Farm<br />  ( Sak )</th>';
							echo '<th class="data_pp">Pengajuan <br />Kepala Departemen <br /> ( Sak )</th>';
							echo '<th>Aksi <br /><input type="checkbox" onchange="Permintaan.checkAllkadiv(this)"></th>';
							echo '<th class="keterangan">Keterangan</th>';							
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						
						foreach($list_pp as $baris){
							/* jika belum diaktifkan untuk versi baru maka bypass aja */
							if(!$versi_baru[$kode_list_farm[$baris['nama_farm']]]){
								continue;
							}
							/** buat keterangan */
							if(empty($baris['persetujuan_pp'])){
								continue;
							}							
							$keterangan = buildHistoryPP($baris);								
							$flok_kandang = explode(' - ',$baris['flok_kandang']);
							$flok = $flok_kandang[0];
							$kandang = $flok_kandang[1];
							echo '<tr data-flok="'.$flok.'" data-status="'.$baris['status_lpb'].'" data-no_pp="'.$baris['no_lpb'].'">';
								$status_lpb = $baris['status_lpb'];								
								$tgl_kirim = '<div>'.implode('</div><div>',array_map('convertElemenTglIndonesia',explode(',',$baris['tgl_kirim']))).'</div>';
								$tgl_kebutuhan = '<div>'.implode('</div><div>',array_map('pecahLaluConvertElemenTglWaktuIndonesia',explode(',',$baris['tgl_kebutuhan']))).'</div>';
								$umur_pakan = '<div>'.implode('</div><div>',array_map('addAttr',explode(',',$baris['umur_pakan']))).'</div>';							
								$checkbox = $status_lpb == 'RV' ? '<input value="'.$baris['no_lpb'].'" name="aksi" type="checkbox">' : '';
								//$status_lpb = convertKode('status_approve',$baris['status_lpb']);																
								echo '<td><span onclick="Permintaan.showDetailPPBudidaya(this)" class="glyphicon glyphicon-plus-sign"></span></td>';
								echo '<td class=" no_pp">'.$baris['no_lpb'].'</td>';
								echo '<td class="">'.$baris['nama_farm'].'</td>';
								echo '<td class="">'.$flok.'</td>';
								echo '<td class="">Kandang '.$kandang.'</td>';								
								echo '<td>'.$tgl_kirim.'</td>';
								echo '<td>'.$tgl_kebutuhan.'</td>';								
								echo '<td>'.$umur_pakan.'</td>';
								echo '<td class="data_pp">'.$baris['optimasi_pp'].'</td>';
								echo '<td class="data_pp">'.$baris['rekomendasi_pp'].'</td>';
								echo '<td class="data_pp">'.$baris['persetujuan_pp'].'</td>';	
								echo '<td>'.$checkbox.'</td>';													
								echo '<td class="keterangan">'.$keterangan.'</td>';																
							echo '</tr>';
							echo '<tr class="detail_pp" style="display:none"></tr>';
						}
						echo '<tbody>';
						echo '</table>';
				//	echo '</div>';	
				}
				else{
					echo '<div>'.$pesan.'</div>';
				}
			?>
