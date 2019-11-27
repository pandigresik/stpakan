<div class="panel panel-default">
  <div class="panel-heading">Rekap Retur Pakan</div>
  <div class="panel-body">
	<div class="col-md-12">
		<center><h1><?php echo $nama_farm; ?></h1></center>
		<?php
        if ($this->session->userdata('level_user') != 'KF' and $this->session->userdata('level_user') != 'AG') {
            ?>
		<div class="row">
			<button type="button" name="tombolBaru" id="btnBaru" class="btn btn-primary">Baru</button>
			<br/><br/>
		</div>
		<?php
        } else {
            echo '<br/><br/>';
        }
        ?>
		<div class="row">
			<div class="col-md-12">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_kandang">Tanggal Retur</label>
						<input type="hidden" class="form-control input-sm field_input" name="nama_user" id="nama_user" value="<?php echo $this->session->userdata('nama_user'); ?>">
						<input type="hidden" class="form-control input-sm field_input" name="level_user" id="level_user" value="<?php echo $this->session->userdata('level_user'); ?>">
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_farm" value="<?php echo $kode_farm; ?>">
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_nama_farm" value="<?php echo $nama_farm; ?>">
						<input type="hidden" class="form-control input-sm field_input" name="today" id="inp_today" value="<?php echo $today; ?>">
						<input type="hidden" class="form-control input-sm field_input" name="today_full" id="inp_today_full" value="<?php echo $today_full; ?>">
						<input type="text" class="form-control" id="inp_tglawal" placeholder="">
						<label for="inp_kandang">s.d</label>
						<input type="text" class="form-control" id="inp_tglakhir" placeholder="">
						<input type="button" class="btn btn-primary" name="btnTampilkan" id="btnTampilkan" value="Tampilkan"/>
					</div>
				</form>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="panel">
			<div style="width:100%; overflow-x: auto;white-space: nowrap;padding-bottom:20px;">
				<table id="tb_rekap" class="table table-condensed table-striped table-bordered">
				</table>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal_buat_retur_pakan" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-header" style="padding:5px">Retur Sisa Pakan</div>
		<div class="modal-body" style="height:500px; overflow-y: auto;white-space: nowrap;padding-bottom:20px;">
			<div class="row">
				<div class="col-md-12">
					<center><h3>FARM <span id="pengajuan_titlefarm"><?php echo $nama_farm; ?></span></h3></center>
				</div>
			</div>
			<br/>			
			<div class="row">
				<div class="col-md-4">
					<form class="form-inline">
						<div class="form-group">
							<label for="inp_kandang">Kandang</label>
							<input type="text" class="form-control input-sm field_input" name="kandang" id="inp_kandang" style="text-align:left">
						</div>
					</form>
				</div>
				
				<div class="col-md-2">
					<form class="form-inline">
						<div class="form-group">
							<label for="inp_flock">Flock</label>
							<input type="text" style="width:50px" class="form-control input-sm field_input" name="flock" id="inp_flock" disabled>
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
				
				<div class="col-md-2">
					<form class="form-inline">
						<div class="form-group pull-right">
							<label for="inp_doc_in">Umur</label>
							<input type="text" class="form-control input-sm field_input"  style="width:50px" name="umur" id="inp_umur" disabled>
						</div>
					</form>
				</div>	
			</div>
			
			<br/>
			
			<div class="row">
				<div class="col-md-2" style="padding-top:7px;">
					<form class="form-inline">
						<div class="form-group pull-right">
							<label for="inp_doc_in">Tanggal Retur</label>
						</div>
					</form>
				</div>	
				<div class="col-md-4">
					<form class="form-inline">
						<div class="form-group">
							<div class="input-group date tgl_retur">
								<input type="text" name="tgl_retur" id="inp_tgl_retur" style="width:120px;" class="form-control disabled" readonly />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</form>
				</div>
			</div>
			
			<br/><br/>
			
			<div class="row">
				<div class="panel panel-primary">
					<div class="panel-heading">Sisa Stok Pakan</div>
					<div class="panel-body">
						<div class="col-md-12">
							<table id="pengajuan_tb_sisa" class="table table-condensed table-striped table-bordered">
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
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="panel panel-primary">
					<div class="panel-heading">Alokasi Retur Sisa Pakan</div>
					<div class="panel-body">
						<div class="col-md-12">
							<table id="alokasi_tb_sisa" class="table table-condensed table-striped table-bordered">
								<thead>
									<tr>
										<th class="col-md-3 vert-align">Alokasi Retur</th>
										<th class="col-md-3 vert-align">Tujuan</th>
										<th class="col-md-3 vert-align">Nama Pakan</th>
										<th class="col-md-1 vert-align">Jumlah<br>(zak)</th>
										<th class="col-md-2 vert-align"></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<select class="form-control" name="alokasi_retur" id="inp_alokasi_retur" onchange="pilih_alokasi_retur(this)">
												<option data-kode_farm="<?php echo $kode_farm; ?>" value="kandang">Kandang</option>
												<option data-kode_farm="<?php echo $kode_farm; ?>" value="gudang">Gudang</option>
											</select>
										</td>
										<td>
											<select class="form-control" name="tujuan_retur" id="inp_tujuan_retur">
											</select>
										</td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row">
				<center><button type="button" name="tombolSimpan" id="btnSimpan" class="btn btn-primary" onclick="simpanReturPakanDB()" style="width:200px">Simpan</button></center>
			</div>
			<br/>
		</div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="pengajuan_modal_sisa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<center><h1>Retur Pakan Ke Gudang</h1></center>
					<center><h3>Farm <span id="pengajuan_titlefarm"></span></h3></center>
				</div>
			</div>
			<br/>
			<div class="row">
				<input type="hidden" name="pengajuan_inp_print_no_retur" id="pengajuan_inp_print_no_retur">
				<input type="hidden" name="pengajuan_inp_print_no_reg" id="pengajuan_inp_print_no_reg">
			</div>
			<div class="row">
				<div class="col-md-2">Kandang</div>
				<div class="col-md-5">: <span id="pengajuan_print_nama_kandang"></span><input type="hidden" name="pengajuan_inp_print_kandang" id="pengajuan_inp_print_kandang"></div>
				<div class="col-md-3">Tanggal Tutup Siklus</div>
				<div class="col-md-2">: <span id="pengajuan_print_tgl_lhk"></span><input type="hidden" name="pengajuan_inp_print_tgl" id="pengajuan_inp_print_tgl"></div>
			</div>
			
			<br/><br/>
			
			<div class="row">
				<div class="col-md-12">
					<table id="pengajuan_tb_sisa_approval" class="table table-condensed table-striped table-bordered">
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
						</tbody>
					</table>
				</div>
			</div>
			
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
					<td class="col-md-4 vert-align borderless" id="id_user_approve2">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
					<td class="col-md-4 vert-align borderless" id="id_user_terima2">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
					<td class="col-md-4 vert-align borderless" id="id_user_buat2"><button name="pengajuan_tombolPrint" id="pengajuan_btnPrint" class="btn btn-primary">Simpan</button></td>
				</tr>
			</table>
		</div>
    </div>
  </div>
