
<table class="table table-bordered" id="list-surat-jalan">
    <thead>
        <tr>
            <th>No Surat Jalan</th>
            <th>No. OP</th>
            <th>Jumlah SJ (Zak)</th>
            <th>Jumlah Aktual (Zak)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $key => $value) { ?>
            <tr ondblclick="pilih_surat_jalan(this)">
                <td class="no-sj"><?php echo $value['kode_surat_jalan']; ?></td>
                <td class="no-op"><?php echo $value['no_op']; ?></td>
                <td class="jml-sj"><?php echo $value['jml_sj']; ?></td>
                <td class="jml-aktual"><?php echo $value['jml_aktual']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>