<div class="panel panel-default">
  <div class="panel-heading">Master Kavling</div>
  <div class="panel-body">
		<ul class="nav nav-tabs">
			<li class="active" id="nav-tab-daftar"><a href="#tab-daftar" data-toggle="tab">Daftar Kavling</a></li>
			<li id="nav-tab-layout"><a href="#tab-layout" data-toggle="tab">Layout Kavling</a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="tab-daftar" style="padding:10px;">
				<div class="row>">
					<button type="button" name="tombolTambah" id="btnTambah" class="btn btn-primary">Baru</button>
					<br/><br/>
				</div>
				<table id="master-kavling" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th  style="width:1%"></th>
						<th class="col-md-2"><input type="text" class="form-control q_search" name="q_namafarm" id="q_namafarm" placeholder="Nama Farm"></th>
						<th class="col-md-2"><input type="text" class="form-control q_search" name="q_namagudang" id="q_namagudang" placeholder="Nama Gudang"></th>
						<th class="col-md-1"><input type="text" class="form-control q_search" name="q_nomorkavling" id="q_nomorkavling" placeholder="Nomor Kavling"></th>
						<th class="col-md-2"></th>
						<th class="col-md-1"><input type="text" class="form-control q_search" name="q_jmlpallet" id="q_jmlpallet" placeholder="Jumlah Pallet"></th>
						<th class="col-md-1">
							<div class="input-group">
								<select class="form-control" name="q_status" id="q_status">
									<option value="">Semua</option>
									<option value="A">Aktif</option>
									<option value="N">Tidak Aktif</option>
								</select>
							</div>
						</th>
						<th></th>
						<th></th>
					</tr>
					<tr>
						<th class="vert-align">No</th>
						<th class="col-md-2 vert-align">Nama Farm</th>
						<th class="col-md-2 vert-align">Nama Gudang</th>
						<th class="col-md-1 vert-align">Nomor Kavling</th>
						<th class="col-md-2 vert-align">Posisi</th>
						<th class="col-md-1 vert-align">Jumlah Pallet</th>
						<th class="col-md-1 vert-align">Status</th>
						<th></th>
						<th></th>
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
			<div class="tab-pane" id="tab-layout" style="padding:10px;">
				<div class="row">
					<div class="col-md-4">
						<form class="form-inline">
							<div class="form-group">
								<label>Nama Farm : </label>
								<select name="selectFarm" class="form-control" id="selectFarm"></select>
							</div>
						</form>
					</div>

					<div class="col-md-6">
						<form class="form-inline">
							<div class="form-group">
								<label>Nama Gudang : </label>
								<select name="selectGudang" class="form-control" id="selectGudang">
									<option value=""> - Pilih Gudang - </option>
								</select>
							</div>
						</form>
					</div>
				</div>

				<div class="row">
					<div id="layout" style="padding:20px;">
					</div>
				</div>
			</div>
		</div>
  </div>
</div>

<?php
$style_label = "col-sm-4";
$style_value = "col-sm-8";
?>