</div>

<div class="modal fade largeWidth" id="modal_sisa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<!--<form name="frPrintSJ" method="post" id="frPrintSJ" action="rekap_retur_pakan/print_sj" target="_blank">-->
					<input type="hidden" name="inp_print_tgl_retur" id="inp_print_tgl_retur">
					<input type="hidden" name="inp_print_nama_farm" id="inp_print_nama_farm">
					<input type="hidden" name="inp_print_nama_kandang" id="inp_print_nama_kandang">
					<input type="hidden" name="inp_print_no_retur" id="inp_print_no_retur">
					<input type="hidden" name="inp_print_no_reg" id="inp_print_no_reg">
					<input type="hidden" name="inp_print_nama_retur" id="inp_print_nama_retur">
					<input type="hidden" name="inp_print_nama_approve" id="inp_print_nama_approve">
					<input type="hidden" name="inp_print_nama_terima" id="inp_print_nama_terima">
					<!--<button type="submit" name="tombolPrint" id="btnPrint" class="btn btn-primary disabled">Print</button>-->
					</form>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<center><h1><span id="titleretur">Retur Pakan Ke Gudang</span></h1></center>
					<center><h3>Farm <span id="titlefarm"></span></h3></center>
				</div>
			</div>
			<br/>
			<div class="row" style="position: relative;" id="label_sj">
				<div class="col-md-2">Kandang Asal</div>
				<div class="col-md-4">: <span id="print_kandang_asal"></span></div>
				<div class="col-md-2">No. Retur Pakan</div>
				<div class="col-md-4">: <span id="print_no_retur"></span></div>
			</div>
			<div class="row">
				<div class="col-md-2">Alokasi Retur</div>
				<div class="col-md-4">: <span id="print_alokasi_retur"></span><input type="hidden" name="inp_print_alokasi_retur" id="inp_print_alokasi_retur"></div>
				<div class="col-md-2">Tanggal Retur</div>
				<div class="col-md-4">: <span id="print_tgl_retur"></span><input type="hidden" name="inp_print_tgl_retur" id="inp_print_tgl_retur"></div>
			</div>
			
			<br/><br/>
			<div>Sisa pakan kandang : <span id="sisa_pakan_kandang"></span></div>
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
						</tbody>
					</table>
				</div>
			</div>
			<div id="divAlasanReject"></div>			
			<table class="table">
				<tr>
					<td class="col-md-4 vert-align borderless">Kepala Farm</td>
					<td class="col-md-4 vert-align borderless">Admin Gudang</td>
					<td class="col-md-4 vert-align borderless">Pengawas Kandang</td>
				</tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr><td class="col-md-4 vert-align borderless"><span id="statusReject"></span></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr>
					<td class="col-md-4 vert-align borderless" id="id_user_approve"><div id="app_point"><button name="tombolPrint" id="btnApprove" class="btn btn-primary">Approve</button></div></td>
					<td class="col-md-4 vert-align borderless" id="id_user_terima">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
					<td class="col-md-4 vert-align borderless" id="id_user_buat">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
				</tr>
			</table>
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
	
	.hidden{
		display:none;
	}
	
	#modal_sisa .modal-dialog .modal-content .modal-body  {max-height:100%;}
	#pengajuan_modal_sisa .modal-dialog .modal-content .modal-body  {max-height:100%;}
</style>
<script type="text/javascript">

</script>

<link type="text/css" href="assets/libs/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />

<script type="text/javascript" src="assets/js/rekap_retur_pakan/retur_sisa_pakan.js"></script>
<script type="text/javascript" src="assets/js/rekap_retur_pakan/jquery-barcode.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/moment.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap-datetimepicker.min.js"></script>