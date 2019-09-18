<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

	/**
	 * Outputs an array in a user-readable JSON format
	 *
	 * @param array $array
	 */
	if ( ! function_exists('display_json'))
	{
	    function display_json($array)
	    {
	        $data = json_indent($array);

	        header('Cache-Control: no-cache, must-revalidate');
	        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	        header('Content-type: application/json');

	        echo $data;
	    }
	}


	/**
	 * Convert an array to a user-readable JSON string
	 *
	 * @param array $array - The original array to convert to JSON
	 * @return string - Friendly formatted JSON string
	 */
	if ( ! function_exists('json_indent'))
	{
	    function json_indent($array = array())
	    {
	        // make sure array is provided
	        if (empty($array))
	            return NULL;

	        //Encode the string
	        $json = json_encode($array);

	        $result        = '';
	        $pos           = 0;
	        $str_len       = strlen($json);
	        $indent_str    = '  ';
	        $new_line      = "\n";
	        $prev_char     = '';
	        $out_of_quotes = true;

	        for ($i = 0; $i <= $str_len; $i++)
	        {
	            // grab the next character in the string
	            $char = substr($json, $i, 1);

	            // are we inside a quoted string?
	            if ($char == '"' && $prev_char != '\\')
	            {
	                $out_of_quotes = !$out_of_quotes;
	            }
	            // if this character is the end of an element, output a new line and indent the next line
	            elseif (($char == '}' OR $char == ']') && $out_of_quotes)
	            {
	                $result .= $new_line;
	                $pos--;

	                for ($j = 0; $j < $pos; $j++)
	                {
	                    $result .= $indent_str;
	                }
	            }

	            // add the character to the result string
	            $result .= $char;

	            // if the last character was the beginning of an element, output a new line and indent the next line
	            if (($char == ',' OR $char == '{' OR $char == '[') && $out_of_quotes)
	            {
	                $result .= $new_line;

	                if ($char == '{' OR $char == '[')
	                {
	                    $pos++;
	                }

	                for ($j = 0; $j < $pos; $j++)
	                {
	                    $result .= $indent_str;
	                }
	            }

	            $prev_char = $char;
	        }

	        // return result
	        return $result . $new_line;
	    }
	}