<div class="modal fade" id="modal_kavling" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Master Kavling</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">
				<?php $disabledFarm = ($this->session->userdata("level_user") == "KF" or $this->session->userdata("level_user") == "AG") ? "disabled" : ""?>
				<?php $disabledGdg = ($this->session->userdata("level_user") == "AG") ? "disabled" : ""?>
				<div class="form-group">
					<input type="hidden" name="ses_kodefarm" value="<?php echo $this->session->userdata("kode_farm");?>" id="ses_kodefarm"/>
					<label for="inp_namafarm" class="<?php echo $style_label;?> control-label">Nama Farm <?php echo $this->session->userdata("kode_farm");?></label>
					<div class="col-md-7 input-group-sm">
						<select style="width:250px;" class="form-control input-sm" name="namafarm" id="inp_namafarm" <?php echo $disabledFarm;?>>
						<?php
						foreach($farm as $f){
						?>
							<option value='<?php echo $f["kode_farm"];?>'><?php echo $f["nama_farm"]." - ".$f["kode_farm"];?></option>
						<?php
						}
						?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_namagudang" class="<?php echo $style_label;?> control-label">Nama Gudang</label>
					<div class="col-md-7 input-group-sm">
						<select style="width:250px;" class="form-control input-sm" name="namagudang" id="inp_namagudang" <?php echo $disabledGdg;?>>
						<?php
						foreach($gudang as $g){
							echo "<option value='".$g["kode_gudang"]."'>".$g["nama_gudang"]." - ".$g["kode_gudang"]."</option>";
						}
						?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_nomorbaris" class="<?php echo $style_label;?> control-label">Nomor Baris</label>
					<div class="col-md-1">
						<select style="width:50px" class="form-control input-sm" name="nomorbaris" id="inp_nomorbaris">
						<?php
						foreach (range('A', 'Z') as $char) {
							echo '<option value="'.$char.'">'.$char.'</option>';
						}
						?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_nomorposisi" class="<?php echo $style_label;?> control-label">Nomor Posisi</label>
					<div class="col-md-1">
						<input type="text" style="width:50px" class="form-control input-sm field_input" name="nomorposisi" id="inp_nomorposisi" onkeyup="cekNumerik(this)" onchange="generateKavling()" required>
					</div>
					<div class="col-md-7">
						<select style="width:150px" class="form-control input-sm" name="namaposisi" id="inp_namaposisi">
							<option value="L">Kiri Lorong</option>
							<option value="R">Kanan Lorong</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_kolom1" class="<?php echo $style_label;?> control-label">Nomor Kolom</label>
					<div class="col-md-2">
						<input type="text" style="width:80px" class="form-control input-sm field_input" name="kolom1" id="inp_kolom1" onkeyup="cekNumerik(this)" onchange="generateKavling()" required>
					</div>
					<div class="col-md-1 hideable" style="vertical-align: middle;text-align : center;">
						<label class="control-label">s.d</label>
					</div>
					<div class="col-md-2 hideable">
						<input type="text" style="width:80px" class="form-control input-sm field_input" name="kolom2" id="inp_kolom2" onkeyup="cekNumerik(this)" onchange="generateKavling()" required>
					</div>
					<div class="col-md-1 hideable" style="vertical-align: middle;text-align : center;">
						<label class="control-label">step</label>
					</div>
					<div class="col-md-2 hideable">
						<input type="text" style="width:80px" class="form-control input-sm field_input" name="step" id="inp_step" onkeyup="cekNumerik(this)" onchange="generateKavling()" required>
					</div>
				</div>
				<div class="form-group hideable">
					<label class="<?php echo $style_label;?> control-label"></label>
					<div class="col-md-8 generate-kavling">

					</div>
				</div>

				<div class="form-group">
					<label for="inp_beratmaksimal" class="<?php echo $style_label;?> control-label">Jumlah Pallet</label>
          <div class="col-md-5">
						<input type="text" class="form-control field_input input-sm"  onkeyup="cekNumerik(this)" name="jmlpallet" id="inp_jmlpallet" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_kodeverifikasi" class="<?php echo $style_label;?> control-label">Kode Verifikasi</label>
					<div class="col-md-5">
						<input type="text" class="form-control field_input input-sm" name="kodeverifikasi" id="inp_kodeverifikasi" required>
					</div>
				</div>
				<div class="form-group">
					<label class="<?php echo $style_label;?> control-label">Status Kavling</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<label class="radio-inline">
							<input type="radio" name="statuskavling" id="stAktif" value="A" checked> Aktif
						</label>

						<label class="radio-inline">
							<input type="radio" name="statuskavling" id="stTdkAktif" value="N"> Tidak Aktif
						</label>

						<label class="radio-inline">
							<input type="radio" name="statuskavling" id="stKunciMasuk" value="M"> Kunci Masuk
						</label>

						<label class="radio-inline">
							<input type="radio" name="statuskavling" id="stKunciKeluar" value="K"> Kunci Keluar
						</label>

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

<style type="text/css">
	.table thead>tr>th.vert-align{
		vertical-align: middle;
		text-align : center;
	}
	.table tbody>tr>td.vert-align{
		vertical-align: middle;
		text-align : center;
	}
	.table tbody tr.highlight td {
		background-color: #CBE8F7;
	}
	.vert-align{
		vertical-align: middle;text-align : center;
	}

</style>

<link rel="stylesheet" type="text/css" href="assets/css/penerimaan_pakan/penerimaan.css">
<script type="text/javascript" src="assets/js/master/kavling.js"></script>
