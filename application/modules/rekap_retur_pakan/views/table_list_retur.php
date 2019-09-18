<table id="tb_rekap" class="table table-condensed table-bordered">
    <thead style="background-color:#F0F0F0">
        <tr>
            <th class="hidden"></th>
            <th class="hidden"></th>
            <th class="hidden"></th>
            <th rowspan="2" class="vert-align" style="width:150px">No. Retur Pakan</th>
            <th rowspan="2" class="vert-align" style="width:150px">Kandang Asal</th>
            <th rowspan="2" class="vert-align" style="width:150px">Tanggal Retur</th>
            <th rowspan="2" class="vert-align" style="width:150px">Alokasi Retur</th>
            <th rowspan="2" class="vert-align" style="width:150px">Nama Pakan</th>
            <th colspan="2" class="vert-align">Pengajuan Retur</th>
            <th rowspan="3" class="vert-align">Keterangan</th>
        </tr>
        <tr>
            <th class="hidden"></th>
            <th class="hidden"></th>
            <th class="hidden"></th>
            <th class="vert-align" style="width:150px">Sak</th>
            <th class="vert-align" style="width:250px">Kg</th>

        </tr>
    </thead>
    <tbody>
        <?php
                        $retur_no = '';
                        $retur_asal = '';
                        $retur_tgl = '';
                        $retur_tujuan = '';

                        foreach ($retur_pakan as $rp) {
                            $keterangan = [];

                            if (!empty($rp['nama_terima'])) {
                                array_push($keterangan, '['.$rp['nama_terima'].'], Diterima '.$rp['tgl_terima']);
                            }

                            if (!empty($rp['nama_approve'])) {
                                $statusApprove = !empty($rp['keterangan2']) ? 'Ditolak ('.$rp['keterangan2'].') ' : 'Disetujui';
                                array_push($keterangan, '['.$rp['nama_approve'].'], '.$statusApprove.' '.$rp['tgl_approve']);
                            }

                            array_push($keterangan, '['.$rp['nama_buat'].'], Dirilis '.$rp['tgl_buat']);
                            $class_tr = !empty($rp['keterangan2']) ? 'bg-pink' : '';

                            $tgl_retur = (isset($rp['tgl_retur']) and !empty($rp['tgl_retur'])) ? $rp['tgl_retur'] : '';
                            $tgl_approve = (isset($rp['nama_approve']) and !empty($rp['nama_approve'])) ? $rp['tgl_approve'] : '';
                            $tgl_terima = (isset($rp['nama_terima']) and !empty($rp['nama_terima'])) ? $rp['tgl_terima'] : '';
                            $no_retur = (isset($rp['no_retur']) and !empty($rp['no_retur'])) ? $rp['no_retur'] : '';

                            $tipe_retur = $rp['tipe_retur'];
                            $backColor = ($tipe_retur == 'gudang') ? 'style="background-color:#E1F9FA"' : '';
                            $backColor = '';

                            if (($no_retur == $retur_no) and ($rp['no_reg'] == $retur_asal) and ($tgl_retur == $retur_tgl) and ($rp['tujuan_retur'] == $retur_tujuan)) {
                                $col_noretur = '';
                                $col_namakdg = '';
                                $col_tgretur = '';
                                $col_tjretur = '';
                                $col_apretur = '';
                            } else {
                                $col_noretur = $no_retur;
                                $col_namakdg = $rp['nama_kandang'];
                                $col_tgretur = $tgl_retur;
                                $col_tjretur = $rp['tujuan_retur'];
                                $col_apretur = $tgl_approve;
                            } ?>
        <tr class="<?php echo $class_tr; ?>" ondblclick="detailRetur(this)" data-noretur="<?php echo $rp['no_retur']; ?>">
            <td class="hidden" data-tipe_retur="<?php echo $tipe_retur; ?>" <?php echo $backColor; ?>><?php echo $rp['no_retur']; ?></td>
            <td class="hidden no_reg" <?php echo $backColor; ?>><?php echo $rp['no_reg']; ?></td>
            <td class="hidden" <?php echo $backColor; ?>><?php echo $rp['kode_kandang']; ?></td>
            <td class="link vert-align no_retur" <?php echo $backColor; ?>><?php echo $col_noretur; ?></td>
            <td class="link vert-align" <?php echo $backColor; ?>><?php echo $col_namakdg; ?></td>
            <td class="link vert-align tgl_retur" <?php echo $backColor; ?> data-tgl-retur_ori="<?php echo $rp['tgl_retur_ori']; ?>" data-tgl_retur="<?php echo $tgl_retur; ?>"><?php echo $col_tgretur; ?></td>
            <td class="link vert-align tujuan_retur" <?php echo $backColor; ?>><?php echo $col_tjretur; ?></td>
            <td class="link vert-align kode_barang" <?php echo $backColor; ?> data-kode_barang="<?php echo $rp['kode_barang']; ?>">
                <?php echo $rp['nama_barang']; ?>
            </td>
            <td class="link vert-align jml_retur" <?php echo $backColor; ?> align="right" data-jml_akhir="<?php echo $rp['jml_akhir']; ?>" data-jml_pakai="<?php echo $rp['jml_retur_pakai']; ?>" data-jml_retur="<?php echo $rp['jml_retur']; ?>" data-brt_retur="<?php echo $rp['brt_retur']; ?>" data-brt_retur_baru="<?php echo $rp['brt_retur']; ?>"><?php echo $rp['jml_retur']; ?></td>
            <td class="link vert-align berat_retur" <?php echo $backColor; ?>><?php echo $rp['brt_retur']; ?></td>
            <td><?php echo '<div>'.implode('<div></div>', $keterangan).'</div>'; ?></td>
            
            
        </tr>
        <?php
                            $retur_no = $no_retur;
                            $retur_asal = $rp['no_reg'];
                            $retur_tgl = $tgl_retur;
                            $retur_tujuan = $rp['tujuan_retur'];
                        }
                        ?>

    </tbody>
</table>