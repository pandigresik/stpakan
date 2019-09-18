<div class="row">
<div class="col-md-4 col-md-offset-4 text-center"><h3>RIWAYAT HARIAN KANDANG</h3></div>
<div class="col-md-4 col-md-offset-4 text-center"  style="margin-top:-25px"><h3><?php echo !empty($nama_farm) ? strtoupper($nama_farm) : '' ?></h3></div>
</div>
<div>
	<form class="form form-horizontal" role="form">
		<div class="form-group">
			<label class="control-label col-md-2">Kandang</label>
			<div class="col-md-2">
				<input type="text" class="form-control" name="kandang">
				<input type="hidden" class="form-control" name="tgldocin">
			</div>
			<div class="col-md-4 col-md-offset-3">
				<label class="control-label col-md-4">Populasi Awal</label>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-2">Flock</label>
			<div class="col-md-2">
				<select class="form-control" name="flock" onchange="Rhk.setDatepicker(this)">
					<option value="">Pilih flock</option>
				</select>
			</div>
			<div class="col-md-4 col-md-offset-3">
				<label class="control-label col-md-4">Jantan</label>
				<div class="col-md-3">
					<input type="text" class="number form-control" name="j_jantan" readonly>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-md-2">Tanggal LHK</label>
			<div class='col-md-5'>
									<div class='col-md-4'>
							            <div class="form-group">
							                <div class="input-group date">
							                    <input type="text" class="form-control parameter" name="startDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>
							                </div>
							            </div>
							        </div>
							    	<div class='col-md-1 vcenter'>s.d.</div>    
							        <div class='col-md-4'>
							            <div class="form-group">
							                <div class="input-group date" >
							                    <input type="text" class="form-control parameter" name="endDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>	
							                </div>
							            </div>
							        </div>
								</div>
			<div class="col-md-4">
				<label class="control-label col-md-4">Betina</label>
				<div class="col-md-3">
					<input type="text" class="number form-control" name="j_betina" readonly>
				</div>
			</div>					
		</div>
		<div class="form-group">
			<div class="col-md-3 col-md-offset-2">
				<div class="btn btn-default" id="tampilkan_btn" onclick="Rhk.list_cari(this)">Tampilkan</div>
			</div>
		</div>

		
	</form>
	<div class="row col-md-12 new-line">
		<div id="detail_rhk" style="margin-left: 40px">
			
		</div>
	</div>
</div>

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/report/rhk.js"></script>

<link rel="stylesheet" type="text/css" href="assets/css/report/stok_pakan.css" >

