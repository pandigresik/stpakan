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
$cekbox_reset = $showCekbox ? '<input type="checkbox" onclick="Permintaan.showHideReview(this)"/>':'';
/* jika direset maka overrride menjadi new */
$class_review = ($reset) ? 'new' : $class_review;
/* jika statusLpb adalah reject maka tampilkan langsung review terakhir */
$showReview = $statusLpb == 'RJ' ? 'no_border' : 'hide';

/* buat table */
foreach($tmp as $kodepj => $perpakan){
	echo '<tr class="kodepj master_row pakan_tambahan">';
		echo '<td><span class="glyphicon glyphicon-chevron-right" onclick="Permintaan.hidden_child(this)"></span>&nbsp;'.$kodepj.'</td>';
		echo '<td>'.$summary_perpakan[$kodepj]['nama_barang'].'</td>';
		echo '<td>'.$summary_perpakan[$kodepj]['bentuk'].'</td>';
		echo '<td>'.$summary_perpakan[$kodepj]['umur_awal'].' - '.$summary_perpakan[$kodepj]['umur_akhir'].'</td>';
		echo '<td class="number">0</td>';
		echo '<td class="number">0</td>';
	echo '</tr>';
	echo '<tr class="detail_row" style="display:none">';
		echo '<td></td>';
		echo '<td colspan="5">';
		/* detail kandang */
			echo '<table class="table custom_table">';
				echo '<thead>';
					echo '<tr>';
						echo '<th rowspan="2">Kandang</th>';
						echo '<th rowspan="2" class="">Populasi</th>';
						echo '<th rowspan="2">Tanggal LHK</th>';
						echo '<th rowspan="2">Kebutuhan (Sak)</th>';
						echo '<th colspan="2">Kuantitas per Kandang (Sak)</th>';
						echo '<th rowspan="2">Persetujuan (Sak)</th>';
					echo '</tr>';
					echo '<tr>';
						echo '<th class="number">Optimasi (Sak)</th>';
						echo '<th class="number">Rekomendasi (sak)</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
		$muncul_review = array('RV','RJ','A');
		foreach($perpakan as $kodekandang => $perkandang){
				$kuantitas = 0;
				$sisa_konsumsi = 0;
				$noreg = $summary_perpakan[$kodepj]['kandang'][$kodekandang]['no_reg'];
				/* ambil total rekomendasi dan review */
				$sumReview = array(
					'jml_rekomendasi' => 0,
					'jml_review' => 0,
					'jml_optimasi' => 0
				);
				if(isset($review[$kodepj])){
					/*
					foreach($review[$kodepj][$noreg] as $vv){
						$sumReview['jml_optimasi'] += $vv['JML_OPTIMASI'];
						$sumReview['jml_rekomendasi'] += (!empty($vv['JML_REKOMENDASI']) ? $vv['JML_REKOMENDASI'] : $vv['JML_OPTIMASI']);
						$sumReview['jml_review'] += (!empty($vv['JML_REVIEW']) ? $vv['JML_REVIEW'] : $vv['JML_OPTIMASI']);
					}
					*/
				}

				echo '<tr class="master_row">';
						echo '<td data-noreg="'.$noreg.'"><span class="glyphicon glyphicon-chevron-right" onclick="Permintaan.hidden_child(this)"></span>&nbsp;'.$cekbox_reset.' '.$kodekandang.'</td>';
						echo '<td class="number">'.formatAngka($summary_perpakan[$kodepj]['kandang'][$kodekandang]['populasi'] ,0).'</td>';
						echo '<td><span class="link_span" onclick="Permintaan.show_lhk_bdy(this)">'.tglIndonesia($summary_perpakan[$kodepj]['kandang'][$kodekandang]['lhk_terakhir'],'-',' ').'</span></td>';
						echo '<td class="number"><span class="link_span" onclick="Permintaan.show_lhk_bdy(this)">'.formatAngka($summary_perpakan[$kodepj]['kandang'][$kodekandang]['kuantitas_keb'],3).'</span></td>';
						echo '<td class="number">'.formatAngka($summary_perpakan[$kodepj]['kandang'][$kodekandang]['optimasi_pp'],0).'</td>';
				//		echo '<td class="number jml_order">'.$sumReview['jml_optimasi'].'</td>';
						echo '<td class="number jml_rekomendasi">'.$sumReview['jml_rekomendasi'].'</td>';
						echo '<td class="number jml_review">'.(in_array($statusLpb,$muncul_review) ? $sumReview['jml_review'] : '').'</td>';

					echo '</tr>';
					echo '<tr class="detail_row" style="display:none">';
					echo '<td></td>';
					echo '<td colspan="6" class="detail_kebutuhan_pakan">';

					echo '<div class="row">';
						/* buat tabel untuk jantan */
								echo '<div class="col-md-12">';
								$jml_tgl_kebutuhan = count($perkandang);
								if(!empty($jml_tgl_kebutuhan)){
									echo '<table class="table">';
										echo '<thead>';
											echo '<tr>';
												echo '<th rowspan="2">Tanggal</th>';
												echo '<th rowspan="2">Kuantitas Keb (sak)</th>';
												echo '<th rowspan="2">Kuantitas Optimasi (sak)</th>';
												echo '<th colspan="2">Rekomendasi PP</th>';
												echo '<th colspan="2">Persetujuan PP</th>';
											echo '</tr>';
											echo '<tr>';
												echo '<th>Kuantitas (sak)</th>';
												echo '<th>Alasan</th>';
												echo '<th>Kuantitas (sak)</th>';
												echo '<th>Alasan</th>';
											echo '</tr>';
										echo '</thead>';
										echo '<tbody>';
										$tanggal_pertama = 0;
										foreach($perkandang as $pertanggal){
											$tgl_keb_asli = $pertanggal['tgl_asli'];
											$defaultReview = '';
											$defaultRekomendasi = '';
									//		$defaultOptimasi = $pertanggal['optimasi_pakan'];
                                            $defaultOptimasi = '';
											$defaultKetReview = '';
											$defaultKetRekomendasi = '';

											if(isset($review[$kodepj])){
												$defaultReview = NULL;
												if(in_array($statusLpb,$muncul_review)){
														$defaultReview = NULL;
												}

												$defaultRekomendasi = NULL;
												$defaultOptimasi = NULL;
												$defaultKetRekomendasi = NULL;
												$defaultKetReview = NULL;
											}

											$jml_rekomendasi = ($editRekomendasi ) ? '<input size="2" onchange="Permintaan.summaryEntry(this,\'jml_rekomendasi\')" data-group="rekomendasi" class="review '.$class_review.'" name="jml_rekomendasi" type="text" value="'.(is_null($defaultRekomendasi)  ? $defaultRekomendasi : $defaultOptimasi) .'">': (!is_null($defaultRekomendasi) ? $defaultRekomendasi : '');
											$ket_rekomendasi = ($editRekomendasi ) ? '<textarea name="keterangan"  data-group="rekomendasi" class="review '.$class_review.'">'.(!is_null($defaultKetRekomendasi) ? $defaultKetRekomendasi : '').'</textarea>' : (!is_null($defaultKetRekomendasi) ? $defaultKetRekomendasi : '');
											$jml_review = $editReview ?'<input size="2"  onchange="Permintaan.summaryEntry(this,\'jml_review\')" class="review '.$showReview.' '.$class_review.'" name="jml_review" type="text" value="'.$defaultReview.'" />': ((!is_null($defaultReview) ? $defaultReview : ''));
											$ket_review = $editReview ?('<textarea name="ket_review" rows="'.($jml_tgl_kebutuhan * 2).'" class="review '.$showReview.' '.$class_review.'">'.(!is_null($defaultKetReview) ? $defaultKetReview : '').'</textarea>') : ((!is_null($defaultKetReview) ? $defaultKetReview : ''));
											$jml_optimasi = $defaultOptimasi;

											$sampah = '';
											$adaSampah = '';
											if(!$tanggal_pertama){
													$sampah = '&nbsp;<i class="glyphicon glyphicon-trash" onclick="Permintaan.hapusTambahPakanAwal(this)" style="margin-right:-15px"></i>';
													$adaSampah = 'sampah';
											}
											echo '<tr class="'.$adaSampah.'">';
												echo '<td>'.$pertanggal['tgl_kebutuhan'].'</td>';
												echo '<td class="number" data-pakan_komposisi="'.$pertanggal['komposisi'].'" data-pakan_forecast="'.$pertanggal['forecast'].'">'.formatAngka($pertanggal['kuantitas'],3).$sampah.'</td>';
												echo '<td class="number jml_order" ><input size="2" class="required no_border '.$classInput.'" data-jmlpp="'.$pertanggal['jml_pp'].'" data-jml_asli="'.$pertanggal['jml_asli'].'" data-pengurang_pp="'.$pertanggal['pengurang_pp'].'" name="jml_order" type="text" value="'.$pertanggal['optimasi_pakan'].'" /></td>';
												echo '<td class="number jml_rekomendasi">'.$jml_rekomendasi.'</td>';
												echo '<td class="keterangan">'.$ket_rekomendasi.'</td>';
												echo '<td class="number jml_review">'.$jml_review.'</td>';
												/* rowspan keterangan ket review */
												if(!$tanggal_pertama){
													echo '<td rowspan="'.$jml_tgl_kebutuhan.'" class="ket_review"></td>';
												}

											echo '</tr>';
											$tanggal_pertama++;
										}
										echo '</tbody>';
									echo '</table>';
								}


							echo '</div>';
						echo '</td>';
					echo '</tr>';

		}
				echo '</tbody>';
			echo '</table>';
		echo '</td>';
	echo '</tr>';
}


?>
