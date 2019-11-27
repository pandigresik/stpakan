<div class="div_atas">
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">Tanggal Rencana DOC-In</div>
				<div class="panel-body">
					<div class="left-inner-addon col-md-10">
            <i class="glyphicon glyphicon-search"></i>
            <input type="search" onchange="Forecast.filter_content(this)" placeholder="Kandang" class="form-control">
          </div>
					<div class="css-treeview" id="div_forecast">
						<?php echo $tree ?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-8">
			<div class="panel panel-default">
				<div class="panel-heading">Detail Kandang</div>
				<div class="panel-body">
					<div id="info_detail_kandang">

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
							<div class="table-responsive" id="pakan_jantan"></div>

						</fieldset>
					</div>

				</div>
			</div>
		</div>
	</div>

<div class="hide" id="lockEditDocIn"><?php echo $lockEditDocIn ?></div>
<div class="hide" id="minTglDocInStandartBaru"><?php echo $minTglDocInStandartBaru ?></div>

<link href="assets/css/forecast/konfirmasi.css" type="text/css" rel="stylesheet">
<!-- sementara tambahkan rand() supaya tidak dicache oleh browser
<script type="text/javascript" src="assets/js/forecast/forecast.js?<?php rand()?>"></script>
-->
