<table class="table table-bordered custom_table" >
	<thead>
			<tr>
				<th class="tanggal" rowspan="3">Tanggal DOC-In</th>
				<th class="" rowspan="3">Hatchery</th>
				<th class="" rowspan="3">Berat DOC</th>
				<th class="" rowspan="3">Populasi Awal</th>
				<th class="" rowspan="3">Umur (hari)</th>
				<th class="tanggal" rowspan="3">Tgl.</th>
				<th class="screen_2" colspan="4">Deplesi</th>
				<th class="screen_2" rowspan="3">Pop. <br /> (ekor)</th>
				<th class="screen_2 dh" colspan="3">Daya Hidup</th>
				<th class="screen_2" colspan="5">Pakan</th>
				<th class="screen_3" colspan="3">Berat Badan</th>
				<th class="screen_3" colspan="3">ADG</th>
				<th class="screen_3" colspan="3">FCR</th>
				<th class="screen_3" colspan="3">IP</th>
			</tr>
			<tr>
				<th class="screen_2" colspan="2">Mati</th>
				<th class="screen_2" colspan="2">Afkir</th>
				<th class="screen_2" rowspan="2">Real (%)</th>
				<th class="screen_2" rowspan="2">Std (%)</th>
				<th class="screen_2" rowspan="2">(%) dr Std</th>
				<th class="screen_2" colspan="2">Pakai</th>
				<th class="screen_2" rowspan="2">Kumulatif (gr)</th>
				<th class="screen_2" rowspan="2">Std (gr)</th>
				<th class="screen_2" rowspan="2">(%) dr Std</th>
				<th class="screen_3" rowspan="2">Real (gr)</th>
				<th class="screen_3" rowspan="2">Std (gr)</th>
				<th class="screen_3" rowspan="2">(%) dr Std</th>
				<th class="screen_3" rowspan="2">Real (gr)</th>
				<th class="screen_3" rowspan="2">Std (gr)</th>
				<th class="screen_3" rowspan="2">(%) dr Std</th>
				<th class="screen_3" rowspan="2">Real</th>
				<th class="screen_3" rowspan="2">Std</th>
				<th class="screen_3" rowspan="2">(%) dr Std</th>
				<th class="screen_3" rowspan="2">Real</th>
				<th class="screen_3" rowspan="2">Std</th>
				<th class="screen_3" rowspan="2">(%) dr Std</th>
			</tr>
			<tr>
				<th class="screen_2">Ekor</th>
				<th class="screen_2">%</th>
				<th class="screen_2">Ekor</th>
				<th class="screen_2">%</th>
				<th class="screen_2">Sak</th>
				<th class="screen_2">gr</th>
			</tr>
		</thead>
		<tbody>
			<?php
		foreach($farms as $f => $farm){
			echo '<tr style="background-color:gray"><td colspan="4"><strong>Farm '.$nama_farm[$f].'('.$f.' '.count($farm).' Kandang )</strong></td><td colspan="13" class="screen_2"></td><td colspan="12" class="screen_3"></td></tr>';
			foreach($farm as $kd => $data){
				$rhk = $data['rhk'];
				$jml_panen = $data['jml_panen'];
				$c_jumlah = $data['c_jumlah'];
				$p_pakan = $data['p_pakan'];
				$r_pakan = $data['r_pakan'];
				$r_sakterakhir = $data['r_sakterakhir'];
				$tgl_docin = $data['tgl_docin'];
				$terimadocin = $data['terimadocin'];
				$jml_baris = intval(count($rhk) / 7);
			echo '<tr style="background-color:green"><td colspan="4"><strong>'.$kd.'</strong></td><td colspan="13" class="screen_2"></td><td colspan="12" class="screen_3"></td></tr>';

				if(!empty($rhk)){
					$berat_persak = 50;
					$berat_pakai_kum = 0;
					$sak_pakai_kum_perpakan = 0;
					$berat_pakai_kum_perpakan = 0;
					$mati = $afkir = $pakai_sak = $pakai_kg = $pakai_kum = 0;
					$_counter = 0;

					foreach($rhk as $tgl =>$r){
						$pakai_kum += $r['brt_pakai'];
						//$_counter = 0;
						if(($r['hari']% 7) == 0){
							$mati += $r['mati'];
							$afkir += $r['afkir'];
							$pakai_sak += $r['jml_pakai'];
							$pakai_kg += $r['brt_pakai'];
						echo '<tr>';
						$jkl = strtolower($r['jk']);
						$jml = $jkl.'_jumlah';

						$persenmati = $mati > 0 ? ($mati / ($r['awal']) * 100) : 0;
						$persenafkir = $afkir > 0 ? ($afkir / ($r['awal']) * 100) : 0;
						if($r['hari'] <= 7){
								$afkir = '-';
								$persenafkir = '-';
							}
						$persenafkir_str = ($persenafkir == '-') ? $persenafkir : formatAngka($persenafkir,3);

						/* rasio terhadap standart */
						$dh_rasio = formatAngka($r['dh'] / $r['dh_std'] * 100,2);
						$pakai_gr_perekor = $pakai_kg / $r['jml'];
						$pakai_kumgr_perekor = $pakai_kum / $r['jml'];
						$pkn_rasio = formatAngka(round($pakai_kumgr_perekor) / $r['pkn_kum_std'] * 100,3);
						$bb_rasio = $r['berat_badan'] > 0 ?  formatAngka($r['berat_badan'] / $r['bb_std'] * 100,3) : '-';
						$adg_rasio = $r['adg'] != '-' && $r['adg_std'] > 0 ?  formatAngka($r['adg'] / $r['adg_std'] * 100,3) : '-';
						$fcr_rasio = $r['fcr'] != '-' ?  formatAngka($r['fcr'] / $r['fcr_std']  * 100,3) : $r['fcr'];
						$ip_rasio = $r['ip'] != '-' ?  formatAngka($r['ip'] / $r['ip_std_umur']  * 100,3) : $r['ip'];
						if(!$_counter){
							$_totalBaris = $jml_baris;
							echo '<td rowspan="'.$_totalBaris.'" class="text-center tanggal">'.$tgl_docin.'</td>';
							echo '<td rowspan="'.$_totalBaris.'" class="text-center">'.$terimadocin['nama_hatchery'].'</td>';
							echo '<td rowspan="'.$_totalBaris.'" class="text-center">'.formatAngka($terimadocin['bb_rata2'],2).'</td>';
							echo '<td rowspan="'.$_totalBaris.'" class="text-center">'.formatAngka($terimadocin['stok_awal'],0).'</td>';
						}
						echo '<td  class="number ftl">'.$r['hari'].'</td>';
						echo '<td class="text-center tanggal ftl">'.tglIndonesia($tgl,'-',' ').'</td>';

						echo '<td  class="number ">'.$mati .'</td>';
						echo '<td  class="number ">'.formatAngka($persenmati,3) .'</td>';
						echo '<td  class="number ">'.$afkir .'</td>';
						echo '<td  class="number ">'.$persenafkir_str.'</td>';
						echo '<td  class="number dh">'.formatAngka($r['jml'],0).'</td>';
						echo '<td  class="number dh">'.formatAngka($r['dh'],2).'</td>';
						echo '<td  class="number dh">'.formatAngka($r['dh_std'],2).'</td>';
						echo '<td  class="number">'.$dh_rasio.'</td>';
						echo '<td  class="number">'.$pakai_sak.'</td>';
						echo '<td  class="number">'.formatAngka($pakai_gr_perekor,3).'</td>';
						echo '<td  class="number">'.formatAngka($pakai_kumgr_perekor,0).'</td>';
						echo '<td  class="number">'.$r['pkn_kum_std'].'</td>';
						echo '<td  class="number">'.$pkn_rasio.'</td>';
						echo '<td  class="number">'.$r['berat_badan'].'</td>';
						echo '<td  class="number">'.formatAngka($r['bb_std'],0).'</td>';
						echo '<td  class="number">'.$bb_rasio.'</td>';
						echo '<td  class="number">'.formatAngka($r['adg'],1).'</td>';
						echo '<td  class="number">'.formatAngka($r['adg_std'],0).'</td>';
						echo '<td  class="number">'.$adg_rasio.'</td>';
						echo '<td  class="number">'.formatAngka($r['fcr'],3).'</td>';
						echo '<td  class="number">'.formatAngka($r['fcr_std'],3).'</td>';
						echo '<td  class="number">'.$fcr_rasio.'</td>';
						echo '<td  class="number">'.$r['ip'].'</td>';
						echo '<td  class="number">'.$r['ip_std_umur'].'</td>';
						echo '<td  class="number">'.$ip_rasio.'</td>';
						echo '</tr>';

						$mati = 0;
						$afkir = 0;
						$pakai_sak = 0;
						$pakai_kg = 0;
						$_counter++;
					}
					else{
						$mati += $r['mati'];
						$afkir += $r['afkir'];
						$pakai_sak += $r['jml_pakai'];
						$pakai_kg += $r['brt_pakai'];
					}
				}
			}
		}
	}
		?>
		</tbody>
	</table>
	<div class="btn prev slider-table" data-current="2" data-min="2" data-max="3" onclick="Rhk.prev(this)"> <i class="glyphicon glyphicon-chevron-left"></i> </div>
	<div class="btn next slider-table" data-current="2" data-min="2" data-max="3" onclick="Rhk.next(this)"> <i class="glyphicon glyphicon-chevron-right"></i> </div
