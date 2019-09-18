<?php
  $_readonly = '';
  $_disable = '';
  if(isset($readonly)){
    $_readonly = $readonly;
    if(!empty($readonly)){
      $_disable = 'disabled';
    }
  }


?>
<div class="row col-md-6">
  <form class="form form-horizontal" onsubmit="return false">
    <div class="form-group">
      <label class="control-label col-md-4">No. Permintaan Sak</label>
      <div class="col-md-4">
        <input type="text" class="form-control" readonly  name="no_ppsk" value="<?php echo $no_ppsk ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">Sisa Sak Tersimpan</label>
      <div class="col-md-4">
        <input type="text" class="form-control number" style="width: 75px" data-prefix_ppsk="<?php echo isset($prefix_ppsk) ? $prefix_ppsk : '' ?>" name="sak_tersimpan"  id="sak_tersimpan" readonly value="<?php echo $sak_tersimpan ?>" />
        <input type="hidden" name="sak_tersimpan_t" id="sak_tersimpan_t" value="<?php echo $sak_tersimpan?>" >
        <input type="hidden" name="prefix_ppsk" id="prefix_ppsk" value="<?php echo $prefix_ppsk?>" >

      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">Keterangan</label>
      <div class="col-md-4">
        <select class="form-control" name="keterangan" id="keterangan" disabled >
          <option value="">Penjualan Sak</option>
        </select>
      </div>
    </div>
    <!--<div class="form-group" style="display: none;">
      <label class="control-label col-md-2">Sisa Budget</label>
      <div class="col-md-1 ">
        <input type="text" class="form-control number" readonly name="budget_sisa" id="budget_sisa" style="width: 75px" value="<?php echo $budget_sisa?>" />
        <input type="hidden" id="budget_sisa_t" value="<?php echo $budget_sisa_t?>" >
      </div>
      <div>
        <label class="control-label col-md-2"  style="width:0px; padding-left:0px; padding-right:30px">Dari</label>
      </div>
      <div class="col-md-1">
        <input type="text" class="form-control number" readonly name="budget_total" id="budget_total" style="width: 75px" value="<?php echo $budget_total?>" />
      </div>
      <div>
        <label class="control-label col-md-2"  style="width:0px; padding-left:0px; padding-right:30px">Sak</label>
      </div>
    </div>!-->
    <div class="form-group">
      <label class="control-label col-md-4">Jumlah Yang Diminta</label>
      <div class="col-md-4">
        <input type="text" class="form-control number" name="jml_sak" value="<?php echo $jml_sak ?>" onfocus="replaceStr()" onkeyup="hitungSisaBudget(this)"/>
      </div>
    </div>
    <!--<div class="form-group">
      <label class="control-label col-md-2">Keterangan</label>
      <div class="col-md-2">
        <textarea class="form-control" name="keterangantmp" <?php echo $_readonly ?> ><?php echo $keterangan ?></textarea>
      </div>
    </div>!-->
    <div class="form-group">
      <label class="control-label col-md-4">Penerima Sak</label>
      <div class="col-md-4">
        <input type="text" class="form-control" name="user_penerima" value="<?php echo $user_penerima ?>"/>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">No. DO</label>
      <div class="col-md-4">
        <input type="text" class="form-control" name="no_do" value="<?php echo $no_do ?>"/>
      </div>
    </div>
    <div class="form-group">
      <div class="col-md-6 col-md-offset-4">
        <?php echo isset($tombol) ? $tombol : '' ?>
      </div>
    </div>
  </form>

</div>