<div class="panel panel-default">
  <div class="panel-heading">Rekap Retur Pakan</div>
  <div class="panel-body">
	<div class="col-md-12">
		<center><h1><?php echo $nama_farm;?></h1></center>
		<div class="row">
			<br/><br/>
		</div>
		<div class="row">
			<div class="col-md-12">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_kandang">Tanggal Tutup Siklus</label>
						<input type="hidden" class="form-control input-sm field_input" name="nama_user" id="nama_user" value="<?php echo $this->session->userdata("nama_user");?>">
						<input type="hidden" class="form-control input-sm field_input" name="level_user" id="level_user" value="<?php echo $this->session->userdata("level_user");?>">
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_farm" value="<?php echo $kode_farm;?>">
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_nama_farm" value="<?php echo $nama_farm;?>">
						<input type="hidden" class="form-control input-sm field_input" name="today" id="inp_today" value="<?php echo $today;?>">
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
			<div class="col-md-12">
				<table id="tb_rekap" class="table table-condensed table-striped table-bordered">
					<thead style="background-color:#F0F0F0">
						<tr>
							<th class="hidden"></th>
							<th class="hidden"></th>
							<th class="hidden"></th>
							<th rowspan="2" class="vert-align" style="width:150px">Kandang</th>
							<th rowspan="2" class="vert-align" style="width:150px">Tanggal Tutup<br>Siklus</th>
							<th rowspan="2" class="vert-align col-md-2">Nama Pakan</th>
							<th rowspan="2" class="vert-align col-md-2">Nomor Retur Pakan</th>
							<th colspan="3" class="vert-align col-md-3">Pengajuan Retur</th>
							<th rowspan="2" class="vert-align col-md-1">Approval</th>
							<th colspan="3" class="vert-align col-md-3">Serah Terima</th>
						</tr>
						<tr>
							<th class="hidden"></th>
							<th class="hidden"></th>
							<th class="hidden"></th>
							<th class="vert-align" style="width:50px">Waktu</th>
							<th class="vert-align" style="width:25px">Sak</th>
							<th class="vert-align" style="width:25px">Kg</th>
							<th class="vert-align" style="width:50px">Waktu</th>
							<th class="vert-align" style="width:25px">Sak</th>
							<th class="vert-align" style="width:25px">Kg</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($retur_pakan as $rp){
							$tgl_retur = (isset($rp["tgl_retur"]) and !empty($rp["tgl_retur"])) ? $rp["tgl_retur"] : "";
							$tgl_approve = (isset($rp["nama_approve"]) and !empty($rp["nama_approve"])) ? $rp["tgl_approve"] : "";
							$tgl_terima = (isset($rp["nama_terima"]) and !empty($rp["nama_terima"])) ? $rp["tgl_terima"] : "";
							$no_retur = (isset($rp["tgl_retur"]) and !empty($rp["tgl_retur"])) ? $rp["no_retur"] : "";
						?>
						<tr>
							<td class="hidden"><?php echo $rp["no_retur"];?></td>
							<td class="hidden"><?php echo $rp["no_reg"];?></td>
							<td class="hidden"><?php echo $rp["kode_kandang"];?></td>
							<td class="link"><?php echo $rp["nama_kandang"];?></td>
							<td class="link"><?php echo $rp["tgl_tutupsiklus"];?></td>
							<td class="link"><?php echo $rp["nama_barang"];?></td>
							<td class="link"><?php echo $no_retur;?></td>
							<td class="link"><?php echo $tgl_retur;?></td>
							<td class="link" align="right"><?php echo $rp["jml_retur"];?></td>
							<td class="link" align="right"><?php echo $rp["brt_retur"];?></td>
							<td class="link"><?php echo $tgl_approve;?></td>
							<td class="link"><?php echo $tgl_terima;?></td>
							<td class="link" align="right"><?php echo $rp["jml_putaway"];?></td>
							<td class="link" align="right"><?php echo $rp["brt_putaway"];?></td>
						</tr>
						<?php
						}
						?>
					</tbody>
				</table>
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

<div class="modal fade bs-example-modal-lg" id="modal_sisa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-body">
			<div class="row">
				<div class="col-md-12">
					<form name="frPrintSJ" method="post" id="frPrintSJ" action="rekap_retur_pakan/print_sj" target="_blank">
					<input type="hidden" name="inp_print_tgl_lhk" id="inp_print_tgl_lhk">
					<input type="hidden" name="inp_print_nama_farm" id="inp_print_nama_farm">
					<input type="hidden" name="inp_print_nama_kandang" id="inp_print_nama_kandang">
					<input type="hidden" name="inp_print_no_retur" id="inp_print_no_retur">
					<input type="hidden" name="inp_print_no_reg" id="inp_print_no_reg">
					<input type="hidden" name="inp_print_nama_retur" id="inp_print_nama_retur">
					<input type="hidden" name="inp_print_nama_approve" id="inp_print_nama_approve">
					<input type="hidden" name="inp_print_nama_terima" id="inp_print_nama_terima">
					<button type="submit" name="tombolPrint" id="btnPrint" class="btn btn-primary disabled">Print</button>
					</form>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<center><h1>Retur Pakan Ke Gudang</h1></center>
					<center><h3>Farm <span id="titlefarm"></span></h3></center>
				</div>
			</div>
			<br/>
			<div class="row" style="position: relative;" id="label_sj">
				<div class="col-md-2" style="height:50px;line-height:50px ;">No. SJ Retur</div>
				<div class="col-md-4" style="height:50px;line-height:50px ;">: <span id="print_sj_retur"></span></div>
				<div class="col-md-5" id="print_barcode_sj"></div>
			</div>
			<div class="row">
				<div class="col-md-2">Kandang</div>
				<div class="col-md-4">: <span id="print_nama_kandang"></span><input type="hidden" name="inp_print_kandang" id="inp_print_kandang"></div>
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
						</tbody>
					</table>
				</div>
			</div>
						
			<table class="table">
				<tr>
					<td class="col-md-4 vert-align borderless">Kepala Unit/Farm</td>
					<td class="col-md-4 vert-align borderless">Admin Gudang</td>
					<td class="col-md-4 vert-align borderless">Pengawas Kandang</td>
				</tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
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
<script type="text/javascript" src="assets/js/rekap_retur_pakan/rekap_retur_pakan.js"></script>
<script type="text/javascript" src="assets/js/rekap_retur_pakan/jquery-barcode.js"></script>