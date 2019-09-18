<div class="panel panel-default">
  <div class="panel-heading">Master Barang</div>
  <div class="panel-body">
	<div class="row>">
		<button type="button" name="tombolTambah" id="btnTambah" class="btn btn-primary">Baru</button>
		<br/><br/>
	</div>
	<table id="master-barang" class="table table-bordered table-striped">
	<thead>
		<tr>
            <th  style="width:1%"></th>
            <th class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_jenisbarang" id="q_jenisbarang">
						<option value="">Semua</option>
						<option value="PA">Pakan Ayam</option>
						<option value="PI">Pakan Ikan</option>
						<option value="O">Obat/Vitamin</option>
						<option value="V">Vaksin</option>
						<option value="L">Lain-lain</option>
					</select>
				</div>
			</th>
            <th class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_tipebarang" id="q_tipebarang">
						<option value="">Semua</option>
						<option value="I">Internal</option>
						<option value="E">Eksternal</option>
					</select>
				</div>
			</th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_kodebarang" id="q_kodebarang" placeholder="Kode Barang"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_namabarang" id="q_namabarang" placeholder="Nama Barang"></th>
            <th class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_bentukbarang" id="q_bentukbarang">
						<option value="">Semua</option>
						<option value="C">Crumble</option>
						<option value="T">Tepung</option>
						<option value="P">Padat</option>
						<option value="A">Cair</option>
						<option value="L">Lain-lain</option>
					</select>
				</div>
			</th>
            <th class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_satuan" id="q_satuan">
						<option value="">Semua</option>
						<?php
<<<<<<< HEAD
                        foreach ($satuan as $s) {
                            ?>
							<option value="<?php echo $s['uom']; ?>"><?php echo $s['uom']; ?></option>
						<?php
                        }
                        ?>
=======
						foreach($satuan as $s){
						?>
							<option value="<?php echo $s["uom"];?>"><?php echo $s["uom"];?></option>
						<?php
						}
						?>
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
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
            <th>No</th>
            <th class="col-md-1">Jenis Barang</th>
            <th class="col-md-1">Tipe Barang</th>
            <th class="col-md-2">Kode Barang</th>
            <th class="col-md-2">Nama Barang</th>
            <th class="col-md-1">Bentuk Barang</th>
            <th class="col-md-1">Satuan</th>
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
<<<<<<< HEAD
$style_label = 'col-sm-4';
$style_value = 'col-sm-8';
=======
$style_label = "col-sm-4";
$style_value = "col-sm-8";
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
?>

<div class="modal fade" id="modal_barang" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Master Barang</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_jenisbarang" class="<?php echo $style_label; ?> control-label">Jenis Barang</label>
					<div class="<?php echo $style_value; ?> input-group">
=======
					<label for="inp_jenisbarang" class="<?php echo $style_label;?> control-label">Jenis Barang</label>
					<div class="<?php echo $style_value;?> input-group">
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
						<select class="form-control input-sm" name="jenisbarang" id="inp_jenisbarang">
							<option value="PA">Pakan Ayam</option>
							<option value="PI">Pakan Ikan</option>
							<option value="O">Obat/Vitamin</option>
							<option value="V">Vaksin</option>
							<option value="L">Lain-lain</option>
						</select>
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_tipepakan" class="<?php echo $style_label; ?> control-label">Tipe Pakan</label>
					<div class="<?php echo $style_value; ?> input-group">
=======
					<label for="inp_tipepakan" class="<?php echo $style_label;?> control-label">Tipe Pakan</label>
					<div class="<?php echo $style_value;?> input-group">
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
						<label class="radio-inline">
							<input type="radio" name="tipepakan" id="inp_tipepakan_eksternal" value="E" checked> Eksternal
						</label>

						<label class="radio-inline">
							<input type="radio" name="tipepakan" id="inp_tipepakan_internal" value="I"> Internal
						</label>

					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_kodebarang" class="<?php echo $style_label; ?> control-label">Kode Barang</label>
					<div class="<?php echo $style_value; ?> input-group">
=======
					<label for="inp_kodebarang" class="<?php echo $style_label;?> control-label">Kode Barang</label>
					<div class="<?php echo $style_value;?> input-group">
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
					<input type="text" class="form-control input-sm field_input" name="kodebarang" id="inp_kodebarang" placeholder="Kode Barang" maxlength="15" required>
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_jenisgrupbarang" class="<?php echo $style_label; ?> control-label">Jenis/Group</label>
					<div class="<?php echo $style_value; ?> input-group">
=======
					<label for="inp_jenisgrupbarang" class="<?php echo $style_label;?> control-label">Jenis/Group</label>
					<div class="<?php echo $style_value;?> input-group">
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
					<input type="text" class="form-control input-sm field_input" name="jenisgrupbarang" id="inp_jenisgrupbarang" placeholder="Jenis/Group">
					<input type="hidden" class="form-control input-sm field_input" name="jenisgrupbarang_val" id="inp_jenisgrupbarang_val">
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_namabarang" class="<?php echo $style_label; ?> control-label">Nama Barang</label>
					<div class="<?php echo $style_value; ?> input-group">
