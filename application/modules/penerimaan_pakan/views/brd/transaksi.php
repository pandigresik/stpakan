<div class="panel panel-default" id="panel-penerimaan-pakan">
    <div class="panel-heading">
        Penerimaan Pakan di Gudang
        <button class="btn btn-default hide" id="btn-tutup" data-no-penerimaan=""
                data-status-terima="" data-no-ba="" onclick="tutup(this)" disabled>Tutup Surat
            Jalan</button>
        <button class="btn btn-default hide" id="btn-detail-penerimaan" onclick="bukti_penerimaan_barang(this)">Bukti Penerimaan Barang</button>
    </div>
    <div class="panel-body detail-do">
        <div class="new-line">
            <div class="col-md-5">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label text-right">Nomor DO</label>

                        <div class="col-md-5">
                            <input type="text" class="form-control" id="nomor-do"
                                   name="nomor-do" placeholder="Nomor DO" onchange="verifikasi_do()" value="<?php echo $nomor_do;?>">
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right grey-label">Nomor
                            Surat Jalan</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label text-right grey-label" id="nomor-sj"></label>
                            <label for="input" class="control-label hide text-right grey-label" id="tanggal-sj"></label>
                            <label for="input" class="control-label hide text-right grey-label" id="kuantitas-kg"></label>
                            <label for="input" class="control-label hide text-right grey-label" id="kuantitas-zak"></label>
                            <label for="input" class="control-label hide text-right grey-label" id="tanggal-verifikasi-do"></label>
                            <label for="input" class="hide control-label text-right grey-label" id="nomor-penerimaan"></label>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right grey-label">Nomor OP</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label text-right grey-label" id="nomor-op"></label>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right grey-label">Target Tanggal Kirim</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label text-right grey-label" id="target-tanggal-kirim"></label>
                        </div>

                    </div>
                    <div class="form-group" id="div-tanggal-terima">
                        <label for="label" class="col-md-4 control-label text-right grey-label">Tanggal Terima</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label text-right grey-label" id="tanggal-terima"></label>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right grey-label">Ekspedisi</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label text-right grey-label" id="nama-ekspedisi"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right grey-label">Nopol Kirim</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label text-right grey-label" id="nopol-kirim"></label>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right grey-label">Nopol Terima</label>

                        <div class="col-md-5">
                            <input type="text" class="form-control validasi"
                                   id="nopol-terima" name="nopol-terima"
                                   onchange="validasi_verifikasi()"
                                   placeholder="Nopol Terima" onkeyup='upper_text(this)' readonly>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label text-right grey-label">Sopir</label>

                        <div class="col-md-5">
                            <input type="text" class="form-control validasi" id="sopir"
                                   name="sopir" placeholder="Sopir"
                                   onchange="validasi_verifikasi()"
                                   onkeyup='upper_text(this)' readonly>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label text-right"></label>


                        <div class="col-md-5">
                            <input type="button" onclick="verifikasi()" class="btn btn-default" id="btn-verifikasi" value="Verifikasi" disabled>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="penimbangan-pakan">
</div>
<div id="pakan-rusak-hilang">
</div>
<link rel="stylesheet" type="text/css"
      href="assets/css/penerimaan_pakan/penerimaan.css">

<script type="text/javascript"
src="assets/js/jquery.alphanum.js"></script>
<script type="text/javascript"
src="assets/js/common.js"></script>
<script type="text/javascript"
src="assets/js/penerimaan_pakan/<?php echo $grup_farm; ?>/transaksi.js"></script>