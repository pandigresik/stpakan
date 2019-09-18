<div class="panel panel-default" id="panel-input-do">
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
                        <label for="" class="col-md-3 control-label text-right">Nomor DO</label>

                        <div class="col-md-5">
                            <input type="text" class="form-control" id="nomor-do" data-no-penerimaan="" 
                                   name="nomor-do" placeholder="Nomor DO" onchange="input_do()" value="<?php echo $nomor_do;?>">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-default hide" id="btn-lanjut" onclick="lanjut()">Lanjut</button>
                        </div>
                        <div class="col-md-2 hide">
                            <button class="btn btn-default hide" id="btn-reset" onclick="reset()">Reset</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default hide" id="panel-daftar-do-sj">
    <div class="panel-heading">
        Daftar DO & Surat Jalan
    </div>
    <div class="panel-body detail-do">
        <div class="new-line">
            <div class="col-md-12" id="table-daftar-do-sj">
                
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default hide" id="panel-penerimaan-pakan">
    <div class="panel-heading">
        &nbsp;
    </div>
    <div class="panel-body detail-do">
        <div class="new-line">
            <div class="col-md-5">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="label" class="col-md-6 control-label text-right grey-label">Kandang</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label form-control grey-label" id="kandang" 
                            style="border: none;box-shadow: none;text-align: left"></label>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-6 control-label text-right grey-label">Nopol Kirim</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label form-control grey-label" id="nopol-kirim" style="border: none;box-shadow: none;text-align: left"></label>
                        </div>

                    </div>
                    <div class="form-group" id="div-tanggal-terima">
                        <label for="label" class="col-md-6 control-label text-right grey-label">Target Tanggal Terima</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label form-control grey-label" id="tanggal-terima" data-tanggal-terima"" style="border: none;box-shadow: none;text-align: left"></label>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right grey-label">&nbsp;</label>

                        <div class="col-md-5">&nbsp;
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label text-right grey-label">Nopol Terima</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label form-control grey-label link_span" onclick="showNopolImage(this)" id="label-nopol-terima" style="border: none;box-shadow: none;text-align: left"></label>
                            <input type="text" class="form-control validasi hide"
                                   id="nopol-terima" name="nopol-terima"
                                   onchange="validasi_verifikasi()"
                                   placeholder="Nopol Terima" onkeyup='upper_text(this)' readonly>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label text-right grey-label">Sopir</label>

                        <div class="col-md-5">
                            <label for="input" class="control-label form-control grey-label" id="label-sopir" style="border: none;box-shadow: none;text-align: left"></label>
                            <input type="text" class="form-control validasi hide" id="sopir"
                                   name="sopir" placeholder="Sopir"
                                   onchange="validasi_verifikasi()"
                                   onkeyup='upper_text(this)' readonly>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-horizontal">
                    <div class="form-group">

                        <div class="col-md-5">
                            <span class="btn">&nbsp;</span>
                        </div>

                    </div>
                    <div class="form-group">

                        <div class="col-md-5">
                            <span class="btn">&nbsp;</span>
                        </div>

                    </div>
                    <div class="form-group">

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
<link rel="stylesheet" type="text/css"
      href="assets/css/mutasi_pakan/tooltipster.css">

<script type="text/javascript"
src="assets/js/jquery.alphanum.js"></script>
<script type="text/javascript"
src="assets/js/common.js"></script>

<script type="text/javascript">
    var kode_farm = '<?php echo $kode_farm; ?>';     
    var global_no_penerimaan = '<?php echo $no_penerimaan; ?>';   
 
        function remove_local_storage(){
            if(global_no_penerimaan == 1){
                localStorage.removeItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan);
                localStorage.removeItem('nopol_sopir_'+kode_farm+'_'+global_no_penerimaan);
                localStorage.removeItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan);
                localStorage.removeItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan);
            }
        }

</script>
<script type="text/javascript"
src="assets/js/mutasi_pakan/jquery.tooltipster.min.js"></script>
<script type="text/javascript"
src="assets/js/penerimaan_pakan/<?php echo $grup_farm; ?>/transaksi.js"></script>
