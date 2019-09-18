<?php if (count($result) ) : ?>

   <?php
	foreach ($result as $data) {
	
         foreach ($data as $key1 => $val1) {
            $itot_trm = 0;
            $itot_pakai = 0;
            $itot_kembali = 0;
            $itot_harian = 0;
            $itot_total = 0;

            $stot_trm = '';
            $stot_pakai = '';
            $stot_kembali = '';
            $stot_harian = '';
            $stot_total = '';
            foreach ($val1 as $key2 => $val2) {
               $i = 0;
               foreach ($val2 as $key3 => $val3) {
                  if ( $i > 5) {
                     if(substr($key3, 0, 3) == 'trm'){
                        $stot_trm .= "<th class='screen_2' style='text-align: center;' width='70'>".str_replace('trm', '', $key3)."</th>";
                        $itot_trm++;
                     }
                     if(substr($key3, 0, 2) == 'pk'){
                        $stot_pakai .= "<th class='screen_2' style='text-align: center;' width='70'>".str_replace('pk', '', $key3)."</th>";
                        $itot_pakai++;
                     }
                     if(substr($key3, 0, 3) == 'kbl'){
                        $stot_kembali .= "<th class='screen_3' style='text-align: center;' width='70'>".str_replace('kbl', '', $key3)."</th>";
                        $itot_kembali++;
                     }
                     if(substr($key3, 0, 3) == 'hr'){
                        $stot_harian .= "<th class='screen_3' style='text-align: center;' width='70'>".str_replace('hr', '', $key3)."</th>";
                        $itot_harian++;
                     }
                     if(substr($key3, 0, 3) == 'ttl'){
                        $stot_total .= "<th class='screen_3' style='text-align: center;' width='70'>".str_replace('ttl', '', $key3)."</th>";
                        $itot_total++;
                     }
                  }
                  $i++;

               }
               break;
            }
            break;
            //return false;
         }
	}
