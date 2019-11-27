<style>
	.list_hand_pallet{color:#777;}
	.list_hand_pallet:hover{color:#000;}
</style>
<!--ganti hand pallet-->
<div class="col-md-12 new-line">
<table class="table table-bordered" id="tbl-ganti-hand-pallet">
    <thead>
        <tr>
            <th class="col-md-2">Hand Pallet</th>
            <th class="col-md-2">Berat Timbang (Kg)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data_hand_pallet as $key => $value) { ?>
            <!--<tr onclick="set_ganti_hand_pallet(this,'<?php echo $data_ke_detail; ?>','<?php echo $data_ke_detail_pakan; ?>')"--> 
            <tr class="list_hand_pallet" onClick="Penerimaanreturpakanfarm.set_hand_pallet(this)" data-beratpallet="<?=$value['berat']?>">
                <td class="ghp_kode_hand_pallet"><?php echo $value['kode_hand_pallet']; ?></td>
                <td class="ghp_berat"><?php echo $value['berat']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
</div>