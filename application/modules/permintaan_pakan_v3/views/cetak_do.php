<?php 
	foreach($list_do as $do){
?>
<div class="section" style="border:1px solid black;margin-bottom : 8px">
	<div class="container">
		<div class="row">
			<div class="col-md-5">
				<div class="text-center">No. DO : <?php echo $do[0]['no_do']?></div>
				<fieldset>
					<legend>Pengirim</legend>
					<table class="table">
						<tbody>
							<tr>
								<td>Ekspedisi</td>
								<td><?php echo $do[0]['nama_ekspedisi']?></td>
							</tr>
							<tr>
								<td>Alamat</td>
								<td><?php echo $do[0]['alamat_ekspedisi']?></td>
							</tr>
							<tr>
								<td>Tanggal Pengiriman</td>
								<td><?php echo $do[0]['tgl_kirim']?></td>
							</tr>
							<tr>
								<td>Nopol</td>
								<td></td>
							</tr>
							<tr>
								<td>Sopir</td>
								<td></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col-md-5">
				<div class="text-center">No. OP  : <?php echo $do[0]['no_op']?></div>
				<fieldset>
					<legend>Penerima</legend>
					<table class="table">
						<tbody>
							<tr>
								<td>Farm</td>
								<td><?php echo $do[0]['nama_farm']?></td>
							</tr>
							<tr>
								<td>Alamat</td>
								<td><?php echo $do[0]['alamat_farm']?></td>
							</tr>

						</tbody>
					</table>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div>Detail Pakan</div>
			<table class="table col-md-5" style="max-width: 80%">
				<thead>
					<tr>
						<th>Nama Barang</th>
						<th>Kuantitas <br />(Sak)
						</th>
						<th>Berat <br />(Kg)
						</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$tot_sak = $tot_berat = 0;
						foreach($do as $d){
							echo '<tr>'.
									'<td>'.$d['nama_barang'].'</td>'.
									'<td>'.$d['jml_kirim'].'</td>'.
									'<td>'.$d['berat'].'</td>'.
								'</tr>';	
							$tot_sak += $d['jml_kirim'];
							$tot_berat += $d['berat'];
							}

					?>
				</tbody>
				<tfoot>
					<tr>
						<td>Total</td>
						<td><?php echo $tot_sak ?></td>
						<td><?php echo $tot_berat ?></td>
					</tr>
				</tfoot>
			</table>
			<table class="table"  style="max-width: 80%">
				<tbody>
					<tr>
						<td>Divalidasi Oleh, <br /> Ekspedisi</td>
						<td>Diberikan Oleh, <br /> Sopir</td>
						<td>Diterima Oleh, <br /> Admin Plant</td>
					</tr>
					<tr><td colspan="3" style="height:60px"></td></tr>
					<tr>
						<td>( _______________ )</td>
						<td>( _______________ )</td>
						<td>( _______________ )</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>
</div>
<?php 
	}
?>