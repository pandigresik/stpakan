<?php $metodeTimbangan = $lockTimbangan ? 'onfocus="Home.getDataTimbang(this)" readonly' : '' ?>
<div class="row col-md-12">
  <form class="form form-horizontal form_permintaan" onsubmit="return false">
	<div class="btn-group" style="margin-bottom:10px">
	  <button class="btn btn-default hide" type="button" id="btnsimpan" onclick="pengembalianSak.submit()">Simpan</button>
	</div>
    <div class="form-group">
      <label class="control-label col-md-2">Scan RFID</label>
      <div class="col-md-3">
        <input type="password" class="form-control" name="scan_rfid" onchange="pengembalianSak.scanRFID(this)" value="" />
      </div>
	  <div class="col-md-3">
		<label id="rfid-status" class="glyphicon" style="font-size:15pt;line-height:20pt;"></label>
		<label id="rfid-status-label" style="color:red;font-size:9pt;"></label>
	  </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-2">Kandang</label>
      <div class="col-md-2">
        <label class="control-label" id="nama_kandang"></label>
      </div>
      <!--<input type="hidden" name="no_reg" value=""  placeholder="no_reg"/>--> <!-- hide content -->
      <!--<input type="hidden" name="kode_flok" value="" placeholder="kode_flok" />--> <!-- hide content -->
    </div>
    <div class="form-group">
      <label class="control-label col-md-2">Kategori Glangsing</label>
      <div class="col-md-2">
        <!--<input type="hidden" name="jml_diminta" value="" placeholder="jml_diminta" />--> <!-- hide content -->
        <!--<input type="text" name="kode_budget" value="" placeholder="kode_budget" />--> <!-- hide content -->
        <!--<select name="no_ppsk" class="form-control" onchange="pengembalianSak.selectKategoriBudget(this)" id="no_ppsk" disabled></select>-->
        <select name="no_ppsk" class="form-control" onchange="pengembalianSak.setTanggalKebutuhan(this.value)" id="no_ppsk" disabled></select>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-2">Tgl Kebutuhan</label>
      <div class="col-md-2">
		
		<!-- baru -->
		<select name="no_ppsk" class="form-control" id="tgl_kebutuhan" onChange="pengembalianSak.selectKategoriBudget(this)" disabled></select>
		<!-- end baru -->
		
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-2">Berat Glangsing</label>
      <div class="col-md-2">
        <input type="text" placeholder="" style="width:90px;display:inline;" id="berat_glansing"
               name="brt_kembali" class="form-control berat-timbang text-center"
               value=""
               <?php //echo $metodeTimbangan ?> 
			   onblur="pengembalianSak.get_berat_timbang(this)"
			   onkeyup="number_only(this)"
			   disabled
			   >
			   <label style="display:inline;">Kg</label>
	 </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-2">Jumlah Glangsing</label>
      <div class="col-md-1">
        <input id="jumlah_kembali" type="text" style="width:50px;display:inline;" class="form-control text-center"  name="jml_kembali" value="" readonly />
		<label style="display:inline;">Sak</label>
	  </div>
    </div>

  </form>

</div>


<script type="text/javascript" src="assets/js/permintaan_glangsing/pengembalian_sak.js"></script>