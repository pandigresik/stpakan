<div class="panel panel-default">
    <div class="panel-heading">Master - Ekspedisi</div>
    <div class="panel-body">
        <div class="row>">
            <button type="button" name="tombolTambah" id="btnTambah"
                    class="btn btn-primary">Baru</button>
            <br />
            <br />
        </div>
        <table id="master-ekspedisi"
               class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="col-md-2"><input type="text"
                                                class="form-control q_search" name="q_nama_ekspedisi"
                                                id="q_nama_ekspedisi" placeholder="search"></th>
                    <th class="col-md-2"><input type="text"
                                                class="form-control q_search" name="q_alamat" id="q_alamat"
                                                placeholder="search"></th>
                    <th class="col-md-4"><input type="text"
                                                class="form-control q_search" name="q_kota" id="q_kota"
                                                placeholder="search"></th>
                    <th class="col-md-4">&nbsp;</th>
                </tr>
                <tr>
                    <th class="col-md-2">Nama Ekspedisi</th>
                    <th class="col-md-2">Alamat</th>
                    <th class="col-md-2">Kota</th>
                    <th class="col-md-2">Jumlah Kendaraan</th>
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

<div class="modal fade" id="modal_ekspedisi" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Master - Ekspedisi</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inp_kode_ekspedisi"
                               class="<?php echo $style_label; ?> control-label">Kode Ekspedisi</label>
                        <div class="<?php echo $style_value; ?> input-group">
                            <input type="hidden" class="form-control field_input"
                                   name="tmp_kode_ekspedisi" id="tmp_inp_kode_ekspedisi"
                                   placeholder="Kode Ekspedisi"
                                   value="<?php echo strtoupper($gen_kode_ekspedisi); ?>"> <input
                                   type="text" class="form-control field_input"
                                   name="kode_ekspedisi" id="inp_kode_ekspedisi"
                                   placeholder="Kode Ekspedisi"
                                   value="<?php echo strtoupper($gen_kode_ekspedisi); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inp_nama_ekspedisi"
                               class="<?php echo $style_label; ?> control-label">Nama Ekspedisi</label>
                        <div class="<?php echo $style_value; ?> input-group">
                            <input type="text" class="form-control field_input"
                                   onkeyup="upper_text(this)" name="nama_ekspedisi"
                                   id="inp_nama_ekspedisi" placeholder="Nama Ekspedisi">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inp_alamat"
                               class="<?php echo $style_label; ?> control-label">Alamat</label>
                        <div class="<?php echo $style_value; ?> input-group">
                            <textarea class="form-control field_input" name="alamat"
                                      onkeyup="upper_text(this)" id="inp_alamat" placeholder="Alamat">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inp_kota"
                               class="<?php echo $style_label; ?> control-label">Kota</label>
                        <div class="<?php echo $style_value; ?> input-group">
                            <select class="form-control field_input" name="kota"
                                    id="inp_kota">
                                <option value="">Pilih Kota</option>
                                <?php foreach ($data_kota as $key => $value) { ?>
                                    <option value="<?php echo $value['KOTA']; ?>"><?php echo $value['KOTA']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="panel panel-default">
                            <div class="panel-heading">Daftar Kendaraan</div>
                            <div class="panel-body">
                                <table id="daftar-kendaraan"
                                       class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="col-md-2">NoPol</th>
                                            <th class="col-md-2">Tipe Kendaraan</th>
                                            <th class="col-md-2">Kuantitas Maksimal (Zak)</th>
                                            <th class="col-md-2">Berat Maksimal (KG)</th>
                                            <th class="col-md-2">Farm tujuan</th>
                                            <!--<th class="col-md-2">Kapasitas ritase (Zak)</th>-->
											<th class="col-md-1">Optimal Rate</th>
											<th class="col-md-1">Maksimal Rit</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-ke="1">
                                            <td><input type="text" onkeyup="kontrol_nopol(this)"
                                                       class="form-control field_input inp_no_pol" name="no_pol"
                                                       placeholder="No Polisi"></td>
                                            <td><select
                                                    class="form-control field_input inp_tipe_kendaraan"
                                                    name="tipe_kendaraan" placeholder="Tipe Kendaraan">
                                                    <option value="">Pilih Tipe Kendaraan</option>
                                                    <?php print_r($data_tipe_kendaraan) ?>
                                                    <?php foreach ($data_tipe_kendaraan as $key => $value) { ?>
                                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                                    <?php } ?>
                                                </select></td>
                                            <td><input type="text" onkeyup="number_only(this)"
                                                       class="form-control field_input inp_kuantitas_maksimal"
                                                       name="kuantitas_maksimal"
                                                       placeholder="Kuantitas Maksimal (Zak)"></td>
                                            <td><input type="text" onkeyup="number_only(this)"
                                                       class="form-control field_input inp_berat_maksimal"
                                                       name="berat_maksimal" placeholder="Berat Maksimal (KG)"></td>
                                            <td>
                                                <select class="form-control field_input inp_kode_farm" name="kode_farm" placeholder="Kode farm">
                                                    <option value="">Pilih Kode farm</option>
                                                </select>  

                                                <div><span class="glyphicon glyphicon-plus">&nbsp;</span></div>
                                            </td>
                                            <td><input type="text" onkeyup="number_only(this)"
                                                       class="form-control field_input inp_max_rit"
                                                       name="max_rit" placeholder="Max rit (Zak)"></td>
											<td><input type="text" onkeyup="number_only(this)"
                                                       class="form-control field_input inp_min_rit"
                                                       name="min_rit" placeholder="Min rit (Zak)"></td>
                                            <td>
                                                <div onclick="hapus_kendaraan(this)" class="deleted">
                                                    <span class="glyphicon glyphicon-minus"></span>
                                                </div>
                                            </td> 
                                        </tr>
                                        <tr>
                                            <td><div onclick="tambah_kendaraan(this)">
                                                    <span class="glyphicon glyphicon-plus">Tambah</span>
                                                </div></td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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

<style>
    div#modal_ekspedisi>.modal-dialog {
        width: 100% !important;
    }
</style>
<script type="text/javascript">
$(function(){
    
});
</script>
<script type="text/javascript" src="assets/js/master/ekspedisi.js"></script>