<?php 
	if(!empty($pplama)){
		$jmlPakan = count(array_keys($pplama[0])) - 1;
?>
	<table class="table table-bordered custom_table">
		<thead>
			<tr>
				<th rowspan="2">Tanggal Kebutuhan</th>
				<th colspan="<?php echo $jmlPakan ?>">Keb. PP telah diajukan</th>								
			</tr>
			<tr>
				<?php
				if(!empty($pplama)){
					$i = 0;	 
					foreach($pplama[0] as $np => $r){
						if($i){
							echo '<th>'.$np.'</th>'; 
						}					
											
						$i++;
					}
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php 			
				if(!empty($pplama)){
					foreach($pplama as $kb => $b){						
						echo '<tr>';
							$i = 0;
							foreach($b as $_b){
								if(!$i){
									echo '<td>'.convertElemenTglIndonesia($_b).'</td>';
								}else{
									echo '<td>'.angkaRibuan($_b).'</td>';
								}
								$i++;
							}							
						echo '</tr>';
					}
				}
			?>
		</tbody>		
	</table>
<?php 
	}else{
		echo 'Belum ada PP yang dibuat';
	}
?>	