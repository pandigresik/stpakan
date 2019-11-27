<div class="panel panel-default">
	<div class="panel-heading">Pengambilan Barang</div>
	<div class="panel-body">
		<div>
			<div data-example-id="togglable-tabs" role="tabpanel"
				class="bs-example bs-example-tabs">
				<ul role="tablist" class="nav nav-tabs" id="myTab">
					<li <?php echo ($tab_active == 1) ? 'class="active"' : ""; ?>
						role="presentation"><a aria-expanded="true"
						aria-controls="transaction" data-toggle="tab" role="tab"
						id="transaction-tab" href="#transaction">Transaksi</a></li>
					<li <?php echo ($tab_active == 2) ? 'class="active"' : ""; ?>
						role="presentation" class=""><a aria-controls="print-preview"
						data-toggle="tab" id="print-preview-tab" role="tab"
						href="#print-preview" aria-expanded="false">Print Preview</a></li>
				</ul>
				<div class="tab-content" id="myTabContent">
					<div aria-labelledby="transaction-tab" id="transaction"
						class="tab-pane fade <?php echo ($tab_active == 1) ? 'active in' : ''; ?>"
						role="tabpanel">
						<div class="new-line">
							<button id="btn-konfirmasi" class="btn btn-default" type="submit"
								disabled="true" onclick="konfirmasi()">Konfirmasi</button>
						</div>
						<div id="transaction-table" class="new-line">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th></th>
										<th>Kode Kandang</th>
										<th>ID Kavling</th>
										<th>Kode Barang</th>
										<th>Nama Barang</th>
										<th>Jumlah (zak)</th>
										<th>Berat (kg)</th>
										<th>Bentuk Pakan</th>
										<th>Keterangan</th>
									</tr>
								</thead>
								<tbody>
							<?php $number = 1; ?>
							<?php foreach($items as $key => $value){ ?>
							<tr data-ke="<?php echo $number; ?>"
										data-tanggal-kirim="<?php echo convert_month($value['tgl_kirim'], 1); ?>"
										data-no-order="<?php echo $value['no_order']; ?>"
										data-no-reg="<?php echo $value['no_reg']; ?>"
										data-kode-farm="<?php echo $value['kode_farm']; ?>">
										<td>
									<?php if($value['keterangan']==0){?>
									  	<input class="radio" type="radio" name="radio"
											onclick="kontrol_option(this)">
									<?php } ?>
								</td>
										<td class='kode-kandang'><?php echo $value['kode_kandang']; ?></td>
										<td class='id-kavling'><?php echo $value['id_kavling']; ?></td>
										<td class='kode-barang'><?php echo $value['kode_barang']; ?></td>
										<td><?php echo $value['nama_barang']; ?></td>
										<td class="jumlah"><?php echo $value['jumlah']; ?></td>
										<td><input type="text" name="berat" placeholder="Berat"
											value="<?php echo (empty($value['berat'])) ? 0 : $value['berat'] ; ?>"
											class="text-center form-control <?php echo (empty($value['berat'])) ? 'berat' : ''; ?>"
											<?php echo (empty($value['berat'])) ? '' : 'readonly' ; ?>
											onchange="kontrol_berat(this)" onkeyup="number_only(this)"></td>
										<td><?php echo $value['bentuk_pakan']; ?></td>
										<td class="keterangan"><?php echo ($value['keterangan']==1) ? 'Confirmed' : ''; ?></td>
									</tr>
							<?php $number++; ?>	
							<?php } ?>
						</tbody>
							</table>
						</div>
						<div class="text-right">
					<?php echo $halaman; ?>
					<!--ul class="pagination">
						<li class="disabled"><a aria-label="Previous" href="#"><span
								aria-hidden="true">«</span></a></li>
						<li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
						<li><a href="#">2</a></li>
						<li><a href="#">3</a></li>
						<li><a href="#">4</a></li>
						<li><a href="#">5</a></li>
						<li><a aria-label="Next" href="#"><span aria-hidden="true">»</span></a></li>
					</ul-->
						</div>
					</div>
					<div aria-labelledby="print-preview-tab" id="print-preview"
						class="tab-pane fade <?php echo ($tab_active == 2) ? "active in" : ""; ?>"
						role="tabpanel">
						<div class="new-line">
							<button class="btn btn-default" type="submit">Print</button>
						</div>
						<div class="text-center">
							<h2>Daftar Pengiriman Barang</h2>
						</div>
						<div class="new-line">
							<div class="col-md-6">
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
							<div class="col-md-6">
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
								<table class="table table-bordered">
									<thead>
										<tr>
											<th>Kode Kandang</th>
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
							<?php if($value['keterangan']==1){ ?>
							<tr>
											<td><?php echo $value['kode_kandang']; ?></td>
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
			</div>
		</div>
	</div>
	<link rel="stylesheet" type="text/css"
		href="assets/css/pengambilan_barang/pengambilan.css">
	<script type="text/javascript"
		src="assets/js/pengambilan_barang/pengambilan.js"></script>