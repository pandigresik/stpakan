<div class="panel panel-default">
    <div class="panel-heading">
        Mutasi Pakan antar Kandang
    </div>
    <div class="panel-body">
        <div>
            <a href="#mutasi_pakan/transaksi" class="btn btn-default" id="btn_baru">Baru</a>
        </div>
        <div class="form form-horizontal" id="form_input">
            <div class="form-group">
                <div class="col-md-8">
                    <div class="">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="0" id="belum_tindak_lanjut" onchange="kontrol_checkbox()">
                                Mutasi yang belum ditindaklanjuti</label>
                        </div>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2" style="text-align: left;">No. Mutasi</label>
                <div class="col-md-2">
                    <input type="text" name="no_mutasi" id="no_mutasi" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2">
                    <select name="tanggal" id="tanggal" class="form-control" onchange="kontrol_tanggal()">
                        <option value="">Pilih</option>
                        <option value="TGL_PEMBERIAN">Tanggal Pembuatan</option>
                        <option value="TGL_KEBUTUHAN">Tanggal Kebutuhan</option>
                    </select>
                </div>
                <div class="col-md-10">
                    <div class="form-inline">

                        <div class="form-group col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="tanggal_awal" name="tanggal_awal" placeholder="" readonly>
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group  col-md-1">
                            <label style="text-align: left;" class="control-label col-md-3">s/d</label>
                        </div>
                        <div class="form-group  col-md-3">
                            <div class="input-group">
                                <input type="text" class="form-control" id="tanggal_akhir" name="tanggal_akhir" placeholder="" readonly>
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="form-group">
                <label style="text-align: left;" class="control-label col-md-2">Kandang</label>
                <div class="col-md-2">
                    <select class="form-control" name="kandang" id="kandang">
                        <option value="">Pilih</option>
                        <?php foreach ($data_kandang as $key => $value) { ?>
                            <option value="<?php echo $value['kode_kandang'] ?>"><?php echo $value['nama_kandang'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button onclick="tampilkan()" id="btn_tampil" class="btn btn-default col-md-offset-5">
                        Tampilkan
                    </button>
                </div>

            </div>

        </div>
        <div class="panel panel-primary row hide" style="margin-bottom: 0px;" id="daftar_mutasi_pakan">
            <div class="panel-heading">
                Daftar Mutasi Pakan
            </div>
            <div class="panel-body"></div>
        </div>
        <div class="panel panel-primary row hide" id="detail_mutasi_pakan">
            <div class="panel-heading">
                Detail Mutasi Pakan
            </div>
            <div class="panel-body"></div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css"
      href="assets/css/mutasi_pakan/mutasi.css">
<script type="text/javascript" src="assets/js/jquery.alphanum.js"></script>
<script type="text/javascript"
src="assets/js/forecast/config.js"></script>
<script type="text/javascript">
    var base_url = "<?php echo $base_url; ?>";
</script>
<script type="text/javascript">
	var level_user = "<?php echo $level_user; ?>";
</script>
<script type="text/javascript"
src="assets/js/mutasi_pakan/<?php echo $grup_farm; ?>/main.js"></script>
