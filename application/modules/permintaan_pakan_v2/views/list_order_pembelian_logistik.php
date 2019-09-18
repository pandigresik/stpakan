
<table class="table table-bordered">
	<thead>
		<tr class="search">
			<th>
				<div class="right-inner-addon ">
					<i class="glyphicon glyphicon-search"></i> 
					<input type="search" class="form-control " name="no_op_logistik" placeholder="Search">
				</div>
			</th>
			<th>
				<div class="right-inner-addon ">
					<i class="glyphicon glyphicon-search"></i> <input type="search"	class="form-control " name="no_pp" placeholder="Search">
				</div>
			</th>
			<th>
				<div class="right-inner-addon ">
					<i class="glyphicon glyphicon-search"></i> <input type="search"	class="form-control " name="nama_farm" placeholder="Search">
				</div>
			</th>

			<th><span class="btn btn-default" onclick="Permintaan.list_order_pembelian_logistik(this)">Cari</span>
			</th>
		</tr>
		<tr>
			<th>No. OP Logistik</th>
			<th>No. PP</th>
			<th>Farm</th>
			<th>Tanggal OP</th>
		</tr>
	</thead>
	<tbody>
		 <?php 
        	foreach($order_pembelian as $baris){
				$tgl_op = $baris['tgl_op'];
        		echo '<tr>';
        		echo '<td>'.$baris['no_op'].'</td>';
        		echo '<td>'.$baris['no_pp'].'</td>';
        		echo '<td>'.$baris['nama_farm'].'</td>';
        		echo '<td>'.tglIndonesia($baris['tgl_op'], '-', ' ').'</td>';
        		echo '<td><div data-lpb="'.$baris['no_pp'].'" class="btn btn-default" onclick="Permintaan.cetak_order_pembelian_logistik(this,\''.$baris['no_op'].'\')">Cetak</div></td>';
        		echo '</tr>';
        	}
        ?>
	</tbody>
</table>
