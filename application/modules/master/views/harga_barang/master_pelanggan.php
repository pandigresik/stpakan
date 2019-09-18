
<table class="table table-bordered table-striped" id="master-pelanggan">
	<thead>
		<tr>
			<th class="col-md-2">Kode Pelanggan</th>
			<th class="col-md-2">Nama Pelanggan</th>
			<th class="col-md-2">Alamat</th>
			<th class="col-md-2">Kota</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($list_pelanggan as $key => $value) { ?>
		<tr ondblclick="pilih_pelanggan(this)">
			<td class='kode_pelanggan'><?php echo $value['KODE_PELANGGAN']; ?></td>
			<td class='nama_pelanggan'><?php echo $value['NAMA_PELANGGAN']; ?></td>
			<td class='alamat'><?php echo $value['ALAMAT']; ?></td>
			<td class='kota'><?php echo $value['KOTA']; ?></td>
		<?php } ?>
	</tbody>
</table>