<div class="panel panel-default">
  <div class="panel-heading">Standar Budidaya</div>
  
  <div class="panel-body">
	<div class="row>">
		<ul class="breadcrumb">
			<li id="set_parameter" class="active">Set Parameter</li>
			<li id="kebutuhan_pakan">Kebutuhan Pakan</li>
			<li id="detail_std_budidaya">Detail Standar Budidaya</li>
		</ul>
	</div>
	<div class="row">
	<!--<button type="button" name="tombolCek" id="btnCek" onclick="calculateStandarTarget()" class="btn btn-primary">Cek Aja</button>-->
	
	<?php $style_label = 'col-sm-2';?>
	<?php $style_value = 'col-sm-3';?>
		<form class="form-horizontal">
			<div class="form-group">
				<label for="inp_strain" class="<?php echo $style_label;?> control-label">Strain *</label>
				<div class="<?php echo $style_value;?> input-group-sm">
					<select class="form-control input-sm" name="strain" id="inp_strain">
						<option value="">Pilih</option>
						<?php
						foreach($strain as $s){
							echo '<option value="'.$s["kode_strain"].'">'.$s["nama_strain"].'</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="box" style="margin:0px 20px 0px 100px">	
					<fieldset>
						<legend> Farm </legend>
						<?php 
						foreach($farm_bdy as $fr){
							echo '<label class="checkbox-inline"><input type="checkbox" class="farm_bdy" value="'.$fr["KODE_FARM"].'">'.$fr["NAMA_FARM"].'</label>';
						}?>
					</fieldset>
				</div>
			</div>
			<div class="form-group">
				<label for="inp_musim" class="<?php echo $style_label;?> control-label"></label>
				<div class="col-sm-10 input-group-sm pull-right">
					<button type="button" name="tombolSet" id="btnSet" class="btn btn-primary disabled">Set</button>
				</div>
			</div>
		</form>
	</div>
	<div class="row" id="section_riwayat">
		<div class="col-md-8">
			<div class="box">	
				<fieldset>
					<legend> Riwayat Standar Budidaya </legend>
					<span style="visibility: hidden;">Selected riwayat : <span id="slc-riwayat"></span></span>
					<table id="riwayat-standar-budidaya" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th class="vert-align col-sm-1" rowspan="2">Nomor</th>
							<th class="vert-align col-sm-1" rowspan="2">Farm</th>
							<th class="vert-align col-sm-2" colspan="2">Masa Berlaku</th>
						</tr>
						<tr>
							<th class="vert-align  col-sm-1" >Awal</th>
							<th class="vert-align  col-sm-1" >Akhir</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					</table>
				</fieldset>
			</div>
			
			<div class="pull-right">
				<button type="button" name="tombolDetail" id="btnDetail" class="btn btn-primary disabled">Detail</button>
				<button type="button" name="tombolBaru" id="btnNew" class="btn btn-primary">Baru</button>
			</div>
			
		</div>
	</div>
	<br/>
	<div class="row"  id="section_kebutuhan_pakan">
		<div class="col-md-8">
			<form class="form-inline">
				<div class="form-group">
					<label for="inp_tanggalefektif">Tanggal Efektif : </label>
					<input type="text" class="form-control" id="inp_tanggalefektif" placeholder="Tanggal Efektif">
				</div>
			</form>
			<br/>
			
			<table class="table table-bordered table-condensed" style="width:400px;margin-left:20px;">
				<thead>
					<tr><th colspan="2">Budget Performances</th></tr>
				</thead>
				<tbody>
					<tr>
						<td class="col-md-2">Daya Hidup</td>
						<td class="col-md-1 vert-align" align="center"><input onkeyup="cekBpDecimal(this)" type="text" style="width:100px;text-align:center" class="form-control" id="inp_bp_daya_hidup"></td>
					</tr>
					<tr>
						<td>Berat Badan</td>
						<td align="center" class="vert-align"><input onkeyup="cekBpDecimal(this)" type="text" class="form-control" style="width:100px;text-align:center" id="inp_bp_berat_hidup"></td>
					</tr>
					<tr>
						<td>FCR</td>
						<td align="center" class="vert-align"><input onkeyup="cekBpDecimal(this)" type="text" class="form-control" style="width:100px;text-align:center" id="inp_bp_fcr"></td>
					</tr>
					<tr>
						<td>Umur Panen</td>
						<td align="center" class="vert-align"><input onkeyup="cekBpNumerik(this)" type="text" class="form-control" style="width:100px;text-align:center" id="inp_bp_umur_panen"></td>
					</tr>
					<tr>
						<td>Index Performance (IP)</td>
						<td align="center" class="vert-align"><input type="text" class="form-control" style="width:100px;text-align:center" id="inp_bp_ip" disabled></td>
					</tr>
					<tr>
						<td>KUM</td>
						<td align="center" class="vert-align"><input type="text" class="form-control" style="width:100px;text-align:center" id="inp_bp_kum" disabled></td>
					</tr>
				</tbody>
			</table>
			<br/>
			
			<div class="box">	
				<fieldset>
					<legend> Kebutuhan Pakan </legend>
					<table id="detail-standar-budidaya" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th class="vert-align col-sm-1">Umur Awal</th>
							<th class="vert-align col-sm-1">Umur Akhir</th>
							<th class="vert-align col-sm-1">Produk Pakan</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
					</table>
					
					<div id="kontrol_input_kebutuhan">
						Kebutuhan pakan : <span class="text-primary link" id="linkTambah">Tambah</span> | <span class="text-danger link" id="linkHapus">Hapus terakhir</span>
					</div>
				</fieldset>
			</div>
		
			<div class="pull-right">
				<button type="button" name="tombolSetDetail" id="btnSetDetail" class="btn btn-primary disabled">Set</button>
			</div>
			
		</div>
	</div>
	<br/>
	<div class="row"  id="section_detail_std_budidaya">
			<div class="box-std">
			<span>Detail Standar Budidaya</span>
			<table id="detail-mingguan-standar-budidaya" class="table table-bordered table-condensed">
			<thead>
				<tr>
					<th class="vert-align" rowspan="2">Umur</th>
					<th class="vert-align" colspan="2">Daya Hidup</th>
					<th class="vert-align" colspan="2">Standar Pakan (gr)</th>
					<th class="vert-align" colspan="2" style="background-color:#FAE9CD">Budget Pakan (gr)</th>
					<th class="vert-align" rowspan="2">BB</th>
					<th class="vert-align" rowspan="2">FCR</th>
					<th class="vert-align" rowspan="2">Jenis Pakan</th>
				</tr>
				<tr>
					<th class="vert-align">KUM</th>
					<th class="vert-align">HR</th>
					<th class="vert-align">KUM</th>
					<th class="vert-align">HR</th>
					<th class="vert-align" style="background-color:#FAE9CD">KUM</th>
					<th class="vert-align" style="background-color:#FAE9CD">HR</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			</table>
			
			<div class="col-sm-12">
				<div class="row pull-right">
					<form class="form-horizontal" id="standar-target">
					</form>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="row">
					<center>
						<button type="button" name="tombolSimpanStd" id="btnSaveStd" onclick="simpanStandarBaru()" class="btn btn-primary">Simpan</button>
					</center>
					
					<center>
						<button type="button" name="tombolPrint" id="btnPrint" class="btn btn-primary" onclick="print_std(this)">Cetak</button>
					
					</center>
				</div>
			</div>
			</div>
	</div>
</div>

<style type="text/css">
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
		width:30px;
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
	
	.link:hover{
		cursor:pointer;
	}
	
	.inp-right{
		text-align:right;
	}
	
	.no-border{
		border:none;
	}
	.box-std{
		margin:0px 15px 15px 15px;
	}
	.box legend {
		border-style: none;
		border-width: 0;
		font-size: 14px;
		line-height: 20px;
		margin-bottom: 0;
		margin-left:20px;
		width:180px;
	}
	.box fieldset {
		margin:0px 15px 15px 15px;
		border: 1px solid  #DBDBDB;
		border-radius:10px;
		padding:10px;
	}
	.input-xs {
		height: 22px;
		padding: 2px 5px;
		font-size: 8px;
		line-height: 1.5;
		border-radius: 3px;
    }
</style>

<script type="text/javascript" src="assets/js/master/std_budidaya_bdy.js"></script>
<script type="text/javascript" src="assets/js/master/combomulticolumn.js"></script>