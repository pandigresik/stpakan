<!--tabel surat jalan-->
<?php //$metodeTimbangan = $lockTimbangan ? 'onfocus="Home.getDataTimbang(this)" readonly' : '' ?>
<div class="panel panel-primary">
	<div class="panel-heading">Surat Jalan</div>	
	<div class="panel-body">	
			<table id="tabeldetailpenerimaan" class="table table-bordered custom_table table-striped">
 			<thead>
 				<tr style="background:#CCC;">
 					<th>No. SJ</th>
 					<th>Farm Asal</th>
 					<th>Target Tanggal Kirim</th>
 					<th>Tanggal Terima</th>
 					<th width="15%">Nopol Terima</th>
 					<th width="10%">Sopir</th>					
 				</tr> 			
 			</thead>
 			<tbody>
				<?php 
					$listKendaraan = '<select class="form-control" id="nopol_datang" onChange="Penerimaanreturpakanfarm.set_btn_verifikasi()">
										<option selected disabled>pilih Kendaraan</option>';
					foreach($list_kendaraan as $nopol){
						$listKendaraan .= '<option>'.$nopol['NOPOL'].'</option>';
					}
					$listKendaraan .= '</select>';
					
					echo '<tr id="tbl_sj" data-nopol="'.$surat_jalan['NOPOL'].'">
							<td class="no_sj">'.$surat_jalan['NO_RETUR'].'</td>
							<td>'.$farm[$surat_jalan['FARM_ASAL']]['nama_farm'].'</td>
							<td>'.$surat_jalan['TGL_KIRIM'].'</td>
							<td>'.$surat_jalan['TGL_TERIMA'].'</td>
							<td class="nopol">'.$listKendaraan.'</td>
							<td class="sopir"><input type="text" class="form-control" id="sopir_datang" onKeyUp="Penerimaanreturpakanfarm.set_huruf(this)" onBlur="Penerimaanreturpakanfarm.set_btn_verifikasi()"></td>
						</tr>';
				?>
	 		</tbody>
 		</table>
		<br>
		<div class='row'>
			<div class='col-md-12' style='text-align:right;'>
				<button type='button' id="btn_verifikasi_sj" class='btn btn-primary' onClick='Penerimaanreturpakanfarm.verifikasi_penerimaan_retur()' disabled>Verifikasi</button>
			</div>
		</div>
		
	</div>	
</div>