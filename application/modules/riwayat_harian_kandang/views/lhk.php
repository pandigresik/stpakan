<div class="panel panel-default">
  <div class="panel-heading">Laporan Harian Kandang</div>
  <div class="panel-body">
	<div class="col-md-12">
		<center><h1><?php echo $nama_farm;?></h1></center>
		<div class="row">
			<button type="button" name="tombolSimpan" id="btnSimpan" class="btn btn-primary" style="display:none;">Simpan</button>
			<!--<button type="button" name="tombolSisa" id="btnSisa" class="btn btn-primary">Sisa</button>
			<button type="button" name="tombolTest" id="btnTest" class="btn btn-primary">Test</button>
			<button type="button" name="tombolTestLoad" id="btnTestLoad" class="btn btn-primary">Test Load</button>-->
			<!--<button type="button" name="tombolTestLoad" id="btnTestLoad2" class="btn btn-primary">Show uniform modal</button>-->
			<br/><br/>
		</div>
		<div class="row">
			<div class="col-md-4">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_kandang">Kandang</label>
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_farm" value="<?php echo $kode_farm;?>">
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_nama_farm" value="<?php echo $nama_farm;?>">
						<input type="hidden" class="form-control input-sm field_input" name="today" id="inp_today" value="<?php echo $today;?>">
						<input type="hidden" class="form-control input-sm field_input" name="doc_in_jantan" id="inp_doc_in_jantan" value="">
						<input type="hidden" class="form-control input-sm field_input" name="doc_in_betina" id="inp_doc_in_betina" value="">
						<input type="text" class="form-control input-sm field_input" name="kandang" id="inp_kandang" style="text-align:left">
					</div>
				</form>
			</div>
			<div class="col-md-4">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_flock">Flock</label>
						<input type="text" class="form-control input-sm field_input" name="flock" id="inp_flock" disabled>
					</div>
				</form>
			</div>
			<div class="col-md-4">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_doc_in">Tanggal DOC-In</label>
						<input type="text" class="form-control input-sm field_input" id="inp_doc_in" disabled>
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
						<div class="form-group">
							<div class="input-group date" id="div_tgl_lhk">
								<input type="text" name="startDate" style="width:120px;" class="form-control disabled" id="inp_tgl_lhk" readonly />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
						<!-- Diganti pilih tanggal-->
						<!--<input type="text" class="form-control input-sm field_input" style="width:100px" name="flock" id="inp_tgl_lhk" disabled>-->
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-2">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_umur">Umur</label>
						<input type="text" class="form-control input-sm field_input"  style="width:100px" name="kandang" id="inp_umur" disabled>
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-2">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_doc_in">BB Jantan</label>
						<input type="text" class="form-control input-sm field_input inp-numeric"  style="width:50px" id="inp_bb_ja" value="0" onkeyup="cekDecimal(this)"> Kg
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-2">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_doc_in">BB Betina</label>
						<input type="text" class="form-control input-sm field_input inp-numeric" style="width:50px" id="inp_bb_be" value="0" onkeyup="cekDecimal(this)"> Kg
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-2">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<button type="button" name="tombolTutupSiklus" id="btnTutupSiklus" class="btn btn-primary" style="display:none;">Tutup Siklus</button>
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
						<table id="lhk_populasi" class="table table-bordered table-condensed">
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
									<td class="right-align-sm">Jantan</td>
									<td>
										<input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAwalJantan" value="0" disabled />
										<input type="hidden" id="inp_j_daya_hidup"/>
										<input type="hidden" id="inp_j_jml_pembagi"/>										
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahJantan" value="0" disabled /></td>
									<td>
										<!--<input type="text" class="form-control input-sm inp-numeric" id="inp_tambahJantanLain" value="0" onkeyup="cekNumerikPopluasi(this)">-->
										<div class="input-group">
											<input type="text" class="form-control input-sm inp-numeric" id="inp_tambahJantanLain" value="0" disabled>
											<span class="input-group-btn">
												<button class="btn btn-sm btn-default disabled" id="btnBrowseTambahJantanLain" onclick="showModalPenambahanLain('J')" type="button">...</button>
											</span>
										</div>	
									
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanMati" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanAfkir" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td>
										<div class="input-group">
											<input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantan" value="0" disabled>
											<input type="hidden" id="inp_j_pindah_semu">
											<span class="input-group-btn">
												<button class="btn btn-sm btn-default disabled" id="btnBrowseKurangJantan" onclick="showModalPengurangan('J')" type="button">...</button>
											</span>
										</div>								
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanSexslip" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanKanibal" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanCampur" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanSeleksi" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td>
										<!--<input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanLain" value="0" onkeyup="cekNumerikPopluasi(this)">-->
										<div class="input-group">
											<input type="text" class="form-control input-sm inp-numeric" id="inp_kurangJantanLain" value="0" disabled>
											<span class="input-group-btn">
												<button class="btn btn-sm btn-default disabled" id="btnBrowseKurangJantanLain" onclick="showModalPenguranganLain('J')" type="button">...</button>
											</span>
										</div>	
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAkhirJantan" value="0" disabled /></td>
								</tr>
								<tr>
									<td class="right-align-sm">Betina</td>
									<td>
										<input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAwalBetina" value="0" disabled />
										<input type="hidden" id="inp_b_daya_hidup"/>
										<input type="hidden" id="inp_b_jml_pembagi"/>
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahBetina" value="0" disabled /></td>
									<td>
										<!--<input type="text" class="form-control input-sm inp-numeric" id="inp_tambahBetinaLain" value="0" onkeyup="cekNumerikPopluasi(this)">-->
										<div class="input-group">
											<input type="text" class="form-control input-sm inp-numeric" id="inp_tambahBetinaLain" value="0" disabled>
											<span class="input-group-btn">
												<button class="btn btn-sm btn-default disabled" id="btnBrowseTambahBetinaLain" onclick="showModalPenambahanLain('B')" type="button">...</button>
											</span>
										</div>	
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangBetinaMati" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangBetinaAfkir" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td>
										<div class="input-group">
											<input type="text" class="form-control input-sm inp-numeric" id="inp_kurangBetina" value="0" disabled>
											<input type="hidden" id="inp_b_pindah_semu">
											<span class="input-group-btn">
												<button class="btn btn-sm btn-default disabled" id="btnBrowseKurangBetina" onclick="showModalPengurangan('B')" type="button">...</button>
											</span>
										</div>								
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangBetinaSexslip" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangBetinaKanibal" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangBetinaCampur" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangBetinaSeleksi" value="0" onkeyup="cekNumerikPopluasi(this)"></td>
									<td>
										<!--<input type="text" class="form-control input-sm inp-numeric" id="inp_kurangBetinaLain" value="0" onkeyup="cekNumerikPopluasi(this)">-->
										<div class="input-group">
											<input type="text" class="form-control input-sm inp-numeric" id="inp_kurangBetinaLain" value="0" disabled>
											<span class="input-group-btn">
												<button class="btn btn-sm btn-default disabled" id="btnBrowseKurangBetinaLain" onclick="showModalPenguranganLain('B')" type="button">...</button>
											</span>
										</div>	
									</td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAkhirBetina" value="0" disabled /></td>
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
						<table id="lhk_pakan" class="table table-bordered table-condensed">
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
						<table id="lhk_obat" class="table table-bordered table-condensed">
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
						<table id="lhk_vaksin" class="table table-bordered table-condensed">
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
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Laporan Harian Kandang - Produksi</div>
				<div class="panel-body">
					<div class="col-md-12">
						<h4>Detail Produksi</h4>
						<table id="lhk_produksi" class="table table-bordered table-condensed">
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
								<tr>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_baik[]" value="0" onkeyup="cekNumerikProduksi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_besar[]" value="0" onkeyup="cekNumerikProduksi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_tipis[]" value="0" onkeyup="cekNumerikProduksi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_kecil[]" value="0" onkeyup="cekNumerikProduksi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_kotor[]" value="0" onkeyup="cekNumerikProduksi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_abnormal[]" value="0" onkeyup="cekNumerikProduksi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_ib[]" value="0" onkeyup="cekNumerikProduksi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_retak[]" value="0" onkeyup="cekNumerikProduksi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_hancur[]" value="0" onkeyup="cekNumerikProduksi(this)"></td>
									<td><input type="text" class="form-control input-sm inp-numeric" name="inp_prod_jumlah[]" value="0" disabled></td>
									<td><input type="text" class="form-control input-sm" name="inp_prod_keterangan[]"></td>
									<td>
									<button type="button" data-toggle="tooltip" onclick="tambahProduksi(this)" title="Tambah Produksi" class="btn btn-sm btn-primary">
										<i class="glyphicon glyphicon-plus-sign"></i>
									</button>
									</td>
								</tr>
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
							<input type="text" class="form-control input-group inp-numeric" style="width:80px" id="inp_berat_telur" name="berat_telur" value="0"  onkeyup="cekDecimal(this)">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button" disabled>gr</button>
							</span>
						</div>
					</td>
					<td style="width:150px;padding-left:30px;"><label for="inp_cv_jantan" class="control-label">CV Jantan</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_cv_jantan" name="cv_jantan" value="0"  onkeyup="cekDecimal(this)">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button" disabled>%</button>
							</span>
						</div>
					</td>
					<td style="width:170px;padding-left:25px;"><label for="inp_uniformity_jantan" class="control-label">Uniformity Jantan</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" disabled style="width:60px" id="inp_uniformity_jantan" name="uniformity_jantan" value="0" onkeyup="cekDecimal(this)">
							<!--<input type="text" class="form-control input-group inp-numeric" style="width:100px" id="inp_uniformity_jantan" name="uniformity_jantan" value="0" onkeyup="cekDecimal(this)">-->
							<div class="input-group-btn">
								<button class="btn btn-default" type="button" id="btnBrowseUniformityJantan">...</button>
								<button class="btn btn-default" type="button" disabled>%</button>
							</div>
							
						</div>
					</td>
					<td style="width:200px;padding-left:30px;"><label for="inp_dayahidup_jantan" class="control-label">Daya Hidup Jantan</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_dayahidup_jantan" name="dayahidup_jantan" value="0" onkeyup="cekDecimal(this)" disabled>
							<span class="input-group-btn">
								<button class="btn btn-default" type="button" disabled>%</button>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td style="width:150px;padding-left:30px;"><label for="inp_cv_betina" class="control-label">CV Betina</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_cv_betina" name="cv_betina" value="0" onkeyup="cekDecimal(this)">
							<span class="input-group-btn">
								<button class="btn btn-default" type="button" disabled>%</button>
							</span>
						</div>
					</td>
					
					<td style="width:170px;padding-left:25px;"><label for="inp_uniformity_betina" class="control-label">Uniformity Betina</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" disabled style="width:60px" id="inp_uniformity_betina" name="uniformity_betina" value="0" onkeyup="cekDecimal(this)">
							<!--<input type="text" class="form-control input-group inp-numeric" style="width:100px" id="inp_uniformity_betina" name="uniformity_betina" value="0" onkeyup="cekDecimal(this)">-->
							<div class="input-group-btn">
								<button class="btn btn-default" type="button" id="btnBrowseUniformityBetina">...</button>
								<button class="btn btn-default" type="button" disabled>%</button>
							</div>
						</div>
					</td>
					
					
					<td style="width:200px;padding-left:30px;"><label for="inp_cv_betina" class="control-label">Daya Hidup Betina</label></td>
					<td>
						<div class="input-group">
							<input type="text" class="form-control input-group inp-numeric" style="width:80px"  id="inp_dayahidup_betina" name="dayahidup_betina" value="0" onkeyup="cekDecimal(this)" disabled>
							<span class="input-group-btn">
								<button class="btn btn-default" type="button" disabled>%</button>
							</span>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>	
  </div>
