<div id="div_content">
  <div class="row col-md-10">
    <div class="btn-group div_btn" style="margin-bottom:10px">
      <?php
      if($level_user == 'KF'){
        echo '<button class="btn btn-primary" disabled type="button" onclick="pengembalianSakAck.simpan()">ACK</button>';
      } ?>
          
      </div>
    <div class="form form-horizontal" id="formFilter">
      <div class="form-group">
        <label class="control-label col-md-2" style="text-align: right;">Kandang</label>
        <div class="col-md-3">
          <select class="form-control" name="kandang" id="kandang" onchange="pengembalianSakAck.loadPage()">
            <option value="%">Semua</option>
            <?php if (isset($kandang)){
            foreach ($kandang as $key => $val) { ?>
              <option value="<?php echo $val['KODE_KANDANG'] ?>"><?php echo $val['NAMA_KANDANG'] ?></option>
            <?php }} ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-md-2" style="text-align: right;">Status Pengembalian</label>
        <div class="col-md-8">
          <div class="checkbox col-md-2">
            <label><input value="BK" type="checkbox" name="filterStatus" onclick="pengembalianSakAck.loadPage()">Belum Kembali</label>
          </div>
          <div class="checkbox col-md-2">
            <label><input value="BA" checked="" type="checkbox" name="filterStatus" onclick="pengembalianSakAck.loadPage()">Belum ACK</label>
          </div>
          <div class="checkbox col-md-2">
            <label><input value="SA" checked="" type="checkbox" name="filterStatus" onclick="pengembalianSakAck.loadPage()">Sudah ACK</label>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row col-md-12" >
    <div id="div_list_permintaan" style="margin-top:10px">
      <table class="table table-bordered custom_table">
        <thead>
          <tr>
            <th>No. Pengambilan</th>
            <th>Tgl Pakai</th>
            <th>Kategori</th>
            <th>Kandang</th>
            <th>Terima<br>(Sak)</th>
            <th>Kembali<br>(Sak)</th>
            <th>Terpakai<br>(Sak)</th>
            <th>Aksi</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
        <tfoot>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<link rel="stylesheet" type="text/css" href="assets/css/permintaan_sak_kosong/permintaan.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/tooltipster.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-light.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-noir.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-punk.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-shadow.css" >
<script type="text/javascript" src="assets/js/permintaan_glangsing/pengembalian_sak_ack.js"></script>
<script type="text/javascript" src="assets/libs/jquery/tooltipster/jquery.tooltipster.min.js"></script>
