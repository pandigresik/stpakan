<?php

$rows = "";

$umur_akhir = 0;

$masa_pertumbuhan_arr = array();
$col_pakantarget_arr = array();
$index_masa_pertumbuhan = 0;

foreach($range as $r){
	if(isset($masa_pertumbuhan) and $masa_pertumbuhan == "LAYER"){
		if(strtoupper($r["deskripsi"]) != "LAYER")
			continue;
	}else{
		if(strtoupper($r["deskripsi"]) == "LAYER")
			continue;
	}
	
	$br = "";
	for($i=0;$i<((($r["umur_akhir"] - $umur_akhir + 1))/2);$i++){
		$br .= "&nbsp;<br/>";
	}
		
	$pengurangan = ($detail_row[$r['umur_awal']]['pengurangan'] > 0) ? $detail_row[$r['umur_awal']]['pengurangan'] : '-';
	$mati = ($detail_row[$r['umur_awal']]['mati_prc'] > 0) ? $detail_row[$r['umur_awal']]['mati_prc'] : '-';
	$afkir = ($detail_row[$r['umur_awal']]['afkir_prc'] > 0) ? $detail_row[$r['umur_awal']]['afkir_prc'] : '-';
	$seleksi = ($detail_row[$r['umur_awal']]['seleksi_prc'] > 0) ? $detail_row[$r['umur_awal']]['seleksi_prc'] : '-';
	
	$cur_jenis_pakan = $detail_row[$r['umur_awal']]['deskripsi_full'];
	$similar_jenis_pakan = 1;
	
	for($j=($r["umur_awal"]+1);$j<=$r["umur_akhir"];$j++){
		
		if($detail_row[$j]['deskripsi_full'] == $cur_jenis_pakan)
			$similar_jenis_pakan++;
	}
	
	$rowspan_pakan = ($similar_jenis_pakan > 1) ? 'rowspan="'.$similar_jenis_pakan.'"' : '';
	
	$rows .= '
	<tr>
		<td rowspan="' . ($r["umur_akhir"] - $umur_akhir + 1) . '" style="vertical-align:middle;text-align:center"><span style="font-size:6px">'.$br.$r["deskripsi"].'</span></td>
		<td class="col-center">'.$r['umur_awal'].'</td>
		<td class="col-center">'.$pengurangan.'</td>
		<td class="col-center">'.$mati.'</td>
		<td class="col-center">'.$afkir.'</td>
		<td class="col-center">'.$seleksi.'</td>
		<td class="col-right">'.$detail_row[$r['umur_awal']]['dh_prc'].'</td>
		<td class="col-right">'.$detail_row[$r['umur_awal']]['target_pkn'].'</td>
		<td class="col-right">'.$detail_row[$r['umur_awal']]['energi'].'</td>
		<td class="col-right">'.$detail_row[$r['umur_awal']]['total_energi'].'</td>
		<td class="col-right">'.$detail_row[$r['umur_awal']]['protein'].'</td>
		<td class="col-right">'.$detail_row[$r['umur_awal']]['total_protein'].'</td>
		<td class="col-right">'.$detail_row[$r['umur_awal']]['target_bb'].'</td>
		<td class="col-right">'.$detail_row[$r['umur_awal']]['bb_prc'].'</td>
		<td '.$rowspan_pakan.'>'.$detail_row[$r['umur_awal']]['deskripsi_full'].'</td>
		<td>'.$detail_row[$r['umur_awal']]['keterangan'].'</td>
	</tr>';
	
	$col_pakantarget_arr[] = $detail_row[$r['umur_awal']]['target_pkn'];
	
	for($i=($r["umur_awal"]+1);$i<=$r["umur_akhir"];$i++){
		$col_pakantarget_arr[] = $detail_row[$i]['target_pkn'];
		
		$pengurangan = ($detail_row[$i]['pengurangan'] > 0) ? $detail_row[$i]['pengurangan'] : '-';
		$mati = ($detail_row[$i]['mati_prc'] > 0) ? $detail_row[$i]['mati_prc'] : '-';
		$afkir = ($detail_row[$i]['afkir_prc'] > 0) ? $detail_row[$i]['afkir_prc'] : '-';
		$seleksi = ($detail_row[$i]['seleksi_prc'] > 0) ? $detail_row[$i]['seleksi_prc'] : '-';
	
		if($similar_jenis_pakan > 1){
			$rowspan_pakan = '';
			
			$similar_jenis_pakan--;
		}else{
			$cur_jenis_pakan = $detail_row[$i]['deskripsi_full'];
			$similar_jenis_pakan = 0;
			for($j=$i;$j<=$r["umur_akhir"];$j++){
				if($detail_row[$j]['deskripsi_full'] == $cur_jenis_pakan)
					$similar_jenis_pakan++;
			}
			
			$rowspan_pakan = '<td rowspan="'.$similar_jenis_pakan.'">'.$cur_jenis_pakan.'</td>';
		}
	
		$rows .='
		<tr>
		<td class="col-center">'.$i.'</td>
		<td class="col-center">'.$pengurangan.'</td>
		<td class="col-center">'.$mati.'</td>		
		<td class="col-center">'.$afkir.'</td>
		<td class="col-center">'.$seleksi.'</td>
		<td class="col-right">'.$detail_row[$i]['dh_prc'].'</td>
		<td class="col-right">'.$detail_row[$i]['target_pkn'].'</td>
		<td class="col-right">'.$detail_row[$i]['energi'].'</td>
		<td class="col-right">'.$detail_row[$i]['total_energi'].'</td>
		<td class="col-right">'.$detail_row[$i]['protein'].'</td>
		<td class="col-right">'.$detail_row[$i]['total_protein'].'</td>
		<td class="col-right">'.$detail_row[$i]['target_bb'].'</td>
		<td class="col-right">'.$detail_row[$i]['bb_prc'].'</td>
		'.$rowspan_pakan.'
		<td>'.$detail_row[$i]['keterangan'].'</td>
		</tr>
		';
	}
	$umur_akhir = $r["umur_akhir"] + 1;
	
	$index_masa_pertumbuhan++;
}

