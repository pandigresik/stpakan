<!--ganti pallet-->
<div class="col-md-12 new-line">
<table class="table table-bordered" id="tbl-ganti-hand-pallet">
    <thead>
        <tr>
            <th class="col-md-2">Pallet</th>
            <th class="col-md-2">Berat Timbang (Kg)</th>
            <th class="col-md-2">Jumlah (Sak)</th>
        </tr>
    </thead>
    <tbody>
        <?php
			foreach($kavling as $kv){
				echo '<tr onClick="Penerimaanreturpakanfarm.set_pallet(this)">
						<td class="kode">'.$kv['KODE_PALLET'].'</td>
						<td class="berat">'.$kv['BERAT_AVAILABLE'].'</td>
						<td class="jml_on_hand">'.$kv['JML_ON_HAND'].'</td>
					</tr>';
			}
		?>
    </tbody>
</table>
</div>