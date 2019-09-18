<table cellpadding="3px">
	<tr>
		<td style="font-size:150%;" align="center">DELIVERY ORDER</td>
	</tr>
	<tr>
		<td>
			<table>				
				<tr>
					<td width="85%"></td>
					<td style="height:68px" width="15%" align="right"><?php echo $barcode ?></td>
				</tr>
				<tr>
					<td width="26%" >No. DO</td>
					<td width="30%" ><?php echo $header_do->no_do ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
		<div style="border:1px solid black;position:auto">
			<table class="table1" width="100%" cellpadding="1" cellspacing="4">
				<tbody>
					<tr>
						<td style="width:25%">Pelanggan</td>
						<td style="width:80%">: <?php echo $suratJalan->nama_pelanggan ?></td>
					</tr>
					<tr>
						<td>Alamat</td>
						<td>: <?php echo $suratJalan->alamat_pelanggan ?></td>
					</tr>
					<tr>
						<td>Tanggal Pengiriman</td>
						<td>: <?php echo tglIndonesia($header_do->tgl_so,'-',' ') ?></td>
					</tr>
					<tr>
						<td>Nopol</td>
						<td>: <?php echo $suratJalan->no_kendaraan ?></td>
					</tr>
					<tr>
						<td>Sopir</td>
						<td>: <?php echo $suratJalan->nama_sopir ?></td>
					</tr>
					<tr>
						<td>Farm Penerima</td>
						<td>: <?php echo $dataFarm->NAMA_FARM ?></td>
					</tr>
					<tr>
						<td></td>
						<td>&nbsp; <?php echo $dataFarm->ALAMAT_FARM ?></td>
					</tr>
					<tr>
						<td></td>
						<td>&nbsp; <?php echo $dataFarm->KOTA ?></td>
					</tr>
				</tbody>
			</table>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			Detail Barang
		</td>
	</tr>
	<tr>
		<td>
			<table class="garis" width="100%"  style="border:1px solid black;" cellpadding="3px">
				<thead>
					<tr>
						<th width="50%" align="left" style="background-color:gray;border-right:1px solid black"><br />Nama Barang</th>
						<th width="50%" align="right" style="background-color:gray;border-right:1px solid black">Kuantitas<br />(Sak)</th>
					</tr>
				</thead>
				<tbody>
				<?php

					if(!empty($detail_do)){
						foreach($detail_do as $d){
							echo '<tr>
								<td width="50%" align="left" style="border:1px solid black">'.$barang[$d->kode_barang]['nama_barang'].'</td>
								<td  align="right" style="border:1px solid black">'.$d->jumlah.' sak</td>
							</tr>';

						}
					}
				?>
				</tbody>
			</table>
			<br />
			Catatan : DO ini berlaku s/d tanggal : <u><?php echo tglIndonesia($berlakuDo,'-',' ') ?></u>
		</td>
	</tr>
	<br />

	<tr>
		<td>
			<table width="100%">
				<tr>
					<td width="33%" align="center">Divalidasi Oleh,</td>
					<td width="33%" align="center">Diberikan Oleh,</td>
					<td width="33%" align="center">Diterima Oleh,</td>
				</tr>
				<tr>
					<td width="33%" align="center">Pelanggan</td>
					<td width="33%" align="center">Sopir</td>
					<td width="33%" align="center">Admin Farm</td>
				</tr>
				<br /><br />
				<br /><br />
				<tr>
					<td align="center" width="33%">(...............................)</td>
					<td align="center" width="33%">(...............................)</td>
					<td align="center" width="33%">(...............................)</td>
				</tr>

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
