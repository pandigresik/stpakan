
<table class="table table-bordered table-striped" id="stok-gudang">
    <thead>
        <tr>
            <th class="text-center col-md-2">Kandang</th>
            <th class="text-center col-md-3">Jenis Pakan</th>
            <th class="text-center col-md-1">Stok</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stok_gudang as $key => $value) { ?>
            <tr>
                <td class='text-center col-md-2'><?php echo $value['nama_kandang']; ?></td>
                <td class='text-center col-md-3'><?php echo $value['nama_barang']; ?></td>
                <td class='text-center col-md-1'><?php echo $value['jml_stok']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>