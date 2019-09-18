<div class="panel panel-default">
	<div class="panel-heading">Master - Daftar OP</div>
	<div class="panel-body">
		<div class="row>">
			<button type="button" name="tombolTambah" id="btnTambah"
				class="btn btn-primary">Baru</button>
			<br />
			<br />
		</div>
		<table id="master-daftar-op-marketing"
			class="table table-bordered table-striped">
			<thead>
				<tr>
					<th class="col-md-2"><select class="form-control q_search"
						name="q_grup" id="q_grup" placeholder="Grup">
					<option value="" selected>SEMUA</option>
					<?php foreach ($grup_farm as $key => $value) { ?>
						<option value="<?php echo $value['GRUP_FARM']; ?>"><?php echo $value['GRUP_FARM_LABEL']; ?></option>
					<?php } ?>
					</select></th>
					<th class="col-md-2"><select class="form-control q_search"
						name="q_namafarm" id="q_namafarm" placeholder="Nama Farm">
					<option value="" selected>SEMUA</option>
					<?php foreach ($farm as $key => $value) { ?>
						<option value="<?php echo $value['KODE_FARM']; ?>"><?php echo $value['NAMA_FARM']; ?></option>
					<?php } ?>
					</select></th>
					<th class="col-md-1"><select class="form-control q_search"
						name="q_tahun" id="q_tahun" placeholder="Tahun">

					<option value="" selected>SEMUA</option>
					<?php foreach ($tahun as $key => $value) { ?>
						<option value="<?php echo $value['TAHUN']; ?>"><?php echo $value['TAHUN']; ?></option>
					<?php } ?>
					</select></th>
					<th class="col-md-2">
						<!--input type="text"
						class="form-control q_search" name="q_tanggal_kirim"
						id="q_tanggal_kirim" placeholder="Tanggal Kirim"-->

		                <div class="input-group">
		                    <input type="text"
							class="form-control q_search" name="q_tanggal_kirim"
							id="q_tanggal_kirim" placeholder="Tanggal Kirim">
		                    <div class="input-group-addon">
		                        <span class="glyphicon glyphicon-calendar"></span>
		                    </div>
		                </div>
					</th>
					<th class="col-md-1">
						<button type="button" onclick="goSearch()" name="tombolCari" id="btnCari"
                    class="btn btn-primary">Cari</button>
					</th>
					<th class="col-md-1">
						<!--input type="text" class="form-control q_search" name="q_no_op_akhir" id="q_no_op_akhir" placeholder="No. OP Akhir"-->
					</th>
					<th class="col-md-1">
						<!--input type="text" class="form-control q_search" name="q_no_op_pakai" id="q_no_op_pakai" placeholder="No. OP Pakai"-->
					</th>
				</tr>
				<tr>
					<th class="col-md-1">Grup</th>
					<th class="col-md-2">Nama Farm</th>
					<th class="col-md-1">Tahun</th>
					<th class="col-md-2">Tanggal Kirim</th>
					<th class="col-md-1">No. OP Awal</th>
					<th class="col-md-1">No. OP Akhir</th>
					<th class="col-md-1">No. OP Pakai</th>
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

<div class="modal fade" id="modal_op_marketing" tabindex="-1"
	role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 50%">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Master - Daftar OP Baru</h4>
			</div>
			<div class="modal-body">
				<form class="form-horizontal">
					<div class="form-group">
						<label for="inp_grup_farm"
							class="<?php echo $style_label;?> control-label">Grup Farm</label>
						<div class="<?php echo $style_value;?> input-group">
							<select class="form-control field_input" name="grup_farm"
								id="inp_grup" placeholder="Grup Farm">
						<?php foreach ($grup_farm as $key => $value) { ?>
						<option value="<?php echo $value['GRUP_FARM']; ?>"><?php echo $value['GRUP_FARM_LABEL']; ?></option>
						<?php } ?>
					</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_grup_farm"
							class="<?php echo $style_label;?> control-label">Nama Farm</label>
						<div class="<?php echo $style_value;?> input-group">
							<select class="form-control field_input" name="grup_farm"
								id="inp_nama_farm" placeholder="Grup Farm">
						<?php foreach ($farm as $key => $value) { ?>
						<option value="<?php echo $value['KODE_FARM']; ?>"><?php echo $value['NAMA_FARM']; ?></option>
						<?php } ?>
					</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_tahun_berlaku"
							class="<?php echo $style_label;?> control-label">Tahun Berlaku</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control field_input"
								name="tahun_berlaku" id="inp_tahun" placeholder="Tahun Berlaku" onkeyup="kontrol_number(this)" onchange="kontrol_op_pakai(this)">
						</div>
					</div>
					<div class="form-group">
						<label for="inp_kodekandang"
							class="<?php echo $style_label;?> control-label">Tanggal Kirim</label>
						<div class="<?php echo $style_value;?> input-group">
							
			                <div class="input-group">
			                    <input type="text" class="form-control field_input"
								name="periode_siklus" id="inp_tanggal_kirim"
								placeholder="Tanggal Kirim" onchange="kontrol_kirim(this)">
			                    <div class="input-group-addon">
			                        <span class="glyphicon glyphicon-calendar"></span>
			                    </div>
			                </div>
						</div>
					</div>
					<div class="form-group">
						<label for="inp_kodekandang"
							class="<?php echo $style_label;?> control-label">No. OP Awal</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control field_input"
								name="periode_siklus" id="inp_no_op_awal"
								placeholder="No. OP Awal" onkeyup="kontrol_number(this)" onchange="kontrol_op_pakai(this)">
						</div>
					</div>
					<div class="form-group">
						<label for="inp_kodekandang"
							class="<?php echo $style_label;?> control-label">No. OP Akhir</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control field_input"
								name="periode_siklus" id="inp_no_op_akhir"
								placeholder="No. OP Akhir" onkeyup="kontrol_number(this)" onchange="kontrol_op_pakai(this)">
						</div>
					</div>
					<div class="form-group">
						<label for="inp_kodekandang"
							class="<?php echo $style_label;?> control-label">No. OP Pakai</label>
						<div class="<?php echo $style_value;?> input-group">
							<input type="text" class="form-control field_input"
								name="periode_siklus" id="inp_no_op_pakai"
								placeholder="No. OP Pakai" onkeyup="kontrol_number(this)" onchange="kontrol_op_pakai(this)">
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

<script type="text/javascript"
	src="assets/js/master/daftar_op_marketing.js"></script>