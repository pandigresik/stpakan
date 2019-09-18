<div class="row">
	<div class="panel panel-primary">
		<div class="panel-heading">Laporan Harian Kandang - Penimbangan per Sekat</div>
		<div class="panel-body">
			<div class="col-md-12">
				<table id="lhk_penimbangan_sekat" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th class="vert-align">Sekat</th>
							<th class="vert-align">Jumlah</th>
							<th class="vert-align">BB (g)</th>
							<th class="vert-align">BB rata-rata(g)</th>
							<th class="vert-align">Keterangan</th>
						</tr>
					</thead>
					<tbody>
						<?php if (isset($jml_sekat) && !empty($jml_sekat)): ?>
							<?php foreach($jml_sekat as $key=>$val): ?>
								<tr>
									<td class="vert-align td_id_sekat" data-sekat="<?php echo $val ?>">Sekat <?php echo $val; ?></td>
									<td class="vert-align td_jumlah_sekat"><input readonly type="text" class="form-control input-sm inp-numeric" id="jumlah" data-mandatory=1 data-min="1" value="" onkeyup="EntriLHK.calcBBRata2(this);"></td>
									<td class="vert-align td_bb_sekat"><input readonly type="text" class="form-control input-sm inp-numeric" id="berat_badan" data-mandatory=1 data-min="1" value="" onkeyup="EntriLHK.calcBBRata2(this);"></td>
									<td class="vert-align td_bb_rata_sekat"><input disabled type="text" class="form-control input-sm inp-numeric" id="berat_badan_rata" value=""></td>
									<td class="vert-align td_keterangan_sekat"><input readonly type="text" class="form-control input-sm" id="keterangan" value=""></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>