<div class="panel panel-default">
    <div class="panel-heading">Pengambilan Barang</div>
    <div class="panel-body">
        <div>
            <div data-example-id="togglable-tabs" role="tabpanel"
                 class="bs-example bs-example-tabs">
                <ul role="tablist" class="nav nav-tabs" id="myTab">
                    <li <?php echo ($tab_active == 1) ? 'class="active"' : ""; ?>
                        role="presentation"><a aria-expanded="true"
                                           aria-controls="transaction" data-toggle="tab" role="tab"
                                           id="transaction-tab" href="#transaction">Transaksi</a></li>
                    <li <?php echo ($tab_active == 2) ? 'class="active"' : ""; ?>
                        role="presentation" class=""><a aria-controls="print-preview"
                                                    data-toggle="tab" id="print-preview-tab" role="tab"
                                                    href="#print-preview" aria-expanded="false">Print Preview</a></li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div aria-labelledby="transaction-tab" id="transaction"
                         class="tab-pane fade <?php echo ($tab_active == 1) ? 'active in' : ''; ?>"
                         role="tabpanel">
                        <!--div class="new-line">
    <button id="btn-konfirmasi" class="btn btn-default" type="submit"
            disabled="true" onclick="konfirmasi()">Konfirmasi</button>
</div-->

                        <div id="transaction-table" class="new-line">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="col-md-1">Kode Kandang</th>
                                        <th class="col-md-1">ID Kavling</th>
                                        <th class="col-md-2">Diserahkan Oleh</th>
                                        <!--th class="col-md-2">Penerima</th>
<th class="col-md-2">Tgl dan Waktu Serah Terima</th-->
                                        <th class="col-md-3" colspan="5"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $number = 1; ?>
                                    <?php foreach ($items_result as $key1 => $value1) { ?>
                                        <?php
                                        $kode = explode('#', $key1);
                                        $kode_kandang = $kode [0];
                                        $id_kavling = $kode [1];
                                        ?>
                                        <tr class='tr-header'
                                            data-ke="<?php echo $number; ?>"
                                            ondblclick="show_detail(this);">
                                            <td><?php echo $kode_kandang; ?></td>
                                            <td><?php echo $id_kavling; ?></td>
                                            <td
                                                data-user-buat="<?php echo empty($value1['user_buat']) ? $penerima : $value1['user_buat']; ?>"><?php echo empty($value1['user_buat']) ? $penerima : $value1['user_buat']; ?></td>
                                            <!--td class="user_gudang">
            <select class="form-control user_gudang" placeholder="User Gudang" name="user_gudang">
                <option value=""></option>
                                            <?php foreach ($user_gudang as $k => $v) { ?>
                    <option value="<?php echo $v['kode_pegawai']; ?>"><?php echo $v['nama_pegawai']; ?></option>
                                            <?php } ?>
            </select>
        </td>
        <td><?php echo '-'; ?></td-->
                                            <td colspan="5"></td>
                                        </tr>
                                        <tr class='tr-detail hide' data-ke="<?php echo $number; ?>">
                                            <td colspan="10">
                                                <table class="table table-bordered" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="col-md-1">Jenis Kelamin</th>
                                                            <th class="col-md-1">Kode Pakan</th>
                                                            <th class="col-md-1">Nama Pakan</th>
                                                            <th class="col-md-1">Bentuk</th>
                                                            <th class="col-md-1">Stok Gudang</th>
                                                            <th class="col-md-1">Kebutuhan Pakan (sak)</th>
                                                            <th class="col-md-1">Sisa Pakan LHK (sak)</th>
                                                            <th class="col-md-1">Sisa Pakan Outstanding</th>
                                                            <th class="col-md-1">Rencana Kirim</th>
                                                            <th class="col-md-1">Timbangan (kg)</th>
                                                            <th class="col-md-1">Timbangan (zak)</th>
                                                            <th class="col-md-1"><div style="width: 100px;">Keterangan</div></th>
                                                    <th class="col-md-1"><div style="width: 100px;">Penerima</div></th>
                                        </tr>
                                        </thead>
                                    <tbody>
                                        <?php foreach ($value1['detail'] as $key2 => $value2) { ?>
                                            <tr
                                                class="tr-sub-detail"
                                                data-no-reg="<?php echo $value2['no_reg']; ?>"
                                                data-kode-kandang="<?php echo $value2['kode_kandang']; ?>"
                                                data-id-kavling="<?php echo $value2['id_kavling']; ?>"
                                                data-no-pallet="<?php echo $value2['no_pallet']; ?>"
                                                data-kode-barang="<?php echo $value2['kode_barang']; ?>"
                                                data-no-order="<?php echo $value2['no_order']; ?>"
                                                data-jenis-kelamin="<?php echo $value2['kode_jenis_kelamin']; ?>"
                                                data-count="<?php echo count($value1['detail']); ?>">
                                                <td><?php echo $value2['jenis_kelamin']; ?></td>
                                                <td><?php echo $value2['kode_barang']; ?></td>
                                                <td><?php echo $value2['nama_barang']; ?></td>
                                                <td><?php echo $value2['bentuk_pakan']; ?></td>
                                                <td><?php echo $value2['jml_stok_gudang']; ?></td>
                                                <td><?php echo $value2['kebutuhan_pakan']; ?></td>
                                                <td><?php echo $value2['sisa_pakan']; ?></td>
                                                <?php //$jumlah = ($value2 ['berat']) ? $value2['tmp_jumlah'] : $value2['jumlah'];  ?>
                                                <?php $jumlah = $value2['tmp_jumlah'] + $value2['jumlah']; ?>
                                                <td><?php echo $value2['jml_order_outstanding'];#$value2['kebutuhan_pakan'] - $jumlah; ?></td>
                                                <td class="rencana_kirim"><?php echo $jumlah; ?></td>
                                                <td><input type="text" placeholder="Timbangan (kg)"
                                                           name="timbangan-kg" class="form-control timbangan_kg"
                                                           onchange="kontrol_timbangan(this)"
                                                           value="<?php echo ($value2 ['berat']) ? $value2 ['berat'] : ''; ?>"
                                                           readonly></td>
                                                <?php $jkt = (empty($value2['jumlah_konversi_timbang'])) ? $value2['tmp_jumlah'] : $value2['jumlah_konversi_timbang'] ; ?>
                                                <td class="timbangan_zak"><?php echo ($value2 ['berat']) ? $jkt : ''; ?></td>
                                                <td class="keterangan"><?php if ($value2 ['berat']) {
                                            if (!empty($value2['jumlah_aktual_sak'])) { ?>Jumlah Konversi Timbang = <?php echo $value2['jumlah_konversi_timbang']; ?> Sak<br>Jumlah Aktual = <?php echo $value2['jumlah_aktual_sak']; ?> Sak <?php } else {
                                                echo 'Selesai';
                                            }
                                        } else {
                                            echo '';
                                        } ?></td>
                                                <td>
        <?php if ($value2 ['berat']) { ?>
                                                        <p><?php echo $value2['user_gudang']; ?></p>
                                                        <p><?php echo convert_month($value2['tgl_buat'], 1) . ' ' . $value2['wkt_buat']; ?></p>

                                                        <?php } else { ?>
                                                        <button
                                                            data-result-timbang=""
                                                            class="btn-selesai btn btn-default" type="button"
                                                            onclick="selesai(this)" ondblclick="not_actived(this)" disabled>Selesai</button>
        <?php } ?>
                                                </td>
                                            </tr>
                                <?php } ?>
                                    </tbody>
                                </table>
                                </td>
                                </tr>
                                <?php $number++; ?>
                            <?php } ?>
                            </tbody>
                            </table>
                        </div>
                        <div class="text-right">
