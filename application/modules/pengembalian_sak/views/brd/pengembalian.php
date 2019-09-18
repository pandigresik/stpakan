<div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#daftarPengembalian" role="tab" data-toggle="tab" id="for_daftarPermintaan">Daftar Pengembalian Sak<span class='help'></span></a>
		</li>
		
		<li>
			<a href="#transaksi" role="tab" data-toggle="tab" id="for_transaksi">Transaksi<span class='help'></span></a>
		</li>
		
	</ul>
</div>

<div class="tab-content new-line">
	<div id="daftarPengembalian" class="tab-pane fade active in">
		<div class="row">	
			<div class="col-md-2">				
				<?php echo $buat_baru ?>
			</div>
			<div class="container col-md-12">	
				<div class="col-md-10">
						<form class="form form-horizontal">
							<div class="form-group">
								<div class="col-md-1">
									<label class="control-label col-md-1">Tanggal</label>										
								</div>
								<div class='col-md-8'>
									<div class='col-md-3'>
							            <div class="form-group">
							                <div class="input-group date">
							                    <input type="text" class="form-control parameter" name="startDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>
							                </div>
							            </div>
							        </div>
							    	<div class='col-md-1 vcenter'>s.d.</div>    
							        <div class='col-md-3'>
							            <div class="form-group">
							                <div class="input-group date" >
							                    <input type="text" class="form-control parameter" name="endDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>	
							                </div>
							            </div>
							        </div>
								</div>
							</div>
							<div class="form-group">
								<div class='col-md-2 col-md-offset-1'>
								   	<span class="btn btn-default" onclick="Pengembalian.list_cari(this)">Cari</span>						                
						        </div>
							</div>
						
					</form>
				</div>	
				
				<div class="col-md-12" id="list_pengembalian">
					<?php 
							
					?>
				</div>	
			</div>		
		</div>
	</div>	
	<div id="transaksi" class="tab-pane fade">
		
	</div>	
	<div id="laporan" class="tab-pane fade">
	
	</div>
</div>
 
<link rel="stylesheet" type="text/css" href="assets/css/pengembalian_sak/pengembalian.css" >
<script type="text/javascript" src="assets/js/forecast/config.js"></script> 
<script type="text/javascript" src="assets/js/pengembalian_sak/pengembalian.js"></script>