if (! function_exists ( 'create_tree' )) {
	function create_tree($arr = array()) {
		if (! empty ( $arr )) {
			$tmp = '<ul>';
			foreach ( $arr as $i => $val ) {
				if (! is_array ( $val )) {
					$tmp .= '<li><a href="#">' . $val . '</a></li>';
				} else {
					$rand = rand ( 0, 1000 );
					$tmp .= '<li><input type="checkbox" id="' . $rand . '" /><label for="' . $rand . '">' . $i . '</label>';
					$tmp .= create_tree ( $val );
				}
			}
			$tmp .= '</ul>';
			return $tmp;
		}
	}
}
/* hanya untuk tabel sederhana, tanpa rowspan dan colspan */
if (! function_exists ( 'create_table_div' )) {
	function create_table_div($header = array(), $arr = array()) {
		$table = '';
		$thead = '';
		$tbody = '';
		if (! empty ( $header )) {
			/* cari jumlah field */
			$countColumn = count ( $header );
			$colClass = 'col-md-' . floor ( 12 / $countColumn );
			$thead .= '<div class="row"><div class="' . $colClass . '">' . implode ( '</div><div class="' . $colClass . '">', $header ) . '</div></div>';
		}
		if (! empty ( $arr )) {
			$tbody .= '<div class="contentTable">';
			foreach ( $arr as $row ) {
				/* cari jumlah field */
				$countColumn = count ( $row );
				$colClass = 'col-md-' . floor ( 12 / $countColumn );
				$tbody .= '<div class="row"><div class="' . $colClass . '">' . implode ( '</div><div class="' . $colClass . '">', $row ) . '</div></div>';
			}
			$tbody .= '</div>';
		}
		$table = $thead . $tbody;
		return $table;
	}
}
if (! function_exists ( 'convert_ke_bulan' )) {
	function convert_ke_bulan($idbulan) {
		$shortName = array (
				'Jan',
				'Feb',
				'Mar',
				'Apr',
				'Mei',
				'Jun',
				'Jul',
				'Ags',
				'Sep',
				'Okt',
				'Nov',
				'Des'
		);
		return $shortName [$idbulan - 1];
	}
}
if (! function_exists ( 'mssql_escape' )) {
	function mssql_escape($data) {
        if(is_numeric($data))
          return $data;
        $unpacked = unpack('H*hex', $data);
        return '0x' . $unpacked['hex'];
    }
}
if (! function_exists ( 'convert_month' )) {
	function convert_month($date, $to) { /* $to = 1:indonesia, 2:english */
		$explode = explode ( ' ', $date );
		if (isset ( $explode [0] ) && isset ( $explode [2] ) && isset ( $explode [2] )) {
			$day = $explode [0];
			$month = $explode [1];
			$year = $explode [2];
			$month_in = array (
					'Januari',
					'Februari',
					'Maret',
					'April',
					'Mei',
					'Juni',
					'Juli',
					'Agustus',
					'September',
					'Oktober',
					'November',
					'Desember'
			);
			$short_month_in = array (
					'Jan',
					'Feb',
					'Mar',
					'Apr',
					'Mei',
					'Jun',
					'Jul',
					'Ags',
					'Sep',
					'Okt',
					'Nov',
					'Des'
			);
			$month_en = array (
					'Jan',
					'Feb',
					'Mar',
					'Apr',
					'May',
					'Jun',
					'Jul',
					'Aug',
					'Sep',
					'Oct',
					'Nov',
					'Dec'
			);
			if ($to == 2) {
				$new_month = array_search ( $month, $short_month_in );
				$month = $month_en [$new_month];
			} else {
				$month = $short_month_in [$month - 1];
			}
			return $day . ' ' . $month . ' ' . $year;
		}
	}
}

if (! function_exists ( 'tglIndonesia' )) {
	function tglIndonesia($tgldb, $separator_asal = '-', $separator_tujuan = '-') {
		if(empty($tgldb)){
			return null;
		}
		/* $tgldb formatnya 2015-05-29 , rubah menjadi 29-Mei-2015 */
		/* cek apakah mengandung jam atau detik, panjang max = 10 karakter */
		$tgldb = substr ( $tgldb, 0, 10 );

		$tgl = explode ( $separator_asal, $tgldb );
		$newTgl = array (
				$tgl[2],
				convert_ke_bulan( $tgl[1] ),
				$tgl[0]
		);
		return implode ( $separator_tujuan, $newTgl );
	}
}



if (! function_exists ( 'angkaRibuan' )) {
	function angkaRibuan($angka) {
		return number_format ( $angka, 0, '', '.' );
	}
}

if (! function_exists ( 'WeekSequenceInMonth' )) {
	function WeekSequenceInMonth($szDate) {
		$omega = new \DateTime ( $szDate );
		$alpha = new \DateTime ();
		$alpha->setDate ( ( int ) $omega->format ( 'Y' ), ( int ) $omega->format ( 'm' ), 1 );
		$delta = (( int ) $alpha->format ( 'N' )) - 1;

		return ( int ) (ceil ( (( float ) $omega->format ( 'j' ) + $delta) / 7 ));
	}
}

