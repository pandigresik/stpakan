<div class="row col-md-12">
	<div class="col-md-12">
		<div class="form-inline">
			<div class="checkbox">
				<label>
					<input type="checkbox" onclick="ReviewPakanRusak.kontrol_chekbox(this)" name="checkbox_tindak_lanjut" value="1" id="checkbox_tindak_lanjut" checked="checked">
					Filter retur pakan rusak yang membutuhkan tindak lanjut</label>
			</div>
		</div>
		<div class="form-inline new-line tanggal_retur">
			<label for="tanggal-kirim">Tanggal Retur </label>
			<div class="form-group">
				<div class="input-group div-date">
					<input type="text" class="form-control" id="startDate" name="startDate" readonly="">
					<div class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</div>
				</div>
			</div>
			<label for="tanggal-kirim">&nbsp;s/d&nbsp;</label>
			<div class="form-group">
				<div class="input-group div-date">
					<input type="text" class="form-control" id="endDate" name="endDate" readonly="">
					<div class="input-group-addon">
						<span class="glyphicon glyphicon-calendar"></span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<span disabled class="btn btn-default btn_cari" onclick="ReviewPakanRusak.header_pakan_rusak(this)">Cari</span>
			</div>
		</div>
	</div>
</div>
<div class="new-line col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			DAFTAR RETUR PAKAN RUSAK
		</div>
		<div class="panel-body">
			<table class="table table-bordered " id="header_pakan_rusak">
				<thead>
					<tr>
						<th class="col-md-2">
						<input type="text" placeholder="No. Retur" id="f_no_retur" name="f_no_retur" class="form-control f_search" onkeyup="ReviewPakanRusak.header_pakan_rusak()">
						</th>
						<th class="col-md-1">
						<input type="text" placeholder="Kandang" id="f_kandang" name="f_kandang" class="form-control f_search" onkeyup="ReviewPakanRusak.header_pakan_rusak()">
						</th>
					</tr>
					<tr>
						<th class="col-md-2">No. Retur</th>
						<th class="col-md-1">Kandang</th>
						<th class="col-md-1">Tanggal/Jam Retur</th>
						<th class="col-md-2">Diserahkan Oleh</th>
						<th class="col-md-2">Penerima</th>
						<th class="col-md-1">Tanggal/Jam Review Kepala</th>
						<th class="col-md-2">Keterangan</th>
						<th class="col-md-2">Lampiran</th>
					</tr>
				</thead>
				<tbody>

				</tbody>
			</table>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			Detail Retur Pakan Rusak <span id="span_no_retur"></span>
		</div>
		<div class="panel-body">
			<div class="row col-md-7">
				<table class="table table-bordered " id="detail_pakan_rusak">
					<thead>
						<tr>
							<th class="col-md-2">Nama Pakan</th>
							<th class="col-md-1">Jenis Kelamin</th>
							<th class="col-md-1">Jumlah Retur (Sak)</th>
							<th class="col-md-1">Timbangan Sak (Kg)</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
			<div class="col-md-2 div_button">

				<div class="form-group">
					<span class="btn btn-default" onclick="ReviewPakanRusak.simpan('A')">Approve</span>
				</div>
				<div class="form-group">
					<span class="btn btn-default" onclick="ReviewPakanRusak.simpan('R')">Reject</span>
				</div>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/review_pakan_rusak/review.css" >

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/review_pakan_rusak/review.js"></script>

