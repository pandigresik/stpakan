<div class="panel panel-default">
  <div class="panel-heading">Realisasi Panen</div>
  <div class="panel-body">
	<div class="col-md-12">
		<center><h1><?php echo $nama_farm;?></h1></center>
		<div class="row">
			<button type="button" name="tombolSimpan" id="btnBaru" class="btn btn-primary" style="display:none;">Baru</button>
			<br/><br/>
		</div>
		<div class="row">
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_kandang">Kandang</label>
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_farm" value="<?php echo $kode_farm;?>">
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_nama_farm" value="<?php echo $nama_farm;?>">
						<input type="hidden" class="form-control input-sm field_input" name="today" id="inp_today" value="<?php echo $today;?>">
						<input type="hidden" class="form-control input-sm field_input" name="doc_in_campur" id="inp_doc_in_campur" value="">
						<input type="hidden" class="form-control input-sm field_input" name="level_user" id="inp_level_user" value="<?php echo $this->session->userdata("level_user_db");?>">
						<input type="text" class="form-control input-sm field_input" name="kandang" id="inp_kandang" style="text-align:left">
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_flock">Flock</label>
						<input type="text" class="form-control input-sm field_input" name="flock" id="inp_flock" disabled>
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_doc_in">Tanggal DOC-In</label>
						<input type="text" class="form-control input-sm field_input" id="inp_doc_in" disabled>
					</div>
				</form>
			</div>
		</div>
		<hr>
		<br/>
		
		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Final Realisasi Panen</div>
				<div class="panel-body">
					<div style="width:1150px; overflow-x: auto;white-space: nowrap;padding-bottom:20px;">
						<table id="panen_final" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th rowspan="2" class="vert-align" style="width:200px">Tanggal Panen</th>
									<th rowspan="2" class="vert-align" style="width:200px">Umur</th>
									<th rowspan="2" class="vert-align" style="width:200px">No. DO</th>
									<th rowspan="2" class="vert-align" style="width:200px">Tonase DO<br/>(kg)</th>
									<th rowspan="2" class="vert-align" style="width:200px">Jumlah DO<br/>(ekor)</th>
									<th rowspan="2" class="vert-align" style="width:200px">Nama<br/>Pelanggan</th>
									<th rowspan="2" class="vert-align" style="width:200px">No. SJ</th>
									<th rowspan="2" class="vert-align" style="width:200px">Tonase<br/>Realisasi (kg)</th>
									<th rowspan="2" class="vert-align" style="width:200px">Jumlah<br/>Realisasi (ekor)</th>
									<th rowspan="2" class="vert-align" style="width:200px">BB Rata-rata<br/>(kg)</th>
									<th colspan="3" class="vert-align" style="">Tanggal / Jam</th>
									<th rowspan="2" class="vert-align" style="width:200px">Tanggal Entry</th>
								</tr>
								<tr>
									<th class="vert-align" style="width:300px">Datang</th>
									<th class="vert-align" style="width:300px">Mulai</th>
									<th class="vert-align" style="width:300px">Selesai</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		<?php
		if($this->session->userdata("level_user_db") == "KBA"){
		?>
		<div id="detil_realisasi_panen">
		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Daftar Tara Keranjang Kosong</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-4">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_flock">No. SJ</label>
									<input type="text" class="form-control input-sm field_input" name="no_sj" id="inp_no_sj">
									<input type="hidden" class="form-control input-sm field_input" name="inp_no_do" id="inp_no_do">
								</div>
							</form>
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-md-3">
							<table id="daftar_tara_keranjang" class="table table-bordered table-condensed table-striped">
								<thead>
									<tr>
										<th class="vert-align" style="width:50px">No</th>
										<th class="vert-align" style="width:200px">Berat</th>
										<th class="vert-align" style="width:200px">Box</th>
										<th class="vert-align" style="width:300px"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="vert-align">1</td>
										<td class="vert-align berat_tara">
											<span class="berat_tara_lbl hide"></span>
											<input type="text" name="berat_tara_keranjang[]" style="text-align:center;" class="form-control input-sm" value="" onkeyup="cekDecimal(this)"/>
										</td>
										<td class="vert-align box_tara">
											<span class="box_tara_lbl hide"></span>
											<input type="text" name="box_tara_keranjang[]" style="text-align:center" class="form-control input-sm" value="" onkeyup="cekNumerik(this)"/>
										</td>
										<td class="vert-align">
										 <div class="control">
											<button type="button" class="btn btn-primary btn-xs" onclick="simpanTaraKeranjang(this)">
												<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>
											</button>
											<button type="button" class="btn btn-danger btn-xs" onclick="batalTaraKeranjang(this)">
												<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
											</button>
										 </div>
										</td>
									</tr>
								</tbody>
								<thead>
									<tr>
										<th class="vert-align" style="width:50px">Total</th>
										<th class="vert-align" style="width:200px" id="total_tara_berat"></th>
										<th class="vert-align" style="width:200px" id="total_tara_box"></th>
										<th class="vert-align" style="width:200px"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Daftar Timbangan Ayam</div>
				<div class="panel-body">
					<div class="row" id="generate_this">
						<div class="col-md-3">
							<table id="daftar_timbang_ayam1" class="table table-bordered table-condensed table-striped">
								<thead>
									<tr>
										<th class="vert-align" style="width:50px;">No</th>
										<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>
										<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>
										<th class="vert-align" style="width:300px"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="vert-align" data-status="draft">1</td>
										<td class="vert-align jumlah_ayam">
											<span data-kolom="1" class="jumlah_ayam_lbl hide"></span>
											<input type="text" name="jumlah_ayam1[]" style="text-align:center;" class="form-control input-sm" value="" onkeyup="cekNumerik(this)"/>
										</td>
										<td class="vert-align tonase_ayam">
											<span data-kolom="1" class="tonase_ayam_lbl hide"></span>
											<input type="text" name="tonase_ayam1[]" style="text-align:center" class="form-control input-sm" value="" onkeyup="cekDecimal(this)"/>
										</td>
										<td class="vert-align">
											<div class="control">
												<button type="button" class="btn btn-primary btn-xs" onclick="simpanJumlahAyam(this, 1)">
													<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>
												</button>
												<button type="button" class="btn btn-danger btn-xs" onclick="batalJumlahAyam(this, 1)">
													<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
												</button>
											</div>
										</td>
									</tr>
								</tbody>
								<thead>
									<tr>
										<th class="vert-align" style="width:50px">Total</th>
										<th class="vert-align" style="width:200px" id="total_jumlah_ayam1"></th>
										<th class="vert-align" style="width:200px" id="total_tonase_ayam1"></th>
										<th class="vert-align" style="width:200px"></th>
									</tr>
								</thead>
							</table>
						</div>
						
						<br/>
						<br/>
						<!--
						<div class="col-md-3">
							<table id="daftar_timbang_ayam2" class="table table-bordered table-condensed table-striped hide">
								<thead>
									<tr>
										<th class="vert-align" style="width:50px;">No</th>
										<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>
										<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>
										<th class="vert-align" style="width:300px"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="vert-align">1</td>
										<td class="vert-align jumlah_ayam">
											<span data-kolom="2" class="jumlah_ayam_lbl hide"></span>
											<input type="text" name="jumlah_ayam2[]" style="text-align:center;" class="form-control input-sm" value=""/>
										</td>
										<td class="vert-align tonase_ayam">
											<span data-kolom="2" class="tonase_ayam_lbl hide"></span>
											<input type="text" name="tonase_ayam2[]" style="text-align:center" class="form-control input-sm" value=""/>
										</td>
										<td class="vert-align">
											<div class="control">
												<button type="button" class="btn btn-primary btn-xs" onclick="simpanJumlahAyam(this, 2)">
													<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>
												</button>
												<button type="button" class="btn btn-danger btn-xs" onclick="batalJumlahAyam(this, 2)">
													<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
												</button>
											</div>
										</td>
									</tr>
								</tbody>
								<thead>
									<tr>
										<th class="vert-align" style="width:50px">Total</th>
										<th class="vert-align" style="width:200px" id="total_jumlah_ayam2"></th>
										<th class="vert-align" style="width:200px" id="total_tonase_ayam2"></th>
										<th class="vert-align" style="width:200px"></th>
									</tr>
								</thead>
							</table>
						</div>-->
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_tot_tarra">Tara</label>
						<input type="text" class="form-control input-sm field_input" id="inp_tot_tarra" disabled style="text-align:right">
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_tot_ayam">Jumlah Ekor</label>
						<input type="text" class="form-control input-sm field_input" id="inp_tot_ayam" disabled style="text-align:right">
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_tot_bruto">Bruto</label>
						<input type="text" class="form-control input-sm field_input" id="inp_tot_bruto" disabled style="text-align:right">
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_tot_netto">Netto</label>
						<input type="text" class="form-control input-sm field_input" id="inp_tot_netto" disabled style="text-align:right">
					</div>
				</form>
			</div>
		</div>
		</div>
		
		<?php
		}
		?>
		<br/><br/>
		<div class="row" id="simpan_action">
			<div class="col-md-6 col-md-offset-5">
				<button type="button" name="tombolSimpan" id="btnSimpan" class="btn btn-primary" style="width:200px">Simpan</button>
			</div>
		</div>
	</div>	
  </div>
