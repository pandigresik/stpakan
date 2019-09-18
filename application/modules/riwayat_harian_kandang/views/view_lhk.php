<div class="panel panel-default">
  <div class="panel-heading">Laporan Harian Kandang</div>
  <div class="panel-body">
	<div class="col-md-12">
		
		
		<div class="row">
			<div class="col-md-4">
				
			</div>
			<div class="col-md-4">
				
			</div>
			<div class="col-md-4">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_doc_in">Tanggal DOC-In</label>
						<input type="text" class="form-control input-sm field_input" id="inp_doc_in" disabled value="<?php echo tglIndonesia($tgl_doc_in,'-',' ') ?>">
					</div>
				</form>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-3">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_flock">Tgl. LHK</label>
						<input type="text" class="form-control input-sm field_input" style="width:100px" name="flock" disabled value="<?php echo tglIndonesia($tgl_lhk,'-',' ') ?>">
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-2">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_umur">Umur</label>
						<input type="text" class="form-control input-sm field_input"  style="width:100px" name="kandang" id="inp_umur" disabled value="<?php echo $umur ?>">
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-2">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_doc_in">BB Jantan</label>
						<input type="text" class="form-control input-sm field_input inp-numeric" style="width:50px" id="inp_bb_ja" value="<?php echo $rhk->J_BERAT_BADAN ?>"  disabled> Kg
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-2">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_doc_in">BB Betina</label>
						<input type="text" class="form-control input-sm field_input inp-numeric" style="width:50px" id="inp_bb_be" value="<?php echo $rhk->B_BERAT_BADAN ?>"  disabled> Kg
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-2">
				<center>
				<form class="form-inline">
					<div class="form-group">
						
						<!--<label class="checkbox-inline">
							<input type="checkbox" id="inp_tutupsiklus" value="T"> <strong>Tutup Siklus</strong>
						</label>
						-->
					</div>
				</form>
				</center>
			</div>
		</div>
		
		<br/>
		
		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Laporan Harian Kandang - Populasi</div>
				<div class="panel-body">
					<div class="col-md-12">
						<table class="table table-bordered table-condensed custom_table">
							<thead>
								<tr>
									<th class="vert-align col-md-1" rowspan="2">Jenis<br>Kelamin</th>
									<th class="vert-align col-md-1" rowspan="2">Populasi<br>Awal</th>
									<th class="vert-align col-md-1" colspan="2">Penambahan</th>
									<th class="vert-align col-md-1" colspan="8">Pengurangan</th>
									<th class="vert-align col-md-1" rowspan="2">Populasi<br/>Akhir</th>
								</tr>
								<tr>
									<th class="vert-align col-md-1" >Terima dari<br/>Kandang<br/>Lain</th>
									<th class="vert-align col-md-1" >Lain-lain</th>
									<th class="vert-align col-md-1" >Mati</th>
									<th class="vert-align col-md-1" >Afkir</th>
									<th class="vert-align col-md-1" >Pindah ke<br/>Kandang<br>Lain</th>
									<th class="vert-align col-md-1" >Sexslip</th>
									<th class="vert-align col-md-1" >Kanibal</th>
									<th class="vert-align col-md-1" >Campur</th>
									<th class="vert-align col-md-1" >Seleksi</th>
									<th class="vert-align col-md-1" >Lain-lain</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<?php 
										$populasiJantanAwal = ($rhk->J_MATI + $rhk->J_AFKIR + $rhk->J_SEXSLIP + $rhk->J_KANIBAL+ $rhk->J_CAMPUR+ $rhk->J_SELEKSI+ $rhk->J_LAIN2+ $rhk->J_JUMLAH);
										$populasiBetinaAwal = ($rhk->B_MATI + $rhk->B_AFKIR + $rhk->B_SEXSLIP + $rhk->B_KANIBAL+ $rhk->B_CAMPUR+ $rhk->B_SELEKSI+ $rhk->B_LAIN2+ $rhk->B_JUMLAH);
									
									?>
									<td class="right-align-sm">Jantan</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAwalJantan" value="<?php echo angkaRibuan($populasiJantanAwal) ?>" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahJantan" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahJantanLain" value="0" disabled ></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanMati" value="<?php echo angkaRibuan($rhk->J_MATI) ?>" disabled ></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanAfkir" value="<?php echo angkaRibuan($rhk->J_AFKIR) ?>" disabled ></td>
									<td>
										
											<input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantan" value="0" disabled>
											
																		
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanSexslip" value="<?php echo angkaRibuan($rhk->J_SEXSLIP) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanKanibal" value="<?php echo angkaRibuan($rhk->J_KANIBAL) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanCampur" value="<?php echo angkaRibuan($rhk->J_CAMPUR) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanSeleksi" value="<?php echo angkaRibuan($rhk->J_SELEKSI) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanLain" value="<?php echo angkaRibuan($rhk->J_LAIN2) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAkhirJantan" value="<?php echo angkaRibuan($rhk->J_JUMLAH) ?>" disabled /></td>
								</tr>
								<tr>
									<td class="right-align-sm">Betina</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAwalBetina" value="<?php echo angkaRibuan($populasiBetinaAwal) ?>" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahBetina" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahBetinaLain" value="0" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanMati" value="<?php echo angkaRibuan($rhk->B_MATI) ?>" disabled ></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanAfkir" value="<?php echo angkaRibuan($rhk->B_AFKIR) ?>" disabled ></td>
									<td>
										
											<input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantan" value="0" disabled>
											
																		
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanSexslip" value="<?php echo angkaRibuan($rhk->B_SEXSLIP) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanKanibal" value="<?php echo angkaRibuan($rhk->B_KANIBAL) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanCampur" value="<?php echo angkaRibuan($rhk->B_CAMPUR) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanSeleksi" value="<?php echo angkaRibuan($rhk->B_SELEKSI) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanLain" value="<?php echo angkaRibuan($rhk->B_LAIN2) ?>" disabled></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAkhirJantan" value="<?php echo angkaRibuan($rhk->B_JUMLAH) ?>" disabled /></td>
								</tr>
								<tr class="rasio">
									<td class="right-align-sm">Rasio</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAwalRasio" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahRasio" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahRasioLain" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangRasioMati" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangRasioAfkir" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangRasio" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangRasioSexslip" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangRasioKanibal" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangRasioCampur" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangRasioSeleksi" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangRasioLain" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAkhirRasio" value="0" disabled /></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Laporan Harian Kandang - Pakan</div>
				<div class="panel-body">
					<div class="col-md-10">
						<table class="table table-bordered table-condensed custom_table">
							<thead>
								<tr>
									<th class="vert-align" rowspan="2">Jenis<br>Kelamin</th>
									<th class="vert-align" rowspan="2">Pakan</th>
									<th class="vert-align" colspan="2">Stok Awal</th>
									<th class="vert-align" colspan="2">Kirim</th>
									<th class="vert-align" colspan="2">Terpakai</th>
									<th class="vert-align" colspan="2">Stok AKhir</th>
								</tr>
								<tr>
									<th width="90px" class="vert-align" >Kg</th>
									<th width="90px" class="vert-align" >Sak</th>
									<th width="90px" class="vert-align" >Kg</th>
									<th width="90px" class="vert-align" >Sak</th>
									<th width="90px" class="vert-align" >Kg</th>
									<th width="90px" class="vert-align" >Sak</th>
									<th width="90px" class="vert-align" >Kg</th>
									<th width="90px" class="vert-align" >Sak</th>
								</tr>
							</thead>
							<tbody>
							<?php 
								if(!empty($rhk_pakan)){
									foreach($rhk_pakan as $pk){
										
										echo '<tr>';										
										echo '<td>'.convertKode('jenis_kelamin',$pk->JENIS_KELAMIN).'</td>';
										echo '<td>'.$pk->KODE_BARANG.'</td>';
										echo '<td></td>';
										echo '<td></td>';										
										echo '<td class="number">'.formatAngka($pk->BRT_TERIMA,2).'</td>';
										echo '<td class="number">'.formatAngka($pk->JML_TERIMA,0).'</td>';
										echo '<td class="number '.$class_pakan[$pk->JENIS_KELAMIN].'">'.formatAngka($pk->BRT_PAKAI,2).'</td>';
										echo '<td class="number">'.formatAngka($pk->JML_PAKAI,0).'</td>';
										echo '<td class="number">'.formatAngka($pk->BRT_AKHIR,2).'</td>';
										echo '<td class="number">'.formatAngka($pk->JML_AKHIR,0).'</td>';
										echo '</tr>';
									}
								}

							?>
							</tbody>
						</table>
					</div>
					<div class="col-md-4"></div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Laporan Harian Kandang - Vaksin dan Obat</div>
				<div class="panel-body">
					<div class="col-md-6">
						<h4>Obat/Vitamin</h4>
						<table class="table table-bordered table-condensed custom_table">
							<thead>
								<tr>
									<th class="vert-align col-md-1" rowspan="2">Nama Barang</th>
									<th class="vert-align col-md-1" rowspan="2">Kode Barang</th>
									<th class="vert-align col-md-1" colspan="2">Kuantitas Pakai (kg)</th>
									<th class="vert-align col-md-1" rowspan="2">Keterangan</th>
									<th class="vert-align col-md-1" rowspan="2"></th>
								</tr>
								<tr>
									<th class="vert-align col-md-1" >Jantan</th>
									<th class="vert-align col-md-1" >Betina</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<div class="col-md-6">
						<h4>Vaksin</h4>
						<table  class="table table-bordered table-condensed custom_table">
							<thead>
								<tr>
									<th class="vert-align col-md-1" rowspan="2">Nama Barang</th>
									<th class="vert-align col-md-1" rowspan="2">Kode Barang</th>
									<th class="vert-align col-md-1" colspan="2">Kuantitas Pakai (kg)</th>
									<th class="vert-align col-md-1" rowspan="2">Keterangan</th>
									<th class="vert-align col-md-1" rowspan="2"></th>
								</tr>
								<tr>
									<th class="vert-align col-md-1" >Jantan</th>
									<th class="vert-align col-md-1" >Betina</th>
								</tr>
							</thead>
							<tbody>
							<?php 
							/*
								if(!empty($rhk_vaksin)){
									foreach($rhk_vaksin as $pk){
										echo '<tr>';										
										echo '<td>'.convertKode('jenis_kelamin',$pk->JENIS_KELAMIN).'</td>';
										echo '<td>'.$pk->KODE_BARANG.'</td>';
										echo '<td>'.$pk->KODE_BARANG.'</td>';
										echo '<td class="number">'.$pk->BERAT_PAKAI.'</td>';
										echo '<td></td>';
										echo '</tr>';
									}
								}
							*/
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Laporan Harian Kandang - Produksi</div>
				<div class="panel-body">
					<div class="col-md-12">
						<h4>Detail Produksi</h4>
						<table class="table table-bordered table-condensed custom_table">
							<thead>
								<tr>
									<th class="vert-align col-md-1">Baik</th>
									<th class="vert-align col-md-1">Besar</th>
									<th class="vert-align col-md-1">Tipis</th>
									<th class="vert-align col-md-1">Kecil</th>
									<th class="vert-align col-md-1">Kotor</th>
									<th class="vert-align col-md-1">Abnormal</th>
									<th class="vert-align col-md-1">IB</th>
									<th class="vert-align col-md-1">Retak</th>
									<th class="vert-align col-md-1">Hancur</th>
									<th class="vert-align col-md-1">Jumlah</th>
									<th class="vert-align col-md-2">Keterangan</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php 
								if(!empty($rhk_produksi)){
									
									echo '<tr>';
									echo '<td class="number">'.$rhk_produksi->PROD_BAIK.'</td>';
									echo '<td class="number">'.$rhk_produksi->PROD_BESAR.'</td>';
									echo '<td class="number">'.$rhk_produksi->PROD_TIPIS.'</td>';
									echo '<td class="number">'.$rhk_produksi->PROD_KECIL.'</td>';
									echo '<td class="number">'.$rhk_produksi->PROD_KOTOR.'</td>';
									echo '<td class="number">'.$rhk_produksi->PROD_ABNORMAL.'</td>';
									echo '<td class="number">'.$rhk_produksi->PROD_IB.'</td>';
									echo '<td class="number">'.$rhk_produksi->PROD_RETAK.'</td>';
									echo '<td class="number">'.$rhk_produksi->PROD_HANCUR.'</td>';
									echo '<td class="number">'.$rhk_produksi->BERAT_TOTAL.'</td>';
									echo '<td class="number">'.$rhk_produksi->KETERANGAN2.'</td>';
									echo '</tr>';
									
								}

							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<table cellpadding="10px" cellspacing="10px">
				<tr>
					<td style="width:100px"><label for="inp_berat_telur" class="control-label">Berat Telur</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px" id="inp_berat_telur" name="berat_telur" value="<?php echo isset($rhk_produksi->BERAT_TOTAL) ? $rhk_produksi->BERAT_TOTAL : '' ?>"  disabled>
							<span class="input-group-addon" id="basic-addon2">gr</span>
						</div>
					</td>
					<td style="width:150px;padding-left:30px;"><label for="inp_cv_jantan" class="control-label">CV Jantan</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_cv_jantan" name="cv_jantan" value="<?php echo isset($rhk_produksi->CV_JANTAN) ? $rhk_produksi->CV_JANTAN : '' ?>" disabled>
							<span class="input-group-addon" id="basic-addon2">%</span>
						</div>
					</td>
					<td style="width:170px;padding-left:25px;"><label for="inp_uniformity_jantan" class="control-label">Uniformity Jantan</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_uniformity_jantan" name="uniformity_jantan"  value="<?php echo isset($rhk->J_UNIFORMITY) ? $rhk->J_UNIFORMITY : '' ?>" disabled>
							<span class="input-group-addon" id="basic-addon2">%</span>
						</div>
					</td>
					<td style="width:200px;padding-left:30px;"><label for="inp_dayahidup_jantan" class="control-label">Daya Hidup Jantan</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_dayahidup_jantan" name="dayahidup_jantan" value="<?php echo isset($rhk->J_DAYA_HIDUP) ? $rhk->J_DAYA_HIDUP : '' ?>" disabled>
							<span class="input-group-addon" id="basic-addon2">%</span>
						</div>
					</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td style="width:150px;padding-left:30px;"><label for="inp_cv_betina" class="control-label">CV Betina</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_cv_betina" name="cv_betina"  value="<?php echo isset($rhk_produksi->CV_BETINA) ? $rhk_produksi->CV_BETINA : '' ?>" disabled>
							<span class="input-group-addon" id="basic-addon2">%</span>
						</div>
					</td>
					
					<td style="width:170px;padding-left:25px;"><label for="inp_uniformity_betina" class="control-label">Uniformity Betina</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_uniformity_betina" name="uniformity_betina"  value="<?php echo isset($rhk->B_UNIFORMITY) ? $rhk->B_UNIFORMITY : '' ?>" disabled>
							<span class="input-group-addon" id="basic-addon2">%</span>
						</div>
					</td>
					
					
					<td style="width:200px;padding-left:30px;"><label for="inp_cv_betina" class="control-label">Daya Hidup Betina</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_dayahidup_betina" name="dayahidup_betina" value="<?php echo isset($rhk->B_DAYA_HIDUP) ? $rhk->B_DAYA_HIDUP : '' ?>" disabled>
							<span class="input-group-addon" id="basic-addon2">%</span>
						</div>
					</td>
				</tr>
			</table>
		</div>
		
	</div>	
  </div>
</div>

