<div class="panel panel-primary">
	<div class="panel-heading">Detail SO</div>
	<div class="panel-body">
		
		<div class="row col-md-12" style="margin-bottom:20px">
			<div class="form-group">
				<label class="control-label col-md-2">Stok Tersedia: </label>
				<label class="control-label col-md-3">Sekam Gumpal: <?php echo isset($stokAwal['GS']) ? $stokAwal['GS']['jml_stok'] : 0  ?> Sak</label>
				<label class="control-label col-md-3">Kotoran (Pupuk): <?php echo isset($stokAwal['GP']) ? $stokAwal['GP']['jml_stok'] : 0 ?> Sak</label>
				<label class="control-label col-md-3">Glangsing: <?php echo isset($stokAwal['GBP']) ? $stokAwal['GBP']['jml_stok'] : 0  ?> Sak</label>
			</div>
		</div>
		<table class="table table-bordered" id="detailTable" style="width:900px">
			<thead>
				<tr>
					<th width="50%">Jenis Barang <i class="glyphicon glyphicon-plus addProduct" onclick="salesOrder.addProduct()"></i></th>
					<th width="10%">Jumlah</th>
					<th width="10%">Satuan</th>
					<th width="15%">Harga<br>(Rp)</th>
					<th width="15%">Total Harga<br>(Rp)</th>
				</tr>
			</thead>
			<tbody id="main_tbody">
				
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>
<style type="text/css">
	.addProduct:hover{
		cursor:pointer;
	}
</style>
