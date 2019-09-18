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
            <th>No. Pengajuan</th>
            <th>Tgl Transaksi</th>
            <th>Kategori</th>
            <th>Harga Jual/Sak (Rp)</th>
            <th>Harga Jual Sebelumnya (Rp)</th>
        </tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($data as $perpp){
        $indexPP = 0;
        $rowspan = count($perpp);
        foreach($perpp as $d){
            echo '<tr>';
            if(!$indexPP){
                $checkbox = '<input type="checkbox"  data-fitur="pengajuan_harga" data-kirim=\''.json_encode(array('no_pengajuan_harga' =>  $d['no_pengajuan_harga'],'no_urut' => $d['no_urut'], 'kode_farm' => $d['kode_farm'] )).'\'  data-no_pengajuan_harga="'.$d['no_pengajuan_harga'].'">';
                echo '<td rowspan="'.$rowspan.'">'.$checkbox.'</td>';
                echo '<td rowspan="'.$rowspan.'">'.$d['no_pengajuan_harga'].'</td>';
                echo '<td rowspan="'.$rowspan.'">'.convertElemenTglWaktuIndonesia($d['tgl_pengajuan']).'</td>';
            }
            echo '<td>'.$d['nama_barang'].'</td>';
            echo '<td>'.angkaRibuan($d['harga_jual']).'</td>';
            $harga_dulu = isset($harga_lama[$d['kode_barang']]) ? $harga_lama[$d['kode_barang']]['harga_jual'] : 0;
            echo '<td>'.angkaRibuan($harga_dulu).'</td>';
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