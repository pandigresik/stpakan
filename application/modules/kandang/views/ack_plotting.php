<?php 
	foreach($data as $siklus => $persiklus){
		foreach($persiklus as $flok => $perflok){
			$keterangan = array();
			$status_ploting = $perflok[0]['STATUS'];
			$user_buat = $perflok[0]['USER_BUAT'];
			$tgl_buat = $perflok[0]['TGL_BUAT'];
			$user_review = $perflok[0]['USER_REVIEW'];
			$tgl_review = $perflok[0]['TGL_REVIEW'];
			$user_ack = $perflok[0]['USER_ACK'];
			$tgl_ack = $perflok[0]['TGL_ACK'];
						
					if(!empty($tgl_buat)){
						if(isset($pegawai_keterangan[$user_buat])){
							array_push($keterangan,'[ '.$pegawai_keterangan[$user_buat]['NAMA_PEGAWAI'].' ] - Dibuat, '.convertElemenTglWaktuIndonesia($tgl_buat));	
						}
							
					}		
				
					if(!empty($tgl_review)){
						if(isset($pegawai_keterangan[$user_review])){
							array_push($keterangan,'[ '.$pegawai_keterangan[$user_review]['NAMA_PEGAWAI'].' ] - Dikoreksi, '.convertElemenTglWaktuIndonesia($tgl_review));	
						}
					}				
				
					if(!empty($tgl_ack)){
						if(isset($pegawai_keterangan[$user_ack])){
							array_push($keterangan,'[ '.$pegawai_keterangan[$user_ack]['NAMA_PEGAWAI'].' ] - Diketahui, '.convertElemenTglWaktuIndonesia($tgl_ack));	
						}
					} 
			
			$rowspan = count($perflok);
			$checkbox = $status_akan_plotting == $perflok[0]['STATUS'] ? '<input class="" data-flok="'.$flok.'" data-siklus="'.$siklus.'" type="checkbox" value="'.$perflok[0]['KODE_SIKLUS'].'" />' : '';
			echo '<tr>';
			echo '<td rowspan="'.$rowspan.'">'.$checkbox.'</td>';
			echo '<td rowspan="'.$rowspan.'">'.$siklus.'</td>';
			echo '<td rowspan="'.$rowspan.'">'.tglIndonesia($perflok[0]['tgl_doc_in'],'-',' ').'</td>';
			echo '<td rowspan="'.$rowspan.'"> Flok'.$flok.'</td>';
			
			$i = 0;
			foreach($perflok as $kandang){
				if($i > 0){
					echo '<tr>';
				} 

				echo '<td> Kandang '.$kandang['kode_kandang'].'</td>';
				echo '<td>'.$kandang['nama_koordinator'].'</td>';
				echo '<td>'.$kandang['nama_pengawas'].'</td>';
				echo '<td>'.$kandang['nama_operator'].'</td>';
				if($i > 0){
					echo '</tr>';
				}else{
					echo '<td rowspan="'.$rowspan.'"><div>'.implode('</div><div>',array_reverse($keterangan)).'</div></td>';
				} 
				$i++;
			}
			echo '</tr>';
		}
	}
?>