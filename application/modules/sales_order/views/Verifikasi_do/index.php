
<div class="col-md-5 col-md-offset-4"> 
	<div class="panel panel-primary">
		<div class="panel-heading">Verifikasi DO</div>
		<div class="panel-body">

			<input type="hidden" name="hkp" value="">

			<div class="form-group row" id="fdo">
				<div class="col-md-6">
					<div class="input-group">
						<input type="text" class="form-control" name="no_do" onkeyup="VerifikasiDO.get_do(this)">
						
					</div>
					<div class="input-group">
						<p>Silahkan Entri No. DO</p>
					</div>
				</div>
				<div class="col-md-6 span-info">
					
				</div>
			</div>


			<div class="form-group row" id="fkendaraan">
				<div class="col-md-6">
					<div class="input-group">
						<input type="text" class="form-control" name="no_kendaraan" onkeyup="VerifikasiDO.get_kendaraan(this)">	
						
					</div>
					<div class="input-group">
						<p>Silahkan Entri No. Kendaraan</p>
					</div>
				</div>
				<div class="col-md-6 span-info">
					
				</div>
			</div>


			<div class="form-group row" id="fpin">
				<div class="col-md-6">
					<div class="input-group">
						<input type="text" class="form-control" name="kode_verifikasi" onkeyup="VerifikasiDO.get_pin(this)">					
					</div>
					<div class="input-group">
						<p>Silahkan Entri No. Pin</p>
					</div>
				</div>
				<div class="col-md-6 span-info">
					
				</div>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript" src="assets/js/verifikasi_do/main.js"></script>