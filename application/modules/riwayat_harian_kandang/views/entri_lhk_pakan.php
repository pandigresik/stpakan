<div class="row">
	<div class="panel panel-primary">
		<div class="panel-heading">Laporan Harian Kandang - Pakan</div>
		<div class="panel-body">
			<div class="col-md-12">
				<table id="lhk_pakan" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th class="vert-align" colspan="4">Jenis<br>Kelamin</th>
							<th class="vert-align" colspan="4">Nama Pakan</th>
							<th class="vert-align" colspan="4">Terpakai (Sak)</th>
						</tr>
					</thead>
					<tbody>
				
						<?php if (isset($pakan_pakai) && !empty($pakan_pakai)): ?>
							<?php foreach($pakan_pakai as $key=>$val): ?>
								<tr>
									<td class="vert-align td_jenis_kelamin" colspan="4"><?php echo 'Campur'; ?></td>
									<td class="vert-align td_nama_pakan" colspan="4" data-kode_barang="<?php echo $key; ?>"><?php echo $val['nama_barang']; ?></td>
									<td class="vert-align td_sak_pakai" colspan="4"><input type="text" data-max="<?php echo $val['jml_stok']; ?>" onchange="EntriLHK.validatorMaxPakai(this)" class="form-control input-sm inp-numeric"  id="inp_sak_terpakai" data-mandatory=1 value="0"></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>