<?php echo $halaman; ?>
                            <!--ul class="pagination">
                                    <li class="disabled"><a aria-label="Previous" href="#"><span
                                                    aria-hidden="true">«</span></a></li>
                                    <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#">4</a></li>
                                    <li><a href="#">5</a></li>
                                    <li><a aria-label="Next" href="#"><span aria-hidden="true">»</span></a></li>
                            </ul-->
                        </div>
                    </div>
                    <div aria-labelledby="print-preview-tab" id="print-preview"
                         class="tab-pane fade <?php echo ($tab_active == 2) ? "active in" : ""; ?>"
                         role="tabpanel">
                        <div class="new-line">
                            <a
                                href="pengambilan_barang/transaksi/cetak_daftar_pengambilan?no_order=<?php echo $no_order; ?>&pick=0"
                                target="_blank">
                                <button type="button" class="btn btn-default link">Print</button>
                            </a>
                        </div>
                        <div class="text-center">
                            <h2>Daftar Pengiriman Barang</h2>
                        </div>
                        <div class="new-line">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
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
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kode Kandang</th>
                                            <th>Jenis Kelamin</th>
                                            <th>ID Kavling</th>
                                            <th>Kode Pakan</th>
                                            <th>Nama Pakan</th>
                                            <th>Bentuk Pakan</th>
                                            <th>Stok Gudang</th>
                                            <th>Kebutuhan Pakan (zak)</th>
                                            <th>Sisa Pakan (zak)</th>
                                            <th>Sisa Pakan Outstanding</th>
                                            <th>Rencana Kirim (zak)</th>
                                            <th>Timbangan (kg)</th>
                                            <th>Timbangan (zak)</th>
                                            <th>Paraf</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php foreach ($items_result as $k => $v) { ?>
    <?php foreach ($v['detail'] as $key => $value) { ?>
        <?php //if ($value['keterangan'] == 1 && $value['penerimaan_kandang'] == 0) {  ?>
        <?php if ($value['keterangan'] == 2) { ?>
                                                    <tr>
                                                        <td><?php echo $value['kode_kandang']; ?></td>
                                                        <td><?php echo $value['jenis_kelamin']; ?></td>
                                                        <td><?php echo $value['id_kavling']; ?></td>
                                                        <td><?php echo $value['kode_barang']; ?></td>
                                                        <td><?php echo $value['nama_barang']; ?></td>
                                                        <td><?php echo $value['bentuk_pakan']; ?></td>
                                                        <td><?php echo $value['jml_stok_gudang']; ?></td>
                                                        <td><?php echo $value['kebutuhan_pakan']; ?></td>
                                                        <td><?php echo $value['sisa_pakan']; ?></td>
                                                        <td><?php echo $value['jml_order_outstanding'];#$value['kebutuhan_pakan'] - ($value['tmp_jumlah'] + $value['jumlah']); ?></td>
                                                        <td><?php echo ($value['tmp_jumlah'] + $value['jumlah']); ?></td>
                                                        <td><?php echo $value['berat']; ?></td>
                                                        <td><?php echo $value['tmp_jumlah']; ?></td>
                                                        <td></td>
                                                    </tr>
        <?php } ?>
    <?php } ?>
<?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" type="text/css"
          href="assets/css/pengambilan_barang/pengambilan.css">
    <script type="text/javascript">
        var daftar_user_gudang = <?php echo $json_user_gudang; ?>;
        //console.log(daftar_user_gudang);
    </script>
    <script type="text/javascript"
    src="assets/js/pengambilan_barang/pengambilan.js"></script>