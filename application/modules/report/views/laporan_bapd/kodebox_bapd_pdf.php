<?php
	$noreg_exp = explode('/', $kodebox[0]['NO_REG']);
	$periode_siklus = $noreg_exp[1];
?>
<table align="center" width="100%">
	<tr><td style="font-size:12pt;"><b><u>Kode Box</u></b></td></tr>
	<tr><td style="font-size:12pt;"><b><u>Berita Acara Penerimaan DOC-In</u></b></td></tr>
	<tr><td style="font-size:9.5pt;">Farm <?=$namafarm?> Periode Siklus <?=$periode_siklus?></td></tr>
	<tr>
		<td>
			<!--table kode box-->
			<style>
				.table_kodebox tr th,
				.table_kodebox tr td{border:1px solid #000;line-height:12pt;}
			</style>
			<table style="border:1px solid #000;font-size:8pt;" align="left" class="table table-striped custom_table table_kodebox" 
				width="100%">
				<thead>
					<tr>
						<th width="8%"> No.Reg</th>
						<th width="5%"> Kandang</th>
						<th width="8%"> Hatchery</th>
						<th width="10%"> Tanggal DOC-In</th>
						<th width="8%"> No.SJ</th>
						<th width="12%"> Kode Box</th>
						<th width="7%"> Jumlah Box</th>
						<th width="7%"> Total Box</th>
						<th width="7%"> Jumlah Ekor<br> DOC</th>
						<th width="7%"> Pop.Afkir</th>
						<th width="7%"> Stok Akhir</th>
						<th width="7%"> BB<br> Rata-rata</th>
						<th width="7%"> Uniformity</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$return 		= '';
						$last_noreg 	= '';
						$rowspan 		= 0;
						$new_noreg		= 0;
						$num 			= 0;
						$total_box 		= 0;
						$arr_rowspan 	= array();
						$arr_total_box 	= array();
						$arr_stok_akhir = array();
						$arr_ekor 		= array();
						foreach($kodebox as $dk){
							$num++;
							if($last_noreg==''){$last_noreg = $dk['NO_REG'];}
							if($num == count($kodebox) || $last_noreg != $dk['NO_REG']){	
								if($num == count($kodebox)){
									$rowspan++;
									$total_box += $dk['JML_BOX'];
								}
								
								array_push($arr_rowspan, $rowspan);
								array_push($arr_total_box, $total_box);
								array_push($arr_ekor, $total_box*102);
								array_push($arr_stok_akhir, ($total_box*102)-$dk['JML_AFKIR']);
								
								if($last_noreg != $dk['NO_REG']){
									$last_noreg = $dk['NO_REG'];
									$rowspan = 1;
									$total_box = $dk['JML_BOX'];
								}
							}else{
								$rowspan++;
								$total_box += $dk['JML_BOX'];
							}
						}
						$last_noreg = '';
						$nosj = '';
						$last_nosj = '';
						$this_sj = '';
						$num = 0;
						foreach($kodebox as $dk){
							$noreg = '';
							$kandang = '';
							$tgl = '';
							$nosj_exp 	= explode('/', $dk['NO_SJ']);
							$this_sj 	= $nosj_exp[0].'/'.$nosj_exp[1];
							
							if($last_noreg != $dk['NO_REG']){
								$last_noreg 	= $dk['NO_REG'];
								$noreg 			= $last_noreg;
								$noreg_exp 		= explode('/', $noreg);
								$kandang 		= $noreg_exp[count($noreg_exp)-1];
								$nama_hatchery 	= $dk['NAMA_HATCHERY'];
								$tgl 			= tglIndonesia($dk['TGL_DOC_IN']);
								$rowspan 		= 1;
								$new_noreg 		= 1;
							}else{
								$noreg 			= '';
								$nama_hatchery 	= '';
								$tgl 			= '';
								$kandang 		= '';
								$new_noreg 		= 0;
								$rowspan++;
							}
							
							if($last_nosj == '' || $last_nosj != $this_sj){
								$nosj 		= $nosj_exp[0].'/'.$nosj_exp[1];
								$last_nosj	= $nosj;
							}else{
								$nosj 		= '';
							}
							
							if($new_noreg==1){
								$return .= '<tr>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.$noreg.'</td>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.$kandang.'</td>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.$nama_hatchery.'</td>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.$tgl.'</td>';
								$return .= '<td> '.$nosj.'</td>';
								$return .= '<td> '.$dk['KODE_BOX'].'</td>';
								$return .= '<td> '.$dk['JML_BOX'].'</td>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.$arr_total_box[$num].'</td>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.Angkaribuan($arr_ekor[$num]).'</td>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.$dk['JML_AFKIR'].'</td>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.Angkaribuan($arr_stok_akhir[$num]).'</td>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.str_replace('.', ',', $dk['BB_RATA2']).'</td>';
								$return .= '<td rowspan="'.$arr_rowspan[$num].'"> '.str_replace('.', ',', $dk['UNIFORMITY']).'</td>';
								$return .= '</tr>';
								$num++;
							}else{
								$return .= '<tr>';
								$return .= '<td> '.$nosj.'</td>';
								$return .= '<td> '.$dk['KODE_BOX'].'</td>';
								$return .= '<td> '.$dk['JML_BOX'].'</td>';
								$return .= '</tr>';
							}
						}
						echo $return;
					?>
				</tbody>
			</table>
			<!-- end tabel kodex box-->
		</td>
	</tr>
</table>