<?php
if(!empty($data)){
    foreach($data as $d){
        $tr_class = 'bg_orange';
        if(!empty($d['TGL_VERIFIKASI'])){
            if(!empty($d['TGL_VERIFIKASI_SJ'])){
                $tr_class = 'bg_abuabu';
            }else{
                $tr_class = 'bg_kuning';
            }
        }
        echo '<tr class="'.$tr_class.'" data-no_do="'.$d['NO_DO'].'">
            <td>'.convertElemenTglIndonesia($d['TGL_PANEN']).'</td>
            <td>'.$d['NOPOL'].'</td>
            <td>'.$d['NAMA_SOPIR'].'</td>
            <td>Kandang '.substr($d['NO_REG'],-2).'</td>
            <td>'.$d['NO_DO'].'</td>
            <td>'.$d['NO_SJ'].'</td>
            <td>'.convertElemenTglWaktuIndonesia($d['TGL_VERIFIKASI']).'</td>
            <td>'.convertElemenTglWaktuIndonesia($d['TGL_VERIFIKASI_SJ']).'</td>
        </tr>';
    }
}else{
    echo '<tr><td colspan="8">Data tidak ditemukan</td></tr>';
}

?>