</div>

<div class="modal fade" id="modal_pengurangan_ayam" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-body">
			<div class="panel panel-primary">
				<div class="panel-heading">Pengurangan Ayam</div>
				<div class="panel-body">
					<div class="col-md-12">
						<table id="md_pengurangan_ayam" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th class="vert-align col-md-3" rowspan="2">Tujuan Kandang</th>
									<th class="vert-align col-md-2" colspan="2">Jumlah Pindah</th>
									<th class="vert-align col-md-2" rowspan="2">Keterangan</th>
									<th class="vert-align col-md-1" rowspan="2">No. Berita Acara</th>
									<th class="vert-align col-md-1" rowspan="2"></th>
								</tr>
								<tr>
									<th class="vert-align col-md-1">Jantan</th>
									<th class="vert-align col-md-1">Betina</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="col-md-12 has-error" id="pindahKandangErrMsgKet" style="display:none;color:red;"></div>
			<div class="col-md-12 has-error" id="pindahKandangErrMsgKet" style="display:none;color:red;"></div>
		</div>
		
		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolBatal" id="btnBatalPengurangan" class="btn btn-default">Batal</button>
				<button type="button" name="tombolSimpan" id="btnSimpanPengurangan" class="btn btn-primary">Simpan</button>
			</div>
		</div>
    </div> 
  </div>
