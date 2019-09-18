<?php 
$image_file = base_url()."assets/images/feedmill_logo.png";
echo '
	<table width="100%" style="font-family:Arial">
		<tr>
			<td style="width:8%"><img src="'.$image_file.'" alt="test alt attribute" border="0" /></td>
			<td style="width:92%">
				<span style="font-weight:bold;font-size:8px;">PT. WONOKOYO JAYA CORPORINDO</span><br>
				<span style="font-weight:bold;font-size:6px;">DIVISI FEEDMILL</span><br>
				<span style="font-weight:bold;font-size:6px;">UNIT GEMPOL</span>
			</td>
		</tr>
	</table>	
	<br><br>
	<table width="100%" style="font-family:Arial;font-size:6px;" id="header-barang">
		<tr>
			<td colspan="4" align="center" style="font-size:8px;"><b>Retur Pakan Ke Gudang</b></td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="font-size:8px;"><b>Farm '.$namafarm.'</b></td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="">&nbsp;</td>
		</tr>
		<tr>
			<td align="left" style="width:18%; height:15px">No. SJ Retur</td>
			<td align="left" style="width:45%;">&nbsp;:&nbsp;'.$noretur.'</td>
			<td align="left" style="width:43%;" colspan="2">'.$barcode.'</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">Kandang</td>
			<td align="left" style="width:45%;">&nbsp;:&nbsp;'.$namakandang.'</td>
			<td align="left" style="width:18%;">Tanggal Tutup Siklus</td>
			<td align="left" style="width:25%;">&nbsp;:&nbsp;'.$tgltutupsiklus.'</td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="">&nbsp;</td>
		</tr>
	</table>
	<br>
	<table width="100%" style="font-family:Arial;font-size:6px;" id="detail-barang">
		<tr>
			<th>Kode Barang</th>
			<th>Nama Barang</th>
			<th>Jumlah<br>(zak)</th>
			<th>Berat<br>(kg)</th>
			<th>Bentuk<br>Pakan</th>
		</tr>';
			foreach($items as $item){
				echo '
				<tr>
					<td>' . $item["kodebarang"] . '</td>
					<td>' . $item["namabarang"] . '</td>
					<td>' . $item["jumlah"] . '</td>
					<td>' . $item["berat"] . '</td>
					<td>' . $item["bentuk"] . '</td>
				</tr>
				';
			}
	echo '</table>	
	<br><br>
	<span align="left" style="font-size:6px;">Tabel di atas adalah sisa pakan dari Kandang pada Akhir Siklus yang dikembalikan ke Gudang. </span>
	<br><br><br><br>	
	<table width="100%" style="font-family:Arial;font-size:6px;" id="ttd">
		<tr>
			<td style="width:30%;">
				Mengetahui<br>
				Kepala Unit/Farm,<br>
				<br><br><br><br>
				( '.$namaapprove.' )
			</td>
			<td style="width:40%;">
				&nbsp;<br>
				Admin Gudang,<br>
				<br><br><br><br>
				( '.$namaterima.' )
			</td>
			<td style="width:30%;">
				&nbsp;<br>
				Pengawas Kandang,<br>
				<br><br><br><br>
				( '.$namaretur.' )
			</td>
		</tr>
';
?>

<style>
	table#header-barang td{
		height: 10px;
	}
	table#detail-barang{
		border: solid 2px black;
		border-collapse: collapse;
	}
	table#detail-barang tr td{
		height: 10px;
		border: solid 2px black;
		border-collapse: collapse;
		vertical-align: middle;
		text-align: center;
	}
	table#detail-barang tr th{
		height: 15px;
		border: solid 2px black;
		border-collapse: collapse;
		font-weight: bold;
		vertical-align: middle;
		text-align: center;
	}
	table#ttd tr td{
		height: 15px;
		vertical-align: middle;
		text-align: center;
	}
	.number{
		text-align: right;
	}
	span.paraf{
		margin: 20px;
	}
</style>