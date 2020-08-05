<div class="row">
	<div class="col-md-3"> Tanggal Doc In : <?php echo $tgl_docin?></div>
</div>
<table class="table table-bordered custom_table" >
	<thead>
			<tr>
				<th class="ftl tanggal" rowspan="3">Tgl.</th>
				<th class="ftl" rowspan="3">Umur (hari)</th>
				<th colspan="12">Performa</th>
				<th colspan="7">Rasio Performa thd. Standar</th>
				<th colspan="5">Pertanggungjawaban Retur</th>
				<th colspan="14">Kontrol Pakan</th>
				<th rowspan="3">Catatan</th>
			</tr>
			<tr>
				<th colspan="3">Deplesi</th>
				<th class="" rowspan="2">Pop. <br /> (ekor)</th>
				<th class="" rowspan="2">Panen <br /> (ekor)</th>
				<th class="dh" rowspan="2">DH</th>
				<th rowspan="2">Kons. (gr)</th>
				<th rowspan="2">Kons. Kum (gr)</th>
				<th rowspan="2">BB (gr)</th>
				<th rowspan="2">ADG (gr/hr)</th>
				<th rowspan="2">FCR</th>

				<th rowspan="2">IP</th>

				<th class="dh" rowspan="2">DH</th>
				<th rowspan="2">Deplesi</th>
				<th rowspan="2">Kons.</th>
				<th rowspan="2">BB</th>
				<th rowspan="2">ADG</th>
				<th rowspan="2">FCR</th>
				<th class="nama_bentuk" rowspan="2">Nama Pakan</th>

				<th colspan="2">Pakan Rusak</th>
				<th colspan="3">Sak Kosong</th>

				<th colspan="2">Stok Awal</th>
				<th colspan="2">Kebutuhan</th>
				<th colspan="4">Pakai</th>
				<th colspan="2">Terima</th>
				<th colspan="2">Stok Akhir</th>
				<th colspan="2">Sisa Akhir - Keb H+1</th>

			</tr>
			<tr>
				<th>Mati</th>
				<th>Afkir</th>
				<th>%</th>

				<th>Dikembalikan</th>
				<th>Diganti</th>

				<th>Dikembalikan</th>
				<th>Terhutang</th>
				<th>Sisa</th>

				<th>Kg</th>
				<th>Sak</th>

				<th>Kg</th>
				<th>Sak</th>

				<th>Kg</th>
				<th>Kum</th>
				<th>Sak</th>
				<th>Kum</th>

				<th>Kg</th>
				<th>Sak</th>

				<th>Kg</th>
				<th>Sak</th>

				<th>Kg</th>
				<th>Sak</th>
			</tr>
		</thead>
		<tbody>
		<?php

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
                $umur_panen = round($up / $total_panen, 2);
                $bb_panen = $bp;
                $dh_panen = $total_panen / $populasi_awal['stok_awal'] * 100;
                $selisih_panen = $populasi_awal['stok_awal'] - $total_panen ;
            }
            
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

