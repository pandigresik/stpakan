<div class="col-md-12">
    <div class="">
        <!--button class="btn btn-default" type="button" onclick='print()'>Print</button-->
        <a href='penerimaan_pakan/transaksi/cetak_berita_acara?no_berita_acara=<?php echo $list['no_ba']; ?>' class='link btn btn-default' target="_blank">Print</a>
    </div>
    <div class="text-center">
        <h2>BERITA ACARA</h2>
    </div>
    <div class="new-line">
        <div class="col-md-6">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-4 text-right">No. Berita Acara</label> <label for="inputEmail3"
                                                class="col-sm-1">:</label> <label for="inputEmail3"
                                                                   class="col-sm-5"><?php echo $list['no_ba']; ?></label>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 text-right">Tanggal Kedatangan</label> <label
                        class="col-sm-1">:</label> <label for="inputEmail3"
                        class="col-sm-5"><?php echo convert_month($list['tgl_kedatangan'],1); ?></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4 text-right">Farm</label> <label class="col-sm-1">:</label>
                    <label class="col-sm-5"><?php echo $list['nama_farm']; ?></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4 text-right">No. SJ</label> <label
                        class="col-sm-1">:</label> <label for="inputEmail3"
                        class="col-sm-5"><?php echo $list['no_sj']; ?></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4 text-right">No. SPM</label> <label class="col-sm-1">:</label>
                    <label class="col-sm-5"><?php echo $list['no_spm']; ?></label>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-4 text-right">No. OP</label> <label class="col-sm-1">:</label>
                    <label class="col-sm-5"><?php echo empty($list['no_op']) ? '-' : $list['no_op']; ?></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4 text-right">No. Penerimaan</label> <label class="col-sm-1">:</label>
                    <label class="col-sm-5"><?php echo $list['no_penerimaan']; ?></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4 text-right">Ekspedisi</label> <label class="col-sm-1">:</label>
                    <label class="col-sm-5"><?php echo $list['ekspedisi']; ?></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4 text-right">No. Kendaraan</label> <label
                        class="col-sm-1">:</label> <label for="inputEmail3"
                        class="col-sm-5"><?php echo $list['no_kendaraan_terima']; ?></label>

                </div>
                <div class="form-group">
                    <label class="col-sm-4 text-right">Nama Sopir</label> <label class="col-sm-1">:</label>
                    <label class="col-sm-5"><?php echo $list['nama_sopir']; ?></label>

                </div>
            </div>
        </div>
    </div>
    <?php 
    $h_r=0;
    foreach($list['detail_barang'] as $key => $value){ 

        if($value['jml_rusak']>0){ 
            $h_r++;
        }
    }
    if($h_r>0){
    ?>
    <div class="form-horizontal col-md-12">
        <label><u>Barang Rusak :</u></label>
    </div>
    <div class="col-md-12">
        <div id="print-preview-table">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center col-md-1">Kode Barang</th>
                        <th class="text-center col-md-2">Nama Barang</th>
                        <th class="text-center col-md-1">Bentuk</th>
                        <th class="text-center col-md-1">Jumlah SJ (sak)</th>
                        <th class="text-center col-md-1">Jumlah Aktual (sak)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($list['detail_barang'] as $key => $value){ ?>
                    <?php if($value['jml_rusak']>0){ ?>
                    <tr>
                        <td class="text-center"><?php echo $value['kode_barang'];   ?></td>
                        <td class="text-center"><?php echo $value['nama_barang'];   ?></td>
                        <td class="text-center"><?php echo $value['bentuk_barang'];   ?></td>
                        <td class="text-right"><?php echo $value['jml_sj'];   ?></td>
                        <td class="text-right"><?php echo $value['jml_rusak'];   ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan='4'>

                            <table class="table table-bordered" style="width:80%;">
                                <thead>
                                    <tr>
                                        <th class="text-center col-md-1">No</th>
                                        <th class="text-center col-md-1">Berat (kg)</th>
                                        <th class="text-center col-md-3">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $no=1; ?>
                                <?php foreach($value['detail_timbang'] as $k => $v){ ?>
                                <?php if($v['jml_putaway']>0){ ?>
                                <tr>
                                    <td class="text-center"><?php echo $no.'.';   ?></td>
                                    <td class="text-center"><?php echo $v['berat_putaway'];   ?></td>
                                    <td class="text-left"><?php echo $v['keterangan_rusak'];   ?></td>
                                </tr>
                                <?php $no++; } } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php 
    }
    $h_k=0;
    foreach($list['detail_barang'] as $key => $value){ 

        if($value['jml_kurang']>0){ 
            $h_k++;
        }
    }
    if($h_k>0){
    ?>
    <div class="form-horizontal col-md-12">
        <label><u>Barang Kurang :</u></label>
    </div>
    <div class="col-md-12">
        <div id="print-preview-table">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center col-md-2">Kode Barang</th>
                        <th class="text-center col-md-2">Nama Barang</th>
                        <th class="text-center col-md-2">Bentuk</th>
                        <th class="text-center col-md-1">Jumlah SJ (sak)</th>
                        <th class="text-center col-md-1">Jumlah Aktual (sak)</th>
                        <th class="text-center">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($list['detail_barang'] as $key => $value){ ?>
                    <?php if($value['jml_kurang']>0){ ?>
                    <tr>
                        <td class="text-center"><?php echo $value['kode_barang'];   ?></td>
                        <td class="text-center"><?php echo $value['nama_barang'];   ?></td>
                        <td class="text-center"><?php echo $value['bentuk_barang'];   ?></td>
                        <td class="text-right"><?php echo $value['jml_sj'];   ?></td>
                        <td class="text-right"><?php echo $value['jml_kurang'];   ?></td>
                        <td class="text-left"><?php echo $value['keterangan_kurang'];   ?></td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>
    <div class="form-horizontal col-md-12 new-line">
        
            <table class="col-md-12 new-line" style="border:none">
                <tbody>
                    <tr>
                        <td class="text-center col-md-4">Mengetahui,<br>Kepala Farm</td>
                        <td class="text-center col-md-4">Penerima/Pengawas</td>
                        <td class="text-center col-md-4">Sopir</td>
                    </tr>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr><td colspan="3">&nbsp;</td></tr>
                    <tr height="20%">
                        <td class="text-center col-md-4">( _________________ )</td>
                        <td class="text-center col-md-4">( _________________ )</td>
                        <td class="text-center col-md-4">( _________________ )</td>
                    </tr>
                </tbody>
            </table>
    </div>
<!--pre><?php //print_r($list) ?></pre-->
</div>
