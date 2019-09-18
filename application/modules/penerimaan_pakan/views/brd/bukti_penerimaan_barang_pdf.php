<?php
$image_file = base_url() . "assets/images/feedmill_logo.png";
$asal_terima_dari = isset($list[0]['asal_terima_dari']) ? strtoupper($list[0]['asal_terima_dari']) : '';
$kota = isset($list[0]['kota']) ? strtoupper($list[0]['kota']) : '';
$no_bpb = isset($list[0]['no_bpb']) ? strtoupper($list[0]['no_bpb']) : '';
$no_op = isset($list[0]['no_op']) ? strtoupper($list[0]['no_op']) : '';
$no_sj = isset($list[0]['no_sj']) ? strtoupper($list[0]['no_sj']) : '';
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
			<td colspan="4" align="center" style="font-size:8px;"><b>Bukti Penerimaan Barang</b></td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="">&nbsp;</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">Asal/Terima dari</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $asal_terima_dari . '</td>
			<td align="left" style="width:18%;">No. BPB</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_bpb . '</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">Kota</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $kota . '</td>
			<td align="left" style="width:18%;">No. OP</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_op . '</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">&nbsp;</td>
			<td align="left" style="width:35%;">&nbsp;</td>
			<td align="left" style="width:18%;">No. SJ</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_sj . '</td>
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
			<th>Bentuk</th>
			<th>Jumlah</th>
			<th>Satuan</th>
			<th>Keterangan</th>
		</tr>';
foreach ($list as $key => $value) {
    if ($value['keterangan'] == 0) {
        echo'<tr>
					<td>' . $value['kode_barang'] . '</td>
					<td>' . $value['nama_barang'] . '</td>
					<td>' . $value['bentuk_barang'] . '</td>
					<td class="number">' . $value['terima_baik_zak'] . '</td>
					<td>' . $value['satuan'] . '</td>
					<td>' . $value['keterangan'] . '</td>
					</tr>';
    }
}
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