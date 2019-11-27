<?php
$image_file = base_url() . "assets/images/feedmill_logo.png";
$tanggal = isset($items[0]['tgl_buat']) ? convert_month($items[0]['tgl_buat'], 1) : '';
$kode_farm = isset($items[0]['kode_farm']) ? strtoupper($items[0]['kode_farm']) : '';
$no_berita_acara = isset($items[0]['no_ba']) ? strtoupper($items[0]['no_ba']) : '';
$nama_farm = isset($items[0]['nama_farm']) ? strtoupper($items[0]['nama_farm']) : '';
$no_sj = isset($items[0]['no_sj']) ? strtoupper($items[0]['no_sj']) : '';
$nama_sopir = isset($items[0]['nama_sopir']) ? strtoupper($items[0]['nama_sopir']) : '';
$no_penerimaan = isset($items[0]['no_penerimaan']) ? strtoupper($items[0]['no_penerimaan']) : '';
$no_kendaraan = isset($items[0]['no_kendaraan_terima']) ? strtoupper($items[0]['no_kendaraan_terima']) : '';
$no_op = isset($items[0]['no_op']) ? strtoupper($items[0]['no_op']) : '';
$no_spm = isset($items[0]['no_spm']) ? strtoupper($items[0]['no_spm']) : '';
$keterangan = isset($items[0]['keterangan']) ? strtoupper($items[0]['keterangan']) : '';
$judul_jumlah = ($tipe_ba == 'R') ? 'Rusak' : 'Kurang';
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
			<td colspan="4" align="center" style="font-size:8px;"><b>Berita Acara</b></td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="">&nbsp;</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">Tanggal</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $tanggal . '</td>
			<td align="left" style="width:18%;">Kode Farm</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $kode_farm . '</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">No. Berita Acara</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_berita_acara . '</td>
			<td align="left" style="width:18%;">Nama Farm</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $nama_farm . '</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">No. SJ</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_sj . '</td>
			<td align="left" style="width:18%;">Nama Sopir</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $nama_sopir . '</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">No. Penerimaan</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_penerimaan . '</td>
			<td align="left" style="width:18%;">No. Kendaraan</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_kendaraan . '</td>
		</tr>
		<tr>
			<td align="left" style="width:18%;">No. OP</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_op . '</td>
			<td align="left" style="width:18%;">No. SPM</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_spm . '</td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="">&nbsp;</td>
		</tr>
	</table>
	<br>
	<span colspan="4" align="left" style="font-size:8px;text-decoration:underline;height:10px;"><b>List barang</b></span>
	<br>
	<table width="100%" style="font-family:Arial;font-size:6px;" id="detail-barang">
		<tr>
			<th>Kode Barang</th>
			<th>Nama Barang</th>
			<th>Bentuk Pakan</th>
			<th>Jumlah ' . $judul_jumlah . '</th>
		</tr>';
foreach ($items as $key => $value) {

    $jumlah = ($tipe_ba == 'R') ? $value['jml_rusak'] : $value['jml_kurang'];
    echo'<tr>
				<td>' . $value['kode_barang'] . '</td>
				<td>' . $value['nama_barang'] . '</td>
				<td>' . $value['bentuk_barang'] . '</td>
				<td class="number">' . $jumlah . '</td>
				</tr>';
}
echo '</table>	
	<br><br>
	<span align="left" style="font-size:8px;text-decoration:underline;height:10px;"><b>Keterangan</b></span>
	<br>	
	<span align="left" style="font-size:6px;">' . $keterangan . '</span>
	<br><br><br><br>	
	<table width="100%" style="font-family:Arial;font-size:6px;" id="ttd">
		<tr>
			<td style="width:20%;">
				Mengetahui<br>
				Kepala Farm,<br>
				<br><br><br><br>
				( _________________ )
			</td>
			<td style="width:60%;">
				&nbsp;<br>
				Penerima Pengawas,<br>
				<br><br><br><br>
				( _________________ )
			</td>
			<td style="width:20%;">
				&nbsp;<br>
				Sopir,<br>
				<br><br><br><br>
				( _________________ )
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