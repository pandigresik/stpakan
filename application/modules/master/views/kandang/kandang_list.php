<div class="panel panel-default">
  <div class="panel-heading">Master Kandang</div>
  <div class="panel-body">
	<div class="row>">
		<button type="button" name="tombolTambah" id="btnTambah" class="btn btn-primary">Baru</button>
		<br/><br/>
	</div>
	<table id="master-kandang" class="table table-bordered table-striped">
	<thead>
		<tr>
            <th class="col-md-1"></th>
            <th style="width:1%"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_namafarm" id="q_namafarm" placeholder="Nama Farm"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_namakandang" id="q_namakandang" placeholder="Nama Kandang"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_kapasitaskandangjantan" id="q_kapasitaskandangjantan" placeholder="Jml Maks Jantan"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_kapasitaskandangbetina" id="q_kapasitaskandangbetina" placeholder="Jml Maks Betina"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_kapasitaskandang" id="q_kapasitaskandang" placeholder="Jml Maks Populasi"></th>
            <th class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_tipekandang" id="q_tipekandang">
						<option value="">Semua</option>
						<option value="O">Open</option>
						<option value="C">Close</option>
					</select>
				</div>
			</th>
            <th class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_tipelantai" id="q_tipelantai">
						<option value="">Semua</option>
						<option value="S">Slate</option>
						<option value="L">Litter</option>
					</select>
				</div>
			</th>
            <th class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_status" id="q_status">
						<option value="">Semua</option>
						<option value="A">Aktif</option>
						<option value="N">Tidak Aktif</option>
					</select>
				</div>
			</th>
        </tr>
		<tr>
			<th>Kode Kandang</th>
			<th>No</th>
            <th class="col-md-2">Nama Farm</th>
            <th class="col-md-2">Nama Kandang</th>
            <th class="col-md-1">Jml Maks Jantan</th>
            <th class="col-md-1">Jml Maks Betina</th>
            <th class="col-md-1">Jml Maks Populasi</th>
            <th class="col-md-1">Tipe Kandang</th>
            <th class="col-md-1">Tipe Lantai</th>
            <th class="col-md-1">Status</th>
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
$style_value = "col-sm-7";
?>

