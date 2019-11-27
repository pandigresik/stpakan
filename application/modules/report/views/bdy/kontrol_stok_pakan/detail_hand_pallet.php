<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered custom_table">
            <thead>
                <tr>
                    <th>KODE_HAND_PALLET</th>
                    <th>TGL_TIMBANG</th>
                    <th>BRT_BERSIH</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(!empty($data)){
                foreach($data as $d){
                    echo '<tr>';
                    echo '<td>'.$d['KODE_HAND_PALLET'].'</td>';
                    echo '<td>'.convertElemenTglWaktuIndonesia($d['TGL_TIMBANG']).'</td>';
                    echo '<td>'.formatAngka($d['BRT_BERSIH'],2).'</td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>