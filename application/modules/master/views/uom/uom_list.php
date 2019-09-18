<div class="panel panel-default">
    <div class="panel-heading">Master - Satuan</div>
    <div class="panel-body">
        <div class="row>">
            <button type="button" name="tombolTambah" id="btnTambah"
                    class="btn btn-primary">Baru</button>
            <br />
            <br />
        </div>
        <table id="master-uom" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="col-md-2"><input type="text"
                                                class="form-control q_search" name="q_satuan" id="q_satuan"
                                                placeholder="search"></th>
                    <th class="col-md-2"><input type="text"
                                                class="form-control q_search" name="q_deskripsi" id="q_deskripsi"
                                                placeholder="search"></th>
                    <th class="col-md-2"><input type="text"
                                                class="form-control q_search" name="q_satuan_dasar"
                                                id="q_satuan_dasar" placeholder="search"></th>
                    <th class="col-md-2"><!--input type="text"
                                                class="form-control q_search" name="q_konversi" id="q_konversi"
                                                placeholder="Konversi"-->

                        <div class="form-inline">
                            <select class="form-control q_search" name="q_konversi" id="q_konversi">
                                <option value="">Semua</option>
                                <?php foreach($list_konversi as $key => $value){ ?>
                                <option value="<?php echo $value['KONVERSI']; ?>"><?php echo $value['KONVERSI']; ?></option>
                                <?php } ?>
                            </select>
                        <button type="button" onclick="goSearch()" name="tombolCari" id="btnCari"
                    class="btn btn-primary">Cari</button>
                        </div>
                        </div>
                    </th>

                </tr>
                <tr>
                    <th class="col-md-2">Satuan</th>
                    <th class="col-md-2">Deskripsi</th>
                    <th class="col-md-2">Satuan Dasar</th>
                    <th class="col-md-2">Konversi</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="row clear-fix">
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

<div class="modal fade" id="modal_uom" tabindex="-1" role="dialog"
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
                            <div class="input-group-addon" onclick="master_uom(this)">
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
                    <button type="button" name="tombolSimpan" id="btnSimpan"
                            class="btn btn-primary">Simpan</button>
                    <button type="button" name="tombolUbah" id="btnUbah"
                            class="btn btn-primary">Ubah</button>
                    <button type="button" name="tombolBatal" id="btnBatal"
                            class="btn btn-primary">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_master_uom" tabindex="-1"
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
    div#modal_master_uom .modal-body {
        max-height: 420px;
        overflow-y: auto;
    }

    #master-base-uom tbody tr:hover {
        background-color: #A1E7FC;
        cursor: pointer;
    }
</style>

<script type="text/javascript" src="assets/js/master/uom.js"></script>