if (! function_exists ( 'convertKode' )) {
	function convertKode($context, $kode) {
		$arr = array (
				'tipe_lantai' => array (
						'S' => 'Slate'
				),
				'tipe_kandang' => array (
						'O' => 'Open',
						'C' => 'Closed'
				),
				'bentuk_barang' => array (
						'T' => 'Tepung',
						'C' => 'Crumble',
						'P' => 'Pellet'
				),
				'status_approve' => array (
						'D' => 'Draft',
						'N' => 'New',
						'A' => 'Approve',
						'C' => 'Complete',
						'V' => 'Void',
						'RV' => 'Review',
						'R' => 'Review',
						'R1' => 'Review',
						'RJ' => 'Reject'
				),
				'berita_acara' => array (
						'D' => 'Draft',
						'N' => 'Rilis',
						'A' => 'Approve',
						'C' => 'Complete',
						'V' => 'Void',
						'RV' => 'Review',
						'RJ' => 'Reject'
				),
				'musim' => array(
						'I' => 'In-Session',
						'O' => 'Out-Session',
				),
				'jenis_kelamin' => array(
					'J' => 'Jantan',
					'B' => 'Betina'
				)
		);
		$result = (! empty ( $arr [$context] [$kode] )) ? $arr [$context] [$kode] : $kode;
		return $result;
	}
}

if (! function_exists ( 'simpleGrouping' )) {
	/**
	 * If you want change array structures to grouping array,
	 * within [your_key_to_group] from array element is your choice..
	 *
	 * @return Array.
	 */
	function simpleGrouping($array, $key_to_group) {
		$groups = array ();
		if(!empty($array)){
			foreach ( $array as $item ) {
				$key = $item [$key_to_group];
	
				if (! isset ( $groups [$key] )) {
					$groups [$key] = array ();
				} 
				$groups [$key] [] = $item;
			}
		}
		
		return $groups;
	}
}

if (! function_exists ( 'get_musim' )) {
	function get_musim($tglDocIn) {
		$_t = explode ( '-', $tglDocIn );
		$_insesion = [
				3,
				4,
				5,
				6,
				7,
				8
		];
		if (in_array ($_t[1], $_insesion )) {
			return 'I';
		} else
			return 'O';
	}
}

if (! function_exists('convertElemenTglIndonesia')){
	function convertElemenTglIndonesia($a){
		return tglIndonesia($a,'-',' ');
	}
}

if (! function_exists('convertElemenTglWaktuIndonesia')){
	function convertElemenTglWaktuIndonesia($a,$detik = false){
		$tgl = null;
		if(!empty($a)){
			/* cek apakah mengandung waktu */
			$t = explode(' ', $a);
			$tgl = tglIndonesia($t[0],'-',' ');
			if(!empty($t[1])){
				if(!$detik){
					$t[1] = substr($t[1], 0, 5);
				}
				$tgl .= ' '.$t[1];
			}
		}

		return $tgl;
	}
}
if (! function_exists('pecahLaluConvertElemenTglWaktuIndonesia')){
	function pecahLaluConvertElemenTglWaktuIndonesia($elm,$detik = false){
		$t = explode(' s/d ',$elm);
		return convertElemenTglIndonesia($t[0]). ' s/d ' .convertElemenTglIndonesia($t[1]);
	}
}

if (! function_exists('addAttr')){
	function addAttr($a){
		$a = explode('*kt*',$a);
		if(!empty($a[1])){
			return '<span class="has-tooltip">'.$a[0].'<span class="tooltip">'.$a[1].'</span></span>';
		}
		else{
			return '<span>'.$a[0].'</span>';
		}

	}
}

if (! function_exists ( 'formatAngka' )) {
	function formatAngka($angka,$decimal) {
		if(!is_null($angka)) return number_format ( (double)$angka, $decimal, ',', '.' );

		return null;

	}
}


//////////////////////////////////////////////////////////////////////
//PARA: Date Should In YYYY-MM-DD Format
//RESULT FORMAT:
// '%y Year %m Month %d Day %h Hours %i Minute %s Seconds'        =>  1 Year 3 Month 14 Day 11 Hours 49 Minute 36 Seconds
// '%y Year %m Month %d Day'                                    =>  1 Year 3 Month 14 Days
// '%m Month %d Day'                                            =>  3 Month 14 Day
// '%d Day %h Hours'                                            =>  14 Day 11 Hours
// '%d Day'                                                        =>  14 Days
// '%h Hours %i Minute %s Seconds'                                =>  11 Hours 49 Minute 36 Seconds
// '%i Minute %s Seconds'                                        =>  49 Minute 36 Seconds
// '%h Hours                                                    =>  11 Hours
// '%a Days                                                        =>  468 Days
//////////////////////////////////////////////////////////////////////
if (! function_exists ( 'dateDifference' )) {
	function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' )
	{
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);

		$interval = date_diff($datetime1, $datetime2);

		return $interval->format($differenceFormat);

	}
}

