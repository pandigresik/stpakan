<?php

	$group_warna = array(
		'#a3e800','#dfF600','#57A200','#33CCFF','#CAFF66','#BADD66'
	);
	$warna_kebutuhan = array();

		echo '<table class="kertas_kerja table-bordered table-striped custom_table">';
		echo '<thead>
			<tr>
				<th rowspan=2 class="ftl">Tanggal</th>
				<th colspan=2 class="ftl">Umur</th>
				<th rowspan="2" class="ftl hide"></th>
				<th colspan=5>Pakan</th>
				<th colspan=4>Approval/Entry</th>
				<th rowspan=2>Kirim</th>
				<th colspan=5>Performa</th>
				<th colspan=4>Rasio thd. Standar</th>
			</tr>

			<tr>
				<th class="ftl">Hari</th>
				<th class="ftl">Minggu</th>
				<th class="kode_nama_pakan">Pakan</th>
				<th>SKP '.dropdownSatuan('c_skp','span.c_skp',$konversi['c_skp']).'</th>
				<th>Rekomendasi PP (Sak)</th>
				<th>PP (Sak)</th>
				<th>Kons '.dropdownSatuan('c_kons','span.c_kons',$konversi['c_kons']).'</th>

				<th class="waktu">LHK</th>
				<th>PP</th>
				<th>OP</th>
				<th>DO</th>


				<th>Populasi (ekor)</th>
				<th>BB (g)</th>
				<th>DH (%)</th>
				<th>KE (g)</th>
				<th>ADG</th>

				<th>BB</th>
				<th>DH</th>
				<th>KE</th>
				<th>ADG</th>
			</tr>
		</thead>';
		echo '<tbody>';

		$persakkg = 50;
		$persakgr = 50000;
		$dh = $dh = $dh_lalu = $dh_lalu = null;
		$populasi_lalu = null;
		$rasio_bb = $rasio_dh = $rasio_ke = $rasio_bb = $rasio_dh = $rasio_ke = null;
		$index_warna = 0;
		$hari_ini_class  = $class_timbang = null;
		$umur_timbang = 0;
		$jml_warna = count($group_warna);
		$timbang_sebelumnya = null;
		foreach($list_kertas_kerja as $kk){
			$hari_libur = $kk['libur'] ? 'abang':'';
			$hari_ini_class = $kk['hari_ini'] ? 'kuning' : '';

			$index_warna_pp = getColorIndex($kk['no_pp'],$jml_warna);
			$index_warna_pp_op = getColorIndex($kk['no_pp_op'],$jml_warna);
			$index_warna_pp_do = getColorIndex($kk['no_pp_do'],$jml_warna);
			$index_warna_pp_tgl_kirim = getColorIndex($kk['no_pp_tgl_kirim'],$jml_warna);
			$index_warna_pp_tgl_kebutuhan = getColorIndex($kk['no_pp_tgl_kebutuhan'],$jml_warna);

			if(!empty($kk['ref_id'])){
				$jam_rilis_div = $kk['jam_rilis'].'<span class="abang">!</span>';
			}
			else{
				$jam_rilis_div = $kk['jam_rilis'];
			}

			$data_pp = !empty($kk['no_pp']) ? $kk['no_pp'] : '';
			$data_jam_op = !empty($kk['no_pp_op']) ? $kk['no_pp_op'] : '';
			$data_jam_do = !empty($kk['no_pp_do']) ? $kk['no_pp_do'] : '';

			$warna_pp = !is_null($index_warna_pp) ? $group_warna[$index_warna_pp] : '#FFFFFF';
			$warna_jam_op = !is_null($index_warna_pp_op) ? $group_warna[$index_warna_pp_op] : '#FFFFFF';
			$warna_jam_do = !is_null($index_warna_pp_do) ? $group_warna[$index_warna_pp_do] : '#FFFFFF';
			$warna_kirim =  !is_null($index_warna_pp_tgl_kirim) ? $group_warna[$index_warna_pp_tgl_kirim] : '#FFFFFF';
			$warna_tgl_kebutuhan =  !is_null($index_warna_pp_tgl_kebutuhan) ? $group_warna[$index_warna_pp_tgl_kebutuhan] : '#FFFFFF';

			$barang_tampil = $kk['kode_barang'].'<span class="pull-right">'.$kk['nama_barang'].'</span>';
			$populasi_skp = empty($kk['jumlah']) ? (empty($populasi_lalu) ? $kk['jml_populasi'] : $populasi_lalu) : $kk['jumlah'];
			$populasi_lalu = $populasi_skp;

			$skp = !empty($kk['jml_performance']) ? $kk['jml_performance'] : round($populasi_skp * $kk['std_pakan'] / $persakgr,3);
			$skp_str = '<span class="c_skp" data-asli="'.$skp.'">'.formatAngka(konversiSatuan($konversi['c_skp'],$skp),3).'</span>';

			$kns = (empty($kk['brt_pakai'])) ? '-':  $kk['brt_pakai']/$persakkg;
			$dh = (!empty($kk['daya_hidup'])) ? $kk['daya_hidup']: null;

			$target_bb = $kk['target_bb'];
			$bb_rhk_str = (empty($kk['berat_badan']) || ($kk['berat_badan'] <= 0)) ? '-' : formatAngka($kk['berat_badan'],3);

			$class_bb = !empty($kk['berat_badan']) && $kk['berat_badan'] < $target_bb ? 'abang' : '';
			$rasio_bb = $bb_rhk_str == '-' ? '-' : formatAngka(($kk['berat_badan']/$target_bb) * 100,3).' %';

			$dh_str = (!empty($dh)) ? formatAngka($dh,2): '-';
			$rasio_dh = $dh_str == '-' ? '-' : formatAngka(($dh / $kk['dh_kum_prc']) * 100,2).' %';


			$ke = $kns == '-' ? '-' : $kns * $persakgr /$populasi_skp ;// dalam gram
			$ke_str = $ke == '-' ? '-': ($ke > $kk['std_pakan'] ? '<span class="abang" data-toogle="tooltip" data-original-title=" Std KE = '.$kk['std_pakan'].'">'.formatAngka($ke,3).'</span>' : formatAngka($ke,3));
			$class_ke = $ke > $kk['std_pakan'] ? 'abang' : '';
			$rasio_ke = $ke_str == '-' ? '-' : formatAngka(($ke / $kk['std_pakan']) * 100,3).' %';

			$kns_str = (empty($kk['brt_pakai'])) ? '-':  '<span class="c_kons" data-asli="'.$kns.'">'.formatAngka(konversiSatuan($konversi['c_kons'],$kns),3).'</span>';
			$tgl_rhk = (empty($kk['tgl_entry_rhk'])) ? '-' : '<span class="link_span" data-no_reg="'.$noreg.'" data-doc_in="'.$docin.'" data-tgl_lhk="'.$kk['tglkebutuhan'].'" onclick="KertasKerja.showLHKBdy(this)">'.convertElemenTglWaktuIndonesia($kk['tgl_entry_rhk']).'</span>';
			$adg = '-';
			$rasio_adg = '-';
			if(!empty($kk['berat_badan'])){
				if(!is_null($timbang_sebelumnya)){
					$adg =  hitungADG($kk['berat_badan'],$kk['umur'],$timbang_sebelumnya['bblalu'],$timbang_sebelumnya['umurlalu']);
				}
				$timbang_sebelumnya = array('bblalu' => $kk['berat_badan'],'umurlalu'=>$kk['umur']);
			}
			$adg_str = ($adg == '-') ? $adg : formatAngka($adg,3);

			$rekomendasi_pp = !empty($kk['rekomendasi']) ? ceil($kk['rekomendasi']) : null;
			$rekomendasi_pp_str = !empty($rekomendasi_pp) ? $rekomendasi_pp : '-';
			$class_pp_str = !empty($kk['jml_pp']) && ($kk['jml_pp'] != $rekomendasi_pp) ? 'abang' : '' ;
			$pp = empty($kk['jml_pp']) ? '-': '<span data-jk="C" data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" class="link_span '.$class_pp_str.'" onclick="KertasKerja.riwayatPP(this)">'.$kk['jml_pp'].'</span>';
			$class_timbang = '';
			if(!empty($kk['berat_badan'])){
				$umur_timbang = $kk['umur'];
			}
			if($umur_timbang == $kk['umur']){
				$class_timbang = 'tebal';
				$umur_timbang += 7;
			}


			echo '<tr data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'">
				<td class="ftl '.$hari_libur.' '.$hari_ini_class.' '.$class_timbang.'">'.tglIndonesia($kk['tglkebutuhan'],'-',' ').'</td>';
			if($kk['umur'] > 0){
					echo '
					<td class="ftl text-center '.$hari_ini_class.' '.$class_timbang.'">'.$kk['umur'].'</td>';
				if(($kk['umur'] - 1) % 7 == 0){
					echo '<td rowspan=7 class="ftl text-center">'.(int)($kk['umur']/7).'</td>';
				}

				echo '
					<td class="ftl hide"></td>
					<td>'.$barang_tampil.'</span></td>
					<td data-no_pp="'.$kk['no_pp_tgl_kebutuhan'].'" style="background-color:'.$warna_tgl_kebutuhan.'" class="number">'.$skp_str.'</td>
					<td class="number">'.$rekomendasi_pp_str.'</td>
					<td class="number">'.$pp.'</td>
					<td class="number">'.$kns_str.'</td>
					<td>'.$tgl_rhk.'</td>';
			}
			else{
				$index_hari_minggu = array(1,3);
				for($i = 0; $i < 8 ; $i++){
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
			if($kk['umur'] > 0){
				echo '
				<td class="number">'.formatAngka($populasi_skp,0).'</td>
				<td class="number '.$class_bb.'">'.$bb_rhk_str.'</td>
				<td class="number">'.$dh_str.'</td>
				<td class="number">'.$ke_str.'</td>
				<td class="number">'.$adg_str.'</td>
				<td class="number">'.$rasio_bb.'</td>
				<td class="number">'.$rasio_dh.'</td>
				<td class="number '.$class_ke.'">'.$rasio_ke.'</td>
				<td class="number"></td>';

			}
			else{
				for($i = 0; $i < 9 ; $i++){
					echo '<td class="diagonal_line"><div class="line" /></td>';
				}
			}
			echo '</tr>';
		}

		echo '</tbody>';
		echo '</table>';


?>
