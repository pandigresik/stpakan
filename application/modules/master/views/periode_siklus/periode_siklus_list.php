<div class="panel panel-default">
	<div class="panel-heading">Master - Periode Siklus</div>
	<div class="panel-body">
		<div class="row>">
			<button type="button" name="tombolTambah" id="btnTambah"
				class="btn btn-primary">Baru</button>
			<br />
			<br />
		</div>
		<table id="master-periode-siklus"
			class="table table-bordered table-striped">
			<thead>
				<tr>
					<th class="col-md-2"><input type="text"
						class="form-control q_search" name="q_periodesiklus"
						id="q_periodesiklus" placeholder="Periode Siklus"></th>
					<th class="col-md-2"><input type="text"
						class="form-control q_search" name="q_namafarm" id="q_namafarm"
						placeholder="Nama Farm"></th>
					<th class="col-md-2"><input type="text"
						class="form-control q_search" name="q_namastrain"
						id="q_namastrain" placeholder="Nama Strain"></th>
					<th class="col-md-2">
						<div class="form-inline">
							<select class="form-control" name="q_status" id="q_status">
								<option value="">Semua</option>
								<option value="A">Aktif</option>
								<option value="N">Tidak Aktif</option>
							</select>
						<button type="button" onclick="goSearch()" name="tombolCari" id="btnCari"
                    class="btn btn-primary">Cari</button>
						</div>
					</th>
				</tr>
				<tr>
					<th class="col-md-2">Periode Siklus</th>
					<th class="col-md-2">Nama Farm</th>
					<th class="col-md-2">Nama Strain</th>
					<th class="col-md-2">Status</th>
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

<div class="modal fade" id="modal_periode_siklus" tabindex="-1"
	role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Master - Periode Siklus</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal">
					<input type="hidden" id="kode_siklus" />
					<div class="form-group">
						<label for="inp_kodekandang"
							class="<?php echo $style_label;?> control-label">Periode Siklus</label>
						<div class="<?php echo $style_value;?> input-group">
                            <input type="text" class="form-control field_input hide"
                                name="periode_siklus" id="inp_periode_siklus2"
                                placeholder="Periode Siklus">
                            <input type="text" class="form-control field_input hide"
                                name="periode_siklus" id="inp_periode_siklus3"
                                placeholder="Periode Siklus" readonly>
							<select type="text" class="form-control field_input"
								name="periode_siklus" id="inp_periode_siklus"
								placeholder="Periode Siklus">
								<?php foreach ($year as $key => $value) { ?>
									<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_tipekandang"
							class="<?php echo $style_label;?> control-label">Nama Farm</label>
						<div class="<?php echo $style_value;?> input-group">
                            <input type="text" class="form-control field_input hide"
                                name="nama_farm" id="inp_nama_farm2"
                                placeholder="Nama Farm" readonly>
							<select class="form-control" name="nama_farm" id="inp_nama_farm">
							<?php foreach($farm as $key => $value){ ?>
							<option value="<?php echo $value['kode_farm']; ?>"><?php echo $value['nama_farm'].' - '.$value['kode_farm']; ?></option>
							<?php } ?>
						</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_tipelantai"
							class="<?php echo $style_label;?> control-label">Nama Strain</label>
						<div class="<?php echo $style_value;?> input-group">
                            <input type="text" class="form-control field_input hide"
                                name="nama_strain" id="inp_nama_strain2"
                                placeholder="Nama Strain" readonly>
							<select class="form-control" name="nama_strain"
								id="inp_nama_strain">
							<?php foreach($strain as $key => $value){ ?>
							<option value="<?php echo $value['KODE_STRAIN']; ?>"><?php echo $value['NAMA_STRAIN'].' - '.$value['KODE_STRAIN']; ?></option>
							<?php } ?>
						</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_statuskandang"
							class="<?php echo $style_label;?> control-label">Status Periode</label>
						<div class="<?php echo $style_value;?> input-group">
							<div class="checkbox">
								<label> <input type="checkbox" name="status_priode"
									id="inp_status_priode" value="A"> Aktif
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-12 control-label" id="inp_msg" style="color:red"></label>
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

<script type="text/javascript" src="assets/js/master/periode_siklus.js"></script>
