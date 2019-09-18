<div class="section detailkandang">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title text-center">Kandang</h3>
		</div>
		<div class="panel-body">
		<?php  
		foreach($list_kandang as $kandang){
			echo '<div onclick="KertasKerja.showKertasKerja(this,'.$grafik.')" data-tglchickin="'.tglIndonesia($kandang['tgl_doc_in'],'-',' ').'" data-rhk_terakhir="'.$kandang['rhk_terakhir'].'" data-noreg="'.$kandang['no_reg'].'" data-kodestd_j="'.$kandang['kode_std_breeding_j'].'"  data-kodestd_b="'.$kandang['kode_std_breeding_b'].'" data-kebutuhanawal="" data-kebutuhanakhir="" data-statussiklus="'.$kandang['status_siklus'].'" class="pointer alert alert-info">';
			echo 'Kandang '.$kandang['kode_kandang'].' ('.$kandang['tipe_kandang'].' House,'.convertKode('musim',get_musim($kandang['tgl_doc_in'])).')'.' Doc-In '.tglIndonesia($kandang['tgl_doc_in'],'-',' ');
			echo '</div>';			
		}
		?>	
							
		</div>
	</div>
</div>

		