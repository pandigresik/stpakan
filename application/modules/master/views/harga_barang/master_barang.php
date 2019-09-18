
<table class="table table-bordered table-striped" id="master-barang">
	<thead>
		<tr>
			<th class="col-md-2">Kode Barang</th>
			<th class="col-md-2">Nama Barang</th>
			<th class="col-md-2">Bentuk Pakan</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($list_barang as $key => $value) { ?>
		<tr ondblclick="pilih_barang(this)">
			<td class='kode_barang'><?php echo $value['KODE_BARANG']; ?></td>
			<td class='nama_barang'><?php echo $value['NAMA_BARANG']; ?></td>
			<td class='bentuk_barang' data-kode-bentuk-barang="<?php echo $value['BENTUK_BARANG']; ?>"><?php echo $value['BENTUK_BARANG_LABEL']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>