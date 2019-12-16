<div id="loading" class="hide">
    <p><img src="assets\images\ajax-loader.gif" /> Mohon Tunggu </p>
</div>
<?php $backColor = 'style="background-color:#E1F9FA"'; ?>
<?php foreach($cetak_form_lhk as $key=>$val): ?>
	<tr>
		<td class="link vert-align" <?php echo $backColor;?>><?php echo tglIndonesia($val['tgl_lhk'],'-',' ');?></td>
		<td class="link vert-align" <?php echo $backColor;?>><?php echo $val['flock'];?></td>
		<td class="link vert-align" <?php echo $backColor;?>><?php echo $val['kandang']?></td>
		<td class="link vert-align" <?php echo $backColor;?>><?php echo tglIndonesia($val['TGL_DOC_IN'],'-',' ');?></td>
		<td class="link vert-align" <?php echo $backColor;?>><?php echo $val['umur_hari'];?></td>
		<td class="link vert-align" <?php echo $backColor;?>>
			<?php if (isset($val['status_cetak']) && !empty($val['status_cetak'])): ?>
				<?php $date = explode(" ",$val['status_cetak']); ?>
				<i class="text-primary"><?php echo 'Dicetak pada ' . tglIndonesia($date[0],'-',' ') . ' ' . substr($date[1],0,8); ?></i>
			<?php elseif ($val['status_entri_lhk']==0 && $val['umur_hari']>1): ?>
				<i class="text-danger">LHK tanggal sebelumnya belum di entri</i>
			<?php else: ?>
				<div class="row">
					<div class="col-md-12">
						<button type="button" class="btn btn-primary btnCetakLHK" data-no_reg = "<?php echo $val['NO_REG']; ?>" data-farm ="<?php echo $kode_farm; ?>" data-nama_farm ="<?php echo $nama_farm; ?>" data-kandang="<?php echo $val['kandang']; ?>" data-kandang="<?php echo $val['kandang']; ?>" data-formatted_tgllhk="<?php echo tglIndonesia($val['tgl_lhk'],'-',' '); ?>" data-tgllhk="<?php echo $val['tgl_lhk']; ?>" data-tgllhk_sebelum="<?php echo $val['tgl_lhk_sebelum']; ?>" data-umur ="<?php echo $val['umur_hari']; ?>" data-flock ="<?php echo $val['flock']; ?>" onclick="CetakLHK.cetak_form_lhk(this)">Cetak</button>
					</div>
				</div>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>