if(!function_exists('getMonthYear')){
	function getMonthYear($tgldb,$separator = '-'){
		$t = explode($separator,$tgldb);
		return convert_ke_bulan($t[1]).' '.$t[0];
	}
}

if(!function_exists('getDateStr')){
	function getDateStr($tgldb,$separator = '-'){
		$t = explode($separator,$tgldb);
		return $t[2];
	}
}
if(!function_exists('FCR')){
	function FCR($rawFCR) {
		return ($rawFCR * 1000) / 1000;
	}
}

if(!function_exists('tabelkonfirmasippic')){
	function tabelkonfirmasippic($rp,$realisasi_produksi){
		$t = '';
		$code_realisasi = array('Sudah' => 'C','Belum' => 'I');
		$realisasi_produksi = $code_realisasi[$realisasi_produksi];
		if(!empty($rp)){
			$data = explode(',', $rp);
			$t = '<table><tbody>';
			$akhir = end($data);
			$cekbox_selesai = $realisasi_produksi == 'C' ? '' : '<div class="checkbox pull-right"><label><input type="checkbox" onclick="Konfirmasi_rp.tandai_berubah(this)"> Selesai</label></div>';

			foreach($data as $tr){
				$plus_sign = '';
				if($tr == $akhir && $realisasi_produksi != 'C'){
					$plus_sign = '&nbsp; <span class="glyphicon glyphicon-plus-sign" onclick="Konfirmasi_rp.reload_rencana_produksi(this)"></span>';
				}
				$t .= '<tr><td class="rencana_produksi" style="padding:2px 0px">'.$tr.'</td><td>'.$plus_sign.'</td></tr>';

			}
			$t .= '</tbody> <tfoot><tr><td colspan="2">'.$cekbox_selesai.'</td></tr></tfoot> </table>';

		}
		return $t;
	}

}
if(!function_exists('tglSebelum')){
	function tglSebelum($tgl,$hari){
		$date = new \DateTime($tgl);$date = new \DateTime($tgl);
		return $date->sub(new \DateInterval('P'.$hari.'D'))->format('Y-m-d');
	}
}

if(!function_exists('beratDalamStandar')){
	function beratDalamStandar($jmlsak,$beratpakan){
		$result = 1;
		$standar_persak = array('atas' => 50.1, 'bawah' => 50.08);
		$max_atas = $jmlsak * $standar_persak['atas'];
		$max_bawah = $jmlsak * $standar_persak['bawah'];
		if(($beratpakan < $max_bawah) or ($beratpakan > $max_atas)) $result = 0;
		return $result;
	}
}

if(!function_exists('dropdownRencanaProduksi')){
	function dropdownRencanaProduksi($arr,$name,$onchange){
		$opt = array();
		array_push($opt,'<select name="'.$name.'" onchange="'.$onchange.'">');
		array_push($opt,'<option value="">Pilih rencana produksi</option>');
		foreach($arr as $r){
			array_push($opt,'<option data-kode_barang="'.$r->kodepj.'" data-jml_produksi="'.$r->jml_produksi.'" value="'.$r->rp.'">'.$r->rp.'</option>');
		}
		array_push($opt,'</select>');
		return implode('',$opt);
	}
}


