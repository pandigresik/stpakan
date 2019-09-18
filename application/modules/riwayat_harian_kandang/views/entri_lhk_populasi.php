<div class="row">
	<div class="panel panel-primary">
		<div class="panel-heading">Laporan Harian Kandang - Populasi</div>
		<div class="panel-body">
			<div class="col-md-12">
				<table id="lhk_populasi" class="table table-bordered table-condensed">
					<thead>
						<tr>
							
							<th class="vert-align col-md-1" colspan="6">Pengurangan Mati</th>
							<th class="vert-align col-md-1" colspan="6">Pengurangan Afkir</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="6" class="td_pengurangan_mati"><input type="text" onchange="EntriLHK.validatorMaxPengurang(this)" data-max="<?php echo $jumlah_ayam ?>" class="form-control input-sm inp-numeric" data-mandatory=1 id="inp_pengurangan_mati" value="" data-min="0"></td>
							<td colspan="6" class="td_pengurangan_afkir"><input type="text" onchange="EntriLHK.validatorMaxPengurang(this)" data-max="<?php echo $jumlah_ayam ?>" class="form-control input-sm inp-numeric" data-mandatory=1 id="inp_pengurangan_afkir" value="" data-min="0"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>