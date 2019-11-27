<?php
$breadcumb_doc = generateBreadcumb(array('Pengawas' ,'Kepala Farm','Admin Budidaya'));						
$totalBox = 0;
foreach($DkodeBox as $box){
	$totalBox += $box['JML_BOX'];
}
?>
<?php if(!empty($bapdoc)){ ?>
<div class="row" style="margin-bottom:15px;padding:15px">
Tanggal DOC-In : <?=tglIndonesia($tgl_docin,'-',' ')?>
	<table class="table table-bordered custom_table">
		<thead>
			<tr>
				<th colspan="9">BAPD <?php echo $breadcumb_doc ?></th>
			</tr>
			<tr>
				<th rowspan="2">Hatchery</th>
				<th colspan="4">Jumlah Box</th>
				<th rowspan="2">BB Rata<br>(Kg)</th>
				<th rowspan="2">Uniformity<br>(%)</th>
				<th rowspan="2">Status</th>
				<th rowspan="2">Keterangan</th>	
			</tr>
			<tr>
				<th>Box</th>
				<th>Ekor</th>
				<th>Afkir</th>
				<th>Stok Awal</th>
			</tr>
		</thead>
		<tbody>	
			<tr>
				<td><?=$bapdoc->NAMA_HATCHERY?></td>
				<td><?=$totalBox?></td>
				<td><?= angkaRibuan($bapdoc->STOK_AWAL + $bapdoc->JML_AFKIR) ?></td>
				<td><?= angkaRibuan($bapdoc->JML_AFKIR) ?></td>
				<td><?= angkaRibuan($bapdoc->STOK_AWAL) ?></td>
				<td><?= formatAngka(($bapdoc->BB_RATA2/1000),3)?></td>
				<td><?= formatAngka($bapdoc->UNIFORMITY,2)?></td>
				<td><?=convertKode('status_approve', $status->STATUS)?></td>
				<td>
					<?php
						$no = 0;
						foreach($log_approval as $log){
							if($no > 0){echo '<br>';}
							$no++;	
							echo $log['NAMA_PEGAWAI'].' - '
								.convertKode('status_approve', $log['STATUS'])
								.', '.convertElemenTglWaktuIndonesia($log['TGL_BUAT']);
						}
					?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<br />
<div class="row" style="padding:15px">
	<div class="col-md-7 sticky-table">
		<table id="tbl_kodebox" class="table table-bordered custom_table">
			<thead>
				<tr class="bg_biru">
					<td colspan="5"><label>Daftar Kode Box</label></td>
				</tr>
				<tr class="sticky-header">
					<th>No.SJ</th>
					<th>Kode Box</th>
					<th>Jumlah Box</th>
					<th>Masuk Farm</th>
					<th>Masuk Kandang</th>
				</tr>
			</thead>
			<tbody>	
			<?php
				if(!empty($DkodeBox)){ 
					$lastSJ 		= '';
					$sj 			= '';
					$verificator 	= '';
					$terimaKandang  = '';
					foreach($DkodeBox as $d_kodebox){
						if($lastSJ != $d_kodebox['NO_SJ'] || $lastSJ == ''){
							$urlImageSJ = $d_kodebox['FOTO'];
							$lastSJ 		= $d_kodebox['NO_SJ'];
							$sj 			= '<td><span class="link_span" onclick="showImage(\''.$urlImageSJ.'\')">'.$d_kodebox['NO_SJ'].'</span></td>';
							$verificator	= '<td>'.$d_kodebox['NAMA_PEGAWAI'].'<br /> ( SECURITY FARM ) <br />'.convertElemenTglWaktuIndonesia($d_kodebox['TgL_TERIMA']).'</td>'; 
							$terimaKandang  = '<td>'.(!empty($d_kodebox['TERIMA_KANDANG']) ? $d_kodebox['NAMA_PENGAWAS'].'<br /> ( PENGAWAS ) <br />'.convertElemenTglWaktuIndonesia($d_kodebox['TERIMA_KANDANG']) : '').'</td>'; 
						}else{
							$sj 			= '<td style="border:none;border-left:1px solid #cdcdcd;" class=""></td>';
							$verificator 	= '<td style="border:none;border-left:1px solid #cdcdcd;" class=""></td>';
							$terimaKandang 	= '<td style="border:none;border-left:1px solid #cdcdcd;" class=""></td>';
						}
					
						echo '<tr>'
								.$sj.
								'<td>'.$d_kodebox['KODE_BOX'].'</td>
								<td>'.$d_kodebox['JML_BOX'].'</td>'
								.$verificator
								.$terimaKandang
							.'</tr>';	
						}	
					}else{
						echo '<tr><td colspan="5">Data tidak ditemukan</td></tr>';
					}
			?>
			</tbody>
		</table>
	</div>
	<div class="col-md-5 pull-right sticky-table">
		<table id="tbl_timbangdoc" class="table table-bordered custom_table">
			<thead>
				<tr class="bg_biru">
					<td colspan="5"><label>Penimbangan DOC</label></td>
				</tr>
				<tr class="sticky-header">
					<th>Penimbangan Ke-</th>
					<th>Tara Box</th>
					<th>Berat Box (DOC)</th>
					<th>Jumlah Box DOC</th>
					<th>Jumlah Ekor</th>
				</tr>
			</thead>
			<tbody>	
			<?php
				$beratDocRata = 0;
				$totalBerat = 0;
				$totalEkor = 0;
				if(!empty($dtimbangdoc)){ 
					foreach($dtimbangdoc as $d_timbang){
						echo '<tr>
								<td>'.$d_timbang['NO_URUT'].'</td>
								<td>'.formatAngka($d_timbang['TARA_BOX'],2).'</td>
								<td>'.formatAngka($d_timbang['BERAT'],2).'</td>
								<td>'.angkaRibuan($d_timbang['JML_BOX']).'</td>
								<td>'.angkaRibuan($d_timbang['JML_EKOR']).'</td>
							</tr>';	
							$totalBerat += $d_timbang['BERAT'];
							$totalEkor += $d_timbang['JML_EKOR'];
						}
						$beratDocRata = ($totalBerat * 1000) / $totalEkor; 	
					}else{
						echo '<tr><td colspan="5">Data tidak ditemukan</td></tr>';
					}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3">Rata - rata (g)</th>
					<th colspan="2"><?php echo formatAngka($beratDocRata,2) ?> </th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<br />
<?php }else{
	echo 'Realisasi DOC tidak ditemukan, karena BAPD belum dientri';
} ?>