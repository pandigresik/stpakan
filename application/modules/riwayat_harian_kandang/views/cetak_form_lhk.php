<div class="panel panel-default">
	<div class="panel-heading">
		Cetak Form LHK
		<div class="pull-right">
            <label>Scan ID Operator</label> <input type="text" id="id_kandang" onchange="CetakLHK.pilih_kandang(this)" />
        </div> 	
	</div>
	<div class="panel-body">
		<div class="col-md-12">
			<center><h1><?php echo 'FARM '.$nama_farm;?></h1></center>
			<hr>
			<center><h3><?php echo 'Cetak Form LHK ';?></h3></center>
			<div class="row">
				<div class="panel">
					<div>
						<table id="tb_rekap" class="table table-condensed table-striped table-bordered custom_table">
							<thead style="background-color:#F0F0F0">
								<tr>
									<th>Tanggal LHK</th>
									<th>Flock</th>
									<th>Kandang</th>
									<th>Tanggal DOC-In</th>
									<th>Umur (Hari)</th>
									<th>Cetak Form LHK</th>
								</tr>
							</thead>
							<tbody>
								<?php echo $list_view_tblcetak_form_lhk; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<link type="text/css" href="assets/libs/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />

<script type="text/javascript" src="assets/js/riwayat_harian_kandang/cetak_form_lhk.js"></script>
<script type="text/javascript" src="assets/js/rekap_retur_pakan/jquery-barcode.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/moment.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.redirect.js"></script>