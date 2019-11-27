<div class="section">
	<h2>Dashboard</h2>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title text-center">Need Approval</h3>
		</div>
		<div class="panel-body">
			<table class="table">
				<thead>
					<tr>
						<th>Header</th>
						<th>#</th>
						<th>Tanggal</th>
						<th>Nama Farm</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$url = array(
						'Forecast' => 'forecast/forecast/kepalafarm/',
						'Permintaan' => 'permintaan_pakan/permintaan_pakan/kepala_farm/',
				);
				foreach($butuh_approval as $approval){
					$approval['tgl_buat'] = tglIndonesia($approval['tgl_buat'],'-',' ');
					$kode_farm = $approval['kode_farm'];
					unset($approval['kode_farm']);
				//	$url_t = $url[$approval['header']].$kode_farm; 
					echo '<tr data-kode_farm="'.$kode_farm.'" ondblclick="Home.redirect_to_url(this)"><td>'.implode('</td><td>',$approval).'</td></tr>';
				}

				?>	
				</tbody>
			</table>					
		</div>
	</div>

</div>
