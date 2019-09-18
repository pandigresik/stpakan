<?php

 				echo '<div class="div_riwayat" data-noreg="'.$noreg.'">';
							echo '<div  class="text-left"><strong>Riwayat BAP DOC - '.$noreg.'</strong></div>';
							foreach($riwayat as $rw){
								echo '<div class="text-left">['.$rw['nama_pegawai'].'] '.$rw['keterangan'].'- <em>'.convertKode('berita_acara',$rw['status']).', '.convertElemenTglWaktuIndonesia($rw['tgl_buat']).'</em></div>';
							}
						echo '</div>';
				?>
