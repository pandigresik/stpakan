
<table class='table table-bordered custom-table'>
	<thead>
		<tr>
			<th>Permintaan Pakan</th>
			<th>Nama Farm</th>
			<th>Nama Pakan</th>
			<th>Jumlah Permintaan (sak)</th>
			<th>Tanggal Pengambilan</th>
			<th>Tanggal Akhir Rencana Produksi</th>
			<th>Kode Rencana Produksi</th>
			<th>Realisasi Produksi</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		if(!empty($kp)){
			$k_rp = array(); /* konfirmasi rencana produksi */
			foreach($kp as $k){
			/*	
				$jml_rp = isset($k_rp[$k['no_lpb']][$k['kode_barang']]) ? count($k_rp[$k['no_lpb']][$k['kode_barang']]) : null;
				$input_rp = empty($jml_rp) ? '<div class="text-center"><span class="btn btn-default" onclick="Forecast.konfirmasi_rencana_produksi(this)">Input</span></div>' : null;
				$rowspan = !empty($jml_rp) ? $jml_rp : 1; 
				$input_kavling = '<div class="text-center"><span class="btn btn-default" onclick="Forecast.konfirmasi_kavling(this)">Input</span></div>';
				$input_sisa_kavling = '<div class="text-center"><span class="btn btn-default" onclick="Forecast.konfirmasi_kavling(this)">Input</span></div>';
			*/	
			//	$akhir_rp = empty($k['tgl_akhir_rencana_produksi']) ? '<input readonly type="text" name="tgl_akhir_rencana_produksi">': tglIndonesia($k['tgl_akhir_rencana_produksi'],'-',' ');
				$koderp = '';
				$adarp = '';
				if(!empty($k['tgl_akhir_rencana_produksi'])){
					$akhir_rp = tglIndonesia($k['tgl_akhir_rencana_produksi'],'-',' ');
					if(empty($k['rencana_produksi'])){
						$koderp = '&nbsp; <span class="glyphicon glyphicon-plus-sign" onclick="Konfirmasi_rp.load_rencana_produksi(this)"></span>';
					}
					else{
						$koderp = tabelkonfirmasippic($k['rencana_produksi'],$k['realisasi_produksi']);
						$adarp = 'adarp';
					}
				}
				else{
					$akhir_rp = '<input readonly type="text" name="tgl_akhir_rencana_produksi">';
				}
			//	$koderp = empty($k['rencana_produksi']) ? '' : tabelkonfirmasippic($k['rencana_produksi'],$k['realisasi_produksi']);
				echo '
				<tr class="header" data-no_op="'.$k['no_op'].'" data-konfirmasi="'.$k['id_konfirmasi'].'">
					<td class="no_lpb">'.$k['no_lpb'].'</td>
					<td class="nama_farm" >'.$k['nama_farm'].'</td>
					<td class="nama_barang" data-kode_barang="'.$k['kode_barang'].'" >'.$k['nama_barang'].'</td>
					<td class="jml_order text-right">'.$k['jml_permintaan'].'</td>
					<td class="tgl_kirim text-right">'.tglIndonesia($k['tgl_kirim'],'-',' ').'</td>
					<td class="tgl_akhir_rencana_produksi">'.$akhir_rp.'</td>
					<td class="koderp '.$adarp.'">'.$koderp.'</td>
					<td>'.$k['realisasi_produksi'].'</td>
				</tr>';
				
			}
		}	
		?>
	</tbody>
</table>