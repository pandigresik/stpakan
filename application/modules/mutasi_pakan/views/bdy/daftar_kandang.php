
<table class="table table-bordered" id="tabel_daftar_kandang">
    <thead>
        <tr>
            <th class="kandang">Kandang</th>
            <th class="umur">Umur (hari)</th>
            <th class="dh">DH</th>
            <th class="fcr">FCR</th>
            <th class="ip">IP</th>
            <th class="stok_gudang">Stok Akhir Gudang</th>
            <th class="stok_kandang">Stok Akhir Kandang</th>
            <th class="aksi"><div class="form-inline"><span>Aksi</span> 
        <span><input type="text" value="<?php echo $kuantitas_pemberian_pakan; ?>" style="width:50px;" name="aksi" id="aksi" class="form-control" readonly></span>
    </div>
</th>
</tr>
</thead>
<tbody>
    <?php if (count($daftar_kandang) > 0) { ?>
        <?php foreach ($daftar_kandang as $key => $value) { ?>
            <tr>
                <td class="kandang" data-no-reg="<?php echo $value['no_reg']; ?>"><?php echo $value['kode_kandang']; ?></td>
                <td class="umur"><?php echo $value['umur']; ?></td>
                <td class="dh <?php echo ($value['dh_red'] == 1) ? 'red_color' : ''; ?>"><?php echo number_format($value['dh'], 2,'.',''); ?></td>
                <td class="fcr <?php echo ($value['fcr_red'] == 1) ? 'red_color' : ''; ?>"><?php echo number_format($value['fcr'], 3,'.',''); ?></td>
                <td class="ip <?php echo ($value['ip_red'] == 1) ? 'red_color' : ''; ?>"><?php echo number_format($value['ip'], 0,'.',''); ?></td>
                <td class="stok_gudang" data-berat-stok-gudang="<?php echo number_format($value['berat_stok_gudang'], 3,'.',''); ?>"><?php echo $value['stok_gudang']; ?></td>
                <td class="stok_kandang" data-berat-stok-kandang="<?php echo number_format($value['berat_stok_kandang'], 3,'.',''); ?>"><?php echo $value['stok_kandang']; ?></td>
                <td class="aksi">
                    <div class="form-inline">
                        <span>
                            <label>
                                <input type="checkbox" class="checkbox" value="<?php echo ($value['exist']==1) ? '1' : '0';?>" onchange="checkbox_kandang(this)" <?php echo ($value['exist']==1) ? 'checked' : '';?>>

                            </label>
                        </span>
                        <input value="<?php echo empty($value['jml_terima']) ? '' : $value['jml_terima'];?>" style="width:50px;" name="aksi" class="aksi <?php echo ($value['exist']==1) ? '' : 'hide';?> text-center" onchange="kontrol_aksi(this)">

                    </div>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="8">Tidak ada data.</td>
        </tr>
    <?php } ?>
</tbody>
</table>