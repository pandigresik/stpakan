<table class="table table-bordered custom_table" >
	<thead>
			<tr>
				<th class="" rowspan="3">Tanggal DOC In</th>
			  <th class="" rowspan="3">Populasi <br> Awal</th>
				<th class="tanggal" rowspan="3">Tgl.</th>
				<th class="" rowspan="3">Umur (hari)</th>
				<th class="screen_2" colspan="12">Performa</th>
				<th class="screen_2" colspan="6">Rasio Performa thd. Standar</th>
				<th class="screen_3" colspan="6">Pertanggungjawaban Retur</th>
				<th class="screen_3" colspan="15">Kontrol Pakan</th>
				<th class="screen_3" rowspan="3">Catatan</th>
			</tr>
			<tr>
				<th class="screen_2" colspan="3">Deplesi</th>
				<th class="screen_2" rowspan="2">Pop. <br /> (ekor)</th>
				<th class="screen_2" style="background-color:pink" rowspan="2">Panen <br /> (ekor)</th>
				<th class="screen_2 dh" rowspan="2">DH</th>
				<th class="screen_2" rowspan="2">Kons. (gr)</th>
				<th class="screen_2" rowspan="2">Kons. Kum (gr)</th>
				<th class="screen_2" rowspan="2">BB (gr)</th>
				<th class="screen_2" rowspan="2">ADG (gr/hr)</th>
				<th class="screen_2" rowspan="2">FCR</th>
				<th class="screen_2" rowspan="2">IP</th>

				<th class="screen_2 dh" rowspan="2">DH</th>
				<th class="screen_2" rowspan="2">Deplesi</th>
				<th class="screen_2" rowspan="2">Kons.</th>
				<th class="screen_2" rowspan="2">BB</th>
				<th class="screen_2" rowspan="2">ADG</th>
				<th class="screen_2" rowspan="2">FCR</th>
				<th class="screen_3 nama_bentuk" rowspan="2">Nama Pakan</th>

				<th class="screen_3" colspan="2">Pakan Rusak</th>
				<th class="screen_3" colspan="3">Sak Kosong</th>

				<th class="screen_3" colspan="2">Stok Awal</th>
				<th class="screen_3" colspan="2">Kebutuhan</th>
				<th class="screen_3">Keb. PP</th>
				<th class="screen_3" colspan="4">Pakai</th>
				<th class="screen_3" colspan="2">Terima</th>
				<th class="screen_3" colspan="2">Stok Akhir</th>
				<th class="screen_3" colspan="2">Sisa Akhir - Keb H+1</th>
			</tr>
			<tr>
				<th class="screen_2">Mati</th>
				<th class="screen_2">Afkir</th>
				<th class="screen_2">%</th>

				<th class="screen_3">Dikembalikan</th>
				<th class="screen_3">Diganti</th>

				<th class="screen_3">Dikembalikan</th>
				<th class="screen_3">Terhutang</th>
				<th class="screen_3">Sisa</th>

				<th class="screen_3">Kg</th>
				<th class="screen_3">Sak</th>

				<th class="screen_3">Kg</th>
				<th class="screen_3">Sak</th>

				<th class="screen_3">Sak</th>

				<th class="screen_3">Kg</th>
				<th class="screen_3">Kum</th>
				<th class="screen_3">Sak</th>
				<th class="screen_3">Kum</th>

				<th class="screen_3">Kg</th>
				<th class="screen_3">Sak</th>

				<th class="screen_3">Kg</th>
				<th class="screen_3">Sak</th>

				<th class="screen_3">Kg</th>
				<th class="screen_3">Sak</th>
			</tr>
		</thead>
		<tbody>
		<?php
	foreach($farms as $f => $farm){
		echo '<tr style="background-color:gray"><td colspan="4"><strong>Farm '.$nama_farm[$f].'('.$f.' '.count($farm).' Kandang )</strong></td><td colspan="18" class="screen_2"></td><td colspan="20" class="screen_3"></td></tr>';
		foreach($farm as $kd => $data){
			$rhk = $data['rhk'];
			$jml_panen = $data['jml_panen'];
			$c_jumlah = $data['c_jumlah'];
			$p_pakan = $data['p_pakan'];
			$r_pakan = $data['r_pakan'];
			$r_sakterakhir = $data['r_sakterakhir'];
			$tgl_docin = $data['tgl_docin'];
			$populasi_awal = $data['populasi_awal'];
			$jml_baris = $data['jml_baris'];
			$approval_pp = $data['approval_pp'];
		echo '<tr style="background-color:green"><td colspan="4"><strong>'.$kd.'</strong></td><td colspan="18" class="screen_2"></td><td colspan="20" class="screen_3"></td></tr>';
		if(!empty($rhk)){
			$header_bulan = null;
			$bulan_ini = null;
			$index_bulan = 0;
			$next_index_bulan = 0;
			$berat_persak = 50;
			$summary_pakan = array(
				'keb_kg' => 0,
				'keb_sak' => 0,
				'pakai_kg' => 0,
				'pakai_sak' => 0,
				'keb_sak_pp' => 0
			);

			$berat_pakai_kum = 0;
			$sak_pakai_kum_perpakan = 0;
			$berat_pakai_kum_perpakan = 0;

            	/* kesimpulan panen */
            $umur_panen = 0;
            $total_panen = 0;
            $bb_rata_panen = null;
            $dh_panen = '-';
            $bb_panen = 0 ;
            $fcr_panen = '';
            $ip_panen = '';
            $selisih_panen = '';
            if(count($jml_panen)){
                $up = 0;
                $bp = 0; /* bb panen */
                foreach($jml_panen as $u => $jp){
                    $up += ($u * $jp['total']);
                    $bp += $jp['bb'];
                    $total_panen += $jp['total'];
                }
                $bb_rata_panen = ($bp / $total_panen) * 1000;
                $umur_panen = $up / $total_panen;
                $bb_panen = $bp;
                $dh_panen = $total_panen / $populasi_awal['stok_awal'] * 100;
                $selisih_panen = $populasi_awal['stok_awal'] - $total_panen ;
            }
			$_totalBaris = $jml_baris + 2;

			$_counter = 0;
			foreach($rhk as $tgl =>$r){
				$tgl_selanjutnya = new \DateTime($tgl);
				$tgl_selanjutnya->add(new \DateInterval('P1D'));
				$hari_berikutnya = $tgl_selanjutnya->format('Y-m-d');
				$bookmark = (($r['hari'])% 7 == 0) ? 'rekap' : '';
				$span_bookmark = '';
				if($bookmark == 'rekap'){
			//		$span_bookmark = '<span class="bookmark">Minggu '.((int)$r['hari']/7 - 1).'</span>';
				}

				echo '<tr class="'.$bookmark.'">';
				$jkl = strtolower($r['jk']);
				$jml = $jkl.'_jumlah';

				$persenmati = $r['deplesi'] > 0 ? ($r['deplesi'] / ($r['awal']) * 100) : 0;
				$brt_pakai_ekor = $r['jml'] > 0 ? $r['brt_pakai']/$r['jml'] : 0 ;
				$berat_pakai_kum += $r['brt_pakai'];
				$berat_pakai_kum_perekor = $r['jml'] > 0 ? formatAngka($berat_pakai_kum / $r['jml'],3) : 0;
				$warna_ke_class = $brt_pakai_ekor > $r['pkn_std'] ? 'ijo' : 'abang';
				$brt_pakai = $brt_pakai_ekor != $r['pkn_std'] ? '<a href="#" class="'.$warna_ke_class.'" data-toogle="tooltip" data-original-title="'.$r['pkn_std'].'">'.formatAngka($brt_pakai_ekor,3).'</a>' : formatAngka($brt_pakai_ekor,3);
				$warna_bb = !empty($r['berat_badan']) && ($r['berat_badan'] > $r['bb_std']) ? 'ijo' : 'abang';
				$brt_bb = (!empty($r['berat_badan']) && ($r['berat_badan']  != $r['bb_std'])) ? '<a href="#" class="'.$warna_bb.'" data-toogle="tooltip" data-original-title="'.$r['bb_std'].'">'.formatAngka($r['berat_badan'] ,3).'</a>' : (!empty($r['berat_badan']) ? formatAngka($r['berat_badan'] ,3) : '-');
				$fcr_str = $r['fcr'] == '-' ? $r['fcr'] : ($r['fcr'] != $r['fcr_std'] ? ($r['fcr'] < $r['fcr_std'] ? '<a href="#" class="ijo" data-toogle="tooltip" data-original-title="'.$r['fcr_std'].'">'.formatAngka($r['fcr'],3).'</a>' : '<a href="#" class="abang" data-toogle="tooltip" data-original-title="'.$r['fcr_std'].'">'.formatAngka($r['fcr'],3).'</a>') : $r['fcr']);
				$ip_str = $r['ip'] == '-' ? $r['ip'] : ($r['ip'] != $r['ip_std'] ? ($r['ip'] > $r['ip_std'] ? '<a href="#" class="ijo" data-toogle="tooltip" data-original-title="'.$r['ip_std'].'">'.$r['ip'].'</a>' : '<a href="#" class="abang" data-toogle="tooltip" data-original-title="'.$r['ip_std'].'">'.$r['ip'].'</a>') : $r['ip']);
				$dh = $r['dh'];
				$dh_class = $dh > $r['dh_std'] ? 'ijo' : 'abang';
				$dh_str = $dh != $r['dh_std'] ? '<a href="#" class="'.$dh_class.'" data-toogle="tooltip" data-original-title="'.$r['dh_std'].'">'.formatAngka($dh,2).'%</a>' : formatAngka($dh,2).'%';
				$t_rowspan = count($p_pakan[$tgl][$r['jk']]);
				$prod_str = !empty($r['produksi']) ? formatAngka($r['produksi'],0) : '-';

				/* rasio terhadap standart */
				$dh_rasio = !is_null($r['dh_std']) ? formatAngka($r['dh'] / $r['dh_std'] * 100,2).'%' : '-';
				$deplesi_rasio = $r['deplesi'] > 0 ? formatAngka($persenmati/ $r['deplesi_std'] * 100,2).'%' : '0%';
				$kons_rasio = !is_null($r['pkn_std']) ? formatAngka($brt_pakai_ekor / $r['pkn_std'] * 100,2).'%' : '-';
				$bb_rasio = $r['berat_badan'] > 0 ?  formatAngka($r['berat_badan'] / $r['bb_std'] * 100,2).'%' : '-';
				$fcr_rasio = $r['fcr'] != '-' ?  formatAngka($r['fcr'] / $r['fcr_std']  * 100,2).'%' : $r['fcr'];

				$warna_rasio_adg = !empty($r['adg']) && ($r['adg'] > $r['adg_std']) ? 'ijo' : 'abang';
				$adg_rasio = $r['adg'] != '-' && $r['adg_std'] > 0 ?  formatAngka($r['adg'] / $r['adg_std'] * 100,2).'%' : '-';
				$adg_rasio_str = (!empty($r['adg']) && ($r['adg']  != $r['adg_std'])) ? '<a href="#" class="'.$warna_rasio_adg.'" data-toogle="tooltip" data-original-title="'.formatAngka($r['adg_std'],3).'">'.$adg_rasio.'</a>' : (!empty($r['adg']) ? $adg_rasio : '-');

				$adg_str = $r['adg'] != '-' ? formatAngka($r['adg'],3) : $r['adg'];

				$c_panen = isset($jml_panen[$r['hari']]) ? formatAngka($jml_panen[$r['hari']]['total'],0) : '-';
				if(!$_counter){
					echo '<td rowspan="'.$_totalBaris.'" class="text-center tanggal">'.$tgl_docin.'</td>';
					echo '<td rowspan="'.$_totalBaris.'" class="text-center tanggal">'.formatAngka($populasi_awal['stok_awal'],0).'</td>';
					$_counter++;
				}
				echo '<td rowspan="'.$t_rowspan.'" class="text-center tanggal">'.tglIndonesia($tgl,'-',' ').$span_bookmark.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$r['hari'].'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$r['mati'] .'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$r['afkir'] .'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.formatAngka($persenmati,3) .'%</td>';
				echo '<td style=\'mso-number-format:"#.##0"\' rowspan="'.$t_rowspan.'"  class="screen_2 number">'.formatAngka($r['jml'],0).'</td>';
				echo '<td style=\'mso-number-format:"#.##0";background-color:pink\' rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$c_panen.'</td>';
				echo '<td style=\'mso-number-format:"#.##0,00"\' rowspan="'.$t_rowspan.'"  class="screen_2 number dh">'.$dh_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$brt_pakai.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$berat_pakai_kum_perekor.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$brt_bb.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$adg_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$fcr_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$ip_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$dh_rasio.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$deplesi_rasio.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$kons_rasio.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$bb_rasio.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$adg_rasio_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="screen_2 number">'.$fcr_rasio.'</td>';

					/* kontrol pakan */
				$_p = 0;
				$tglnya = $r['tgl'];
				
				foreach($p_pakan[$tglnya][$r['jk']] as $kb => $z){
					if($_p > 0){
						echo '<tr class="'.$bookmark.'">';
					}
					$berat_pakai_kum_perpakan += $z['brt_pakai'];
					$sak_pakai_kum_perpakan += $z['jml_pakai'];
					$jml_awal = ($z['jml_akhir'] + $z['jml_pakai']) - $z['jml_terima'];
					$brt_awal = ($z['brt_akhir'] + $z['brt_pakai']) - $z['brt_terima'];
					/* stok akhir - kebutuhan hari selanjutnya */

					if(isset($p_pakan[$hari_berikutnya][$r['jk']][$kb]) && isset($rhk[$hari_berikutnya])){
						$brt_sisa_pakai = $z['brt_akhir'] - (($p_pakan[$hari_berikutnya][$r['jk']][$kb]['komposisi_pakan'] * $rhk[$hari_berikutnya]['pkn_std'] * $rhk[$hari_berikutnya]['jml']) / 1000);
						$jml_sisa_pakai = $z['jml_akhir'] - (($p_pakan[$hari_berikutnya][$r['jk']][$kb]['komposisi_pakan'] * $rhk[$hari_berikutnya]['pkn_std'] * $rhk[$hari_berikutnya]['jml']) / 50000);
					}
					else{
						$brt_sisa_pakai = $z['brt_akhir'];
						$jml_sisa_pakai = $z['jml_akhir'];
					}
					$class_sisa_pakai = $brt_sisa_pakai < 0 ? 'abang' : '';
					$brt_awal = formatAngka($brt_awal,3);

					$keb_kg = ($z['komposisi_pakan'] * $r['jml'] * $r['pkn_std']) / 1000;
					$keb_sak = $keb_kg / $berat_persak;
					$keb_sak_pp = 0;
					$class_keb_sak_pp = "";
					if(isset($approval_pp[$tglnya])){
						$keb_sak_pp = 1;
						if(isset($approval_pp[$tglnya][$r['jk']])){
							if(isset($approval_pp[$tglnya][$r['jk']][$kb])){
								$keb_sak_pp = $approval_pp[$tglnya][$r['jk']][$kb]['jml_order'];
								$class_keb_sak_pp = $approval_pp[$tglnya][$r['jk']][$kb]['class_elm'];
							}
						}	
					}
					$class_stok_akhir = '';
					if($z['jml_akhir'] <= 0){
						if($z['brt_akhir'] > 0){
							$class_stok_akhir = 'abang';
						}
					}
					else{
						$class_stok_akhir = !beratDalamStandar($z['jml_akhir'],$z['brt_akhir']) ? 'abang' : '';
					}

					echo '<td class="screen_3 nama_bentuk">'.$z['nama_pakan'].'</td>';

					echo '<td class="screen_3 number">'.$z['pakan_retur'].'</td>';
					echo '<td class="screen_3 number">'.$z['pakan_diganti'].'</td>';
					echo '<td class="screen_3 number">'.$z['sak_retur'].'</td>';
					echo '<td class="screen_3 number">'.$z['sak_hutang'].'</td>';
					echo '<td class="screen_3 number">'.$z['sisa_hutang'].'</td>';

					echo '<td class="screen_3 number">'.$brt_awal.'</td>';
					echo '<td class="screen_3 number">'.$jml_awal.'</td>';

					echo '<td class="screen_3 number">'.formatAngka($keb_kg,3).'</td>';
					echo '<td class="screen_3 number">'.formatAngka($keb_sak,3).'</td>';

					echo '<td class="screen_3 number '.$class_keb_sak_pp.'">'.formatAngka($keb_sak_pp,0).'</td>';

					echo '<td class="screen_3 number">'.formatAngka($z['brt_pakai'],3).'</td>';
					echo '<td class="screen_3 number">'.formatAngka($berat_pakai_kum_perpakan,3).'</td>';
					echo '<td class="screen_3 number">'.$z['jml_pakai'].'</td>';
					echo '<td class="screen_3 number">'.formatAngka($sak_pakai_kum_perpakan,0).'</td>';

					echo '<td class="screen_3 number">'.formatAngka($z['brt_terima'],3).'</td>';
					echo '<td class="screen_3 number">'.$z['jml_terima'].'</td>';

					echo '<td class="screen_3 number '.$class_stok_akhir.'">'.formatAngka($z['brt_akhir'],3).'</td>';
					echo '<td class="screen_3 number '.$class_stok_akhir.'">'.$z['jml_akhir'].'</td>';

					echo '<td class="screen_3 number '.$class_sisa_pakai.'">'.number_format($brt_sisa_pakai,3,',','.').'</td>';
					echo '<td class="screen_3 number '.$class_sisa_pakai.'">'.number_format($jml_sisa_pakai,3,',','.').'</td>';

					echo '<td class="screen_3"></td>';

					$summary_pakan['keb_kg'] += $keb_kg;
					$summary_pakan['keb_sak'] += $keb_sak;
					$summary_pakan['pakai_kg'] += $z['brt_pakai'];
					$summary_pakan['pakai_sak'] += $z['jml_pakai'];
					$summary_pakan['keb_sak_pp'] += $keb_sak_pp;
					if($_p > 0){
						echo '</tr>';
					}
					$_p++;
				}
				echo '</tr>';
			}

            if($bb_panen > 0){
                $fcr_panen = round($summary_pakan['pakai_kg'] / $bb_panen , 3);
                $ip_panen = formatAngka((($dh_panen * (round($bb_rata_panen / 1000,2)) * 100) / ($fcr_panen * $umur_panen )),0);
                $fcr_panen = formatAngka($fcr_panen,3);
                $dh_panen = formatAngka($dh_panen,2);
            }
			echo '<tr class="rekap panen">
								<td colspan="2"></td>
								<td class="screen_2"></td>
								<td class="screen_2" colspan="2"><strong>Total Panen</strong></td>
                <td class="screen_2 text-right">'.formatAngka($total_panen,0).'</td>
                <td class="screen_2 text-right">'.$dh_panen.'</td>
                <td class="screen_2" colspan="2"></td>
                <td class="screen_2 text-right">'.formatAngka($bb_rata_panen,3).'</td>
                <td class="screen_2"></td>
                <td class="screen_2 text-right">'.$fcr_panen.'</td>
                <td class="screen_2 text-right">'.$ip_panen.'</td>
                <td colspan="6" class="screen_2 text-right"></td>
								<td colspan="8" class="screen_3 text-right"></td>
								<td class="screen_3 number">'.formatAngka($summary_pakan['keb_kg'],3).'</td>
								<td class="screen_3 number">'.formatAngka($summary_pakan['keb_sak'],3).'</td>
								<td class="screen_3 number">'.formatAngka($summary_pakan['keb_sak_pp'],0).'</td>
								<td class="screen_3 number">'.formatAngka($summary_pakan['pakai_kg'],3).'</td>
                <td class="screen_3 "></td>
								<td class="screen_3 number">'.formatAngka($summary_pakan['pakai_sak'],0).'</td>
								<td class="screen_3" colspan="10"></td>
			</tr>';
            	echo '<tr class="rekap panen">
							<td colspan="2"></td>
							<td class="screen_2"></td>
							<td class="screen_2" colspan="2"><strong>Selisih Panen</strong></td>
              <td class="screen_2 text-right" style="text-align:right">'.formatAngka($selisih_panen,0).'</td>
							<td class="screen_2" colspan="18"></td>
							<td class="screen_3" colspan="16"></td>
			</tr>';
		}
	}
}
		?>
		</tbody>
	</table>
	<div class="btn prev slider-table" data-current="2" data-min="2" data-max="3" onclick="Rhk.prev(this)"> <i class="glyphicon glyphicon-chevron-left"></i> </div>
	<div class="btn next slider-table" data-current="2" data-min="2" data-max="3" onclick="Rhk.next(this)"> <i class="glyphicon glyphicon-chevron-right"></i> </div>
