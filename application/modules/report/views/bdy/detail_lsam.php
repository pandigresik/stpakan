<div class="row">
	<div class="col-md-3"> Tanggal Doc In : <?php echo $tgl_docin ?></div>
	<div class="col-md-3"> Hatchery : <?php echo $terimadocin['nama_hatchery'] ?></div>
	<div class="col-md-3"> Berat DOC : <?php echo formatAngka($terimadocin['bb_rata2'],2) ?></div>
	<div class="col-md-3"> Populasi awal : <?php echo formatAngka($terimadocin['stok_awal'],0) ?></div>
</div>
<table class="table table-bordered custom_table" >
	<thead>
			<tr>
				<th class="ftl" rowspan="3">Umur (hari)</th>
				<th class="ftl tanggal" rowspan="3">Tgl.</th>
				<th class="" colspan="4">Deplesi</th>
				<th class="" rowspan="3">Pop. <br /> (ekor)</th>
				<th class="dh" colspan="3">Daya Hidup</th>
				<th colspan="5">Pakan</th>
				<th colspan="3">Berat Badan</th>
				<th colspan="3">ADG</th>
				<th colspan="3">FCR</th>
				<th colspan="3">IP</th>
			</tr>
			<tr>
				<th colspan="2">Mati</th>
				<th colspan="2">Afkir</th>
				<th rowspan="2">Real (%)</th>
				<th rowspan="2">Std (%)</th>
				<th rowspan="2">(%) dr Std</th>
				<th colspan="2">Pakai</th>
				<th rowspan="2">Kumulatif (gr)</th>
				<th rowspan="2">Std (gr)</th>
				<th rowspan="2">(%) dr Std</th>
				<th rowspan="2">Real (gr)</th>
				<th rowspan="2">Std (gr)</th>
				<th rowspan="2">(%) dr Std</th>
				<th rowspan="2">Real (gr)</th>
				<th rowspan="2">Std (gr)</th>
				<th rowspan="2">(%) dr Std</th>
				<th rowspan="2">Real</th>
				<th rowspan="2">Std</th>
				<th rowspan="2">(%) dr Std</th>
				<th rowspan="2">Real</th>
				<th rowspan="2">Std</th>
				<th rowspan="2">(%) dr Std</th>
			</tr>
			<tr>
				<th>Ekor</th>
				<th>%</th>
				<th>Ekor</th>
				<th>%</th>
				<th>Sak</th>
				<th>gr</th>
			</tr>
		</thead>
		<tbody>
		<?php

		if(!empty($rhk)){
			$berat_persak = 50;

			$berat_pakai_kum = 0;
			$sak_pakai_kum_perpakan = 0;
			$berat_pakai_kum_perpakan = 0;
			$mati = $afkir = $pakai_sak = $pakai_kg = $pakai_kum = 0;
			foreach($rhk as $tgl =>$r){
				$pakai_kum += $r['brt_pakai'];
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
				if($r['hari'] > 7){
						$afkir = '-';
						$persenafkir = '-';
					}
				$persenafkir_str = ($persenafkir == '-') ? $persenafkir : formatAngka($persenafkir,3);
				/*
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
*/
				/* rasio terhadap standart */
				$dh_rasio = formatAngka($r['dh'] / $r['dh_std'] * 100,2);
				$pakai_gr_perekor = $pakai_kg / $r['jml'];
				$pakai_kumgr_perekor = $pakai_kum / $r['jml'];
				$pkn_rasio = formatAngka(round($pakai_kumgr_perekor) / $r['pkn_kum_std'] * 100,3);
				$bb_rasio = $r['berat_badan'] > 0 ?  formatAngka($r['berat_badan'] / $r['bb_std'] * 100,3) : '-';
				$adg_rasio = $r['adg'] != '-' && $r['adg_std'] > 0 ?  formatAngka($r['adg'] / $r['adg_std'] * 100,3) : '-';
				$fcr_rasio = $r['fcr'] != '-' ?  formatAngka($r['fcr'] / $r['fcr_std']  * 100,3) : $r['fcr'];
				$ip_rasio = $r['ip'] != '-' ?  formatAngka($r['ip'] / $r['ip_std_umur']  * 100,3) : $r['ip'];
	/*			$deplesi_rasio = $r['deplesi'] > 0 ? formatAngka($persenmati/ $r['deplesi_std'] * 100,2).'%' : '0%';
				$kons_rasio = formatAngka($brt_pakai_ekor / $r['pkn_std'] * 100,2).'%';



				$warna_rasio_adg = !empty($r['adg']) && ($r['adg'] > $r['adg_std']) ? 'ijo' : 'abang';

				$adg_rasio_str = (!empty($r['adg']) && ($r['adg']  != $r['adg_std'])) ? '<a href="#" class="'.$warna_rasio_adg.'" data-toogle="tooltip" data-original-title="'.formatAngka($r['adg_std'],3).'">'.$adg_rasio.'</a>' : (!empty($r['adg']) ? $adg_rasio : '-');
*/
			//	$adg_rasio = '-';
	//			$adg_str = $r['adg'] != '-' ? formatAngka($r['adg'],3) : $r['adg'];

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
			}
			else{
				$mati += $r['mati'];
				$afkir += $r['afkir'];
				$pakai_sak += $r['jml_pakai'];
				$pakai_kg += $r['brt_pakai'];
			}
		}

		}
		?>
		</tbody>
	</table>
