<div class="col-md-8">
    <!--pre><?php print_r($layout); ?></pre-->
    <?php foreach ($layout as $key0 => $value0) { ?>
    <?php $ke = 1; ?>
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
                                                data-kode-flok="<?php echo $value5[0]['KODE_FLOK']; ?>"
                                                data-nama-gudang="<?php echo $key0; ?>"
                                                data-jumlah-zak="<?php echo (empty($value5[0]['NAMA_KANDANG'])) ? '' : $value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']; ?>"
                                                class="text-center text-middle text-value <?php echo ((isset($value5[0]['JML_ON_HAND_KANDANG'])) && ($value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']) > 0) ? 'isi' : 'tidak-isi'; ?>">

                                                <div>
                                                    <u><H4><?php echo $key5; ?></H4></u>
                                                </div>
                                                <div class="nama-kandang"><?php echo $value5[0]['GRUP_BARANG']; ?></div>
                                                <div onmouseover="detail_selected(this);" onclick="detail_selected(this)" class="total_zak tooltipster" title="
                                                    <?php 
                                                        echo 'Flok '.$value5[0]['KODE_FLOK'].''; 
                                                        //echo '<br>Jenis Pakan : '.$value5[0]['NAMA_BARANG'].''; 
                                                        echo '<br>Tanggal Kedatangan : '.date('d M Y', strtotime($value5[0]['TGL_KEDATANGAN'])).''; 
                                                        echo '<br>Jumlah Sak : '.($value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']).''; 
                                                        echo '<table>';
                                                        echo '<tbody><tr>';
                                                        echo '  <td>Kode Pakan</td>';
                                                        echo '  <td>Nama Pakan</td>';
                                                        echo '  <td>Jumlah (Sak)</td>';
                                                        echo '</tr>';
                                                        echo '<tr>';
                                                        echo '  <td>'.$value5[0]['KODE_BARANG'].'</td>';
                                                        echo '  <td>'.$value5[0]['NAMA_BARANG'].'</td>';
                                                        echo '  <td>'.($value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']).'</td>';
                                                        echo '</tr>';
                                                        echo '</tbody></table>';
                                                    ?>
                                                ">
                                                    <?php if (!empty($value5[0]['KODE_FLOK'])) { ?>
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
                                                        <?php echo (empty($value5[0]['KODE_FLOK'])) ? '' : $value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']; ?>
                                                    <?php } ?>
                                                </div>
                                                <?php $detail_barang = ""; ?>
                                                <?php if (!empty($value5[0]['KODE_FLOK'])) { ?>
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
                                                    <div><?php echo $value5[0]['KODE_FLOK']; ?></div>
                                                    <div class="new-line row"><div class="col-md-3">Jenis Pakan</div><div class="col-md-1">:</div><div class=""><?php echo $value5[0]['NAMA_BARANG']; ?></div></div>
                                                    <div class="new-line row"><div class="col-md-3">Tgl Kedatangan</div><div class="col-md-1">:</div><div class=""><?php echo date('d M Y', strtotime($value5[0]['TGL_KEDATANGAN'])); ?></div></div>
                                                    <div class="new-line row"><div class="col-md-3">Jumlah sak</div><div class="col-md-1">:</div><div class=""><?php echo $value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']; ?></div></div>
                                                    <!--br>
                                                    <table class="detail-per-kavling">
                                                        <tbody> 
                                                            <tr>
                                                                <td colspan="3" style="border: 0;"><?php echo $value5[0]['KODE_FLOK']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: 0;">Jenis Pakan</td><td>:</td><td><?php echo $value5[0]['NAMA_BARANG']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: 0;">Tgl Kedatangan</td><td>:</td><td><?php ''; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: 0;">Jumlah sak</td><td>:</td><td><?php echo $value5[0]['JML_ON_HAND_KANDANG'] + $value5[0]['JML_ON_PUTAWAY_KANDANG']; ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table-->
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
            <br>
            <?php $ke++; ?>
        <?php } ?>
    <?php } ?>

</div>
<div class="col-md-4">
    <div class="div-header">Stok Gudang</div>
    <div class="div-select form-horizontal">
        <div class="form-group">
            <label class="col-md-2 control-label">Gudang</label>

            <div class="col-md-6">
                <select class="form-control" onchange="set_gudang()" id="gudang">
                    <?php foreach ($gudang as $key => $value) { ?>
                        <option value="<?php echo $value['kode_gudang']; ?>"><?php echo $value['nama_gudang']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="div-table">

    </div>
</div>





    <link rel="stylesheet" type="text/css"
          href="assets/css/penerimaan_pakan/penerimaan.css">
    <link rel="stylesheet" type="text/css"
          href="assets/css/mutasi_pakan/tooltipster.css">
    <style rel="stylesheet">
        .div-header{
            border: 2px solid #000000;
            padding: 2%;
            background-color: #357ebd;
            width: 100%;
            color: #ffffff;
            font-weight: bold;
        }
        .div-select, .div-table{
            margin-top: 2%;
        }
    </style>

    <script type="text/javascript"
    src="assets/js/jquery.alphanum.js"></script>
    <script type="text/javascript"
    src="assets/js/common.js"></script>

    <script type="text/javascript"
    src="assets/js/mutasi_pakan/jquery.tooltipster.min.js"></script>

    <script type="text/javascript">
    /*
        $('.detail_selected').tooltip();

        $('.tooltipster').tooltipster({
            animation: 'fade',
            delay: 200,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'hover',
            contentAsHTML : true
                                    
        });
    */
        function detail_selected(e) {
            $('div.total_zak').removeClass('tooltipster');
            $(e).addClass('tooltipster');
            
                            $('.tooltipster').tooltipster({
                                animation: 'fade',
                                delay: 200,
                                theme: 'tooltipster-default',
                                touchDevices: false,
                                trigger: 'hover',
                                contentAsHTML : true,
                                content: $(e).attr('title')
                            });
                            $('.tooltipster').tooltipster('show');
        }

        $('table.tbl-layout-kavling').css('border', 'none');
        $('table.tbl-layout-kavling thead tr th.no-border').css('border', 'none');
        $('table.tbl-layout-kavling th').css('border-color', 'black');
        $('table.tbl-layout-kavling td').css('border-color', 'black');

        function set_gudang(){
            var kode_gudang = $('#gudang').val();
            $.ajax({
                type : 'POST',
                dataType : 'html',
                url : "penerimaan_pakan/transaksi/set_gudang/",
                data : {
                    kode_gudang : kode_gudang
                }
            }).done(function(data) {
                $('div.div-table').html(data);
                var tabel_stok = $('div.div-table table#stok-gudang');
                if(tabel_stok.length > 0) {
                    tabel_stok.scrollabletable({
                        'max_height_scrollable' : 300
                    });
                }
            }).fail(function(reason) {
                console.info(reason);
            }).then(function(data) {
            });
        }

        set_gudang();
    </script>