?>
<table border="1" style="max-width: 100%" class="fixed_headers">

   <thead>
      <tr>
         <th rowspan=3 style="text-align: center;" width="100">TGL</th>
         <th rowspan=3 style="text-align: center;" width="100">TGL Kebutuhan</th>
         <th rowspan=3 style="text-align: center;" width="100">Kandang</th>
         <th class="screen_2" rowspan="2" colspan="<?php echo $itot_trm + 2 ?>" style="text-align: center;">Terima Kandang</th>
         <th class="screen_2" rowspan="2" colspan="<?php echo $itot_pakai + 2 ?>" style="text-align: center;">RHK</th>
         <th class="screen_3" rowspan="2" colspan="<?php echo $itot_kembali + 2 ?>" style="text-align: center;">Pengembalian Sak Kosong</th>
         <th class="screen_3" colspan="<?php echo $itot_kembali * 2 ?>" style="text-align: center;">Hutang Sak</th>
      </tr>
   <tr>
      <th class="screen_3" colspan="<?php echo $itot_kembali?>" style="text-align: center;" width="70">Harian</th>
      <th class="screen_3" colspan="<?php echo $itot_kembali?>" style="text-align: center;" width="70">Total</th>
   </tr>
   <tr>
      <th class="screen_2" style='text-align: center;' width="150">Tgl Terima</th>
      <?php echo $stot_trm?>
      <th class="screen_2" style='text-align: center;' width="70">Total</th>
      <th class="screen_2" style='text-align: center;' width="150">Tgl Pakai</th>
      <?php echo $stot_pakai?>
      <th class="screen_2" style='text-align: center;' width="70">Total</th>
      <th class="screen_3"  style='text-align: center;' width="150">Tgl Kembali</th>
      <?php echo $stot_kembali?>
      <th class="screen_3" style='text-align: center;' width="70">Total</th>
      <?php echo $stot_kembali?>
      <?php echo $stot_kembali?>
   </tr>
   </thead>
   <tbody>
      <?php
      $tot_hutang = array();
	  foreach ($result as $key => $data) {
		  echo '<tr><td style="padding-left:15px;" colspan="3"><strong>'.$key.'</strong></td>';
		  echo '<td class="screen_2" colspan="'.($itot_trm + $itot_pakai + 4).'">&nbsp;</td>';
		  echo '<td class="screen_3" colspan="'.($itot_kembali * 3 + 2).'">&nbsp;</td></tr>';
		  foreach ($data as $key1 => $val1) {
			 $count = count($val1);
			 $total = 0;
			 $tot_trm = 0;
			 $tot_pakai = 0;
			 $tot_kembali = 0;

			 foreach ($val1 as $key2 => $val2) {
				$i = 0;
				foreach ($val2 as $key3 => $val3) {
				   if ( $i > 5) {
					  if(substr($key3, 0, 3) == 'trm'){
						 $tot_trm += $val3;
					  }
					  if(substr($key3, 0, 2) == 'pk'){
						 $tot_pakai += $val3;
					  }
					  if(substr($key3, 0, 3) == 'kbl'){
						 $tot_kembali += $val3;
					  }

					  if(substr($key3, 0, 2) == 'hr'){
						 if ( ! isset($tot_hutang[$val2['no_reg']][$key3]) ) {
							$tot_hutang[$val2['no_reg']][$key3] = 0;
						 }
					  }

	//									echo "<td>".$key3."</td>";
				   }
				   $i++;
				}
			 }

			 $stat = 0;
			 foreach ($val1 as $key2 => $val2) {

				echo "<tr>";
				if ($stat == 0) {
				//	$stat = 1;
				   echo "<td style='text-align: center;' rowspan='$count'>".convertElemenTglWaktuIndonesia($val2['tgl'])."</td>";
				}

				   echo "<td style='text-align: center;'>".convertElemenTglWaktuIndonesia($val2['tgl_keb'])."</td>";
				   echo "<td style='text-align: center;'>".$val2['no_reg']."</td>";
				   echo "<td class='screen_2' style='text-align: center;'>".convertElemenTglWaktuIndonesia($val2['tgl_terima'])."</td>";
				   $i = 0;
				   $trm = '';
				   $pakai = '';
				   $kembali = '';
				   $harian = '';
				   $total = '';
				   foreach ($val2 as $key3 => $val3) {
					  if ( $i > 5) {
						 if(substr($key3, 0, 3) == 'trm'){
							$trm .= "<td class='screen_2' style='text-align: right;'>".$val3."</td>";
						 }
						 if(substr($key3, 0, 2) == 'pk'){
							$pakai .= "<td class='screen_2' style='text-align: right;'>".$val3."</td>";
						 }
						 if(substr($key3, 0, 3) == 'kbl'){
							$kembali .= "<td class='screen_3' style='text-align: right;'>".$val3."</td>";
						 }
						 if(substr($key3, 0, 2) == 'hr'){
							$harian .= "<td class='screen_3' style='text-align: right;'>".$val3."</td>";
							$tot_hutang[$val2['no_reg']][$key3] += $val3;
							$total .= "<td class='screen_3' style='text-align: right;'>".$tot_hutang[$val2['no_reg']][$key3]."</td>";
						 }
						 if(substr($key3, 0, 3) == 'ttl'){
						 }

	//									echo "<td>".$key3."</td>";
					  }
					  $i++;
				   }

				   if ($stat == 0) {
					  $stat = 1;
					  //echo "<td >".$val2['tgl']."</td>";
					  echo $trm. "<td class='screen_2' rowspan='$count' style='text-align: center;'>$tot_trm</td>"."<td class='screen_2' style='text-align: center;'>".convertElemenTglWaktuIndonesia($val2['tgl_pakai'])."</td>".$pakai. "<td class='screen_2' rowspan='$count' style='text-align: center;'>$tot_pakai</td>"."<td class='screen_3' style='text-align: center;'>".convertElemenTglWaktuIndonesia($val2['tgl_kembali'])."</td>".$kembali. "<td class='screen_3' rowspan='$count' style='text-align: center;'>$tot_kembali</td>".$harian.$total;
				   }else{
					  echo $trm."<td class='screen_2' style='text-align: center;'>".convertElemenTglWaktuIndonesia($val2['tgl_pakai'])."</td>".$pakai."<td class='screen_3' style='text-align: center;'>".convertElemenTglWaktuIndonesia($val2['tgl_kembali'])."</td>".$kembali.$harian.$total;
				   }

				echo "</tr>";
			 }
		  }
      }
	  ?>

   <?php endif ?>
   </tbody>
</table>
<div class="btn prev slider-table" data-current="2" data-min="2" data-max="3" onclick="PJSK.prev(this)"> <i class="glyphicon glyphicon-chevron-left"></i> </div>
<div class="btn next slider-table" data-current="2" data-min="2" data-max="3" onclick="PJSK.next(this)"> <i class="glyphicon glyphicon-chevron-right"></i> </div>
