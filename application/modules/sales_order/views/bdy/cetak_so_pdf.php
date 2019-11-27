<table cellpadding="3px">
	<tr>
		<td>
			<table>
				<tr>
					<td width="50%" style="text-decoration:underline"><h1>Sales Order</h1></td>
					<td width="50%" align="right">No. Order : <?php echo $header_do->no_so ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%"  cellspacing="2px"  cellpadding="1px">
				<tr>
					<td width="20%">Tanggal Order</td>
					<td width="80%"> : <?php echo tglIndonesia($header_do->tgl_so,'-',' ') ?></td>
				</tr>
				<tr>
					<td width="20%">Nama Pelanggan</td>
					<td width="80%"> : <?php echo $pelanggan->nama_pelanggan ?></td>
				</tr>
				<tr>
					<td width="20%">Alamat Pelanggan</td>
					<td width="80%"> : <?php echo $header_do->alamat ?></td>
				</tr>
				<tr>
					<td width="20%">No. Telp. Pelanggan</td>
					<td width="80%"> : <?php echo $header_do->no_telp ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<br />
	<tr>
		<td>
			<table class="garis" width="100%" cellpadding="3px">
				<thead>
					<tr style="text-align:center">
						<th width="30%" style="background-color:gray;line-height:23px">Jenis Barang<br ></th>
						<th width="15%" style="background-color:gray;line-height:23px">Jumlah</th>
						<th width="20%" style="background-color:gray;line-height:23px">Satuan</th>
						<th width="15%"style="background-color:gray;line-height:23px">Harga (Rp)</th>
						<th width="20%" style="background-color:gray;line-height:23px">Total Harga (Rp)</th>
					</tr>
				</thead>
				<tbody>
				<?php 
					$totalSak = 0;
					$totalHarga = 0;
					if(!empty($detail_do)){
						foreach($detail_do as $d){
							echo '<tr>
								<td width="30%">'.$barang[$d->kode_barang]['nama_barang'].'</td>
								<td align="right">'.$d->jumlah.'</td>
								<td>Sak</td>
								<td align="right">'.number_format($d->harga_jual,2,',','.').'</td>
								<td align="right">'.number_format($d->harga_total,2,',','.').'</td>
							</tr>';
							$totalSak += $d->jumlah;
							$totalHarga += $d->harga_total;
						}
					}
				?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4" align="right">Total Qty (Sak)</td>
						<td align="right"><?php echo angkaRibuan($totalSak) ?></td>
					</tr>
					<tr>
						<td colspan="4" align="right">Grand Total (Rp)</td>
						<td align="right"><?php echo number_format($totalHarga,2,',','.') ?></td>
					</tr>
				</tfoot>
			</table>
		</td>
	</tr>
</table>
<style>
	table.garis{
		
	}
	table.garis th,table.garis td{
		border: 1px solid black;
		border-collapse: collapse;		
	}
	
</style>