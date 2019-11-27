<div class="panel panel-default">
	<div class="panel-heading">Master - Harga Barang</div>
	<div class="panel-body">
		<div class="row>">
			<button type="button" name="tombolTambah" id="btnTambah"
				class="btn btn-primary">Baru</button>
			<br />
			<br />
		</div>
		<table id="master-harga-barang"
			class="table table-bordered table-striped">
			<thead>
				<tr>
					<th class="col-md-2"><input type="text"
						class="form-control q_search" name="q_pelanggan" id="q_pelanggan"
						placeholder="Pelanggan"></th>
					<th class="col-md-2"><input type="text"
						class="form-control q_search" name="q_kode_barang"
						id="q_kode_barang" placeholder="Kode Barang"></th>
					<th class="col-md-2"><input type="text"
						class="form-control q_search" name="q_nama_barang"
						id="q_nama_barang" placeholder="Nama Barang"></th>
					<th class="col-md-1">
						<div class="input-group">
							<select class="form-control" name="q_satuan" id="q_satuan">
								<option value="">Semua</option>
								<?php foreach($list_satuan as $key => $value){ ?>
								<option value="<?php echo $value['deskripsi']; ?>"><?php echo $value['deskripsi']; ?></option>
								<?php } ?>
							</select>
						</div>
					</th>
					<th class="col-md-1">
						<div class="input-group">
							<select class="form-control" name="q_bentuk_pakan"
								id="q_bentuk_pakan">
								<option value="">Semua</option>
								<?php foreach($list_bentuk as $key => $value){ ?>
								<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
					</th>
					<th class="col-md-2">
						
		                <div class="input-group">
		                   <input type="text"
						class="form-control q_search" name="q_tanggal_berlaku"
						id="q_tanggal_berlaku" placeholder="Tanggal Berlaku">
		                    <div class="input-group-addon">
		                        <span class="glyphicon glyphicon-calendar"></span>
		                    </div>
		                </div>
					</th>
					<th class="col-md-2">
						<button type="button" onclick="goSearch()" name="tombolCari" id="btnCari"
                    class="btn btn-primary">Cari</button>
					</th>

				</tr>
				<tr>
					<th class="col-md-2">Pelanggan</th>
					<th class="col-md-2">Kode Barang</th>
					<th class="col-md-2">Nama Barang</th>
					<th class="col-md-2">Satuan</th>
					<th class="col-md-2">Bentuk Pakan</th>
					<th class="col-md-2">Tanggal Berlaku</th>
					<th class="col-md-2">Harga</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<div class="row clear-fix">
			<div class="col-md-3 pull-right">
				<button id="previous" class="btn btn-sm btn-primary" disabled>Previous</button>
				<lable>Page <lable id="page_number"></lable> of <lable
					id="total_page"></lable></lable>
				<button id="next" class="btn btn-sm btn-primary">Next</button>
			</div>
		</div>
	</div>
</div>

<?php
$style_label = "col-sm-4";
$style_value = "col-sm-8";
?>

<div class="modal fade" id="modal-harga-barang" tabindex="-1"
	role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Master - Harga Barang</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal">
					<div class="form-group">
						<label for="inp_pelanggan"
							class="<?php echo $style_label;?> control-label">Pelanggan</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" data-kode-pelanggan="" class="form-control field_input"
								name="pelanggan" id="inp_pelanggan" placeholder="Pelanggan"><div onclick="list_pelanggan(this)" class="input-group-addon"><b>...</b></div>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_kode_barang"
							class="<?php echo $style_label;?> control-label">Kode Barang</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control field_input"
								name="kode_barang" id="inp_kode_barang" onchange="cari_barang(this)"
								placeholder="Kode Barang"><div onclick="list_barang(this)" class="input-group-addon"><b>...</b></div>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_nama_barang"
							class="<?php echo $style_label;?> control-label">Nama Barang</label>
						<div class="<?php echo $style_value;?> input-group">
							<label for="inp_nama_barang" style="text-align: left"
								class="<?php echo $style_value;?> control-label" id="inp_nama_barang">...</label>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_bentuk_pakan"
							class="<?php echo $style_label;?> control-label">Bentuk Pakan</label>
						<div class="<?php echo $style_value;?> input-group">
							<label for="inp_bentuk_pakan" style="text-align: left"
								class="<?php echo $style_value;?> control-label" id="inp_bentuk_pakan" data-kode-bentuk-barang="">...</label>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_satuan"
							class="<?php echo $style_label;?> control-label">Satuan</label>
						<div class="<?php echo $style_value;?> input-group">
							<select class="form-control" name="satuan" id="inp_satuan">
							<?php foreach($list_satuan as $key => $value){ ?>
							<option value="<?php echo $value['uom']; ?>"><?php echo $value['deskripsi']; ?></option>
							<?php } ?>
						</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_tanggal_efektif"
							class="<?php echo $style_label;?> control-label">Tanggal Efektif</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control field_input"
								name="tanggal_efektif" id="inp_tanggal_berlaku" data-tanggal-berlaku="" onchange="kontrol_efektif(this)"
								placeholder="Tanggal Efektif"><div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_harga"
							class="<?php echo $style_label;?> control-label">Harga</label>
						<div class="<?php echo $style_value;?> input-group">
							<div onclick="list_barang(this)" class="input-group-addon"><b>Rp</b></div><input type="text" class="form-control field_input" name="harga"
								id="inp_harga" placeholder="Harga" data-affixes-stay="true" data-thousands="." data-decimal=",">
						</div>
					</div>
				</form>
			</div>

			<div class="modal-footer" style="margin: 0px; padding: 3px;">
				<div class="pull-right">
					<button type="button" name="tombolSimpan" id="btnSimpan"
						class="btn btn-primary">Simpan</button>
					<button type="button" name="tombolUbah" id="btnUbah"
						class="btn btn-primary">Ubah</button>
					<button type="button" name="tombolBatal" id="btnBatal"
						class="btn btn-primary">Batal</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-master-pelanggan" tabindex="-1"
	role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Master - Pelanggan</h4>
			</div>
			<div class="modal-body"></div>

			<div class="modal-footer" style="margin: 0px; padding: 3px;">
				<div class="pull-right">
					<button type="button" name="tombolKembali"
						class="btn btn-primary btnKembali">Kembali</button>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-master-barang" tabindex="-1"
	role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Master - Barang</h4>
			</div>
			<div class="modal-body"></div>

			<div class="modal-footer" style="margin: 0px; padding: 3px;">
				<div class="pull-right">
					<button type="button" name="tombolKembali"
						class="btn btn-primary btnKembali">Kembali</button>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
div#modal-master-pelanggan .modal-body ,div#modal-master-barang .modal-body {
	max-height: 420px;
	overflow-y: auto;
}

#master-barang tbody tr:hover, #master-pelanggan tbody tr:hover {
	background-color: #A1E7FC;
	cursor: pointer;
}
</style>
<script type="text/javascript" src="assets/js/jquery.maskMoney.js"></script>
<script type="text/javascript" src="assets/js/master/harga_barang.js"></script>