/*
[NOTE BY danbrown AT php DOT net: The array_diff_assoc_recursive function is a
combination of efforts from previous notes deleted.
Contributors included (Michael Johnson), (jochem AT iamjochem DAWT com),
(sc1n AT yahoo DOT com), and (anders DOT carlsson AT mds DOT mdh DOT se).]
*/
if(!function_exists('array_diff_assoc_recursive')){
	function array_diff_assoc_recursive($array1, $array2)
	{
		foreach($array1 as $key => $value)
		{
			if(is_array($value))
			{
				if(!isset($array2[$key]))
				{
					$difference[$key] = $value;
				}
				elseif(!is_array($array2[$key]))
				{
					$difference[$key] = $value;
				}
				else
				{
					$new_diff = array_diff_assoc_recursive($value, $array2[$key]);
					if($new_diff != FALSE)
					{
						$difference[$key] = $new_diff;
					}
				}
			}
			elseif(!isset($array2[$key]) || $array2[$key] != $value)
			{
				$difference[$key] = $value;
			}
		}
		return !isset($difference) ? 0 : $difference;
	}

}

/* cari hari kerja terdekat */
if(!function_exists('hari_kerja_terdekat')){
	function hari_kerja_terdekat($tgl,$arr_libur = array()){
		$index_hari = date('w',strtotime($tgl));
		$kerja = 0;
		$result = '';
		$diffDay = new DateInterval('P1D');
		while(!$kerja){
			/* 0 adalah hari ahad */
			if($index_hari == 0 || in_array($tgl,$arr_libur)){
				$tgl_date = new Datetime($tgl);
				$tgl_date->sub($diffDay);
				$tgl = $tgl_date->format('Y-m-d');
				$index_hari = date('w',strtotime($tgl));
			}
			else{
				$kerja = 1;
				$result = $tgl;
			}
		}
		return $result;
	}
}
if(!function_exists('isMutasi')){
	/* 000001/MT/CB/I/2017 */
	function isMutasi($ref){
		$t = substr($ref,7,2);
		return $t == 'MT' ? 1 : 0;
	}
}
if (! function_exists ( 'cetak_r' )) {
    function cetak_r($value, $die = TRUE) {
        echo "<pre>";
        print_r($value);
        if ($die) {
          die();
        }
    }
}

if (! function_exists ( 'arr2DToarrKey' )) {
	/**
	 * If you want change array structures to grouping array,
	 * within [your_key_to_group] from array element is your choice..
	 *
	 * @return Array.
	 */
	function arr2DToarrKey($array, $key_to_group) {
		$groups = array ();
		if(!empty($array)){
			foreach ( $array as $item ) {
				$key = $item [$key_to_group];
				$groups [$key] = $item;
			}
		}
		return $groups;
	}
}

if(!function_exists('tglSetelah')){
	function tglSetelah($tgl,$hari){
		$date = new \DateTime($tgl);
		return $date->add(new \DateInterval('P'.$hari.'D'))->format('Y-m-d');
	}
}
if(!function_exists('indexDay')){
	function indexDay($date){
		return date('N', strtotime($date));
	}
}

if(!function_exists('outputJson')){
		function outputJson($data){
			$CI = & get_instance();
			$CI->output
			->set_content_type('application/json')
			->set_output(json_encode($data));

		}
	}