$jenis_kelamin = (strtoupper($jk) == 'J') ? "JANTAN" : "BETINA";
$tipe_kandang = (strtoupper($tipe) == 'J') ? "CLOSED" : "OPEN";

$standar_target_html = "";

if($masa_pertumbuhan != "LAYER"){
	$summary = array();
	$summary_avg = array();
	for($i=0; $i<count($range); $i++){
		
		$summary_sub = 0;
		$min = $range[$i]['umur_awal'];
		$max = $range[$i]['umur_akhir'];
		
		for($j=$min; $j<=$max; $j++){
			$summary_sub += $col_pakantarget_arr[$j];
		}
		
		$summary[$i] = sprintf("%01.2f", ($summary_sub * 7 / 1000));
	}

	$summary_grand = 0;
	$summary_grand_avg = 0;
	for($i=0;$i<count($summary);$i++){
		$summary_grand += ($summary[$i]);
	}

	$summary_grand = $summary_grand;

	for($i=0;$i<count($summary);$i++){
		$summary_avg[$i] = sprintf("%01.2f", ($summary[$i] / $summary_grand * 100));
	}

	for($i=0;$i<count($summary_avg);$i++){
		$summary_grand_avg += ($summary_avg[$i]);
	}

	$standar_target_html = '
	<table width="100%" style="border:1px solid #fff;"><tr><td width="60%" style="border:1px solid #fff;"></td><td width="40%" style="border:1px solid #fff;">
	<span style="font-size:10px;">Standar Target</span>
	<div class="borderless">
	<table style="font-size:7px;" cellspacing="2" cellpadding="5">';
	for($i=0; $i<count($range); $i++){
		$standar_target_html .= '
		<tr>
			<td width="50" style="border:1px solid #fff;" >'.$range[$i]["deskripsi"].'</td>
			<td width="40" align="right">'.$summary[$i].'</td>
			<td width="35" style="border:1px solid #fff;" align="left">Kg/Ekr</td>
			<td width="40" align="right">'.$summary_avg[$i].'</td>
			<td width="25" style="border:1px solid #fff;" align="left">%</td>
		</tr>
		';
	}
	$standar_target_html .= '
		<tr>
			<td width="50" style="border:1px solid #fff;">TOTAL</td>
			<td width="40" align="right">'.sprintf("%01.2f", $summary_grand).'</td>
			<td width="35" style="border:1px solid #fff;" align="left"></td>
			<td width="40" align="right">'.sprintf("%01.2f", $summary_grand_avg).'</td>
			<td width="25" style="border:1px solid #fff;" align="left"></td>
		</tr>
		';
	$standar_target_html .= '</table></div></td></tr></table>';
}

echo '
	<span style="text-align:center;">Standar Budidaya Parent Stock</span>
	<br/>
	<span style="text-align:center;">Periode '.$masa_pertumbuhan.' - '.$jenis_kelamin.' '.$tipe_kandang.' HOUSE</span>
	<br/>
	<span style="text-align:center;">Tahun '.$tahun.'</span>
	<br/></br/>
	<br/></br/>
	
	<table style="font-size:7px;" border="1" colspan="0" cellpadding="2">
		<thead>
			<tr>
				<th class="header" rowspan="2">Masa Pertumbuhan</th>
				<th class="header" rowspan="2">Umur Minggu</th>
				<th class="header" rowspan="2">Pengurangan</th>
				<th class="header" colspan="3">Deplesi</th>
				<th class="header" rowspan="2">Daya Hidup<br/>(%)</th>
				<th class="header" colspan="5">Pakan</th>
				<th class="header" colspan="2">Berat Badan</th>
				<th class="header" rowspan="2">Jenis Pakan</th>
				<th class="header col-sm-2" rowspan="2">Keterangan</th>
			</tr>
			<tr>
				<th class="header">Mati<br/>(%)</th>
				<th class="header">Afkir<br/>(%)</th>
				<th class="header">Seleksi<br/>(%)</th>
				<th class="header">Gr/Ek/Hr Target</th>
				<th class="header">Energi (kcal/hr)</th>
				<th class="header">Cum. Energi (kcal)</th>
				<th class="header">Protein<br/>(gr)</th>
				<th class="header">Cum. Protein (gr)</th>
				<th class="header">Gr/Ek Target</th>
				<th class="header">Weight Gain<br/>(%)</th>
			</tr>
		</thead>
		<tbody>'.$rows.'
		</tbody>
	</table>
';

if($masa_pertumbuhan != "LAYER"){
	echo '<br/><br/>'.$standar_target_html;
}

?>



<style>
	table {
		border-collapse: collapse;
	}

	table, th, td {
		border: 1px solid black;
	}
	
	.borderless table{
		border: 1px solid #fff;
	}
		
	.header{
		vertical-align: middle;
		text-align: center;
	}
	
	.detail{
		background-color:red;
	}
	
	.col-center{
		text-align:center;
	}
	
	.col-right{
		text-align:right;
	}
</style>