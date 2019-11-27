<div class="panel panel-default">
		<div class="panel-heading">
			Berita Acara Penerimaan DOC In
		</div>
		<div class="panel-body">
			<div>
				<div class="row">
				<form class="form">
					<div class="form-inline col-md-12">
							<label for="tanggal-docinawal">Tanggal Doc In</label>
							<div class="form-group">
								<div class="input-group date">
									<input type="text" readonly name="startDate" placeholder="Tanggal Terima Awal" class="form-control required">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>
							<label for="tanggal-docinakhir">&nbsp;s/d&nbsp;</label>
							<div class="form-group">
								<div class="input-group date">
									<input type="text" readonly name="endDate"  placeholder="Tanggal Terima Akhir" class="form-control required" >
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<select name="status_siklus" class="form-control">
									<option value="O">Siklus Berjalan</option>
									<option value="C">Tutup Siklus</option>
								</select>
							</div>
							<div class="form-group">
								<span class="btn btn-default btn_cari" onclick="ReportBAPD.showListFarm(this)">Tampilkan</span>
							</div>
						</div>
					</form>
			</div>
		</div>
		<div class="new-line">
			<div id="list_bapdocin">

			</div>
		</div>
</div>

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/penerimaan_docin/reportbapd.js"></script>
