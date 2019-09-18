<div class="div_atas">
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">Mingguan</div>
				<div class="panel-body">		
					<div class="css-treeview" id="div_forecast">
						<?php echo $tree ?>
					</div>
				</div>	
			</div>	
		</div>
		
		<div class="col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading">Kandang sudah tutup siklus</div>
				<div class="panel-body">
					<div id="tutup_siklus">
						<?php echo $kandang_tutup_siklus ?>
					</div>
				</div>	
			</div>	
		</div>
	</div>
	
	<div class="div_bawah">
		<div class="panel panel-default">
			<div class="panel-heading">Detail Rencana DOC-In <span id="TglCheckIn"></span><span id="infoKandang" class="hide"></span></div>
			<div class="panel-body">
				<div class="col-md-12">
					<div class="col-md-2" id="div_tombol_simpan">
						<?php echo $div_tombol_simpan ?>
					</div>
					<div class="col-md-8 col-md-offset-2">
						<h2>Perencanaan Kebutuhan Jenis Pakan</h2>
						<div class="row" id="baris_pertama">
							
						</div>
						<div class="row" id="baris_kedua">
							
						</div>
					</div>
				</div>
				<div class="col-md-12">
					<div class="col-md-6">
						<fieldset>
							<legend>Jantan</legend>
							<div class="" id="pakan_jantan"></div>
							
						</fieldset>
					</div>
					<div class="col-md-6">
						<fieldset>
							<legend>Betina</legend>
							<div class="" id="pakan_betina"></div>
							
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>

<div class="hide" id="canCreateForecast"><?php echo $canCreateForecast ?></div>
<div class="hide" id="lockEditPakan"><?php echo $lockEditPakan ?></div>
<div class="hide" id="lockEditDocIn"><?php echo $lockEditDocIn ?></div>

<!-- sementara tambahkan rand() supaya tidak dicache oleh browser 
<script type="text/javascript" src="assets/js/forecast/forecast.js?<?php rand()?>"></script>
-->
<script type="text/javascript" src="assets/js/forecast/farm.js"></script>	