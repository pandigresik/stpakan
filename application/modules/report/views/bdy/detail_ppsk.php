<!DOCTYPE html>
<html>
<head>
	<title>ST Pakan</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=10.0">
	<base href="<?php echo base_url() ?>" />
</head>
<body>
<div class="col-md-10 col-md-offset-1">	
<h3 class="text-center">Detail Pengembalian</h3>	
<table class="table table-bordered custom_table" >
	<thead>
		<tr>
			<th>Tanggal Pengembalian</th>
			<th>Tanggal Kebutuhan</th>
			<th>User Pengembalian</th>
			<th>Kandang</th>
			<th>Sak Dikembalikan</th>
		</tr>
	</thead>
		<tbody>
		<?php
			foreach($detailPengembalian as $d){
				echo '<tr>
					<td>'.convertElemenTglWaktuIndonesia($d->tgl_kembali).'</td>
					<td>'.convertElemenTglIndonesia($d->tgl_terima).'</td>
					<td>'.$d->user_pengembali.'</td>
					<td>'.substr($d->no_reg,-2).'</td>
					<td>'.$d->jml_kembali.'</td>
				</tr>';
			}
		?>
		</tbody>
	</table>
		</div>
	</body>
	<link rel="stylesheet" media="all" type="text/css" href="assets/libs/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/css/home.css" >
</html>		