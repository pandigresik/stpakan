<?php 
echo '
<div class="col-md-12">		
<table class="table table-bordered konfirmasi_table">
	<thead>
		<tr>
			<th rowspan="2" class="text-center col-md-2">No. Konfirmasi</th>
			<th rowspan="2" colspan="2" class="text-center col-md-4">Farm</th>
			<th colspan="2" class="text-center col-md-3">Populasi</th>
			<th rowspan="2" class="text-center col-md-1">User</th>
			<th rowspan="2" class="text-center col-md-2">Tanggal</th>
		</tr>
		<tr>
			<th class="text-center">Betina</th>
			<th class="text-center">Jantan</th>
		</tr>					
	</thead>
	<tbody>	';
	$data_ke = 1;
	foreach($konfirmasi_ppic as $key0 => $value0){
	foreach($value0 as $key1 => $value1){
		if($data_ke	> 1){
			echo '<tr><td colspan="7"></td></tr>';
		}
		echo '<tr class="tr_header'.$data_ke.'">';
		//echo '<td class="td_no_konfirmasi">'.$value1['no_konfirmasi'].'</td>';
		echo (!empty($value1['no_konfirmasi'])) ? '<td class="td_no_konfirmasi"><span ondblclick="detail_konfirmasi_forecast(this)">'.$value1['no_konfirmasi'].'</span></td>' : '<td class="td_no_konfirmasi">-</td>';
		echo '<td colspan="2" data-kode-farm="'.$value1['kode_farm'].'" class="td_farm"><span class="glyphicon glyphicon-plus" onclick="hide_detail(this,'.$data_ke.')"></span> '.$value1['nama_farm'].'</td>';
		echo '<td class="text-right">'.$value1['populasi_betina'].'</td>';
		echo '<td class="text-right">'.$value1['populasi_jantan'].'</td>';
		echo (!empty($value1['user'])) ? '<td class="td_user">'.$value1['user'].'</td>' : '<td class="td_user"><span class="btn btn-default" onclick="ack('.$data_ke.')">Ack</span></td>';
		echo (!empty($value1['tanggal'])) ? '<td class="td_tanggal text-center">'.tglIndonesia(date('Y-m-d',strtotime($value1['tanggal'])),'-',' ').' '.date('H:i',strtotime($value1['tanggal'])).'</td>' : '<td class="td_tanggal text-center">-</td>';
		echo '</tr>';
		echo '<tr class="hide '.$data_ke.'">';
		$rowspan = count($value1['detail'])+1;
		echo '<td rowspan='.$rowspan.'></td>';
		echo '<th class="text-center">Kandang</td>';
		echo '<th class="text-center">Tanggal Chick-in</td>';
		echo '<th class="text-center">Betina</td>';
		echo '<th class="text-center">Jantan</td>';
		echo '<td rowspan='.$rowspan.' colspan="2"></td>';
		echo '</tr>';
		foreach($value1['detail'] as $key2 => $value2){
			echo '<tr class="hide '.$data_ke.'" ondblclick="perencanaan_doc_in(this)" 
			data-doc-in="'.$value2['tanggal_chickin'].'" 
			data-kode-strain="'.$value2['kode_strain'].'" 
			data-tipe-kandang="'.$value2['tipe_kandang'].'"
			>';
			echo '<td class="td_no_reg" data-no-reg="'.$value2['no_reg'].'">'.$value2['nama_kandang'].'</td>';
			echo '<td class="td_tgl_chickin">'.tglIndonesia(date('Y-m-d',strtotime($value2['tanggal_chickin'])),'-',' ').'</td>';
			echo '<td class="text-right td_populasi_betina">'.$value2['jml_betina'].'</td>';
			echo '<td class="text-right td_populasi_jantan">'.$value2['jml_jantan'].'</td>';
			echo '</tr>';
		}
		$data_ke++;
	}
	}
echo '</tbody>
</table>
</div>		
';
echo '<div id="data-notif" class="hide">';
	if(isset($notif)){
		echo json_encode($notif);
	}
echo '</div>';		
		



