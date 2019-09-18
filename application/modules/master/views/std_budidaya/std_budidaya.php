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
				<label for="inp_jeniskelamin" class="<?php echo $style_label;?> control-label">Jenis Kelamin *</label>
				<div class="<?php echo $style_value;?> input-group-sm">
					<label class="radio-inline col-sm-5">
						<input type="radio" name="jeniskelamin" id="inp_jeniskelaminjantan" value="J" checked> Jantan
					</label>
					<label class="radio-inline">
						<input type="radio" name="jeniskelamin" id="inp_jeniskelaminbetina" value="B"> Betina
					</label>
				</div>
			</div>
			
			<div class="form-group">
				<label for="inp_tipekandang" class="<?php echo $style_label;?> control-label">Tipe Kandang *</label>
				<div class="<?php echo $style_value;?> input-group-sm">
					<label class="radio-inline col-sm-5">
						<input type="radio" name="tipekandang" id="inp_tipekandang_open" value="O" checked> Open House
					</label>
					<label class="radio-inline">
						<input type="radio" name="tipekandang" id="inp_tipekandang_close" value="C"> Closed House
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="inp_musim" class="<?php echo $style_label;?> control-label">Musim *</label>
				<div class="<?php echo $style_value;?> input-group-sm">
					<label class="checkbox-inline col-sm-5">
						<input type="checkbox" id="inp_musim_in" value="I"> In Season
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" id="inp_musim_out" value="O"> Out Season
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="inp_musim" class="<?php echo $style_label;?> control-label"></label>
				<div class="col-sm-10 input-group-sm pull-right">
					<button type="button" name="tombolSet" id="btnSet" class="btn btn-primary">Set</button>
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
							<th class="vert-align col-sm-1" rowspan="2">Musim</th>
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
			<!--<br/><input type="button" name="ambilData" class="btn btn-primary" value="Ambil Data" onclick="ambilData()"/> <br/><br/>
			<br/><input type="button" name="isiDataDummy" class="btn btn-primary" value="Generate Data from Array" onclick="isiDataDummy()"/> <br/><br/>
			--><table id="detail-mingguan-standar-budidaya" class="table table-bordered table-condensed">
			<thead>
				<tr>
					<th class="vert-align" rowspan="2">Masa Pertumbuhan</th>
					<th class="vert-align" rowspan="2">Umur Minggu</th>
					<th class="vert-align" rowspan="2">Pengurangan</th>
					<th class="vert-align" colspan="3">Deplesi</th>
					<th class="vert-align" rowspan="2">Daya Hidup (%)</th>
					<th class="vert-align" colspan="5">Pakan</th>
					<th class="vert-align" colspan="2">Berat Badan</th>
					<th class="vert-align" rowspan="2">Jenis Pakan</th>
					<th class="vert-align col-sm-2" rowspan="2">Keterangan</th>
				</tr>
				<tr>
					<th class="vert-align">Mati (%)</th>
					<th class="vert-align">Afkir (%)</th>
					<th class="vert-align">Seleksi (%)</th>
					<th class="vert-align">Gr/Ek/Hr Target</th>
					<th class="vert-align">Energi (kcal/hr)</th>
					<th class="vert-align">Cum. Energi (kcal)</th>
					<th class="vert-align">Protein (gr)</th>
					<th class="vert-align">Cum. Protein (gr)</th>
					<th class="vert-align">Gr/Ek Target</th>
					<th class="vert-align">Weight Gain (%)</th>
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

<script type="text/javascript" src="assets/js/master/std_budidaya.js"></script>
<script type="text/javascript" src="assets/js/master/combomulticolumn.js"></script>