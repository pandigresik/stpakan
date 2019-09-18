<div class="panel panel-default">
    <div class="panel-heading">Daftar Penerimaan Pakan</div>
    <div class="panel-body">
        <div>
            <button href='#penerimaan_pakan/transaksi' class='btn btn-default' onclick='baru(this,0)'>Baru</button>
        </div>
        <div class="form-inline new-line">
            <div class="checkbox">
                <label><input type="checkbox" onclick="kontrol_chekbox(this)" name="do_belum_diterima" value="1" id="do_belum_diterima" checked="checked"> Filter DO yang belum diterima</label>
            </div>
            <label for="tanggal-kirim" class="tanggal-kirim-label">Tanggal Kirim</label>
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" id="tanggal-kirim-awal"
                           name="tanggal-kirim-awal" placeholder="Tanggal Kirim Awal"
                           readonly>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
            <div class="checkbox">
                <label>&nbsp;s/d</label>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" id="tanggal-kirim-akhir"
                           name="tanggal-kirim-akhir" placeholder="Tanggal Kirim Akhir"
                           readonly>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
            <button class="btn btn-default" id="btn-cari"
                    onclick="goSearch()">Cari</button>
        </div>
        <div id="daftar-do-table" class="new-line">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="no_op" ></th>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="no_do" ></th>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="no_sj" ></th>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="nama_ekspedisi" ></th>
                        <th><input class="form-control filter" placeholder="cari" type="text" name="tanggal_kirim"  readonly></th>
                        <th>
                            <!--button class="btn btn-default" id="btn-cari"
                            onclick="goSearch()">Cari</button--></th>
                    </tr>
                    <tr>
                        <th class="col-md-1">No. OP</th>
                        <th class="col-md-1">No. DO</th>
                        <th class="col-md-1">No. SJ</th>
                        <th class="col-md-1">Ekspedisi</th>
                        <th class="col-md-1">Tanggal Kirim</th>
                        <th class="col-md-1">Tanggal Terima</th>
                        <th class="col-md-1">Jam Terima</th>
                        <th class="col-md-1">Penerima</th>
                        <th class="col-md-1">No. BA</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="new-line clear-fix">
                <div class="col-md-3 pull-right">
                    <button id="previous" class="btn btn-sm btn-primary" disabled>Previous</button>
                    <lable>Page <lable id="page_number"></lable> of <lable
                            id="total_page"></lable></lable>
                    <button id="next" class="btn btn-sm btn-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="assets/css/penerimaan_pakan/penerimaan.css">
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript"
src="assets/js/penerimaan_pakan/<?php echo $grup_farm; ?>/daftar_do.js"></script>
