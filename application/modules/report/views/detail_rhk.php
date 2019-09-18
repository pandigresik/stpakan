<table class="table table-bordered custom_table stok_pakan">
	<thead>
			<tr>
				<th class="ftl" rowspan="3">Tgl.</th>
				<th class="ftl" colspan="2">Umur</th>
				<th class="ftl" rowspan="3">Sex</th>
				<th class="ftl" rowspan="3">Pop. <br /> (ekor)</th>
				<th class="ftl" rowspan="3">Rasio Pop. (J/B)</th>
				<th colspan="9">Performa</th>
				<th colspan="8">Kontrol Pakan</th>
				<th colspan="5">Pertanggungjawaban Retur</th>
				<th rowspan="3">Catatan</th>
			</tr>
			<tr>
				<th class="ftl" rowspan="2">Hari</th>
				<th class="ftl" rowspan="2">Minggu</th>
				<th class="dh" rowspan="2">DH</th>
				<th colspan="2">Mati/Afkir</th>
				<th rowspan="2">KE (g)</th>
				<th rowspan="2">BB (kg)</th>
				<th rowspan="2">FCR</th>
				<th rowspan="2">ADG (g/hr)</th>
				<th colspan="2">Produksi Telur</th>
				
				<th class="nama_bentuk" rowspan="2">Nama Pakan</th>
				<th colspan="2">Stok Awal</th>
				<th rowspan="2">Terima (sak)</th>
				<th colspan="2">Pakai</th>
				<th colspan="2">Stok Akhir</th>
				
				<th colspan="2">Pakan Rusak</th>
				<th colspan="3">Sak Kosong</th>
			</tr>
			<tr>				
				<th>Ekor</th>
				<th>%</th>
				
				<th>Butir</th>
				<th>%</th>
				
				<th>Kg</th>
				<th>Sak</th>
				
				<th>Kg</th>
				<th>Sak</th>
				
				<th>Kg</th>
				<th>Sak</th>
				
				<th>Dikembalikan</th>
				<th>Diganti</th>
				
				<th>Dikembalikan</th>
				<th>Terhutang</th>
				<th>Sisa</th>
			</tr>
		</thead>
		<tbody>
		<?php 
