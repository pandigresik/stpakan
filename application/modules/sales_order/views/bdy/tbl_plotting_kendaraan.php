<table class="table table-bordered custom_table">
	<thead>
		<tr>
			<th width="1%"><input type="checkbox" value="" onclick="plottingKendaraan.clickAll(this);" /></th>
			<th width="10%">No. SO</th>
			<th width="4%">Tanggal SO</th>
			<th width="10%">No. DO</th>
			<th width="10%">No. SJ</th>
			<th width="4%">Farm Asal</th>
			<th width="10%">Pelanggan</th>
			<th width="10%">Alamat Pelanggan</th>
			<th width="10%">No. Telp Pelanggan</th>
			<th width="10%">No. Kendaraan</th>
			<th width="10%">Sopir</th>
			<th width="10%">No. Telp Sopir</th>
			<th width="1%"></th>
		</tr>
	</thead>
	<tbody id="main_tbody">
		<?php foreach ($list_sales_order as $key => $val): ?>
		<?php //$val['limited_timeline_plotting'] = '0' ?>
			<tr class="<?php echo ((!(isset($val['no_sj']) && !empty($val['no_sj']))) && $val['limited_timeline_plotting']=='0') ? 'bg-danger' : ''; ?>" data-nomor_so = "<?php echo $val['no_so']; ?>" data-nomor_do = "<?php echo $val['no_do']; ?>" data-kode_farm="<?php echo $val['KODE_FARM']; ?>" onclick="plottingKendaraan.highlight(this);">
				<td>
					<?php if ((!(isset($val['no_sj']) && !empty($val['no_sj']))) && $val['limited_timeline_plotting']=='0'): ?>
						<input type="checkbox" value="" onclick="plottingKendaraan.enableSimpan(this);" />
					<?php endif; ?>
				</td>
				<td class="nomor_so" rowspan="~~~"><?php echo $val['no_so']; ?></td>
				<td class="tgl_so"><?php echo $val['tgl_so']; ?></td>
				<td class="nomor_do"><?php echo $val['no_do']; ?></td>
				<td class="nomor_sj"><?php echo $val['no_sj']; ?></td>
				<td data-kode_farm="<?php echo $val['KODE_FARM']; ?>" class="farm_asal"><?php echo $val['NAMA_FARM']; ?></td>
				<td data-kode_pelanggan="<?php echo $val['KODE_PELANGGAN']; ?>" class="nama_pelgn"><?php echo $val['NAMA_PELANGGAN']; ?></td>
				<td data-kota_pelanggan="<?php echo $val['kota_pelanggan']; ?>" class="almt_pelgn"><?php echo $val['alamat_pelanggan']; ?></td>
				<td class="telp_pelgn"><?php echo $val['telp_pelanggan']; ?></td>
				<?php
				if (empty($val['no_sj']) && $val['limited_timeline_plotting'] == '1'){ ?>
					<td colspan="3" style="color:red">
						Plotting kendaraan melebihi batas timeline (<?php echo tglIndonesia($val['tgl_batas_so'],'-',' ').' '.$val['max_time'] ?>)
					</td>
				<?php }else{?>
					<td class="nomor_kendaraan">
						<?php if (isset($val['no_sj']) && !empty($val['no_sj']) || $val['limited_timeline_plotting']=='1'): ?>
							<?php echo $val['no_kendaraan']; ?>
						<?php else: ?>
							<input data-mandatory="1" class="form-control" type="text" name="nomor_kendaraan" onkeyup="plottingKendaraan.upperCaseWord(this);"/>
						<?php endif; ?>
					</td>
					<td class="nama_sopir">
						<?php if (isset($val['no_sj']) && !empty($val['no_sj']) || $val['limited_timeline_plotting']=='1'): ?>
							<?php echo $val['nama_sopir']; ?>
						<?php else: ?>
							<input data-mandatory="1" class="form-control" type="text" name="nama_sopir"/>
						<?php endif; ?>
					</td>
					<td class="telp_sopir">
						<?php if (isset($val['no_sj']) && !empty($val['no_sj']) || $val['limited_timeline_plotting']=='1'): ?>
							<?php echo $val['no_telp_sopir']; ?>
						<?php else: ?>
							<input data-mandatory="1" class="form-control" type="text" name="telp_sopir"/>
						<?php endif; ?>
					</td>
				<?php }?>
				<td>
					<?php if (!(isset($val['no_sj']) && !empty($val['no_sj']) || $val['limited_timeline_plotting']=='1')): ?>
						<div class="container" style="width:35px; padding: 0;">
							<span aria-hidden="true" class="impor glyphicon glyphicon-plus btn-collapse hide" onclick="plottingKendaraan.addDtlSopir(this);"></span>
							<span aria-hidden="true" class="impor glyphicon glyphicon-minus btn-collapse hide" onclick="plottingKendaraan.removeDtlSopir(this);"></span>
						</div>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
	</tfoot>
</table>
