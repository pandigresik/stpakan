<?php

ini_set('memory_limit', '2500000M');
set_time_limit(10000000000);

$image_file = base_url() . "assets/images/feedmill_logo.png";
echo '
	<table width="100%" style="font-family:Arial">
		<tr>
			<td style="width:8%"><img src="' . $image_file . '" alt="test alt attribute" border="0" /></td>
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
			<td colspan="3" align="center" style="font-size:8px;"><b>FARM '.$nama_farm.'</b></td>
		</tr>
		<tr>
			<td colspan="3" align="center" style="font-size:6px;"><b>'.$nama_kandang.'</b></td>
		</tr>
		<tr>
			<td colspan="3" align="center" style="">&nbsp;</td>
		</tr>
		<tr>
			<td align="center" style="width:33%;">Tipe Kandang : '.$tipe_kandang.'</td>
			<td align="center" style="width:33%;">Kapasitas :' . $kapasitas . ' ekor</td>
			<td align="center" style="width:33%;">Jantan : '.$jantan.' ekor, Betina : '.$betina.' ekor</td>
		</tr>
		<tr>
			<td colspan="3" align="center" style="">&nbsp;</td>
		</tr>
	</table>
	<br>
	<table width="100%" style="font-family:Arial;font-size:6px;" id="detail-barang">
		<tr>
			<th>Minggu+Hari</th>
			<th>Tanggal</th>
			<th>Jenis Kelamin</th>
			<th>Kode Pakan</th>
			<th>Nama Pakan</th>
			<th>Bentuk</th>
			<th>Keb. Pakan/Ekor (gr)</th>
			<th>Kuantitas (Sak)</th>
		</tr>'.
		$data_html;
echo '</table>		
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
    .number{
        text-align: right;
    }
</style>