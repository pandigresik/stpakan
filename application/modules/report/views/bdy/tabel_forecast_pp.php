			<?php
					$pesan = 'Data tidak ditemukan';
					if(!empty($data)){
						$selisih = array();
						echo '<table class="table table-striped custom_table">';
						echo '<thead>';
							echo '<tr>';
							echo '<th>Tanggal Kirim</th>';
							echo '<th>Nama Pakan</th>';
							echo '<th>Kuantitas Forecast</th>';
							echo '<th>Kuantitas PP</th>';
							echo '<th>Selisih</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody class="text-center">';
						$tgl_lalu = null;
						$hutang_forecast = array();
						$hutang_pp = array();
						foreach($data as $baris){
							if(!isset($hutang_forecast[$baris['kode_barang']])){
								$hutang_forecast[$baris['kode_barang']] = 0;
							}
							if(!isset($hutang_pp[$baris['kode_barang']])){
								$hutang_pp[$baris['kode_barang']] = 0;
							}
							$kuantitas_forecast = $baris['kuantitas_forecast'] - $hutang_forecast[$baris['kode_barang']];
							$kuantitas_pp = $baris['kuantitas_pp'] - $hutang_pp[$baris['kode_barang']];
							$forecast_tampil = ceil($kuantitas_forecast);
							$pp_tampil = ceil($kuantitas_pp);
							$hutang_forecast[$baris['kode_barang']] = $forecast_tampil - $kuantitas_forecast;
							$hutang_pp[$baris['kode_barang']] = $pp_tampil - $kuantitas_pp;
							$_tmpselisih = $forecast_tampil - $pp_tampil;
							if(!isset($selisih[$baris['kode_barang']])){
								$selisih[$baris['kode_barang']] = $_tmpselisih;
							}
							else{
								$selisih[$baris['kode_barang']] += $_tmpselisih;
							}


							echo '<tr>';
							echo '<td>'.($tgl_lalu != $baris['tgl_kirim'] ? convertElemenTglWaktuIndonesia($baris['tgl_kirim']) : '').'</td>';
							echo '<td>'.$baris['nama_barang'].'</td>';
							echo '<td>'.angkaRibuan($forecast_tampil).'</td>';
							echo '<td>'.angkaRibuan($pp_tampil).'</td>';
							echo '<td>'.angkaRibuan($selisih[$baris['kode_barang']]).'</td>';
							echo '</tr>';

							$tgl_lalu = $baris['tgl_kirim'];
						}
						echo '<tbody>';
						echo '</table>';
					}
					else{
						echo '<div>'.$pesan.'</div>';
					}
				?>
