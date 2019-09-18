<?php if(empty($message)) { 
	$header_do = $do[0];
	?>
<div class="section">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-3">No. PO</div>
					<div class="col-md-4"> : <?php echo $header_do['no_op'] ?></div>
				</div>
				<div class="row">
					<div class="col-md-3">Tanggal kirim</div>
					<div class="col-md-4"> : <?php echo tglIndonesia($header_do['tgl_kirim'],'-',' ') ?></div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-2">Ekspedisi</div>
					<div class="col-md-6"> : <?php echo $header_do['nama_ekspedisi'] ?></div>
				</div>
				<div class="row">
					<div class="col-md-2">Tujuan</div>
					<div class="col-md-4"> : <?php echo $header_do['nama_farm'] ?></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="section">
	<div class="container col-md-12">
		<div class="row">
			<div class="col-md-12">
				<table class="table">
					<thead>
						<tr>
							<th>Kode Pakan</th>
							<th>Nama Pakan</th>
							<th>Bentuk</th>
							<th>Kuantitas <br /> (Zak)</th>
							<th>Berat <br /> (Kg)</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$tot_sak = $tot_berat = 0; 
						foreach($do as $perdo){
							echo '<tr>'.
									'<td>'.$perdo['kode_barang'].'</td>'.
									'<td>'.$perdo['nama_barang'].'</td>'.
									'<td>'.$perdo['bentuk_barang'].'</td>'.
									'<td>'.$perdo['jml_kirim'].'</td>'.
									'<td>'.$perdo['berat'].'</td>'.
								'</tr>';
							$tot_sak += $perdo['jml_kirim'];
							$tot_berat += $perdo['berat'];
						}?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3"></td>
							<td><?php echo $tot_sak ?></td>
							<td><?php echo $tot_berat ?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
<?php } 
else{
	echo '<div>'.$message.'</div>';
}

?>