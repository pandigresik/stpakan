<!-- Alokasi dan timbang penerimaan pakan -->
<?php //$metodeTimbangan = $lockTimbangan ? 'onfocus="Home.getDataTimbang(this)" readonly' : '' ?>
<style>
	.row_data_pengiriman{background:#95BCF2;}
</style>
<br>
<div class="panel panel-primary">
	<div class="panel-heading">Alokasi dan Penimbangan Pakan</div>	
	<div class="panel-body">
			<div class='row'>
				<div class='col-md-12'>
					<button type='button' class='btn btn-primary' onClick='Penerimaanreturpakanfarm.visualisasi_kavling()'>Visualisasi Kavling</button>
				</div>
			</div>
			<br>
			<div class="row">
			<table class="table table-bordered custom_table" id="table_alokasi" data-kodefarm="<?=$kodefarm?>" data-kodesiklus="<?=$kode_siklus[0]['KODE_SIKLUS']?>">
 			<thead>
 				<tr style="background:#CCC;">
 					<th>Kode Pakan</th>
 					<th>Nama Pakan</th>
 					<th>Bentuk Pakan</th>
 					<th>Jumlah SJ</th>
 					<th>Sak Terima</th>
 					<th>Sak Tolak</th>					
 					<th>Sak Hilang</th>					
 				</tr> 			
 			</thead>
 			<tbody>
				<?php  
					$opt_kandang = '<option selected disabled>pilih kandang</option>';
					foreach($kandang as $kd){
						$opt_kandang .= '<option value="'.$kd['kode_kandang'].'_'.$kd['no_flok'].'_'.$kd['nama_kandang'].'">'.$kd['nama_kandang'].'</option>';
					}
				
					$rowID = 0;
					foreach($detail_pallet as $pallet){
						if($pallet['JML_PICK'] > 0){
							$rowID++;
							$thisKandang = '';
							echo '<tr id="row_penerimaan'.$rowID.'" class="row_data_penerimaan" style="cursor:default;" data-jmlpick="'.$pallet['JML_PICK'].'" data-rowid="'.$rowID.'">
									<td class="kode_pakan">'.$pallet['KODE_BARANG'].'</td>
									<td>'.$pallet['NAMA_BARANG'].'</td>
									<td>'.convertKode('bentuk_barang', $pallet['BENTUK_BARANG']).'</td>
									<td class="jml_pick">'.$pallet['JML_PICK'].'</td>
									<td></td>
									<td></td>
									<td></td>
								</tr>';
							
								/*detail alokasi timbang*/
								echo '<tr><td colspan="7">';
								echo '<table id="tabel_timbang'.$rowID.'" class="table custom_table tabel_timbang" align="right" style="width:92%;">
										<thead>	
											<tr style="background:#CCC;font-size:10pt;">
												<th>No</th>
												<th width="15%">Tanggal<br>Kebutuhan</th>
												<th>Kandang</th>
												<th>Kavling-Pallet</th>
												<th>Berat Pallet<br>(Kg)</th>
												<th width="15%">Berat Timbang<br>(Kg)</th>
												<th>Berat Bersih<br>(Kg)</th>
												<th>Timbangan<br>(Sak)</th>
												<th>Scan<br>Barcode Pallet</th>
											</tr>
										</thead>
										<tbody>
											<tr id="set_alokasi1" class="row_timbang set_alokasi" style="cursor:default;">
												<td>1</td>
												<td class="alokasi_tgl_kebutuhan">
													<div class="input-group date">
														<input type="text" class="form-control parameter input_datepicker" onClick="Penerimaanreturpakanfarm.set_tgl_kebutuhan(this)"/>
														<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
														</span>
													</div>
												</td>
												<td class="alokasi_kandang">
													<select class="form-control" onChange="Penerimaanreturpakanfarm.set_alokasi_kandang(this)" 
														data-farm="'.$kodefarm.'" data-rowid="set_alokasi1" data-siklus="'.$kode_siklus[0]['KODE_SIKLUS'].'" 
														data-kodepakan="'.$pallet['KODE_BARANG'].'" data-table="tabel_timbang'.$rowID.'">
														'.$opt_kandang.'
													</select>
												</td>
												<td class="no_pallet" data-rowid="set_alokasi1"></td>
												<td class="alokasi_berat"></td>
												<td class="alokasi_berat_timbang">
													<input type="text" class="form-control" onCLick="Penerimaanreturpakanfarm.get_data_timbang(this)" data-rowlist="'.$rowID.'" 
													data-table="tabel_timbang'.$rowID.'" data-rowid="set_alokasi1" readonly>
												</td>
												<td class="alokasi_berat_bersih"></td>
												<td class="alokasi_jml_sak_timbang"></td>
												<td class="scan_barcode_status"></td>
											</tr>
										</tbody>
									</table>';
									/*end detail alokasi timbang*/
							echo '</td></tr>';
						}
					}
					$rowID++;
				?>
	 		</tbody>
 		</table>
		</div>
		
		<br><br>
		<div class='row'>
			<center>
				<br><button type='button' id='btn_alokasi_simpan' class='btn btn-primary hide' onClick="Penerimaanreturpakanfarm.simpan()">Simpan</button><br><br>
			</center>
		</div>
		<br>
		
	</div>	
</div>
<br>