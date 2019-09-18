
        
                        <div class="col-md-12">
                            <a href="penerimaan_pakan/transaksi/cetak_bukti_penerimaan_barang?no_op=<?php echo $list[0]['no_op']; ?>&no_penerimaan=<?php echo $list[0]['no_bpb']; ?>" target="_blank"> <button type="button" class="btn btn-default link">Print</button></a>
                        </div>
        <div class="col-md-12 new-line">
            <div class="col-md-6 left-content">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4 text-right">Asal/Terima dari</label> <label
                            for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($list[0]['asal_terima_dari']) ? $list[0]['asal_terima_dari'] : ''; ?></label>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4 text-right">Kota</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($list[0]['kota']) ? $list[0]['kota'] : ''; ?></label>

                    </div>
                </div>
            </div>
            <div class="col-md-6 right-content">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4 text-right">No. BPB</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($list[0]['no_bpb']) ? $list[0]['no_bpb'] : ''; ?></label>

                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4 text-right">No. OP</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($list[0]['no_op']) ? $list[0]['no_op'] : ''; ?></label>

                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4 text-right">No. SJ</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($list[0]['no_sj']) ? $list[0]['no_sj'] : ''; ?></label>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
<table class="table table-bordered" id="tbl-detail-penerimaan">
    <thead>
        <tr>
            <th class="col-md-2">Kode Barang</th>
            <th class="col-md-2">Nama Barang</th>
            <th class="col-md-2">Bentuk</th>
            <th class="col-md-1">Jumlah</th>
            <th class="col-md-2">Satuan</th>
            <th class="col-md-2">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $key => $value) { ?>
            <tr>
                <td><?php echo $value['kode_barang']; ?></td>
                <td><?php echo $value['nama_barang']; ?></td>
                <td><?php echo $value['bentuk_barang']; ?></td>
                <td><?php echo $value['terima_baik_zak']; ?></td>
                <td><?php echo $value['satuan']; ?></td>
                <td><?php echo $value['keterangan']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
        </div>