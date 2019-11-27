<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );
if (! function_exists ( 'rasioDh' )) {
//	$b_dh,$b_dh_prc,$kk['hari']
	function rasioDh($b_dh,$b_dh_prc,$umur) {
		return $b_dh/(1-(1-$b_dh_prc)*(($umur%7)+1)/7);
	}
}
/* dari no_lpb convert menjadi index warna */
if (! function_exists ( 'getColorIndex' )) {
	function getColorIndex($no_pp,$jml_warna) {
		if(!empty($no_pp)){
			return intval(substr($no_pp, 0,6))%$jml_warna;
		}
		else return null;
	}
}

/* cari jumlah yang harus ditampilkan berdasarkan konversi satuan */
if (! function_exists ( 'konversiSatuan' )) {
	function konversiSatuan($satuan,$nilai) {
		$pengali = array('sak' => 1, 'kg' => 50);
		return $pengali[$satuan] * $nilai;
	}
}

/* buat combobox untuk konversi satuan */
if (! function_exists ( 'dropdownSatuan' )) {
	function dropdownSatuan($name,$target,$nilai) {
		$pengali = array('sak' => 1, 'kg' => 50);
		$s = '<select name="'.$name.'" onchange="KertasKerja.konversiSatuan(this,\''.$target.'\')">';
		foreach($pengali as $id => $val){
			if($id == $nilai){
				$selected = 'selected';
			}
			else{
				$selected = '';
			}
			$s .= '<option data-pengali="'.$val.'" value="'.$id.'" '.$selected.'>'.ucfirst($id).'</option>';
		}
		$s .= '</select>';
		
		return $s;
	}
}

if(! function_exists ( 'hitungADG' )){
	function hitungADG($bb,$umur,$bblalu,$umurlalu){
		return ($bb - $bblalu) / ($umur - $umurlalu);
	}	
}
