<div class='text-center'><h2>Konfirmasi Rencana dan Realisasi Produksi</h2></div>
<div class='col-md-12'>

	<form class="form form-horizontal">

							<div class="form-group">
								<label class="control-label col-md-2">Periode Kirim</label>
								<div class='col-md-8'>
									<div class='col-md-3'>
							            <div class="form-group">
							                <div class="input-group date">
							                    <input type="text" class="form-control parameter" name="startDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>
							                </div>
							            </div>
							        </div>
							    	<div class='col-md-1 vcenter'>s.d.</div>
							        <div class='col-md-3'>
							            <div class="form-group">
							                <div class="input-group date" >
							                    <input type="text" class="form-control parameter" name="endDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>
							                </div>
							            </div>
							        </div>
								</div>
							</div>
		<div class="form-group">
			<label class="control-label col-md-2">Pakan</label>
			<div class='col-md-8'>
				<?php if(!empty($list_pakan)){
					foreach($list_pakan as $lp){
						echo '<div class="checkbox col-md-3">
								<label><input name="kode_pakan" value="'.$lp['kode_barang'].'" type="checkbox" checked />'.$lp['nama_barang'].'</label>
							</div>';
					}

				}else{
					echo '<div class="col-md-2">
								<label>Daftar pakan tidak ditemukan</label>
							</div>';
				}

				?>

			</div>
		</div>
		<div class="form-group">
			<div class='col-md-2 col-md-offset-2'>
			   	<span class="btn btn-default" id="cari_konfirmasi_bdy" onclick="Konfirmasi_rp.cari_bdy(this,'#tabel_konfirmasi_rencana_produksi')">Filter</span>
	        </div>
		</div>
		<div class="form-group">
			<div class='col-md-12' id='chekbok_filter'>
				   	<div class="checkbox col-md-3 col-md-offset-2">
						<label><input value="input_tanggal_produksi" type="checkbox" onclick="Konfirmasi_rp.filterInput(this,'#chekbok_filter')" />Input Estimasi Tanggal Produksi</label>
					</div>
					<div class="checkbox col-md-2">
					    <label><input value="input_rencana_produksi" type="checkbox" onclick="Konfirmasi_rp.filterInput(this,'#chekbok_filter')" />Input Rencana Produksi</label>
					</div>
					<div class="checkbox col-md-2">
					    <label><input value="input_kelolosan_pakan" type="checkbox" onclick="Konfirmasi_rp.filterInput(this,'#chekbok_filter')"/>Input Pakan Lolos QC</label>
					</div>
			</div>
		</div>
	</form>
</div>

<div class="row col-md-12">
	<div id="tabel_konfirmasi_rencana_produksi">
	</div>
	<div id="btnSimpan" class='btn btn-default pull-right' onclick='Konfirmasi_rp.simpan_bdy(this)'>Simpan</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/forecast/konfirmasi.css?v=1" >
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/forecast/forecastHandler.js"></script>
<script type="text/javascript" src="assets/js/forecast/konfirmasi_rencana_produksi.js"></script>
