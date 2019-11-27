<div class="section">
	<h2>Dashboard</h2>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title text-center">Acknowlegement</h3>
		</div>
		<div class="panel-body">
			<table class="table table-bordered custom_table">
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
						'Order Pembelian' => 'permintaan_pakan/pembelian_pakan/order/',
				);
				foreach($list_ack as $ack){
					$ack['tgl_buat'] = tglIndonesia($ack['tgl_buat'],'-',' ');
					$kode_farm = $ack['header'] == 'Forecast' ? $ack['kode_farm'] : 1;
					unset($ack['kode_farm']);
					$url_t = $url[$ack['header']].$kode_farm;
					echo '<tr data-kode_farm="'.$kode_farm.'" ondblclick="Home.redirect_to_url(this)"><td>'.implode('</td><td>',$ack).'</td></tr>';						
				}

				?>	
				</tbody>
			</table>					
		</div>
	</div>

</div>

<script type="text/javascript" src="assets/js/home/admin_breeding.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>