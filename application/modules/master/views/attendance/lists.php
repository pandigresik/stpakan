<?php

if (!empty($data)) {
    foreach ($data as $d) {
        echo '<tr>
            <td>'.++$awal.'</td>
            <td>'.$d['KODE_PEGAWAI'].'</td>
            <td>'.$d['NAMA_PEGAWAI'].'</td>
            <td>'.$d['BADGE_NUMBER'].'</td>
            <td>'.$d['NAMAABSEN'].'</td>
            <td>'.$d['DESKRIPSI'].'</td>
        </tr>';
    }
} else {
    echo '<tr><td colspan="8">Data tidak ditemukan</td></tr>';
}
