<div class='text-center'><h2>Konfirmasi Pemenuhan Permintaan</h2></div>
<div class='form form-inline'>
	<div class='row'>
	
		<div class='col-md-2'>
			<label>Penampilan data permintaan</label>
		</div>
		<div class='col-md-10'>
			<div class='col-md-3'>
				<div class='form-group'>
					<select name='status_realisasi' class='form-control'>
						<option value=''>Semuanya</option>
						<option value='C'>Sudah Realisasi</option>
						<option value='I' selected>Belum Realisasi</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='col-md-2'>
			<label>Tanggal Pengambilan</label>
		</div>
		<div class='col-md-10'>
			<div class='col-md-3'>
				<div class='form-group'>
					<div class='input-group date'>
						<input type='text' class='form-control parameter' name='startDate' readonly />
						<span class='input-group-addon'>
							<span class='glyphicon glyphicon-calendar'></span>
						</span>
					</div>
				</div>
			</div>
			<div class='col-md-1 vcenter'>s.d.</div>    
			<div class='col-md-5'>
				<div class='form-group'>
					<div class='input-group date' >
						<input type='text' class='form-control parameter' name='endDate' readonly />
						<span class='input-group-addon'>
							<span class='glyphicon glyphicon-calendar'></span>
					    </span>	
					</div>
				</div>
				<div class='btn btn-default' id="cari_konfirmasi" onclick='Konfirmasi_rp.cari(this,"#tabel_konfirmasi_rencana_produksi")'>Cari</div>	
			</div>
			
			<div class='btn btn-default pull-right' onclick='Konfirmasi_rp.simpan(this)'>Simpan</div>									
		</div>
	</div>
</div>
<br >
<div id="tabel_konfirmasi_rencana_produksi">
</div>

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/forecast/forecastHandler.js"></script>
<script type="text/javascript" src="assets/js/forecast/konfirmasi_rencana_produksi.js"></script>