</div>

<div class="modal fade" id="modal_penambahan_ayam_lain" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:70%">
    <div class="modal-content">
		<div class="modal-header">
			<center><h4 class="modal-title">Penambahan Lain-lain</h4></center>
		</div>
		<div class="modal-body">
			<div class="panel panel-primary">
				<div class="panel-heading">Penambahan Lain-lain</div>
				<div class="panel-body">
					<div class="col-md-12">
						<table id="md_penambahan_ayam_lain" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th class="vert-align col-md-4" colspan="2">Jumlah Penambahan Lain-lain</th>
									<th class="vert-align col-md-2" rowspan="2">Keterangan</th>
									<th class="vert-align col-md-5" colspan="2">Berita Acara</th>
								</tr>
								<tr>
									<th class="vert-align col-md-2">Jantan</th>
									<th class="vert-align col-md-2">Betina</th>
									<th class="vert-align col-md-2">No. Berita Acara</th>
									<th class="vert-align col-md-3">Lampiran Berita Acara</th>
								</tr>
							</thead>
							<tbody>							
								<tr>
									<td><input type="number" class="form-control input-sm" name="slider_tambah_lain_jantan[]" value="" onchange="checkBatasJantanLain(this)"></td>
									<td><input type="number" class="form-control input-sm" name="slider_tambah_lain_betina[]" value="" onchange="checkBatasBetinaLain(this)"></td>
									<td><input type="text" class="form-control" name="inp_tambah_jantan_lain_ket[]"></td>
									<td><input type="text" class="form-control" name="inp_tambah_jantan_lain_nomemo[]"></td>
									<td>
										<div class="input-group">
											<span class="input-group-btn">
												<span class="btn btn-primary btn-file">
													Browse <input type="file" name="uploadFileTambahLain[]">
												</span>
											</span>
											<input class="form-control" type="text" readonly="">
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="col-md-12 has-error" id="tambahLainErrMsgKet" style="display:none;color:red;"></div>
		</div>
		
		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolBatal" id="btnBatalPenambahanLain" class="btn btn-default">Batal</button>
				<button type="button" name="tombolSimpan" id="btnSimpanPenambahanLain" class="btn btn-primary">Simpan</button>
			</div>
		</div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_pengurangan_ayam_lain" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:70%">
    <div class="modal-content">
		<div class="modal-header">
			<center><h4 class="modal-title">Pengurangan Lain-lain</h4></center>
		</div>
		<div class="modal-body">
			<div class="panel panel-primary">
				<div class="panel-heading">Pengurangan Lain-lain</div>
				<div class="panel-body">
					<div class="col-md-12">
						<table id="md_pengurangan_ayam_lain" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th class="vert-align col-md-4" colspan="2">Jumlah Pengurangan Lain-lain</th>
									<th class="vert-align col-md-2" rowspan="2">Keterangan</th>
									<th class="vert-align col-md-5" colspan="2">Berita Acara</th>
								</tr>
								<tr>
									<th class="vert-align col-md-2">Jantan</th>
									<th class="vert-align col-md-2">Betina</th>
									<th class="vert-align col-md-2">No. Berita Acara</th>
									<th class="vert-align col-md-3">Lampiran Berita Acara</th>
								</tr>
							</thead>
							<tbody>							
								<tr>
									<td><input type="number" class="form-control input-sm" name="slider_kurang_lain_jantan[]" value="" onchange="checkBatasJantanLain(this)"></td>
									<td><input type="number" class="form-control input-sm" name="slider_kurang_lain_betina[]" value="" onchange="checkBatasBetinaLain(this)"></td>
									<td><input type="text" class="form-control" name="inp_kurang_jantan_lain_ket[]"></td>
									<td><input type="text" class="form-control" name="inp_kurang_jantan_lain_nomemo[]"></td>
									<td>
										<div class="input-group">
											<span class="input-group-btn">
												<span class="btn btn-primary btn-file">
													Browse <input type="file" name="uploadFileKurangLain[]">
												</span>
											</span>
											<input class="form-control" type="text" readonly="">
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="col-md-12 has-error" id="kurangLainErrMsgKet" style="display:none;color:red;"></div>
		</div>
		
		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolBatal" id="btnBatalPenguranganLain" class="btn btn-default">Batal</button>
				<button type="button" name="tombolSimpan" id="btnSimpanPenguranganLain" class="btn btn-primary">Simpan</button>
			</div>
		</div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal_sisa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-body">
			<form name="returForm" method="post" id="returForm" action="riwayat_harian_kandang/printRetur" target="_blank">
			
			<div class="row">
				<div class="col-md-12">
					<center><h1>Retur Pakan Ke Gudang</h1></center>
					<center><h3>Farm <span id="titlefarm"></span></h3></center>
				</div>
			</div>
			<br/>
			<div class="row">
				<input type="hidden" name="inp_print_kodefarm" id="inp_print_kodefarm">
				<input type="hidden" name="inp_print_farm" id="inp_print_farm">
			</div>
			<div class="row">
				<div class="col-md-2">Kandang</div>
				<div class="col-md-5">: <span id="print_nama_kandang"></span><input type="hidden" name="inp_print_kandang" id="inp_print_kandang"></div>
				<div class="col-md-3">Tanggal Tutup Siklus</div>
				<div class="col-md-2">: <span id="print_tgl_lhk"></span><input type="hidden" name="inp_print_tgl" id="inp_print_tgl"></div>
			</div>
			
			<br/><br/>
			
			<div class="row">
				<div class="col-md-12">
					<table id="tb_sisa" class="table table-condensed table-striped table-bordered">
						<thead>
							<tr>
								<th class="vert-align">Kode Pakan</th>
								<th class="vert-align">Nama Pakan</th>
								<th class="vert-align">Jumlah<br>(zak)</th>
								<th class="vert-align">Berat<br>(kg)</th>
								<th class="vert-align">Bentuk<br>Pakan</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="vert-align">11-1234-21</td>
								<td class="vert-align">P 3 COBB</td>
								<td class="vert-align">10</td>
								<td class="vert-align">501.23</td>
								<td class="vert-align">CRUMBLE</td>
							</tr>
							<tr>
								<td class="vert-align">11-1234-22</td>
								<td class="vert-align">PJB COBB</td>
								<td class="vert-align">2</td>
								<td class="vert-align">120.25</td>
								<td class="vert-align">CRUMBLE</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<br/>
			
			Tabel di atas adalah sisa pakan dari Kandang pada Akhir Siklus yang dikembalikan ke Gudang.
			<br/><br/><br/>
			
			<table class="table borderless">
				<tr>
					<td class="col-md-4 vert-align borderless">Kepala Unit/Farm</td>
					<td class="col-md-4 vert-align borderless">Admin Gudang</td>
					<td class="col-md-4 vert-align borderless">Pengawas Kandang</td>
				</tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr>
					<td class="col-md-4 vert-align borderless">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
					<td class="col-md-4 vert-align borderless">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
					<td class="col-md-4 vert-align borderless"><button name="tombolPrint" id="btnPrint" class="btn btn-primary">Simpan</button></td>
				</tr>
			</table>
			</form>
		</div>
    </div>
  </div>
