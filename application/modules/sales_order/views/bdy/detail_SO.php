<div class="panel panel-primary">
	<div class="panel-heading">Detail SO</div>
	<div class="panel-body">
		<?php 
			if(!empty($listSO)){
				foreach($listSO as $so){
					echo '<div class="alert alert-success" data-so="'.$so->no_so.'" onclick="laporanStokGlangsing.detailSO(this)">
						'.$so->no_so.'
					</div>';
					echo '<div class="tabel_detail_so" data-so="'.$so->no_so.'">
					
					</div>';		
				}
			}
		?>
		
		
	</div>
</div>

