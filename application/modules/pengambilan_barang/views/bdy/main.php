        <div class="form-inline new-line">
            <label for="tanggal-kirim">Tanggal Kirim</label>
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" id="tanggal-kirim-awal" name="tanggal-kirim-awal" placeholder="Tanggal Kirim Awal" value="<?php echo tglIndonesia($hari_ini,'-',' ') ?>" readonly>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" id="tanggal-kirim-akhir" name="tanggal-kirim-akhir" placeholder="Tanggal Kirim Akhir" value="<?php echo tglIndonesia($hari_ini,'-',' ') ?>" readonly>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
            <button class="btn btn-default" id="btn-cari" onclick="Pengambilan.get_data_pengambilan()">Cari</button>
            <div class="pull-right">
                <label>Scan RFID</label> &nbsp;<input type="text" class="scan_rfid form-control" onchange="Pengambilan.pilih_nomer_order(this)" />
            </div>
        </div>
        <div id="picking-list-table" class="new-line">

        </div>
<link rel="stylesheet" type="text/css" href="assets/css/pengambilan_barang/pengambilan.css?v=0.1">
<script type="text/javascript" src="assets/js/jquery.alphanum.js"></script>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/spin.min.js"></script>
<script type="text/javascript" src="assets/js/pengambilan_barang/<?php echo $grup_farm; ?>/pengambilan.js"></script>

