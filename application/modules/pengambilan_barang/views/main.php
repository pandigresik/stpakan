<div class="panel panel-default">
    <div class="panel-heading">Pengambilan Barang</div>
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
        <div class="form-inline new-line filter_pengambilan">
            <div class="checkbox col-md-4">
                <label><input type="checkbox" checked="checked" id="checkbox_normal" value="1" name="checkbox_normal" onclick="kontrol_chekbox(this)"> Pengambilan Normal</label>
            </div><div class="checkbox col-md-4">
                <label><input type="checkbox" checked="checked" id="checkbox_retur" value="1" name="checkbox_retur" onclick="kontrol_chekbox(this)"> Pengambilan dari Retur Pakan Rusak</label>
            </div><div class="checkbox col-md-4">
                <label><input type="checkbox" onclick="kontrol_chekbox(this)" name="checkbox_belum_proses" value="1" id="checkbox_belum_proses" checked="checked"> Belum Selesai Proses</label>
            </div>
        </div>
        <br>
        <div id="picking-list-table" class="new-line">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No. Pengambilan</th>
                        <th>Tgl Kirim</th>
                        <th>Tgl Kebutuhan</th>
                        <th>Jumlah Kebutuhan</th>
                        <th>Jumlah Belum Proses</th>
                        <th>No. Referensi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $key => $value) { ?>
                        <?php //if($value['status_order'] != 'D'){ ?>
                        <tr class='tr_order' ondblclick='get_data_detail_pengambilan(this,1)'>
                            <td class='no_pengambilan'><?php echo (empty($value['no_order'])) ? '-' : $value['no_order']; ?></td>
                            <td class="first"
                                data-no-order='<?php echo $value['no_order']; ?>'
                                data-kode-farm='<?php echo $value['kode_farm']; ?>'
                                data-generate='<?php echo $value['generate']; ?>'><!--span
                                    href='#pengambilan_barang/transaksi' style='color: #428bca;'
                                    class='btn link'-->
                                        <?php echo (empty($value['tgl_kirim'])) ? '-' : convert_month($value['tgl_kirim'], 1); ?>
                                <!--/span--></td>
                            <td><?php echo (empty($value['tgl_keb_awal'])) ? '-' : convert_month($value['tgl_keb_awal'], 1) . ' s/d ' . convert_month($value['tgl_keb_akhir'], 1); ?></td>
                            <td class='jml_kebutuhan'><?php echo $value['jumlah_kebutuhan']; ?></td>
                            <td class='jml_belum_proses'><?php echo $value['jumlah_belum_proses'] ?></td>
                            <td><?php echo empty($value['no_referensi']) ? '-' : $value['no_referensi']; ?></td>
                            <td>
                                <?php if ($value['generate'] == 1) { ?>
                                    <span style='color: #428bca;'
                                          data-kode-farm='<?php echo $value['kode_farm']; ?>'
                                          data-tanggal-kirim='<?php echo convert_month($value['tgl_kirim'], 1); ?>'
                                          data-tanggal-kebutuhan-awal='<?php echo convert_month($value['tgl_keb_awal'], 1); ?>'
                                          data-tanggal-kebutuhan-akhir='<?php echo convert_month($value['tgl_keb_akhir'], 1); ?>'
                                          href='#' class='btn link' onclick='generate(this)'>Generate</span>
                                      <?php } else { ?>
                                    <span style='color: #428bca;'
                                          href='#pengambilan_barang/transaksi' class='btn link'
                                          onclick='cetak_picking_list(this)'>Cetak Picking List</span>
                                      <?php } ?>
                            </td>
                        </tr>
                        <?php //} ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css"
      href="assets/css/pengambilan_barang/pengambilan.css">
<script type="text/javascript" src="assets/js/jquery.alphanum.js"></script>
<script type="text/javascript"
src="assets/js/pengambilan_barang/pengambilan.js"></script>