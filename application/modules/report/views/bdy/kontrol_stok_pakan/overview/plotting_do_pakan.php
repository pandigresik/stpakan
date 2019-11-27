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
            <th></th>
            <th>No.DO</th>
            <th>Tgl Kirim</th>
            <th>Ekspedisi</th>
            <th>Rit</th>
            <th>Jenis Pakan</th>
            <th>Qty DO (Sak)</th>
        </tr>';
    echo '</thead>';
    echo '<tbody>';
    $flok_lama = '';
    foreach($data as $_tglkirim => $do){
        $indexTglKirim = 0;
        $checkbox = '<input type="checkbox" data-fitur="plotting_do_pakan" data-kirim=\''.json_encode(array('kode_farm' => $kode_farm, 'tgl_kirim' => $_tglkirim)).'\' data-kodefarm="'.$kode_farm.'" data-tglkirim="'.$_tglkirim.'">';
        $rowspan = $do['rowspan'];
        foreach($do['detail'] as $no_do => $perdo){
            $rowspanDO = count($perdo);
            echo '<tr>';
            if(!$indexTglKirim){
                echo '<td rowspan="'.$rowspan.'">'.$checkbox.'</td>';
            }
            echo '<td rowspan="'.$rowspanDO.'">'.$no_do.'</td>';
            if(!$indexTglKirim){
                echo '<td rowspan="'.$rowspan.'">'.convertElemenTglWaktuIndonesia($_tglkirim).'</td>';
            }
            $indexDO = 0;
            foreach($perdo as $d){
                if($indexDO){
                    echo '<tr>';
                }
                if(!$indexDO){
                    echo '<td rowspan="'.$rowspanDO.'">'.$d['nama_ekspedisi'].'</td>';
                    echo '<td rowspan="'.$rowspanDO.'">'.$d['rit'].'</td>';
                }
                echo '<td rowspan="'.$rowspanDO.'">'.$d['nama_barang'].'</td>';
                echo '<td rowspan="'.$rowspanDO.'">'.$d['jml_muat'].'</td>';
                $indexDO++;
            }
            $indexTglKirim++;    
        }
    }
    echo '</tbody>';
    echo '</table>';
}else {
    echo '';
}
?>
</div>