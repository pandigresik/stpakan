<div class="panel panel-default">
	<div class="panel-heading">Review Retur Sak Kosong</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-8">
				  	<div class="checkbox">
					   	<label>
					    	<input type="checkbox" name="filter_retur" checked> Filter retur sak yang membutuhkan tindak lanjut
					   	</label>
					</div>	
				</div>	
		</div>
		<div class="row">	
			<div class="col-md-12">	
						<form class="form form-inline">
							
								<div class="form-group">
									<label class="control-label ">Tanggal Retur</label>										
								</div>
								
									
							            <div class="form-group">
							                <div class="input-group date">
							                    <input type="text" class="form-control parameter" name="startDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>
							                </div>
							            </div>
							       
							    	<div class=' vcenter'>s.d.</div>    
							        
							            <div class="form-group">
							                <div class="input-group date" >
							                    <input type="text" class="form-control parameter" name="endDate" readonly />
							                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
							                    </span>	
							                </div>
							            </div>
							        	<span class="btn btn-default" id="btn_cari" onclick="Approval.list_cari(this)">Cari</span>
							   	
					</form>
					
			</div>		
		</div>
	</div>	
</div>
<div id="list_farm_retur">
	
</div>
 

<script type="text/javascript" src="assets/js/forecast/config.js"></script> 
<script type="text/javascript" src="assets/js/pengembalian_sak/approval.js"></script>

