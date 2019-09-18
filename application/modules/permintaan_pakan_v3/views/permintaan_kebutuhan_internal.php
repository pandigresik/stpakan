<?php
$tmp = $kebutuhan_pakan;

$readonly = '';
$gantiPakan = '';
if(!$editKuantitas){
	$readonly = 'readonly';
}
else{
	// $gantiPakan = ' &nbsp;<span class="btn btn-primary" onclick="Permintaan.ganti_pakan(this)"><span class="glyphicon glyphicon-share"></span></span>';
}
$classInput = '';
if(!$_new){
	if($hitung_ulang){
		$classInput = 'change';
	}
}
else{
	$classInput = 'new';
}

/* buat table */

echo '<table class="table">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Kode Barang</th>';
			echo '<th>Nama Barang</th>';
			echo '<th>Bentuk Pakan</th>';
			echo '<th class="number">Kuantitas Keb</th>';
			echo '<th class="number">Kuantitas PP</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
foreach($tmp as $kodepj => $perpakan){
	$tampil_total_pp_per_pakan = empty($summary_perpakan[$kodepj]['kuantitas_pp']) ? $summary_perpakan[$kodepj]['kuantitas_pp_default'] : $summary_perpakan[$kodepj]['kuantitas_pp'];
	echo '<tr class="kodepj master_row">';
		echo '<td><span class="glyphicon glyphicon-chevron-right" onclick="Permintaan.hidden_child(this)"></span>&nbsp;'.$kodepj.'</td>';
		echo '<td>'.$summary_perpakan[$kodepj]['nama_barang'].'</td>';
		echo '<td>'.$summary_perpakan[$kodepj]['bentuk'].'</td>';
		echo '<td class="number">'.formatAngka($summary_perpakan[$kodepj]['kuantitas_kebutuhan'],2).'</td>';
		echo '<td class="number">'.formatAngka($tampil_total_pp_per_pakan,0).'</td>';
	echo '</tr>';
	echo '<tr class="detail_row" style="display:none">';
		echo '<td></td>';
		echo '<td colspan="4">';
		/* detail kandang */
			echo '<table class="table header-fixed">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Kandang</th>';
						echo '<th class="number">Populasi</th>';
						echo '<th>Tanggal LHK</th>';
						echo '<th>Umur <br /> (minggu)</th>';
						echo '<th class="number">Kuantitas Keb <br /> (sak)</th>';
						echo '<th class="number">Kuantitas PP <br /> (sak)</th>';
						echo '<th>Keterangan</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
		foreach($perpakan as $kodekandang => $perkandang){
				$jatah_pakan_jantan = isset($summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb_jantan']) ? $summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb_jantan'] : 0;
				$jatah_pakan_betina = isset($summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb_betina']) ? $summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb_betina'] : 0;
			
				$sisa_konsumsi_jantan = (float)$summary_perpakan[$kodepj]['kandang'][$kodekandang]['sisa_pakan_jantan'];
				$sisa_konsumsi_betina = (float)$summary_perpakan[$kodepj]['kandang'][$kodekandang]['sisa_pakan_betina'];
				$pengurang_pp_jantan = $summary_perpakan[$kodepj]['kandang'][$kodekandang]['pengurang_pp_jantan'];
				$pengurang_pp_betina = $summary_perpakan[$kodepj]['kandang'][$kodekandang]['pengurang_pp_betina'];
				$default_pp_jantan = ceil($summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb_jantan'] - $sisa_konsumsi_jantan);
				$default_pp_betina = ceil($summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb_betina'] - $sisa_konsumsi_betina);
				$total_default_pp = $default_pp_jantan + $default_pp_betina;
				$tampil_total_pp = empty($summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_pp']) ? $total_default_pp : $summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_pp'];
						echo '<tr class="master_row">';
						echo '<td data-noreg="'.$summary_perpakan[$kodepj]['kandang'][$kodekandang]['no_reg'].'"><span class="glyphicon glyphicon-chevron-right" onclick="Permintaan.hidden_child(this)"></span>&nbsp;'.$kodekandang.'</td>';
						echo '<td class="number">'.formatAngka(($summary_perpakan[$kodepj]['kandang'][$kodekandang]['populasi_jantan'] + $summary_perpakan[$kodepj]['kandang'][$kodekandang]['populasi_betina']),0).'</td>';
						echo '<td><span class="link_span" onclick="Permintaan.show_lhk(this)">'.tglIndonesia($summary_perpakan[$kodepj]['kandang'][$kodekandang]['lhk_terakhir'],'-',' ').'</span></td>';
						echo '<td>'.$summary_perpakan[$kodepj]['kandang'][$kodekandang]['umur_awal'].' s.d '.$summary_perpakan[$kodepj]['kandang'][$kodekandang]['umur_akhir'].'</td>';
						echo '<td class="number"><span class="link_span" onclick="Permintaan.show_lhk(this)">'.formatAngka($summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb'],2).'</span></td>';
						echo '<td class="number">'.$tampil_total_pp.'</td>';
						echo '<td><input type="text" name="keterangan '.$classInput.'" value="'.$summary_perpakan[$kodepj]['kandang'][$kodekandang]['keterangan'].'" '.$readonly.'></td>';
					echo '</tr>';
					echo '<tr class="detail_row" style="display:none">';
						echo '<td></td>';
						echo '<td colspan="6" class="detail_kebutuhan_pakan">';
						
							echo '<div class="row">';
						/* buat tabel untuk jantan */
								echo '<div class="col-md-6">';
								$jml_tgl_kebutuhan = count($perkandang['jantan']);
								if(!empty($jml_tgl_kebutuhan)){
									echo '<div class="checkbox"><label><input disabled type="checkbox" data-kelamin="J" onclick="Permintaan.add_pakan_eksternal(this)">Populasi jantan '.formatAngka($summary_perpakan[$kodepj]['kandang'][$kodekandang]['populasi_jantan'],0).'<span class="sumber_pakan"></span></label></div>';
									echo '<table class="table">';
										echo '<thead>';
											echo '<tr>';
												echo '<th>Tanggal</th>';
												echo '<th>Kuantitas Keb</th>';
												echo '<th>Kuantitas PP</th>';
											echo '</tr>';
										echo '</thead>';
										echo '<tbody>';
										
										$first_element = true;
										$kuantitas = $summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb_jantan'];
										foreach($perkandang['jantan'] as $pertanggal){
											echo '<tr>';
												echo '<td data-umur="'.$pertanggal['umur'].'">'.$pertanggal['tgl_kebutuhan'].'</td>';
												echo '<td class="number" data-pakan_forecast="'.$pertanggal['forecast'].'">'.formatAngka($pertanggal['kuantitas'],2).'</td>';
												
												if($first_element){
													$jml_asli = $kuantitas - $sisa_konsumsi_jantan;
													if($hitung_ulang){
														
														$jml_tampil = ceil($jml_asli);
													}
													else{
														$jml_tampil = !is_null($pertanggal['pp']) ? $pertanggal['pp'] : ceil($jml_asli);
													}
													echo '<td class="kotak number" rowspan="'.$jml_tgl_kebutuhan.'"><input class="required no_border '.$classInput.'" name="jml_order" type="text" data-pengurang_pp="'.$pengurang_pp_jantan.'" data-jml_asli="'.$jml_asli.'" data-db_value="'.formatAngka($jml_tampil,0).'" data-old_value="'.formatAngka($jml_tampil,0).'" value="'.formatAngka($jml_tampil,0).'" onchange="Permintaan.update_total_kebutuhan_pakan(this)" '.$readonly.'>'.$gantiPakan.'</td>';
													$first_element = false;
												}	
													

											echo '</tr>';
										}
										echo '</tbody>';
									echo '</table>';
								}
								echo '</div>';	
								/* buat tabel untuk betina */
								echo '<div class="col-md-6">';
								$jml_tgl_kebutuhan = count($perkandang['betina']);
								if(!empty($jml_tgl_kebutuhan)){
								echo '<div class="checkbox"><label><input disabled type="checkbox" data-kelamin="B" onclick="Permintaan.add_pakan_eksternal(this)">Populasi betina '.formatAngka($summary_perpakan[$kodepj]['kandang'][$kodekandang]['populasi_betina'],0).'<span class="sumber_pakan"></span></label></div>';
									echo '<table class="table">';
										echo '<thead>';
											echo '<tr>';
												echo '<th>Tanggal</th>';
												echo '<th>Kuantitas Keb</th>';
												echo '<th>Kuantitas PP</th>';
											echo '</tr>';
										echo '</thead>';
										echo '<tbody>';
										
										$first_element = true;
										$kuantitas = $summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb_betina'];
										foreach($perkandang['betina'] as $pertanggal){
											echo '<tr>';
												echo '<td data-umur="'.$pertanggal['umur'].'">'.$pertanggal['tgl_kebutuhan'].'</td>';
												echo '<td class="number" data-pakan_forecast="'.$pertanggal['forecast'].'">'.formatAngka($pertanggal['kuantitas'],2).'</td>';
												
												if($first_element){
													$jml_asli = $kuantitas - $sisa_konsumsi_betina;
													if($hitung_ulang){
														$jml_tampil = ceil($jml_asli);
													}
													else{
														$jml_tampil = !is_null($pertanggal['pp']) ? $pertanggal['pp'] : ceil($jml_asli);
													}
													echo '<td class="kotak number" rowspan="'.$jml_tgl_kebutuhan.'"><input class="required no_border '.$classInput.'"" name="jml_order" type="text" max="'.$kuantitas.'" min="0" data-pengurang_pp="'.$pengurang_pp_betina.'"  data-jml_asli="'.$jml_asli.'" data-db_value="'.formatAngka($jml_tampil,0).'" data-old_value="'.formatAngka($jml_tampil,0).'" value="'.formatAngka($jml_tampil,0).'" onchange="Permintaan.update_total_kebutuhan_pakan(this)" '.$readonly.'>'.$gantiPakan.'</td>';
													$first_element = false;
												}
												

											echo '</tr>';
										}
										echo '</tbody>';
									echo '</table>';
								}
								echo '</div>';
							echo '</div>';
						echo '</td>';
					echo '</tr>';
				
		}
				echo '</tbody>';
			echo '</table>';
		echo '</td>';
	echo '</tr>';
}
	echo '</tbody>';
echo '</table>';
?>


