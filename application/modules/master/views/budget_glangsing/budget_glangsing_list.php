<div class="panel panel-default">
    <div class="panel-heading">Master Budget Glangsing</div>
    <div class="panel-body">
        <div class="form-inline">
        </div>
        <div class="form-horizontal">
            <button onclick="add_budget()" class="btn btn-default">Baru</button>
            <br>
            <table id="search_table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class=" ctext-center id_pallet" style="padding-left:0px; width:240px">
                            <input type="text" class="text-center form-control q_search" name="q_id_budget" id="q_id_budget"
                                                    placeholder="search" onkeyup="goSearch();">
                        </th>
                        <th class="text-center tanggal_penimbangan" style="padding-left:0px; width:620px">
                            <input type="text" class="text-center form-control q_search" name="q_nama_budget" id="q_nama_budget"
                                                    placeholder="search" onkeyup="goSearch();">
                        </th>
                        <th class="text-center tara" style="padding-left:0px; width:210px">
                            <select class="form-control" onchange="goSearch();" id="q_kategori" name="q_kategori">
                                <option value="">Semua</option>
                                <option value="I">Internal</option>
                                <option value="E">Eksternal</option>
                            </select>
                        </th>
                        <th class="text-center keterangan" style="padding-left:0px; width:210px">
                            <select class="form-control" onchange="goSearch();" id="q_status" name="q_status">
                                <option value="">Semua</option>
                                <option value="A">Aktif</option>
                                <option value="N">Tidak Aktif</option>
                            </select>
                        </th>
                    </tr>
                </thead>
            </table>

        <div class="row clear-fix hide">
            <div class="col-md-3 pull-right">
                <button id="previous" class="btn btn-sm btn-primary" disabled>Previous</button>
                <lable>Page <lable id="page_number"></lable> of <lable
                        id="total_page"></lable></lable>
                <button id="next" class="btn btn-sm btn-primary">Next</button>
            </div>
        </div>
    </div>
</div>

<?php
$style_label = "col-sm-4";
$style_value = "col-sm-8";
?>

