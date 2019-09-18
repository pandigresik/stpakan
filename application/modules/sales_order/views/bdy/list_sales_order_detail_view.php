<div class="panel panel-primary">
	<div class="panel-heading">Detail SO <?php echo $no_so ?></div>
	<div class="panel-body">				
		<?php 
			if(!empty($no_ref)){
				echo 'SO pengganti dari <span class="link_span">'.$no_ref.'</span>';
			}
		?>
		<table class="table table-bordered" id="detailTable" style="width:900px">
			<thead>
				<tr>
					<th width="50%">Jenis Barang</th>
					<th class="text-center" width="10%">Jumlah</th>
					<th class="text-center" width="10%">Satuan</th>
					<th class="text-center" width="15%">Harga<br>(Rp)</th>
					<th class="text-center" width="15%">Total Harga<br>(Rp)</th>
				</tr>
			</thead>
			<tbody id="main_tbody">
				<?php 
					foreach($detail_do as $do){
						echo '<tr>
							<td>'.$barang[$do->kode_barang]['nama_barang'].'</td>
							<td class="text-center">'.angkaRibuan($do->jumlah).'</td>
							<td class="text-center">Sak</td>
							<td class="text-center">'.angkaRibuan($do->harga_jual).'</td>
							<td class="text-center">'.angkaRibuan($do->harga_total).'</td>
						</tr>';
					}
				?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>

