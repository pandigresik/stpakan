<?php

	$group_warna = array(
		'#d3e800','#df8e00','#57c200','#33CCFF','#CCFF66'
	);
	$warna_kebutuhan = array();
	if($header){
			
		echo '<table class="kertas_kerja table-bordered table-striped custom_table">';
		echo '<thead>
			<tr>
				<th rowspan=2 class="ftl">Tanggal</th>
				<th colspan=2 class="ftl">Umur </th>				
		<!--		
				<span class="pull-right" onclick="KertasKerja.showHideColumn(this,\'.lhk.waktu\')"><i class="hide_column glyphicon glyphicon-minus-sign"></i></span>
				<th class="ftl nama_pakan" rowspan=2>Nama Pakan <span class="pull-right" onclick="KertasKerja.showHideColumn(this,\'.lhk.waktu\')"><i class="hide_column glyphicon glyphicon-minus-sign"></i></span></th>
		-->		
				<th rowspan=2 class="ftl lhk waktu">Jam Entry LHK</th>
				<th rowspan=2 class="ftl">Jenis Kelamin</th>
				<th colspan=2 class="">Pakan</th> 
				<th rowspan=2>SKP '.dropdownSatuan('skp','span.skp',$konversi['skp']).'</th>
				<th rowspan=2>Rekomendasi PP (Sak)</th>
				<th rowspan=2>PP (Sak)<span class="pull-right" onclick="KertasKerja.showHideColumn(this,\'.time_pp_do.waktu\')"><i class="hide_column glyphicon glyphicon-minus-sign"></i></span></th>
								
				<th rowspan=3 class="waktu time_pp_do">Tanggal/Jam Approval PP</th>
				<th rowspan=3 class="waktu time_pp_do">Tanggal/Jam Entry DO</th>  
				
				<th rowspan=2>Kirim (Sak)<span class="pull-right" onclick="KertasKerja.showHideColumn(this,\'.timeline2.waktu\')"><i class="hide_column glyphicon glyphicon-minus-sign"></i></span></th>
				
				<th rowspan=2 class="waktu timeline2">Tanggal Rencana Kirim</th>
				<th class="waktu timeline2" colspan=2>Muat Pakan dari FM</th>
				<th class="waktu timeline2" colspan=2>Penerimaan Pakan di Gudang</th>		

				<th rowspan=2>Kons '.dropdownSatuan('kons','span.kons',$konversi['kons']).'</th>
				<th colspan=4>Performa</th>
				<th colspan=3>Rasio Performa Vs Standar</th>
			</tr>
			
			<tr>
				<th class="ftl">Hari</th>
				<th class="ftl">Minggu</th>
				<th>Kode</th>
				<th class="nama_pakan">Nama</th>
				<th class="waktu timeline2">Tanggal / Jam</th>
				<th class="waktu timeline2">Rit Tersisa</th>
				
				<th class="waktu timeline2">Tanggal / Jam</th>
				<th class="waktu timeline2">Status</th>

				<th>Populasi</th>
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
		$grouping_perbaris = array('j','b');
		$persakkg = 50;
		$persakgr = 50000;
		$tot_perminggu = array('j' => array('skp'=> 0,'pp' => 0,'kons' => 0,'rekomendasi_pp'=> 0),'b' => array('skp'=> 0,'pp' => 0,'kons'=> 0,'rekomendasi_pp'=> 0));
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
		$rowspan_timeline_pp = 2;
		foreach($list_kertas_kerja as $kk){
			
			$hari_ini_class = '';
			$class_timbang = '';
			$index_warna_pp = getColorIndex($kk['no_pp'],$jml_warna);
			$index_warna_pp_op = getColorIndex($kk['no_pp_op'],$jml_warna);
			$index_warna_pp_do = getColorIndex($kk['no_pp_do'],$jml_warna);
			$index_warna_pp_tgl_kirim = getColorIndex($kk['no_pp_tgl_kirim'],$jml_warna);
			$index_warna_pp_tgl_kebutuhan = getColorIndex($kk['no_pp_tgl_kebutuhan'],$jml_warna);
			
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
			
			
			$b_barang_tampil = (empty($kk['pp_b_kode_barang'])) ? $kk['b_barang'] : $kk['pp_b_kode_barang'];
			$j_barang_tampil = (empty($kk['pp_j_kode_barang'])) ? $kk['j_barang'] : $kk['pp_j_kode_barang'];
			$b_nama_barang_tampil = (empty($kk['pp_b_kode_barang'])) ? $kk['b_nama_barang'] : $kk['pp_b_nama_barang'];
			$j_nama_barang_tampil = (empty($kk['pp_j_kode_barang'])) ? $kk['j_nama_barang'] : $kk['pp_j_nama_barang'];
			
			$b_barang_tampil_class = ($b_barang_tampil !=  $kk['b_barang']) ? 'abang' : '';
			$j_barang_tampil_class = ($j_barang_tampil !=  $kk['j_barang']) ? 'abang' : '';

			$j_populasi_skp = empty($kk['j_jml']) ? $kk['j_jml_forecast'] : $kk['j_jml'];
			$b_populasi_skp = empty($kk['b_jml']) ? $kk['b_jml_forecast'] : $kk['b_jml'];
			
			$b_skp = round($b_populasi_skp * $kk['b_target_pakan'] / $persakgr,3);
			$j_skp = round($j_populasi_skp * $kk['j_target_pakan'] / $persakgr,3);
	
			$b_skp_str = '<span class="skp" data-asli="'.$b_skp.'">'.formatAngka(konversiSatuan($konversi['skp'],$b_skp),3).'</span>';
			$j_skp_str = '<span class="skp" data-asli="'.$j_skp.'">'.formatAngka(konversiSatuan($konversi['skp'],$j_skp),3).'</span>';
			
			
			$tgl_rhk = (empty($kk['tgl_entry_rhk'])) ? '-' : '<span class="link_span" data-no_reg="'.$kk['no_reg'].'" data-doc_in="'.$kk['tgl_doc_in'].'" data-tgl_lhk="'.$kk['tglkebutuhan'].'" onclick="KertasKerja.showLHK(this)">'.convertElemenTglWaktuIndonesia($kk['tgl_entry_rhk']).'</span>';
			/* b_pakan_pakai dalam satuan kg */
			/*
			$b_kns = (empty($kk['b_pakan_pakai'])) ? '-':  $kk['b_pakan_pakai']/$kk['b_jml']/$persakkg;
			$j_kns = (empty($kk['j_pakan_pakai'])) ? '-':  $kk['j_pakan_pakai']/$kk['j_jml']/$persakkg;
			*/
			$b_kns = (empty($kk['b_pakan_pakai'])) ? '-':  $kk['b_pakan_pakai'] / $persakkg;
			$j_kns = (empty($kk['j_pakan_pakai'])) ? '-':  $kk['j_pakan_pakai'] / $persakkg;
			$b_kns_str = (empty($kk['b_pakan_pakai'])) ? '-':  '<span class="kons" data-asli="'.$b_kns.'">'.formatAngka(konversiSatuan($konversi['kons'],$b_kns),2).'</span>';
			$j_kns_str = (empty($kk['j_pakan_pakai'])) ? '-':  '<span class="kons" data-asli="'.$j_kns.'">'.formatAngka(konversiSatuan($konversi['kons'],$j_kns),2).'</span>';
			/*
			$b_ke = $b_kns == '-' ? '-' : $b_kns * $persakgr /$kk['b_jml'] ;
			$j_ke = $j_kns == '-' ? '-' : $j_kns * $persakgr / $kk['j_jml'];
			*/
			$b_ke = $b_kns == '-' ? '-' : $b_kns * $persakgr / $kk['b_jml'] ;// dalam gram
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
				$class_b_ke = '';
				$class_j_ke = '';
				$class_b_bb = '';
				$class_j_bb = '';
				$class_b_dh = '';
				$class_j_dh = '';
				$class_b_rasiodh = '';
				$class_j_rasiodh = '';
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
				$class_j_dh = $kk['j_dh'] < $kk['j_dh_prc'] ? 'abang' : '';
				$class_b_dh = $kk['b_dh'] < $kk['b_dh_prc'] ? 'abang' : '';
				$class_j_rasiodh = $kk['j_dh'] < $kk['j_dh_prc'] ? 'abang' : '';
				$class_b_rasiodh = $kk['b_dh'] < $kk['b_dh_prc'] ? 'abang' : '';
				
				$b_rasio_dh = formatAngka(rasioDh($b_dh,$b_dh_prc,$kk['hari']),2).' %';
				$b_rasio_ke = $b_ke_str == '-' ? '-' : formatAngka(($b_ke / $b_target_pakan) * 100,2).' %';
				$j_rasio_dh = formatAngka(rasioDh($j_dh,$j_dh_prc,$kk['hari']),2).' %';
				$j_rasio_ke = $j_ke_str == '-' ? '-' : formatAngka(($j_ke / $j_target_pakan) * 100,2).' %';
				
				$b_bb_rhk_str = (empty($kk['b_bb_rhk']) || ($kk['b_bb_rhk'] <= 0)) ? '-' : formatAngka($kk['b_bb_rhk'],2).' %';
				$j_bb_rhk_str = (empty($kk['j_bb_rhk']) || ($kk['j_bb_rhk'] <= 0)) ? '-' : formatAngka($kk['j_bb_rhk'],2).' %';
				
				$b_rasio_bb = $b_bb_rhk_str == '-' ? '-' : formatAngka(($kk['b_bb_rhk']/$b_target_bb) * 100,2).' %';
				$j_rasio_bb = $j_bb_rhk_str == '-' ? '-' : formatAngka(($kk['j_bb_rhk']/$j_target_bb) * 100,2).' %';
				
				$class_b_bb = !empty($kk['b_bb_rhk']) && $kk['b_bb_rhk'] != $b_target_bb ? 'abang' : '';
				$class_j_bb = !empty($kk['j_bb_rhk']) && $kk['j_bb_rhk'] != $j_target_bb ? 'abang' : '';
				/* jika b_bb_rhk > 0 */
				if(($kk['b_bb_rhk'] > 0) && ($kk['j_bb_rhk'] > 0)) {
					$umur_timbang = $kk['hari'];
				}	
			
			}
			if($kk['hari'] == $umur_timbang){
				$class_timbang = 'tebal';
				$umur_timbang += 7;
			}
			
			$hari_libur = $kk['libur'] ? 'abang':'';
			
			$class_jpp_str = !empty($kk['pp_jantan']) && ($kk['pp_jantan'] != $kk['j_rekomendasi_pp']) ? 'abang' : '' ;
			$class_bpp_str = !empty($kk['pp_betina']) && ($kk['pp_betina'] != $kk['b_rekomendasi_pp']) ? 'abang' : '' ;
			
			$pp_jantan = empty($kk['pp_jantan']) ? '-': '<span data-jk="J"  data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" class="link_span '.$class_jpp_str.'" onclick="KertasKerja.riwayatPP(this)">'.$kk['pp_jantan'].'</span>';
		//	$pp_jantan = empty($kk['pp_jantan']) ? '-': $kk['pp_jantan'];
			$pp_betina = empty($kk['pp_betina']) ? '-': '<span data-jk="B"  data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" class="link_span '.$class_bpp_str.'" onclick="KertasKerja.riwayatPP(this)">'.$kk['pp_betina'].'</span>';
		//	$pp_betina = empty($kk['pp_betina']) ? '-': $kk['pp_betina'];
			$j_rekomendasi_pp = empty($kk['j_rekomendasi_pp']) ? '-' : $kk['j_rekomendasi_pp'];
			$b_rekomendasi_pp = empty($kk['b_rekomendasi_pp']) ? '-' : $kk['b_rekomendasi_pp'];
			/* cari nilai total perminggu */
			$tot_perminggu['j']['skp'] += $j_skp;
			$tot_perminggu['j']['pp'] += $kk['pp_jantan'];
			$tot_perminggu['j']['kons'] += $j_kns;
			$tot_perminggu['j']['rekomendasi_pp'] += $kk['j_rekomendasi_pp'];
			$tot_perminggu['b']['skp'] += $b_skp;
			$tot_perminggu['b']['pp'] += $kk['pp_betina'];
			$tot_perminggu['b']['kons'] += $b_kns;
			$tot_perminggu['b']['rekomendasi_pp'] += $kk['b_rekomendasi_pp'];
			
			foreach($grouping_perbaris as $index_jk){
				if($index_jk == 'j'){
					$rowspan_tgl = 2;
					if($kk['hari'] < 0){
						$rowspan_tgl = 1;
					}
					echo '<tr data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">
					<td rowspan="'.$rowspan_tgl.'" class="ftl '.$hari_libur.' '.$hari_ini_class.' '.$class_timbang.'">'.tglIndonesia($kk['tglkebutuhan'],'-',' ').'</td>';
					
						if($kk['hari'] >= 0){
							echo '
						<td rowspan=2 class="ftl text-center '.$hari_ini_class.' '.$class_timbang.'">'.$kk['hari'].'</td>';
							if($kk['hari'] % 7 == 0){
								echo '<td rowspan=14 class="ftl text-center">'.$kk['umur_minggu'].'</td>';
							}
						
						echo '
						<td rowspan=2 class="ftl lhk waktu">'.$tgl_rhk.'</td>		
						<td class="ftl">Jantan</td>		
						<td class="'.$j_barang_tampil_class.'">'.$j_barang_tampil.'</td>
						<td class="nama_pakan">'.$j_nama_barang_tampil.'</td>	
								
						<td data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" style="background-color:'.$warna_tgl_kebutuhan.'" class="number">'.$j_skp_str.'</td>
						<td class="number">'.$j_rekomendasi_pp.'</td>						
						<td class="number '.$class_jpp_str.'">'.$pp_jantan.'</td>';
						
						echo '
							<td class="waktu time_pp_do" rowspan="'.$rowspan_timeline_pp.'" data-pekan="'.$kk['umur_minggu'].'" data-col="app" data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">'.convertElemenTglWaktuIndonesia($kk['approve_pp']).'</td>
							<td class="waktu time_pp_do" rowspan="'.$rowspan_timeline_pp.'" data-pekan="'.$kk['umur_minggu'].'" data-col="edo" data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">'.convertElemenTglWaktuIndonesia($kk['entry_do']).'</td>';
								
						echo '
						<td data-no_pp="'.$kk['no_pp_tgl_kirim'].'" rowspan=2 style="background-color:'.$warna_kirim.'"  class="number">'.$kk['total_kirim'].'</td>';
								
						
							
						echo '
							<td class="waktu timeline2" rowspan="'.$rowspan_timeline_pp.'" data-pekan="'.$kk['umur_minggu'].'" data-col="rk"  data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">'.convertElemenTglWaktuIndonesia($kk['rencana_kirim']).'</td>
							<td class="waktu timeline2" rowspan="'.$rowspan_timeline_pp.'" data-pekan="'.$kk['umur_minggu'].'" data-col="sj"  data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">'.convertElemenTglWaktuIndonesia($kk['sj_terakhir']).'</td>
							<td class="waktu timeline2" rowspan="'.$rowspan_timeline_pp.'" data-pekan="'.$kk['umur_minggu'].'" data-col="sdo" data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">'.$kk['sisa_do'].'</td>
							<td class="waktu timeline2" rowspan="'.$rowspan_timeline_pp.'" data-pekan="'.$kk['umur_minggu'].'" data-col="ttk"  data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">'.convertElemenTglWaktuIndonesia($kk['terima_terakhir']).'</td>
							<td class="waktu timeline2" rowspan="'.$rowspan_timeline_pp.'" data-pekan="'.$kk['umur_minggu'].'" data-col="spn"  data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">'.$kk['status_penerimaan'].'</td>';
									
												
					echo '	
						<td class="number '.$j_kns_class.'">'.$j_kns_str.'</td>
				
					';
						if($kk['hari'] >= 0){
							echo '
							<td class="number">'.angkaRibuan($kk['j_jml']).'</td>
							<td class="number '.$class_j_bb.'">'.$j_bb_rhk_str.'</td>
							<td class="number '.$class_j_dh.'">'.$j_dh_str.'</td>
							<td class="number">'.$j_ke_str.'</td>
					
							<td class="number '.$class_j_bb.'">'.$j_rasio_bb.'</td>
							<td class="number '.$class_j_rasiodh.'">'.$j_rasio_dh.'</td>
							<td class="number '.$class_j_ke.'">'.$j_rasio_ke.'</td>
					
							';
						}
						else{
							for($i = 0; $i < 7 ; $i++){
								echo '<td class="diagonal_line"><div class="line" /></td>';
							}
						}
								
					}
					else{
						$index_hari_minggu = array(0,1,2,3);
						for($i = 0; $i < 25 ; $i++){
							if(in_array($i,$index_hari_minggu)){
								echo '<td class="ftl diagonal_line"><div class="line" /></td>';
							}
							else{
								echo '<td class="diagonal_line"><div class="line" /></td>';
							}
								
						}
					
					}
					
				}
				else{
					if($kk['hari'] >= 0){
						/* tutup dulu tag baris */
						echo '</tr>';
						echo '<tr>';
						echo '
						<td class="ftl">Betina</td>		
						<td class="'.$b_barang_tampil_class.'">'.$b_barang_tampil.'</td>			
						<td class="nama_pakan">'.$b_nama_barang_tampil.'</td>
				
						<td data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" style="background-color:'.$warna_tgl_kebutuhan.'" class="number">'.$b_skp_str.'</td>
						<td class="number">'.$b_rekomendasi_pp.'</td>
						<td class="number '.$class_bpp_str.'">'.$pp_betina.'</td>
						<td class="number '.$b_kns_class.'">'.$b_kns_str.'</td>
					
					';
						if($kk['hari'] >= 0){
							echo '
							<td class="number">'.angkaRibuan($kk['b_jml']).'</td>
							<td class="number '.$class_b_bb.'">'.$b_bb_rhk_str.'</td>
							<td class="number '.$class_b_dh.'">'.$b_dh_str.'</td>
							<td class="number">'.$b_ke_str.'</td>
									
							<td class="number '.$class_b_bb.'">'.$b_rasio_bb.'</td>
							<td class="number '.$class_b_rasiodh.'">'.$b_rasio_dh.'</td>
							<td class="number '.$class_b_ke.'">'.$b_rasio_ke.'</td>
									
							';
						}
						else{
							for($i = 0; $i < 8 ; $i++){
								echo '<td class="diagonal_line"><div class="line" /></td>';
							}
						}
						
					}
					echo '</tr>';
				}
				
				
			}
			/* buat rekap perminggu */
			if($kk['hari'] % 7 == 6 && $kk['hari'] >= 0){
				echo '<tr class="rekap">';
				echo '<td colspan="5" class="ftl number">Subtotal Jantan</td>';
				echo '<td colspan="2"></td>';
				echo '<td class="number"><span class="skp" data-asli="'.$tot_perminggu['j']['skp'].'">'.formatAngka($tot_perminggu['j']['skp'],3).'</span></td>';
				echo '<td class="number">'.$tot_perminggu['j']['rekomendasi_pp'].'</td>';
				echo '<td class="number">'.$tot_perminggu['j']['pp'].'</td>';
				echo '<td colspan="8" rowspan="2"></td>';
				echo '<td class="number"><span class="kons" data-asli="'.$tot_perminggu['j']['kons'].'">'.formatAngka($tot_perminggu['j']['kons'],3).'</span></td>';
				echo '<td colspan="7"></td>';
				echo '</tr>';
				echo '<tr class="ftl rekap">';
				echo '<td colspan="5" class="ftl number">Subtotal Betina</td>';
				echo '<td colspan="2"></td>';
				echo '<td class="number"><span class="skp" data-asli="'.$tot_perminggu['b']['skp'].'">'.formatAngka($tot_perminggu['b']['skp'],3).'</span></td>';
				echo '<td class="number">'.$tot_perminggu['b']['rekomendasi_pp'].'</td>';
				echo '<td class="number">'.$tot_perminggu['b']['pp'].'</td>';
				
				echo '<td class="number"><span class="kons" data-asli="'.$tot_perminggu['b']['kons'].'">'.formatAngka($tot_perminggu['b']['kons'],3).'</span></td>';
				echo '<td colspan="7"></td>';
				echo '</tr>';
				/* reset nilainya */
				$tot_perminggu['j']['skp'] = 0;
				$tot_perminggu['j']['pp'] = 0;
				$tot_perminggu['j']['kons'] = 0;
				$tot_perminggu['j']['rekomendasi_pp'] = 0;
				$tot_perminggu['b']['skp'] = 0;
				$tot_perminggu['b']['pp'] = 0;
				$tot_perminggu['b']['kons'] = 0;
				$tot_perminggu['b']['rekomendasi_pp'] = 0;
			}
			
			
			
		}
	if($header){
		echo '</tbody>';
		echo '</table>';
		
	}	
?>	
						
		






			