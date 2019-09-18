<?php
$breadcumb_doc = generateBreadcumb(array('Pengawas' ,'Kepala Farm','Admin Budidaya'));						
$totalBox = 0;
foreach($DkodeBox as $box){
	$totalBox += $box['JML_BOX'];
}
?>
<?php if(!empty($bapdoc)){ ?>
<div class="row col-md-12" style="margin-bottom:15px">
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
<div class="row container">
	<div class="col-md-7 sticky-table">
		<table id="tbl_kodebox" class="table table-bordered custom_table">
			<thead>
				<tr class="bg_biru">
					<td colspan="5"><label>Daftar Kode Box</label></td>
				</tr>
				<tr class="sticky-header">
					<th>No.SJ</th>
					<th>Diverifikasi Oleh</th>
					<th>Tgl Verifikasi</th>
					<th>Kode Box</th>
					<th>Jumlah Box</th>
				</tr>
			</thead>
			<tbody>	
			<?php
				if(!empty($DkodeBox)){ 
					$lastSJ 		= '';
					$sj 			= '';
					$verificator 	= '';
					foreach($DkodeBox as $d_kodebox){
						if($lastSJ != $d_kodebox['NO_SJ'] || $lastSJ == ''){
							$urlImageSJ = $d_kodebox['FOTO'];
							$lastSJ 		= $d_kodebox['NO_SJ'];
							$sj 			= '<td><span class="link_span" onclick="showImage(\''.$urlImageSJ.'\')">'.$d_kodebox['NO_SJ'].'</span></td>';
							$verificator	= '<td>'.$d_kodebox['NAMA_PEGAWAI'].'</td>'; 
						}else{
							$sj 			= '<td style="border:none;border-left:1px solid #cdcdcd;" class=""></td>';
							$verificator 	= '<td style="border:none;border-left:1px solid #cdcdcd;" class=""></td>';
						}
					
						echo '<tr>'
								.$sj
								.$verificator
								.'<td>'.convertElemenTglWaktuIndonesia($d_kodebox['TgL_TERIMA']).'</td>
								<td>'.$d_kodebox['KODE_BOX'].'</td>
								<td>'.$d_kodebox['JML_BOX'].'</td>
							</tr>';	
						}	
					}else{
						echo '<tr><td colspan="5">Data tidak ditemukan</td></tr>';
					}
			?>
			</tbody>
		</table>
	</div>
</div>
<br />
<?php }else{
	echo 'Realisasi DOC tidak ditemukan, karena BAPD belum dientri';
} ?>