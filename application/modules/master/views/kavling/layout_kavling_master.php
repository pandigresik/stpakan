<?php $ke = 1; ?>
<?php foreach ($layout as $key0 => $value0) { ?>
    <?php foreach ($value0 as $key1 => $value1) { ?>
        <table class="table table-bordered text-center tbl-layout-kavling">
            <thead>
                <?php if ($ke == 1) { ?>
                    <tr>
                        <th class="text-center text-middle no-border"></th>
                        <th class="text-center text-middle no-border"></th>
                        <?php $alphabet = range($max_no_baris, 'A'); ?>
                        <th class="text-center text-middle text-baris" colspan='<?php echo count($alphabet); ?>'><h3><?php echo $key0; ?></h3></th>
            <th class="text-center text-middle no-border"></th>
            <th class="text-center text-middle no-border"></th>
            </tr>
            <tr>
                <th class="text-center text-middle no-border"></th>
                <th class="text-center text-middle no-border"></th>
                <?php $alphabet = range($max_no_baris, 'A'); ?>
                <th class="text-center text-middle text-baris" colspan='<?php echo count($alphabet); ?>'>BARIS</th>
                <th class="text-center text-middle no-border"></th>
                <th class="text-center text-middle no-border"></th>
            </tr>
            <tr>
                <th class="text-center text-middle no-border"></th>
                <th class="text-center text-middle no-border"></th>
                <?php $alphabet = range($max_no_baris, 'A'); ?>
                <?php foreach ($alphabet as $key => $value) { ?>
                    <th class="text-center text-middle text-baris"><?php echo $value; ?></th>
                <?php } ?>
                <th class="text-center text-middle no-border"></th>
                <th class="text-center text-middle no-border"></th>
            </tr>
        <?php } ?>
        </thead>
        <tbody>
            <?php foreach ($value1 as $key2 => $value2) { ?>
                <?php foreach ($value2 as $key3 => $value3) { ?>
                    <tr>
                        <?php if (($key2 == 'L' && $key3 == $data_kolom[$key0][$key1][$key2]['min_kolom']) || ($key2 == 'R' && $key3 == $data_kolom[$key0][$key1][$key2]['max_kolom'])) { ?>
                            <td class="text-center text-middle text-posisi"
                                rowspan="<?php echo count($value2); ?>"><div class="vertical-text">
                                    <b>POSISI</b>
                                </div></td>
                            <td class="text-center text-middle text-posisi"
                                rowspan="<?php echo count($value2); ?>"><b><?php echo $key1; ?></b></td>

                        <?php } ?>
                        <?php $alphabet = range($max_no_baris, 'A'); ?>
                        <?php foreach ($alphabet as $key => $value) { ?>
                            <?php //foreach($value3 as $key4 => $value4){ ?>
                            <?php if (isset($value3[$value])) { ?>
                                <?php foreach ($value3[$value] as $key5 => $value5) { ?>
                                    <?php if ($value == substr($key5, 0, 1)) { ?>
                                        <td ondblclick="selected(this)" 
                                            data-no-kavling="<?php echo $key5; ?>"
                                            data-no-reg="<?php echo $value5[0]['NO_REG']; ?>"
                                            data-nama-gudang="<?php echo $key0; ?>"
                                            data-jumlah-zak="<?php echo (empty($value5[0]['NAMA_KANDANG'])) ? '' : $value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']; ?>"
                                            class="text-center text-middle text-value <?php echo ((isset($value5[0]['JML_ON_HAND_KANDANG'])) && ($value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']) > 0) ? 'isi' : 'tidak-isi'; ?>">

                                            <div>
                                                <u><H4><?php echo $key5; ?></H4></u>
                                            </div>
                                        </td>
                                    <?php } ?>
                                <?php } ?>
                            <?php } else { ?>
                                <td class="text-center text-middle text-road"></td>

                            <?php } ?>
                            <?php //} ?>
                        <?php } ?>
                        <td class="text-center text-middle text-kolom"><b><?php echo $key3; ?></b></td>
                        <?php if (($key2 == 'L' && $key3 == $data_kolom[$key0][$key1][$key2]['min_kolom']) || ($key2 == 'R' && $key3 == $data_kolom[$key0][$key1][$key2]['max_kolom'])) { ?>
                            <td class="text-center text-middle text-kolom"
                                rowspan="<?php echo count($value2); ?>"><div class="vertical-text">
                                    <b>KOLOM</b>
                                </div></td>

                        <?php } ?>
                    </tr>
                <?php } ?>
            <?php } ?>
        </tbody>
        </table>
        <?php $ke++; ?>
    <?php } ?>
<?php } ?>