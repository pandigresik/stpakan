<?php if(isset($headerMessage)){
    echo '<div class="text-center"><h4>'.$headerMessage.'</h4></div>';
}else{ ?>
    <div class="pointer alert alert-info div_detailkandang">&nbsp; <input type="checkbox" onclick="KSP.checkAll(this)">&nbsp; &nbsp; &nbsp; <?php echo $header ?><span class="label label-warning pull-right"><?php echo $jumlah ?></span></div>
<?php } ?>
<div class="detailrhk">
<?php
if(!empty($data)){
    echo '<table class="table table-bordered custom_table">';
    echo '<thead>';
    echo '<tr>
            <th rowspan="2"></th>
            <th rowspan="2">No. PP</th>
            <th rowspan="2">Kandang</th>
            <th rowspan="2">Tgl. Kirim</th>
            <th rowspan="2">Tgl Kebutuhan</th>
            <th rowspan="2">Umur</th>
            <th rowspan="2">Jenis Pakan</th>
            <th rowspan="2">Rekomendasi PP (Sak)</th>
            <th colspan="2">Permintaan Sak</th>
        </tr>';
    echo '<tr>
            <th>Kafarm</th>
            <th>kadept PI</th>
        </tr>';    
    echo '</thead>';
    echo '<tbody>';
    foreach($data as $perpp){
        $indexPP = 0;
        $rowspan = count($perpp);
        foreach($perpp as $d){
            echo '<tr>';
            if(!$indexPP){
                $kandang = substr($d['no_reg'],-2);
                $checkbox = '<input type="checkbox" data-fitur="pp" data-kirim=\''.json_encode(array('no_lpb' => $d['no_lpb'], 'kandang' => $kandang,'tgl_kirim' => $d['tgl_kirim'])).'\' data-no_lpb="'.$d['no_lpb'].'">';
                
                $tgl_kebutuhan = array_unique(array(convertElemenTglWaktuIndonesia($d['tgl_keb_awal']),convertElemenTglWaktuIndonesia($d['tgl_keb_akhir'])));
                $tgl_kebutuhan_str = implode(' s/d ',$tgl_kebutuhan);            
                $umur = array_unique(array($d['umur_awal'],$d['umur_akhir']));
                $umur_str = implode(' s/d ',$umur);

                echo '<td rowspan="'.$rowspan.'">'.$checkbox.'</td>';
                echo '<td rowspan="'.$rowspan.'">'.$d['no_lpb'].'</td>';
                echo '<td rowspan="'.$rowspan.'">'.$kandang.'</td>';
                echo '<td rowspan="'.$rowspan.'">'.convertElemenTglWaktuIndonesia($d['tgl_kirim']).'</td>';
                echo '<td rowspan="'.$rowspan.'">'.$tgl_kebutuhan_str.'</td>';
                echo '<td rowspan="'.$rowspan.'">'.$umur_str.'</td>';
            }
            echo '<td>'.$d['nama_barang'].'</td>';
            echo '<td>'.$d['rekomendasi'].'</td>';
            echo '<td>'.$d['kafarm'].'</td>';
            echo '<td>'.$d['kadept'].'</td>';
            $indexPP++;    
            echo '</tr>';
        }
    }
    echo '</tbody>';
    echo '</table>';
}else {
    echo '';
}
?>
</div>