</div>

<div class="modal fade" id="modal_notif_penimbangan" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-body">
			<div class="col-md-12">
				<div>[Nilai netto/jumlah ekor] tidak sama dengan [tonase realisasi/jumlah realisasi]
				</div>
				<table id="tb_perbandingan" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th class="vert-align col-md-1">Netto</th>
							<th class="vert-align col-md-1">Tonase<br/>Realisasi</th>
							<th class="vert-align col-md-1">Jumlah<br/>Ekor</th>
							<th class="vert-align col-md-1">Jumlah<br/>Realisasi</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<div>
				Apakah Anda yakin untuk menyetujui daftar penimbangan ayam?
				<ol>
					<li>Jika ya, maka sistem akan mengubah nilai tonase realisasi dan jumlah realisasi yang dientri Admin Farm</li>
					<li>Jika tidak, maka hasil perhitungan ulang akan disimpan tetapi tidak ditampilkan</li>
				</ol>
				Nilai tonase dan jumlah realisasi yang tersimpan akan ditampilkan pada LHK.
				</div>
			</div>
		</div>
		
		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolCancel" id="btnCancelPenyimpanan" class="btn btn-default" onClick="batal_simpan_detail(this)">Batal</button>
				<button type="button" name="tombolBatal" id="btnBatalPenyimpanan" class="btn btn-default" onClick="simpan_detil(this)">Tidak</button>
				<button type="button" name="tombolSimpan" id="btnSimpanPenyimpanan" class="btn btn-primary" onClick="simpan_detil(this)">Ya</button>
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
	.table tbody tr.highlight-red td {
		background-color: #FFEDF0;
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

<link type="text/css" href="assets/libs/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="assets/libs/bootstrap/js/moment.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/riwayat_harian_kandang/realisasi_panen.js"></script>

<script type="text/javascript">

</script>