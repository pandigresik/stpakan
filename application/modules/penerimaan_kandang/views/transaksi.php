<div class="panel panel-default">
    <div class="panel-heading">Penerimaan Kandang</div>
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
                        rrole="presentation" class=""><a aria-controls="print-preview"
                                                     data-toggle="tab" id="print-preview-tab" role="tab"
                                                     href="#print-preview" aria-expanded="false">Print Preview</a></li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div aria-labelledby="transaction-tab" id="transaction"
                         class="tab-pane fade <?php echo ($tab_active == 1) ? 'active in' : ''; ?>"
                         role="tabpanel">
                        <div class="new-line">
                            <button id="btn-konfirmasi" class="btn btn-default" type="submit"
                                    disabled="true" onclick="konfirmasi()">Konfirmasi</button>
                        </div>
                        <div id="transaction-table" class="new-line">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th><input class="form-control filter" placeholder="Search"
                                                   type="text" name="kode_kandang" onkeyup="filter(this)"></th>
                                        <th><input class="form-control filter" placeholder="Search"
                                                   type="text" name="jenis_kelamin" onkeyup="filter(this)"></th>
                                        <th><input class="form-control filter" placeholder="Search"
                                                   type="text" name="kode_barang" onkeyup="filter(this)"></th>
                                        <th><input class="form-control filter" placeholder="Search"
                                                   type="text" name="nama_barang" onkeyup="filter(this)"></th>
                                        <th><input class="form-control filter" placeholder="Search"
                                                   type="text" name="jumlah" onkeyup="filter(this)"></th>
                                        <th><input class="form-control filter" placeholder="Search"
                                                   type="text" name="bentuk_pakan" onkeyup="filter(this)"></th>
                                        <th><select class="form-control filter" placeholder="User Gudang"
                                                    name="user_gudang" onchange="filter(this)">
                                                <option value="Semua">Semua</option>
                                            <?php foreach ($user_gudang as $key => $value) { ?>
                                                <option value="<?php echo $value['nama_pegawai']; ?>"><?php echo $value['nama_pegawai']; ?></option>
                                            <?php } ?>
                                            </select></th>
                                        <!--th><select class="form-control filter" placeholder="Remark"
                                                    name="remark" onchange="filter(this)">
                                                <option>Semua</option>
                                                <option>Received</option>
                                                <option>-</option>
                                            </select></th-->
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th class="kode_kandang">Kode Kandang</th>
                                        <th class="jenis_kelamin">Jenis Kelamin</th>
                                        <th class="kode_barang">Kode Barang</th>
                                        <th class="nama_barang">Nama Barang</th>
                                        <th class="jumlah">Jumlah (zak)</th>
                                        <th class="bentuk_pakan">Bentuk Pakan</th>
                                        <th class="bentuk_pakan">Diserahkan Oleh</th>
                                        <th class="bentuk_pakan">Penerima</th>
                                        <th class="bentuk_pakan">Tanggal dan Waktu Penerimaan</th>
                                        <!--th class="remark">Remark</th-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $number = 1; ?>
                                    <?php foreach ($items as $key => $value) { ?>
                                        <tr data-ke="<?php echo $number; ?>"
                                            data-no-reg="<?php echo $value['no_reg']; ?>"
                                            data-kode-farm="<?php echo $value['kode_farm']; ?>"
                                            data-no-penerimaan-kandang="<?php echo $value['no_penerimaan_kandang']; ?>"
                                            data-no-order="<?php echo $value['no_order']; ?>"
                                            data-jenis-kelamin="<?php echo $value['kode_jenis_kelamin']; ?>">
                                            <td>
                                                <?php if ($value['remark'] == 0) { ?>
                                                    <input class="radio" type="radio" name="radio"
                                                           onclick="kontrol_option(this)">
                                                       <?php } ?>
                                            </td>
                                            <td class="fkode_kandang"><?php echo $value['kode_kandang']; ?></td>
                                            <td class="fjenis_kelamin"><?php echo $value['jenis_kelamin']; ?></td>
                                            <td class="fkode_barang"><?php echo $value['kode_barang']; ?></td>
                                            <td class="fnama_barang"><?php echo $value['nama_barang']; ?></td>
                                            <?php if ($value['jumlah'] > 0) { ?>
                                                <td class="fjumlah"><?php echo $value['jumlah']; ?></td>
                                            <?php } else { ?>
                                                <td class="fjumlah"><input type="text" name="jumlah"
                                                                           placeholder="Jumlah" value="0"
                                                                           data-value="<?php echo $value['tmp_jumlah']; ?>"
                                                                           class="text-center form-control jumlah"
                                                                           onchange="kontrol_berat(this)" onkeyup="number_only(this)"></td>
                                                <?php } ?>
                                            <td class="fbentuk_pakan"><?php echo $value['bentuk_pakan']; ?></td>

                                            <?php if (!empty($value['user_gudang'])) { ?>
                                                <td class="fuser_gudang"><?php echo $value['user_gudang']; ?></td>
                                            <?php } else { ?>
                                                <td class="fuser_gudang"><select class="form-control" placeholder="User Gudang"
                                                            name="user_gudang" onchange="kontrol_user_gudang(this)">
                                                        <option value="">Semua</option>
                                                    <?php foreach ($user_gudang as $key1 => $value1) { ?>
                                                        <option value="<?php echo $value1['kode_pegawai']; ?>"><?php echo $value1['nama_pegawai']; ?></option>
                                                    <?php } ?>
                                                    </select></td>
                                                <?php } ?>

                                            <td class="fuser_buat"><?php if(empty($value['user_buat'])){ echo '-'; } else{ echo $value['user_buat']; }?></td>

                                            <td class="ftgl_buat"><?php if(empty($value['tgl_buat'])){ echo '-'; } else { echo convert_month($value['tgl_buat'],1); ?> <?php echo date('H:i',strtotime($value['waktu_buat'])); }?></td>
                                            <!--td class="fremark"><?php //echo ($value['remark'] == 1) ? 'Received' : '-'; ?></td-->
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
                            <a href="penerimaan_kandang/transaksi/cetak_daftar_penerimaan?no_order=<?php echo $items[0]['no_order']; ?>&pick=0" target="_blank"> <button type="button" class="btn btn-default link">Print</button></a>
                        </div>
                        <div class="text-center">
                            <h2>Daftar Penerimaan Kandang</h2>
                        </div>
                        <div class="new-line">
                            <div class="col-md-6">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-4">Farm</label> <label
                                            for="inputEmail3" class="col-sm-1">:</label> <label
                                            for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['farm']) ? strtoupper($items[0]['farm']) : ''; ?></label>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-4">Tanggal Pengiriman</label>
                                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                                            for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['tgl_kirim']) ? convert_month($items[0]['tgl_kirim'], 1) : ''; ?></label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-horizontal">
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
                                            <th>Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Jumlah (zak)</th>
                                            <th>Bentuk Pakan</th>
                                            <th>Diserahkan Oleh</th>
                                            <th>Penerima</th>
                                            <th>Tanggal dan Waktu Penerimaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $key => $value) { ?>
                                            <?php if ($value['remark'] == 1) { ?>
                                                <tr>
                                                    <td><?php echo $value['kode_kandang']; ?></td>
                                                    <td><?php echo $value['jenis_kelamin']; ?></td>
                                                    <td><?php echo $value['kode_barang']; ?></td>
                                                    <td><?php echo $value['nama_barang']; ?></td>
                                                    <td><?php echo $value['jumlah']; ?></td>
                                                    <td><?php echo $value['bentuk_pakan']; ?></td>
                                                    <td><?php echo $value['user_gudang']; ?></td>
                                                    <td><?php echo $value['user_buat']; ?></td>
                                                    <td><?php echo convert_month($value['tgl_buat'],1).' '.date('H:i',strtotime($value['tgl_buat'])); ?></td>
                                                </tr>
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
          href="assets/css/penerimaan_kandang/penerimaan.css">
    <script type="text/javascript"
    src="assets/js/penerimaan_kandang/penerimaan.js"></script>