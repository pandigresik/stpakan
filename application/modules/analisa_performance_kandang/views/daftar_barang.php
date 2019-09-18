<table class="table table-bordered text-center" id="tbl-daftar-barang">
    <thead>
        <tr>
            <th class="text-center"></th>
            <th class="text-center">Kode Barang</th>
            <th class="text-center">Nama Barang</th>
            <th class="text-center">Bentuk Pakan</th>
            <th class="text-center">Jumlah Keb. (zak)</th>
            <th class="text-center">Jumlah PP. (zak)</th>
        </tr>
    </thead>
    <tbody class='main-tbody'>
        <?php $data_ke1 = 1; ?>
        <?php foreach ($barang as $key1 => $value1) { ?>
            <tr class="tmp_header_barang" data-ke="<?php echo $data_ke1; ?>"
                data-kode-barang="<?php echo $key1; ?>">
                <td><a onclick="show_detail(this,1)"
                       data-ke="<?php echo $data_ke1; ?>">Detail <span
                            class="glyphicon glyphicon-chevron-right"></span></a></td>
                <td><?php echo $key1; ?></td>
                <td><?php echo $value1['nama_barang']; ?></td>
                <td><?php echo $value1['bentuk_barang']; ?></td>
                <td><?php echo $value1['sum_jml_kebutuhan_barang']; ?></td>
                <td><?php echo $value1['sum_jml_pp_barang']; ?></td>
            </tr>
            <tr class="hide header_barang" data-ke="<?php echo $data_ke1; ?>">
                <td></td>
                <td colspan="5">
                    <table class="table table-bordered text-center"
                           class="tbl-daftar-kandang">
                        <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center">Kandang</th>
                                <th class="text-center">Populasi</th>
                                <th class="text-center">Tgl LHK</th>
                                <th class="text-center">Umur (Minggu)</th>
                                <th class="text-center">Jumlah Kebutuhan (zak)</th>
                                <th class="text-center">Jumlah Stok (zak)</th>
                                <th class="text-center">Jumlah PP (zak)</th>
                                <th class="text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $data_ke2 = ($data_ke1) * 10; ?>
                            <?php foreach ($value1['data_kandang'] as $key2 => $value2) { ?>
                                <tr data-ke="<?php echo $data_ke2; ?>"
                                    data-sum-jumlah-pp="<?php echo $value2['sum_jml_pp_kandang']; ?>"
                                    class="data-kandang"
                                    data-lhk="<?php echo convert_month($value2['tgl_rhk_terakhir'], 1); ?>"
                                    data-no-reg="<?php echo $value2['no_reg']; ?>"
                                    data-umur="<?php echo $value2['sum_hari_kandang']; ?>">
                                    <td><a onclick="show_detail(this,2)"
                                           data-ke="<?php echo $data_ke2; ?>">Detail <span
                                                class="glyphicon glyphicon-chevron-right"></span></a></td>
                                    <td><?php echo $value2['nama_kandang']; ?></td>
                                    <td><?php echo $value2['populasi']; ?></td>
                                    <td><?php echo convert_month($value2['tgl_rhk_terakhir'], 1); ?></td>
                                    <td><?php echo $value2['range_umur']; ?></td>
                                    <td><?php echo $value2['sum_jml_kebutuhan_kandang']; ?></td>
                                    <td><?php echo $value2['stok_kandang']; ?></td>
                                    <td><?php echo $value2['sum_jml_pp_kandang']; ?></td>
                                    <td><input type="text" class="form-control keterangan"
                                               name="keterangan" placeholder="Keterangan"></td>
                                </tr>
                                <tr class="hide header_kandang" data-ke="<?php echo $data_ke2; ?>">
                                    <td></td>
                                    <td colspan="8">
                                        <?php if (($value2['ada'] == 2) || ($value2['ada'] == 3)) { ?>
                                            <div class='col-sm-6'>
                                                <table
                                                    class="table table-bordered text-center tbl-daftar-tgl-kebutuhan-jantan">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="3">Jantan</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Tanggal</th>
                                                            <th class="text-center">Jumlah Kebutuhan (zak)</th>
                                                            <th class="text-center">Jumlah PP (zak)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $data_ke3 = 1; ?>
                                                        <?php foreach ($value2['data_tgl_kebutuhan'] as $key3 => $value3) { ?>
                                                            <tr data-ke="<?php echo $data_ke3; ?>"
                                                                class="data-tgl-kebutuhan"
                                                                data-tgl-kebutuhan="<?php echo convert_month($key3, 1); ?>"
                                                                data-jml-forcast="<?php echo ''; ?>"
                                                                data-jml-performance="<?php echo $value3['j_jml_kebutuhan']; ?>"
                                                                data-jml-pp="<?php echo $value3['j_jml_pp']; ?>">
                                                                <td><?php echo convert_month($key3, 1); ?></td>
                                                                <td><?php echo $value3['j_jml_kebutuhan']; ?></td>
                                                                <td><input onkeyup='number_only(this)'
                                                                           onchange="kontrol_jumlah_pp(this)" type="text"
                                                                           value="<?php echo $value3['j_jml_pp']; ?>"
                                                                           class="form-control text-center jumlah_pp" name="jumlah_pp"
                                                                           placeholder="Jumlah PP (zak)"></td>
                                                            </tr>
                                                            <?php $data_ke3++; ?>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } ?>
                                        <?php if (($value2['ada'] == 1) || ($value2['ada'] == 3)) { ?>
                                            <div class='col-sm-6'>
                                                <table
                                                    class="table table-bordered text-center tbl-daftar-tgl-kebutuhan-betina">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" colspan="3">Betina</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center">Tanggal</th>
                                                            <th class="text-center">Jumlah Kebutuhan (zak)</th>
                                                            <th class="text-center">Jumlah PP (zak)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $data_ke3 = 1; ?>
                                                        <?php foreach ($value2['data_tgl_kebutuhan'] as $key3 => $value3) { ?>
                                                            <tr data-ke="<?php echo $data_ke3; ?>"
                                                                class="data-tgl-kebutuhan"
                                                                data-tgl-kebutuhan="<?php echo convert_month($key3, 1); ?>"
                                                                data-jml-forcast="<?php echo ''; ?>"
                                                                data-jml-performance="<?php echo $value3['b_jml_kebutuhan']; ?>"
                                                                data-jml-pp="<?php echo $value3['b_jml_pp']; ?>">
                                                                <td><?php echo convert_month($key3, 1); ?></td>
                                                                <td><?php echo $value3['b_jml_kebutuhan']; ?></td>
                                                                <td><input onkeyup='number_only(this)'
                                                                           onchange="kontrol_jumlah_pp(this)" type="text"
                                                                           value="<?php echo $value3['b_jml_pp']; ?>"
                                                                           class="form-control text-center jumlah_pp" name="jumlah_pp"
                                                                           placeholder="Jumlah PP (zak)"></td>
                                                            </tr>
                                                            <?php $data_ke3++; ?>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php $data_ke2++; ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <?php $data_ke1++; ?>
        <?php } ?>
    </tbody>
</table>