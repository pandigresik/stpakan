<div class="section">
	<h2>Dashboard</h2>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title text-center">Acknowlegement</h3>
		</div>
		<div class="panel-body">
			<table class="table">
				<thead>
					<tr>
						<th>No. OP</th>
						<th>No. PP</th>
						<th>Tanggal OP</th>
						<th>Tanggal Kirim</th>
						<th>Nama Farm</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($list_op as $op){
					$tglKirim = $op['tgl_kirim']; 
					$op['tgl_kirim'] = tglIndonesia($op['tgl_kirim'],'-',' ');
					$op['tgl_op'] = tglIndonesia($op['tgl_op'],'-',' ');
					echo '<tr ondblclick="Logistik.buat_do(\''.$tglKirim.'\',\''.$op['no_op'].'\',\''.$op['no_pp'].'\')"><td>'.implode('</td><td>',$op).'</td></tr>';
				}

				?>	
				</tbody>
			</table>					
		</div>
	</div>

</div>

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/home/logistik.js"></script>