</div>

<div class="modal bs-example-modal-lg" id="modal_uniformity" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Uniformity</h4>
		</div>
		<div class="modal-body">
			<div class="panel panel-default">
				<div class="panel-body">
					<center><h2>Farm <?php echo $nama_farm;?></h2></center>
					<center><h4>Tabel Berat Badan</h4></center>
					<div class="row">
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_umur" style="width:100px">Jenis Kelamin</label>
									<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_jk">
								</div>
							</form>
						</div>
						<div class="col-md-6">
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_umur" style="width:100px">Umur</label>
									<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_umur" onkeyup="cekNumerik(this)">
								</div>
								<div class="form-group">
									<span>hari</span>
								</div>
							</form>
						</div>
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_tberat" style="width:100px">Target Berat</label>
									<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_tberat" onkeyup="cekNumerik(this)">
								</div>
								<div class="form-group">
									<span>gram</span>
								</div>
							</form>
						</div>
					</div>
					<br/>
					<div class="panel panel-primary">
						<div class="panel-heading">Detail Penimbangan</div>
						<div class="panel-body">
							<div class="col-md-12">
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_tsampling" style="width:120px">Total Sampling</label>
										<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_tsampling" onkeyup="cekNumerik(this)">
									</div>
									<div class="form-group">
										<button type="button" name="btnSimpanUniform" id="btnOKTimbang"  class="btn btn-primary">OK</button>
									</div>
								</form>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<table id="preview_detail_penimbangan" class="table table-bordered table-condensed">
									<thead>
										<tr>
											<th class="vert-align">Berat(g)</th>
											<th class="vert-align">Jumlah Ayam<br>(ekor)</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div class="col-md-6">
							<p>
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_uniformity" style="width:120px;font-weight:normal">Uniformity</label>
										<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_uniformity" onkeyup="cekNumerik(this)">
									</div>
									<div class="form-group">
										<span>%</span>
									</div>
								</form>
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_uniformity" style="width:120px;font-weight:normal">Status</label>
										<label for="inp_uni_uniformity" id="lbl_status_uniformity" style="width:120px"></label>
									</div>
								</form>
							</p>
							</div>
						</div>
					</div>					
				</div>
			</div>
		</div>
    </div>
  </div>
