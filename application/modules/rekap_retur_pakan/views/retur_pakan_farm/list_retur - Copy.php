<div class="row">
 	<div class="container col-md-12">
 		<table class="table table-bordered custom_table" id="tabellistretur">
 			<thead>
 				<tr>
 					<th>No. Retur Pakan</th>
 					<th>Farm Asal</th>
 					<th>Farm Tujuan</th>
 					<th>Tanggal Kirim</th>
 					<th>Jenis Pakan</th>
 					<th>Jumlah Sak</th>
 					<th>Keterangan</th>
 					<th>Lampiran</th>
					<?php 
						if($user_level == 'KDV' || $user_level == 'KD'){
							echo "<th>Aksi
								<br><input type='checkbox' onClick='Returpakanfarm.pilihSemua(this)'>
							</th>";
						}
					?>
 					<th>Status</th> 					
 				</tr> 			
 			</thead>
 			<tbody>
	 		<?php
				if(!empty($returs)){ 
					/*
					foreach($returs as $r){
						echo '<tr onclick="Returpakanfarm.pilih(this)" data-reject='.(in_array(trim($r['STATUS']),array_keys($rejectedStatus)) ? 1 : 0).'>
							<td class="no_retur" data-retur="'.$r['NO_RETUR'].'" onclick="Returpakanfarm.detail(this)"><span class="link_span">'.$r['NO_RETUR'].'</span></td>
							<td class="farm_asal">'.$farm[$r['FARM_ASAL']]['nama_farm'].'</td>
							<td class="farm_tujuan">'.$farm[$r['FARM_TUJUAN']]['nama_farm'].'</td>
							<td class="tgl_kirim">'.tglIndonesia($r['TGL_KIRIM'],'-',' ').'</td>
							<td></td>
							<td></td>
							<td></td>
							<td>'.$r['LAMPIRAN'].'</td>
							<td class="status" data-kodestatus="'.$r['STATUS'].'">'.$listStatus[$r['STATUS']].'</td>
						</tr>';
					}
					*/
					
					/* baru */
					$numRow = 1;
					foreach($returs as $r){
						if($r['STATUS'] != 'V'){
						$status='';
						$num = 0;
						$cb = '';
						$noReturAct = '';
						
						if($user_level == 'KD' || $user_level == 'KDV'){
							$cb = "<td>
								<input class='list_retur_cb' type='checkbox' style='cursor:pointer;' class='CBrow' data-row='TR$numRow' onClick='Returpakanfarm.pilihCheck(this)'>
								</td>";
							$checkCB = 0;
						}
						
						foreach($listHistory[$r['NO_RETUR']] as $hretur){
							$status .= '['.$hretur['NAMA_PEGAWAI'].'] - '.$listStatus[$hretur['STATUS']].'<br>';
								
							if($user_level == 'KD' || $user_level == 'KDV'){	
								if(!$checkCB){
									if($user_level == 'KD' && $hretur['STATUS'] == 'RV' 
										|| $user_level == 'KDV' && $hretur['STATUS'] == 'A'){
										$cb = '<td></td>';
									}
									$checkCB = 1;
								}
							}
						}
						
						if(strlen($r['NO_REFERENSI'])>0){
							foreach($listHistory[$r['NO_REFERENSI']] as $hretur){
								$keterangan = ' - ';
								if($hretur['STATUS']=='RJ1' || $hretur['STATUS']=='RJ2'){
									$keterangan = $hretur['KETERANGAN'].' - ';
								}
								$status .= '['.$hretur['NAMA_PEGAWAI'].']'
									.$keterangan
									.$listStatus[$hretur['STATUS']].'<br>';
							}
						}
					
						
						if($user_level == 'KF'){
							$noReturAct = 'onClick="Returpakanfarm.tampilUbah(this)" data-rowID="TR'.$numRow.'"';
						}
					
						echo '<tr id="TR'.$numRow.'" class="TRrow" data-retur="'.$r['NO_RETUR'].'" data-kodestatus="'.$r['STATUS'].'" data-reject='.(in_array(trim($r['STATUS']),array_keys($rejectedStatus)) ? 1 : 0).' style="cursor:default;">
							<td class="no_retur" '.$noReturAct.' data-retur="'.$r['NO_RETUR'].'"><span class="link_span">'.$r['NO_RETUR'].'</span></td>
							<td class="farm_asal">'.$farm[$r['FARM_ASAL']]['nama_farm'].'</td>
							<td class="farm_tujuan">'.$farm[$r['FARM_TUJUAN']]['nama_farm'].'</td>
							<td class="tgl_kirim">'.tglIndonesia($r['TGL_KIRIM'],'-',' ').'</td>
							<td>'.$listPakanSisa[$r['NO_RETUR']][0]['NAMA_PAKAN'].'</td>
							<td>'.$listPakanSisa[$r['NO_RETUR']][0]['JUMLAH'].'</td>
							<td>'.$listHistory[$r['NO_RETUR']][count($listHistory[$r['NO_RETUR']])-1]['KETERANGAN'].'</td>
							<td><a href="lampiran/baca?f='.$r['LAMPIRAN'].'" target="_blank">'.$r['LAMPIRAN'].'</a></td>
							'.$cb.'
							<td class="status" data-kodestatus="'.$r['STATUS'].'">'.$status.'</td>
						</tr>';
						
						$numRow++;
						}
					}
					/* end baru */
				}
	 		?>
	 		</tbody>
 		</table>
	</div>
</div>
