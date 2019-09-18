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
            <th rowspan="2">No. PPSK</th>
            <th rowspan="2">Tgl Kebutuhan</th>
            <th rowspan="2">Kategori</th>
            <th rowspan="2">Jml Sak Diminta</th>
            <th colspan="2">Over Budget</th>
        </tr>';
    echo '<tr>
            <th>Jml Sak</th>
            <th>Alasan</th>
        </tr>';    
    echo '</thead>';
    echo '<tbody>';
        foreach($data as $d){
            $checkbox = '<input type="checkbox" data-kirim=\''.json_encode(array('no_ppsk' => $d['no_ppsk'], 'tgl_kebutuhan' => $d['tgl_kebutuhan'],'jml_sak' => $d['jml_diminta'], 'kategori' => $d['nama_barang'])).'\' data-fitur="ppsk" data-no_ppsk="'.$d['no_ppsk'].'" />';
            echo '<tr>';
            echo '<td>'.$checkbox.'</td>';
            echo '<td>'.$d['no_ppsk'].'</td>';
            echo '<td>'.convertElemenTglWaktuIndonesia($d['tgl_kebutuhan']).'</td>';
            echo '<td>'.$d['nama_barang'].'</td>';
            echo '<td>'.$d['jml_diminta'].'</td>';
            echo '<td>'.$d['jml_over_budget'].'</td>';
            echo '<td>'.$d['keterangan'].'</td>';
            echo '</tr>';
        }
    
    echo '</tbody>';
    echo '</table>';
}else {
    echo '';
}
?>
</div>