<div class="panel panel-default">
  <div class="panel-heading">Config Timeline</div>
  <div class="panel-body">
	<table id="master-general-config" class="table table-bordered table-striped">
	<thead>
		<tr>
            <th></th>
            <th class="col-md-2">
							<div class="input-group">
								<select class="form-control" name="kode_farm" onchange="GeneralConfig.getReport(1)">
									<option value="">Semua</option>
									<?php
										if(!empty($farm)){
											foreach($farm as $f){
												echo '<option value="'.$f['kode_farm'].'">'.$f['nama_farm'].'</option>';
											}
										}
									?>
								</select>
							</div>
						</th>
            <th class="col-md-2">
							<div class="input-group">
								<select class="form-control" name="context" onchange="GeneralConfig.getReport(1)">
									<option value="">Semua</option>
									<?php
										if(!empty($context)){
											foreach($context as $c){
												echo '<option>'.$c['context'].'</option>';
											}
										}
									?>
								</select>
							</div>
						</th>
	  </tr>
		<tr>
            <th style="width:1%">No</th>
            <th class="col-md-2 kode_farm">Kode Farm</th>
						<th class="col-md-2">Context</th>
            <th class="col-md-2">Kode Config</th>
            <th class="col-md-2">Deskripsi</th>
            <th class="col-md-1">Nilai</th>
            <th class="col-md-1">Status</th>
        </tr>
    </thead>
	<tbody>
	</tbody>
	</table>
	<div class="row clear-fix">
        <div class="col-md-3 pull-right">
            <button  id="previous" onclick="GeneralConfig.prev(this);" class="btn btn-sm btn-primary" disabled>Previous</button>
            <label>Page <label id="page_number"></label> of <label id="total_page"></label></label>
            <button  id="next" onclick="GeneralConfig.next(this);" class="btn btn-sm btn-primary">Next</button>
        </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="assets/js/master/config_general.js"></script>
