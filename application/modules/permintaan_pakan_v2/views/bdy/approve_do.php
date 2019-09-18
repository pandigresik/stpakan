<div class="panel panel-default">
		<div class="panel-heading">
			Order Pembelian Pakan
		</div>
		<div class="panel-body">
			
			<div>
				<div class="row col-md-12">
				<?php
                if($access == 'approve'){
                       echo '<span class="btn btn-primary" onclick="ApproveDO.approve(this)">Approve</span> &nbsp; <span class="btn btn-default" onclick="ApproveDO.reject(this)">Reject</span>';
                }
                ?>
				<form class="form form-horizontal" role="form">
                <div class="form-group">
                    <div class="col-md-12">
                            <div class="checkbox">
								<label>
									<input type="checkbox" name="tindaklanjut" checked> Belum ditindaklanjuti
								</label>
							</div>
                            </div>
                            </div>
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
                                    <div class="form-group">
                                        <div class="col-md-2 col-md-offset-2"><span class="btn btn-default btn_cari" onclick="ApproveDO.cari()">Cari</span></div>
                                    </div>
                                </form>
			</div>
		</div>
		<div id="div_list_order">
	                                <table class="table table-bordered">
	                                    <thead>    
                                            
                                                <tr>
                                                    <th></th>
                                                    <th>Tanggal Kirim</th>
                                                    <th>Farm</th>    
                                                    <th>Rit</th>    
                                                    <th>No. OP</th>
                                                    <th>No. PP</th>
                                                    <th>Total Kirim</th>
                                                    <th>Ekspedisi</th>
                                                    <th>Total Pakan</th>
                                                    
                                                    <th>Keterangan</th>	
                                                </tr>
                                            </thead>	                                    
	                                    <tbody>
	                                        
	                                    </tbody>
	                                </table>
                               </div> 
</div>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/permintaan_pakan_v2/approvaldo.js"></script>
