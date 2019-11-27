<div class="">	
		<table class="table table-bordered" id="detailTable">
			<thead>
				<tr>
					<th>Tanggal Pengeluaran</th>
					<th>No. DO</th>
					<th>Pelanggan</th>
					<th>No. Telp Pelanggan</th>
					<th>No. Kendaraan</th>
					<th>Sopir</th>
					<th>No. Telp Sopir</th>
					<th>No. SJ</th>
					<th>Realiasasi Glangsing (Sak)</th>
				</tr>
			</thead>
			<tbody id="main_tbody">
				<?php 				
					if(!empty($surat_jalan)){
						foreach($surat_jalan as $sj){
							$tgl_pengeluaran = empty($sj->tgl_realisasi) ? tglIndonesia(date('Y-m-d'),'-',' ') : tglIndonesia($sj->tgl_realisasi,'-',' ');
							$nomer_sj = empty($sj->tgl_realisasi) ? '' : $sj->no_sj;
							$jml_sak = empty($sj->tgl_realisasi) ? '' : $sj->jml_sak;
							echo '<tr onclick="realisasiPenjualan.rowOnClick(this)" data-no_sj="'.$sj->no_sj.'">
								<td>'.$tgl_pengeluaran.'</td>
								<td>'.$sj->no_do .'</td>
								<td>'.$sj->nama_pelanggan .'</td>
								<td>'.$sj->no_telp_pelanggan .'</td>
								<td>'.$sj->no_kendaraan .'</td>
								<td>'.$sj->nama_sopir .'</td>
								<td>'.$sj->no_telp_sopir .'</td>
								<td class="no_sj">'.$nomer_sj.'</td>
								<td class="jml_sak">'.$jml_sak.'</td>
							</tr>';
						}
					} 
				?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>	
</div>
