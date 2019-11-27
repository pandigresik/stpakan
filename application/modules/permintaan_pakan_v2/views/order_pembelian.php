<div class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><?php echo (!$rekap) ? 'Order' : 'Rekap' ?> Pembelian Pakan</h3>
                            </div>
                            <div class="panel-body">
                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <div class="col-sm-2">
                                            <label for="startDate" class="control-label">Tanggal Kirim</label>
                                        </div>
                                        <div class="col-sm-3">
                                        	<div class="input-group">	
                                                <?php 
                                                    if(!empty($pencarian['tglawal'])){
                                                        $tglKirim = $pencarian['tglawal'];
                                                    }
                                                ?>
	                                            <input type="text" class="form-control"  value="<?php echo convertElemenTglWaktuIndonesia($tglKirim) ?>" name="startDate">
	                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 vcenter">s. d</div>
                                        <div class="col-sm-3">
                                        	<div class="input-group">	
                                                <?php 
                                                    if(!empty($pencarian['tglakhir'])){
                                                        $tglKirim = $pencarian['tglakhir'];
                                                    }
                                                ?>                                                    
	                                            <input type="text" class="form-control" value="<?php echo convertElemenTglWaktuIndonesia($tglKirim) ?>" name="endDate">
	                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div id="div_list_order">
	                                <table class="table table-bordered">
	                                    <thead>    
                                            <tr class="search">
                                                <th></th>
                                                <th>
                                                    <div class="right-inner-addon ">                                                    
                                                    <?php 
                                                        $cariFarm = '';
                                                        if(!empty($pencarian['kodefarm'])){
                                                            $cariFarm = $farm[$pencarian['kodefarm']];
                                                        }
                                                    ?>  
                                                        <i class="glyphicon glyphicon-search"></i>
                                                        <input type="search" class="form-control " name="nama_farm" placeholder="Search" value="<?php echo $cariFarm ?>">
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="right-inner-addon ">
                                                        <i class="glyphicon glyphicon-search"></i>
                                                        <input type="search" class="form-control " name="no_op" placeholder="Search">
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
                                                        <input type="search" class="form-control " name="no_do_ekspedisi" placeholder="Search">
                                                    </div>
                                                </th>
                                                <th></th>
                                                <th></th>
                                                <th>
                                                    <span class="btn btn-default" id="spanCari" onclick="Plotting.list_order_pembelian_pakan(this,<?php echo $rekap ?>)">Cari</span>
                                                </th>
                                                
                                                </tr>
                                                <tr>
                                                    <th>Tanggal Kirim</th>
                                                    <th>Farm</th>    
                                                    <th>No. OP</th>
                                                    <th>No. PP</th>
                                                    <th>Total Pakan</th>
                                                    <th>Expedisi</th>
                                                    <th>No. DO</th>	
                                                    <th>Keterangan</th>	
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
        <div class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Detail Pengiriman</h3>
                            </div>
                            <div class="panel-body" id="div_transaksi_order_pembelian">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="assets/js/forecast/config.js"></script>        
        <script type="text/javascript" src="assets/js/permintaan_pakan_v2/order_pembelian.js"></script>
        <link rel="stylesheet" type="text/css" href="assets/css/permintaan_pakan_v2/order_pembelian.css?v=0.0.1" >