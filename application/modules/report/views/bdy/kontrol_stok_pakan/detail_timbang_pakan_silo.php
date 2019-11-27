<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered custom_table">
            <thead>
                <tr>
                    <th>No. Urut</th>
                    <th>Jumlah</th>
                    <th>Berat</th>
                    <th>Tgl Timbang</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $totalJml = 0;
            $totalBerat = 0;
            if(!empty($data)){
                foreach($data as $d){
                    echo '<tr>';
                    echo '<td>'.$d['no_urut'].'</td>';
                    echo '<td>'.$d['jml'].'</td>';
                    echo '<td>'.formatAngka($d['berat'],2).'</td>';
                    echo '<td>'.convertElemenTglWaktuIndonesia($d['tgl_buat']).'</td>';
                    echo '</tr>';

                    $totalJml += $d['jml'];
                    $totalBerat += $d['berat'];
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr class="text-center">
                <td>Total</td>
                <td><?php echo angkaRibuan($totalJml) ?></td>
                <td><?php echo formatAngka($totalBerat,2) ?></td>
                <td></td>
            </tr>
        </table>
    </div>
</div>