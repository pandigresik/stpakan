<div style="margin-top:5px">
<table class="table table-bordered">
	<thead>
		<tr>
			<th class="text-center" rowspan="2">Unit</th>
			<th class="text-center" rowspan="2">Pelanggan</th>
			<th class="text-center" colspan="7">SO / DO</th>
			<th class="text-center" rowspan="2">Keterangan</th>
		</tr>
		<tr>
			<th class="text-center">No. SO</th>
			<th class="text-center">Tanggal SO</th>
			<th class="text-center">No. DO</th>
			<th class="text-center">Jenis Barang</th>
			<th class="text-center">Jumlah (Sak)</th>
			<th class="text-center">Harga / Sak</th>
			<th class="text-center">Total Pembayaran (Rp)</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$_dbarang = array();
			$_dharga = array(); 
			$_djumlah = array(); 
			foreach($detail as $d){
				array_push($_dbarang,$barang[$d->kode_barang]['nama_barang']);
				array_push($_djumlah,number_format($d->jumlah,2,',','.'));
				array_push($_dharga,number_format($d->harga_jual,2,',','.'));
			}
			$status_SO = 'active';
			$tgl_sekarang = date('Y-m-d');
			$_dtimeline = array();
			if($header->tgl_so < $tgl_sekarang){
				if($header->status_order != 'A'){
					$tgl_so_date = new \Datetime($header->tgl_so.' 00:01:01');
					$tglBatal =  $tgl_so_date->add(new \DateInterval('P1D'))->format('Y-m-d H:i:s');
					$_dtimeline[] = '[By System] - Dibatalkan, '.convertElemenTglWaktuIndonesia($tglBatal);					
				}
			}
			$keterangan = array(
				'N' => 'Dibuat ',
				'U' => 'Verifikasi ',
				'A' => 'Pembayaran  ',
				'V' => 'Dibatalkan  '
			);
			foreach($log as $l){
				$_dtimeline[] = '['.$pegawai[$l->user_buat]['nama_pegawai'].'], '.$keterangan[$l->status].' '.convertElemenTglWaktuIndonesia($l->tgl_buat);
			}					
			if(!empty($surat_jalans)){
				$_sj = $surat_jalans[0];
				$_dtimeline[] = '['.$pegawai[$_sj->user_buat]['nama_pegawai'].'], Plotting kendaraan '.convertElemenTglWaktuIndonesia($_sj->tgl_buat);;
			}
			$linkCetakSO = site_url("sales_order/sales_order/printSO/".$header->no_so);			
			$linkCetakDO = site_url("sales_order/sales_order/printDO/".$header->no_so);			
			echo '<tr>
				<td>'.$nama_farm.'</td>
				<td>'.$nama_pelanggan.'</td>
				<td><a href="'.$linkCetakSO.'" target="_blank">'.$header->no_so.'</a></td>
				<td>'.tglIndonesia($header->tgl_so,'-',' ').'</td>
				<td><a href="'.$linkCetakDO.'" target="_blank">'.$header->no_so.'</a></td>
				<td><div>'.implode('</div><div>',$_dbarang).'</div></td>
				<td align="right"><div>'.implode('</div><div>',$_djumlah).'</div></td>
				<td align="right"><div>'.implode('</div><div>',$_dharga).'</div></td>
				<td align="right">'.number_format($header->harga_total,2,',','.').'</td>
				<td><div>'.implode('</div><div>',$_dtimeline).'</div></td>
			</tr>';
		?>
	</tbody>
</table>
	<?php		
	if(!empty($surat_jalans)){
	foreach($surat_jalans as $surat_jalan){			
		echo '<div class="row" style="margin:5px">
				<div class="col-md-10 col-md-offset-2">';
			echo '<div class="alert alert-success" data-sj="'.$surat_jalan->no_sj.'" onclick="laporanStokGlangsing.detailSJ(this)">
				'.$surat_jalan->no_sj.'
			</div>';
	$user_realisasi = isset($pegawai[$surat_jalan->user_realisasi]) ? $pegawai[$surat_jalan->user_realisasi]['nama_pegawai'] : '';			
	$realisasi = '<td>[ '.$user_realisasi.' ], Dibuat '.tglIndonesia($surat_jalan->tgl_realisasi,'-',' ').'</td>';		
	echo '<div class="tabel_detail_sj" style="display:none" data-sj="'.$surat_jalan->no_sj.'">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>No. SJ</th>
					<th>Tanggal. SJ</th>
					<th>Sopir</th>
					<th>No. Kendaraan</th>
					<th>Banyaknya</th>
					<th>Nama Barang</th>
					<th>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>'.$surat_jalan->no_sj.'</td>
					<td>'.tglIndonesia($surat_jalan->tgl_buat,'-',' ').'</td>
					<td>'.$surat_jalan->nama_sopir.'</td>
					<td>'.$surat_jalan->no_kendaraan.'</td>
					<td>'.number_format($header->jumlah_total,2,',','.').'</td>
					<td><div>'.implode('</div><div>',$_dbarang).'</div></td>
					'.$realisasi.'
				</tr>
			</tbody>
		</table>
	</div>';
echo '</div></div>';	
	}
}		
	?>				
</div>
