<div class="panel panel-default">
  <div class="panel-heading">Master Farm</div>
  <div class="panel-body">
	<div class="row>">
		<button type="button" name="tombolTambah" id="btnTambah" class="btn btn-primary">Baru</button>
		<br/><br/>
	</div>
	<table id="master-farm" class="table table-bordered table-striped">
	<thead>
		<tr>
            <th style="width:1%"></th>
            <th class="col-md-1"><input type="text" class="form-control q_search" name="q_kodefarm" id="q_kodefarm" placeholder="Kode Farm"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_namafarm" id="q_namafarm" placeholder="Nama Farm"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_alamat" id="q_alamat" placeholder="Alamat"></th>
            <th class="col-md-1"><input type="text" class="form-control q_search" name="q_kota" id="q_kota" placeholder="Kota"></th>
			<th class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_tipefarm" id="q_tipefarm">
						<option value="">Semua</option>
						<option value="I">Internal</option>
						<option value="E">Eksternal</option>
					</select>
				</div>
			</th>
            <th class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_grup" id="q_grup">
						<option value="">Semua</option>
						<option value="BRD">Breeding</option>
						<option value="BDY">Budidaya</option>
					</select>
				</div>
			</th>
			<th class="col-md-1"><input type="text" class="form-control q_search" name="q_gruppelanggan" id="q_gruppelanggan" placeholder="Grup Pelanggan"></th>
        </tr>
		<tr>
            <th>No</th>
            <th class="col-md-1">Kode Farm</th>
            <th class="col-md-2">Nama Farm</th>
            <th class="col-md-2">Alamat</th>
            <th class="col-md-1">Kota</th>
            <th class="col-md-1">Tipe Farm</th>
            <th class="col-md-1">Grup</th>
            <th class="col-md-2">Grup Pelanggan</th>
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
$style_label = "col-sm-3";
$style_value = "col-sm-9";
?>

<div class="modal fade" id="modal_farm" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Master Farm</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">
				<div class="form-group">
					<label for="inp_kodefarm" class="<?php echo $style_label;?> control-label">Kode Farm</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="kodefarm" id="inp_kodefarm" placeholder="Kode Farm" maxlength="10" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_namafarm" class="<?php echo $style_label;?> control-label">Nama Farm</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="namafarm" id="inp_namafarm" placeholder="Nama Farm" maxlength="50" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_alamat" class="<?php echo $style_label;?> control-label">Alamat</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="alamat" id="inp_alamat" placeholder="Alamat" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_kota" class="<?php echo $style_label;?> control-label">Kota</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<select id="inp_kota" name="kota"  class="form-control multicolumn" onchange="" style="width:200px;">
							<option value="">Pilihan : </option>
							<option class="header">Kota +Propinsi</option>
							<?php
							foreach($kota as $k){
							?>
								<option value="<?php echo $k["kota"];?>"><?php echo $k["kota"] . ' + ' . $k["propinsi"];?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_tipefarm" class="<?php echo $style_label;?> control-label">Tipe Farm</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<label class="radio-inline">
							<input type="radio" name="tipefarm" id="inp_tipefarminternal" value="I" checked onclick="mFarm.enableJmlFlok()"> Internal
						</label>
						<label class="radio-inline">
							<input type="radio" name="tipefarm" id="inp_tipefarmeksternal" value="E" onclick="mFarm.enableJmlFlok()"> Eksternal
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_grupfarm" class="<?php echo $style_label;?> control-label">Grup Farm</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<label class="radio-inline">
							<input type="radio" name="grupfarm" id="inp_grupfarmbreeding" value="BRD" checked onclick="mFarm.enableJmlFlok()"> Breeding
						</label>
						<label class="radio-inline">
							<input type="radio" name="grupfarm" id="inp_grupfarmbudidaya" value="BDY" onclick="mFarm.enableJmlFlok()"> Budidaya
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_jmlflok" class="<?php echo $style_label;?> control-label">Jumlah Flok</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" value="0" name="jml_flok" id="inp_jmlflok" placeholder="Jumlah Flok" maxlength="2" required disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_gruppelanggan" class="<?php echo $style_label;?> control-label">Grup Pelanggan</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<select id="inp_gruppelanggan" class="form-control">
							<?php
							foreach($pelanggan as $p){
							?>
								<option value="<?php echo $p["kode_pelanggan"];?>"><?php echo $p["nama_pelanggan"];?></option>
							<?php
							}
							?>
						</select>
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

<script type="text/javascript" src="assets/js/master/farm.js"></script>
<script type="text/javascript" src="assets/js/master/combomulticolumn.js"></script>