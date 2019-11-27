<?php
	$group_warna = array(
		'#d3e800','#df8e00','#57c200','#33CCFF','#CCFF66'
	);
	$warna_kebutuhan = array();
	if($header){
		echo '<table class="kertas_kerja table-bordered table-striped custom_table">';
		echo '<thead>
			<tr>
				<th rowspan=3 class="ftl">Tanggal</th>
				<th rowspan=2 colspan=2 class="ftl">Umur</th>
				<th colspan=10>Pakan</th>
				<th rowspan=2 colspan=4>Approval/Entry</th>
				<th rowspan=3>Kirim</th>
				<th colspan=8>Performa</th>
				<th colspan=6>Rasio thd. Standar</th>
			</tr>
			<tr>
				<th colspan=5>Jantan</th>
				<th colspan=5>Betina</th>
				
				<th colspan=4>Jantan</th>
				<th colspan=4>Betina</th>
				
				<th colspan=3>Jantan</th>
				<th colspan=3>Betina</th>
			</tr>
			<tr>
				<th class="ftl">Hari</th>
				<th class="ftl">Minggu</th>
				<th class="kode_nama_pakan">Pakan</th>
				<th>SKP '.dropdownSatuan('j_skp','span.j_skp',$konversi['j_skp']).'</th>
				<th>Rekomendasi PP (Sak)</th>
				<th>PP (Sak)</th>
				<th>Kons '.dropdownSatuan('j_kons','span.j_kons',$konversi['j_kons']).'</th>
		
				<th class="kode_nama_pakan">Pakan</th>
				<th>SKP '.dropdownSatuan('b_skp','span.b_skp',$konversi['b_skp']).'</th>
				<th>Rekomendasi PP (Sak)</th>
				<th>PP (Sak)</th>
				<th>Kns '.dropdownSatuan('b_kons','span.b_kons',$konversi['b_kons']).'</th>
		
				<th class="waktu">LHK</th>
				<th>PP</th>
				<th>OP</th>
				<th>DO</th>
		
				<th>Populasi</th>
				<th>BB (g)</th>
				<th>DH (%)</th>
				<th>KE (g)</th>
			
				<th>Populasi</th>
				<th>BB (g)</th>
				<th>DH (%)</th>
				<th>KE (g)</th>
		
				<th>BB (g)</th>
				<th>DH (%)</th>
				<th>KE (g)</th>
				
				<th>BB (g)</th>
				<th>DH (%)</th>
				<th>KE (g)</th>
			</tr>
		</thead>';
		echo '<tbody>';
	}	
		$persakkg = 50;
		$persakgr = 50000;
		$b_dh = $j_dh = $b_dh_lalu = $j_dh_lalu = null;
		$b_populasi_lalu = $j_populasi_lalu = null; 
		$b_rasio_bb = $b_rasio_dh = $b_rasio_ke = $j_rasio_bb = $j_rasio_dh = $j_rasio_ke = null;
		$index_warna = 0; 
		$hari_ini_class  = $class_timbang = null;
		$umur_timbang = 0;
		$jml_warna = count($group_warna);
		$b_jumlah_awal = isset($list_kertas_kerja[0]['b_jml_awal']) ? $list_kertas_kerja[0]['b_jml_awal'] : 0;
		$j_jumlah_awal = isset($list_kertas_kerja[0]['j_jml_awal']) ? $list_kertas_kerja[0]['j_jml_awal'] : 0;
		$j_populasi_skp = 0;
		$b_populasi_skp = 0;
		
		foreach($list_kertas_kerja as $kk){
			
			$hari_ini_class = '';
			$class_timbang = '';
			$index_warna_pp = getColorIndex($kk['no_pp'],$jml_warna);
			$index_warna_pp_op = getColorIndex($kk['no_pp_op'],$jml_warna);
			$index_warna_pp_do = getColorIndex($kk['no_pp_do'],$jml_warna);
			$index_warna_pp_tgl_kirim = getColorIndex($kk['no_pp_tgl_kirim'],$jml_warna);
			$index_warna_pp_tgl_kebutuhan = getColorIndex($kk['no_pp_tgl_kebutuhan'],$jml_warna);
			
			if(!empty($kk['pp_rilis_void'])){
				$jam_rilis_div = $kk['pp_rilis_void'].'<span class="abang">!</span>';
			}
			else{
				$jam_rilis_div = $kk['jam_rilis'];
			}
			
			if($kk['hari_ini']){
				$hari_ini_class = 'hari_ini';
			}
						
			$j_kns_class = '';
			$b_kns_class = '';
			
			$warna_pp = !empty($index_warna_pp) ? $group_warna[$index_warna_pp] : '#FFFFFF';
			$warna_jam_op = !empty($index_warna_pp_op) ? $group_warna[$index_warna_pp_op] : '#FFFFFF';
			$warna_jam_do = !empty($index_warna_pp_do) ? $group_warna[$index_warna_pp_do] : '#FFFFFF';
			$warna_kirim =  !empty($index_warna_pp_tgl_kirim) ? $group_warna[$index_warna_pp_tgl_kirim] : '#FFFFFF';
			$warna_tgl_kebutuhan =  !empty($index_warna_pp_tgl_kebutuhan) ? $group_warna[$index_warna_pp_tgl_kebutuhan] : '#FFFFFF';
			
			$data_pp = !empty($kk['no_pp']) ? $kk['no_pp'] : '';
			$data_jam_op = !empty($kk['no_pp_op']) ? $kk['no_pp_op'] : '';
			$data_jam_do = !empty($kk['no_pp_do']) ? $kk['no_pp_do'] : '';
			
			$b_barang_tampil_asli = (empty($kk['pp_b_kode_barang'])) ? $kk['b_barang'] : $kk['pp_b_nama_barang'];
			$j_barang_tampil_asli = (empty($kk['pp_j_kode_barang'])) ? $kk['j_barang'] : $kk['pp_j_nama_barang'];
			
			$b_barang_tampil = (empty($kk['pp_b_kode_barang'])) ? $kk['b_barang'].' <span class="pull-right">'.$kk['b_nama_barang'] : $kk['pp_b_kode_barang'].' <span class="pull-right">'.$kk['pp_b_nama_barang'];
			$j_barang_tampil = (empty($kk['pp_j_kode_barang'])) ? $kk['j_barang'].' <span class="pull-right">'.$kk['j_nama_barang'] : $kk['pp_j_kode_barang'].' <span class="pull-right">'.$kk['pp_j_nama_barang'];
			
			$b_barang_tampil_class = ($b_barang_tampil_asli !=  $kk['b_barang']) ? 'abang' : '';
			$j_barang_tampil_class = ($j_barang_tampil_asli !=  $kk['j_barang']) ? 'abang' : '';
			
			$j_populasi_skp = empty($kk['j_jml']) ? $kk['j_jml_forecast'] : $kk['j_jml'];
			$b_populasi_skp = empty($kk['b_jml']) ? $kk['b_jml_forecast'] : $kk['b_jml'];
			
			$b_skp = round($b_populasi_skp * $kk['b_target_pakan'] / $persakgr,3);
			$j_skp = round($j_populasi_skp * $kk['j_target_pakan'] / $persakgr,3);
			
			$b_skp_str = '<span class="b_skp" data-asli="'.$b_skp.'">'.formatAngka(konversiSatuan($konversi['b_skp'],$b_skp),3).'</span>';
			$j_skp_str = '<span class="j_skp" data-asli="'.$j_skp.'">'.formatAngka(konversiSatuan($konversi['j_skp'],$j_skp),3).'</span>';
			$tgl_rhk = (empty($kk['tgl_entry_rhk'])) ? '-' : '<span class="link_span" data-no_reg="'.$kk['no_reg'].'" data-doc_in="'.$kk['tgl_doc_in'].'" data-tgl_lhk="'.$kk['tglkebutuhan'].'" onclick="KertasKerja.showLHK(this)">'.convertElemenTglWaktuIndonesia($kk['tgl_entry_rhk']).'</span>';
			/* b_pakan_pakai dalam satuan kg */
			/*
			$b_kns = (empty($kk['b_pakan_pakai'])) ? '-':  $kk['b_pakan_pakai']/$kk['b_jml']/$persakkg;
			$j_kns = (empty($kk['j_pakan_pakai'])) ? '-':  $kk['j_pakan_pakai']/$kk['j_jml']/$persakkg;
			*/
			$b_kns = (empty($kk['b_pakan_pakai'])) ? '-':  $kk['b_pakan_pakai']/$persakkg;
			$j_kns = (empty($kk['j_pakan_pakai'])) ? '-':  $kk['j_pakan_pakai']/$persakkg;
	/*		$b_kns_str = (empty($kk['b_pakan_pakai'])) ? '-':  formatAngka($b_kns,2);
			$j_kns_str = (empty($kk['j_pakan_pakai'])) ? '-':  formatAngka($j_kns,2);
	*/		
			$b_kns_str = (empty($kk['b_pakan_pakai'])) ? '-':  '<span class="b_kons" data-asli="'.$b_kns.'">'.formatAngka(konversiSatuan($konversi['b_kons'],$b_kns),2).'</span>';
			$j_kns_str = (empty($kk['j_pakan_pakai'])) ? '-':  '<span class="j_kons" data-asli="'.$j_kns.'">'.formatAngka(konversiSatuan($konversi['j_kons'],$j_kns),2).'</span>';
			/*
			$b_ke = $b_kns == '-' ? '-' : $b_kns * $persakgr /$kk['b_jml'] ;
			$j_ke = $j_kns == '-' ? '-' : $j_kns * $persakgr / $kk['j_jml'];
			*/
			$b_ke = $b_kns == '-' ? '-' : $b_kns * $persakgr /$kk['b_jml'] ;// dalam gram
			$j_ke = $j_kns == '-' ? '-' : $j_kns * $persakgr / $kk['j_jml']; // dalam gram
			
			$b_ke_str = $b_ke == '-' ? '-': ($b_ke > $kk['b_target_pakan'] ? '<span class="abang" data-toogle="tooltip" data-original-title=" Std KE = '.$kk['b_target_pakan'].'">'.formatAngka($b_ke,2).'</span>' : formatAngka($b_ke,2));
			$j_ke_str = $j_ke == '-'  ? '-': ($j_ke > $kk['j_target_pakan'] ? '<span class="abang" data-toogle="tooltip" data-original-title=" Std KE = '.$kk['j_target_pakan'].'">'.formatAngka($j_ke,2).'</span>' : formatAngka($j_ke,2));
			
			
			
			if(empty($kk['tgl_entry_rhk'])){
				$b_rasio_bb = '-';
				$b_rasio_dh = '-';
				$b_rasio_ke = '-';
				$j_rasio_bb = '-';
				$j_rasio_dh = '-';
				$j_rasio_ke = '-';
				$b_dh_str =  '-';
				$j_dh_str =  '-';
				$b_bb_rhk_str = '-';
				$j_bb_rhk_str = '-';
				$class_j_bb = '';
				$class_b_bb = '';
				$class_b_ke = '';
				$class_j_ke = '';
				$class_b_rasiodh = '';
				$class_j_rasiodh = '';
				$class_b_dh = '';
				$class_j_dh = '';
				
			}
			else{
				
				$b_dh = (!empty($kk['b_dh'])) ? $kk['b_dh']: null;
				$j_dh = (!empty($kk['j_dh'])) ? $kk['j_dh']: null;
				$b_dh_str = (!empty($b_dh)) ? formatAngka($b_dh,2).' %' : '-';
				$j_dh_str = (!empty($j_dh)) ? formatAngka($j_dh,2).' %' : '-';
				$b_target_pakan = $kk['b_target_pakan'];
				$j_target_pakan = $kk['b_target_pakan'];
				$b_target_bb = $kk['b_target_bb'];
				$j_target_bb = $kk['j_target_bb'];
				$b_dh_prc = $kk['b_dh_prc']/100;
				$j_dh_prc = $kk['j_dh_prc']/100;
				
				$class_b_ke = $b_ke > $b_target_pakan ? 'abang' : '';
				$class_j_ke = $j_ke > $j_target_pakan ? 'abang' : '';
				
				$b_rasio_dh = formatAngka(rasioDh($b_dh,$b_dh_prc,$kk['hari']),2).' %';
				$b_rasio_ke = $b_ke_str == '-' ? '-' : formatAngka(($b_ke / $b_target_pakan) * 100,2).' %';
				$j_rasio_dh = formatAngka(rasioDh($j_dh,$j_dh_prc,$kk['hari']),2).' %';
				$j_rasio_ke = $j_ke_str == '-' ? '-' : formatAngka(($j_ke / $j_target_pakan) * 100,2).' %';
				
				$class_j_dh = $j_dh < $kk['j_dh_prc'] ? 'abang' : '';
				$class_b_dh = $b_dh < $kk['b_dh_prc'] ? 'abang' : '';
				$class_j_rasiodh = $j_dh < $kk['j_dh_prc'] ? 'abang' : '';  
				$class_b_rasiodh = $b_dh < $kk['b_dh_prc'] ? 'abang' : '';
				
				$b_bb_rhk_str = (empty($kk['b_bb_rhk']) || ($kk['b_bb_rhk'] <= 0)) ? '-' : formatAngka($kk['b_bb_rhk'],2).' %';
				$j_bb_rhk_str = (empty($kk['j_bb_rhk']) || ($kk['j_bb_rhk'] <= 0)) ? '-' : formatAngka($kk['j_bb_rhk'],2).' %';
				$class_b_bb = !empty($kk['b_bb_rhk']) && $kk['b_bb_rhk'] != $b_target_bb ? 'abang' : '';
				$class_j_bb = !empty($kk['j_bb_rhk']) && $kk['j_bb_rhk'] != $j_target_bb ? 'abang' : '';
				
				$b_rasio_bb = $b_bb_rhk_str == '-' ? '-' : formatAngka(($kk['b_bb_rhk']/$b_target_bb) * 100,2).' %';
				$j_rasio_bb = $j_bb_rhk_str == '-' ? '-' : formatAngka(($kk['j_bb_rhk']/$j_target_bb) * 100,2).' %';
				 
				
				/* jika b_bb_rhk > 0 */
				if(($kk['b_bb_rhk'] > 0) && ($kk['j_bb_rhk'] > 0)) {
					$umur_timbang = $kk['hari'];
				}	
				/* jika kons +- 5% dari skp jadikan merah 
				$batas_kons = 0.05;
				if($b_kns != '-'){
					$max_selisih = $b_skp * $batas_kons;
					if(abs($b_kns - $b_skp) > $max_selisih){
						$b_kns_class = 'abang';
					} 
				}
				if($j_kns != '-'){
					$max_selisih = $j_skp * $batas_kons;
					if(abs($j_kns - $j_skp) > $max_selisih){
						$j_kns_class = 'abang';
					}	
				}
				*/
			}
			if($kk['hari'] == $umur_timbang){
				$class_timbang = 'tebal';
				$umur_timbang += 7;
			}
			
			$hari_libur = $kk['libur'] ? 'abang':'';
			$class_jpp_str = !empty($kk['pp_jantan']) && ($kk['pp_jantan'] != $kk['j_rekomendasi_pp']) ? 'abang' : '' ;
			$class_bpp_str = !empty($kk['pp_betina']) && ($kk['pp_betina'] != $kk['b_rekomendasi_pp']) ? 'abang' : '' ;
			$pp_jantan = empty($kk['pp_jantan']) ? '-': '<span data-jk="J" data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" class="link_span '.$class_jpp_str.'" onclick="KertasKerja.riwayatPP(this)">'.$kk['pp_jantan'].'</span>';
			$j_rekomendasi_pp = empty($kk['j_rekomendasi_pp']) ? '-' : $kk['j_rekomendasi_pp'];
			$pp_betina = empty($kk['pp_betina']) ? '-': '<span data-jk="B"  data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" class="link_span '.$class_bpp_str.'" onclick="KertasKerja.riwayatPP(this)">'.$kk['pp_betina'].'</span>';
			$b_rekomendasi_pp = empty($kk['b_rekomendasi_pp']) ? '-' : $kk['b_rekomendasi_pp'];
			echo '<tr data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">
				<td class="ftl '.$hari_libur.' '.$hari_ini_class.' '.$class_timbang.'">'.tglIndonesia($kk['tglkebutuhan'],'-',' ').'</td>';
			if($kk['hari'] >= 0){
					echo ' 	
					<td class="ftl text-center '.$hari_ini_class.' '.$class_timbang.'">'.$kk['hari'].'</td>';
				if($kk['hari'] % 7 == 0){
					echo '<td rowspan=7 class="ftl text-center">'.$kk['umur_minggu'].'</td>';
				}			
					
				echo '					
					<td class="'.$j_barang_tampil_class.'">'.$j_barang_tampil.'</span></td>
					<td data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" style="background-color:'.$warna_tgl_kebutuhan.'" class="number">'.$j_skp_str.'</td>
					<td class="number">'.$j_rekomendasi_pp.'</td>
					<td class="number">'.$pp_jantan.'</td>
					<td class="number '.$j_kns_class.'">'.$j_kns_str.'</td>
							
					<td class="'.$b_barang_tampil_class.'">'.$b_barang_tampil.'</span></td>
					<td data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" style="background-color:'.$warna_tgl_kebutuhan.'" class="number">'.$b_skp_str.'</td>
					<td class="number">'.$b_rekomendasi_pp.'</td>
					<td class="number">'.$pp_betina.'</td>
					<td class="number '.$b_kns_class.'">'.$b_kns_str.'</td>
	
					<td>'.$tgl_rhk.'</td>';
			}
			else{
				$index_hari_minggu = array(1,2);
				for($i = 0; $i < 13 ; $i++){
					if(in_array($i,$index_hari_minggu)){
						echo '<td class="ftl diagonal_line"><div class="line" /></td>';
					}
					else{
						echo '<td class="diagonal_line"><div class="line" /></td>';
					}
					
				}
				
			}	
			echo '
				<td style="background-color:'.$warna_pp.'" class="td_pp" data-no_pp="'.$data_pp.'">'.$jam_rilis_div.'</td>
				<td style="background-color:'.$warna_jam_op.'" class="td_pp" data-no_pp="'.$data_jam_op.'">'.$kk['jam_op'].'</td>		
				<td style="background-color:'.$warna_jam_do.'" class="td_do" data-no_pp="'.$data_jam_do.'">'.$kk['jam_do'].'</td>
				<td style="background-color:'.$warna_kirim.'" class="td_kirim number" data-no_pp="'.$kk['no_pp_tgl_kirim'].'">'.$kk['total_kirim'].'</td>				
			';
			if($kk['hari'] >= 0){
			echo '	
				<td class="number">'.angkaRibuan($kk['j_jml']).'</td>
				<td class="number '.$class_j_bb.'">'.$j_bb_rhk_str.'</td>
				<td class="number '.$class_j_dh.'">'.$j_dh_str.'</td>
				<td class="number">'.$j_ke_str.'</td>

				<td class="number">'.angkaRibuan($kk['b_jml']).'</td>
				<td class="number '.$class_b_bb.'">'.$b_bb_rhk_str.'</td>
				<td class="number '.$class_b_dh.'">'.$b_dh_str.'</td>
				<td class="number">'.$b_ke_str.'</td>

				<td class="number '.$class_j_bb.'">'.$j_rasio_bb.'</td>
				<td class="number '.$class_j_rasiodh.'">'.$j_rasio_dh.'</td>
				<td class="number">'.$j_rasio_ke.'</td>

				<td class="number '.$class_b_bb.'">'.$b_rasio_bb.'</td>
				<td class="number '.$class_b_rasiodh.'">'.$b_rasio_dh.'</td>
				<td class="number '.$class_b_ke.'">'.$b_rasio_ke.'</td>';
			}
			else{
				for($i = 0; $i < 14 ; $i++){
					echo '<td class="diagonal_line"><div class="line" /></td>';
				}
			}

			echo '</tr>';
		
			
		}
	if($header){
		echo '</tbody>';
		echo '</table>';
		
	}	
?>	
						
		






			