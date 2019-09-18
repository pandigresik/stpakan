			<?php
					if(!empty($list_pp)){
						echo '<table class="table custom_table">';
						echo '<thead>';
							echo '<tr>';
							echo '<th class="ftl no_pp">No. PP</th>';
							echo '<th class="ftl">Flock</th>';
							echo '<th class="ftl">Kandang</th>';																					
							echo '<th class="tanggal">Tanggal <br /> Kirim</th>';
							echo '<th class="tgl_kebutuhan">Tanggal <br /> Kebutuhan</th>';
							echo '<th class="data_pp">Umur Pakan</th>';							
							echo '<th class="data_pp">Rekomendasi PP <br /> ( Sak )</th>';
							echo '<th class="data_pp">Pengajuan <br /> Kepala Farm <br /> ( Sak )</th>';
							echo '<th class="data_pp">Pengajuan <br /> Kepala Departemen <br /> ( Sak )</th>';
							echo '<th class="keterangan">Keterangan</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						//$hidden_kuantitas_pp = array('V','RJ');
						$show_persetujuan_pp = array('A','RV','RJ','V');
						foreach($list_pp as $baris){
							echo '<tr>';
								$status_lpb = $baris['status_lpb'];
								$tgl_buat = convertElemenTglWaktuIndonesia($baris['tgl_buat']);
								$tgl_rilis = (!empty($baris['tgl_rilis'])) ? convertElemenTglWaktuIndonesia($baris['tgl_rilis']) : '';
								$tgl_approve1 = (!empty($baris['tgl_approve1'])) ? convertElemenTglWaktuIndonesia($baris['tgl_approve1']) : '';
								/** buat keterangan */
								$keterangan = buildHistoryPP($baris);								
								$flok_kandang = explode(' - ',$baris['flok_kandang']);
								$flok = $flok_kandang[0];
								$kandang = $flok_kandang[1];
							
								$tgl_kirim = '<div>'.implode('</div><div>',array_map('convertElemenTglIndonesia',explode(',',$baris['tgl_kirim']))).'</div>';
								$umur_pakan = '<div>'.implode('</div><div>',array_map('addAttr',explode(',',$baris['umur_pakan']))).'</div>';							
								$status_lpb = convertKode('status_approve',$baris['status_lpb']);
							//	$tombol_aksi = (!empty($baris['ref_id']) && ($baris['status_ref_id'] == 'V')) ? '<span class="btn btn-primary" data-ref_id="'.$baris['ref_id'].'" data-no_pp="'.$baris['no_lpb'].'" data-no_flok="'.$flok.'" onclick="Permintaan.comparePP(this)">Compare</span>' : '';								
								
								$tmp_tgl_kebutuhan = explode(' s/d ',$baris['tgl_kebutuhan']);								
								$tgl_kebutuhan = convertElemenTglWaktuIndonesia($tmp_tgl_kebutuhan[0]).' s/d '.convertElemenTglWaktuIndonesia($tmp_tgl_kebutuhan[1]);
								$lock_pp = in_array($baris['status_lpb'],$lockPP) ? 1 : 0;
								if(!empty($baris['ref_id'])){
									$link_lpb = '<span data-lockpp ="'.$lock_pp.'" data-flok="'.$flok.'" data-status="'.$baris['status_lpb'].'" data-no_pp="'.$baris['no_lpb'].'" class="link_span has-tooltip_pp" onclick="Permintaan.transaksi_pp_bdy(this,\'#for_transaksi\')">'.$baris['no_lpb'].'<span class="tooltip_pp" data-lockpp ="1" data-flok="'.$flok.'" data-status="V" data-no_pp="'.$baris['ref_id'].'" class="link_span" onclick="event.stopPropagation();Permintaan.transaksi_pp_bdy(this,\'#for_transaksi\')">'.$baris['ref_id'].'</span></span>';											
								}else{
									$link_lpb = '<span data-lockpp ="'.$lock_pp.'" data-flok="'.$flok.'" data-status="'.$baris['status_lpb'].'" data-no_pp="'.$baris['no_lpb'].'" class="link_span" onclick="Permintaan.transaksi_pp_bdy(this,\'#for_transaksi\')">'.$baris['no_lpb'].'</span>';
								}
								
								echo '<td class="ftl no_pp">'.$link_lpb.'</td>';
								echo '<td class="ftl">'.$flok.'</td>';
								echo '<td class="ftl">'.$kandang.'</td>';								
								echo '<td class="tanggal">'.$tgl_kirim.'</td>';
								echo '<td class="tgl_kebutuhan">'.$tgl_kebutuhan.'</td>';						
								echo '<td class="data_pp">'.$umur_pakan.'</td>';
								echo '<td class="data_pp">'.$baris['optimasi_pp'].'</td>';
								echo '<td class="data_pp">'.$baris['rekomendasi_pp'].'</td>';
								echo '<td class="data_pp">'.(in_array($baris['status_lpb'],$show_persetujuan_pp) ? $baris['persetujuan_pp'] : '').'</td>';						
								echo '<td class="keterangan">'.$keterangan.'</td>';																
							echo '</tr>';
						}
						echo '<tbody>';
						echo '</table>';
					}
					else{
						echo '<div>'.$pesan.'</div>';
					}
				?>
