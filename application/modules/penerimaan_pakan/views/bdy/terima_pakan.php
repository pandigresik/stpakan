<div class="panel panel-default">
    <div class="panel-heading">Penerimaan Pakan Dari Farm Lain ( per nomer kavling )</div>
    <div class="panel-body">
        
        <form class="form form-horizontal new-line" onsubmit="return false">
            <div class="form-group">
                <label class="control-label col-md-2">No. DO</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="no_do">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2">Kandang penerima</label>
                <div class="col-md-4">
                    <select name="no_reg" class="form-control">
                        <?php 
                            foreach($no_reg as $nr){
                                echo '<option value="'.$nr->no_reg.'" data-flok="'.$nr->flok_bdy.'">Kandang '.$nr->kode_kandang.' ( '.$nr->no_reg.' )</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2">Pakan </label>
                <div class="col-md-4">
                    <select name="kode_barang" class="form-control">
                        <?php 
                            foreach($barang as $b){
                                echo '<option value="'.$b->kode_barang.'" >'.$b->nama_barang.'</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2">Jumlah Sak</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="kuantitas">
                </div>
            </div>
            <div class="form-group">                
                <div class="col-md-4 col-md-offset-2">
                    <button type="submit" class="btn btn-danger" onclick="TerimaPakan.terima(this)">Terima</button>
                </div>
            </div>                
        </form>
    </div>
</div>
<script type="text/javascript" src="assets/js/penerimaan_pakan/terima_pakan_farm.js"></script>