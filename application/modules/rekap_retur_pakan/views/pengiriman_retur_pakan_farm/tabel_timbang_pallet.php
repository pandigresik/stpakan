<!-- tabel timbang pengiriman pallet -->
<?php error_reporting(E_ALL & ~E_NOTICE); ?>
<?php $metodeTimbangan = $lockTimbangan ? 'onfocus="Home.getDataTimbang(this)" readonly' : '' ?>
<div class="panel panel-primary">
	<div class="panel-heading">Pengiriman Retur Pakan</div>	
	<div class="panel-body">	
		<table id="tabel_list_pallet" class="col-md-12">
			<tbody>
			<?php 
				if(count($detail)>0){
					$kode_pakan = '';
					$dataAwal = 0;
					$bedaBarang = 0;
					$dataTabel = array();
					$dataAkhir = 0;
					$thisKodePallet = '';
					//$thisBeratPallet = 0;
					$rowID = -1;
					$thisOnHand = 0;
					$thisBeratAvailable = 0;
					$arrayPallet = array();
					$numArrayPallet = 0;
					$hide = '';
					$dkavling = '';
					
					foreach($detail as $thisPallet){
						$rowID++;
						$bedaBarang = 0;
						
						if($kode_pakan != $thisPallet['KODE_BARANG']){
							if($dataAwal == 2){
								$dkavling .=  '</tbody>
									</table>
								</td></tr>';
							}
							$bedaBarang = 1;
							$kode_pakan = $thisPallet['KODE_BARANG'];
							$dataAwal = 1;
						}
						
						if($bedaBarang){
							$dkavling .= '<tr class="jenis_pakan" data-pakan="'.$kode_pakan.'">							
								<td colspan="2">
									<b><span class="glyphicon glyphicon-chevron-right" onClick="Pengirimanreturpakanfarm.showDetailTimbang(this)"></span>
									&nbsp;'.$thisPallet['NAMA_BARANG'].'</b>
								</td>
							</tr>';
						}
						if($dataAwal==1){
							$dkavling .= '<tr class="detail_kavling hide">
								<td></td>
								<td>
									<table class="table table-bordered custom_table">
										<thead>
											<tr>
												<th>Kavling-Pallet</th>
												<th>Berat Pallet</th>
												<th width="16%">Berat Timbang<br>(Kg)</th>
												<th>Berat Bersih<br>(Kg)</th>
												<th>Stok Pallet<br>(Sak)</th>
												<th>Jumlah Kirim<br>(Sak)</th>
												<th>Sisa Pallet<br>(Sak)</th>
												<th>Diserahkan<br>Oleh</th>
												<th width="10%">Scan Barcode Pallet</th>
											</tr>
										</thead>
										<tbody>';
								$dataAwal = 2;
							}
								
							if($thisKodePallet != $thisPallet['KODE_PALLET']){
								$thisKodePallet = $thisPallet['KODE_PALLET'];
								//$thisBeratPallet = $thisPallet['BERAT_PALLET'];
								$thisOnHand = $thisPallet['JML_ON_HAND'];
								$thisBeratAvailable = $thisPallet['BERAT_AVAILABLE'];
								$this_berat_putaway = $thisPallet['BERAT_PUTAWAY'];
								$this_jml_putaway = $thisPallet['JML_PUTAWAY'];
							}else{
								//$thisBeratPallet += $thisPallet['BERAT_PALLET'];
								$thisOnHand += $thisPallet['JML_ON_HAND'];
								$thisBeratAvailable += $thisPallet['BERAT_AVAILABLE'];
								$this_berat_putaway += $thisPallet['BERAT_PUTAWAY'];
								$this_jml_putaway += $thisPallet['JML_PUTAWAY'];
							}
							$berat_pallet = $mpallet[$thisKodePallet]['brt_bersih']+$hpallet[0]['BRT_BERSIH'];
							//0 belum | 1 sudah
							switch($kirim){
								case 0: 
								if($thisKodePallet != $detail[$rowID+1]['KODE_PALLET']){
									$rata_rata_kavling = round($this_berat_putaway/$this_jml_putaway);
									$dkavling .= '<tr id="trpallet'.$rowID.'" class="row_kavling '.$hide.'" data-kodepakan="'.$thisPallet['KODE_BARANG'].'">
										<td class="kode_pallet" data-kodepallet="'.$thisKodePallet.'">'.$thisKodePallet.'</td>
										<td class="berat_pallet" data-beratpallet="'.$berat_pallet.'">'.$berat_pallet.'</td>
										<td class="berat_timbang" data-beratavailable="'.$thisBeratAvailable.'">
											<input type="text" class="form-control val_berat_timbang" readonly="true" onClick="Pengirimanreturpakanfarm.get_data_timbang(this)" data-rowid="trpallet'.$rowID.'">
										</td>
										<td class="berat_bersih" data-ratakavling="'.$rata_rata_kavling.'"></td>
										<td class="jml_on_hand" data-onhand="'.$thisOnHand.'"></td>
										<td class="jml_kirim"></td>
										<td class="jml_sisapallet"></td>
										<td class="nama_admin"></td>
										<td class="scan_barcode">
											<input type="text" class="form-control input_barcode hide" onBlur="Pengirimanreturpakanfarm.barcode_pallet_check(this)">
											<center><button class="btn btn-danger btn_reset hide" onClick="Pengirimanreturpakanfarm.reset_hitung_timbang(this)" data-rowID="'.$rowID.'">Reset</button></center>
										</td>
									</tr>';
								}
								break;
								case 1:
								$hitungStok = $thisOnHand + $thisPallet['JML_PICK'];
								$hitungBerat = $thisPallet['BERAT_PICK'] - $thisBeratPallet;
								$dkavling .= '<tr>
									<td>'.$thisKodePallet.'</td>
									<td>'.$berat_pallet.'</td>
									<td>'.$thisPallet['BERAT_PICK'].'</td>
									<td>'.round($hitungBerat, 3).'</td>
									<td>'.$hitungStok.'</td>
									<td>'.$thisPallet['JML_PICK'].'</td>
									<td>'.$thisOnHand.'</td>
									<td>'.$thisPallet['NAMA_PEGAWAI'].'</td>
									<td><center><p class="glyphicon glyphicon-ok" style="color:green;"></p></center></td>
								</tr>';
								break;
							}
					}
					
					
					if(count($detail)>0){
						$dkavling .=  '</tbody>
									</table>
								</td></tr>';
					}
					
					echo $dkavling;
				}
			?>			
			</tbody>
		</table>
		
		<div class="col-md-12">
			<br><br>
			<center>
				<?php
					switch($kirim){
						case 0:
							echo '<button type="button" class="btn btn-primary" onClick="Pengirimanreturpakanfarm.inputSelesai()">Selesai</button>';
						break;
						case 1:
							echo '<button type="button" class="btn btn-primary" onClick="Pengirimanreturpakanfarm.cetakSJretur()">Cetak SJ</button>';
						break;
					}
				?>
			</center>
		</div>
		
	</div>	
</div>