<div class="modal fade" id="modal_pallet" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Master - Satuan</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inp_kodekandang"
                               class="<?php echo $style_label; ?> control-label">Satuan</label>
                        <div class="<?php echo $style_value; ?> input-group">
                            <input type="text" class="form-control field_input"
                                   name="periode_siklus" id="inp_satuan" placeholder="Satuan">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inp_kodekandang"
                               class="<?php echo $style_label; ?> control-label">Deskripsi</label>
                        <div class="<?php echo $style_value; ?> input-group">
                            <input type="text" class="form-control field_input"
                                   name="periode_siklus" id="inp_deskripsi" placeholder="Deskripsi">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inp_kodekandang"
                               class="<?php echo $style_label; ?> control-label">Satuan Dasar</label>
                        <div class="<?php echo $style_value; ?> input-group">
                            <input type="text" class="form-control field_input"
                                   data-satuan="" name="periode_siklus" id="inp_satuan_dasar"
                                   placeholder="Satuan Dasar">
                            <div class="input-group-addon" onclick="master_pallet(this)">
                                <b>...</b>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inp_kodekandang"
                               class="<?php echo $style_label; ?> control-label">Konversi</label>
                        <div class="<?php echo $style_value; ?> input-group">
                            <input type="text" class="form-control field_input"
                                   onkeyup="number_only(this)" name="periode_siklus"
                                   id="inp_konversi" placeholder="Konversi">
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer" style="margin: 0px; padding: 3px;">
                <div class="pull-right">
                   
                    <button type="button" name="tombolUbah" id="btnUbah"
                            class="btn btn-primary">Ubah</button>
                    <button type="button" name="tombolBatal" id="btnBatal"
                            class="btn btn-primary">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_master_pallet" tabindex="-1"
     role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 50%">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Master - Satuan</h4>
            </div>
            <div class="modal-body"></div>

            <div class="modal-footer" style="margin: 0px; padding: 3px;">
                <div class="pull-right">
                    <button type="button" name="tombolKembali" id="btnKembali"
                            class="btn btn-primary">Kembali</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    div#modal_master_pallet .modal-body {
        max-height: 420px;
        overflow-y: auto;
    }
    /*
    #master-pallet tbody tr:hover {
        background-color: #A1E7FC;
        cursor: pointer;
    }
    */
    /*
    #master-pallet thead{
        display: block;
    }

    #master-pallet tbody{
        display: block;
        max-height: 380px;
        overflow-y: auto;
        overflow-x: none;
    }
    */
    #master-pallet .id_pallet{
        min-width: 180px;
        max-width: 180px;
    }
    #master-pallet .tanggal_penimbangan{
        min-width: 250px;
        max-width: 250px;
    }
    #master-pallet .tara{
        min-width: 250px;
        max-width: 250px;
    }
    #master-pallet .keterangan{
        min-width: 370px;
        max-width: 370px;
    }

    .btn-glyphicon {
         border: 2px solid #000000;
         border-radius: 50px;
         padding: 3px;
         margin-left: 10px;
    }

    .red_border{
        border: 2px solid red;
    }

    .tooltips-fade-show {
        opacity: 1;
    }
    .tooltips-fade {
        opacity: 0;
        transition-property: opacity;
    }
    .tooltips-base {
        font-size: 0;
        left: 0;
        line-height: 0;
        overflow: visible;
        padding: 0;
        pointer-events: none;
        position: relative;
        top: 0;
        width: 100px;
        z-index: 9999999;
        display: inline-block;
        margin-left:15px;
    }
    .tooltips-default {
        background: #4c4c4c none repeat scroll 0 0;
        border: 2px solid #000;
        border-radius: 5px;
        color: #fff;
    }.tooltips-base .tooltips-content {
        overflow: hidden;
    }
    .tooltips-default .tooltips-content {
        font-family: Arial,sans-serif;
        font-size: 14px;
        line-height: 30px;
        overflow: hidden;
        padding: 8px 10px;
    }
    .tooltips-arrow {
        display: block;
        height: 100%;
        left: 0;
        position: absolute;
        text-align: center;
        top: 0;
        width: 100%;
        z-index: -1;
    }
    .tooltips-arrow-right .tooltips-arrow-border {
        border-bottom: 9px solid transparent !important;
        border-right: 9px solid;
        border-top: 9px solid transparent !important;
        margin-top: -8px;
    }
    .tooltips-arrow-right span, .tooltips-arrow-right .tooltips-arrow-border {
        border-bottom: 8px solid transparent !important;
        border-right: 8px solid;
        border-top: 8px solid transparent !important;
        left: -7px;
        margin-top: -7px;
        top: 50%;
    }
    .tooltips-arrow-right span, .tooltips-arrow-right .tooltips-arrow-border {
        border-bottom: 8px solid transparent !important;
        border-right: 8px solid;
        border-top: 8px solid transparent !important;
        left: -7px;
        margin-top: -7px;
        top: 50%;
    }
    .tooltips-arrow span, .tooltips-arrow-border {
        display: block;
        height: 0;
        position: absolute;
        width: 0;
    }
    .tooltips-arrow span, .tooltips-arrow-border {
        display: block;
        height: 0;
        position: absolute;
        width: 0;
    }
    .tooltips-default {
        color: #fff;
    }.tooltips-arrow-right span, .tooltips-arrow-right .tooltips-arrow-border {
        border-bottom: 8px solid transparent !important;
        border-right: 8px solid;
        border-top: 8px solid transparent !important;
        left: -7px;
        margin-top: -7px;
        top: 50%;
    }
    .tooltips-arrow span, .tooltips-arrow-border {
        display: block;
        height: 0;
        position: absolute;
        width: 0;
    }
    div.span_keterangan{
        width: 200px;
    }

    .medium-large>.modal-dialog {
        width: 75% !important;
    }

    .table-header{
        font-weight: bold;
    }

    #history-pallet td{
        vertical-align: middle;
        text-align: center;
    }

    #search_table, #search_table tr, #search_table th, #search_table td{
        border: none;
    }

</style>

<link rel="stylesheet" type="text/css"
      href="assets/css/mutasi_pakan/tooltipster.css">
<script type="text/javascript"
src="assets/js/mutasi_pakan/jquery.tooltipster.min.js"></script>
<script type="text/javascript" src="assets/js/master/budget_glangsing.js"></script>
<script type="text/javascript" src="assets/js/jquery.scrollabletable3.js"></script>
