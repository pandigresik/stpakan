<div class="tab-content new-line">
	<div id="overview" class="tab-pane active in">
		<div class="row">
			<div class="col-md-12">
				<div class="btn btn-default" onclick="plottingPelaksanaAck.ack(this)">Ack</div>
			</div>
		 	<div class="container col-md-12">
				<div class="row col-md-6">
					<form class="form form-horizontal form_cari" onsubmit="return false">
						<div class="form-group">
							<label for="" class="col-md-2 control-label">
								<p class="text-left">Farm</p>
							</label>
							<div class="col-md-6">
								<select name="farm" class="form-control" onchange="plottingPelaksanaAck.loadPlottingFarm(this)">
									<?php										
										foreach($farm as $f){
											$selected = $farm_terpilih == $f['KODE_FARM'] ? 'selected' : '';
											echo '<option value="'.$f['KODE_FARM'].'" '.$selected.'>'.$f['NAMA_FARM'].'</option>';
										}
									?>
								</select>
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
								<th></th>		
                                <th><input class="form-control filter" placeholder="cari" type="text" name="siklus" ></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="tgl_doc_in" ></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="flock" ></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="kandang" ></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="koordinator"></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="pengawas"></th>
                                <th><input class="form-control filter" placeholder="cari" type="text" name="operator"></th>
                                <th></th>
                            </tr>
                            <tr>
								<th class="col-md-1">Aksi</th>
                                <th class="col-md-1">Siklus</th>
                                <th class="col-md-1">Tgl DOC In</th>
                                <th class="col-md-1">Flock</th>
                                <th class="col-md-1">Kandang</th>
                                <th class="col-md-1">Koordinator <br>pengawas</th>
                                <th class="col-md-1">Pengawas</th>
                                <th class="col-md-1">Operator</th>
								<th class="col-md-1">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
				<!--	
                    <div class="new-line clear-fix">
                        <div class="col-md-3 pull-right">
                            <button id="previous" class="btn btn-sm btn-primary" disabled>Previous</button>
                            <label>Page <label id="page_number"></label> of <label id="total_page"></label></label>
                            <button id="next" class="btn btn-sm btn-primary">Next</button>
                        </div>
                    </div>
				-->	
                </div>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" type="text/css" href="assets/css/kandang/plotting_pelaksana.css">
<script type="text/javascript" src="assets/js/kandang/plotting_pelaksana_ack.js"></script>
