<div class="panel panel-default">
    <div class="panel-heading">Verfikasi Kendaraan Panen</div>
    <div class="panel-body">
        <div class="form-inline new-line">
			<div class="row">
				<div class="col-md-4">
					<div class="checkbox">
                		<label><input type="checkbox" name="do_tindaklanjut" 
							checked="checked"> Filter DO yang belum diterima</label>
            		</div>
				</div>
				<div class="col-md-2">
					<label for="tanggal-panen" class="tanggal-panen-label">Tanggal Panen</label>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<div class="input-group">
							<input type="text" class="form-control" id="tanggal-panen-awal"
								   name="tanggal-panen-awal" placeholder="dd M yy"
								   readonly>
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-1">
					<center><label>s/d</label></center>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<div class="input-group">
							<input type="text" class="form-control" id="tanggal-panen-akhir"
								   name="tanggal-panen-akhir" placeholder="dd M yy"
								   readonly>
							<div class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</div>
						</div>
					</div>
				</div>
				<div>
					<button class="btn btn-default" id="btn-cari" onclick="Verifikasi.cari(this)">Cari</button>
				</div>
			</div>
        </div>
		
        <div id="daftar-do-table" class="new-line">
            <table class="table table-bordered">
                <thead>
                    <tr class="filter">
                        <th><input class="form-control filter" placeholder="cari" type="text" name="tgl_panen" readonly /></th>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="nopol" /></th>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="sopir" /></th>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="kandang" /></th>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="no_do" /></th>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="no_sj" /></th>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <th>Tanggal Panen</th>
                        <th>No. Pol</th>
                        <th>Sopir</th>
                        <th>Kandang</th>
                        <th>No. DO</th>
                        <th>No. SJ</th>
                        <th>Masuk Farm</th>
                        <th>Keluar Farm</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="new-line clear-fix">
                <div class="pull-right pagination">
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="assets/js/security/verifikasi_kendaraan.js"></script>