
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-8">	  		
	  		 <form class="form-horizontal" role="form">
			  <div class="form-group">
			  	<div class="col-md-8 col-md-offset-2">
				  	<div class="checkbox">
					   	<label>
					    	<input type="checkbox" name="filter_flok" checked> Filter yang belum punya flock
					   	</label>
					</div>	
				</div>	
			  </div>
			  <div class="form-group">
			    <label class="control-label col-md-2" for="">Tanggal DOC-In</label>
			    <div class="col-md-10">
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
			        <div class="col-md-3">
			        	<span class="btn btn-default" id="tampilkan_flok">Tampilkan</span>
			        </div>
			    </div>
			  </div>
			  <div class="form-group">
			  	<div class="col-md-4 col-md-offset-2">
					<div class="btn btn-default" id="flock_btn">Set Flock</div>
				</div>	
			  </div>
			 </form> 
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-12" id="div_tabel_flock">
		
		</div>
	</div>


<script type="text/javascript" src="assets/js/forecast/flock.js"></script>	