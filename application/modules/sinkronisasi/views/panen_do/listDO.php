<?php

    echo '<table class="table table-bordered custom_table">
        <thead>
            <tr>                
                <th>No. Reg</th>
                <th>Nomor DO</th>
                <th>Tonase (Kg)</th>
                <th>Jumlah (ekor)</th>
                <th>No. SJ</th>
                <th>Rit</th>
                <th>Nopol</th>
                <th>Sopir</th>
                <th>Tim Panen</th>
                <th>Mulai Panen</th>
                <th>Selesai Panen</th>
                <th>Nama pelanggan</th>                
            </tr>                                
        </thead>';
    echo '<tbody>';
if (!empty($dos)) {
    foreach ($dos as $do) {
        if ($kode_farm != substr($do['noreg'], 0, 2)) {
            continue;
        }
        echo '<tr>            
                <td data-tglpanen="'.$do['tanggal'].'" class="no_reg">'.substr($do['noreg'], 0, 2).'/'.substr($do['noreg'], 2, 4).'-'.substr($do['noreg'], 7, 1).'/'.substr($do['noreg'], 8, 2).'</td>
                <td class="no_do">'.$do['nomor_do'].'</td>
                <td class="berat" data-berat="'.$do['kg'].'">'.formatAngka($do['kg'], 0).'</td>
                <td class="jumlah" data-jumlah="'.$do['ekor'].'">'.formatAngka($do['ekor'], 0).'</td>
                <td class="no_sj">'.$do['nomor_sj'].'</td>
                <td class="rit">'.$do['rit'].'</td>
                <td class="nopol">'.trim(preg_replace('/\s+/', '', $do['nopol'])).'</td>
                <td class="sopir" data-id_sopir="'.$do['id_sopir'].'">'.$do['sopir'].'</td>
                <td class="tim_panen" data-nik_timpanen="'.$do['nik_panen'].'">'.$do['tim_panen'].'</td>
                <td class="mulai_panen" data-rcn_mulai_panen="'.$do['rcn_mulai_panen'].'">'.convertElemenTglWaktuIndonesia($do['rcn_mulai_panen']).'</td>
                <td class="selesai_panen" data-rcn_selesai_panen="'.$do['rcn_selesai_panen'].'">'.convertElemenTglWaktuIndonesia($do['rcn_selesai_panen']).'</td>
                <td>RPA</td>                
            </tr>';
    }
}
if (!empty($dob)) {
    foreach ($dob as $do) {
        if ($kode_farm != substr($do['noreg'], 0, 2)) {
            continue;
        }
        echo '<tr>            
        <td data-tglpanen="'.$do['tanggal'].'" class="no_reg">'.substr($do['noreg'], 0, 2).'/'.substr($do['noreg'], 2, 4).'-'.substr($do['noreg'], 7, 1).'/'.substr($do['noreg'], 8, 2).'</td>
                <td class="no_do">'.$do['nomor_do'].'</td>
                <td class="berat" data-berat="'.$do['kg'].'">'.formatAngka($do['kg'], 0).'</td>
                <td class="jumlah" data-jumlah="'.$do['ekor'].'">'.formatAngka($do['ekor'], 0).'</td>
                <td class="no_sj">SB'.substr($do['nomor_sj'], 2).'</td>
                <td class="rit">'.$do['rit'].'</td>
                <td class="nopol">'.trim(preg_replace('/\s+/', '', $do['nopol'])).'</td>
                <td class="sopir" data-id_sopir="'.$do['id_sopir'].'">'.$do['sopir'].'</td>
                <td class="tim_panen" data-nik_timpanen="'.$do['nik_panen'].'">'.$do['tim_panen'].'</td>
                <td class="mulai_panen" data-rcn_mulai_panen="'.$do['rcn_mulai_panen'].'">'.convertElemenTglWaktuIndonesia($do['rcn_mulai_panen']).'</td>
                <td class="selesai_panen" data-rcn_selesai_panen="'.$do['rcn_selesai_panen'].'">'.convertElemenTglWaktuIndonesia($do['rcn_selesai_panen']).'</td>
                <td>RPA</td>                
            </tr>';
    }
}
    echo '</tbody>';
    echo '</table>';
