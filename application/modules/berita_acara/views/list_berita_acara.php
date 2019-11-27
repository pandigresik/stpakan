
<table class="table table-bordered" id="list-berita-acara">
    <thead>
        <tr>
            <th>No. Berita Acara</th>
            <th>No. Penerimaan</th>
            <th>No. Surat Jalan</th>
            <th>Tipe Berita Acara</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $key => $value) { ?>
            <tr ondblclick="pilih_berita_acara(this)">
                <td class="no-ba"><?php echo $value['NO_BA']; ?></td>
                <td class="no-penerimaan"><?php echo $value['NO_PENERIMAAN']; ?></td>
                <td class="no-sj"><?php echo $value['kode_surat_jalan']; ?></td>
                <td class="tipe-ba" data-tipe-ba="<?php echo $value['TIPE_BA']; ?>"><?php echo $value['TIPE_BA_LABEL']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>