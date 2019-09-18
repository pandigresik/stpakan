<?php foreach ($list_plotting as $key => $value):?>
	<tr>
		<td class="text-center" style="vertical-align: middle" data-kode_pegawai="<?php echo $value['KODE_PEGAWAI']; ?>" data-nama_pegawai="<?php echo $value['NAMA_PEGAWAI']; ?>">
			<?php echo $value['NAMA_PEGAWAI']; ?>
		</td>
		<td class="text-center" style="vertical-align: middle" data-grup_pegawai="<?php echo $value['GRUP_PEGAWAI']; ?>">
			<?php echo $value['DESKRIPSI']; ?>
		</td>
		<td class="text-center" style="vertical-align: middle">
			<?php
				if ($value['DESKRIPSI'] == 'Koordinator Pengawas Produksi')
					$readonly = '';
				else
					$readonly = 'readonly';
					 
				if ($complete_ploting)
					$disabled = 'disabled';
				else
					$disabled = '';			
			?>
			<input type="text" class="form-control" name="flock" onchange="plottingPelaksana.scanRFID(this)" data-jenis="flock" data-otomatis_kode_flok="<?php echo isset($automatic_flock_plotting[0]['FLOK_BDY']) && !empty($automatic_flock_plotting[0]['FLOK_BDY']) ? $automatic_flock_plotting[0]['FLOK_BDY'] : null;  ?>" data-deskripsi="<?php echo $value['DESKRIPSI']; ?>" <?php echo $readonly; ?> <?php echo $disabled; ?> value="<?php echo $value['KODE_FLOK'] ?>" />
				<?php if ($complete_ploting): ?>
					<div><span>Flock :</span> <input type="text" name="tags_flock_min_close_<?php echo $key; ?>" placeholder="Tags" class="tm-input_min_close hide"/></div>
				<?php else: ?>
					<?php if(empty($readonly)): ?>
						<div><span>Flock :</span> <input type="text" name="tags_flock_<?php echo $key; ?>" placeholder="Tags" class="tm-input hide"/></div>
					<?php else: ?>
						<div><span>Flock :</span> <input type="text" name="tags_flock_<?php echo $key; ?>" placeholder="Tags" class="tm-input_min_close hide"/></div>
					<?php endif; ?>
					
				<?php endif; ?>
			
		</td>
		<td class="text-center" style="vertical-align: middle">
			<?php
				if ($value['DESKRIPSI'] == 'Koordinator Pengawas Produksi')
					$readonly = 'readOnly';
				else {
					if(!empty($value['KODE_KANDANG'])){
						$readonly = 'readonly';
					}else{
						$readonly = '';
					}
				} 
							
				if ($complete_ploting)
					$disabled = 'disabled';
				else
					$disabled = '';	
			?>
			<input type="text" class="form-control" name="kandang" onchange="plottingPelaksana.scanRFID(this)" data-jenis="kandang" data-deskripsi="<?php echo $value['DESKRIPSI']; ?>" <?php echo $readonly; ?> <?php echo $disabled; ?> value="<?php echo $value['KODE_KANDANG'] ?>" />
			<?php if ($complete_ploting): ?>
				<div><span>Kandang :</span> <input type="text" name="tags_kandang_<?php echo $key; ?>" placeholder="Tags" class="tm-input_min_close hide"/></div>
			<?php else: ?>
				<?php if (!empty($readonly)): ?>
					<div><span>Kandang :</span> <input type="text" name="tags_kandang_<?php echo $key; ?>" placeholder="Tags" class="tm-input_min_close hide"/></div>	
				<?php else: ?>	
					<div><span>Kandang :</span> <input type="text" name="tags_kandang_<?php echo $key; ?>" placeholder="Tags" class="tm-input hide"/></div>	
				<?php endif; ?>	
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
