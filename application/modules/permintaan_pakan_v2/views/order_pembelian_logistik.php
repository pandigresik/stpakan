<div class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Cetak Order Pembelian</h3>
                            </div>
                            <div class="panel-body">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label for="startDate" class="control-label">Tanggal OP</label>
                                        </div>
                                        <div class="col-sm-3">
                                        	<div class="input-group">	
	                                            <input type="text" class="form-control" name="startDate">
	                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 vcenter">s. d</div>
                                        <div class="col-sm-3">
                                        	<div class="input-group">	
	                                            <input type="text" class="form-control" name="endDate">
	                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div id="div_list_order">
	                                <table class="table table-bordered">
	                                    <thead>
	                                        <tr class="search">
	                                            <th>
	                                                <div class="right-inner-addon ">
	                                                    <i class="glyphicon glyphicon-search"></i>
	                                                    <input type="search" class="form-control " name="no_op_logistik" placeholder="Search">
	                                                </div>
	                                            </th>
	                                            <th>
	                                                <div class="right-inner-addon ">
	                                                    <i class="glyphicon glyphicon-search"></i>
	                                                    <input type="search" class="form-control " name="no_pp" placeholder="Search">
	                                                </div>
	                                            </th>
	                                            <th>
	                                                <div class="right-inner-addon ">
	                                                    <i class="glyphicon glyphicon-search"></i>
	                                                    <input type="search" class="form-control " name="nama_farm" placeholder="Search">
	                                                </div>
	                                            </th>
	                                           
	                                            <th>
	                                                <span class="btn btn-default" onclick="Permintaan.list_order_pembelian_logistik(this)">Cari</span>
	                                            </th>
	                                        </tr>
	                                        <tr>
	                                            <th>No. OP Logistik</th>
	                                            <th>No. PP</th>
	                                            <th>Farm</th>	                                
	                                            <th>Tanggal OP</th>	            
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
        </div>
        
        <script type="text/javascript" src="assets/js/permintaan_pakan_v2/ppHandler.js"></script>
        <script type="text/javascript" src="assets/js/permintaan_pakan_v2/cetak_order_pembelian.js"></script>