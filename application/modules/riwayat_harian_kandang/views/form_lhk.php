<?php $image_file = base_url()."assets/images/feedmill_logo.png"; ?>

<table width="100%" style="font-family:Arial">
	<tr>
		<td style="width:90%">
			<span style="font-weight:bold;font-size:14px;"><?php echo 'FARM '. $namafarm; ?></span><br>
			<span style="font-weight:bold;font-size:6px;"><?php echo 'LHK '.tglIndonesia($tgllhk,'-',' ').' Kandang '.$namakandang.' Umur '.$umur.' hari'?></span><br>
		</td>
		<td style="width:10%; align:right;">
			<span><?php echo $barcode; ?></span>
		</td>
	</tr>
</table>
<br/>
<table width="100%" style="font-family:Arial;font-size:6px;" id="detail-barang">
	<tr>
		<td colspan="12" align="center" style="background-color:gray; border:1px solid black; font-size:8px;"><b>Laporan Harian Kandang - Penimbangan per Sekat</b></td>
	</tr>
	<tr>
		<th style="width:25%;"><span><br/></span>Sekat</th>
		<th style="width:25%;"><span><br/></span>Jumlah</th>
		<th style="width:25%;"><span><br/></span>BB(g)</th>
		<th style="width:25%;"><span><br/></span>Keterangan</th>
	</tr>
	<?php foreach($default_sekat as $key=>$val): ?>
		<tr>
			<th align="left" style="width:25%"><span><br/></span>Sekat <?php echo $val; ?></th>
			<th align="left" style="width:25%"></th>
			<th align="left" style="width:25%"></th>
			<th align="left" style="width:25%;"></th>
		</tr>
	<?php endforeach; ?>	
</table>
<table width="100%" style="font-family:Arial;font-size:6px;" id="detail-barang">
	<tr>
		<td colspan="12" align="center" style="background-color:gray; border:1px solid black; font-size:8px;"><b>Laporan Harian Kandang - Populasi</b></td>
	</tr>
	<tr>
		<th style="width:50%;"><span><br/></span>Mati</th>
		<th style="width:50%;"><span><br/></span>Afkir</th>
	</tr>
	<tr>
		<th style="width:50%;"></th>
		<th style="width:50%;"></th>
	</tr>
</table>
<table width="100%" style="font-family:Arial;font-size:6px;" id="detail-barang">
	<tr>
		<td colspan="12" align="center" style="background-color:gray; border:1px solid black; font-size:8px;"><b>Laporan Harian Kandang - Pakan</b></td>
	</tr>
	<tr>
		<th style="width:33%;"><span><br/></span>Jenis Kelamin</th>
		<th style="width:33%;"><span><br/></span>Nama Pakan</th>
		<th style="width:34%;"><span><br/></span>Terpakai(Sak)</th>
	</tr>
	<?php foreach($pakan_pakai as $key=>$val): ?>
		<tr>
			<th style="width:33%;"><span><br/></span>Campur</th>
			<th style="width:33%;"><span><br/></span><?php echo $val['nama_barang']; ?></th>
			<th style="width:34%;"></th>
		</tr>
	<?php endforeach; ?>
</table>
<table width="100%" style="display:none;font-family:Arial;font-size:6px;" id="detail-barang">
	<tr>
		<td colspan="12" align="center" style="background-color:gray; border:1px solid black; font-size:8px;"><b>Laporan Harian Kandang - Permintaan Kandang</b></td>
	</tr>
	<tr>
		<th style="width:25%;"><span><br/></span>Nama Pakan</th>
		<th style="width:25%;"><span><br/></span>Tanggal Kebutuhan</th>
		<th style="width:25%;"><span></span>Rekomendasi Kebutuhan(Sak)</th>
		<th style="width:25%;"><span></span>Rekomendasi Permintaan(Sak)</th>
	</tr>
	<?php foreach($pakan_rekomendasi as $key=>$val): ?>
		<tr>
			<th style="width:25%;"><span><br/></span><?php echo $val['nama_barang']; ?></th>
			<th style="width:25%;"><span><br/></span><?php echo tglIndonesia($val['tglkebutuhan'],'-',' '); ?></th>
			<th style="width:25%;"><span><br/></span><?php echo $val['kebutuhan_pakan'] ?></th>
			<th style="width:25%;"></th>
		</tr>
	<?php endforeach; ?>
</table>
<table width="100%" style="font-family:Arial">
	<tr>
		<td style="width:100%; text-align:right;" align="right">
			<span><i><span align="right" style="font-size:6px; text-align:right; align:right;">Dicetak oleh <?php echo $nama_user . ' pada ' . tglIndonesia($today_date,'-',' ') . ' pukul ' . substr($today_time,0,8); ?> </span></i></span>
		</td>
	</tr>
</table>
<br/><br/><br/><br/>
<table width="100%" style="font-family:Arial;font-size:6px;" id="ttd">
	<tr>
		<td style="width:20%;">
			Operasional Kandang<br>
			<br><br><br><br>
			( _________________ )
		</td>
		<td style="width:60%;">
			Pengawas Kandang<br>
			<br><br><br><br>
			( _________________ )
		</td>
		<td style="width:20%;">
			Kepala Farm<br>
			<br><br><br><br>
			( _________________ )
		</td>
	</tr>
</table>	

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
		height: 20px;
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