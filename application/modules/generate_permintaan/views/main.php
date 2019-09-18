<div class="panel panel-default">
    <div class="panel-heading">Generate Permintaan</div>
    <div class="panel-body">
        <!--div>
                <a href="#generate_permintaan/main/baru"
                        class="btn btn-default">Baru</a>
        </div-->
        <div id="order-kandang-table" class="new-line">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tgl Kirim</th>
                        <th>Tgl Kebutuhan</th>
                        <th>Qty PP</th>
                        <th>Generate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $key => $value) { ?>
                        <tr>
                            <td><?php echo convert_month($value['tgl_kirim'], 1); ?></td>
                            <td><?php echo convert_month($value['tgl_keb_awal'], 1) . ' s/d ' . convert_month($value['tgl_keb_akhir'], 1); ?></td>
                            <td><?php echo $value['jumlah_kebutuhan']; ?></td>
                            <th><a data-kode-farm='<?php echo $value['kode_farm']; ?>'
                                   data-tanggal-kirim='<?php echo convert_month($value['tgl_kirim'], 1); ?>'
                                   data-tanggal-kebutuhan-awal='<?php echo convert_month($value['tgl_keb_awal'], 1); ?>'
                                   data-tanggal-kebutuhan-akhir='<?php echo convert_month($value['tgl_keb_akhir'], 1); ?>'
                                   href='#' class='link' onclick='generate(this)'>Generate</a></th>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div id="contain-daftar-barang" class=''></div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css"
      href="assets/css/generate_permintaan/generate_permintaan.css">
<script type="text/javascript"
src="assets/js/generate_permintaan/generate_permintaan.js"></script>