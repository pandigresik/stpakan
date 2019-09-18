<div>
	<div>
		<table class="table table-bordered custom_table">
			<thead>
				<tr>
					<th rowspan="2">Budget Per Siklus (Sak)</th>
					<th colspan="2">Total Permintaan</th>
					<th rowspan="2">Total Sak yang Diambil</th>
					<th rowspan="2">Total Sak Dikembalikan</th>
					<th rowspan="2">Total Sak Terpakai</th>					
					<th rowspan="2">SO/DO (Sak)</th>
					<th rowspan="2">Sisa Stok Tersedia (Sak)</th>
					<th rowspan="2">Verifikasi Pemb. (Sak)</th>
					<th rowspan="2">Surat Jalan (Sak)</th>					
					<th rowspan="2">Sisa Stok (Sak)</th>								
				</tr>
				<tr>
					<th>Realisasi</th>
					<th>Over Budget</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo angkaRibuan($total_budget) ?></td>
					<td><?php echo angkaRibuan($ppsk->minta) ?></td>
					<td><?php echo ($ppsk->minta - $total_budget > 0 ? angkaRibuan($ppsk->minta - $total_budget) : 0) ?></td>
					<td><?php echo angkaRibuan($ppsk->minta) ?></td>
					<td><?php echo angkaRibuan($ppsk->kembali) ?></td>
					<td><?php echo angkaRibuan($ppsk->pakai) ?></td>
					<td><?php echo angkaRibuan($so['so']) ?></td>
					<td><?php echo angkaRibuan($ppsk->pakai - $so['so']) ?></td>
					<td><?php echo angkaRibuan($so['verifikasi_pembayaran']) ?></td>
					<td><?php echo angkaRibuan($so['sj']) ?></td>
					<td><?php echo angkaRibuan($ppsk->pakai - $so['sj']) ?></td>
				</tr>
			</tbody>
		</table>
	</div>	
	<div class="table_paging">
		<div class="btn prev slider-table" data-current="1" data-min="1" data-max="4" onclick="KPPG.prev(this)"> <i class="glyphicon glyphicon-chevron-left"></i> </div>
		<div class="btn next slider-table" data-current="1" data-min="1" data-max="4" onclick="KPPG.next(this)"> <i class="glyphicon glyphicon-chevron-right"></i> </div>
		<table class="table table-bordered custom_table page_1">
			<thead>
				<tr>
					<th >No. Permintaan</th>
					<th >Tanggal Permintaan</th>
					<th >Tanggal Kebutuhan</th>
					<th >Budget Tersedia</th>
					<th >Sak yang Diminta</th>
					<th >Over Budget</th>
					<th >Reviewer</th>
					<th >Tanggal Review</th>
					<th >Approver</th>
					<th >Tanggal Approval</th>					
				</tr>					
			</thead>
			<tbody>
			<?php 
				$budgetTersedia = $total_budget;
				$overBudget = 0;
				$totalPakai = 0;
				for($i = 0; $i < count($detailPpsk) ; $i++){
					echo '<tr>';								
						$dp = $detailPpsk[$i];
						$overBudget = $dp->jml_diminta > $budgetTersedia  ? $dp->jml_diminta - $budgetTersedia : 0;						
						echo '
							<td class="no_ppsk">'.$dp->no_ppsk.'</td>
							<td class="tgl">'.convertElemenTglIndonesia($dp->tgl_permintaan).'</td>
							<td class="tgl">'.convertElemenTglIndonesia($dp->tgl_kebutuhan).'</td>
							<td>'.angkaRibuan($budgetTersedia).'</td>
							<td>'.angkaRibuan($dp->jml_diminta).'</td>						
							<td>'.$overBudget.'</td>
							<td class="pegawai">'.$dp->user_review.'</td>
							<td class="tglwaktu">'.convertElemenTglWaktuIndonesia($dp->tgl_review).'</td>
							<td class="pegawai">'.$dp->user_approve.'</td>
							<td class="tglwaktu">'.convertElemenTglWaktuIndonesia($dp->tgl_approve).'</td>							
						';															
					echo '</tr>';
				}
			?>
			</tbody>
		</table>
		<table class="table table-bordered custom_table page_2"  style="display:none">
			<thead>
				<tr>
					<th >No. Permintaan</th>
					<th >Tanggal Permintaan</th>
					<th >Tanggal Kebutuhan</th>
					<th >Budget Tersedia</th>
					<th >Sak yang Diminta</th>					
					<th >Tanggal Pengambilan</th>
					<th >Sak Diambil</th>
					<th >Tanggal Pengembalian</th>
					<th >Sak Dikembalikan</th>
					<th >Sak Terpakai</th>
					<th >Sisa Budget</th>
					<th >Tanggal Pemusnahan</th>
					<th >Berita Acara Pemusnahan</th>															
				</tr>					
			</thead>
			<tbody>
			<?php 
				$budgetTersedia = $total_budget;
				$overBudget = 0;
				$totalPakai = 0;
				for($i = 0; $i < count($detailPpsk) ; $i++){
					echo '<tr>';								
						$dp = $detailPpsk[$i];
						$overBudget = $dp->jml_diminta > $budgetTersedia  ? $dp->jml_diminta - $budgetTersedia : 0;
						$pakai = $dp->jml_diambil - $dp->jml_kembali;
						$totalPakai += $pakai;
						echo '
							<td class="no_ppsk">'.$dp->no_ppsk.'</td>
							<td class="tgl">'.convertElemenTglIndonesia($dp->tgl_permintaan).'</td>
							<td class="tgl">'.convertElemenTglIndonesia($dp->tgl_kebutuhan).'</td>
							<td>'.angkaRibuan($budgetTersedia).'</td>
							<td>'.angkaRibuan($dp->jml_diminta).'</td>																				
							<td class="tglwaktu">'.convertElemenTglWaktuIndonesia($dp->tgl_terima).'</td>
							<td class="sak_diambil">'.angkaRibuan($dp->jml_diambil).'</td>					
							<td class="tglwaktu"><span class="link_span" data-ppsk="'.$dp->no_ppsk.'" onclick="KPPG.showDetailPengembalian(this)">'.convertElemenTglWaktuIndonesia($dp->tgl_kembali).'</span></td>								
							<td>'.angkaRibuan($dp->jml_kembali).'</td>
							<td>'.angkaRibuan($pakai).'</td>
							<td>'.angkaRibuan($budgetTersedia - $pakai).'</td>
							<td></td>
							<td></td>							
						';
						$budgetTersedia = $budgetTersedia - $pakai;											
					echo '</tr>';
				}
			?>
			</tbody>
		</table>
		<table class="table table-bordered custom_table page_3"  style="display:none">
			<thead>
				<tr>					
					<th rowspan="2">Stok Tersedia</th>
					<th colspan="11">Rencana Penjualan Glangsing</th>								
				</tr>	
				<tr>									
					<th>Tanggal SO/DO</th>
					<th>No. SO/DO</th>
					<th>Pelanggan</th>
					<th>Jumlah Sak</th>
					<th>Harga Jual/Sak (Rp)</th>
					<th>Total Pembayaran (Rp)</th>
					<th>Term Pembayaran</th>
					<th>User Verifikasi</th>
					<th>Tanggal Verifikasi Pembayaran</th>
					<th>Total Transfer (Rp)</th>
					<th>Bukti</th>
				</tr>
			</thead>
			<tbody>
			<?php 				
				for($i = 0; $i < count($detailSO) ; $i++){
					echo '<tr>';								
						$ds = $detailSO[$i];
						if($i == 0){
							echo '<td rowspan="'.count($detailSO).'">'.$totalPakai.'</td>';
						}						
						echo '
							<td class="tgl">'.convertElemenTglIndonesia($ds->tgl_so).'</td>
							<td class="no_so">'.$ds->no_so.'</td>
							<td class="pegawai">'.$ds->nama_pelanggan.'</td>
							<td>'.angkaRibuan($ds->jumlah_total).'</td>
							<td>'.angkaRibuan($ds->harga).'</td>
							<td>'.angkaRibuan($ds->harga_total).'</td>
							<td>'.$ds->term_pembayaran.'</td>
							<td class="pegawai">'.$ds->verifikasi.'</td>
							<td class="tglwaktu">'.convertElemenTglWaktuIndonesia($ds->tgl_verifikasi).'</td>
							<td>'.angkaRibuan($ds->nominal_bayar).'</td>
							<td class="no_so"><a href="'.site_url($ds->lampiran).'" target="_blank"><i class="glyphicon glyphicon-paperclip"></i></a></td>
						';											
					echo '</tr>';
				}
			?>
			</tbody>
		</table>
		<table class="table table-bordered custom_table page_4"  style="display:none">
			<thead>
				<tr>					
					<th rowspan="2">Stok Tersedia</th>
					<th colspan="11">Rencana Penjualan Glangsing</th>								
				</tr>	
				<tr>									
					<th>Tanggal SO/DO</th>
					<th>No. SO/DO</th>
					<th>Pelanggan</th>
					<th>Jumlah Sak</th>
					<th>Harga Jual/Sak (Rp)</th>
					<th>Total Pembayaran (Rp)</th>
					<th>User Ack Verifikasi</th>
					<th>Tanggal Ack Verifikasi Pemb.</th>
					<th>Tanggal Plotting</th>
					<th>No.Pol</th>
					<th>Sopir</th>								
				</tr>
			</thead>
			<tbody>
			<?php 				
				for($i = 0; $i < count($detailSO) ; $i++){
					echo '<tr>';								
						$ds = $detailSO[$i];
						if($i == 0){
							echo '<td rowspan="'.count($detailSO).'">'.$totalPakai.'</td>';
						}						
						echo '
							<td class="tgl">'.convertElemenTglIndonesia($ds->tgl_so).'</td>
							<td class="no_so">'.$ds->no_so.'</td>
							<td class="pegawai">'.$ds->nama_pelanggan.'</td>
							<td>'.angkaRibuan($ds->jumlah_total).'</td>
							<td>'.angkaRibuan($ds->harga).'</td>
							<td>'.angkaRibuan($ds->harga_total).'</td>
							<td class="pegawai">'.$ds->user_ack.'</td>
							<td class="tglwaktu">'.convertElemenTglWaktuIndonesia($ds->tgl_ack).'</td>
							<td class="tglwaktu">'.convertElemenTglWaktuIndonesia($ds->tgl_buat).'</td>
							<td>'.$ds->no_kendaraan.'</td>
							<td class="pegawai">'.$ds->nama_sopir.'</td>							
						';											
					echo '</tr>';
				}
			?>
			</tbody>
		</table>
		<table class="table table-bordered custom_table page_5"  style="display:none">
			<thead>
				<tr>										
					<th colspan="9">Realisasi Penjualan Glangsing</th>					
				</tr>	
				<tr>																			
					<th>No. SO/DO</th>
					<th>Tanggal Verifikasi DO</th>
					<th>User Verifikasi DO</th>
					<th>Tanggal Pengeluaran</th>
					<th>No.SJ</th>
					<th>Realisasi Glangsing</th>
					<th>User Cetak SJ</th>
					<th>User Verifikasi SJ</th>
					<th>Tanggal Verifikasi SJ</th>			
				</tr>
			</thead>
			<tbody>
			<?php 
				$budgetTersedia = $total_budget;
				$overBudget = 0;
				$totalPakai = 0;
				for($i = 0; $i < count($detailSO) ; $i++){
					echo '<tr>';								
						$ds = $detailSO[$i];						
						echo '							
							<td class="no_so">'.$ds->no_do.'</td>
							<td class="tglwaktu">'.convertElemenTglWaktuIndonesia($ds->tgl_verifikasi_do).'</td>
							<td class="pegawai">'.$ds->user_verifikasi_do.'</td>
							<td class="tglwaktu">'.convertElemenTglWaktuIndonesia($ds->tgl_realisasi).'</td>
							<td class="no_so">'.$ds->no_sj.'</td>
							<td>'.angkaRibuan($ds->jumlah).'</td>
							<td class="pegawai">'.$ds->user_realisasi.'</td>
							<td class="pegawai">'.$ds->user_verifikasi_sj.'</td>
							<td class="tglwaktu">'.convertElemenTglWaktuIndonesia($ds->tgl_verifikasi_security).'</td>
						';											
					echo '</tr>';
				}
			?>
			</tbody>
		</table>
	</div>
</div>