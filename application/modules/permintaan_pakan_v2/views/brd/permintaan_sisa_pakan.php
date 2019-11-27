<div class="col-md-8">
	<div class="col-md-3" data-tgl_kirim="<?php echo tglIndonesia($tgl_kirim,'-',' ') ?>">Periode Kebutuhan</div>
	<div class="col-md-3">
		<div class="form-group">
			<div class="input-group date">
				<input type="text" readonly="" name="tgl_keb_awal" class="form-control" value="<?php echo tglIndonesia($kebutuhan_awal,'-',' ')?>">
				<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
			</div>
		</div>
	</div>
	<div class="col-md-1 vcenter">s.d.</div>    
	<div class="col-md-3">
		<div class="form-group">
			<div class="input-group date">
				<input type="text" readonly="" name="tgl_keb_akhir" class="form-control"  value="<?php echo tglIndonesia($kebutuhan_akhir,'-',' ')?>">
				<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>	
			</div>
		</div>
	</div>
</div>
<div class="col-md-12">
	<?php 
		$jenis_kelamin_arr = array('J','B');
		foreach($perkandang as $noreg => $kandang){
		echo '<div data-noreg="'.$noreg.'" class="sisa_konsumsi_pakan" style="display:none">';
		echo '<div class="row">';
			foreach($jenis_kelamin_arr as $jk){
				echo '<div class="col-md-6">';
				echo '<div>Jenis Kelamin : '.convertKode('jenis_kelamin',$jk).'</div>';
				echo '<table class="table table-bordered">
						<thead>
							<tr>
								<th>Kode Barang</th>
								<th>Nama Barang</th>
								<th>Sisa Hutang PP</th>
								<th>Hutang Retur Sak </th>
								<th>Sisa Kebutuhan</th>
							</tr>
						</thead>
						<tbody>';
				if(isset($kandang[$jk])){
				$jenis_kelamin = $kandang[$jk];
					foreach($jenis_kelamin as $kb => $barang){
						echo '<tr>
								<td>'.$kb.'</td>
								<td>'.$barang['nama_barang'].'</td>
								<td class="number">'.number_format ( (double)$barang['hutang_pp_sebelumnya'],2, ',', '.' ).'</td>
								<td class="number">'.number_format ( (double)$barang['hutang_retur_sak'],0, ',', '.' ).'</td>
								<td class="number">'.number_format($barang['sisa_kebutuhan'],0).'</td>	
						</tr>';
						
					}
				}
				echo '
						</tbody>
					</table>';
				echo '</div>';
			}
		echo '</div>';	
		echo '</div>';	

		}

	?>
	
</div>