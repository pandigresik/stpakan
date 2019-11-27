<div class="panel panel-primary">
	<div class="panel-heading"><div class="row"><div class="col-md-10">Rincian Performance DOC In</div> <?php echo isset($tombolbapd) ?$tombolbapd : ''  ?></div></div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-11">
				<table class="table table-bordered  custom_table" data-table="bapdoc">
					<thead>
						<tr>
							<th colspan="4">Jumlah DOC</th>
							<th rowspan="2">BB <br /> Rata - rata</th>
							<th rowspan="2">Uniformity (%)</th>
						</tr>
						<tr>
							<th>Box</th>
							<th>Ekor</th>
							<th>Afkir</th>
							<th>Stok Awal</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<?php
								$jml_afkir = isset($jmlafkir) ? formatAngka($jmlafkir,0) : '<input name="jml_afkir" type="text" class="control-form number" onchange="BAPD.updateStokAwal(this)" />';
								$stokawal = isset($stokawal) ? $stokawal : $jmlekor ;
								$bbrata2 = isset($bbrata2) ? formatAngka($bbrata2,2) : '<input name="bb_rata2" type="text" class="control-form number" />';
								$uniformity = isset($uniformity) ? formatAngka($uniformity,2) : '<input name="uniformity" type="text" class="control-form number" />';
							?>
							<td class="number jmlbox"><?php echo formatAngka($jmlbox,0) ?></td>
							<td class="number jmlekor"><?php echo formatAngka($jmlekor,0) ?></td>
							<td class="number jmlafkir"><?php echo $jml_afkir ?></td>
							<td class="number stokawal"><?php echo formatAngka($stokawal,0) ?></td>
							<td class="number rata-rata"><?php echo $bbrata2 ?></td>
							<td class="number uniformity"><?php echo $uniformity ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
