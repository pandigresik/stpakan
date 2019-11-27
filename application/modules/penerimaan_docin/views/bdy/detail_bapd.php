<?php
	if(isset($showheader) && $showheader){
		echo '<div class="panel panel-primary">
			<div class="panel-heading">Penerimaan DOC In</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-11">';
	}
?>
			<?php
					if(!empty($sj)){
				echo '<div class="row">';
					echo '<div class="col-md-11">';
						echo '<table class="table table-striped custom_table" data-table="bapdocbox">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>No. SJ</th>';
								echo '<th>Tanggal Penerimaan</th>';
								echo '<th>Jumlah Box</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($sj as $baris){
							echo '<tr>';
								$tglpenerimaan = convertElemenTglWaktuIndonesia($baris['tgl_terima']);
								echo '<td class="sj">'.$baris['no_sj'].'</td>';
								echo '<td class="tgl_terima">'.$tglpenerimaan.'</td>';
								echo '<td class="jmlbox"><span class="col-md-6 text-center jmlbox" onclick="BAPD.show_suratjalan(this)" data-listsj=\''.json_encode($baris['list_sj']).'\'>'.$baris['jmlbox'].'</span></td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '</table>';
					echo '</div>';
					echo '<div class="col-md-1">';
						echo '<span class="btn btn-default lanjut" data-noreg="'.$noreg.'" onclick="BAPD.show_kodebox(this)">Lihat Kode Box</span>';
					echo '</div>';
				echo '</div>';
				if(isset($showheader) && $showheader){
					echo '</div>
					</div>
				</div>
			</div>';
			}
					echo $performance;
						if(isset($riwayat)){
						echo '<div class="div_riwayat" data-noreg="'.$noreg.'">';
							echo '<div  class="text-left"><strong>Riwayat BAP DOC - '.$noreg.'</strong></div>';
							foreach($riwayat as $rw){
								echo '<div class="text-left">['.$rw['nama_pegawai'].'] '.$rw['keterangan'].'- <em>'.convertKode('berita_acara',$rw['status']).', '.convertElemenTglWaktuIndonesia($rw['tgl_buat']).'</em></div>';
							}
						echo '</div>';
						}
					}
					else{
						echo '<div>Data tidak ditemukan</div>';
					}
				?>
