			<?php
					if(!empty($list_bapd)){
						echo '<table class="table  custom_table">';
						echo '<thead>';
							echo '<tr>';
								echo '<th rowspan="2">Siklus</th>';
								echo '<th rowspan="2">Kandang</th>';
								echo '<th rowspan="2">Tanggal DOC In</th>';
								echo '<th rowspan="2">Hatchery</th>';
								echo '<th colspan="4">Jumlah DOC</th>';
								echo '<th rowspan="2">BB Rata-rata (Kg)</th>';
								echo '<th rowspan="2">Uniformity (%)</th>';
								echo '<th rowspan="2">Status</th>';
								echo '<th rowspan="2">Keterangan</th>';
							echo '</tr>';
							echo '<tr>';
								echo '<th>Box</th>';
								echo '<th>Ekor</th>';
								echo '<th>Afkir</th>';
								echo '<th>Stok Awal</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						$convert_status = array(
							'A' => 'approve',
							'D' => 'buat',
							'N' => 'rilis',
							'RJ' => 'reject',
							'RV' => 'review'
						);
						foreach($list_bapd as $baris){
							$bisaEntry = 0;
							if($user_level == 'P'){
								if(empty($baris['status'])){
									$bisaEntry = 1;
								}
								if(in_array($baris['status'],array('RJ'))){
									$bisaEntry = 1;
								}
							}
							$keterangan = isset($riwayat[$baris['no_reg']]) ? $riwayat[$baris['no_reg']] : array();
							$keterangan_tmp = array();
							if(!empty($keterangan)){
								foreach($keterangan as $k){
									array_push($keterangan_tmp,'['.$k['nama_pegawai'].'], Di'.$convert_status[$k['status']]. (!empty($k['keterangan']) ? '( <em>'.$k['keterangan'].' </em>)' : '') .' '.convertElemenTglWaktuIndonesia($k['tgl_buat']));
								}
							}
							
							echo '<tr class="'.($bisaEntry ? "bg-pink" : "").'" '.($bisaEntry ? 'ondblclick="BAPD.entryPerformance(this)"' : '').' data-status="'.$baris['status'].'" data-level="'.$user_level.'" data-no_reg="'.$baris['no_reg'].'" data-tgldoc="'.$baris['tgl_doc_in'].'">';
								$status = isset($convert_status[$baris['status']]) ? ucwords($convert_status[$baris['status']]) : '';
								if($user_level == 'KF'){
									if($baris['status'] == 'N'){
										$status = '<input type="checkbox" value="'.$baris['no_reg'].'"/>';
									}
								}
								$tgldocin = convertElemenTglIndonesia($baris['tgl_doc_in']);
								echo '<td class="noreg">'.$baris['periode_siklus'].'</td>';
								echo '<td class="kandang" data-kode_kandang="'.$baris['kode_kandang'].'">Kandang '.$baris['kode_kandang'].'</td>';
								echo '<td class="tanggal">'.$tgldocin.'</td>';
								echo '<td class="hatchery">'.$baris['nama_hatchery'].'</td>';
								echo '<td class="" data-no_reg="'.$baris['no_reg'].'" onclick="BAPD.show_detailsj(this)"><i class="glyphicon glyphicon-plus"></i> <span class="link_span">'.$baris['jmlboxterima'].'</span></td>';
								echo '<td class="jmlbox" data-jmlterima="'.($baris['jmlboxterima']*102).'">'.angkaRibuan($baris['jmlboxterima']*102).'</td>';
								echo '<td class="jml_afkir">'.$baris['jml_afkir'].'</td>';
								echo '<td class="stok_awal">'.angkaRibuan($baris['stok_awal']).'</td>';
								echo '<td class="bb_rata2">'.formatAngka($baris['bb_rata2'],5).'</td>';
								echo '<td class="uniformity">'.formatAngka($baris['uniformity'],2).'</td>';
								echo '<td class="status">'.$status.'</td>';
								echo '<td class="keterangan"><div>'.implode('</div><div>',$keterangan_tmp).'</div></td>';
							echo '</tr>';
							echo '<tr class="detailbapdoc" style="display:none">';
								echo '<td></td>';
								echo '<td></td>';
								echo '<td></td>';
								echo '<td></td>';
								echo '<td class="tddetail" colspan="7"></td>';
								echo '<td></td>';
							echo '<tr>';
						}
						echo '</tbody>';
						echo '</table>';
					}
					else{
						echo '<div>Data tidak ditemukan</div>';
					}
				?>
