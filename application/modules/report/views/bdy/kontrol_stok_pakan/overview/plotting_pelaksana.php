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
        <th>Flok</th>
        <th>Kandang</th>
        <th>Koor. Pengawas</th>
        <th>Pengawas</th>
        <th>Operator 1</th>
        <th>Operator 2</th>
        </tr>';
    echo '</thead>';
    echo '<tbody>';
    $flok_lama = '';
    foreach($data as $d){
        $flok = $d['flok_bdy'];
        $kandang = substr($d['no_reg'],-2);
        $checkbox = '';
        $flok_str = '';
        if($flok != $flok_lama){
            $flok_str = $flok;
            $flok_lama = $flok;
            $checkbox = '<input type="checkbox"  data-fitur="plotting_pelaksana" data-kirim=\''.json_encode(array('kode_siklus' => $kode_siklus, 'flok' => $flok )).'\' data-kodesiklus="'.$kode_siklus.'" data-flokbdy="'.$flok.'">';
        }
        $operator = explode(',',$d['operator']);
        echo '<tr>';
        echo '<td>'.$checkbox.'</td>';
        echo '<td>'.$flok_str.'</td>';
        echo '<td><span class="link_span">'.$kandang.'</span></td>';
        echo '<td>'.$d['koordinator'].'</td>';
        echo '<td>'.$d['pengawas'].'</td>';
        echo '<td>'.$operator[0].'</td>';
        echo '<td>'.(isset($operator[1]) ? $operator[1] : '').'</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}else {
    echo '';
}
?>
</div>