
<table class="table table-bordered table-striped" id="history-hand_pallet">
    <thead>
        <tr>
            <th class="text-center col-md-2">ID hand_pallet</th>
            <th class="text-center col-md-3">Tanggal Penimbangan</th>
            <th class="text-center col-md-2">Status</th>
            <th class="text-center col-md-2">Tara (Kg)</th>
            <!--<th class="text-center col-md-3">Keterangan</th>-->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($hand_pallet as $key => $value) { ?>
            <?php $status_label = $value['STATUS_LABEL']; ?>
            <?php
                if($key == (count($hand_pallet)-1) && !$value['_DEFAULT']){
                    $status_label = '<select data-status="'.$value['STATUS_PALLET'].'" onchange="dialog_status_hand_pallet(this)" id="select_status" name="select_status" class="form-control">';
                    foreach ($status as $k => $v) {
                        $selected = ($k == $value['STATUS_PALLET']) ? "selected" : "" ;
                        $status_label .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
                    }
                    $status_label .= '</select>';
                }
            ?>
            <tr>
                <td class='text-center col-md-2 id_hand_pallet'><?php echo $value['KODE_HAND_PALLET']; ?></td>
                <td class='text-center col-md-3 tanggal' data-tanggal='<?php echo date('Y-m-d', strtotime($value['TGL_TIMBANG'])); ?>'><?php echo date('d-M-Y', strtotime($value['TGL_TIMBANG'])); ?></td>
                <td class='text-center col-md-2 status'><?php echo $status_label; ?></td>
                <td class='text-center col-md-2 tara'><?php echo $value['BRT_BERSIH']; ?></td>
                <!--<td class='text-center col-md-3 keterangan'><?php //echo $value['KETERANGAN']; ?></td>-->
            </tr>
        <?php } ?>
    </tbody>
</table>
