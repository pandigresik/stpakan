<?php
if(!empty($kebutuhan_pakan)){
echo '<div class="table-responsive">';	
	echo '<table class="table custom_table">';
	echo '<thead>
		<tr>
			<th rowspan="2">Nama Kandang</th>
			<th rowspan="2">Kode Pakan</th>
			<th rowspan="2">Nama Pakan</th>
			<th rowspan="2">Sisa Hutang PP</th>
			<th rowspan="2">Sisa Hutang Retur Sak</th>
			<th rowspan="2">Sisa Kebutuhan</th>
			<th rowspan="2">Kuantitas Kebutuhan</th>
			<th rowspan="2">Kuantitas PP</th>
			<th colspan="2">Rekomendasi PP</th>
			<th colspan="2">Persetujuan PP</th>
		</tr>
		<tr>
			<th>Kuantitas</th>
			<th>Alasan</th>
			<th>Kuantitas</th>
			<th>Alasan</th>
		</tr>
	</thead>';
	echo '<tbody>';
	foreach($summary_perpakan as $kb =>$perpakan){
		$kode_pakan = $kb;
		$nama_pakan = $perpakan['nama_barang'];
		foreach($perpakan['kandang'] as $kd => $perkandang){
			$kuantitas_keb = $perkandang['kuantitas_keb'];
			$kuantitas_pp = $perkandang['kuantitas_pp'];
			$no_reg = $perkandang['no_reg'];
			$hutang_pp = isset($sisa_pakan[$no_reg][$kode_pakan]['hutang_pp_sebelumnya']) ? $sisa_pakan[$no_reg][$kode_pakan]['hutang_pp_sebelumnya'] : '';
			$hutang_retur_sak = isset($sisa_pakan[$no_reg][$kode_pakan]['hutang_retur_sak']) ? $sisa_pakan[$no_reg][$kode_pakan]['hutang_retur_sak'] : '';
			$sisa_kebutuhan = isset($sisa_pakan[$no_reg][$kode_pakan]['sisa_kebutuhan']) ? $sisa_pakan[$no_reg][$kode_pakan]['sisa_kebutuhan'] : '';
			/* ambil total rekomendasi dan review */
			$sumReview = array(
				'jml_rekomendasi' => 0,
				'ket_rekomendasi' => array(),
				'jml_review' => 0,
				'ket_review' => array()
			);
			foreach($review[$kode_pakan][$no_reg] as $vv){
				$sumReview['jml_rekomendasi'] += $vv['JML_REKOMENDASI'];
				$sumReview['jml_review'] += $vv['JML_REVIEW'];
				if(!empty($vv['KET_REKOMENDASI'])){
						array_push($sumReview['ket_rekomendasi'], $vv['KET_REKOMENDASI']);
				}
				if(!empty($vv['KET_REVIEW'])){
						array_push($sumReview['ket_review'], $vv['KET_REVIEW']);
				}

			}
	
			$sumReview['ket_review'] = '<div>'.implode(array_unique($sumReview['ket_review']),'</div><div>').'</div>';
			$sumReview['ket_rekomendasi'] = '<div>'.implode(array_unique($sumReview['ket_rekomendasi']),'</div><div>').'</div>';

			echo '<tr>
					<td> Kandang '.$kd.'</td>
					<td>'.$kode_pakan.'</td>
					<td>'.$nama_pakan.'</td>
					<td>'.$hutang_pp.'</td>
					<td>'.$hutang_retur_sak.'</td>
					<td>'.$sisa_kebutuhan.'</td>
					<td>'.$kuantitas_keb.'</td>
					<td>'.$kuantitas_pp.'</td>
					<td>'.$sumReview['jml_rekomendasi'].'</td>
					<td>'.$sumReview['ket_rekomendasi'].'</td>
					<td>'.$sumReview['jml_review'].'</td>
					<td>'.$sumReview['ket_review'].'</td>
				</tr>';
		}
	}
	echo '</tbody>';
	echo '</table>';
echo '</div>';	
}


?>
