<div class="panel panel-default">
    <div class="panel-heading">LHK - Analisa Performance Kandang</div>
    <div class="panel-body">
        <div>
            <a href="#analisa_performance_kandang/main/baru"
               class="btn btn-default">Baru</a>
        </div>
        <div id="order-kandang-table" class="new-line">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tgl Kirim</th>
                        <th>Tgl Kebutuhan</th>
                        <th>Qty PP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $key => $value) { ?>
                        <tr>
                            <td data-no-order='<?php echo $value['no_order']; ?>'
                                data-status='<?php echo $value['status_order']; ?>'
                                data-kode-farm='<?php echo $value['kode_farm']; ?>'
                                data-tgl-kebutuhan-awal='<?php echo convert_month($value['tgl_keb_awal'], 1); ?>'
                                data-tgl-kebutuhan-akhir='<?php echo convert_month($value['tgl_keb_akhir'], 1); ?>'><a
                                    href='#' class='link' onclick='get_data_detail_order(this)'><?php echo convert_month($value['tgl_kirim'], 1); ?></td>
                            <td><?php echo convert_month($value['tgl_keb_awal'], 1) . ' s/d ' . convert_month($value['tgl_keb_akhir'], 1); ?></td>
                            <td><?php echo $value['jumlah_kebutuhan']; ?></td>
                            <td><?php echo $value['status_order_label']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css"
      href="assets/css/analisa_performance_kandang/analisa.css">
<script type="text/javascript"
src="assets/js/analisa_performance_kandang/analisa.js"></script>