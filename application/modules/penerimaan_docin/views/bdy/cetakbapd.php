<?php
	$hari = array('Minggu','Senin','Selasa','Rabu','Kamis','Jum\'at','Sabtu');
?>
<html>
<body>
<div class="container col-md-offset-1 col-md-10">
	<div class="text-center"><h3>BERITA ACARA PENERIMAAN DOC</h3></div>
	<div class="row">
		<div class="col-md-2">No. BAP</div>
		<div class="col-md-4">:&nbsp;&nbsp;<?php echo 'BAP-'.$noreg ?></div>
	</div>
	<div class="row">
		<div class="col-md-2">Nama Farm</div>
		<div class="col-md-4">:&nbsp;&nbsp;<?php echo $nama_farm ?></div>
	</div>
	<div class="row">
		<div class="col-md-2">Tanggal DOC-In</div>
		<div class="col-md-4">:&nbsp;&nbsp;<?php echo $tgl_docin ?></div>
	</div>
	<br />
	<br />
	<div>
		<p>Dengan hormat, </p>
		<p>Pada hari ini, hari <?php echo $hari[$index_hari] ?>,<?php echo $hari_ini ?> telah diterima DOC pedaging dengan rincian sebagai berikut :</p>
	</div>
	<div>
		<table class="table table-bordered  custom_table">
			<thead>
				<tr>
					<th>Hatchery</th>
					<th>No. SJ</th>
					<th>Tanggal Penerimaan</th>
					<th>Jumlah Box</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($sj as $j){
					echo '<tr>
								<td>'.$hatchery.'</td>
								<td>'.$j['no_sj'].'</td>
								<td>'.convertElemenTglWaktuIndonesia($j['tgl_terima']).'</td>
								<td>'.$j['jmlbox'].'</td>
					</tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<br />
	<div>
		<p>Berdasarkan perhitungan ulang di kandang - kandang, jumlah dan performance DOC yang diterima sebagai berikut : </p>
	</div>
	<div>
		<table class="table table-bordered  custom_table" data-table="bapdoc">
			<thead>
				<tr>
					<th colspan="4">Jumlah DOC</th>
					<th rowspan="2">BB <br /> Rata - rata</th>
					<th rowspan="2">Uniformity (%)</th>
				</tr>
				<tr>
					<th>Box</th>
					<th>Ekor</th>
					<th>Afkir</th>
					<th>Stok Awal</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php

						$jml_afkir = formatAngka($performance['jmlafkir'],0);
						$stokawal =  $performance['stokawal'];
						$bbrata2 =  formatAngka($performance['bbrata2'],2) ;
						$uniformity = formatAngka($performance['uniformity'],2);
					?>
					<td class="number jmlbox"><?php echo formatAngka($performance['jmlbox'],0) ?></td>
					<td class="number jmlekor"><?php echo formatAngka($performance['jmlekor'],0) ?></td>
					<td class="number jmlafkir"><?php echo $jml_afkir ?></td>
					<td class="number stokawal"><?php echo formatAngka($stokawal,0) ?></td>
					<td class="number rata-rata"><?php echo $bbrata2 ?></td>
					<td class="number uniformity"><?php echo $uniformity ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<br />
	<div>
		<p>Demikian berita acara ini dibuat sebenar-benarnya dan sesuai dengan kenyataan yang ada.</p>
	</div>

	<div class="row">
		<div class="col-md-2 text-center">Penghitung</div>
		<div class="col-md-2 col-md-offset-3 text-center">Saksi</div>
		<div class="col-md-2 col-md-offset-3 text-center">Mengetahui</div>
	</div>
	<br />
	<br />
	<br />
	<br />
	<div class="row">
		<div class="col-md-2 text-center">Admin Farm</div>
		<div class="col-md-2 col-md-offset-3 text-center">Pengawas Kandang</div>
		<div class="col-md-2 col-md-offset-3 text-center">Kepala Farm</div>
	</div>
</div>
</body>
<link rel="stylesheet" media="all" type="text/css" href="<?php echo site_url('assets/libs/bootstrap/css/bootstrap.min.css') ?>">
<link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/css/home.css') ?>" >
</html>
