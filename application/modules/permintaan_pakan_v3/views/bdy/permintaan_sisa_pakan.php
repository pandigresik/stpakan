<table class="table table-bordered custom_table">
	<thead>
		<tr>
			<th>Jenis Pakan</th>
			<th>Budget Pakan <br /> (Sak)</th>
			<th>Akumulasi Kuantitas PP <br /> (Sak)</th>
			<th>Sisa Budget Pakan <br /> (Sak)</th>
		</tr>
	</thead>
	<tbody>
		<?php 			
			if(!empty($budget)){
				foreach($budget as $kb => $b){
					$kuantitas_pp = isset($info_pp[$kb]) ? $info_pp[$kb]['kuantitas'] : 0;
					echo '<tr>
						<td>'.$b['nama_barang'].'</td>
						<td>'.angkaRibuan($b['budget']).'</td>
						<td>'.angkaRibuan($kuantitas_pp).'</td>
						<td>'.angkaRibuan($b['budget'] - $kuantitas_pp).'</td>
					</tr>';
				}
			}
		?>
	</tbody>		
</table>