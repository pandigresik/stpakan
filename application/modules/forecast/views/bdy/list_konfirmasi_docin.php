			<?php
				$pesan = 'Data tidak ditemukan';
					if(!empty($list_konfirmasi)){
					echo '<div class="table-responsive">';	
						echo '<table class="table">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>Farm</th>';
								echo '<th>Siklus</th>';
								echo '<th>Tanggal DOC In</th>';
								echo '<th>Tanggal Kebutuhan</th>';								
								echo '<th>Status</th>';
								echo '<th>Umur Panen</th>';
								echo '<th>Tanggal <br /> Approve Kadept</th>';
								echo '<th>Tanggal Tindak <br /> Lanjut Kadiv</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($list_konfirmasi as $baris){
							echo '<tr>';
								$status = $baris['status'];
								$tgl_docin = convertElemenTglWaktuIndonesia($baris['tgl_chickin']);
								$tgl_approve = convertElemenTglWaktuIndonesia($baris['approve_kadept']);
								$tgl_akhir_kebutuhan = convertElemenTglWaktuIndonesia($baris['akhir_kebutuhan']);
								$umur_panen = $baris['umur_panen'];
								$nama_farm = $baris['nama_farm'];
								$kode_farm = $baris['kode_farm'];
								$siklus = $baris['periode_siklus'];								
								$tgl_approvekadiv = (!empty($baris['tgl_approvekadiv'])) ? convertElemenTglWaktuIndonesia($baris['tgl_approvekadiv']) : '<div class="btnApproval row"><span class="btn btn-default" onclick="AktivasiKandang.approve(this,\''.$kode_farm.'\',\''.$baris['tgl_chickin'].'\',\''.$nama_farm.'\',\'KDV\')">Approve</span> <span onclick="AktivasiKandang.reject(this,\''.$kode_farm.'\',\''.$baris['tgl_chickin'].'\',\''.$nama_farm.'\',\'KDV\')" class="btn btn-default">Reject</span></div>';

								echo '<td>'.$nama_farm.'</td>';
								echo '<td>'.$siklus.'</td>';
								echo '<td>'.$tgl_docin.'</td>';
								echo '<td>'.$tgl_docin.' s/d '.$tgl_akhir_kebutuhan.'</td>';						
								echo '<td>'.$status.'</td>';
								echo '<td>'.$umur_panen.'</td>';
								echo '<td>'.$tgl_approve.'</td>';
								echo '<td class="aksi">'.$tgl_approvekadiv.'</td>';
							echo '</tr>';
						}
						echo '<tbody>';
						echo '</table>';
					echo '</div>';	
					}
					else{
						echo '<div>'.$pesan.'</div>';
					}
				?>
