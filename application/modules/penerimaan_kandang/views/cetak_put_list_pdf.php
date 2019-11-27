<?php
$image_file = base_url() . "assets/images/feedmill_logo.png";
$farm = isset($items[0]['farm']) ? strtoupper($items[0]['farm']) : '';
$tanggal_kirim = isset($items[0]['tgl_kirim']) ? convert_month($items[0]['tgl_kirim'], 1) : '';
$tanggal_kebutuhan = isset($items[0]['tgl_keb_awal']) ? convert_month($items[0]['tgl_keb_awal'], 1) . ' s/d ' . convert_month($items[0]['tgl_keb_akhir'], 1) : '';
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
			<td colspan="4" align="center" style="font-size:8px;"><b>Daftar Penerimaan Kandang</b></td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="">&nbsp;</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">Farm</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $farm . '</td>
			<td align="left" style="width:18%;">Tanggal Kebutuhan</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $tanggal_kebutuhan . '</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">Tanggal Pengiriman</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $tanggal_kirim . '</td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="">&nbsp;</td>
		</tr>
	</table>
	<br>
	<table width="100%" style="font-family:Arial;font-size:6px;" id="detail-barang">
		<tr>
			<th>Kode Kandang</th>
			<th>Jenis Kelamin</th>
			<th>Kode Barang</th>
			<th>Nama Barang</th>
			<th>Jumlah (zak)</th>
			<th>Bentuk Pakan</th>
			<th>Diserahkan Oleh</th>
			<th>Penerima</th>
			<th>Tanggal dan Waktu Penerimaan</th>
		</tr>';
foreach ($items as $key => $value) {
    if ($value['remark'] == 1) {
        echo'<tr>
					<td>' . $value['kode_kandang'] . '</td>
					<td>' . $value['jenis_kelamin'] . '</td>
					<td>' . $value['kode_barang'] . '</td>
					<td>' . $value['nama_barang'] . '</td>
					<td class="number">' . $value['jumlah'] . '</td>
					<td>' . $value['bentuk_pakan'] . '</td>    
					<td>' . $value['user_gudang'] . '</td>
					<td>' . $value['user_buat'] . '</td>
					<td>' . convert_month($value['tgl_buat'],1).' '.date('H:i',strtotime($value['tgl_buat'])) . '</td>
                                                
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