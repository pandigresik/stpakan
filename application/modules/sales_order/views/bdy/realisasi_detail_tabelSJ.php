<div class="panel panel-primary">
<div class="panel-heading">Detail Pengeluaran - DO : <?php echo $surat_jalan->no_do ?> ( <?php echo empty($surat_jalan->tgl_realisasi) ? tglIndonesia(date('Y-m-d'),'-',' ' ) : tglIndonesia($surat_jalan->tgl_realisasi,'-',' ' ) ?>) </div>
<div class="panel-body">
	<div class="col-md-6 col-md-offset-3">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>No. Kendaraan</th>
					<th>Jenis Barang</th>
					<th>Jumlah</th>
				</tr>
			</thead>
			<?php 
				if(!empty($detail)){
					$nomerSJ = !empty($surat_jalan->tgl_realisasi) ? '<br /> (No. SJ '.$surat_jalan->no_sj.' )' : '';					
					$no = 0;
					$jmlBaris = count($detail);
					foreach($detail as $dt){						
						echo '<tr>';
						if(!$no){
							echo '<td rowspan="'.$jmlBaris.'">'.$surat_jalan->no_kendaraan.' '.$nomerSJ.'</td>';
						}
							
						echo '<td class="nama_barang">'.$barang[$dt->kode_barang]['nama_barang'].'</td>
							<td class="jumlah">'.angkaRibuan($dt->jumlah).'</td>
						</tr>';
						$no++;
					}
				}
			?>
			</table>
	</div>
</div>
</div>

