<div class="panel panel-default">
  <div class="panel-heading">Pemantauan LHK</div>
  <div class="panel-body">
	<div class="col-md-12">
		<center><h1><span id="lbl_pemantauan_lhk">&nbsp;</span></h1></center>
		<br/><br/>
		<div class="row">
			<div style="width:200px;float:left">
				<form class="form-inline">
					<div class="form-group" style="color:#FF0000;">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_lhk_tidak_sesuai_timeline">LHK tidak sesuai timeline
							</label>
						</div>							
					</div>
				</form>
			</div>
			<div style="width:150px;float:left">
				<form class="form-inline">
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_lhk_sesuai_timeline"> LHK sesuai timeline
							</label>
						</div>
					</div>
				</form>
			</div>
			<div style="width:150px;float:left">
				<form class="form-inline">
					<div class="form-group" style="color:#E6A205">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_lhk_belum_dientry"> LHK belum dientry
							</label>
						</div>
					</div>
				</form>
			</div>
			<!--
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group" style="color:#0C4EE8;">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_lhk_pakan_berlebih"> Konsumsi Pakan Berlebih
							</label>
						</div>
					</div>
				</form>
			</div>
			-->
		</div>
		
		<div class="row">
			<div style="width:200px;float:left">
				<form class="form-inline">
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_belum_konfirmasi"> Belum konfirmasi
							</label>
						</div>							
					</div>
				</form>
			</div>
			<div style="width:150px;float:left">
				<form class="form-inline">
					<div class="form-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="q_sudah_konfirmasi"> Sudah konfirmasi
							</label>
						</div>
					</div>
				</form>
			</div>
		</div>
		
		<div class="row">
			<div style="width:300px;float:left">
				<form class="form-inline">
					<span>Tanggal LHK : </span>
					<div class="form-group">	
						<input type="hidden" name="leveluser" style="width:150px;" class="form-control" id="q_leveluser" value="<?php echo $level;?>"/>
						<input type="hidden" name="namafarmtrue" style="width:150px;" class="form-control" id="q_farm_true" value="<?php echo $farms[0]["nama_farm"];?>"/>
						<input type="hidden" name="namafarm" style="width:150px;" class="form-control" id="q_farm" value="<?php echo $farms[0]["kode_farm"];?>"/>
							
						<div class="input-group date" id="div_q_start_tgl_lhk">
							<input type="text" name="startDate" style="width:150px;" class="form-control disabled" id="q_start_tgl_lhk" readonly />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</form>
			</div>
			<div style="width:250px;float:left">
				<form class="form-inline">
					<span>s/d&nbsp;&nbsp;&nbsp;&nbsp;</span>
					<div class="form-group">						
						<div class="input-group date" id="div_q_end_tgl_lhk">
							<input type="text" name="endDate" style="width:150px;" class="form-control disabled" id="q_end_tgl_lhk" readonly />
							<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</form>
			</div>
			<div>
				<button type="button" name="tombolCari" id="btnCari" class="btn btn-primary">Cari</button>
			</div>
		</div>
		
		<br/>
		
		<div class="row">
			<div id="column-left" class="col-md-2" style="margin:0px;padding:1px;display:none">
				<div class="panel panel-default">
					<div class="panel-heading">Daftar Farm</div>
					<div class="panel-body" id="daftar_farm">
						<?php
						foreach($farms as $farm){
							$badge = ($farm["jml"] > 0) ? "<span class='badge'>".$farm["jml"]."</span>" : "";
							echo "<div data-farm='".$farm["kode_farm"]."' class='menu_farm' onclick='change_farm(this)'>".strtoupper($farm["nama_farm"]) . $badge . "</div>";
						}
						
						?>
					</div>
				</div>
			</div>
			<div id="content" class="col-md-10"  style="margin:0px;padding:1px;">
				<div class="panel panel-default">
					<div class="panel-heading" id="lbl_nama_farm" style="display:none"> 
						<span class="glyphicon glyphicon-align-justify" id="btn" aria-hidden="true" style="display:none"></span>
						<?php echo "FARM <span id='span_lbl_farm'>".strtoupper($farms[0]["nama_farm"])."</span>";?>
					</div>
					<div class="panel-body">
						<table id="tb_lhk" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<td><input type="text" class="form-control search" name="q_kandang" id="q_kandang" placeholder="Kandang"></td>
									<td><input type="text" class="form-control search" name="q_noreg" id="q_noreg" placeholder="No. Reg"></td>
									<td colspan="6"></td>
								</tr>
								<tr>
									<th class="vert-align" rowspan="2" style="width:150px;">Kandang</th>
									<th class="vert-align" rowspan="2" style="width:150px;">No. Reg</th>
									<th class="vert-align" rowspan="2" style="width:150px;">Tanggal LHK</th>
									<th class="vert-align" rowspan="2" style="width:200px;">Tanggal entry LHK</th>
									<th class="vert-align" rowspan="2" style="width:200px;">Status LHK</th>
									<th class="vert-align" colspan="3">Acknowledge</th>
									<th class="vert-align" rowspan="2">Keterangan</th>
								</tr>
								<tr>
									<th class="vert-align" style="width:120px;">Kepala Farm</th>
									<th class="vert-align" style="width:120px;">Kadep Pemeliharaan</th>
									<th class="vert-align" style="width:120px;">Kadiv Budidaya</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>	
  </div>
