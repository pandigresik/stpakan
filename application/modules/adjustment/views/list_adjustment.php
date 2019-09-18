<div class="panel panel-default">
  <div class="panel-heading">Adjustment</div>
  <div class="panel-body">
	<div class="row>">
		<button type="button" name="tombolTambah" id="btnTambah" class="btn btn-primary">Baru</button>
		<br/><br/>
	</div>
	<table id="tb_adjustemnt" class="table table-bordered table-striped">
	<thead>
		<tr>
            <td  style="width:1%"></td>
            <td class="col-md-2"><input type="text" class="form-control q_search" name="q_noadjustment" id="q_noadjustment" placeholder="No. Adjustment"></td>
            <td class="col-md-2"><input type="text" class="form-control" id="inp_tanggal" placeholder="Tanggal"></td>
            <td class="col-md-2"><input type="text" class="form-control q_search" name="q_namafarm" id="q_namafarm" placeholder="Nama Farm"></td>
            <td class="col-md-1">
				<div class="input-group">
					<select class="form-control" name="q_tipe" id="q_tipe">
						<option value="">Semua</option>
						<option value="I">In</option>
						<option value="O">Out</option>
					</select>
				</div>
			</td>
            <td class="col-md-2"><input type="text" class="form-control q_search" name="q_alasan" id="q_alasan" placeholder="Alasan"></td>
        </tr>
		<tr>
			<th></th>
			<th>No. Adjustment</th>
			<th>Tanggal</th>
			<th>Nama Farm</th>
			<th>Tipe</th>
			<th>Alasan</th>
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

<div class="modal fade" id="modal_barang" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel">Master Barang</h4>
		</div>
		<div class="modal-body">
			<form class="form-horizontal">
				<div class="form-group">
					<label for="inp_noadjustment" class="col-sm-4 control-label">No. Adjustment</label>
					<div class="col-sm-8 input-group-sm">
						<input type="text" class="form-control input-sm field_input" name="noadjustment" id="inp_noadjustment" maxlength="5" required disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_tgladjustment" class="col-sm-4 control-label">Tanggal Adjustment</label>
					<div class="col-sm-8 input-group-sm">
					<input type="text" class="form-control input-sm field_input" name="tgladjustment" id="inp_tgladjustment" required>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_namafarm" class="col-sm-4 control-label">Nama Farm</label>
					<div class="col-sm-8 input-group-sm">
						<select class="form-control input-sm" name="namafarm" id="inp_namafarm">
							<?php foreach($farm as $f){?>
								<option value="I"><?php echo $f["nama_farm"];?></option>
							<?php }?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="inp_bentukbarang" class="col-sm-4 control-label">Tipe</label>
					<div class="col-sm-8 input-group-sm">
						<select class="form-control input-sm" name="tipe" id="inp_tipe">
							<option value="I">In</option>
							<option value="O">Out</option>
						</select>
					</div>
				</div>
			</form>
		</div>
		
		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolSimpan" id="btnSimpan" class="btn btn-primary disabled">Simpan</button>
				<button type="button" name="tombolUbah" id="btnUbah" class="btn btn-primary disabled">Ubah</button>
				<button type="button" name="tombolBatal" id="btnBatal" class="btn btn-primary">Batal</button>
			</div>
		</div>
    </div>
  </div>
</div>

<script type="text/javascript" src="assets/js/adjustment/adjustment.js"></script>