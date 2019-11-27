<div class="row">
	<div class="col-md-12">
		<div class="text-center header-content">
			<h2>Daftar Pengambilan Barang</h2>
		</div>
		<div class="new-line">
			<div class="col-md-6 left-content">
				<div class="form-horizontal">
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-4">Farm</label> <label
							for="inputEmail3" class="col-sm-1">:</label> <label
							for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['farm']) ? strtoupper($items[0]['farm']) : ''; ?></label>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-4">Tanggal Pengiriman</label>
						<label for="inputEmail3" class="col-sm-1">:</label> <label
							for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['tgl_kirim']) ? convert_month($items[0]['tgl_kirim'], 1) : ''; ?></label>

					</div>
				</div>
			</div>
			<div class="col-md-6 right-content">
				<div class="form-horizontal">
					<div class="form-group">
						<label for="inputEmail3" class="col-sm-4">Tanggal Kebutuhan</label>
						<label for="inputEmail3" class="col-sm-1">:</label> <label
							for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['tgl_keb_awal']) ? convert_month($items[0]['tgl_keb_awal'], 1) . ' s/d ' . convert_month($items[0]['tgl_keb_akhir'], 1) : ''; ?></label>

					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div id="print-preview-table">
				<table class="table table-bordered table-content">
					<thead>
						<tr>
							<th>Kode Kandang</th>
							<th>ID Kavling</th>
							<th>Kode Barang</th>
							<th>Nama Barang</th>
							<th>Jumlah (zak)</th>
							<th>Berat (kg)</th>
							<th>Bentuk Pakan</th>
							<th>Paraf</th>
						</tr>
					</thead>
					<tbody>
							<?php foreach($items as $key => $value){ ?>
							<?php if($value['keterangan']==0){ ?>
							<tr>
							<td><?php echo $value['kode_kandang']; ?></td>
							<td><?php echo $value['id_kavling']; ?></td>
							<td><?php echo $value['kode_barang']; ?></td>
							<td><?php echo $value['nama_barang']; ?></td>
							<td><?php echo $value['jumlah']; ?></td>
							<td><?php echo $value['berat']; ?></td>
							<td><?php echo $value['bentuk_pakan']; ?></td>
							<td></td>
						</tr>
							<?php } } ?>
						</tbody>
				</table>
			</div>
		</div>
	</div>
</div>