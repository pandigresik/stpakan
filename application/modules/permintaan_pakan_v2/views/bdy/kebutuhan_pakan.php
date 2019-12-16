				<?php
					$setMaxReview = isset($set_max_review) ? $set_max_review : 1; 	
//					log_message('error',$setMaxReview);							
					if(!empty($kebutuhan_pakan)){						
						$readonly_rekomendasi = !$edit_rekomendasi ? 'readonly' : '';
						$readonly_review = !$edit_review ? 'readonly' : '';
						$requiered_review = $edit_review ? 'required' : '';
						
						foreach($kebutuhan_pakan as $kb => $perbarang){														
							$pertanggal = $perbarang['pertanggal'];
							$rowspan = count($pertanggal);							
							$index = 0;
							$class_pakan_tambahan = '';	
							$summary = $perbarang['summary'];
							$review_kb = isset($review[$kb]) ? $review[$kb] : array();							
							$classAdg = '';
							$adgDibawahStandart = 0;
							if(isset($summary['adg'])){
								if($summary['adg'] < $summary['adg_standart']){
									$classAdg = 'abang';
									$adgDibawahStandart = 1;
								}
							}
							foreach($pertanggal as $p){
								$maxJumlahReview = 1000;
								
								$tgl_keb = $p['tgl_asli'];
								$jmlRekomendasi = isset($review_kb[$tgl_keb]) ? $review_kb[$tgl_keb]['JML_REKOMENDASI'] : '';								
								$jmlReview = isset($review_kb[$tgl_keb]) ? $review_kb[$tgl_keb]['JML_REVIEW'] : '';
								$ketRekomendasi = isset($review_kb[$tgl_keb]) ? $review_kb[$tgl_keb]['KET_REKOMENDASI'] : '';								
								$ketReview = isset($review_kb[$tgl_keb]) ? $review_kb[$tgl_keb]['KET_REVIEW'] : '';
								if($edit_review){
									if(empty($jmlRekomendasi)){
										$jmlRekomendasi = 0;
										$jmlReview = 0;
									}else{
										/** ini sebagai nilai default */
										$jmlReview = $jmlRekomendasi;
									}
									if(empty($ketRekomendasi)){
										$ketRekomendasi = 'keterangan generate by system';
									}
								}
								
								if(isset($pakan_tambahan)){									
									$class_pakan_tambahan = 'pakan_tambahan';																		
								}
								if($p['forecast'] <= 0){									
									$class_pakan_tambahan = 'pakan_tambahan';																		
								}

								/** jika bukan pakan tambahan, maka set maksimal review */
								if(empty($class_pakan_tambahan) && $setMaxReview){
									if($adgDibawahStandart){
										$maxJumlahReview = $p['rekomendasi_pp'] + floor($p['rekomendasi_pp'] * .5);
									}else{
										$maxJumlahReview = $p['rekomendasi_pp'] + floor($p['kuantitas'] * .5);
									}
								}

								$input_kf = '<input class="required '.$class_pakan_tambahan.'" '.$readonly_rekomendasi.' name="jml_rekomendasi" size="1" type="text" data-max="'.$p['rekomendasi_pp'].'"  value="'.$jmlRekomendasi.'" />';
								$review_kdp = $show_review ? '<input class="'.$requiered_review.'" '.$readonly_review.' name="jml_review" size="1" type="text" value="'.$jmlReview.'"  data-max="'.$maxJumlahReview.'" />' : '';
								$keterangan_kf = '<textarea name="ket_rekomendasi" class="required" '.$readonly_rekomendasi.' cols="10" rows="6" data-minlength="10" maxlength="60">'.$ketRekomendasi.'</textarea>';
								$keterangan_kdp = $show_review ? '<textarea name="ket_review" class="'.$requiered_review.'" '.$readonly_review.' cols="10" rows="6" data-minlength="10"  data-maxlength="1000">'.$ketReview.'</textarea>' : '';

								echo '<tr data-kode_barang="'.$kb.'" class="'.$class_pakan_tambahan.'" ondblclick="Permintaan.pilihPakanTambahan(this)">';
								if(!$index){
									echo '<td class="kode_barang" data-kode_barang="'.$kb.'" rowspan="'.$rowspan.'">'.$summary['nama_barang'].'</td>';
									echo '<td rowspan="'.$rowspan.'">'.angkaRibuan($summary['populasi']).'</td>';
									echo '<td class="lhk" data-lhk_terakhir="'.$summary['lhk_terakhir'].'" rowspan="'.$rowspan.'">'.tglIndonesia($summary['lhk_terakhir'],'-',' ').'</td>';
								}
								echo '<td>'.$p['umur'].'</td>';
								echo '<td data-tgl_kebutuhan="'.$p['tgl_asli'].'" class="tgl_kebutuhan">'.$p['tgl_kebutuhan'].'</td>';
								echo '<td>'.$p['kuantitas'].'</td>';
																
								if(!$index){
									//echo '<td class="sisa_gudang" rowspan="'.$rowspan.'">'.$summary['sisa_gudang'].'</td>';
									echo '<td class="sisa_kandang" rowspan="'.$rowspan.'">'.$summary['sisa_kandang'].'</td>';									
									echo '<td class="'.$classAdg.'" rowspan="'.$rowspan.'">'.(!empty($summary['adg']) ? formatAngka($summary['adg'] ,0) : '').'</td>';
									echo '<td class="'.$classAdg.'" rowspan="'.$rowspan.'">'.(!empty($summary['adg_standart']) ? formatAngka(($summary['adg'] / $summary['adg_standart']) * 100 ,2) : '').' %</td>';
									echo '<td rowspan="'.$rowspan.'">'.(!empty($summary['kons']) ? formatAngka($summary['kons'],0) : '').'</td>';
									echo '<td rowspan="'.$rowspan.'">'.(!empty($summary['kons_standart']) ? formatAngka(($summary['kons'] / $summary['kons_standart']) * 100 ,2) : '').' %</td>';
								}
								
							//	echo '<td class="pakan_farm_lain">'.$p['pakan_farm_lain'].'</td>';
								echo '<td class="rekomendasi_pp" data-komposisi="'.$p['komposisi'].'" data-pengurang_pp="'.$p['pengurang_pp'].'" data-jml_pp_asli="'.$p['jml_asli'].'" data-pengurang_pp="'.$p['pengurang_pp'].'" data-forecast="'.$p['forecast'].'">'.$p['rekomendasi_pp'].'</td>';
								echo '<td>'.$input_kf.'</td>';
								echo '<td>'.$keterangan_kf.'</td>';								
								echo '<td>'.$review_kdp.'</td>';
								if(!$index){
									echo '<td rowspan="'.$rowspan.'">'.$keterangan_kdp.'</td>';									
								}
								echo '</tr>';
								$index++;
							}
						}
					}
				?>		