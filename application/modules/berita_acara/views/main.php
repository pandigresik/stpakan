<div class="panel panel-default">
    <div class="panel-heading">Berita Acara</div>
    <div class="panel-body">
        <div>
            <div data-example-id="togglable-tabs" role="tabpanel"
                 class="bs-example bs-example-tabs">
                <ul role="tablist" class="nav nav-tabs" id="myTab">
                    <li role="presentation" class="active"><a href="#transaction"
                                                              id="transaction-tab" role="tab" data-toggle="tab"
                                                              aria-controls="transaction" aria-expanded="true">Transaksi</a></li>
                    <li class="" role="presentation"><a aria-expanded="false"
                                                        href="#print-preview" role="tab" id="print-preview-tab"
                                                        data-toggle="tab" aria-controls="print-preview">Print Preview</a></li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div aria-labelledby="transaction-tab" id="transaction"
                         class="tab-pane fade active in" role="tabpanel">
                        <div class="form-horizontal new-line">
                            <a href='#berita_acara/main' class="btn btn-default" class='link'>Baru</a>
                            <a id="btn-simpan" class="btn btn-default" type="submit"
                               onclick="simpan()">Simpan</a> <a href='#berita_acara/main'
                               class="btn btn-default" class='link'>Batal</a>
                        </div>
                        <div class="form-horizontal new-line">
                            <div class="form-group">
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">No. Berita Acara</p></label>

                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" placeholder="No. Berita Acara"
                                               value="<?php echo $no_ba; ?>" name="no-berita-acara"
                                               id="no-berita-acara" class="form-control" readonly>
                                        <div class="input-group-addon" onclick="list_berita_acara(this)">
                                            <b>...</b>
                                        </div>
                                    </div>
                                </div>
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">No. Penerimaan</p></label>

                                <div class="col-md-2">
                                    <input type="text" placeholder="No. Penerimaan"
                                           name="no-penerimaan" id="no-penerimaan" class="form-control form-clear"
                                           readonly>

                                </div>
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">Nama Sopir</p></label>

                                <div class="col-md-2">
                                    <input type="text" placeholder="Nama Sopir" name="nama-sopir"
                                           id="nama-sopir" class="form-control form-clear" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">No. SJ</p></label>

                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="text" placeholder="No. SJ" name="no-sj" id="no-sj"
                                               class="form-control" readonly>
                                        <div class="input-group-addon" onclick="list_surat_jalan(this)">
                                            <b>...</b>
                                        </div>
                                    </div>
                                </div>
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">No. OP</p></label>

                                <div class="col-md-2">
                                    <input type="text" placeholder="No. OP" name="no-op" id="no-op"
                                           class="form-control form-clear" readonly>

                                </div>
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">No. Kendaraan</p></label>

                                <div class="col-md-2">
                                    <input type="text" placeholder="No. Kendaraan"
                                           name="no-kendaraan" id="no-kendaraan" class="form-control form-clear"
                                           readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">Tipe Berita Acara</p></label>

                                <div class="col-md-2">
                                    <select placeholder="Tipe Berita Acara"
                                            name="tipe-berita-acara" id="tipe-berita-acara"
                                            class="form-control" onchange="ubah_judul_tipe_ba(this)">
                                        <option value="">Tipe Berita Acara</option>
                                        <option value="K">Kurang</option>
                                        <option value="R">Rusak</option>
                                    </select>
                                </div>
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">Kode Farm</p></label>

                                <div class="col-md-2">
                                    <input type="text" placeholder="Kode Farm" name="kode-farm"
                                           id="kode-farm" class="form-control form-clear" readonly>

                                </div>
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">No. SPM</p></label>

                                <div class="col-md-2">
                                    <input type="text" placeholder="No. SPM" name="no-spm"
                                           id="no-spm" class="form-control form-clear" readonly>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left"></p></label>

                                <div class="col-md-2"></div>
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">Nama Farm</p></label>

                                <div class="col-md-2">
                                    <input type="text" readonly="" placeholder="Nama Farm"
                                           name="nama-farm" id="nama-farm" class="form-control form-clear" readonly>


                                </div>
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left"></p></label>

                                <div class="col-md-2"></div>
                            </div>
                        </div>
                        <div class="daftar-barang-table">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Bentuk</th>
                                        <th>Jumlah <span class='tipe-ba'>Rusak</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-md-2 control-label" for=""><p
                                        class="text-left">Keterangan</p></label>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <textarea id='keterangan' class="form-control form-clear" keyup="upper_text(this)"></textarea>
                        </div>
                    </div>
                    <div aria-labelledby="print-preview-tab" id="print-preview"
                         class="tab-pane fade" role="tabpanel">
                        <div class="new-line">
                            <a href="berita_acara/main/cetak_daftar_penerimaan?no_sj=&tipe_ba=" target="_blank" id="btn-print"> <button type="button" class="btn btn-default link">Print</button></a>
                        </div>
                        <div class="text-center">
                            <h2>BERITA ACARA</h2>
                        </div>
                        <div class="new-line">
                            <div class="col-md-6">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-4">Tanggal</label> <label
                                            class="col-sm-1">:</label> <label class="col-sm-5 form-clear"
                                            id="ptanggal-berita-acara"></label>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4">No. Berita Acara</label> <label
                                            class="col-sm-1">:</label> <label class="col-sm-5 form-clear"
                                            id="pno-berita-acara"></label>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4">No. SJ</label> <label class="col-sm-1">:</label>
                                        <label class="col-sm-5 form-clear" id="pno-sj"></label>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4">No. Penerimaan</label> <label
                                            class="col-sm-1">:</label> <label class="col-sm-5 form-clear"
                                            id="pno-penerimaan"></label>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4">No. OP</label> <label class="col-sm-1">:</label>
                                        <label class="col-sm-5 form-clear" id="pno-op"></label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-horizontal">
                                    <div class="form-group">
                                        <label class="col-sm-4">Kode Farm</label> <label
                                            class="col-sm-1">:</label> <label class="col-sm-5 form-clear"
                                            id="pkode-farm"></label>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4">Nama Farm</label> <label
                                            class="col-sm-1">:</label> <label class="col-sm-5 form-clear"
                                            id="pnama-farm"></label>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4">Nama Sopir</label> <label
                                            class="col-sm-1">:</label> <label class="col-sm-5 form-clear"
                                            id="pnama-sopir"></label>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4">No. Kendaraan</label> <label
                                            class="col-sm-1">:</label> <label class="col-sm-5 form-clear"
                                            id="pno-kendaraan"></label>

                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4">No. SPM</label> <label
                                            class="col-sm-1">:</label> <label class="col-sm-5 form-clear"
                                            id="pno-spm"></label>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal col-md-12">
                            <label><u>List barang :</u></label>
                        </div>
                        <div class="col-md-12">
                            <div class="daftar-barang-table">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Bentuk Pakan</th>
                                            <th>Jumlah <span class='tipe-ba'>Rusak</span></th>
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
                            <label id="pketerangan" class="form-clear"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" type="text/css"
          href="assets/css/berita_acara/berita_acara.css">
    <script type="text/javascript"
    src="assets/js/berita_acara/berita_acara.js"></script>