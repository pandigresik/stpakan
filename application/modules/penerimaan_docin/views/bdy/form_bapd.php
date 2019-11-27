<div class="row col-md-12">
  <form class="form form-horizontal">
    <div class="form-group">
      <div class="col-md-3">
        <?php
          $hide = '';
          $disabled = '';
          $khatchery = '';
          $nreg = '';
          $doc_in = '';
          if(isset($ubahbap) && $ubahbap){
            echo '<span class="btn btn-default" onclick="BAPD.ubahformbap(this)">Ubah</span>';
            $hide = 'hide';
            $disabled = 'disabled';
            $khatchery = $kode_hatchery;
            $nreg = $no_reg;
            $doc_in = $tgl_doc_in;
          }
        ?>
        <span class="btn btn-default tmbdraft <?php echo $hide ?>" data-revisi="0" onclick="BAPD.simpanbapdoc(this,'D')">Simpan Draft</span>
        <span class="btn btn-default tmbrilis" data-revisi="<?php echo $revisi ?>" onclick="BAPD.simpanbapdoc(this,'N')">Rilis</span>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-1">Kandang</label>
      <div class="col-md-2">
        <select class="form-control" onchange="BAPD.data_kandang(this)" name="kode_kandang" <?php echo $disabled ?> >
          <option value="">Pilih Kandang</option>
          <?php
            if(!empty($list_kandang)){
              foreach($list_kandang as $ls){
                if($ls['NO_REG'] == $nreg){
                  $selected = "selected";
                }
                else{
                  $selected = "";
                }
                echo '<option '.$selected.' data-tgldocin="'.$ls['TGL_DOC_IN'].'" value="'.$ls['NO_REG'].'">Kandang '.$ls['KODE_KANDANG'].'</option>';
              }
            }
          ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-1">No. Reg</label>
      <div class="col-md-2">
        <input type="text" class="form-control" readonly  name="no_reg" value="<?php echo $nreg ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-1">Tgl. DOC In</label>
      <div class="col-md-2">
        <input type="text" class="form-control" readonly  name="tgl_doc_in" value="<?php echo $doc_in ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-1">Hatchery</label>
      <div class="col-md-2">
        <select class="form-control" name="kode_hatchery" onchange="BAPD.list_suratjalan(this)" <?php echo $disabled ?> >
          <option value="">Pilih Hatchery</option>
          <?php
            if(!empty($list_hatchery)){
              foreach($list_hatchery as $ls){
                if($ls['KODE_HATCHERY'] == $khatchery){
                  $selected = "selected";
                }
                else{
                  $selected = "";
                }
                echo '<option '.$selected.' value="'.$ls['KODE_HATCHERY'].'">'.$ls['NAMA_HATCHERY'].'</option>';
              }
            }
          ?>
        </select>
      </div>
    </div>
  </form>
  <div class="div_bapsuratjalan">
    <?php
    if(isset($detail_bap)){
      echo $detail_bap;
    }
    ?>
  </div>
</div>
