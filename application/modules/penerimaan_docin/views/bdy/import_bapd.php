<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">Daftar BAPD</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<form class="form form-inline" action="" onsubmit="return false">
								<select name="kode_farm" class="form-control" id="">
									<option value="">-- Farm --</option>
									<?php
										if(!empty($list_farm)){
											foreach($list_farm as $lf){
												echo '<option value="'.$lf.'">'.$nama_farm[$lf].'</option>';
											}
										}
									?>
								</select>
								<select name="kode_siklus" class="form-control" id="">
									<option value="">-- Siklus --</option>
									<?php
										if(!empty($list_siklus)){
											foreach($list_siklus as $lf){
												echo '<option value="'.$lf.'">'.$lf.'</option>';
											}
										}
									?>
								</select>
								<button class="btn btn-default" onclick="ImportBapd.cari(this)">Cari</button>
							</form>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-md-9">
							<div class="sticky-table">
							<table class="table table-bordered" id="tablebapd">
								<thead>
									<tr class="sticky-header">
										<th>Farm</th>
										<th>Siklus</th>
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
								<?php 
									if(!empty($listBapd)){
										foreach($listBapd as $bapd){
											$sudahUpload = empty($bapd['jmlbapd']) ? 0 : 1;
											echo '<tr data-upload="'.$sudahUpload.'" onclick="ImportBapd.pilihSiklus(this)">
												<td class="kode_farm" data-kode_farm="'.$bapd['kode_farm'].'" >'.$nama_farm[$bapd['kode_farm']].'</td>
												<td class="periode_siklus">'.$bapd['periode_siklus'].'</td>
												<td>'.($sudahUpload ? '<span class="link_span"><em>Uploaded</em></span>' : '').'</td>
											</tr>';
										}
									}
								?>	
								</tbody>
							</table>
							</div>
						</div>
						<div class="col-md-3" id="divBtn">
							<div class="sudahupload hide">
								<div class="btn btn-default" onclick="ImportBapd.preview()">Preview</div>	
								<br /><br />
								<div class="btn btn-default new_line" onclick="ImportBapd.laporanBapd()">Lap. BAPD</div>	
							</div>		
							<div class="belumupload hide">
								<div class="btn btn-default" onclick="ImportBapd.import()">Import</div>	
							</div>		
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">Detail Kode Box</div>
				<div class="panel-body" id="detailkodebox">

				</div>
			</div>
		</div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/jquery.stickytable.css?v=0.2" >
<script type="text/javascript" src="assets/libs/js-xlsx/dist/xlsx.full.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.stickytable.js"></script>
<script type="text/javascript" src="assets/js/penerimaan_docin/import_bapd.js"></script>

