<table class="table table-bordered custom_table header-fixed list_pengembalian">
	<thead>
		<tr class="search">
			<th>
				 <div class="right-inner-addon ">
                    <i class="glyphicon glyphicon-search"></i>
                    <input type="search" class="form-control " name="no_pengembalian" placeholder="Search" onchange="Pengembalian.filter_content(this)">
                </div>
			</th>
			<th>
				<div class="right-inner-addon ">
                    <i class="glyphicon glyphicon-search"></i>
                    <input type="search" class="form-control " name="kandang" placeholder="Search" onchange="Pengembalian.filter_content(this)">
                </div>
			</th>
			<th colspan="4"></th>
		</tr>
		<tr>
			<th class="no_pengembalian">No. Retur Pakan</th>
			<th class="kandang">Kandang</th>
			<th class="tanggal">Tanggal Waktu Retur</th>
			<th class="jml_kirim">Jumlah Pakan Rusak</th>
			<th class="jml_pakai">Pengawas Kandang</th>
			<th class="target">Admin Gudang</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(!empty($list_pengembalian)){
			foreach($list_pengembalian as $kembali){
				echo '<tr>
					<td class="no_pengembalian"><span class="link_span" data-no_pengembalian="'.$kembali['NO_PENGEMBALIAN'].'" onclick="Pengembalian.transaksi(this,\'#for_transaksi\')">'.$kembali['NO_PENGEMBALIAN'].'</span></td>
					<td class="kandang">'.$kembali['NAMA_KANDANG'].'</td>
					<td class="tanggal">'.convertElemenTglWaktuIndonesia($kembali['TGL_BUAT']).'</td>
					<td class="number jml_kirim">'.$kembali['JML_KIRIM'].'</td>	
					<td class="number jml_pakai">'.$kembali['JML_PAKAI'].'</td>	
					<td class="number target">'.$kembali['JML_PAKAI'].'</td>
				</tr>';
				
			}
		}
		else{
			echo '<tr><td colspan=6 style="border: none;">Data tidak ditemukan</td></tr>';
		}
	
		 ?>
	</tbody>
	<tfoot>
	</tfoot>
</table>