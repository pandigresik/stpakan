<div class="row">
	<div class="col-md-3"> Gudang 01 </div>
</div>
<table class="table table-bordered custom_table" >
	<thead>
			<tr>
				<th rowspan="2">TGL DATANG.</th>
 				<th rowspan="2">KODE PALLET</th>
				<th rowspan="2">KODE BARANG</th>
				<th rowspan="2">NO DO</th>
				<th rowspan="2">FLOK</th>
				<th rowspan="2">NO PENYIMPANAN</th>
				<th rowspan="2">KETERANGAN</th>
				<th rowspan="2">NO REFERENSI</th>
				<th colspan="2">TERIMA</th>
				<th colspan="2">KELUAR</th>
				<th rowspan="2">SUSUT(KG)</th>
				<th rowspan="2">TGL SIMPAN</th>
			</tr>
			<tr>
				<th>SAK</th>
				<th>KG</th>
				<th>SAK</th>
				<th>KG</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$html = array();
		if(!empty($lists)){
			foreach($lists as $ls){
				$terima = $ls['penerimaan'];
				$rowspan = count($ls['pengambilan'])+2;
				
				$put_sak = $terima['terima_sak'];
				$put = '
				<tr>
					<td rowspan="'.$rowspan.'">'.tglIndonesia($terima['tgl_datang'],'-',' ').'</td>
					<td rowspan="'.$rowspan.'">'.$terima['kode_pallet'].'</td>
					<td rowspan="'.$rowspan.'">'.$terima['kode_barang'].'</td>
					<td rowspan="'.$rowspan.'">'.$terima['no_do'].'</td>
					<td rowspan="'.$rowspan.'">'.$terima['flok'].'</td>
					<td rowspan="'.$rowspan.'">'.$terima['no_penyimpanan'].'</td>
					<td>'.$terima['keterangan'].'</td>
					<td></td>
					<td>'.formatAngka($terima['terima_sak'],2).'</td>
					<td>'.formatAngka($terima['terima_kg'],2).'</td>
					<td></td>
					<td></td>
					<td></td>
					<td>'.convertElemenTglWaktuIndonesia($terima['tgl_simpan']).'</td>
				</tr>';
				
				// echo '<tr>
					// <td rowspan="'.$rowspan.'">'.tglIndonesia($terima['tgl_datang'],'-',' ').'</td>
					// <td rowspan="'.$rowspan.'">'.$terima['kode_pallet'].'</td>
					// <td rowspan="'.$rowspan.'">'.$terima['kode_barang'].'</td>
					// <td rowspan="'.$rowspan.'">'.$terima['no_do'].'</td>
					// <td rowspan="'.$rowspan.'">'.$terima['flok'].'</td>
					// <td rowspan="'.$rowspan.'">'.$terima['no_penyimpanan'].'</td>
					// <td>'.$terima['keterangan'].'</td>
					// <td></td>
					// <td>'.formatAngka($terima['terima_sak'],2).'</td>
					// <td>'.formatAngka($terima['terima_kg'],2).'</td>
					// <td></td>
					// <td></td>
					// <td></td>
					// <td>'.convertElemenTglWaktuIndonesia($terima['tgl_simpan']).'</td>
				// </tr>';
				$keluar = $ls['pengambilan'];
				$total_keluar = 0;
				$pick_sak = 0;
				$pick = '';
				foreach($keluar as $kl){
					$pick_sak += $kl['ambil_sak'];
					$pick .= '
					<tr>
						<td>'.$kl['keterangan'].'</td>
						<td>'.$kl['no_referensi'].'</td>
						<td></td>
						<td></td>
						<td>'.formatAngka($kl['ambil_sak'],2).'</td>
						<td>'.formatAngka($kl['ambil_kg'],2).'</td>
						<td></td>
						<td>'.convertElemenTglWaktuIndonesia($kl['tgl_simpan']).'</td>
					</tr>';
					
					// echo '<tr>
						// <td>'.$kl['keterangan'].'</td>
						// <td>'.$kl['no_referensi'].'</td>
						// <td></td>
						// <td></td>
						// <td>'.formatAngka($kl['ambil_sak'],2).'</td>
						// <td>'.formatAngka($kl['ambil_kg'],2).'</td>
						// <td></td>
						// <td>'.convertElemenTglWaktuIndonesia($kl['tgl_simpan']).'</td>
					// </tr>';
					$total_keluar += $kl['ambil_kg'];
				}
				$susut = $total_keluar - $terima['terima_kg'];
				
				if(($put_sak-$pick_sak)==0){
					echo $put;
					echo $pick;
					
					echo '<tr>
						<td colspan="6">Habis Kavling</td>
						<td class="'.($susut < 0 ? 'abang': '').'">'.formatAngka($susut,2).'</td>
						<td></td>
					</tr>';
				}
			}
		}
		?>
		</tbody>
	</table>
