<div class="row">
    <div class="col-md-12">
        <a
            href='pengambilan_barang/transaksi/cetak_daftar_pengambilan?no_order=<?php echo $no_order; ?>&pick=1'
            class='link btn btn-default' target="_blank">Print</a>
    </div>
    <div class="col-md-12">
        <div class="text-center header-content">
            <h2>Picking List</h2>
        </div>
        <div class="new-line">
            <div class="col-md-6 left-content">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4">No. Pengambilan</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['no_order']) ? strtoupper($items[0]['no_order']) : ''; ?></label>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4">Farm</label> <label
                            for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['farm']) ? strtoupper($items[0]['farm']) : ''; ?></label>
                    </div>
                </div>
            </div>
            <div class="col-md-6 right-content">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4">Tanggal Pengiriman</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['tgl_kirim']) ? convert_month($items[0]['tgl_kirim'], 1) : ''; ?></label>

                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4">Tanggal Kebutuhan</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['tgl_keb_awal']) ? convert_month($items[0]['tgl_keb_awal'], 1) . ' s/d ' . convert_month($items[0]['tgl_keb_akhir'], 1) : ''; ?></label>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div id="print-preview-table">
                <table class="table table-bordered table-content">
                    <thead>
                        <tr>
                            <th>Kode Kandang</th>
                            <th>Jenis Kelamin</th>
                            <th>Kavling-Pallet</th>
                            <th>Kode Pakan</th>
                            <th>Nama Pakan</th>
                            <th>Bentuk</th>
                            <th>Stok Gudang</th>
                            <th>Kebutuhan Pakan</th>
                            <th>Sisa Pakan LHK (sak)</th>
                            <th>Sisa Pakan Outstanding</th>
                            <th>Rencana Kirim</th>
                            <!--th>Berat (kg)</th-->
                            <th>Paraf</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php #$tmp_on_pick = ''; ?>
                        <?php #$tmp_pick = ''; ?>
                        <?php foreach ($items as $key => $value) { ?>
                            <?php #if(($tmp_on_pick != $value['tmp_jumlah']) && ($tmp_pick != $value['jumlah'])){ ?>
                            <?php if ($value['keterangan'] == 0) { ?>
                                <tr>
                                    <td><?php echo $value['kode_kandang']; ?></td>
                                    <td><?php echo $value['jenis_kelamin']; ?></td>
                                    <td><?php echo $value['kode_pallet']; ?></td>
                                    <td><?php echo $value['kode_barang']; ?></td>
                                    <td><?php echo $value['nama_barang']; ?></td>
                                    <td><?php echo $value['bentuk_pakan']; ?></td>
                                    <td><?php echo $value['jml_stok_gudang']; ?></td>
                                    <td><?php echo $value['kebutuhan_pakan']; ?></td>
                                    <td><?php echo $value['sisa_pakan']; ?></td>
                                    <td><?php echo $value['jml_order_outstanding'];#$value['kebutuhan_pakan'] - $value['jumlah']; ?></td>
                                    <td><?php echo $value['jumlah']; ?></td>
                                    <!--td><?php //echo $value['berat'];  ?></td-->
                                    <td></td>
                                </tr>
                            <?php } ?>
                            <?php #} ?>
                            <?php #$tmp_on_pick = $value['jumlah']; ?>
                            <?php #$tmp_pick = $value['tmp_jumlah']; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>