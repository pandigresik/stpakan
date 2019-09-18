<?php 
	$kode_barang = isset($list_cari['kode_barang']) ? $list_cari['kode_barang'] : '';
	$nama_barang = isset($list_cari['nama_barang']) ? $list_cari['nama_barang'] : '';
	$bentuk_barang = isset($list_cari['bentuk_barang']) ? $list_cari['bentuk_barang'] : '';
	$grup_barang = isset($list_cari['grup_barang']) ? $list_cari['grup_barang'] : '';
	$arr_bentuk_barang = array(
		'C' => 'Crumble',
		'T' => 'Tepung',
		'P' => 'Pellet',
	);
?>
<table id="KonfigurasiPakanbarang" class="table table-bordered table-striped">
	<thead>
		<tr>
            <th class="col-md-1"><input type="text" class="form-control search" name="kode_barang" placeholder="Kode Barang"  onchange="KonfigurasiPakan.cariBarang(this)" value="<?php echo $kode_barang ?>"></th>
            <th class="col-md-1"><input type="text" class="form-control search" name="nama_barang" placeholder="Nama Barang"  onchange="KonfigurasiPakan.cariBarang(this)" value="<?php echo $nama_barang ?>"></th>
            <th class="col-md-1">
				<div class="input-group">
					<select class="form-control search" name="bentuk_barang"  onchange="KonfigurasiPakan.cariBarang(this)">
						<option value="">Semua</option>
						<?php 
							foreach($arr_bentuk_barang as $v => $t){
								if($v == $bentuk_barang){
									$selected = 'selected';
								}
								else{
									$selected = '';		
								}
								echo '<option value="'.$v.'" '.$selected.' >'.$t.'</option>'; 
							}
						?>
						
					</select>
				</div>
			</th>
             <th class="col-md-1">
				<div class="input-group">
					<select class="form-control search" name="grup_barang" onchange="KonfigurasiPakan.cariBarang(this)">
						<option value="">Semua</option>
						<?php 
						foreach($list_grup_barang as $grup){
							if($grup['id'] == $grup_barang){
								$selected = 'selected';
							}
							else{
								$selected = '';
							}
							echo '<option value="'.$grup['id'].'" '.$selected.' >'.$grup['name'].'</option>';
						}
						?>
					</select>
				</div>
			</th>
			<th class="col-md-1">
			</th>
        </tr>
		<tr>
          
            <th class="col-md-1">Kode Barang</th>
            <th class="col-md-1">Nama Barang</th>
            <th class="col-md-1">Bentuk Barang</th>
            <th class="col-md-1">Jenis Pakan</th>
            <th class="col-md-1">Status</th>
        </tr>
    </thead>
	<tbody>
		<?php 
			if(!empty($list_barang)){
				foreach($list_barang as $barang){
					echo '<tr>
  							<td class="col-md-1">'.$barang['kode_barang'].'</td>
							<td class="col-md-1">'.$barang['nama_barang'].'</td>
							<td class="col-md-1">'.$barang['bentuk_barang'].'</td>
            				<td class="col-md-1">'.$barang['grup_barang'].'</td>
            				<td class="col-md-1"><input data-kode_barang="'.$barang['kode_barang'].'" type="checkbox" /></td>
  						</tr>';
				}
			}
		?>
	</tbody>
</table>