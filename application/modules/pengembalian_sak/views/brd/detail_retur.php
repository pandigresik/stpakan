<div class="col-md-7">
<table class="table table-bordered custom_table">
	<thead>
		<tr>
			<th>Nama Pakan</th>
			<th>Jenis Kelamin</th>
			<th>Jumlah Retur (Sak)</th>
			<th>Berat Sak (Gr)</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			if(!empty($list_retur)){
				foreach($list_retur as $kp => $retur){
					foreach($retur as $jk => $item){
						echo '<tr>';
						echo '<td>'.$item['nama_pakan'].'</td>';
						echo '<td class="text-center">'.$jk.'</td>';
						echo '<td class="number">'.$item['jml_retur'].'</td>';
						echo '<td class="number">'.$item['brt_sak'].'</td>';
						echo '</tr>';	
					}
					
				}
			}
		?>
	</tbody>
</table>
</div>
<?php if(empty($keputusan)){ ?>
<div class="col-md-5">
	<div onclick="Approval.approveretursak(this,'A')"><span class="btn btn-default">Approve</span></div>
	<div onclick="Approval.approveretursak(this,'R')" class="new-line"><span class="btn btn-danger">Reject</span></div>
	
</div>
<?php } ?>