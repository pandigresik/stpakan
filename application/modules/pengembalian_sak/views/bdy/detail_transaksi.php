<?php
$lepasKontrolFlag = ''; 
if($lepaskontrol){
	$lepasKontrolFlag = 'data-lepaskontrol=1';
}

if($tipe == 'append'){ 
	$no=0;
	$count_pakan = 0;
	$lastRowid = $rowid;
	//$thisreg = 'nr'.$rowid;
	
	foreach($list_pakan as $pakan){
		$kode_barang = $pakan['kode_barang'];
		$jk = $pakan['jenis_kelamin'];
		$retur_sak = !empty($sak_kembali[$kode_barang][$jk]) ? $sak_kembali[$kode_barang][$jk] : 0;
		$belum_kembali = $pakan['jml_pakai'] - $retur_sak;
		if($belum_kembali>0){
			$count_pakan++;
		}
	}
	
	foreach($list_pakan as $pakan){
		//$lastRowid++;
		$kode_barang = $pakan['kode_barang'];
		$jk = $pakan['jenis_kelamin'];
		$retur_sak = !empty($sak_kembali[$kode_barang][$jk]) ? $sak_kembali[$kode_barang][$jk] : 0;
		//$outstanding = $pakan['jml_pakai'] - $retur_sak;
		$outstanding = $maxjmltimbang[$kode_barang][$jk]['stok'];
		$lhk = $maxjmltimbang[$kode_barang][$jk]['lhk'];
		$belum_kembali = $pakan['jml_pakai'] - $retur_sak;					
		
		if($belum_kembali > 0){
			$lastRowid++;			
			$rowid++;
			$namakandang = '';
			if($no==0){
				$namakandang = '<td rowspan="'.$count_pakan.'">'.$nama_kandang[0]->NAMA.'</td>';
			}
			//echo '<tr id="tr'.$lastRowid.'" class="'.$pakan['kode_barang'].'" data-noregdata="'.$thisreg.'" data-kode_barang='.$pakan['kode_barang'].' data-jenis_kelamin='.$pakan['jenis_kelamin'].' data-jml_retur="'.$retur_sak.'" data-jml_kirim="'.$pakan_dikirim[$kode_barang][$jk].'"  data-jml_pakai="'.$pakan['jml_pakai'].'">
			echo '<tr id="tr'.$rowid.'">
					'.$namakandang.'
					<td>'.$pakan['nama_barang'].'</td>
					<td class="text-center target_kembali">'.$belum_kembali.'</td>							
					<td class="jml_kembali">
					<input type="text" class="form-control number input_jml_kembali" data-field="Jumlah pengembalian" data-maxvalue="'.$belum_kembali.'" 
					data-maxpakai="'.$lhk.'" value=0 name="jml_pengembalian" onchange="Pengembalian.checkMaxValue(this)" 
					data-noreg="'.$no_reg.'" data-kode_barang='.$pakan['kode_barang'].' data-jenis_kelamin='.$pakan['jenis_kelamin'].' 
					data-jml_retur="'.$retur_sak.'" data-jml_kirim="'.$pakan_dikirim[$kode_barang][$jk].'"  data-jml_pakai="'.$pakan['jml_pakai'].'"
					'.$lepasKontrolFlag.' onkeyup="number_only(this)"/>
					</td>
				</tr>';
			$no++;
		}
	}

 }else{ ?>
 
<div class="panel panel-default">
	<div class="panel-heading">Detail Pengembalian Sak Kosong </div>
	<div class="panel-body">
		<table data-no_reg=<?php echo $no_reg ?> id="tabel_detail_pengembalian_sak" class="table table-bordered custom_table">
			<thead>
				<tr>
					<th>Kandang</th>
					<th>Nama Pakan</th>
					<th width="18%">Target Pengembalian (Sak)</th>
					<th width="18%">Jumlah Pengembalian (Sak)</th>
					<th width="16%">Berat Sak<br>(Kg)</th>
				</tr>
			</thead>
			<tbody>

				<?php
				$no=0;
				//$count_pakan = count($list_pakan);
				$count_pakan = 0;
				$rowid=1;
				//$thisreg = 'nr1';
				$setDisplay = array();
				
				foreach($list_pakan as $pakan){
					$kode_barang = $pakan['kode_barang'];
					$jk = $pakan['jenis_kelamin'];
					$retur_sak = !empty($sak_kembali[$kode_barang][$jk]) ? $sak_kembali[$kode_barang][$jk] : 0;
					$belum_kembali = $pakan['jml_pakai'] - $retur_sak;
					if($belum_kembali>0){
						$count_pakan++;
					}
				}
				
				foreach($list_pakan as $pakan){ 
					$kode_barang = $pakan['kode_barang'];
					$jk = $pakan['jenis_kelamin'];
					$retur_sak = !empty($sak_kembali[$kode_barang][$jk]) ? $sak_kembali[$kode_barang][$jk] : 0;
					//$outstanding = $pakan['jml_pakai'] - $retur_sak;
					$outstanding = $maxjmltimbang[$kode_barang][$jk]['stok'];
					$lhk = $maxjmltimbang[$kode_barang][$jk]['lhk'];
					$belum_kembali = $pakan['jml_pakai'] - $retur_sak;					
					
					if($belum_kembali > 0){	
						$namakandang = '';
						if($no==0){
							$namakandang = '<td rowspan="'.$count_pakan.'">'.$nama_kandang[0]->NAMA.'</td>';
						}
						$tdTimbang = '';
						if($no==0){
							$tdTimbang = '<td id="berat_timbang_sak" rowspan="'.$count_pakan.'"></td>';
						}
						echo '<tr id="tr'.$rowid.'">
							'.$namakandang.'
							<td>'.$pakan['nama_barang'].'</td>
							<td class="text-center target_kembali">'.$belum_kembali.'</td>							
							<td class="jml_kembali">
								<input type="text" class="form-control number input_jml_kembali" data-field="Jumlah pengembalian" data-maxvalue="'.$belum_kembali.'" 
								data-maxpakai="'.$lhk.'" value=0 name="jml_pengembalian" onchange="Pengembalian.checkMaxValue(this)" 
								data-noreg="'.$no_reg.'" data-kode_barang='.$pakan['kode_barang'].' data-jenis_kelamin='.$pakan['jenis_kelamin'].' 
								data-jml_retur="'.$retur_sak.'" data-jml_kirim="'.$pakan_dikirim[$kode_barang][$jk].'"  data-jml_pakai="'.$pakan['jml_pakai'].'"
<<<<<<< HEAD
								'.$lepasKontrolFlag.' onkeyup="number_only(this)"/>
=======
								onkeyup="number_only(this)"/>
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
							</td>
							'.$tdTimbang.'
						</tr>';
						$no++;
						$rowid++;
					}
				}
				?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>	
<?php } ?>