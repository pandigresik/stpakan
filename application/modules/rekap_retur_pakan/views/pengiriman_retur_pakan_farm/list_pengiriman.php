<!-- tabel list pengiriman -->
<div class="row">
 	<div class="container col-md-12">
 		<table class="table table-bordered custom_table" id="tabellistretur">
 			<thead>
 				<tr>
 					<th>No. Retur Pakan</th>
 					<th>Farm Asal</th>
 					<th>Farm Tujuan</th>
 					<th>Tanggal Kirim</th>
 					<th>Jenis Pakan</th>
 					<th>Jumlah Sak</th>
 					<th width="15%">Nama Sopir</th>
 					<th width="10%">Kendaraan</th>					
 				</tr> 			
 			</thead>
 			<tbody>
	 		<?php
				if(!empty($returs)){ $numRow = 1;
					$select_kendaraan = '';
					if(count($list_kendaraan)>0){
						foreach($list_kendaraan as $lk){
							$select_kendaraan .= '<option>'.$lk['NO_KENDARAAN'].'</option>';
						}
					}
					
					foreach($returs as $r){  
						$status='';
						$num = 0;
						$noReturAct = '';					
						
						if($user_level == 'KF'){
							$noReturAct = 'onClick="Returpakanfarm.tampilUbah(this)" data-rowid="TR'.$numRow.'"';
						}
						
						$sopir = $r['SOPIR'];
						$tr = '<tr id="TR'.$numRow.'" class="TRrow" ondblClick="Pengirimanreturpakanfarm.lihat_detail_kirim(this)" data-noretur="'.$r['NO_RETUR'].'">';
						$nopol = $r['NOPOL'];
						if(empty($sopir)){
							$sopir = '<input type="text" class="form-control input_nama_sopir hide" onBlur="Pengirimanreturpakanfarm.get_platnomor(this, TR'.$numRow.')" onKeyUp="Pengirimanreturpakanfarm.set_huruf(this)">';
							$tr = '<tr id="TR'.$numRow.'" class="TRrow" ondblClick="Pengirimanreturpakanfarm.set_pengiriman(this)" data-retur="'.$r['NO_RETUR'].'" 
								data-kodestatus="'.$r['STATUS'].'"  data-jmlretur="'.$listPakanSisa[$r['NO_RETUR']][0]['JUMLAH'].'"
								data-reject='.(in_array(trim($r['STATUS']),array_keys($rejectedStatus)) ? 1 : 0).' style="background:#EA9999;">';
							$nopol = '<input type="text" class="form-control select_kendaraan hide" onKeyUp="Pengirimanreturpakanfarm.setUppercase(this)">';
						}
					
						$nmpakan = array();
						$kdpakan = array();
						$jmlretur = array();
						foreach($listPakanSisa[$r['NO_RETUR']] as $pakan){
							array_push($kdpakan, $pakan['KODE_PAKAN']);
							array_push($nmpakan, $pakan['NAMA_PAKAN']);
							array_push($jmlretur, $pakan['JUMLAH']);
						}
						$jml = implode(',', $jmlretur);
						$namapakan = implode('<br>', $nmpakan);
						$kodepakan = implode(',', $kdpakan);
						
						echo $tr.'<td class="no_retur" data-retur="'.$r['NO_RETUR'].'" '.$noReturAct.' onclick="Pengirimanreturpakanfarm.detail(this)"><span class="link_span">'.$r['NO_RETUR'].'</span></td>
							<td class="farm_asal">'.$farm[$r['FARM_ASAL']]['nama_farm'].'</td>
							<td class="farm_tujuan">'.$farm[$r['FARM_TUJUAN']]['nama_farm'].'</td>
							<td class="tgl_kirim">'.tglIndonesia($r['TGL_KIRIM'],'-',' ').'</td>
							<td class="jenis_pakan" data-pakan="'.$kodepakan.'">'.$namapakan.'</td>
							<td class="jumlah_retur" data-jmlretur="'.$jml.'">'.str_replace(',', '<br>', $jml).'</td>
							<td>'.$sopir.'</td>
							<td>'.$nopol.'</td>
						</tr>';
						$numRow++;
					}
				}
			?>
	 		</tbody>
 		</table>
	</div>
</div>
