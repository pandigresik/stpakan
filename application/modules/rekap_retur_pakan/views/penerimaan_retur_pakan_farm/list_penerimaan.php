<!--table penerimaan-->
<div class="row">
	<div class="container col-md-12">
		<table class="table table-bordered custom_table table-striped" id="tabellistpenerimaan">
			<thead>
				<tr style="background:#CCC;">
					<th>No. SJ</th>
					<th>Farm Asal</th>
					<th>Target Tanggal Kirim</th>
					<th>Tanggal Terima</th>
					<th>Jam Terima</th>
					<th>Penerima</th>
					<th>No.BA</th>					
				</tr> 			
			</thead>
			<tbody>
			<?php 
				if(!empty($terima)){ 
					foreach($terima as $data){
						echo '<tr ondblclick="Penerimaanreturpakanfarm.show_detail_penerimaan(this)" data-noretur="'.$data['NO_RETUR'].'">
								<td>'.$data['NO_RETUR'].'</td>
								<td>'.$farm[$data['FARM_ASAL']]['nama_farm'].'</td>
								<td>'.$data['TGL_KIRIM'].'</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>';
					}
				}
			?>
			</tbody>
		</table>
	</div>
</div>