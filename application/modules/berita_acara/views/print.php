
<div aria-labelledby="print-preview-tab" id="print-preview"
     class="tab-pane fade" role="tabpanel">
    <div class="new-line">
        <button class="btn btn-default" type="button" onclick='print()'>Print</button>
    </div>
    <div class="text-center">
        <h2>BERITA ACARA</h2>
    </div>
    <div class="new-line">
        <div class="col-md-6">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-4">Tanggal</label> <label for="inputEmail3"
                                                                   class="col-sm-1">:</label> <label for="inputEmail3"
                                                                   class="col-sm-5"></label>
                </div>
                <div class="form-group">
                    <label class="col-sm-4">No. Berita Acara</label> <label
                        class="col-sm-1">:</label> <label for="inputEmail3"
                        class="col-sm-5"><?php echo (isset($data[0]['no_ba'])) ? $data[0]['no_ba'] : ''; ?></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4">No. SJ</label> <label class="col-sm-1">:</label>
                    <label for="inputEmail3" class="col-sm-5"></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4">No. Penerimaan</label> <label
                        class="col-sm-1">:</label> <label for="inputEmail3"
                        class="col-sm-5"></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4">No. OP</label> <label class="col-sm-1">:</label>
                    <label for="inputEmail3" class="col-sm-5"></label>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-4">Kode Farm</label> <label class="col-sm-1">:</label>
                    <label for="inputEmail3" class="col-sm-5"></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4">Nama Farm</label> <label class="col-sm-1">:</label>
                    <label for="inputEmail3" class="col-sm-5"></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4">Nama Sopir</label> <label class="col-sm-1">:</label>
                    <label for="inputEmail3" class="col-sm-5"></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4">No. Kendaraan</label> <label
                        class="col-sm-1">:</label> <label for="inputEmail3"
                        class="col-sm-5"></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4">No. SPM</label> <label class="col-sm-1">:</label>
                    <label for="inputEmail3" class="col-sm-5"></label>

                </div>
            </div>
        </div>
    </div>
    <div class="form-horizontal col-md-12">
        <label><u>List barang :</u></label>
    </div>
    <div class="col-md-12">
        <div id="print-preview-table">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Bentuk Pakan</th>
                        <th>Jumlah Rusak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php //foreach($items as $key => $value){ ?>
                    <?php //if($value['keterangan']==1){ ?>
                    <tr>
                        <td><?php //echo $value['kode_kandang'];   ?></td>
                        <td><?php //echo $value['kode_barang'];   ?></td>
                        <td><?php //echo $value['nama_barang'];   ?></td>
                        <td><?php //echo $value['tmp_jumlah'];   ?></td>
                        <td></td>
                    </tr>
                    <?php //} } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="form-horizontal col-md-12">
        <label><u>Keterangan :</u></label>
    </div>
    <div class="form-horizontal col-md-12">
        <label></label>
    </div>
</div>