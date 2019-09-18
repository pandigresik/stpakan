<!--pre>{print_r($layout)}</pre-->
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
                                            <div class="nama-kandang hide"><?php echo $value5[0]['NAMA_KANDANG']; ?></div>
                                            <div onclick="detail_selected(this)" class="total_zak">
                                                <?php if (!empty($value5[0]['NAMA_KANDANG'])) { ?>
                                                    <!--a class="detail_selected" title="" data-placement="top"
                                                            data-toggle="tooltip" href="#"
                                                            data-original-title="
                                                    <?php
                                                    //foreach ( $value5 as $key6 => $value6 ) {
                                                    //echo '[' . $value6 ['KODE_BARANG'] . " = " . ($value6 ['JML_ON_HAND_BARANG'] + $value6 ['JML_ON_PUTAWAY_BARANG']) . " zak] ";
                                                    //}
                                                    ?>
                                                    ">
                                                    <?php //echo (empty($value5[0]['NAMA_KANDANG'])) ? '' : $value5[0]['JML_ON_HAND_KANDANG']+$value5[0]['JML_ON_PUTAWAY_KANDANG']; ?>
                                                    </a-->
                                                    <?php echo (empty($value5[0]['NAMA_KANDANG'])) ? '' : $value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']; ?>
                                                <?php } ?>
                                            </div>
                                            <?php $detail_barang = ""; ?>
                                            <?php if (!empty($value5[0]['NAMA_KANDANG'])) { ?>
                                                <?php
                                                $tmp_array = [];
                                                foreach ($value5 as $key6 => $value6) {
                                                    $tmp_array[$value6 ['KODE_BARANG']] = array(
                                                        'NAMA_BARANG' => $value6 ['NAMA_BARANG'],
                                                        'JUMLAH_STOK' => ($value6 ['JML_ON_HAND_BARANG'] + $value6 ['JML_ON_PUTAWAY_BARANG']),
                                                    );
                                                    //$detail_barang .= $value6 ['KODE_BARANG'] . ', ' . $value6 ['NAMA_BARANG'] . " = " . ($value6 ['JML_ON_HAND_BARANG'] + $value6 ['JML_ON_PUTAWAY_BARANG']) . " zak<br>";
                                                }
                                                foreach ($tmp_array as $key7 => $value7) {
                                                    $detail_barang .= $key7 . ', ' . $value7 ['NAMA_BARANG'] . " = " . $value7 ['JUMLAH_STOK'] . " zak<br>";
                                                }
                                                ?>
                                            <?php } ?>
                                            <div class="detail-barang hide" data-detail-barang="<?php echo $detail_barang; ?>">
                                            	<div><?php echo $value5[0]['NAMA_KANDANG']; ?></div>
                                            	<div class="new-line">Total Jumlah (sak) : <?php echo $value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']; ?></div>
                                            	<br>
                                            	<table class="table table-bordered detail-per-kavling">
                                            		<thead>	
                                            			<tr><th class="text-center">Kode Pakan</th><th class="text-center">Nama Pakan</th><th class="text-center">Jumlah (sak)</th></tr>
                                            		</thead>	
                                            		<tbody>	
                                                		<?php foreach ($tmp_array as $key7 => $value7) { ?>
                                            			<tr><td class="text-center"><?php echo $key7; ?></td><td class="text-center"><?php echo $value7 ['NAMA_BARANG']; ?></td><td class="text-center"><?php echo $value7 ['JUMLAH_STOK']; ?></td></tr>
                                            			<?php } ?>
                                            		</tbody>
                                            	</table>
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