</div>

<div class="modal bs-example-modal-lg" id="modal_uniformity_betina" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Uniformity</h4>
		</div>
		<div class="modal-body">
			<div class="panel panel-default">
				<div class="panel-body">
					<center><h2>Farm <?php echo $nama_farm;?></h2></center>
					<center><h4>Tabel Berat Badan</h4></center>
					<div class="row">
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_umur" style="width:100px">Jenis Kelamin</label>
									<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_jk_betina">
								</div>
							</form>
						</div>
						<div class="col-md-6">
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_umur" style="width:100px">Umur</label>
									<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_umur_betina" onkeyup="cekNumerik(this)">
								</div>
								<div class="form-group">
									<span>hari</span>
								</div>
							</form>
						</div>
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_tberat" style="width:100px">Target Berat</label>
									<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_tberat_betina" onkeyup="cekNumerik(this)">
								</div>
								<div class="form-group">
									<span>gram</span>
								</div>
							</form>
						</div>
					</div>
					<br/>
					<div class="panel panel-primary">
						<div class="panel-heading">Detail Penimbangan</div>
						<div class="panel-body">
							<div class="col-md-12">
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_tsampling" style="width:120px">Total Sampling</label>
										<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_tsampling_betina" onkeyup="cekNumerik(this)">
									</div>
									<div class="form-group">
										<button type="button" name="btnSimpanUniform" id="btnOKTimbang_betina" class="btn btn-primary">OK</button>
									</div>
								</form>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<table id="preview_detail_penimbangan_betina" class="table table-bordered table-condensed">
									<thead>
										<tr>
											<th class="vert-align">Berat(g)</th>
											<th class="vert-align">Jumlah Ayam<br>(ekor)</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div class="col-md-6">
							<p>
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_uniformity" style="width:120px;font-weight:normal">Uniformity</label>
										<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_uniformity_betina" onkeyup="cekNumerik(this)">
									</div>
									<div class="form-group">
										<span>%</span>
									</div>
								</form>
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_uniformity" style="width:120px;font-weight:normal">Status</label>
										<label for="inp_uni_uniformity" id="lbl_status_uniformity_betina" style="width:120px"></label>
									</div>
								</form>
							</p>
							</div>
						</div>
					</div>					
				</div>
			</div>
		</div>
    </div>
  </div>
