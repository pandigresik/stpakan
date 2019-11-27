<div class="panel panel-default">
    <div class="panel-heading">LHK - Analisa Performance Kandang</div>
    <div class="panel-body">
        <div class="form-group">
            <a href="#" id="btn-baru" onclick="simpan_baru()"
               class="btn btn-default"
               <?php echo ($status == 'D') ? '' : 'disabled'; ?>>Simpan</a><a
               href="#analisa_performance_kandang/main" id="btn-batal"
               class="btn btn-default">Batal</a><a href="#" id="btn-release"
               class="btn btn-default" onclick="release()"
               <?php echo ($status == 'D') ? '' : 'disabled'; ?>>Rilis</a>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">Filtering</div>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group hide">
                        <label for="" class="col-md-2 control-label">
                            <p class="text-left">Data Hide</p>
                        </label>

                        <div class="col-md-2">
                            <input type="text" class="form-control" id="status" name="status"
                                   placeholder="Status Order" value="<?php echo $status; ?>"
                                   readonly> <input type="text" class="form-control" id="no-order"
                                   name="no-order" placeholder="No. Order"
                                   value="<?php echo $no_order; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">
                            <p class="text-left">Kode Farm</p>
                        </label>

                        <div class="col-md-2">
                            <input type="text" class="form-control" id="kode-farm"
                                   name="kode-farm" placeholder="Kode Farm"
                                   value="<?php echo $farm; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">
                            <p class="text-left">Tanggal Kirim</p>
                        </label>

                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control" id="tanggal-kirim"
                                       name="tanggal-kirim" onchange="convert_datepicker(this)"
                                       placeholder="Tanggal Kirim"
                                       value="<?php echo $tanggal_kirim; ?>" onchange="clear_date()">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">
                            <p class="text-left">Tanggal Kebutuhan</p>
                        </label>

                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                       id="tanggal-kebutuhan-awal" name="tanggal-kebutuhan-awal"
                                       placeholder="Tanggal Kebutuhan Awal" readonly
                                       value="<?php echo $tanggal_kebutuhan_awal; ?>">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>
                        <label class="col-md-1 control-label" for="">
                            <p class="text-center">s/d</p>
                        </label>
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control"
                                       id="tanggal-kebutuhan-akhir" name="tanggal-kebutuhan-akhir"
                                       placeholder="Tanggal Kebutuhan Akhir"
                                       onchange="convert_datepicker(this)"
                                       value="<?php echo $tanggal_kebutuhan_akhir; ?>"
                                       onchange="clear_date()">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <a href="#" class="btn btn-default" onclick="generate()">Generate</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <a href="#" class="btn btn-default hide" id="btn-tambah-barang"
               onclick="tambah_barang()"
               <?php echo ($status == 'D' || $status == '') ? '' : 'disabled'; ?>>Tambah
                Barang</a>
        </div>
        <div id="contain-daftar-barang"></div>
    </div>
</div>
<link rel="stylesheet" type="text/css"
      href="assets/css/analisa_performance_kandang/analisa.css">
<script type="text/javascript"
src="assets/js/analisa_performance_kandang/analisa.js"></script>