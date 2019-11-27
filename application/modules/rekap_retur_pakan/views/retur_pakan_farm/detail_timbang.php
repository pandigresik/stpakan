<?php $metodeTimbangan = $lockTimbangan ? 'onfocus="Home.getDataTimbang(this)" readonly' : '' ?>
<div class="panel panel-primary">
	<div class="panel-heading">Pengiriman Retur Pakan</div>	
	<div class="panel-body">			
		<table class="col-md-12">
			<tbody>
			<?php 
		
				if(!empty($detail)){										
					foreach($detail as $d){
						echo '<tr>							
							<td colspan="2"><span class="glyphicon glyphicon-chevron-right" onclick="Returpakanfarm.showDetailTimbang(this)"></span>&nbsp;'.$d[0]['NAMA_BARANG'].'</td>
						</tr>';
						
						echo '<tr class="detail_kavling hide">
								<td></td>
								<td>
									<table class="table table-bordered custom_table">
										<thead>
											<tr>
												<th>Kavling-Pallet</th>
												<th>Berat Pallet</th>
												<th>Berat Timbang (Kg)</th>
												<th>Berat Bersih (Kg)</th>
												<th>Stok Pallet (Sak)</th>
												<th>Jumlah Kirim (Sak)</th>
												<th>Keterangan</th>
												<th>Sisa Pallet (Sak)</th>
												<th></th>
											</tr>
										</thead>
										<tbody>';
										$sudahTimbang = 0;
										$btnSimpan = '<span class="btn btn-default" onclick="Returpakanfarm.simpanTimbang(this)">Simpan</span>';																				
										foreach($d as $_d){
											$totalBeratPallet = $_d['BERAT_HAND_PALLET'] + $_d['BERAT_PALLET'];
											$jumlahKirim = empty($_d['JML_PICK']) ? $_d['JML_ON_PICK'] : $_d['JML_PICK'];											
											$beratBersih =  '' ;
											$jumlahSak = '';
											$jumlahKirimStr = '';
											$sisaSak = ''; 
											$keterangan = '';
											$inputTimbang = '<input type="text" name="berat-timbang" class="form-control berat-timbang text-center" onblur="Returpakanfarm.kontrol_timbangan(this)" '.$metodeTimbangan.' >';
											if(!$sudahTimbang){
												if(!empty($_d['JML_PICK'])){
													$sudahTimbang = 1;
												}
											}											
											if($sudahTimbang){
												$beratBersih =  $_d['BERAT_PICK'] ;
												$jumlahSak = $_d['JML_AKTUAL'] + $jumlahKirim;
												$jumlahKirimStr = $jumlahKirim;
												$sisaSak = $jumlahSak - $jumlahKirim; 
												$keterangan = 'Selesai';
												$inputTimbang = number_format((( $beratBersih / $jumlahKirim ) * $jumlahSak) + $totalBeratPallet,3);
											}
											
											echo '<tr data-no-referensi="'.$_d['NO_REFERENSI'].'">
												<td class="kode_pallet" data-no-pallet="'.$_d['NO_PALLET'].'" data-berat-pallet="'.$totalBeratPallet.'" data-jml-sak="'.$_d['JML_AKTUAL'].'">'.$_d['KODE_PALLET'].'</td>
												<td class="berat-pallet">'.$totalBeratPallet.'</td>
												<td class="berat-timbang">'.$inputTimbang.'</td>
                                                <td class="berat-bersih">'.$beratBersih.'</td>
                                                <td class="jumlah-sak">'.$jumlahSak.'</td>
												<td class="jumlah-kirim" data-jumlah-kirim="'.$jumlahKirim.'">'.$jumlahKirimStr.'</td>
												<td class="keterangan">'.$keterangan.'</td>
												<td class="sisa-sak">'.$sisaSak.'</td>
												<td class="reset hide"><span class="btn btn-default" onclick="Returpakanfarm.resetTimbang(this)">Reset</span></td>
											</tr>';											
										}
						echo			'</tbody>
									</table>
								</td>
							</tr>';
						if($sudahTimbang){
							$btnSimpan = '<span class="btn btn-default" data-no_referensi="'.$d[0]['NO_REFERENSI'].'" onclick="Returpakanfarm.cetakSJRetur(this)">Cetak SJ Retur</span>';										
						}
					}
				}
			?>
			</tbody>
		</table>
		<br />
		<div class="row col-md-12 text-center">
			<?php echo $btnSimpan ?>
		</div>
	</div>		
	
</div>