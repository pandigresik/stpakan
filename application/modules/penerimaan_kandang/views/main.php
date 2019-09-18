<div class="panel panel-default">
    <div class="panel-heading">Penerimaan Kandang</div>
    <div class="panel-body">
        <!--div>
                <a href="#pengambilan_barang/transaksi" class="btn btn-default">Baru</a>
        </div-->
        <div class="form-inline new-line">
            <label for="tanggal-kirim">Tanggal Kirim</label>
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" id="tanggal-kirim-awal"
                           name="tanggal-kirim-awal" placeholder="Tanggal Kirim Awal"
                           readonly>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" id="tanggal-kirim-akhir"
                           name="tanggal-kirim-akhir" placeholder="Tanggal Kirim Akhir"
                           readonly>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
            <button class="btn btn-default" id="btn-cari"
                    onclick="get_data_pengambilan()">Cari</button>
        </div>
        <div id="picking-list-table" class="new-line">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tgl Kirim</th>
                        <th>Tgl Kebutuhan</th>
                        <th>Jumlah Kebutuhan</th>
                        <th>Jumlah Belum Proses</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $key => $value) { ?>
                        <?php if ($value['status_order'] == 'C') { ?>
                            <tr>
                                <td data-no-order='<?php echo $value['no_order']; ?>'
                                    data-kode-farm='<?php echo $value['kode_farm']; ?>'><span
                                        href='#penerimaan_kandang/transaksi' class='btn link' style='color:#428bca;'
                                        onclick='get_data_detail_pengambilan(this,1)'><?php echo convert_month($value['tgl_kirim'], 1); ?></span></td>
                                <td class='tgl_kebutuhan'><?php echo convert_month($value['tgl_keb_awal'], 1) . ' s/d ' . convert_month($value['tgl_keb_akhir'], 1); ?></td>
                                <td><?php echo $value['jumlah_kebutuhan']; ?></td>
                                <td><?php echo $value['jumlah_belum_proses']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css"
      href="assets/css/penerimaan_kandang/penerimaan.css">
<script type="text/javascript"
src="assets/js/penerimaan_kandang/penerimaan.js"></script>