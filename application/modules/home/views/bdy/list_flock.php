<div class="section detailflock">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title text-center">Flock</h3>
		</div>
		<div class="panel-body">
		<?php
		foreach($list_flock as $flock){
			echo '<div onclick="'.$action.'" data-flock="'.$flock['flok_bdy'].'" data-noreg="'.$flock['no_reg'].'" data-tglchickin="'.tglIndonesia($flock['tgl_doc_in'],'-',' ').'" data-rhk_terakhir="'.$flock['rhk_terakhir'].'" data-kodestd_j="'.$flock['kode_std_breeding_j'].'"  data-kodestd_b="'.$flock['kode_std_breeding_b'].'" data-kebutuhanawal="" data-kebutuhanakhir="" data-statussiklus="'.$flock['status_siklus'].'" class="pointer alert alert-info div_detailflock">';
			if(!empty($tipe)){
					echo 'Flock '.$flock['flok_bdy'] ;
			}		
			else{
					echo 'flock '.$flock['kode_flock'].' ('.$flock['tipe_flock'].' House,'.convertKode('musim',get_musim($flock['tgl_doc_in'])).')'.' Doc-In '.tglIndonesia($flock['tgl_doc_in'],'-',' ');
			}
			echo '</div>';
		}
		?>

		</div>
	</div>
</div>
