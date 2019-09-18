<?php 
$jml_pakan = count($header_pakan);
$th_arr = array('forecast_pakan' => 'Forecast','kebutuhan_pakan' => 'Simulasi','pp' =>'PP');
$colspan = $jml_pakan *  count($th_arr);
echo '
<div class="container col-md-12">		
<table class="table table-bordered custom_table">
	<thead>
		<tr>
			<th rowspan="3" class="ftl">Periode</th>
			<th rowspan="3" class="ftl">Tanggal Kebutuhan</th>
			<th colspan="'.$colspan.'">Pakan</th>
		</tr>
		<tr>';
		foreach($header_pakan as $kodepj => $pakan){
			echo '<th colspan="3" data-kodepj="'.$kodepj.'">'.$pakan['nama'].' '.convertKode('bentuk_barang',$pakan['bentuk']).'</th>';
		}				
echo '</tr>
	  <tr>';
		$i = 1;
		do{
			foreach($th_arr as $th){
				echo '<th>'.$th.'</th>';
			}
			$i++;
		}while($i <= $jml_pakan);
		
echo '</tr>						
	</thead>
	<tbody>	';
	foreach($rekap as $indexBulan => $perbulan){
		foreach($perbulan as $indexPekan => $perpekan){
			$jml_tgl_kebutuhan = count($perpekan);
			foreach($perpekan as $indextgl => $pertglkebutuhan){
			echo '<tr>';
				if($indextgl == $list_pekan[$indexBulan][$indexPekan]['awal']){
					echo '<td class="text-center ftl" rowspan="'.$jml_tgl_kebutuhan.'">'.tglIndonesia($list_pekan[$indexBulan][$indexPekan]['awal'],'-',' ').' - '.tglIndonesia($list_pekan[$indexBulan][$indexPekan]['akhir'],'-',' ').'</td>';
				}
				echo '<td class="text-center ftl">'.tglIndonesia($indextgl,'-',' ').'</td>';
				foreach($header_pakan as $kodepj => $pakan){
					foreach($th_arr as $index => $th){
						$jml_j = isset($pertglkebutuhan[$kodepj]['jantan'][0][$index]) ? $pertglkebutuhan[$kodepj]['jantan'][0][$index] : 0;
						$jml_b = isset($pertglkebutuhan[$kodepj]['betina'][0][$index]) ? $pertglkebutuhan[$kodepj]['betina'][0][$index] : 0;
						echo '<td class="number">'.($jml_j + $jml_b).'</td>';
					}
				}
			echo '</tr>';	 
			}
		}
	}
echo '</tbody>
</table>
</div>		
';



