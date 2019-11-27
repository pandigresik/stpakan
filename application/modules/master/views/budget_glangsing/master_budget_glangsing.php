
<table class="table table-bordered table-striped" id="master-base-pallet">
    <thead>
        <tr>
            <th class="">Satuan</th>
            <th class="">Deskripsi</th>
            <th class="">Satuan Dasar</th>
            <th class="">Konversi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pallet as $key => $value) { ?>
            <tr>
                <td class='id_pallet'><?php echo $value['KODE_PALLET']; ?></td>
                <td class='tara'><?php echo $value['BERAT']; ?></td>
                <td class='jenis'><?php echo $value['JENIS']; ?></td>
                <td class='siklus'><?php echo $value['PERIODE_SIKLUS']; ?></td>
                <td class='keterangan'><input class="text-center input_keterangan" name="input_keterangan" style="width:150px;" value=""></td>
            </tr>
        <?php } ?>
    </tbody>
</table>