<div class="row">
	<?php
	if(!empty($rhk)){
	?>
		<div class="col-md-3"> Tanggal Doc In : <?php echo $rhk[0]['tglDocIn'] ?></div>
		<div class="col-md-3"> Hatchery : -</div>
		<div class="col-md-3"> Berat DOC : <?php echo formatAngka($rhk[0]['bbDoc'],2) ?></div>
		<div class="col-md-3"> Populasi awal : <?php echo formatAngka($rhk[0]['stokAwal'],0) ?></div>
	<?php
	}
	?>
	
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
//print_r($rhk); 
		if(!empty($rhk)){
			$con = 0;
			foreach($rhk as $r){
				$con++;
				if($con == 5){
					echo '<tr class="rekap panen">';
				}else{
					echo '<tr>';
				}
				echo '<td  class="number ftl">'.str_replace(".00", "", $r['umur']).'</td>';
				echo '<td class="text-center tanggal ftl">'.$r['tanggal'].'</td>';
				echo '<td  class="number ">'.$r['jmlMati'] .'</td>';
				echo '<td  class="number ">'.formatAngka($r['prcMati'],3) .'</td>';
				echo '<td  class="number ">'.$r['jmlAfkir'] .'</td>';
				echo '<td  class="number ">'.formatAngka($r['prcAfkir'],3) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['jmlPopulasi'],0) .'</td>';
				echo '<td  class="number dh">'.formatAngka($r['dhReal'],2) .'</td>';
				echo '<td  class="number dh">'.formatAngka($r['dhStd'],2) .'</td>';
				echo '<td  class="number dh">'.formatAngka($r['dhBanding'],2) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['jmlPakan'],0) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['brtPakan'],3) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['brtPakanKum'],3) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['brtStd'],0) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['brtBanding'],3) .'</td>';
				echo '<td  class="number ">'.$r['bbReal'] .'</td>';
				echo '<td  class="number ">'.$r['bbStd'] .'</td>';
				echo '<td  class="number ">'.formatAngka($r['bbBanding'],3) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['adgReal'],3) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['adgStd'],3) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['adgBanding'],3) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['fcrReal'],3) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['fcrStd'],3) .'</td>';
				echo '<td  class="number ">'.formatAngka($r['fcrBanding'],3) .'</td>';
				echo '<td  class="number ">'.$r['ipReal'] .'</td>';
				echo '<td  class="number ">'.$r['ipStd'] .'</td>';
				echo '<td  class="number ">'.formatAngka($r['ipBanding'],3) .'</td>';
				
				echo '</tr>';
			}
		}
			
		?>
		</tbody>
	</table>
