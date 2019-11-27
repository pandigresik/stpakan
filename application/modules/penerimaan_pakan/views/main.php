<div class="panel panel-default">
    <div class="panel-heading">Daftar Penerimaan Pakan</div>
    <div class="panel-body">
        <div>
            <button href='#penerimaan_pakan/transaksi' class='btn btn-default btn-baru' onclick='baru(this,0)'>Baru</button>
        </div>
        <div class="form-inline new-line">
            <label for="tanggal-kirim">Tanggal Kirim</label>
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
                    onclick="get_data_daftar_penerimaan()">Cari</button>
        </div>
        <div id="daftar-penerimaan-pakan-table" class="new-line">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="col-md-1"><input class="form-control filter" placeholder="search" type="text" name="no_op" onkeyup="filter(this)"></th>
                        <th class="col-md-2"><input class="form-control filter" placeholder="search" type="text" name="no_berita_acara" onkeyup="filter(this)"></th>
                        <th class="col-md-2"><input class="form-control filter" placeholder="search" type="text" name="no_penerimaan" onkeyup="filter(this)"></th>
                        <th class="col-md-2"><input class="form-control filter" placeholder="search" type="text" name="no_sj" onkeyup="filter(this)"></th>
                        <th class="col-md-3"><input class="form-control filter" placeholder="search" type="text" name="ekspedisi" onkeyup="filter(this)"></th>
                        <th class="col-md-2">
                            <button class="btn btn-default" id="btn-cari"
                            onclick="filter(this)">Cari</button></th>
                    </tr>
                    <tr>
                        <th>No. OP</th>
                        <th>No. Berita Acara</th>
                        <th>No. Penerimaan</th>
                        <th>No. SJ</th>
                        <th>Ekspedisi</th>
                        <th>Tanggal Kirim</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $data_ke = 1; ?>
                    <?php foreach ($list as $key => $value) { ?>
                        <?php 
                            $detail_barang = '';
                            $barang = [];
                            $no_ba = '';
                            $ba = [];
                            foreach ($value['detail_barang'] as $key1 => $value1) {
                                if(!in_array($value1['kode_barang'], $barang)){
                                    $detail_barang .= ($key1==0) ? $value1['kode_barang'].';'.$value1['jml_sj'] : '?'.$value1['kode_barang'].';'.$value1['jml_sj'];
                                }
                                $barang[] = $value1['kode_barang'];
                                if(!in_array($value1['no_berita_acara'], $ba)){
                                    $no_ba .= ($key1==0) ? $value1['no_berita_acara'] : ', '.$value1['no_berita_acara'];
                                }
                                $ba[] = $value1['no_berita_acara'];
                            } 
                        ?>
                        <tr ondblclick="baru(this,1)"
                            data-ke="<?php echo $data_ke; ?>"
                            data-no-do="<?php echo $value['no_do']; ?>"
                            data-no-kendaraan-kirim="<?php echo $value['no_kendaraan_kirim']; ?>"
                            data-no-spm="<?php echo $value['no_spm']; ?>"
                            data-detail-barang="<?php echo $detail_barang; ?>">
                            <td class='fno_op'><?php echo $value['no_op']; ?></td>
                            <?php if(!empty($no_ba) && $no_ba != '-'){?>
                            <td class='fno_berita_acara' data-no-ba="<?php echo $no_ba; ?>" onclick="print_view_berita_acara(this)"><b style="color:blue;"><?php echo $no_ba; ?></b></td>
                            <?php }else{?>
                            <td class='fno_berita_acara' data-no-ba="<?php echo $no_ba; ?>"><?php echo $no_ba; ?></td>
                            <?php }?>
                            <td class='fno_penerimaan'><?php echo $value['no_penerimaan']; ?></td>
                            <td class='fno_sj'><?php echo $value['no_sj']; ?></td>
                            <td class='fekspedisi'><?php echo $value['ekspedisi']; ?></td>
                            <td class='ftanggal_kirim'><?php echo convert_month($value['tanggal_kirim'], 1); ?></td>
                            
                        </tr>
                    <?php $data_ke++; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css"
      href="assets/css/penerimaan_pakan/penerimaan.css">
<script type="text/javascript"
src="assets/js/penerimaan_pakan/penerimaan.js"></script>