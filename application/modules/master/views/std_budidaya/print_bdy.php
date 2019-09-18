<?php

$rows = "";

$umur_akhir = 0;

$masa_pertumbuhan_arr = array();
$col_pakantarget_arr = array();

$similar_jenis_pakan = 1;
$index_row = 0;

$jenis_pakan = array();
$cur_pakan = "";
$key_similar = 0;

$i = 0;
foreach($detail_row as $key=>$value){
	if(!empty($cur_pakan) and !empty($value["nama_barang"]) and $cur_pakan == $value["nama_barang"]){
		$detail_row[$key_similar]["rowspan"] += 1;
		$detail_row[$key]["rowspan"] = 'x';
	}else{
		$key_similar = $key;
		$detail_row[$key]["rowspan"] = 1;
	}
	
	
	$cur_pakan = $value["nama_barang"];
}

foreach($detail_row as $data){
	$cur_jenis_pakan = $data["nama_barang"];
	
	$colPakan = "";
	if($data["rowspan"] > 1){
		$br = "";
		for($i=0;$i<($data["rowspan"]/2)+1;$i++){
			$br .="<br/>";
		}
		$colPakan = '<td width="150" rowspan="'.$data["rowspan"].'" style="text-align:center;vert-align:middle;" vertical-align="middle">'.$br.$cur_jenis_pakan.'</td>';
	}else{
		if($data["rowspan"] != 'x')
			$colPakan = '<td width="150" style="text-align:center;vert-align:middle;" vertical-align="middle">'.$cur_jenis_pakan.'</td>';
	}
	
	$rows .= '
	<tr>
		<td class="col-center" width="25">'.$data['STD_UMUR'].'</td>
		<td class="col-right" width="40">'.$data['DH_KUM_PRC'].'</td>
		<td class="col-right" width="40">'.number_format(($data['DH_HR_PRC']*10/10), 2, '.', '').'</td>
		<td class="col-right" width="40">'.$data['PKN_KUM_STD'].'</td>
		<td class="col-right" width="40">'.$data['PKN_HR_STD'].'</td>
		<td class="col-right" width="40" style="background-color:#FAE9CD">'.$data['PKN_KUM'].'</td>
		<td class="col-right" width="40" style="background-color:#FAE9CD">'.$data['PKN_HR'].'</td>
		<td class="col-right">'.$data['TARGET_BB'].'</td>
		<td class="col-right">'.number_format($data['FCR'], 3, '.', '').'</td>
		'.$colPakan.'
	</tr>';

	$index_row++;
}
$months = array();
$months[0] = "Jan";
$months[1] = "Feb";
$months[2] = "Mar";
$months[3] = "Apr";
$months[4] = "May";
$months[5] = "Jun";
$months[6] = "Jul";
$months[7] = "Aug";
$months[8] = "Sep";
$months[9] = "Oct";
$months[10] = "Nov";
$months[11] = "Dec";

$months_id = array(12);
$months_id[0] = "Januari";
$months_id[1] = "Februari";
$months_id[2] = "Maret";
$months_id[3] = "April";
$months_id[4] = "Mei";
$months_id[5] = "Juni";
$months_id[6] = "Juli";
$months_id[7] = "Agustus";
$months_id[8] = "September";
$months_id[9] = "Oktober";
$months_id[10] = "Nopember";
$months_id[11] = "Desember";

$tgl_arr = explode(',', $masa_berlaku);
$tgl = $tgl_arr[0]*1 . ' ' . $months_id[array_search($tgl_arr[1], $months)] .' '. $tgl_arr[2];
	
echo '
	<span style="text-align:center;">Standar Performa Budidaya</span>
	<br/>
	<span style="text-align:center;">Berlaku per '.$tgl.'</span>
	<br/></br/>
	<br/></br/>
	
	<table style="font-size:7px;" border="1" colspan="0" cellpadding="2">
		<thead>
			<tr>
				<th class="vert-align" rowspan="2" width="25">Umur</th>
				<th class="vert-align" colspan="2" width="80">Daya Hidup</th>
				<th class="vert-align" colspan="2" width="80">Standar Pakan (gr)</th>
				<th class="vert-align" colspan="2" width="80" style="background-color:#FAE9CD">Budget Pakan (gr)</th>
				<th class="vert-align" rowspan="2">BB</th>
				<th class="vert-align" rowspan="2">FCR</th>
				<th class="vert-align" rowspan="2" width="150">Jenis Pakan</th>
			</tr>
			<tr>
				<th class="vert-align" width="40">KUM</th>
				<th class="vert-align" width="40">HR</th>
				<th class="vert-align" width="40">KUM</th>
				<th class="vert-align" width="40">HR</th>
				<th class="vert-align" width="40" style="background-color:#FAE9CD">KUM</th>
				<th class="vert-align" width="40" style="background-color:#FAE9CD">HR</th>
			</tr>
		</thead>
		<tbody>'.$rows.'
		</tbody>
	</table>
';
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
	
	.vert-align{
		vertical-align: middle;
		text-align : center;
	}
	.table tbody>tr>td.vert-align{
		vertical-align: middle;
		text-align : center;
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