//				$brt_pakai_ekor = (is_numeric($r['brt_pakai']/$r['jml']) ? $r['brt_pakai']/$r['jml'] : 0) ;
				$berat_pakai_kum += $r['brt_pakai'];
				//$berat_pakai_kum_perekor = formatAngka($berat_pakai_kum / $r['jml'],3);
				if(($r['jml'] != 0) && (is_numeric($r['jml']))){
					$brt_pakai_ekor = $r['brt_pakai']/$r['jml'];
					$berat_pakai_kum_perekor = formatAngka($berat_pakai_kum / $r['jml'],3);
				}else{
					$brt_pakai_ekor = 0;
					$berat_pakai_kum_perekor = 0;
				}
				
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
               
				echo '<td rowspan="'.$t_rowspan.'" data-noreg="'.$noreg.'" data-tgltransaksi="'.$tgl.'" onclick="Rhk.showAttachmentRhk(this)" class="text-center tanggal ftl">'.tglIndonesia($tgl,'-',' ').$span_bookmark.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number ftl">'.$r['hari'].'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$r['mati'] .'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$r['afkir'] .'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.formatAngka($persenmati,3) .'%</td>';
				echo '<td style=\'mso-number-format:"#.##0"\' rowspan="'.$t_rowspan.'"  class="number">'.formatAngka($r['jml'],0).'</td>';
				echo '<td style=\'mso-number-format:"#.##0"\' rowspan="'.$t_rowspan.'"  class="number">'.$c_panen.'</td>';
				echo '<td style=\'mso-number-format:"#.##0,00"\' rowspan="'.$t_rowspan.'"  class="number dh">'.$dh_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$brt_pakai.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$berat_pakai_kum_perekor.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$brt_bb.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$adg_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$fcr_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$ip_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$dh_rasio.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$deplesi_rasio.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$kons_rasio.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$bb_rasio.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$adg_rasio_str.'</td>';
				echo '<td rowspan="'.$t_rowspan.'"  class="number">'.$fcr_rasio.'</td>';

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

					echo '<td class="number">'.$z['pakan_retur'].'</td>';
					echo '<td class="number">'.$z['pakan_diganti'].'</td>';
					echo '<td class="number">'.$z['sak_retur'].'</td>';
					echo '<td class="number">'.$z['sak_hutang'].'</td>';
					echo '<td class="number">'.$z['sisa_hutang'].'</td>';

					echo '<td class="number">'.$brt_awal.'</td>';
					echo '<td class="number">'.$jml_awal.'</td>';

					echo '<td class="number">'.formatAngka($keb_kg,3).'</td>';
					echo '<td class="number">'.formatAngka($keb_sak,3).'</td>';

					echo '<td class="number">'.formatAngka($z['brt_pakai'],3).'</td>';
					echo '<td class="number">'.formatAngka($berat_pakai_kum_perpakan,3).'</td>';
					echo '<td class="number">'.$z['jml_pakai'].'</td>';
					echo '<td class="number">'.formatAngka($sak_pakai_kum_perpakan,0).'</td>';

					echo '<td class="number">'.formatAngka($z['brt_terima'],3).'</td>';
					echo '<td class="number">'.$z['jml_terima'].'</td>';

					echo '<td class="number '.$class_stok_akhir.'">'.formatAngka($z['brt_akhir'],3).'</td>';
					echo '<td class="number '.$class_stok_akhir.'">'.$z['jml_akhir'].'</td>';

					echo '<td class="number '.$class_sisa_pakai.'">'.number_format($brt_sisa_pakai,3,',','.').'</td>';
					echo '<td class="number '.$class_sisa_pakai.'">'.number_format($jml_sisa_pakai,3,',','.').'</td>';

					echo '<td></td>';

					$summary_pakan['keb_kg'] += $keb_kg;
					$summary_pakan['keb_sak'] += $keb_sak;
					$summary_pakan['pakai_kg'] += $z['brt_pakai'];
					$summary_pakan['pakai_sak'] += $z['jml_pakai'];
					if($_p > 0){
						echo '</tr>';
					}
					$_p++;
				}
				echo '</tr>';
			}
            
            if($bb_panen > 0){
				$fcr_panen = round($summary_pakan['pakai_kg'] / $bb_panen , 3);
				$dh_panen = round($dh_panen,2);
				$ip_panen = formatAngka((($dh_panen * (round($bb_rata_panen / 1000,4)) * 100) / ($fcr_panen * $umur_panen )),0);				
                $fcr_panen = formatAngka($fcr_panen,3);
				//$dh_panen = formatAngka($dh_panen,2);
				//log_message('error',round($bb_rata_panen / 1000,3));
            }
			echo '<tr class="rekap panen">
				<td class="ftl"><strong>Total Panen</strong></td>
                <td class="ftl"><strong>'.formatAngka($umur_panen,2).'</strong></td>
				<td colspan="4"></td>
                <td class="text-right">'.formatAngka($total_panen,0).'</td>
                <td class="text-right">'.$dh_panen.'</td>                
                <td colspan="2"></td>
                <td class="text-right">'.formatAngka($bb_rata_panen,3).'</td>
                <td></td>
                <td class="text-right">'.$fcr_panen.'</td>
                <td class="text-right">'.$ip_panen.'</td>
                <td colspan="14" class="text-right"></td>
				
				<td class="number">'.formatAngka($summary_pakan['keb_kg'],3).'</td>
				<td class="number">'.formatAngka($summary_pakan['keb_sak'],3).'</td> 
				<td></td>             
				<td class="number">'.formatAngka($summary_pakan['pakai_kg'],3).'</td>
                <td></td>
				<td class="number">'.formatAngka($summary_pakan['pakai_sak'],0).'</td>
				<td colspan="10"></td>
			</tr>';
            	echo '<tr>
				<td class="ftl" colspan="2">&nbsp;</td>              
				<td colspan="39"></td>
			</tr>';
		}
		?>
		</tbody>
	</table>
