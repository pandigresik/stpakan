<table class="table table-bordered">
	<thead>
		<tr>
			<th style="width: 15px">Minggu</th>
			<?php echo '<th>'.implode('</th><th>',$header).'</th>'?>
		</tr>
	</thead>
	<tbody>
		<?php 
		$i = 1;
		do{
			echo '<tr>';
			foreach($header as $index_bulan => $bln){
				echo '<td>'.$i.'</td>';
				if(isset($tbody[$index_bulan][$i])){
					echo '<td>';
					foreach($tbody[$index_bulan][$i] as $perdocin){
				
						echo '<div class="box_approval '.strtolower($perdocin['nama_farm']).'">'.
								 '<div class="header_approval">';
								 if(!empty($perdocin['approve'])){
								 	$cek ='<i class="pull-right glyphicon glyphicon-ok"></i>';
								 }
								 else {				 	
								 	$cek ='<div class="drop_reject label-primary">R</div>'.
								 			'<div class="drag_card label-default" data-no_reg="'.$perdocin['no_reg'].'">C</div>'.
								 			'<div class="drop_approve label-success">A</div>';
								 	if($perdocin['jmlRilis'] > 1){
								 		$cek .= '<i class="pull-right glyphicon glyphicon-remove"></i>';
								 	}
								 	
								 }
									 	
								echo  $cek.
								 	'</div>'.
								 '<div class="body_approval">'.
									 '<div>'.$perdocin['nama_farm'].'</div>'.
									 '<div class="'.strtolower($perdocin['strain']).'">'.$perdocin['strain'].'</div>'.
									 '<div>'.tglIndonesia($perdocin['tgl_doc_in'],'-',' ').', Kd'.$perdocin['kode_kandang'].'</div>'.
									 '<div> Jantan : '.angkaRibuan($perdocin['jantan']).', Betina : '.angkaRibuan($perdocin['betina']).'</div>'.
								 '<div>'.
								 '</div>'.
								 '</div>'.
							 '</div>';
						
					}
					echo '</td>';
				}
				else{
					echo '<td></td>';
				}
				
			}
			$i++;
			echo '</tr>';
		}while($i <= $max_week)
		
		?>
	</tbody>
</table>

<script type="text/javascript" src="assets/js/forecast/approval_presdir.js"></script> 

<style>
.box_approval{
	border : 1px solid green;
	max-width : 15%;
	margin : 2px;
	text-align : center;
}
.box_approval .header_approval{
	background-color : gray;
	padding : 3px;
	height: 30px;
	
}
.header_approval div{
	display : inline;
	margin : 2px;
	padding: 2px;
}
.singosari5{
	background-color : #ffe6e6;
}
.batu {
	background-color : #9ebb82
}
.cobb{
	color : red
}
</style>