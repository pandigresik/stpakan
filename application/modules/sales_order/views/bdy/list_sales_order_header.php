<div class="panel panel-primary">
	<div class="panel-heading">Laporan Stok Glangsing</div>
	<div class="panel-body">		
		<table class="table table-bordered" id="headerTable">
			<thead>
				<tr>
					<th>No. SO</th>
					<th>Tanggal SO</th>
					<th>No. DO</th>
					<th>Farm Asal</th>
					<th>Pelanggan</th>
					<th>Alamat Pelanggan</th>
					<th>No. Telp<br>Pelanggan</th>
					<th>Term<br>Pembayaran<br>(Hari)</th>
					<th>Jumlah<br>(Sak)</th>
					<th>Total<br>Pembayaran</th>
					<th>Keterangan</th>
				</tr>
			</thead>
			<tbody id="main_tbody">
				<?php
					if(!empty($list_so)){						
						foreach($list_so as $so){
							$keterangan = array();
							$statusStr = array(
								'N' => 'Dibuat',
								'U' => 'Diverifikasi',
								'A' => 'Diverifikasi',
								'V' => 'Dibatalkan'
							);
							
							foreach($keterangan_so[$so['no_so']] as $k){
								$keterangan[] = '['.$k['nama_pegawai'].'] - '.$statusStr[$k['status']].', '.convertElemenTglWaktuIndonesia($k['tgl_buat']);
							}
							$class_ref = empty($so['no_ref']) ? '' : 'bg-pink';
							echo '<tr class="'.$class_ref.'" ondblClick="salesOrder.showDetail(this)" data-status_order="'.$so['status_order'].'" onclick="salesOrder.rowOnClick(this)" data-no_so="'.$so['no_so'].'">
								<td>'.$so['no_so'].'</td>
								<td>'.tglIndonesia($so['tgl_so'],'-',' ').'</td>
								<td>'.$so['no_so'].'</td>
								<td>'.$so['nama_farm'].'</td>
								<td>'.$so['NAMA_PELANGGAN'].'</td>
								<td>'.$so['alamat'].'</td>	
								<td>'.$so['no_telp'].'</td>
								<td>'.$so['term_pembayaran'].'</td>
								<td>'.$so['jumlah_total'].'</td>
								<td align="right">'.angkaRibuan($so['harga_total']).'</td>
								<td><div>'.implode('</div><div>',$keterangan).'</div></td>
							</tr>';
						}						
					}
					/** tampilan default */						
					echo '<tr class="new">
							<td></td>
							<td>'.tglIndonesia($tgl_sekarang,'-',' ').'</td>
							<td></td>
							<td>'.$nama_farm[$kode_farm].'</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>';
				?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>
