<div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#overview" role="tab" data-toggle="tab" id="for_overview" onclick="plottingPelaksana.stopFinger()">Overview<span class='help'></span></a>
		</li>

		<li>
			<a href="#plotting" role="tab" data-toggle="tab" id="for_plotting" onclick="plottingPelaksana.triggerFinger()">Plotting<span class='help'></span></a>
		</li>
	</ul>
</div>

<div class="tab-content new-line">
	<div id="overview" class="tab-pane fade active in">
		<div class="row">
		 	<div class="container col-md-12">
				<div class="row col-md-6">
					<form class="form form-horizontal form_cari" onsubmit="return false">
						<div class="form-group">
							<label for="" class="col-md-2 control-label">
								<p class="text-left">Farm</p>
							</label>
							<div class="col-md-6">
								<input type="text" class="form-control" readonly name="farm" value="<?php echo $farm[0]['NAMA_FARM'] ?>" />
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-md-2 control-label">
								<p class="text-left">Periode</p>
							</label>
							<div class="col-md-3">
								<select class="form-control" name="periode1"  >
									<?php
										if(!empty($siklus)){
											foreach($siklus as $ls){
												if ($ls['siklus_sebelum'] == $ls['KODE_SIKLUS']) {
													$selected = 'selected';
												}else {
													$selected = '';
												}
												echo '<option value="'.$ls['KODE_SIKLUS'].'"'.$selected.'>'.$ls['PERIODE_SIKLUS'].'</option>';
											}
										}
									?>
								</select>
							</div>

							<label class="col-md-1 control-label" for="">
								<p class="text-center">s/d</p>
							</label>
							<div class="col-md-3">
								<select class="form-control" name="periode2">
									<?php
										if(!empty($siklus)){
											foreach($siklus as $ls){
												echo '<option value="'.$ls['KODE_SIKLUS'].'">'.$ls['PERIODE_SIKLUS'].'</option>';
											}
										}
									?>
								</select>
							</div>
							<div class="col-md-1">
								<span class="btn btn-default" onclick="goSearch(this)">Cari</span>
							</div>
						</div>

					</form>
				</div>
                <div id="daftar-do-table" class="new-line">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="siklus" ></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="tgl_doc_in" ></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="flock" ></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="kandang" ></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="koordinator"></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="pengawas"></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="operator"></th>
                                <!-- <th>
                                    button class="btn btn-default" id="btn-cari"
                                    onclick="goSearch()">Cari</button</th>-->
                            </tr>
                            <tr>
                                <th class="col-md-1">Siklus</th>
                                <th class="col-md-1">Tgl DOC In</th>
                                <th class="col-md-1">Flock</th>
                                <th class="col-md-1">Kandang</th>
                                <th class="col-md-1">Koordinator <br>pengawas</th>
                                <th class="col-md-1">Pengawas</th>
                                <th class="col-md-1">Operator</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="new-line clear-fix">
                        <div class="col-md-3 pull-right">
                            <button id="previous" class="btn btn-sm btn-primary" disabled>Previous</button>
                            <label>Page <label id="page_number"></label> of <label id="total_page"></label></label>
                            <button id="next" class="btn btn-sm btn-primary">Next</button>
                        </div>
                    </div>
                </div>
			</div>
		</div>
	</div>
	<div id="plotting" class="tab-pane fade">
		<div class="row">
		 	<div class="container col-md-12">
				<div class="row col-md-6">
					<form class="form form-horizontal form_plotting" onsubmit="return false">
						<div class="form-group">
							<label class="control-label col-md-4">Farm</label>
							<div class="col-md-6">
								<input type="text" class="form-control" readonly name="farm" data-kode_farm="<?php echo $farm[0]['KODE_FARM'] ?>" value="<?php echo $farm[0]['NAMA_FARM'] ?>"  />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-4">Siklus</label>
							<div class="col-md-6">
								<select class="form-control" name="siklus" disabled >
						          	<?php
								  		if(!empty($siklus)){
						              		foreach($siklus as $ls){
						                		echo '<option value="'.$ls['KODE_SIKLUS'].'">'.$ls['PERIODE_SIKLUS'].'</option>';
						              		}
						            	}
						          	?>
						        </select>
							</div>
						</div>
					</form>
				</div>
				<div class="clearfix"></div>
				<div class="row">
					<div class="abang text-center">Harap melakukan finger untuk ploting</div>
				</div>
				<div class="row col-md-12">
					<table class="table table-bordered plotting_table">
						<thead>
							<tr>
								<th class="text-center">Flock</th>
								<th class="text-center">Koordinator Pengawas</th>
								<th class="text-center">Pengawas</th>
								<th class="text-center">Kandang</th>
								<th class="text-center">Operator</th>
							</tr>
						</thead>
						<tbody>
						<?php 
							if(!empty($list_plotting)){
								foreach($list_plotting as $lp){
									echo '<tr>
										<td class="flok">'.$lp['flok'].'</td>
										<td class="koordinator" data-kode_pegawai="'.$lp['koordinator'].'">'.$lp['nama_koordinator'].'</td>
										<td class="pengawas" data-kode_pegawai="'.$lp['pengawas'].'">'.$lp['nama_pengawas'].'</td>
										<td class="kandang">'.$lp['kode_kandang'].'</td>
										<td class="operator" data-kode_pegawai="'.$lp['operator'].'">'.$lp['nama_operator'].'</td>
									</tr>';
								}
							}
						?>					
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="row">
		 	<div class="container col-md-12">
				<div class="row col-md-12">
					<div class="form-group"></div>
				</div>
			</div>
		</div>	
		<div class="row">
			<div class="container col-md-12">
				<div class="row col-md-12">
					<?php if (!$complete_ploting): ?>
						<button id="save" class="btn btn-sm btn-primary pull-right" onclick="plottingPelaksana.save();">Simpan</button>
					<?php endif; ?>	
				</div>
			</div>
		</div>	
	</div>
</div>
<script type="text/javascript" src="assets/libs/jquery-rowspanizer/jquery.rowspanizer.js"></script>
<link rel="stylesheet" type="text/css" href="assets/css/kandang/plotting_pelaksana.css">
<script type="text/javascript" src="assets/js/kandang/plotting_pelaksana.js"></script>
