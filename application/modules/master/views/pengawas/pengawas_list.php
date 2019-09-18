<div class="panel panel-default">
  <div class="panel-heading">Master Pegawai</div>
  <div class="panel-body">
	<div class="row>">
		<button type="button" name="tombolTambah" id="btnTambah" class="btn btn-primary">Baru</button>
		<br/><br/>
	</div>
	<table id="master-pengawas" class="table table-bordered table-striped">
	<thead>
		<tr>
            <th  style="width:1%"></th>
            <th class="col-md-1"><input type="text" class="form-control q_search" name="q_kodepengawas" id="q_kodepengawas" placeholder="Kode Pegawai"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_namapengawas" id="q_namapengawas" placeholder="Nama Pegawai"></th>
            <th class="col-md-2">
				<div class="input-group">
					<select class="form-control" name="q_jeniskelamin" id="q_jeniskelamin">
						<option value="">Semua</option>
						<option value="L">Laki-laki</option>
						<option value="P">Perempuan</option>
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
            <th class="col-md-1">Kode Pegawai</th>
            <th class="col-md-2">Nama Pegawai</th>
            <th class="col-md-2">Jenis Kelamin</th>
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
$style_value = "col-sm-8";
?>

<div class="modal fade" id="modal_pengawas" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Master Pegawai</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">
				<div class="form-group">
					<label for="inp_kodepengawas" class="<?php echo $style_label;?> control-label">Kode Pegawai</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="kodepengawas" id="inp_kodepengawas" placeholder="Kode Pegawai" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_namapengawas" class="<?php echo $style_label;?> control-label">Nama Pegawai</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="namapengawas" id="inp_namapengawas" placeholder="Nama Pegawai" maxlength="50" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_jeniskelamin" class="<?php echo $style_label;?> control-label">Jenis Kelamin</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<label class="radio-inline">
							<input type="radio" name="jeniskelamin" id="inp_jeniskelaminlaki" value="L" checked> Laki-laki
						</label>
						<label class="radio-inline">
							<input type="radio" name="jeniskelamin" id="inp_jeniskelaminperempuan" value="P"> Perempuan
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_telp" class="<?php echo $style_label;?> control-label">No. Telp.</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="telp" id="inp_telp" placeholder="No. Telp." maxlength="15" onkeyup="cekNumerik(this)" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_gruppegawai" class="<?php echo $style_label;?> control-label">Grup Pegawai</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<select class="form-control input-sm" name="gruppegawai" id="inp_gruppegawai">
							<?php
							foreach($grups as $g){
								echo '<option value="' . $g["grup_pegawai"] . '">' . $g["deskripsi"] . '</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_username" class="<?php echo $style_label;?> control-label">Username</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm col-md-6 field_input" name="username" id="inp_username" placeholder="Username" maxlength="15" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_password" class="<?php echo $style_label;?> control-label">Password</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="password" class="form-control input-sm col-md-6 field_input" name="password" id="inp_password" placeholder="Password" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_status" class="<?php echo $style_label;?> control-label">Status</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="status" id="inp_status" value="A"> Aktif
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_status" class="<?php echo $style_label;?> control-label">Farm</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<?php 
							if(!empty($farm)){
								foreach($farm as $f){
									echo '<div class="checkbox">
											<label>
												<input type="checkbox" name="kode_farm[]" value="'.$f->kode_farm.'"> '.$f->nama_farm.'
											</label>
										</div>';
								}
							}
						?>	
						
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

<script type="text/javascript" src="assets/js/master/pengawas.js"></script>