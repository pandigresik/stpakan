<form class="form form-horizontal" action="<?php echo site_url('sinkronisasi/adminer/createSinkron') ?>" method="post" onsubmit="return false">    
    <div class="form-group">
        <label for="" class="col-md-2">Farm Tujuan</label>
        <div class="col-md-10">
            <select name="kode_farm" class="form-control">
                <?php 
                    if(!empty($farms)){
                        foreach($farms as $f){
                            echo '<option value="'.$f['KODE_FARM'].'">'.$f['NAMA_FARM'].'</option>';
                        }
                    }
                ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-md-2">Transaksi</label>
        <div class="col-md-10">
            <input type="text" name="transaksi" class="form-control">
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-md-2">Aksi</label>
        <div class="col-md-10">
            <select name="aksi" onchange="Adminer.generateSinkron(this)" class="form-control">
                <?php 
                    if(!empty($aksi)){
                        foreach($aksi as $ak){
                            echo '<option value="'.$ak['kode'].'">'.$ak['label'].'</option>';
                        }
                    }
                ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-md-2">Id auto increment</label>
        <div class="col-md-10">            
            <label class="checkbox"><input type="checkbox" onclick="Adminer.generateSinkron(this)" name="status_identity">Auto increment ( default false )</label>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-md-2">Kunci</label>                    
        <div class="col-md-10">
            <?php 
                if(!empty($fields)){
                    foreach($fields as $_k => $cl){
                        echo '<label class="checkbox col-md-3"><input onclick="Adminer.generateSinkron(this)" type="checkbox" data-index="'.$_k.'" name="kunci" value="'.$cl.'" >'.$cl.'</label>';
                    }
                }
            ?>                                
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-md-2">Preview</label>                    
        <div class="col-md-10 preview_div">
            
        </div>
    </div>    
</form>