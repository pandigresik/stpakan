<div class="panel panel-default">
  <div class="panel-heading">Master Pelanggan</div>
  <div class="panel-body">
	<div class="row>">
		<button type="button" name="tombolTambah" id="btnTambah" class="btn btn-primary">Baru</button>
		<br/><br/>
	</div>
	<table id="master-pelanggan" class="table table-bordered table-striped">
	<thead>
		<tr>
            <th  style="width:1%"></th>
            <th class="col-md-1"><input type="text" class="form-control q_search" name="q_kodepelanggan" id="q_kodepelanggan" placeholder="Kode Pelanggan"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_namapelanggan" id="q_namapelanggan" placeholder="Nama Pelanggan"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_alamat" id="q_alamat" placeholder="Alamat"></th>
            <th class="col-md-1"><input type="text" class="form-control q_search" name="q_kota" id="q_kota" placeholder="Kota"></th>
        </tr>
		<tr>
            <th>No</th>
            <th class="col-md-1">Kode Pelanggan</th>
            <th class="col-md-2">Nama Pelanggan</th>
            <th class="col-md-2">Alamat</th>
            <th class="col-md-1">Kota</th>
        </tr>
    </thead>
	<tbody>
	</tbody>
	</table>
	<div class="row clear-fix">
        <div class="col-md-3 pull-right">
            <button  id="previous" class="btn btn-sm btn-primary" disabled>Previous</button>
            <lable>Page <lable id="page_number"></lable> of <lable id="total_page"></lable></lable>
            <button  id="next" class="btn btn-sm btn-primary">Next</button>
        </div>
    </div>
  </div>
</div>

<?php
$style_label = "col-sm-4";
$style_value = "col-sm-8";
?>

<div class="modal fade" id="modal_pelanggan" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Master Pelanggan</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">
				<div class="form-group">
					<label for="inp_kodepelanggan" class="<?php echo $style_label;?> control-label">Kode Pelanggan</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="kodepelanggan" id="inp_kodepelanggan" placeholder="Kode Pelanggan" maxlength="10" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_namapelanggan" class="<?php echo $style_label;?> control-label">Nama Pelanggan</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="namapelanggan" id="inp_namapelanggan" placeholder="Nama Pelanggan" maxlength="40" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_alamat" class="<?php echo $style_label;?> control-label">Alamat</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="alamat" id="inp_alamat" placeholder="Alamat" maxlength="100" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_kota" class="<?php echo $style_label;?> control-label">Kota</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm col-md-6 field_input" name="kota" id="inp_kota" placeholder="Kota" maxlength="30" required>
					</div>
				</div>
			</form>
		</div>
		
		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolSimpan" id="btnSimpan" class="btn btn-primary">Simpan</button>
				<button type="button" name="tombolUbah" id="btnUbah" class="btn btn-primary">Ubah</button>
				<button type="button" name="tombolBatal" id="btnBatal" class="btn btn-default">Batal</button>
			</div>
		</div>
    </div>
  </div>
</div>

<script type="text/javascript" src="assets/js/master/pelanggan.js"></script>