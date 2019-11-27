<?php
  $_readonly_over = 'readonly';
  $_readonly = '';
  $_disable = '';
  if(isset($readonly)){
    $_readonly = $readonly;
    if(!empty($readonly)){
      $_disable = 'disabled';
    }
  }
  if (isset($readonly_over)) {
    $_readonly_over = $readonly_over;
  }


?>
<div class="row col-md-6">
  <form class="form form-horizontal form_permintaan" onsubmit="return false">
    <div class="form-group">
      <label class="control-label col-md-4">No. Permintaan Sak</label>
      <div class="col-md-6">
        <input type="text" class="form-control" readonly  name="no_ppsk" value="<?php echo $no_ppsk ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">Sisa Sak Tersimpan</label>
      <div class="col-md-6">
        <input type="text" class="form-control number" style="width: 100px" data-prefix_ppsk="<?php echo isset($prefix_ppsk) ? $prefix_ppsk : '' ?>" name="sak_tersimpan"  id="sak_tersimpan" readonly value="<?php echo $sak_tersimpan ?>" />
        <input type="hidden" name="sak_tersimpan_t" id="sak_tersimpan_t" value="<?php echo $sak_tersimpan?>" >
        <input type="hidden" name="prefix_ppsk" id="prefix_ppsk" value="<?php echo $prefix_ppsk?>" >

      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">Kategori</label>
      <div class="col-md-4">
         <label class="radio-inline"><input type="radio" <?php echo $_disable; if($kategori == 'I') echo " checked"; ?> onchange="load_keterangan(this)" id='kategori'name="kategori" value="I">Internal</label>
         <label class="radio-inline"><input type="radio" <?php echo $_disable; if($kategori == 'E') echo " checked"; ?> onchange="load_keterangan(this)" name="kategori" value="E" id='kategori'>Eksternal</label>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">Keterangan</label>
      <div class="col-md-4">
        <select class="form-control" name="keterangan" id="keterangan" <?php echo $_disable ?> onchange="loadTotalBudget(this)" >
          <option value="">Pilih</option>
          <?php
            if(!empty($keterangan)){
              foreach($keterangan as $ls){
                if($ls->KODE_BUDGET == $kode_budget){
                  $selected = "selected";
                }
                else{
                  $selected = "";
                }
                echo '<option '.$selected.' value="'.$ls->KODE_BUDGET.'">'.$ls->NAMA_BUDGET.'</option>';
              }
            }
          ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">Sisa Budget</label>
      <div class="col-md-2 ">
        <input type="text" class="form-control number" readonly name="budget_sisa" id="budget_sisa" style="width: 100px" value="<?php echo $budget_sisa?>" />
        <input type="hidden" id="budget_sisa_t" value="<?php echo $budget_sisa_t?>" >
      </div>
      <div class=" col-md-1">
        <label class="control-label"  style="width:0px; padding-left:0px; padding-right:30px">Dari</label>
      </div>
      <div class="col-md-2">
        <input type="text" class="form-control number" readonly name="budget_total" id="budget_total" style="width: 100px" value="<?php echo $budget_total?>" />
      </div>
      <div  class=" col-md-1">
        <label class="control-label"  style="width:0px; padding-left:0px; padding-right:30px">Sak</label>
      </div>
    </div>
    <div class="form-group ctrl_hrg_jual hide">
      <label class="control-label col-md-4">Harga Jual</label>
	  <div class=" col-md-1" style="width:35px; padding-left:20px; padding-right:0px; ">
        <label class="control-label"  style="width:0px; padding-left:0px; padding-right:0px; margin-left:0px; margin-right:0px;">Rp.</label>
      </div>
      <div class="col-md-2">
        <input type="text" class="form-control number" name="hrg_jual" id="hrg_jual" value="<?php echo $hrg_sak ?>" <?php echo $_readonly ?>/>
      </div>
      <div class=" col-md-1" style="width:30px; padding-left:0px; padding-right:0px; margin-left:-10px; ">
        <label class="control-label"  style="width:30px; padding-left:0px; padding-right:0px; ">, 00</label>
      </div>
      <!--div class="col-md-6">
        <input type="text" class="form-control number" name="hrg_jual" id="hrg_jual" value="<?php echo $hrg_sak ?>" <?php echo $_readonly ?>/>
      </div-->
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">Jumlah Yang Diminta</label>
      <div class="col-md-6">
        <input type="text" class="form-control number" name="jml_sak" id="jml_sak" value="<?php echo $jml_sak ?>" <?php echo $_readonly ?> onkeyup="hitungSisaBudget(this)"/>
        <span style="color: red; display: none" id="over_warning">*) Jumlah yang diminta melebihi sisa budget. Mohon mengisi pengajuan over budget</span>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">Pengajuan Over Budget</label>
      <div class="col-md-6">
        <input type="text" class="form-control number" name="jml_over" id="jml_over" value="<?php echo $jml_over ?>" <?php echo $_readonly_over ?>/>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-4">Alasan Over Budget</label>
      <div class="col-md-6">
        <textarea cols="43" class="form-control" id="alasan_over" name="alasan_over" <?php echo $_readonly_over ?>><?php echo $alasan_over?></textarea>
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
      <div class="col-md-6">
        <select class="form-control" name="user_peminta" <?php echo $_disable ?> >
          <option value="">Pilih</option>
          <?php
            if(!empty($list_user)){
              foreach($list_user as $ls){
                echo $ls['KODE_PEGAWAI'];
                if($ls['KODE_PEGAWAI'] == $user_peminta){
                  $selected = "selected";
                }
                else{
                  $selected = "";
                }
                echo '<option '.$selected.' value="'.$ls['KODE_PEGAWAI'].'">'.$ls['NAMA_PEGAWAI'].'</option>';
              }
            }
          ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <div class="col-md-6 col-md-offset-4">
        <?php echo isset($tombol) ? $tombol : '' ?>
        <span id="tooltip-reject"></span>
        <span class="tooltipster-span hide">
          <div class="panel panel-primary" style="margin-bottom: 0px">
            <div class="panel-heading">Konfirmasi Reject</div>
            <div class="panel-body">
              <div class="form-group">
                <div style="margin-bottom: 5px">
                  <span>Mohon mengisi keterangan reject <br>(Min. 10 karakter)</span>
                </div>
                <textarea class="form-control" onkeyup="lengthCek(this)" cols="50" id="keterangan_reject" name="keterangan_reject"></textarea>
              </div>
              <div class="form-group pull-right" style="margin-bottom:0px">
                <button class="btn btn-default" onclick="$('#tooltip-reject').tooltipster('hide');">batal</button>
                <button class="btn btn-primary btn_simpan_reject" disabled style="margin-left: 5px" onclick="permintaanSak.reject(this)">Simpan</button>
              </div>
            </div>
          </div>
        </span>
      </div>
    </div>
  </form>

</div>
