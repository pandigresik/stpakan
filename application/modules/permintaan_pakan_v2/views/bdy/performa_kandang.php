<?php 
	if(!empty($lhks)) { 
		$barisAwal = $lhks[0];
		$jmlPakan = count($barisAwal['pakai']) - 1;
?>
<div class="table-responsive sticky-table">
<table class="table table-bordered custom_table">
	<thead>
		<tr class="sticky-header">
			<th class="ftl" rowspan="2">Tanggal LHK</th>
			<th class="ftl" rowspan="2">Umur</th>
			<th colspan="<?php echo $jmlPakan ?>">Pemakaian</th>
			<th rowspan="2">Populasi Akhir</th>
			<th rowspan="2">BB rata-rata (gr)</th>
			<th rowspan="2">DH (%)</th>
			<th rowspan="2">FCR</th>
			<th rowspan="2">ADG</th>
			<th rowspan="2">IP</th>
		</tr>
		<tr>
			<?php
			$index = 0; 
			if(!empty($barisAwal['pakai'])){	
				foreach($barisAwal['pakai'] as $_k => $ba){
					if($index){
						echo '<th>'.$_k.'</th>';
					}					
					$index++;
				}
			}	
			?>
		</tr>
	</thead>
	<tbody>
	
		<?php 					
			foreach($lhks as $lhk){
				echo '<tr>';				
				echo '<td class="ftl"><span class="link_span" data-tgl_transaksi="'.$lhk['tgl_transaksi'].'" data-noreg="'.$noreg.'" onclick="Permintaan.show_lhk_bdy(this)">'.convertElemenTglIndonesia($lhk['tgl_transaksi']).'</span></td>';
				echo '<td class="ftl">'.$lhk['umur'].'</td>';
				$index = 0; 
				if(!empty($lhk['pakai'])){		
					foreach($lhk['pakai'] as $_k => $ba){
						if($index){
							echo '<td>'.angkaRibuan($ba).'</td>';
						}					
						$index++;
					}
				}
				echo '<td>'.angkaRibuan($lhk['jumlah']).'</td>';
				echo '<td>'.angkaRibuan($lhk['bb'] * 1000).'</td>';
				echo '<td>'.formatAngka($lhk['dh'],2).'</td>';
				echo '<td>'.formatAngka($lhk['fcr'],3).'</td>';
				echo '<td>'.formatAngka($lhk['adg'],3).'</td>';
				echo '<td>'.formatAngka($lhk['ip'],0).'</td>';
				echo '</tr>';
			}
		?>
	</tbody>		
</table>
</div>
<?php } else { echo 'Belum ada LHK' ;} ?>