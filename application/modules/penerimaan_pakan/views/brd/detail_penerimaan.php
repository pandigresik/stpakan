<?php if(!empty($list[0]['no_ba'])){ ?>
<div class="col-md-12">
        <button class="btn btn-default" type="button" data-no-ba="<?php echo $list[0]['no_ba']; ?>" onclick='print_view_berita_acara(this)'>Print Preview Berita Acara</button>
        
</div>
<?php } ?>
<div class="col-md-12 new-line">
<table class="table table-bordered" id="tbl-detail-penerimaan">
    <thead>
        <tr>
            <th class="col-md-2">Kode Barang</th>
            <th class="col-md-2">Nama Barang</th>
            <th class="col-md-2">Bentuk</th>
            <th class="col-md-1">Berat SJ (Kg)</th>
            <th class="col-md-1">Jumlah SJ (Zak)</th>
            <th class="col-md-2">Terima Baik (Zak)</th>
            <th class="col-md-2">Terima Baik (Kg)</th>
            <th class="col-md-2">Terima Rusak (Zak)</th>
            <th class="col-md-2">Terima Rusak (Kg)</th>
            <th class="col-md-2">Jumlah Kurang (Zak)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $key => $value) { ?>
            <tr>
                <td><?php echo $value['kode_barang']; ?></td>
                <td><?php echo $value['nama_barang']; ?></td>
                <td><?php echo $value['bentuk_barang']; ?></td>
                <td><?php echo $value['berat_sj']; ?></td>
                <td><?php echo $value['jumlah_sj']; ?></td>
                <td><?php echo $value['terima_baik_zak']; ?></td>
                <td><?php echo $value['terima_baik_kg']; ?></td>
                <td><?php echo $value['terima_rusak_zak']; ?></td>
                <td><?php echo $value['terima_rusak_kg']; ?></td>
                <td><?php echo $value['jumlah_kurang_zak']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
</div>