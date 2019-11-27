<div class="row">
	<div class="form">
		<div class="form-group">
			<label class="control-label col-md-1">Periode Chick-in</label>
			<div class="col-md-8">
				<div class="col-md-3">
					<div class="form-group">
						<div class="input-group date">
							<input type="text" readonly="" name="startDate"	class="form-control" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-1 vcenter">s.d.</div>
				<div class="col-md-3">
					<div class="form-group">
						<div class="input-group date">
							<input type="text" readonly="" name="endDate" class="form-control" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<?php if(isset($show_output) && !$show_output){
				echo '<span class="btn btn-primary" onclick="KertasKerja.showListFarm(this,'.$show_output.')">Tampilkan</span>';
			}else{
				echo '<input type="checkbox" class="input-filter hide" name="tabular" value="tabular" checked="checked">';
				echo '<span class="btn btn-primary" onclick="KertasKerja.showListFarm(this,'.$show_output.')">Tampilkan</span>';
			}
			?>
			</div>
		</div>


	</div>
</div>


<div class="section">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title text-center">Kertas Kerja</h3>
		</div>
		<div class="panel-body">
		<?php if(!empty($list_farm)){
			foreach($list_farm as $farm){
				echo '<div onclick="KertasKerja.showKandang(this)" data-kodefarm="'.$farm['kode_farm'].'" class="pointer alert alert-info">';
				echo 'Farm '.$farm['nama_farm'].' ('.$farm['strain'].','.$farm['jml_kandang'].' Kandang)';
				echo '</div>';
			}
		}
		?>

		</div>
	</div>
</div>
<div id = 'graphdiv2'></div>
<link rel="stylesheet" type="text/css" href="assets/css/home/kertaskerja.css?v=0.1">
<link rel="stylesheet" type="text/css" href="assets/libs/c3/css/c3.min.css">
<link rel="stylesheet" type="text/css" href="assets/libs/ion.rangeSlider/css/ion.rangeSlider.css">
<link rel="stylesheet" type="text/css" href="assets/libs/ion.rangeSlider/css/ion.rangeSlider.skinHTML5.css">

<script type="text/javascript" src="assets/js/jquery.scrollabletable2.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/home/kertaskerja.js"></script>
<script type="text/javascript" src="assets/js/permintaan_pakan/ppHandler.js"></script>
<script type="text/javascript" src="assets/libs/dygraph-combined.js"></script>
