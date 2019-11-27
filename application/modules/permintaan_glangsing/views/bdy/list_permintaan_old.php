<div class="panel panel-primary">
	<div class="panel-heading">Daftar Permintaan Sak </div>
	<div class="panel-body">
		<!--
		<table class="table table-bordered custom_table list_permintaan">
		-->
		<table class="table table-bordered custom_table">
			<thead>
				<tr>
						<th rowspan="2">No. Permintaan Sak</th>
						<th rowspan="2" id="th_keterangan">
							Keterangan
						</th>
						<th rowspan="2">Jumlah Sak</th>
						<th colspan="2">Over Budget</th>

						<th rowspan="2">Penerima Sak</th>
						<th rowspan="2">Status</th>
						<th rowspan="2">Tgl Rilis</th>
						<th colspan="3">Tindak Lanjut</th>

					</tr>
					<tr>
						<th>Jumlah</th>
						<th>Alasan</th>
						<th>Kadept Pemeliharaan Internal</th>
						<th>Kadept Admin Budidaya</th>
						<th>Kadiv Budidaya</th>
					</tr>
				<!-- <tr>
					<th>No. Permintaan Sak</th>
					<th id="th_keterangan">
						Keterangan
						<span class="caret btn-column-filter" style="cursor:pointer;"></span>
					</th>
					<th>Jumlah Sak</th>
					<th>Penerima Sak</th>
					<th>Status</th>
					<th>Tgl Rilis</th>
					<th>Tgl Ack</th>
					<th>Tgl Approve</th>
				</tr> -->
			</thead>
			<tbody>
				<?php
				if(!empty($list_permintaan)){
					foreach($list_permintaan as $minta){
						echo '<tr >
							<td><span class="link_span" data-kode_budget="'.$minta['KODE_BUDGET'].'" data-status="'.$minta['STATUS'].'" data-no_ppsk="'.$minta['NO_PPSK'].'" data-kode_farm="'.$minta['KODE_FARM'].'" onclick="permintaanSak.editView(this)">'.$minta['NO_PPSK'].'</span></td>
							<td class="Keterangan">'.$minta['KETERANGAN'].'</td>
							<td class="number">'.$minta['JML_SAK'].'</td>
							<td>'.$minta['JML_OVER'].'</td>
							<td>'.$minta['ALASAN_OVER'].'</td>
							<td>'.$minta['NAMA_PEGAWAI'].'</td>
							<td id="stt_ppsk">'.$minta['STATUS_DESC'].'</td>
							<td>'.convertElemenTglWaktuIndonesia($minta['TGL_RILIS']).'</td>
							<td>'.(($minta['STATUS'] != 'N') ? convertElemenTglWaktuIndonesia($minta['TGL_ACK']) : "").'</td>
							<td>'.((($minta['STATUS'] == 'A' || $minta['STATUS'] == 'RJ') && ($minta['GRUP_PEGAWAI'] == 'WKDV' || $minta['GRUP_PEGAWAI'] == 'KDV' || $minta['GRUP_PEGAWAI'] == 'WKBA')) ? convertElemenTglWaktuIndonesia($minta['TGL_APPROVE']) : "").'</td>
							<td>'.((($minta['STATUS'] == 'A' || $minta['STATUS'] == 'RJ') && $minta['GRUP_PEGAWAI'] == 'KDV') ? convertElemenTglWaktuIndonesia($minta['TGL_APPROVE_KADIV']) : "").'</td>
						</tr>';
					}
				}
				else{
					echo '<tr><td colspan=11>Data tidak ditemukan</td></tr>';
				}
				 ?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>
