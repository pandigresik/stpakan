<div class="panel panel-default">
  <div class="panel-heading">Master Gudang</div>
  <div class="panel-body">
	<div class="row>">
		<button type="button" name="tombolTambah" id="btnTambah" class="btn btn-primary">Baru</button>
		<br/><br/>
	</div>
	<table id="master-gudang" class="table table-bordered table-striped">
	<thead>
		<tr>
            <th style="width:1%"></th>
            <th class="col-md-1"><input type="text" class="form-control q_search" name="q_kodefarm" id="q_namafarm" placeholder="Nama Farm"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_namafarm" id="q_kodegudang" placeholder="Kode Gudang"></th>
            <th class="col-md-2"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_namagudang" id="q_namagudang" placeholder="Nama Gudang"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_maxberat" id="q_maxberat" placeholder="Maks. Berat"></th>
            <th class="col-md-2"><input type="text" class="form-control q_search" name="q_maxkuantitas" id="q_maxkuantitas" placeholder="Maks. Qty"></th>
	</tr>
		<tr>
            <th>No</th>
            <th class="col-md-1">Nama Farm</th>
            <th class="col-md-2">Kode Gudang</th>
            <th class="col-md-2"></th>
            <th class="col-md-2">Nama Gudang</th>
            <th class="col-md-2">Maks. Berat</th>
            <th class="col-md-2">Maks. Kuantitas</th>
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

<div class="modal fade" id="modal_gudang" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Master Gudang</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">
				<div class="form-group">
					<label class="<?php echo $style_label;?> control-label">Nama Farm</label>
					<div class="<?php echo $style_value;?> input-group-sm">
						<select class="form-control input-sm" name="namafarm" id="inp_namafarm">
							<?php
							foreach($farm as $f){
							?>
								<option value="<?php echo $f["kode_farm"];?>"><?php echo $f["nama_farm_full"];?></option>
							<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_kodegudang" class="<?php echo $style_label;?> control-label">Kode Gudang</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="kodegudang" id="inp_kodegudang" placeholder="Kode Gudang" maxlength="10" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_namagudang" class="<?php echo $style_label;?> control-label">Nama Gudang</label>
					<div class="<?php echo $style_value;?> input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="namagudang" id="inp_namagudang" placeholder="Nama Gudang" maxlength="30" required>
					</div>
				</div>
        <div class="form-group">
  					<label class="<?php echo $style_label;?> control-label" for="inp_beratmaksimal">Berat Maksimal</label>
  					<div class="<?php echo $style_value;?>">
  						<div class="input-group">
  							<input type="text" required="" onkeyup="cekNumerik(this)" id="inp_maxberat" name="beratmaksimal" class="form-control field_input input-sm">
  							<span class="input-group-addon">Kg/Pallet</span>
  						</div>
  					</div>
  			</div>
        <div class="form-group">
					<label class="<?php echo $style_label;?> control-label" for="inp_beratmaksimal">Qty. Maksimal</label>
					<div class="<?php echo $style_value;?>">
						<div class="input-group">
							<input type="text" required="" onkeyup="cekNumerik(this)" id="inp_maxkuantitas" name="qtymaksimal" class="form-control field_input input-sm">
							<span class="input-group-addon">Sak/Pallet</span>
						</div>
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

<script type="text/javascript" src="assets/js/master/gudang.js"></script>
