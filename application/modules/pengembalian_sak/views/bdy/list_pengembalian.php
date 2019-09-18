<div class="panel panel-default">
	<div class="panel-heading">Daftar Pengembalian Sak Kosong </div>
	<div class="panel-body">
		<table class="table table-bordered custom_table header-fixed list_pengembalian">
			<thead>
				<tr class="search">
					<th>
						 <div class="right-inner-addon ">
		                    <i class="glyphicon glyphicon-search"></i>
		                    <input type="search" class="form-control " name="tanggal" placeholder="Search" onchange="Pengembalian.filter_content(this)" data-target="no_pengembalian">
		                </div>
					</th>
					<th>
						<div class="right-inner-addon ">
		                    <i class="glyphicon glyphicon-search"></i>
		                    <input type="search" class="form-control " data-target="flock" placeholder="Search" onchange="Pengembalian.filter_content(this)">
		                </div>
					</th>
					<th>
						<div class="right-inner-addon">
							<i class="glyphicon glyphicon-search"></i>
							<input type="search" class="form-control" data-target="kandang" placeholder="Search" onchange="Pengembalian.filter_content(this)">
						</div>
					</th>
					<th></th>
					<th></th>
				</tr>
				<tr>
					<!--<th class="no_pengembalian">No. Pengembalian</th>-->
					<th class="tanggal">Tanggal / Jam Pengembalian</th>
					<th class="flock">Flok</th>
					<th class="kandang">Kandang</th>
					<th class="jenis_pakan">Jenis Pakan</th>
					<th class="jml_aktual">Jumlah Pengembalian<br>(Sak)</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(!empty($list_pengembalian)){					
					$kandang_val = '';
					$flock_val = '';
					$tgl_val = '';
					foreach($list_pengembalian as $kembali){
						if($kandang_val == $kembali['NAMA_KANDANG'] && $tgl_val == convertElemenTglWaktuIndonesia($kembali['TGL_BUAT'])){
							$skandang = 'style="display:none;"';
							$tdkandang = 'style="border:none;border-left:1px solid #dfdfdf;"';
						}else{
							$skandang = '';
							$kandang_val = $kembali['NAMA_KANDANG'];
							$tdkandang = 'style="border:none;border-top:1px solid #dfdfdf;border-left:1px solid #dfdfdf;"';
						}
						
						if($flock_val == $kembali['NO_FLOK'] && $tgl_val == convertElemenTglWaktuIndonesia($kembali['TGL_BUAT'])){
							$sflock = 'style="display:none;"';
							$tdflok = 'style="border:none;border-left:1px solid #dfdfdf;"';
						}else{
							$sflock = '';
							$flock_val = $kembali['NO_FLOK'];
							$tdflok = 'style="border:none;border-top:1px solid #dfdfdf;border-left:1px solid #dfdfdf;"';
						}
						
						if($tgl_val == convertElemenTglWaktuIndonesia($kembali['TGL_BUAT'])){
							$stgl = 'style="display:none;"';
							$tdtgl = 'style="border:none;"';
						}else{
							$stgl = '';
							$tgl_val = convertElemenTglWaktuIndonesia($kembali['TGL_BUAT']);
							$tdtgl = 'style="border:none;border-top:1px solid #dfdfdf;"';
						}
						
						echo '<tr>
								<td class="tanggal" '.$tdtgl.'><div '.$stgl.'>'.convertElemenTglWaktuIndonesia($kembali['TGL_BUAT']).'</div></td>
								<td class="flock" '.$tdflok.'><div '.$sflock.'>'.$kembali['NO_FLOK'].'</div></td>
								<td class="kandang" '.$tdkandang.'><div '.$skandang.'>'.$kembali['NAMA_KANDANG'].'</div></td>
								<td class="jenis_pakan">'.$kembali['NAMA_PAKAN'].'</td>
								<td class="number jml_aktual">'.$kembali['SAK_KEMBALI'].'</td>
							</tr>';
					}
				}
				else{
					echo '<tr><td colspan="4">Data tidak ditemukan</td></tr>';
				}
				?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>
