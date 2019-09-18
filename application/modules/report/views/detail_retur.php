<table class="table detail_retur">
	<caption>
		<h4>
		<?php echo 'Kandang '. $kandang.' , '.convertElemenTglIndonesia($tgl_transaksi)?>
		</h4>
	</caption>
	<thead>
			<tr>				
				<th colspan="2"></th>
				<th colspan="2"></th>
				<?php if(!empty($header_pakan)){
					foreach($header_pakan as $z => $h){
						echo '<th class="header_pakan" data-kode_pakan="'.$kode_pakan_arr[$z].'">'.$h.'</th>';
					}
	
				}
					
				?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>a</td>
				<td class="text-right">Hutang Awal Sak</td>
				<td colspan="2"></td>
				
				<?php if(!empty($hutang_awal)){
					foreach($hutang_awal as $h){
						echo '<td class="number">'.$h.'</td>';
					}
	
				}
				?>
			</tr>
			<tr>
				<td>b</td>
				<td class="text-right">Retur Sak <?php echo !empty($ada_retur) ? '&nbsp;&nbsp;<span class="glyphicon glyphicon-plus" data-noreg="'.$noreg.'" data-tgl_transaksi="'.$tgl_transaksi.'" onclick="StokPakan.rincian_retur(this,\'rincian\')"></span>' : '' ?></td>
				<td class="tmp_td" style="display:none">No. Retur</td>
				<td class="tmp_td" style="display:none">Jam</td>
				<td class="tmp_td2" colspan="2"></td>
				<?php if(!empty($retur_sak)){
					foreach($retur_sak as $h){
						echo '<td class="number">'.$h.'</td>';
					}
	
				}
				?>
			</tr>
			<tr>
				<td>c</td>
				<td class="text-right">Hutang Retur <div> a - b </div></td>
				<td colspan="2"></td>
				<?php if(!empty($hutang_retur)){
					foreach($hutang_retur as $h){
						echo '<td class="number">'.$h.'</td>';
					}
	
				}
				?>
			</tr>
			<tr>
				<td>d</td>
				<td class="text-right">Pelunasan Hutang (Sak) <?php echo !empty($ada_pelunasan) ? '&nbsp;&nbsp;<span class="glyphicon glyphicon-plus" data-noreg="'.$noreg.'" data-tgl_transaksi="'.$tgl_transaksi.'" onclick="StokPakan.rincian_retur(this,\'pelunasan\')"></span>' : '' ?></td>
				<td class="tmp_td" style="display:none">No. Retur</td>
				<td class="tmp_td" style="display:none">Tanggal / Jam</td>
				<td class="tmp_td2" colspan="2"></td>
				<?php if(!empty($pelunasan_hutang)){
					foreach($pelunasan_hutang as $h){
						echo '<td class="number pelunasan" data-kode_pakan="'.$kode_pakan_arr[$z].'">'.$h.'</td>';
					}
	
				}
				?>
			</tr>
			<tr>
				<td>e</td>
				<td class="text-right">Sisa Hutang Retur (Sak) <div> c - d </div></td>
				<td colspan="2"></td>
				<?php if(!empty($sisa_hutang)){
					foreach($sisa_hutang as $h){
						echo '<td class="number">'.$h.'</td>';
					}
	
				}
				?>
			</tr>
	
		</tbody>
	</table>	



