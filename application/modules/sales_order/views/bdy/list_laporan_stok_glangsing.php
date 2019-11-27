<div class="panel panel-primary">
	<div class="panel-heading">Laporan Stok Glangsing</div>
	<div class="panel-body">
	
		<table class="table table-bordered custom_table">
			<thead>
				<tr>
					<th width="12%">Farm</th>
					<th width="10%">Siklus</th>
					<th width="18%">Jenis Barang</th>
					<th width="10%">Stok Tersedia<br>(Sak)</th>
					<th width="10%">SO / DO<br>(Sak)</th>
					<th width="10%">Sisa Stok Tersedia<br>(Sak)</th>
					<th width="10%">Status Penerimaan Uang<br>(Sak)</th>
					<th width="10%">Surat Jalan<br>(Sak)</th>
					<th width="10%">Sisa Stok<br>(Sak)</th>
				</tr>
			</thead>
			<tbody id="main_tbody">
			<?php
			//print_r($so_do_harian);
			if(!empty($list_estimasi_jumlah)){
				foreach ($list_estimasi_jumlah as $kf => $perfarm) {
					$indexFarm = 0;					
					$bisaBuatSO = 1;
					$so_farm = isset($so_do_harian[$kf]) ? $so_do_harian[$kf] : array();
					$so_penerimaan_uang_farm = isset($so_do_verifikasi[$kf]) ? $so_do_verifikasi[$kf] : array();
					$so_surat_jalan_farm = isset($so_do_sj[$kf]) ? $so_do_sj[$kf] : array();
					
					foreach($perfarm as $ks => $persiklus){
						$so_farm_siklus = array();
						if(!empty($so_farm)){
							$so_farm_siklus = isset($so_farm[$ks]) ? $so_farm[$ks] :array();
						}
						$so_penerimaan_uang_siklus = array();
						if(!empty($so_penerimaan_uang_farm)){
							$so_penerimaan_uang_siklus = isset($so_penerimaan_uang_farm[$ks]) ? $so_penerimaan_uang_farm[$ks] :array();
						}
						$so_surat_jalan_siklus = array();
						if(!empty($so_surat_jalan_farm)){
							$so_surat_jalan_siklus = isset($so_surat_jalan_farm[$ks]) ? $so_surat_jalan_farm[$ks] :array();
						}						
						$indexSiklus = 0;
						$rowspan = count($persiklus);
						
						foreach($persiklus as $brg){							
							$_kb = $brg['kode_barang'];
							// $so_do_lalu = 0;
							$stok_tersedia = $brg['jml_estimasi'];;//$stok_tersedia - $so_do_lalu;
							$status_penerimaan_uang = isset($so_penerimaan_uang_siklus[$_kb]) ? $so_penerimaan_uang_siklus[$_kb]['jumlah'] : 0;
							$surat_jalan = isset($so_surat_jalan_siklus[$_kb]) ? $so_surat_jalan_siklus[$_kb]['jumlah'] : 0;
							$so_do_hari_ini = isset($so_farm_siklus[$_kb]) ? $so_farm_siklus[$_kb]['jumlah'] : 0;
							$sisa_stok_tersedia = $stok_tersedia - $so_do_hari_ini;
							$sisa_stok = $stok_tersedia - $surat_jalan;
							$namaFarm = $brg['nama_farm'];
							if($indexFarm){
								$namaFarm = '';
								if($bisaBuatSO){
									if($sisa_stok_tersedia > 0){
										$bisaBuatSO = 0;
									}
								}
							}
							echo '<tr data-buatso="'.$bisaBuatSO.'" data-kode_farm="'.$brg['kode_farm'].'" data-kode_siklus="'.$brg['kode_siklus'].'" onclick="laporanStokGlangsing.rowOnClick(this)">';
							if(!$indexSiklus){
								echo '<td rowspan="'.$rowspan.'">'.$namaFarm.'</td>';
								echo '<td rowspan="'.$rowspan.'">'.$brg['periode_siklus'].'</td>';
							}
							
							echo '<td>'.$brg['nama_barang'].'</td>';
							echo '<td class="stok_tersedia">'.angkaRibuan($stok_tersedia).'</td>';
							echo '<td class="so_do">'.angkaRibuan($so_do_hari_ini).'</td>';
							echo '<td class="sisa_stok_tersedia">'.angkaRibuan($sisa_stok_tersedia).'</td>';
							echo '<td class="status_penerimaan_uang">'.angkaRibuan($status_penerimaan_uang).'</td>';
							echo '<td class="surat_jalan">'.angkaRibuan($surat_jalan).'</td>';
							echo '<td class="sisa_stok">'.angkaRibuan($sisa_stok).'</td>';
							echo '</tr>';
						
							$indexSiklus++;
						}
						$indexFarm++;
					}					
				}
			}	
			?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>
