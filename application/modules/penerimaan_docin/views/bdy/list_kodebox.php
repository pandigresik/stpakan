			<?php
					if(!empty($boxs)){
						echo '
						<div class="row">
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-5">Farm / Siklus</div>
									<div class="col-md-7">: '.$nama_farm.'/'.$periode_siklus.'</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-5">Total SJ diterima</div>
									<div class="col-md-7">: '.$jml_sj.' Buah</div>
								</div>
							</div>
						
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-5">Jumlah Kandang</div>
									<div class="col-md-7">: '.$jml_kandang.' Kandang</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-5">Total Box diterima</div>
									<div class="col-md-7">: '.$jml_box['jml_box'].' Box</div>
								</div>
							</div>
						
						</div>
						';
						echo '<div class="sticky-table">';
						echo '<table class="table table-striped  custom_table ">';
						echo '<thead>';
							echo '<tr class="sticky-header">';
								echo '<th>Kandang</th>';
								echo '<th>No. SJ</th>';
								echo '<th>Kode Box</th>';
								echo '<th>Jumlah Box</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($boxs as $box){
							echo '<tr>';
								echo '<td>'.substr($box['NO_REG'],-2).'</td>';	
								echo '<td>'.$box['NO_SJ'].'</td>';	
								echo '<td>'.$box['KODE_BOX'].'</td>';	
								echo '<td>'.$box['JML_BOX'].'</td>';	
							echo '<tr>';
						}
						echo '</tbody>';
						echo '</table>';
						echo '</div>';
					}
					else{
						echo '<div>Data tidak ditemukan</div>';
					}
				?>
