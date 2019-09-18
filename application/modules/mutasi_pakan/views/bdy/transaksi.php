
<div id="riwayat-pengambilan-pakan" class="panel panel-default">
    <div class="panel-heading">
        Mutasi Pakan antar Kandang
    </div>
    <div class="panel-body">
        <div class="row" id="div_baru">

            <div class="col-md-6">
                <div class="form-horizontal">

                    <div class="form-group <?php echo empty($mutasi_pakan['NO_MUTASI']) ? 'hide' : '' ; ?>">
                        <label for="label" class="col-md-5 control-label" style="text-align: left">No. Mutasi</label>

                        <div class="col-md-5">
                            <label for="label" class="control-label" style="text-align: left" id="no_mutasi"><?php echo empty($mutasi_pakan['NO_MUTASI']) ? '' : $mutasi_pakan['NO_MUTASI'] ; ?></label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="label" class="col-md-5 control-label" style="text-align: left">Tanggal Pemberian Pakan</label>

                        <div class="col-md-5">
                            <input type="text" class="form-control" id="tanggal_pemberian" name="tanggal_pemberian" data-tanggal-pemberian="<?php echo $data_server['tanggal_server']; ?>" value="<?php echo date('d M Y', strtotime($data_server['tanggal_server'])) ?>" readonly>
                        </div>

                    </div>

                    <div class="form-group">
                        <label style="text-align: left" class="col-md-5 control-label" for="label">Nama Farm</label>

                        <div class="col-md-5">
                            <input type="text" name="nama_farm" id="nama_farm" class="form-control" value="<?php echo $data_server['farm']; ?>" readonly>
                        </div>

                    </div>
                    <div class="form-group">
                        <label style="text-align: left" class="col-md-5 control-label" for="label">Jenis Pakan</label>

                        <div class="col-md-5">
                            <select class="form-control" name="jenis_pakan" id="jenis_pakan" onchange="kontrol_kuantitas_pemberian_pakan()" <?php echo empty($mutasi_pakan['KODE_BARANG']) ? '' : 'disabled' ; ?>>
                                <option value="">Pilih</option>
                                <?php foreach ($data_jenis_pakan as $key => $value) { ?>
                                	<?php $selected = ($value['kode_barang'] == $mutasi_pakan['KODE_BARANG']) ? "selected" : ""; ?>
                                    <option value="<?php echo $value['kode_barang'] ?>" <?php echo $selected; ?>><?php echo $value['nama_barang'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                    <div class="form-group">
                        <label style="text-align: left" class="col-md-5 control-label" for="label">Kuantitas Pemberian Pakan</label>

                        <div class="col-md-2">
                            <input value="<?php echo empty($mutasi_pakan['JML_MUTASI']) ? '' : $mutasi_pakan['JML_MUTASI'] ; ?>" type="text" onkeyup="kontrol_kuantitas_pemberian_pakan()" onchange="kontrol_kuantitas_pemberian_pakan()" name="kuantitas_pemberian_pakan" id="kuantitas_pemberian_pakan" class="form-control test-tooltip" data-toggle="tooltip" data-placement="right" title="Kuantitas pemberian pakan akan berdampak pada kuantitas konsumsi/ekor.">
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-5">
                            <button class="btn btn-default" id="btn_simpan" onclick="simpan()" data-aksi="<?php echo $aksi; ?>" disabled>
                                Simpan
                            </button></label>

                        <div class="col-md-2">
                            <button class="btn btn-default" id="btn_tampilkan" onclick="tampilkan()">
                                Tampilkan
                            </button>

                        </div>

                        <div class="col-md-2">
                        	<?php if(empty($mutasi_pakan['NO_MUTASI'])){ ?>
                            <button id="btn_batal" class="btn btn-default" onclick="batal()">
                                Batal
                            </button>
                            <?php } else { ?>
                            <a href="#mutasi_pakan/main" class="btn btn-default">Batal</a>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-horizontal">

                    <div class="form-group <?php echo empty($mutasi_pakan['NO_MUTASI']) ? 'hide' : '' ; ?>">
                        <label for="label" class="col-md-5 control-label" style="text-align: left">&nbsp;</label>

                        <div class="col-md-5">
                            <label for="label" class="control-label" style="text-align: left">&nbsp;</label>
                        </div>

                    </div>

                    <div class="form-group">
                        <label style="text-align: left" class="col-md-4 control-label" for="label">Tanggal Kebutuhan</label>

                        <div class="col-md-5">
                            <input type="text" name="tanggal_kebutuhan" id="tanggal_kebutuhan" class="form-control" data-tanggal-kebutuhan="<?php echo $data_server['tanggal_server_besok_lusa']; ?>" value="<?php echo date('d M Y', strtotime($data_server['tanggal_server_besok_lusa'])) ?>" readonly>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label" style="text-align: left">Kandang Asal</label>

                        <div class="col-md-5">
                            <select class="form-control" id="kandang_asal" name="kandang_asal" onchange="kontrol_kuantitas_pemberian_pakan()" <?php echo empty($mutasi_pakan['kode_kandang']) ? '' : 'disabled' ; ?>>
                                <option value="">Pilih</option>
                                <?php foreach ($data_kandang as $key => $value) { ?>
                                	<?php $selected = ($value['kode_kandang'] == $mutasi_pakan['kode_kandang']) ? "selected" : ""; ?>
                                    <option value="<?php echo $value['kode_kandang'] ?>" data-no-reg="<?php echo $value['no_reg'] ?>" <?php echo $selected; ?>><?php echo $value['nama_kandang'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="label" class="col-md-4 control-label" style="text-align: left">Alasan</label>

                        <div class="col-md-5"><textarea class="form-control" id="alasan" rows="2" cols="10" name="alasan"><?php echo isset($mutasi_pakan['alasan']) ? $mutasi_pakan['alasan'] : ''; ?></textarea>
                            
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-primary row hide" style="margin-bottom: 0px;" id="daftar_kandang">
            <div class="panel-heading">
                Daftar Kandang
            </div>
            <div class="panel-body"></div>
        </div>

    </div>
</div>
<link rel="stylesheet" type="text/css"
      href="assets/css/mutasi_pakan/mutasi.css">
<link rel="stylesheet" type="text/css"
      href="assets/css/mutasi_pakan/tooltipster.css">
<script type="text/javascript"
src="assets/js/mutasi_pakan/jquery.tooltipster.min.js"></script>
<script type="text/javascript"
src="assets/js/mutasi_pakan/<?php echo $grup_farm; ?>/transaksi.js"></script>
