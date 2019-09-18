<div class="container col-md-12">
<div class="col-md-4 col-md-offset-4 text-center"><h3><?php echo !empty($nama_farm) ? strtoupper($nama_farm) : '' ?></h3></div>
	<table class="table table-bordered custom_table stok_pakan" data-tgl_transaksi="<?php echo $tgl_transaksi ?>">
		<thead>
			<tr>
				<th rowspan=2>Kavling</th>
				<th rowspan=2>Kandang</th>
				<th colspan=2>Umur</th>
				<th colspan=2>Pakan</th>
				<th colspan=5>Gudang</th>
				<th colspan=8>Kandang</th>
			</tr>
			<tr>
				<th>Minggu</th>
				<th>Hari</th>
				<th class="nama_bentuk">Nama, Bentuk</th>
				<th>Kode</th>

				<th>Stok Awal (Sak)</th>
				<th>Terima (Sak)</th>
				<th>Keluar (Sak)</th>
				<th>Stok Akhir (Sak)</th>
				<th>Umur Pakan (Hari)</th>

				<th>Stok Awal (Sak)</th>
				<th>Terima (Sak)</th>
				<th>Pakai (Sak)</th>
				<th>Retur Sak Kosong</th>
				<th>Hutang Retur Sak Kosong</th>
				<th>Sisa Hutang Retur Sak</th>
				<th>Stok Akhir (Sak)</th>
				<th>Umur Pakan (Hari)</th>
			</tr>
		</thead>
		<tbody>
		<?php
	//	echo '<pre>';print_r($list_stok);
		$standart_umur_pakan = 5;
		$p_index = 1;
		if(!empty($list_stok)){
			foreach($list_stok as $l){
				echo '<tr>';
				/* dapatkan jumlah jenis kelamin untuk menentukan rowspan */
				$rowspan = count($l['detail']);
				foreach($l['data']['depan'] as $z => $d){
					if($z == 'kavling'){
						echo '<td class="text-center parent_'.$p_index.'" data-parent="parent_'.$p_index.'" data-rowspan_asli="'.$rowspan.'" data-kavling="'.$d.'" rowspan="'.$rowspan.'">'.$d.'</td>';
					}
					else{
						echo '<td class="text-center parent_'.$p_index.'" rowspan="'.$rowspan.'">'.$d.'</td>';
					}

				}
				$tmp_p_index = $p_index;
				$i = 0;
				foreach($l['detail'] as $perpj){
					if($i != 0){
						echo '<tr>';
					}
					$j_jenis_kelamin = count($perpj['detail']);

					$class_pakan = '';
						if($j_jenis_kelamin == 1){
							$class_pakan = $perpj['detail'][0]['jenis_kelamin'] == 'B' ? 'coklat' : 'biru';
							echo '<td class="'.$class_pakan.'">'.$perpj['sum']['nama_pakan'].'</td>';
						}
						else{
							echo '<td> <span class="glyphicon glyphicon-plus plus_sign" data-kavling="'.$perpj['sum']['no_kavling'].'" data-kodebarang="'.$perpj['sum']['kode_barang'].'" data-parent="parent_'.$p_index.'" onclick="StokPakan.per_jenis_kelamin(this)"></span>&nbsp;'.$perpj['sum']['nama_pakan'].'</td>';
						}

						echo '<td>'.$perpj['sum']['kode_barang'].'</td>';
						echo '<td class="number">'.$perpj['sum']['stok_awal'].'</td>';
						$detail_terima = $perpj['sum']['terima_gudang'] > 0 ? '<span class="glyphicon glyphicon-plus plus_sign" data-tglterima="'.$tgl_transaksi.'" data-kodebarang="'.$perpj['sum']['kode_barang'].'" data-parent="parent_'.$p_index.'" data-nokavling="'.$perpj['sum']['no_kavling'].'" onclick="StokPakan.detail_terima_pakan(this)"></span>' : '';
						echo '<td class="number">'.$detail_terima.$perpj['sum']['terima_gudang'].'</td>';
						$class_gudang_keluar = $perpj['sum']['keluar_gudang'] != $perpj['sum']['terima_kandang'] ? 'abang' : '';
						echo '<td class="number '.$class_gudang_keluar.'">'.$perpj['sum']['keluar_gudang'].'</td>';
						echo '<td class="number">'.$perpj['sum']['stok_akhir'].'</td>';
						$class_umur_g = $perpj['sum']['umur_pakan'] > $standart_umur_pakan ? 'abang' : '';
						echo '<td class="number '.$class_umur_g.'">'.$perpj['sum']['umur_pakan'].'</td>';
						echo '<td class="number">'.$perpj['sum']['stok_awal_kandang'].'</td>';
						echo '<td class="number">'.$perpj['sum']['terima_kandang'].'</td>';
						echo '<td class="number">'.$perpj['sum']['pakai_kandang'].'</td>';
						/* untuk retur sak kosong */
						if($tmp_p_index == $p_index){
							foreach($l['data']['belakang'] as $zz => $d){
								$isi_d = $d;
								$tampil = 1;
								switch($zz){
									case 'retur':
										$class_bg = $d > 0 ? 'bg_orange' : '';
										$isi_d = '<span class="link_span" data-noreg="'.$l['data']['belakang']['noreg'].'" data-kandang="'.$l['data']['depan'].'" onclick="StokPakan.show_retur_sak(this)">'.$d.'</span>';
										break;
									case 'hutang_retur':
										$class_bg = $d > 0 ? 'bg_orange' : '';
										break;
									case 'sisa_retur':
										$class_bg = $d > 0 ? 'abang' : '';
										break;
									default :
										$tampil = 0;

								}
								if($tampil){
									echo '<td class="number '.$class_bg.' parent_'.$p_index.'" rowspan="'.$rowspan.'">'.$isi_d.'</td>';
								}

							}
						}

						echo '<td class="number">'.$perpj['sum']['stok_akhir_kandang'].'</td>';
						$class_umur_k = $perpj['sum']['umur_pakan_kandang'] > $standart_umur_pakan ? 'abang' : '';
						echo '<td class="number '.$class_umur_k.'">'.$perpj['sum']['umur_pakan_kandang'].'</td>';
						echo '</tr>';

					if($j_jenis_kelamin > 1){
						$l = 0;
						foreach($perpj['detail'] as $jk => $pj){
						$class_pakan = $pj['jenis_kelamin'] == 'B' ? 'coklat' : 'biru';
						echo '<tr data-kavling="'.$perpj['sum']['no_kavling'].'" data-kodebarang="'.$perpj['sum']['kode_barang'].'" class="hide detail_jenis_kelamin breakdown">';
							echo '<td class="'.$class_pakan.' number" >'.strtoupper(convertKode('jenis_kelamin',$pj['jenis_kelamin'])).'</td>';
							echo '<td></td>';
							echo '<td class="number">'.$pj['stok_awal'].'</td>';
							echo '<td class="number">'.$pj['terima_gudang'].'</td>';
							$class_gudang_keluar = $pj['keluar_gudang'] != $pj['terima_kandang'] ? 'abang' : '';
							echo '<td class="number '.$class_gudang_keluar.'">'.$pj['keluar_gudang'].'</td>';
							echo '<td class="number">'.$pj['stok_akhir'].'</td>';
							if($l == 0){
								echo '<td rowspan=2></td>';
							}
							echo '<td class="number">'.$pj['stok_awal_kandang'].'</td>';
							echo '<td class="number">'.$pj['terima_kandang'].'</td>';
							echo '<td class="number">'.$pj['pakai_kandang'].'</td>';

							echo '<td class="number">'.$pj['stok_akhir_kandang'].'</td>';
							if($l == 0){
								echo '<td rowspan=2></td>';
							}
						echo '</tr>';
						$l++;
						}
					}

					echo '</tr>';
					$i++;
					$tmp_p_index++;
				}
				$p_index++;
			}
		}
		?>
		</tbody>
	</table>
</div>
