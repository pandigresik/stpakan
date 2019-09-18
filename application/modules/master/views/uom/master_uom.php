
<table class="table table-bordered table-striped" id="master-base-uom">
    <thead>
        <tr>
            <th class="col-md-2">Satuan</th>
            <th class="col-md-2">Deskripsi</th>
            <th class="col-md-2">Satuan Dasar</th>
            <th class="col-md-2">Konversi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($uom as $key => $value) { ?>
            <tr ondblclick="pilih_uom(this)">
                <td class='satuan'><?php echo $value['UOM']; ?></td>
                <td class='deskripsi'><?php echo $value['DESKRIPSI']; ?></td>
                <td><?php echo $value['DESKRIPSI_BASE_UOM']; ?></td>
                <td><?php echo $value['KONVERSI']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>