//		echo '<pre>';print_r($p_pakan);
		if(!empty($rhk)){
			$header_bulan = null;
			$bulan_ini = null;
			$index_bulan = 0;
			$next_index_bulan = 0;
			foreach($rhk as $tgl =>$r){
				if(empty($header_bulan)){
					$header_bulan = getMonthYear($tgl);
					echo '<tr><td class="ftl" colspan="6"><strong>'.$header_bulan.'</strong></td></tr>'; 
				}
				$bulan_ini = getMonthYear($tgl);
				if($bulan_ini != $header_bulan){
					$header_bulan = $bulan_ini;
					echo '<tr><td class="ftl" colspan="6"><strong>'.$header_bulan.'</strong></td></tr>'; 
				}
				
				echo '<tr>';
				foreach($r['header'] as $k => $td){
					if($k == 'tgl'){
						$b_rowspan = isset($p_pakan[$td]['B']) ? count($p_pakan[$td]['B']) : 0;;
						$j_rowspan = isset($p_pakan[$td]['J']) ? count($p_pakan[$td]['J']) : 0;
						$rowspan = $b_rowspan + $j_rowspan;
						
						$td = getDateStr($td);
					}
					echo '<td rowspan="'.$rowspan.'" class="text-center ftl">'.$td.'</td>';	
				}
				$y = 0;
				foreach($r['detail'] as $dd){
					if($y > 0){
						echo '<tr>';	
					}
					$jkl = strtolower($dd['jk']);
					$jml = $jkl.'_jumlah';
					$t_rowspan = ${$jkl.'_rowspan'};
					$persenmati = $dd['mati'] > 0 ? $dd['mati'] / ($dd['jml'] + $dd['mati']) : 0;
					$brt_pakai_ekor = $dd['brt_pakai']/$dd['jml'] * 1000; // dalam gram 
					$brt_pakai = $brt_pakai_ekor != $dd['pkn_std'] ? '<a href="#" class="abang" data-toogle="tooltip" data-original-title="'.$dd['pkn_std'].'">'.formatAngka($brt_pakai_ekor,3).'</a>' : formatAngka($brt_pakai_ekor,3); 		
					$brt_bb = (!empty($dd['berat_badan']) && ($dd['berat_badan'] != $dd['bb_std'])) ? '<a href="#" class="abang" data-toogle="tooltip" data-original-title="'.$dd['bb_std'].'">'.formatAngka($dd['berat_badan'],3).'</a>' : !empty($dd['berat_badan']) ? formatAngka($dd['berat_badan'],3) : '-';
				//	$dh = ($dd['jml']/${$jml}) * 100;
					$dh = $dd['dh'];
					$dh_str = $dh < $dd['dh_std'] ? '<a href="#" class="abang" data-toogle="tooltip" data-original-title="'.$dd['dh_std'].'">'.formatAngka($dh,2).' %</a>' :  formatAngka($dh,2).' %';
					$prod_str = !empty($dd['produksi']) ? formatAngka($dd['produksi'],0) : '-';
					echo '<td class="text-center ftl" rowspan="'.$t_rowspan.'">'.$dd['jk'].'</td>';	
					echo '<td class="number ftl" rowspan="'.$t_rowspan.'">'.formatAngka($dd['jml'],0).'</td>';
					if($y == 0){
						echo '<td class="ftl" rowspan="'.$rowspan.'">1:'.formatAngka($dd['rasio'],2).'</td>';
					}
				  	echo '<td class="number dh" rowspan="'.$t_rowspan.'">'.$dh_str.'</td>';
					echo '<td class="number" rowspan="'.$t_rowspan.'">'.$dd['mati'] .'</td>';
					echo '<td class="number" rowspan="'.$t_rowspan.'">'.formatAngka(($persenmati * 100),3) .'%</td>';
					echo '<td class="number" rowspan="'.$t_rowspan.'">'.$brt_pakai.'</td>';
					echo '<td class="number" rowspan="'.$t_rowspan.'">'.$brt_bb.'</td>';
					echo '<td rowspan="'.$t_rowspan.'">'.$dd['fcr'].'</td>';
					echo '<td rowspan="'.$t_rowspan.'">'.$dd['adg'].'</td>';
					echo '<td class="number" rowspan="'.$t_rowspan.'">'.$prod_str.'</td>';
					echo '<td rowspan="'.$t_rowspan.'">-</td>';
					
					/* kontrol pakan */
					$_p = 0;
					$tglnya = $r['header']['tgl'];
					foreach($p_pakan[$tglnya][$dd['jk']] as $z){
						if($_p > 0){
							echo '<tr>';
						}
						$jml_awal = ($z['jml_akhir'] + $z['jml_pakai']) - $z['jml_terima'];
						$brt_awal = ($z['brt_akhir'] + $z['brt_pakai']) - $z['brt_terima'];
						$class_stok_akhir = '';
						if($z['jml_akhir'] <= 0){
							if($z['brt_akhir'] > 0){
								$class_stok_akhir = 'abang';	
							}		
						}
						else{
							$class_stok_akhir = !beratDalamStandar($z['jml_akhir'],$z['brt_akhir']) ? 'abang' : ''; 
						}

						echo '<td class="nama_bentuk">'.$z['nama_pakan'].'</td>';
						echo '<td class="number">'.formatAngka($brt_awal,3).'</td>';
						echo '<td class="number">'.$jml_awal.'</td>';
						
						echo '<td class="number">'.$z['jml_terima'].'</td>';
						echo '<td class="number">'.formatAngka($z['brt_pakai'],3).'</td>';
						echo '<td class="number">'.$z['jml_pakai'].'</td>';
						echo '<td class="number'.$class_stok_akhir.'">'.formatAngka($z['brt_akhir'],3).'</td>';
						echo '<td class="number '.$class_stok_akhir.'">'.$z['jml_akhir'].'</td>';
						
						echo '<td class="number">'.$z['pakan_diganti'].'</td>';
						echo '<td class="number">'.$z['pakan_retur'].'</td>';
						echo '<td class="number">'.$z['sak_retur'].'</td>';
						echo '<td class="number">'.$z['sak_hutang'].'</td>';
						
						echo '<td class="number">'.$z['sisa_hutang'].'</td>';
						echo '<td></td>';
						if($_p > 0){
							echo '</tr>';
						}
						$_p++;
					}
					if($y > 0){
						echo '</tr>';
					}
					$y++;
				}
								
				echo '</tr>';
			}
		}
		?>
		</tbody>
	</table>	



