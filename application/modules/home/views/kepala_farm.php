<div class="section">
	<h2>Dashboard</h2>
	<div class="panel panel-default">
		
		<div class="panel-body">
			<!-- 
			<div class="text-center"><h2>Notifikasi Analisa Performance Kandang</h2></div>
			<div class="analisa_kandang">
				<?php /* echo $grafik */ ?>
			</div>
			 -->
			<div class="text-center"><h2>Notifikasi Permintaan Pakan Ke Feedmill</h2></div>
			<div class="analisa_pp">
				<?php echo $grafik_pp ?>
			</div>
			<div class="legend">
				<div class="row col-md-12">
					<div class="col-md-4">
						<div class="legend">
							<div class="box-legend rhk"></div><label>Permintaan Pakan</label><br >
							<div class="box-legend sudah_input_rhk"></div><label>Sudah Input LHK</label><br >
							<div class="box-legend belum_input_rhk"></div><label>Belum Input LHK</label><br >
						</div>
					</div>
					<div class="col-md-4">
						<div class="legend">
							<div class="box-legend tgl_kebutuhan"></div><label>Tgl Kebutuhan</label><br >
							<div class="box-legend sudah_input_rhk"></div><label>Hari ini (Sudah Input LHK)</label><br >
							<div class="box-legend belum_input_rhk"></div><label>Hari ini (Belum Input LHK)</label><br >
						</div>
					</div>
					<div class="col-md-4">
						<div class="legend">
							<div class="box-legend tgl_kirim"></div><label>Tgl. Pengiriman</label><br >
							<div class="box-legend akhir_permintaan"></div><label>Batas Akhir Permintaan Pakan</label><br >
							<div class="box-legend akhir_analisa_performance"></div><label>Batas Akhir Analisa Performance Kandang</label><br >
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
<?php 
echo '<div id="data-notif" class="hide">';
	if(isset($notif)){
		echo json_encode($notif);
	}
echo '</div>';
?>	

<link rel="stylesheet" type="text/css" href="assets/libs/gantti/css/gantti.css" >
<link rel="stylesheet" type="text/css" href="assets/css/home/gantti_custom.css">
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/home/kepalafarm.js"></script>

