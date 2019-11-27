<div class="row">
 	<div class="container col-md-12">
 		<table class="table table-bordered custom_table" id="tabellistretur">
 			<thead>
 				<tr>
 					<th></th>
 					<th>No. Retur Pakan</th>
 					<th>Farm Tujuan</th>
 					<th>Tanggal Kirim</th>
 					<th>Jumlah Kebutuhan</th> 					
 				</tr> 			
 			</thead>
 			<tbody>
	 		<?php
				if(!empty($returs)){														
					foreach($returs as $r){ 
						$no_pengiriman = empty($r['no_pengiriman']) ? '<span onclick="Returpakanfarm.generate(this)" class="btn btn-default">Generate</span>' : $r['no_pengiriman'];
						echo '<tr ondblclick="Returpakanfarm.timbang(this)" data-timbang="'.(empty($r['jml_kebutuhan']) ? 0 : 1).'">
							<td class="no_pengiriman">'.$no_pengiriman.'</td>							
							<td class="no_retur" data-no_retur="'.$r['no_retur'].'">'.$r['no_retur'].'</td>
							<td class="farm_tujuan">'.$farm[$r['farm_tujuan']]['nama_farm'].'</td>
							<td class="tgl_kirim">'.tglIndonesia($r['tgl_kirim'],'-',' ').'</td>
							<td class="jml_kebutuhan">'.formatAngka($r['jml_kebutuhan'],0).'</td>
						</tr>';
					}
				}
	 		?>
	 		</tbody>
 		</table>
	</div>
</div>