</div>

<style type="text/css">
	hr {
		-moz-border-bottom-colors: none;
		-moz-border-image: none;
		-moz-border-left-colors: none;
		-moz-border-right-colors: none;
		-moz-border-top-colors: none;
		border-color: #EEEEEE -moz-use-text-color #FFFFFF;
		border-style: solid none;
		border-width: 1px 0;
		margin: 18px 0;
	}
	
	.table thead>tr>th.vert-align{
		vertical-align: middle;
		text-align : center;
	}
	.table tbody>tr>td.vert-align{
		vertical-align: middle;
		text-align : center;
	}
	.table tbody>tr>td.vert-align-sm{
		vertical-align: middle;
		text-align : center;
		font-size:12px;
		padding:2px;
	}
	.table tbody>tr>td.right-align-sm{
		vertical-align: middle;
		text-align : right;
		font-size:12px;
		padding:2px;
	}
	.table tbody tr.highlight td {
		background-color: #CBE8F7;
	}
	
	.table tbody>tr.rasio {
		background-color: #CBE8F7;
	}
	
	.table tbody>tr>td.borderless{
		border: none;
	}

	.link:hover{
		cursor:pointer;
	}
	
	.col-centered {
		display:inline-block;
		float:none;
		/* reset the text-align */
		text-align:left;
		/* inline-block space fix */
		margin-right:-4px;
	}
	
	.inp-numeric{
		text-align:right;
	}
	
	    .btn-file {
        position: relative;
        overflow: hidden;
    }
    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
	#modal_sisa .modal-dialog .modal-content .modal-body  {max-height:100%;}

</style>
<script type="text/javascript">

</script>
<link type="text/css" href="assets/libs/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="assets/libs/bootstrap/js/moment.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/riwayat_harian_kandang/riwayat_harian_kandang.js"></script>