<table class="table table-bordered custom_table header-fixed list_pengembalian">
	<thead>
		<tr class="search">
			<th>
				 <div class="right-inner-addon ">
                    <i class="glyphicon glyphicon-search"></i>
                    <input type="search" class="form-control " data-target="tgl_buat" placeholder="Search" onchange="Pengembalian.filter_content(this)">
                </div>
			</th>
			<th>
				<div class="right-inner-addon ">
                    <i class="glyphicon glyphicon-search"></i>
                    <input type="search" class="form-control " data-target="flok" placeholder="Search" onchange="Pengembalian.filter_content(this)">
                </div>
			</th>
			<th colspan="6"></th>
			<th>
				<div class="right-inner-addon ">
                    <i class="glyphicon glyphicon-search"></i>
                    <input type="search" class="form-control " data-target="kandang" placeholder="Search" onchange="Pengembalian.filter_content(this)">
                </div>
			</th>
		</tr>
		<tr>
			<th class="no_pengembalian">No. Pengembalian</th>
			<th class="kandang">Kandang</th>
			<th class="tanggal">Tanggal</th>
			<th class="jml_kirim">Jumlah Kirim</th>
			<th class="jml_pakai">Jumlah Pakai</th>
			<th class="target">Target <br >Pengembalian (Sak)</th>
			<th class="jml_aktual">Jumlah Aktual (Sak)</th>
			<th class="outstanding">Outstanding</th>
			<th class="status">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if(!empty($list_pengembalian)){
			foreach($list_pengembalian as $kembali){
				echo '<tr>
					<td class="no_pengembalian"><span class="link_span" data-no_pengembalian="'.$kembali['NO_PENGEMBALIAN'].'" onclick="Pengembalian.transaksi(this,\'#for_transaksi\')">RS/'.$kembali['NO_PENGEMBALIAN'].'</span></td>
					<td class="kandang">'.$kembali['NAMA_KANDANG'].'</td>
					<td class="tanggal">'.convertElemenTglWaktuIndonesia($kembali['TGL_BUAT']).'</td>
					<td class="number jml_kirim">'.$kembali['JML_KIRIM'].'</td>
					<td class="number jml_pakai">'.$kembali['JML_PAKAI'].'</td>
					<td class="number target">'.$kembali['JML_PAKAI'].'</td>
					<td class="number jml_aktual">'.$kembali['AKTUAL'].'</td>
					<td class="number outstanding">'.$kembali['HUTANG'].'</td>
					<td class="status">'.$kembali['STATUS'].'</td>
				</tr>';

			}
		}
		else{
			echo '<tr><td colspan=8>Data tidak ditemukan</td></tr>';
		}

		 ?>
	</tbody>
	<tfoot>
	</tfoot>
</table>
