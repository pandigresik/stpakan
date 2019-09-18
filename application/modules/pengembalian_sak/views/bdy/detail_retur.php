<div class="col-md-7">
<table class="table table-bordered custom_table">
	<thead>
		<tr>
			<th>Nama Pakan</th>			
			<th>Jumlah Retur (Sak)</th>
			<th>Berat Sak (Gr)</th>
			<th>Keterangan</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			if(!empty($list_retur)){
				foreach($list_retur as $kp => $retur){
					foreach($retur as $jk => $item){
						$keterangan = empty($item['keterangan']) ? '<input type="text" name="keterangan" />' : $item['keterangan']; 
						echo '<tr data-retur_sak_kosong_item_pakan="'.$item['retur_sak_kosong_item_pakan'].'">';
						echo '<td>'.$item['nama_pakan'].'</td>';						
						echo '<td class="number">'.$item['jml_retur'].'</td>';
						echo '<td class="number">'.$item['brt_sak'].'</td>';
						echo '<td class="number">'.$keterangan.'</td>';
						echo '</tr>';	
					}
					
				}
			}
		?>
	</tbody>
</table>
</div>
<?php if(empty($keputusan)){ 
	if(!empty($reviewkadept)){
		if($approve){
		echo '<div class="col-md-5">
				<div onclick="Approval.approveretursak(this,\'A\')"><span class="btn btn-default">Approve</span></div>
				<div onclick="Approval.approveretursak(this,\'R\')" class="new-line"><span class="btn btn-danger">Reject</span></div>
			</div>';
		}
	}
	else{
		echo '<div class="col-md-5">
				<div onclick="Approval.reviewretursak(this)"><span class="btn btn-default">Review</span></div>
				<div onclick="Approval.cancelreview(this)" class="new-line"><span class="btn btn-danger">Cancel</span></div>
			</div>';	
	}
}	
?>

