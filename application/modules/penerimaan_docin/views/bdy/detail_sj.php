<?php
						echo '<table class="table table-striped custom_table" data-table="bapdocbox">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>No. SJ</th>';
								echo '<th>Tanggal Penerimaan</th>';
								echo '<th>Jumlah Box</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($sj as $baris){
							echo '<tr>';
								$tglpenerimaan = convertElemenTglWaktuIndonesia($baris['TGL_TERIMA']);
								echo '<td class="sj">'.$baris['NO_SJ'].'</td>';
								echo '<td class="tgl_terima">'.$tglpenerimaan.'</td>';
								echo '<td class="jmlbox">'.$baris['JML_BOX'].'</td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '</table>';
?>
