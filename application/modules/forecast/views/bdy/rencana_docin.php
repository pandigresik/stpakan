<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">Perencanaan DOC In</div>
				<div class="panel-body">
					<div class="heading text-center">PERENCANAAN DOC IN<br />Farm Budidaya</div>
					<div>
						<div class="panel panel-default">
							<div class="panel-heading">Siklus Tahunan</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-6">
										<?php
										echo '<div class="table-responsive">';
											echo '<table class="table table-bordered" id="tabelSiklusTahunan">
													<thead>
														<tr onclick="Forecast.">
															<th>Tahun</th>
															<th>Status</th>
														</tr>
													</thead>
													<tbody>
														';
											if(!empty($siklusTahunan)){
												foreach($siklusTahunan as $th){
													echo '<tr data-awal_docin="'.$th['awal_docin'].'">
															<td class="tahun">'.$th['tahun'].'</td>
															<td class="status">'.$th['status'].'</td>
														</tr>';
												}
											}
												echo '</tbody>
													</table>';
											echo '</div>';
										?>
									</div>
									<div class="col-md-6">
										<div class="row">
											<div  data-aksi="import" class="btn btn-default <?php echo ($canImport) ? '' : 'disabled' ?>" <?php echo ($canImport) ? 'onclick="Import.showDiv(\'#import_div\')"' : '' ?> >Import</div>
										</div>
										<div class="row new-line">
										<?php if($canApprove){
										echo '
										    <div class="btn-group">
										        <button type="button" data-toggle="dropdown" class="btn btn-default dropdown-toggle">Approve <span class="caret"></span></button>
										        <ul class="dropdown-menu">
										            <li><a data-status="'.$statusApprove.'" onclick="Import.rilis_approve(event,this)" href="#">Approve</a></li>
										            <li><a data-status="'.$statusApprove.'" onclick="Import.reject(event,this)" href="#">Reject</a></li>
										        </ul>
										    </div>';
										}
										else{
											echo '<div data-status="'.$statusApprove.'"  onclick="Import.rilis_approve(event,this)" class="btn btn-default">Rilis</div>';
										}
										?>
										</div>
										<div class="row new-line">
											<div class="btn btn-default"  onclick="Import.preview('#preview_div')">Preview</div>
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
<div class="row">
	<?php if($canImport){ ?>
		<div class="col-md-5" id="import_div">
			<div class="panel panel-default">
				<div class="panel-heading">Import Perencanaan DOC In <span class="pull-right"><a href="file_upload/docTemplate.xls" target="_blank" >Contoh file</a></span></div>
				<div class="panel-body">
					<div class="col-md-12 ">
                            <div class="form-group form-horizontal">
                                <div class="form-inline new-line">
                                    <label class="col-md-3" for="docinfile">Nama File</label>
                                    <div class="form-group col-md-6">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="docinfile" name="docinfile">
                                             <span class="btn btn-default btn-file input-group-addon">
                                                <b>...</b> <input type="file" id="docinfileupload" />
                                             </span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="form-group col-md-12">
                             <div class="col-md-offset-3 new-line">
                              	<div class="btn btn-default" onclick="Import.bacaXLS('#docinfileupload','#tabel_docin')">Import From Excel</div>&nbsp;
                             	<div class="btn btn-default" onclick="Import.simpanDocIn()">Simpan</div>
                             </div>
                       </div>
                       <div class="panel panel-default row">
                       	<div class="panel-heading">Detail Perencanaan DOC In</div>
                       	<div class="panel-body">
                       		<div class="row">
	                       		<table class="table table-bordered custom_table table-striped" id="tabel_docin">
	                       			<thead>
	                       				<tr>
	                       					<th>Farm</th>
	                       					<th>Kandang</th>
	                       					<th>Tanggal DOC In</th>
	                       					<th>Siklus</th>
	                       					<th>Populasi <br /> (ekor)</th>
	                       					<th>Tanggal Panen</th>
	                       					<th>Strain</th>
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
	<?php } ?>
		<div class="col-md-8" id="preview_div">
			<div class="panel panel-default">
				<div class="panel-heading">Preview Perencanaan DOC In</div>
				<div class="panel-body">
					<div class="row">
				        <div class="col-md-12">
				          <form class="form-horizontal" role="form">
				            <div class="form-group">
				              <div class="col-sm-1">
				                <label for="inputFarm" class="control-label">Farm</label>
				              </div>
				              <div class="col-sm-6">
				                <select class="form-control" name="list_farm">
				                	<option value="">Pilih Farm</option>
				                </select>
				              </div>
				            </div>
				            <div class="form-group">
				              <div class="col-sm-offset-1 col-sm-6">
				                <span class="btn btn-default" onclick="Import.tampilkanDocIn()">Tampilkan</span>
				                <span class="btn btn-default" onclick="Import.cetakPerencanaanDocIn(this)">Cetak</span>
				              </div>
				            </div>

						      <div class="form-group">
		      					<div class="col-md-4 col-md-offset-1">
		      						<div class="row">
		      							<div class="col-md-6">
		      								<label class="control-label" >Daya Hidup</label>
		      							</div>
		      							<div class="col-md-6">
		      								<div class="input-group">
		      									<input type="text" name="dayahidup" class="form-control col-md-4 number" />
		      									<span class="input-group-addon">%</span>
		      								</div>
		      							</div>
		      						</div>
		      					</div>
		      					<div class="col-md-4">
		      						<div class="row">
		      							<div class="col-md-6">
		      								<label class="control-label" >FCR</label>
		      							</div>
		      							<div class="col-md-6">
		      								<input type="text"  name="fcr" class="form-control col-md-2 number" />
		      							</div>
		      						</div>
		      					</div>
		      					<div class="col-md-3">
		      						<div class="row">
		      							<div class="col-md-4">
		      								<label class="control-label" >IP</label>
		      							</div>
		      							<div class="col-md-8">
		      								<input type="text"  name="ip" class="form-control col-md-2 number" />
		      							</div>
		      						</div>
		      					</div>
		      				</div>
		      				  <div class="form-group">
		      					<div class="col-md-4 col-md-offset-1">
		      						<div class="row">
		      							<div class="col-md-6">
		      								<label class="control-label" >Berat Badan</label>
		      							</div>
		      							<div class="col-md-6">
		      								<div class="input-group">
		      									<input type="text"  name="beratbadan" class="form-control col-md-4 number" />
		      									<span class="input-group-addon">g</span>
		      								</div>
		      							</div>
		      						</div>
		      					</div>
		      					<div class="col-md-4">
		      						<div class="row">
		      							<div class="col-md-6">
		      								<label class="control-label" >Umur Panen</label>
		      							</div>
		      							<div class="col-md-6">
		      								<div class="input-group">
		      									<input type="text"  name="umurpanen" class="form-control col-md-4 number" />
		      									<span class="input-group-addon">hari</span>
		      								</div>
		      							</div>
		      						</div>
		      					</div>
		      					<div class="col-md-3">
		      						<div class="row">
		      							<div class="col-md-4">
		      								<label class="control-label" >Kum</label>
		      							</div>
		      							<div class="col-md-8">
		      								<input type="text"  name="kum" class="form-control col-md-2 number" />
		      							</div>
		      						</div>
		      					</div>
		      				</div>

				          </form>
				        </div>
      				</div>
      				<!-- tempat tabel -->
      				<div class="row">
      					<table class="table table-bordered custom_table table-striped" id="preview_tabel_docin">
	                     	<thead>
	                     		<tr>
	                     			<th data-id="kandang">Kandang</th>
	                     			<th data-id="tgl_docin">Tanggal DOC In</th>
	                     			<th data-id="siklus">Siklus</th>
	                     			<th data-id="strain">Strain</th>
	                     			<th data-id="populasi">Populasi <br /> (ekor)</th>
	                     			<th data-id="tgl_panen">Tanggal Panen</th>
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
<script type="text/javascript" src="assets/libs/js-xlsx/dist/xlsx.full.min.js"></script>
<script type="text/javascript" src="assets/js/forecast/import_docin.js"></script>
