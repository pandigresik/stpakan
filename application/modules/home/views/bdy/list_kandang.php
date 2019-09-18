<div class="section detailkandang">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title text-center">Kandang</h3>
		</div>
		<div class="panel-body">
		<?php
		$arrflock = array();
		$arrfl = array();
		$arrkd = array();
		$str_frm = '';
		$str_kd = '';
		$str_fl = '';
		$flock = '';
		$loopRow = 0;
		foreach($list_kandang as $kandang){

			$loopRow ++;
			$str_kd = '<div onclick="'.$action.'" data-tglchickin="'.tglIndonesia($kandang['tgl_doc_in'],'-',' ').'" data-rhk_terakhir="'.$kandang['rhk_terakhir'].'" data-noreg="'.$kandang['no_reg'].'" data-kodestd_j="'.$kandang['kode_std_breeding_j'].'"  data-kodestd_b="'.$kandang['kode_std_breeding_b'].'" data-kebutuhanawal="" data-kebutuhanakhir="" data-statussiklus="'.$kandang['status_siklus'].'" class="pointer alert alert-info div_detailkandang">';
			if(!empty($tipe)){
					$str_kd .= 'Kandang '.$kandang['kode_kandang'] ;
			}		
			else{
					$str_kd .= 'Kandang '.$kandang['kode_kandang'].' ('.$kandang['tipe_kandang'].' House,'.convertKode('musim',get_musim($kandang['tgl_doc_in'])).')'.' Doc-In '.tglIndonesia($kandang['tgl_doc_in'],'-',' ');
			}
			$str_kd .= '</div>';

			if($flock != $kandang['flok_bdy']){
				$flock = $kandang['flok_bdy'];
				$arrflock[] = $flock;

				$str_fl = '<div onclick="Rhk.showDetailLsam(this,\'lsam_flock\')" data-flock="'.$kandang['flok_bdy'].'" data-farm="'.$kandang['kode_farm'].'" data-periode="'.$kandang['periode_siklus'].'" data-tglchickin="'.tglIndonesia($kandang['tgl_doc_in'],'-',' ').'" data-rhk_terakhir="'.$kandang['rhk_terakhir'].'" data-kodestd_j="'.$kandang['kode_std_breeding_j'].'"  data-kodestd_b="'.$kandang['kode_std_breeding_b'].'" data-kebutuhanawal="" data-kebutuhanakhir="" data-statussiklus="'.$kandang['status_siklus'].'" class="pointer alert alert-info div_detailflock" style="background-color:#ffffcc;">';
				if(!empty($tipe)){
						$str_fl .= '-- FLOCK '.$kandang['flok_bdy'] ;
				}		
				else{
						$str_fl .= '-- FLOCK '.$kandang['kode_flock'].' ('.convertKode('musim',get_musim($kandang['tgl_doc_in'])).')'.' Doc-In '.tglIndonesia($kandang['tgl_doc_in'],'-',' ');
				}
				$str_fl .= '</div>';
				$arrfl[$flock] = $str_fl;
			}

			if(count($list_kandang) <= $loopRow){
				//$str_frm = '<div onclick="Rhk.showDetailLsam(this,\'lsam_farm\')" data-farm="'.$kandang['kode_farm'].'" data-periode="'.$kandang['periode_siklus'].'" data-tglchickin="'.tglIndonesia($kandang['tgl_doc_in'],'-',' ').'" data-rhk_terakhir="'.$kandang['rhk_terakhir'].'" data-kodestd_j="'.$kandang['kode_std_breeding_j'].'"  data-kodestd_b="'.$kandang['kode_std_breeding_b'].'" data-kebutuhanawal="" data-kebutuhanakhir="" data-statussiklus="'.$kandang['status_siklus'].'" class="pointer alert alert-info div_detailflock" style="background-color:#ffffff;">';
				if(!empty($tipe)){
					//	$str_frm .= '---- FARM '.$kandang['nama_farm'] ;
				}		
				else{
				//		$str_frm .= '---- FARM '.$kandang['nama_farm'].' ('.convertKode('musim',get_musim($kandang['tgl_doc_in'])).')'.' Doc-In '.tglIndonesia($kandang['tgl_doc_in'],'-',' ');
				}
				//$str_frm .= '</div>';
			}


			$arrkd[$flock][] = $str_kd;
		}

		for($i=0; $i<count($arrflock); $i++){
			$flock = $arrflock[$i];

			for($j=0; $j<count($arrkd[$flock]); $j++){
				echo $arrkd[$flock][$j];
			}

			if($tipe == 'lsam'){
				echo $arrfl[$flock];	
			}
			
		}
		echo $str_frm;
		

		?>

		</div>
	</div>
</div>
