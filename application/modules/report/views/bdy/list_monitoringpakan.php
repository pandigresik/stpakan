<?php $jmlpakan = count($listpakan) ?>
<table class="table table-bordered custom_table">
	<thead>
		<tr>
			<th colspan="2" class="ftl">Kebutuhan</th>
			<th colspan="<?php echo (1+$jmlpakan) ?>">Forecast</th>
			<th colspan="<?php echo (5+2*$jmlpakan) ?>">Permintaan Pakan</th>
			<th colspan="<?php echo (4+$jmlpakan) ?>">Delivery Order</th>
			<th colspan="<?php echo (2+2*$jmlpakan) ?>">Penerimaan Gudang</th>
			<th colspan="<?php echo (1+2*$jmlpakan) ?>">Pengiriman Ke Kandang</th>
			<th colspan="<?php echo (1+2*$jmlpakan) ?>">Pemakaian di LHK</th>
			<th colspan="<?php echo (1+2*$jmlpakan) ?>">Pengembalian Sak Kosong</th>
		</tr>
		<tr>
			<th rowspan="2" class="ftl">Umur</th>
			<th rowspan="2" class="ftl">Tanggal</th>
			<th rowspan="2">Rencana Kirim</th>
			<?php foreach($listpakan as $k => $val){
				echo '<th rowspan="2">'.$val.'</th>';
			}
			?>
			<th rowspan="2">Tgl. Kirim</th>
			<th rowspan="2">No. PP</th>
			<?php foreach($listpakan as $k => $val){
				echo '<th colspan="2">'.$val.'</th>';
			}
			?>
			<th rowspan="2">Tgl. Rilis</th>
			<th rowspan="2">Tgl. Review</th>
			<th rowspan="2">Tgl. Approve</th>
			<th rowspan="2">Tgl. Buat</th>
			<th rowspan="2">No. DO</th>
			<?php foreach($listpakan as $k => $val){
				echo '<th rowspan="2">'.$val.'</th>';
			}
			?>
			<th rowspan="2">Jumlah (Sak)</th>
			<th rowspan="2">Total (Sak)</th>
			<th rowspan="2">Tgl. Terima</th>
			<th rowspan="2">No. DO</th>
			<?php foreach($listpakan as $k => $val){
				echo '<th rowspan="2">'.$val.'</th>';
			}
			?>
			<th colspan="<?php echo count($listpakan) ?>">Selisih Terhadap DO</th>
			<th rowspan="2">Tgl. Kirim</th>
			<?php foreach($listpakan as $k => $val){
				echo '<th rowspan="2">'.$val.'</th>';
			}
			?>
			<th colspan="<?php echo count($listpakan) ?>">Selisih Terhadap PP</th>
			<th rowspan="2">Tgl. Entry</th>
			<?php foreach($listpakan as $k => $val){
				echo '<th rowspan="2">'.$val.'</th>';
			}
			?>
			<th colspan="<?php echo count($listpakan) ?>">Selisih Terhadap Pengiriman Kandang</th>
			<th rowspan="2">Tgl. Kembali</th>
			<?php foreach($listpakan as $k => $val){
				echo '<th rowspan="2">'.$val.'</th>';
			}
			?>
			<th colspan="<?php echo count($listpakan) ?>">Selisih Terhadap LHK</th>
		</tr>
		<tr>
			<?php foreach($listpakan as $k => $val){
				echo '<th rowspan="2">Perhari</th>';
				echo '<th rowspan="2">Total</th>';
			}
			?>
			<?php
			for($x = 0; $x < 4; $x++){
				foreach($listpakan as $k => $val){
					echo '<th rowspan="2">'.$val.'</th>';
				}
			}
			?>
		</tr>
	</thead>
	<tbody>
	<?php
	$no = 1;
	$tmpkebutuhanpp = array();
	$totalsemuapp = array();
	$totalsemuado = array();
	$totalsemuagudang = array();
	$totalselisihgudangdo = array();
	$totalselisihkandangpp = array();
	$totalsemuakandang = array();
	$totalsemualhk = array();
	$totalsemuaretur = array();
	$totalforecast = array();
	$totalsemuaselisihlhk = array();
	$totalsemuaselisihretur = array();
	foreach($listpakan as $k => $val){
		$totalsemuapp[$k] = 0 ;
		$totalsemuado[$k] = 0 ;
		$totalsemuagudang[$k] = 0 ;
		$totalsemuakandang[$k] = 0 ;
		$totalsemualhk[$k] = 0 ;
		$totalsemuaretur[$k] = 0 ;
		$totalselisihgudangdo[$k] = 0;
		$totalselisihkandangpp[$k] = 0;
		$totalforecast[$k] = 0;
		$totalsemuaselisihlhk[$k] = 0;
		$totalsemuaselisihretur[$k] = 0;
	}
	foreach($tglkebutuhan as $dt){
		$tglkeb = $dt->format('Y-m-d');
		echo '<tr>';
			echo '<td class="ftl">'.$no++.'</td>';
			echo '<td class="tanggal ftl">'.tglIndonesia($tglkeb,'-',' ').'</td>';
			if(isset($forecast[$tglkeb])){
				$colspanforecast = count(array_unique($forecast[$tglkeb]['tglkebutuhan']));
				$kirimforecast = $forecast[$tglkeb]['tgl_kirim'];
				echo '<td rowspan="'.$colspanforecast.'" class="tanggal">'.tglIndonesia($kirimforecast,'-',' ').'</td>';
				foreach($listpakan as $k => $val){
				//	print_r($forecast[$tglkeb][$kirimforecast]);
					echo '<td rowspan="'.$colspanforecast.'">'.divbaris($forecast[$tglkeb][$kirimforecast][$k],'DECIMAL').'</td>';
					foreach($forecast[$tglkeb][$kirimforecast][$k] as $vf){
							$totalforecast[$k] += $vf;
					}

				}
			}
			if(isset($listpp[$tglkeb])){
				$tmpkebutuhanpp = array_unique($listpp[$tglkeb]['tglkebutuhan']);
				$colspanpp = count($tmpkebutuhanpp);
				$nopp = $listpp[$tglkeb]['no_lpb'];
				echo '<td rowspan="'.$colspanpp.'" class="tanggal">'.tglIndonesia($listpp[$tglkeb]['tgl_kirim'],'-',' ').'</td>';
				echo '<td rowspan="'.$colspanpp.'" class="tanggalwaktu">'.$nopp.'</td>';
				foreach($listpakan as $k => $val){
					$jmlpp = isset($listpp[$tglkeb]['jml_pp'][$k]) ? $listpp[$tglkeb]['jml_pp'][$k] : '-';
					echo '<td rowspan="'.$colspanpp.'">'.divbaris($listpp[$tglkeb]['jml_order'][$k],'JML').'</td>';
					echo '<td rowspan="'.$colspanpp.'">'.$jmlpp.'</td>';
					$totalsemuapp[$k] += $jmlpp;
				}
				echo '<td rowspan="'.$colspanpp.'" class="tanggalwaktu">'.convertElemenTglWaktuIndonesia($listpp[$tglkeb]['tgl_rilis']).'</td>';
				echo '<td rowspan="'.$colspanpp.'" class="tanggalwaktu">'.convertElemenTglWaktuIndonesia($listpp[$tglkeb]['tgl_review']).'</td>';
				echo '<td rowspan="'.$colspanpp.'" class="tanggalwaktu">'.convertElemenTglWaktuIndonesia($listpp[$tglkeb]['tgl_approve']).'</td>';
				/* list do */
				if(isset($listdo[$nopp])){
					echo '<td rowspan="'.$colspanpp.'"class="tanggalwaktu">'.divbaris($listdo[$nopp]['TGL_BUAT'],'TGL_BUAT').'</td>';
					echo '<td rowspan="'.$colspanpp.'">'.divbaris($listdo[$nopp]['NO_DO'],'NO_DO').'</td>';
					$jmlsakperdo = array();
					$jmlsakperbarang = array();
					foreach($listpakan as $k => $val){
						$jmlsakperbarang[$k] = 0;
						$doperbarang = array();
						foreach($listdo[$nopp]['NO_DO'] as $do){
							$tmpdo = $listdo[$nopp][$do][$k];
							if(!isset($jmlsakperdo[$do])){
								$jmlsakperdo[$do] = 0;
							}
							$jmlsakperbarang[$k] += $tmpdo;
							$jmlsakperdo[$do] += $tmpdo;
							array_push($doperbarang,$tmpdo);
							$totalsemuado[$k] += $tmpdo;
						}
						echo '<td rowspan="'.$colspanpp.'">'.divbaris($doperbarang,'JML').'</td>';
					}
					echo '<td rowspan="'.$colspanpp.'">'.divbaris($jmlsakperdo,'JML').'</td>';
					echo '<td rowspan="'.$colspanpp.'">'.array_sum($jmlsakperdo).'</td>';
				}
				else{
					for($c = 0; $c < (count($listpakan) + 4) ; $c++){
						echo '<td rowspan="'.$colspanpp.'">-</td>';
					}
				}
				/* terima gudang */
				if(isset($terimagudang[$nopp])){
					echo '<td rowspan="'.$colspanpp.'"class="tanggalwaktu">'.divbaris($terimagudang[$nopp]['TGL_BUAT'],'TGL_BUAT').'</td>';
					echo '<td rowspan="'.$colspanpp.'">'.divbaris($terimagudang[$nopp]['NO_DO'],'NO_DO').'</td>';
	//				echo '<td rowspan="'.$colspanpp.'">'.divbaris($listdo[$nopp],'NO_DO').'</td>';
				$jmlsakperbaranggudang = array();
				foreach($listpakan as $k => $val){
					$jmlsakperbaranggudang[$k] = 0;
					$doperbarang = array();
					foreach($listdo[$nopp]['NO_DO'] as $do){
						$tmpdo = $listdo[$nopp][$do][$k];
						array_push($doperbarang,$tmpdo);
						$jmlsakperbaranggudang[$k] += $tmpdo;
					}
					$totalsemuagudang[$k] += $jmlsakperbaranggudang[$k];
					echo '<td rowspan="'.$colspanpp.'">'.divbaris($doperbarang,'JML').'</td>';
				}
					foreach($listpakan as $k => $val){
						$totalselisihgudangdo[$k] += ($jmlsakperbaranggudang[$k] - $jmlsakperbarang[$k]);
						echo '<td rowspan="'.$colspanpp.'">'.($jmlsakperbaranggudang[$k] - $jmlsakperbarang[$k]).'</td>';
					}
				}
				else{
					echo '<td rowspan="'.$colspanpp.'">-</td>';
					echo '<td rowspan="'.$colspanpp.'">-</td>';
					if(isset($listdo[$nopp])){
						foreach($listpakan as $k => $val){
							echo '<td rowspan="'.$colspanpp.'">-</td>';
						}
						foreach($listpakan as $k => $val){
							$totalselisihgudangdo[$k] += (0 - $jmlsakperbarang[$k]);
							echo '<td rowspan="'.$colspanpp.'">'.(0 - $jmlsakperbarang[$k]).'</td>';
						}
					}else{
						foreach($listpakan as $k => $val){
							echo '<td rowspan="'.$colspanpp.'">-</td>';
						}
						foreach($listpakan as $k => $val){
							echo '<td rowspan="'.$colspanpp.'">-</td>';
						}
					}
				}
			}
			else{
				if(!in_array($tglkeb,$tmpkebutuhanpp)){
					for($i = 0; $i < (7 + count($listpakan) * 7); $i++){
							echo '<td>-</td>';
					}
				}
			}
			/* terima kandang */
			if(isset($terimakandang[$tglkeb])){
				echo '<td class="tanggalwaktu">'.convertElemenTglWaktuIndonesia($terimakandang[$tglkeb]['tgl_buat']).'</td>';
				foreach($listpakan as $k => $val){
					$jmlterima = isset($terimakandang[$tglkeb]['jml'][$k]) ? $terimakandang[$tglkeb]['jml'][$k] : '0';
					echo '<td>'.$jmlterima.'</td>';
					$totalsemuakandang[$k] += $jmlterima;
				}

				foreach($listpakan as $k => $val){
					$jmlpp = isset($orderpp[$tglkeb][$k]) ? $orderpp[$tglkeb][$k] : 0;
					$jmlterima = isset($terimakandang[$tglkeb]['jml'][$k]) ? $terimakandang[$tglkeb]['jml'][$k] : 0;
					$jmlselisih = $jmlterima - $jmlpp;
					$totalselisihkandangpp[$k] += $jmlselisih;
					echo '<td>'.$jmlselisih.'</td>';
				}
			}
			else{
				echo '<td>-</td>';
				if(isset($orderpp[$tglkeb])){
					foreach($listpakan as $k => $val){
						echo '<td>-</td>';
					}
					foreach($listpakan as $k => $val){
						$jmlpp = isset($orderpp[$tglkeb][$k]) ? $orderpp[$tglkeb][$k] : 0;
						$jmlterima = isset($terimakandang[$tglkeb]['jml'][$k]) ? $terimakandang[$tglkeb]['jml'][$k] : 0;
						$jmlselisih = $jmlterima - $jmlpp;
						$totalselisihkandangpp[$k] += $jmlselisih;
						echo '<td>'.$jmlselisih.'</td>';
					}
				}else{
					for($y = 0; $y < 2; $y++){
						foreach($listpakan as $k => $val){
							echo '<td>-</td>';
						}
					}
				}
			}

			/* rhk */
			if(isset($rhk[$tglkeb])){
				echo '<td class="tanggalwaktu">'.convertElemenTglWaktuIndonesia($rhk[$tglkeb]['tgl_buat']).'</td>';
				foreach($listpakan as $k => $val){
					$jmlterima = isset($rhk[$tglkeb]['jml'][$k]) ? $rhk[$tglkeb]['jml'][$k] : 0;
					echo '<td>'.$jmlterima.'</td>';
					$totalsemualhk[$k] += $jmlterima;
				}
				foreach($listpakan as $k => $val){
					$jmlterima = isset($rhk[$tglkeb]['jml'][$k]) ? $rhk[$tglkeb]['jml'][$k] : 0;
					$jmlterimakandang = isset($terimakandang[$tglkeb]['jml'][$k]) ? $terimakandang[$tglkeb]['jml'][$k] : 0;
					echo '<td>'.($jmlterima - $jmlterimakandang).'</td>';
					$totalsemuaselisihlhk[$k] += ($jmlterima - $jmlterimakandang);
				}
			}
			else{
				echo '<td>-</td>';
				foreach($listpakan as $k => $val){
					echo '<td>-</td>';
				}
				if(isset($terimakandang[$tglkeb])){
					foreach($listpakan as $k => $val){
						$jmlterimakandang = isset($terimakandang[$tglkeb]['jml'][$k]) ? $terimakandang[$tglkeb]['jml'][$k] : 0;
						echo '<td>'.(0 - $jmlterimakandang).'</td>';
						$totalsemuaselisihlhk[$k] += (0 - $jmlterimakandang);
					}
				}
				else{

					foreach($listpakan as $k => $val){
						echo '<td>-</td>';
					}
				}
			}

			/* sak kembali */
			if(isset($sakkembali[$tglkeb])){
				echo '<td class="tanggalwaktu">'.convertElemenTglWaktuIndonesia($sakkembali[$tglkeb]['tgl_buat']).'</td>';
				foreach($listpakan as $k => $val){
					$jmlterima = isset($sakkembali[$tglkeb]['jml'][$k]) ? $sakkembali[$tglkeb]['jml'][$k] : 0;
					echo '<td>'.$jmlterima.'</td>';
					$totalsemuaretur[$k] += $jmlterima;
				}
				foreach($listpakan as $k => $val){
					$sakrhk = isset($rhk[$tglkeb]['jml'][$k]) ? $rhk[$tglkeb]['jml'][$k] : 0;
					$retursak =  isset($sakkembali[$tglkeb]['jml'][$k]) ? $sakkembali[$tglkeb]['jml'][$k] : 0;
					$selisih = $retursak - $sakrhk;
					echo '<td>'.$selisih.'</td>';
					$totalsemuaselisihretur[$k] += $selisih;
				}
			}	else{
					echo '<td>-</td>';
					foreach($listpakan as $k => $val){
						echo '<td>-</td>';
					}
					if(isset($rhk[$tglkeb])){
						foreach($listpakan as $k => $val){
							$sakrhk = isset($rhk[$tglkeb]['jml'][$k]) ? $rhk[$tglkeb]['jml'][$k] : 0;
							$retursak =  isset($sakkembali[$tglkeb]['jml'][$k]) ? $sakkembali[$tglkeb]['jml'][$k] : 0;
							$selisih = $retursak - $sakrhk;
							echo '<td>'.$selisih.'</td>';
							$totalsemuaselisihretur[$k] += $selisih;
						}
					}
					else{
						foreach($listpakan as $k => $val){
							echo '<td>-</td>';
						}
					}
				}
		/*
			else{
				echo '<td>-</td>';
				for($x = 0; $x < 3; $x++){
					foreach($listpakan as $k => $val){
						echo '<td>-</td>';
					}
				}
			}
		*/
		echo '</tr>';
	};
	?>
	<tr>
		<td class="ftl" colspan="2">Total</td>
		<td></td>
		<?php
		/* total forecast */
		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalforecast[$k],2).'</td>';
		}
		?>
		<td colspan="2"></td>
		<?php
		/* total pp */
		foreach($listpakan as $k => $val){
			echo '<td></td>';
			echo '<td>'.formatAngka($totalsemuapp[$k],0).'</td>';
		}
		?>
		<td colspan="5"></td>
		<?php
		/* total do */
		$totaldoall = 0;
		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalsemuado[$k],0).'</td>';
			$totaldoall += $totalsemuado[$k];
		}
		echo '<td>'.formatAngka($totaldoall,0).'</td>';
		echo '<td>'.formatAngka($totaldoall,0).'</td>';
		?>
		<td colspan="2"></td>
		<?php
		/* total terima gudang */
		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalsemuagudang[$k],0).'</td>';
		}

		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalselisihgudangdo[$k],0).'</td>';
		}
		?>
		<td></td>
		<?php
		/* total terima kandang */
		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalsemuakandang[$k],0).'</td>';
		}

		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalselisihkandangpp[$k],0).'</td>';
		}
		?>
		<td></td>
		<?php
		/* total lhk */
		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalsemualhk[$k],0).'</td>';
		}
		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalsemuaselisihlhk[$k],0).'</td>';
		}
		?>
		<td></td>
		<?php
		/* total retur sak */
		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalsemuaretur[$k],0).'</td>';
		}

		foreach($listpakan as $k => $val){
			echo '<td>'.formatAngka($totalsemuaselisihretur[$k],0).'</td>';
		}
		?>
	</tr>
	</tbody>
</table>
