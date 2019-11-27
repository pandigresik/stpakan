<?php
$image_file = base_url() . "assets/images/feedmill_logo.png";
$no_berita_acara = isset($list['no_ba']) ? strtoupper($list['no_ba']) : '';
$tanggal = isset($list['tgl_kedatangan']) ? convert_month($list['tgl_kedatangan'], 1) : '';
$nama_farm = isset($list['nama_farm']) ? strtoupper($list['nama_farm']) : '';
$no_sj = isset($list['no_sj']) ? strtoupper($list['no_sj']) : '';
$no_spm = isset($list['no_spm']) ? strtoupper($list['no_spm']) : '';
$no_op = isset($list['no_op']) ? strtoupper($list['no_op']) : '';
$no_penerimaan = isset($list['no_penerimaan']) ? strtoupper($list['no_penerimaan']) : '';
$ekspedisi = isset($list['ekspedisi']) ? strtoupper($list['ekspedisi']) : '';
$no_kendaraan = isset($list['no_kendaraan_terima']) ? strtoupper($list['no_kendaraan_terima']) : '';
$nama_sopir = isset($list['nama_sopir']) ? strtoupper($list['nama_sopir']) : '';

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
			<td colspan="4" align="center" style="font-size:8px;"><b>BERITA ACARA</b></td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="">&nbsp;</td>
		</tr>
		<tr>
			<td align="right" style="width:18%;">No. Berita Acara</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_berita_acara . '</td>
			<td align="right" style="width:18%;">No. OP</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_op . '</td>
		</tr>
		<tr>
			<td align="right" style="width:18%;">Tanggal Kedatangan</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $tanggal . '</td>
			<td align="right" style="width:18%;">No. Penerimaan</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_penerimaan . '</td>
		</tr>
		<tr>
			<td align="right" style="width:18%;">Farm</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $nama_farm . '</td>
			<td align="right" style="width:18%;">Ekspedisi</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $ekspedisi . '</td>
		</tr>
		<tr>
			<td align="right" style="width:18%;">No. SJ</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_sj . '</td>
			<td align="right" style="width:18%;">No. Kendaraan</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_kendaraan . '</td>
		</tr>
		<tr>
			<td align="right" style="width:18%;">No. SPM</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $no_spm . '</td>
			<td align="right" style="width:18%;">Nama Sopir</td>
			<td align="left" style="width:35%;">&nbsp;:&nbsp;' . $nama_sopir . '</td>
		</tr>
		<tr>
			<td colspan="4" align="center" style="">&nbsp;</td>
		</tr>
	</table>';
$h_r = 0;
foreach ($list['detail_barang'] as $key => $value) {
	if($value['jml_rusak']>0){
		$h_r++;
	}
}
	if($h_r>0){
echo '
	<br>
	<span colspan="4" align="left" style="font-size:8px;text-decoration:underline;height:10px;"><b>Barang Rusak</b></span>
	<br>
	<table width="100%" style="font-family:Arial;font-size:6px;" id="detail-barang">
		<tr>
			<th>Kode Barang</th>
			<th>Nama Barang</th>
			<th>Bentuk</th>
			<th>Jumlah SJ (sak)</th>
			<th>Jumlah Aktual (sak)</th>
		</tr>';
foreach ($list['detail_barang'] as $key => $value) {
	if($value['jml_rusak']>0){
    echo'<tr>
				<td>' . $value['kode_barang'] . '</td>
				<td>' . $value['nama_barang'] . '</td>
				<td>' . $value['bentuk_barang'] . '</td>
				<td align="right">' . $value['jml_sj'] . '</td>
				<td align="right">' . $value['jml_rusak'] . '</td>
				</tr>';
	echo '
		<tr>
			<td></td>
			<td colspan="4">
			<br><br>&nbsp;&nbsp;
			<table width="80%" style="font-family:Arial;font-size:6px;padding=5px;" id="detail-barang">
				<tr>
					<th>No</th>
					<th>Berat (kg)</th>
					<th>Keterangan</th>
				</tr>

	';
			$no = 1;
			foreach ($value['detail_timbang'] as $k => $v) {
			    echo'<tr>
							<td>' . $no . '.</td>
							<td>' . $v['berat_putaway'] . '</td>
							<td align="left">' . $v['keterangan_rusak'] . '</td>
							</tr>';
							$no++;
			}

	echo '	
			</table>
			<br>
			</td>
		</tr>

	';
	}
}
echo '</table>	';
}
$h_k = 0;
foreach ($list['detail_barang'] as $key => $value) {
	if($value['jml_kurang']>0){
		$h_k++;
	}
}
	if($h_k>0){
echo '<br>
	<span colspan="4" align="left" style="font-size:8px;text-decoration:underline;height:10px;"><b>Barang Kurang</b></span>
	<br>
	<table width="100%" style="font-family:Arial;font-size:6px;" id="detail-barang">
		<tr>
			<th>Kode Barang</th>
			<th>Nama Barang</th>
			<th>Bentuk</th>
			<th>Jumlah SJ (sak)</th>
			<th>Jumlah Aktual (sak)</th>
			<th>Keterangan</th>
		</tr>';
foreach ($list['detail_barang'] as $key => $value) {
	if($value['jml_kurang']>0){
    echo'<tr>
				<td>' . $value['kode_barang'] . '</td>
				<td>' . $value['nama_barang'] . '</td>
				<td>' . $value['bentuk_barang'] . '</td>
				<td align="right">' . $value['jml_sj'] . '</td>
				<td align="right">' . $value['jml_kurang'] . '</td>
				<td align="left">' . $value['keterangan_kurang'] . '</td>
				</tr>';
	}
}
echo '</table>';
}
echo '<br><br><br><br>	
	<table width="100%" style="font-family:Arial;font-size:6px;" id="ttd">
		<tr>
			<td style="width:20%;">
				Mengetahui<br>
				Kepala Farm,<br>
				<br><br><br><br><br>
				( _________________ )
			</td>
			<td style="width:60%;">
				&nbsp;<br>
				Penerima Pengawas,<br>
				<br><br><br><br><br>
				( _________________ )
			</td>
			<td style="width:20%;">
				&nbsp;<br>
				Sopir,<br>
				<br><br><br><br><br>
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