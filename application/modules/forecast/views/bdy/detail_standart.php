<div class="table-responsive">
<table class="table table-bordered custom_table">
		<thead>
			<tr>
				<th>Standart</th>
				<th>Nilai</th>
			</tr>
		</thead>
	<tbody>
		<?php foreach($list as $i => $val){ ?>
		<tr>
			
			<?php 
				foreach($val as $k => $v){
					echo '<tr>
					<td>'.$k.'</td>
					<td class="number">'.( ($k != 'Umur Panen') ? formatAngka($v,3) : $v) .'</td>
				</tr>';	
				}	
				 
			?>
		</tr>
		<?php } ?>		
	</tbody>
</table>
</div>