if(!function_exists('buildHistoryPP')){
	function buildHistoryPP($baris){
		$result = array();
		$status_rilis = isset($baris['status_rilis']) ? $baris['status_rilis'] : 1;
		$status_review = isset($baris['status_review']) ? $baris['status_review'] : 1;
		$status_approve = isset($baris['status_approve']) ? $baris['status_approve'] : 1;
		switch($baris['status_lpb']){			
			case 'V':	
			case 'RJ':
				$class_approve = !$status_approve ? 'abang' : '';
				if(!empty($baris['tgl_approve1'])){
					array_push($result,'<div class="'.$class_approve.'">[ '.$baris['user_approve'].' ] - Direject, ( '.$baris['ket_reject'].' ) '.convertElemenTglWaktuIndonesia($baris['tgl_approve1']).'</div>');
				}
				break;				
			case 'A': 
				$class_approve = !$status_approve ? 'abang' : '';
				if(!empty($baris['tgl_approve1'])){
					array_push($result,'<div class="'.$class_approve.'">[ '.$baris['user_approve'].' ] - Diapprove, '.convertElemenTglWaktuIndonesia($baris['tgl_approve1']).'</div>');		
				}				
				break;
		}
		switch($baris['status_lpb']){							
			case 'V':
			case 'RJ':
			case 'A':
			case 'RV':
				$class_review = !$status_review ? 'abang' : '';
				if(!empty($baris['tgl_review'])){
					array_push($result,'<div class="'.$class_review.'">[ '.$baris['user_review'].' ] - Dikoreksi, '.convertElemenTglWaktuIndonesia($baris['tgl_review']).'</div>');
				}	
			case 'N':
				$class_rilis = !$status_rilis ? 'abang' : '';
				if(!empty($baris['tgl_rilis'])){
					array_push($result,'<div class="'.$class_rilis.'">[ '.$baris['user_buat'].' ] - Dirilis, '.convertElemenTglWaktuIndonesia($baris['tgl_rilis']).'</div>');
				}	
			case 'D':										
				$class_rilis = !$status_rilis ? 'abang' : '';						
				array_push($result,'<div class="'.$class_rilis.'">[ '.$baris['user_buat'].' ] - Dibuat, '.convertElemenTglWaktuIndonesia($baris['tgl_buat']));
		}
		return implode('',$result);	
	}
}
if(!function_exists('hitungFCR')){
	function hitungFCR($jml,$bb_rata,$gr_pakan){
		$keb_per_ekor = round($gr_pakan/$jml);
		$t = $bb_rata > 0 ? ($keb_per_ekor / ($bb_rata * 1000)) : null;
		return $t;
	}
}
if(!function_exists('hitungADG')){
	function hitungADG($data_bb,$bb_lalu){				
		$bb = $data_bb['berat_badan'];
		$umur = $data_bb['hari'];
		$bbLalu = $bb_lalu['berat_badan'];
		$umurLalu = $bb_lalu['hari'];		
		return ($bb - $bbLalu) / ($umur - $umurLalu) * 1000;
	}
}
if(!function_exists('hitungIP')){
	function hitungIP($dh,$bb,$fcr,$umur){
		$result = null;
		if(!empty($bb) && !empty($fcr)){
		//	$result = round((($dh / 100) * $bb * 100) / ($fcr * $umur));			
			$result = ceil((($dh * $bb ) / ($fcr * $umur)) * 100);			
		//	$result = round((($dh * $bb ) / ($fcr * $umur)) * 100);			
		}
		return $result;
	}
}

if(!function_exists('summaryFarm')){
	function summaryFarm($arr,$event,$classExt = ''){
		$result = array();
		if(!empty($arr)){
			$dataFarm = array();
			foreach($arr as $r){
				$kode_siklus = $r['kode_siklus'];
				if(!isset($dataFarm[$kode_siklus])){
					$dataFarm[$kode_siklus] = array('farm' => $r['kode_farm'], 'periode_siklus' => $r['periode_siklus'], 'strain' => $r['kode_strain'], 'nama_farm' =>  $r['nama_farm'], 'jml_kandang' => 0 );
				}
				$dataFarm[$kode_siklus]['jml_kandang']++;
			}
			$_content = array();
			if(!empty($dataFarm)){
				foreach($dataFarm as $ks => $f){
					$_tmp = '<div '.$event.' data-kodefarm="'. $f['farm'] . '" class="pointer alert biru '.$classExt.'" data-kodesiklus="'. $ks . '" style="border-bottom:1px solid blue;margin-top:5px">';
					$_tmp .= 'Farm '. $f['nama_farm'] . ' periode siklus '. $f['periode_siklus'] . ' ('. $f['strain'] . ','. $f['jml_kandang'] . ' Kandang)';
					$_tmp .= '</div>';
	
					array_push($_content,$_tmp);				
				}
			}
			$result = implode(' ',$_content);
		}
		return $result;
	}
}

if(!function_exists('generateBreadcumb')){
	function generateBreadcumb($arr){
		$result = '';
		if(!empty($arr)){
			$result  = '<ul class="breadcrumb"><li><span class="biru">'.implode('</li><li><span class="biru">',$arr).'</li></ul>';			
		}
		return $result;
	}
}	