<div class="modal fade" id="modal_kandang" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Master Kandang</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">

					<div class="form-group">
						<label class="<?php echo $style_label;?> control-label">Nama Farm</label>
						<div class="<?php echo $style_value;?> input-group">
							<select class="form-control" name="namafarm" id="inp_namafarm" onchange="mKandang.filterInputan(this)">
								<?php
								foreach($farm as $f){
								?>
									<option data-jmlflok="<?php echo $f["jml_flok"] ?>" data-grupfarm="<?php echo $f["grup_farm"] ?>" value="<?php echo $f["kode_farm"];?>"><?php echo $f["nama_farm_full"];?></option>
								<?php
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_kodekandang" class="<?php echo $style_label;?> control-label">Kode Kandang</label>
						<div class="<?php echo $style_value;?> input-group">
						<input type="text" class="form-control field_input required" name="kodekandang" id="inp_kodekandang" placeholder="Kode Kandang" maxlength="5">
						</div>
					</div>
					<div class="form-group">
						<label for="inp_namakandang" class="<?php echo $style_label;?> control-label">Nama Kandang</label>
						<div class="<?php echo $style_value;?> input-group">
						<input type="text" class="form-control field_input  required" name="namakandang" id="inp_namakandang" placeholder="Nama Kandang" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label for="inp_digitcek" class="<?php echo $style_label;?> control-label">Digit Cek</label>
						<div class="<?php echo $style_value;?> input-group">
						<input type="text" class="form-control required digit" name="digitcek" id="inp_digitcek" placeholder="Digit Cek" maxlength="10">
						</div>
					</div>
				<!-- awal  khusus untuk breeding -->
					<div class="form-group" data-grupfarm="BRD">
						<label for="inp_kapasitaskandangjantan" class="<?php echo $style_label;?> control-label">Jml Max Jantan</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control field_input numeric required" name="kapasitaskandangjantan" id="inp_kapasitaskandangjantan" placeholder="Jml Max Jantan">
							<span class="input-group-addon" id="basic-addon2">Ekor</span>
						</div>
					</div>
					<div class="form-group" data-grupfarm="BRD">
						<label for="inp_kapasitaskandangbetina" class="<?php echo $style_label;?> control-label">Jml Max Betina</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control field_input numeric required" name="kapasitaskandangbetina" id="inp_kapasitaskandangbetina" placeholder="Jml Max Betina">
							<span class="input-group-addon" id="basic-addon2">Ekor</span>
						</div>
					</div>
					<div class="form-group" data-grupfarm="BRD">
						<label for="inp_luaskandangjantan" class="<?php echo $style_label;?> control-label">Luas Kandang Jantan</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control numeric required" name="luaskandangjantan" id="inp_luaskandangjantan" placeholder="Luas Kandang Jantan">
							<span class="input-group-addon" id="basic-addon2">M<sup>2</sup></span>
						</div>
					</div>
					<div class="form-group" data-grupfarm="BRD">
						<label for="inp_luaskandangbetina" class="<?php echo $style_label;?> control-label">Luas Kandang Betina</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control numeric required" name="luaskandangbetina" id="inp_luaskandangbetina" placeholder="Luas Kandang Betina">
							<span class="input-group-addon" id="basic-addon2">M<sup>2</sup></span>
						</div>
					</div>
				<!-- akhir  khusus untuk breeding -->
				<!-- awal  khusus untuk budidaya -->
					<div class="form-group" data-grupfarm="BDY">
						<label for="inp_kapasitaskandang" class="<?php echo $style_label;?> control-label">Kapasitas Kandang</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control field_input numeric required" name="kapasitaskandang" id="inp_kapasitaskandang" placeholder="Kapasitas kandang">
							<span class="input-group-addon" id="basic-addon2">Ekor</span>
						</div>
					</div>
					<div class="form-group" data-grupfarm="BDY">
						<label for="inp_luaskandang" class="<?php echo $style_label;?> control-label">Luas Kandang</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control numeric required" name="luaskandang" id="inp_luaskandang" placeholder="Luas Kandang">
							<span class="input-group-addon" id="basic-addon2">M<sup>2</sup></span>
						</div>
					</div>
					<div class="form-group" data-grupfarm="BDY">
						<label for="inp_jmlsekat" class="<?php echo $style_label;?> control-label">Jumlah Sekat/Bagian</label>
						<div class="<?php echo $style_value;?> input-group">
						<input type="text" class="form-control numeric required" name="jml_sekat" id="inp_jmlsekat" placeholder="Jumlah Sekat/Bagian" maxlength="3">
						</div>
					</div>
				<!-- akhir  khusus untuk budidaya -->
					<div class="form-group">
						<label for="inp_tipekandang" class="<?php echo $style_label;?> control-label">Tipe Kandang</label>
						<div class="<?php echo $style_value;?> input-group">
							<select class="form-control" name="tipekandang" id="inp_tipekandang">
								<option value="O">Open</option>
								<option value="C">Close</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_tipelantai" class="<?php echo $style_label;?> control-label">Tipe Lantai</label>
						<div class="<?php echo $style_value;?> input-group">
							<select class="form-control" name="tipelantai" id="inp_tipelantai">
								<option value="S">Slate</option>
								<option value="L">Litter</option>
							</select>
						</div>
					</div>
				<!-- awal khusus budidaya -->
					<div class="form-group" data-grupfarm="BDY">
						<label for="inp_noflok" class="<?php echo $style_label;?> control-label">Flok</label>
						<div class="<?php echo $style_value;?> input-group">
							<select class="form-control" name="noflok" id="inp_noflok">

							</select>
						</div>
					</div>
				<!-- akhir khusus budidaya -->
					<div class="form-group">
						<label for="inp_statuskandang" class="<?php echo $style_label;?> control-label">Status Kandang</label>
						<div class="<?php echo $style_value;?> input-group">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="statuskandang" id="inp_statuskandang" value="A"> Aktif
								</label>
							</div>
						</div>
					</div>

			</form>
		</div>

		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolSimpan" id="btnSimpan" class="btn btn-primary">Simpan</button>
				<button type="button" name="tombolUbah" id="btnUbah" class="btn btn-primary">Ubah</button>
				<button type="button" name="tombolBatal" id="btnBatal" class="btn btn-primary">Batal</button>
			</div>
		</div>
    </div>
  </div>
</div>

<script type="text/javascript" src="assets/js/master/kandang.js"></script>