</div>


<div class="modal-lhk modal bs-example-modal-lg" id="modal_lhk" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-body" style="margin:0px;padding:10px;">
			<div class="panel panel-default">
			  <div class="panel-heading">Laporan Harian Kandang</div>
			  <div class="panel-body">
				<div class="col-md-12">
					<center><h1><?php echo $nama_farm;?></h1></center>
					<div class="row">
						<button type="button" name="tombolSimpan" id="btnLanjutRilis" onclick="simpan_acknowledge_kf(this)" class="btn btn-primary" style="display:block;">Simpan dan Rilis</button>
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
									<input type="hidden" class="form-control input-sm field_input" name="doc_in_campur" id="inp_doc_in_campur" value="">
									<input type="hidden" class="form-control input-sm field_input" name="last_bb_rata" id="inp_last_bb_rata" value="">
									<input type="hidden" class="form-control input-sm field_input" name="status_lhk" id="inp_status_lhk" value="">
									<input type="hidden" class="form-control input-sm field_input" name="noreg_lhk" id="inp_noreg_lhk" value="">
									<input type="hidden" class="form-control input-sm field_input" name="tgl_transaksi" id="inp_tgl_trnsaksi" value="">
									<input type="hidden" class="form-control input-sm field_input" name="tgl_input" id="inp_tgl_input" value="">
									<input type="hidden" class="form-control input-sm field_input" name="tgl_lhk" id="inp_tgl_transaksi" value="">
									<input type="hidden" class="form-control input-sm field_input" name="user_buat" id="inp_user_buat" value="">
									<input type="hidden" class="form-control input-sm field_input" name="tgl_buat" id="inp_tgl_buat" value="">
									<input type="text" class="form-control input-sm field_input disabled" style="width:150px" name="kandang" id="inp_kandang" disabled style="text-align:left">
								</div>
							</form>
						</div>
						<div class="col-md-2">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_flock">Flock</label>
									<input type="text" class="form-control input-sm field_input" style="width:50px" name="flock" id="inp_flock" disabled>
								</div>
							</form>
						</div>
						<div class="col-md-3">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_doc_in">DOC-In</label>
									<input type="text" class="form-control input-sm field_input" style="width:100px" id="inp_doc_in" disabled>
								</div>
							</form>
						</div>
						<div class="col-md-3">
							<form class="form-inline">
								<div class="form-group pull-right">
									<label for="inp_doc_in">Umur</label>
									<input type="text" class="form-control input-sm field_input"  style="width:100px" name="umur" id="inp_umur" disabled> hari
								</div>
							</form>
						</div>			
					</div>
					<hr>
					<div class="row">
						<table class="tb_lhk" style="width:100%;padding:10px;border:0px">
							<tr>
								<td class="pull-right"><label>Tgl.Lhk</label></td>
								<td>
								<form class="form-inline">
									<div class="form-group">
										<div class="form-group">
											<div class="input-group" id="div_tgl_lhk">
												<input type="text" name="startDate" style="width:120px;" class="form-control disabled" id="inp_tgl_lhk" disabled />
												<span class="input-group-addon">
													<span class="glyphicon glyphicon-calendar"></span>
												</span>
											</div>
										</div>
										<!-- Diganti pilih tanggal-->
										<!--<input type="text" class="form-control input-sm field_input" style="width:100px" name="flock" id="inp_tgl_lhk" disabled>-->
									</div>
								</form>
								</td>
								<td class="pull-right"><label>IP</label></td>
								<td>
								<form class="form-inline">
									<div class="form-group">
										<input type="text" class="form-control input-sm field_input"  style="width:75px;" name="ip" id="inp_ip" disabled> &nbsp;&nbsp;&nbsp;
									</div>
								</form>
								</td>
								<td class="pull-right"><label>FCR</label></td>
								<td>
								<form class="form-inline">
									<div class="form-group">
										<input type="text" class="form-control input-sm field_input"  style="width:75px" name="fcr" id="inp_fcr" disabled>
									</div>
								</form>
								</td>
							</tr>
							<tr>
								<td class="pull-right"><label>Populasi awal setelah umur 7</label></td>
								<td>
								<form class="form-inline">
									<div class="form-group">
										<input type="text" class="form-control input-sm field_input"  style="width:100px;text-align:right" name="populasi_awal_stlh_umur_7" id="inp_populasi_awal_stlh_umur_7" disabled> ekor
										<input type="hidden" class="form-control input-sm field_input"  style="width:100px;text-align:right" name="populasi_awal_stlh_umur_7_temp" id="inp_populasi_awal_stlh_umur_7_temp">
									</div>
								</form>
								</td>
								<td class="pull-right"><label>BB rata-rata</label></td>
								<td>
								<form class="form-inline">
									<div class="form-group">
										<input type="text" class="form-control input-sm field_input"  style="width:75px" name="bb_rata" id="inp_bb_rata" disabled> Kg
									</div>
								</form>
								</td>
								<td class="pull-right"><label>ADG</label></td>
								<td>
								<form class="form-inline">
									<div class="form-group">
										<input type="text" class="form-control input-sm field_input"  style="width:75px" name="adg" id="inp_adg" disabled>
									</div>
								</form>
								</td>
							</tr>
						</table>
					</div>
					<br/>
					
					<div class="row">
						<div class="panel panel-primary">
							<div class="panel-heading">Laporan Harian Kandang - Penimbangan per Sekat</div>
							<div class="panel-body">
								<div class="col-md-8">
									<table id="lhk_sekat" class="table table-bordered table-condensed">
										<thead>
											<tr>
												<th class="vert-align col-md-2">Sekat</th>
												<th class="vert-align" style="width:50px">Jumlah</th>
												<th class="vert-align" style="width:50px">BB (g)</th>
												<th class="vert-align col-md-6">Keterangan</th>
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
							<div class="panel-heading">Laporan Harian Kandang - Populasi</div>
							<div class="panel-body">
								<div class="col-md-12">
									<table id="lhk_populasi" class="table table-bordered table-condensed">
										<thead>
											<tr>
												<th class="vert-align col-md-1" rowspan="2">Populasi<br>Awal</th>
												<th class="vert-align col-md-1">Penambahan</th>
												<th class="vert-align col-md-1" colspan="4">Pengurangan</th>
												<!--<th class="vert-align col-md-1" rowspan="2">Panen</th>-->
												<th class="vert-align col-md-1" rowspan="2">Populasi<br/>Akhir</th>
												<th class="vert-align col-md-1" rowspan="2">DH (%)</th>
												<th class="vert-align col-md-2" rowspan="2">Keterangan</th>
											</tr>
											<tr>
												<th class="vert-align col-md-1" >Lain-lain</th>
												<th class="vert-align col-md-1" >Mati</th>
												<th class="vert-align col-md-1" >Afkir</th>
												<th class="vert-align col-md-1" >Lain-lain</th>
												<th class="vert-align col-md-2" >Panen</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAwal" value="0" disabled /></td>
												<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahLain" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
												<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangMati" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
												<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangAfkir" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
												<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangLain" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
												<td><input type="text" class="form-control input-sm inp-numeric" id="inp_panen" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
												<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAkhir" value="0" disabled /></td>
												<td>
													<input type="text" class="form-control input-sm inp-numeric" id="inp_dayahidup" value="0" disabled />
													<input type="hidden" class="form-control input-sm inp-numeric" id="inp_dayahidup_temp" value="0"/>
												</td>
												<td>
													<textarea class="form-control" id="inp_ket_kematian" disabled></textarea>
												</td>
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
								<div class="col-md-12">
									<table id="lhk_pakan" class="table table-bordered table-condensed">
										<thead>
											<tr>
												<th class="vert-align" rowspan="2">Jenis<br>Kelamin</th>
												<th class="vert-align" rowspan="2">Pakan</th>
												<th class="vert-align" colspan="2">Stok Awal</th>
												<th class="vert-align" colspan="2">Pakan Rusak</th>
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
				</div>	
			  </div>
			</div>

		</div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_pengisian_keterangan" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:30%;;">
    <div class="modal-content">
		<div class="modal-body" style="margin:0px;padding:0px;>
			<div class="panel panel-primary">
				<div class="panel-heading">Keterangan Populasi Mati</div>
				<div class="panel-body">
					<div class="col-md-12">
						<textarea class="form-control" style="width:100%" name="inp_pengisian_keterangan" onkeyup="checkPengisianKeterangan(this)" id="inp_pengisian_keterangan"></textarea>
					</div>
					<div class="col-md-12 has-error" id="pengisian_keteranganErrMsg" style="display:none;color:red;"></div>
					<div class="col-md-12">
						<br/>
						<center>
							<button type="button" name="tombolLanjutSimpan" id="btntombolLanjutSimpan" class="btn btn-primary disabled">Simpan</button>
						</center>
					</div>
				</div>
			</div>
		</div>
		
		<div class="modal-footer" style="margin:0px auto;padding:3px;">
			
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
	
	.glyphicon:hover{
		cursor:pointer;
	}
	
	.menu_farm{
		margin:0px;
		display:block;
		padding:3px;
	}
	.menu_farm:hover{
		cursor:pointer;
		background-color:#F5F5F5;
	}
	.tb_lhk td{
		padding:10px;
		vertical-align:middle;
		line-height:20px;
	}
	
	.modal-lhk .modal-dialog{
		overflow-y: initial !important
	}
	.modal-lhk. modal-body{
		height: 600px;
		height: 500px;
		overflow-x: auto;
		overflow-y: auto;
	}
</style>

<link type="text/css" href="assets/libs/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="assets/libs/bootstrap/js/moment.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/riwayat_harian_kandang/pemantauan_lhk_bdy.js"></script>