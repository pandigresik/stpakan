	<table class="table table-bordered custom_table">
		<thead>
			<tr>
				<th>Tanggal Panen</th>
				<th>Status</th>								
			</tr>			
		</thead>
		<tbody>
			<?php 			
				if(!empty($rencanaPanen)){
					foreach($rencanaPanen as $kb => $b){						
						echo '<tr>';
						echo '<td>'.convertElemenTglWaktuIndonesia($b['tgl_panen']).'</td>';												
						echo '<td>'.$b['status'].'</td>';											
						echo '</tr>';
					}
				}else{
					echo '<tr><td colspan="2">Data rencana panen tidak ditemukan</td></tr>';
				}
			?>
		</tbody>		
	</table>
