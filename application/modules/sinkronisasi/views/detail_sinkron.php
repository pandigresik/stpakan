<table class="table table-bordered custom_table">
	<thead>
			<tr>
				<th>Aksi</th>
				<th>Tabel</th>
				<th>Kunci</th>
			</tr>
		</thead>
		<tbody>
		<?php 
		if(!empty($data)){
			foreach($data as $l){
				$kunci = json_decode($l['kunci'],1);
				$kunci_arr = array();
				foreach($kunci as $k => $v){
					array_push($kunci_arr,'<span class="label label-success">'.$k.' = '.$v.'</span>');
				}
				echo '<tr>';
				echo '<td>'.$l['aksi'].'</td>';
				echo '<td>'.$l['tabel'].'</td>';
				echo '<td>'.implode(' &nbsp; ',$kunci_arr).'</td>';
				echo '</tr>';
			}
		}else{
			echo '<tr><td colspan="3">Data tidak ditemukan</td></tr>';
		}
		?>
		</tbody>
	</table>	



