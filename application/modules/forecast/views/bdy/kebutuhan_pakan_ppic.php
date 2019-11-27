<div class="table-responsive">
<table class="table table-bordered custom_table" id="tabelrencanakirim">
	<thead>
		<tr class="hide">
			<th colspan=4>Breakdown Kebutuhan Pakan</th>
		</tr>
		<tr class="hide">
			<th colspan=2></th>
		</tr>
		<tr class="hide">
			<th colspan=2>Farm</th><th><?php echo $nama_farm ?></th>
		</tr>
		<tr class="hide">
			<th colspan=2>DOC In</th><th><?php echo tglIndonesia($docin,'-',' ') ?></th>
		</tr>
		<tr class="hide">
			<th colspan=2>Populasi</th><th><?php echo $populasi ?></th>
		</tr>
		<tr class="hide">
			<th colspan=2></th>
		</tr>
		<tr>
			<th rowspan="2">Tanggal Kebutuhan</th>
			<th colspan="3">Forecast</th>
			<th colspan="3">PP</th>
		</tr>

		<tr>
			<th>Tanggal Kirim</th>
			<?php
			foreach($grouping['mb'] as $k => $val){
				echo '<th>'.trim($val).'</th>';
			}
			?>
			<th>Tanggal Kirim</th>
			<?php
			foreach($grouping['mb'] as $k => $val){
				echo '<th class="namapakan" data-kodepakan="'.$k.'">'.trim($val).'</th>';
			}
			?>
		</tr>
	</thead>
	<tbody>
<?php
$tmp_tgl = '';
foreach($ack as $a){
	if($tmp_tgl != $a['tgl_kebutuhan']){
		echo '<tr>';
		echo '<td>'.tglIndonesia($a['tgl_kebutuhan'],'-',' ').'</td>';
		echo '<td class="tgl_kirim" data-tglkirim="'.$a['tgl_kirim'].'">'.tglIndonesia($a['tgl_kirim'],'-',' ').'</td>';
		foreach($grouping['mb'] as $k => $val){
			echo '<td class="number">'.(isset($grouping['forecast'][$a['tgl_kebutuhan']][$k]) ? number_format($grouping['forecast'][$a['tgl_kebutuhan']][$k],2,',','.') : '-').'</td>';
		}
		echo '<td  class="tgl_kirimpp" data-tglkirimpp="'.$a['tgl_kirim_pp'].'">'.(!empty($a['tgl_kirim_pp']) ? tglIndonesia($a['tgl_kirim_pp'],'-',' ') : '-').'</td>';
		foreach($grouping['mb'] as $k => $val){
			echo '<td class="number '.$k.'" data-tglkirimpp="'.$a['tgl_kirim_pp'].'">'.(isset($grouping['pp'][$a['tgl_kirim_pp']][$k]) ? $grouping['pp'][$a['tgl_kirim_pp']][$k] : '-').'</td>';
		}
		echo '</tr>';
		$tmp_tgl = $a['tgl_kebutuhan'];
	}

}
?>
	</tbody>
</table>
</div>