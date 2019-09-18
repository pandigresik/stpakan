            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="r-kode-pakan">Kode Pakan</th>
                        <th class="r-nama-pakan">Nama Pakan</th>
                        <th class="r-tgl-jam-pengambilan">Tgl/Jam Pengambilan</th>
                        <th class="r-jml-yg-diminta">Jumlah Yang Diminta</th>
                        <th class="r-hutang-sak-kosong">Hutang Sak Kosong (Kembali)</th>
                        <th class="r-jumlah-terima">Jumlah Terima</th>
                        <th class="r-stok-gudang">Sisa Stok Gudang</th>
                        <th class="r-user-gudang">User Gudang</th>
                        <th class="r-user-kandang">User Kandang</th>
                        <th class="r-keterangan">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayat as $key => $value) { ?>
                        <tr>
                            <td class="r-kode-pakan"><?php echo $value['kode_pakan']; ?></td>
                            <td class="r-nama-pakan"><?php echo $value['nama_pakan']; ?></td>
                            <td class="r-tgl-jam-pengambilan"><?php echo empty($value['jml_yg_diminta']) ? '-' : date('d M Y H:i',strtotime($value['tanggal_ambil'])); ?></td>
                            <!--td class="r-jml-yg-diminta"><?php #echo $value['jml_yg_diminta']; ?></td-->
                            <td class="r-jml-yg-diminta"><?php echo $value['jml_yg_diminta']+$value['jml_hutang_sak_kosong']; ?></td>
                            <td class="r-hutang-sak-kosong"><?php echo $value['jml_hutang_sak_kosong']; ?></td>
                            <td class="r-jumlah-terima"><?php echo $value['jml_terima']; ?></td>
                            <td class="r-stok-gudang"><?php echo $value['jml_sisa_stok_gudang']; ?></td>
                            <td class="r-user-gudang"><?php echo $value['user_gudang']; ?></td>
                            <td class="r-user-kandang"><?php echo $value['user_kandang']; ?></td>
                            <td class="r-keterangan"><?php echo $value['keterangan']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
