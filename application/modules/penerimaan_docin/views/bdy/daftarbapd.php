<div class="panel panel-default">
		<div class="panel-heading">
			Berita Acara Penerimaan DOC In
		</div>
		<div class="panel-body">
			<?php
			if($access == 'approve'){
					echo '<span class="btn btn-primary" onclick="BAPD.ackapprove(this)">Approve</span> &nbsp; <span class="btn btn-default" onclick="BAPD.reject(this)">Reject</span>';
			}
			?>
			<div id="list_farm" class="header text-center row">
				<div name="divFarm" class="col-md-4 col-md-offset-4"><h3><?php echo strtoupper($nama_farm) ?></h3></div>
			</div>
			<div>
				<div class="row">
				<form class="form">
						<div class="form-inline col-md-12  text-center">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="tindaklanjut" checked> BAPD belum ditindaklanjuti
								</label>
							</div>
						</div>
				</form>		
					<form class="form form-horizontal col-md-4 col-md-offset-4">
							<div class="form-group">
								<label class="col-md-2">Kandang</label>
								<div class="col-md-10">
									<select class="form-control" name="kode_kandang" <?php echo $disabled ?> >
										<option value="">Pilih Kandang</option>
										<?php
											if(!empty($list_kandang)){
												foreach($list_kandang as $ls){
													echo '<option value="'.$ls['KODE_KANDANG'].'">Kandang '.$ls['KODE_KANDANG'].'</option>';
												}
											}
										?>
									</select>					
								</div>	
							</div>
							<div class="form-group">
								<label class="col-md-2">Hatchery</label>
								<div class="col-md-10">
									<select class="form-control" name="kode_hatchery" <?php echo $disabled ?> >
										<option value="">Pilih Hatchery</option>
										<?php
											if(!empty($list_hatchery)){
												foreach($list_hatchery as $ls){
													echo '<option value="'.$ls['KODE_HATCHERY'].'">'.$ls['NAMA_HATCHERY'].'</option>';
												}
											}
										?>
									</select>						
								</div>	
							</div>
							<div class="form-group">
								<label class="col-md-2">Siklus</label>
								<div class="col-md-10">
									<select class="form-control" name="kode_siklus" <?php echo $disabled ?> >
										<option value="">Pilih Siklus</option>
										<?php
											if(!empty($list_siklus)){
												foreach($list_siklus as $ls){
													echo '<option value="'.$ls['kode_siklus'].'">'.$ls['periode_siklus'].'</option>';
												}
											}
										?>
									</select>
								</div>	
							</div>
							<div class="form-group">
								<div class="col-md-10 col-md-offset-2">			
									<span onclick="BAPD.list_bapd(this,'#list_bapdocin')" class="btn btn-default btn_cari">Cari</span>
								</div>	
							</div>
						</div>
					</form>
			</div>
		</div>
		<div class="">
			<div id="list_bapdocin">

			</div>
		</div>
</div>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/penerimaan_docin/bapd.js"></script>
<script type="text/javascript" src="assets/js/penerimaan_docin/beritaacara.js"></script>
