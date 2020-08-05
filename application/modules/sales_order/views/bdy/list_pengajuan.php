<div class="panel panel-primary">
	<div class="panel-heading">Pengajuan Harga</div>
	<div class="panel-body">
		<!--
		<table class="table table-bordered custom_table list_pengajuan">
		-->
		<div class="table-responsive">
		<table class="table table-bordered custom_table">
			<thead>
				<tr>
					<th width="10%">Tgl Pengajuan</th>
					<th width="10%">Ref</th>
					<th width="10%">Farm</th>
					<th width="20%">Jenis Barang</th>
					<th width="10%">Estimasi Jumlah<br>(Sak)</th>
					<th width="10%">Harga Jual Regional<br>(Rp)</th>
					<?php
						if($level_user != 'KF'){
							echo '<th width="10%">Harga Jual<br>(Rp)</th>';
						}
					?>					
					<?php
						if(in_array($level_user,array('KDLOG','KF'))){
							echo '<th width="40%">Status</th>';							
						}else{
							echo '<th width="35%">Status</th>
								<th width="5%">Aksi</th>';
						}
					?>
				</tr>
			</thead>
			<tbody id="main_tbody">
				<?php				
				$sudahPengajuan = 0;
				$statusPengajuanTerakhir = '';
				/*				
				foreach ($pengajuan_terakhir as $key => $ph) {
					if($tgl_sekarang == $ph['tgl_pengajuan']){
						$sudahPengajuan = 1;						
						break;
					}
				}*/
				$loop = 0;
				$str = '';				
				if($can_write && !$sudahPengajuan){					
					if(in_array($level_user,array('KDLOG','KF'))){
						$jmlBarang = count($barang);
						foreach ($barang as $key2 => $mb) {							
								$disableBarang = $mb['TIPE_BARANG'] == 'I' ? 'readonly' : '';
								$str .= '<tr class="entry_pengajuan hide">';
								if($loop == 0){
									$str .= '<td class="tgl_pengajuan" rowspan="~~">'.$tgl_pengajuan_text.'</td>';	
									$str .= '<td class="ref" rowspan="~~"></td>';		
									$dropdown = array('<select data-userlevel="'.$level_user.'" class="form-control" onchange="pengajuanHarga.setEstimasiJml(this)">');
									array_push($dropdown,'<option value="">- Pilih Farm -</option>');									
									foreach ($list_farm as $key => $farm) {		
										$estimasiSak = array();
										$pengajuanSebelumnya = array();
										if(isset($list_estimasi_jumlah[$farm['KODE_FARM']])){
											if(!empty($list_estimasi_jumlah[$farm['KODE_FARM']])){
												foreach($list_estimasi_jumlah[$farm['KODE_FARM']] as $kb){
													$estimasiSak[$kb['kode_barang']] = $kb['jml_estimasi'];
												}
											}
										}
										if(isset($pengajuan_terakhir[$farm['KODE_FARM']])){
											if(!empty($pengajuan_terakhir[$farm['KODE_FARM']])){
												$pengajuanSebelumnya = $pengajuan_terakhir[$farm['KODE_FARM']];
											}
										}
																				 										
										array_push($dropdown,'<option data-pengajuanlama='.json_encode($pengajuanSebelumnya).' data-estimasi='.json_encode($estimasiSak).' value="'.$farm['KODE_FARM'].'">'.$farm['NAMA_FARM'].'</option>');
									}							
									array_push($dropdown,'</select>');
									$str .= '<td class="nama_farm" rowspan="~~">'.implode(' ',$dropdown).'</td>';
								}
								$str .= '<td class="jenis_barang">'.$mb['NAMA_BARANG'].'</td>';
								$str .= '<td class="estimasi_jumlah" data-kodebarang="'.$mb['KODE_BARANG'].'" style="text-align:right"></td>';
								if($level_user == 'KF'){
								$str .= '<td><div class="form-group">										
										<div class="col-md-11" style="padding-right:0px;">
											<input '.$disableBarang.'  style="padding:1px;" type="text" class="form-control number" data-tgl_pengajuan="'.$tgl_pengajuan.'" data-kode_barang="'.$mb['KODE_BARANG'].'" name="harga_reg" value="0"/>
										</div>										      
										</div></td>';
								}else{
									$str .= '<td class="harga_reg" data-kode_barang="'.$mb['KODE_BARANG'].'"><div class="form-group">								      
								      <label class="control-label col-md-11" style="padding:10px 0 0 0;text-align:right">0</label>								      
								    </div></td>';
								}		
								if($level_user != 'KF'){
									$str .= '<td><div class="form-group">										
										<div class="col-md-11" style="padding-right:0px;">
											<input '.$disableBarang.'  style="padding:1px;" type="text" class="form-control number" data-tgl_pengajuan="'.$tgl_pengajuan.'" data-kode_barang="'.$mb['KODE_BARANG'].'" name="harga" value="0"/>
										</div>										      
										</div></td>';
								}
								
								if($loop == 0){
									$str .= '<td class="keterangan" rowspan="~~"></td>';
								}
								$str .= '</tr>';
								$loop++;									
						
						}
				
						$str = str_replace('~~', $loop, $str);						
					}
				}
				echo $str;
							
				if(!empty($list_pengajuan)){
					$str = '';
					$no_pengajuan_harga = '';
					$status = '';
					
					$keterangan = '';
					$aksi = '';
					$tgl_pengajuan = '';
					$readonly = false;
					$approved = false;
				
					foreach($list_pengajuan as $data){
						$no_urut = 0;
						if ($no_pengajuan_harga != $data['no_pengajuan_harga']){
							$str .= '<tr data-userlevel="'.$level_user.'" ondblclick="pengajuanHarga.buatRef(this)" data-no_pengajuan_harga="'.$data['no_pengajuan_harga'].'" data-no_urut="~no_urut~">';
							$str = str_replace('~~', $loop, $str);
							$loop = 0;
							$no_pengajuan_harga = $data['no_pengajuan_harga'];

							$keterangan = '';
							$aksi = '';
							$status_ph = '';
							foreach($list_keterangan[$no_pengajuan_harga] as $val){
								if(empty($status_ph) && ($val['status'] == 'A')){
									$status_ph = $val['status'];
								}
								if($val['no_pengajuan_harga'] == $data['ref_id']){
									$keterangan .= '<div>['.$val['nama_pegawai'].'] - '.$val['status_detail'].', '.convertElemenTglWaktuIndonesia($val['tgl_buat']).'</div>';
									if($val['keterangan'] != ''){
										$keterangan .= '<div style="color:#ff0000">'.(($val['status'] == 'N')? 'Revisi - ':'').$val['keterangan'].'</div>';
									}
								}elseif($val['no_pengajuan_harga'] == $no_pengajuan_harga){
									$keterangan .= '<div>['.$val['nama_pegawai'].'] - '.$val['status_detail'].', '.convertElemenTglWaktuIndonesia($val['tgl_buat']).'</div>';
									if($val['keterangan'] != ''){
										$keterangan .= '<div style="color:#ff0000">'.(($val['status'] == 'N')? 'Revisi - ':'').$val['keterangan'].'</div>';
									}
								//	$aksi = $checkbox[$val['status']];																		
									$readonly = (!(in_array($val['status'],$rejectStatus) && $level_user == 'KDLOG'));
									$approved = ($val['status'] == 'A' && $val['grup_pegawai'] == 'KDV');
								}
								if(!$no_urut){
									$no_urut = $val['no_urut'];
								}								
							}
							if(in_array($data['status'],$canApproveStatus)){
								$aksi = $checkbox[$data['status']];	
								if(isset($pengajuan_terakhir)){
									if($no_pengajuan_harga < $pengajuan_terakhir[$data['kode_farm']]['no_pengajuan_harga']){
										$aksi = '';
									}
								}
								/* jika sudah kadaluarsa hilangkan checkboxnya */							
								$indexDayPengajuan = indexDay($data['tgl_pengajuan']);
								$selisihHari = 0;
								if($indexDayPengajuan < $indexExp){
									$selisihHari = $indexExp - $indexDayPengajuan;
								}else{
									$selisihHari = 7 - ($indexDayPengajuan - $indexExp);
								}
								/* abaikan untuk pengecekan jika melebihi hari jumat
								$expiredDate = tglSetelah($data['tgl_pengajuan'],$selisihHari);
								if($tgl_sekarang >= $expiredDate){
									$aksi = '';
								}*/
							}
							
							$str .= '<td data-kode_farm="'.$data['kode_farm'].'" data-status_pengajuan="'.$val['status'].'" class="tgl_pengajuan" rowspan="~~">
										<div>'.convertElemenTglIndonesia($data['tgl_pengajuan']).'</div>
										<div>['.$data['no_pengajuan_harga'].']</div>
										</td>';
							$str .= '<td class="ref" rowspan="~~">'.$data['ref_id'].'</td>';				
							$str .= '<td class="nama_farm" rowspan="~~">'.$data['nama_farm'].'</td>';
						}else{
							$str .= '<tr>';
						}
						/* jika can_write aktif dan  tgl pengajuan < tglsekarang maka non aktifkan */						
						if($data['tgl_pengajuan'] < $tgl_sekarang){							
							if($can_write){								
								$readonly = 1;
							}else{
								if(isset($pengajuan_terakhir)){									
									if($no_pengajuan_harga == $pengajuan_terakhir[$data['kode_farm']]['no_pengajuan_harga']){
										if(in_array($data['status'],$rejectStatus) && $level_user == 'KDLOG'){							
											$readonly = 0;
										}
									}
								}	
							}																				
						}						
						
						$str .= '<td class="jenis_barang">'.$data['nama_barang'].'</td>';
						$str .= '<td class="estimasi_jumlah" style="text-align:right">'.angkaRibuan($data['estimasi_jumlah']).'</td>';
						//$str .= '<td><input type="text" class="form-control number" name="harga" value="0"/></td>';
						if($approved){
							$str .= '<td><div class="form-group">								      
								      <label class="control-label col-md-11" style="padding:10px 0 0 0;text-align:right">'.formatAngka($data['harga_reg'],0).'</label>								      
								    </div></td>';
							$str .= '<td><div class="form-group">								      
								      <label class="control-label col-md-11" style="padding:10px 0 0 0;text-align:right">'.formatAngka($data['harga_jual'],0).'</label>								      
								    </div></td>';
						}else{
							$readonly = 1;
							$str .= '<td><div class="form-group">								      
								      <label class="control-label col-md-11" style="padding:10px 0 0 0;text-align:right">'.formatAngka($data['harga_reg'],0).'</label>								      
								    </div></td>';
								
							if($level_user != 'KF'){
								$str .= '<td><div class="form-group">								      
								      <div class="col-md-11" style="padding-right:0px;">
								        <input type="text"  style="padding:1px;" class="form-control number" '.(($readonly)? 'readonly="readonly"':'data-no_pengajuan_harga="'.$no_pengajuan_harga.'" data-tgl_pengajuan="'.$data['tgl_pengajuan'].'" data-jumlah="'.$data['estimasi_jumlah'].'" data-kode_farm="'.$data['kode_farm'].'" data-kode_barang="'.$data['kode_barang'].'"').' value="'.formatAngka($data['harga_jual'],0).'"/>
								      </div>								      
								    </div></td>';
							}							
							
						}
						if ($loop == 0){							
							$str = str_replace('~no_urut~', $no_urut, $str);
							$str .= '<td data-statusph="'.$status_ph.'" class="keterangan" style="text-align:left" rowspan="~~">'.$keterangan.'</td>';
							if(!in_array($level_user,array('KDLOG','KF'))){
								$str .= '<td data-statusph="'.$status_ph.'" class="aksi" rowspan="~~">'.$aksi.'</td>';
							}
						}
						$str .= '</tr>';
						$loop++;
					}
					$str = str_replace('~~', $loop, $str);
					echo $str;
				}
				 ?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
		</div>
	</div>
</div>