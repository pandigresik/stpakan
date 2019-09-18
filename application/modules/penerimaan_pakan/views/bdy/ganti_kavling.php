
<div class="col-md-12">
        <button class="btn btn-default" type="button" onclick="set_ganti_kavling('','<?php echo $data_ke_detail; ?>','<?php echo $data_ke_detail_pakan; ?>')">Kavling Kosong</button>
</div>
<div class="col-md-12 new-line">
<table class="table table-bordered" id="tbl-ganti-kavling">
    <thead>
        <tr>
            <th class="col-md-2">Kavling</th>
            <th class="col-md-2">Berat Timbang (Kg)</th>
            <th class="col-md-2">Jumlah (Sak)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $key => $value) { ?>
            <tr onclick="set_ganti_kavling(this,'<?php echo $data_ke_detail; ?>','<?php echo $data_ke_detail_pakan; ?>')">
                <td class="gk_no_kavling"><?php echo $key; ?></td>
                <td class="gk_berat"><?php echo $value['berat']; ?></td>
                <td class="gk_sak"><?php echo $value['sak']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
</div>