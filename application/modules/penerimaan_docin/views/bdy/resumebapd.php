<div class="listbapd">
			<?php
					if(!empty($rbapd)){
						echo '<table class="table table-striped  custom_table">';
						echo '<thead>';
							echo '<tr>';
								echo '<th rowspan="2">No. Reg</th>';
								echo '<th rowspan="2">Kandang</th>';
								echo '<th rowspan="2">Hatchery</th>';
								echo '<th rowspan="2">Tanggal DOC In</th>';
								echo '<th colspan="4">Jumlah DOC</th>';
								echo '<th rowspan="2">BB rata-rata</th>';
								echo '<th rowspan="2">Uniformity (%)</th>';
								echo '<th colspan="2">Tindak Lanjut</th>';
							echo '</tr>';
							echo '<tr>';
								echo '<th>Box</th>';
								echo '<th>Ekor</th>';
								echo '<th>Afkir</th>';
								echo '<th>Stok Awal</th>';
								echo '<th>Pengawas Kandang</th>';
								echo '<th>Kepala Farm</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($rbapd as $baris){
							echo '<tr>';
								$tglreview = (!empty($baris['tindaklanjutpengawas'])) ? convertElemenTglWaktuIndonesia($baris['tindaklanjutpengawas']) : '';
								$tglapprove = (!empty($baris['tindaklanjutkafarm'])) ? convertElemenTglWaktuIndonesia($baris['tindaklanjutkafarm']) : '';
								$tgldocin = convertElemenTglIndonesia($baris['tgl_doc_in']);
								echo '<td>'.$baris['no_reg'].'</td>';
								echo '<td>'.$baris['kode_kandang'].'</td>';
								echo '<td>'.$baris['nama_hatchery'].'</td>';
								echo '<td class="tanggal">'.$tgldocin.'</td>';
								echo '<td class="status">'.formatAngka($baris['jmlbox'],0).'</td>';
								echo '<td class="status">'.formatAngka($baris['stok_awal'],0).'</td>';
								echo '<td class="status">'.formatAngka($baris['jml_afkir'],0).'</td>';
								echo '<td class="status">'.formatAngka($baris['stok_awal'],0).'</td>';
								echo '<td class="status">'.formatAngka($baris['bb_rata2'],2).'</td>';
								echo '<td class="status">'.formatAngka($baris['uniformity'],2).'</td>';
								echo '<td class="tanggal">'.$tglreview.'</td>';
								echo '<td class="tanggal">'.$tglapprove.'</td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '</table>';
					}
					else{
						echo '<div>Data tidak ditemukan</div>';
					}
				?>
</div>
