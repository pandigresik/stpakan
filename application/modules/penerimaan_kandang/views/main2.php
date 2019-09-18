<div class="panel panel-default">
	<div class="panel-heading">Penerimaan Kandang</div>
	<div class="panel-body">
		<div class="form-inline new-line">
			<label for="tanggal-kirim">Tanggal Kirim</label>
			<div class="form-group">
				<div class="input-group">
					<input type="text" class="form-control" id="tanggal-kirim"
						name="tanggal-kirim" placeholder="Tanggal Kirim" readonly>
					<div class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</div>
				</div>
			</div>
			<button class="btn btn-default" id="btn-cari"
				onclick="get_data_penerimaan()">Cari</button>
		</div>
		<div id="picking-list-table" class="new-line">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Tgl Kirim</th>
						<th>Tgl Kebutuhan</th>
						<th>Jumlah Kebutuhan</th>
						<th>Jumlah Belum Proses</th>
						<!--th></th-->
					</tr>
				</thead>
				<tbody>
					<?php foreach($list as $key => $value){ ?>
					<tr>
						<td><a href='#penerimaan_kandang/transaksi' class='link'
							onclick='get_data_detail_penerimaan(this,1)'><?php echo $value['tgl_kirim']; ?></a></td>
						<td><?php echo $value['tgl_kebutuhan']; ?></td>
						<td><?php echo $value['jumlah_kebutuhan']; ?></td>
						<td><?php echo $value['jumlah_belum_proses']; ?></td>
						<!--td><a href='#penerimaan_kandang/transaksi' class='link' onclick='get_data_detail_penerimaan(this,2)'><?php echo $value['cetak']; ?></a></td-->
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<link rel="stylesheet" type="text/css"
	href="assets/css/penerimaan_kandang/penerimaan.css">
<script type="text/javascript"
	src="assets/js/penerimaan_kandang/penerimaan.js"></script>