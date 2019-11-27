<!--pre><?php print_r($detail_mutasi_pakan); ?></pre-->
<table class="table table-bordered" id="tabel_detail_mutasi_pakan">
    <thead>
        <tr>
            <th rowspan="2">Jenis Pakan</th>
            <th colspan="5">Kandang Asal</th>
            <th rowspan="2">Kuantitas Mutasi</th>
            <th colspan="7">Kandang Tujuan</th>
        </tr>
        <tr>
            <th>Kandang</th>
            <th>Umur (hari)</th>
            <th>DH</th>
            <th>FCR</th>
            <th>IP</th>
            <th>Kandang</th>
            <th>Umur (hari)</th>
            <th>DH</th>
            <th>FCR</th>
            <th>IP</th>
            <th>Stok Akhir Gudang</th>
            <th>Stok Akhir Kandang</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($detail_mutasi_pakan as $key => $value) { ?>
            <tr>
                <td class="valign_top" rowspan="<?php echo $rowspan; ?>"><?php echo $value['jenis_pakan']; ?></td>
                <td class="valign_top" rowspan="<?php echo $rowspan; ?>"><?php echo $value['kandang_asal']; ?></td>
                <td class="valign_top" rowspan="<?php echo $rowspan; ?>"><?php echo $value['umur_asal']; ?></td>
                <td class="valign_top <?php echo ($value['dh_asal_red'] == 1) ? 'red_color' : ''; ?>" rowspan="<?php echo $rowspan; ?>"><?php echo number_format($value['dh_asal'], 2,'.',''); ?></td>
                <td class="valign_top <?php echo ($value['fcr_asal_red'] == 1) ? 'red_color' : ''; ?>" rowspan="<?php echo $rowspan; ?>"><?php echo number_format($value['fcr_asal'], 3,'.',''); ?></td>
                <td class="valign_top <?php echo ($value['ip_asal_red'] == 1) ? 'red_color' : ''; ?>" rowspan="<?php echo $rowspan; ?>"><?php echo number_format($value['ip_asal'], 0,'.',''); ?></td>
                <?php foreach ($value['detail'] as $k => $v) { ?>
                    <td><?php echo $v['kuantitas_mutasi']; ?></td>
                    <td><?php echo $v['kandang_tujuan']; ?></td>
                    <td><?php echo $v['umur_tujuan']; ?></td>
                    <td class="<?php echo ($v['dh_tujuan_red'] == 1) ? 'red_color' : ''; ?>"><?php echo number_format($v['dh_tujuan'], 2,'.',''); ?></td>
                    <td class="<?php echo ($v['fcr_tujuan_red'] == 1) ? 'red_color' : ''; ?>"><?php echo number_format($v['fcr_tujuan'], 3,'.',''); ?></td>
                    <td class="<?php echo ($v['ip_tujuan_red'] == 1) ? 'red_color' : ''; ?>"><?php echo number_format($v['ip_tujuan'], 0,'.',''); ?></td>
                    <td><?php echo $v['stok_gudang']; ?></td>
                    <td><?php echo $v['stok_kandang']; ?></td>
                </tr>
            <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>