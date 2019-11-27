<div class="row">
    <div class="col-md-12 sticky-table">
        <table class="table table-bordered custom_table">
            <thead>
                <tr class="sticky-header">
                    <th>NO_KAVLING</th>
                    <th>KODE_PALLET</th>
                    <th>TGL_TIMBANG</th>
                    <th>BRT_BERSIH</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if(!empty($data)){
                foreach($data as $d){
                    echo '<tr>';
                    echo '<td>'.$d['NO_KAVLING'].'</td>';
                    echo '<td>'.$d['KODE_PALLET'].'</td>';
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