=======
					<label for="inp_namabarang" class="<?php echo $style_label;?> control-label">Nama Barang</label>
					<div class="<?php echo $style_value;?> input-group">
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
					<input type="text" class="form-control input-sm field_input" name="namabarang" id="inp_namabarang" placeholder="Nama Barang" maxlength="50" required>
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_namaaliasbarang" class="<?php echo $style_label; ?> control-label">Nama Alias Barang</label>
					<div class="<?php echo $style_value; ?> input-group">
=======
					<label for="inp_namaaliasbarang" class="<?php echo $style_label;?> control-label">Nama Alias Barang</label>
					<div class="<?php echo $style_value;?> input-group">
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
					<input type="text" class="form-control input-sm field_input" name="namaaliasbarang" id="inp_namaaliasbarang" placeholder="Nama Alias Barang" maxlength="25" required>
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_bentukbarang" class="<?php echo $style_label; ?> control-label">Bentuk Barang</label>
					<div class="<?php echo $style_value; ?> input-group">
=======
					<label for="inp_bentukbarang" class="<?php echo $style_label;?> control-label">Bentuk Barang</label>
					<div class="<?php echo $style_value;?> input-group">
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
						<select class="form-control input-sm" name="bentukbarang" id="inp_bentukbarang">
							<option value="C">Crumble</option>
							<option value="T">Tepung</option>
							<option value="P">Padat</option>
							<option value="A">Cair</option>
							<option value="L">Lain-lain</option>
						</select>
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_satuanbarang" class="<?php echo $style_label; ?> control-label">Satuan Barang</label>
					<div class="<?php echo $style_value; ?> input-group">
						<select class="form-control input-sm" name="satuanbarang" id="inp_satuanbarang">
							<?php
                            foreach ($satuan as $s) {
                                ?>
								<option value="<?php echo $s['uom']; ?>"><?php echo $s['uom']; ?></option>
							<?php
                            }
                            ?>
=======
					<label for="inp_satuanbarang" class="<?php echo $style_label;?> control-label">Satuan Barang</label>
					<div class="<?php echo $style_value;?> input-group">
						<select class="form-control input-sm" name="satuanbarang" id="inp_satuanbarang">
							<?php
							foreach($satuan as $s){
							?>
								<option value="<?php echo $s["uom"];?>"><?php echo $s["uom"];?></option>
							<?php
							}
							?>
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
						</select>
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_jeniskelaminternak" class="<?php echo $style_label; ?> control-label">Jenis Kelamin Ternak</label>
					<div class="<?php echo $style_value; ?> input-group">
=======
					<label for="inp_jeniskelaminternak" class="<?php echo $style_label;?> control-label">Jenis Kelamin Ternak</label>
					<div class="<?php echo $style_value;?> input-group">
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
						<label class="checkbox-inline">
							<input type="checkbox" id="inp_jeniskelaminternak_betina" value="B"> Betina
						</label>
						<label class="checkbox-inline">
							<input type="checkbox" id="inp_jeniskelaminternak_jantan" value="J"> Jantan
						</label>
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_usiawalternak" class="<?php echo $style_label; ?> control-label">Usia Awal Ternak</label>
=======
					<label for="inp_usiawalternak" class="<?php echo $style_label;?> control-label">Usia Awal Ternak</label>
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
					<div class="col-md-3 input-group">
						<input type="text" class="form-control input-sm field_input" name="usiawalternak" id="inp_usiawalternak" onkeyup="cekNumerik(this)" required>
						<span class="input-group-addon" id="basic-addon2">Minggu</span>
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_usiakhirternak" class="<?php echo $style_label; ?> control-label">Usia Akhir Ternak</label>
=======
					<label for="inp_usiakhirternak" class="<?php echo $style_label;?> control-label">Usia Akhir Ternak</label>
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
					<div class="col-md-3 input-group">
						<input type="text" class="form-control field_input input-sm" name="usiakhirternak" id="inp_usiakhirternak" onkeyup="cekNumerik(this)" required>
						<span class="input-group-addon" id="basic-addon2">Minggu</span>
					</div>
				</div>
				<div class="form-group">
<<<<<<< HEAD
					<label for="inp_status" class="<?php echo $style_label; ?> control-label">Status</label>
					<div class="<?php echo $style_value; ?> input-group">
=======
					<label for="inp_status" class="<?php echo $style_label;?> control-label">Status</label>
					<div class="<?php echo $style_value;?> input-group">
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
						<div class="checkbox">
							<label>
								<input type="checkbox" name="status" id="inp_status" value="A"> Aktif
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

<<<<<<< HEAD
<script type="text/javascript" src="assets/js/master/barang.js"></script>
=======
<script type="text/javascript" src="assets/js/master/config_general.js"></script>
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
