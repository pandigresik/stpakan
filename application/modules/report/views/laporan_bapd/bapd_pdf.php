<!--layout PDF BAPD-->
<style>
	table{font-size:8.5pt;line-height:8.6pt;}
	.center{text-align:center;}
	.bordered_bottom{border-bottom:1px solid #000;}
	.layout_title{line-height:20pt;}
	.layout_header{line-height:15pt;}
	
	.tbl_layout{
		border:1px solid #000;
		padding:5px 20px;
	}
	
	.tbl_bordered{
		border:1px solid #000;
		padding:3px 0;	
	}
	.tbl_bordered .tbl_title{
		background-color:#cfcfcf;
		border-top:1px solid #000;
	}
	.tbl_bordered tr th,
		.tbl_bordered tr td{
		border-left:1px solid #000;
		border-right:1px solid #000;
	}	
	
	.row_opening{
		margin:0;
		padding:0;
	}
	.row_notes{line-height:20pt;font-size:9pt;}
	.row_footnotes{
		padding:0;
		margin:0;
		line-height:10pt;
		font-size:9pt;
	}
	.row_sign{
		font-size:8pt;
		line-height:6pt;
	}
</style>
<?php
	$en_day 		= array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
	$id_day 		= array('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
	$day 			= date('D', strtotime($tgl_docin));
	$hari_docin		= str_replace($en_day, $id_day, $day);
	//$day 			= date('D', strtotime($mengetahui[0]->TGL_BUAT));
	//$hari_approve	= str_replace($en_day, $id_day, $day);
?>

<table class="tbl_layout">	
	<tr class="layout_title">
		<td class="center" colspan="2">
			<h3><u>Berita Acara Penerimaan DOC-In</u></h3>
		</td>
	</tr>
	
	<tr class="layout_header">
		<td>
			<p>Nama Farm : <?=$nama_farm?>/Kandang <?=$no_kandang?></p>
		</td>
		<td>
			<p>No.Reg : <?=$noreg?></p>
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<p class="row_opening">
				Dengan hormat,<br>
				Pada hari ini, 
					<?=$hari_docin//$hari_approve?> 
					<?=convertElemenTglIndonesia($tgl_docin/*$mengetahui[0]->TGL_BUAT*/)?> 
					telah diterima DOC pedaging dengan rincian sebagai berikut :
			</p>
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<table class="tbl_bordered" width="100%" align="center">
				<thead>
					<tr>
						<th class="tbl_title" width="16%">No.SJ</th>
						<th class="tbl_title" width="18%">Jam Tiba</th>
						<th class="tbl_title" width="10%">Jumlah Box</th>
						<th class="tbl_title" width="10%">Strain</th>
						<th class="tbl_title" width="46%">Keterangan</th>
					</tr>
				</thead>
				<tbody>
					<?php 	
						$sj = array();
						$lastsj = '';
						$jmlBox = 0;
						$tgl = array();
						$len = 0;
						$thisJml = '';
						$thisStrain = '';
						$thisKet = '';
						
						foreach($dataSJ as $dsj){
							if($lastsj == ''){
								$lastsj = $dsj['NO_SJ'];
								$exp_sj = explode('/', $lastsj);
								$sj[$len] = $exp_sj[0].'/'.$exp_sj[1];
								$tgl[$len] = $dsj['TGL_TERIMA'];
							}
							if($lastsj != $dsj['NO_SJ']){
								$len++;
								$lastsj = $dsj['NO_SJ'];
								$exp_sj = explode('/', $lastsj);
								$sj[$len] = $exp_sj[0].'/'.$exp_sj[1];
								$tgl[$len] = $dsj['TGL_TERIMA'];
							}
							$jmlBox += $dsj['JML_BOX'];
						}
						for($i=0;$i<count($sj);$i++){
							$thisJml = '';
							$thisStrain = '';
							$thisKet = '';
							if($i==0){
								$thisJml = $jmlBox;
								$thisStrain = $strain;
								$thisKet = 'DOC dari '.$hatchery;
							}
							$jam_tiba = convertElemenTglWaktuIndonesia($tgl[$i]);
							echo '<tr valign="top">
									<td><p>'.$sj[$i].'</p></td>
									<td><p>'.$jam_tiba.'</p></td>
									<td><p>'.$thisJml.'</p></td>
									<td><p>'.$thisStrain.'</p></td>
									<td><p>'.$thisKet.'</p></td>
								</tr>';
						}
					?>	
				</tbody>
			</table>
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			Berdasarkan perhitungan ulang di kandang - kandang, jumlah dan perfomance DOC yang diterima sebagai berikut :
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<table class="tbl_bordered" width="100%" align="center" style="font-size:8.1pt;line-height:8.4pt;">
				<thead>
				<tr>
					<th class="tbl_title" rowspan="2" width="13%">No.SJ</th>
					<th class="tbl_title bordered_bottom" colspan="5" width="42%">Jumlah DOC</th>
					<th class="tbl_title" rowspan="2" width="8%">BB</th>
					<th class="tbl_title" rowspan="2" width="12%">Jumlah<br>Seharusnya</th>
					<th class="tbl_title" rowspan="2" width="13%">Uniformity</th>
					<th class="tbl_title bordered_bottom" colspan="2" width="12%">Selisih</th>
				</tr>
				<tr>
					<th class="tbl_title" width="5%">Box</th>
					<th class="tbl_title" width="8%">Ekor</th>
					<th class="tbl_title" width="8%">Mati Box</th>
					<th class="tbl_title" width="13%">DOC&lt;36 gram</th>
					<th class="tbl_title" width="8%">Stok Awal</th>
					<th class="tbl_title" width="6%">ekor</th>
					<th class="tbl_title" width="6%">%</th>
				</tr>
				</thead>
				<tbody>
					<?php
						for($i=0;$i<count($sj);$i++){
							$thisJml = '';
							$thisEkor = '';
							$thisMati = '';
							$thisAfkir = '';
							$thisStokAwal = '';
							$thisBB = '';
							$thisUniformity = '';
							$thisSelisihJml = '';
							$thisSelisihPersen = '';
							if($i==0){
								$thisJml = $jmlBox;
								$thisEkor = $jmlBox*102;
								$thisMati = '0';
								$thisAfkir = $afkir;
								$thisStokAwal = $thisEkor - $thisAfkir;
								$thisBB = $bb;
								$thisUniformity = $uniformity;
								$hitungAfkirStokAwal = $thisStokAwal + $afkir;
								//$thisSelisihJml = $thisEkor - $hitungAfkirStokAwal;
								//$thisSelisihPersen = round(($thisSelisihJml/$thisEkor)*100);
								$thisSelisihJml='0'; 
								$thisSelisihPersen='0';
							}
							
							echo '<tr>
								<td><p>'.$sj[$i].'</p></td>
								<td><p>'.$thisJml.'</p></td>
								<td><p>'.angkaRibuan($thisEkor).'</p></td>
								<td><p>'.$thisMati.'</p></td>
								<td><p>'.$thisAfkir.'</p></td>
								<td><p>'.angkaRibuan($thisStokAwal).'</p></td>
								<td><p>'.str_replace('.', ',', $thisBB).'</p></td>
								<td><p>'.angkaRibuan($thisEkor).'</p></td>
								<td><p>'.str_replace('.', ',', $thisUniformity).'</p></td>
								<td><p>'.$thisSelisihJml.'</p></td>
								<td><p>'.$thisSelisihPersen.'</p></td>
							</tr>';
						}
					?>
				</tbody>
			</table><br>
			<b class="row_notes">Data mentah penimbangan dan perhitungan sampling DOC terlampir</b>
			<p class="row_footnotes">Demikian berita acara ini dibuat sebenar - benarnya dan sesuai dengan kenyataan yang ada.</p>
		</td>
	</tr>
	
	<tr>
		<td colspan="2">
			<table class="row_sign" width="100%" align="center"> 
				<tr>
					<td class="center" width="33%">
						Saksi,<br><br>
						<?php
							if(count($saksi)>0){
								echo '<img width="55px" src="assets/images/approved.png"><br><br>';
								echo '('.$saksi[0]->NAMA_PEGAWAI.')';
							}
						?>
					</td>
					<td class="center" width="33%">
						Penghitung,<br><br>
						<?php
							if(count($penghitung)>0){
								echo '<img width="55px" src="assets/images/approved.png"><br><br>';
								echo '('.$penghitung[0]->NAMA_PEGAWAI.')';
							}
						?>
					</td>
					<td class="center" width="33%">
						Mengetahui,<br><br>
						<?php
							if(count($mengetahui)>0){
								echo '<img width="55px" src="assets/images/approved.png"><br><br>';
								echo '('.$mengetahui[0]->NAMA_PEGAWAI.')';
							}
						?>
					</td>
				</tr>
			</table>
			<br><br>
		</td>
	</tr>
</table>