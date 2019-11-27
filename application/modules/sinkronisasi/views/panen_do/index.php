<div class="row">
    <div class="col-md-12">
        <form class="form form-horizontal">
            <div class="form-group">
                <label for="tglPanen" class="control-label col-md-2">Tanggal Panen</label>
                <div class="col-md-2">
                    <div class="input-group">	
	                    <input class="form-control" name="tglPanen" type="text" value="<?php echo  tglIndonesia(date('Y-m-d'),'-',' ') ?>">
	                    <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <span onclick="Sinc_DO.ambilDO(this,'sinkronisasi/panen_do/ambilDO')" class="btn btn-primary">Sinkron DO</span>
                    <span onclick="Sinc_DO.ambilDO(this,'sinkronisasi/panen_do/ambilDO2')" class="btn btn-danger">Sinkron DO2</span>
                    <span onclick="Sinc_DO.simpanDO(this)" class="btn btn-success">Simpan ke ST-Pakan </span>
                </div>
            </div>    
        </form>        
    </div>
    <div class="col-md-12" id="divListDO">
        
    </div>
</div>

<script type="text/javascript" src="assets/js/sinkronisasi/panen_do.js"></script>
