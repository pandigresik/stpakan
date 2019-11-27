<table cellpadding="3px">	
	<tr>
		<td>
			<table>
				<tr>
					<td width="60%"></td>
					<td width="40%">
						Farm <?php echo $dataFarmAsal->NAMA_FARM.', '.tglIndonesia($suratJalan['PICKED_DATE'],'-',' ') ?>
						<br />
						<table width="100%">
							<tr>
								<td width="20%">Kepada</td>
								<td width="80%">
									<?php echo $dataFarmTujuan->NAMA_FARM ?><br />
									<?php echo $dataFarmTujuan->ALAMAT_FARM.' , '.$dataFarmTujuan->KOTA ?>
								</td>
							</tr>
						</table>						
												
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
					<td width="30%" align="center"><u><strong>Surat Jalan Umum</strong></u><br/>Faktur Menyusul</td>
					<td width="20%" align="center"><?php echo $barcode ?><br /><br /><br /><br /><div><?php echo $suratJalan['NO_REFERENSI'] ?></div></td>
				</tr>
			</table>
		</td>
	</tr>
	<?php 
		$tmpKendaraan = explode('#',$suratJalan['KETERANGAN2']);
		$kendaraan = json_decode($tmpKendaraan[1],1);
	?>
	<tr> 
		<td>
			Bersama ini dengan kendaraan atas sopir <u><strong><?php echo $sopir ?></strong></u> dengan No. Kendaraan : <strong><?php echo $nopol ?></strong>
			kami ada kirim barang - barang tersebut dibawah ini, 			
		</td>
	</tr> 
	<tr>
		<td>
			<table class="garis" width="100%"  align="center" style="border:1px solid black;" cellpadding="8px">
				<thead>
					<tr>
						<th width="50%" style="background-color:gray;border-right:1px solid black">Banyaknya</th>												
						<th width="50%" style="background-color:gray;border-right:1px solid black">Nama Barang</th>						
					</tr>
				</thead>
				<tbody>
				<?php 
					
					if(!empty($detail_sj)){
						foreach($detail_sj as $d){
							echo '<tr>
								<td style="border:1px solid black">'.angkaRibuan($d['JML_PICK']).'</td>								
								<td width="50%" style="border:1px solid black">'.$d['NAMA_PAKAN'].'</td>						
							</tr>';
							
						}
					}
				?>
				</tbody>				
			</table>
			
		</td>
	</tr>
	<br />
	
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td align="center" width="33%">Penerima,</td>
					<td align="center" width="33%">Sopir,</td>
					<td align="center" width="33%">Pengirim,</td>
				</tr>
				<br /><br />
				<br /><br />
				<tr>
					<td align="center" width="33%">(...............................)</td>
					<td align="center" width="33%">(...............................)</td>
					<td align="center" width="33%">(...............................)</td>
				</tr>
					
			</table>
		</td>	
	</tr>
</table>
<style>
	table.garis{
		
	}
	table.garis th,table.garis td{
		border: 1px solid black;
		border-collapse: collapse;
	}
	
</style>